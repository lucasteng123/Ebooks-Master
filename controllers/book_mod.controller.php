<?php

/*
*** IMPORTANT ***
Post data is NOT strictly sanitized, since
only admins have access to this page.

It should, however, be sanitized enough that
if a hacker managed to login they would not
be able to perform injected database operations.
*/

function check_video_url($url) {
	/* Not good enough -_-
	$props = array();
	parse_str(parse_url($url, PHP_URL_QUERY), $props);
	if (array_key_exists('v', $props)) {
		return $props['v'];
	} else {
		return false;
	}
	*/
	// Awesome code from http://stackoverflow.com/questions/3392993 !!
	$matches = array();
	preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
	if (count($matches) < 1) {
		return false;
		return ''; // why did I ever even do this?
	} else {
		return $matches[0];
	}
}

session_start();

$methods = array();

$methods['run'] = function($instance) {

	$r = $instance->route;

	$json = array(
		'status' => 'server_error',
		'message' => "No response generated"
	);

	ob_start();
	print_r($_POST);
	$post_data = ob_get_clean();

	try {

		// Generate a SiteDB instance
		$con = $instance->tools['con_manager']->get_connection();
		$sitedb = new SiteDB($con);
		$ss = new SiteStrings($con);
		$gc = new GuessContest($con);

		if ($_SESSION['logged_in'] === AccountMgr::SESSION_OKAY) {
			if ($r[0] == "update_video") {
				$bookID = $_POST['id'];
				$videoIdent = check_video_url($_POST['url']);

				// Condition: access is granted if user owns book or user is admin.
				$isAccessGranted = ($sitedb->check_user_owns_book($_SESSION['account']['id'], $bookID))
					|| ($_SESSION['is_admin_user'] === "yes");

				if (filter_var($bookID, FILTER_VALIDATE_INT) === false) {
					$json = array(
						'status' => 'error',
						'message' => "Book ID is not an integer"
					);
				} else if ($videoIdent === false) { // false returned by check_video_url
					$json = array(
						'status' => 'error',
						'message' => "The video URL was invalid"
					);
				} else if (! $isAccessGranted) {
					$json = array(
						'status' => 'error',
						'message' => "The logged in account doesn't own this book"
					);
				} else {
					// Set video as awaiting moderator approval
					$sitedb->update_book_video_off($bookID, 2); // 1 is reserved for if the user disables it
					// Admins get to skip approval!
					if ($_SESSION['is_admin_user'] === "yes") $sitedb->update_book_video_off($bookID, 0);
					$sitedb->update_book_video($bookID, $videoIdent);
					$json = array(
						'status' => 'okay',
						'message' => "The book's video was updated!"
					);
				}
			}
			else {
				$json['status'] = "error";
				$json['message'] = "This type of operation doesn't exist.";
			}

		} else {
			$json = array(
				'status' => 'error',
				'message' => "Not logged in. (session expired?)"
			);
		}

	} catch (PDOException $e) {
		$json = array(
			'status' => 'error',
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
			'status' => 'error',
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

/*

$methods = array();

$methods['run'] = function($instance) {
	$r = $instance->route;
	$page = new Template();
	$page->set_template_file(SITE_PATH.'/templates/home.template.php');
	if (VarTools::key_exists_equals(0,$r,"ajax")) {
		$page->run();
	} else {
		$html = new Template();
		$html->set_template_file(SITE_PATH.'/templates/full.template.php');
		$html->subTemplate = $page;
		$html->run();
	}
};

$page_controller = new Controller($methods);
unset($methods);

*/