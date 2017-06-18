<html>
<head>
	<title>CLASSIFIED -- ABCOF</title>
	<?php
		HtmlShortcuts::includeCSS('./res/css/e_normalize.css');
		HtmlShortcuts::includeCSS('./res/css/print.css');
	?>
	<style type="text/css">
	@page {
		size: 21cm 29.7cm;
		/*margin: 30mm 45mm 30mm 45mm;*/
	}

	.page-container {
		width: 8.5in;
	}
	.label {
		display: inline-block;
		width: 2.635in;
		min-height: 1in;
		border: 1px solid #CCC;
		padding-left: 5mm;
		padding-top: 5mm;
		vertical-align: top;
		margin-top: 5mm;
		background-image: url('../watermrk_ohalf.png');
		background-position: bottom right;
		background-repeat: no-repeat;
		-webkit-print-color-adjust: exact;
		word-wrap: break-word;
	}
	.label li {
		list-style: none;
	}
	span.whiteline {
		background-color: #FFF;
	}
	.label .title {
		font-size: 24px;
		font-family: sans-serif;
	}
	</style>
</head>
<body>
	<div class="page-container">

	<?php foreach ($list as $item) { ?>
		<div class="label">
			<li><span class="title whiteline"><?php echo $item['name']; ?></span></li>
			<li><span class="whiteline"><?php echo $item['email']; ?></span></li>
			<li>Guess: <span class="whiteline"><?php echo $item['guess']; ?></span></li>
		</div>
	<?php } ?>
	</div>
</body>
</html>
