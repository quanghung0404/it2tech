/**
 * @version     $Id: showlist.js 16115 2012-09-18 05:21:34Z giangnd $
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
	var JSNISShowlist = function (params) {
		this.params = $.extend({
		}, params);

		this.viewModalButton 					= $('.jsn-is-view-modal');
		this.modalAuthorizationMessage			= $('.jsn-is-view-authorization-message-modal');
		this.modalProfileFormWindowModalButton 	= $('.jsn-is-form-modal');
		this.self			 = null;
		this.initialize();
		this.registerEvents();
	};
	
	JSNISShowlist.prototype = {
		initialize: function () {
			this.self = this;
		},

		registerEvents: function () {
			var self = this.self;
			this.viewModalButton.click(
				$.proxy(function (event) {
					event.preventDefault();
					JSNISShowlist.prototype.openModalWindow(this, self);
				}, this)
			);
			
			this.modalProfileFormWindowModalButton.click(	
				function (event) {					
					event.preventDefault();
					JSNISShowlist.prototype.openProfileFormWindowModal(this, self);
				}				
			);
			
			this.modalAuthorizationMessage.click(	
					function (event) {					
						event.preventDefault();
						JSNISShowlist.prototype.openAuthorizationMessageWindowModal(this, self);
					}				
			);			
		},
		
		openProfileFormWindowModal: function (obj, self) {
			var data 	= jQuery.parseJSON($(obj).attr('rel'));
			var link 	= $(obj).attr('href');
			var title 	= $(obj).attr('title');						
			this.modalFormWindowModal = new JSNModal({
				width: data.size.x,
				height: data.size.y,
				url: link,
				title: title,
				scrollable: true,
				buttons: [
					          {
					        	  text: self.params.language.JSN_IMAGESHOW_SAVE, 
					        	  click: function () {			        		  
										if (typeof gIframeOnSubmitFunc != 'undefined')
										{
											gIframeOnSubmitFunc($(this));
										}
										else
										{
											console.log('Iframe function not available');
										}					        		  
					        	  }
					          }, 
					          {
					        	  text: self.params.language.JSN_IMAGESHOW_CLOSE, 
					        	  click: $.proxy( function () { 
					        		  this.modalFormWindowModal.close(); 
					        	  }, this)
					          }
					    ]
			});
			this.modalFormWindowModal.iframe.css('overflow-x', 'hidden');
			this.modalFormWindowModal.container.css('overflow', 'hidden');
			this.modalFormWindowModal.show();			
		},
		
		checkEditedProfile: function (url, params) {		
			JSNISShowlist.prototype.toggleLoadingIcon('jsn-create-source', true);
			$.get(url).success(function(res) {
				var data = JSON.parse(res);
				if (data.success)
				{
					alert(data.msg);
					JSNISShowlist.prototype.toggleLoadingIcon('jsn-create-source', false);
					return false;
				}
				
				JSNISShowlist.prototype.validateProfile(params.validate_url);
			});	
		},
		
		submitProfileForm: function() {
			var data = JSNISShowlist.prototype.getAllInputs();	
			var link = 'index.php?option=' + data['option'] + '&controller=' + data['controller'] + '&task=' + data['task'];		
			$.post(link, data, function (data) {
			
			}).success(function(res){
				window.parent.location.reload(true);
			});	
		},
		
		validateProfile: function (url)
		{
			$.get(url).success(function(res) {
				var data = JSON.parse(res);
				if (!data.success)
				{
					alert(data.msg);
					JSNISShowlist.prototype.toggleLoadingIcon('jsn-create-source', false);
					return false;
				}			
				JSNISShowlist.prototype.submitProfileForm();
			});
		},
		
		toggleLoadingIcon: function(elementID, toggle) {
			var element = $('#' + elementID);
			if (toggle)
			{
				element.addClass('show-loading');
			}
			else
			{
				element.removeClass('show-loading');
			}	
		},
		
		getAllInputs: function()
		{
			var allInputs = $(":input");
			var options = {};
			
			allInputs.each(function(i, el)
			{
				if ($(el).attr('disabled') == undefined && $(el).attr('disabled') != 'disabled')
				{
					var name = $(el).attr('name');
					if ($(el).attr('type') == 'radio')
					{
						if ($(el).attr("checked") != undefined && $(el).attr("checked") == 'checked') {
							var value = $(el).val();
						}
					}
					else
					{
						var value = $(el).val();
					}
					if (value != undefined)
					{
						options[name] = value;
					}	
				}
			});	
			return options;
		},	

		openModalWindow: function (obj, self) {
			gIframeFunc = undefined;
			buttons = new Array();
			data = jQuery.parseJSON($(obj).attr('rel'));
			link = $(obj).attr('href');
			title = $(obj).attr('title');
			buttons = data.buttons;
						
			function getButtons(modal) 
			{
				if (data.buttons.ok && !data.buttons.close)
				{
					buttons	=	[{id : 'btn-save-showlist', text: modal.params.language.JSN_IMAGESHOW_SAVE, click: function () { 
									if(typeof gIframeFunc != 'undefined')
									{
										gIframeFunc();
									} 
								}}];
				}
				else if (data.buttons.close && !data.buttons.ok)
				{
					buttons =[{text: modal.params.language.JSN_IMAGESHOW_CLOSE, click: function (){ modal.modalWindow.close(); }}];
				}
				else
				{
					buttons =[{id : 'btn-save-showlist', text: modal.params.language.JSN_IMAGESHOW_SAVE, click: function (){
						if(typeof gIframeFunc != 'undefined')
						{
							gIframeFunc();
						}  
					}}, {text: modal.params.language.JSN_IMAGESHOW_CLOSE, click: function (){ modal.modalWindow.close(); }}];
				}	
				return buttons;
			}
			
			this.modalWindow = new JSNModal({
				width: data.size.x,
				height: data.size.y,
				url: link,
				title: title,
				scrollable: true,
				buttons: getButtons(this)
			});
			this.modalWindow.iframe.css('overflow-x', 'hidden');	
			this.modalFormWindowModal.container.css('overflow', 'hidden');
			this.modalWindow.show();
		},
		
		openAuthorizationMessageWindowModal: function (obj, self) {
			var data 		= jQuery.parseJSON($(obj).attr('rel'));
			var title 		= $(obj).attr('title');		
			var link 		= baseUrl+'administrator/'+$(obj).attr('href');
			var AuthorizationMessageWindowModal = new $.JSNISUIWindow(link,{
				width: data.size.x,
				height: data.size.y,
				title: title,
				scrollContent: true,
				buttons:
				[
				{
					text: self.params.language.JSN_IMAGESHOW_CLOSE,
					click: function (){
						$(this).dialog('close');
					}
				},
				]
			});			
			
		}		
	};

	return JSNISShowlist;
});