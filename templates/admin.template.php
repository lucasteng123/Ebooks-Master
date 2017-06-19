<?php /*ob_start();*/ ?>
<!DOCTYPE html>
<html>
<head lang="en">
	<meta http-equiv="x-ua-compatible" content="IE=Edge"/> 
	<meta charset="UTF-8">
	<title>Best EBooks</title>
	<link rel="icon" type="image/ico" href="http://www.dubedev.com/icon.png"/>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans|Oxygen|Lato' rel='stylesheet' type='text/css'>
	<?php
		$cssPath = "./res/style/";
		$scriptPath = "./res/script/";

		// Normalize
		HtmlShortcuts::includeCSS($cssPath.'e_normalize.css');

		//HtmlShortcuts::includeJQ();
	?>
	<!-- Bootstrap from CDN -->
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<?php

		// Vendor Code
		HtmlShortcuts::includeCSS('./res/vendor/jasny-bootstrap/css/jasny-bootstrap.min.css');
		HtmlShortcuts::includeJS('./res/vendor/jasny-bootstrap/js/jasny-bootstrap.min.js');
		HtmlShortcuts::includeCSS($cssPath.'/vendor/offcanvas.css');

		// Stock Code
		HtmlShortcuts::includeJS($scriptPath.'e_animate.js');

		// This Website

		// CSS
		HtmlShortcuts::includeCSS($cssPath.'animation.css');
		HtmlShortcuts::includeCSS($cssPath.'full.css');
		HtmlShortcuts::includeCSS($cssPath.'site_slideout.css');
		HtmlShortcuts::includeCSS($cssPath.'bookslist.css');
		HtmlShortcuts::includeCSS($cssPath.'admin.css');

		// JS
		HtmlShortcuts::includeJS($scriptPath.'gui.js');
		HtmlShortcuts::includeJS($scriptPath.'admin_page.js');
		HtmlShortcuts::includeJS($scriptPath.'lib/BlogEditor.js');

	?>
	<script type="text/javascript">
		function do_onload_things() {
			// most important, so called first.
			activate_tabs("#SITE_INNER");
			activate_sidelinks();

			activate_book_buttons();
			set_ajax_submits();

			setup_offcanvas();
			<?php
				if (isset($startView)) {
					echo ("set_active_tab('#SITE_INNER','".$startView."');");
				}
			?>

			// Setup the blog editor
			new BlogEditor($('.blog-editor').first());
		}
	</script>
