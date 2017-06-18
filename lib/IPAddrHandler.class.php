<?php
class IPAddrHandler {
	public static function get_remote_addr_128() {
		/*
		Thanks to Sander Steffann & Cem Kalyoncu on StackOverflow,
		http://stackoverflow.com/questions/22636912
		http://stackoverflow.com/questions/1448871
		*/
		$remote_addr = $_SERVER['REMOTE_ADDR'];
		if (strpos($remote_addr, ":") === false) {
			$remote_addr = "::ffff:".$remote_addr;
		}

		return inet_pton($remote_addr);
	}
}
