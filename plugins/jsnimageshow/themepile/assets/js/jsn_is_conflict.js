if (typeof jQuery.noConflict() == 'function') {	
	var jsnThemePilejQuery = jQuery.noConflict();
	jsnThemePilejQuery.curCSS = jsnThemePilejQuery.css;
}
try {
	if (JSNISjQueryBefore && JSNISjQueryBefore.fn.jquery) {
		jQuery = JSNISjQueryBefore;
	}
} catch (e) {
	console.log(e);
}