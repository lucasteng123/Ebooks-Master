<?php

/* submission for videovote */

session_start();

$methods = array();

$methods['do_thing'] = function($instance) {
	$json = array(
		'status' => 'error',
		'message' => 'empty response'
	);

	try {
		// initialize module
		$con = $instance->tools['con_manager']->get_connection();
		$gc = new GuessContest($con);
		// verify user input
		$name =  $_POST['name'];
		$email = $_POST['email'];
		$guess = $_POST['guess'];
		if ( filter_var($email, FILTER_VALIDATE_EMAIL) === false ) {
			$json = array(
				'status' => 'error',
				'message' => 'The email you entered was invalid!'
			);
			return $json;
		}
		// attempt to add to database
		$gc->add_contestant($name,$email,$guess);
		// display success message
		$json['status'] = "okay";
		return $json;
	} catch (GuessContestException $e) {
		$json = array(
			'status' => 'error',
			'message' => $e->getMessage()
		);
		return $json;
	} catch (Exception $e) {
		$json = array(
			'status' => '500',
			'message' => $e->getMessage()
		);
		return $json;
	}
};

$methods['run'] = function($instance) {
	$json = $instance->do_thing();
	ob_clean();
	echo json_encode($json);
	return;
};

$page_controller = new Controller($methods);
unset($methods);
