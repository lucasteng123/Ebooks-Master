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
		'status' => 'error',
		'message' => "Undefined error :/"
	);


	if ($_SESSION['logged_in'] !== AccountMgr::SESSION_OKAY
			|| $_SESSION['is_admin_user'] !== "yes") {
		
		$json['status'] = "error";
		$json['message'] = "Not logged in; your session may hae expired.";
		ob_clean();
		echo json_encode($json);
	}


	try {
		$con = $instance->tools['con_manager']->get_connection();
		$imgdb = new ImageDB($con);
		$sitedb = new SiteDB($con);

		$bookID = $_POST['id'];

		$imageID = $imgdb->add_from_post_request('cover_image',"Book Cover");
		$sitedb->update_book_cover($bookID,$imageID);

		$json = array(
			'status' => 'good',
			'message' => "Upload okay! :)"
		);
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
