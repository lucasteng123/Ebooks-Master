<?php

/*
	GitFeed class to be loaded with autoloader.

	Loads the feed for a specified repository using the GitHub API.
	Operations to do so include fetching the page and parsing JSON.
*/

class GitFeed {

	function __construct($user,$repo) {
		$this->user = $user;
		$this->repo = $repo;
		//$this->parse();
	}
	function fetch_events_array() {
		$url = "https://api.github.com/repos/";
		$url .= $this->user . "/";
		$url .= $this->repo . "/events";

		// Options to add required UserAgent for accessing GitHub.
		$options = array(
			'http' => array('user_agent'=>$_SERVER['HTTP_USER_AGENT'])
		);
		$context = stream_context_create($options);

		$json = file_get_contents($url, false, $context);
		$array = json_decode($json); // *RD voice* SO AWESOME!

		return $array;
	}
}
