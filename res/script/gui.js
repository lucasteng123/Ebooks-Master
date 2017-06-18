function setup_offcanvas() {
  $('[data-toggle="offcanvas"]').click(function () {
  	button = $(this);
  	if (button.html() == "Menu") button.html("&lt;");
  	else button.html("Menu");
    $('.row-offcanvas').toggleClass('active');
  });
}

// === SLIDEOUT MENUS ===
function activate_slideout_links(selector) {
	$('.slideout-link').each(function (i, link) {
		var pageName = $(link).data('page');
		$(link).click(function(e) {
			console.log('Clicky!');
			// Activate the correct page
			$(selector).find('.slideout-page').each(function (j, page) {
				console.log("Name: "+$(page).data('name'));
				if ($(page).data('name') == pageName) {
					$(page).css('display','block');
				} else {
					$(page).css('display','none');
				}
			});
			// Display slideout
			$('#slideout-box').addClass('visible');
		});
	});
	$('.slideout-hide').each(function (i, link) {
		$(link).click(function(e) {
			// Hide slideout
			$('#slideout-box').removeClass('visible');
		});
	});
}
function show_particular_slideout(selector, pageName) {
	// Activate the correct page
	$(selector).find('.slideout-page').each(function (j, page) {
		console.log("Name: "+$(page).data('name'));
		if ($(page).data('name') == pageName) {
			$(page).css('display','block');
		} else {
			$(page).css('display','none');
		}
	});
	// Display slideout
	$('#slideout-box').addClass('visible');
}

function activate_tabs(selector) {
	$('.jq-tab').each(function (i, link) {
		var pageName = $(link).data('page');
		$(link).click(function(e) {
			$(selector).find('.jq-tab').each(function (k, tab) {
				$(tab).removeClass('selected');
				$(tab).removeClass('active');
			});
			$(link).addClass('selected');
			$(link).addClass('active');
			console.log('Clicky!');
			// Activate the correct page
			$(link).closest('.jq-tab-scope').find('.jq-page').each(function (j, page) {
				console.log("Name: "+$(page).data('name'));
				if ($(page).data('name') == pageName) {
					$(page).addClass('visible');
				} else {
					$(page).removeClass('visible');
				}
			});
		});
	});
}

function activate_feature_scrollers() {
	$(".feature-canvas").each(function (c, canvas) {

		// set initial state
		var booksList = $(canvas).find('.books-container').first();
		$(canvas).find('.canvas-slide-under.prev').first().scrollLeft(0);
		$(canvas).find('.canvas-slide-under.next').first().scrollLeft(booksList.width() + 90);

		// set mous handles
		$(canvas).find('.canvas-slide').each(function (s, slider_elem) {
			var scrollTimer = null;
			$(slider_elem).mouseenter(function () {
				scrollTimer = setInterval(function () {
					var toAdd = ($(slider_elem).hasClass('prev')) ? -5 : 5;
					console.log("tick:"+toAdd);
					var booksList = $(canvas).find('.books-container').first();
					var currPos = $(booksList).scrollLeft();
					$(booksList).scrollLeft(currPos + toAdd);
					$(canvas).find('.canvas-slide-under.prev').first().scrollLeft(currPos + toAdd);
					console.log(currPos + toAdd);
					$(canvas).find('.canvas-slide-under.next').first().scrollLeft(currPos + toAdd + booksList.width() + 90);
				},20);
			});
			$(slider_elem).mouseleave(function () {
				clearInterval(scrollTimer);
			});
		});
	});
}

function activate_video_buttons() {
	$('.jq-video-play').each(function (i, obj) {
		$(obj).click(function (e) {
			var vidid = $(obj).data('vidid');
			newContents = '<iframe class="video" src="https://www.youtube.com/embed/'+vidid+'" frameborder="0" allowfullscreen></iframe>';
			$(obj).parent().html(newContents);
		});
	});
}

function activate_misc_buttons() {
	var newsletter_button_function = function() {
		var email = prompt("Please enter your email address to subscribe!");
		var url = "index.php?location=ajax_newsletter"
		if (email != null) {
			emailData = {'email': email};
			var jqxhr = $.ajax({
				type: "POST",
				url: url,
				data: emailData, // serializes the form's elements.
				dataType: "json"
			});
			jqxhr.done(function (data) {
				if (data.status == "okay") {
					var msg = "Success! You have subscribed to our newsletter!\n\n";
					msg += "You may unsubscribe at any time by clicking the 'unsubscribe' link in any email you receive from us.";
					alert(msg);
				}
				else if (data.status == "form_error") {
					alert("Email was invalid; please try again!");
					newsletter_button_function();
				}
				else {
					alert("An unknown error occured!\n"+data.status+"\n"+data.message);
				}
			});
			jqxhr.fail(function () {
				alert("An error happened! Sorry about that. :/ Please try again soon!");
			});
		}
	};
	$('.beste-button').each(function () {
		$(this).click(function () {
			if ($(this).data('act') == "newsletter") {
				newsletter_button_function();
			}
		});
	});
}