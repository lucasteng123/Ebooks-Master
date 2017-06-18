<?php

/*
	Account Manager
		-> Validates all input data
		-> Enters new accounts into database
		-> Checks user logins and starts sessions
		-> Checks sessions for timeouts (todo)
*/

class AccountMgrException extends Exception {
	const POST_VARIABLE_INVALID = 1;
	const POST_UNDER_LIMIT = 2;
	const POST_OVER_LIMIT = 3;
	const POST_NO_VALUE = 4;
	const POST_NOT_SAME = 5;

	const NOT_UNIQUE_USERNAME = 10;
	const NOT_UNIQUE_EMAIL = 10;
	const ACCOUNT_NOT_FOUND = 11;
	const INCORRECT_PASSWORD = 12;
	const EXPIRED_PASSWORD = 13;
	const ACCOUNT_LOCKED = 14;

	const RESET_CODE_FAIL = 20;

	const PDO_ERROR = 9000;
	const GENERIC_INTERNAL_ERROR = 9001;
}

class AccountMgr {
	private $pdo = null;
	private $wd = null;

	private $last_entry_id = null;
	private $last_activation_code = null;

	private $max_post_size = 204800; // 200KB - should be well enough for a blog entry

	private $last_exception = null;

	private $max_attempts = 5;
	private $login_cooldown = 900; // 15 minutes in seconds

	const SESSION_OKAY = 51;
	const SESSION_NEWPASS = 52;
	const SESSION_EXPIRED = 53;

