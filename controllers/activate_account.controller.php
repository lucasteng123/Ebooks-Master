<?php

require_once(SITE_PATH.'/scripts/setup_full_template.php');

$methods = array();

$methods['run'] = function($instance) {
	$r = $instance->route;

	$user_id = $r[0];
	$user_cd = $r[1];

	if (filter_var($r[0], FILTER_VALIDATE_INT) === false) {
		die('Invalid account ID!');
	}

	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$accmgr = new AccountMgr($con);
	$ss = new SiteStrings($con);

	$tmpl = new Template();
	$tmpl->set_template_file(SITE_PATH.'/templates/simple_message.template.php');

	$html = new Template();
	$html->set_template_file(SITE_PATH.'/templates/full.template.php');
	// Add information to page
	setup_full_template($html, $sitedb, $ss, $_SESSION);
	$html->part_frontbooks = $tmpl;

	try {
		if ($accmgr->attempt_account_activation($user_id, $user_cd) === true) {
			$tmpl->title = "Account Activated!";
			$tmpl->message = "Your account was successfully activated; thank you for verifying your email address!<br />You may still need to login.";
		}
		else {
			$tmpl->title = "Activation Error!";
			$tmpl->message = "Double-check that you entered your activation code correctly!";
		}
	} catch (AccountMgrException $e) /* on fire */ {
		$tmpl->title = "Account Locked!";
		$tmpl->message = $e->getMessage();
	}

		$html->run();

};

$page_controller = new Controller($methods);
unset($methods);
