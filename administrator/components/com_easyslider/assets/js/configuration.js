/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

(function($){ 
	$(document).ready(function() {
		 $("#jsn-configuration-page").addClass("jsn-easyslider-hide");
	});
	
    $(window).load(function(){
        $(".jsn-modal-overlay,.jsn-modal-indicator").remove();
        $("body").append($("<div/>", {
            "class":"jsn-modal-overlay",
            "style":"z-index: 1000; display: inline;"
        })).append($("<div/>", {
            "class":"jsn-modal-indicator",
            "style":"display:block"
        })).addClass("jsn-loading-page");

        $(".jsn-modal-overlay,.jsn-modal-indicator").delay(1200).queue(function () {
            $(this).remove();
            $("#jsn-configuration-page").removeClass("jsn-easyslider-hide");
        });
        
    });
})(jQuery);
