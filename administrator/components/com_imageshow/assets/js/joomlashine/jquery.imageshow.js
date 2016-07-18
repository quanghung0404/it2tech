/**
 * @version    $Id: jquery.imageshow.js 17065 2012-10-16 04:06:37Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
(function($){ 
	$.JQJSNISImageShow = function(options){
		
		this.options  = $.extend({}, options);
		/**
		 * Make a Simple Slide, used at 2 pages: Showlist settings, Showcase settings
		 */
		this.simpleSlide = function (clickID, slideID, childTagName ,duration, cookie, cookieName){
			var self = this;
			//Set default value
			$("#" + slideID).addClass('expand');
			$("#" + clickID).click(function () {
			    $("#" + slideID).slideToggle(duration);
				if ($("#" + slideID).hasClass('expand'))
				{
					$("#" + slideID).removeClass('expand').addClass('collapse');
					$(childTagName, this).removeClass('icon-chevron-up').addClass('icon-chevron-down');
					self.cookie.set(cookieName, 'collapse');
				} 
				else
				{
					$("#" + slideID).removeClass('collapse').addClass('expand');
					$(childTagName, this).removeClass('icon-chevron-down').addClass('icon-chevron-up');
					self.cookie.set(cookieName, 'expand');
				} 
			});
		};
		
		/**
		 *  Get/Set Cookie
		 */
		this.cookie = {
			set : function(name, value){
				$.cookie(name, value);
			},
			get : function(name, type){
				switch(type){
					case 'int':
						return parseInt($.cookie(name));
					case 'float': 
						return parseFloat($.cookie(name));
					default:
						return $.cookie(name);
				}
			},
			exists : function(name){
				return $.cookie(name) == null ? false : true;
			}
		};
		
		this.showHintText = function() {
			var hintIcons	 = $('.hint-icon');
			var hintContents = $('.jsn-preview-hint-text-content');
			var hintCloses 	 = $('.jsn-preview-hint-close');
			
			hintIcons.each(function(i, e) 
			{
				$(e).unbind('click').click(function()
				{
					hintContents.each(function(z, el)
					{
						if (z == i) 
						{
							el.addClass('hint-active');
						}
						else
						{
							el.removeClass('hint-active');
						}
					});
				});
			});
			
			hintCloses.each(function(x, e)
			{
				$(e).unbind('click').click(function()
				{
					hintContents.each(function (z, el)
					{
						if (z == x) 
						{
							el.removeClass('hint-active');
						}
					});
				});
			});
		};
		
		this.comfirmBox = function(msg) {
			//var menuToolBar = $("#toolbar #jsn-is-menu-button #jsn-menu .jsn-submenu a");
			var menuToolBar	= $('#toolbar #jsn-menu-item-toolbar-menu .dropdown-menu a');
			menuToolBar.click(function(e){
			    var selfLink = this;
			    $("#confirmSaveForm").remove();
			    $(this).after(
				$("<div/>",{
				    "id":"confirmSaveForm"
				}).append(
				    $("<div/>",{
					"class":"jsn-bootstrap"
				    }).append(
					$("<div/>",{
					    "class":"jsn-is-confirmbox-message"
					}).append(msg)
					)
				    )
				);
			    $("#confirmSaveForm").dialog({
					height: 200,
					width: 400,
					title:"Confirm",
					draggable: false,
					resizable: false,
					autoOpen: true,
					modal: true,
					buttons: {
					    Yes: function() {
					    	$(this).dialog("close");
					    	var el = $('<input type="hidden" name="jsn-menu-link-redirect" id="jsn-menu-link-redirect" value="' + $(selfLink).attr("href") + '" />');
					    	el.appendTo($("form[name=adminForm]"));
							Joomla.submitbutton('save');
							return false;
					    },
					    No: function() {
					    	$(this).dialog("close");
					    	window.location.href=$(selfLink).attr("href");
					    	return false;
					    }
					}
			    })
			    return false;
			})			
		};
	};
})(jQuery);