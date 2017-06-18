function BlogEditor(jqObject) {
	var self = this;
	self.editorButtons = jqObject.find('.editor-buttons');
	self.editorArea = jqObject.find('.editor-area');

	self.bind_controls();
}

BlogEditor.prototype.add_and_select = function(contents, pos, len) {
	var self = this;
	var editor = self.editorArea;

	// Get cursor position
	var curPos = self.get_cursor();

	// Insert text at cursor position
	var oldText = editor.val();
	var newText = [oldText.slice(0, curPos), contents, oldText.slice(curPos)].join('');
	editor.val(newText);

	// Modify selection
	var newCurPos = curPos + pos;
	self.set_select(newCurPos, newCurPos + len);
};

BlogEditor.prototype.get_cursor = function() {
	var self = this;
	// Thanks to http://tinyurl.com/bepvwr
	var ctrl = self.editorArea.get(0);
	var pos = 0;

	if (document.selection) {
		var sel = document.selection.createRange();
		sel.moveStart('character', -ctrl.value.length);
		pos = sel.text.length;
	} else if (ctrl.selectionStart || ctrl.selectionStart == 0) {
		pos = ctrl.selectionStart;
	} else {
		pos = editorArea.val().length;
	}

	return pos;
};

BlogEditor.prototype.set_select = function(pos, len) {
	var self = this;
	// Thanks to http://tinyurl.com/bepvwr
	var ctrl = self.editorArea.get(0);

	if (ctrl.setSelectionRange) {
		ctrl.setSelectionRange(pos,len);
	} else if (ctrl.createTextRange) {
		var range = ctrl.createTextRange();
		range.collapse(true);
		range.moveStart(pos);
		range.moveEnd(pos+len);
		range.select();
	}
};

BlogEditor.prototype.bind_controls = function() {
	var self = this;

	var buttons = self.editorButtons;
	var editor = self.editorArea;
	var ctrl = self.editorArea.get(0);

	buttons.find('.editor-button').each(function () {
		$(this).click(function () {
			switch ($(this).data('action')) {
				case "link":
					ctrl.focus();
					self.add_and_select("[url=http://____]click here[/url] ",12,4);
					break;
				case "image":
					ctrl.focus();
					self.add_and_select("[img][/img]",11,0);
					break;
			}
		});
	});
};
