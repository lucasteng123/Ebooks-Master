<?php

/*
There are notes in this file!
Check the first few bytes of each line for
the sequence '\\*' to find these notes!
*/

session_start();

// Using the Isbn library; https://github.com/Fale/isbn
require_once(SITE_PATH.'/lib/vendor/isbn/Check.php');
require_once(SITE_PATH.'/lib/vendor/isbn/CheckDigit.php');
require_once(SITE_PATH.'/lib/vendor/isbn/Hyphens.php');
require_once(SITE_PATH.'/lib/vendor/isbn/Translate.php');
require_once(SITE_PATH.'/lib/vendor/isbn/Validation.php');
require_once(SITE_PATH.'/lib/vendor/isbn/Isbn.php');

$methods = array();

$methods['run'] = function($instance) {

	$json = array(
		'status' => 'okay',
		'message' => "Upload okay!"
	);

	ob_start();
	print("TEST");
	print_r($_FILES);
	print_r($_POST);
	$infos = ob_get_clean();

	try {
		$con = $instance->tools['con_manager']->get_connection();

		// Instantiate classes for storing images & books
		$imgdb = new ImageDB($con);
		//$imgdb->set_scale_by_width(200,300);
		$sitedb = new SiteDB($con);

		// Instantiate ISBN validation library
		$isbn = new Isbn\Isbn();



		// Input Validation
		{

			// Attain valid ISBN (or fail and break)


			// Ensure no duplicate ISBN

			// Validate other inputs
			$title = $_POST['book_title'];
			$author = $_POST['book_author'];
			$desc = $_POST['book_description'];
			$price = $_POST['book_price'];
			$link = $_POST['book_link'];

			// Instantiate the validator
//*			// * change 'default' to 'string-multilang' to whitelist characters
			$FV_std = new FieldValidator();
			$FV_std->set_filter_setting('default_preset','default'); // Allow multilingual characters

			$FV_std->set_filter_setting('range',"{1,200}");
			if (!$FV_std->verify($title)) {
				$json['status'] = "form_error";
				$json['message'] = "The title was empty, too long, or invalid.";
				ob_clean();
				echo json_encode($json);
				return;
			}

			$FV_std->set_filter_setting('range',"{1,100}");
			if (!$FV_std->verify($author)) {
				$json['status'] = "form_error";
				$json['message'] = "The author was empty, too long, or invalid.";
				ob_clean();
				echo json_encode($json);
				return;
			}

			$FV_std->set_filter_setting('range',"{1,600}");
			if (!$FV_std->verify($desc)) {
				$json['status'] = "form_error";
				$json['message'] = "The description was empty, too long, or invalid.";
				ob_clean();
				echo json_encode($json);
				return;
			}

			if ( filter_var($link, FILTER_VALIDATE_URL) === false ) {
				$json['status'] = "form_error";
				$json['message'] = "The URL was empty, too long, or invalid.";
				ob_clean();
				echo json_encode($json);
				return;
			}

			$price = str_replace('$','',$_POST['book_price']);
			if (!is_numeric($price)) {
				$json['status'] = "form_error";
				$json['message'] = "The price was empty, too long, or invalid.";
				ob_clean();
				echo json_encode($json);
				return;
			}
		}

		// Check if user is logged in
		$account_id = null;
		if ($_SESSION['logged_in'] === AccountMgr::SESSION_OKAY) {
			$account_id = $_SESSION['account']['id'];

			// Attempt to add the book cover
			$imageID = $imgdb->add_from_post_request('cover_image',"Book Cover");
			$bookentryID = $sitedb->insert_book_from_sanitized_data($imageID,$isbn_clean,$title,$author,$desc,$link,$price,"unchecked",$account_id);

			$json['status'] = "okay";
			$json['message'] = "Upload script not complete yet.";
			$json['message'] .= " File uploaded with ID " . $imgdb->get_last_entry_id();
			$json['bookurl'] = "?location=book/".$bookentryID;
		} else {
			// Attempt to add the book cover
			$imageID = $imgdb->add_from_post_request('cover_image',"Book Cover");
			$bookentryID = $sitedb->insert_book_from_sanitized_data($imageID,$isbn_clean,$title,$author,$desc,$link,$price,"awaiting-account",null);

			$json['status'] = "nosession";
			$json['message'] = "You must register or login to complete the process.";
			$json['bookurl'] = "?location=book/".$bookentryID;
			$json['booktitle'] = $title;
			
			$_SESSION['book-pending-account']['id'] = $bookentryID;
			$_SESSION['book-pending-account']['title'] = $title;
			$_SESSION['book-pending-account']['url'] = "?location=book/".$bookentryID;
		}

	} catch (ImageDBException $e) {
			$json['status'] = "form_error";
			$json['message'] = "ImageDB error: " . $e->getMessage();
			if ($e->getCode() == ImageDBException::ERR_FILE_TOO_LARGE) {
				$json['message'] = "Size of cover must be less than 2MB!";
			} else if ($e->getCode == ImageDBException::ERR_INTERNAL_MOVE) {
				$json['message'] = "An error occured while uploading cover. Too large?";
			}
	} catch (PDOException $e) {
		$json = array(
			'status' => 'server_error',
			'message' => "The following internal error occured: ".$e->getMessage()
		);
		ob_clean();
		echo json_encode($json);
		return;
	} catch (Exception $e) {
		/*switch ($e->getCode()) {
			case AccountMgrException::INCORRECT_PASSWORD:
				if (isset($_SESSION['attempts'])) $_SESSION['attempts'] += 1;
				else $_SESSION['attempts'] = 1;
		}*/
		$json = array(
			'status' => 'server_error',
			'message' => $e->getMessage()
		);
		ob_clean();
		echo json_encode($json);
		return;
	}
	ob_clean();
	echo json_encode($json);
};

$page_controller = new Controller($methods);
unset($methods);
