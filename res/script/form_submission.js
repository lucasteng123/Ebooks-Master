// A function that decrements a timer and
// reloads the page when the timer hits 0
/* Parameters:
	[JQObject] timer_obj -> jquery object with data-time attribute
*/
function reload_with_timer(timer_obj) {
	newTime = timer_obj.data('time') - 1;
	timer_obj.data('time',newTime);
	timer_obj.html(newTime);
	if (newTime == 0) {
		location.reload();
	}
}

function redirect_with_timer(timer_obj,url) {
	newTime = timer_obj.data('time') - 1;
	timer_obj.data('time',newTime);
	timer_obj.html(newTime);
	if (newTime == 0) { 
		// alert(url);
		location.replace(url);
	}
}

function set_ajax_form_checks() {
	$('.validate-group').each(function (v, vgroup) {
		if (typeof $(vgroup).data('ms') !== 'undefined') {
			var ms = $(vgroup).data('ms');
			// Validate-group has a maximum size!
			var textarea = $(vgroup).find('textarea').first();
			textarea.on('propertychange change keyup paste input', function () {
				var length = $(this).val().length;
				var diff = Math.abs(ms - length);
				var helpBlock = $(vgroup).find('.help-block').first();
				if (length < ms) {
					helpBlock.html(length+'/1200; '+diff+' characters left!');
				}
				else if (length == ms) {
					helpBlock.html('1200/1200; that\'s like the maximum.');
				}
				else if (length == ms+1) {
					helpBlock.html('1 character too many.');
				}
				else if (length == ms+2) {
					helpBlock.html('2 characters too many.');
				}
				else if (length == ms+3) {
					helpBlock.html('3 characters <s>three many</s> too many.');
				}
				else if (length < ms+20) {
					helpBlock.html(diff+' characters too many.');
				}
				else if (length < ms+50) {
					helpBlock.html(diff+' characters over; I think you\'re pushing it.');
				}
				else if (length < ms+100) {
					helpBlock.html(diff+' characters over; I bet you\'re just holding a key down.');
				}
				else if (length < ms+200) {
					helpBlock.html(diff+' characters over D:');
				}
				else if (length < ms+1000) {
					helpBlock.html(diff+' characters over; I\'m gonna bet this is a copy&amp;paste.');
				}
				else if (length < ms+2000) {
					helpBlock.html(diff+' characters over; Holy mac-n-cheese louise!');
				}
				else if (length < ms+9000) {
					helpBlock.html('That\'s just way too much text');
				}

				else if (length < ms+10000) {
					helpBlock.html('IT\'S OVER NINE THOUSAND!!');
				}
				else {
					helpBlock.html(diff+' characters too many.');
				}
			});
		}
	});
}

