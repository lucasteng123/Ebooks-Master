<?php

foreach ($bookinfo as $key => $val) {
	$bookinfo[$key] = nl2br(htmlentities($val));
}

$tmpl_string_views = (($v = $bookinfo['views']) == 1) ? $v." view" : $v." views";
$tmpl_string_datetime = str_replace('@','at',date("M j, Y @ G:i",strtotime($bookinfo['date_posted'])));

$visibility = $bookinfo['visibility'];
if ($visibility=='public') $visibility = "Shows up in results.";
if ($visibility=='unchecked') $visibility = "DOES NOT show in results. (awaiting approval) Visible with link.";
?>
<div class="bookview container-fluid no-spacing" data-direction="ascending">
	<div class="row">
		<div class="col col-xs-12">
			<a href="<?php echo WEB_PATH.'/?location=book/'.$bookinfo['id']; ?>"><div class="btn btn-primary">Back to Book</div></a>
			<br /><br />

<form class="form-horizontal" action="<?php echo WEB_PATH.'/?location=edit_book/'.$bookinfo['id']; ?>" method="POST">

	<!-- Form Name -->
	<legend>Edit Book</legend>

	<!-- ISBN -->
	<div class="form-group">
		<label class="col-md-3 control-label" for="isbn">ISBN (optional)</label>
		<div class="col-md-9">
			<input id="isbn" name="isbn" type="text" placeholder="978-3-16-148410-0" class="normal-input form-control input-md"
			value="<?php echo $bookinfo['isbn']; ?>">
		</div>
	</div>
	<!-- Book Title -->
	<div class="form-group">
		<label class="col-md-3 control-label" for="book_title">Book Title</label>
		<div class="col-md-9">
			<input id="book_title" name="book_title" type="text" placeholder="The Science of Deduction" class="normal-input form-control input-md" required="" value="<?php echo $bookinfo['title']; ?>">
		</div>
	</div>
	<?php /*
	<!-- Category Selection -->
	<div class="form-group">
		<label class="col-md-3 control-label" for="category">Category</label>
		<div class="col-md-9">
			<select id="category" name="category" class="multiplesel-input form-control selectpicker" multiple>
				<?php
				foreach ($categories as $item) {
					$name = $item['name'];
					$baseid = $item['id'];
					foreach ($item['categories'] as $subcat) {
						$sname = $subcat['name'];
						$sid = $subcat['id'];
						echo '<option value="'.$sid.'">'.$name.' &gt; '.$sname.'</option>';
					}
				}
				?>
			</select>
		</div>
	</div>
	*/ ?>
	<!-- Author -->
	<div class="form-group">
		<label class="col-md-3 control-label" for="book_author">Author</label>
		<div class="col-md-9">
			<input id="book_author" name="book_author" type="text" placeholder="Sherlock Holmes" class="normal-input form-control input-md" required=""
			value="<?php echo $bookinfo['author']; ?>">
		</div>
	</div>
	<!-- Description -->
	<div class="form-group">
		<label class="col-md-3 control-label" for="book_description">Description</label>
		<div class="col-md-9 validate-group" data-ms="1200">
			<textarea id="book_description" name="book_description" type="text" class="normal-input form-control input-md" placeholder="Text" required=""><?php echo htmlentities($bookinfo['description']); ?></textarea>
			<p class="help-block">12,000 words maximum</p>
		</div>
	</div>
	<?php /*
	<!-- Cover Image Upload -->
	<div class="form-group">
		<label class="col-md-3 control-label" for="cover_image">Book Cover</label>
		<div class="col-md-9">
			<div class="input-group">
				<span class="input-group-btn">
					<span class="progress-under-button progress-bar progress-bar-striped active"></span>
					<span class="file-upload-button btn btn-primary btn-file">Browse...
						<input name="cover_image" class="ajax-file-upload file-input" type="file" multiple />
					</span>
				</span>
				<input type="text" class="pseudo-input form-control input-md" readonly tabIndex="-1" placeholder="Maximum size: 1000 KB" />
			</div>
		</div>
	</div>
	*/ ?>
	<!-- Link -->
	<div class="form-group">
		<label class="col-md-3 control-label" for="book_link">Link</label>
		<div class="col-md-9">
			<input id="book_link" name="book_link" type="text" placeholder="http://www.amazon.ca/A-Book-ebook" class="normal-input form-control input-md" required="" value="<?php echo $bookinfo['link']; ?>">
			<p class="help-block">Amazon Kindle, Nook. Kobo, iTunes, and others.</p>
		</div>
	</div>
	<!-- Price on Site -->
	<div class="form-group">
		<label class="col-md-3 control-label" for="book_price">Price (CAD)</label>
		<div class="col-md-9">
			<input id="book_price" name="book_price" type="text" class="normal-input form-control input-md" placeholder="$1.00" required=""
			value="<?php echo $bookinfo['price']; ?>" />
		</div>
	</div>

	<!-- Button (Double) -->
	<div class="form-group">
		<label class="col-md-3 control-label sr-only" for="submitbook">Submit, or Cancel?</label>
		<div class="col-md-9">
			<input type="submit" id="submitbook" name="submitbook" class="btn btn-success" value="Submit Changes" />
		</div>
	</div>

</form>

		</div>
	</div>
	<?php if ($displayVideo) { ?>
	<div class="row">
		<div class="col col-xs-12">
			<hr />
			<?php if ($displayVideoModerationWarning) { ?>
			<div class="alert alert-danger">
				This is a preview. Your video will not be visible for other users until it has been checked and approved.
			</div>
			<?php } ?>
			<div class="media-div">
				<div class="video-canvas">
					<iframe class="video" <?php /*width="420" height="315"*/ ?> src="https://www.youtube.com/embed/<?php echo $bookinfo['video_url'] ?>" frameborder="0" allowfullscreen></iframe>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
	<?php if (false) {?>
	<div class="row">
		<div class="col col-xs-12">
			<hr />
			<pre>
				<?php print_r($bookinfo); ?>
			</pre>
		</div>
	</div>
	<?php } ?>
</div>
