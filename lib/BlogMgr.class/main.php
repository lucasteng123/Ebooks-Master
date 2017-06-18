<?php

/*
	FEATURES TODOLIST
		-> Add comments
*/

/*
	RETURN VALUE FORMAT
		Array( Int error [0 on success], Array() affected posts )
	Array() affected posts
		Array( String name, Int error )
*/

class BlogMgr {
	private $pdo = null;
	private $wd = null;

	private $last_entry_id = null;

	private $max_post_size = 204800; // 200KB - should be well enough for a blog entry

	private $last_exception = null;

	const OKAY = 0;
	const POST_EXCEEDED_LIMIT = 1;
	const POST_VARIABLE_INVALID = 2;
	const POST_VARIABLE_EMPTY = 3;
	const INTERNAL_ERROR = 4;

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
			trigger_error("Error during ImageDB setup: ".var_dump($errors));
		}
	}
	function ensure_feed_exists($identifier) {
		// VALIDATE
		if (is_array($identifier) || !preg_match('/^[A-Za-z0-9_]+$/',$identifier)) {
			return self::POST_VARIABLE_INVALID;
		}
		if (strlen(utf8_decode($identifier)) < 1) {
			return self::POST_VARIABLE_EMPTY;
		}
		if (strlen(utf8_decode($identifier)) > 20) {
			return self::POST_EXCEEDED_LIMIT;
		}

		if (!$this->ensure_unique_feed_indentifier($identifier)) {
			return self::OKAY; // Feed already exists; no need to create.
		}

		// ENTER
		try {
			$sql = "INSERT INTO blogmgr_feeds (identifier, name, date_created) VALUES (:identifier, :name, now())";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("identifier", $identifier, PDO::PARAM_STR );
			$stmt->bindValue("name", "Automatically Generated Feed", PDO::PARAM_STR );
			$stmt->execute();
			$this->last_entry_id = $this->pdo->lastInsertId();
		} catch (PDOException $e) {
			return self::INTERNAL_ERROR;
		}

		// YAY!
		return self::OKAY;
	}
	function get_feed_id($identifier) {
		// TODO: Make this better
		try {
			$sql = "SELECT id FROM blogmgr_feeds WHERE identifier = :identifier";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("identifier", $identifier, PDO::PARAM_STR );
			$stmt->execute();
		 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		 		return $row['id'];
		 	} else {
		 		return -1;
		 	}
		} catch (PDOException $e) {
			$this->last_exception = $e;
			return -2;
		}
		echo "unreachable point";
	}
	function new_author($name) {
		// VALIDATE
		if (is_array($name)) {
			return self::POST_VARIABLE_INVALID;
		}
		if (strlen(utf8_decode($name)) < 1) {
			return self::POST_VARIABLE_EMPTY;
		}
		if (strlen(utf8_decode($name)) > 40) {
			return self::POST_EXCEEDED_LIMIT;
		}

		// ENTER
		try {
			$sql = "INSERT INTO blogmgr_authors (name) VALUES (:name)";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("name", $name, PDO::PARAM_STR );
			$stmt->execute();
			$this->last_entry_id = $this->pdo->lastInsertId();
		} catch (PDOException $e) {
			return self::INTERNAL_ERROR;
		}

		// YAY!
		return self::OKAY;
	}
	function get_authors() {
		// TODO: Make this better
		try {
			$sql = "SELECT id,name FROM blogmgr_authors";
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute();
			$results = array();
		 	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		 		$results[] = $row;
		 	}
		 	return $results;
		} catch (PDOException $e) {
			return self::INTERNAL_ERROR;
		}
		// this pt unreachable
	}
	function get_posts_in_feed($feed_id) { // returns array(status, resultset)
		if (!is_numeric($feed_id)) {
			return array(self::POST_VARIABLE_INVALID);
		}
		if ($feed_id < 0) {
			return array(self::POST_VARIABLE_INVALID);
		}
		// TODO: Make this better
		try {
			$sql = "SELECT * FROM blogmgr_posts WHERE feed = :feed_id ORDER BY date_posted DESC";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("feed_id", $feed_id, PDO::PARAM_INT );
			$stmt->execute();
			$results = array();
		 	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		 		$results[] = $row;
		 	}
		 	return array(self::OKAY, $results);
		} catch (PDOException $e) {
			return array(self::INTERNAL_ERROR);
		}
		// this pt unreachable
	}
	function new_blog_entry($feed_id,$author_id,$title,$contents) {
		// VALIDATE
		if (!is_numeric($feed_id) || !is_numeric($author_id)) {
			return self::POST_VARIABLE_INVALID;
		}
		if (strlen(utf8_decode($title)) < 1 || strlen(utf8_decode($contents)) < 1) {
			return self::POST_VARIABLE_EMPTY;
		}
		if (strlen(utf8_decode($title) > 40)) {
			return self::POST_EXCEEDED_LIMIT;
		}
		if (strlen(utf8_decode($contents)) > $this->max_post_size) {
			return self::POST_EXCEEDED_LIMIT;
		}


		// ENTER
		try {
			$sql = "INSERT INTO blogmgr_posts (feed,title,author,contents,date_posted) VALUES (:feed,:title,:author,:contents,now())";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("feed", $feed_id, PDO::PARAM_INT );
			$stmt->bindValue("title", $title, PDO::PARAM_STR );
			$stmt->bindValue("author", $author_id, PDO::PARAM_INT );
			$stmt->bindValue("contents", $contents, PDO::PARAM_STR );
			$stmt->execute();
			$this->last_entry_id = $this->pdo->lastInsertId();
		} catch (PDOException $e) {
			$this->last_exception = $e;
			return self::INTERNAL_ERROR;
		}

		// YAY!
		return self::OKAY;
	}
	function edit_blog_entry($entry_id,$title,$contents) {
		// VALIDATE
		if (!is_numeric($entry_id)) {
			return self::POST_VARIABLE_INVALID;
		}
		if (strlen(utf8_decode($title)) < 1 || strlen(utf8_decode($contents)) < 1) {
			return self::POST_VARIABLE_EMPTY;
		}
		if (strlen(utf8_decode($title) > 40)) {
			return self::POST_EXCEEDED_LIMIT;
		}
		if (strlen(utf8_decode($contents)) > $this->max_post_size) {
			return self::POST_EXCEEDED_LIMIT;
		}


		// ENTER
		try {
			$sql = "UPDATE blogmgr_posts SET title=:title, contents=:contents, date_edited=now() WHERE id=:entry"; // The SQL update statement looks rather different from the INSERT statement. It's a tad inconvenient.
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("title", $title, PDO::PARAM_STR );
			$stmt->bindValue("contents", $contents, PDO::PARAM_STR );
			$stmt->bindValue("entry", $entry_id, PDO::PARAM_INT );
			$stmt->execute();
			$this->last_entry_id = $this->pdo->lastInsertId();
		} catch (PDOException $e) {
			$this->last_exception = $e;
			return self::INTERNAL_ERROR;
		}

		// YAY!
		return self::OKAY;
	}
	function get_last_exception() {
		return $this->last_exception;
	}
	function get_last_entry_id() {
		return $this->last_entry_id;
	}

	private function ensure_unique_feed_indentifier($identifier) {
		$sql = "SELECT id FROM blogmgr_feeds WHERE identifier=:identifier";

		// Obtain id of form
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "identifier", $identifier, PDO::PARAM_STR );
	 	$stmt->execute();
	 	$row = '';
	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		return false;
	 	} else {
	 		return true;
	 	}
	}
}
