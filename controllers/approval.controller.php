<?php

session_start();

$methods = array();

$methods['run'] = function($instance) {

	$html = new Template();

	// Generate a SiteDB instance
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$ss = new SiteStrings($con);

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

		$vvote_info = array();
		{
			$vvote_info = $sitedb->list_detailed_base_categories();

			$hpid = $ss->get_value('homepage.videovote_id');
			$item = array(
				'name' => "Homepage Videos",
				'video_pair_id' => $hpid,
				'id' => 'homepage'
			);
			$item = array_merge($item, $sitedb->get_vote_pair_info($hpid));

			array_unshift($vvote_info, $item);
		}
		$html->vvote_info = $vvote_info;


		$html->set_template_file(SITE_PATH.'/templates/admin.template.php');

		// Check for view
		$r = $instance->route;
		if (isset($r[0])) {
			$html->startView=$r[0];
		}

		// Query information for page.
		{
			$sql = "SELECT * FROM books WHERE visibility=:visibility";
			$stmt = $con->prepare($sql);
			$stmt->bindValue("visibility", "unchecked", PDO::PARAM_STR );
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