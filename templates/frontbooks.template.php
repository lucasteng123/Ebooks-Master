<?php function bookitems() { ?>
			<?php for ($i=0; $i<8; $i++) { ?>
			<div class="bookitem">
				<div class="cover-container coverflow-item">
					<div class="cover test-cover" style="background-image: url('./res/img/white-t-shirt.svg'); background-size: contain; background-color: none; background-repeat: no-repeat;"></div>
				</div>
				<div class="text-container">
					<h3 class="text-primary">Harry Potter 4 <small>J.K. Rowling</small></h3>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
					</p>
				</div>
			</div><!-- /.bookitem -->
			<?php } ?>
			<div class="bookitem">
				<div class="cover-container coverflow-item">
					<div class="cover test-cover" style="background-image: url('./res/img/white-t-shirt.svg'); background-size: contain; background-color: none; background-repeat: no-repeat;"></div>
				</div>
				<div class="text-container">
					<h3 class="text-primary">Harry Potter 6 <small>J.K. Rowling</small></h3>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
					</p>
				</div>
			</div><!-- /.bookitem -->
			<div class="bookitem">
				<div class="cover-container coverflow-item">
					<div class="cover test-cover" style="background-image: url('./res/img/white-t-shirt.svg'); background-size: contain; background-color: none; background-repeat: no-repeat;"></div>
				</div>
				<div class="text-container">
					<h3 class="text-primary">Harry Potter 4 <small>J.K. Rowling</small></h3>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
					</p>
				</div>
			</div><!-- /.bookitem -->
			<div class="bookitem">
				<div class="cover-container coverflow-item">
					<div class="cover test-cover" style="background-image: url('./res/img/white-t-shirt.svg'); background-size: contain; background-color: none; background-repeat: no-repeat;"></div>
				</div>
				<div class="text-container">
					<h3 class="text-primary">The Secret Under my Skin <small>Janet McNaughton</small></h3>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
					</p>
				</div>
			</div><!-- /.bookitem -->
			<div class="bookitem">
				<div class="cover-container coverflow-item">
					<div class="cover test-cover" style="background-image: url('./res/img/white-t-shirt.svg'); background-size: contain; background-color: none; background-repeat: no-repeat;"></div>
				</div>
				<div class="text-container">
					<h3 class="text-primary">Harry Potter 6 <small>J.K. Rowling</small></h3>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
					</p>
				</div>
			</div><!-- /.bookitem -->
<?php } ?>

<?php
$bookitems = function($books) {
	foreach ($books as $book) {
?>
	<div class="bookitem">
	<a href="?location=book/<?php echo $book['id']; ?>">
		<div class="cover-container coverflow-item">
			<div class="cover test-cover" style="background-image: url('./uploads/img/<?php echo $book['filename']; ?>');"></div>
		</div>
	</a>
		<div class="text-container">
			<h3 class="text-primary"><?php echo $book['title']; ?> <small><?php echo $book['author']; ?></small></h3>
			<p>
				<?php echo $book['short_description']; ?>
			</p>
		</div>
	</div><!-- /.bookitem -->
<?php
	}
};
?>

<!-- SLIDER FOR TSHIRTS -->
<div class="pagebox feature-pagebox">
	<div class="title"><?php echo $item['title']; ?></div>

	<div class="jq-page tshirt-canvas coverflow-canvas visible" data-name="featured">
		<div class="canvas-slide prev"><span class="glyphicon glyphicon-chevron-left"></span></div>
		<div class="canvas-slide next"><span class="glyphicon glyphicon-chevron-right"></span></div>
		<div class="canvas-slide-under prev"><div class="artificial-padding"></div><?php bookitems(); ?><div class="artificial-padding"></div></div>
		<div class="canvas-slide-under next"><div class="artificial-padding"></div><?php bookitems(); ?><div class="artificial-padding"></div></div>
		<div class="books-container"><!-- wrapper required for scrolling to work -->
			<?php bookitems(); ?>
			<div class="artificial-padding"></div>
		</div><!-- /.books-container -->
	</div><!-- /.feature-canvas -->
</div>

