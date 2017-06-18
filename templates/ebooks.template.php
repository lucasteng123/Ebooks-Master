<h1 class="blog-title"><?php echo $ss->get_html('ebookstitle'); ?></h1>

<?php foreach ($posts as $post) { ?>
	<div class="panel panel-default ebooks-blog-post">
		<div class="panel-body">
			<img class="smaller" src="<?php echo $post->get_img_src(); ?>" />
			<h2><?php echo ucwords($post->get_title()); ?></h2>
			<?php
			$previewLength = 80*5;
			$text = $post->get_text();
			$text = (strlen($text) > $previewLength) ? substr($text, 0, $previewLength).'...' : $text;
			$text = htmlspecialchars($text);
			//$text = nl2br($text);
			echo $text;
			?>
			<a href="<?php echo WEB_PATH.'/ebooks/'.$post->get_id(); ?>">[click here to read more]</a>
		</div>
	</div>
<?php } ?>