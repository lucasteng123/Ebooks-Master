<?php

class SiteStrings {
	private $pdo = null;
	private $wd = null;
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
			trigger_error("Error during EPL setup: ".var_dump($errors));
		}
	}
	static function text_to_html($iniValue) {
		$htmlValue = htmlspecialchars($iniValue);
		$htmlValue = str_replace("\t","&nbsp;&nbsp;&nbsp;",$htmlValue);
		$htmlValue = str_replace("\n","<br />",$htmlValue);
		return $htmlValue;
	}
	// === PUBLIC FUNCTIONS
	function get_html($name,$break = "<br />") {
		$html = htmlspecialchars($this->get_value($name));
		$html = str_replace("\t","&nbsp;&nbsp;&nbsp;",$html);
		$html = str_replace("\n",$break,$html);
		return $html;
	}
	function get_input($name) {
		$retVal = htmlspecialchars($this->get_value($name));
		return $retVal;
	}
	function get_value($name) {
		$id = $this->create_post($name)[1];
		$sql = "SELECT * FROM site_strings WHERE id=:id";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "id", $id, PDO::PARAM_INT );
	 	$stmt->execute();
	 	$row = '';
	 	if (!( $row = $stmt->fetch(PDO::FETCH_ASSOC) )) {
	 		return "[Error: no row]";
	 	} else { // ALTER EXISTING PROPERTY
	 		return $row['value'];
	 	}
	}
	function set_value($name, $value) {
		$id = $this->create_post($name)[1];
 		$sql = "UPDATE site_strings SET value=:value WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue( "id", $id, PDO::PARAM_INT );
		$stmt->bindValue( "value", $value, PDO::PARAM_STR );
		$stmt->execute();
	}
	
	private function create_post($name) { // returns (str status, int post_id)
		if (!isset($name) || $name == null) {
			throw new Exception("SiteStrings was passed a null string name!");
		}
		// Same as get_post_id, but creates post if needed
		if (($id = $this->get_post_id($name)) != -1) {
			return array('exists',$id);
		} else {
			$sql = "INSERT INTO site_strings (name) VALUES (:name)";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("name", $name, PDO::PARAM_STR );
			$stmt->execute();
			$entryId = $this->pdo->lastInsertId();
			return array('created',$entryId);
		}
	}

	private function get_post_id($name) { // returns int post_id or -1
		$sql = "SELECT id FROM site_strings WHERE name=:name";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "name", $name, PDO::PARAM_STR );
	 	$stmt->execute();
	 	$row = '';
	 	if (!( $row = $stmt->fetch(PDO::FETCH_ASSOC) )) {
	 		return -1;
	 	}
	 	return $row['id'];
	}
}
