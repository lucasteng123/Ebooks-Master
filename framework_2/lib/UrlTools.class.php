<?php
class UrlTools {
	function validateURL($url) {
		$pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
		return preg_match($pattern, $url);
	}

	function getTitleURL($url) {
		$page=file_get_contents($url);
		$titleStart=strpos($page,'<title>')+7;
		$titleLength=strpos($page,'</title>')-$titleStart;
		$title=substr($page,$titleStart,$titleLength);
		return $title;
	}

	function getExistsURL($url) {
		$handle = @fopen($url,'r');
		if ($handle !== false) {
		   return true;
		} else {
		   return false;
		}
	}

	function prob_a_url($text) { // Checks if a string is probably a url
		$urlPrefixes = array(
			'http://',
			'www.',
			'https://',
		);
		foreach ($urlPrefixes as $key => $value) {
			if (substr($text,0,strlen($value))==$value) {
				return true;
			}
		}
		return false;
	}
}
?>