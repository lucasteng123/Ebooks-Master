<?php

$methods['get'] = function ($instance, $postID) {
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$eblog = new EbooksBlogMgr($con);

	$comments = $eblog->get_post_comments($postID);

	for ($i=0; $i < count($comments); $i++) {
		$comment = $comments[$i];
		if ($comment['author'] === null) {
			$comments[$i]['author'] = "Guest";
		}
	}

	return array(
		'status' => "okay",
		'comments' => $comments,
	);
};

$methods['post'] = function ($instance, $postID) {
	session_start();

	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$eblog = new EbooksBlogMgr($con);

	// Null uploader for guest comment
	$uploader = null;
	// Set to session value if logged in
	if ($_SESSION['logged_in'] == AccountMgr::SESSION_OKAY) {
		$uploader = $_SESSION['account']['id'];
	}

	// Post contents
	$contents = $_POST['comment'];
	// TODO: limit comment length
	$eblog->insert_comment($contents, $postID, $uploader);

	return array(
		'status' => "okay",
		'echo' => $_POST,
	);
};

// ENTRY POINT
$methods['run'] = function ($instance) {
	$r = $instance->route;

	if (!isset($r[0]) || !isset($r[1])) {
		$responseArray = array(
			'status' => "error",
			'message' => "Request error: tried to fetch comments with invalid parameters",
		);
		ob_clean();
		echo json_encode($responseArray);
		return;
	}

	$responseArray = null;

	try {
		if ($r[0] == "post")
			$responseArray = $instance->post($r[1]);
		else // if ($r[0] == "get")
			$responseArray = $instance->get($r[1]);
	} catch (PDOException $e) {
		$responseArray = array(
			'status' => "error",
			'message' => "An unknown error occured (db error: ".$e->getMessage().")",
		);
	} catch (Exception $e) {
		$responseArray = array(
			'status' => "error",
			'message' => "An unknown error occured",
		);
	}

	ob_clean();
	echo json_encode($responseArray);
	return;
};

$page_controller = new Controller($methods);
unset($methods);