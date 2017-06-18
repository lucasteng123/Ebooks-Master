<?php

session_start();

require_once(SITE_PATH.'/scripts/setup_full_template.php');
// require_once(SITE_PATH.'/scripts/setup_frontbooks_template.php');

$methods = array();

$methods['run'] = function($instance) {
	$r = $instance->route;

	// Generate a SiteDB instance
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$ss = new SiteStrings($con);

	// Generate topbooks template
	$topbooks_tmpl = new Template();
	$topbooks_tmpl->set_template_file(SITE_PATH.'/templates/tshirts.template.php');
	{
		// setup_frontbooks_template($topbooks_tmpl, $con, $sitedb, $bookLister, $query_array);
	}

	$html = new Template();
	$html->set_template_file(SITE_PATH.'/templates/full.template.php');

	// Add information to page
	setup_full_template($html, $sitedb, $ss, $_SESSION);

	// Add subpages to page
	$html->part_frontbooks = $topbooks_tmpl;
	//$html->part_bookslist = $testing_tmpl;

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