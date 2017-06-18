$(window).scroll(function(e) {
	$(".scrolling").each(function(i, obj) {
		var obj = $(obj);
		if (obj.visible(true)) {
			obj.addClass(obj.data('scrollclass'));
		}
	});
	if ($(window).scrollTop() >= 176) {
		$('.scrolling_fixed').addClass('fixed');
	} else {
		$('.scrolling_fixed').removeClass('fixed');
	}
});


function get_sign(num) {
	if (num < 0) return -1;
	if (num > 0) return 1;
	if (num == 0) return 0;
}

function cap_magnitude(num,max) {
	if (num > max || num < -max) {
		return max * get_sign(num);
	} else {
		return num;
	}
}

function handle_photoloads() {

	$('.flip_in').each(function (i, obj) {
		var obj = $(obj);
		var img = $(this).find("img").first();
		var minDelay = obj.data('min-delay');
		img.one('load', function() {
			console.log("hi");
			setTimeout(function () {
				obj.css({
					transform: "rotateX(0deg)",
					MozTransform: "rotateX(0deg)",
					WebkitTransform: "rotateX(0deg)",
					msTransform: "rotateX(0deg)",
					opacity: "1"
				});
			},minDelay);
		});
		if (img.prop("complete")) {
			img.trigger("load");
		}
	});
	
}

function handle_coverflow() {
	$('.coverflow-item').each(function (i, obj) {
		$(obj).mouseover(function (e) {
			$(obj).data('active',"true");
			var angle = 45;
			$(obj).closest('.coverflow-canvas').find('.coverflow-item').each(function (j, subobj) {
				console.log("TEST");
				if ($(subobj).data('active') == "true") {
					angle = -45;
					setProps($(subobj),transformPropList,'rotateY(0deg)');
				} else {
					setProps($(subobj),transformPropList,'rotateY('+angle+'deg)');
				}
			});
			$(obj).data('active',"false");
		});
	});
}

function handle_coverflow_2() {
	$('.coverflow-item').each(function (i, obj) {
		$(obj).mouseenter(function (e) {
			$(obj).data('active',"true");

			var activeOrder = 0;
			var index = 0;
			$(obj).closest('.coverflow-canvas').find('.books-container .coverflow-item').each(function (j, subobj) {
				if ($(subobj).data('active') == "true") {
					activeOrder = index;
					return;
				}
				index++;
			});

			index = 0; // reset index to loop again
			$(obj).closest('.coverflow-canvas').find('.books-container .coverflow-item').each(function (j, subobj) {
				angleFactor = activeOrder - index;
				angle = 20*get_sign(angleFactor) + 5*angleFactor; // 10deg plus 5deg per index
				angle = cap_magnitude(angle,60);
				if ($(subobj).data('active') == "true") {
					angle = -45;
					setProps($(subobj),transformPropList,'rotateY(0deg) scale(1.1)');
				} else {
					setProps($(subobj),transformPropList,'rotateY('+angle+'deg) scale(1)');
				}
				index++;
			});
			$(obj).data('active',"false");

			// == Get the extra copies for reflections/glass/etc ==

			index = 0; // reset index to loop again
			$(obj).closest('.coverflow-canvas').find('.canvas-slide-under.next').find('.coverflow-item').each(function (j, subobj) {
				angleFactor = activeOrder - index;
				angle = 20*get_sign(angleFactor) + 5*angleFactor; // 10deg plus 5deg per index
				angle = cap_magnitude(angle,60);
				if (index == activeOrder) {
					angle = -45;
					setProps($(subobj),transformPropList,'rotateY(0deg) scale(1.1)');
				} else {
					setProps($(subobj),transformPropList,'rotateY('+angle+'deg) scale(1)');
				}
				index++;
			});

			index = 0; // reset index to loop again
			$(obj).closest('.coverflow-canvas').find('.canvas-slide-under.prev').find('.coverflow-item').each(function (j, subobj) {
				angleFactor = activeOrder - index;
				angle = 20*get_sign(angleFactor) + 5*angleFactor; // 10deg plus 5deg per index
				angle = cap_magnitude(angle,60);
				if (index == activeOrder) {
					angle = -45;
					setProps($(subobj),transformPropList,'rotateY(0deg) scale(1.1)');
				} else {
					setProps($(subobj),transformPropList,'rotateY('+angle+'deg) scale(1)');
				}
				index++;
			});
		});
	});
	$('.coverflow-canvas .books-container').mouseleave(function (e) {
		$('.coverflow-canvas').find('.coverflow-item').each(function (j, subobj) {
			setProps($(subobj),transformPropList,'rotateY(0deg) scale(1)');
		});
	});
}

