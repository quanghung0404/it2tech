/**
 * @version    $Id: jsn_is_classictheme.js 17228 2012-10-18 10:19:53Z haonv $
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
	JSNISClassicTheme = {
		themeStyleName : 'javascript',	
		previewModalWindow : null,
		Skin:function(){
			var javascriptSkin		= $('#theme-classic-javascript');
			var javascriptSkinInput	= $("#theme-classic-javascript :input");
			var flashSkin			= $('#theme-classic-flash');
			var flashSkinInput		= $('#theme-classic-flash :input');
			$('#theme_style_name').ddslick({
			    width: 120,
			    onSelected: function (data) {
			    	themeStyleName = data.selectedData.value;
			    	$('#theme_style_name_value').val(themeStyleName);
			    	if(themeStyleName == "flash"){
						javascriptSkin.hide();
						javascriptSkinInput.attr("disabled", true);
						flashSkinInput.attr("disabled", false);
						flashSkin.show();
			    	}else{
						flashSkin.hide();
						flashSkinInput.attr("disabled", true);
						javascriptSkinInput.attr("disabled", false);
						javascriptSkin.show();
			    	}
			    	
			    }
			});
			JSNISClassicTheme.enableCollapseAccordion();
			JSNISClassicTheme.enablePreviewModalWindow();
		},	
		enablePreviewModalWindow : function(){
			JSNISClassicTheme.previewModalWindow = $("#jsn-windowbox-content").dialog({
				resizable: false,
				width: 645,
				height: 645,
				title: 'PREVIEW',
		        autoOpen: false,
		        modal: true,		
				buttons:
				[{
					text:"Close",
					class: 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
					click: function (){
						document.getElementById('jsn-flash-preview-object').clear();
		        		$(this).dialog("close");
					}
				}]			
		    });
			$('#preview-showcase-link').click(function(){
				JSNISClassicTheme.ShowcasePreview();
			});
		},
		enableCollapseAccordion: function()
		{
			$('.jsn-accordion-control').each(function(){
				$(this).children('span').each(function(){
					if($(this).hasClass('collapse-all'))
					{
						
						$(this).click(function(){
							$(this).parent().siblings('h3').each(function(){
								if($(this).hasClass('ui-state-active') == true){
									$(this).trigger('click');
								}
							});
						});
					}
					if ($(this).hasClass('expand-all')){
						$(this).click(function(){
							$(this).parent().siblings('h3').each(function(){
								if($(this).hasClass('ui-state-active') == false){
									$(this).trigger('click');
								}
							});
						});	
					}
				});
			});
		},
		ShowcaseSwitchBrowsingMode:function(){
			var valueMode			= $('#thumbpanel_thumb_browsing_mode').val();
			var thumbpanelThumbRow	= $('#thumbpanel_thumb_row');
			if(valueMode == 'sliding')
			{
				thumbpanelThumbRow.val(1);
				thumbpanelThumbRow.attr('readonly', true);
				thumbpanelThumbRow.css('background', '#ECE9D8');
			}
			else
			{
				thumbpanelThumbRow.val(1);
				thumbpanelThumbRow.attr('readonly', false);
				thumbpanelThumbRow.css('background', '#fff');
			}
		},
		ShowcaseViewGraphic: function(url){
			var imgpanelBgValueFirst = $('#imgpanel_bg_value_first').val();
			if(imgpanelBgValueFirst == ''){
				return false;
			}
			$('#view-image-graphic').attr("href", url+imgpanelBgValueFirst);
		},

		ColorChangeEvent:function(){
			$('.color-selector').each(function(){
				var self		= $(this);
				var colorInput  = self.siblings("input").first();
				
				self.ColorPicker({
					color: $(colorInput).val(),
					onShow: function (colpkr) {
						$(colpkr).fadeIn(500);
						return false;
					},
					onHide: function (colpkr) {
						$(colpkr).fadeOut(500);
						return false;
					},
					onChange: function (hsb, hex, rgb) {
						$(colorInput).val('#' + hex);
						$('#'+self.attr('id')+' div').css('backgroundColor', '#' + hex);
						$(colorInput).change();
					}
				});
			});
		},
		ShowcaseChangeBg:function(){
			var originalValue			= $('#imgpanel_bg_type').val();
			var solidValue				= $('#solid_value');

			var gradientFirstValue		= $('#gradient_link_1');
			var gradientSecondValue		= $('#gradient_link_2');

			var imgpanelBgValueFirst	= $('#imgpanel_bg_value_first');
			var imgpanelBgValueLast		= $('#imgpanel_bg_value_last');
			var bgInputValueFirst		= $('#jsn-bg-input-value-first');
			var bgInputValueSecond		= $('#jsn-bg-input-value-second');
			var spanSolidValue			= $('#solid_value div');
			var spanGradientFirstValue	= $('#gradient_link_1 div');
			var spanGradientSecondValue	= $('#gradient_link_2 div');
			var patternTitle			= $('#pattern_title');
			var imageTitle				= $('#image_title');
			if (originalValue == 'linear-gradient' && imgpanelBgValueFirst.val() == '')
			{
				solidValue.hide();
				bgInputValueSecond.show();
				patternTitle.hide();
				imageTitle.hide();
				imgpanelBgValueFirst.removeClass('input-xxlarge').addClass('input-mini');
				imgpanelBgValueFirst.val('#595959');
				imgpanelBgValueLast.val('#262626');
				JSNISClassicTheme['background-type-' + originalValue] = {'firstValue' : imgpanelBgValueFirst.val(), 'lastValue': imgpanelBgValueLast.val()};
			}
			var imgpanelBgType	= $('#imgpanel_bg_type');
			imgpanelBgType.click(function(){
				var value = imgpanelBgType.val();
				JSNISClassicTheme['background-type-' + value] = {'firstValue' : imgpanelBgValueFirst.val(), 'lastValue': imgpanelBgValueLast.val()};
			});
			imgpanelBgType.change(function() {	
				
				var value = imgpanelBgType.val();
				
				switch (value) 
				{
					case 'solid-color':
						solidValue.show();
						gradientFirstValue.hide();
						bgInputValueSecond.hide();
						patternTitle.hide();
						imageTitle.hide();
						spanSolidValue.css('background-color', (JSNISClassicTheme['background-type-' + value] ) ? JSNISClassicTheme['background-type-' + value].firstValue : '#595959');
						imgpanelBgValueFirst.removeClass('input-xxlarge').addClass('input-mini');
						imgpanelBgValueFirst.val((JSNISClassicTheme['background-type-' + value] ) ? JSNISClassicTheme['background-type-' + value].firstValue : '#595959');
						imgpanelBgValueLast.val((JSNISClassicTheme['background-type-' + value]) ? JSNISClassicTheme['background-type-' + value].lastValue : '');
						JSNISClassicTheme['background-type-' + value] = {'firstValue' : imgpanelBgValueFirst.val(), 'lastValue': imgpanelBgValueLast.val()};
						break;
					case 'linear-gradient':	
					case 'radial-gradient':
						solidValue.hide();
						gradientFirstValue.show();
						bgInputValueSecond.show();
						spanGradientFirstValue.css('background-color', (JSNISClassicTheme['background-type-' + value] ) ? JSNISClassicTheme['background-type-' + value].firstValue : '#000000');
						spanGradientSecondValue.css('background-color', (JSNISClassicTheme['background-type-' + value] ) ? JSNISClassicTheme['background-type-' + value].lastValue : '#ffffff');
						patternTitle.hide();
						imageTitle.hide();
						imgpanelBgValueFirst.removeClass('input-xxlarge').addClass('input-mini');
						imgpanelBgValueFirst.val((JSNISClassicTheme['background-type-' + value]) ? JSNISClassicTheme['background-type-' + value].firstValue : '#000000');
						imgpanelBgValueLast.val((JSNISClassicTheme['background-type-' + value]) ? JSNISClassicTheme['background-type-' + value].lastValue : '#ffffff');
						JSNISClassicTheme['background-type-' + value] = {'firstValue' : imgpanelBgValueFirst.val(), 'lastValue': imgpanelBgValueLast.val()};
						break;
					case 'pattern':
						solidValue.hide();
						gradientFirstValue.hide();
						bgInputValueSecond.hide();
						patternTitle.show();
						imageTitle.hide();
						imgpanelBgValueFirst.removeClass('input-mini').addClass('input-xxlarge');
						imgpanelBgValueFirst.val((JSNISClassicTheme['background-type-' + value]) ? JSNISClassicTheme['background-type-' + value].firstValue : '');
						imgpanelBgValueLast.val('');
						JSNISClassicTheme['background-type-' + value] = {'firstValue' : imgpanelBgValueFirst.val(), 'lastValue': imgpanelBgValueLast.val()};
						break;				
					case 'image':
						solidValue.hide();
						gradientFirstValue.hide();
						bgInputValueSecond.hide();
						patternTitle.hide();
						imageTitle.show();
						imgpanelBgValueFirst.removeClass('input-mini').addClass('input-xxlarge');
						imgpanelBgValueFirst.val((JSNISClassicTheme['background-type-' + value]) ? JSNISClassicTheme['background-type-' + value].firstValue : '');
						imgpanelBgValueLast.val('');
						JSNISClassicTheme['background-type-' + value] = {'firstValue' : imgpanelBgValueFirst.val(), 'lastValue': imgpanelBgValueLast.val()};
						break;	
					default:
						break;
				}
				imgpanelBgValueFirst.change();
				imgpanelBgValueLast.change();
			});

			$('#imgpanel_bg_type').trigger('click');
			$('#imgpanel_bg_type').trigger('change');
	
			// BEGIN imgpanel_bg_type JS
			var jsImgpanelBgType	= $('#js_imgpanel_bg_type');
			var jsImgpanelBgValue	= $('#js_imgpanel_bg_value');
			var jsSolidColor		= $('#js_solid_color');
			var jsPatternValue		= $('#js_pattern_value');
			var jsPatternTitle		= $('#js_pattern_title');
			var jsImageTitle		= $('#js_image_title');
			var jsBackgroundValue	= $('#js_background_value');
			
			jsImgpanelBgType.click(function(){
				var value = jsImgpanelBgType.val();
				JSNISClassicTheme['js-background-type-' + value] = {'value' : jsImgpanelBgValue.val()};
			});
			jsImgpanelBgType.change(function() {	
				var value = jsImgpanelBgType.val();
				
				switch (value) 
				{
					case 'solid-color':
						jsSolidColor.show();
						jsPatternValue.hide();
						jsPatternTitle.hide();
						jsImageTitle.hide();
						jsBackgroundValue.hide();
						jsImgpanelBgValue.removeClass('input-xxlarge').addClass('input-mini');
						jsImgpanelBgValue.val((JSNISClassicTheme['js-background-type-' + value] ) ? JSNISClassicTheme['js-background-type-' + value].value : ''); //(JSNISClassicTheme['js-background-type-' + value] ) ? JSNISClassicTheme['js-background-type-' + value].value : '';	
						JSNISClassicTheme['js-background-type-' + value] = {'value' : jsImgpanelBgValue.val()};	
						break;
					case 'pattern':
						jsSolidColor.hide();
						jsPatternValue.show();
						jsPatternTitle.show();
						jsBackgroundValue.hide();
						jsImageTitle.hide();
						jsImgpanelBgValue.removeClass('input-mini').addClass('input-xxlarge');
						jsImgpanelBgValue.val((JSNISClassicTheme['js-background-type-' + value] ) ? JSNISClassicTheme['js-background-type-' + value].value : '');//(JSNISClassicTheme['js-background-type-' + value] ) ? JSNISClassicTheme['js-background-type-' + value].value : ''; 
						JSNISClassicTheme['js-background-type-' + value] = {'value' : jsImgpanelBgValue.val()};
						break;				
					case 'image':
						jsSolidColor.hide();
						jsPatternValue.hide();
						jsBackgroundValue.show();
						jsPatternTitle.hide();
						jsImageTitle.show();
						jsImgpanelBgValue.removeClass('input-mini').addClass('input-xxlarge');
						jsImgpanelBgValue.val((JSNISClassicTheme['js-background-type-' + value] ) ? JSNISClassicTheme['js-background-type-' + value].value : '');//(JSNISClassicTheme['js-background-type-' + value] ) ? JSNISClassicTheme['js-background-type-' + value].value : '';
						JSNISClassicTheme['js-background-type-' + value] = {'value' : $('#js_imgpanel_bg_value').val()};
						break;	
					default:
						break;
				}
				jsImgpanelBgValue.change();
			})
			jsImgpanelBgType.trigger('click');
			jsImgpanelBgType.trigger('change');
			
			// END imgpanel_bg_type JS		
			
			//Start color picker
			JSNISClassicTheme.ColorChangeEvent();
		},
		ReplaceVals: function (n) {
			if (n == "a") { n = 10; }
			if (n == "b") { n = 11; }
			if (n == "c") { n = 12; }
			if (n == "d") { n = 13; }
			if (n == "e") { n = 14; }
			if (n == "f") { n = 15; }
			
			return n;
		},
		hextorgb: function (strPara) {
			var casechanged=strPara.toLowerCase(); 
			var stringArray=casechanged.split("");
			if(stringArray[0] == '#'){
				for(var i = 1; i < stringArray.length; i++){			
					if(i == 1 ){
						var n1 = JSNISClassicTheme.ReplaceVals(stringArray[i]);				
					}else if(i == 2){
						var n2 = JSNISClassicTheme.ReplaceVals(stringArray[i]);
					}else if(i == 3){
						var n3 = JSNISClassicTheme.ReplaceVals(stringArray[i]);
					}else if(i == 4){
						var n4 = JSNISClassicTheme.ReplaceVals(stringArray[i]);
					}else if(i == 5){
						var n5 = JSNISClassicTheme.ReplaceVals(stringArray[i]);
					}else if(i == 6){
						var n6 = JSNISClassicTheme.ReplaceVals(stringArray[i]);
					}			
				}
				
				var returnval = ((16 * n1) + (1 * n2));
				var returnval1 = 16 * n3 + n4;
				var returnval2 = 16 * n5 + n6;
				return new Array(((16 * n1) + (1 * n2)), ((16 * n3) + (1 * n4)), ((16 * n5) + (1 * n6)));
			}
			return new Array(255, 0, 0);
		},
	
		getShowcaseJSON: function()
		{
			var options = {};
			var imgpanelBgValue = '';
			
			$('#theme-classic-flash :input').each(function()		
			{
				var name	= $(this).attr('name');//el.name;
				if ($(this).attr('type') == 'radio')
				{
					if($(this).attr("checked") == 'checked'){
						var value	= $(this).val();
					}
				}else{
					var value	= $(this).val();
				}	
				
				if(value)
				{	
					if (name == 'imgpanel_bg_value[]')
					{
						imgpanelBgValue += ","+value;
						options['imgpanel_bg_value'] = imgpanelBgValue;	
					}
					else
					{
						options[name] = value;
					}
				}	
			});	
			var showcaseJSON = JSNISClassicTheme.prepareShowcaseData(options);
			return showcaseJSON;
		},
	
		currentShowcaseSetting : null,
	
		ShowcasePreview: function()
		{
			var showlistID 		= $('#showlist_id').val();
			var showlistURL 	= 'index.php?option=com_imageshow&controller=showlist&format=showlist&showlist_id='+showlistID;
			JSNISClassicTheme.currentShowcaseSetting = JSNISClassicTheme.getShowcaseJSON();
			$.ajax({
				type: "POST",
				url:  showlistURL,
				cache: false,
			}).done(function(showlistJSON) { 
				if(JSNISClassicTheme.currentShowcaseSetting != '')
				{	
					JSNISClassicTheme.previewModalWindow.dialog('open');
					try {
						document.getElementById('jsn-flash-preview-object').loadData(JSNISClassicTheme.currentShowcaseSetting, showlistJSON);
					}catch(err){
						
					}
					return true;
				}
			});	
			return true;
		},
		
		ChangeWatermark:function(){
			var value = $('#imgpanel_watermark_position').val();
			if(value =='center'){
				$('#imgpanel_watermark_offset').attr("disabled", true);
			}else{
				$('#imgpanel_watermark_offset').attr("disabled", false);
			}
		},
		EnableShowCasePreview:function(){
			var value			= $('#showlist_id').val();
			var previewButton 	= $('#preview-showcase-link');
			
			if(value == 0){
				previewButton.attr("disabled", true);
				previewButton.css('color','#ccc');
			}else{
				previewButton.attr("disabled", false);
				previewButton.css('color','#000');
			}
		},
	
		prepareShowcaseData: function(data)
		{
			try{
				if(data['imgpanel_bg_value'].indexOf('#') < 0 && data['imgpanel_bg_value'] != '')
				{
					var backgroundValue = data['showcase_base_url'] + data['imgpanel_bg_value'].substr(1);
				}else{
					var backgroundValue = data['imgpanel_bg_value'].substr(1);
				}
			}catch(err){
				var backgroundValue = null;
			}
			
			var objGeneral = {
				'round-corner' 			: data['general_round_corner_radius'],
				'border-stroke' 		: data['general_border_stroke'],
				'background-color' 		: data['background_color'],
				'border-color' 			: data['general_border_color'],
				'number-images-preload' : data['general_number_images_preload'],
				'images-order'			: data['general_images_order'],
				'title-source' 			: data['general_title_source'],
				'description-source' 	: data['general_des_source'],
				'link-source' 			: data['general_link_source'],
				'open-link-in' 			: data['general_open_link_in']
			};
			
			var objImage = {
				'default-presentation'	: data['imgpanel_presentation_mode'],
				'background-type' 		: data['imgpanel_bg_type'],
				'background-value' 		: backgroundValue,
				'show-watermark' 		: data['imgpanel_show_watermark'],
				'watermark-path' 		: (data['imgpanel_watermark_path'] != null && data['imgpanel_watermark_path'] != '') ? (data['showcase_base_url'] + data['imgpanel_watermark_path']) : '',
				'watermark-opacity' 	: data['imgpanel_watermark_opacity'],
				'watermark-position' 	: data['imgpanel_watermark_position'],
				'watermark-offset' 		: data['imgpanel_watermark_offset'],
				'show-inner-shadow' 	: data['imgpanel_show_inner_shawdow'],
				'inner-shadow-color' 	: (data['imgpanel_inner_shawdow_color'] != '') ? data['imgpanel_inner_shawdow_color'] : '' ,
				'show-overlay' 			: (data['imgpanel_show_overlay_effect'] == 2) ? 'no' : data['imgpanel_show_overlay_effect'],
				'overlay-type' 			: data['imgpanel_overlay_effect_type'],
				'fitin-settings'		: {
												'transition-type' 		: data['imgpanel_img_transition_type_fit'],
												'transition-timing' 	: 2,
												'click-action' 			: data['imgpanel_img_click_action_fit'],
												'open-link-in' 			: data['imgpanel_img_open_link_in_fit'],
												'show-image-shadow'		: data['imgpanel_img_show_image_shadow_fit']
										  },
				'expandout-settings'	: {
												'transition-type' 		: data['imgpanel_img_transition_type_expand'],
												'transition-timing' 	: 2,
												'motion-type' 			: (data['imgpanel_img_motion_type_expand'] == 'no-motion') ? data['imgpanel_img_motion_type_expand'] : data['imgpanel_img_zooming_type_expand'] + '-' + data['imgpanel_img_motion_type_expand'],
												'motion-timing' 		: 3,
												'click-action' 			: data['imgpanel_img_click_action_expand'],
												'open-link-in' 			: data['imgpanel_img_open_link_in_expand']
										  }		
			};
			
			var objThumb = {
				'show-panel' 					: data['thumbpanel_show_panel'],
				'panel-position' 				: data['thumbpanel_panel_position'],
				'collapsible-panel' 			: data['thumbpanel_collapsible_position'],
				'background-color'	 			: data['thumbpanel_thumnail_panel_color'],
				'thumbnail-row' 				: data['thumbpanel_thumb_row'],
				'thumbnail-width' 				: data['thumbpanel_thumb_width'],
				'thumbnail-height' 				: data['thumbpanel_thumb_height'],
				'thumbnail-opacity'				: data['thumbpanel_thumb_opacity'],
				'active-state-color' 			: data['thumbpanel_active_state_color'],
				'normal-state-color' 			: data['thumbpanel_thumnail_normal_state'],
				'thumbnails-browsing-mode'	 	: data['thumbpanel_thumb_browsing_mode'],
				'thumbnails-presentation-mode'  : data['thumbpanel_presentation_mode'],
				'thumbnail-border'	 			: data['thumbpanel_border'],
				'show-thumbnails-status' 		: data['thumbpanel_show_thumb_status'],
				'enable-big-thumbnail'	 		: data['thumbpanel_enable_big_thumb'],
				'big-thumbnail-size' 			: data['thumbpanel_big_thumb_size'],
				'big-thumbnail-color' 			: data['thumbpanel_big_thumb_color'],
				'big-thumbnail-border'	 		: data['thumbpanel_thumb_border']
			};
			
			var objInfo = {
				'panel-presentation' 			: data['infopanel_presentation'],
				'panel-position'				: data['infopanel_panel_position'],
				'background-color-fill' 		: data['infopanel_bg_color_fill'],
				'show-title' 					: data['infopanel_show_title'],
				'click-action'	 				: data['infopanel_panel_click_action'],
				'open-link-in'	 				: data['infopanel_open_link_in'],
				'title-css' 					: (data['infopanel_title_css'] !='') ? data['infopanel_title_css'] : '',
				'show-description'	 			: data['infopanel_show_des'],
				'description-length-limitation' : data['infopanel_des_lenght_limitation'],
				'description-css' 				: (data['infopanel_des_css'] !='') ? data['infopanel_des_css'] : '',
				'show-link'						: data['infopanel_show_link'],
				'link-css' 						: (data['infopanel_link_css'] != '') ? data['infopanel_link_css'] : ''	
			};
			
			var objToolbar = {
				'panel-position' 				: data['toolbarpanel_panel_position'],
				'panel-presentation' 			: data['toolbarpanel_presentation'],
				'show-image-navigation' 		: data['toolbarpanel_show_image_navigation'],
				'show-slideshow-player' 		: data['toolbarpanel_slideshow_player'],
				'show-fullscreen-switcher' 		: data['toolbarpanel_show_fullscreen_switcher'],
				'show-tooltip' 					: data['toolbarpanel_show_tooltip']	
			};
			
			var objSlide = {
				'image-presentation'	: (data['slideshow_enable_ken_burn_effect'] == 'yes') ? 'expand-out' : data['imgpanel_presentation_mode'],
				'slide-timing' 			: data['slideshow_slide_timing'],
				'auto-play' 			: data['slideshow_auto_play'],
				'slideshow-looping' 	: data['slideshow_looping'],
				'enable-kenburn'		: data['slideshow_enable_ken_burn_effect'],
				'show-status' 			: data['slideshow_show_status'],
				'show-thumbnail-panel'  : (data['slideshow_hide_thumb_panel'] == 'yes') ? 'off': data['thumbpanel_show_panel'],
				'show-image-navigation' : (data['slideshow_hide_image_navigation'] == 'yes') ? 'no' : data['toolbarpanel_show_image_navigation'],
				'show-watermark' 		: data['imgpanel_show_watermark'],
				'show-overlay' 			: (data['imgpanel_show_overlay_effect'] == 'during') ? 'yes' : data['imgpanel_show_overlay_effect']
			};
			
			var objShowcase = {
				'showcase' : {
					'general': objGeneral,
					'image-panel' : objImage,
					'thumbnail-panel' : objThumb,
					'information-panel' : objInfo,
					'toolbar-panel' : objToolbar,
					'slideshow' : objSlide
				}
			};
			return JSON.encode(objShowcase);
		},
		trim: function(str, chars) {
			return this.ltrim(this.rtrim(str, chars), chars);
		},
		ltrim: function(str, chars) {
			chars = chars || "\\s";
			return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
		},
		rtrim: function(str, chars) {
			if (typeof str !== 'undefined') {
				chars = chars || "\\s";
				return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
			} else 
				return '';
		},
		parserCss: function(str) {
			objCsstitle = {};
			var css = str.split(';');
			var length = css.length;
			var index  = 0;
			for (var i = 0; i < length; i++)
			{
				var value = css[i].replace(/(\r\n|\n|\r)/gm,"");
				if (value != '')
				{	
					var tmpCss = value.split(':');
					objCsstitle [this.trim(tmpCss[0], " ")] = this.trim(tmpCss[1], " ");
					index++;
				}
			}
			
			return objCsstitle;
		},
		ChangeVisual: function(fieldName,value)
		{
			switch(fieldName)
			{
				case 'imgpanel_presentation_mode':
					if(value=='fit-in'){
						$('#jsn-image-expand').hide();
						$('#jsn-image-fit').show();
					}else{
						$('#jsn-image-fit').hide();
						$('#jsn-image-expand').show();
					}
				break;
				case 'imgpanel_bg_type':
					js_imgpanel_bg_value = $('#js_imgpanel_bg_value').val();
					if(js_imgpanel_bg_value!=''){
						$('#jsn-preview-container-background').css('background', 'none');
						switch(value){
							case 'solid-color':
								$('#jsn-preview-container-background').css('background-color', js_imgpanel_bg_value);
							break;
							case 'pattern':
								backgroundPattern = $('#adminForm input[name=showcase_base_url]').val() + js_imgpanel_bg_value;
								$('#jsn-preview-container-background').css('background', 'url("'+backgroundPattern+'") repeat scroll left center transparent');
							break;
							case 'image':
								backgroundImage	= $('#adminForm input[name=showcase_base_url]').val() + js_imgpanel_bg_value;
								$('#jsn-preview-container-background').css('background', 'url("'+backgroundImage+'") no-repeat scroll left center transparent');
								$('#jsn-preview-container-background').css('background-clip', 'border-box');
								$('#jsn-preview-container-background').css('background-size', 'cover');
							break;
						}	
					}
				break;
				case 'imgpanel_bg_value[]':
					if(value!=''){
						backgroundType = $('#js_imgpanel_bg_type').val();
						JSNISClassicTheme['js-background-type-' + backgroundType] = {'value' : value};
						switch(backgroundType){
							case 'solid-color':
								$('#jsn-preview-container-background').css('background', 'url("") no-repeat scroll left center transparent');
								$('#jsn-preview-container-background').css('background-color', value);
							break;
							case 'pattern':
								backgroundPattern = $('#adminForm input[name=showcase_base_url]').val() + value;
								$('#jsn-preview-container-background').css('background', 'url("'+backgroundPattern+'") repeat scroll left center transparent');
							break;
							case 'image':
								backgroundImage	= $('#adminForm input[name=showcase_base_url]').val() + value;
								$('#jsn-preview-container-background').css('background', 'url("'+backgroundImage+'") no-repeat scroll left center transparent');
								$('#jsn-preview-container-background').css('background-clip', 'border-box');
								$('#jsn-preview-container-background').css('background-size', 'cover');
							break;
						}
					}
				break;
				case 'thumbpanel_show_panel':	
					thumbpanelPanelPosition				= $('#js_thumbpanel_panel_position').val();
					previewThumbnailsContainerHeight	= parseInt($('#js_thumbpanel_thumb_height').val())+parseInt($('#js_thumbpanel_border').val())*2+8;
					switch(value){
						case 'on':
							$('#jsn-preview-thumbnails-container').show();
							$('#jsn-preview-background').css('height',(346-previewThumbnailsContainerHeight)+'px');
							$('#jsn-image-fit-no-thumbnail').hide();
							$('#jsn-image-fit-img').show();
							if(thumbpanelPanelPosition == "top"){
								$('#jsn-preview-thumbnails-container').css('top','0px');
								$('#jsn-image-fit').css('top',(previewThumbnailsContainerHeight+13)+'px');
								$('#jsn-preview-background').css('height','346px');
								$('#jsn-preview-image-nav').css('top',(260 + previewThumbnailsContainerHeight)/2+'px');
							}else{
								$('#jsn-preview-thumbnails-container').css('top',(350-previewThumbnailsContainerHeight)+'px');
								$('#jsn-preview-image-nav').css('top',(260 - previewThumbnailsContainerHeight)/2+'px');
							}
						break;
						case 'off':
							$('#jsn-preview-thumbnails-container').hide();
							$('#jsn-preview-background').css('height','346px');
							$('#jsn-image-fit-img').hide();
							$('#jsn-image-fit-no-thumbnail').show();
							$('#jsn-preview-image-nav').css('top','125px');
							if(thumbpanelPanelPosition == "top"){
								$('#jsn-image-fit').css('top','13px');
							}	
						break;
					}
					this.ChangeVisual('infopanel_panel_position',$('#js_infopanel_panel_position').val());
				break;
				case 'thumbpanel_panel_position':	
					if($('#js_thumbpanel_show_panel').val() == 'on')
					{	
						previewThumbnailsContainerHeight = parseInt($('#js_thumbpanel_thumb_height').val())+parseInt($('#js_thumbpanel_border').val())*2+8;
						switch(value){
							case 'top':
								$('#jsn-preview-thumbnails-container').css('top','0px');
								$('#jsn-preview-background').css('height','346px');
								$('#jsn-preview-image-nav').css('top',(260 + previewThumbnailsContainerHeight)/2+'px');
								$('#jsn-image-fit').css('top',(previewThumbnailsContainerHeight+13)+'px');
							break;
							case 'bottom':
								$('#jsn-preview-thumbnails-container').css('top',(350-previewThumbnailsContainerHeight)+'px');
								$('#jsn-preview-background').css('height',(346-previewThumbnailsContainerHeight)+'px');
								$('#jsn-preview-image-nav').css('top',(260 - previewThumbnailsContainerHeight)/2+'px');
								$('#jsn-image-fit').css('top','13px');
							break;
						}
						this.ChangeVisual('infopanel_panel_position',$('#js_infopanel_panel_position').val());
					}	
				break;
				case 'thumbpanel_thumnail_panel_color':
					$('#jsn-preview-thumbnails-container').css({'background-color':value});
				break;
				case 'thumbpanel_active_state_color':
					$('#jsn-preview-thumbnails-image-active').css({'border':$('#js_thumbpanel_border').val()+'px solid '+value});
				break;	
				case 'thumbpanel_thumnail_normal_state':
					thumbnailNormalColor = JSNISClassicTheme.hextorgb(value);
					thumbnailNormalValue = 'rgba('+thumbnailNormalColor[0]+','+thumbnailNormalColor[1]+','+thumbnailNormalColor[2]+', 0.3)';
					$('.jsn-preview-thumbnails-image-normal').css({'border':$('#js_thumbpanel_border').val()+'px solid '+thumbnailNormalValue});
				break;
				case 'thumbpanel_thumb_width':
					$('.jsn-preview-thumbnails-image img').css('width', value+'px');
				break;
				case 'thumbpanel_thumb_height':
					thumbnailPanelPosition = $('#js_thumbpanel_panel_position').val();
					$('#jsn-preview-thumbnails-image').css('height', (parseInt(value)+parseInt($('#js_thumbpanel_border').val())*2)+'px');
					$('.jsn-preview-thumbnails-image img').css('height', value+'px');
					previewThumbnailsContainerHeight = parseInt(value)+parseInt($('#js_thumbpanel_border').val())*2+5;
					$('#jsn-preview-thumbnails-container').css('height', previewThumbnailsContainerHeight+'px');
					$('#jsn-preview-thumb-nav-left').css({'height':previewThumbnailsContainerHeight+'px','background-position':'-498px 50%'});
					$('#jsn-preview-thumb-nav-right').css({'height':previewThumbnailsContainerHeight+'px','background-position':'-575px 50%'});
					$('#jsn-image-fit-img').css({'height':(315 - previewThumbnailsContainerHeight)+'px','left':(542-((315 - previewThumbnailsContainerHeight)*(375/260)))/2+'px'});
					if( $('#js_thumbpanel_show_panel').val() == 'on')
					{
						switch(thumbnailPanelPosition){
							case 'top':
								$('#jsn-image-fit').css('top',(previewThumbnailsContainerHeight+16)+'px');
								$('#jsn-preview-image-nav').css('top',(260 + previewThumbnailsContainerHeight)/2+'px');
								$('#jsn-preview-background').css('height','346px');
							break;
							case 'bottom':
								$('#jsn-preview-thumbnails-container').css('top','');
								$('#jsn-preview-image-nav').css('top',(260 - parseInt(value))/2+'px');
								$('#jsn-preview-background').css('height',(342-previewThumbnailsContainerHeight)+'px');
							break;
						}
						this.ChangeVisual('infopanel_panel_position',$('#js_infopanel_panel_position').val());
					}	
				break;
				case 'thumbpanel_border':
					thumbnailPanelPosition = $('#js_thumbpanel_panel_position').val();
					$('.jsn-preview-thumbnails-image img').css('border', value+'px solid #666666');
					$('#jsn-preview-thumbnails-image-active').css('border', value+'px solid '+$('#js_thumbpanel_active_state_color').val());
					thumbnailNormalColor = JSNISClassicTheme.hextorgb($('#js_thumbpanel_thumnail_normal_state').val());
					thumbnailNormalValue = 'rgba('+thumbnailNormalColor[0]+','+thumbnailNormalColor[1]+','+thumbnailNormalColor[2]+', 0.3)';
					$('.jsn-preview-thumbnails-image-normal').css('border', value+'px solid '+thumbnailNormalValue);
					$('#jsn-preview-thumbnails-image').css('height', (parseInt($('#js_thumbpanel_thumb_height').val())+parseInt(value)*2)+'px');
					previewThumbnailsContainerHeight = parseInt($('#js_thumbpanel_thumb_height').val())+parseInt(value)*2+5;
					$('#jsn-preview-thumbnails-container').css('height', previewThumbnailsContainerHeight+'px');
					$('#jsn-preview-thumb-nav-left').css({'height':previewThumbnailsContainerHeight+'px','background-position':'-498px 50%'});
					$('#jsn-preview-thumb-nav-right').css({'height':previewThumbnailsContainerHeight+'px','background-position':'-575px 50%'});
					$('#jsn-image-fit-img').css({'height':(315 - previewThumbnailsContainerHeight)+'px','left':(542-((315 - previewThumbnailsContainerHeight)*(375/260)))/2+'px'});
					if ($('#js_thumbpanel_show_panel').val() == 'on')
					{
						switch(thumbnailPanelPosition){
							case 'top':
								$('#jsn-image-fit').css('top',(previewThumbnailsContainerHeight+16)+'px');
								$('#jsn-preview-image-nav').css('top',(260 + previewThumbnailsContainerHeight)/2+'px');
								$('#jsn-preview-background').css('height','346px');
							break;
							case 'bottom':
								$('#jsn-preview-thumbnails-container').css('top','');
								$('#jsn-preview-image-nav').css('top',(260 - previewThumbnailsContainerHeight)/2+'px');
								$('#jsn-preview-background').css('height',(342-previewThumbnailsContainerHeight)+'px');
							break;
						}
						this.ChangeVisual('infopanel_panel_position',$('#js_infopanel_panel_position').val());
					}
				break;	
				case 'infopanel_presentation':
					switch(value){
						case 'on':
							$('#jsn-preview-caption-wrapper').show();
						break;
						case 'off':
							$('#jsn-preview-caption-wrapper').hide();
						break;
					}	
				break;
				case 'infopanel_panel_position':	
					previewThumbnailsContainerHeight	= parseInt($('#js_thumbpanel_thumb_height').val())+parseInt($('#js_thumbpanel_border').val())*2+8;
					thumbnailPanelPosition				= $('#js_thumbpanel_panel_position').val();
					thumbnailPanelPresent				= $('#js_thumbpanel_show_panel').val();
					switch(value){
						case 'bottom':
							switch(thumbnailPanelPosition){
								case 'top':
									$('#jsn-preview-caption-wrapper').css({'top':'','bottom':'0px'});
								break;
								case 'bottom':
									switch(thumbnailPanelPresent){
										case 'on':
											$('#jsn-preview-caption-wrapper').css({'top':'','bottom':previewThumbnailsContainerHeight+'px'});
										break;
										case 'off':
											$('#jsn-preview-caption-wrapper').css({'top':'','bottom':'0px'});
										break;	
									}
								break;	
							}
						break;
						case 'top':
							switch(thumbnailPanelPosition){
								case 'top':
									switch(thumbnailPanelPresent){
										case 'on':
											$('#jsn-preview-caption-wrapper').css({'top':(previewThumbnailsContainerHeight)+'px','bottom':''});
										break;
										case 'off':
											$('#jsn-preview-caption-wrapper').css({'top':'0px','bottom':''});
										break;	
									}
								break;
								case 'bottom':
									$('#jsn-preview-caption-wrapper').css({'top':'0px','bottom':''});
								break;	
							}
						break;
					}	
				break;
				case 'infopanel_bg_color_fill':
					$('#jsn-preview-caption-wrapper').css('background','none repeat scroll 0 0 '+value);
				break;
				case 'js_infopanel_show_title':
					if(value=='yes'){
						$('#js-jsn-preview-title').show();
					}else{
						$('#js-jsn-preview-title').hide();
					}
				break;
				case 'infopanel_title_css':
					var objCsstitle =this.parserCss(value);
					$('#js-jsn-preview-title').css(objCsstitle);
				break;
				case 'js_infopanel_show_des':
					if(value=='yes'){
						$('#js-jsn-preview-description').show();
					}else{
						$('#js-jsn-preview-description').hide();
					}
				break;
				case 'infopanel_des_lenght_limitation':
					previewDescription		= 'Description Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut la et dolore magna aliqua.';
					previewDescription		= previewDescription.split(" ",value);
	
					var shortenDecription	= '';
					for(i=0;i<value;i++){
						if (previewDescription[i] != undefined)
						{	
							shortenDecription	+= previewDescription[i]+' ';
						}
					}
					shortenDecription += ' ...';
					$('#js-jsn-preview-description').html(shortenDecription)
				break;
				case 'infopanel_des_css':
					var objCsstitle =this.parserCss(value);
					$('#js-jsn-preview-description').css(objCsstitle);
				break;
				case 'js_infopanel_show_link':
					if(value=='yes'){
						$('#js-jsn-preview-link').show();
					}else{
						$('#js-jsn-preview-link').hide();
					}
				break;
				case 'infopanel_link_css':
					var objCsstitle =this.parserCss(value);
					$('#js-jsn-preview-link').css(objCsstitle);
					$('#js-jsn-preview-a-link').css(objCsstitle);
				break;		
				case 'toolbarpanel_presentation':
					switch(value){
						case 'off':
							$('#jsn-preview-image-nav').hide();
						break;
                        case 'on':
                            $('#jsn-preview-image-nav').show();
                        break;
						case 'auto':
							$('#jsn-preview-image-nav').show();
						break;	
					}
				break;	
			}
		},
		changeValueFlash: function(panel, name, value)
		{
			var objParam = JSNISClassicTheme.compareFieldFlash(name, value);
			JSNISClassicTheme.sendAgrToVisualFlash(panel, objParam.name, objParam.value);
		},
	
		sendAgrToVisualFlash: function(panel, name , value)
		{
			try{
				document.getElementById('jsn-flash-visual-object').loadData(panel, name, value);
			}catch(error){
				
			}
		},
	
		addEvent2ChangeValueVisualFlash: function(elementClass) 
		{
			$('.'+elementClass).each(function()
			{
				var event = 'change';
				if ($(this).attr('type') == 'radio') event = 'click';
				
				$(this).bind(event, function()
				{
					JSNISClassicTheme.changeValueFlash(elementClass, $(this).attr('name'), $(this).attr('value'));
				});
			});
		},
	
		// active visual flash
		visualFlash: function()
		{
			this.addEvent2ChangeValueVisualFlash('imagePanel');
			this.addEvent2ChangeValueVisualFlash('informationPanel');
			this.addEvent2ChangeValueVisualFlash('thumbnailPanel');
			this.addEvent2ChangeValueVisualFlash('toolbarPanel');
			this.addEvent2ChangeValueVisualFlash('slideshowPanel');
			this.onClickSlideShowPanel();
		},
	
		visualJS: function()
		{
			this.addEvent2ChangeValueVisualJS('jsImagePanel');
			this.addEvent2ChangeValueVisualJS('jsInformationPanel');
			this.addEvent2ChangeValueVisualJS('jsThumbnailPanel');
			this.addEvent2ChangeValueVisualJS('jsToolbarPanel');
		},
	
		addEvent2ChangeValueVisualJS: function(elementClass) 
		{
			$('.'+elementClass).each(function()
			{
				var event = 'change';
				if ($(this).attr('type') == 'radio') event = 'click';
				$(this).bind(event, function()
				{
					JSNISClassicTheme.ChangeVisual($(this).attr('name'), $(this).attr('value'));
				});
				if($(this).attr('type') != 'radio' || $(this).attr("checked") == 'checked')
					JSNISClassicTheme.ChangeVisual($(this).attr('name'), $(this).attr('value'));
			});
		},	
		parseParamFlash: function(paramObj, value)
		{
			var paramValue;
	
			var type = paramObj.type;
			
			if (type == 'string')
			{
				paramValue = value;
			} // something else later
			
			paramName = paramObj.value;
			paramName = paramName.replace(/-/g, ' ');
			
			paramName = paramName.toLowerCase().replace(/\b[a-z]/g, function(letter) {
			    return letter.toUpperCase();
			});
			paramName = paramName.replace(/ /g,'');
		
			paramName = paramName.charAt(0).toLowerCase() + paramName.slice(1);
			
			var newObj = {name : paramName, value : paramValue};
			return newObj;
		},
		compareSpecifyFieldFlash: function(fieldName, value)
		{
			if (fieldName == 'imgpanel_bg_value[]')
			{
				fieldName = 'imgpanel_bg_value';
				var backgroundValue = $('#imgpanel_bg_value_first').val() + "," + $('#imgpanel_bg_value_last').val();
				
				if ($('#imgpanel_bg_value_last').val() == '')
				{
					backgroundValue = backgroundValue.slice(0, -1);
				}
				
				try
				{
					if (backgroundValue.indexOf('#') < 0 && backgroundValue != '')
					{
						var value = $('#adminForm input[name=showcase_base_url]').val() + backgroundValue;
					}
					else
					{
						var value = backgroundValue;
					}
				}
				catch (err)
				{
					var value = null;
				}
			}
			
			if (fieldName == 'imgpanel_watermark_path')
			{
				var value = $('#adminForm input[name=showcase_base_url]').val() + value;
			}
			
			if (fieldName == 'slideshow_hide_thumb_panel')
			{
				if (value == 'yes')
				{
					var value = 'off';
				}
				else
				{
					var value = $('#thumbpanel_show_panel').val();
				}
			}
			
			if (fieldName == 'slideshow_hide_image_navigation')
			{
				if (value == 'yes'){
					var value = 'no';
				}else{
					var value = 'yes';
				}
			}
			
			var compareObj = {fieldName : fieldName, value : value};
			
			return compareObj;
		},
	
		compareFieldFlash: function(fieldName, value)
		{
			var compareSpecifyObj = JSNISClassicTheme.compareSpecifyFieldFlash(fieldName, value);
			
			var baseObj = 
			{
				imgpanel_presentation_mode 		: {'type' : 'string', 'value' : 'default-presentation'},
				imgpanel_bg_type 				: {'type' : 'string', 'value' : 'background-type'},
				imgpanel_bg_value 				: {'type' : 'string', 'value' : 'background-value'},
				imgpanel_show_watermark 		: {'type' : 'string', 'value' : 'show-watermark'},
				imgpanel_watermark_path 		: {'type' : 'string', 'value' : 'watermark-path'},
				imgpanel_watermark_opacity 		: {'type' : 'string', 'value' : 'watermark-opacity'},
				imgpanel_watermark_position 	: {'type' : 'string', 'value' : 'watermark-position'},
				imgpanel_watermark_offset 		: {'type' : 'string', 'value' : 'watermark-offset'},
				imgpanel_show_inner_shawdow 	: {'type' : 'string', 'value' : 'show-inner-shadow'},
				imgpanel_inner_shawdow_color 	: {'type' : 'string', 'value' : 'inner-shadow-color'},
				imgpanel_show_overlay_effect 	: {'type' : 'string', 'value' : 'show-overlay'},
				imgpanel_overlay_effect_type 	: {'type' : 'string', 'value' : 'overlay-type'},
				
				imgpanel_img_transition_type_fit 		: {'type' : 'string', 'value' : 'transition-type'},
				imgpanel_img_click_action_fit	 		: {'type' : 'string', 'value' : 'click-action'},
				imgpanel_img_show_image_shadow_fit		: {'type' : 'string', 'value' : 'show-image-shadow'},
									  
				imgpanel_img_transition_type_expand 	: {'type' : 'string', 'value' : 'transition-type'},
				imgpanel_img_transition_timing_expand 	: {'type' : 'string', 'value' : 'transition-timing'},
				imgpanel_img_motion_type_expand 		: {'type' : 'string', 'value' : 'motion-type'},
				imgpanel_img_click_action_expand 		: {'type' : 'string', 'value' : 'click-action'},
							
				thumbpanel_show_panel 				: {'type' : 'string', 'value' : 'show-panel'},
				thumbpanel_panel_position	 		: {'type' : 'string', 'value' : 'panel-position'},
				thumbpanel_collapsible_position 	: {'type' : 'string', 'value' : 'collapsible-panel'},
				thumbpanel_thumnail_panel_color 	: {'type' : 'string', 'value' : 'background-color'},
				thumbpanel_thumb_row 				: {'type' : 'string', 'value' : 'thumbnail-row'},
				thumbpanel_thumb_width 				: {'type' : 'string', 'value' : 'thumbnail-width'},
				thumbpanel_thumb_height 			: {'type' : 'string', 'value' : 'thumbnail-height'},
				thumbpanel_thumb_opacity 			: {'type' : 'string', 'value' : 'thumbnail-opacity'},
				thumbpanel_active_state_color	 	: {'type' : 'string', 'value' : 'active-state-color'},
				thumbpanel_thumnail_normal_state 	: {'type' : 'string', 'value' : 'normal-state-color'},
				thumbpanel_thumb_browsing_mode	 	: {'type' : 'string', 'value' : 'thumbnails-browsing-mode'},
				thumbpanel_presentation_mode 		: {'type' : 'string', 'value' : 'thumbnails-presentation-mode'},
				thumbpanel_border	 				: {'type' : 'string', 'value' : 'thumbnail-border'},
				thumbpanel_show_thumb_status 		: {'type' : 'string', 'value' : 'show-thumbnails-status'},
				thumbpanel_enable_big_thumb 		: {'type' : 'string', 'value' : 'enable-big-thumbnail'},
				thumbpanel_big_thumb_size 			: {'type' : 'string', 'value' : 'big-thumbnail-size'},
				thumbpanel_big_thumb_color	 		: {'type' : 'string', 'value' : 'big-thumbnail-color'},
				thumbpanel_thumb_border 			: {'type' : 'string', 'value' : 'big-thumbnail-border'},
				
				infopanel_presentation 				: {'type' : 'string', 'value' : 'panel-presentation'},
				infopanel_panel_position 			: {'type' : 'string', 'value' : 'panel-position'},
				infopanel_bg_color_fill 			: {'type' : 'string', 'value' : 'background-color-fill'},
				infopanel_show_title 				: {'type' : 'string', 'value' : 'show-title'},
				infopanel_panel_click_action 		: {'type' : 'string', 'value' : 'click-action'},
				infopanel_title_css 				: {'type' : 'string', 'value' : 'title-css'},
				infopanel_show_des	 				: {'type' : 'string', 'value' : 'show-description'},
				infopanel_des_lenght_limitation 	: {'type' : 'string', 'value' : 'description-length-limitation'},
				infopanel_des_css 					: {'type' : 'string', 'value' : 'description-css'},
				infopanel_show_link 				: {'type' : 'string', 'value' : 'show-link'},
				infopanel_link_css	 				: {'type' : 'string', 'value' : 'link-css'},
							
				toolbarpanel_panel_position 			: {'type' : 'string', 'value' : 'panel-position'},
				toolbarpanel_presentation 				: {'type' : 'string', 'value' : 'panel-presentation'},
				toolbarpanel_show_image_navigation 		: {'type' : 'string', 'value' : 'show-image-navigation'},
				toolbarpanel_slideshow_player 			: {'type' : 'string', 'value' : 'show-slideshow-player'},
				toolbarpanel_show_fullscreen_switcher	: {'type' : 'string', 'value' : 'show-fullscreen-switcher'},
				toolbarpanel_show_tooltip	 			: {'type' : 'string', 'value' : 'show-tooltip'},
				
				slideshow_presentation_mode 			: {'type' : 'string', 'value' : 'image-presentation'},
				slideshow_slide_timing 					: {'type' : 'string', 'value' : 'slide-timing'},
				slideshow_auto_play						: {'type' : 'string', 'value' : 'auto-play'},
				slideshow_looping 						: {'type' : 'string', 'value' : 'slideshow-looping'},
				slideshow_enable_ken_burn_effect 		: {'type' : 'string', 'value' : 'enable-kenburn'},
				slideshow_show_status	 				: {'type' : 'string', 'value' : 'show-status'},
				slideshow_hide_thumb_panel 				: {'type' : 'string', 'value' : 'show-thumbnail-panel'},
				slideshow_hide_image_navigation 		: {'type' : 'string', 'value' : 'show-image-navigation'},
				slideshow_show_watermark 				: {'type' : 'string', 'value' : 'show-watermark'},
				slideshow_show_overlay_effect	 		: {'type' : 'string', 'value' : 'show-overlay'}
			};
			
			return JSNISClassicTheme.parseParamFlash(baseObj[compareSpecifyObj.fieldName], compareSpecifyObj.value);
		},
		checkImagePresentationMode: function()
		{
			var mode	= $('#js_imgpanel_presentation_mode').val();
			switch(mode){
				case 'expand-out':
					JSNISClassicTheme.openAccordion('js-image-panel',['image-presentation']);
					break;
				case 'fit-in':
					JSNISClassicTheme.openAccordion('js-image-panel',['background']);
					break;
			}
		},
		openAccordion: function(panelID, accIDs) // call by visual flash
		{
			$('.'+panelID).trigger('click');
			var accordion = $('#'+panelID + '-' + accIDs[0]);
			if (accordion.hasClass('ui-state-active') == false)
			{
				accordion.trigger('click');
			}
			var accOpendIDs = [];
			for (var i = 0; i < accIDs.length; i++)
			{
				accOpendIDs[i] = panelID + '-' + accIDs[i];
			}
			accordion.siblings().each(function(){
				var elID 	= $(this).attr('id');
				var elClass = $(this).attr('class');
				if(elID){
					if($.inArray(elID, accOpendIDs) >= 0){
						if($(this).hasClass('ui-state-active') == false){
							$(this).trigger('click');
						}
					}else if($(this).hasClass('ui-state-active')){
						$(this).trigger('click');
					}
				}
			});
		},
		
		getCurrentShowcaseSetting: function()
		{
			return JSNISClassicTheme.currentShowcaseSetting;
		},
	
		showPreviewHintText: function()
		{
			var hintText 	= $('#jsn-preview-hint-text');
			var content 	= $('#jsn-preview-hint-text-content');
			var hintTextImg = $('#jsn-preview-hint-text-img');
			hintTextImg.mouseover(function()
			{
				hintTextImg.addClass('hint-text-active').removeClass('hint-text-deactive');
				hintText.css({'width':'500px'});
				content.show();
			}).mouseout(function(){
				hintTextImg.removeClass('hint-text-active').addClass('hint-text-deactive');
				hintText.css({'width':''});
				content.hide();
			});	
		},
		openLinkIn: function(elChangeID, elShowID, parent)
		{
			var elChange = $('#'+elChangeID);
			var elShow	 = $('#'+elShowID);
			
			if (elChange.attr('value') == 'open-image-link') {
				elShow.show();
			} else {
				elShow.hide();
			}
			
			elChange.change(function()
			{
				var wrapParent = $('#'+parent).parent();
				if (elChange.attr('value') == 'open-image-link') {
					elShow.show();
				} else {
					elShow.hide();
				}
			});
		},

		saveAccordionStatusCookie: function(el, cookieName)
		{
			var settings	= {};
			var elID		= el.attr('id');
			var accStatus	= (el.attr('class').indexOf('ui-state-active') > 0)?true:false;
			settings[elID]	= accStatus;
			if (!$.cookie(cookieName))
			{
				$.cookie(cookieName, JSON.encode(settings));
			}
			else
			{
				var objSetting		= $.parseJSON($.cookie(cookieName));
				objSetting[elID]	= accStatus;
				$.cookie(cookieName, JSON.encode(objSetting));
			}
		},
		loadAccordionSettingCookie: function(cookieName)
		{
			if($.cookie(cookieName)){
				var settings	= $.parseJSON($.cookie(cookieName));
				$.each(settings, function(id, val) {
					if(val==true){
						$('#'+id).trigger('click');
					}
				});
			}	
		},
		slideShowTabIsOpened: false,
	
		getStatusSlideShowTab: function()
		{
			return JSNISClassicTheme.slideShowTabIsOpened;
		},
	
		onClickSlideShowPanel: function()
		{
			$('.jsn-theme-panel').each(function()
			{
				$(this).click(function()
				{
					if ($(this).attr('class').indexOf('slideshow-panel') >= 0)
					{
						JSNISClassicTheme.slideShowTabIsOpened = true;
						JSNISClassicTheme.sendAgrToVisualFlash('slideshowPanel', 'active', 'true');
					}
					else
					{
						JSNISClassicTheme.slideShowTabIsOpened = false;
						JSNISClassicTheme.sendAgrToVisualFlash('slideshowPanel', 'active', 'false');
					}
				});
			});
		},
	
		showcaseChangeImageMotionType: function(me)
		{
			var zooming = $('#jsn-image-zooming-type');
			if(me.value == 'no-motion'){
				zooming.hide();
			} else {
				zooming.show();
			}
		},
	
		fixDisplayNoMotion: function()
		{
			if ($('#imgpanel_img_motion_type_expand').attr('value') == 'no-motion'){
				$('#jsn-image-zooming-type').hide();
			} else {
				$('#jsn-image-zooming-type').show();
			}
		}
	};

})(jQuery);