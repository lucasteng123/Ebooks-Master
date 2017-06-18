<?php

session_start();

$methods = array();

$methods['run'] = function($instance) {

	$html = new Template();

	// Generate a SiteDB instance
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$eblog = new EbooksBlogMgr($con);
	$ss = new SiteStrings($con);
	$gc = new GuessContest($con);

	if ($_SESSION['logged_in'] === AccountMgr::SESSION_OKAY
		&& $_SESSION['is_admin_user'] === "yes") {

		// Same stuff that index controller does
		$html->user_logged_in = "yes";
		$html->user_name = $_SESSION['account']['name'];
		$html->user_username = $_SESSION['account']['username'];

		// Add information to page
		$html->adminDisplay = "yes";
		$html->ticker_messages = $sitedb->list_ticker_messages();
		$html->categories = $sitedb->list_categories();
		$html->newsletter_emails = $sitedb->get_newsletter_mails();

		$vvote_info = array();
		{
			$vvote_info = $sitedb->list_detailed_base_categories();

			$hpid = $ss->get_value('homepage.videovote_id');
			$item = array(
				'name' => "Homepage Videos",
				'video_pair_id' => $hpid,
				'id' => 'homepage'
			);
			$hpvidinfo = $sitedb->get_vote_pair_info($hpid);
			if (is_array($hpvidinfo)) {
				$item = array_merge($item, $hpvidinfo);
			}

			array_unshift($vvote_info, $item);
		}
		$html->vvote_info = $vvote_info;


		$html->set_template_file(SITE_PATH.'/templates/admin.template.php');
		$html->ss = $ss;

		// Check for view
		$r = $instance->route;
		if (isset($r[0])) {
			$html->startView=$r[0];
		}

		// Add information about guessing contest to page
		$isContestRunning = $gc->check_contest_running();
		$html->isContestRunning = $isContestRunning;
		$html->contestsList = $gc->list_contests();
		$html->gcWinners = $gc->list_roll();
		if ($isContestRunning) {
			$html->numberOfContestants = $gc->get_number_of_contestants();
		}

		// Query information for page.
		{
			$sql = "SELECT b.*, i.filename FROM books b LEFT JOIN uploaded_images i ON b.image_id=i.id WHERE b.visibility='unchecked' OR b.visibility='unpaid'";
			$stmt = $con->prepare($sql);
			//$stmt->bindValue("visibility", "unchecked", PDO::PARAM_STR );
			$stmt->execute();
			$uncheckedBooks = array();
		 	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		 		{
					$link_name = 'Unknown';
					// This code block thanks to stanev01 on SO
					// http://stackoverflow.com/questions/16027102
					$pieces = parse_url($bookinfo['link']);
					$domain = isset($pieces['host']) ? $pieces['host'] : '';
					if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
						$link_name = $regs['domain'];
					}
					$row['link_domain'] = $link_name;
				}
		 		$uncheckedBooks[] = $row;
		 	}
		 	$html->uncheckedBooks = $uncheckedBooks;
		}

		// Query information for page.
		{
			$sql = "SELECT b.*, i.filename FROM books b LEFT JOIN uploaded_images i ON b.image_id=i.id WHERE b.video_url_off=2";
			$stmt = $con->prepare($sql);
			//$stmt->bindValue("visibility", "unchecked", PDO::PARAM_STR );
			$stmt->execute();
			$uncheckedVideos = array();
		 	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		 		{
					$link_name = 'Unknown';
					// This code block thanks to stanev01 on SO
					// http://stackoverflow.com/questions/16027102
					$pieces = parse_url($bookinfo['link']);
					$domain = isset($pieces['host']) ? $pieces['host'] : '';
					if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
						$link_name = $regs['domain'];
					}
					$row['link_domain'] = $link_name;
				}
		 		$uncheckedVideos[] = $row;
		 	}
		 	$html->uncheckedVideos = $uncheckedVideos;
		}

		$html->uncheckedComments = $eblog->get_unchecked_comments();

	} else {
		$html->set_template_file(SITE_PATH.'/templates/403.template.php');
	}

	$html->run();
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