
<div class="pagebox">
	<div class="container-fluid">
		<div class="row">
			<div class="col col-xs-12">
				<h3>Top Books [shows up on homepage]</h3>
			</div>
		</div>
	</div>
	<div class="feature-canvas coverflow-canvas">
	<?php for ($i=0; $i < 3; $i++) { ?>
		<div class="bookitem">
			<div class="cover-container coverflow-item">
				<div class="cover test-cover" style="background-image: url('./uploads/testimg/1.jpg');"></div>
			</div>
			<div class="text-container">
				<h3 class="text-primary">Harry Potter 6 <small>J.K. Rowling</small></h3>
				<p>
					Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
				</p>
			</div>
		</div>
		<div class="bookitem">
			<div class="cover-container coverflow-item">
				<div class="cover test-cover" style="background-image: url('./uploads/testimg/2.jpg');"></div>
			</div>
			<div class="text-container">
				<h3 class="text-primary">Harry Potter 4 <small>J.K. Rowling</small></h3>
				<p>
					Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
				</p>
			</div>
		</div>
		<div class="bookitem">
			<div class="cover-container coverflow-item">
				<div class="cover test-cover" style="background-image: url('./uploads/testimg/3.jpg');"></div>
			</div>
			<div class="text-container">
				<h3 class="text-primary">The Secret Under my Skin <small>Janet McNaughton</small></h3>
				<p>
					Lorem ipsum dolor sit amet, consectetur adipiscing elit. In porta diam mauris, lacinia consectetur velit laoreet sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
				</p>
			</div>
		</div>
	<?php } ?>
	</div>
</div>

<h1>Testing Controls</h1>
<div class="pagebox">
	<div class="container">
		<div class="row">
			<div class="col col-xs-12 col-md-6">
				<h2>Ticker Style</h2>
				<h4>Edges</h4>
				<button onclick="st_ticker_style('faded');" type="button" class="btn btn-primary btn-bolded btn-md">Faded Ticker</button>
				<button onclick="st_ticker_style('boxed');" type="button" class="btn btn-default btn-bolded btn-md">Boxed Ticker</button>
				<h4>Colour Scheme</h4>
				<button onclick="st_ticker_style('black');" type="button" class="btn btn-default btn-bolded btn-md">Black</button>
				<button onclick="st_ticker_style('midnight');" type="button" class="btn btn-primary btn-bolded btn-md">Midnight</button>
				<button onclick="st_ticker_style('highlighted');" type="button" class="btn btn-default btn-bolded btn-md">Highlighted</button>
				<button onclick="st_ticker_style('inverted');" type="button" class="btn btn-default btn-bolded btn-md">Inverted</button>
				<h2>Searchbox Style</h2>
				<h4>Shadow</h4>
				<button onclick="st_search_style('0');" type="button" class="btn btn-primary btn-bolded btn-md">Red Glow</button>
				<button onclick="st_search_style('1');" type="button" class="btn btn-default btn-bolded btn-md">Red Layer</button>
				<button onclick="st_search_style('2');" type="button" class="btn btn-default btn-bolded btn-md">None</button>
			</div>
		</div>
	</div>
</div>