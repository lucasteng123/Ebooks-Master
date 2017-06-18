<?php

$methods = array();

$methods['run'] = function($instance) {
	/*if (
		//$RO_SESSION['is_admin_user'] !== "yes"
		$_SERVER['REMOTE_ADDR'] != "127.0.0.1"
		) {
			die('ERR: NOT LOCALHOST: '.$_SERVER['REMOTE_ADDR']);
	}*/
	echo "<pre>";
	//session_start();
	//session_destroy();
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$accmgr = new AccountMgr($con);
	$ss = new SiteStrings($con);

	$mail = new SiteMail();

	$tmpl = new Template();
	$tmpl->ss = $ss;
	$tmpl->set_template_file(SITE_PATH.'/templates/email_account_activation.template.php');

	//$result = $mail->send_activation_email("eric.alex.dube@gmail.com","nothing",$tmpl);
	$result = $mail->send_activation_email("eric@dubedev.com","nothing",$tmpl);
	if ($result) {
		echo "Was successful!";
	} else {
		echo "Didn't work!";
	}

};

$page_controller = new Controller($methods);
unset($methods);
