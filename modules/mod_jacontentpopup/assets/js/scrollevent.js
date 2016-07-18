jQuery(window).bind('resize', function(){
	if(window.parent && window.parent.iframeResize){
		window.parent.iframeResize();
	}
});

document.onmousewheel = function(e, data){
	if(window.parent && window.parent.iframeWheel && window.parent.popupIscroll){
		window.parent.iframeWheel(window.event.wheelDelta/120);
	}
};