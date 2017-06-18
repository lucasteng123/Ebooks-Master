var changedByPage = false;
function start_polling_page_url() {
	// Thanks to Christoph on SackOverflow [http://tinyurl.com/cqwglf4]
	// Thanks also to the reallysimplehistory project

	var get_url_without_hash = function () {
		var windowURL = window.location.href;
		var i = windowURL.indexOf("#");
		return (i >= 0) ? windowURL.substr(0,i) : windowURL;
	}

	var lastURL = get_url_without_hash();
	
	return setInterval(function () {
		if (lastURL != get_url_without_hash()) {
			console.log("poll_hash: hash changed");
			if (changedByPage) {
				console.log("poll_hash: ajax load - not reloading page");
				lastURL = get_url_without_hash();
				changedByPage = false;
			} else {
				location.reload();
			}
		}
	}, 100)
}

function BestEbooksContentsLoader() {

	var on_videovote_load = function (data) {
		activate_video_buttons();
	}
	var on_bookslist_load = function (data) {
		// do nothing
	}

	var update_url = function(generatedURL,title) {
		fullPath = window.location.pathname + generatedURL
		window.history.pushState({'pageTitle':"BestEBooks"},"",fullPath);
	}

	var load_videovote = function(categoryIdent) {
		// Update #LPP-videovote
		var videovoteURL = "index.php?location=ajax_videovote/"+categoryIdent;
		var jqxhr = $.get(videovoteURL, function (data) {
			$("#LPP-videovote").html(data);
			$("#LPP-videovote").data('currentview', categoryIdent);
			on_videovote_load(data);
		});
		jqxhr.fail(function () {
			$("#LPP-videovote").html('<div class="error">Failed to Load the Video Vote Box</div>');
		});
	}

	var load_booklist = function(listIdent, fallbackurl) {
		// Update #LPP-videovote
		var bookslistURL = "index.php?location=ajax_bookslist/"+listIdent;
		var jqxhr = $.get(bookslistURL, function (data) {
			$("#LPP-bookslist").html(data);
			$("#LPP-bookslist").data('currentview', listIdent);
			on_bookslist_load(data);
		});
		jqxhr.fail(function () {
			location.replace(fallbackurl);
		});
	}

	var load_page_from_path = function(path) {

		// Inform location poller not to refresh the page
		changedByPage = true;

		// Commonly Checked Conditions
		var isSubCategory = (tabType == "subcategory");
		// Get category ID
		var catID = (isSubCategory) ? $(this).data('catid') : $(this).data('id');

		// Generate the URL
		{
			var urlPart = (isSubCategory) ? "subcat" : "cat";
			generatedURL = "?location=bookslist/"+urlPart+"/"+$(this).data('id');
		}

		// Page modifications for bookslist view
		{
			$('#LPP-bookslistctrl').removeClass("dispnone");
		}

		// Update bookslist to category or subcategory view
		if (isSubCategory) {
			// Update #LPP-bookslist to subcat-ID
			var subcatID = $(this).data('id');
			load_booklist("subcat-"+subcatID);
			$("#LPP-videovote").html("");
			$("#LPP-videovote").data('currentview', "none");
		} else {
			// Update #LPP-bookslist to cat-ID
			load_booklist("cat-"+catID, generatedURL);
			// Update category-related elements if category is changed
			if (videovoteView != "cat-"+catID) {
				// Update #LPP-videovote
				load_videovote("cat-"+catID);
			}
		}

		update_url(generatedURL);
}

/*

	$('.ajax-tab').each(function (i, obj) {
		$(this).click(function(e) {
			// Event-related Variables (i.e. properties of button)
			var tabType = $(this).data('tab-type');

			// Situation-related Variables (state of the page)
			var videovoteView = $("#LPP-videovote").data('currentview');
			var bookslistView = $("#LPP-bookslist").data('currentview');

			// Variables to Definitely be Used
			var generatedURL = "";

			// Category or Subcategory
			if (tabType == "category" || tabType == "subcategory") {

				// Inform location poller not to refresh the page
				changedByPage = true

				// Commonly Checked Conditions
				var isSubCategory = (tabType == "subcategory");
				// Get category ID
				var catID = (isSubCategory) ? $(this).data('catid') : $(this).data('id');

				// Generate the URL
				{
					var urlPart = (isSubCategory) ? "subcat" : "cat";
					generatedURL = "?location=bookslist/"+urlPart+"/"+$(this).data('id');
				}

				// Page modifications for bookslist view
				{
					$('#LPP-bookslistctrl').removeClass("dispnone");
				}

				// Update bookslist to category or subcategory view
				if (isSubCategory) {
					// Update #LPP-bookslist to subcat-ID
					var subcatID = $(this).data('id');
					load_booklist("subcat-"+subcatID);
					$("#LPP-videovote").html("");
					$("#LPP-videovote").data('currentview', "none");
				} else {
					// Update #LPP-bookslist to cat-ID
					load_booklist("cat-"+catID, generatedURL);
					// Update category-related elements if category is changed
					if (videovoteView != "cat-"+catID) {
						// Update #LPP-videovote
						load_videovote("cat-"+catID);
					}
				}

				update_url(generatedURL);
			}

			if (tabType == "homepage") {
				// Inform location poller not to refresh the page
				changedByPage = true
				// Page modifications for non-bookslist view
				$('#LPP-bookslistctrl').addClass("dispnone");
				$('#LPP-videovote').addClass("dispnone");
				update_url("");
			}
			
			e.stopPropagation();
			e.preventDefault();
		});
	});

*/