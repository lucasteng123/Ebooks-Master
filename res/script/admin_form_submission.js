function set_admin_submits() {

	var finished = function (message, status, redir, formObj) {
		var messageObj = formObj.closest('form').find('.message').first();
		console.log('|'+status+'|'+message);
		if (status == "error"  || status == "bad") alert("An error occured!\n\n"+message);
		if (status == "good") {
			message += ' Reloading in <span class="timer" data-time="5">5</span>...';
		}
		if (status == "good_dnr") {
		}
		messageObj.html(message);
		messageObj.addClass('visible');
		messageObj.addClass(status);
		messageObj.css('display','block');
		if (status == "good") {
			setInterval(function () {
				redirect_with_timer(messageObj.find('.timer').first(), redir);
			}, 500)
		}
	};

	function regular_submit(e, obj, url, redir) {
		/*
		var finished = function (message, status, redir) {
			var messageObj = $(obj).closest('form').find('.message').first();
			console.log('|'+status+'|'+message);
			if (status == "error"  || status == "bad") alert("An error occured!\n\n"+message);
			if (status == "good") {
				message += ' Reloading in <span class="timer" data-time="5">5</span>...';
			}
			if (status == "good_dnr") {
			}
			messageObj.html(message);
			messageObj.addClass('visible');
			messageObj.addClass(status);
			messageObj.css('display','block');
			if (status == "good") {
				setInterval(function () {
					redirect_with_timer(messageObj.find('.timer').first(), redir);
				}, 500)
			}
		};
		*/
		var jqxhr = $.ajax({
			type: "POST",
			url: url,
			data: $(obj).serialize(), // serializes the form's elements.
			dataType: "json"
		});
		jqxhr.done(function (data) {
			finished(data.message, data.status, redir, $(obj));
		});
		jqxhr.fail(function (data) {
			finished("Server error encountered!","bad", $(obj));
		});
		e.stopPropagation();
	    e.preventDefault();
	}
	
	$('.ajax-form').each(function (i, obj) {
		if ($(obj).data('name') == "mod_book_feature") {
			$(obj).submit(function(e) {
				regular_submit(
					e,obj,
					"index.php?location=ajax_admin/mod_book_feature",
					window.location.href
				);
			});
		}
		if ($(obj).data('name') == "mod_book_link") {
			$(obj).submit(function(e) {
				regular_submit(
					e,obj,
					"index.php?location=ajax_admin/mod_book_link",
					window.location.href
				);
			});
		}
		else if ($(obj).data('name') == "mod_book_simple") {
			$(obj).submit(function(e) {
				var submitted = $(obj).find('[name=submitted]').first().val();
				//alert(submitted);
				if (confirm("Are you sure you want to do this? ["+submitted+"]")) {
					regular_submit(
						e,obj,
						"index.php?location=ajax_admin/mod_book_simple",
						window.location.href
					);
				}
				e.stopPropagation();
				e.preventDefault();
			});
		}
		else if ($(obj).data('name') == "mod_book_cover") {
			$(obj).submit(function(e) {
				var url = "index.php?location=ajax_coverupload";
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
					finished(data.message, data.status, window.location.href, $(obj));
				});
				jqxhr.fail(function () {
					finished("Server error encountered!","bad", $(obj));
				});
				e.stopPropagation();
			    e.preventDefault();
			});
		}
		else if ($(obj).data('name') == "ebooks_blog_post_remove") {
			$(obj).submit(function(e) {
				var submitted = $(obj).find('[name=submitted]').first().val();
				//alert(submitted);
				if (confirm("Are you sure you want to do this? ["+submitted+"]")) {
					regular_submit(
						e,obj,
						"index.php?location=ajax_admin/ebooks_blog_post_remove",
						window.location.href
					);
				}
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
