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
			<div class="heading" style="font-size: 24px; color: #A00;">A new account has been created!</div>
			<div class="infotext">
				Name: <?php echo $name; ?><br />
				Email: <?php echo $email; ?><br />
			</div>
			<br />
		</div>
	</body>
</html>