	function __construct($pdo) {
		if ( !(gettype($pdo) === 'object') ) {
			FrameworkException::throw_datatype_exception(VarTools::what_is($pdo), 'PDO');
		}
		$this->pdo = $pdo;
		$this->wd = realpath(dirname(__FILE__));
	}
	function setup() {
		$sql = file_get_contents($this->wd . "/table_creation.sql");
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		if ($stmt->errorCode() != 0) {
			$msg = $errors[2];
			trigger_error("Error during AccountMgr setup: ".var_dump($errors));
		}

		// The following is bullshit.
		// Bullshit that worked, somehow...
		$sql = file_get_contents($this->wd . "/table_creation_2.sql");
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		if ($stmt->errorCode() != 0) {
			$msg = $errors[2];
			trigger_error("Error during AccountMgr setup: ".var_dump($errors));
		}
	}
	function change_username($email,$username) {
		$sql = "UPDATE accountmgr_accounts SET username=:username WHERE reset_email=:email";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "email", $email, PDO::PARAM_STR );
	 	$stmt->bindValue( "username", $username, PDO::PARAM_STR );
	 	$stmt->execute();
	}
	function new_account($username,$password,$name,$email,$p_verify = false) {
		// THROWS: PDOException, AccountMgrException

		// Default params
		if ($p_verify === false) $p_verify = $password;

		// CHECK ISSET
		if (!isset($username) || !isset($password) || !isset($name) || !isset($email))
			throw new AccountMgrException("unset field", AccountMgrException::POST_NO_VALUE);

		// Initialize Validators
		$FV_alphanumeric = new FieldValidator();
		$FV_alphanumeric->set_filter_setting('default_preset','alphanum'); // Allow A-z0-9_

		$FV_name = new FieldValidator();
		$FV_name->set_filter_setting('default_preset','alpha-multilang'); // Allow multilingual characters

		$FV_email = new FieldValidator();
		$FV_email->set_filter_setting('default_preset','email');

		// Rewrote some things - 2015-06-06 - now using indexes and arrays.
		$fields = array(
			array($username,"username", 2,     40,    $FV_name        ),
			array($password,"password", 2,     512,   false           ),
			array($name,    "name",     2,     40,    $FV_name        ),
			array($email,   "email",    false, false, $FV_email       ),

		);
		foreach ($fields as $field) {
			// CHECK LENGTH
			if ($field[2] !== false) if (strlen(utf8_decode($field[0])) < $field[2]) {
				throw new AccountMgrException("The ".$field[1]." given is too short! It must be at least ".$field[2]." characters.", AccountMgrException::POST_UNDER_LIMIT);
			}
			if ($field[3] !== false) if (strlen(utf8_decode($field[0])) > $field[3]) {
				throw new AccountMgrException("The ".$field[1]." given is too long! It must no longer than ".$field[3]." characters.", AccountMgrException::POST_OVER_LIMIT);
			}

			// CHECK FORMAT
			if ($field[4] !== false) {
				if (!$field[4]->verify($field[0])) {
					throw new AccountMgrException("Invalid value for ".$field[1].".", AccountMgrException::POST_VARIABLE_INVALID);
				}
			}
		}
		// CHECK IF USERNAME EXISTS
		if (!$this->ensure_unique_username($username))
			throw new AccountMgrException("This username is taken already!", AccountMgrException::NOT_UNIQUE_USERNAME);

		if (!$this->ensure_unique_email($email))
			throw new AccountMgrException("This email has been used here before!", AccountMgrException::NOT_UNIQUE_EMAIL);

		if ($password != $p_verify)
			throw new AccountMgrException("The two passwords you typed do not match!", AccountMgrException::POST_NOT_SAME);

		// GENERATE SALT AND HASH
		$genSalt = HashFunctions::generate_salt();
		$genHash = HashFunctions::compute_hash($password, $genSalt);

		// GENERATE ACTIVATION CODE
		$activCode = HashFunctions::generate_activation_code();

		// ENTER INTO DATABASE (potential PDOException here)
		$sql = "INSERT INTO accountmgr_accounts (name,username,p_hash,p_salt,reset_email,activation,date_created) VALUES (:name,:username,:p_hash,:p_salt,:email,:activation,now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("name", $name, PDO::PARAM_STR );
		$stmt->bindValue("username", $username, PDO::PARAM_STR );
		$stmt->bindValue("p_hash", $genHash, PDO::PARAM_STR );
		$stmt->bindValue("p_salt", $genSalt, PDO::PARAM_STR );
		$stmt->bindValue("email", $email, PDO::PARAM_STR );
		$stmt->bindValue("activation", $activCode, PDO::PARAM_STR );
		$stmt->execute();

		$this->last_activation_code = $activCode;
		$this->last_entry_id = $this->pdo->lastInsertId();

		return $this->last_entry_id;
	}
	// Returns ID or throws an exception
	function login_with_email($email,$password) {
		// CHECK ISSET
		if (!isset($email)) throw new AccountMgrException("unset field", AccountMgrException::POST_NO_VALUE);
		if (!isset($password)) throw new AccountMgrException("unset field", AccountMgrException::POST_NO_VALUE);
		// CHECK LENGTH
		if (
			strlen(utf8_decode($email)) < 2
			|| strlen(utf8_decode($password)) < 2
			) {
			throw new AccountMgrException("One of the fields is too short.", AccountMgrException::POST_UNDER_LIMIT);
		}
		if (
			strlen(utf8_decode($email)) > 80
			|| strlen(utf8_decode($password)) > 512
			) {
			throw new AccountMgrException("One of the fields is too long.", AccountMgrException::POST_OVER_LIMIT);
		}
	 	// Prepare SQL statement for finding the user
		$sql = "SELECT * FROM accountmgr_accounts WHERE reset_email=:email";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "email", $email, PDO::PARAM_STR );
	 	$stmt->execute();

	 	if (!( $row = $stmt->fetch(PDO::FETCH_ASSOC) )) {
	 		throw new AccountMgrException("No such account was found.", AccountMgrException::ACCOUNT_NOT_FOUND);
	 	} else {
			$this->verify_account_login($row, $password);
			$this->set_logged_in_session($row);
	 	}

	 	

	 	return $row['id'];
	}
	function force_remove_account_by_email($email) {
		// This function added last-minute

		$accid = $this->get_id_from_email($email);

		$sql = "DELETE FROM accountmgr_permissions WHERE account=:accid";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "accid", $accid, PDO::PARAM_INT );
	 	$stmt->execute();
		$sql = "DELETE FROM accountmgr_accounts WHERE reset_email=:email";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "email", $email, PDO::PARAM_STR );
	 	$stmt->execute();
	 	return;
	}
	public function attempt_account_activation($accid,$activCode) {
		$sql = "SELECT * FROM accountmgr_accounts WHERE id=:accid";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue( "accid", $accid, PDO::PARAM_INT );
		$stmt->execute();

		if (!( $row = $stmt->fetch(PDO::FETCH_ASSOC) )) {
			throw new AccountMgrException("No such account was found.", AccountMgrException::ACCOUNT_NOT_FOUND);
		} else {
			// Throw exception if past last attempt
			$this->check_attempts($row);

			if ($activCode === $row['activation'] || $row['activation'] === 'OK') {
				$this->activate_account($row);
				return true;
			} else {
				$this->log_login_attempt($row);
				return false;
			}
		}
	}

	public function attempt_pass_reset($accid, $reset_code, $password) {
		$this->ensure_pass_reset_code_matches($accid, $reset_code);
		$pSize = strlen(utf8_decode($password));
		if ($pSize < 2) {
				throw new AccountMgrException("Password is too small!", AccountMgrException::POST_UNDER_LIMIT);
		}
		if ($pSize > 512) {
				throw new AccountMgrException("Password is too large!", AccountMgrException::POST_OVER_LIMIT);
		}

		$genSalt = HashFunctions::generate_salt();
		$genHash = HashFunctions::compute_hash($password, $genSalt);

		$sql = "UPDATE accountmgr_accounts SET p_hash=:phash, p_salt=:psalt, pwd_reset='OK', attempts=0 WHERE id=:accid";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "accid", $accid,   PDO::PARAM_INT );
	 	$stmt->bindValue( "phash", $genHash, PDO::PARAM_STR );
	 	$stmt->bindValue( "psalt", $genSalt, PDO::PARAM_STR );
	 	$stmt->execute();
	}
	public function ensure_pass_reset_code_matches($accid, $reset_code) {
		$sql = "SELECT pwd_reset FROM accountmgr_accounts WHERE id=:accid";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue( "accid", $accid, PDO::PARAM_INT );
		$stmt->execute();

		if (!( $row = $stmt->fetch(PDO::FETCH_ASSOC) )) {
			throw new AccountMgrException("No such account was found.", AccountMgrException::ACCOUNT_NOT_FOUND);
		} else {
			$pwd_reset = $row['pwd_reset'];
			if ($pwd_reset == 'OK') throw new AccountMgrException("This account is not in password reset mode!", AccountMgrException::RESET_CODE_FAIL);
			if ($pwd_reset !== $reset_code) throw new AccountMgrException("The reset code used does not match the record on file!", AccountMgrException::RESET_CODE_FAIL);
		}
	}

	// Returns void or throws an exception.
	private function verify_account_login($user_row_data, $password) {
		// Check login attempts
		$this->check_attempts($user_row_data);
		$attempts = intval($user_row_data['attempts']);

		// Check password hash
		if ( ! HashFunctions::verify_password($password,$user_row_data['p_salt'],$user_row_data['p_hash']) ) {
			$this->log_login_attempt($user_row_data);
			$info = ''.(4 - $attempts).' attempts remaining!';
			if ($attempts == 4) $info = "You've exhausted all your attempts! Please try again in 15 minutes.";
			throw new AccountMgrException("The given password didn't match. ".$info, AccountMgrException::INCORRECT_PASSWORD);
		}
	}
	private function check_attempts(&$user_row_data) {
		// This function just throws an exception if the
		// user hasn't waited long enough before attempting.
		// Oh; it also modifies $user_row_data['attempts']
		$attempts = intval($user_row_data['attempts']);
		if ($attempts >= $this->max_attempts) {
			$lastTimestamp = strtotime($user_row_data['last_attempt']);
			$thisTimestamp = time();
			$difference = $thisTimestamp - $lastTimestamp;
			if ($difference < $this->login_cooldown) {
				throw new AccountMgrException("Your account is locked! You may try again within 15 minutes.", AccountMgrException::ACCOUNT_LOCKED);
			} else {
				// Will update database when log_login_attempt() is called.
				$user_row_data['attempts'] = 0;
			}
		}

	}
	private function activate_account($user_row_data) {
		$attempts = intval($user_row_data['attempts']);
		$attempts = $attempts + 1;
		$sql = "UPDATE accountmgr_accounts SET activation='OK' WHERE id=:accid";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "accid", $user_row_data['id'], PDO::PARAM_INT );
	 	$stmt->execute();
	}
	private function set_logged_in_session($user_row_data) {
		$this->reset_login_attempts($user_row_data);
	 	// Begin user session
	 	@session_start();
	 	if ($user_row_data['pwd_reset'] === "EX") $_SESSION['logged_in'] = self::SESSION_NEWPASS; // expired password; prompt for reset
	 	else $_SESSION['logged_in'] = self::SESSION_OKAY;
	 	// Load some info in server memory while we're here
	 	$_SESSION['account']['email'] = $user_row_data['reset_email'];
	 	$_SESSION['account']['name'] = $user_row_data['name'];
	 	$_SESSION['account']['id'] = $user_row_data['id'];
	 	$_SESSION['account']['username'] = $user_row_data['username'];
	}
	private function reset_login_attempts($user_row_data) {
		$sql = "UPDATE accountmgr_accounts SET attempts=:attempts WHERE id=:accid";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "accid",    $user_row_data['id'], PDO::PARAM_INT );
	 	$stmt->bindValue( "attempts", "0", PDO::PARAM_INT );
	 	$stmt->execute();
	 	ob_start();
	 	print_r($user_row_data);
	 	$info = ob_get_clean();
	 	file_put_contents(SITE_PATH."/log.txt",$info);
	}
	private function log_login_attempt($user_row_data) {
		$attempts = intval($user_row_data['attempts']);
		$attempts = $attempts + 1;

		$sql = "UPDATE accountmgr_accounts SET attempts=:attempts, last_attempt=now() WHERE id=:accid";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "accid", $user_row_data['id'], PDO::PARAM_INT );
	 	$stmt->bindValue( "attempts", $attempts, PDO::PARAM_INT );
	 	$stmt->execute();
	}
	function add_permission($username, $permission) {
		// CHECK ISSET
		if (!isset($username)) throw new AccountMgrException("unset field", AccountMgrException::POST_NO_VALUE);
		if (!isset($permission)) throw new AccountMgrException("unset field", AccountMgrException::POST_NO_VALUE);
		// CHECK LENGTH
		if (
			strlen(utf8_decode($username)) < 2
			|| strlen(utf8_decode($permission)) < 2
			) {
			throw new AccountMgrException("One of the fields is too short.", AccountMgrException::POST_UNDER_LIMIT);
		}
		if (
			strlen(utf8_decode($username)) > 40
			|| strlen(utf8_decode($permission)) > 40
			) {
			throw new AccountMgrException("One of the fields is too long.", AccountMgrException::POST_OVER_LIMIT);
		}
		// CHECK FORMAT
		$FV_alphanumeric = new FieldValidator();
		$FV_alphanumeric->set_filter_setting('default_preset','alphanum'); // Allow A-z0-9_
		if ( !$FV_alphanumeric->verify($username) ) {
			throw new AccountMgrException("Invalid username value.", AccountMgrException::POST_VARIABLE_INVALID);
		}
		$FV_permission = new FieldValidator();
		$FV_permission->set_filter_setting('default_preset','permission'); // Allow A-z0-9_
		if ( !$FV_permission->verify($permission) ) {
			throw new AccountMgrException("Invalid permission value.", AccountMgrException::POST_VARIABLE_INVALID);
		}
		// CHECK IF USERNAME EXISTS
		$account_id = $this->get_id_from_username($username); // throws ACCOUNT_NOT_FOUND

		// ENTER INTO DATABASE (potential PDOException here)
		try {
			$sql = "INSERT INTO accountmgr_permissions (account,permission) VALUES (:accid,:permission)";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("accid", $account_id, PDO::PARAM_STR );
			$stmt->bindValue("permission", $permission, PDO::PARAM_STR );
			$stmt->execute();
			$this->last_entry_id = $this->pdo->lastInsertId();
		} catch (PDOException $e) {
			if ($e->getCode() == 1062) {
				//   Duplicate entry wasn't added. This is okay.
			} else {
				throw $e;
			}
		}

	}
	function rm_permission($username, $permission) {
		// CHECK ISSET
		if (!isset($username)) throw new AccountMgrException("unset field", AccountMgrException::POST_NO_VALUE);
		if (!isset($permission)) throw new AccountMgrException("unset field", AccountMgrException::POST_NO_VALUE);
		// CHECK LENGTH
		if (
			strlen(utf8_decode($username)) < 2
			|| strlen(utf8_decode($permission)) < 2
			) {
			throw new AccountMgrException("One of the fields is too short.", AccountMgrException::POST_UNDER_LIMIT);
		}
		if (
			strlen(utf8_decode($username)) > 40
			|| strlen(utf8_decode($permission)) > 40
			) {
			throw new AccountMgrException("One of the fields is too long.", AccountMgrException::POST_OVER_LIMIT);
		}
		// CHECK FORMAT
		$FV_alphanumeric = new FieldValidator();
		$FV_alphanumeric->set_filter_setting('default_preset','alphanum'); // Allow A-z0-9_
		if ( !$FV_alphanumeric->verify($username) ) {
			throw new AccountMgrException("Invalid username value.", AccountMgrException::POST_VARIABLE_INVALID);
		}
		$FV_permission = new FieldValidator();
		$FV_permission->set_filter_setting('default_preset','permission'); // Allow A-z0-9_
		if ( !$FV_permission->verify($permission) ) {
			throw new AccountMgrException("Invalid permission value.", AccountMgrException::POST_VARIABLE_INVALID);
		}
		// CHECK IF USERNAME EXISTS
		$account_id = $this->get_id_from_username($username); // throws ACCOUNT_NOT_FOUND

		// REMOVE FROM DATABASE (potential PDOException here)
		$sql = "DELETE FROM accountmgr_permissions WHERE account=:accid AND permission=:permission)";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("accid", $account_id, PDO::PARAM_STR );
		$stmt->bindValue("permission", $permission, PDO::PARAM_STR );
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();

	}
	function revoke_all_permissions($email) {
		// CHECK IF USERNAME EXISTS
		$account_id = $this->get_id_from_email($email); // throws ACCOUNT_NOT_FOUND

		// REMOVE FROM DATABASE (potential PDOException here)
		$sql = "DELETE FROM accountmgr_permissions WHERE account=:accid";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("accid", intval($account_id), PDO::PARAM_INT );
		$stmt->execute();
		return;
	}
	function list_all_the_accounts() {
		$dataToReturn = array();

		$sql = "SELECT id,name,username,reset_email,pwd_reset FROM accountmgr_accounts";

	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->execute();
	 	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		if (!is_numeric($row['id']))
	 			throw new AccountMgrException("row id not numeric",AccountMgrException::GENERIC_INTERNAL_ERROR);
	 		$sql = "SELECT account,permission FROM accountmgr_permissions WHERE account=:accid";
		 	$stmt2 = $this->pdo->prepare($sql);
		 	$stmt2->bindValue( "accid", $row['id'], PDO::PARAM_STR );
		 	$stmt2->execute();
		 	$permRow = '';
		 	$row['permissions'] = array();
		 	while ( $permRow = $stmt2->fetch(PDO::FETCH_ASSOC) ) {
		 		$row['permissions'][] = $permRow['permission'];
		 	}
		 	$dataToReturn[] = $row;
	 	}
	 	return $dataToReturn;
	}
	function gen_pass_reset_code($email) {
		$activCode = HashFunctions::generate_activation_code();
		$sql = "UPDATE accountmgr_accounts SET pwd_reset=:code WHERE reset_email=:email";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "email", $email, PDO::PARAM_STR );
	 	$stmt->bindValue( "code", $activCode, PDO::PARAM_STR );
	 	$stmt->execute();
	 	return $activCode;
	}
	function check_email_exists($email) {
		$sql = "SELECT * FROM accountmgr_accounts WHERE reset_email=:email";

	 	$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("email", $email, PDO::PARAM_STR );
	 	$stmt->execute();
	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		return true;
	 	}
	 	return false;
	}
	function ensure_email_exists($email) {
		$sql = "SELECT * FROM accountmgr_accounts WHERE reset_email=:email";

	 	$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("email", $email, PDO::PARAM_STR );
	 	$stmt->execute();
	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		return $row['id'];
	 	} else {
	 		throw new AccountMgrException("No such account was found.", AccountMgrException::ACCOUNT_NOT_FOUND);
	 	}
	}
	function get_account_info($account_id) {

		$dataToReturn = array();

		$sql = "SELECT name,username,reset_email,pwd_reset FROM accountmgr_accounts WHERE id=:id";

	 	$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id", $account_id, PDO::PARAM_STR );
	 	$stmt->execute();
	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		if (!is_numeric($account_id))
	 			throw new AccountMgrException("row id not numeric",AccountMgrException::GENERIC_INTERNAL_ERROR);
	 		$sql = "SELECT account,permission FROM accountmgr_permissions WHERE account=:accid";
		 	$stmt2 = $this->pdo->prepare($sql);
		 	$stmt2->bindValue( "accid", $account_id, PDO::PARAM_STR );
		 	$stmt2->execute();
		 	$permRow = '';
		 	$row['permissions'] = array();
		 	while ( $permRow = $stmt2->fetch(PDO::FETCH_ASSOC) ) {
		 		$row['permissions'][] = $permRow['permission'];
		 	}
		 	$row['permissions'][] = "site.eggsaladsandwich";
		 	$dataToReturn = $row;
	 	} else {
	 		throw new AccountMgrException("Couldn't fetch an account after obtaining id. The account might have been deleted in the midst of this operation.",AccountMgrException::ACCOUNT_NOT_FOUND);
	 	}
	 	return $dataToReturn;
	}



	function get_last_entry_id() {
		return $this->last_entry_id;
	}
	function get_last_activation_code() {
		return $this->last_activation_code;
	}

	private function ensure_unique_username($username) {
		$sql = "SELECT id FROM accountmgr_accounts WHERE username=:username";

	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "username", $username, PDO::PARAM_STR );
	 	$stmt->execute();
	 	$row = '';
	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		return false;
	 	} else {
	 		return true;
	 	}
	}
	private function ensure_unique_email($email) {
		$sql = "SELECT id FROM accountmgr_accounts WHERE reset_email=:email";

	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "email", $email, PDO::PARAM_STR );
	 	$stmt->execute();
	 	$row = '';
	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		return false;
	 	} else {
	 		return true;
	 	}
	}
	private function get_id_from_username($username) {
		$sql = "SELECT id FROM accountmgr_accounts WHERE username=:username";

	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "username", $username, PDO::PARAM_STR );
	 	$stmt->execute();
	 	$row = '';
	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		if (!is_numeric($row['id']))
	 			throw new AccountMgrException("row id not numeric",AccountMgrException::GENERIC_INTERNAL_ERROR);
	 		return $row['id'];
	 	} else {
	 		throw new AccountMgrException("Failed to get id from username; account must not exist.",AccountMgrException::ACCOUNT_NOT_FOUND);
	 	}
	}
	private function get_id_from_email($email) {
		$sql = "SELECT id FROM accountmgr_accounts WHERE reset_email=:email";

	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "email", $email, PDO::PARAM_STR );
	 	$stmt->execute();
	 	$row = '';
	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		if (!is_numeric($row['id']))
	 			throw new AccountMgrException("row id not numeric",AccountMgrException::GENERIC_INTERNAL_ERROR);
	 		return $row['id'];
	 	} else {
	 		throw new AccountMgrException("Failed to get id from username; account must not exist.",AccountMgrException::ACCOUNT_NOT_FOUND);
	 	}
	}
}
