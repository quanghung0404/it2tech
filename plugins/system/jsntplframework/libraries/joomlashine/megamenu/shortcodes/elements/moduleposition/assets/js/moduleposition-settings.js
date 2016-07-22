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
	$.JSNTplMMShortcodeModulePositionSettings = function(params) {
		// Initialize parameters
		this.params = $.extend({}, params);

		// Initialize functionality
		$(document).ready($.proxy(this.init(), this));
	};

	$.JSNTplMMShortcodeModulePositionSettings.prototype = {
		init : function() {
			var self = this;
			//self.renderModulePostion();
			$('#jsn-tpl-mm-element-module-position', $('#jsn-mm-element-moduleposition')).val($('#param-position_id', $('#jsn-mm-element-moduleposition')).val());
            $.JSNTplMMShortcodeModulePositionSelectPosition = function (postion) {

            	$('#jsn-tpl-mm-element-module-position', $('#jsn-mm-element-moduleposition')).val(postion);
            	$('#param-position_id', $('#jsn-mm-element-moduleposition')).val(postion).trigger('change');
            	
            };
            
            $('#jsn-tpl-mm-element-module-position-iframe-content').on("load", function() {
                $(this).removeClass('hidden');
                $('#jsn-tpl-mm-element-module-position-wrapper').removeClass('jsn-megamenu-loading');
            });
		}
	};
	
	$(document).ready(function() {
		new $.JSNTplMMShortcodeModulePositionSettings();
	});
	
})(jQuery);
