<?
// AS FAR AS I KNOW, this class won't actually work, because it doesn't have the other required classes available to it.
// 
class AccountManagerException extends Exception {
	const INTERNAL_ERROR = 0;
	private $errorFields;
	private $n;
	private $msg;

	public function __construct($code = self::INTERNAL_ERROR, $message = '', $fields = null) {
		$this->n = $code;
		$this->msg = $message;
		parent::__construct($message, $code);
	}

	public function log() {
		//TODO
		print("ERROR: ".$this->msg);
	}
}

class AccountManager {
	// Returns for any
	const SUCCESS = 0;
	const INVALID_FIELDS = 1;
	const EMPTY_FIELDS = 2;
	const INTERNAL_ERROR = 3;
	const ACCOUNT_EXIST = 4;
	// Returns for login
	const NOT_ACTIVATED = 20;
	const PASSWORD = 21;

	private $fields = false;
	private $errFields = false;
	public function getErrFields() {
		return $this->errFields;
	}

	public function setFields($fields) {
		$this->fields = $fields;
	}

	public function registerAccount() {
		try {
			$reqFields = array('email','pass','name');
			if (!$this->checkForFields($reqFields)) return self::EMPTY_FIELDS;

			// Check Valid
			$this->errFields = array();
			if (!Validator::chkField($this->fields['email'],"email"))
				$this->errFields[] = 'email';
			if (!Validator::chkField($this->fields['pass'],"default","{6,256}"))
				$this->errFields[] = 'pass';
			if (!Validator::chkField($this->fields['name'],"name-lang","{2,128}"))
				$this->errFields[] = 'name';
			if (count($this->errFields) > 0) {
				return self::INVALID_FIELDS;
			}

			$con = DBConnectionManager::getConnection();

			// Hash pass
			$genSalt = HashFunctions::generateSalt();
			$genHash = HashFunctions::getHash($this->fields['pass'], $genSalt);
			unset($this->fields['pass']);

			if ($this->accountExists()) { // accountExists() uses $this->fields['email'] unless a string is passed
				return self::ACCOUNT_EXIST;
			}

			// Unset optional fields will be set to a default before this point
			
			// Create a profile for the account
			$sql = "INSERT INTO profiles (modified) VALUES (now())";
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$profileID = $con->lastInsertId();

			// Create account
			$sql = "INSERT INTO accounts (email,name,salt,hash,profile,created) VALUES (:email, :name, :salt, :hash, :profile, now())";
			$stmt = $con->prepare($sql);

			$stmt->bindValue( "email", $this->fields['email'], PDO::PARAM_STR );
			$stmt->bindValue( "name",  $this->fields['name'],  PDO::PARAM_STR );
			$stmt->bindValue( "salt",  $genSalt, PDO::PARAM_STR );
			$stmt->bindValue( "hash",  $genHash, PDO::PARAM_STR );
			$stmt->bindValue( "profile",  $profileID, PDO::PARAM_INT );
			$stmt->execute();
			$accountID = $con->lastInsertId();

			// Create user's first feed
			// TODO: ContentAdders should be instantiable
			ContentAdders::addNewFeed($accountID,"Default Feed",DBConstants::SIMPLE_FEED);
			$feedID = ContentAdders::getLastID();

			// Update profile with new feed as the user's default feed
			$sql = "UPDATE profiles SET default_feed=:feed WHERE id=:profile";
			$stmt = $con->prepare($sql);
			$stmt->bindValue( "feed",  $feedID, PDO::PARAM_INT );
			$stmt->bindValue( "profile",  $profileID, PDO::PARAM_INT );
			$stmt->execute();
			
	 		return self::SUCCESS;
	 	} catch (PDOException $e) {
	 		return self::INTERNAL_ERROR;
	 	} catch (Exception $e) {
	 		return self::INTERNAL_ERROR;
	 	}
	}

	public function attemptLogin() {
		try {
			// Check Required
			$reqFields = array('email','pass');
			if (!$this->checkForFields($reqFields)) return self::EMPTY_FIELDS;

			// Check Valid
			$this->errFields = array();
			if (!Validator::chkField($this->fields['email'],"email"))
				$this->errFields[] = 'email';
			if (!Validator::chkField($this->fields['pass'],"default","{6,256}"))
				$this->errFields[] = 'pass';
			if (count($this->errFields) > 0) {
				return self::INVALID_FIELDS;
			}

			// Set vars
			$reqEmail = $this->fields['email'];
			$reqPass = $this->fields['pass'];

			// Get Connection
			$con = DBConnectionManager::getConnection();
			$sql = "SELECT salt, hash, id, email, name, alias FROM accounts WHERE email=:email";
		 	// Prepare SQL statement for finding the user
		 	$stmt = $con->prepare($sql);
		 	$stmt->bindValue( "email", $reqEmail, PDO::PARAM_STR );
		 	$stmt->execute();

		 	if (!( $row = $stmt->fetch(PDO::FETCH_ASSOC) )) {
		 		return self::ACCOUNT_EXIST; // Account doesn't exist
		 	}
		 	// Declare some things
		 	$salt = $row['salt'];
		 	$hash = $row['hash'];
		 	
		 	$ui = array();
		 	$ui['id'] = $row['id'];
		 	$ui['email'] = $row['email'];
		 	$ui['name'] = $row['name'];
		 	$ui['alias'] = $row['alias'];

		 	// Check that hash and salt are valid
		 	if (empty($hash) || empty($salt)) return self::INTERNAL_ERROR;

		 	// Hash pass
			$requestHash = HashFunctions::getHash($reqPass, $salt);
			unset($this->fields['pass']);

			if ($hash === $requestHash) {
				SessionManager::setSession($ui);
				return self::SUCCESS;
			} else {
				return self::PASSWORD;
			}
		} catch (PDOException $e) {
			// todo: log error
			return INTERNAL_ERROR;
		} catch (Exception $e) {
			// todo: log error
			return INTERNAL_ERROR;
		}
	}