function animate_fade_thingies() {
	$('.jq-pulse-col').each(function (i, obj) {
		setInterval(function () {
			$(obj).toggleClass('state-off');
		},800)
	});
}

function run_tickertape() {
	$('.anim-ticker').each(function (i, obj) {
		var obj = $(obj);

		var height = obj.height();
		var delay = obj.data('delay');
		var transition = obj.data('transition');

		var messageList = obj.find('.anim-ticker-messages').first()


		tDelay = 0;
		messageList.find('span').each(function (j, msg) {
			tDelay += delay;
		});

		theFunc = function() {
			seq = []

			messageList.find('span').each(function (j, msg) {
				var msg = $(msg);
				seq.push([function (args) {
					//console.log("push one");
	                var msg = args[0];
	                var messageList = args[1];

	                messageList.find('span').each(function (k,kobj) {
	                	var kobj = $(kobj);
	                	kobj.css('top','-'+height+'px');
	                });

	                setProps(msg,transitionPropList,'all 0s linear');
	                msg.css('top',height);
	                

	            },40,[msg,messageList]]);

	            seq.push([function (args) {
	            	//console.log("push two");
	                var msg = args[0];

	                setProps(msg,transitionPropList,'all '+transition+'ms linear');
	                msg.css('top','0');
	            },delay-40,[msg]]);
			});
			runSequenceWData(seq);
		};
		theFunc();
		setInterval(theFunc,tDelay);
		/*
		var obj = $(obj);
		var txtObj = obj.find('.anim-ticker-text').first();

		cWidth = obj.width(); // container
		tWidth = txtObj.width(); // text

		txtObj.css('left',cWidth+'px'); // POSITIVE

		seconds = tWidth / pxPerSec;
		mSeconds = seconds * 1000;

		console.log(mSeconds);
		console.log(delay);

		// REPEAT CODE ===
		offset = tWidth;
		console.log(mSeconds);
		setProps(txtObj,transitionPropList,'all '+seconds+'s linear');
		txtObj.css('left','-'+offset+'px'); // NEGATIVE
		setTimeout(function () {
			offset = cWidth;
			setProps(txtObj,transitionPropList,'all 0s linear');
			txtObj.css('left',offset+'px'); // POSITIVE
		},mSeconds);
		//             ===

		setInterval(function () {
			offset = tWidth;
			console.log(mSeconds);
			setProps(txtObj,transitionPropList,'all '+seconds+'s linear');
			txtObj.css('left','-'+offset+'px'); // NEGATIVE
			setTimeout(function () {
				offset = cWidth;
				setProps(txtObj,transitionPropList,'all 0s linear');
				txtObj.css('left',offset+'px'); // POSITIVE
			},mSeconds);
		},mSeconds+delay);
		*/
	});
		/*
		for (var i = 0; i < text.length; i++) {
			obj.append("<span>"+text.charAt(i)+"</span>");
		}
	

		// Call to functoin in e_animate.js
		setInterval(function () {
			// Sequence of animations to run
			seq = [];=
			obj.find('span').each(function () {
	    		var item = $(this);
	    		seq.push([function (args) {
	                var item = args[0];
	                setProps(item,transitionPropList,"all 10ms ease");
	            },10,[item]]);
	    		seq.push([function (args) {
	                var item = args[0];
	                item.css('color','#CFC');
	            },40,[item]]);

	    		seq.push([function (args) {
	                var item = args[0];
	                setProps(item,transitionPropList,"all 2s ease");
	            },10,[item]]);
	    		seq.push([function (args) {
	                var item = args[0];
	                item.css('color','#3C3');
	            },10,[item]]);
	    	});
			runSequenceWData(seq);
			console.log("hey!");
		}, 6000);
		*/
}
