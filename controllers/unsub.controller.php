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
		if (SecretHashThing::hash($user_id) == $user_cd) {
			$sitedb->remove_newsletter_subscription($user_id);
			$tmpl->title = "You have unsubscribed!";
			$tmpl->message = "You have unsubscribed to the BestEbooks.ca newsletter. You can subscribe again at any time by clicking the newsletter button and entering your email!";
		} else {
			$tmpl->title = "Unsubscribe failed!";
			$tmpl->message = "We do a complicated secret code thingy to prevent other people from unsubscribing your email address. Unfortunately, it either did not work, or you unsuccessfully made an attempt to hack our complicated secret code thingy.";
		}

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
