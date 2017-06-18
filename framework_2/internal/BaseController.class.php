<?php
abstract class BaseController {
	protected $route;
	protected $template;
	protected $info = array();
	function __construct($route, $template) {
		$this->route = $route;
		$this->template = $template;
	}
	function __set($index, $value) {
		$this->info[$index] = $value;
	}
	function sayHi() {
		print("Hello!");
		print($info['queryString']);
	}
	function checkRedirect() {
		return false;
	}
	abstract function doPost();
	abstract function doGet();
}