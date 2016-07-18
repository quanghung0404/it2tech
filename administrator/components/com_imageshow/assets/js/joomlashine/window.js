/**
* 
* Window plugin support open multi-window with variables 
*
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html

Descriptions:
	1. Required files/libs:
		- jQuery lib
		- jQuery UI
**/

(function($){
	var _internalCheck,
		_uiwIndex  = 0,
		_uiWindowInstances = [];
	$.extend({
		/**
		* 
		* Add popup window. An customize of jQuery UI dialog.
		*
		* @param: (string) (link) is url page you want to open in popup
		* @param: (string) (array) is setting for that window and jQueryUI dialog
		* @return: POPUP page and jQuery object		 
		*/
		JSNISUIWindow: function(link, options){
			var ops = $.extend({
				width        : 800,
				height       : 500,				
				autoOpen     : true,
				closeOnEscape: false,
				draggable    : true,
				hide         : false,
				modal        : true,
				position     : 'center',
				frameID		 : '',				
				resizable    : false,
				show         : true,
				buttons      : {},
				open         : {},
				dialogClass  : 'jsn-master',
				close        : undefined,
				search       : undefined,
				switchMode   : true,
				scrollContent: true,
				tmplComponent: /tmpl=component/.test( link ),
				title		 : ''
			}, options);

			_uiwIndex++;
			var $this = this;
			var $id = 'uiwindow'+'-'+_uiwIndex;
			if (_uiWindowInstances[$id] !== undefined) return _uiWindowInstances[$id];
			if (ops.modal){
				$('.jsnui-window-iframe').parent().remove();
			}
			var $iframeId    = 'iframe-'+$id;
			if (ops.frameID != '')
			{ 
				$iframeId = ops.frameID;
			}			
			var imgLoadingId = 'img-loading-'+$id;
			var $w  =  $('<div/>', {
				'id'   : $id,
				'style': 'overflow:hidden;'
			}).appendTo('body');
			
			var $el = $('<iframe />', {
				'id'          : $iframeId,
				'frameborder' : '0',
				'name'        : 'jsnisuiwindow',
				'class'       : 'jsnui-window-iframe',
				'scrolling'	  : (options.scrollContent == false) ? 'no' : 'yes'
			})
			.appendTo($w)
			.css({
				'width'       : ops.width,
				'height'      : ops.height-65,
				'overflow-y'  : (options.scrollContent == false) ? 'hidden' : 'scroll',
				'overflow-x'  : 'hidden',
				'position'	  : 'relative'
			});
			
			//if ((/^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\[\]\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\[\]\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+\[\],;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+\[\],;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+\[\],;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+\[\],;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+\[\],;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+\[\],;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+\[\],;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+\[\],;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+\[\],;=]|:|@)|\/|\?)*)?$/i.test(link))){
				$el.attr('src', link);
			//}
			
			/**
			 * Call dialog of jQuery UI
			 */
			if (ops.modal){
				_uiWindowInstances = new Array();
				$('.ui-window-site-manager').remove();
			}
			
			if ( ops.close == undefined ){
				ops.close = function(event, e){
					$w.remove();
				};
			}
			
			$w.dialog({
				width        : ops.width,
				height       : ops.height,
				autoOpen     : ops.autoOpen,
				closeOnEscape: ops.closeOnEscape,
				draggable    : false,
				hide         : ops.hide,
				modal        : ops.modal,
				position     : ops.position,
				resizable    : ops.resizable,
				//show         : ops.show,
				buttons      : ops.buttons,
				open         : ops.open,
				dialogClass  : ops.dialogClass,
				resizeStop   : function(event, e){
					$w.trigger("jsn.window.resize");
					$el.animate({
						width : $w.parent().width()-30,
						height: $w.parent().height()-70
					}, 500 );
				},
				close        : ops.close,
				title		 : ops.title
			});
			
			//Add image loading
			if ($('.ui-widget-overlay').find('.img-box-loading').length){
				$('.ui-widget-overlay').find('.img-box-loading').remove();
			}
			
			var imgBoxLoading = $('<div />', {
				'class' : 'loading',
				'id' : 'loading-wrapper'
			})
			.appendTo($('#'+$id))
			.css({
				'top'     : $el.height()/2-12+'px',
				'left'    : $el.width()/2-12+'px',
				'z-index' : '99999'
			});

			$('<img />', {
				'id'    : imgLoadingId,
				'class' : 'imgLoading',
				'src'   : baseUrl+'administrator/components/com_imageshow/assets/images/icons-24/icon-24-loading-circle.gif'
			})
			.appendTo(imgBoxLoading)
			.css({
				'left' : '12px',
				'top'  : '12px'
			});			

			if ($w.parent()[0].tagName != 'body'){
				$w.parent().hide();
			}

			if ($.browser.msie && $.browser.version < 9){
				var subHeight = 60;
			}else{
				var subHeight = 59;
			}

			
			if ($.checkResponse !== undefined) {
				$.checkResponse($el.contents());
			}

			if ( ops.tmplComponent ){
				
				var form = $el.contents().find('form[name="adminForm"]');
				if ( !/tmpl=component/.test( form.attr('action') ) ){
					form.attr('action', form.attr('action') + '&tmpl=component');
				}
			}
			
			if ( $.browser.msie && $.browser.version < 8 ){
				$el.css({
					'width' : ops.width + 18,
					'height': ops.height
				});
			}
			
			$w.css({
				'height':  ops.height - subHeight
			})
			.parent()
			.show();
			$el.trigger("uiwindow.iframeloaded");
			$el.load(function(){
				imgBoxLoading.hide();
			});
			
			/**
			 * 
			 * Resize window
			 *
			 * @param: (Array) (options) is array store options new size
			 */
			this.resize = function(options)
			{
				var _newSize = $.extend({
					width    : ops.width,
					height   : ops.height,
					complete : function(){
						
					}
				}, options);
				ops.width  = _newSize.width;
				ops.height = _newSize.height;

				$w.trigger("jsn.window.resize");
				$el.css('visibility', 'hidden');
				$w.parent().animate({
					width  : _newSize.width,
					height : _newSize.height,
					left   : $(window).width()/2 - _newSize.width/2,
					top    : $(window).height()/2 - _newSize.height/2
				}, 500, function(){
					$w.dialog("option", _newSize);
					$w.css({
						'width'  : $w.parent().width(),
						'height' : $w.parent().height()-65
					});
					$el.css({
						'width'  : $w.parent().width(),
						'height' : $w.parent().height()-65
					});
					$el.css('visibility', 'visible');
					_newSize.complete();
				});
			};
			
			/**
			 * 
			 * Change SRC of iframe content
			 *
			 * @param: (string) (src) is link of page need to show
			 */
			this.changeIframeSrc = function(newSrc)
			{
				//if ((/^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(newSrc))){
					var loading = $('<div />', {
						'class' : 'ui-widget-overlay-changemode'
					});
					$('<img />', {
						'id'  : 'img-loading',
						'src' : baseUrl+'administrator/components/com_imageshow/assets/images/icons-32/ajax-loader-big.gif'
					})
					.appendTo(loading)
					.css({
						'position': 'relative',
						'top'     : $w.height()/2-12+'px',
						'left'    : $w.width()/2-12+'px'
					});
					$w.resize(function(){
						loading.css({
							'width' : 0,
							'height': 0
						}).css({
							'width' : $w.width(),
							'height': $w.height()
						});
						loading.find('img#img-loading').css
						(
							{
								'position': 'relative',
								'top'     : $(this).height()/2-12+'px',
								'left'    : $(this).width()/2-12+'px'
						    }
						);
					});
					$w.append(loading);
					$el.attr('src', newSrc);
					$el.css('visibility', 'hidden').unbind("uiwindow.iframeloaded").bind("uiwindow.iframeloaded", function(){
						$el.css('visibility', 'visible');
						$w.find('div.ui-widget-overlay-changemode').remove();
					});
				//}
			};
						
			/**
			 * 
			 * Get Iframe of content window
			 *
			 * @return: jQuery Element
			 */
			this.getDialogBox = function(){
				return $w;
			};
			/**
			 * 
			 * Get Iframe of content window
			 *
			 * @return: jQuery Element
			 */
			this.getIframe = function(){
				return $el;
			};
			/**
			* 
			* Show function
			*
			* @return: None/show dialog
			*/
			this.show = function(){
				$w.dialog("open");
			};
			
			/**
			* 
			* Close
			*
			* @return: None/Close dialog 
			*/
			this.close = function(){
				$w.dialog("close");
			};
			
			/**
			* 
			* Option (overwrite)
			*
			* @param: (jQuery object) (option)
			* @return: None
			*/
			this.option = function(option){
				$w.dialog("option", option);
			};

			/**
			* 
			* Submit form
			*
			* @param: (string) (task) is joomla task name submit
			* @return: Submit page
			*/
			this.submitForm = function(task, submitType, __stateReadyCallback){
				submitType = ( submitType == undefined ) ? 'Save' : submitType;
				var completed_require_fields = true;
				if ($el.contents().find('form[name="adminForm"]').length == 0) return false;
				$el.contents().find('input[class~="required"]').each(function(){
					if ($(this).val() == ''){
						$(this).removeClass('invalid').addClass('invalid');
						$(this).attr('aria-invalid', 'true');
						completed_require_fields = false;
						if ($(this).prev().hasClass('required')){
							$(this).prev().removeClass('invalid').addClass('invalid');
							$(this).prev().attr('aria-invalid', 'true');
						}
					}
				});

				if (completed_require_fields){
					if ( submitType.trim().toLowerCase() == 'save & close' ){
						$w.parent().fadeOut(300, function(){
							$el.load(function(){
								$w.dialog("close");
							});
						});
					}
					$el[0].onload = function(){
						if ($.isFunction(__stateReadyCallback)){
							__stateReadyCallback();
						}
					};
					$el.contents().find('input[name="task"]').val(task);
					$el.contents().find('form[name="adminForm"]').submit();
					return true;
				}
				return false;
			};

			//Somthing jqueryui dialog hidden body page
			clearInterval(_internalCheck);
			_internalCheck = setInterval(function(){
				if ($('body').css('display') == 'none'){
					$('body').show();
				}
				
				if ( parseFloat($w.parent().css('top')) < 0){
					$w.parent().css({
						'top': $(window).height() / 2 - $w.parent().height()/2
					});
				}
			}, 100);
			
			//reassignment
			$this = this;
			_uiWindowInstances[$id] = this;
			return this;
		},
		
		/**
		* 
		* Add popup window for confirmation. An customize of jQuery UI dialog.
		*
		* @param: (string) (msg) is text of message
		* @param: (jQuery) (ops) is options
		* @return: jQuery element
		*/
		
		JSNISUIConfirm: function(title, msg, ops){
			_uiwIndex++;
			var _ops = $.extend({
				title      : title,
				width      : 300,
				height     : 150,
				modal      : true,
				resizable  : false,
				draggable  : false,
				dialogClass: 'ui-window-site-manager',
				buttons    : {}
			}, ops);
			
			var $id  =  'uiwindowid-'+_uiwIndex;
			var $cfm =  $('<div id="'+$id+'" style="overflow:hidden;"/>').appendTo('body').html(msg);
			$cfm.dialog({
				width      : _ops.width,
				height     : _ops.height,
				modal      : _ops.modal,
				draggable  : _ops.draggable,
				resizable  : _ops.resizable,
				dialogClass: _ops.dialogClass,
				open       : function(){
					$cfm.parent().css('z-index', $.topZIndex());
				},
				buttons    : _ops.buttons,
				close      : function(){
					$cfm.remove();
				}
			}).addClass('ui-window-content ui-window-confirmation-content');
			
			$cfm.next().append('<div class="ui-window-toolbar-title">'+_ops.title+'</div>');
			$cfm.next().addClass('ui-window-toolbar-button ui-window-confirmation');
			$cfm.parent().hide();
			$cfm.next().addClass('ui-window-toolbar-button').css('top', -(_ops.height - 45));
			//$cfm.css('top', '45px');
			//fixed IE7
			if ( $.browser.msie && $.browser.version < 8 ){
				$('button', $('.ui-dialog-buttonset')).unbind("mouseenter").bind("mouseenter", function(){
					$(this).addClass('ui-state-hover').addClass('ui-state-hover-ie7');
				}).unbind("mouseleave").bind("mouseleave", function(){
					$(this).removeClass('ui-state-hover').removeClass('ui-state-hover-ie7');
				});
			}
			$cfm.css('height',  _ops.height - 65).parent().show();
			/**
			 * Window scroll
			 */
			$(document).scroll(function(){
				$cfm.parent().css({
					'top': $(window).scrollTop() + $(window).height() / 2 - $cfm.parent().height()/2
				});
			});
			return $cfm;
		}
	});
	
	/**
	* 
	* Get Instances JSNUIWindow
	* 
	* @return: Close all windows
	*/
	$.closeAllJSNWindow = function(){
		try{
			$('.jsnui-window-iframe').parent().dialog("close");
		}catch(e){
			console.log(e.message);
		}
	};
})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);