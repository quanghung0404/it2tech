/**
 * 
 * JSN Language
 *
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 
 Descriptions:
    1. Required files/libs:
       - jQuery lib
*/
var JSNISLang = {};

(function($){
	JSNISLang = {		
		languages:[],
		/**
		 * 
		 * Define language
		 *
		 * @param : (string) LANG_VAR is key to get lang text
		 * @param : (string) LANG_TEXT is text language
		 * @return: Add/update to array languages
		 */
		add: function( LANG_VAR, LANG_TEXT ){
			this.languages[LANG_VAR] = LANG_TEXT;
		},
		/**
		 * 
		 * Translate language
		 *
		 * @param : (string) LANG_VAR is language key
		 * @param : (array/object) changes is array/object store: {"find_me":"change_me"}  
		 * @return: (string) LANG_TEXT after translated
		 */
		translate: function( LANG_VAR, changes ){
			if ( this.languages[LANG_VAR] != undefined ){
				var translated = this.languages[LANG_VAR];
				for( k in changes ){
					translated = translated.replace(k, changes[k]);
				}
				return translated;
			}
			return '';
		}
	};
})(jQuery);


