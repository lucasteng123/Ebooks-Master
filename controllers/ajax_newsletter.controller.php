<?php

/* submission for videovote */

session_start();

$methods = array();

$methods['run'] = function($instance) {

	$thingy = null;
	$json = array(
		'status' => 'error',
		'message' => 'empty response'
	);

	try {
		if ( filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false ) {
			$json = array(
				'status' => 'form_error',
				'message' => 'The email you entered was invalid!'
			);
			ob_clean();
			echo json_encode($json);
			return;
		}

		// eventually if case here for home category

		$con = $instance->tools['con_manager']->get_connection();
		$sitedb = new SiteDB($con);
		$sitedb->add_newsletter_subscription($_POST['email']);
		$json['status'] = "okay";

	} catch (SiteDBException $e) {
		$json = array(
			'status' => 'error',
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

	ob_clean();
	echo json_encode($json);
};

$page_controller = new Controller($methods);
unset($methods);
