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
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$user_id = $_POST['id'];
			$user_cd = $_POST['cd'];
			$password = $_POST['password'];
			if (filter_var($user_id, FILTER_VALIDATE_INT) === false) {
				die('Invalid account ID!');
			}

			$accmgr->attempt_pass_reset($user_id, $user_cd, $password);
			$tmpl->set_template_file(SITE_PATH.'/templates/simple_message.template.php');
			$tmpl->title = "Password changed!";
			$tmpl->message = "You may now login with your newly set password!";

			$html->run();
		} else {
			$user_id = $r[0];
			$user_cd = $r[1];
			if (filter_var($user_id, FILTER_VALIDATE_INT) === false) {
				die('Invalid account ID!');
			}

			$accmgr->ensure_pass_reset_code_matches($user_id, $user_cd);

			$tmpl->user_cd = $user_cd;
			$tmpl->user_id = $user_id;
			$tmpl->set_template_file(SITE_PATH.'/templates/reset_form.template.php');
			$html->run();
		}
	} catch (AccountMgrException $e) {
		$tmpl->set_template_file(SITE_PATH.'/templates/simple_message.template.php');
		switch ($e->getCode()) {
			case AccountMgrException::ACCOUNT_LOCKED:
				$tmpl->title = "Account Locked!";
				$tmpl->message = $e->getMessage();
				break;
			case AccountMgrException::RESET_CODE_FAIL:
				$tmpl->title = "Incorrect Reset Code!";
				$tmpl->message = $e->getMessage();
				break;
			default:
				$tmpl->title = "An error occured...";
				$tmpl->message = $e->getMessage();
		}
		$html->run();
	}

};

$page_controller = new Controller($methods);
unset($methods);
