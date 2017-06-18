<!--
BestEbooks.ca
=============

If you can read this, that means this email message is
not correctly formatted by your software!

Don't worry, that's okay! The important thing here is
that you send a password reset request on BestEbooks.ca!

If you did not try to reset your password, or accidentally
clicked the "forgot" button, just ignore this message.

To reset your password, use the following link.

<?php echo $reset_pass_link; ?>


-->
<html>
	<head>
		<title></title>
		<meta content="">
		<style>
			BODY {
				padding: 0;
				margin: 0;
			}
			.title {
				height: 40px;
				line-height: 40px;
				font-size: 20px;
				font-family: sans-serif;
				text-align: left;
				padding: 0 15px;
				background-color: #000;
				color: #EEE;
			}
			.heading {
				font-size: 24px;
				color: #A00;
			}
			.infotext {
				font-size: 14px;
				margin-top: 7px;
				max-width: 400pt;
			}
			.padded-contents {
				padding: 15px;
			}
			.spacer-vert-15 {
				display: inline-block;
				width: 100%;
				height: 15px;
			}
			.activ {
				display: inline-block;
				width: 100px;
				height: 40px;
				margin: 15px 0;
				line-height: 40px;
				background-color: #0A0;
				color: #fff;
				text-decoration: none;
				text-align: center;
				font-family: sans-serif;
				font-weight: bold;
				-webkit-border-radius: 6px;
				border-radius: 6px;
			}
			.activ:hover {
				background-color: #0D0;
			}

			.activ:link {
				color: #DDD;
			}
		</style>
	</head>
	<body>
		<div class="title" style="
				height: 40px;
				line-height: 40px;
				font-size: 20px;
				font-family: sans-serif;
				text-align: left;
				padding: 0 15px;
				background-color: #000;
				color: #EEE;
				">BestEbooks.ca</div>
		<div class="padded-contents" style="padding: 15px;">
			<div class="heading" style="font-size: 24px; color: #A00;">Password Reset</div>
			<div class="infotext"><?php echo $ss->get_html("email.account_pass_reset"); ?></div>
			<a class="activ" target="_blank" href="<?php echo $reset_pass_link; ?>" style="
				display: inline-block;
				padding: 0 15px;
				height: 40px;
				margin: 15px 0;
				line-height: 40px;
				background-color: #0A0;
				color: #fff;
				text-decoration: none;
				text-align: center;
				font-family: sans-serif;
				font-weight: bold;
				-webkit-border-radius: 6px;
				border-radius: 6px;
				">Reset Password</a>
			<br />
			or, follow this link in your browser:<br />
			<?php echo $reset_pass_link; ?>
		</div>
	</body>
</html>