<?php

session_start();
session_write_close();

require_once(SITE_PATH.'/scripts/setup_videovote_template.php');

$methods = array();

$methods['run'] = function($instance) {
	$r = $instance->route;

	// Generate a SiteDB instance
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);

	$page = new Template();
	$page->set_template_file(SITE_PATH.'/templates/videovote.template.php');
	setup_videovote_template($page,$con,$sitedb,$r[1],$_SESSION);
	$page->run();
};

$page_controller = new Controller($methods);
unset($methods);
