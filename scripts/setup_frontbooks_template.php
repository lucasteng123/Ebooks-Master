<?php
/* Notes:
 - a session should be started before calling
 - must be included with require_once
    (though I suppose that's like saying "code should be written well")
*/

/*
function verify_valid_route($r) {
	$goodToGo = array_key_exists(0, $r) && array_key_exists(1, $r)
		&& ($r[0] === "cat" || $r[0] === "subcat")
		&& (! filter_var($r[1], FILTER_VALIDATE_INT) === false);

	if ($goodToGo) $goodToGo = $r[1] >= 0;

	if ( ! $goodToGo ) return false;
	return true;
}
function verify_base_category_exists($con,$type,$queryID) {
	$sql = "SELECT * FROM base_categories WHERE id=:baseid";
	$stmt = $con->prepare($sql);
	$stmt->bindValue("baseid",  $queryID, PDO::PARAM_INT );
	$stmt->execute();
	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		return true;
	} else {
		return false;
	}
	return false;
}
*/


function setup_frontbooks_template($frontbooks_tmpl, $con, $sitedb, $bookLister, $query_array) {
	$sql = "SELECT * FROM base_categories";
	$stmt = $con->prepare($sql);
	$stmt->bindValue("baseid",  $baseID, PDO::PARAM_INT );
	$stmt->execute();
	$page_book_lists = array();
	while ( $basecat = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		$bookLister->set_query_string("bcat=".$basecat['id']."&sort=views&order=desc");
		$list_item = array();
		$list_item['title'] = "Top Books in ".ucwords($basecat['name']);
		$list_item['books'] = $bookLister->fetch_book_list_by_query();

		$page_book_lists[] = $list_item;
	}
	$frontbooks_tmpl->page_book_lists = $page_book_lists;

	$bookLister->set_query_string("sort=views&order=desc");
	$frontbooks_tmpl->list_mostviewed = $bookLister->fetch_book_list_by_query();

	$bookLister->set_query_string("sort=date&order=desc");
	$frontbooks_tmpl->list_newest = $bookLister->fetch_book_list_by_query();

	$bookLister->set_query_string("featured=1&sort=date&order=desc");
	$frontbooks_tmpl->list_featured = $bookLister->fetch_book_list_by_query();
}
