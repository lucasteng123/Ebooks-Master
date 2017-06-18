function BlogComments(commentArea, getAction, postAction) {
	// Set instance '_this' to counter conflict with element 'this'
	var _this = this;

	// Set instance variables
	_this.commentArea = commentArea;
	_this.getAction = getAction;
	_this.postAction = postAction;

	// Extract comment list element
	_this.commentList = _this.commentArea.find('.comments-list').first();

	// Generate request data array
	_this.getData = {};
	_this.commentArea.find('> .param').each(function () {
		_this.getData[$(this).data('name')] = $(this).data('value');
	});

	// Extract form element
	var commentForm = _this.commentArea.find('form').first();

	// Set submit handler on form
	commentForm.submit(function (e) {
		var jqxhr = $.ajax({
			type: "POST",
			url: _this.postAction,
			data: commentForm.serialize(), // serializes the form's elements.
			dataType: "json"
		});
		jqxhr.done(function (data) {
			console.log(data.echo);
			if (data.status == "okay") {
				commentForm[0].reset();
				_this.refresh_comments();
				alert("Your comment will appear pending admin approval");
			} else if (data.status == "error") {
				alert(data.message);
			} else {
				alert("An unknown error occured when posting your comment");
			}
		});
		jqxhr.fail(function () {
			alert("An error occured communicating with the server. Did your internet connection go down?");
		});
		e.stopPropagation();
		e.preventDefault();
	});

	// Load comments
	_this.refresh_comments();
}
BlogComments.prototype.refresh_comments = function() {
	var _this = this;

	var jqxhr = $.ajax({
		type: "GET",
		url: _this.getAction,
		dataType: "json"
	});
	jqxhr.done(function (data) {
		if (data.status == "okay") {
			_this.set_comment_list(data.comments);
		} else if (data.status == "error") {
			alert(data.message);
		} else {
			alert("An unknown error occured while loading comments");
		}
	});
	jqxhr.fail(function () {
		alert("An error occured communicating with the server. Did your internet connection go down?");
	});
};
BlogComments.prototype.set_comment_list = function(data) {
	var _this = this;
	_this.clear_comments();
	data.forEach(function (val, ind, arry) {
		_this.add_comment(val);
	});
};
BlogComments.prototype.clear_comments = function() {
	this.commentList.html('');
};
BlogComments.prototype.add_comment = function(data) {
	var author   = data.author;
	var contents = data.contents;

	// Create comment elemment
	var commentElem = (function (author, contents) {
		var commentElem = $("<div></div>");
		commentElem.addClass('list-item');
		commentElem.append('<div class="writer">'+author+'</div class="author">');
		commentElem.append('<p class="comment">'+contents+'</p>');
		return commentElem;
	})(author, contents);

	// Append comment element to comments list
	this.commentList.append(commentElem);
};

// JQuery bindings
$.fn.instantiate_comment_area = function(getAction, postAction) {
	return new BlogComments(this, getAction, postAction);
};