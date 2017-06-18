<?php
/* Notes:
 - a session should be started before calling
 - must be included with require_once
    (though I suppose that's like saying "code should be written well")
*/

require_once(SITE_PATH.'/scripts/setup_videovote_template.php');

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

// This function takes an array of values and converts it
// into a query string with some of those values removed.
function get_query_without_indexes($query_array,$keysToDelete) {
	$query_array_copy = $query_array;
	foreach ($query_array_copy as $key => $val) {
		foreach ($keysToDelete as $nokey) if ($key == $nokey) {
			unset($query_array_copy[$key]);
		}
	}
	return http_build_query($query_array_copy);
}

function setup_bookslist_template($bookslist_tmpl, $con, $sitedb, $bookLister, $query_array) {

	// Setup videovote template if a base category is queried
	if (
		array_key_exists('bcat',$query_array)
		&& !array_key_exists('q',$query_array)
		&& !array_key_exists('cat',$query_array)
	) {
		if (($video_pair_id = $sitedb->check_category_has_video($query_array['bcat'])) !== false) {
			$videovote_tmpl = new Template();
			$videovote_tmpl->set_template_file(SITE_PATH.'/templates/videovote.template.php');
			setup_videovote_template($videovote_tmpl, $con, $sitedb, $video_pair_id, $_SESSION);
			$bookslist_tmpl->videovote_tmpl = $videovote_tmpl;
		}
	}

	if (array_key_exists('bcat', $query_array)
		|| array_key_exists('cat', $query_array)) {
		$bookslist_tmpl->show_searchbar = true;
	}

	// Prepare bookslister
	$bookLister->set_query_array($query_array);

	// Get and set info
	$page_info = $bookLister->get_page_info_by_query();
	$bookslist_tmpl->page_info = $page_info;
	if (array_key_exists('nogui', $query_array)) {
		$bookslist_tmpl->nogui = 1;
	}

	// Get and set list of books
	$books = $bookLister->fetch_book_list_by_query();
 	$bookslist_tmpl->books = $books;

	// Add query metadata to sorting template
	$resultsInfo = $bookLister->get_results_info();
	$bookslist_tmpl->results_info = $resultsInfo;
	// Todo: get rid of these once it's already sanitized data
	$bookslist_tmpl->sort = htmlentities($resultsInfo['sort']);
	$bookslist_tmpl->order = htmlentities($resultsInfo['order']);
	$bookslist_tmpl->page = htmlentities($resultsInfo['page']);
	$bookslist_tmpl->result_count = htmlentities($resultsInfo['count']);

	// Generate base query for sort buttons
	$bookslist_tmpl->base_query_sorting = get_query_without_indexes($query_array,array('noctrl','sort','order'));
	$bookslist_tmpl->base_query_pages = get_query_without_indexes($query_array,array('noctrl','off'));
	$bookslist_tmpl->base_query_search = get_query_without_indexes($query_array,array('noctrl','q'));
	/*
 	$baseQuery = '';
 	if (array_key_exists('bcat', $query_array)) $baseQuery .= '&bcat='.htmlentities($query_array['bcat']);
 	if (array_key_exists('cat', $query_array)) $baseQuery .= '&cat='.htmlentities($query_array['cat']);
 	$bookslist_tmpl->baseQuery = $baseQuery;
 	*/
}
