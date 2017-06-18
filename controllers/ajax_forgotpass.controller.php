<?php

$methods = array();

$methods['run'] = function($instance) {
	try {
		$con = $instance->tools['con_manager']->get_connection();
		$accmgr = new AccountMgr($con);
		$mailer = new SiteMail($con);
		$ss = new SiteStrings($con);

		// Attempt account registration
		$userID = $accmgr->new_account($_POST['user'],$_POST['pass'],$_POST['name'],$_POST['email'],$_POST['pass_retype']);

		// Send activation email
		$activCode = $accmgr->get_last_activation_code();
		// http://stackoverflow.com/questions/15110355
		$thisHereLocation = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$activLink = dirname($thisHereLocation)."/?location=activate_account/".$userID."/".$activCode;

		$tmpl = new Template();
		$tmpl->set_template_file(SITE_PATH.'/templates/email_account_activation.template.php');
		$tmpl->activation_link = $activLink;
		$tmpl->ss = $ss;

		// User must have submitted a valid email address if we're here,
		// so just use POST email without any additional checks.
		if (DEV_MODE) $mailer->set_fake_mail(true);
		$mailer->send_user_email($_POST['email'],"Account Activation - BestEbooks.ca",$tmpl);

	} catch (AccountMgrException $e) {
		/*
		Hehe, I'm just gonna leave this here because I actually
		almost did this until I thought about it for a few seconds.
		switch ($e->getCode()) {
			case AccountMgrException::INCORRECT_PASSWORD:
				if (isset($_SESSION['attempts'])) $_SESSION['attempts'] += 1;
				else $_SESSION['attempts'] = 1;
		}*/
		$json = array(
			'status' => 'register_error',
			'message' => $e->getMessage()
		);
		ob_clean();
		echo json_encode($json);
		return;
	} catch (PDOException $e) {
		$json = array(
			'status' => 'server_error',
			'message' => "The following internal error occured: ".$e->getMessage()
		);
		ob_clean();
		echo json_encode($json);
		return;
	} catch (Exception $e) {
		/*switch ($e->getCode()) {
			case AccountMgrException::INCORRECT_PASSWORD:
				if (isset($_SESSION['attempts'])) $_SESSION['attempts'] += 1;
				else $_SESSION['attempts'] = 1;
		}*/
		$json = array(
			'status' => 'server_error',
			'message' => $e->getMessage()
		);
		ob_clean();
		echo json_encode($json);
		return;
	}

	$json = array(
		'status' => 'okay',
		'message' => "Login okay!"
	);
	ob_clean();
	echo json_encode($json);
};

$page_controller = new Controller($methods);
unset($methods);
