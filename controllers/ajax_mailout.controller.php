<?php

/*
There are notes in this file!
Check the first few bytes of each line for
the sequence '\\*' to find these notes!
*/

session_start();

// Using the Isbn library; https://github.com/Fale/isbn
require_once(SITE_PATH.'/lib/vendor/isbn/Check.php');
require_once(SITE_PATH.'/lib/vendor/isbn/CheckDigit.php');
require_once(SITE_PATH.'/lib/vendor/isbn/Hyphens.php');
require_once(SITE_PATH.'/lib/vendor/isbn/Translate.php');
require_once(SITE_PATH.'/lib/vendor/isbn/Validation.php');
require_once(SITE_PATH.'/lib/vendor/isbn/Isbn.php');

$methods = array();

$methods['run'] = function($instance) {

	$json = array(
		'status' => 'error',
		'message' => "Undefined error :/"
	);


	if ($_SESSION['logged_in'] !== AccountMgr::SESSION_OKAY
			|| $_SESSION['is_admin_user'] !== "yes") {
		
		$json['status'] = "error";
		$json['message'] = "Not logged in; your session may hae expired.";
		ob_clean();
		echo json_encode($json);
	}


	try {
		$con = $instance->tools['con_manager']->get_connection();
		$imgdb = new ImageDB($con);
		$sitedb = new SiteDB($con);
		$mailer = new SiteMail($con);
		if (DEV_MODE) $mailer->set_fake_mail(true);


		$tmpl = new Template();
		$tmpl->set_template_file(SITE_PATH.'/templates/email_newsletter.template.php');
		$tmpl->title = $_POST['title'];
		$tmpl->contents = $_POST['contents'];

		// if an image was uploaded
		if (isset($_FILES['send_image']['error'])) {
			$json = array(
				'status' => 'error',
				'message' => "It worked!"
			);
			ob_clean();
			echo json_encode($json);
			// store the image
			$imageID = $imgdb->add_from_post_request('send_image',"Email Image");
			$file = $imgdb->get_image_by_id($imageID)['filename'];
			$tmpl->image = WEB_PATH.'/uploads/img/'.$file;
		}


		$mails = $sitedb->get_newsletter_mails();
		foreach ($mails as $mail) {
			$hash = SecretHashThing::hash($mail);
			$tmpl->link = "http://bestebooks.ca/?location=unsub/".urlencode($mail)."/".$hash;
			$mailer->send_user_email($mail,"BestEbooks.ca Newsletter",$tmpl);
		}

		$json = array(
			'status' => 'good',
			'message' => "The newsletter was sent!"
		);
	} catch (ImageDBException $e) {
			$json['status'] = "form_error";
			$json['message'] = "ImageDB error: " . $e->getMessage();
			if ($e->getCode() == ImageDBException::ERR_FILE_TOO_LARGE) {
				$json['message'] = "Size of cover must be less than 2MB!";
			} else if ($e->getCode == ImageDBException::ERR_INTERNAL_MOVE) {
				$json['message'] = "An error occured while uploading cover. Too large?";
			}
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
	ob_clean();
	echo json_encode($json);
};

$page_controller = new Controller($methods);
unset($methods);
