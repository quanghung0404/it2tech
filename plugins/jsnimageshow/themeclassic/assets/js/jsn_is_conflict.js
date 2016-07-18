if (typeof jQuery.noConflict() == 'function') {	
	var jsnThemeClassicjQuery = jQuery.noConflict();
	var $jppc = jQuery.noConflict();
}
try {
	if (JSNISjQueryBefore && JSNISjQueryBefore.fn.jquery) {
		jQuery = JSNISjQueryBefore;
	}
} catch (e) {
	console.log(e);
}