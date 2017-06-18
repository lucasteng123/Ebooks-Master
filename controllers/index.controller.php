<?php

session_start();

require_once(SITE_PATH.'/scripts/setup_full_template.php');
require_once(SITE_PATH.'/scripts/setup_videovote_template.php');
require_once(SITE_PATH.'/scripts/setup_frontbooks_template.php');

$methods = array();

$methods['run'] = function($instance) {
	$r = $instance->route;

	// Generate a SiteDB instance
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$ss = new SiteStrings($con);
	$gc = new GuessContest($con);
	$bookLister = new BookLister($con);
	$bookLister->set_result_limit(15);
	$shirts = new ShirtMgr($con);

	// Generate topbooks template
	$topbooks_tmpl = new Template();
	$topbooks_tmpl->set_template_file(SITE_PATH.'/templates/frontbooks.template.php');
	{
		$isContestRunning = $gc->check_contest_running();
		$topbooks_tmpl->isContestRunning = $isContestRunning;
		if ($isContestRunning) {
			$topbooks_tmpl->wordLength = $gc->get_current_word_length();
			// Set contest word (used to generate boxes and detect spaces)
			$topbooks_tmpl->word = $gc->get_current_word();
		}
		$topbooks_tmpl->video = $ss->get_value('gc.video');
		$topbooks_tmpl->gcPrize = $ss->get_value('gc.prize_string');
		$topbooks_tmpl->gcDesc = $ss->get_html('gc.description');
		$topbooks_tmpl->gcTitle = $ss->get_html('gc.title');
		$topbooks_tmpl->gcQuestion = $ss->get_html('gc.question');
		$topbooks_tmpl->gcWinners = $gc->list_roll();

		$video_pair_id = $ss->get_value("homepage.videovote_id");

		if ($video_pair_id != "none") {
			// Generate videovote template
			$videovote_tmpl = new Template();
			{
				$videovote_tmpl->set_template_file(SITE_PATH.'/templates/videovote.template.php');
				$videovote_tmpl->title = $ss->get_html("homepage.videovote_title");
				setup_videovote_template($videovote_tmpl, $con, $sitedb, $video_pair_id, $_SESSION);
				$videovote_tmpl->has_videos = true;
				$videovote_tmpl->base_category = "";
			}
			$topbooks_tmpl->videovote_tmpl = $videovote_tmpl;
		}

		{
			$result = $shirts->get_tshirt_list();
			$shirts_tmpl = new Template();
			$shirts_tmpl->set_template_file(SITE_PATH . '/templates/tshirts.template.php');
			$shirts_tmpl->tshirts = $result;
			$topbooks_tmpl->tshirts = $shirts_tmpl;
		}

		setup_frontbooks_template($topbooks_tmpl, $con, $sitedb, $bookLister, $query_array);
	}

	$html = new Template();
	$html->set_template_file(SITE_PATH.'/templates/full.template.php');

	// Add information to page
	setup_full_template($html, $sitedb, $ss, $_SESSION);

	// Add subpages to page
	$html->part_frontbooks = $topbooks_tmpl;
	//$html->part_bookslist = $testing_tmpl;

	$html->run();
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