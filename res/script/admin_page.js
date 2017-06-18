function redirect_with_timer(timer_obj,url) {
	newTime = timer_obj.data('time') - 1;
	timer_obj.data('time',newTime);
	timer_obj.html(newTime);
	if (newTime == 0) {
		location.replace(url);
	}
}

function reload_with_timer(timer_obj) {
	newTime = timer_obj.data('time') - 1;
	timer_obj.data('time',newTime);
	timer_obj.html(newTime);
	if (newTime == 0) {
		location.reload();
	}
}

function set_ajax_submits() {
	
	function regular_submit(e, obj, url, redir) {
		var finished = function (message, status, redir) {
			var messageObj = $(obj).closest('form').find('.message').first();
			console.log('|'+status+'|');
			if (status == "good") {
				message += ' Reloading in <span class="timer" data-time="5">5</span>...';
			}
			messageObj.html(message);
			messageObj.addClass('visible');
			messageObj.addClass(status);
			if (status == "good") {
				setInterval(function () {
					redirect_with_timer(messageObj.find('.timer').first(), redir);
				}, 500)
			}
		};
		var jqxhr = $.ajax({
			type: "POST",
			url: url,
			data: $(obj).serialize(), // serializes the form's elements.
			dataType: "json"
		});
		jqxhr.done(function (data) {
			finished(data.message, data.status, redir);
		});
		jqxhr.fail(function (data) {
			finished("Server error encountered!","bad");
		});
		e.stopPropagation();
	    e.preventDefault();
	}
	
	$('.ajax-form').each(function (i, obj) {
		if ($(obj).data('name') == "ticker") {
			$(obj).submit(function(e) {
				regular_submit(
					e,obj,
					"index.php?location=ajax_admin/ticker",
					"?location=admin/ticker"
				);
			});
		} else if ($(obj).data('name') == "category") {
			$(obj).submit(function(e) {
				regular_submit(
					e,obj,
					"index.php?location=ajax_admin/categories",
					"?location=admin/categories"
				);
			});
		} else if ($(obj).data('name') == "subcat") {
			$(obj).submit(function(e) {
				regular_submit(
					e,obj,
					"index.php?location=ajax_admin/subcats",
					"?location=admin/categories");
			});
		} else if ($(obj).data('name') == "videovote") {
			$(obj).submit(function(e) {
				regular_submit(
					e,obj,
					"index.php?location=ajax_admin/videovote",
					"?location=admin/videovote");
			});
		} else if ($(obj).data('name') == "sitestrings") {
			$(obj).submit(function(e) {
				regular_submit(
					e,obj,
					"index.php?location=ajax_admin/sitestrings",
					"?location=admin/sitestrings");
			});
		} /*else if ($(obj).data('name') == "emailout") {
			$(obj).submit(function(e) {
				if (confirm("Are you sure you want to mail to all subscribers?")) {
					regular_submit(
						e,obj,
						"index.php?location=ajax_admin/emailout",
						"?location=admin/emailout");
				} else {
					e.stopPropagation();
					e.preventDefault();
				}
			});
		}*/ else if ($(obj).data('name') == "guess_contest") {
			$(obj).submit(function(e) {
				if (confirm("Are you sure you want to perform this action?")) {
					regular_submit(
						e,obj,
						"index.php?location=ajax_admin/guess_contest",
						"?location=admin/game");
				} else {
					e.stopPropagation();
					e.preventDefault();
				}
			});
		} else if ($(obj).data('name') == "guess_contest_ss") {
			$(obj).submit(function(e) {
				if (confirm("Are you sure you want to perform this action?")) {
					regular_submit(
						e,obj,
						"index.php?location=ajax_admin/sitestrings",
						"?location=admin/game");
				} else {
					e.stopPropagation();
					e.preventDefault();
				}
			});
		} else if ($(obj).data('name') == "emailout") {
			$(obj).submit(function(e) {
				var url = "index.php?location=ajax_mailout";
				var form_data = new FormData();
				{
					$(obj).find('.normal-input').each(function (j, inputObj) {
						var name = $(inputObj).attr('name');
						var valu = $(inputObj).val();
						form_data.append(name,valu);
					});
					$(obj).find('.file-input').each(function (j, inputObj) {
						var name = $(inputObj).attr('name');
						var valu = $(inputObj)[0].files[0];
						form_data.append(name,valu);
					});
				}
				var jqxhr = $.ajax({
					type: "POST",
					url: url,
					data: form_data, // serializes the form's elements.
					processData: false,
					contentType: false,
					dataType: "json"
				});
				jqxhr.done(function (data) {
					if (data.status == "good") {
						alert(data.message);
					} else {
						alert("The following error occured:\n\n"+data.message);
					}
				});
				jqxhr.fail(function () {
					alert("An internal error occured!");
				});
				e.stopPropagation();
			    e.preventDefault();
			});
		} else if ($(obj).data('name') == "ebooks_post") {
			$(obj).submit(function(e) {
				var url = "index.php?location=ajax_admin/ebooks_post";
				var form_data = new FormData();
				{
					$(obj).find('.normal-input').each(function (j, inputObj) {
						var name = $(inputObj).attr('name');
						var valu = $(inputObj).val();
						form_data.append(name,valu);
					});
					$(obj).find('.file-input').each(function (j, inputObj) {
						var name = $(inputObj).attr('name');
						var valu = $(inputObj)[0].files[0];
						form_data.append(name,valu);
					});
				}
				var jqxhr = $.ajax({
					type: "POST",
					url: url,
					data: form_data, // serializes the form's elements.
					processData: false,
					contentType: false,
					dataType: "json"
				});
				jqxhr.done(function (data) {
					if (data.status == "good") {
						// Display success message sent by server
						alert(data.message);
						// Reset the form
						$(obj)[0].reset();
					} else {
						alert("The following error occured:\n\n"+data.message);
					}
				});
				jqxhr.fail(function () {
					alert("An internal error occured!");
				});
				e.stopPropagation();
			    e.preventDefault();
			});
		} else if ($(obj).data('name') == "tshirts_post") {
			$(obj).submit(function(e) {
				var url = "index.php?location=ajax_admin/tshirts_post";
				var form_data = new FormData();
				{
					$(obj).find('.normal-input').each(function (j, inputObj) {
						var name = $(inputObj).attr('name');
						var valu = $(inputObj).val();
						form_data.append(name,valu);
					});
					$(obj).find('.file-input').each(function (j, inputObj) {
						var name = $(inputObj).attr('name');
						var valu = $(inputObj)[0].files[0];
						form_data.append(name,valu);
					});
				}
				var jqxhr = $.ajax({
					type: "POST",
					url: url,
					data: form_data, // serializes the form's elements.
					processData: false,
					contentType: false,
					dataType: "json"
				});
				jqxhr.done(function (data) {
					if (data.status == "good") {
						// Display success message sent by server
						alert(data.message);
						// Reset the form
						$(obj)[0].reset();
					} else {
						alert("The following error occured:\n\n"+data.message);
					}
				});
				jqxhr.fail(function () {
					alert("An internal error occured!");
				});
				e.stopPropagation();
			    e.preventDefault();
			});
		}
	});
	$('input[type=submit]').each(function (i, obj) {
		$(obj).click(function () {
			$(obj).closest('form').find('input[name=submitted]').val($(obj).attr('name'));
		});
	});
}

