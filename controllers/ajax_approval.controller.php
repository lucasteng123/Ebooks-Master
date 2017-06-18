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
	$r = $instance->route;

	$json = array(
		'status' => 'okay',
		'message' => "Operation okay!"
	);

	if (
		(! $_SESSION['logged_in'] === AccountMgr::SESSION_OKAY)
		|| (! $_SESSION['is_admin_user'] === "yes")
		) {
		$json = array(
			'status' => 'error',
			'message' => "unauthorized access"
		);
		ob_clean();
		echo json_encode($json);
		return;
	}

	if (!array_key_exists(0, $r) || !array_key_exists(1, $r)) {
		$json = array(
			'status' => 'error',
			'message' => "request is invalid [1]"
		);
		ob_clean();
		echo json_encode($json);
		return;
	}
	if (filter_var($r[1], FILTER_VALIDATE_INT) === false) {
		$json = array(
			'status' => 'error',
			'message' => "request is invalid [2:".$r[1]."]"
		);
		ob_clean();
		echo json_encode($json);
		return;
	}

	// Set variables based on request parameters
	$operation = $r[0];
	$bookID = $r[1];

	try {
		$con = $instance->tools['con_manager']->get_connection();
		$sitedb = new SiteDB($con);
		$eblog = new EbooksBlogMgr($con);


		// Check that first arg is approve or decline
		switch ($r[0]) {
			case "approve":
				$sitedb->update_book_visibility($bookID,"public");
				break;
			case "decline":
				$sitedb->update_book_visibility($bookID,"removed");
				break;
			case "video-approve":
				$sitedb->update_book_video_off($bookID,0);
				break;
			case "video-decline":
				$sitedb->update_book_video_off($bookID,3); // 1 off, 2 waiting, 3 declined
				break;
			case "comment-approve":
				$eblog->update_comment_state($bookID, EbooksBlogMgr::COMMENT_APPROVED);
				break;
			case "comment-decline":
				$eblog->update_comment_state($bookID, EbooksBlogMgr::COMMENT_DECLINED); // 1 off, 2 waiting, 3 declined
				break;
			default:
				$json = array(
					'status' => 'error',
					'message' => "request is invalid"
				);
				ob_clean();
				echo json_encode($json);
				return;
		}

	} catch (ImageDBException $e) {
			$json['status'] = "form_error";
			$json['message'] = "ImageDB error: " . $e->getMessage();
			if ($e->getCode() == ImageDBException::ERR_FILE_TOO_LARGE) {
				$json['message'] = "Size of cover must be less than 2MB!";
			} else if ($e->getCode() == ImageDBException::ERR_INTERNAL_MOVE) {
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
