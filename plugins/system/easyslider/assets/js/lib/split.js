/**
 * Created by phong on 8/28/15.
 */

void function ( exports, $, _ ) {

	exports.ES_SplitText = function ( selector, options ) {
		$(selector).each(function () {
			var el = this;
			var html = el.innerHTML.replace(/\n+/g, ' ').trim();
			var regex = new RegExp("(<\/?[^>]+>)?([^<]+)(<\/?[^>]+>)?", "gim");
			var split = html.trim().replace(regex, function ( match, before, text, after, position, string ) {
				var text = (text || '').trim();
				return (before || '') + ' ' + (text ? splitWords(text) : '') + ' ' + (after || '');
			});
			el.innerHTML = split;
		})
	}
	function splitWords ( string ) {
		return string.trim().split(/\s+/g).map(function ( word ) {
			return $('<span class="split-word" style="display: inline-block;">').html(splitChars(word)).get(0).outerHTML;
		}).join(' ')
	}

	function splitChars ( string ) {
		return string.trim().split('').map(function ( char ) {
			return $('<span class="split-char" style="display: inline-block;">').html(char).get(0).outerHTML;
		}).join('')
	}
}(this, jQuery, _);