function activate_tabs(selector) {
	$('.jq-tab').each(function (i, link) {
		var pageName = $(link).data('page');
		$(link).click(function(e) {
			$(selector).find('.jq-tab').each(function (k, tab) {
				$(tab).removeClass('active');
			});
			$(link).addClass('active');
			// Activate the correct page
			$(selector).find('.jq-page').each(function (j, page) {
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
function set_active_tab(selector, pageName) {
	$(selector).find('.jq-tab').each(function (k, tab) {
		if ($(tab).data('page') == pageName) {
			$(tab).addClass('active');
		} else {
			$(tab).removeClass('active');
		}
	});
	$(selector).find('.jq-page').each(function (j, page) {
		if ($(page).data('name') == pageName) {
			$(page).addClass('visible');
		} else {
			$(page).removeClass('visible');
		}
	});
}

function change_main_view(page) {
	$('.loadable-page-part').each(function (j, pagepart) {
		$(pagepart).addClass('dispnone');
	});
	$("#LPP-"+page).removeClass('dispnone');
	/*
	if (page == "adminbox") {
		$('.loadable-page-part').each(function (j, pagepart) {
			$(pagepart).addClass('dispnone');
		});
		$("#LPP-adminbox").removeClass('dispnone');
	}
	else if (page == "booksmod") {
		$('.loadable-page-part').each(function (j, pagepart) {
			$(pagepart).addClass('dispnone');
		});
		$("#LPP-booksmod").removeClass('dispnone');
	}
	else if (page == "vidsmod") {
		$('.loadable-page-part').each(function (j, pagepart) {
			$(pagepart).addClass('dispnone');
		});
		$("#LPP-vidsmod").removeClass('dispnone');
	}
	else if (page == "commentsmod") {
		$('.loadable-page-part').each(function (j, pagepart) {
			$(pagepart).addClass('dispnone');
		});
		$("#LPP-commentsmod").removeClass('dispnone');
	}
	*/
}
function activate_sidelinks() {
	$('.ajax-tab').each(function (i, obj) {
		$(this).click(function(e) {
			var page = $(this).data('page');
			change_main_view(page);
		});
	});
}

function append_book_deltwith(action, title, link, consl) {
	var style = (action == "approve") ? "green" : "red";
	var text = "Unknown Action...";
	if (action == "approve") text = "Approved!";
	if (action == "decline") text = "Declined!";
	if (action == "video-approve") text = "Approved!";
	if (action == "video-decline") text = "Declined!";
	var html = '<span class="'+style+'">'+text+'</span> ';
	html += title+' ';
	html += '<a href="'+link+'" target="_blank">';
	html += '<input type="button" class="btn btn-xs" value="Visit Page" />';
	html += '</a><br />';
	consl.append(html);
}

function activate_book_buttons() {
	$('.approval-buttons .ajax-button').each(function (i, obj) {
		$(obj).click(function () {
			var finished = function (message,status) {
				alert(status+": "+message);
			};
			var action = $(obj).data('name');
			var title = $(obj).closest('.approval-buttons').find("input[name='title']").first().val();
			var link = $(obj).closest('.approval-buttons').find("input[name='link']").first().val();
			var consl = $("#LPP-booksmod .ajax-console");

			var id = $(obj).closest('.approval-buttons').data('id');

			if (action == "approve" || action == "decline") {
				var r = false;
				if (action == "approve") r = confirm("Accept this book, \""+title+"\"?");
				if (action == "decline") {
					r = confirm("Reject this book, \""+title+"\"?");
					var reason = prompt("Reason for book rejection:","No comment was given.");
					$(obj).closest('.approval-buttons').find("input[name='reason']").first().val(reason);
				}


				if (r != true) return;
				var url = "index.php?location=ajax_approval/"+action+'/'+id; // the script where you handle the form input.
				var jqxhr = $.ajax({
					type: "POST",
					url: url,
					data: $(this).serialize(), // serializes the form's elements.
					dataType: "json"
				});
				jqxhr.done(function (data) {
					finished(data.message,data.status);
					$(obj).closest('.bookitem').addClass('dispnone');
					append_book_deltwith(action,title,link,consl);
				});
				jqxhr.fail(function (data) {
					finished("Server error encountered!","bad");
				});
			} else if (action == "comment-approve" || action == "comment-decline") {
				var r = false;
				if (action == "comment-approve") r = confirm("Accept this comment?");
				if (action == "comment-decline") {
					r = confirm("Reject this comment?");
					$(obj).closest('.approval-buttons').find("input[name='reason']").first().val(reason);
				}
				if (r != true) return;
				var url = "index.php?location=ajax_approval/"+action+'/'+id; // the script where you handle the form input.
				var jqxhr = $.ajax({
					type: "POST",
					url: url,
					data: $(this).serialize(), // serializes the form's elements.
					dataType: "json"
				});
				jqxhr.done(function (data) {
					finished(data.message,data.status);
					location.reload();
				});
				jqxhr.fail(function (data) {
					finished("Server error encountered!","bad");
				});
			} else {
				alert('This has not been implemented yet: '+name);
			}
		});
	});



	$('.video-approval-buttons .ajax-button').each(function (i, obj) {
		$(obj).click(function () {
			var finished = function (message,status) {
				alert(status+": "+message);
			};
			var action = $(obj).data('name');
			var title = $(obj).closest('.video-approval-buttons').find("input[name='title']").first().val();
			var link = $(obj).closest('.video-approval-buttons').find("input[name='link']").first().val();
			var consl = $("#LPP-vidsmod .ajax-console");

			var id = $(obj).closest('.video-approval-buttons').data('id');
			if (action == "video-approve" || action == "video-decline") {
				var r = false;
				if (action == "video-approve") r = confirm("Accept this video?");
				if (action == "video-decline") {
					r = confirm("Reject this video?");
					//var reason = prompt("Reason for rejection:","No comment was given.");
					//$(obj).closest('.video-approval-buttons').find("input[name='reason']").first().val(reason);
				}
				if (r != true) return;

				var url = "index.php?location=ajax_approval/"+action+'/'+id; // the script where you handle the form input.
				var jqxhr = $.ajax({
					type: "POST",
					url: url,
					data: $(this).serialize(), // serializes the form's elements.
					dataType: "json"
				});
				jqxhr.done(function (data) {
					finished(data.message,data.status);
					$(obj).closest('.bookitem').addClass('dispnone');
					append_book_deltwith(action,title,link,consl);
				});
				jqxhr.fail(function (data) {
					finished("Server error encountered!","bad");
				});
			} else {
				alert('This has not been implemented yet: '+name);
			}
		});
	});
}