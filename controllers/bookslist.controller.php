<?php

session_start();

require_once(SITE_PATH.'/scripts/setup_full_template.php');
require_once(SITE_PATH.'/scripts/setup_bookslist_template.php');
//require_once(SITE_PATH.'/scripts/setup_videovote_template.php');

$methods = array();

$methods['nope'] = function($instance) {
	echo "<h2>This page does not exist</h2>";
	return;
};

$methods['run'] = function($instance) {
	$query_string = preg_replace('/^\??location=bookslist\//','',$_SERVER['QUERY_STRING']);
	parse_str($query_string, $query_array);

	// Setup backend modules
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$ss = new SiteStrings($con);
	$bookLister = new BookLister($con);

	// Setup full page template
	$html = new Template();
	$html->set_template_file(SITE_PATH.'/templates/full.template.php');
	setup_full_template($html, $sitedb, $ss, $_SESSION);

	// Setup bookslist template
	$bookslist_tmpl = new Template();
	$bookslist_tmpl->set_template_file(SITE_PATH.'/templates/bookslist.template.php');
	$html->part_bookslist = $bookslist_tmpl;
	setup_bookslist_template($bookslist_tmpl, $con, $sitedb, $bookLister, $query_array);

	$html->title = "Books in ".$bookLister->get_page_info_by_query()['title'];

	// Show LPP-videovote if page is a main category and has a video
	/*
	if (array_key_exists('bcat',$query_array))
	if ($sitedb->check_category_has_video($query_array['bcat'])) {
		$videovote_tmpl = new Template();
		$videovote_tmpl->set_template_file(SITE_PATH.'/templates/videovote.template.php');
		setup_videovote_template($videovote_tmpl, $con, $sitedb, $query_array['bcat'], $_SESSION);
		$bookslist_tmpl->videovote_tmpl = $videovote_tmpl;
	}
	*/
	$html->run();

};

$page_controller = new Controller($methods);
unset($methods);