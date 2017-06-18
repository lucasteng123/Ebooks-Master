<html>
	<head>
		<title></title>
		<meta content="">
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
				">BestEbooks.ca Newsletter</div>
		<div class="padded-contents" style="padding: 15px;">
			<div class="heading" style="font-size: 24px; color: #A00;"><?php echo htmlentities($title); ?></div><br />
			<div class="infotext"><?php echo nl2br(htmlentities($contents)); ?></div>
			<a class="activ" target="_blank" href="<?php echo "http://bestebooks.ca"; ?>" style="
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
				">Go to BestEbooks.ca</a>
			<br />
			<?php if (isset($image)) { ?>
				<img src="<?php echo $image; ?>" />
			<?php } ?>
			<br />
			<a href="<?php echo $link; ?>">Unsubscribe</a>
		</div>
	</body>
</html>
<!-- GMail doesnt support style tags and that's rather disappointing -->