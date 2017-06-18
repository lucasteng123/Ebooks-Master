<?php
define('DEV_MODE', true);

define('TEST_PAGE', "projects");

if (false /*gethostname() == "eric-desktop"*/) {
	include("/var/www/framework_2/framework.php");
} else {
	include("./framework_2/framework.php");
}
$_STRINGS = SITE_PATH."/conf/strings.ini";

function mainf() {
	// Setup the framework...
	PathHandler::add_include_path(SITE_PATH."/lib");
	//framework_load_paths(); - now called by inclusion

	// Setup tools...
	$dbConfig = new Configurator();
	$dbConfig->set_from_ini_file("../database_confs/ddev_database.ini");

	// Load controller...
	$router = new Router();
	$router->set_route_from_request(TEST_PAGE);
	//$router->set_route_from_request("");
	$controller = $router->get_controller(SITE_PATH."/controllers");

	if ($controller === false) { // 404 Page
		$router->set_route_from_request("404");
		$controller = $router->get_controller(SITE_PATH."/errors");
		$controller->run();
	} else {
		$controller->add_tool('con_manager', new DBConnectionManager($dbConfig));
		$controller->run();
	}
}

try {
mainf();
} catch (FrameworkException $e) {
	// TODO: Remove exception dump before release, or they'll know too much! ;)
	echo "This is a debugging page; if you're seeing this message, the server is likely undergoing live maintainence.<hr />";
	echo "Error! Framework caught the error detailed below:<br />\n<pre>";
	echo var_dump($e)."</pre>";
} catch (Exception $e) {
	echo "SEVERE ERROR<br />".$e->getMessage();
}
exit();