</head>
<?php /*echo preg_replace('/\n+/', '', ob_get_clean()); flush(); ob_flush(); ob_start();*/ ?>
<body id="body" onload="do_onload_things();">

	<div id="SITE_HEADER" class="header">
		<div class="titlebar container-fluid" style="background-color: #77C; margin-bottom: 15px;">
			<div class="container">
				<div class="row">
					<div class="col no-spacing col-sm-12 col-xs-12">
						<div class="logo ajax-tab" data-tab-type="homepage">BestEbooks Admin</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="SITE_MIDDLE" class="container">

		<div class="row row-offcanvas row-offcanvas-left">
			<div class="col-md-2 col-sm-3 col-xs-6 no-spacing sidebar-offcanvas" id="sidebar">
				<div class="category-container blue">
					<div class="category">
						<a href="."><div class="title" data-tab-type="category" data-id="1">BestEbooks</div></a>
						<div class="subcats">
							<div class="subcat ajax-tab" data-page="adminbox" data-catid="1" data-id="1">
								<div class="title">Site Configuration</div>
							</div>
							<div class="subcat ajax-tab" data-page="booksmod" data-catid="1" data-id="2">
								<div class="title">Book Approval <span class="badge"><?php echo count($uncheckedBooks); ?></span></div>
							</div>
							<div class="subcat ajax-tab" data-page="vidsmod">
								<div class="title">Video Approval <span class="badge"><?php echo count($uncheckedVideos); ?></span></div>
							</div>
							<div class="subcat ajax-tab" data-page="commentsmod">
								<div class="title">Comment Approval <span class="badge"><?php echo count($uncheckedComments); ?></span></div>
							</div>
						</div>
					</div>
				</div>

			</div>
			<div id="SITE_INNER" class="col col-md-10 col-sm-9 col-xs-12 pagebox-container SITE_INNER">

	<span class="loadable-page-part" id="LPP-adminbox" data-currentview="none">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-default btn-bolded btn-md" data-toggle="offcanvas">Menu</button>
		</p>

		<div class="pagebox feature-pagebox">
			<ul class="nav nav-tabs feature-tabs">
				<li data-page="ticker" class="jq-tab active"><a href="#">Ticker Text</a></li>
				<li data-page="sitestrings" class="jq-tab"><a href="#">Site Strings</a></li>
				<li data-page="categories" class="jq-tab"><a href="#">Categories</a></li>
				<li data-page="videovote" class="jq-tab"><a href="#">Video Vote</a></li>
				<li data-page="emailout" class="jq-tab"><a href="#">Newsletter Mailout</a></li>
				<li data-page="game" class="jq-tab"><a href="#">Game</a></li>
				<li data-page="ebooks" class="jq-tab"><a href="#">/ebooks</a></li>
				<li data-page="shirts" class="jq-tab"><a href="#">Shirts</a></li>
				<?php if (in_array('admin.op',$_SESSION['permissions'])) { ?>
				<li data-page="moderators" class="jq-tab"><a href="#">Moderators</a></li>
				<?php } ?>
			</ul>
			<div class="visible jq-page admin-canvas" data-name="ticker">
				<div class="container-fluid">
					<div class="row">
						<?php foreach ($ticker_messages as $item) { 
							$message = $item['msg'];
							$linkurl = $item['linkurl'];
							$id = $item['id'];
						?>
							<form class="ajax-form" data-name="ticker">
								<div class="col col-xs-4">
									<input name="message" type="text" class="form-control" value="<?php echo $message ?>" />
								</div>
								<div class="col col-xs-4">
									<input name="linkurl" type="text" class="form-control" value="<?php echo $linkurl ?>" />
								</div>
								<div class="col col-xs-4">
									<input name="update" type="submit" value="Update" class="btn btn-primary" />
									<input name="remove" type="submit" value="Remove" class="btn btn-primary" />
								</div>
								<div class="col col-xs-8">
									<span class="message"></span>
								</div>
								<input type="hidden" name="id" value="<?php echo $id ?>" />
								<input type="hidden" name="submitted" />
								<div class="col col-xs-12 spacer"></div>
							</form>
						<?php } ?>

						<div class="col col-xs-12 spacer"></div>

						<form class="ajax-form" data-name="ticker">
							<div class="col col-xs-4">
								<input name="message" type="text" class="form-control" placeholder="Enter some text..." />
							</div>
							<div class="col col-xs-4">
								<input name="linkurl" type="text" class="form-control" placeholder="Enter some text..." />
							</div>
							<div class="col col-xs-4">
								<input name="addnew" type="submit" value="Add New" class="btn btn-primary" />
							</div>
							<div class="col col-xs-8">
								<span class="message error">Test Message</span>
								<span class="message success">Test Message</span>
							</div>
							<input type="hidden" name="submitted" />
						</form>
					</div>
				</div>
			</div>
