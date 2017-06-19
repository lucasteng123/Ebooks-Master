<?php
 define('DEV_MODE', true);
/*
switch (gethostname()) {
	case "freakin-anvil-of-a-pc":
		define('WEB_PATH','http://127.0.0.1/bestebooks');
		break;
	default:
		define('WEB_PATH','http://bestebooks.ca');
}
*/

// Double-check URL (TODO: eventually use mod_rewrite for this)
$host = $_SERVER['SERVER_NAME'];
$ri   = $_SERVER['REQUEST_URI'];
if ($host == "bestebooks.ca") {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://www.bestebooks.ca".$ri);
}

/* Awesome web-path detection code */
$pattern = '/^'.preg_quote($_SERVER['DOCUMENT_ROOT'],'/').'/';
$webpath = "http://".$_SERVER['HTTP_HOST'].preg_replace($pattern,'',getcwd());
define('WEB_PATH',$webpath);

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
	if (gethostname() == "freakin-anvil-of-a-pc") {
		$dbConfig->set_from_ini_file("../../database_confs/bestebooks_database.ini");
	} else {
		$dbConfig->set_from_ini_file("../database_confs/bestebooks_database.ini");
	}

	// Load controller...
	$router = new Router();
	$router->set_route_from_request(array_key_exists('location', $_GET) ? $_GET['location'] : '/');
	if (isset($_GET['tx']) && isset($_GET['st'])) $router->set_route_from_request("payment");
	
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
	echo "<br />";
	echo "This is a debugging page; if you're seeing this message, the server is likely undergoing live maintainence.<hr />";
	echo "Error! Framework caught the error detailed below:<br />\n<pre>";
	echo var_dump($e)."</pre>";
} catch (Exception $e) {
	echo "SEVERE ERROR<br />".$e->getMessage();
}
exit();
