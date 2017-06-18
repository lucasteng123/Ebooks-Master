<?php

// IMPORTANT DEFINITIONS
define ('FRAMEWORK_PATH', realpath(dirname(__FILE__)) );
define('SITE_PATH',getcwd());
define('ROOT_PATH',getcwd());
if (!defined('DEV_MODE')) {
	define('DEV_MODE', false);
}
$_FRAMEWORK = array();

// NECESSARY CLASSES FOR AUTOLOAD
class FrameworkException extends Exception {
	const AUTOLOAD = 0;

	const PATH_HANDLER = 1;
	const PATH_HANDLER_INEXISTANT_PATH_INCLUSION = 1.1;
	const PATH_HANDLER_ONLY_PATH_REMOVAL = 1.2;

	const CONFIG = 3;
	const CONFIG_MISSING_KEY = 3.1;
	const CONFIG_INVALID_TYPE = 3.2;
	const CONFIG_INVALID = 3.3; // invalid configuration object

	const CONTROLLER = 4;
	const CONTROLLER_NOT_FOUND = 4.1;

	const DATATYPE = 5;

	const ASSERTION = 6;

	static function throw_datatype_exception($got, $expected, $test=false) {
		throw new FrameworkException(MiscTools::get_calling_class().": Expecting '".$expected."', got '".$got."'", self::DATATYPE);
	}
	static function assert($boolean, $msg="None given") {
		if (!$boolean) {
			throw new FrameworkException(MiscTools::get_calling_class().": Assertion failed! Additional info: ".$msg);
		}
	}
}
class GeneralPurposeException extends Exception {

}
class PathHandler {
	static function add_include_path ($path) {
		foreach (func_get_args() AS $path)
		{
			if (!file_exists($path) OR (file_exists($path) && filetype($path) !== 'dir'))
			{
				throw new FrameworkException (
					"Include path '{$path}' does not exist",
					FrameworkException::PATH_HANDLER_INEXISTANT_PATH_INCLUSION
					);
				continue;
			}
			
			$paths = explode(PATH_SEPARATOR, get_include_path());
			
			if (array_search($path, $paths) === false)
				// formerly array_push
				array_unshift($paths, $path);
			
			set_include_path(implode(PATH_SEPARATOR, $paths));
		}
	}

	static function remove_include_path ($path) {
		foreach (func_get_args() AS $path)
		{
			$paths = explode(PATH_SEPARATOR, get_include_path());
			
			if (($k = array_search($path, $paths)) !== false)
				unset($paths[$k]);
			else
				continue;
			
			if (!count($paths))
			{
				throw new FrameworkException (
					"Include path '{$path}' can not be removed because it is the only",
					FrameworkException::PATH_HANDLER_ONLY_PATH_REMOVAL
					);
				continue;
			}
			
			set_include_path(implode(PATH_SEPARATOR, $paths));
		}
	}
}

// AUTOLOAD
function __autoload($className) {
	$possibleLocations = array();
	$possibleLocations[] = $className . '.class.php';
	$possibleLocations[] = $className.".class/main.php";
	$possibleLocations[] = $className."/".$className.".php";
	foreach ($possibleLocations as $location) {
		@include($location);
		if (class_exists($className)) return;
	}
	if (!class_exists($className)) {
		throw new FrameworkException (
			"Autoload failed; no file or folder with the given classname (".$className.") was readable",
			FrameworkException::AUTOLOAD
		);
	}
}

function framework_load_paths() { // throws FrameworkException
	PathHandler::add_include_path(FRAMEWORK_PATH.DIRECTORY_SEPARATOR."lib");
	PathHandler::add_include_path(FRAMEWORK_PATH.DIRECTORY_SEPARATOR."internal");
	PathHandler::add_include_path(FRAMEWORK_PATH.DIRECTORY_SEPARATOR."types");
}
function do_nothing() {
	return;
}


$_FRAMEWORK['error_log'] = array();
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
	if (DEV_MODE) echo "de errno:".$errno.";errstr:".$errstr.";errfile:".$errfile.";errline:".$errline;
	/*if (DEV_MODE && $errno==8) {
		var_dump(debug_backtrace());
	}*/
	switch ($errno) {
		case E_RECOVERABLE_ERROR:
		case E_USER_ERROR:
			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
			break;
		case E_WARNING:
		case E_USER_WARNING:
			break;
		case E_NOTICE:
		case E_USER_NOTICE:
			break;
		default:
			
			break;
	}
	$notice = array();
	$notice['errno'] = $errno;
	$notice['errstr'] = $errstr;
	$notice['errfile'] = $errfile;
	$notice['errline'] = $errline;
	$_FRAMEWORK['error_log'][] = $notice;
}
function framework_shutdown_function() {
	$var = error_get_last();
	if ($var) {
		echo "Oops; an error occured to such an extent that the operation had to stop, and a web page could not be sent to you.";
		if (DEV_MODE) {
			echo "<br /><br />DEV MODE IS ON:<br /><pre>";
				print_r($var);
			}
			echo "</pre>";
		}
}
set_error_handler("exception_error_handler");
register_shutdown_function('framework_shutdown_function');
framework_load_paths();
/*
// TODO IF BORED
// -> Load paths from config into $_FRAMEWORK['path'];
// -> Set framework_load_paths to load from $_FRAMEWORK['path'];
*/
return 0;