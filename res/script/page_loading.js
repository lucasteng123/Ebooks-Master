// Thanks to Christoph on SackOverflow [http://tinyurl.com/cqwglf4]
// Thanks also to the reallysimplehistory project
var changedByPage = false;
function start_polling_page_url() {

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

var WEB_PATH = "";

var clean = function () {
	$('#LPP-bookslistctrl').addClass("dispnone");
	$('#LPP-videovote').addClass("dispnone");
	$('#LPP-frontbooks').addClass("dispnone");
	$('#LPP-bookslist').addClass("dispnone");
	$('#LPP-bookview').addClass("dispnone");
}
var on_ajax_page_load = function (data) {
	activate_video_buttons();
	set_videovote_submits();
	activate_sort_buttons();
	handle_coverflow_2();
	activate_tabs(".feature-pagebox");
	activate_feature_scrollers();
	activate_searchbar();
}
var on_videovote_load = function (data) {
	activate_video_buttons();
	set_videovote_submits();
}
var on_bookslist_load = function (data) {
	activate_sort_buttons();
}
var on_frontpagebooks_load = function (data) {
	handle_coverflow_2();
	activate_tabs(".feature-pagebox");
	activate_feature_scrollers();
	set_videovote_submits();
}

var update_url = function(generatedURL,title) {
	changedByPage = true;
	fullPath = window.location.pathname + generatedURL
	window.history.pushState({'pageTitle':"BestEBooks"},"",fullPath);
}


var load_booklist = function(queryString, backURL) {
	console.log("QS:"+queryString);
	// Update #LPP-videovote
	var bookslistURL = WEB_PATH+"?location=ajax_bookslist/"+queryString;
	var fallbackURL = WEB_PATH+"?location=bookslist/"+queryString;
	if (typeof backURL !== 'undefined') {
		fallbackURL = backURL;
	}

	var jqxhr = $.get(bookslistURL, function (data) {
		$("#LPP-bookslist").html(data);
		$("#LPP-bookslist").data('currentview', queryString);
		on_ajax_page_load(data);
		update_url(fallbackURL);
	});
	jqxhr.fail(function () {
		location.replace(fallbackURL);
	});
}
var load_partial_booklist = function(queryString, selector, backQuery) {
	// Update #LPP-videovote
	var bookslistURL = WEB_PATH+"?location=ajax_bookslist/"+queryString;
	var fallbackURL = WEB_PATH+"?location=bookslist/"+queryString;
	if (typeof backQuery !== 'undefined') {
		fallbackURL = "?location=bookslist/"+backQuery;
	}

	var jqxhr = $.get(bookslistURL, function (data) {
		$(selector).first().html(data);
		$(selector).first().data('currentview', queryString);
		on_ajax_page_load(data);

		changedByPage = true;
		update_url(fallbackURL);
	});
	jqxhr.fail(function () {
		location.replace(fallbackURL);
	});
}

var load_frontpagebooks = function(fallbackurl) {
	// Update #LPP-frontbooks
	var frontbooksURL = WEB_PATH+"?location=ajax_frontbooks";
	var jqxhr = $.get(frontbooksURL, function (data) {
		$("#LPP-frontbooks").html(data);
		on_frontpagebooks_load(data);
	});
	jqxhr.fail(function () {
		location.replace(fallbackurl);
	});
}

function set_ajax_loaders() {
	$('.ajax-tab').each(function (i, obj) {
		$(this).click(function(e) {
			// Event-related Variables (i.e. properties of button)
			var tabType = $(this).data('tab-type');
			// Situation-related Variables (state of the page)
			var bookslistView = $("#LPP-bookslist").data('currentview');

			// Variables to Definitely be Used
			var generatedURL = "";

			// Category or Subcategory
			if (tabType == "category" || tabType == "subcategory") {

				// Inform location poller not to refresh the page
				changedByPage = true;

				// Commonly Checked Conditions
				var isSubCategory = (tabType == "subcategory");
				// Get category ID
				var catID = (isSubCategory) ? $(this).data('catid') : $(this).data('id');

				// Generate the URL
				{
					var urlPart = (isSubCategory) ? "cat" : "bcat";
					generatedURL = WEB_PATH+"?location=bookslist/"+urlPart+"="+$(this).data('id');
				}

				location.replace(generatedURL);
				return;

				// --- Page modifications for bookslist view ---
				clean();

				// Update bookslist to category or subcategory view
				if (isSubCategory) {
					// Update #LPP-bookslist to subcat-ID
					var subcatID = $(this).data('id');
					load_booklist("cat="+subcatID);
				} else {
					// Update #LPP-bookslist to cat-ID
					load_booklist("bcat="+catID+"&showvid=1");
				}

				$('#LPP-bookslist').removeClass("dispnone");
			}

			if (tabType == "homepage") {
				// Inform location poller not to refresh the page
				changedByPage = true;

				/* No url to generate */

				// --- Page modifications for homepage view ---
				{
					// Do not display bookslist or videovote
					clean();
					// Display the front-page books
					$('#LPP-frontbooks').removeClass("dispnone");
				}

				// Update the front-page books box
				load_frontpagebooks();

				update_url("");
			}
			
			e.stopPropagation();
			e.preventDefault();
		});
	}); // .ajaxtab each
}

function activate_searchbar() {
	$('.ajax-search').each(function (i, sform) {
		$(sform).off('.beste')
		.on('submit.beste',function (e) {
			clean();
			$('#LPP-bookslist').removeClass("dispnone");

			var query = $(sform).find('.ajax-search-input').first().val();

			var base = $(sform).data('query');
			location.href = base+"&q="+query;
			//load_booklist(base+"&q="+query);
			
			e.stopPropagation();
			e.preventDefault();
		});
	});
}

// === BOOKSLIST QUERY SCRIPTS ===
function activate_sort_buttons() {
	$('.page-choosey-button').each(function (i, obj) {
		$(obj).off('.beste')
		.on('click.beste',function(e) {
			// Don't keep button pressed in
			$(obj).blur();
			load_partial_booklist($(obj).data('query')+"&nogui=1","#LPP-bookslist .ajax-book-results-wctrl",$(obj).data('query'));

			// e.preventDefault();

		});
	});

  // Indented weirdly... sorry 'bout that.
  $('.result-sort-btn').each(function (i, obj) {
    $(obj).off('.beste')
    .on('click.beste',function(e) {
    	// don't keep button pressed in
    	$(obj).blur();
    	// initialize sorting to ascending by default
    	var sortBy = "";
    	// check if button is already activated
    	if ($(obj).hasClass('btn-primary')) {
    		// button was already clicked before,
    		// so change the direction of sorting.
	    	if ($(obj).data('direction') == "descending") {
	    		console.log("setting to desc");
	    		$(obj).find('.icon-ascending').first().css('display','inline');
	    		$(obj).find('.icon-descending').first().css('display','none');
	    		$(obj).data('direction', "ascending");
	    	} else {
	    		console.log("setting to asc");
	    		$(obj).find('.icon-ascending').first().css('display','none');
	    		$(obj).find('.icon-descending').first().css('display','inline');
	    		$(obj).data('direction', "descending");
	    		// change sort to descending
	    		var sortBy = "&order=desc";
	    	}
    	} else {
    		// button was not clicked before,
    		// so change the sort type.
    		
    		$('.result-sort-btn').each(function (j, obj2) {
	    		$(obj2).removeClass('btn-primary');
	    		$(obj2).addClass('btn-default');
	    		$(obj2).find('.glyphicon').css('display','none');
	    	});
	    	$(obj).removeClass('btn-default');
	    	$(obj).addClass('btn-primary');
	    	if ($(obj).attr('data-reverse-direction')) {
				$(obj).find('.icon-descending').first().css('display','inline');
		    	$(obj).data('direction', "descending");
	    		var sortBy = "&order=desc";
	    	} else {
		    	$(obj).find('.icon-ascending').first().css('display','inline');
		    	$(obj).data('direction', "ascending");
		    }
    	}


		// Inform location poller not to refresh the page
		//changedByPage = true; not changing url anymore!
    	load_partial_booklist($(obj).data('query')+sortBy+"&nogui=1","#LPP-bookslist .ajax-book-results-wctrl",$(obj).data('query')+sortBy);
    	/*
    	$('.result-sort-btn').each(function (j, obj2) {
    		$(obj2).removeClass('btn-primary');
    		$(obj2).addClass('btn-default');
    		$(obj2).find('.glyphicon').first().css('display','none');
    	});
    	$(obj).removeClass('btn-default');
    	$(obj).addClass('btn-primary');
    	$(obj).find('.glyphicon').first().css('display','inline');
    	*/
    });
  });
}
