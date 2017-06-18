<?php

/*

Site Database Functions for BestEbooks.com
Written by Eric Dube' (eric.alex.dube@gmail.com)

DEPENDANCIES:
 - [SQL DEPENDANCY] ImageDB.class (by Eric)
 - [PHP DEPENDANCY] IPAddrHandler.class.php (by Eric)

*/

class SiteDBException extends Exception {
	const POST_VARIABLE_INVALID = 1;
	const POST_UNDER_LIMIT = 2;
	const POST_VARIABLE_EMPTY = 2.1;
	const POST_OVER_LIMIT = 3;
	const POST_NO_VALUE = 4;

	const API_ERROR = 500;
	
	const ERR_INTERNAL = 9001;
	const ERR_NOT_IMPLEMENTED = 9002; // an incomplete function was called
}

class SiteDB {
	private $pdo = null;
	private $wd = null;

	private $last_entry_id = null;

	private $max_post_size = 204800; // 200 KB

	public $debug_buffer = array();

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
		$sql = file_get_contents($this->wd . "/table_creation_2.sql");
		$qr = $this->pdo->exec($sql);
		$sql = file_get_contents($this->wd . "/table_creation_3.sql");
		$qr = $this->pdo->exec($sql);
		//print('<pre>'.$this->pdo->errorInfo().'</pre>');
		//$stmt->execute();
		/*
		if ($stmt->errorCode() != 0) {
			$msg = $errors[2];
			trigger_error("Error during SiteDB setup: ".var_dump($errors));
		}
		*/
	}
	function setup_newsletter() {
		$sql = file_get_contents($this->wd . "/table_creation_newsletter.sql");
		$qr = $this->pdo->exec($sql);
		//print('<pre>'.$this->pdo->errorInfo().'</pre>');
		//$stmt->execute();
		/*
		if ($stmt->errorCode() != 0) {
			$msg = $errors[2];
			trigger_error("Error during SiteDB setup: ".var_dump($errors));
		}
		*/
	}
	function destroy_records() {
		echo "REMOVE ticker_message; ";
		$stmt = $this->pdo->prepare("DROP TABLE IF EXISTS ticker_messages");
		$stmt->execute();

		echo "REMOVE book_views; ";		
		$stmt = $this->pdo->prepare("DROP TABLE IF EXISTS book_views");
		$stmt->execute();
		
		echo "REMOVE books_categories; ";
		$stmt = $this->pdo->prepare("DROP TABLE IF EXISTS books_categories");
		$stmt->execute();
		
		echo "REMOVE books; ";
		$stmt = $this->pdo->prepare("DROP TABLE IF EXISTS books");
		$stmt->execute();

		echo "REMOVE categories; ";
		$stmt = $this->pdo->prepare("DROP TABLE IF EXISTS categories");
		$stmt->execute();

		echo "REMOVE video_votes; ";
		$stmt = $this->pdo->prepare("DROP TABLE IF EXISTS video_votes");
		$stmt->execute();

		echo "REMOVE base_categories; ";
		$stmt = $this->pdo->prepare("DROP TABLE IF EXISTS base_categories");
		$stmt->execute();

		echo "REMOVE video_vote_pairs; ";
		$stmt = $this->pdo->prepare("DROP TABLE IF EXISTS video_vote_pairs");
		$stmt->execute();

		echo "REMOVE vote_videos; ";
		$stmt = $this->pdo->prepare("DROP TABLE IF EXISTS vote_videos");
		$stmt->execute();
		echo "<br />";
	}

	// === TICKER MESSAGES ===
	function insert_ticker_message_directly($message, $link) {
		$sql = "INSERT INTO ticker_messages (msg, linkurl, date_posted) VALUES (:msg, :linkurl, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("msg",  $message, PDO::PARAM_STR );
		$stmt->bindValue("linkurl", $link, PDO::PARAM_STR );
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();

		return $this->last_entry_id;
	}
	function create_ticker_message($message, $link) {
		$sql = "INSERT INTO ticker_messages (msg, linkurl, date_posted) VALUES (:msg, :linkurl, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("msg",  $message, PDO::PARAM_STR );
		$stmt->bindValue("linkurl", $link, PDO::PARAM_STR );
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();

		return $this->last_entry_id;
	}
	function update_ticker_message($message, $link, $id) {
		$sql = "UPDATE ticker_messages SET msg=:msg, linkurl=:linkurl WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("msg",  $message, PDO::PARAM_STR );
		$stmt->bindValue("linkurl", $link, PDO::PARAM_STR );
		$stmt->bindValue("id", $id, PDO::PARAM_INT );
		$stmt->execute();
	}
	function remove_ticker_message($id) {
		$sql = "DELETE FROM ticker_messages WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id", $id, PDO::PARAM_INT );
		$stmt->execute();
	}
	function list_ticker_messages() {
		$dataToReturn = array();

		$sql = "SELECT * FROM ticker_messages";
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->execute();

		$returnValue = array();
	 	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		$returnValue[] = $row;
	 	}
	 	return $returnValue;
	}

	// === CATEGORIES ===
	function list_categories() {
		$dataToReturn = array();

		$sql = "SELECT * FROM base_categories ORDER BY name ASC";
		$catQuery = $this->pdo->prepare($sql);
		$catQuery->execute();

		$base_categories = array();
	 	while ( $row = $catQuery->fetch(PDO::FETCH_ASSOC) ) {
			// fetch an array of categories for this row
			$categories = array();
			{
				$sql = "SELECT * FROM categories WHERE base_category=:baseid ORDER BY name ASC";
				$subcatQuery = $this->pdo->prepare($sql);
				$subcatQuery->bindValue("baseid",       $row['id'],      PDO::PARAM_INT);
				$subcatQuery->execute();
			 	while ( $r = $subcatQuery->fetch(PDO::FETCH_ASSOC) ) {
			 		$r['name'] = preg_replace("/(?!\s)\/(?!\s)/", " / ", $r['name']);
			 		$categories[] = $r;
			 	}
			 }
		 	$row['categories'] = $categories;
		 	// Append this row
	 		$base_categories[] = $row;
	 	}

	 	return $base_categories;
	}
	function list_detailed_base_categories($specific_id = null) {
		$dataToReturn = array();

		$sql = "SELECT
		c.name as name, c.id as id,
		c.video_pair_id as video_pair_id,
		a.id as a_id, b.id as b_id,
		a.video_title as a_video_title, b.video_title as b_video_title,
		a.youtube_id as a_youtube_id, b.youtube_id as b_youtube_id,
		a.votecount as a_votecount, b.votecount as b_votecount
		FROM base_categories c
		LEFT JOIN video_vote_pairs v ON c.video_pair_id=v.id
		LEFT JOIN vote_videos a ON v.vid_a=a.id
		LEFT JOIN vote_videos b ON v.vid_b=b.id
		";
		if ( $specific_id != null )
			$sql .= "WHERE c.id=:baseid";
		$catQuery = $this->pdo->prepare($sql);
		if ( $specific_id != null )
			$catQuery->bindValue("baseid",  $specific_id,  PDO::PARAM_INT);
		$catQuery->execute();

		$base_categories = array();
	 	while ( $row = $catQuery->fetch(PDO::FETCH_ASSOC) ) {
		 	// Append this row
	 		$base_categories[] = $row;
	 	}

	 	return $base_categories;
	}
	function create_base_category($name) {
		$sql = "INSERT INTO base_categories (name, date_posted) VALUES (:name, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("name",         $name,        PDO::PARAM_STR);
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();

		return $this->last_entry_id;
	}
	function remove_base_category($baseID) {
		// Note; to make future implementations easy,
		// should always check ===false for constraint error.
		$sql = "SELECT COUNT(*) FROM categories WHERE base_category=:baseid";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("baseid",  $baseID,  PDO::PARAM_INT);
		$stmt->execute();
		// Return false if there was a matching categry
		if ($stmt->fetchColumn()) return false;
		// Remove this base category from the table
		$sql = "DELETE FROM base_categories WHERE id=:baseid";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("baseid",  $baseID,  PDO::PARAM_INT);
		$stmt->execute();
		// Yay! We're done!
		return true;
	}
	function rename_base_category($baseID,$name) {
		$sql = "UPDATE base_categories SET name=:name WHERE id=:baseid";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("name",         $name,        PDO::PARAM_STR);
		$stmt->bindValue("baseid",       $baseID,      PDO::PARAM_INT);
		$stmt->execute();
		// Yay! We're done!
		return;
	}
	function ensure_base_category_exists($baseID) {
		$sql = "SELECT COUNT(*) FROM base_categories WHERE id=:baseid";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("baseid",       $baseID,      PDO::PARAM_INT);
		$stmt->execute();
		// Return true if there was a matching categry
		if ($stmt->fetchColumn()) return true;
		else return false;
	}
	function create_category($baseID,$name) {
		$sql = "INSERT INTO categories (base_category, name, date_posted) VALUES (:baseid, :name, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("name",         $name,        PDO::PARAM_STR);
		$stmt->bindValue("baseid",       $baseID,      PDO::PARAM_INT);
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();

		return $this->last_entry_id;
	}
	function remove_category_nullify_books($catID) {
		// Set existing books in category to null category
		// to avoid foreign key constraint error.
		$sql = "UPDATE books SET category=NULL WHERE category=:category";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("category",  $catID,  PDO::PARAM_INT);
		$stmt->execute();
		// Remove this category from the table
		$sql = "DELETE FROM categories WHERE id=:category";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("category",  $catID,  PDO::PARAM_INT);
		$stmt->execute();
		// Yay! We're done!
		return;
	}
	function rename_category($catID,$name) {
		$sql = "UPDATE categories SET name=:name WHERE id=:category";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("name",         $name,        PDO::PARAM_STR);
		$stmt->bindValue("category",       $catID,      PDO::PARAM_INT);
		$stmt->execute();
		// Yay! We're done!
		return;
	}
	function ensure_category_exists($catID) {
		$sql = "SELECT COUNT(*) FROM categories WHERE id=:category";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("category",       $catID,      PDO::PARAM_INT);
		$stmt->execute();
		// Return true if there was a matching categry
		if ($stmt->fetchColumn()) return true;
		else return false;
	}

	// === VIDEO VOTES ===
	function insert_video($youtube_id, $video_title, $votecount) {
		$sql = "INSERT INTO vote_videos (youtube_id, video_title, votecount, date_posted) VALUES (:youtube_id,:video_title,:votecount,now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("youtube_id",   $youtube_id,   PDO::PARAM_STR);
		$stmt->bindValue("video_title",  $video_title,  PDO::PARAM_STR);
		$stmt->bindValue("votecount",    $votecount,    PDO::PARAM_INT);
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();

		return $this->last_entry_id;
	}
	function insert_video_vote_pair($vidA, $vidB) {
		$sql = "INSERT INTO video_vote_pairs (vid_a, vid_b) VALUES (:vid_a, :vid_b)";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("vid_a",  $vidA,   PDO::PARAM_INT);
		$stmt->bindValue("vid_b",  $vidB,   PDO::PARAM_INT);
		$stmt->execute();

		$this->last_entry_id = $this->pdo->lastInsertId();
		return $this->last_entry_id;
	}
	function apply_video_pair_to_basecat($video_pair_id, $baseID) {
		$sql = "UPDATE base_categories SET video_pair_id=:video_pair_id WHERE id=:baseid";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("video_pair_id",   $video_pair_id,   PDO::PARAM_INT);
		$stmt->bindValue("baseid",          $baseID,          PDO::PARAM_INT);
		$stmt->execute();
	}
	function insert_video_pair_into_basecat($vidA, $vidB, $baseID) {
		$video_pair_id = $this->insert_video_vote_pair($vidA, $vidB);
		$this->apply_video_pair_to_basecat($video_pair_id);
	}
	function nullify_video_pair_in_basecat($baseID) {
		$sql = "UPDATE base_categories SET video_pair_id=NULL WHERE id=:baseid";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("baseid",          $baseID,          PDO::PARAM_INT);
		$stmt->execute();
	}

	function check_category_has_video($baseID) {
		$sql = "SELECT video_pair_id FROM base_categories WHERE id=:baseid";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("baseid",       $baseID,      PDO::PARAM_INT);
		$stmt->execute();
	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		if ($row['video_pair_id'] == null) return false;
	 		else return $row['video_pair_id'];
	 	} else {
	 		// False, since category doesn't actually exist
	 		return false;
	 	}
	}

	function update_video_info($youtube_id, $video_title, $vidid) {
		$sql = "UPDATE vote_videos SET youtube_id=:youtube_id, video_title=:video_title WHERE id=:vidid";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("youtube_id",   $youtube_id,   PDO::PARAM_INT);
		$stmt->bindValue("video_title",  $video_title,  PDO::PARAM_INT);
		$stmt->bindValue("vidid",        $vidid,        PDO::PARAM_INT);
		$stmt->execute();
	}
	function set_video_vote($account_id, $video_pair_id, $video_voted) {

		file_put_contents("log.txt", "Video pair ID: ".$video_pair_id);

		$voted = null;
		switch ($video_voted) {
			case "left":
				$voted = 0;
				break;
			case "right":
				$voted = 1;
				break;
			default:
				throw new SiteDBException("Video voted must be 'left' or 'right' (stored as 0 and 1 respectively)", SiteDBException::API_ERROR);
		}
		$sql = "INSERT INTO video_votes (account_id, video_pair_id, video_voted, date_posted) VALUES (:account_id,:video_pair_id,:video_voted,now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("account_id",     $account_id,     PDO::PARAM_INT);
		$stmt->bindValue("video_pair_id",  $video_pair_id,  PDO::PARAM_INT);
		$stmt->bindValue("video_voted",    $voted,          PDO::PARAM_INT);
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();

		return $this->last_entry_id;
	}
	function get_votes($video_pair_id) {
		$sql = "SELECT video_voted FROM video_votes WHERE video_pair_id=:video_pair_id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("video_pair_id",       $video_pair_id,         PDO::PARAM_INT);
		$stmt->execute();

		$lefts = 0; $rights = 0;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if (intval($row['video_voted']) === 0) {
				$lefts++;
			} else {
				$rights++;
			}
		}

		return array($lefts, $rights);
	}

	function get_account_vote($account_id, $video_pair_id) {
		$sql = "SELECT * FROM video_votes WHERE account_id=:account_id AND video_pair_id=:video_pair_id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("account_id",     $account_id,     PDO::PARAM_INT);
		$stmt->bindValue("video_pair_id",  $video_pair_id,  PDO::PARAM_INT);
		$stmt->execute();

		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			//echo "<h1>[".var_dump($row['video_voted'])."]</h1>";
			return (intval($row['video_voted']) === 0) ? 'left' : 'right';
		} else {
			return false;
		}
	}



	function get_vote_pair_info($video_pair_id) {
		// string taken from query. This feature uses tables
		//of votes now, not incremented value.
		/*,
		a.votecount as a_votecount, b.votecount as b_votecount*/
		$sql = "SELECT
		a.id as a_id, b.id as b_id,
		a.video_title as a_video_title, b.video_title as b_video_title,
		a.youtube_id as a_youtube_id, b.youtube_id as b_youtube_id
		FROM video_vote_pairs v
		LEFT JOIN vote_videos a ON v.vid_a=a.id
		LEFT JOIN vote_videos b ON v.vid_b=b.id
		WHERE v.id=:video_pair_id
		";
		$sql = $this->pdo->prepare($sql);
		$sql->bindValue("video_pair_id",  $video_pair_id,  PDO::PARAM_INT);
		$sql->execute();

	 	if ( $row = $sql->fetch(PDO::FETCH_ASSOC) ) {
		 	// Append this row
	 		return $row;
	 	}
	 	return false;
	}

	// === BOOKS ===
	function insert_book_from_sanitized_data($image_id,$account_id,$categories,$isbn,$title,$author,$desc,$link,$price,$visibility) {
		// Data must be already sanitized!
		$sql = "INSERT INTO books (image_id, isbn, title, author, description, link, price, currency,visibility,uploader_id,category, date_posted) VALUES (:image_id,:isbn,:title,:author,:description,:link,:price,:currency,:visibility,:uploader_id,:category, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("image_id",     $image_id,     PDO::PARAM_INT);
		$stmt->bindValue("uploader_id",  $account_id,   PDO::PARAM_INT);
		$stmt->bindValue("category",     $categories[0],  PDO::PARAM_INT);
		$stmt->bindValue("isbn",         $isbn,         PDO::PARAM_STR);
		$stmt->bindValue("title",        $title,        PDO::PARAM_STR);
		$stmt->bindValue("author",       $author,       PDO::PARAM_STR);
		$stmt->bindValue("description",  $desc,         PDO::PARAM_STR);
		$stmt->bindValue("link",         $link,         PDO::PARAM_STR);
		$stmt->bindValue("price",        $price,        PDO::PARAM_STR);
		$stmt->bindValue("currency",     "CAD",         PDO::PARAM_STR);
		$stmt->bindValue("visibility",   $visibility,   PDO::PARAM_STR);
		$stmt->execute();
		$bookID = $this->pdo->lastInsertId();
		$this->last_entry_id = $bookID;

		foreach ($categories as $cat) {
			$this->debug_buffer[] = "adding books_cats: ".$cat;
			$sql = "INSERT INTO books_categories (book_id, cat_id) VALUES (:book_id, :cat_id)";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("book_id",     $bookID,   PDO::PARAM_INT);
			$stmt->bindValue("cat_id",      $cat,      PDO::PARAM_INT);
			$stmt->execute();
		}

		return $this->last_entry_id;
	}
	function update_book_from_sanitized_data($bookID,$isbn,$title,$author,$desc,$link,$price) {
		// Data must be already sanitized!
		$sql = "UPDATE books
		SET
			isbn = :isbn,
			title = :title,
			author = :author,
			description = :description,
			link = :link,
			price = :price,
			currency = :currency,
			date_posted = now()
		WHERE id = :book_id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("isbn",         $isbn,         PDO::PARAM_STR);
		$stmt->bindValue("title",        $title,        PDO::PARAM_STR);
		$stmt->bindValue("author",       $author,       PDO::PARAM_STR);
		$stmt->bindValue("description",  $desc,         PDO::PARAM_STR);
		$stmt->bindValue("link",         $link,         PDO::PARAM_STR);
		$stmt->bindValue("price",        $price,        PDO::PARAM_STR);
		$stmt->bindValue("currency",     "CAD",         PDO::PARAM_STR);
		$stmt->bindValue("book_id",     $bookID,   PDO::PARAM_INT);
		$stmt->execute();
		$bookID = $this->pdo->lastInsertId();
		$this->last_entry_id = $bookID;

		return $this->last_entry_id;
	}
	function update_book_cover($bookID, $imageID) {
		$sql = "UPDATE books SET image_id=:image_id WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",        $bookID,   PDO::PARAM_INT);
		$stmt->bindValue("image_id",  $imageID,  PDO::PARAM_INT);
		$stmt->execute();
	}
	function update_book_video($bookID, $videoURL) {
		$sql = "UPDATE books SET video_url=:video_url WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",           $bookID,      PDO::PARAM_INT);
		$stmt->bindValue("video_url",   $videoURL,  PDO::PARAM_STR);
		$stmt->execute();
	}
	function update_book_video_off($bookID, $setting) {
		$sql = "UPDATE books SET video_url_off=:video_setting WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",           $bookID,      PDO::PARAM_INT);
		$stmt->bindValue("video_setting",   $setting,  PDO::PARAM_INT);
		$stmt->execute();
	}
	function delete_book($bookID) {
		$sql = "DELETE FROM book_views WHERE book_id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",           $bookID, PDO::PARAM_INT);
		$stmt->execute();
		$sql = "DELETE FROM books_categories WHERE book_id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",           $bookID, PDO::PARAM_INT);
		$stmt->execute();
		$sql = "DELETE FROM books WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",           $bookID, PDO::PARAM_INT);
		$stmt->execute();
	}
	function update_book_visibility($bookID, $visibility) {
		$sql = "UPDATE books SET visibility=:visibility WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",           $bookID,      PDO::PARAM_INT);
		$stmt->bindValue("visibility",   $visibility,  PDO::PARAM_STR);
		$stmt->execute();
	}
	function update_book_feature($bookID, $feature) {
		$sql = "UPDATE books SET featured=:feature WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",        $bookID,   PDO::PARAM_INT);
		$stmt->bindValue("feature",   $feature,  PDO::PARAM_INT);
		$stmt->execute();
	}
	function update_book_link($bookID, $link) {
		$sql = "UPDATE books SET link=:link WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",     $bookID, PDO::PARAM_INT);
		$stmt->bindValue("link",   $link,   PDO::PARAM_INT);
		$stmt->execute();
	}
	function update_book_account($bookID, $accountID) {
		$sql = "UPDATE books SET uploader_id=:uploader_id WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",           $bookID,     PDO::PARAM_INT);
		$stmt->bindValue("uploader_id",  $accountID,  PDO::PARAM_INT);
		$stmt->execute();

	}
	function check_user_owns_book($userID, $bookID) {
		$sql = "SELECT * FROM books WHERE id=:book_id AND uploader_id=:uploader_id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("book_id",     $bookID,     PDO::PARAM_INT);
		$stmt->bindValue("uploader_id", $userID,     PDO::PARAM_INT);
		$stmt->execute();

		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			return true;
		} else {
			return false;
		}
		return false; // in case a bit flips due to cosmic radiation
	}
	function get_book_from_id($bookID) {
		$sql = "SELECT * FROM books WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",     $bookID,     PDO::PARAM_INT);
		$stmt->execute();

		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['exists'] = true;
		} else {
			$row = array('exists' => false);
		}

		return $row;
	}
	function get_detailed_book_from_id($bookID) {
		$sql = "SELECT b.*, i.filename,
		u.reset_email as uploader_email, u.name as uploader_name
		FROM books b
		LEFT JOIN uploaded_images i ON b.image_id=i.id
		LEFT JOIN accountmgr_accounts u ON u.id=b.uploader_id
		WHERE b.id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",     $bookID,     PDO::PARAM_INT);
		$stmt->execute();

		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['exists'] = true;
		} else {
			$row = array('exists' => false);
		}

		return $row;
	}

	// === BOOK VIEWS ===
	function log_book_view($bookID) {
		$visitorIP = IPAddrHandler::get_remote_addr_128();

		$sql = "SELECT * FROM book_views WHERE book_id=:id AND remote_ip=:ip";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",     $bookID,     PDO::PARAM_INT);
		$stmt->bindValue("ip",     $visitorIP,  PDO::PARAM_LOB);
		$stmt->execute();

		// If row does not exist, add view.
		if ( ! $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
			$sql = "INSERT INTO book_views (book_id, remote_ip, date_posted) VALUES (:id, :ip, now())";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("id",     $bookID,     PDO::PARAM_INT);
			$stmt->bindValue("ip",     $visitorIP,  PDO::PARAM_LOB);
			$stmt->execute();

			$sql = "UPDATE books SET views=views+1 WHERE id=:id";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("id",           $bookID,      PDO::PARAM_INT);
			$stmt->execute();
		}

	}

	// === NEWS LETTER ===

	function add_newsletter_subscription($email) {

		$sql = "SELECT * FROM newsletter_subscriptions WHERE email=:email";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("email",  $email, PDO::PARAM_STR );
		$stmt->execute();

		// If row does not exist, add view.
		if ( ! $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
			$sql = "INSERT INTO newsletter_subscriptions (email, date_posted) VALUES (:email, now())";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("email",  $email, PDO::PARAM_STR );
			$stmt->execute();
			$this->last_entry_id = $this->pdo->lastInsertId();

			return $this->last_entry_id;
		} else {
			return -1;
		}
	}
	function remove_newsletter_subscription($email) {
		$sql = "DELETE FROM newsletter_subscriptions WHERE email=:email";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("email",  $email, PDO::PARAM_STR );
		$stmt->execute();
	}

	function get_newsletter_mails() {
		$sql = "SELECT * FROM newsletter_subscriptions";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$mails = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$mails[] = $row['email'];
		}
		return $mails;
	}
}