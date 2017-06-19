<?php /*ob_start();*/
$pageTitle = "Best ebooks at BestEbooks.ca";
if ( isset( $title ) )$pageTitle = $title . " | " . $pageTitle;
?>
<!DOCTYPE html>
<html>

<head lang="en">
	<meta http-equiv="x-ua-compatible" content="IE=Edge"/>
	<meta charset="UTF-8">
	<!-- use actual device width -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content='<?php
	if (isset($alt_description)) {
		echo $alt_description;
	} else {
		echo "Ebook or ebooks, we have the best ebooks in fiction, non-fiction. Ebooks trailers, Contest and cash giveaways. Come vote for your favorite authors video. We at bestebooks.ca want to say \"read a book, enjoy!\"";
	}
	?>'>
	<title>
		<?php echo $pageTitle; ?>
	</title>
	<link rel="icon" type="image/ico" href="http://www.dubedev.com/icon.png"/>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans|Oxygen|Lato' rel='stylesheet' type='text/css'>
	<?php
	//$resFolder = "bestebooks.ca/res/";
	$resFolder = WEB_PATH . '/res/';
	$cssPath = $resFolder . "style/";
	$scriptPath = $resFolder . "script/";

	// Normalize
	//HtmlShortcuts::includeCSS($cssPath.'e_normalize.css');

	//HtmlShortcuts::includeJQ();
	?>
	<!-- Bootstrap from CDN -->
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<?php

	// Vendor Code
	HtmlShortcuts::includeCSS( $resFolder . 'vendor/jasny-bootstrap/css/jasny-bootstrap.min.css' );
	HtmlShortcuts::includeJS( $resFolder . 'vendor/jasny-bootstrap/js/jasny-bootstrap.min.js' );
	HtmlShortcuts::includeCSS( $resFolder . 'vendor/bootstrap-select/css/bootstrap-select.min.css' );
	HtmlShortcuts::includeJS( $resFolder . 'vendor/bootstrap-select/js/bootstrap-select.min.js' );
	HtmlShortcuts::includeCSS( $cssPath . '/vendor/offcanvas.css' );

	// Stock Code
	HtmlShortcuts::includeJS( $scriptPath . 'e_animate.js' );

	// This Website

	// CSS
	HtmlShortcuts::includeCSS( $cssPath . 'animation.css' );
	HtmlShortcuts::includeCSS( $cssPath . 'full.css' );
	HtmlShortcuts::includeCSS( $cssPath . 'site_slideout.css' );
	HtmlShortcuts::includeCSS( $cssPath . 'bookslist.css' );
	HtmlShortcuts::includeCSS( $cssPath . 'bookview.css' );
	HtmlShortcuts::includeCSS( $cssPath . 'ebooks.css' );
	// CSS added on
	HtmlShortcuts::includeCSS( $cssPath . 'guess_contest.css' );
	HtmlShortcuts::includeCSS( $cssPath . 'tshirt_sale.css' );
	HtmlShortcuts::includeCSS( $cssPath . 'tshirts.css' );

	// JS Classes
	HtmlShortcuts::includeJS( $scriptPath . 'BlogComments.js' );

	// JS
	HtmlShortcuts::includeJS( $scriptPath . 'gui.js' );
	HtmlShortcuts::includeJS( $scriptPath . 'animations.js' );
	HtmlShortcuts::includeJS( $scriptPath . 'form_submission.js' );
	HtmlShortcuts::includeJS( $scriptPath . 'page_loading.js' );
	HtmlShortcuts::includeJS( $scriptPath . 'style-testing.js' );
	HtmlShortcuts::includeJS( $scriptPath . 'admin_form_submission.js' );
	// JS added on
	HtmlShortcuts::includeJS( $scriptPath . 'guess_contest.js' );

	?>
	<script type="text/javascript">
		function do_onload_things() {
			// most important, so called first.
			WEB_PATH = "<?php echo WEB_PATH; ?>";
			set_ajax_loaders();
			set_ajax_form_checks();
			set_ajax_submits();
			set_ajax_uploads();
			set_videovote_submits();
			activate_searchbar();

			activate_slideout_links( "#slideout-box" );
			activate_tabs( "#login-register-area" );
			activate_tabs( ".feature-pagebox" );
			activate_feature_scrollers();

			run_tickertape();
			setup_offcanvas();
			activate_sort_buttons();
			activate_video_buttons();
			handle_coverflow_2();

			start_polling_page_url();

			animate_fade_thingies();

			activate_misc_buttons();

			<?php
				if (true) { // just true for now...
					echo "set_admin_submits();";
				}
			?>

			// addon stuff
			guessContest = new GuessContest();
			guessContest.bind_to_page( $( '#guess_contest_container' ) );
		}
	</script>

	<!-- SOCIAL MEDIA -->
	<script>
		window.twttr = ( function ( d, s, id ) {
			var js, fjs = d.getElementsByTagName( s )[ 0 ],
				t = window.twttr || {};
			if ( d.getElementById( id ) ) return t;
			js = d.createElement( s );
			js.id = id;
			js.src = "https://platform.twitter.com/widgets.js";
			fjs.parentNode.insertBefore( js, fjs );

			t._e = [];
			t.ready = function ( f ) {
				t._e.push( f );
			};

			return t;
		}( document, "script", "twitter-wjs" ) );
	</script>
	<script src="https://apis.google.com/js/platform.js" async defer></script>


