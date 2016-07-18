/**
 * @version     $Id: launchpad.js 17049 2012-10-15 11:43:54Z giangnd $
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
	var JSNISLauchPad = function (params) {
		this.params = $.extend({
		}, params);

		this.modalButton 				= $('.jsn-is-modal');
		this.prentationMethodComboBox 	= $('#presentation_method');
		this.showlistComboBox 			= $('#showlist_id');
		this.showcaseComboBox 			= $('#showcase_id');
		this.goLink						= $('#jsn-go-link');
		this.goLinkModal				= $('#jsn-go-link-modal');	
		this.menuType					= $('#menutype');
		this.iconEditShowlist 			= $('#edit-showlist');
		this.iconEditShowcase 			= $('#edit-showcase');
		this.iconAddShowlist 			= $('#add-showlist');
		this.iconAddShowcase 			= $('#add-showcase');
		this.iconCannotAddShowcase		= $('#cannot-add-showcase');
		this.iconCannotAddShowlist		= $('#cannot-add-showlist');
		this.method			 			= $('#presentation_method');
		this.self			 			= null;
		this.initialize();
		this.registerEvents();
	};

	JSNISLauchPad.prototype = {
		initialize: function () {
			this.self = this;
		},

		registerEvents: function () {
			var self = this.self;
			this.modalButton.click(
				$.proxy(function (event) {
					event.preventDefault();
					this.openModalWindow();
				}, this)
			);
			
			this.prentationMethodComboBox.change(
				$.proxy(function (event) {
					this.selectPresentationMethod();
				}, this)			
			);
			
			this.showlistComboBox.change(
				$.proxy(function (event) {
					this.enableButton();
				}, this)			
			);	
			
			this.showcaseComboBox.change(
				$.proxy(function (event) {
					this.enableButton();
				}, this)			
			);	
			
			this.menuType.change(
				$.proxy(function (event) {
					this.createViaMenuItem();
				}, this)			
			);	
			
			this.iconEditShowlist.click(	
				function (event) {					
					event.preventDefault();
					JSNISLauchPad.prototype.openSLSCFormWindowModal(this, self);
				}				
			);
			this.iconAddShowlist.click(	
				function (event) {					
					event.preventDefault();
					JSNISLauchPad.prototype.openSLSCFormWindowModal(this, self);
				}				
			);			
			this.iconEditShowcase.click(	
				function (event) {					
					event.preventDefault();
					JSNISLauchPad.prototype.openSLSCFormWindowModal(this, self);
				}				
			);
			
			this.iconAddShowcase.click(	
				function (event) {					
					event.preventDefault();
					JSNISLauchPad.prototype.openSLSCFormWindowModal(this, self);
				}				
			);	
			
			this.iconCannotAddShowcase.click(	
				function (event) {					
					event.preventDefault();
					JSNISLauchPad.prototype.confirmBox(this, self);
				}				
			);
			this.iconCannotAddShowlist.click(	
				function (event) {					
					event.preventDefault();
					JSNISLauchPad.prototype.confirmBox(this, self);
				}				
			);				
		},
		
		/**
		 * the openSLSCFormWindowModal funtion is using a deprecated classe JSNISUIWindow, that will be removed next version.
		 * So this function have to be edited in the future 
		 */
		openSLSCFormWindowModal: function (obj, self) {	
			
			if ($(obj).attr('href') == 'javascript:void(0);' || $(obj).attr('href') == 'javascript:void(0)' || $(obj).attr('href') == '') return;
					
			var data 	= jQuery.parseJSON($(obj).attr('rel'));
			var wWidth  = $(window).width()*0.9;
			var wHeight = $(window).height()*0.8;			
			var link 	= self.params.pathRoot + 'administrator/' + $(obj).attr('href');
			var title 	= $(obj).attr('title');	
			var modalSLSCFormWindowModal = new $.JSNISUIWindow(link,{
					width: wWidth,
					height: wHeight,
					title: data.title,
					scrollContent: true,
					buttons:
					[{
						text:self.params.language.JSN_IMAGESHOW_SAVE,
						click: function (){
							if(typeof gIframeFunc != 'undefined')
							{
								gIframeFunc();
							}
							else
							{
								console.log('Iframe function not available')
							}
						}
					},
					{
						text: self.params.language.JSN_IMAGESHOW_CLOSE,
						click: function (){
							$(this).dialog('close');
						}
					},
					]
			});	
		},
		
		openModalWindow: function () {
			var buttons = new Array();
			var data 	= jQuery.parseJSON(this.modalButton.attr('rel'));
			var link 	= this.modalButton.attr('href');
			var title 	= this.modalButton.attr('title');
			buttons = data.buttons;
						
			function getButtons(modal) 
			{
				if (data.buttons.ok && !data.buttons.close)
				{
					buttons =[{text: modal.params.language.JSN_IMAGESHOW_OK, click: function () { modal.close(); }}];
				}
				else if (data.buttons.close && !data.buttons.ok)
				{
					buttons =[{text: modal.params.language.JSN_IMAGESHOW_CLOSE, click: function (){ modal.modalWindow.close(); }}];
				}
				else
				{
					buttons =[{text: modal.params.language.JSN_IMAGESHOW_OK, click: function (){ modal.close(); }}, {text: modal.params.language.JSN_IMAGESHOW_CLOSE, click: function (){ modal.close(); }}];
				}	
				return buttons;
			}
			
			this.modalWindow = new JSNModal({
				width: data.size.x,
				height: data.size.y,
				url: link,
				title: title,
				buttons: getButtons(this)
			});
			this.modalWindow.show();
		},
		
		selectPresentationMethod: function() {
			
			var showlistID 		= parseInt($('option:selected', this.showlistComboBox).val());
			var showcaseID 		= parseInt($('option:selected', this.showcaseComboBox).val());
			var methodValue 	= $('option:selected', this.method).val();
			
			this.goLink.attr('href', 'javascript:void(0)')
					.addClass('disabled')
					.css('display', 'none');
			
			this.goLinkModal.attr({href: 'javascript:void(0)', title: this.params.language.CPANEL_GO})
					.addClass('disabled')
					.css('display', 'none');
			
			this.menuType.css('display', 'none');
			
			if(methodValue == "module")
			{				
				this.goLink.attr('href', 'index.php?option=com_imageshow&task=launchAdapter&type=module&' + 'showlist_id=' + showlistID + '&showcase_id=' + showcaseID)
					.removeClass('disabled')
					.css('display', 'inline-block');				
			}
			else if(methodValue == "plugin")
			{
				this.goLinkModal.attr({href: 'index.php?option=com_imageshow&task=plugin&tmpl=component&' + 'showlist_id=' + showlistID + '&showcase_id=' + showcaseID, 
										title: this.params.language.CPANEL_PLUGIN_SYNTAX_DETAILS})
					.removeClass('disabled')
					.css('display', 'inline-block');		
			}
			else if(methodValue == "menu")
			{
				this.menuType.css('display', 'inline-block');
				this.goLink.css('display', 'inline-block');
				this.menuType.val('');
			}
			else
			{
				this.goLink.css('display', 'inline-block');
			}
		},
		
		enableButton: function() {
			
			var showlistID 			= parseInt($('option:selected', this.showlistComboBox).val());
			var showcaseID 			= parseInt($('option:selected', this.showcaseComboBox).val());		
			
			
			this.method.val('');
			
			this.goLink.attr('href', 'javascript:void(0)')
				.addClass('disabled')
				.css('display', 'inline-block');

			this.goLinkModal.attr('href', 'javascript:void(0)')
				.addClass('disabled')
				.css('display', 'none');	
			
			this.menuType.css('display', 'none');
			
			if (showcaseID && showlistID)
			{
				this.method.addClass('active').attr('disabled', false);
			}
			else
			{
				this.method.removeClass('active').attr('disabled', true);
			}	
			
			if (showcaseID)
			{
				this.iconEditShowcase.removeClass("disabled")
								.attr({href: 'index.php?option=com_imageshow&controller=showcase&task=edit&cid[]=' + showcaseID + '&tmpl=component', 
										//target: '_blank',
										title: this.params.language.CPANEL_EDIT_SELECTED_SHOWCASE
									});
			}
			else
			{
				this.iconEditShowcase.addClass("disabled")
								.attr({href: 'javascript:void(0)', 
										//target: '', 
										title: this.params.language.CPANEL_YOU_MUST_SELECT_SOME_SHOWCASE_TO_EDIT
									});							
			}
			
			if (showlistID)
			{
				this.iconEditShowlist.removeClass("disabled")
								.attr({href: 'index.php?option=com_imageshow&controller=showlist&task=edit&cid[]=' + showlistID + '&tmpl=component',
										//target: '_blank', 
										title: this.params.language.CPANEL_EDIT_SELECTED_SHOWLIST
									});

			}
			else
			{
				this.iconEditShowlist.addClass("disabled")
								.attr({href: 'javascript:void(0)', 
										//target: '', 
										title: this.params.language.CPANEL_YOU_MUST_SELECT_SOME_SHOWLIST_TO_EDIT
									});
			}			
		},
		
		createViaMenuItem: function () {
			
			var showlistID 			= parseInt($('option:selected', this.showlistComboBox).val());
			var showcaseID 			= parseInt($('option:selected', this.showcaseComboBox).val());	
			var menuTypeValue		= $('option:selected', this.menuType).val();
			
			this.goLink.attr('href', 'javascript:void(0)')
			.addClass('disabled');

			if(menuTypeValue != "")
			{
				this.goLink.attr('href', 'index.php?option=com_imageshow&task=launchAdapter&type=menu&menutype=' + menuTypeValue + '&' + 'showlist_id=' + showlistID + '&showcase_id=' + showcaseID)
						.removeClass("disabled");
			}			
		},
		
		confirmBox: function (obj, self) {
			var data 	= JSON.parse($(obj).attr('rel'));
			var msg 	= data.content;
			var title 	= data.title;
			
			var cfm 	= $('<div id="jsn-is-cpanel-confirmbox-container" style="overflow:hidden;"/>').appendTo('body').html(msg);
	
			cfm.dialog({
				width      : 500,
				height     : 250,
				modal      : true,
				draggable  : false,
				resizable  : false,
				title		: title,
				buttons : 
						[
							{
								text: self.params.language.JSN_IMAGESHOW_CLOSE,
								click: function (){
									cfm.remove();
								}
							}
						]			
			});
			
			
		}
	};

	return JSNISLauchPad;
});