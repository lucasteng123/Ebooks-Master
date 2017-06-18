<?php

session_start();

require_once(SITE_PATH.'/scripts/setup_full_template.php');
require_once(SITE_PATH.'/scripts/setup_videovote_template.php');
require_once(SITE_PATH.'/scripts/setup_frontbooks_template.php');

$methods = array();


$methods['single'] = function($instance, $html, $ebooks, $eblog) {
	$r = $instance->route;

	if (filter_var($r[0], FILTER_VALIDATE_INT) === false) {
		// Return template with error message (invalid id)
		die("temporary error 1");
	}
	$postID = intval($r[0]);

	// Get post from database
	$post = $eblog->get_single_post($postID);

	if ($post === false) {
		// Return template with error message (post not found)
		header('Location: '.WEB_PATH.'/ebooks');
	}

	// Setup single post template
	$ebooks->set_template_file(SITE_PATH.'/templates/ebooks_single.template.php');

	// Apply meta information
	$ebooks->postID = $postID;

	// Apply post information
	$html->title = $post->get_title();
	$ebooks->contents = $post->get_html();
	$ebooks->comments = $post->get_comments();
	$ebooks->post = $post; // Ideally this should be eliminated

	// $ebooks modified by reference (i.e. willn't return)
};
$methods['list'] = function($instance, $ebooks, $eblog) {
	// Set template to multiple entries template
	$ebooks->set_template_file(SITE_PATH.'/templates/ebooks.template.php');
	// Fetch list of blog entries and apply to template
	$ebooks->posts = $eblog->list_blog_posts();
	// print_r($eblog->list_blog_posts());

	// $ebooks modified by reference (i.e. willn't return)
};

$methods['main'] = function($instance) {
	$r = $instance->route;

	// Generate a SiteDB instance
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$ss = new SiteStrings($con);
	// Instiatiate the EbooksBlog Manager
	$eblog = new EbooksBlogMgr($con);

	// Instantiate full page tempalte
	$html = new Template();
	$html->set_template_file(SITE_PATH.'/templates/full.template.php');
	// Add information to page
	setup_full_template($html, $sitedb, $ss, $_SESSION);

	// Instantiate the Ebooks blog
	$ebooks = new Template();
	$ebooks->ss = $ss;

	// Add ebooks template to HTML
	$html->part_blog = $ebooks;

	// If a specific blog entry was requested
	if (isset($r[0]) && $r[0] != '') {
		// Fetch and return single-entry page
		$instance->single($html, $ebooks,$eblog);
		if ($_SESSION['is_admin_user'] === "yes") {
			$ebooks->adminDisplay = true;
		}
	} else {
		// Fetch and return entry listing page
		$instance->list($ebooks,$eblog);
	}

	return $html;
};

$methods['run'] = function($instance) {
	// Get template from main methodd
	$tmpl = $instance->main();

	// Run the template
	$tmpl->run();
};

$page_controller = new Controller($methods);
unset($methods);

/*

$methods = array();

$methods['run'] = function($instance) {
	$r = $instance->route;
	$page = new Template();
	$page->set_template_file(SITE_PATH.'/templates/home.template.php');
	if (VarTools::key_exists_equals(0,$r,"ajax")) {
		$page->run();
	} else {
		$html = new Template();
		$html->set_template_file(SITE_PATH.'/templates/full.template.php');
		$html->subTemplate = $page;
		$html->run();
	}
};

$page_controller = new Controller($methods);
unset($methods);

*/