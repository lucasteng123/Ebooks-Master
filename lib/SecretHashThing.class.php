<?php
class SecretHashThing {
	public static function hash($mail) {
		return substr(HashFunctions::compute_hash($mail,"s3cr37h@s#c0d3_@dfgo:gdrnhui:[dxjhdrf5689#8f^3&*gd{\91234"),4,14);
	}
}
