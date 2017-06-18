<?php

require_once(SITE_PATH.'/scripts/setup_full_template.php');

$methods = array();

$methods['run'] = function($instance) {
	$r = $instance->route;

	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$accmgr = new AccountMgr($con);
	$ss = new SiteStrings($con);

	$tmpl = new Template();
	$html = new Template();
	$html->set_template_file(SITE_PATH.'/templates/full.template.php');
	// Add information to page
	setup_full_template($html, $sitedb, $ss, $_SESSION);
	$html->part_frontbooks = $tmpl;

	try {
		$user_id = $r[0]; // email
		$user_cd = $r[1]; // code

		$tmpl->set_template_file(SITE_PATH.'/templates/simple_message.template.php');
		$tmpl->title = "BestEbooks Privacy Policy";
		$tmpl->message = $ss->get_html('beste.privacy');

		$html->run();
	} catch (Exception $e) {
		$tmpl->set_template_file(SITE_PATH.'/templates/simple_message.template.php');
		$tmpl->title = "An error occured...";
		$tmpl->message = $e->getMessage();
		$html->run();
	}

};

$page_controller = new Controller($methods);
unset($methods);
