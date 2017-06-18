<?php

session_start();

require_once(SITE_PATH.'/scripts/setup_full_template.php');

$methods = array();

$methods['run'] = function($instance) {
	$r = $instance->route;

	// Generate a SiteDB instance
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$ss = new SiteStrings($con);

	$goodToGo = array_key_exists(0, $r) && is_numeric($r[0]);
	if (($idToGet = filter_var($r[0], FILTER_VALIDATE_INT)) === false) {
		$goodToGo = false;
	}

	if ($goodToGo) {

		$html = new Template();
		$html->set_template_file(SITE_PATH.'/templates/full.template.php');
 
		// Populate the bookview template
		$bookinfo = $sitedb->get_detailed_book_from_id($idToGet);
		// Log this particular book view
		// This call handles ip checking [IPs are stored in the database]
		$sitedb->log_book_view($idToGet);

		$small_title = "Title Error";
		$l = strlen(utf8_decode($bookinfo['title']));
	 	if ($l > 15) {
	 		$row['shortened'] = true;
	 		$small_title = mb_substr($bookinfo['title'], 0, 15, 'utf-8') . '...';
	 	} else {
	 		$small_title = $bookinfo['title'];
	 	}
	 	$html->title = $small_title;
		

		if ($bookinfo['exists'] === true) {
			// Determine some facts about this book
			$userOwnsBook = $_SESSION['account']['id'] == $bookinfo['uploader_id'];

			// Redirect user if they don't own this.
			if (!$userOwnsBook) if (
				$bookinfo['visibility'] !== "public"
				&& ! ($_SESSION['is_admin_user'] === "yes")
			) {
				header('Location: '.WEB_PATH);
				return;
			}

			// Setup the template
			$bookview_tmpl = new Template();
			$bookview_tmpl->set_template_file(SITE_PATH.'/templates/book.template.php');
			$html->part_bookview = $bookview_tmpl;

			// Apply book-related information to template
			$bookview_tmpl->bookinfo = $bookinfo;
			$bookview_tmpl->bookid = $r[0];
			$bookview_tmpl->book_price = number_format((float)$bookinfo['price'], 2, '.', '');

			// Determine if template should display video
			$bookview_tmpl->displayVideo = false; // pessimistic default
			if ($bookinfo['video_url'] != null) {
				// If the video has been approved
				if ($bookinfo['video_url_off'] == 0) {
					$bookview_tmpl->displayVideo = true; // Yay!
				}
				// If the video is awaiting approval
				if ($bookinfo['video_url_off'] == 2 && $userOwnsBook) {
					$bookview_tmpl->displayVideo = true;
					$bookview_tmpl->displayVideoModerationWarning = true;
				}
			}
			// Set book link name
			/*{
				$link_name = 'Unknown';
				// This code block thanks to stanev01 on SO
				// http://stackoverflow.com/questions/16027102
				$pieces = parse_url($bookinfo['link']);
				$domain = isset($pieces['host']) ? $pieces['host'] : '';
				if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
					$link_name = $regs['domain'];
				}
				$bookview_tmpl->book_link_name = $link_name;
			}*/
		} else {
			$bookview_tmpl = new Template();
			$bookview_tmpl->set_template_file(SITE_PATH.'/templates/book404.template.php');
			$html->part_bookview = $bookview_tmpl;
		}

		// Add information to page
		setup_full_template($html, $sitedb, $ss, $_SESSION);
		if ($_SESSION['is_admin_user'] === "yes") {
			setup_full_template_bookctrl($html, $bookinfo);
		}

		if ($_SESSION['logged_in'] === AccountMgr::SESSION_OKAY) {
			$html->user_logged_in = "yes";
			$html->user_name = $_SESSION['account']['name'];
			$html->user_username = $_SESSION['account']['username'];
			if ($userOwnsBook) {
				$bookview_tmpl->show_owner_control = true;
				if ($bookinfo['visibility'] == "unpaid") $bookview_tmpl->show_unpaid = true;
			}
			if ($_SESSION['is_admin_user'] === "yes") {
				$html->adminDisplay = "yes";
				$bookview_tmpl->show_adminfo = true;
			}
		}
		$html->run();
	} else {
		// Show 404 page.
		echo "<h2>This page does not exist</h2>";
	}


};

$page_controller = new Controller($methods);
unset($methods);