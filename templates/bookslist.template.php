<!--<pre style="font-size: 8px; line-height: 10px;">
<?php
	//print_r($row);
?>
</pre>-->

<?php //echo "<h1>[test:".$sort."]</h1>"; ?>





<?php if ( ! isset($nogui) ) { ?>

<?php
if (isset($videovote_tmpl)) {
	$videovote_tmpl->title = $page_info['base_category_name'];
	$videovote_tmpl->run();
} else { ?>

<h1 class="bookslist-title"><?php echo str_replace('>','<span class="glyphicon glyphicon-chevron-right"></span>',$page_info['title']); ?></h1>

<?php } ?>

<?php } ?>

<span class="ajax-book-results-wctrl"><!-- wrapper for ajax loading -->

	<?php if ( isset($show_searchbar) ) { ?>
	<span class="bookslist-search-style">
		<div class="search-area container-fluid">
			<div class="row">
				<div class="col col-xs-12 no-spacing">
					<form class="form-horizontal ajax-search" role="form" data-query="<?php echo WEB_PATH.'?location=bookslist&'.$base_query_search; ?>">
						<!-- TODO: Figure out what "role" is actually for. -->
						<div class="form-group">
							<label class="control-label sr-only">Search:</label>
							<div class="col-sm-10 col-xs-9">
								<input type="text" class="form-control search-input ajax-search-input" placeholder="Search within <?php echo $page_info['scope_name']; ?>" value="<?php echo htmlentities($results_info['query']); ?>" />
							</div>
							<div class="col-sm-2 col-xs-3">
								<!--
								<input type="button" class="form-control button_b" />
								-->
								<button type="button" class="btn btn-default btn-bolded btn-block button_b"><span class="glyphicon glyphicon-search ajax-search-button"></span><span class="text"> Find</span></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</span>
	<?php } ?>


	<?php if ( ! isset($noctrl) ) { ?>
	<div class="controls">
		<div class="btn-group result-sort-buttons" data-resultset-id="resultset_2">
			<?php
				$buttons = array(
					'author' => array('author','Author','glyphicon-sort-by-alphabet','glyphicon-sort-by-alphabet-alt'),
					'title' => array('title','Title','glyphicon-sort-by-alphabet','glyphicon-sort-by-alphabet-alt'),
					'date' => array('date','Uploaded','glyphicon-sort-by-order','glyphicon-sort-by-order-alt'),
					'views' => array('views','Views','glyphicon-sort-by-attributes','glyphicon-sort-by-attributes-alt')
				);
				foreach ($buttons as $name => $b) {
					$qry = $base_query_sorting."&sort=".$b[0];
					$selStyle = ' style="display:inline;"';
					$pri = '';
					$s1 = ''; $s2 = '';
					$pri = " btn-default";
					$dd = ' data-direction="ascending"';
					if ($name == 'views' || $name == 'date')
						$dd = ' data-direction="descending" data-reverse-direction="1"';
					if ($sort==$b[0]) {
						$pri = " btn-primary";
						if ($order=='desc') {
							$s2 = $selStyle;
							$dd = ' data-direction="descending"';
						} else {
							$s1 = $selStyle;
							$dd = ' data-direction="ascending"';
						}
					}
					$label = $b[1];
			?>
				<button
				data-query="<?php echo $qry ?>" type="button"
				class="btn result-sort-btn<?php echo $pri ?>"<?php echo $dd; ?>>
					<?php echo $label ?>
					<span class="glyphicon <?php echo $b[2]; ?> icon-ascending"<?php echo $s1; ?>></span>
					<span class="glyphicon <?php echo $b[3]; ?> icon-descending"<?php echo $s2; ?>></span>
				</button>
			<?php } ?>
		</div>

		<?php ob_start(); ?>
		<ul class="pagination rfloat">
			<?php
				// To anybody who may have to edit this code
				// in the future, I sincerely apologize.
				$p = $page;

				// Prev & Next
				$prevOffset = $result_count*($p - 2);
				$nextOffset = $result_count*($p);
				// Note: 10*(p-1) is the offset for page p, not page p-1.
				// this is because page 1 is at offset zero, page 2 at 10, etc...

				// Previous Page Arrow
				if ($p != 1) {
					$data = 'data-query="'.$base_query_pages.'&off='.$prevOffset.'"';
					echo '<li '.$data.' class="page-choosey-button"><a href="#"><span class="glyphicon glyphicon-chevron-left icon-ascending"></span></a></li>';
				}

				// All Page Numbers
				{
					$leftPages = $p - 1;
					{
						if ($leftPages > 4) $leftPages = 4;
					}
					$rightPages = 2;
					if ($p == 1) $rightPages = 4;
					else if ($p == 2) $rightPages = 3;

					for ($i=$p-$leftPages; $i < $p; $i++) {
						$newOffset = $result_count*($i - 1);
						$data = 'data-query="'.$base_query_pages.'&off='.$newOffset.'"';
						echo '<li '.$data.' class="page-choosey-button"><a href="#">'.$i.'</a></li>';
					}
					echo '<li class="active"><a href="#">'.$p.'</a></li>';
					$showNextArrow = true; $j = 0;
					for ($i=$p+1; $i <= $p+$rightPages; $i++) {
						if ($results_info['nextpage'][$j]) {
							$newOffset = $result_count*($i - 1);
							$data = 'data-query="'.$base_query_pages.'&off='.$newOffset.'"';
							echo '<li '.$data.' class="page-choosey-button"><a href="#">'.$i.'</a></li>';
						} else {
							//echo "What?".$j.":".$results_info['nextpage'][$j];
							$showNextArrow = false;
							break;
						}
						$j++;
					}
				}

				// Next Page Arrow
				if ($showNextArrow) {
					$data = 'data-query="'.$base_query_pages.'&off='.$nextOffset.'"';
					echo '<li '.$data.' class="page-choosey-button"><a href="#"><span class="glyphicon glyphicon-chevron-right icon-ascending"></span></a></li>';
				}
			?>
			<!--
			<li><a href="#">1</a></li>
			<li><a href="#">2</a></li>
			<li><a href="#">3</a></li>
			<li><a href="#">4</a></li>
			<li><a href="#">5</a></li>
			-->
			<!--<li><a href="#"><span class="glyphicon glyphicon-chevron-right icon-ascending"></span></a></li>-->
		</ul>
		<?php $paginationBuffer = ob_get_clean(); ?>
		<?php echo $paginationBuffer; ?>

	<?php } ?>
	</div>