<!--  ________  ___  _________  _______   ________  _________  ________  ___  ________   ________  ________      
     |\   ____\|\  \|\___   ___\\  ___ \ |\   ____\|\___   ___\\   __  \|\  \|\   ___  \|\   ____\|\   ____\     
     \ \  \___|\ \  \|___ \  \_\ \   __/|\ \  \___|\|___ \  \_\ \  \|\  \ \  \ \  \\ \  \ \  \___|\ \  \___|_    
      \ \_____  \ \  \   \ \  \ \ \  \_|/_\ \_____  \   \ \  \ \ \   _  _\ \  \ \  \\ \  \ \  \  __\ \_____  \   
       \|____|\  \ \  \   \ \  \ \ \  \_|\ \|____|\  \   \ \  \ \ \  \\  \\ \  \ \  \\ \  \ \  \|\  \|____|\  \  
         ____\_\  \ \__\   \ \__\ \ \_______\____\_\  \   \ \__\ \ \__\\ _\\ \__\ \__\\ \__\ \_______\____\_\  \ 
        |\_________\|__|    \|__|  \|_______|\_________\   \|__|  \|__|\|__|\|__|\|__| \|__|\|_______|\_________\
        \|_________|                        \|_________|                                             \|_________|
                                                                                                                 
        I kept loosing this part of the file, so I labelled it with giant ASCII-art text. -->
			<div class="jq-page admin-canvas" data-name="sitestrings">
			<?php $items = array(
					array('Videos on Home Page (title)','homepage.videovote_title'),
					array('/ebooks Page Title','ebookstitle'),
					array('New Books Email Address (new books are sent here)','mail.newbooks'),
					array('Copyright Text','copytext'),
				); foreach ($items as $item) { ?>
				<div class="col col-xs-12 h3">
					<?php echo $item[0]; ?>
				</div>
				<form class="ajax-form" data-name="sitestrings">
					<div class="col col-xs-9">
						<input name="value" type="text" class="form-control" value="<?php echo $ss->get_value($item[1]); ?>" />
					</div>
					<div class="col col-xs-3">
						<input name="update" type="submit" value="Update" class="btn btn-primary" />
					</div>
					<div class="col col-xs-9">
						<span class="message"></span>
					</div>
					<input type="hidden" name="name" value="<?php echo $item[1]; ?>" />
					<div class="col col-xs-12 spacer"></div>
				</form>
			<?php } ?>
			<?php $items = array(
					array('Upload Blurb','upload-blurb'),
					array('About Us','homepage.aboutus'),
					array('Password Reset Email','email.account_pass_reset'),
					array('Welcome Email','email.account_welcome'),
					array('Account Activation Email','email.account_activate'),
					array('Privacy Policy','beste.privacy'),
				); foreach ($items as $item) { ?>
				<div class="col col-xs-12 h3">
					<?php echo $item[0]; ?>
				</div>
				<form class="ajax-form" data-name="sitestrings">
					<div class="col col-xs-9">
						<textarea name="value" class="form-control" style="min-height: 100px;"><?php echo $ss->get_value($item[1]); ?></textarea>
					</div>
					<div class="col col-xs-3">
						<input name="update" type="submit" value="Update" class="btn btn-primary" />
					</div>
					<div class="col col-xs-9">
						<span class="message"></span>
					</div>
					<input type="hidden" name="name" value="<?php echo $item[1]; ?>" />
					<div class="col col-xs-12 spacer"></div>
				</form>
			<?php } ?>
			</div>
			<div class="jq-page admin-canvas" data-name="categories">
				<div class="container-fluid">
					<div class="row">
						<?php foreach ($categories as $item) {
							$name = $item['name'];
							$baseid = $item['id'];
						?>
							<form class="ajax-form" data-name="category">
								<div class="col col-xs-8">
									<input name="name" type="text" class="form-control" value="<?php echo $name ?>" />
								</div>
								<div class="col col-xs-4">
									<input name="update" type="submit" value="Update" class="btn btn-primary" />
									<input name="remove" type="submit" value="Remove" class="btn btn-primary" />
								</div>
								<div class="col col-xs-8">
									<span class="message"></span>
								</div>
								<input type="hidden" name="id" value="<?php echo $baseid ?>" />
								<input type="hidden" name="submitted" />
								<div class="col col-xs-12 spacer"></div>
							</form>

							<div class="subcat-container col-xs-12"><div class="inner">
								<?php foreach ($item['categories'] as $subcat) {
									$sname = $subcat['name'];
									$sid = $subcat['id'];
								?>
									<form class="ajax-form" data-name="subcat">
										<div class="col col-xs-8">
											<input name="name" type="text" class="form-control" value="<?php echo $sname ?>" />
										</div>
										<div class="col col-xs-4">
											<input name="update" type="submit" value="Update" class="btn btn-default" />
											<input name="remove" type="submit" value="Remove" class="btn btn-default" />
										</div>
										<div class="col col-xs-8">
											<span class="message"></span>
										</div>
										<input type="hidden" name="id" value="<?php echo $sid ?>" />
										<input type="hidden" name="basecat" value="<?php echo $baseid ?>" />
										<input type="hidden" name="submitted" />
										<div class="col col-xs-12 spacer"></div>
									</form>
								<?php } ?>
								<form class="ajax-form" data-name="subcat">
									<div class="col col-xs-8">
										<input name="name" type="text" class="form-control" placeholder="Enter some text..." />
									</div>
									<div class="col col-xs-4">
										<input name="addnew" type="submit" value="Add New" class="btn btn-default" />
									</div>
									<div class="col col-xs-8">
										<span class="message error">Test Message</span>
										<span class="message success">Test Message</span>
									</div>
									<input type="hidden" name="basecat" value="<?php echo $baseid ?>" />
									<input type="hidden" name="submitted" />
								</form>
							</div></div>
							<div class="col col-xs-12 spacer"></div>
							<div class="col col-xs-12 spacer"></div>
						<?php } ?>

						

						<form class="ajax-form" data-name="category">
							<div class="col col-xs-8">
								<input name="name" type="text" class="form-control" placeholder="Enter some text..." />
							</div>
							<div class="col col-xs-4">
								<input name="addnew" type="submit" value="Add New" class="btn btn-primary" />
							</div>
							<div class="col col-xs-8">
								<span class="message error">Test Message</span>
								<span class="message success">Test Message</span>
							</div>
							<input type="hidden" name="submitted" />
						</form>
					</div>
				</div>
			</div>
			<div class="jq-page admin-canvas" data-name="videovote">
				<div class="container-fluid">
					<div class="row">
						<div class="col col-xs-12 h3">
							Note: to remove video voting from a category, simply
							leave either urlbox blank.
							<br />
							<br />
							Entering unrecognized URLs will result in the videovote not being displayed on the page.
						</div>
						<?php foreach ($vvote_info as $item) {
							$name = $item['name'];
							$baseid = $item['id'];
							$ybegin = "http://www.youtube.com/watch?v=";
						?>
							<div class="col col-xs-12 h1">
								<?php echo $name; ?>
							</div>
							<div class="col col-xs-6">
								Left Video
							</div>
							<div class="col col-xs-6">
								Right Video
							</div>
							<form class="ajax-form" data-name="videovote">
								<input type="hidden" name="submitted" />

								<input type="hidden" name="baseid" value="<?php echo $baseid; ?>" />
								<input type="hidden" name="vid_a_id" value="<?php echo $item['a_id']; ?>" />
								<input type="hidden" name="vid_b_id" value="<?php echo $item['b_id']; ?>" />

								<div class="col col-xs-12 spacer"></div>
								<div class="col col-xs-6">
									<input name="vid_a_title" type="text" class="form-control" placeholder="Enter a Title" value="<?php echo $item['a_video_title']; ?>" />
								</div>
								<div class="col col-xs-6">
									<input name="vid_b_title" type="text" class="form-control" placeholder="Enter a Title" value="<?php echo $item['b_video_title']; ?>" />
								</div>
								<div class="col col-xs-6">
									<input name="vid_a" type="text" class="form-control" placeholder="Enter YouTube URL" value="<?php if ($item['a_youtube_id']) echo $ybegin.$item['a_youtube_id']; ?>" />
								</div>
								<div class="col col-xs-6">
									<input name="vid_b" type="text" class="form-control" placeholder="Enter YouTube URL" value="<?php if ($item['a_youtube_id']) echo $ybegin.$item['b_youtube_id']; ?>" />
								</div>
								<div class="col col-xs-12 spacer"></div>

								<div class="col col-xs-12">
									<span class="message error">Test Message</span>
									<span class="message success">Test Message</span>
								</div>
								<div class="col col-xs-12">
									<input name="set" type="submit" value="Set Videos" class="btn btn-primary" />
									<input name="fix" type="submit" value="Update Info" class="btn btn-default" />
								</div>
							</form>
						<?php } ?>
					</div>
				</div>
			</div>
