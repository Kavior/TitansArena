var images = new Array()
function preload() {
	for (i = 0; i < preload.arguments.length; i++) {
		images[i] = new Image()
		images[i].src = preload.arguments[i]
	}
}

var loader = $('<div class="loader"><div class="loading-container"><div class="loader-text">Loading...</div><div class="loader-loading"></div></div></div>');
var simpleLoader = $('<div class="simple-loading"></div>');

function showLoader(){
	$('html').append(loader);
}

$.fn.showSimpleLoader = function(){
	simpleLoader.css({opacity : 1});
	$(this).append(simpleLoader);
};

function hideLoader(duration){
	 $('.loader').animate({
		 opacity : 0
	 }, duration, function(){ $(this).remove(); });
}

$.fn.hideSimpleLoader = function(duration){
	 $('.simple-loading').animate({
		 opacity : 0
	 }, duration, function(){ $(this).remove(); });
}