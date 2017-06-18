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
			<?php
				$show_video_upload = (isset($show_owner_control) && $show_owner_control==true)
					|| (isset($show_adminfo) && $show_adminfo==true);
			?>
			<!-- Payment Box -->
			<?php if (isset($show_unpaid) && $show_unpaid==true) { ?>
				<div class="user-controls">
				<!-- PayPal -->
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="GRMNMNVTUCRNQ">
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
				<!-- /PayPal -->
				<span>Currently your book will not show up in search results.<br />You will need to buy your book placement.</span>
				</div>
			<?php } ?>
			<!-- User Options -->
			<?php if ($show_video_upload) { ?>
				<div class="user-controls<?php if ($show_adminfo) { echo " admin-coloured"; } ?>">
					<h4>Customize Your Book:</h4>
					<form class="ajax-form form-horizontal" style="width:100%;" data-name="custom_book_video">
							<input type="hidden" name="id" value="<?php echo $bookid; ?>" />

							<legend class="h5">Add a Video</legend>
							<div class="form-group">
								<label class="col-md-2 control-label">YouTube URL</label>
								<div class="col-md-8">
									<input name="url" type="text" placeholder="https://www.youtube.com/watch?v=example" class="normal-input form-control" required="">
								</div>
								<div class="col-md-2">
									<input type="submit" value="Set Video!" class="btn btn-primary btn-block">
								</div>
							</div>
							<div class="message" style="display:none;">Reloading in <div class="timer">1</div></div>
					</form>
				</div>
			<?php } ?>

			<!-- Admin Controls -->
			<?php if (isset($show_adminfo) && $show_adminfo==true) { ?>
				<div class="adminfo">
				<h4>Admin Information:</h4>
				<table class="outer_table">
					<tr>
						<td>Current Visibiliity:</td>
						<td>[<?php echo $bookinfo['visibility']; ?>] <?php echo $visibility; ?></td>
					</tr>
					<!--<tr>
						<td>IP Address:</td>
						<td>N/A</td>
					</tr>-->
					<tr>
						<td>Uploader:</td>
						<td>
							<table>
								<tr>
									<td>Name</td>
									<td><?php echo $bookinfo['uploader_name']; ?></td>
								</tr>
								<tr>
									<td>Email</td>
									<td><?php echo $bookinfo['uploader_email']; ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>Feature this book?</td>
						<td>
							<form class="ajax-form" data-name="mod_book_feature">
								<?php if ($bookinfo['featured'] == 1) { ?>
									<input name="remove" class="btn btn-xs btn-default" type="submit" value="Remove From Featured" />
								<?php } else { ?>
									<input name="feature" class="btn btn-xs btn-default" type="submit" value="Feature This Book" />
								<?php } ?>
								<input type="hidden" name="id" value="<?php echo $bookid; ?>" />
								<input type="hidden" name="submitted" />
								<div class="message" style="display:none;">Reloading in <div class="timer">1</div></div>
							</form>
						</td>
					</tr>
					<tr>
						<td>Modify Link</td>
						<td>
							<form class="ajax-form" data-name="mod_book_link">
								<input name="link" type="text" class="input-xs" value="<?php echo $bookinfo['link']; ?>" />
								<input name="submit" class="btn btn-xs btn-default" type="submit" value="Change" />
								<input type="hidden" name="id" value="<?php echo $bookid; ?>" />
								<input type="hidden" name="submitted" />
								<div class="message" style="display:none;">Reloading in <div class="timer">1</div></div>
							</form>
						</td>
					</tr>
					<tr>
						<td>More Options</td>
						<td>
							<form class="ajax-form" data-name="mod_book_simple">
								<input name="delete" class="btn btn-xs btn-default" type="submit" value="Delete Book" />
								<input name="set_unpaid" class="btn btn-xs btn-default" type="submit" value="Set As Unpaid" />
								<input name="set_unchecked" class="btn btn-xs btn-default" type="submit" value="Set As Unchecked" />
								<input name="set_public" class="btn btn-xs btn-default" type="submit" value="Set As Public" />
								<input type="hidden" name="id" value="<?php echo $bookid; ?>" />
								<input type="hidden" name="submitted" />
								<div class="message" style="display:none;"></div>
							</form>
						</td>
					</tr>
					<tr>
						<td>Upload New Cover</td>
						<td>
							<form class="ajax-form form-inline" data-name="mod_book_cover">
								<input class="normal-input" type="hidden" name="id" value="<?php echo $bookid; ?>" />
								<div class="input-group">
									<input class="file-input" name="cover_image" type="file" multiple />
								</div>
								<div class="input-group">
									<input type="submit" class="btn btn-xs btn-primary" />
								</div>
								<div class="message" style="display:none;">Reloading in <div class="timer">1</div></div>
							</form>
						</td>
					</tr>
					<tr>
						<td>&nbsp</td>
						<td>
							<a href="<?php echo WEB_PATH.'/?location=edit_book/'.$bookid; ?>">
								<input type="button" value="Open Book Editor" />
							</a>
						</td>
					</tr>
					<!--<tr>
						<td>Set Visibility Silently:</td>
						<td>
							<input class="btn btn-xs btn-default" type="button" value="public" title="Shows up in results." />
							<input class="btn btn-xs btn-default" type="button" value="unchecked" title="Put back on approve/decline page." />
							<input class="btn btn-xs btn-default" type="button" value="rejected" title="DOES NOT show in results, NOT visible with link. (unless admin)" />
						</td>
					</tr>-->
				</table>
				</div>
			<?php } ?>
			<!-- / Admin Controls -->






			<div class="title no-spacing col col-xs-12 h1"><?php echo $bookinfo['title']; ?><br />
			<small><?php echo $bookinfo['author']; ?><small></small></small>
			<div class="spacer"></div>
			</div>
			<!--
			<div class="rating no-spacing col col-xs-12">
				Book Quality: 
				<span class="ajax-rating-group">
					<img class="ajax-rating-star" src="res/img/star_on.png" />
					<img class="ajax-rating-star" src="res/img/star_on.png" />
					<img class="ajax-rating-star" src="res/img/star_on.png" />
					<img class="ajax-rating-star" src="res/img/star_off.png" />
					<img class="ajax-rating-star" src="res/img/star_off.png" />
				</span>
			</div>
			-->
			<div class="col col-xs-4 col-md-3 col-lg-2 cover-column no-spacing">
				<div class="cover-container">
					<img class="cover" src="uploads/img/<?php echo $bookinfo['filename'] ?>" />
					<img class="cover-highlight" src="res/img/bookover.png" />
				</div>
				<div class="spacer-3rd"></div>
				<a href="<?php echo $bookinfo['link']; ?>" target="_blank">
					<div class="btn btn-block btn-primary">Buy This Book<br /><small>Click Here<?php //echo $book_link_name; ?></small></div>
				</a>
				<div class="price">$<?php echo $book_price; ?> (CAD)</div>
			</div>
			<div class="col col-xs-8 col-md-9 col-lg-10">
				<div class="h3 no-spacing"><?php echo $bookinfo['title']; ?><br />
				<small>
				<?php echo $tmpl_string_views; ?> - Posted on <?php echo $tmpl_string_datetime; ?>
				</small>
				</div>
				<ul class="share-buttons">
				<!-- Facebook Like -->
					<li>
						<div class="fb-like" data-href="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
					</li>
					<!-- Twitter Tweet -->
					<li>
						<a class="twitter-share-button" href="https://twitter.com/intent/tweet?text=Take%20a%20look%20at%20this!">Tweet</a>
					</li>
					<!-- Google +1 -->
					<li>
						<div class="g-plusone" data-size="medium"></div>
					</li>
				</ul>
				<br />
				<div class="description-column">
					<?php echo $bookinfo['description']; ?>
				</div>
			</div>
			<div class="col col-xs-2 pull-right" class="home-button-container">
				<a href="<?php echo WEB_PATH; ?>"><div class="btn btn-primary">Back to Home</div></a>
			</div>
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
