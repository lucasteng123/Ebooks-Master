<?php

session_start();

function check_ssl_on_effing_iis_servers() {
	// Seriously, IIS should just stop existing.
	return
		(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'/*WTF?*/)
		|| $_SERVER['SERVER_PORT'] == 443;
}

$methods = array();

$methods['run'] = function($instance) {
	$accountID = -1;
	$accmgr = null; // should be i'th'scope.

	$thingy = null;
	$json = array();

	try {
		$con = $instance->tools['con_manager']->get_connection();
		$accmgr = new AccountMgr($con);

		// This if block runs (and returns function)
		// if the "forgot" button was clicked!
		if ($_POST['submitted'] == "forgot") {
			$email = $_POST['user'];

			// Verify valid email
			$FV_email = new FieldValidator();
			$FV_email->set_filter_setting('default_preset','email');
			if ($FV_email->verify($email)) {

				// Generate code and link
				$userID = $accmgr->ensure_email_exists($email);
				$code = $accmgr->gen_pass_reset_code($email);
				// http://stackoverflow.com/questions/15110355
				$thisHereLocation = (check_ssl_on_effing_iis_servers() ? 'https://' : 'http://').$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
				$link = dirname($thisHereLocation)."/?location=reset_password/".$userID."/".$code;

				// Generate email template
				$ss = new SiteStrings($con);
				$tmpl = new Template();
				$tmpl->ss = $ss;
				$tmpl->set_template_file(SITE_PATH.'/templates/email_password_reset.template.php');
				$tmpl->reset_pass_link = $link;

				// Send the email message
				$mailer = new SiteMail($con);
				if (DEV_MODE) $mailer->set_fake_mail(true);
				//$mailer->set_fake_mail(true);
				$mailer->send_user_email($email,"Password Reset - BestEbooks.ca",$tmpl);

				// Return message
				$json = array(
					'status' => 'forgot',
					'message' => "An email was sent to ".$email." with further instructions for resetting your password!",
				);
				ob_clean();
				echo json_encode($json);
				return;

			} else {
				$json = array(
					'status' => 'form_error',
					'message' => "The email address entered was not a valid email address. :/",
				);
				ob_clean();
				echo json_encode($json);
				return;
			}
			die("unreachable point");
		}

		$accountID = $accmgr->login_with_email($_POST['user'],$_POST['pass']);

		// Check permissions and set additional session variables
		$info = $accmgr->get_account_info($accountID);
		$thingy = $info;
		$perm = "site.admin";
		if (in_array($perm,$info['permissions'])) {
			$_SESSION['is_admin_user'] = "yes";
			$_SESSION['permissions'] = $info['permissions'];
		}

		// Set default redirect to the user's uploaded books page
		$json['redirect'] = WEB_PATH."/?location=payment";

		if (isset($_SESSION['book-pending-account']['id'])) {
			$sitedb = new SiteDB($con);
			$sitedb->update_book_account($_SESSION['book-pending-account']['id'],$accountID);
			$sitedb->update_book_visibility($_SESSION['book-pending-account']['id'],"unpaid");

			// Email About New Book
			$ss = new SiteStrings($con);
			$mailer = new SiteMail($con);
			$tmpl = new Template();
			$tmpl->set_template_file(SITE_PATH.'/templates/email_new_book.template.php');
			if (DEV_MODE) $mailer->set_fake_mail(true);
			$mailer->send_user_email($ss->get_value('mail.newbooks'),"New Book for Approval",$tmpl);

			$json['redirect'] = $_SESSION['book-pending-account']['url'];

			// Unset book-pending session variables, since we no longer need them
			unset($_SESSION['book-pending-account']);
		}
	} catch (AccountMgrException $e) {
		/*switch ($e->getCode()) {
			case AccountMgrException::INCORRECT_PASSWORD:
				if (isset($_SESSION['attempts'])) $_SESSION['attempts'] += 1;
				else $_SESSION['attempts'] = 1;
		}*/
		$json = array(
			'status' => 'login_error',
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

	$json['status'] = "okay";
	$json['message'] = "Login okay!";
	ob_clean();
	echo json_encode($json);
};

$page_controller = new Controller($methods);
unset($methods);
