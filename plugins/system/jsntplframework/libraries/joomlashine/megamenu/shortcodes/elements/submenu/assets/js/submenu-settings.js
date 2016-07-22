/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2015 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

(function($) {
	$.JSNTplMMShortcodeSubmenuSettings = function(params) {
		// Initialize parameters
		this.params = $.extend({}, params);

		// Initialize functionality
		$(document).ready($.proxy(this.init(), this));
	};

	$.JSNTplMMShortcodeSubmenuSettings.prototype = {
		init : function() {
			
			this.container = $('#jsn-mm-element-submenu');
			$('#parent-param-parent_id', this.container).hide();			

			$('#param-parent_id', this.container).val($('#jsn_tpl_mm_selected_menu_id', $('#jsn-megamenu-builder')).val());
			$('#param-parent_id', this.container).trigger('change');
			
		},
	
	};
	
	$(document).ready(function() {
		new $.JSNTplMMShortcodeSubmenuSettings();
	});
	
})(jQuery);
