<?php

class BookListerException extends Exception {
}

class BookLister {
	private $pdo = null;
	private $queryArray = null;
	private $resultsInfo = array();

	function __construct($pdo) {
		if ( !(gettype($pdo) === 'object') ) {
			FrameworkException::throw_datatype_exception(VarTools::what_is($pdo), 'PDO (object)');
		}
		$this->pdo = $pdo;
		$this->wd = realpath(dirname(__FILE__));
		$this->SETTINGS = array(
			'count' => 10,
			'next_pages' => 2
		);
	}

	function set_result_limit($count) {
		$this->SETTINGS['count'] = $count;
	}

	function set_query_string($queryStr) {
		// TODO: throw exception API_ERROR if $queryStr is not a string.
		// (or a framework datatype exception)
		$this->queryString = $queryStr;
		parse_str($queryStr,$qArr);
		$this->queryArray = $qArr;
	}
	function set_query_array($queryArr) {
		// TODO: throw exception API_ERROR if $queryStr is not an array.
		// (or a framework datatype exception)
		$this->queryArray = $queryArr;
	}

	function get_results_info() {
		// metadata for book results
		return $this->resultsInfo;
	}

	function get_page_info_by_query() {
		/* Returns an array with the following values:
			base_category_name -> string (empty string if none)
			category_name -> string (empty string if none)
			base_category_dne -> set and true if queried base category doesn't exist
			category_dne -> set and true if queried category doesn't exist
		*/

		// TODO: throw exception API_ERROR if $queryArray is not a null.
		$_QUERY = $this->queryArray;

		$retVal = array();

		// Default values
		$retVal['base_category_name'] = "";
		$retVal['category_name'] = "";
		$retVal['title'] = "Results";

		// Check if base category is queried, fetch its name
		if (array_key_exists('bcat', $_QUERY)) {
			// Verify query parameter value
			if (filter_var($_QUERY['bcat'], FILTER_VALIDATE_INT) === false) {
				$retVal['base_category_dne'] = true;
				$retVal['base_category_invalid'] = true;
			} else {
				$baseID = $_QUERY['bcat'];
				// Add SQL condition and binding
				$sql = "SELECT * FROM base_categories WHERE id=:baseid";
				$stmt = $this->pdo->prepare($sql);
				$stmt->bindValue("baseid",  $baseID, PDO::PARAM_INT );
				$stmt->execute();
				if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		 			$retVal['base_category_name'] = $row['name'];
		 			$retVal['title'] = $row['name'];
		 			$retVal['scope_name'] = $row['name'];
		 		} else {
		 			$retVal['base_category_dne'] = true;
		 		}
			}
		}

