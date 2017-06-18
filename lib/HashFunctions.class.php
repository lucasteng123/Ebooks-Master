<?php // Included by: other php files in system
class HashFunctions { // Because namespaces in PHP sort of don't work at all :/
	public static function generate_salt() {
		//Note: CURRENTLY USING RAND FUNCTION W/O SEED!
		$rndstring = "";
		$length = 64;
		$a = "";
		$b = "";
		$template = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		settype($length, "integer");
		settype($rndstring, "string");
		settype($a, "integer");
		settype($b, "integer");
		for ($a = 0; $a <= $length; $a++) {
			$b = rand(0, strlen($template) - 1);
			$rndstring .= $template[$b];
		}
		return $rndstring;
	}

	public static function compute_hash($pass,$salt) {
		return hash('sha256', $salt . $pass);
	}

	public static function verify_password($pass,$salt,$hash) {
		if (self::compute_hash($pass,$salt) === $hash) return true;
		else return false;
	}


	// Odd-ball function - really just for convenience when using AccountMgr.class
	public static function generate_activation_code($chars = false) {
		$rndstring = "";
		$length = 7;
		$a = "";
		$b = "";
		$template = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($chars !== false) {
			$template = $chars;
		}
		settype($length, "integer");
		settype($rndstring, "string");
		settype($a, "integer");
		settype($b, "integer");
		for ($a = 0; $a <= $length; $a++) {
			$b = rand(0, strlen($template) - 1);
			$rndstring .= $template[$b];
		}
		return $rndstring;
	}
}
