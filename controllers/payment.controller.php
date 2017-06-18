<?php

session_start();

require_once(SITE_PATH.'/scripts/setup_full_template.php');

$methods = array();

$methods['run'] = function($instance) {
	$r = $instance->route;

	// Generate a SiteDB instance
	$con = $instance->tools['con_manager']->get_connection();
	$sitedb = new SiteDB($con);
	$ss = new SiteStrings($con);
	$bookLister = new BookLister($con);

	$html = new Template();
	$html->set_template_file(SITE_PATH.'/templates/full.template.php');

	// Add information to page
	setup_full_template($html, $sitedb, $ss, $_SESSION);

	// Add subpages to page
	if ($_SESSION['logged_in'] !== AccountMgr::SESSION_OKAY) {
		// === Apply error template to page ===
		$message = new Template();
		$message->set_template_file(SITE_PATH.'/templates/simple_message.template.php');
		$html->part_frontbooks = $message;
		$message->title = "You are not currently logged in.";
		$message->message = "You can no longer view your uploaded books because you aren't logged in.<br />If you just logged out from your account page, this is normal.<br /><br /><b>If you reached this page after a payment</b>, please <b>DO NOT</b> close this page. Login in a new tab or window and then reload this page.";
	} else {
		// === Apply payment template to page ===
		$payment_tmpl = new Template();
		// set template
		$payment_tmpl->set_template_file(SITE_PATH.'/templates/payment.template.php');
		$html->part_frontbooks = $payment_tmpl;

		// === Check if a payment was completed ===
		$payment = new Payment();
		$payment->check_payment();
		if ($payment->has_payment()) {
			$bookid = $payment->get_value('custom');
			$payment_tmpl->has_payment = true;
			if (!$payment->has_valid_tx()) {
				$payment_tmpl->has_error = true;
				$payment_tmpl->pay_msg = "Could not receive payment information from PayPal! Did you cancel your payment?";
			} else if ($payment->get_payment_status() != "completed") {
				$payment_tmpl->has_error = true;
				$payment_tmpl->pay_msg = "The payment was not completed. (".$payment->get_payment_status().")";
			} else if (filter_var($bookid, FILTER_VALIDATE_INT) === false) {
				$payment_tmpl->has_error = true;
				$payment_tmpl->pay_msg = "The book ID sent to the server was invalid. The payment system may have been tampered with, and this event has been logged.";
			} else {
				$payment_tmpl->pay_msg = "Your payment has been complete! Your book should now appear in the list as paid.";
				// Change book listing:
				$bookid = intval($bookid);
				$sitedb->update_book_visibility($bookid, "unchecked");
			}
		}

		$page = 1;
		if (isset($r[0])) {
			$page = intval($r[0]); // enforce valid int
		}

		// === Add list of books to Payment Page ===
		$payment_tmpl->user_books = $bookLister->fetch_book_list_by_account($_SESSION['account']['id'], $page);
		$payment_tmpl->user_books_pagec = $bookLister->get_results_info()['page_count'];
		$payment_tmpl->user_books_page = $page;
	}
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