		// Check if category is queried, fetch its name
		if (array_key_exists('cat', $_QUERY)) {
			// Verify query parameter value
			if (filter_var($_QUERY['cat'], FILTER_VALIDATE_INT) === false) {
				$retVal['category_dne'] = true;
				$retVal['category_invalid'] = true;
			} else {
				$catID = $_QUERY['cat'];
				// Add SQL condition and binding
				$sql = "SELECT a.name as aname, c.name as cname FROM categories c
				LEFT JOIN base_categories a ON c.base_category=a.id
				WHERE c.id=:catid";

				$stmt = $this->pdo->prepare($sql);
				$stmt->bindValue("catid",  $catID, PDO::PARAM_INT );
				$stmt->execute();
				if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		 			$retVal['base_category_name'] = $row['aname'];
		 			$retVal['category_name'] = $row['cname'];
		 			$retVal['title'] = $row['aname'] . ' > '.$row['cname'];
		 			$retVal['scope_name'] = $row['cname'];
		 		} else {
		 			$retVal['category_dne'] = true;
		 		}
			}
		}

		if (array_key_exists('q', $_QUERY)) {
			$add = " in All Categories";
			if (strlen($retVal['base_category_name']) > 0) $add = " in ".$retVal['base_category_name'];
			if (strlen($retVal['category_name']) > 0) $add = " in ".$retVal['category_name'];
			$retVal['title'] = "Results for '".htmlentities($_QUERY['q'])."'".$add;
		}

		return $retVal;
	}

	function fetch_book_list_by_query() {
		$SETTINGS['count'] =      $this->SETTINGS['count'];
		$SETTINGS['next_pages'] = $this->SETTINGS['next_pages'];

		// TODO: throw exception API_ERROR if $queryArray is not a null.
		$_QUERY = $this->queryArray;

		$bindings = array();

		// Generate baseSQL
		// - depends on whether we're filtering by category, subcategory, or none
		$baseSQL = "SELECT b.*, i.filename FROM books b
		LEFT JOIN uploaded_images i ON b.image_id = i.id
		LEFT JOIN categories c ON b.category = c.id WHERE b.visibility=:visibility";
		{
			// Check if query filters by base-category
			if (array_key_exists('bcat', $_QUERY)) {
				// Verify query parameter value
				if (filter_var($_QUERY['bcat'], FILTER_VALIDATE_INT) === false) {
					return false;
				}
				// Add SQL condition and binding
				$paramValue = $_QUERY['bcat'];
				$baseSQL .= " AND c.base_category=:baseid";
				// Append an array as one element of the bindings array
				$bindings[] = array('baseid', $paramValue, PDO::PARAM_INT);
			}

			if (array_key_exists('cat', $_QUERY)) {
				// Verify query parameter value
				if (filter_var($_QUERY['cat'], FILTER_VALIDATE_INT) === false) {
					return false;
				}
				// Add SQL condition and binding
				$paramValue = $_QUERY['cat'];
				$baseSQL .= " AND b.category=:cid";
				// Append an array as one element of the bindings array
				$bindings[] = array('cid', $paramValue, PDO::PARAM_INT);
			}

			if (array_key_exists('featured', $_QUERY)) {
				$baseSQL .= " AND b.featured=1";
			}
		}

		// Generate searchString
		$searchString = "";
		{
			if (array_key_exists('q', $_QUERY)) {
				$words = explode(' ',$_QUERY['q']);
				$this->resultsInfo['hasquery'] = true;
				$this->resultsInfo['query'] = $_QUERY['q'];
				if (count($words) > 0) {
					$searchString .= " AND (";
					foreach ($words as $key => $q) {
						$q = '%'.$q.'%';
						if ($key > 0) $searchString .= " OR";
						$searchString .= " (b.title LIKE :query".$key." OR b.author LIKE :query".$key." OR b.isbn=:query".$key.")";
						$bindings[] = array('query'.$key, $q, PDO::PARAM_STR);
					}
					$searchString .= ")";
				}
			} else {
				$this->resultsInfo['hasquery'] = false;
			}
		}

		// Generate sortString
		$this->resultsInfo['sort'] = 'date';
		$sortString = " ORDER BY b.date_posted";
		{
			$direction = "ASC";
			$this->resultsInfo['order'] = 'asc';
			if (array_key_exists('order', $_QUERY)) {
				if ($_QUERY['order'] == "desc") {
					$direction = "DESC";
					$this->resultsInfo['order'] = 'desc';
				}
			}

			// Bit mixed here:
			// The default order for sorting by a particular field is ASCENDING
			// The default result set, if no field is specified, is by DATE DESCENDING
			if (array_key_exists('sort', $_QUERY)) {
				switch ($_QUERY['sort']) {
					case 'author':
						$this->resultsInfo['sort'] = 'author';
						$sortString = " ORDER BY b.author ".$direction;
						break;
					case 'title':
						$this->resultsInfo['sort'] = 'title';
						$sortString = " ORDER BY b.title ".$direction;
						break;
					case 'date':
						$this->resultsInfo['sort'] = 'date';
						$sortString = " ORDER BY b.date_posted ".$direction;
						break;
					case 'views':
						$this->resultsInfo['sort'] = 'views';
						$sortString = " ORDER BY b.views ".$direction;
						break;
					default:
						$this->resultsInfo['sort'] = 'date';
						$this->resultsInfo['order'] = 'desc';
						$sortString = " ORDER BY b.date_posted DESC";
				}
			} else {
				$this->resultsInfo['sort'] = 'date';
				$this->resultsInfo['order'] = 'desc';
				$sortString = " ORDER BY b.date_posted DESC";
			}
		}

		// Generate LIMIT and OFFSET
		$limOffString = null;
		$this->resultsInfo['page'] = 1;
		$this->resultsInfo['count'] = $SETTINGS['count'];
		{
			$count = $SETTINGS['count'];
			$limit = " LIMIT ".$count;
			$offset = " OFFSET 0";
			if (array_key_exists('off', $_QUERY))
			if (! filter_var($_QUERY['off'], FILTER_VALIDATE_INT) === false)
			if (intval($_QUERY['off']) >= 0) {
				$offset = " OFFSET ".intval($_QUERY['off']);
				$this->resultsInfo['page'] = floor(intval($_QUERY['off'])/$count) + 1;
			}

			$limOffString = $limit.$offset;
		}
		if ($this->resultsInfo['page'] < 3) $SETTINGS['next_pages'] += 2;

		$finalSQL = $baseSQL.$searchString.$sortString.$limOffString;

		// "Prepare" statement (PDO might just be emulating *shrugs*)
		$stmt = $this->pdo->prepare($finalSQL);
		// Apply generated bindings
		foreach ($bindings as $b) {
			$stmt->bindValue($b[0], $b[1], $b[2]);
		}
		// Apply constant bindings
		$stmt->bindValue("visibility", "public", PDO::PARAM_STR );
		// Execute the statement
		$stmt->execute();

		// List all the books found
		$results = array();
	 	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		// Compute additional row information
		 	$row['description_length'] = strlen(utf8_decode($row['description']));
		 	if ($row['description_length'] > 200) {
		 		$row['shortened'] = true;
		 		$row['short_description'] = mb_substr($row['description'], 0, 200, 'utf-8') . ' ...';
		 	} else {
		 		$row['shortened'] = false;
		 		$row['short_description'] = $row['description'];
		 	}
		 	// Append this row
	 		$results[] = $row;
	 	}
	 	$this->resultsInfo['results_count'] = count($results);

	 	// Check if further pages exist
	 	for ($i=0; $i < $SETTINGS['next_pages']; $i++) {
	 		$off = ($this->resultsInfo['page']+$i)*$SETTINGS['count'];
	 		$sql = $baseSQL.$searchString.$sortString." LIMIT 1 OFFSET ".$off;
			// "Prepare" statement (PDO might just be emulating *shrugs*)
			$stmt = $this->pdo->prepare($sql);
			// Apply generated bindings
			foreach ($bindings as $b) {
				$stmt->bindValue($b[0], $b[1], $b[2]);
			}
			// Apply constant bindings
			$stmt->bindValue("visibility", "public", PDO::PARAM_STR );
			// Execute the statement
			$stmt->execute();

			if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
				$this->resultsInfo['nextpage'][$i] = true;
			} else {
				$this->resultsInfo['nextpage'][$i] = false;
			}
	 	}

	 	// Return all the books found. Yay!
	 	return $results;

	} //function


	function fetch_book_list_by_account($uploaderID) {

		// Determine how many pages of books this person will have
		// (this functions differently from paginating books in a
		// category, since the amount of books should be much smaller)

		$sql = "SELECT COUNT(*) FROM books";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$this->resultsInfo['full_count'] = $stmt->fetchColumn();

		// sql code
		$sql = "SELECT b.*, i.filename FROM books b
		LEFT JOIN uploaded_images i ON b.image_id = i.id
		LEFT JOIN categories c ON b.category = c.id WHERE b.uploader_id=:uploader_id";

		// "Prepare" statement (PDO might just be emulating *shrugs*)
		$stmt = $this->pdo->prepare($sql);
		// Apply constant bindings
		$stmt->bindValue("uploader_id", $uploaderID, PDO::PARAM_INT );
		// Execute the statement
		$stmt->execute();

		// List all the books found
		$results = array();
	 	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		// Compute additional row information
		 	$row['description_length'] = strlen(utf8_decode($row['description']));
		 	if ($row['description_length'] > 200) {
		 		$row['shortened'] = true;
		 		$row['short_description'] = mb_substr($row['description'], 0, 200, 'utf-8') . ' ...';
		 	} else {
		 		$row['shortened'] = false;
		 		$row['short_description'] = $row['description'];
		 	}
		 	// Append this row
	 		$results[] = $row;
	 	}
	 	$this->resultsInfo['results_count'] = count($results);

	 	return $results;

	} //function
} //class

