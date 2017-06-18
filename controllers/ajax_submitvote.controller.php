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
		if ($_SESSION['logged_in'] !== AccountMgr::SESSION_OKAY) {
			$json = array(
				'status' => 'guest_vote_error',
				'message' => 'Must be logged in to vote!'
			);
			ob_clean();
			echo json_encode($json);
			return;
		}
		if ( filter_var($_POST['baseid'], FILTER_VALIDATE_INT) === false ) {
			$json = array(
				'status' => 'form_error',
				'message' => 'Server received invalid data!'
			);
			ob_clean();
			echo json_encode($json);
			return;
		}

		// eventually if case here for home category

		$con = $instance->tools['con_manager']->get_connection();
		$sitedb = new SiteDB($con);
		$sitedb->set_video_vote($_SESSION['account']['id'], intval($_POST['baseid']), $_POST['voted']);
		$votes = $sitedb->get_votes(intval($_POST['baseid']));
		$json['status'] = "okay";
		$json['video_voted'] = $_POST['voted'];
		$json['video_a_votes'] = $votes[0];
		$json['video_b_votes'] = $votes[1];

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
