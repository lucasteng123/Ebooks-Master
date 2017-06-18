function GuessContest() {
	var self = this;
	self.letterBoxes = [];
	self.parentObject = null;
}

GuessContest.prototype.bind_to_page = function(jqObject) {
	var self = this;

	self.parentObject = jqObject;

	self.guessBox = jqObject.find('input[name=guess_entry]').first();
	self.guessButton = jqObject.find('input[name=guess_button]').first();
	self.enterButton = jqObject.find('input[name=enter_button]').first();
	self.lettersHolder = jqObject.find('.letter-boxes-holder').first();

	self.lettersHolder.find('.letter-box').each(function () {
		self.letterBoxes.push($(this));
	});

	self.post_bind_initialization();
};
GuessContest.prototype.post_bind_initialization = function() {
	var self = this;


	self.guessBox.on('change input', function (e) {
		// Thanks to Max Shawabkeh, SO questions/2220196
		var guessText = self.guessBox.val();
		var limit = Math.max(
			guessText.length, self.letterBoxes.length
		);
		for (var i=0; i < limit; i++) {
			if (i < guessText.length) {
				var c = guessText.charAt(i);
				// if this is a space, then check if the user entered a space.
				// if the user did not enter a space, add one.
				/*if (self.letterBoxes[i].hasClass('space')) {
					if (c != ' ') i--;
				} else {*/
					self.letterBoxes[i].html(c.toUpperCase());
				//}
			} else {
				self.letterBoxes[i].html('&nbsp;');
			}
		}
	});

	self.guessButton.on('click', function () {
		self.parentObject.find("span[name=guess_entry_form]").first().addClass('dispnone');
		self.parentObject.find("span[name=info_entry_form]").first().removeClass('dispnone');
	});

	self.enterButton.on('click', function () {
		self.submit_guess();
	});
};

GuessContest.prototype.show_success_screen = function() {
	var self = this;
	self.parentObject.find("span[name=guess_entry_form]").first().addClass('dispnone');
	self.parentObject.find("span[name=info_entry_form]").first().addClass('dispnone');
	self.parentObject.find("span[name=success_message]").first().removeClass('dispnone');
};

GuessContest.prototype.submit_guess = function() {
	var self = this;

	data = {};
	data['guess'] = self.parentObject.find("input[name=guess_entry]").first().val();
	data['name']  = self.parentObject.find("input[name=name]").first().val();
	data['email'] = self.parentObject.find("input[name=email]").first().val();

	var url="?location=guess_contest";
	var jqxhr = $.ajax({
		type: "POST",
		url: url,
		data: data, // serializes the form's elements.
		dataType: "json"
	});
	jqxhr.done(function (data) {
		if (data.status == "okay") {
			self.show_success_screen();
		} else if (data.status == "error") {
			alert(data.message);
		} else {
			alert("An error occured during submission! :/ please try again later!");
		}
	});
	jqxhr.fail(function (data) {
		alert("An error occured during submission! :/ please try again later!");
	});
};