function set_ajax_submits() {
	/*
	NOTE & TODO

	A LOT OF PARTS OF THIS FUNCTION ARE VERY REDUNDANT.
	EVENTUALLY THIS SHOULD BE SWITCHED OVER TO THE SAME
	STRUCTURE AS IN admin_form_submission.js AND SHOULD
	BE IMPROVED UPON. [Est. 120 minutes of work]
	*/
	$('.ajax-form select').each(function (i, obj) {
		$(obj).change(function () {
			var value = $(obj).find("option:selected").first().val();
			$(obj).parent().find('.ajax-selectdata').val(value);
		});
	});
	$('button[type=submit]').each(function (i, obj) {
		$(obj).click(function () {
			$(obj).closest('form').find('input[name=submitted]').val($(obj).attr('name'));
		});
	});
	$('.ajax-form').each(function (i, obj) {
		if ($(obj).data('name') == "login_form") {
			$(obj).submit(function(e) {
				var url = "index.php?location=ajax_login"; // the script where you handle the form input.
				var jqxhr = $.ajax({
					type: "POST",
					url: url,
					data: $(this).serialize(), // serializes the form's elements.
					dataType: "json"
				});
				jqxhr.done(function (data) {
					var errmsg = $(obj).find('.error-message').first();
					var frgmsg = $(obj).find('.forgot-message').first();
					var okmsg  = $(obj).find('.success-message').first();
					if (data.status == "okay") {
						errmsg.css('display',"none");
						frgmsg.css('display',"none");
						okmsg.css('display',"block");
						var timerObj = okmsg.find('.timer').first();
						if ("redirect" in data) {
							setInterval(function () {
								redirect_with_timer(timerObj, data.redirect);
							}, 1000);
						} else {
							setInterval(function () {
								reload_with_timer(timerObj);
							}, 1000);
						}
						$(obj).get(0).reset();
						// Call redirect function with timer
					} else if (data.status == "forgot") {
						$(obj).get(0).reset();
						var frgmsg = $(obj).find('.forgot-message').first();
						frgmsg.css('display',"block");
						frgmsg.html(data.message);
					} else if (data.status == "login_error") {
						var errmsg = $(obj).find('.error-message').first();
						errmsg.css('display',"block");
						errmsg.html(data.message);
					} else {
						var errmsg = $(obj).find('.error-message').first();
						errmsg.css('display',"block");
						errmsg.html("An internal error occured while logging in :/ try reloading the page.");
					}
				});
				jqxhr.fail(function () {
					alert("Error!");
					var errmsg = $(obj).find('.error-message').first();
					errmsg.css('display',"block");
					errmsg.html("Error: Could not reach server! :(");
				});
				e.stopPropagation();
			    e.preventDefault();
			});
		} else if ($(obj).data('name') == "logout_form") {
			$(obj).submit(function(e) {
				var url = "index.php?location=ajax_logout"; // the script where you handle the form input.
				var jqxhr = $.ajax({
					type: "GET",
					url: url
				});
				jqxhr.done(function (data) {
					var errmsg = $(obj).find('.error-message').first();
					var okmsg = $(obj).find('.success-message').first();
					errmsg.css('display',"none");
					okmsg.css('display',"block");
					var timerObj = okmsg.find('.timer').first();
					setInterval(function () {
						reload_with_timer(timerObj);
					}, 1000);
				});
				jqxhr.fail(function () {
					alert("Error!");
					var errmsg = $(obj).find('.error-message').first();
					errmsg.css('display',"block");
				});
				e.stopPropagation();
			    e.preventDefault();
			});
		} else if ($(obj).data('name') == "reg_form") {

			// THIS COULD POSSIBLY BECOME THE MOST
			// COMPLICATED JAVASCRIPT CODE ON HERE
			/* (I forget why I wrote the above comment
				... it's not that bad) */

			$(obj).submit(function(e) {
				var url = "index.php?location=ajax_register"; // the script where you handle the form input.
				var jqxhr = $.ajax({
					type: "POST",
					url: url,
					data: $(this).serialize(), // serializes the form's elements.
					dataType: "json"
				});
				jqxhr.done(function (data) {
					var errmsg = $(obj).find('.error-message').first();
					var okmsg = $(obj).find('.success-message').first();
					if (data.status == "okay") {
						errmsg.css('display',"none");
						okmsg.css('display',"block");
						var timerObj = okmsg.find('.timer').first();
						setInterval(function () {
							reload_with_timer(timerObj);
						}, 1000);
						$(obj).get(0).reset();
						// Call redirect function with timer
					} else if (data.status == "login_error"
						|| data.status == "register_error") {
						var errmsg = $(obj).find('.error-message').first();
						errmsg.css('display',"block");
						errmsg.html(data.message);
					} else {
						var errmsg = $(obj).find('.error-message').first();
						errmsg.css('display',"block");
						errmsg.html("Unhandled error: "+data.message);
					}
				});
				jqxhr.fail(function () {
					alert("Error!");
					var errmsg = $(obj).find('.error-message').first();
					errmsg.css('display',"block");
					errmsg.html("Error: Could not reach server! :(");
				});
				e.stopPropagation();
			    e.preventDefault();
			});

		} else if ($(obj).data('name') == "bookupload_form") {
			$(obj).submit(function(e) {

				// Don't submit if already waiting for response
				if (! $("#submit_load_indicator").hasClass('dispnone')) {
					e.stopPropagation();
					e.preventDefault();
					return false;
				}
				$('#submit_load_indicator').removeClass('dispnone');

				
				var url = "index.php?location=ajax_bookupload";
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
					$(obj).find('.multiplesel-input').each(function (j, inputObj) {
						var selectedOptions = [];
						$(this).find('option').each(function () {
							if (this.selected == true) {
								selectedOptions.push(this.value);
							}
						});
						// console.log(selectedOptions);
						form_data.append($(this).attr('name'), JSON.stringify(selectedOptions));
					});
					$(obj).find('.checkbox-input').each(function (j, inputObj) {
						var selectedOptions = [];
						alert("found a place for checkboxes");
						$(this).find('input[type=checkbox]').each(function () {
							alert('found a checkbox:', this.checked);
							if (this.checked == true) {
								selectedOptions.push(this.value);
							}
						});
						// console.log(selectedOptions);
						form_data.append($(this).attr('name'), JSON.stringify(selectedOptions));
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
					var errmsg = $(obj).find('.error-message').first();
					var okmsg = $(obj).find('.success-message').first();
					if (data.status == "okay") {
						errmsg.css('display',"none");
						okmsg.css('display',"block");
						// Call redirect function with timer
						var timerObj = okmsg.find('.timer').first();
						setInterval(function () {
							redirect_with_timer(timerObj,data.bookurl);
						}, 1000);
					} else if (data.status == "nosession") {
						// User needs to login or register before submitting the book.
						$("#login_to_submit").find('.title').first().html(data.booktitle);
						$("#login_to_submit").addClass('visible');
						show_particular_slideout("#slideout-box",'login');
					} else if (data.status == "form_error"
						|| data.status == "register_error") {
						var errmsg = $(obj).find('.error-message').first();
						errmsg.css('display',"block");
						errmsg.html(data.message);
					} else {
						var errmsg = $(obj).find('.error-message').first();
						errmsg.css('display',"block");
						errmsg.html("Unknown error ["+data.status+"]: "+data.message);
					}
				});
				jqxhr.fail(function () {
					alert("Error!");
					var errmsg = $(obj).find('.error-message').first();
					errmsg.css('display',"block");
					errmsg.html("Error: Could not reach server! :(");
				});
				jqxhr.always(function () {
					$('#submit_load_indicator').addClass('dispnone');
				});
				e.stopPropagation();
			    e.preventDefault();
			});
		} else if ($(obj).data('name') == "custom_book_video") {
			$(obj).submit(function(e) {
				var url = "index.php?location=book_mod/update_video"; // the script where you handle the form input.
				var jqxhr = $.ajax({
					type: "POST",
					url: url,
					data: $(this).serialize(), // serializes the form's elements.
					dataType: "json"
				});
				jqxhr.done(function (data) {
					if (data.status == "okay") {
						alert("The video was successfully changed!");
						location.reload();
					} else {
						alert("An error occured updating your video :( "+data.message);
					}
				});
				jqxhr.fail(function () {
					alert("An error occured updating your video :( please make sure it's a valid YouTube URL!");
				});
				e.stopPropagation();
			    e.preventDefault();
			});
		}
	});
}

