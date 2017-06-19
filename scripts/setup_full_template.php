<?php
/* Notes:
 - a session should be started before calling
 - script must be included with require_once due to function definition
*/

require_once(SITE_PATH.'/scripts/misc_functions.php');
 
function setup_full_template_bookctrl() {
	//
}

function setup_full_template($tmpl, $sitedb, $ss, $RO_SESSION) {
	$tmpl->ss = $ss;
	$tmpl->ticker_messages = $sitedb->list_ticker_messages();
	$tmpl->categories = $sitedb->list_categories();
	$tmpl->paylist_items = $sitedb->get_pay_list();

	if (isset($RO_SESSION['book-pending-account'])) {
		$tmpl->book_pending = htmlentities($RO_SESSION['book-pending-account']['title'], ENT_QUOTES, 'UTF-8');
	}

	if ($RO_SESSION['logged_in'] === AccountMgr::SESSION_OKAY) {
		$tmpl->user_logged_in = "yes";
		$tmpl->user_name = $RO_SESSION['account']['name'];
		$tmpl->user_username = $RO_SESSION['account']['username'];
		if ($RO_SESSION['is_admin_user'] === "yes") {
			$tmpl->adminDisplay = "yes";
		}
	}
}
