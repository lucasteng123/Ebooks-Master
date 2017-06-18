<?php

session_start();

$methods = array();

$methods['run'] = function($instance) {
	session_unset();
	session_destroy();
	echo "Successfully logged out!";
};

$page_controller = new Controller($methods);
unset($methods);
