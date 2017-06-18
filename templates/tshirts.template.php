
<!-- <div class="pagebox feature-pagebox jq-tab-scope"> -->

	<?php
		foreach($tshirts as $tshirt) {
			$tmpl = new Template();
			$tmpl->tshirt = $tshirt;
			$tmpl->set_template_file(SITE_PATH . '/templates/parts/tshirt_single.template.php');
			$tmpl->run();
		}
	?>
<!-- </div> -->


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