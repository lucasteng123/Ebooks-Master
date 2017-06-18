// Eric's Animation Script

// Assuming this function is free...
// Came from http://www.javascriptkit.com/javatutors/setcss3properties.shtml
function setProps(thingy,proparray,value){
    for (var i=0; i<proparray.length; i++){ //loop through possible properties
        thingy.css(proparray[i],value)
    }
}

function runSequence(funcList) {
	funcData = funcList.shift();
	func = funcData[0]
	time = funcData[1]
	func();
	if (funcList.length > 0) {
		setTimeout(function () {
			runSequence(funcList);
		}, time);
	}
}

function runSequenceWData(funcList) {
	funcData = funcList.shift();
	func = funcData[0]
	time = funcData[1]
	data = funcData[2]
	// console.log("Data: "+data);
	func(data);
	if (funcList.length > 0) {
		setTimeout(function () {
			runSequenceWData(funcList);
		}, time);
	}
}

// Predefineed thingies
transitionPropList = ['transition','-webkit-transition','-moz-transition']
transformPropList = ['transform','-webkit-transform','-moz-transform']
borderPropList = ['border-radius','-webkit-border-radius']