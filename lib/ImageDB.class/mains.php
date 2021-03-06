<?php

/*
	SECURITY CHECKLIST

	THIS CLASS:
		-> Valid $_FILE
		-> File MIME type
		-> File size
		INTENTION
			-> All security related to file binary

	API USER:
		-> Valid $friendly_name value
		-> Valid $files_key value
*/

class ImageDBException extends Exception {
	const ERR_FILES_NOT_SET = 1;
	const ERR_FILE_TOO_LARGE = 2;
	const ERR_UPLOAD_ERR = 3;
	const ERR_FILE_INVALID_MIME = 4;
	const ERR_INTERNAL = 4;
	const ERR_INTERNAL_MOVE = 4.1;
}

class ImageDB {
	private $pdo = null;
	private $wd = null;

	private $last_upload_error = 0;
	private $size_limit = 2097152; // 2 MB
	private $upld_location = ""; // set in constructor
	private $last_entry_id = null;

	const UPLOAD_OKAY = 0;
	const ERR_FILES_NOT_SET = 1;
	const ERR_FILE_TOO_LARGE = 2;
	const ERR_UPLOAD_ERR = 3;
	const ERR_FILE_INVALID_MIME = 4;
	const ERR_INTERNAL = 4;
	const ERR_INTERNAL_MOVE = 4.1;

	function __construct($pdo,$upld_location = null) {
		$this->upld_location = SITE_PATH.'/uploads/img';
		if ($upld_location != null) $this->upld_location = $upld_location;
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
			trigger_error("Error during ImageDB setup: ".var_dump($errors));
		}
	}
	function reset_all_tables() { // erases all table contents!
		$stmt = $this->pdo->prepare("DELETE FROM uploaded_images");
		$stmt->execute();
	}

	function set_size_limit($new_limit) {
		$this->size_limit = $new_limit;
	}
	function add_from_post_request($files_key,$friendly_name,$image_feed) {
		// Check for valid file
		if (!isset($_FILES[$files_key]['error']) || is_array($_FILES[$files_key]['error'])) {
			throw new ImageDBException("No file was sent.",ImageDBException::ERR_FILES_NOT_SET);
		}
		if (! $_FILES[$files_key]['error'] == UPLOAD_ERR_OK /*PHP constant*/) {
			$last_upload_error = $_FILES[$files_key]['error'];
			throw new ImageDBException("An error occured while uploading the file.",ImageDBException::ERR_UPLOAD_ERR);
		}
		if ($_FILES[$files_key]['size'] > $this->size_limit) {
			throw new ImageDBException("The image sent is too large. Resize the image so that it's under the maximum size (".$this->size_limit/(1024*1024)."MB)",ImageDBException::ERR_FILE_TOO_LARGE);
		}
		// Vars for check for file MIME type
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$valid_image_types = array(
			'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
		);
		// Set extension and check MIME type
		if (false === $file_extension = array_search(
			$finfo->file($_FILES[$files_key]['tmp_name']),
			$valid_image_types, true)) {

			throw new ImageDBException("The file send is not in a permitted format. Make sure the image is saved in one of these formats: JPEG (.jpg), PNG (.png), GIF (.gif).",ImageDBException::ERR_FILE_INVALID_MIME);
		}
		// File name: '??????????.ext'
		$gen_file_name = uniqid("upld_").'.'.$file_extension;
		// File name: SITE_PATH.'/uploads/img/??????????.ext'
		$gen_file_path = $this->upld_location.'/'.$gen_file_name;
		// Move file (or return with an error)
		if (!move_uploaded_file($_FILES[$files_key]['tmp_name'], $gen_file_path)) {
			throw new ImageDBException("An internal error occured while moving the file.",ImageDBException::ERR_INTERNAL_MOVE);
		}

		// Yay!
		return $this->insert_image_directly($friendly_name,$gen_file_name,$image_feed);
	}
	function insert_image_directly($friendly_name,$file_name,$image_feed) {
		// No security checks; valid data must be passed to this method.

		// Register the new image in the database
		$sql = "INSERT INTO uploaded_images (filename, friendly_name, image_feed, date_uploaded) VALUES (:file, :name, :feed, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("name", $friendly_name, PDO::PARAM_STR );
		$stmt->bindValue("file", $file_name, PDO::PARAM_STR );
		$stmt->bindValue("feed", $image_feed, PDO::PARAM_STR );
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();

		return $this->last_entry_id;
	}
	function get_image_by_id($id) {
		// No security checks; valid data must be passed to this method.

		$dataToReturn = array();

		$sql = "SELECT * FROM uploaded_images WHERE id=:id";
		
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id", $id, PDO::PARAM_INT );
	 	$stmt->execute();

	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		return $row;
	 	}
	}
	function list_all_the_images() {
		$dataToReturn = array();

		$sql = "SELECT * FROM uploaded_images";

	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->execute();

		$returnValue = array();
	 	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		$returnValue[$row['image_feed']][] = $row;
	 	}
	 	return $returnValue;
	}
	function get_last_upload_error() {
		return $last_upload_error;
	}
	function get_last_entry_id() {
		return $this->last_entry_id;
	}
}
