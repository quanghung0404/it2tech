/**
 * @version    $Id: jsn_is_accordions.js 17202 2012-10-18 07:38:25Z haonv $
 * @package    JSN.ImageShow
 * @subpackage JSN.ThemeClassic
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
(function ($){
	$.fn.togglepanels = function(cookieName){
	  return this.each(function(){
	    $(this).addClass("ui-accordion ui-accordion-icons ui-widget ui-helper-reset")
	  .find("h3")
	    .addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-top ui-corner-bottom")
	    .hover(function() { $(this).toggleClass("ui-state-hover"); })
	    .prepend('<span class="ui-icon ui-icon-triangle-1-e"></span>')
	    .click(function() {
	      $(this)
	        .toggleClass("ui-accordion-header-active ui-state-active ui-state-default ui-corner-bottom")
	        .find("> .ui-icon").toggleClass("ui-icon-triangle-1-e ui-icon-triangle-1-s").end()
	        .next().slideToggle();
	      	JSNISClassicTheme.saveAccordionStatusCookie($(this),cookieName);
	      return false;
	    })
	    .next()
	      .addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom")
	      .hide();
	  });
	};
})(jQuery);