<div class="pagebox feature-pagebox jq-tab-scope">
	<ul class="nav nav-tabs feature-tabs">
		<li role="presentation" data-page="featured" class="jq-tab active"><a href="#">Featured</a></li>
		<li role="presentation" data-page="newest" class="jq-tab"><a href="#">Newest</a></li>
		<li role="presentation" data-page="views" class="jq-tab"><a href="#">Most Viewed</a></li>
		<li role="presentation" class="not-a-tab">BestEbooks</li>
	</ul>
	<div class="jq-page feature-canvas coverflow-canvas" data-name="newest">
		<div class="canvas-slide prev"><span class="glyphicon glyphicon-chevron-left"></span></div>
		<div class="canvas-slide next"><span class="glyphicon glyphicon-chevron-right"></span></div>
		<div class="canvas-slide-under prev"><div class="artificial-padding"></div><?php $bookitems($list_newest); ?><div class="artificial-padding"></div></div>
		<div class="canvas-slide-under next"><div class="artificial-padding"></div><?php $bookitems($list_newest); ?><div class="artificial-padding"></div></div>
		<div class="books-container"><!-- wrapper required for scrolling to work -->
			<?php $bookitems($list_newest); ?>
			<!-- Another padding div for Firefox, who wants to watch the world burn. -->
			<div class="artificial-padding"></div>
		</div><!-- /.books-container -->
	</div>
	<div class="jq-page feature-canvas coverflow-canvas" data-name="views">
		<div class="canvas-slide prev"><span class="glyphicon glyphicon-chevron-left"></span></div>
		<div class="canvas-slide next"><span class="glyphicon glyphicon-chevron-right"></span></div>
		<div class="canvas-slide-under prev"><div class="artificial-padding"></div><?php $bookitems($list_mostviewed); ?><div class="artificial-padding"></div></div>
		<div class="canvas-slide-under next"><div class="artificial-padding"></div><?php $bookitems($list_mostviewed); ?><div class="artificial-padding"></div></div>
		<div class="books-container"><!-- wrapper required for scrolling to work -->
			<?php $bookitems($list_mostviewed); ?>
			<!-- Another padding div for Firefox, who wants to watch the world burn. -->
			<div class="artificial-padding"></div>
		</div><!-- /.books-container -->
	</div>
	<div class="jq-page feature-canvas coverflow-canvas visible" data-name="featured"><!--
		<h2>Updating Admin Page<br />Meanwhile, Glitches May Happen<br /></h2>
		<p style="width:100%;white-space:normal;font-size: 18px; color: #A00">
		Testing book titles are randomly generated and therefore not predictable; viewer discretion advised.
		</p>-->
		<div class="canvas-slide prev"><span class="glyphicon glyphicon-chevron-left"></span></div>
		<div class="canvas-slide next"><span class="glyphicon glyphicon-chevron-right"></span></div>
		<div class="canvas-slide-under prev"><div class="artificial-padding"></div><?php $bookitems($list_featured); ?><div class="artificial-padding"></div></div>
		<div class="canvas-slide-under next"><div class="artificial-padding"></div><?php $bookitems($list_featured); ?><div class="artificial-padding"></div></div>
		<div class="books-container"><!-- wrapper required for scrolling to work -->
			<?php $bookitems($list_featured); ?>
			<!-- Another padding div for Firefox, who wants to watch the world burn. -->
			<div class="artificial-padding"></div>
		</div><!-- /.books-container -->
	</div><!-- /.feature-canvas -->
</div><!-- /.pagebox -->


<?php
if (isset($videovote_tmpl)) {
	$videovote_tmpl->run();
}
?>