<?php if ($results_info['results_count'] < 1) {
	//$nogui = true;
	$noctrl = true;
	?>
	<?php if ($results_info['hasquery']) { ?>
		<h2>No Results</h2>
		<h3>We couldn't find what you were looking for. Try removing words or changing your spelling.</h3>
	<?php } else { ?>
		<h2>Nothing Here Yet</h2>
		<h3>This category is empty! You could be the first to submit a book here; just click the "Submit Book" link at the top of the page!</h3>
	<?php } ?>
<?php } ?>



	<div id="resultset_1" class="ajax-books-resultset bookslist" data-direction="ascending">
		<?php foreach ($books as $book) {
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
			//echo "<pre>"; print_r($book); echo "</pre>";
		?>
			<a href="?location=book/<?php echo $bid; ?>">
				<div class="bookitem">
					<div class="cover-container">
						<div class="cover test-cover" style="background-image: url('./uploads/img/<?php echo $image; ?>');"></div>
					</div>
					<div class="text-container">
						<h3 class="text-primary"><?php echo $title; ?> <small><?php echo $author; ?></small></h3>
						<?php //echo $price; ?>
						<p>
							<?php echo $desc; ?>
						</p>
					</div>
				</div><br />
			</a>
		<?php } ?>
	</div>
	
	<?php echo $paginationBuffer; ?>
</span>