</head>
<?php /*echo preg_replace('/\n+/', '', ob_get_clean()); flush(); ob_flush(); ob_start();*/ ?>

<body id="body" onload="do_onload_things();">
	<!-- ANALYTICS TRACKING -->
	<script>
		( function ( i, s, o, g, r, a, m ) {
			i[ 'GoogleAnalyticsObject' ] = r;
			i[ r ] = i[ r ] || function () {
				( i[ r ].q = i[ r ].q || [] ).push( arguments )
			}, i[ r ].l = 1 * new Date();
			a = s.createElement( o ),
				m = s.getElementsByTagName( o )[ 0 ];
			a.async = 1;
			a.src = g;
			m.parentNode.insertBefore( a, m )
		} )( window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga' );

		ga( 'create', 'UA-54877061-5', 'auto' );
		ga( 'send', 'pageview' );
	</script>
	<!-- SOCIAL MEDIA -->
	<div id="fb-root"></div>
	<script>
		( function ( d, s, id ) {
			var js, fjs = d.getElementsByTagName( s )[ 0 ];
			if ( d.getElementById( id ) ) return;
			js = d.createElement( s );
			js.id = id;
			js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId=161412083938730";
			fjs.parentNode.insertBefore( js, fjs );
		}( document, 'script', 'facebook-jssdk' ) );
	</script>

	<div id="SITE_WRAPPER">
		<div id="SITE_SLIDEOUT">
			<div class="container">
				<div class="row">
					<div class="col no-spacing col-sm-3 col-xs-0">
					</div>
					<div class="col slideout-box-col no-spacing col-sm-6 col-xs-12">
						<div id="slideout-box" class="slideout-box">

							<div class="container-fluid slideout-page" data-name="submitbook">

								<form class="ajax-form form-horizontal" data-name="bookupload_form">

									<!-- Form Name -->
									<legend>Submit Your Book</legend>

									<div class="blurb">
										<?php echo $ss->get_html('upload-blurb'); ?>
										<!--
										Welcome to BestEbooks!<br />
										Lorem ipsum dolor sit amet; Sed vel bibendum eros, a iaculis tellus. Maecenas quis scelerisque sem. Sed in odio tellus. Duis vestibulum convallis dolor, in luctus ante lacinia non. Integer auctor ante bibendum leo consequat faucibus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
									-->
									</div>

									<!-- ISBN -->
									<div class="form-group">
										<label class="col-md-3 control-label" for="isbn">ISBN (optional)</label>
										<div class="col-md-9">
											<input id="isbn" name="isbn" type="text" placeholder="978-3-16-148410-0" class="normal-input form-control input-md">
										</div>
									</div>
									<!-- Book Title -->
									<div class="form-group">
										<label class="col-md-3 control-label" for="book_title">Book Title</label>
										<div class="col-md-9">
											<input id="book_title" name="book_title" type="text" placeholder="The Science of Deduction" class="normal-input form-control input-md" required="">
										</div>
									</div>
									<!-- Category Selection -->
									<div class="form-group">
										<label class="col-md-3 control-label" for="category">Category</label>
										<div class="col-md-9">
											<select id="category" name="category" class="multiplesel-input form-control selectpicker" multiple>
												<?php
												foreach ( $categories as $item ) {
													$name = $item[ 'name' ];
													$baseid = $item[ 'id' ];
													foreach ( $item[ 'categories' ] as $subcat ) {
														$sname = $subcat[ 'name' ];
														$sid = $subcat[ 'id' ];
														echo '<option value="' . $sid . '">' . $name . ' &gt; ' . $sname . '</option>';
													}
												}
												?>
											</select>
										</div>
									</div>
									<!-- Author -->
									<div class="form-group">
										<label class="col-md-3 control-label" for="book_author">Author</label>
										<div class="col-md-9">
											<input id="book_author" name="book_author" type="text" placeholder="Sherlock Holmes" class="normal-input form-control input-md" required="">
										</div>
									</div>
									<!-- Description -->
									<div class="form-group">
										<label class="col-md-3 control-label" for="book_description">Description</label>
										<div class="col-md-9 validate-group" data-ms="1200">
											<textarea id="book_description" name="book_description" type="text" class="normal-input form-control input-md" placeholder="Text" required></textarea>
											<p class="help-block">12,000 words maximum</p>
										</div>
									</div>
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
												<input type="text" class="pseudo-input form-control input-md" readonly tabIndex="-1" placeholder="Maximum size: 1000 KB"/>
											</div>
										</div>
									</div>
									<!-- Link -->
									<div class="form-group">
										<label class="col-md-3 control-label" for="book_link">Link</label>
										<div class="col-md-9">
											<input id="book_link" name="book_link" type="text" placeholder="http://www.amazon.ca/A-Book-ebook" class="normal-input form-control input-md" required="">
											<p class="help-block">Amazon Kindle, Nook. Kobo, iTunes, and others.</p>
										</div>
									</div>
									<!-- Price on Site -->
									<div class="form-group">
										<label class="col-md-3 control-label" for="book_price">Price (CAD)</label>
										<div class="col-md-9">
											<input id="book_price" name="book_price" type="text" class="normal-input form-control input-md" placeholder="$1.00" required=""/>
										</div>
									</div>
									<div class="form-group checkbox-input" data-nam="paylist"><!-- .checkbox-input class required for JavaScript handler -->
										<label class= "col-md-3 control-label" for="paylist">Other Options</label>
										<div class="col-xs-9" style="border-left: 1px #D1D1D1 solid;">
											<?php 
										$dark = true;
										foreach ($paylist_items as $paylist_item){?>
											<div class="row paylist<?php if($dark){ ?> darkbg<?php } ?>">
												<div class="col-xs-1" style="margin-top: 20px">
													<input type="checkbox" name="paylist" value="<?php echo($paylist_item["id"]);?>">
												</div>
												<div class="col-xs-11">
													<div class="row">
														<h4 style="margin:8px 0px 0px;">
															<?php echo($paylist_item["copy"]);?>
														</h4>
													</div>
													<div class="row">
														<p style="font-weight: 200">
															<?php echo($paylist_item["price"]);?>
														</p>
													</div>
												</div>
											</div>
											<?php $dark = !$dark;} ?>
										</div>
									</div>
									<div class="form-group" style="padding:0;margin:0;">
										<div class="col-md-3">&nbsp;</div>
										<div class="col-md-9">
											<div class="error-message">
											</div>
											<div class="success-message">
												Your book was uploaded!<br/> Redirecting you to book in <span class="timer" data-time="3">3</span> seconds.
											</div>
										</div>
									</div>

									<!-- Button (Double) -->
									<div class="form-group">
										<label class="col-md-3 control-label sr-only" for="submitbook">Submit, or Cancel?</label>
										<div class="col-md-9">
											<button type="submit" id="submitbook" name="submitbook" class="btn btn-success">Submit</button>
											<img class="dispnone" id="submit_load_indicator" src="<?php echo WEB_PATH."/ res/img/ajax-loader.gif "; ?>"/>
											<button type="button" id="cancelbook" name="cancelbook" class="btn btn-danger otherside slideout-hide">Close</button>
										</div>
									</div>

								</form>

							</div>


							<div class="container-fluid slideout-page" data-name="aboutus">
								<h2>About Us</h2>
								<?php echo $ss->get_html('homepage.aboutus'); ?>

								<div class="padding"></div>

								<button type="button" id="closeabout" name="closeabout" class="btn btn-primary slideout-hide">Close</button>
								<div class="padding"></div>
							</div>


							<?php if ($user_logged_in === "yes") { ?>
							<div class="container-fluid slideout-page" data-name="account">
								<form class="ajax-form" data-name="logout_form">
									<h2>
										<?php echo $user_name; ?>'s Account</h2>
									<h4>
										<?php echo $user_username; ?>
									</h4>

									<div class="padding"></div>


									<div class="error-message">
										That's weird; the server couldn't be reached. Try reloading the page!
									</div>
									<div class="success-message">
										Logged out!<br/> Reloading the page in <span class="timer" data-time="3">3</span> seconds.
									</div>

									<button type="button" id="closeaccount" name="closeaccount" class="btn btn-danger slideout-hide">Close</button>
									<a href="?location=payment">
									<button type="button" id="closeaccount" name="closeaccount"
									class="btn btn-success slideout-hide">My Account</button>
								</a>
								

									<button type="submit" id="logout" name="logout" class="btn btn-danger otherside">Log Out</button>
									<div class="padding"></div>
								</form>
							</div>
							<?php } ?>




							<div id="login-register-area" class="container-fluid slideout-page no-spacing jq-tab-scope" data-name="login">

								<div class="loginregbox">
									<div data-page="login_form" class="jq-tab tab selected">Login</div>
									<div data-page="reg_form" class="jq-tab tab">Register</div>
								</div>

								<div id="login_to_submit" <?php echo (isset($book_pending)) ? ' class="visible"' : '' ?>>
									<span class="line1">Your entry, <span class="title"><?php echo (isset($book_pending)) ? $book_pending : '' ?></span>,</span><br/>will be submitted as soon as you login or register.
								</div>

								<form class="ajax-form jq-page form-horizontal padded padded-ex-bottom hiddenform visible" data-name="login_form">


									<!-- Form Name -->
									<legend class="sr-only">Login</legend>

									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-3 control-label" for="isbn">Email</label>
										<div class="col-md-9">
											<input id="isbn" name="user" type="text" placeholder="my.name@example.ca" class="form-control input-md" required="">
										</div>
									</div>
									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-3 control-label" for="book_title">Password</label>
										<div class="col-md-9">
											<input name="pass" type="password" placeholder="Enter Password" class="form-control input-md">
										</div>
									</div>
									<div class="col-md-3">&nbsp;</div>
									<div class="col-md-9">
										<div class="error-message">
										</div>
										<div class="success-message">
											Login was successful!<br/> Reloading the page in <span class="timer" data-time="3">3</span> seconds.
										</div>
										<div class="forgot-message">
										</div>
									</div>
									<input type="hidden" name="submitted" value="not defined"/>
									<!-- Button (Double) -->
									<div class="form-group">
										<label class="col-md-3 control-label" for="submit"><span class="sr-only">Login Button</span></label>
										<div class="col-md-9">
											<button type="submit" id="loginbutton" name="submit" class="btn btn-success">Login</button>
											<button type="submit" id="forgotbutton" name="forgot" class="btn btn-warning">Forgot</button>
											<button type="button" id="cancelbutton" name="cancel" class="btn btn-danger slideout-hide">Close</button>
										</div>
									</div>

								</form>

								<form class="ajax-form jq-page form-horizontal padded padded-ex-bottom hiddenform" data-name="reg_form">


									<!-- Form Name -->
									<legend class="sr-only">Register</legend>

									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-3 control-label" for="isbn">Your Email</label>
										<div class="col-md-9">
											<input name="email" type="text" placeholder="my.name@example.ca" class="form-control input-md" required="">
											<p class="help-block">You will login using this email address.</p>
										</div>
									</div>
									<span class="related-fields">
										<!-- Text input-->
										<div class="form-group related-to-below">
											<label class="col-md-3 control-label" for="book_title">Password</label>
											<div class="col-md-9">
												<input name="pass" type="password" placeholder="Enter a password" class="form-control input-md" required="">
											</div>
										</div>
										<!-- Text input-->
										<div class="form-group">
											<label class="col-md-3 control-label" for="book_title">Repeat</label>
											<div class="col-md-9">
												<input name="pass_retype" type="password" placeholder="Type this password again" class="form-control input-md" required="">
												<!--<p class="help-block">Password should be [TODO: criteria here].</p>-->
											</div>
										</div>
									</span>
									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-3 control-label" for="book_title">Username</label>
										<div class="col-md-9">
											<input name="user" type="text" placeholder="spaces and letters only" class="form-control input-md" required="">
											<p class="help-block">A display name for your uploaded links.</p>
										</div>
									</div>
									<!-- Text input-->
									<div class="form-group">
										<label class="col-md-3 control-label" for="book_author">Your Name</label>
										<div class="col-md-9">
											<input name="name" type="text" placeholder="Sherlock Holmes" class="form-control input-md" required="">
										</div>
									</div>
									<div class="col-md-3">&nbsp;</div>
									<div class="col-md-9">
										<div class="error-message">
										</div>
										<div class="success-message">
											Register was successful!<br/> You will receive an email from us shortly!
										</div>
									</div>
									<!-- Button (Double) -->
									<div class="form-group">
										<label class="col-md-3 control-label" for="submitbook"><span class="sr-only">Submit, or Cancel?</span></label>
										<div class="col-md-9">
											<button type="submit" id="submitbook" name="submitbook" class="btn btn-success">Submit</button>
											<button type="button" id="cancelbook" name="cancelbook" class="btn btn-danger slideout-hide">Close</button>
										</div>
									</div>

								</form>
							</div>
						</div>
					</div>
					<div class="col no-spacing col-sm-3 col-xs-0">
					</div>
				</div>
			</div>
		</div>
		<div id="SITE_HEADER" class="header">
			<div class="toplinks container-fluid">
				<div class="container">
					<?php if ($user_logged_in === "yes") { ?>
					<a class="rfloat slideout-link" href="<?php echo WEB_PATH.'/ebooks'; ?>">
						<div style="border-left: 1px solid #777; padding-left:8px; margin-left: -8px;">
							Ebooks Blog
						</div>
					</a>
					<div class="rfloat slideout-link" data-page="aboutus">
						About Us
					</div>
					<!--
						<div class="rfloat slideout-link" data-page="null">
							Contact
						</div>
						-->
					<div class="rfloat slideout-link" data-page="submitbook">
						Submit Book
					</div>
					<div class="rfloat slideout-link important" data-page="account">
						Welcome,
						<?php echo $user_name; ?>
					</div>
					<?php } else { ?>
					<a class="rfloat slideout-link" href="<?php echo WEB_PATH.'/ebooks'; ?>">
						<div style="border-left: 1px solid #777; padding-left:8px; margin-left: -8px;">
							Ebooks Blog
						</div>
					</a>
					<div class="rfloat slideout-link" data-page="aboutus">
						About Us
					</div>
					<!--
						<div class="rfloat slideout-link" data-page="null">
							Contact Us
						</div>
						-->
					<div class="rfloat slideout-link" data-page="submitbook">
						Submit Your Book
					</div>
					<div class="rfloat slideout-link" data-page="login">
						Login/Register
					</div>
					<?php } ?>
				</div>
			</div>
			<div class="titlebar container-fluid">
				<div class="container">
					<div class="row">
						<div class="col no-spacing col-sm-3 col-xs-12">
							<a href="<?php echo WEB_PATH; ?>">
								<div class="logo" data-tab-type="homepage">
									<img src="<?php echo WEB_PATH.'/res/img/bestelogo_white.png'; ?>" title="best ebooks - BestEbooks.ca Logo"/>
									<div class="in-behind jq-pulse-col anim-600"></div>
								</div>
							</a>
						</div>
						<div class="col no-spacing col-sm-9 col-xs-12">
							<div class="ticker anim-ticker clientinfohover" data-delay="4000" data-transition="500">
								<!--<div class="anim-ticker-text">I Am Some Text. I am really long text. This is a ticker. --- I Am Some Text. I am really long text. This is a ticker.</div>-->
								<div class="anim-ticker-messages">
									<?php
									foreach ( $ticker_messages as $item ) {
										$message = $item[ 'msg' ];
										$link = $item[ 'linkurl' ];
										if ( $link != "" )echo '<a href="' . $link . '" target="_blank">';
										echo '<span class="ticker-message">' . $message . '</span>';
										if ( $link != "" )echo '</a>';
									}
									?>
									<!--
									<span class="ticker-message">The Quick Brown Fox Jumped.</span>
									<span class="ticker-message">The Lazy Dog Also Jumped.</span>
									<span class="ticker-message">Quick, Ask Zoey; Whats The Fox Say?</span>
									-->
								</div>

								<div class="tickerfade leftfade"></div>
								<div class="tickerfade rightfade"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="functional container-fluid">
				<div class="functional-inner container">
					<div class="row">
						<div class="col col-xs-12 col-md-2 col-sm-3 no-spacing">
							<div class="button_a beste-button" data-act="newsletter">Newsletter</div>
						</div>
						<div class="col col-xs-12 col-md-8 col-sm-6">
							<form class="form-horizontal ajax-search" role="form" data-query="<?php echo WEB_PATH; ?>/?location=bookslist">
								<!-- TODO: Figure out what "role" is actually for. -->
								<div class="form-group">
									<label class="control-label sr-only">Search:</label>
									<div class="col-sm-10 col-xs-9">
										<input type="text" class="form-control search-input ajax-search-input" placeholder="Enter title, author, or ISBN"/>
									</div>
									<div class="col-sm-2 col-xs-3">
										<!--
										<input type="button" class="form-control button_b" />
										-->
										<button type="submit" class="btn btn-default btn-bolded btn-block button_b"><span class="glyphicon glyphicon-search ajax-search-button"></span><span class="text"> Find</span></button>
									</div>
								</div>
							</form>
						</div>
						<div class="col col-xs-12 col-md-2 col-sm-3">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="SITE_MIDDLE" class="container">

			<div class="row row-offcanvas row-offcanvas-left">
				<div class="col-md-2 col-sm-3 col-xs-6 no-spacing sidebar-offcanvas" id="sidebar">
					<?php if ($adminDisplay === "yes") { ?>
					<div class="category-container blue">
						<div class="category">
							<a href="<?php echo WEB_PATH; ?>?location=admin">
								<div class="title" data-tab-type="category" data-id="1">Admin Controls
									<div class="spacer"></div>
								</div>
							</a>
							<!--<div class="subcats">
								<div class="subcat ajax-tab" data-tab-type="subcategory" data-catid="1" data-id="1">
									<div class="title">Site Configuration</div>
								</div>
								<div class="subcat ajax-tab" data-tab-type="subcategory" data-catid="1" data-id="2">
									<div class="title">Book Approval <span class="badge">0</span></div>
								</div>
							</div>-->
						</div>
					</div>
					<?php } ?>

					<div class="category-container">

						<?php foreach ($categories as $item) {
							$name = $item['name'];
							$baseid = $item['id'];
						?>
						<div class="item category">
							<a href="#"><div class="title ajax-tab" data-tab-type="category" data-id="<?php echo $baseid ?>"><?php echo $name ?></div></a>
							<div class="item subcats">
								<?php foreach ($item['categories'] as $subcat) {
									$sname = $subcat['name'];
									$sid = $subcat['id'];
								?>
								<div class="item subcat ajax-tab" data-tab-type="subcategory" data-catid="<?php echo $baseid ?>" data-id="<?php echo $sid ?>">
									<div class="title"><span class="glyphicon glyphicon-chevron-right"></span>
										<?php echo $sname ?>
									</div>
								</div>
								<?php } ?>
							</div>
						</div>
						<?php } ?>
					</div>

				</div>
				<div class="col col-md-10 col-sm-9 col-xs-12 pagebox-container SITE_INNER">
					<p class="pull-left visible-xs">
						<button type="button" class="btn btn-default btn-bolded btn-md" data-toggle="offcanvas">Menu</button>
					</p>
					<?php
					if ( isset( $part_videovote ) ) {
						echo '<span class="loadable-page-part" id="LPP-videovote" data-currentview="none">';
						$part_videovote->run();
						echo '</span>';
					} else {
						echo '<span class="loadable-page-part" id="LPP-videovote" data-currentview="none"></span>';
					}
					?>
					<?php
					if ( isset( $part_frontbooks ) ) {
						echo '<span class="loadable-page-part" id="LPP-frontbooks" data-currentview="none">';
						$part_frontbooks->run();
						echo '</span>';
					} else {
						echo '<span class="loadable-page-part" id="LPP-frontbooks" data-currentview="none"></span>';
					}
					?>
					<span class="loadable-page-part<?php echo ($show_bookslistctrl===true) ? " " : " dispnone " ?>" id="LPP-bookslistctrl">
		          	</span>
				

					<span class="loadable-page-part" id="LPP-bookslist" data-currentview="none">
						<?php
						if ( isset( $part_bookslist ) ) {
							$part_bookslist->run();;
						}
						?>
					</span>
					<span class="loadable-page-part" id="LPP-bookview" data-currentview="none">
						<?php
						if ( isset( $part_bookview ) ) {
							$part_bookview->run();;
						}
						?>
					</span>
					<span class="loadable-page-part" id="LPP-blog" data-currentview="none">
						<?php
						if ( isset( $part_blog ) ) {
							$part_blog->run();
						}
						?>
					</span>
				</div>
			</div>

		</div>
	</div>
	<a name="nothing"></a>
	<div id="SITE_FOOTER" class="container-fluid">
		<div class="container">
			<!--<div class="heading h3">BestEbooks.ca</div>-->
			<span class="footer-item">
				<?php echo $ss->get_value('copytext'); ?>
			</span>
			<span class="footer-item"><a href="?location=privacy">Privacy</a></span>
		</div>
	</div>

</body>

</html>
<?php /*echo preg_replace('/\n+|\t+/', '', ob_get_clean());*/ ?>