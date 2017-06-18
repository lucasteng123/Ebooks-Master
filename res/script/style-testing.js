
function st_ticker_style(toapply) {
	var styles = toapply.split(" ");

	var obj_ticker = $(".ticker").first();
	var obj_ticker_messages = obj_ticker.find('.anim_ticker_messages').first();

	for (var i = 0; i < styles.length; i++) {
		var style = styles[i];

		if (style == "faded") {
			obj_ticker.find('.tickerfade').css('display', 'block');
		} else if (style == "boxed") {
			obj_ticker.find('.tickerfade').css('display', 'none');
			obj_ticker.find('.anim-ticker-messages').css('border-radius', '6px');
		}

		else if (style == "midnight") {
			obj_ticker.find('.anim-ticker-messages').css('color', '#CCC');
			obj_ticker.find('.anim-ticker-messages').css('background-color', '#10101A');
		} else if (style == "black") {
			obj_ticker.find('.anim-ticker-messages').css('color', '#CCC');
			obj_ticker.find('.anim-ticker-messages').css('background-color', '#000');
		} else if (style == "highlighted") {
			obj_ticker.find('.anim-ticker-messages').css('color', '#CCC');
			obj_ticker.find('.anim-ticker-messages').css('background-color', '#333');
		} else if (style == "inverted") {
			obj_ticker.find('.anim-ticker-messages').css('color', '#333');
			obj_ticker.find('.anim-ticker-messages').css('background-color', '#FFF');
		}
	}
}

function st_search_style(toapply) {
	var styles = toapply.split(" ");

	$('.search-input').removeClass('alt_style_1');
	$('.search-input').removeClass('alt_style_2');

	for (var i = 0; i < styles.length; i++) {
		var style = styles[i];

		if (style == "1") {
			$('.search-input').addClass('alt_style_1');
		} else if (style == "2") {
			$('.search-input').addClass('alt_style_2');
		}
	}
		
}