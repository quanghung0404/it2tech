/**
 * @version     $Id: help.js 16587 2012-10-02 02:30:55Z giangnd $
 * @package     JSN.ImageShow
 * @subpackage  Html
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
define([
	'jquery', 
	'jsn/libs/modal',
], 

function ($, JSNModal) 
{
	var JSNISHelp = function (params) {
		this.params = $.extend({
		}, params);

		this.modalButton 				= $('.jsn-is-helper-modal');
		this.self			 			= null;
		this.initialize();
		this.registerEvents();
	};

	JSNISHelp.prototype = {
		initialize: function () {
			this.self = this;
		},

		registerEvents: function () {
			var self = this.self;
			this.modalButton.click(
				function (event) {
					event.preventDefault();
					JSNISHelp.prototype.openModalWindow(this, self);
				}
			);			
		},
		
		openModalWindow: function (obj, self) {
			var buttons = new Array();
			var link 	= 'index.php?option=com_imageshow&controller=help&tmpl=component';
			var title 	= "Help";
						
			function getButtons(modal) 
			{
				buttons =[{text: modal.params.language.JSN_IMAGESHOW_CLOSE, click: function (){ modal.modalWindow.close(); }}];
				return buttons;
			}
			
			this.modalWindow = new JSNModal({
				width: $(window).width()*0.95,
				height: $(window).height()*0.95,
				url: link,
				title: title,
				buttons: [ 
				          {
				        	  text: self.params.language.JSN_IMAGESHOW_CLOSE, 
				        	  click: $.proxy( function () { 
				        		  this.modalWindow.close(); 
				        	  }, this)
				          }
				    ]
			});
			this.modalWindow.show();
		}
	};

	return JSNISHelp;
});