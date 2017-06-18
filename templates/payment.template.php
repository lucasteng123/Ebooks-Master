<?php if ($has_payment) { ?>
<div class="alert <?php echo ($has_error) ? 'alert-danger' : 'alert-success' ?>"><?php echo $pay_msg; ?></div>
<?php } ?>
<div class="alert alert-info">
	Click a book to add a video to your book and view more information.
</div>
<div id="resultset_1" class="bookslist">
	<?php foreach ($user_books as $book) {
		foreach ($book as $key => $val) {
			$book[$key] = nl2br(htmlentities($val));
		}
		$image = $book['filename'];
		$title = $book['title'];
		$author = $book['author'];
		$link = $book['link'];
		$price = $book['price'];
		$desc = $book['short_description'];
		$bid = $book['id'];
		$vis = $book['visibility'];
		//echo "<pre>"; print_r($book); echo "</pre>";
		$visList = array(
			"unchecked" => "Awaiting Approval",
			"unpaid" => "Payment Required",
			"public" => "Present in Public Listings",
		);
		$visibility = (array_key_exists($vis, $visList)) ? $visList[$vis] : ucwords($vis);
	?>
		<a href="?location=book/<?php echo $bid; ?>" class="booktile_link">
			<div class="booktile">
				<div class="cover-container">
					<div class="cover test-cover" style="background-image: url('./uploads/img/<?php echo $image; ?>');"></div>
				</div>
				<div class="text-container">
					<h3 class="text-primary"><?php echo $title; ?> <small><?php echo $author; ?></small></h3>
					<?php //echo $price; ?>
					<table>
						<tr>
							<td>Status:</td>
							<td><?php echo $visibility; ?></td>
						</tr>
					</table>
				</div>
			</div><br />
		</a>
	<?php } ?>
</div>
<nav>
	<ul class="pagination">
	<?php
		for ($i=0; $i < $user_books_pagec; $i++) {
			$pageN = $i + 1;
			$url = WEB_PATH . "?location=payment/".$pageN;
			$active = ($pageN == $user_books_page) ? ' class="active"' : '';
			echo "<li$active><a href=\"$url\">$pageN</a></li>\n";
		}
	?>
	</ul>
</nav>