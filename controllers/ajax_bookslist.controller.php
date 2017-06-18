<?php

session_start();

require_once(SITE_PATH.'/scripts/setup_bookslist_template.php');

$methods = array();

$methods['run'] = function($instance) {
	$query_string = preg_replace('/^\??location=ajax_bookslist\//','',$_SERVER['QUERY_STRING']);
	parse_str($query_string, $query_array);

	// Setup backend modules
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$bookLister = new BookLister($con);

	$page = new Template();
	$page->set_template_file(SITE_PATH.'/templates/bookslist.template.php');
	setup_bookslist_template($page, $con, $sitedb, $bookLister, $query_array);
	//if (!$pagegood) $page->set_template_file(SITE_PATH.'/templates/404.template.php');
	$page->run();
};

$page_controller = new Controller($methods);
unset($methods);