<!--  ________  ________  ________   _________  _______   ________  _________   
     |\   ____\|\   __  \|\   ___  \|\___   ___\\  ___ \ |\   ____\|\___   ___\ 
     \ \  \___|\ \  \|\  \ \  \\ \  \|___ \  \_\ \   __/|\ \  \___|\|___ \  \_| 
      \ \  \    \ \  \\\  \ \  \\ \  \   \ \  \ \ \  \_|/_\ \_____  \   \ \  \  
       \ \  \____\ \  \\\  \ \  \\ \  \   \ \  \ \ \  \_|\ \|____|\  \   \ \  \ 
        \ \_______\ \_______\ \__\\ \__\   \ \__\ \ \_______\____\_\  \   \ \__\
         \|_______|\|_______|\|__| \|__|    \|__|  \|_______|\_________\   \|__|
                                                            \|_________|         -->
			<div class="jq-page admin-canvas" data-name="game">

				<div class="col col-xs-12">
				<?php if ($isContestRunning) { ?>
					<div class="panel panel-info">
						<div class="panel-heading">
							Current Contest Controls
						</div>
						<div class="panel-body">
							<form class="ajax-form" data-name="guess_contest">
								<input name="end" type="submit" value="End Contest" class="btn btn-primary" /><br />
								<span class="message"></span>
								<input type="hidden" name="submitted" />
							</form>
						</div>
						<table class="table">
							<tr>
								<td>Contestants</td>
								<td><?php echo $numberOfContestants; ?></td>
							</tr>
						</table>
					</div>
				<?php } else { ?>
					<div class="panel panel-info">
						<div class="panel-heading">
							Start New Contest
						</div>
						<div class="panel-body">
							<form class="ajax-form" data-name="guess_contest">
								<div class="col col-xs-9">
									<input name="sword" type="text" class="form-control" placeholder="Secret Word" />
								</div>
								<div class="col col-xs-3">
									<input name="begin" type="submit" value="Begin" class="btn btn-primary" />
								</div>
								<div class="col col-xs-9">
									<span class="message"></span>
								</div>
								<input type="hidden" name="submitted" />
								<div class="col col-xs-12 spacer"></div>
							</form>
						</div>
					</div>
				<?php } ?>

				<!-- new winner -->
				<div class="panel panel-info">
					<div class="panel-heading">
						Add New Winner
					</div>
					<div class="panel-body">
						<form class="ajax-form" data-name="guess_contest">
							<div class="col col-xs-9">
								<input name="name" type="text" class="form-control" placeholder="Winner's Name" />
							</div>
							<div class="col col-xs-3">
								<input name="add_winner" type="submit" value="Add" class="btn btn-primary" />
							</div>
							<div class="col col-xs-9">
								<span class="message"></span>
							</div>
							<input type="hidden" name="submitted" />
							<div class="col col-xs-12 spacer"></div>
						</form>
					</div>
				</div>

				<div class="panel panel-info">
					<div class="panel-heading">
						<div class="h3 panel-title">Contest Strings</div>
					</div>
					<div class="panel-body">
						<div class="col col-xs-12 h3">
							Prize String
						</div>
						<form class="ajax-form" data-name="guess_contest">
							<input type="hidden" name="submitted" />
							<div class="col col-xs-9">
								<input name="value" type="text" class="form-control" />
							</div>
							<div class="col col-xs-3">
								<input name="update_prize" type="submit" value="Update" class="btn btn-primary" />
							</div>
							<div class="col col-xs-9">
								<span class="message"></span>
							</div>
							<div class="col col-xs-12 spacer"></div>
						</form>
						<div class="col col-xs-12 h3">
							Winners Video <small>(leave blank &amp; click 'update' to remove)</small>
						</div>
						<form class="ajax-form" data-name="guess_contest">
							<input type="hidden" name="submitted" />
							<div class="col col-xs-9">
								<input name="video" type="text" class="form-control" />
							</div>
							<div class="col col-xs-3">
								<input name="update_video" type="submit" value="Update" class="btn btn-primary" />
							</div>
							<div class="col col-xs-9">
								<span class="message"></span>
							</div>
							<div class="col col-xs-12 spacer"></div>
						</form>
						<div class="col col-xs-12 h3">
							Contest Question
						</div>
						<form class="ajax-form" data-name="guess_contest_ss">
							<input type="hidden" name="submitted" />
							<div class="col col-xs-9">
								<input name="value" type="text" class="form-control" value="<?php echo $ss->get_value('gc.question'); ?>" />
							</div>
							<div class="col col-xs-3">
								<input name="update_question" type="submit" value="Update" class="btn btn-primary" />
							</div>
							<div class="col col-xs-9">
								<span class="message"></span>
							</div>
							<input type="hidden" name="name" value="gc.question" />
							<div class="col col-xs-12 spacer"></div>
						</form>
						<div class="col col-xs-12 h3">
							Contest Title
						</div>
						<form class="ajax-form" data-name="guess_contest_ss">
							<input type="hidden" name="submitted" />
							<div class="col col-xs-9">
								<input name="value" type="text" class="form-control" value="<?php echo $ss->get_value('gc.title'); ?>" />
							</div>
							<div class="col col-xs-3">
								<input name="update_title" type="submit" value="Update" class="btn btn-primary" />
							</div>
							<div class="col col-xs-9">
								<span class="message"></span>
							</div>
							<input type="hidden" name="name" value="gc.title" />
							<div class="col col-xs-12 spacer"></div>
						</form>
						<div class="col col-xs-12 h3">
							Contest Description
						</div>
						<form class="ajax-form" data-name="guess_contest_ss">
							<div class="col col-xs-9">
								<textarea name="value" class="form-control" style="min-height: 100px;"><?php echo $ss->get_value("gc.description"); ?></textarea>
							</div>
							<div class="col col-xs-3">
								<input name="update_desc" type="submit" value="Update" class="btn btn-primary" />
							</div>
							<div class="col col-xs-9">
								<span class="message"></span>
							</div>
							<input type="hidden" name="name" value="gc.description" />
							<div class="col col-xs-12 spacer"></div>
						</form>
					</div>
				</div>


				</div>

				<div class="col col-xs-6">
				<h3>Contests</h3>
				<?php foreach ($contestsList as $contest) {
					?>
					<div class="panel panel-default">
						<div class="panel-heading">
						<?php if ($contest['date_closed'] == null) { ?>
						<?php echo $contest['date_posted']; ?> - <span>Currently Running</span>
						<?php } else { ?>
						<?php echo $contest['date_posted']; ?> - <?php echo $contest['date_closed']; ?>
						<?php } ?>
						</div>
						<div class="panel-body">
							<a href="?location=list_contestants/<?php echo $contest['id']; ?>/winners" target="_blank">
								<input name="contest" type="submit" value="Print Winners" class="btn btn-primary" />
							</a>
							<a href="?location=list_contestants/<?php echo $contest['id']; ?>" target="_blank">
								<input name="contest" type="submit" value="Print Everyone" class="btn btn-primary" />
							</a>
						</div>
					</div>
				<?php } ?>
				</div>
				<div class="col col-xs-6">
					<h3>Previous Winners</h3>
					<table class="table">
						<?php foreach ($gcWinners as $winner) { ?>
						<tr>
							<td><?php echo $winner['name']; ?></td>
							<td><form class="ajax-form" data-name="guess_contest">
							<input type="hidden" name="submitted" />
							<input type="hidden" name="id" value="<?php echo $winner['id']; ?>" />
							<input type="submit" name="remove_winner" class="btn btn-xs btn-primary" value="Remove" />
							<span class="message"></span>
							</form></td>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>


<!--
.______    __        ______     _______      ___
|   _  \  |  |      /  __  \   /  _____|    /  /
|  |_)  | |  |     |  |  |  | |  |  __     /  / 
|   _  <  |  |     |  |  |  | |  | |_ |   /  /  
|  |_)  | |  `----.|  `--'  | |  |__| |  /  /   
|______/  |_______| \______/   \______| /__/    
     _______. __    __   __  .______     .___________.    _______.
    /       ||  |  |  | |  | |   _  \    |           |   /       |
   |   (----`|  |__|  | |  | |  |_)  |   `---|  |----`  |   (----`
    \   \    |   __   | |  | |      /        |  |        \   \    
.----)   |   |  |  |  | |  | |  |\  \----.   |  |    .----)   |   
|_______/    |__|  |__| |__| | _| `._____|   |__|    |_______/    
-->                                                                  



			<div class="jq-page admin-canvas" data-name="ebooks">
				<form class="ajax-form blog-editor" data-name="ebooks_post">
					<div class="col col-xs-12">
						<h1>/ebooks</h1>
					</div>
					<div class="col col-xs-6">
						<h3>Title</h3>
						<input name="title" type="text" class="normal-input form-control" value="" />
					</div>
					<div class="col col-xs-6">
						<h3>Post ID</h3>
						<input name="postid" type="text" class="normal-input form-control" placeholder="(leave blank if making a new post)" />
					</div>
					<div class="col col-xs-12">
						<h3>Contents</h3>
						<div class="panel panel-default editor-buttons">
							<div class="panel-body">
								<div data-action="link" class="editor-button btn btn-xs btn-info">Link</div>
								<div data-action="image" class="editor-button btn btn-xs btn-info">Image</div>
							</div>
						</div>
						<textarea name="contents" class="editor-area wide normal-input form-control"></textarea>
					</div>
					<div class="col col-xs-9">
						<input class="file-input" name="post_image" type="file" />
					</div>
					<div class="col col-xs-12 spacer"></div>
					<div class="col col-xs-9">
						<input name="update" type="submit" value="Post to Blog" class="btn btn-primary" />
					</div>
					<div class="col col-xs-9">
						<span class="message"></span>
					</div>
					<div class="col col-xs-12 spacer"></div>
				</form>
			</div>


			<div class="jq-page admin-canvas" data-name="shirts">

		<div class="col-xs-12 tshirt-container">
				<?php
				$tmpl = new Template();
				$tmpl->set_template_file(SITE_PATH . '/templates/parts/tshirt_single.template.php');
				foreach($shirts_list as $tshirt) {
					$tmpl->tshirt = $tshirt;
					$tmpl->run();
				}
				?>
		</div>
		<div class="col-xs-12"><hr></div>
		<div class="col-md-3"><h2 style="text-align: right;vertical-align: center;">New T-Shirt</h2></div>
		<div class="col-md-9">
			<div class="col-md-4"></div>
			<div class="col-md-8"></div>

			<form action="<?php echo WEB_PATH; ?>/?location=tshirt-update"  method="post" enctype="multipart/form-data">

				<div class="form-group">
				<label for="image"> Path to Image </label>
				<div class="input-group">
					
					<!-- <input type="text" class="form-control" name="image">
					<span class="input-group-btn">
        				<button class="btn btn-default" type="button">Browse</button>
      				</span> -->
      				<input name="image" type="file" />
				</div>
				</div>
				Post ID (for testing)
				<input type="text" name="id" value="" />
				<div class="form-group">
					<label for="name"> Name for T-Shirt </label>
					<input type="text" class="form-control" name="name">
				</div>
				<div class="form-group">
					<label for="name"> Description </label>
					<input type="text" class="form-control" name="description">
				</div>
				<div class="form-group">
					<label for="colors"> Desired Colors (comma seperated) </label>
					<input type="text" class="form-control" name="colors">
				</div>
				<div class="form-group">
					<label for="colors"> Desired Sizes (comma seperated) </label>
					<input type="text" class="form-control" name="size">
				</div>
				<div class="input-group" style="padding:10px 0px 30px 0px">
					
					<span class="input-group-addon">$</span>
					<input type="text" class="form-control" name="price">
				</div>
				<div class="form-group">
					<input class="btn btn-primary"
					type="submit" value="Delete" name="delete">
					<input class="btn btn-primary" type="submit">
				</div>
			</form>	
		</div>				
			</div>

			<div class="jq-page admin-canvas" data-name="moderators">
				<div class="col col-xs-12 h3">
					Upload Blurb
				</div>
				<form class="ajax-form" data-name="sitestrings">
					<div class="col col-xs-9">
						<textarea name="value" class="form-control"><?php echo $ss->get_value('upload-blurb'); ?></textarea>
					</div>
					<div class="col col-xs-3">
						<input name="update" type="submit" value="Update" class="btn btn-primary" />
					</div>
					<div class="col col-xs-9">
						<span class="message"></span>
					</div>
					<input type="hidden" name="name" value="upload-blurb" />
					<div class="col col-xs-12 spacer"></div>
				</form>
				<div class="col col-xs-12 h3">
					Videos on Home Page (title)
				</div>
				<form class="ajax-form" data-name="sitestrings">
					<div class="col col-xs-9">
						<input name="value" type="text" class="form-control" value="<?php echo $ss->get_value('homepage.videovote_title'); ?>" />
					</div>
					<div class="col col-xs-3">
						<input name="update" type="submit" value="Update" class="btn btn-primary" />
					</div>
					<div class="col col-xs-9">
						<span class="message"></span>
					</div>
					<input type="hidden" name="name" value="upload-blurb" />
					<div class="col col-xs-12 spacer"></div>
				</form>
			</div>

			<div class="jq-page admin-canvas" data-name="emailout">
				<form class="ajax-form" data-name="emailout">
					<div class="col col-xs-12 h3">
						Newsletter Title
					</div>
					<div class="col col-xs-9">
						<input name="title" type="text" class="normal-input form-control" value="" />
					</div>
					<div class="col col-xs-12 h3">
						Newsletter Contents
					</div>
					<div class="col col-xs-9">
						<textarea name="contents" class="normal-input form-control" style="min-height: 100px;">Newsletter Text</textarea>
					</div>
					<div class="col col-xs-9">
						<input class="file-input" name="send_image" type="file" />
					</div>
					<div class="col col-xs-12 spacer"></div>
					<div class="col col-xs-9">
						<input name="update" type="submit" value="Mail to All Subscribers" class="btn btn-primary" />
					</div>
					<div class="col col-xs-9">
						<span class="message"></span>
					</div>
					<input type="hidden" name="name" value="<?php echo $item[1]; ?>" />
					<div class="col col-xs-12 spacer"></div>
				</form>
				<div class="col col-xs-12">
					<h3>Current subscriptions (<?php echo count($newsletter_emails); ?>):</h3>
					<pre><?php foreach ($newsletter_emails as $email) { echo $email."\n"; } ?></pre>
				</div>
			</div>


		</div>
	</span> <!-- loadable page part -->

				<span class="loadable-page-part dispnone" id="LPP-booksmod" data-currentview="none">
					<div class="ajax-console">
					</div>
					<?php foreach ($uncheckedBooks as $row) { ?>
	<div class="bookitem">
		<div class="cover-container">
			<img class="cover" src="./uploads/img/<?php echo $row['filename']; ?>" />
		</div>
		<div class="text-container">
			<div class="title"><?php echo $row['title']; ?> <span class="faded">-</span> <small><?php echo $row['author']; ?></small></div>
			<textarea readonly><?php echo $row['description']; ?></textarea>
			<table class="ajax-book-editor">
				<tr>
					<td class="prop">Price</td>
					<td class="valu">$<?php echo $row['price']; ?></td>
					<td class="prop">
						<button
							class="ajax-book-edit-button btn btn-xs btn-warning"
							data-name="price"
						style="opacity:0.5">----</button>
					</td>
				</tr>
				<tr>
					<td class="prop">URL</td>
					<td class="valu"><a href="<?php echo $row['link']; ?>" target="_blank"><button type="button" class="btn btn-xs btn-primary">Visit Link</button></a> <?php echo $row['link']; ?></td>
					<td class="prop">
						<button
							class="ajax-book-edit-button btn btn-xs btn-warning"
							data-name="link"
						style="opacity:0.5">----</button>
					</td>
				</tr>
				<tr>
					<td class="prop">ISBN</td>
					<td class="valu"><?php echo $row['isbn']; ?></td>
					<td class="prop">
						<button
							class="ajax-book-edit-button btn btn-xs btn-warning"
							data-name="isbn"
						style="opacity:0.5">----</button>
					</td>

				</tr>
				<tr>
					<td class="prop">Author</td>
					<td class="valu"><?php echo $row['author']; ?></td>
					<td class="prop">
						<button
							class="ajax-book-edit-button btn btn-xs btn-warning"
							data-name="price"
						style="opacity:0.5">----</button>
					</td>

				</tr>
			</table>
			
			<div class="btn-group approval-buttons" data-id="<?php echo $row['id']; ?>">
				<input type="hidden" name="title" value="<?php echo $row['title']; ?>" />
				<input type="hidden" name="link" value="<?php echo $row['link']; ?>" />
				<input type="hidden" name="reason" value="No comment was given." />
				<?php if ($row['visibility'] == "unpaid") { ?>
				<button type="button" class="btn btn-warning" style="pointer-events:none;">This book has not been paid for yet.</button>
				<?php } else { ?>
				<button data-name="approve" type="button" class="ajax-button btn btn-success">Approve</button>
				<button data-name="decline" type="button" class="ajax-button btn btn-danger">Decline</button>
				<!--<button style="opacity:0.5;" data-name="modify" type="button" class="ajax-button btn btn-warning">Modify [coming soon]</button>-->
				<?php } ?>
				<a href="?location=book/<?php echo $row['id']; ?>" target="_blank"><button type="button" class="btn btn-default">Preview Page</button></a>
			</div>
		</div>
	</div>
					<?php } ?>
					<div class="resize_spacing"></div>
				</span>



<span class="loadable-page-part dispnone" id="LPP-vidsmod" data-currentview="none">
	<div class="ajax-console">
	</div>

	<?php foreach ($uncheckedVideos as $row) { ?>
	<div class="bookitem">
		<div class="cover-container">
			<img class="cover" src="./uploads/img/<?php echo $row['filename']; ?>" />
		</div>
		<div class="text-container">
			<div class="title"><?php echo $row['title']; ?> <span class="faded">-</span> <small><?php echo $row['author']; ?></small></div>
			
			<iframe class="video" <?php /*width="420" height="315"*/ ?> src="https://www.youtube.com/embed/<?php echo $row['video_url']; ?>" frameborder="0" allowfullscreen></iframe>
			<br />
			
			<div class="btn-group video-approval-buttons" data-id="<?php echo $row['id']; ?>">
				<input type="hidden" name="title" value="<?php echo $row['title']; ?>" />
				<input type="hidden" name="link" value="<?php echo $row['link']; ?>" />
				<input type="hidden" name="reason" value="No comment was given." />

				<button data-name="video-approve" type="button" class="ajax-button btn btn-success">Approve</button>
				<button data-name="video-decline" type="button" class="ajax-button btn btn-danger">Decline</button>

				<a href="?location=book/<?php echo $row['id']; ?>" target="_blank"><button type="button" class="btn btn-default">Show Book</button></a>
			</div>
		</div>
	</div>
	<?php } ?>

</span>



<span class="loadable-page-part dispnone" id="LPP-commentsmod" data-currentview="none">
	<div class="ajax-console">
	</div>


	<?php foreach ($uncheckedComments as $row) { ?>
	<div class="panel panel-default bookitem">
		<div class="text-container panel-body">
			<div class="title"><?php echo ($row['author'] == null) ? 'Guest' : $row['author']; ?></div>
			<?php echo $row['contents']; ?>
			<br />
			
			<div class="btn-group approval-buttons" data-id="<?php echo $row['id']; ?>">
				<input type="hidden" name="title" value="<?php echo $row['title']; ?>" />
				<input type="hidden" name="link" value="<?php echo $row['link']; ?>" />
				<input type="hidden" name="reason" value="No reason was given." />

				<button data-name="comment-approve" type="button" class="ajax-button btn btn-xs btn-success">Approve</button>
				<button data-name="comment-decline" type="button" class="ajax-button btn btn-xs btn-danger">Decline</button>
			</div>
		</div>
	</div>
	<?php } ?>

</span>




			</div>
		</div>

	</div>
	<span id="SITE_FOOTER">
	</span>

</body>
</html>
<?php /*echo preg_replace('/\n+|\t+/', '', ob_get_clean());*/ ?>
