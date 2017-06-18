<?php
class Registry { // Currently not used
	private $vars = array();
	public function __set($index, $value) {
	    $this->vars[$index] = $value;
	}
	public function __get($index) {
	    return $this->vars[$index];
	}

}