<?php foreach ($page_book_lists as $item) { ?>
	<div class="pagebox feature-pagebox">
		<div class="title"><?php echo $item['title']; ?></div>

		<div class="jq-page feature-canvas coverflow-canvas visible" data-name="featured">
			<div class="canvas-slide prev"><span class="glyphicon glyphicon-chevron-left"></span></div>
			<div class="canvas-slide next"><span class="glyphicon glyphicon-chevron-right"></span></div>
			<div class="canvas-slide-under prev"><div class="artificial-padding"></div><?php $bookitems($item['books']); ?><div class="artificial-padding"></div></div>
			<div class="canvas-slide-under next"><div class="artificial-padding"></div><?php $bookitems($item['books']); ?><div class="artificial-padding"></div></div>
			<div class="books-container"><!-- wrapper required for scrolling to work -->
				<?php $bookitems($item['books']); ?>
				<div class="artificial-padding"></div>
			</div><!-- /.books-container -->
		</div><!-- /.feature-canvas -->

	</div><!-- /.pagebox -->
<?php } ?>

	<div class="panel panel-default guess-contest-panel" id="guess_contest_container">
		<div class="panel-heading">
			<div class="panel-title">
				<?php echo $gcTitle; ?>
			</div>
		</div>
		<div class="panel-body row guess-contest-box">
			<div class="col col-lg-3 visible-lg description-col">
				<div class="inner">
					<?php echo $gcDesc; ?>
				</div>
			</div>
			<div class="col col-lg-3 visible-lg">
				<!-- to make up for absolutely-positioned column -->
			</div>
			<?php
				$hasVideo = $video !== "none";
				$prizeChars = str_split($gcPrize);
			?>
			<div class="col col-xs-12 col-md-3 pull-right winners-col">
				<?php if ($hasVideo) { ?>
					<div class="container-fluid embed-responsive embed-responsive-4by3">
						<iframe class="embed-responsive-item embed-responsive-fish" src="https://www.youtube.com/embed/<?php echo $video; ?>" allowfullscreen></iframe>
					</div>
					<div class="winners-prize">
						<div class="money-tiles">
							<?php foreach($prizeChars as $char) {
								echo '<div class="tile">'.$char.'</div>';
							} ?>
						</div>
					</div>
				<?php } else { ?>
					<div class="winners-prize">
						<strong>Cash Giveaway</strong><br />
						<div class="money-tiles">
							<?php foreach($prizeChars as $char) {
								echo '<div class="tile">'.$char.'</div>';
							} ?>
						</div>
					</div>
					<div class="winners-roll">
						<strong>Winners Roll</strong><br />
						<?php foreach($gcWinners as $winner) { ?>
							<div><span class="name"><?php echo $winner['name']; ?></span></div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
			<div class="col col-xs-12 col-md-3 winners-col-placeholder hidden-xs hidden-sm pull-right">
				<!-- to make up for absolutely-positioned column -->
			</div>
			<div class="col col-xs-12 col-md-9 col-lg-6">
				<?php if ($isContestRunning) { ?>
					<div class="letter-boxes-holder guessing">
						<?php for ($i=0;$i<$wordLength;$i++) { ?>
						<div class="letter-box"></div>
						<?php } ?>
					</div>
					<div class="description-holder hidden-lg"><div class="inner">
						This is a description of the contest, maybe with a hint or something.
					</div></div>
					<div class="question-holder">
						<?php echo $gcQuestion; ?>
					</div>
					<div class="inputs-holder">
						<span name="guess_entry_form" class="form-inline">
							<input name="guess_entry" class="form-control" type="text" placeholder=""
							maxlength="<?php echo $wordLength; ?>" />
							<input name="guess_button" class="form-control btn-primary" type="button" value="Guess!" />
						</span>
						<span name="info_entry_form" class="form-horizontal dispnone">
							<div class="form-group">
								<label class="col-xs-2">Name: </label>
								<div class="col-xs-10">
									<input name="name" class="form-control" type="text" placeholder="Name Smith" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-2">Email: </label>
								<div class="col-xs-10">
									<input name="email" class="form-control" type="text" placeholder="example@example.com" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-2"></label>
								<div class="col-xs-10">
									<input name="enter_button" class="form-control btn-primary btn-block" type="button" value="Enter!" />
								</div>
							</div>
						</span>
						<span name="success_message" class="dispnone">
							<h3>Success!</h3>
							<p>Your guess has been sent.</p>
						</span>
					<?php } else { ?>
						<div class="inputs-holder">
							<h3>Contest is Currently Not Running</h3>
							Please check back again later!
						</div>
					<?php } ?>
				</div>
			</div>
		</div>

	</div>


<?php /*
<div class="pagebox feature-pagebox">
	<div class="title">Top books in Fiction</div>
	<div class="jq-page feature-canvas coverflow-canvas" data-name="newest">
		<h2>No Contents Yet for Newest</h2>
	</div>
	<div class="jq-page feature-canvas coverflow-canvas" data-name="views">
		<h2>No Contents Yet for Views</h2>
	</div>
	<div class="jq-page feature-canvas coverflow-canvas visible" data-name="featured">
		<div class="canvas-slide prev"><span class="glyphicon glyphicon-chevron-left"></span></div>
		<div class="canvas-slide next"><span class="glyphicon glyphicon-chevron-right"></span></div>
		<div class="canvas-slide-under prev"><div class="artificial-padding"></div><?php bookitems(); ?><div class="artificial-padding"></div></div>
		<div class="canvas-slide-under next"><div class="artificial-padding"></div><?php bookitems(); ?><div class="artificial-padding"></div></div>
		<div class="books-container"><!-- wrapper required for scrolling to work -->
			<?php bookitems(); ?>
			<div class="artificial-padding"></div>
		</div><!-- /.books-container -->
	</div><!-- /.feature-canvas -->
</div><!-- /.pagebox -->

<div class="pagebox feature-pagebox">
	<div class="title">Top books in Non-Fiction</div>
	<div class="jq-page feature-canvas coverflow-canvas" data-name="newest">
		<h2>No Contents Yet for Newest</h2>
	</div>
	<div class="jq-page feature-canvas coverflow-canvas" data-name="views">
		<h2>No Contents Yet for Views</h2>
	</div>
	<div class="jq-page feature-canvas coverflow-canvas visible" data-name="featured">
		<div class="canvas-slide prev"><span class="glyphicon glyphicon-chevron-left"></span></div>
		<div class="canvas-slide next"><span class="glyphicon glyphicon-chevron-right"></span></div>
		<div class="canvas-slide-under prev"><div class="artificial-padding"></div><?php bookitems(); ?><div class="artificial-padding"></div></div>
		<div class="canvas-slide-under next"><div class="artificial-padding"></div><?php bookitems(); ?><div class="artificial-padding"></div></div>
		<div class="books-container"><!-- wrapper required for scrolling to work -->
			<?php bookitems(); ?>
			<div class="artificial-padding"></div>
		</div><!-- /.books-container -->
	</div><!-- /.feature-canvas -->
</div><!-- /.pagebox -->
*/ ?>