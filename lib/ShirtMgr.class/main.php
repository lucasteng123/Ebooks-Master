<?php

// Something like a private class
class TShirt {
	// Constructed using a row from the database
	function __construct($row) {
		$this->row = $row;
	}
	function get_id() {
		return $this->row['id'];
	}
	function get_title() {
		return htmlspecialchars($this->row['title']);
	}
	function get_img_src() {
		$imageSrc = WEB_PATH.'/uploads/img/'.$this->row['filename'];
		return $imageSrc;
	}
	function get_html_no_tags() {
		$text = htmlspecialchars($this->row['contents']);
		$text = nl2br($text);

		return $text;
	}
	function get_html() {
		$text = $this->get_html_no_tags();

		// Search for [url] tags
		{
			$match = '/\[url=(.*)\](.*)\[\/url\]/';
			$text = preg_replace($match, '<a href="$1">$2</a>', $text);
		}
		// Search for [img] tags
		{
			$imageSrc = WEB_PATH.'/uploads/img/'.$this->row['filename'];
			$match = '/\[img\](.*)\[\/img\]/';
			$text = preg_replace($match, '<img src="'.$imageSrc.'" title="$1" />', $text);
		}

		return $text;
	}
	function get_text() {
		$text = $this->row['contents'];

		// Search for [url] tags
		{
			$match = '/\[url=(.*)\](.*)\[\/url\]/';
			$text = preg_replace($match, '', $text);
		}
		// Search for [img] tags
		{
			$imageSrc = WEB_PATH.'/uploads/img/'.$this->row['filename'];
			$match = '/\[img\](.*)\[\/img\]/';
			$text = preg_replace($match, '', $text);
		}

		return $text;
	}
}

class ShirtMgr {
	private $pdo = null;
	private $wd = null;

	private $last_entry_id = null;

	public $debug_buffer = array();

	const COMMENT_AWAITING_APPROVAL = 0;
	const COMMENT_APPROVED = 1;
	const COMMENT_DECLINED = 2;
	const COMMENT_REMOVED = 3;

	function __construct($pdo) {
		if ( !(gettype($pdo) === 'object') ) {
			FrameworkException::throw_datatype_exception(VarTools::what_is($pdo), 'PDO');
		}
		$this->pdo = $pdo;
		$this->wd = realpath(dirname(__FILE__));
	}
	function setup() {
		$sql = file_get_contents($this->wd . "/table_creation.sql");
		$qr = $this->pdo->exec($sql);
	}
	function destroy_records() {
		$sql = file_get_contents($this->wd . "/table_deletion.sql");
		$qr = $this->pdo->exec($sql);
	}

	function get_tshirt_list() {
		// Get tools
		$pdo = $this->pdo;
		$sql = "SELECT * FROM tshirts";
		// Prepare statement
		$stmt = $pdo->prepare( $sql );
		// Bind values
		$stmt->execute();
		// Fetch results into associative array
		$result = array();
		while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
			$result[] = $row;
		}
		return $result;
	}

	// === TICKER MESSAGES ===
	function insert_tshirt($title, $contents, $imageID) {
		$sql = "INSERT INTO tshirts (title, contents, image_id, date_posted) VALUES (:title, :contents, :image_id, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("title",    $title,    PDO::PARAM_STR );
		$stmt->bindValue("contents", $contents, PDO::PARAM_STR );
		$stmt->bindValue("image_id", $imageID,  PDO::PARAM_INT );
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();

		return $this->last_entry_id;
	}
	function update_tshirt($title, $contents, $imageID, $id) {
		$sql = "UPDATE tshirts SET title=:title, contents=:contents, image_id=:image_id WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("title",    $title,    PDO::PARAM_STR );
		$stmt->bindValue("contents", $contents, PDO::PARAM_STR );
		$stmt->bindValue("image_id", $imageID,  PDO::PARAM_INT );
		$stmt->bindValue("id", $id, PDO::PARAM_INT );
		$stmt->execute();
	}
	function remove_tshirt($id) {
		$sql = "DELETE FROM tshirts WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id", $id, PDO::PARAM_INT );
		$stmt->execute();
	}
	function list_tshirts() {
		$dataToReturn = array();

		$sql = "SELECT p.*, i.filename FROM blog_posts p
		LEFT JOIN uploaded_images i ON p.image_id = i.id
		ORDER BY date_posted DESC";
	 	$posts_statement = $this->pdo->prepare($sql);
	 	$posts_statement->execute();

		$returnValue = array();
	 	while ( $row = $posts_statement->fetch(PDO::FETCH_ASSOC) ) {

	 		// Create TShirt object form row
	 		$shirt = new TShirt($row);

	 		// Add TShirt object to array
	 		$returnValue[] = $shirt;
	 	}

	 	// Return array of TShirt objects
	 	return $returnValue;
	}
	function get_single_tshirt($postID) {
		$dataToReturn = array();

		$sql = "SELECT p.*, i.filename FROM blog_posts p
		LEFT JOIN uploaded_images i ON p.image_id = i.id
		WHERE p.id = :postid";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue("postid", $postID, PDO::PARAM_INT );
	 	$stmt->execute();

	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

	 		// Create TShirt object form row
	 		$blogPost = new TShirt($row);

	 		// Return TShirt object
	 		return $blogPost;
	 	} else {
	 		// Return false if post not found
	 		return false;
	 	}
	}
}
