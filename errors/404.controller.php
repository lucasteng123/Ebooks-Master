<?php

$methods = array();

$methods['run'] = function($instance) {
	$r = $instance->route;
	$page = new Template();
	$page->set_template_file(SITE_PATH.'/templates/404.template.php');
	$page->run();
};

$page_controller = new Controller($methods);
unset($methods);