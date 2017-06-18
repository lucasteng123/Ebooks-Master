<?php
define('USE_SANDBOX', true);
//Lf4BESerTmA7mX7j-n-gVBUCBN49xN22gidM4bWgAA3bYYuMysnCe8HvM3C
$token = "Lf4BESerTmA7mX7j-n-gVBUCBN49xN22gidM4bWgAA3bYYuMysnCe8HvM3C";
if (count($_POST) > 0) echo '<pre>'.print_r($_POST).'</pre>';
if (count($_GET) > 0) echo '<pre>'.print_r($_GET).'</pre>';

if (isset($_GET['tx'])) {
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
	$lines = preg_split("/\r\n|\n|\r/", $result);
	echo '<pre name="response">'.$result.'</pre>';
	if (! $lines[0] === 'SUCCESS') {
		echo 'Unable to successfully get transaction information! Totally lame!';
	} else {
		echo '<pre>';
		for ($i=0;$i<count($lines);$i++) {
			$parts = explode('=', $lines[$i]);
			$key = $parts[0]; $value = urldecode($parts[1]);
			echo $key.': '.$value."\n";
		}
		echo '</pre>';
	}
}

?>
<h2>Test Form</h2>
<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="AM2CG3PJG9B38">
<input type="hidden" name="custom" value="7357">
<input type="image" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<hr />
<h2>Real Form</h2>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="GRMNMNVTUCRNQ">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<hr />