function set_ajax_uploads() {
	$('.ajax-file-upload').each(function (i, obj) {
		$(obj).click(function () {
			var pseudoInp = $(obj).closest('.form-group').find('.pseudo-input');
			pseudoInp.val("Please wait for file explorer...");
			var button = $(obj).closest('.form-group').find('.file-upload-button').first();
			// Make browse button animate! (prevents user frustration)
			button.addClass('processing');
		});
		$(obj).on('change', function () {
			var pseudoInp = $(obj).closest('.form-group').find('.pseudo-input');
			/*
			I'm choosing the remove "C:\fakepath" approach over split & pop.
			If the browser chooses to send an unconventional path,
			then I think it is okay that the user sees this full path.
			*/
			var filename = $(obj).val().replace(/C:\\fakepath\\/i, '')
			pseudoInp.val(filename);
			console.log($(obj).val());
			// Make browse button stop animating! (also prevents user frustration, hehe)
			var button = $(obj).closest('.form-group').find('.file-upload-button').first();
			button.removeClass('processing');
		});
	})
}

function set_videovote_submits() {
	console.log('setting');
	$('.ajax-videovote').each(function (i, obj) {
		$(obj).off('.beste')
		.on('click.beste',function (e) {
			console.log($(obj).data('videoside'));
			var url = "index.php?location=ajax_submitvote"; // the script where you handle the form input.
			var jqxhr = $.ajax({
				type: "POST",
				url: url,
				data: "voted="+$(obj).data('videoside')+"&baseid="+$(obj).data('category'), // serializes the form's elements.
				dataType: "json"
			});
			jqxhr.done(function (data) {
				if (data.status == "okay") {
					$(obj).closest('.vote-buttons-container').find('.ajax-videovote').each(function (j, btn) {
						$(btn).html((j==0) ? data['video_a_votes'] : data['video_b_votes']);
						$(btn).addClass('nohover');
						$(btn).removeClass('ajax-videovote');
					});
					$(obj).html('Voted! '+$(obj).html());
					$(obj).addClass('green');
					// Call redirect function with timer
				} else if (data.status == "guest_vote_error") {
					alert("You can submit your vote once you login or register!\n\nRegistration is super-quick - we promise!");
				} else {
					alert("Sorry; an error occured while voting :/");
				}
			});
			jqxhr.fail(function () {
				alert("Error!");
				var errmsg = $(obj).find('.error-message').first();
				errmsg.css('display',"block");
				errmsg.html("Error: Could not reach server! :(");
			});
		});
	});
}