	// ==== My privates are down here ====
	private function checkForFields($names) {
		foreach ($names as $name)
			if (!array_key_exists($name, $this->fields)) return false;
		return true;
	}
	// throws PDOException
	private function accountExists($email = false) {
		if ($email == false) $email = $this->fields['email'];
		// Get Connection
		$con = DBConnectionManager::getConnection();
		$sql = "SELECT COUNT(*) FROM accounts WHERE email=:email";
		$stmt = $con->prepare( $sql );
		$stmt->bindValue( "email", $email, PDO::PARAM_STR );
	 	$stmt->execute();
	 	$exists = $stmt->fetchColumn();

	 	return $exists;
	}
}

/*
class RegisterException extends Exception {
	const INTERNAL_ERROR = 0;
	const ACCOUNT_EXIST = 1;
	private $errorFields;
	private $n;
	private $msg;

	public function __construct($code, $message, $fields = null) {
		$this->n = $code;
		$this->msg = $message;
		parent::__construct($message, $code);
	}

	public function log() {
		//TODO
		print("ERROR: ".$this->msg);
	}
}

class RegisterSubmit {

	// Return values:
	public static const INTERNAL_ERROR = 0;

	private $fields = array();
	//$generated = array();
	private $mysqli = null;

	private function chkFields() {
		// chkField(<input>,<filter>,<range (regex-style)>)
		if (!Validator::chkField($this->fields['email'],"email"))
			return false;
		if (!Validator::chkField($this->fields['pass'],"default","{6,256}"))
			return false;
		if (!Validator::chkField($this->fields['name'],"name-lang","{2,128}"))
			return false;
		return true;
	}

	private function prepareData() {
		// Get db connection
		$con = SqlFunctions::connect();
		if ($con[1]) {
			// TODO: Handle error
			die($con[1]);
		}
		$this->mysqli = $con[0];

		// Hash pass
		$this->fields['salt'] = HashFunctions::generateSalt();
		$this->fields['hash'] = HashFunctions::getHash($this->fields['pass'], $this->fields['salt']);
		unset($this->fields['pass']);
	}

	private function accountOkay() {
		$sql = "SELECT email FROM accounts WHERE email=?";
		$mysqli = $this->mysqli;
	 	// Prepare SQL statement for finding the user
	 	if (!($stmt = $mysqli->prepare($sql))) {
	 		// TODO: Log error here
	 		$mysqli->close();
	 		throw new RegisterException(0,$mysqli->error);
	 	}
	 	// Bind email to query
	 	if ( !($stmt->bind_param("s", $data['email'])) ) {
	 		// TODO: Log error here
	 		$mysqli->close();
	 		throw new RegisterException(0,$mysqli->error);
	 	}
	 	// Execute and store result. Oh wait, that's exactly what it says...
	 	if (!$stmt->execute()) {
	 		// TODO: Log error here
	 		$mysqli->close();
	 		throw new RegisterException(0,$mysqli->error);
	 	}
	 	$stmt->store_result();
	 	// Check if user exists
	 	if ( $stmt->num_rows ) {
	 		$stmt->close(); $mysqli->close();
	 		return false;
	 	}
	 	return true;
	}

	private function submitToDatabase() {
		$mysqli = $this->mysqli;

		$fieldNames=array(); foreach ($this->fields as $key => $value) $fieldNames[] = $key;
		$qMarks=array(); foreach ($this->fields as $value) $qMarks[] = "?";

		// Unset optional fields will be set to a default in prepareData()
		$sql = "INSERT INTO accounts (email,name,salt,hash,created) VALUES (?, ?, ?, ?, now())"; // Add automatic fields
		if (!($stmt = $mysqli->prepare($sql))) {
			throw new RegisterException(0,$mysqli->error);
		}
		
		if ( !($stmt->bind_param("ssss",
			$this->fields['email'],
			$this->fields['name'],
			$this->fields['salt'],
			$this->fields['hash']
			)) ) {
	 		// TODO
	 		$mysqli->close();
	 		throw new RegisterException(0,$mysqli->error);
		}

		if (!$stmt->execute()) {
	 		// TODO: Log error here
	 		$mysqli->close();
	 		throw new RegisterException(0,$mysqli->error);
	 	}
	}


	public function run() {
		foreach (array('email','pass','name') as $field)
			$this->fields[$field] = array_key_exists($field, $_POST) ? $_POST[$field] : "";

		try {
			if ($this->chkFields()) {
				$this->prepareData();
				if ($this->accountOkay()) {
					$this->submitToDatabase();
				} else {
					// TODO
					throw new RegisterException(1,"exists");
				}
				
			} else {
				// TODO
				throw new RegisterException(1,"invalid fields");
			}
		} catch (RegisterException $e) {
			$e->log();
		}
	}
}
*/