<?php // These aren't the functions you're looking for.

class DBConnectionManager {
	private $error;
	private $lastException;
	private $con;
	private $config;

	function __construct($config) {
		if (!$config instanceof Configurator) {
			throw new FrameworkException (
				"DBConnectionManager received an invalid configurator.",
				FrameworkException::CONFIG_INVALID
				);
		}
		if (!$config->has_properties(array('user','pass','host','schema'))) {
			throw new FrameworkException (
				"DBConnectionManager is missing database login info.",
				FrameworkException::CONFIG_MISSING_KEY
				);
		}
		$this->config = $config;
	}

	function unset_sql_mode($con) {
		$sql = "SET sql_mode = '';";
		$stmt = $con->prepare($sql);
		$stmt->execute();
		if ($stmt->errorCode() != 0) {
			$msg = $errors[2];
			trigger_error("Failed to unset sql mode: ".var_dump($errors));
		}
	}

	function getError() {
		return $this->lastException;
	}
	function get_connection() {
		if ($this->con instanceof PDO) {
			// $this->unset_sql_mode($this->con);
			return $this->con;
		}

		try {
			$this->con = $this->connect();
			$this->unset_sql_mode($this->con);
			return $this->con;
		} catch (PDOException $e) {
			$this->lastException = $e; //catch and show the error
			$this->error = $e->getCode();
			throw $e;
		}

	}

	function getConnection() {
		call_user_func_array($this->get_connection, func_get_args());
	}

	private function connect() {
		$config = $this->config;
		$dbDsn = "mysql:host=".$config->get_property('host').";dbname=".$config->get_property('schema');
		$con = new PDO( $dbDsn, $config->get_property('user'), $config->get_property('pass') );
		$con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		return $con;
	}
}

?>
