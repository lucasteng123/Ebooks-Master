<?php
class Templater {
	function get_template_from_name($template, $templateName, $config) {
		// Set initial values
		$templatePaths = $config->get_property('template_paths');

		// Generate list of possible locations
		$possibleLocations = array();
		foreach ($templatePaths as $path) {
			if (file_exists($path."/".$templateName) && filetype($path."/".$templateName) == "dir") {
					$possibleLocations[] = $path."/".$templateName.".template/main.php";
					$possibleLocations[] = $path."/".$templateName.".template/".$templateName.".php";
			} else {
				$possibleLocations[] = $path."/".$templateName . '.template.php';
			}
		}

		// Look though possible locations. Set actual location to first readable.
		$actualLocation = "";
		foreach ($possibleLocations as $location) {
			if (is_readable($location)) {
				$actualLocation = $location;
				break;
			}
		}

		include($location); // sets $page_controller
		if (!$page_controller instanceof Controller) throw new Exception("Invalid controller");
		return $page_controller;
	}
}
/*
class Router_v0 {
	function set_controller_from_route($template, $route, $config) {
		$controllerName = $route->get_controller_name();
		$controllerPaths = $config->get_property('controller_paths');
		if (!is_array($controllerPaths)) $controllerPaths = array($controllerPaths);

		$possibleLocations = array();
		foreach ($controllerPaths as $path) {
			if (file_exists($path."/".$controllerName) && filetype($path."/".$controllerName) == "dir") {
					$possibleLocations[] = $path."/".$controllerName.".controller/main.php";
					$possibleLocations[] = $path."/".$controllerName.".controller/".$controllerName.".php";
			} else {
				$possibleLocations[] = $path."/".$controllerName . '.controller.php';
			}
		}
		$this->controller = null;
		foreach ($possibleLocations as $location) {
			if (is_readable($location)) {
				include($location); // sets $page_controller
				if (!$page_controller instanceof Controller) throw new Exception("Invalid controller");
				$this->controller = $page_controller;
				return;
			}
		}
		throw new Exception("Controller wasn't found.");
	}
}
*/