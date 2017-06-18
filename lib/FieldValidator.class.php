<?php
class FieldValidator {
	private $value;
	private $filter;
	private $filter_settings;

	function __construct() {
		$this->filter_settings = array();
		$this->filter_settings['range'] = '*';
		$this->filter_settings['default_preset'] = "default";
		$this->filter = function($value, $filter_settings) {
			//preg_match('/^.'.$filter_settings['range'].'$/i', $value); // not sure why this was here
			$range = $filter_settings['range'];
			switch ($filter_settings['default_preset']) { // Check how field's validity should be determined
	        	case "alpha-multilang":
	            	// May contain multilanguage letter, mark, dash, connection punctuation, '.'
	            	return preg_match('/^[\pL\pM\p{Pd}\p{Pc}\'\.\s]'.$range.'$/u', $value);
	            case "string-multilang":
	            	// May contain multilanguage letter, mark, dash, connection punctuation, '.'
	            	return preg_match('/^[\pL\pM\pN\p{Pd}\p{Pc}_;,\'/().\-\s]'.$range.'$/u', $value);
	        	case "email": // Must be valid email address
	            	return filter_var($value, FILTER_VALIDATE_EMAIL);
	        	case "alphanum": // Must be alphanumeric
	            	return preg_match('/^[a-zA-Z0-9_]'.$range.'$/', $value);
	        	case "permission": // Must be alphanumeric
	            	return preg_match('/^[a-zA-Z0-9_.*]'.$range.'$/', $value);
	            case "currency-dollars":
	            	return preg_match('/^\$?[0-9]+\.?[0-9]?[0-9]?$/');
	        	case "default": // Basically anything
	            	return preg_match('/^.'.$range.'$/i', $value);
	        	case "no-filter":
	            	return true;
	        }
		}; // end of $this->filter = function ( ... ) { ... };
	}
	function set_filter_setting($setting, $value) {
		$this->filter_settings[$setting] = $value;
	}
	function get_filter_setting($setting) {
		return $this->filter_settings[$setting];
	}

	function set_value($value) {
		$this->value = $value;
	}
	function verify($value = null) {
		if (is_null($value)) {
			$value = $this->value;
		}
		$func = $this->filter;
		$result = $func($value, $this->filter_settings);
		// TODO: Assert that result is boolean?
		return $result;
	}
}