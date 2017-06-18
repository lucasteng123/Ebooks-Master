<?php

/*
	SECURITY CHECKLIST

	THIS CLASS:
		-> Valid $_FILE
		-> File MIME type
		-> File size
		INTENTION
			-> All security related to file binary

	API USER:
		-> Valid $friendly_name value
		-> Valid $files_key value
*/

class GitXML {

	function __construct($location) {
		$this->file = $location;
		$this->object = simplexml_load_file($this->file);
		if (!$this->object) {
			trigger_error("Error during GitXML setup: Could not load file with simplexml!");
		}
		//$this->parse();
	}
	function outputTest() {
		foreach ($this->object->entry as $entry) {
			print($entry->title);
			print("<br />");
		}

	}
}
