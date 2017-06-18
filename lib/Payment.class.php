<?php
define('USE_SANDBOX', true);
class Payment {
	function __construct() {
		$this->token = "Lf4BESerTmA7mX7j-n-gVBUCBN49xN22gidM4bWgAA3bYYuMysnCe8HvM3C";
		$this->hasPayment = false;
		$this->validTX = false;
		$this->paymentStatus = '';
		$this->responseValues = null;
	}
	function check_payment() {
		$token = $this->token;
		if (isset($_GET['tx'])) {
			$this->hasPayment = true;
			$url = "https://www.paypal.com/cgi-bin/webscr";
			if (USE_SANDBOX) $url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
			$data = array(
				'cmd' => "_notify-synch",
				'tx' => $_GET['tx'],
				'at' => $token,
			);
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data),
				),
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
			VarTools::predump($result);
			$lines = preg_split("/\r\n|\n|\r/", $result);
			if (! $lines[0] === 'SUCCESS') {
				$this->validTX = false;
				$this->responseValues = null;
			} else {
				$this->validTX = true;
				$this->responseValues = array();
				for ($i=0;$i<count($lines);$i++) {
					$parts = explode('=', $lines[$i]);
					$key = $parts[0]; $value = urldecode($parts[1]);
					$this->responseValues[$key] = $value;
					$this->paymentStatus = strtolower($this->responseValues['payment_status']);
				}
			}
		} else {
			$this->hasPayment = false;
		}
	}
	function has_payment() {
		return $this->hasPayment;
	}
	function has_valid_tx() {
		return $this->validTX;
	}
	function get_payment_status() {
		return $this->paymentStatus;
	}
	function get_value($key) {
		if ($this->responseValues !== null) {
			return $this->responseValues[$key];
		}
	}
}
