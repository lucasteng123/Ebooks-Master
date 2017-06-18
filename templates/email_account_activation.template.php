<!--
BestEbooks.ca
=============

If you can read this, that means this email message is
not correctly formatted by your software!

Don't worry, that's okay! The important thing here is
that you activate your account on BestEbooks.ca!

If you did not make an account on BestEbooks.ca, just
ignore this message.

To activate your account, use the following link. Note
that your account may be removed later if you don't do
this.

<?php echo $activation_link; ?>


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
			<div class="heading" style="font-size: 24px; color: #A00;">Welcome to BestEbooks.ca!</div>
			<div class="infotext"><?php echo $ss->get_html("email.account_welcome"); ?></div>
			<div class="spacer-vert-15" style="
				display: inline-block;
				width: 100%;
				height: 15px;
				"></div>
			<div class="heading" style="font-size: 24px; color: #A00;">Activate Your Account!</div>
			<div class="infotext" style="font-size: 14px; margin-top: 7px; max-width: 400pt;"><?php echo $ss->get_html("email.account_activate"); ?></div>
			<a class="activ" target="_blank" href="<?php echo $activation_link; ?>" style="
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
				">Activate</a>
			<br />
			or, follow this link in your browser:<br />
			<?php echo $activation_link; ?>
		</div>
	</body>
</html>