/**
 * @version     $Id: maintenance.js 16077 2012-09-17 02:30:25Z giangnd $
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

function($, JSNModal)
{ 
	var JSNISMaintenance = function (params) 
	{
		this.params = $.extend({
		}, params);
		
		this.modalFormWindowModalButton 		= $('.jsn-is-form-modal');
		this.modalProfileFormWindowModalButton 	= $('.jsn-is-profile-form-modal');
		this.modalViewWindowModalButton 		= $('.jsn-is-view-modal');
		this.deleteThemeButton					= $('.jsn-is-delete-theme');
		this.deleteSourceButton					= $('.jsn-is-delete-source');
		this.viewProfileButton					= $('.jsn-is-view-profile');
		this.viewImageSourceLink				= $('#linksources');
		this.viewDataLink						= $('#linkdata');
		this.viewThemeLink						= $('#linkthemes');
		this.viewLangLink						= $('#linklangs');
		this.viewMsgLink						= $('#linkmsgs');
		this.viewConfigLink						= $('#linkconfigs');
		this.deleteProfileButton				= $('.jsn-is-delete-profile');
		this.restoreFinishButton				= $('#jsn-restore-finish-button');
		this.restoreCancelButton				= $('#jsn-restore-button-cancel');
		this.self								= null;
		this.initialize();
		this.registerEvents();	
		
	};
	
	JSNISMaintenance.prototype = {
		initialize: function () {
			this.self = this;
			this.autoSelectMenu();
		},	
		
		registerEvents: function () {
			var self = this.self;
			var imageSourceLink = this.viewImageSourceLink;
			
			this.modalFormWindowModalButton.click(	
				function (event) {					
					event.preventDefault();
					JSNISMaintenance.prototype.openFormWindowModal(this, self);
				}				
			);
			
			this.modalProfileFormWindowModalButton.click(	
				function (event) {					
					event.preventDefault();
					JSNISMaintenance.prototype.openProfileFormWindowModal(this, self, imageSourceLink);
				}				
			);
		
			this.modalViewWindowModalButton.click(	
					function (event) {					
						event.preventDefault();
						JSNISMaintenance.prototype.openViewWindowModal(this, self);
					}				
				);			
			
			this.deleteThemeButton.click(
				function (event) {
					event.preventDefault();
					JSNISMaintenance.prototype.deleteTheme(this, self);	
				}
			);
			
			this.deleteSourceButton.click(
				function (event) {
					event.preventDefault();
					JSNISMaintenance.prototype.deleteSource(this, self);
				}				
			);
			
			this.viewProfileButton.click(					
				function (event) {
					event.preventDefault();
					JSNISMaintenance.prototype.viewProfile(this, self);
				}			
			);	
			
			this.deleteProfileButton.click(
				function (event) {
					event.preventDefault();
					JSNISMaintenance.prototype.deleteProfile(this, self);	
				}
			);		

			this.restoreFinishButton.click(
				function (event) {
					event.preventDefault();
					JSNISMaintenance.prototype.clearRestoreSession(this, self);	
				}
			);
			
			this.restoreCancelButton.click(
					function (event) {
						event.preventDefault();
						JSNISMaintenance.prototype.clearRestoreSession(this, self);	
					}
				);	
		},
		
		openFormWindowModal: function (obj, self) {
			var data 	= jQuery.parseJSON($(obj).attr('rel'));
			var link 	= $(obj).attr('href');
			var title 	= $(obj).attr('name');						
			this.modalFormWindowModal = new JSNModal({
				width: data.size.x,
				height: data.size.y,
				url: link,
				title: title,
				scrollable: true,
				buttons: [
					          {
					        	  text: self.params.language.JSN_IMAGESHOW_SAVE, 
					        	  click: $.proxy( function () {
					        		  this.modalFormWindowModal.options.loaded = function (modal) {
					        			  modal.container.dialog('close');
					        			  modal.container.dialog('destroy');
					        			  modal.container.unbind('load');	
					        		  };				        		  
					        		  this.modalFormWindowModal.submitForm('#frm_is_param');
					        	  }, this)
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
		
		openProfileFormWindowModal: function (obj, self, imageSourceLink) {
			var data 	= jQuery.parseJSON($(obj).attr('rel'));
			var link 	= $(obj).attr('href');
			var title 	= $(obj).attr('name');						
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
											gIframeOnSubmitFunc($(this), imageSourceLink);
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
		
		openViewWindowModal: function (obj, self) {
			var data 	= jQuery.parseJSON($(obj).attr('rel'));
			var link 	= $(obj).attr('href');
			var title 	= $(obj).attr('name');					
			this.modalFormWindowModal = new JSNModal({
				width: data.size.x,
				height: data.size.y,
				url: link,
				title: title,
				scrollable: true,
				buttons: [
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
		
		deleteTheme: function (obj, self) {
			var data 	= jQuery.parseJSON($(obj).attr('rel'));
			var cfm 	= confirm(self.params.language.JSN_IMAGESHOW_CONFIRM);
			if (cfm)
			{
				$.post('index.php?option=com_imageshow&controller=maintenance&type=themes&task=deleteTheme&' + data.token + '=1', {
					theme_id : data.theme_id,
					theme_name : data.theme_name
				}).success(function(res){
					$('#linkthemes').trigger('click');	
				});	
			}
		},
		
		deleteSource: function (obj, self) {
			var data 	= jQuery.parseJSON($(obj).attr('rel'));
			var cfm 	= confirm(self.params.language.JSN_IMAGESHOW_CONFIRM);
			if (cfm)
			{
				$.post('index.php?option=com_imageshow&controller=maintenance&type=themes&task=deleteImageSource&' + data.token + '=1', {
					source_id : data.source_id,
					1 : data.token
				}).success(function(res){
					$('#linksources').trigger('click');	
				});	
			}
		},
		
		viewProfile: function (obj, self) {
			var data = jQuery.parseJSON($(obj).attr('rel'));			
			$(obj).toggleClass('jsn-image-source-title-close');
			$('.' + data.container_id).toggleClass('jsn-image-source-profile-close');
		},
		
		checkEditedProfile: function (url, params, ciframe, imageSourceLink) {		
			JSNISMaintenance.prototype.toggleLoadingIcon('jsn-create-source', true);
			$.get(url).success(function(res) {
				var data = JSON.parse(res);
				if (data.success)
				{
					alert(data.msg);
					JSNISMaintenance.prototype.toggleLoadingIcon('jsn-create-source', false);
					return false;
				}
				
				JSNISMaintenance.prototype.validateProfile(params.validate_url, ciframe, imageSourceLink);
			});	
		},
		
		submitForm: function(ciframe, imageSourceLink) {
			if (typeof gIframeSubmitFunc != 'undefined')
			{
				gIframeSubmitFunc();
				ciframe.find('iframe').unbind('load').load(function () {
					imageSourceLink.click();
					ciframe.find('iframe').parent().dialog("close");
					ciframe.find('iframe').parent().dialog('destroy');
					ciframe.find('iframe').unbind('load');					
				});
			}
			else
			{
				console.log('Iframe function not available');
			}
		},
		
		validateProfile: function (url, ciframe, imageSourceLink)
		{
			$.get(url).success(function(res) {
				var data = JSON.parse(res);
				if (!data.success)
				{
					alert(data.msg);
					JSNISMaintenance.prototype.toggleLoadingIcon('jsn-create-source', false);
					return false;
				}	
		
				JSNISMaintenance.prototype.submitForm(ciframe, imageSourceLink);
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
		
		deleteProfile: function (obj, self) {
			var data 	= jQuery.parseJSON($(obj).attr('rel'));
			var cfm 	= confirm(self.params.language.MAINTENANCE_SOURCE_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_IMAGE_SOURCE_PROFILE);
			if (cfm)
			{
				$.post('index.php?option=com_imageshow&controller=maintenance&task=removeProfile&' + data.token + '=1', {
					external_source_profile_title : data.external_source_profile_title,
					external_source_id : data.external_source_id,
					image_source_name : data.image_source_name,
					totalshowlist : data.totalshowlist,
					external_source_profile_id : data.external_source_profile_id,
				}).success(function(res){
					$('#linksources').trigger('click');	
				});	
			}			
		},
		
		autoSelectMenu: function () {
			
			if (this.params.group == 'configs')
			{
				this.viewConfigLink.trigger('click');
			}
			else if(this.params.group == 'msgs')
			{
				this.viewMsgLink.trigger('click');
			}	
			else if (this.params.group == 'langs')
			{
				this.viewLangLink.trigger('click');
			}
			else if (this.params.group == 'data')
			{
				this.viewDataLink.trigger('click');	
			}	
			else if (this.params.group == 'sources')
			{
				this.viewImageSourceLink.trigger('click');
			}
			else if (this.params.group == 'themes')
			{
				this.viewThemeLink.trigger('click');
			}
			else
			{
				//Nothing
			}	
		},
		
		clearRestoreSession: function (obj, self) {	
			$.get('index.php?option=com_imageshow&controller=data&task=clearRestoreSession').success(function(res) {
				window.location.reload(true);
			});			
		}
	};
	
	return JSNISMaintenance;
});