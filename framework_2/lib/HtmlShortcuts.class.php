<?php
class HtmlShortcuts {
	static function drawTab($text,$from,$to) {
		global $default_page;
		print('<div class="tab"><div class="tabbox" onClick="ajaxLoad(\''.$from.'\',\''.$to.'\')">'.$text.'</div></div>');
		if ($default_page==$text) {
			$default_page=$from;
		}
	}
	static function drawFullBackground($src) {
		print("<img src=\"$src\" style=\"position:fixed;top:0;left:0;width:100%;height:100%;z-index:-20\" />");
	}
	static function includeCSS($src) {
		print('<link rel="stylesheet" type="text/css" href="'.$src.'">');
	}
	static function includeJS($src) {
		print('<script src="'.$src.'"></script>');
	}
	static function includeJQ() {
		print('<script src="http://code.jquery.com/jquery-latest.js"></script>');
	}
	static function metaCharset($set) {
		print('<meta http-equiv="Content-Type" content="text/html" charset="'.$set.'" />');
	}

	static function pre($text) {
		print("<pre>\n");
		print($text."\n");
		print("</pre>\n");
	}
	static function ln() {
		print("\n");
	}
	static function expCSSProp($property, $value) {
		$str="";
		$prefixes = array('-webkit-','-moz-','-o-','-ms-','');
		foreach ($prefixes as $prefix) {
			$str .= $prefix . $property . ":" . $value . ";";
		}
		return $str;
	}
}
?>
