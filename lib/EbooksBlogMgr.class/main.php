<?php

// Something like a private class
class EbooksBlogPost {
	// Constructed using a row from the database
	function __construct($row,$comments) {
		$this->row = $row;
		$this->comments = $comments;
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
	function get_comments() {
		return $comments;
	}
}
// Something like a private class
class EbooksBlogComment {
	function __construct($row) {
		$this->row = $row;
	}
	function get_author() {
		return "Anonymous";
	}
	function get_html() {
		$text = htmlspecialchars($row['contents']);
		$text = nl2br($text);

		return $text;
	}
}

class EbooksBlogMgr {
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
		$stmt = $this->pdo->prepare("DROP TABLE IF EXISTS blog_comments");
		$stmt->execute();
		
		$stmt = $this->pdo->prepare("DROP TABLE IF EXISTS blog_posts");
		$stmt->execute();
	}

	// === TICKER MESSAGES ===
	function insert_blog_post($title, $contents, $imageID) {
		$sql = "INSERT INTO blog_posts (title, contents, image_id, date_posted) VALUES (:title, :contents, :image_id, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("title",    $title,    PDO::PARAM_STR );
		$stmt->bindValue("contents", $contents, PDO::PARAM_STR );
		$stmt->bindValue("image_id", $imageID,  PDO::PARAM_INT );
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();

		return $this->last_entry_id;
	}
	function insert_comment($contents, $postID, $uploaderID) {
		$sql = "INSERT INTO blog_comments (contents, post_id, uploader_id, approval_status, date_posted) VALUES (:contents, :post_id, :uploader_id, :approval_status, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("contents",    $contents,   PDO::PARAM_STR );
		$stmt->bindValue("post_id",     $postID,     PDO::PARAM_INT );
		$stmt->bindValue("uploader_id", $uploaderID, PDO::PARAM_INT );
		$stmt->bindValue("approval_status", EbooksBlogMgr::COMMENT_AWAITING_APPROVAL, PDO::PARAM_INT );
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();

		return $this->last_entry_id;
	}
	function update_comment_state($commentID, $state) {
		$sql = "UPDATE blog_comments SET approval_status=:approval_status WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id", $commentID, PDO::PARAM_INT );
		$stmt->bindValue("approval_status", $state, PDO::PARAM_INT );
		$stmt->execute();
	}
	function update_blog_post($title, $contents, $imageID, $id) {
		$sql = "UPDATE blog_posts SET title=:title, contents=:contents, image_id=:image_id WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("title",    $title,    PDO::PARAM_STR );
		$stmt->bindValue("contents", $contents, PDO::PARAM_STR );
		$stmt->bindValue("image_id", $imageID,  PDO::PARAM_INT );
		$stmt->bindValue("id", $id, PDO::PARAM_INT );
		$stmt->execute();
	}
	function remove_blog_post($id) {
		$sql = "DELETE FROM blog_comments WHERE post_id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id", $id, PDO::PARAM_INT );
		$stmt->execute();
		$sql = "DELETE FROM blog_posts WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id", $id, PDO::PARAM_INT );
		$stmt->execute();
	}
	function list_blog_posts() {
		$dataToReturn = array();

		$sql = "SELECT p.*, i.filename FROM blog_posts p
		LEFT JOIN uploaded_images i ON p.image_id = i.id
		ORDER BY date_posted DESC";
	 	$posts_statement = $this->pdo->prepare($sql);
	 	$posts_statement->execute();

		$returnValue = array();
	 	while ( $row = $posts_statement->fetch(PDO::FETCH_ASSOC) ) {

	 		// Query comments on this post
	 		$sql = "SELECT * FROM blog_comments
	 		WHERE post_id = :post_id
	 		ORDER BY date_posted ASC";
	 		// -- prepare
		 	$comments_statement = $this->pdo->prepare($sql);
	 		// --- bind post id and execute
	 		$postID = $row['id'];
	 		$comments_statement->bindValue("post_id", $postID, PDO::PARAM_INT );
		 	$comments_statement->execute();
		 	// -- fetch all comments
		 	$comments = $comments_statement->fetchAll(PDO::FETCH_ASSOC);

	 		// Create EbooksBlogPost object form row
	 		$blogPost = new EbooksBlogPost($row, $comments);

	 		// Add EbooksBlogPost object to array
	 		$returnValue[] = $blogPost;
	 	}

	 	// Return array of EbooksBlogPost objects
	 	return $returnValue;
	}
	function get_single_post($postID) {
		$dataToReturn = array();

		$sql = "SELECT p.*, i.filename FROM blog_posts p
		LEFT JOIN uploaded_images i ON p.image_id = i.id
		WHERE p.id = :postid";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue("postid", $postID, PDO::PARAM_INT );
	 	$stmt->execute();

	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		// Query comments on this post
	 		$sql = "SELECT * FROM blog_comments
	 		WHERE post_id = :post_id
	 		AND approval_status = :state
	 		ORDER BY date_posted ASC";
	 		// -- prepare
		 	$stmt = $this->pdo->prepare($sql);
	 		// --- bind post id and execute
	 		$postID = $row['id'];
	 		$stmt->bindValue("post_id", $postID, PDO::PARAM_INT );
	 		$stmt->bindValue("state", EbooksBlogMgr::COMMENT_APPROVED, PDO::PARAM_INT );
		 	$stmt->execute();
		 	// -- fetch all comments
		 	$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

	 		// Create EbooksBlogPost object form row
	 		$blogPost = new EbooksBlogPost($row, $comments);

	 		// Return EbooksBlogPost object
	 		return $blogPost;
	 	} else {
	 		// Return false if post not found
	 		return false;
	 	}
	}
	function get_unchecked_comments() {
		$dataToReturn = array();

		$sql = "SELECT c.*, a.name AS author FROM blog_comments c
		LEFT JOIN accountmgr_accounts a ON c.uploader_id = a.id
		WHERE approval_status = :state
		ORDER BY date_posted ASC";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue("state", EbooksBlogMgr::COMMENT_AWAITING_APPROVAL, PDO::PARAM_INT );
	 	$stmt->execute();

		$returnValue = array();
	 	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		// Add EbooksBlogPost object to array
	 		$returnValue[] = $row;
	 	}

	 	// Return array of EbooksBlogPost objects
	 	return $returnValue;
	}
	function get_post_comments($postID) {

		// Query comments on this post
		$sql = "SELECT c.*, a.name AS author FROM blog_comments c
		LEFT JOIN accountmgr_accounts a ON c.uploader_id = a.id
		WHERE c.post_id = :post_id
		AND approval_status = 1
		ORDER BY date_posted ASC";
		// -- prepare
		$stmt = $this->pdo->prepare($sql);
		// --- bind post id and execute
		$stmt->bindValue("post_id", $postID, PDO::PARAM_INT );
		$stmt->execute();
		// -- fetch all comments
		$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $comments;
	}
}
