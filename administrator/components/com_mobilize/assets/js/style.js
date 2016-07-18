define([
    'jquery',
    'jsn/libs/modal',
    'jsn/visualdesignstyle',
    'jquery.json',
    'jquery.ui'],
    function ($, JSNModal, JSNVisualDesignStyle) {
        var JSNMobilizeStyle = function (params) {
            this.params = params;
            this.lang = params.language;
            this.token = params.token;
            this.pathRoot = params.pathRoot;
            this.generateStyle = new JSNVisualDesignStyle(params);
            this.init();
        }
        JSNMobilizeStyle.prototype = {
            init:function () {
                var self = this;
                $(document).click(function () {
                    $(".mobilize-dialog").remove();
                    $(".jsn-element-active").removeClass("jsn-element-active");
                    $(".ui-state-edit").removeClass("ui-state-edit");
                });
                $(".jsn-iconbar a").click(function (e) {
                    var buttons = {}, selfAction = this;
					
                    buttons[self.lang['JSN_MOBILIZE_SAVE']] = $.proxy(function () {
                        var items = [];
						$('.jsn-imagefile').remove();
						$('.fileupload').remove();
                        $("#jsn-block-container").find("input,select").each(function () {
                            if ($(this).hasClass("jsn-m-radio")) {
                                if ($(this).is(":checked")) {
                                    var item = {};
                                    item.key = $(this).attr("name");
                                    item.value = $(this).val();
                                    items.push(item);
                                }
                            } else {
                                var item = {};
                                item.key = $(this).attr("name");
                                item.value = $(this).val();
                                items.push(item);
                            }
                        });
                        $(selfAction).next("input.jsn-input-style").val($.toJSON(items));
                        self.changeStyle($(selfAction));
                        //  $(this).dialog("close");
                        $("#jsn-block-container").parents(".ui-dialog").remove();
                        //$(selfAction).parents(".jsn-block").removeClass("jsn-edit-state");
                        $(".colorpicker").remove();
                    }, this);
                    buttons[self.lang['JSN_MOBILIZE_CANCEL']] = $.proxy(function () {
                        //  $(this).dialog("close");
                        $("#jsn-block-container").parents(".ui-dialog").remove();
                        //   $(selfAction).parents(".jsn-block").removeClass("jsn-edit-state");
                        $(".colorpicker").remove();
                    }, this);
                    var height = $(window).height();
                    var valueDefault = $(this).parents().find("input[type=hidden]").val() ? $.evalJSON($(this).parents().find("input[type=hidden]").val()) : "";
                    var idContainer = $(this).parents().find("input[type=hidden]").attr("id");
                    var defaultValue = {};
                    if (valueDefault) {
                        $.each(valueDefault, function () {
                            defaultValue[this.key] = this.value;
                        });
                    }
                    //  if (idContainer == "input_style_jsn_user_bottom" || idContainer == "input_style_jsn_content_bottom" || idContainer == "input_style_jsn_content_top" || idContainer == "input_style_jsn_user_top") {
                    //  return false;
                    // }
                    if ($(this).attr("data-action") == "menu") {
                        self.generateStyle.createModalGenerateStyle($(this), self.designStyleMenu(this), defaultValue, buttons, "Style Settings", 750, height * (95 / 100));
                    }
                    if ($(this).attr("data-action") == "module") {
                        self.generateStyle.createModalGenerateStyle($(this), self.designStyleModule(this), defaultValue, buttons, "Style Settings", 750, height * (95 / 100));
						self.styleImageModul($(this));
                    }
                    if ($(this).attr("data-action") == "logo") {
                        self.generateStyle.createModalGenerateStyle($(this), self.designStyleLogo(this), defaultValue, buttons, "Style Settings", 750, height * (95 / 100));
                    }
                    if ($(this).attr("data-action") == "mainbody") {
                        self.generateStyle.createModalGenerateStyle($(this), self.designStyleMainBody(this), defaultValue, buttons, "Style Settings", 750, height * (95 / 100));
                    }
                    if ($(this).attr("data-action") == "switcher") {
                        self.generateStyle.createModalGenerateStyle($(this), self.designStyleSwitcher(this), defaultValue, buttons, "Style Settings", 750, height * (95 / 100));
                    }
		//Set style template
					if ($(this).attr("data-action") == "template") {
						self.generateStyle.createModalGenerateStyle($(this), self.designStyleTemplate(this), defaultValue, buttons, "Template Style", 750, height * (95 / 100));
						$('#tab_container').css('display','none');
						self.styleImageModul($(this));
					}
		//end style template
                    
					self.fileChange();
                    $("#jsn-block-container").find("input.jsn-borderThickness,input.jsn-input-number,input.jsn-input-number,input.jsn-roundedCornerRadius").each(function () {
                        $(this).keypress(function (e) {
                            if (e.which == 45) {
                                var valueInput = $(this).val();
                                valueInput = valueInput.replace(new RegExp('-', 'g'), '');
                                $(this).val('-' + valueInput);
                                return false;
                            }
                            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                                return false;
                            }
                        });
                    });
                    e.stopPropagation();
                });
                self.getBackgroundIconColor();
            },
			styleImageModul:function(e){
                var self=this;
                var id = e.parent().parent().find('.jsn-column-container').attr('id');
                    id = id.replace(/-/g, "_");
                $('#option-' + id + '_container_ba_backgroundType-select').append($("<option/>", {'value': 'img', "text": "Image"}));
                    self.showHide(id,0);
                    $('.jsn-image').hide();
                    $('#option-' + id + '_container_ba_backgroundType-select').change(function() {
                        if ($(this).val() === 'img') {
                            $(this).parent().parent().find('.jsn-gradientColor').css('display', 'none');
                            $(this).parent().parent().find('#option-' + id + '_container_ba_soildColor-color').css('display', 'none');
                            $(this).parent().parent().find('.jsn-soildColor').css('display', 'none');	
                            self.showHide(id,1);
                            $(this).parent().parent().parent().parent().find('.jsn-imagefile').each(function() {
                                 $(this).parent().append($("<form/>", {'action':"index.php?option=com_mobilize&view=profiles&task=profiles.uploadImage",'id':'frmFileupload','enctype':"multipart/form-data","name": "frmFile", "style": "display:none", 'class': "frmFileupload jsn_add", 'method': 'POST'}
                                                        ).append($("<input/>", {"type": "file", "style": "display:none", "name": 'fileupload', 'class': "fileupload jsn_add"})														   )
                                                        ).append($("<button/>", {"class": "btn btn_file jsn_add","text": self.lang['JSN_MOBILIZE_BROWSER'], 'style': 'margin-left:5px;margin-right:5px;'})
                                                        ).append($("<i/>", {'text':'(jpg, png)'})
                                                        ).append($("<br>")
                                                        ).append($('<span/>',{'class':'errFile','text':self.lang['JSN_MOBILIZE_UPLOAD'],'style':'display:none;color:red'}))
                            })
							$('.btn_file').click(function() {
								$('.fileupload').click();
							})
                            self.fileChange();
                        } else {
                            $(this).parent().parent().find('.jsn-gradientColor').css('display', 'block');
                            $(this).parent().parent().find('.jsn-soildColor').css('display', 'block');
                            $('.jsn_add').remove();
							$('.errFile').remove();
                            $(this).parent().parent().find('#option-' + id + '_container_ba_soildColor-color').css('display', 'none');
                            self.showHide(id,0);
                        }
                    })
                    $('.ui-button-text-only').click(function() {
                        self.loadStyleModul();
                    })
                    $('.imgFile').click(function() {
                        $('.fileupload').click();
                    })
                    self.bgImage(id);
            },
			convertColor:function (colorStr, op) {
				var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(colorStr);
				    result ='('+ parseInt(result[1], 16) +','+ parseInt(result[2], 16) + ',' + parseInt(result[3], 16) +','+ op +')';
					return result;
			},
			loadStyleModul:function (){
				var self=this;
				var cmd;
                var pre;
				var prefix;
				var color;
				var pathImg;
				var colorEffect;
				var colorOpacity;
				var imgW;
				var imgH;
				var Arrtotal = {};
				$('head').find('style').each(function (){
					if($(this).attr('data-title') === 'loadstyle'){
						$(this).remove();
					}
				})
                $('.divopacity').remove();
				$(".jsn-input-style").each(function (){
					$(this).parent().find('a').each(function (){
						if($(this).attr('data-action') !== 'logo' && $(this).attr('data-action') !== 'menu'){
							pre = $(this).parent().parent().find('.jsn-column-container').attr('id');
							if(typeof pre !== 'undefined'){
								prefix = pre.replace(/-/g, "_");
								$(this).parent().find('#input_style_'+prefix).each(function (){
									if($(this).val() !==''){
										var rtl;
										var arrVl = $.evalJSON($(this).val());
										var arr = {};
										$.each(arrVl, function (i, v) {
											if(v.key === prefix +'_container_image'){
												arr[0] = v.value;
											}
											if(v.key === prefix +'_container_effectColor'){
												if(v.value !== ''){
													arr[1] = v.value;
												}else{
													arr[1] = '';
												}
											}
											if(v.key === prefix +'_container_opacity'){
												arr[2] = v.value;
											}
											if(v.key === prefix +'_container_imageWidth'){
												arr[3] = v.value;
											}
											if(v.key === prefix +'_container_imageHeight'){
												arr[4] = v.value;
											}
											if (v.key === prefix +'_container_ba_backgroundType'){
												arr[5] = v.value; 
											}
											if (v.key === prefix +'_container_bo_border_radius'){
												arr[6] = v.value; 
											}
											if(v.key === prefix +'_container_rtl'){
												rtl = v.value;
												
											}
										})
										Arrtotal[prefix] = arr;
										var jsnid = $(this).parent().parent().find('.jsn-column-container').attr('id');
										
											$('#'+jsnid).find('style').each(function (){
												if($(this).attr('data') === jsnid){
													$(this).remove();
												}
											})
										if(rtl === 'right'){
											$('#'+jsnid).append('<style data='+jsnid+'>#'+jsnid+' .jsn-iconbar{right:auto;left:5px}</style>');
										}
										if(rtl === 'left'){
											$('#'+jsnid).append('<style data='+jsnid+'>#'+jsnid+' .jsn-iconbar{right:5px;left:auto}</style>');
										}
									}
								})
							}
						}
					})
				})
				if(typeof Arrtotal !== 'undefined' && Arrtotal !== ''){
					$.each(Arrtotal, function (i, v) {
						if(v[5] === 'img'){
							cmd=1;
							var cssbg = 'background: url('+ self.pathRoot + v[0] +')  !important;background-size: '+ v[3] +' '+ v[4]+'  !important;';
							if(i === 'jsn_template'){
								if(typeof v[1] !== 'undefined' && v[1] !== ''){
									color = self.convertColor(v[1],v[2]);
									$('<div class="divopacity" style="width: 100%;height: 100%;position: absolute;top: 0;left: 0;background-color: rgba'+ color +';"></div>').insertBefore('#jsn-mobilize');
								}
								self.loadStyleTemplate(cssbg);
							}else{
								pre = i.replace(/_/g, "-");
								$('#'+pre).css({'background':'url('+ self.pathRoot + v[0] +')','background-size':''+ v[3] +' '+ v[4] +'','position':'relative'});
								$('#'+pre).children().css({'position':'relative'});
								if(pre === 'jsn-footer'){
									$('.jsn-total').removeAttr("style");
									$('#jsn-switcher').removeAttr("style");
									$('.jsn-total').css({'background':'url('+ self.pathRoot + v[0] +')','background-size':''+ v[3] +' '+ v[4] +'','position':'relative'});
								}
								if(typeof v[1] !== 'undefined' && v[1] !== ''){
									color = self.convertColor(v[1],v[2]);
									var fst = $('#'+ pre).children('div').first();
									var radius='';
                                    if(typeof v[6] !=='undefined' && v[6]!==''){
                                        radius = v[6];
                                    }
									$('<div class="divopacity" style="border-radius:'+ radius +';width: 100%;height: 100%;position: absolute;top: 0;left: 0;background-color: rgba'+ color +';"></div>').insertBefore(fst);
								}
								self.loadStyleTemplate();
							}
						}else{
							if(i === 'jsn_template'){
								var cssbg = 'background: url('+ self.pathRoot + v[0] +') ;background-size: '+ v[3] +' '+ v[4]+'  !important;';
								self.loadStyleTemplate(cssbg);
							}
						}
						
					})
				}
				if(cmd !== 1){
					$('head').find('style').each(function (){
						if($(this).attr('data-title') === 'loadstyle'){
							$(this).remove();
						}
					})
					self.loadStyleTemplate('');
				}
			},
			fileChange:function(){
				var self=this;
				$('.fileupload').change(function (){
						var vlFile = $('.fileupload').val();
						var file = vlFile.split('.');
						var arrEx = ["jpg",'JPG','png','PNG'];
						var lt = file.length;
						if(vlFile === ''){
							$('.jsn-imagefile').val('');
						}
						if(arrEx.indexOf(file[lt - 1]) !== -1){
							vlFile = vlFile.replace('fakepath','...');
							$('.jsn-imagefile').val(vlFile);
							$('.errFile').hide();
							self.uploadImage();
						}else{
							$('.errFile').show();
							return false;
						}
                })
			},
			uploadImage: function() {
				var self=this;
				$( 'form#frmFileupload' ).submit( function( e ) {
					$.ajax({
						url: "index.php?option=com_mobilize&view=profiles&task=profiles.uploadImage&" + self.token + "=1",
						type: 'POST',
						data: new FormData( this ),
						processData: false,
						contentType: false,
						success:function (msg) {
							$('.jsn-image').each(function (){
								$(this).val(msg);
							})
//							
							
						}
					});
					e.preventDefault();
				});
				$( 'form#frmFileupload' ).submit();
		    },
			showHide:function(id,stt){
				var attCss;
				if(stt === 0){
					attCss ='jsn_hide';
				}else{
					attCss = 'jsn_show';
				}
				$('.jsn_hide').removeClass('jsn_hide');
				$('.jsn_show').removeClass('jsn_show');
				$('#option-'+ id +'_container_ba_backgroundType-select').each(function (){
					$(this).parent().parent().parent().parent().find('.jsn-imagefile').each(function() {
						$(this).parent().parent().parent().addClass(attCss);
					})
					$(this).parent().parent().parent().parent().find('#option-'+ id +'_container_effectColor-color').each(function() {
						$(this).parent().parent().parent().addClass(attCss);
					})
					$(this).parent().parent().parent().parent().find('#option-'+ id +'_container_imageWidth-select').each(function() {
						$(this).parent().parent().addClass(attCss);
					})
					$(this).parent().parent().parent().parent().find('#option-'+ id +'_container_imageHeight-select').each(function() {
						$(this).parent().parent().addClass(attCss);
					})
				})
				$('.jsn_hide').hide();
				$('.jsn_show').show();
			},
			bgImage:function (id){
				var self = this;
				var vl = $('#input_style_'+ id).val();
				if(vl!==''){
					var arrVl = $.evalJSON(vl);
					$.each(arrVl, function(i, v) {
						if (v.key === id +'_container_ba_backgroundType') {
							if(v.value === 'img'){
								$('#option-'+ id +'_container_ba_backgroundType-select').val(v.value);
								$('#option-'+ id +'_container_ba_backgroundType-select').each(function() {
								   $(this).parent().parent().parent().parent().find('.jsn-imagefile').each(function() {
										$(this).parent().append($("<form/>", {'action':"index.php?option=com_mobilize&view=profiles&task=profiles.uploadImage",'id':'frmFileupload','enctype':"multipart/form-data","name": "frmFile", "style": "display:none", 'class': "frmFileupload jsn_add", 'method': 'POST'}
																).append($("<input/>", {"type": "file", "style": "display:none", "name": 'fileupload', 'class': "fileupload jsn_add"})														   )
																).append($("<button/>", {"class": "btn btn_file jsn_add","text": self.lang['JSN_MOBILIZE_BROWSER'], 'style': 'margin-left:5px;margin-right:5px;'})
																).append($("<i/>", {'text':'(jpg, png)'})
																).append($("<br>")
																).append($('<span/>',{'class':'errFile','text':self.lang['JSN_MOBILIZE_UPLOAD'],'style':'display:none;color:red'}))
								   })
							   })
							   $('.btn_file').click(function() {
								   $('.fileupload').click();
							   })
								$('#option-'+ id +'_container_ba_backgroundType-select').parent().parent().find('.jsn-gradientColor').css('display', 'none');
								$('#option-'+ id +'_container_ba_backgroundType-select').parent().parent().find('.jsn-select-color').css('display', 'none');
								self.showHide(id,1);
							}
						}
						if (v.key === id +'_container_image') {
							$('.imgFile').val(v.value);
						}
					})
				}
			},
			
			loadStyleTemplate:function (e){
				if(typeof e === 'undefined'){e=''}
				var vl = $('#input_style_jsn_template').val();
				var totalCss;
				var cssBg = $('#jsn-template').attr('style');
				var cssTitle = $('#jsn-template h2').attr('style');
				var cssPra = $('#jsn-template p').attr('style');
				var cssLink;
				var paddingIcon;
				var rtl;
				if(vl !== ''){
					var arrVl = $.evalJSON(vl);
					$.each(arrVl, function (i, v) {
						if (v.key === 'jsn_template_content_link_linkColor'){
							cssLink = v.value;
						}
						if(v.key  === 'jsn_template_container_sp_paddingright'){
							paddingIcon = parseInt(v.value) + 41;
						}
						if(v.key  === 'jsn_template_container_rtl'){
							rtl = v.value;
						}
					})
					$('.jsn-row-container').find('.jsn-iconbar').each(function (){
						var vr = $(this).find('a').attr('data-action');
						if( vr === 'module' || vr === 'mainbody' || vr === 'menu' || vr === 'social' || vr === 'switcher'){
							$(this).attr('style', 'right:-'+ paddingIcon+'px');
						}
					})
					$('#jsn-template').find('a').attr('style', cssLink);
					var iconbartpl;
					if(rtl === 'right'){
						iconbartpl = '.jsn-master .jsn-iconbar-trigger .jsn-iconbar{right:auto;left:5px}';
					}
					totalCss = '<style data-title="loadstyle">'+iconbartpl+' .jsn-master #jsn-mobilize{text-align:'+rtl+'}.jsn-master .jsn-bootstrap #mobilize .jsn-section-content{position:relative;' + e +''+cssBg + '}.jsn-master #mobilize-design a{color:' + cssLink + '} .jsn-master #mobilize-design p{' + cssPra + '} .jsn-master #mobilize-design h1, .jsn-master #mobilize-design h2,.jsn-master #mobilize-design h3,.jsn-master #mobilize-design h4,.jsn-master #mobilize-design h5,.jsn-master #mobilize-design h6{' + cssTitle + '} </style>';
					$('head').append(totalCss);;
				}
			},
            hexToRgb:function (h) {
                var r = parseInt((this.cutHex(h)).substring(0, 2), 16), g = ((this.cutHex(h)).substring(2, 4), 16), b = parseInt((this.cutHex(h)).substring(4, 6), 16)
                return r + '' + b + '' + b;
            },
            convertHextToRgba:function (hex, opacity) {
                hex = hex.replace('#', '');
                r = parseInt(hex.substring(0, 2), 16);
                g = parseInt(hex.substring(2, 4), 16);
                b = parseInt(hex.substring(4, 6), 16);
                var result = 'rgba(' + r + ',' + g + ',' + b + ',' + opacity / 100 + ')';
                return result;
            },
            cutHex:function (h) {
                return (h.charAt(0) == "#") ? h.substring(1, 7) : h
            },
            getBackgroundIconColor:function () {
                var colorIcon = $("#jsn-menu .mobilize-menu .link-menu-mobilize i").css("color");
                colorIcon = colorIcon.match(/\((.*?)\)/);
                $("#setStyle style").html("#jsn-menu .mobilize-menu .link-menu-mobilize:hover{ background-color: rgba(" + colorIcon[1] + ",0.15); }");
            },

            changeStyle:function (_this) {
                var self = this;
                var checkRegexBackground = false;
                var parents = $(_this).parents(".jsn-row-container").find(".jsn-column-container"), style = {}, styleIcon = {};
                var optionStyle = $(_this).parents(".jsn-row-container").find("input.jsn-input-style").val();
                var optionsStyle = "", containerShadown = [], containerTitle = {}, moduleStyle = {}, contentTitle = {}, contentBody = {}, optionsShadow = [], moduleContaienrBackgroundType = "", containerBackgroundType = "";
                if (optionStyle) {
                    optionsStyle = $.evalJSON(optionStyle);
                }
                if ($(_this).attr("data-action") == "logo") {
                    $.each(optionsStyle, function (i, val) {
                        if (val.key == "jsn_logo_content_alignment") {
                            style['text-align'] = val.value;
                        }
                    });
                }
                $.each(optionsStyle, function (i, val) {
                    if (val.key == $(parents).attr("id").replace(/-/g, "_") + "_container_ba_backgroundType") {
                        containerBackgroundType = val.value;
                    }
                    if (val.key == $(parents).attr("id").replace(/-/g, "_") + "_module_tabContainer_ba_backgroundType") {
                        moduleContaienrBackgroundType = val.value;
                    }
                });
                $.each(optionsStyle, function (i, val) {
                    var keyContainer = $(parents).attr("id").replace(/-/g, "_") + "_container_";
                    var keyContainerTitle = $(parents).attr("id").replace(/-/g, "_") + "_module_tabContent_title_";
                    var keyModuleContainer = $(parents).attr("id").replace(/-/g, "_") + "_module_tabContainer_";
                    var keyContentTitle = $(parents).attr("id").replace(/-/g, "_") + "_content_title_";
                    var keyContentBody = $(parents).attr("id").replace(/-/g, "_") + "_content_body_";
                    var key = val.key;
                    if (key) {
                        var nameInputSplit = key.split("_");
                        var nameInput = nameInputSplit[nameInputSplit.length - 1];
                        if (key.search(new RegExp(keyContainerTitle, "i")) > -1) {
                            if (val.value) {
                                switch (nameInput) {
                                    case "fontFace":
                                        containerTitle["font-family"] = val.value;
                                        var check = true;
                                        var arrCheck = ['Verdana', 'Georgia', 'Courier New', 'Arial', 'Tahoma', 'Trebuchet MS'];
                                        $("head").find("link.jsn-mobilize-font").each(function () {
                                            if ($(this).attr("id") == "google-font-" + val.value.replace(" ", "-")) {
                                                check = false;
                                            }
                                        });
                                        if (check && $.inArray(val.value, arrCheck) < 1) {
                                            $("head").append($("<link/>", {"class":"jsn-mobilize-font", "id":"google-font-" + val.value.replace(" ", "-"), "rel":"stylesheet", "type":"text/css", "href":"http://fonts.googleapis.com/css?family=" + val.value.replace(" ", "+")}));
                                        }
                                        break;
                                    case "fontStyle":
                                        if (val.value == "bold") {
                                            containerTitle["font-weight"] = val.value;
                                        }
                                        else {
                                            containerTitle["font-style"] = val.value;
                                        }
                                        break;
                                    case "fontSize":
                                        containerTitle["font-size"] = val.value + "px";
                                        break;
                                    case "iconColor":
                                    case "linkColor":
                                    case "fontColor":
                                        containerTitle["color"] = val.value;
                                        break;
                                }
                            }
                        }
                        if (key.search(new RegExp(keyContentTitle, "i")) > -1) {
                            if (val.value) {
                                switch (nameInput) {
                                    case "fontFace":
                                        contentTitle["font-family"] = val.value;
                                        var check = true;
                                        var arrCheck = ['Verdana', 'Georgia', 'Courier New', 'Arial', 'Tahoma', 'Trebuchet MS'];
                                        $("head").find("link.jsn-mobilize-font").each(function () {
                                            if ($(this).attr("id") == "google-font-" + val.value.replace(" ", "-")) {
                                                check = false;
                                            }
                                        });
                                        if (check && $.inArray(val.value, arrCheck) < 1) {
                                            $("head").append($("<link/>", {"class":"jsn-mobilize-font", "id":"google-font-" + val.value.replace(" ", "-"), "rel":"stylesheet", "type":"text/css", "href":"http://fonts.googleapis.com/css?family=" + val.value.replace(" ", "+")}));
                                        }
                                        break;
                                    case "fontStyle":
                                        if (val.value == "bold") {
                                            contentTitle["font-weight"] = val.value;
                                        }
                                        else {
                                            contentTitle["font-style"] = val.value;
                                        }
                                        break;
                                    case "fontSize":
                                        contentTitle["font-size"] = val.value + "px";
                                        break;
                                    case "iconColor":
                                    case "linkColor":
                                    case "fontColor":
                                        contentTitle["color"] = val.value;
                                        break;
                                }
                            }
                        }
                        if (key.search(new RegExp(keyContentBody, "i")) > -1) {
                            if (val.value) {
                                switch (nameInput) {
                                    case "fontFace":
                                        contentBody["font-family"] = val.value;
                                        var check = true;
                                        var arrCheck = ['Verdana', 'Georgia', 'Courier New', 'Arial', 'Tahoma', 'Trebuchet MS'];
                                        $("head").find("link.jsn-mobilize-font").each(function () {
                                            if ($(this).attr("id") == "google-font-" + val.value.replace(" ", "-")) {
                                                check = false;
                                            }
                                        });
                                        if (check && $.inArray(val.value, arrCheck) < 1) {
                                            $("head").append($("<link/>", {"class":"jsn-mobilize-font", "id":"google-font-" + val.value.replace(" ", "-"), "rel":"stylesheet", "type":"text/css", "href":"http://fonts.googleapis.com/css?family=" + val.value.replace(" ", "+")}));
                                        }
                                        break;
                                    case "fontStyle":
                                        if (val.value == "bold") {
                                            contentBody["font-weight"] = val.value;
                                        }
                                        else {
                                            contentBody["font-style"] = val.value;
                                        }
                                        break;
                                    case "fontSize":
                                        contentBody["font-size"] = val.value + "px";
                                        break;
                                    case "iconColor":
                                    case "linkColor":
                                    case "fontColor":
                                        contentBody["color"] = val.value;
                                        break;
                                }
                            }
                        }
                        if (key.search(new RegExp(keyModuleContainer, "i")) > -1) {
                            if (val.value) {
                                switch (nameInput) {
                                    case "soildColor":
                                        if (moduleContaienrBackgroundType == "Solid") {
                                            moduleStyle["background"] = val.value;
                                        }
                                        break;
                                    case "gradientColor":
                                        if (moduleContaienrBackgroundType == "Gradient") {
                                            checkRegexBackground = true;
                                            moduleStyle["background"] = val.value;
                                        }
                                        break;
                                    case "borderThickness":
                                        var border = val.value;
                                        border = border ? border : 0;
                                        moduleStyle["border"] = border + "px";
                                        break;
                                    case "borderStyle":
                                        moduleStyle["border-style"] = val.value;
                                        break;
                                    case "borderColor":
                                        moduleStyle["border-color"] = val.value;
                                        break;
                                    case "paddingleft":
                                        moduleStyle["padding-left"] = val.value + "px";
                                        break;
                                    case "paddingright":
                                        moduleStyle["padding-right"] = val.value + "px";
                                        break;
                                    case "paddingtop":
                                        moduleStyle["padding-top"] = val.value + "px";
                                        break;
                                    case "paddingbottom":
                                        moduleStyle["padding-bottom"] = val.value + "px";
                                        break;
                                    case "marginleft":
                                        moduleStyle["margin-left"] = val.value + "px";
                                        break;
                                    case "marginright":
                                        moduleStyle["margin-right"] = val.value + "px";
                                        break;
                                    case "margintop":
                                        moduleStyle["margin-top"] = val.value + "px";
                                        break;
                                    case "marginbottom":
                                        moduleStyle["margin-bottom"] = val.value + "px";
                                        break;
                                    case "roundedCornerRadius":
                                        var border = val.value;
                                        border = border ? border : 0;
                                        moduleStyle["border-radius"] = border + "px";
                                        break;
                                    case "shadowSpread":

                                        if (val.value > 0) {
                                            var shadow = val.value;
                                            shadow = shadow ? shadow : 0;
                                            optionsShadow.push("0px 0px 5px " + shadow + "px");
                                        } else {
                                            optionsShadow.push("0px");
                                        }

                                        break;
                                    case "shadowColor":
                                        optionsShadow.push(self.convertHextToRgba(val.value, 25));
                                        break;
                                }
                            }
                        }
                        if (key.search(new RegExp(keyContainer, "i")) > -1) {
                            if (val.value) {
                                switch (nameInput) {
                                    case "soildColor":
                                        if (containerBackgroundType == "Solid") {

                                            style["background"] = val.value;
                                        }
                                        break;
                                    case "gradientColor":
                                        if (containerBackgroundType == "Gradient") {
                                            checkRegexBackground = true;
                                            style["background"] = val.value;
                                        }
                                        break;
                                    case "borderThickness":
                                        var border = val.value;
                                        border = border ? border : 0;
                                        style["border"] = border + "px";
                                        break;
                                    case "borderStyle":
                                        style["border-style"] = val.value;
                                        break;
                                    case "borderColor":
                                        style["border-color"] = val.value;
                                        break;
                                    case "paddingleft":
                                        style["padding-left"] = val.value + "px";
                                        break;
                                    case "paddingright":
                                        style["padding-right"] = val.value + "px";
                                        break;
                                    case "paddingtop":
                                        style["padding-top"] = val.value + "px";
                                        break;
                                    case "paddingbottom":
                                        style["padding-bottom"] = val.value + "px";
                                        break;
									case "marginleft":
                                        style["margin-left"] = val.value + "px";
                                        break;
									case "margintop":
                                        style["margin-top"] = val.value + "px";
                                        break;
									case "marginright":
                                        style["margin-right"] = val.value + "px";
                                        break;
									case "marginbottom":
                                        style["margin-bottom"] = val.value + "px";
                                        break;
                                    case "iconColor":
                                        styleIcon['color'] = val.value;
                                        break;
                                    case "shadowSpread":
                                        if (val.value > 0) {
                                            var shadow = val.value;
                                            shadow = shadow ? shadow : 0;
                                            containerShadown.push("0px 0px 5px " + shadow + "px");
                                        } else {
                                            containerShadown.push("0px 0px 0px 0px");
                                        }
                                        break;
                                    case "shadowColor":
                                        containerShadown.push(self.convertHextToRgba(val.value, 25));
                                        break;
									case "rtl":
										if(val.value !== '1'){
											style['text-align'] = val.value;
										}else{
											style['text-align'] = 'inherit';
										}
                                        break;
                                }
                            }
                        }
                    }
                });
                var textTitle = $('#input_style_jsn_typestyle').val();
                if( typeof textTitle === 'undefined' && textTitle !=='' ) {
                    textTitle = $.evalJSON(textTitle);
                }
                if(textTitle === '"Simple"' || textTitle === '"Retro"' || textTitle === '"Flat"' || textTitle === '"Modern"'){ 
                    $('#jsn-menu').find('style').each(function (){
                       if($(this).attr('data') === 'jsn_menu'){
                           $(this).remove();
                       }
                   })
                    $('#jsn-mainbody').find('style').each(function (){
                       if($(this).attr('data') === 'jsn_menu'){
                           $(this).remove();
                       }
                   })
                   $('#jsn-menu').append('<style data="jsn_menu">#jsn-menu{border-bottom:1px solid #f2f2f2 !important}</style>');
                   $('#jsn-menu').find('ul li').find('a').each(function (){
                       $(this).css({'border-left':'1px solid #f2f2f2'});
                   })
                }

                if(textTitle === '"Metro"' || textTitle === '"Glass"' || textTitle === '"Solid"'){
                    $('#jsn-menu').find('style').each(function (){
                        if($(this).attr('data') === 'jsn_menu'){
                            $(this).remove();
                        }
                    })
                    $('#jsn-mainbody').find('style').each(function (){
                        if($(this).attr('data') === 'jsn_menu'){
                            $(this).remove();
                        }
                    })
                    $('#jsn-menu').append('<style data="jsn_menu">#jsn-menu{border-bottom:1px solid #373737 !important}</style>');
                    $('#jsn-menu').find('ul li').find('a').each(function (){
                        $(this).css({'border-left':'1px solid #373737'});
                    })
                }
                if(textTitle === '"Retro"'){
                     $('#jsn-mainbody').append('<style data="jsn_menu">#jsn-mainbody h2{font-weight:bold;text-transform:uppercase}</style>');
                }

                if (optionsShadow && optionsShadow.length > 0) {

                    moduleStyle["webkit-box-shadow"] = optionsShadow.join(" ");
                    moduleStyle["box-shadow"] = optionsShadow.join(" ");
                }

                if (moduleStyle) {
                    $(parents).find(".jsn-element").removeAttr("style");
                    $(parents).find(".jsn-element").css(moduleStyle);
                    var tmpGetCss = $("<div/>");
                    $(tmpGetCss).css(moduleStyle);
                    $(parents).find(".jsn-column").attr("data-module-style", $(tmpGetCss).attr("style"));

                }
                if (containerTitle) {
                    $(parents).find(".jsn-element-content").removeAttr("style");
                    $(parents).find(".jsn-element-content").css(containerTitle);
                    var tmpGetCss = $("<div/>");
                    $(tmpGetCss).css(containerTitle);
                    $(parents).find(".jsn-column").attr("data-title-style", $(tmpGetCss).attr("style"));
                }
                if (contentTitle) {
                    $(parents).find("h2").css(contentTitle);
                }
                if (contentBody) {
                    $(parents).find("p").css(contentBody);
                }
                if (containerShadown && optionsShadow.length > 0) {
                    style["webkit-box-shadowbox-shadow"] = containerShadown.join(" ");
                    style["box-shadow"] = containerShadown.join(" ");
                }
                if (style) {
					if($(parents).attr('id') === 'jsn-footer'){
						$('.jsn-total').css(style);
					}
                    parents.css(style);
                    if (checkRegexBackground == true) {
                        var background = style.background;
						if(typeof background !== 'undefined'){
                        var op = background.match(/(.*?)\((.*?), (.*?)\s(.*?), (.*?)\s(.*?)\)/);
                        if (op) {
                            var cssBackground = '';
                            var opt2 = '', opt3 = '', opt4 = '', opt5 = '', opt6 = '';
                            if (op[2]) {
                                opt2 = op[2];
                            }
                            if (op[3]) {
                                opt3 = op[3];
                            }
                            if (op[4]) {
                                opt4 = op[4];
                            }
                            if (op[5]) {
                                opt5 = op[5];
                            }
                            if (op[6]) {
                                opt6 = op[6];
                            }
                            cssBackground += "background: " + opt3 + ";";
                            cssBackground += "background:linear-gradient(135deg, " + opt3 + " " + opt4 + "," + opt5 + " " + opt6 + ");";
                            cssBackground += "background:-moz-linear-gradient(" + opt2 + ", " + opt3 + " " + opt4 + ", " + opt5 + " " + opt6 + ");";
                            cssBackground += "background:-webkit-gradient(linear, left top, right bottom, color-stop(" + opt4 + "," + opt3 + "), color-stop(" + opt6 + "," + opt5 + "));";
                            cssBackground += "background:-webkit-linear-gradient(" + opt2 + ", " + opt3 + " " + opt4 + "," + opt5 + " " + opt6 + ");";
                            cssBackground += "background:-o-linear-gradient(" + opt2 + ", " + opt3 + " " + opt4 + "," + opt5 + " " + opt6 + ");";
                            cssBackground += "background:-ms-linear-gradient(" + opt2 + ", " + op[3] + " " + opt4 + "," + opt5 + " " + opt6 + ");";
                            cssBackground += "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='" + opt3 + "', endColorstr='" + opt5 + "',GradientType=1 );";
                            var getStyleParents = $(parents).attr("style");
                            $(parents).attr("style", getStyleParents + ";" + cssBackground);
                        }
						}

                    }
                    if (styleIcon) {
                        parents.find(".link-menu-mobilize i").removeAttr("style");
                        parents.find(".link-menu-mobilize i").css(styleIcon);
                        self.getBackgroundIconColor();
                    }
                }
            },
			designStyleTemplate:function (_this) {
                var self = this, name = $(_this).parents(".jsn-row-container").find(".jsn-column-container").attr("id");
                name = 'jsn_template';
                var options = {
                    container:{
                        tabContent:{
                            background:{
                                title:"Background",
                                type:"fieldset",
                                fieldsetContent:{
                                    comboContentBackground:self.generateStyle.generateStyleCombo(name + "_container_ba", "comboContentBackground"),
									imageFile:{
										title:"Image File",
										type:"comboContent",
										content:{
											textFile:{
												type:'text',
												name:name + "_container_imagefile",
												label:"",
												attrs:{
													name:"file",
													class:"jsn-imagefile imgFile"
												}
											},
											imageFile:{
												type:'text',
												name:name + "_container_image",
												label:"",
												attrs:{
													class:"jsn-image",
												}
											},
										}
									},
									colorEffect:{
										title:"Color Effect",
										type:"comboContent",
										content:{
											color:{
												type:'color',
												name:name + "_container_effectColor",
												label:"",
												attrs:{
													class:"jsn-select-color jsn-effectColor input-small"
												}
											},
											opacity:{
												type:'select',
												name:name + "_container_opacity",
												label:"",
												options:{
													'0.1':'0.1',
													'0.2':'0.2',
													'0.3':'0.3',
													'0.4':'0.4',
													'0.5':'0.5',
													'0.6':'0.6',
													'0.7':'0.7',
													'0.8':'0.8',
													'0.9':'0.9'
												},
												attrs:{
													title:'Color Opacity',
													class:"jselect jsn-input-fluid"
												}
											},
										}
									},
									imageWidth:{
										type:'select',
										name:name + "_container_imageWidth",
										label:"Image Width",
										options:{
											'100%':'Full',
											'auto':'Auto'
										},
										attrs:{

											class:"select jsn-input-fluid"
										}
									},
									imageHeight:{
										type:'select',
										name:name + "_container_imageHeight",
										label:"Image Height",
										options:{
											'auto':'Auto',
											'100%':'Full',
										},
										attrs:{
											class:"select jsn-input-fluid"
										}
									},
                                }
                            },
                            spacing:{
                                title:"Spacing",
                                type:"fieldset",
                                fieldsetContent:{
                                    padding:self.generateStyle.generateStyleCombo(name + "_container_sp", "comboPadding")
                                }
                            },
							titleText:{
                                title:"Title Text",
                                type:"fieldset",
                                fieldsetContent:{
									comboContentFace:self.generateStyle.generateStyleCombo(name + "_content_title_fo", "comboContentFace"),
									comboContentAttributes:self.generateStyle.generateStyleCombo(name + "_content_title_fo", "comboContentAttributes")
                                }
                            },
							paragraphText:{
                                title:"Paragraph Text",
                                type:"fieldset",
                                fieldsetContent:{
									comboContentFace:self.generateStyle.generateStyleCombo(name + "_content_body_fo", "comboContentFace"),
                                    comboContentAttributes:self.generateStyle.generateStyleCombo(name + "_content_body_fo", "comboContentAttributes"),
                                    linkColor:{
                                        name:name + "_content_link_linkColor",
                                        label:"Link Color",
                                        type:'color',
                                        attrs:{
                                            class:"jsn-select-color jsn-linkColor input-small"
                                        }
                                    }
                                }
                            },
							contentText:{
                                title:"Content",
                                type:"fieldset",
                                fieldsetContent:{
                                    linkColor:{
                                        name:name + "_container_rtl",
                                        label:"Text Direction",
                                        type:'select',
                                        options:{
										'left':'Left to Right',
										'right':'Right to Left',
										},
										attrs:{
											class:"select jsn-input-fluid"
										}
                                    }
                                }
                            },
                        }
                    },
                };
                return options;
            },
            designStyleSwitcher:function (_this) {
                var self = this, name = $(_this).parents(".jsn-row-container").find(".jsn-column-container").attr("id");
                name = name.replace(/\-/g, "_");
                var options = {
                    container:{
                        title:"Container",
                        type:"tab",
                        tabContent:{
                            background:{
                                title:"Background",
                                type:"fieldset",
                                fieldsetContent:{
                                    comboContentBorder:self.generateStyle.generateStyleCombo(name + "_container_bo", "comboContentBorder"),
                                    comboContentBackground:self.generateStyle.generateStyleCombo(name + "_container_ba", "comboContentBackground")
                                }
                            },
                            spacing:{
                                title:"Spacing",
                                type:"fieldset",
                                fieldsetContent:{
                                    padding:self.generateStyle.generateStyleCombo(name + "_container_sp", "comboPadding")

                                }
                            }
                        }
                    }
                };
                return options;
            },
            designStyleMainBody:function (_this) {
                var self = this, name = $(_this).parents(".jsn-row-container").find(".jsn-column-container").attr("id");
                name = name.replace(/\-/g, "_");
                var options = {
                    container:{
                        title:"Container",
                        type:"tab",
                        tabContent:{
                            background:{
                                title:"Background",
                                type:"fieldset",
                                fieldsetContent:{
                                    comboContentBorder:self.generateStyle.generateStyleCombo(name + "_container_bo", "comboContentBorder"),
                                    comboContentBackground:self.generateStyle.generateStyleCombo(name + "_container_ba", "comboContentBackground"),
                                    comboContentShadow:self.generateStyle.generateStyleCombo(name + "_container_sh", "comboContentShadow"),
                                    comboContentRadius:self.generateStyle.generateStyleCombo(name + "_container_bo", "comboContentRadius")
                                }
                            },
                            spacing:{
                                title:"Spacing",
                                type:"fieldset",
                                fieldsetContent:{
                                    padding:self.generateStyle.generateStyleCombo(name + "_container_sp", "comboPadding")

                                }
                            },
							contentText:{
                                title:"Content",
                                type:"fieldset",
                                fieldsetContent:{
                                    linkColor:{
                                        name:name + "_container_rtl",
                                        label:"Text Direction",
                                        type:'select',
                                        options:{
										'left':'Left to Right',
										'right':'Right to Left',
										},
										attrs:{
											class:"select jsn-input-fluid"
										}
                                    }
                                }
                            }
                        }
                    },
                    content:{
                        title:"Content",
                        type:"tab",
                        tabContent:{
                            title:{
                                title:"Title",
                                type:"fieldset",
                                fieldsetContent:{
                                    comboContentFace:self.generateStyle.generateStyleCombo(name + "_content_title_fo", "comboContentFace"),
                                    comboContentAttributes:self.generateStyle.generateStyleCombo(name + "_content_title_fo", "comboContentAttributes")
                                }
                            },
                            body:{
                                title:"Body",
                                type:"fieldset",
                                fieldsetContent:{
                                    comboContentFace:self.generateStyle.generateStyleCombo(name + "_content_body_fo", "comboContentFace"),
                                    comboContentAttributes:self.generateStyle.generateStyleCombo(name + "_content_body_fo", "comboContentAttributes"),
                                    linkColor:{
                                        name:name + "_content_link_linkColor",
                                        label:"Link Color",
                                        type:'color',
                                        attrs:{
                                            class:"jsn-select-color jsn-linkColor input-small"
                                        }
                                    }
                                }
                            }
                        }
                    }
                };
                return options;
            },
            designStyleModule:function (_this) {
                var self = this, name = $(_this).parents(".jsn-row-container").find(".jsn-column-container").attr("id");
                name = name.replace(/\-/g, "_");
                var options = {
                    container:{
                        title:"Container",
                        type:"tab",
                        tabContent:{
                            background:{
                                title:"Background",
                                type:"fieldset",
                                fieldsetContent:{
                                    comboContentBackground:self.generateStyle.generateStyleCombo(name + "_container_ba", "comboContentBackground"),
									imageFile:{
										title:"Image File",
										type:"comboContent",
										content:{
											textFile:{
												type:'text',
												name:name + "_container_imagefile",
												label:"",
												attrs:{
													name:"file",
													class:"jsn-imagefile imgFile"
												}
											},
											imageFile:{
												type:'text',
												name:name + "_container_image",
												label:"",
												attrs:{
													class:"jsn-image",
												}
											},
										}
									},
									colorEffect:{
										title:"Color Effect",
										type:"comboContent",
										content:{
											color:{
												type:'color',
												name:name + "_container_effectColor",
												label:"",
												attrs:{
													class:"jsn-select-color jsn-effectColor input-small"
												}
											},
											opacity:{
												type:'select',
												name:name + "_container_opacity",
												label:"",
												options:{
													'0.1':'0.1',
													'0.2':'0.2',
													'0.3':'0.3',
													'0.4':'0.4',
													'0.5':'0.5',
													'0.6':'0.6',
													'0.7':'0.7',
													'0.8':'0.8',
													'0.9':'0.9'
												},
												attrs:{
													title:'Color Opacity',
													class:"jselect jsn-input-fluid"
												}
											},
										}
									},
									imageWidth:{
										type:'select',
										name:name + "_container_imageWidth",
										label:"Image Width",
										options:{
											'100%':'Full',
											'auto':'Auto'
										},
										attrs:{

											class:"select jsn-input-fluid"
										}
									},
									imageHeight:{
										type:'select',
										name:name + "_container_imageHeight",
										label:"Image Height",
										options:{
											'auto':'Auto',
											'100%':'Full',
										},
										attrs:{
											class:"select jsn-input-fluid"
										}
									},
                                }
                            },
							Title :{
								title:"Title Text",
								type:"fieldset",
								fieldsetContent:{
									comboContentFace:self.generateStyle.generateStyleCombo(name + "_content_title_fo", "comboContentFace"),
									comboContentAttributes:self.generateStyle.generateStyleCombo(name + "_content_title_fo", "comboContentAttributes")
								}
							},
							paragraphText:{
                                title:"Paragraph Text",
                                type:"fieldset",
                                fieldsetContent:{
									comboContentFace:self.generateStyle.generateStyleCombo(name + "_container_text_fo", "comboContentFace"),
                                    comboContentAttributes:self.generateStyle.generateStyleCombo(name + "_container_text_fo", "comboContentAttributes"),
                                    linkColor:{
                                        name:name + "_container_link_linkColor",
                                        label:"Link Color",
                                        type:'color',
                                        attrs:{
                                            class:"jsn-select-color jsn-linkColor input-small"
                                        }
                                    }

                                }
                            },
							boder:{
								title:"Border",
                                type:"fieldset",
								fieldsetContent:{
									comboContentBorder:self.generateStyle.generateStyleCombo(name + "_container_bo", "comboContentBorder"),
								}
							},
                            spacing:{
                                title:"Spacing",
                                type:"fieldset",
                                fieldsetContent:{
									margin:self.generateStyle.generateStyleCombo(name + "_container_sp", "comboMargin"),
                                    padding:self.generateStyle.generateStyleCombo(name + "_container_sp", "comboPadding")
                                }
                            },
							contentText: {
								title: "Content",
								type: "fieldset",
								fieldsetContent: {
									linkColor: {
										name: name + "_container_rtl",
										label: "Text Direction",
										type: 'select',
										options: {
											'left': 'Left to Right',
											'right': 'Right to Left',
										},
										attrs: {
											class: "select jsn-input-fluid"
										}
									}
								}
							}
                        }
                    },
                    module:{
                        title:"Module",
                        type:"tab",
						tabContent:{
							background:{
								title:"Background",
								type:"fieldset",
								fieldsetContent:{
									comboContentBackground:self.generateStyle.generateStyleCombo(name + "_module_tabContainer_ba", "comboContentBackground"),
								}
							},
							border:{
								title:"Border",
								type:"fieldset",
								fieldsetContent:{
									comboContentBorder:self.generateStyle.generateStyleCombo(name + "_module_tabContainer_bo", "comboContentBorder"),
								}
							},
							spacing:{
								title:"Spacing",
								type:"fieldset",
								fieldsetContent:{
									margin:self.generateStyle.generateStyleCombo(name + "_module_tabContainer_sp", "comboMargin"),
									padding:self.generateStyle.generateStyleCombo(name + "_module_tabContainer_sp", "comboPadding")
								}
							},
						}
                    }
                };
                return options;
            },
            designStyleLogo:function (_this) {
                var self = this, name = $(_this).parents(".jsn-row-container").find(".jsn-column-container").attr("id");
                name = name.replace(/\-/g, "_");
                var options = {
                    container:{
                        title:"Container",
                        type:"tab",
                        tabContent:{
                            background:{
                                title:"Background",
                                type:"fieldset",
                                fieldsetContent:{
                                    comboContentBorder:self.generateStyle.generateStyleCombo(name + "_container_bo", "comboContentBorder"),
                                    comboContentBackground:self.generateStyle.generateStyleCombo(name + "_container_ba", "comboContentBackground")
                                }
                            },
                            spacing:{
                                title:"Spacing",
                                type:"fieldset",
                                fieldsetContent:{
                                    padding:self.generateStyle.generateStyleCombo(name + "_container_sp", "comboPadding")
                                }
                            }
                        }
                    },
                    content:{
                        title:"Content",
                        type:"tab",
                        tabContent:{
                            background:{
                                title:"Logo",
                                type:"fieldset",
                                fieldsetContent:{
                                    alignment:{
                                        type:'select',
                                        label:"Alignment",
                                        name:name + "_content_alignment",
                                        options:{
                                            "left":"Left",
                                            "center":"Center",
                                            "right":"Right"
                                        },
                                        attrs:{
                                            'class':'input-small'
                                        }
                                    }
                                }
                            }
                        }
                    }
                };
                return options;
            },
            designStyleMenu:function (_this) {

                var self = this, name = $(_this).parents(".jsn-row-container").find(".jsn-column-container").attr("id");
                name = name.replace(/\-/g, "_");
                var options = {
                    container:{
                        title:"Container",
                        type:"tab",
                        tabContent:{
                            background:{
                                title:"Background",
                                type:"fieldset",
                                fieldsetContent:{
                                    comboContentBorder:self.generateStyle.generateStyleCombo(name + "_container_bo", "comboContentBorder"),
                                    comboContentBackground:self.generateStyle.generateStyleCombo(name + "_container_ba", "comboContentBackground"),
                                    activeColor:{
                                        name:name + "_container_ba_activeColor",
                                        type:'color',
                                        label:"Active BG Color",
                                        attrs:{
                                            class:"jsn-select-color jsn-activeColor input-small"
                                        }
                                    }
                                }
                            },
                            Foreground:{
                                title:"Foreground",
                                type:"fieldset",
                                fieldsetContent:{
                                    /*
                                     iconColor:{
                                     type:'color',
                                     label:"Icon Color",
                                     name:name + "_container_ic_iconColor",
                                     attrs:{
                                     'class':'jsn-select-color jsn-iconColor input-small'
                                     }
                                     }
                                     */
                                    iconColor:{
                                        type:'select',
                                        label:"Icon Color",
                                        name:name + "_container_ic_iconColor",
                                        options:{

                                            "white":"white",
                                            "black":"black"
                                        },
                                        attrs:{
                                            'class':'input-small'
                                        }
                                    }
                                }
                            },
							contentText:{
                                title:"Content",
                                type:"fieldset",
                                fieldsetContent:{
                                    linkColor:{
                                        name:name + "_container_rtl",
                                        label:"Text Direction",
                                        type:'select',
                                        options:{
										'left':'Left to Right',
										'right':'Right to Left',
										},
										attrs:{
											class:"select jsn-input-fluid"
										}
                                    }
                                }
                            }
                        }
                    },
                    sublevel1:{
                        title:"Sublevel 1",
                        type:"tab",
                        tabContent:{
                            background:{
                                title:"Background",
                                type:"fieldset",
                                fieldsetContent:{
                                    comboContent:self.generateStyle.generateStyleCombo(name + "_sublevel1_bo", "comboContent"),
                                    normalColor:{
                                        name:name + "_sublevel1_ba_normalColor",
                                        type:'color',
                                        label:"Normal BG Color",
                                        attrs:{
                                            class:"jsn-select-color jsn-normalColor input-small"
                                        }
                                    },
                                    activeColor:{
                                        name:name + "_sublevel1_ba_activeColor",
                                        type:'color',
                                        label:"Active BG Color",
                                        attrs:{
                                            class:"jsn-select-color jsn-activeColor input-small"
                                        }
                                    }
                                }
                            },
                            font:{
                                title:"Foreground",
                                type:"fieldset",
                                fieldsetContent:{
                                    comboContentFace:self.generateStyle.generateStyleCombo(name + "_sublevel1_fo", "comboContentFace"),
                                    comboContentAttributes:self.generateStyle.generateStyleCombo(name + "_sublevel1_fo", "comboContentAttributes")
                                }
                            }
                        }
                    },
                    sublevel2:{
                        title:"Sublevel 2",
                        type:"tab",
                        tabContent:{
                            background:{
                                title:"Background",
                                type:"fieldset",
                                fieldsetContent:{
                                    normalColor:{
                                        name:name + "_sublevel2_ba_normalColor",
                                        type:'color',
                                        label:"Normal BG Color",
                                        attrs:{
                                            class:"jsn-select-color jsn-normalColor input-small"
                                        }
                                    },
                                    activeColor:{
                                        name:name + "_sublevel2_ba_activeColor",
                                        type:'color',
                                        label:"Active BG Color",
                                        attrs:{
                                            class:"jsn-select-color jsn-activeColor input-small"
                                        }
                                    }
                                }
                            },
                            font:{
                                title:"Foreground",
                                type:"fieldset",
                                fieldsetContent:{
                                    comboContentFace:self.generateStyle.generateStyleCombo(name + "_sublevel2_fo", "comboContentFace"),
                                    comboContentAttributes:self.generateStyle.generateStyleCombo(name + "_sublevel2_fo", "comboContentAttributes")
                                }
                            }
                        }
                    }
                };
                return options;
            },
            profileStyleList:function () {
				var style = {
					simple:{
						title:"Simple",
						thumbnail:"components/com_mobilize/assets/images/thumbnail/simple.png",
						style:{
							'jsn_template':'[{"key":"jsn_template_container_ba_backgroundType","value":"Solid"},{"key":"jsn_template_container_ba_soildColor","value":""},{"key":"jsn_template_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_template_container_image","value":""},{"key":"jsn_template_container_effectColor","value":""},{"key":"jsn_template_container_opacity","value":"0.1"},{"key":"jsn_template_container_imageWidth","value":"100%"},{"key":"jsn_template_container_imageHeight","value":"auto"},{"key":"jsn_template_container_sp_paddingleft","value":"0"},{"key":"jsn_template_container_sp_paddingright","value":"0"},{"key":"jsn_template_container_sp_paddingbottom","value":"0"},{"key":"jsn_template_container_sp_paddingtop","value":"0"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_title_fo_fontSize","value":""},{"key":"jsn_template_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_body_fo_fontSize","value":""},{"key":"jsn_template_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_template_content_link_linkColor","value":""}]',
							'jsn_menu':'[{"key":"jsn_menu_container_bo_borderThickness","value":"0"},{"key":"jsn_menu_container_bo_borderStyle","value":"dotted"},{"key":"jsn_menu_container_bo_borderColor","value":"#ffffff"},{"key":"jsn_menu_container_ba_backgroundType","value":"Solid"},{"key":"jsn_menu_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_menu_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_menu_container_ba_activeColor","value":"#474747"},{"key":"jsn_menu_container_ic_iconColor","value":"black"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderThickness","value":"0"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderStyle","value":"solid"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderColor","value":"#ffffff"},{"key":"jsn_menu_sublevel1_ba_normalColor","value":"#ffffff"},{"key":"jsn_menu_sublevel1_ba_activeColor","value":"#43b2d1"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel1_fo_fontSize","value":""},{"key":"jsn_menu_sublevel1_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel1_fo_fontColor","value":"#000000"},{"key":"jsn_menu_sublevel2_ba_normalColor","value":"#ffffff"},{"key":"jsn_menu_sublevel2_ba_activeColor","value":""},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel2_fo_fontSize","value":""},{"key":"jsn_menu_sublevel2_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel2_fo_fontColor","value":"#000000"}]',
							'jsn_mobile_tool':'[{"key":"jsn_mobile_tool_container_ba_backgroundType","value":"Solid"},{"key":"jsn_mobile_tool_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_mobile_tool_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_container_image","value":""},{"key":"jsn_mobile_tool_container_effectColor","value":""},{"key":"jsn_mobile_tool_container_opacity","value":"0.1"},{"key":"jsn_mobile_tool_container_imageWidth","value":"100%"},{"key":"jsn_mobile_tool_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_title_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_text_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_mobile_tool_container_link_linkColor","value":""},{"key":"jsn_mobile_tool_container_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_container_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_container_bo_borderColor","value":""},{"key":"jsn_mobile_tool_container_sp_marginleft","value":""},{"key":"jsn_mobile_tool_container_sp_marginright","value":""},{"key":"jsn_mobile_tool_container_sp_marginbottom","value":""},{"key":"jsn_mobile_tool_container_sp_margintop","value":""},{"key":"jsn_mobile_tool_container_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_container_sp_paddingright","value":""},{"key":"jsn_mobile_tool_container_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_container_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_mobile_tool_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_margintop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_container_rtl","value":"left"}]',
							'jsn_content_top':'[{"key":"jsn_content_top_container_ba_backgroundType","value":"Solid"},{"key":"jsn_content_top_container_ba_soildColor","value":"#404040"},{"key":"jsn_content_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_container_image","value":""},{"key":"jsn_content_top_container_effectColor","value":""},{"key":"jsn_content_top_container_opacity","value":"0.1"},{"key":"jsn_content_top_container_imageWidth","value":"100%"},{"key":"jsn_content_top_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_content_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_container_title_fo_fontSize","value":""},{"key":"jsn_content_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_container_text_fo_fontSize","value":""},{"key":"jsn_content_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_top_container_link_linkColor","value":""},{"key":"jsn_content_top_container_bo_borderThickness","value":"0"},{"key":"jsn_content_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_container_bo_borderColor","value":""},{"key":"jsn_content_top_container_sp_marginleft","value":""},{"key":"jsn_content_top_container_sp_marginright","value":""},{"key":"jsn_content_top_container_sp_marginbottom","value":""},{"key":"jsn_content_top_container_sp_margintop","value":""},{"key":"jsn_content_top_container_sp_paddingleft","value":""},{"key":"jsn_content_top_container_sp_paddingright","value":""},{"key":"jsn_content_top_container_sp_paddingbottom","value":""},{"key":"jsn_content_top_container_sp_paddingtop","value":""},{"key":"jsn_content_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_top_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginright","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_content_top_module_tabContainer_sp_margintop","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_user_top':'[{"key":"jsn_user_top_container_ba_backgroundType","value":"Solid"},{"key":"jsn_user_top_container_ba_soildColor","value":"#f5f5f5"},{"key":"jsn_user_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_container_image","value":""},{"key":"jsn_user_top_container_effectColor","value":""},{"key":"jsn_user_top_container_opacity","value":"0.1"},{"key":"jsn_user_top_container_imageWidth","value":"100%"},{"key":"jsn_user_top_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_title_fo_fontSize","value":""},{"key":"jsn_user_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_text_fo_fontSize","value":""},{"key":"jsn_user_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_top_container_link_linkColor","value":""},{"key":"jsn_user_top_container_bo_borderThickness","value":"0"},{"key":"jsn_user_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_container_bo_borderColor","value":""},{"key":"jsn_user_top_container_sp_marginleft","value":""},{"key":"jsn_user_top_container_sp_marginright","value":""},{"key":"jsn_user_top_container_sp_marginbottom","value":""},{"key":"jsn_user_top_container_sp_margintop","value":""},{"key":"jsn_user_top_container_sp_paddingleft","value":""},{"key":"jsn_user_top_container_sp_paddingright","value":""},{"key":"jsn_user_top_container_sp_paddingbottom","value":""},{"key":"jsn_user_top_container_sp_paddingtop","value":""},{"key":"jsn_user_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_top_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginright","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_user_top_module_tabContainer_sp_margintop","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_mainbody':'[{"key":"jsn_mainbody_container_bo_borderThickness","value":"0"},{"key":"jsn_mainbody_container_bo_borderStyle","value":"solid"},{"key":"jsn_mainbody_container_bo_borderColor","value":"#ffffff"},{"key":"jsn_mainbody_container_ba_backgroundType","value":"Solid"},{"key":"jsn_mainbody_container_ba_soildColor","value":""},{"key":"jsn_mainbody_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mainbody_container_sh_shadowSpread","value":""},{"key":"jsn_mainbody_container_sh_shadowColor","value":""},{"key":"jsn_mainbody_container_bo_roundedCornerRadius","value":""},{"key":"jsn_mainbody_container_sp_paddingleft","value":"20"},{"key":"jsn_mainbody_container_sp_paddingright","value":"20"},{"key":"jsn_mainbody_container_sp_paddingbottom","value":"10"},{"key":"jsn_mainbody_container_sp_paddingtop","value":"10"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFace","value":"Arial"},{"key":"jsn_mainbody_content_title_fo_fontSize","value":"20"},{"key":"jsn_mainbody_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFace","value":"Verdana"},{"key":"jsn_mainbody_content_body_fo_fontSize","value":""},{"key":"jsn_mainbody_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_mainbody_content_link_linkColor","value":""}]',
							'jsn_user_bottom':'[{"key":"jsn_user_bottom_container_ba_backgroundType","value":"Solid"},{"key":"jsn_user_bottom_container_ba_soildColor","value":"#555555"},{"key":"jsn_user_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_container_image","value":""},{"key":"jsn_user_bottom_container_effectColor","value":""},{"key":"jsn_user_bottom_container_opacity","value":"0.1"},{"key":"jsn_user_bottom_container_imageWidth","value":"100%"},{"key":"jsn_user_bottom_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_bottom_container_link_linkColor","value":""},{"key":"jsn_user_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_container_bo_borderColor","value":""},{"key":"jsn_user_bottom_container_sp_marginleft","value":""},{"key":"jsn_user_bottom_container_sp_marginright","value":""},{"key":"jsn_user_bottom_container_sp_marginbottom","value":""},{"key":"jsn_user_bottom_container_sp_margintop","value":""},{"key":"jsn_user_bottom_container_sp_paddingleft","value":""},{"key":"jsn_user_bottom_container_sp_paddingright","value":""},{"key":"jsn_user_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_user_bottom_container_sp_paddingtop","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginright","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_margintop","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_content_bottom':'[{"key":"jsn_content_bottom_container_ba_backgroundType","value":"Solid"},{"key":"jsn_content_bottom_container_ba_soildColor","value":"#f5f5f5"},{"key":"jsn_content_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_container_image","value":""},{"key":"jsn_content_bottom_container_effectColor","value":""},{"key":"jsn_content_bottom_container_opacity","value":"0.1"},{"key":"jsn_content_bottom_container_imageWidth","value":"100%"},{"key":"jsn_content_bottom_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_bottom_container_link_linkColor","value":""},{"key":"jsn_content_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_container_bo_borderColor","value":""},{"key":"jsn_content_bottom_container_sp_marginleft","value":""},{"key":"jsn_content_bottom_container_sp_marginright","value":""},{"key":"jsn_content_bottom_container_sp_marginbottom","value":""},{"key":"jsn_content_bottom_container_sp_margintop","value":""},{"key":"jsn_content_bottom_container_sp_paddingleft","value":""},{"key":"jsn_content_bottom_container_sp_paddingright","value":""},{"key":"jsn_content_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_content_bottom_container_sp_paddingtop","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginright","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_margintop","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_footer':'[{"key":"jsn_footer_container_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_container_ba_soildColor","value":"#333333"},{"key":"jsn_footer_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_container_image","value":""},{"key":"jsn_footer_container_effectColor","value":""},{"key":"jsn_footer_container_opacity","value":"0.1"},{"key":"jsn_footer_container_imageWidth","value":"100%"},{"key":"jsn_footer_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_title_fo_fontSize","value":""},{"key":"jsn_footer_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_text_fo_fontSize","value":""},{"key":"jsn_footer_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_footer_container_link_linkColor","value":""},{"key":"jsn_footer_container_bo_borderThickness","value":"0"},{"key":"jsn_footer_container_bo_borderStyle","value":"solid"},{"key":"jsn_footer_container_bo_borderColor","value":""},{"key":"jsn_footer_container_sp_marginleft","value":""},{"key":"jsn_footer_container_sp_marginright","value":""},{"key":"jsn_footer_container_sp_marginbottom","value":""},{"key":"jsn_footer_container_sp_margintop","value":""},{"key":"jsn_footer_container_sp_paddingleft","value":""},{"key":"jsn_footer_container_sp_paddingright","value":""},{"key":"jsn_footer_container_sp_paddingbottom","value":""},{"key":"jsn_footer_container_sp_paddingtop","value":""},{"key":"jsn_footer_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_footer_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_footer_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_footer_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginright","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_margintop","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_switcher':'[{"key":"jsn_switcher_container_bo_borderThickness","value":"0"},{"key":"jsn_switcher_container_bo_borderStyle","value":"solid"},{"key":"jsn_switcher_container_bo_borderColor","value":""},{"key":"jsn_switcher_container_ba_backgroundType","value":"Solid"},{"key":"jsn_switcher_container_ba_soildColor","value":"#333333"},{"key":"jsn_switcher_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_switcher_container_sp_paddingleft","value":""},{"key":"jsn_switcher_container_sp_paddingright","value":""},{"key":"jsn_switcher_container_sp_paddingbottom","value":""},{"key":"jsn_switcher_container_sp_paddingtop","value":""}]'
						}
					},
					flat:{
						title:"Flat",
						thumbnail:"components/com_mobilize/assets/images/thumbnail/flat.png",
						style:{
							'jsn_template':'[{"key":"jsn_template_container_ba_backgroundType","value":"Solid"},{"key":"jsn_template_container_ba_soildColor","value":""},{"key":"jsn_template_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_template_container_image","value":""},{"key":"jsn_template_container_effectColor","value":""},{"key":"jsn_template_container_opacity","value":"0.1"},{"key":"jsn_template_container_imageWidth","value":"100%"},{"key":"jsn_template_container_imageHeight","value":"auto"},{"key":"jsn_template_container_sp_paddingleft","value":"0"},{"key":"jsn_template_container_sp_paddingright","value":"0"},{"key":"jsn_template_container_sp_paddingbottom","value":"0"},{"key":"jsn_template_container_sp_paddingtop","value":"0"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_title_fo_fontSize","value":""},{"key":"jsn_template_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_body_fo_fontSize","value":""},{"key":"jsn_template_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_template_content_link_linkColor","value":""}]',
							'jsn_menu':'[{"key":"jsn_menu_container_bo_borderThickness","value":"0"},{"key":"jsn_menu_container_bo_borderStyle","value":"solid"},{"key":"jsn_menu_container_bo_borderColor","value":""},{"key":"jsn_menu_container_ba_backgroundType","value":"Solid"},{"key":"jsn_menu_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_menu_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_menu_container_ba_activeColor","value":"#404040"},{"key":"jsn_menu_container_ic_iconColor","value":"black"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderThickness","value":"0"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderStyle","value":"solid"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderColor","value":""},{"key":"jsn_menu_sublevel1_ba_normalColor","value":"#333333"},{"key":"jsn_menu_sublevel1_ba_activeColor","value":""},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel1_fo_fontSize","value":""},{"key":"jsn_menu_sublevel1_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel1_fo_fontColor","value":"#000000"},{"key":"jsn_menu_sublevel2_ba_normalColor","value":""},{"key":"jsn_menu_sublevel2_ba_activeColor","value":""},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel2_fo_fontSize","value":""},{"key":"jsn_menu_sublevel2_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel2_fo_fontColor","value":"#000000"}]',
							'jsn_mobile_tool':'[{"key":"jsn_mobile_tool_container_ba_backgroundType","value":"Solid"},{"key":"jsn_mobile_tool_container_ba_soildColor","value":"#34495e"},{"key":"jsn_mobile_tool_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_container_image","value":""},{"key":"jsn_mobile_tool_container_effectColor","value":""},{"key":"jsn_mobile_tool_container_opacity","value":"0.1"},{"key":"jsn_mobile_tool_container_imageWidth","value":"100%"},{"key":"jsn_mobile_tool_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_title_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_text_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_mobile_tool_container_link_linkColor","value":""},{"key":"jsn_mobile_tool_container_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_container_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_container_bo_borderColor","value":""},{"key":"jsn_mobile_tool_container_sp_marginleft","value":""},{"key":"jsn_mobile_tool_container_sp_marginright","value":""},{"key":"jsn_mobile_tool_container_sp_marginbottom","value":""},{"key":"jsn_mobile_tool_container_sp_margintop","value":""},{"key":"jsn_mobile_tool_container_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_container_sp_paddingright","value":""},{"key":"jsn_mobile_tool_container_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_container_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_mobile_tool_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_margintop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_container_rtl","value":"left"}]',
							'jsn_content_top':'[{"key":"jsn_content_top_container_bo_borderThickness","value":"0"},{"key":"jsn_content_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_container_bo_borderColor","value":""},{"key":"jsn_content_top_container_ba_backgroundType","value":"Solid"},{"key":"jsn_content_top_container_ba_soildColor","value":"#3498db"},{"key":"jsn_content_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #cbdef2 0%, #f0e8ef 47%, #e1f7ec 100%)"},{"key":"jsn_content_top_container_sp_paddingleft","value":""},{"key":"jsn_content_top_container_sp_paddingright","value":""},{"key":"jsn_content_top_container_sp_paddingbottom","value":""},{"key":"jsn_content_top_container_sp_paddingtop","value":""},{"key":"jsn_content_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_top_module_tabContainer_ba_backgroundType","value":"Gradient"},{"key":"jsn_content_top_module_tabContainer_ba_soildColor","value":"#facd52"},{"key":"jsn_content_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #fcfcfc 0%, #f2f2f2 1%, #f0f0f0 98%, #ffffff 100%)"},{"key":"jsn_content_top_module_tabContainer_bo_roundedCornerRadius","value":"5"},{"key":"jsn_content_top_module_tabContainer_sh_shadowSpread","value":"2"},{"key":"jsn_content_top_module_tabContainer_sh_shadowColor","value":"#202930"},{"key":"jsn_content_top_module_tabContainer_sp_marginleft","value":"5"},{"key":"jsn_content_top_module_tabContainer_sp_marginright","value":"5"},{"key":"jsn_content_top_module_tabContainer_sp_marginbottom","value":"15"},{"key":"jsn_content_top_module_tabContainer_sp_margintop","value":"15"},{"key":"jsn_content_top_module_tabContainer_sp_paddingleft","value":"15"},{"key":"jsn_content_top_module_tabContainer_sp_paddingright","value":"15"},{"key":"jsn_content_top_module_tabContainer_sp_paddingbottom","value":"10"},{"key":"jsn_content_top_module_tabContainer_sp_paddingtop","value":"10"},{"value":""},{"value":""},{"key":"jsn_content_top_module_tabContent_title_fo_fontFaceType","value":"google fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_module_tabContent_title_fo_fontFace","value":"Open Sans"},{"key":"jsn_content_top_module_tabContent_title_fo_fontSize","value":"18"},{"key":"jsn_content_top_module_tabContent_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_module_tabContent_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_top_module_tabContent_body_fo_fontFaceType","value":"google fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_module_tabContent_body_fo_fontFace","value":"Open Sans"},{"key":"jsn_content_top_module_tabContent_body_fo_fontSize","value":""},{"key":"jsn_content_top_module_tabContent_body_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_module_tabContent_body_fo_fontColor","value":"#6b6b6b"},{"key":"jsn_content_top_module_tabContent_link_linkColor","value":""}]',
							'jsn_user_top':'[{"key":"jsn_user_top_container_ba_backgroundType","value":"Solid"},{"key":"jsn_user_top_container_ba_soildColor","value":"#2ecc70"},{"key":"jsn_user_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_container_image","value":""},{"key":"jsn_user_top_container_effectColor","value":""},{"key":"jsn_user_top_container_opacity","value":"0.1"},{"key":"jsn_user_top_container_imageWidth","value":"100%"},{"key":"jsn_user_top_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_title_fo_fontSize","value":""},{"key":"jsn_user_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_text_fo_fontSize","value":""},{"key":"jsn_user_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_top_container_link_linkColor","value":""},{"key":"jsn_user_top_container_bo_borderThickness","value":"0"},{"key":"jsn_user_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_container_bo_borderColor","value":""},{"key":"jsn_user_top_container_sp_marginleft","value":""},{"key":"jsn_user_top_container_sp_marginright","value":""},{"key":"jsn_user_top_container_sp_marginbottom","value":""},{"key":"jsn_user_top_container_sp_margintop","value":""},{"key":"jsn_user_top_container_sp_paddingleft","value":""},{"key":"jsn_user_top_container_sp_paddingright","value":""},{"key":"jsn_user_top_container_sp_paddingbottom","value":""},{"key":"jsn_user_top_container_sp_paddingtop","value":""},{"key":"jsn_user_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_top_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginright","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_user_top_module_tabContainer_sp_margintop","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_mainbody':'[{"key":"jsn_mainbody_container_bo_borderThickness","value":"0"},{"key":"jsn_mainbody_container_bo_borderStyle","value":"dotted"},{"key":"jsn_mainbody_container_bo_borderColor","value":"#ffffff"},{"key":"jsn_mainbody_container_ba_backgroundType","value":"Solid"},{"key":"jsn_mainbody_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_mainbody_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mainbody_container_sh_shadowSpread","value":""},{"key":"jsn_mainbody_container_sh_shadowColor","value":"#ffffff"},{"key":"jsn_mainbody_container_bo_roundedCornerRadius","value":""},{"key":"jsn_mainbody_container_sp_paddingleft","value":"20"},{"key":"jsn_mainbody_container_sp_paddingright","value":"20"},{"key":"jsn_mainbody_container_sp_paddingbottom","value":"10"},{"key":"jsn_mainbody_container_sp_paddingtop","value":"10"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFaceType","value":"google fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFace","value":"Lato"},{"key":"jsn_mainbody_content_title_fo_fontSize","value":"24"},{"key":"jsn_mainbody_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_title_fo_fontColor","value":"#2195cf"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFaceType","value":"google fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFace","value":"Open Sans"},{"key":"jsn_mainbody_content_body_fo_fontSize","value":""},{"key":"jsn_mainbody_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_body_fo_fontColor","value":"#333333"},{"key":"jsn_mainbody_content_link_linkColor","value":""}]',
							'jsn_user_bottom':'[{"key":"jsn_user_bottom_container_ba_backgroundType","value":"Solid"},{"key":"jsn_user_bottom_container_ba_soildColor","value":"#e74d3c"},{"key":"jsn_user_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_container_image","value":""},{"key":"jsn_user_bottom_container_effectColor","value":""},{"key":"jsn_user_bottom_container_opacity","value":"0.1"},{"key":"jsn_user_bottom_container_imageWidth","value":"100%"},{"key":"jsn_user_bottom_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_bottom_container_link_linkColor","value":""},{"key":"jsn_user_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_container_bo_borderColor","value":""},{"key":"jsn_user_bottom_container_sp_marginleft","value":""},{"key":"jsn_user_bottom_container_sp_marginright","value":""},{"key":"jsn_user_bottom_container_sp_marginbottom","value":""},{"key":"jsn_user_bottom_container_sp_margintop","value":""},{"key":"jsn_user_bottom_container_sp_paddingleft","value":""},{"key":"jsn_user_bottom_container_sp_paddingright","value":""},{"key":"jsn_user_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_user_bottom_container_sp_paddingtop","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginright","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_margintop","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_content_bottom':'[{"key":"jsn_content_bottom_container_ba_backgroundType","value":"Solid"},{"key":"jsn_content_bottom_container_ba_soildColor","value":"#1abc9c"},{"key":"jsn_content_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_container_image","value":""},{"key":"jsn_content_bottom_container_effectColor","value":""},{"key":"jsn_content_bottom_container_opacity","value":"0.1"},{"key":"jsn_content_bottom_container_imageWidth","value":"100%"},{"key":"jsn_content_bottom_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_bottom_container_link_linkColor","value":""},{"key":"jsn_content_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_container_bo_borderColor","value":""},{"key":"jsn_content_bottom_container_sp_marginleft","value":""},{"key":"jsn_content_bottom_container_sp_marginright","value":""},{"key":"jsn_content_bottom_container_sp_marginbottom","value":""},{"key":"jsn_content_bottom_container_sp_margintop","value":""},{"key":"jsn_content_bottom_container_sp_paddingleft","value":""},{"key":"jsn_content_bottom_container_sp_paddingright","value":""},{"key":"jsn_content_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_content_bottom_container_sp_paddingtop","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginright","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_margintop","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_footer':'[{"key":"jsn_footer_container_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_container_ba_soildColor","value":"#2c3e50"},{"key":"jsn_footer_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_container_image","value":""},{"key":"jsn_footer_container_effectColor","value":""},{"key":"jsn_footer_container_opacity","value":"0.1"},{"key":"jsn_footer_container_imageWidth","value":"100%"},{"key":"jsn_footer_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_title_fo_fontSize","value":""},{"key":"jsn_footer_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_text_fo_fontSize","value":""},{"key":"jsn_footer_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_footer_container_link_linkColor","value":""},{"key":"jsn_footer_container_bo_borderThickness","value":"0"},{"key":"jsn_footer_container_bo_borderStyle","value":"solid"},{"key":"jsn_footer_container_bo_borderColor","value":""},{"key":"jsn_footer_container_sp_marginleft","value":""},{"key":"jsn_footer_container_sp_marginright","value":""},{"key":"jsn_footer_container_sp_marginbottom","value":""},{"key":"jsn_footer_container_sp_margintop","value":""},{"key":"jsn_footer_container_sp_paddingleft","value":""},{"key":"jsn_footer_container_sp_paddingright","value":""},{"key":"jsn_footer_container_sp_paddingbottom","value":""},{"key":"jsn_footer_container_sp_paddingtop","value":""},{"key":"jsn_footer_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_footer_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_footer_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_footer_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginright","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_margintop","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_switcher':'[{"key":"jsn_switcher_container_bo_borderThickness","value":"0"},{"key":"jsn_switcher_container_bo_borderStyle","value":"solid"},{"key":"jsn_switcher_container_bo_borderColor","value":""},{"key":"jsn_switcher_container_ba_backgroundType","value":"Solid"},{"key":"jsn_switcher_container_ba_soildColor","value":"#2c3e50"},{"key":"jsn_switcher_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_switcher_container_sp_paddingleft","value":""},{"key":"jsn_switcher_container_sp_paddingright","value":""},{"key":"jsn_switcher_container_sp_paddingbottom","value":""},{"key":"jsn_switcher_container_sp_paddingtop","value":""}]'
						}
					},
					retro:{
						title:"Retro",
						thumbnail:"components/com_mobilize/assets/images/thumbnail/metro.png",
						style:{
							'jsn_template':'[{"key":"jsn_template_container_ba_backgroundType","value":"Solid"},{"key":"jsn_template_container_ba_soildColor","value":""},{"key":"jsn_template_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_template_container_image","value":""},{"key":"jsn_template_container_effectColor","value":""},{"key":"jsn_template_container_opacity","value":"0.1"},{"key":"jsn_template_container_imageWidth","value":"100%"},{"key":"jsn_template_container_imageHeight","value":"auto"},{"key":"jsn_template_container_sp_paddingleft","value":"0"},{"key":"jsn_template_container_sp_paddingright","value":"0"},{"key":"jsn_template_container_sp_paddingbottom","value":"0"},{"key":"jsn_template_container_sp_paddingtop","value":"0"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_title_fo_fontSize","value":""},{"key":"jsn_template_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_body_fo_fontSize","value":""},{"key":"jsn_template_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_template_content_link_linkColor","value":""}]',
							'jsn_menu':'[{"key":"jsn_menu_container_bo_borderThickness","value":"0"},{"key":"jsn_menu_container_bo_borderStyle","value":"dotted"},{"key":"jsn_menu_container_bo_borderColor","value":"#ffffff"},{"key":"jsn_menu_container_ba_backgroundType","value":"Solid"},{"key":"jsn_menu_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_menu_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_menu_container_ba_activeColor","value":"#474747"},{"key":"jsn_menu_container_ic_iconColor","value":"black"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderThickness","value":"0"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderStyle","value":"solid"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderColor","value":"#ffffff"},{"key":"jsn_menu_sublevel1_ba_normalColor","value":"#ffffff"},{"key":"jsn_menu_sublevel1_ba_activeColor","value":"#43b2d1"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel1_fo_fontSize","value":""},{"key":"jsn_menu_sublevel1_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel1_fo_fontColor","value":"#000000"},{"key":"jsn_menu_sublevel2_ba_normalColor","value":"#ffffff"},{"key":"jsn_menu_sublevel2_ba_activeColor","value":""},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel2_fo_fontSize","value":""},{"key":"jsn_menu_sublevel2_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel2_fo_fontColor","value":"#000000"}]',
							'jsn_mobile_tool':'[{"key":"jsn_mobile_tool_container_ba_backgroundType","value":"img"},{"key":"jsn_mobile_tool_container_ba_soildColor","value":""},{"key":"jsn_mobile_tool_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/retroTl.png"},{"key":"jsn_mobile_tool_container_effectColor","value":""},{"key":"jsn_mobile_tool_container_opacity","value":"0.1"},{"key":"jsn_mobile_tool_container_imageWidth","value":"auto"},{"key":"jsn_mobile_tool_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_title_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_text_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_mobile_tool_container_link_linkColor","value":""},{"key":"jsn_mobile_tool_container_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_container_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_container_bo_borderColor","value":""},{"key":"jsn_mobile_tool_container_sp_marginleft","value":""},{"key":"jsn_mobile_tool_container_sp_marginright","value":""},{"key":"jsn_mobile_tool_container_sp_marginbottom","value":""},{"key":"jsn_mobile_tool_container_sp_margintop","value":""},{"key":"jsn_mobile_tool_container_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_container_sp_paddingright","value":""},{"key":"jsn_mobile_tool_container_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_container_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_mobile_tool_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_margintop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_container_rtl","value":"left"}]',
							'jsn_content_top':'[{"key":"jsn_content_top_container_ba_backgroundType","value":"img"},{"key":"jsn_content_top_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_content_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/retroCt.png"},{"key":"jsn_content_top_container_effectColor","value":""},{"key":"jsn_content_top_container_opacity","value":"0.1"},{"key":"jsn_content_top_container_imageWidth","value":"auto"},{"key":"jsn_content_top_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_content_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_container_title_fo_fontSize","value":""},{"key":"jsn_content_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_container_text_fo_fontSize","value":""},{"key":"jsn_content_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_top_container_link_linkColor","value":""},{"key":"jsn_content_top_container_bo_borderThickness","value":"0"},{"key":"jsn_content_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_container_bo_borderColor","value":""},{"key":"jsn_content_top_container_sp_marginleft","value":""},{"key":"jsn_content_top_container_sp_marginright","value":""},{"key":"jsn_content_top_container_sp_marginbottom","value":""},{"key":"jsn_content_top_container_sp_margintop","value":""},{"key":"jsn_content_top_container_sp_paddingleft","value":""},{"key":"jsn_content_top_container_sp_paddingright","value":""},{"key":"jsn_content_top_container_sp_paddingbottom","value":""},{"key":"jsn_content_top_container_sp_paddingtop","value":""},{"key":"jsn_content_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_top_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginleft","value":"5"},{"key":"jsn_content_top_module_tabContainer_sp_marginright","value":"5"},{"key":"jsn_content_top_module_tabContainer_sp_marginbottom","value":"15"},{"key":"jsn_content_top_module_tabContainer_sp_margintop","value":"15"},{"key":"jsn_content_top_module_tabContainer_sp_paddingleft","value":"15"},{"key":"jsn_content_top_module_tabContainer_sp_paddingright","value":"15"},{"key":"jsn_content_top_module_tabContainer_sp_paddingbottom","value":"10"},{"key":"jsn_content_top_module_tabContainer_sp_paddingtop","value":"10"}]',
							'jsn_user_top':'[{"key":"jsn_user_top_container_ba_backgroundType","value":"img"},{"key":"jsn_user_top_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_user_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/retroUt.png"},{"key":"jsn_user_top_container_effectColor","value":""},{"key":"jsn_user_top_container_opacity","value":"0.1"},{"key":"jsn_user_top_container_imageWidth","value":"auto"},{"key":"jsn_user_top_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_title_fo_fontSize","value":""},{"key":"jsn_user_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_text_fo_fontSize","value":""},{"key":"jsn_user_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_top_container_link_linkColor","value":""},{"key":"jsn_user_top_container_bo_borderThickness","value":"0"},{"key":"jsn_user_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_container_bo_borderColor","value":""},{"key":"jsn_user_top_container_sp_marginleft","value":""},{"key":"jsn_user_top_container_sp_marginright","value":""},{"key":"jsn_user_top_container_sp_marginbottom","value":""},{"key":"jsn_user_top_container_sp_margintop","value":""},{"key":"jsn_user_top_container_sp_paddingleft","value":""},{"key":"jsn_user_top_container_sp_paddingright","value":""},{"key":"jsn_user_top_container_sp_paddingbottom","value":""},{"key":"jsn_user_top_container_sp_paddingtop","value":""},{"key":"jsn_user_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_top_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginleft","value":"5"},{"key":"jsn_user_top_module_tabContainer_sp_marginright","value":"5"},{"key":"jsn_user_top_module_tabContainer_sp_marginbottom","value":"15"},{"key":"jsn_user_top_module_tabContainer_sp_margintop","value":"15"},{"key":"jsn_user_top_module_tabContainer_sp_paddingleft","value":"15"},{"key":"jsn_user_top_module_tabContainer_sp_paddingright","value":"15"},{"key":"jsn_user_top_module_tabContainer_sp_paddingbottom","value":"10"},{"key":"jsn_user_top_module_tabContainer_sp_paddingtop","value":"10"}]',
							'jsn_mainbody':'[{"key":"jsn_mainbody_container_bo_borderThickness","value":"0"},{"key":"jsn_mainbody_container_bo_borderStyle","value":"dotted"},{"key":"jsn_mainbody_container_bo_borderColor","value":"#ffffff"},{"key":"jsn_mainbody_container_ba_backgroundType","value":"img"},{"key":"jsn_mobile_tool_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/retroBd.png"},{"key":"jsn_mainbody_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_mainbody_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mainbody_container_sh_shadowSpread","value":""},{"key":"jsn_mainbody_container_sh_shadowColor","value":"#ffffff"},{"key":"jsn_mainbody_container_bo_roundedCornerRadius","value":""},{"key":"jsn_mainbody_container_sp_paddingleft","value":"20"},{"key":"jsn_mainbody_container_sp_paddingright","value":"20"},{"key":"jsn_mainbody_container_sp_paddingbottom","value":"10"},{"key":"jsn_mainbody_container_sp_paddingtop","value":"10"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFaceType","value":"google fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFace","value":"Arvo"},{"key":"jsn_mainbody_content_title_fo_fontSize","value":"20"},{"key":"jsn_mainbody_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFaceType","value":"google fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFace","value":"Arvo"},{"key":"jsn_mainbody_content_body_fo_fontSize","value":""},{"key":"jsn_mainbody_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_mainbody_content_link_linkColor","value":""}]',
							'jsn_user_bottom':'[{"key":"jsn_user_bottom_container_ba_backgroundType","value":"img"},{"key":"jsn_user_bottom_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_user_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/retroUb.png"},{"key":"jsn_user_bottom_container_effectColor","value":""},{"key":"jsn_user_bottom_container_opacity","value":"0.1"},{"key":"jsn_user_bottom_container_imageWidth","value":"auto"},{"key":"jsn_user_bottom_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_bottom_container_link_linkColor","value":""},{"key":"jsn_user_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_container_bo_borderColor","value":""},{"key":"jsn_user_bottom_container_sp_marginleft","value":""},{"key":"jsn_user_bottom_container_sp_marginright","value":""},{"key":"jsn_user_bottom_container_sp_marginbottom","value":""},{"key":"jsn_user_bottom_container_sp_margintop","value":""},{"key":"jsn_user_bottom_container_sp_paddingleft","value":""},{"key":"jsn_user_bottom_container_sp_paddingright","value":""},{"key":"jsn_user_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_user_bottom_container_sp_paddingtop","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginleft","value":"5"},{"key":"jsn_user_bottom_module_tabContainer_sp_marginright","value":"5"},{"key":"jsn_user_bottom_module_tabContainer_sp_marginbottom","value":"15"},{"key":"jsn_user_bottom_module_tabContainer_sp_margintop","value":"15"},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingleft","value":"15"},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingright","value":"15"},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingbottom","value":"10"},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingtop","value":"10"}]',
							'jsn_content_bottom':'[{"key":"jsn_content_bottom_container_ba_backgroundType","value":"img"},{"key":"jsn_content_bottom_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_content_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/retroCb.png"},{"key":"jsn_content_bottom_container_effectColor","value":""},{"key":"jsn_content_bottom_container_opacity","value":"0.1"},{"key":"jsn_content_bottom_container_imageWidth","value":"auto"},{"key":"jsn_content_bottom_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_bottom_container_link_linkColor","value":""},{"key":"jsn_content_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_container_bo_borderColor","value":""},{"key":"jsn_content_bottom_container_sp_marginleft","value":""},{"key":"jsn_content_bottom_container_sp_marginright","value":""},{"key":"jsn_content_bottom_container_sp_marginbottom","value":""},{"key":"jsn_content_bottom_container_sp_margintop","value":""},{"key":"jsn_content_bottom_container_sp_paddingleft","value":""},{"key":"jsn_content_bottom_container_sp_paddingright","value":""},{"key":"jsn_content_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_content_bottom_container_sp_paddingtop","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginleft","value":"5"},{"key":"jsn_content_bottom_module_tabContainer_sp_marginright","value":"5"},{"key":"jsn_content_bottom_module_tabContainer_sp_marginbottom","value":"15"},{"key":"jsn_content_bottom_module_tabContainer_sp_margintop","value":"15"},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingleft","value":"15"},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingright","value":"15"},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingbottom","value":"10"},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingtop","value":"10"}]',
							'jsn_footer':'[{"key":"jsn_footer_container_ba_backgroundType","value":"img"},{"key":"jsn_footer_container_ba_soildColor","value":"#000000"},{"key":"jsn_footer_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/retroFt.png"},{"key":"jsn_footer_container_effectColor","value":""},{"key":"jsn_footer_container_opacity","value":"0.1"},{"key":"jsn_footer_container_imageWidth","value":"auto"},{"key":"jsn_footer_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_title_fo_fontSize","value":""},{"key":"jsn_footer_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_text_fo_fontSize","value":""},{"key":"jsn_footer_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_footer_container_link_linkColor","value":""},{"key":"jsn_footer_container_bo_borderThickness","value":"0"},{"key":"jsn_footer_container_bo_borderStyle","value":"solid"},{"key":"jsn_footer_container_bo_borderColor","value":""},{"key":"jsn_footer_container_sp_marginleft","value":""},{"key":"jsn_footer_container_sp_marginright","value":""},{"key":"jsn_footer_container_sp_marginbottom","value":""},{"key":"jsn_footer_container_sp_margintop","value":""},{"key":"jsn_footer_container_sp_paddingleft","value":""},{"key":"jsn_footer_container_sp_paddingright","value":""},{"key":"jsn_footer_container_sp_paddingbottom","value":""},{"key":"jsn_footer_container_sp_paddingtop","value":""},{"key":"jsn_footer_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_footer_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_footer_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_footer_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginleft","value":"5"},{"key":"jsn_footer_module_tabContainer_sp_marginright","value":"5"},{"key":"jsn_footer_module_tabContainer_sp_marginbottom","value":"15"},{"key":"jsn_footer_module_tabContainer_sp_margintop","value":"15"},{"key":"jsn_footer_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_switcher':'[{"key":"jsn_switcher_container_bo_borderThickness","value":"1"},{"key":"jsn_switcher_container_bo_borderStyle","value":"solid"},{"key":"jsn_switcher_container_bo_borderColor","value":"#2e2e2e"},{"key":"jsn_switcher_container_ba_backgroundType","value":"Solid"},{"key":"jsn_switcher_container_ba_soildColor","value":"#121212"},{"key":"jsn_switcher_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_switcher_container_sp_paddingleft","value":""},{"key":"jsn_switcher_container_sp_paddingright","value":""},{"key":"jsn_switcher_container_sp_paddingbottom","value":"10"},{"key":"jsn_switcher_container_sp_paddingtop","value":"10"}]'
						}
					},
					glass:{
						title:"Glass",
						thumbnail:"components/com_mobilize/assets/images/thumbnail/glass.png",
						style:{
							'jsn_template':'[{"key":"jsn_template_container_bo_border_radius","value":"5px"},{"key":"jsn_template_container_ba_backgroundType","value":"img"},{"key":"jsn_template_container_ba_soildColor","value":""},{"key":"jsn_template_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_template_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/glassBg.jpg"},{"key":"jsn_template_container_effectColor","value":""},{"key":"jsn_template_container_opacity","value":"0.1"},{"key":"jsn_template_container_imageWidth","value":"100%"},{"key":"jsn_template_container_imageHeight","value":"100%"},{"key":"jsn_template_container_sp_paddingleft","value":"20"},{"key":"jsn_template_container_sp_paddingright","value":"20"},{"key":"jsn_template_container_sp_paddingbottom","value":"20"},{"key":"jsn_template_container_sp_paddingtop","value":"20"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_title_fo_fontSize","value":""},{"key":"jsn_template_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_body_fo_fontSize","value":""},{"key":"jsn_template_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_template_content_link_linkColor","value":""}]',
							'jsn_menu':'[{"key":"jsn_menu_container_bo_borderThickness","value":"0"},{"key":"jsn_menu_container_bo_borderStyle","value":"hidden"},{"key":"jsn_menu_container_bo_borderColor","value":""},{"key":"jsn_menu_container_ba_backgroundType","value":"Solid"},{"key":"jsn_menu_container_ba_soildColor","value":"#282828"},{"key":"jsn_menu_container_ba_gradientColor","value":"-moz-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_menu_container_ba_activeColor","value":"#404040"},{"key":"jsn_menu_container_ic_iconColor","value":"#ffffff"},{"key":"jsn_menu_sublevel1_bo_borderThickness","value":"0"},{"key":"jsn_menu_sublevel1_bo_borderStyle","value":"hidden"},{"key":"jsn_menu_sublevel1_bo_borderColor","value":""},{"key":"jsn_menu_sublevel1_ba_normalColor","value":"#333333"},{"key":"jsn_menu_sublevel1_ba_activeColor","value":""},{"key":"jsn_menu_sublevel1_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel1_fo_fontSize","value":""},{"key":"jsn_menu_sublevel1_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel1_fo_fontColor","value":""},{"key":"jsn_menu_sublevel2_ba_normalColor","value":""},{"key":"jsn_menu_sublevel2_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel2_fo_fontSize","value":""},{"key":"jsn_menu_sublevel2_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel2_fo_fontColor","value":""}]',
							'jsn_mobile_tool':'[{"key":"jsn_mobile_tool_container_bo_borderrgb","value":"1px solid rgba(255,255,255,0.15)"},{"key":"jsn_mobile_tool_container_bo_border_radius","value":"5px"},{"key":"jsn_mobile_tool_container_ba_backgroundType","value":"img"},{"key":"jsn_mobile_tool_container_ba_soildColor","value":""},{"key":"jsn_mobile_tool_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_container_image","value":""},{"key":"jsn_mobile_tool_container_effectColor","value":"#ffffff"},{"key":"jsn_mobile_tool_container_opacity","value":"0.2"},{"key":"jsn_mobile_tool_container_imageWidth","value":"100%"},{"key":"jsn_mobile_tool_container_imageHeight","value":"100%"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_title_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_text_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_mobile_tool_container_link_linkColor","value":""},{"key":"jsn_mobile_tool_container_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_container_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_container_bo_borderColor","value":""},{"key":"jsn_mobile_tool_container_sp_marginleft","value":""},{"key":"jsn_mobile_tool_container_sp_marginright","value":""},{"key":"jsn_mobile_tool_container_sp_marginbottom","value":"0"},{"key":"jsn_mobile_tool_container_sp_margintop","value":"20"},{"key":"jsn_mobile_tool_container_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_container_sp_paddingright","value":""},{"key":"jsn_mobile_tool_container_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_container_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_mobile_tool_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_margintop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_container_rtl","value":"left"}]',
							'jsn_content_top':'[{"key":"jsn_content_top_container_bo_borderrgb","value":"1px solid rgba(255,255,255,0.15)"},{"key":"jsn_content_top_container_bo_border_radius","value":"5px"},{"key":"jsn_content_top_container_ba_backgroundType","value":"img"},{"key":"jsn_content_top_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_content_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_container_image","value":""},{"key":"jsn_content_top_container_effectColor","value":"#ffffff"},{"key":"jsn_content_top_container_opacity","value":"0.2"},{"key":"jsn_content_top_container_imageWidth","value":"100%"},{"key":"jsn_content_top_container_imageHeight","value":"100%"},{"value":""},{"value":""},{"key":"jsn_content_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_container_title_fo_fontSize","value":""},{"key":"jsn_content_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_container_text_fo_fontSize","value":""},{"key":"jsn_content_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_top_container_link_linkColor","value":""},{"key":"jsn_content_top_container_bo_borderThickness","value":"0"},{"key":"jsn_content_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_container_bo_borderColor","value":""},{"key":"jsn_content_top_container_sp_marginleft","value":""},{"key":"jsn_content_top_container_sp_marginright","value":""},{"key":"jsn_content_top_container_sp_marginbottom","value":"20"},{"key":"jsn_content_top_container_sp_margintop","value":"20"},{"key":"jsn_content_top_container_sp_paddingleft","value":""},{"key":"jsn_content_top_container_sp_paddingright","value":""},{"key":"jsn_content_top_container_sp_paddingbottom","value":""},{"key":"jsn_content_top_container_sp_paddingtop","value":""},{"key":"jsn_content_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_top_module_tabContainer_ba_soildColor","value":"#3e8ede"},{"key":"jsn_content_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginleft","value":"5"},{"key":"jsn_content_top_module_tabContainer_sp_marginright","value":"5"},{"key":"jsn_content_top_module_tabContainer_sp_marginbottom","value":"15"},{"key":"jsn_content_top_module_tabContainer_sp_margintop","value":"15"},{"key":"jsn_content_top_module_tabContainer_sp_paddingleft","value":"15"},{"key":"jsn_content_top_module_tabContainer_sp_paddingright","value":"15"},{"key":"jsn_content_top_module_tabContainer_sp_paddingbottom","value":"10"},{"key":"jsn_content_top_module_tabContainer_sp_paddingtop","value":"10"}]',
							'jsn_user_top':'[{"key":"jsn_user_top_container_bo_borderrgb","value":"1px solid rgba(255,255,255,0.15)"},{"key":"jsn_user_top_container_bo_border_radius","value":"5px"},{"key":"jsn_user_top_container_ba_backgroundType","value":"img"},{"key":"jsn_user_top_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_user_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_container_image","value":""},{"key":"jsn_user_top_container_effectColor","value":"#ffffff"},{"key":"jsn_user_top_container_opacity","value":"0.2"},{"key":"jsn_user_top_container_imageWidth","value":"100%"},{"key":"jsn_user_top_container_imageHeight","value":"100%"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_title_fo_fontSize","value":""},{"key":"jsn_user_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_text_fo_fontSize","value":""},{"key":"jsn_user_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_top_container_link_linkColor","value":""},{"key":"jsn_user_top_container_bo_borderThickness","value":"0"},{"key":"jsn_user_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_container_bo_borderColor","value":""},{"key":"jsn_user_top_container_sp_marginleft","value":""},{"key":"jsn_user_top_container_sp_marginright","value":""},{"key":"jsn_user_top_container_sp_marginbottom","value":"0"},{"key":"jsn_user_top_container_sp_margintop","value":"20"},{"key":"jsn_user_top_container_sp_paddingleft","value":""},{"key":"jsn_user_top_container_sp_paddingright","value":""},{"key":"jsn_user_top_container_sp_paddingbottom","value":""},{"key":"jsn_user_top_container_sp_paddingtop","value":""},{"key":"jsn_user_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_top_module_tabContainer_ba_soildColor","value":"#f5b236"},{"key":"jsn_user_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginleft","value":"5"},{"key":"jsn_user_top_module_tabContainer_sp_marginright","value":"5"},{"key":"jsn_user_top_module_tabContainer_sp_marginbottom","value":"15"},{"key":"jsn_user_top_module_tabContainer_sp_margintop","value":"15"},{"key":"jsn_user_top_module_tabContainer_sp_paddingleft","value":"15"},{"key":"jsn_user_top_module_tabContainer_sp_paddingright","value":"15"},{"key":"jsn_user_top_module_tabContainer_sp_paddingbottom","value":"10"},{"key":"jsn_user_top_module_tabContainer_sp_paddingtop","value":"10"}]',
							'jsn_mainbody':'[{"key":"jsn_mainbody_container_bo_borderThickness","value":"0"},{"key":"jsn_mainbody_container_bo_borderStyle","value":"dotted"},{"key":"jsn_mainbody_container_bo_borderColor","value":"#ffffff"},{"key":"jsn_mainbody_container_ba_backgroundType","value":"Solid"},{"key":"jsn_mainbody_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_mainbody_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mainbody_container_sh_shadowSpread","value":""},{"key":"jsn_mainbody_container_sh_shadowColor","value":"#ffffff"},{"key":"jsn_mainbody_container_bo_roundedCornerRadius","value":""},{"key":"jsn_mainbody_container_sp_paddingleft","value":"20"},{"key":"jsn_mainbody_container_sp_paddingright","value":"20"},{"key":"jsn_mainbody_container_sp_paddingbottom","value":"10"},{"key":"jsn_mainbody_container_sp_paddingtop","value":"10"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFaceType","value":"google fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFace","value":"Ubuntu"},{"key":"jsn_mainbody_content_title_fo_fontSize","value":"20"},{"key":"jsn_mainbody_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFace","value":"Verdana"},{"key":"jsn_mainbody_content_body_fo_fontSize","value":""},{"key":"jsn_mainbody_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_mainbody_content_link_linkColor","value":""}]',
							'jsn_user_bottom':'[{"key":"jsn_user_bottom_container_bo_borderrgb","value":"1px solid rgba(255,255,255,0.15)"},{"key":"jsn_user_bottom_container_bo_border_radius","value":"5px"},{"key":"jsn_user_bottom_container_ba_backgroundType","value":"img"},{"key":"jsn_user_bottom_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_user_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_container_image","value":""},{"key":"jsn_user_bottom_container_effectColor","value":"#ffffff"},{"key":"jsn_user_bottom_container_opacity","value":"0.2"},{"key":"jsn_user_bottom_container_imageWidth","value":"100%"},{"key":"jsn_user_bottom_container_imageHeight","value":"100%"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_bottom_container_link_linkColor","value":""},{"key":"jsn_user_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_container_bo_borderColor","value":""},{"key":"jsn_user_bottom_container_sp_marginleft","value":""},{"key":"jsn_user_bottom_container_sp_marginright","value":""},{"key":"jsn_user_bottom_container_sp_marginbottom","value":"0"},{"key":"jsn_user_bottom_container_sp_margintop","value":"20"},{"key":"jsn_user_bottom_container_sp_paddingleft","value":""},{"key":"jsn_user_bottom_container_sp_paddingright","value":""},{"key":"jsn_user_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_user_bottom_container_sp_paddingtop","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_bottom_module_tabContainer_ba_soildColor","value":"#41961c"},{"key":"jsn_user_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginleft","value":"5"},{"key":"jsn_user_bottom_module_tabContainer_sp_marginright","value":"5"},{"key":"jsn_user_bottom_module_tabContainer_sp_marginbottom","value":"15"},{"key":"jsn_user_bottom_module_tabContainer_sp_margintop","value":"15"},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingleft","value":"15"},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingright","value":"15"},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingbottom","value":"10"},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingtop","value":"10"}]',
							'jsn_content_bottom':'[{"key":"jsn_content_bottom_container_bo_borderrgb","value":"1px solid rgba(255,255,255,0.15)"},{"key":"jsn_content_bottom_container_bo_border_radius","value":"5px"},{"key":"jsn_content_bottom_container_ba_backgroundType","value":"img"},{"key":"jsn_content_bottom_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_content_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_container_image","value":""},{"key":"jsn_content_bottom_container_effectColor","value":"#ffffff"},{"key":"jsn_content_bottom_container_opacity","value":"0.2"},{"key":"jsn_content_bottom_container_imageWidth","value":"100%"},{"key":"jsn_content_bottom_container_imageHeight","value":"100%"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_bottom_container_link_linkColor","value":""},{"key":"jsn_content_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_container_bo_borderColor","value":""},{"key":"jsn_content_bottom_container_sp_marginleft","value":""},{"key":"jsn_content_bottom_container_sp_marginright","value":""},{"key":"jsn_content_bottom_container_sp_marginbottom","value":"0"},{"key":"jsn_content_bottom_container_sp_margintop","value":"20"},{"key":"jsn_content_bottom_container_sp_paddingleft","value":""},{"key":"jsn_content_bottom_container_sp_paddingright","value":""},{"key":"jsn_content_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_content_bottom_container_sp_paddingtop","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_bottom_module_tabContainer_ba_soildColor","value":"#ff5f29"},{"key":"jsn_content_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginleft","value":"5"},{"key":"jsn_content_bottom_module_tabContainer_sp_marginright","value":"5"},{"key":"jsn_content_bottom_module_tabContainer_sp_marginbottom","value":"15"},{"key":"jsn_content_bottom_module_tabContainer_sp_margintop","value":"15"},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingleft","value":"15"},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingright","value":"15"},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingbottom","value":"10"},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingtop","value":"10"}]',
							'jsn_footer':'[{"key":"jsn_footer_container_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_container_ba_soildColor","value":""},{"key":"jsn_footer_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_container_image","value":""},{"key":"jsn_footer_container_effectColor","value":""},{"key":"jsn_footer_container_opacity","value":"0.1"},{"key":"jsn_footer_container_imageWidth","value":"100%"},{"key":"jsn_footer_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_title_fo_fontSize","value":""},{"key":"jsn_footer_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_text_fo_fontSize","value":""},{"key":"jsn_footer_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_footer_container_link_linkColor","value":""},{"key":"jsn_footer_container_bo_borderThickness","value":"0"},{"key":"jsn_footer_container_bo_borderStyle","value":"solid"},{"key":"jsn_footer_container_bo_borderColor","value":""},{"key":"jsn_footer_container_sp_marginleft","value":""},{"key":"jsn_footer_container_sp_marginright","value":""},{"key":"jsn_footer_container_sp_marginbottom","value":""},{"key":"jsn_footer_container_sp_margintop","value":""},{"key":"jsn_footer_container_sp_paddingleft","value":""},{"key":"jsn_footer_container_sp_paddingright","value":""},{"key":"jsn_footer_container_sp_paddingbottom","value":""},{"key":"jsn_footer_container_sp_paddingtop","value":""},{"key":"jsn_footer_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_footer_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_footer_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_footer_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginright","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_margintop","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_switcher':'[{"key":"jsn_switcher_container_bo_borderThickness","value":"0"},{"key":"jsn_switcher_container_bo_borderStyle","value":"solid"},{"key":"jsn_switcher_container_bo_borderColor","value":""},{"key":"jsn_switcher_container_ba_backgroundType","value":"Solid"},{"key":"jsn_switcher_container_ba_soildColor","value":""},{"key":"jsn_switcher_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_switcher_container_sp_paddingleft","value":""},{"key":"jsn_switcher_container_sp_paddingright","value":""},{"key":"jsn_switcher_container_sp_paddingbottom","value":""},{"key":"jsn_switcher_container_sp_paddingtop","value":""}]'
						}
					},
					metro:{
						title:"Metro",
						thumbnail:"components/com_mobilize/assets/images/thumbnail/retro.png",
						style:{
							'jsn_template':'[{"key":"jsn_template_container_ba_backgroundType","value":"Solid"},{"key":"jsn_template_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_template_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_template_container_image","value":""},{"key":"jsn_template_container_effectColor","value":""},{"key":"jsn_template_container_opacity","value":"0.1"},{"key":"jsn_template_container_imageWidth","value":"100%"},{"key":"jsn_template_container_imageHeight","value":"auto"},{"key":"jsn_template_container_sp_paddingleft","value":"20"},{"key":"jsn_template_container_sp_paddingright","value":"20"},{"key":"jsn_template_container_sp_paddingbottom","value":"20"},{"key":"jsn_template_container_sp_paddingtop","value":"20"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_title_fo_fontSize","value":""},{"key":"jsn_template_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_body_fo_fontSize","value":""},{"key":"jsn_template_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_template_content_link_linkColor","value":""}]',
							'jsn_menu':'[{"key":"jsn_menu_container_bo_borderThickness","value":"0"},{"key":"jsn_menu_container_bo_borderStyle","value":"solid"},{"key":"jsn_menu_container_bo_borderColor","value":""},{"key":"jsn_menu_container_ba_backgroundType","value":"Solid"},{"key":"jsn_menu_container_ba_soildColor","value":"#131313"},{"key":"jsn_menu_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_menu_container_ba_activeColor","value":"#404040"},{"key":"jsn_menu_container_ic_iconColor","value":"white"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderThickness","value":"0"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderStyle","value":"solid"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderColor","value":""},{"key":"jsn_menu_sublevel1_ba_normalColor","value":"#333333"},{"key":"jsn_menu_sublevel1_ba_activeColor","value":""},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel1_fo_fontSize","value":""},{"key":"jsn_menu_sublevel1_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel1_fo_fontColor","value":"#000000"},{"key":"jsn_menu_sublevel2_ba_normalColor","value":""},{"key":"jsn_menu_sublevel2_ba_activeColor","value":""},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel2_fo_fontSize","value":""},{"key":"jsn_menu_sublevel2_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel2_fo_fontColor","value":"#000000"}]',
							'jsn_mobile_tool':'[{"key":"jsn_mobile_tool_container_ba_backgroundType","value":"Solid"},{"key":"jsn_mobile_tool_container_ba_soildColor","value":"#2b5697"},{"key":"jsn_mobile_tool_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_container_image","value":""},{"key":"jsn_mobile_tool_container_effectColor","value":""},{"key":"jsn_mobile_tool_container_opacity","value":"0.1"},{"key":"jsn_mobile_tool_container_imageWidth","value":"100%"},{"key":"jsn_mobile_tool_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_title_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_text_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_mobile_tool_container_link_linkColor","value":""},{"key":"jsn_mobile_tool_container_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_container_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_container_bo_borderColor","value":""},{"key":"jsn_mobile_tool_container_sp_marginleft","value":""},{"key":"jsn_mobile_tool_container_sp_marginright","value":""},{"key":"jsn_mobile_tool_container_sp_marginbottom","value":"10"},{"key":"jsn_mobile_tool_container_sp_margintop","value":"10"},{"key":"jsn_mobile_tool_container_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_container_sp_paddingright","value":""},{"key":"jsn_mobile_tool_container_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_container_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_mobile_tool_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_margintop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_container_rtl","value":"left"}]',
							'jsn_content_top':'[{"key":"jsn_content_top_container_ba_backgroundType","value":"Solid"},{"key":"jsn_content_top_container_ba_soildColor","value":"#9f00a7"},{"key":"jsn_content_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_container_image","value":""},{"key":"jsn_content_top_container_effectColor","value":""},{"key":"jsn_content_top_container_opacity","value":"0.1"},{"key":"jsn_content_top_container_imageWidth","value":"100%"},{"key":"jsn_content_top_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_content_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_container_title_fo_fontSize","value":""},{"key":"jsn_content_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_container_text_fo_fontSize","value":""},{"key":"jsn_content_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_top_container_link_linkColor","value":""},{"key":"jsn_content_top_container_bo_borderThickness","value":"0"},{"key":"jsn_content_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_container_bo_borderColor","value":""},{"key":"jsn_content_top_container_sp_marginleft","value":""},{"key":"jsn_content_top_container_sp_marginright","value":""},{"key":"jsn_content_top_container_sp_marginbottom","value":"10"},{"key":"jsn_content_top_container_sp_margintop","value":""},{"key":"jsn_content_top_container_sp_paddingleft","value":""},{"key":"jsn_content_top_container_sp_paddingright","value":""},{"key":"jsn_content_top_container_sp_paddingbottom","value":""},{"key":"jsn_content_top_container_sp_paddingtop","value":""},{"key":"jsn_content_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_top_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginright","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_content_top_module_tabContainer_sp_margintop","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_user_top':'[{"key":"jsn_user_top_container_ba_backgroundType","value":"Solid"},{"key":"jsn_user_top_container_ba_soildColor","value":"#00a300"},{"key":"jsn_user_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_container_image","value":""},{"key":"jsn_user_top_container_effectColor","value":""},{"key":"jsn_user_top_container_opacity","value":"0.1"},{"key":"jsn_user_top_container_imageWidth","value":"100%"},{"key":"jsn_user_top_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_title_fo_fontSize","value":""},{"key":"jsn_user_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_text_fo_fontSize","value":""},{"key":"jsn_user_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_top_container_link_linkColor","value":""},{"key":"jsn_user_top_container_bo_borderThickness","value":"0"},{"key":"jsn_user_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_container_bo_borderColor","value":""},{"key":"jsn_user_top_container_sp_marginleft","value":""},{"key":"jsn_user_top_container_sp_marginright","value":""},{"key":"jsn_user_top_container_sp_marginbottom","value":"10"},{"key":"jsn_user_top_container_sp_margintop","value":""},{"key":"jsn_user_top_container_sp_paddingleft","value":""},{"key":"jsn_user_top_container_sp_paddingright","value":""},{"key":"jsn_user_top_container_sp_paddingbottom","value":""},{"key":"jsn_user_top_container_sp_paddingtop","value":""},{"key":"jsn_user_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_top_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginright","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_user_top_module_tabContainer_sp_margintop","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_mainbody':'[{"key":"jsn_mainbody_container_bo_borderThickness","value":"0"},{"key":"jsn_mainbody_container_bo_borderStyle","value":"solid"},{"key":"jsn_mainbody_container_bo_borderColor","value":""},{"key":"jsn_mainbody_container_ba_backgroundType","value":"Solid"},{"key":"jsn_mainbody_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_mainbody_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mainbody_container_sh_shadowSpread","value":""},{"key":"jsn_mainbody_container_sh_shadowColor","value":""},{"key":"jsn_mainbody_container_bo_roundedCornerRadius","value":""},{"key":"jsn_mainbody_container_sp_paddingleft","value":"20"},{"key":"jsn_mainbody_container_sp_paddingright","value":"20"},{"key":"jsn_mainbody_container_sp_paddingbottom","value":"10"},{"key":"jsn_mainbody_container_sp_paddingtop","value":"10"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFaceType","value":"google fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFace","value":"Arvo"},{"key":"jsn_mainbody_content_title_fo_fontSize","value":"20"},{"key":"jsn_mainbody_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFace","value":"Verdana"},{"key":"jsn_mainbody_content_body_fo_fontSize","value":""},{"key":"jsn_mainbody_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_mainbody_content_link_linkColor","value":""}]',
							'jsn_user_bottom':'[{"key":"jsn_user_bottom_container_ba_backgroundType","value":"Solid"},{"key":"jsn_user_bottom_container_ba_soildColor","value":"#d21f46"},{"key":"jsn_user_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_container_image","value":""},{"key":"jsn_user_bottom_container_effectColor","value":""},{"key":"jsn_user_bottom_container_opacity","value":"0.1"},{"key":"jsn_user_bottom_container_imageWidth","value":"100%"},{"key":"jsn_user_bottom_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_bottom_container_link_linkColor","value":""},{"key":"jsn_user_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_container_bo_borderColor","value":""},{"key":"jsn_user_bottom_container_sp_marginleft","value":""},{"key":"jsn_user_bottom_container_sp_marginright","value":""},{"key":"jsn_user_bottom_container_sp_marginbottom","value":"10"},{"key":"jsn_user_bottom_container_sp_margintop","value":""},{"key":"jsn_user_bottom_container_sp_paddingleft","value":""},{"key":"jsn_user_bottom_container_sp_paddingright","value":""},{"key":"jsn_user_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_user_bottom_container_sp_paddingtop","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginright","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_margintop","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_content_bottom':'[{"key":"jsn_content_bottom_container_ba_backgroundType","value":"Solid"},{"key":"jsn_content_bottom_container_ba_soildColor","value":"#2b5697"},{"key":"jsn_content_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_container_image","value":""},{"key":"jsn_content_bottom_container_effectColor","value":""},{"key":"jsn_content_bottom_container_opacity","value":"0.1"},{"key":"jsn_content_bottom_container_imageWidth","value":"100%"},{"key":"jsn_content_bottom_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_bottom_container_link_linkColor","value":""},{"key":"jsn_content_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_container_bo_borderColor","value":""},{"key":"jsn_content_bottom_container_sp_marginleft","value":""},{"key":"jsn_content_bottom_container_sp_marginright","value":""},{"key":"jsn_content_bottom_container_sp_marginbottom","value":"10"},{"key":"jsn_content_bottom_container_sp_margintop","value":""},{"key":"jsn_content_bottom_container_sp_paddingleft","value":""},{"key":"jsn_content_bottom_container_sp_paddingright","value":""},{"key":"jsn_content_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_content_bottom_container_sp_paddingtop","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginright","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_margintop","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_footer':'[{"key":"jsn_footer_container_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_footer_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_container_image","value":""},{"key":"jsn_footer_container_effectColor","value":""},{"key":"jsn_footer_container_opacity","value":"0.1"},{"key":"jsn_footer_container_imageWidth","value":"100%"},{"key":"jsn_footer_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_title_fo_fontSize","value":""},{"key":"jsn_footer_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_text_fo_fontSize","value":""},{"key":"jsn_footer_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_footer_container_link_linkColor","value":""},{"key":"jsn_footer_container_bo_borderThickness","value":"0"},{"key":"jsn_footer_container_bo_borderStyle","value":"solid"},{"key":"jsn_footer_container_bo_borderColor","value":""},{"key":"jsn_footer_container_sp_marginleft","value":""},{"key":"jsn_footer_container_sp_marginright","value":""},{"key":"jsn_footer_container_sp_marginbottom","value":""},{"key":"jsn_footer_container_sp_margintop","value":""},{"key":"jsn_footer_container_sp_paddingleft","value":""},{"key":"jsn_footer_container_sp_paddingright","value":""},{"key":"jsn_footer_container_sp_paddingbottom","value":""},{"key":"jsn_footer_container_sp_paddingtop","value":""},{"key":"jsn_footer_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_footer_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_footer_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_footer_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginright","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_margintop","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_switcher':'[{"key":"jsn_switcher_container_bo_borderThickness","value":"0"},{"key":"jsn_switcher_container_bo_borderStyle","value":"solid"},{"key":"jsn_switcher_container_bo_borderColor","value":""},{"key":"jsn_switcher_container_ba_backgroundType","value":"Solid"},{"key":"jsn_switcher_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_switcher_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_switcher_container_sp_paddingleft","value":""},{"key":"jsn_switcher_container_sp_paddingright","value":""},{"key":"jsn_switcher_container_sp_paddingbottom","value":""},{"key":"jsn_switcher_container_sp_paddingtop","value":""}]'
						}
					},
					modern:{
						title:"Modern",
						thumbnail:"components/com_mobilize/assets/images/thumbnail/modern.png",
						style:{
							'jsn_template':'[{"key":"jsn_template_container_ba_backgroundType","value":"Solid"},{"key":"jsn_template_container_ba_soildColor","value":""},{"key":"jsn_template_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_template_container_image","value":""},{"key":"jsn_template_container_effectColor","value":""},{"key":"jsn_template_container_opacity","value":"0.1"},{"key":"jsn_template_container_imageWidth","value":"100%"},{"key":"jsn_template_container_imageHeight","value":"auto"},{"key":"jsn_template_container_sp_paddingleft","value":"0"},{"key":"jsn_template_container_sp_paddingright","value":"0"},{"key":"jsn_template_container_sp_paddingbottom","value":"0"},{"key":"jsn_template_container_sp_paddingtop","value":"0"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_title_fo_fontSize","value":""},{"key":"jsn_template_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_body_fo_fontSize","value":""},{"key":"jsn_template_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_template_content_link_linkColor","value":""}]',
							'jsn_menu':'[{"key":"jsn_menu_container_bo_borderThickness","value":"0"},{"key":"jsn_menu_container_bo_borderStyle","value":"solid"},{"key":"jsn_menu_container_bo_borderColor","value":""},{"key":"jsn_menu_container_ba_backgroundType","value":"Solid"},{"key":"jsn_menu_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_menu_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_menu_container_ba_activeColor","value":"#404040"},{"key":"jsn_menu_container_ic_iconColor","value":"black"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderThickness","value":"0"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderStyle","value":"solid"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderColor","value":""},{"key":"jsn_menu_sublevel1_ba_normalColor","value":"#ffffff"},{"key":"jsn_menu_sublevel1_ba_activeColor","value":""},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel1_fo_fontSize","value":""},{"key":"jsn_menu_sublevel1_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel1_fo_fontColor","value":"#000000"},{"key":"jsn_menu_sublevel2_ba_normalColor","value":""},{"key":"jsn_menu_sublevel2_ba_activeColor","value":""},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel2_fo_fontSize","value":""},{"key":"jsn_menu_sublevel2_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel2_fo_fontColor","value":"#000000"}]',
							'jsn_mobile_tool':'[{"key":"jsn_mobile_tool_container_ba_backgroundType","value":"img"},{"key":"jsn_mobile_tool_container_ba_soildColor","value":""},{"key":"jsn_mobile_tool_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/modernTl.jpg"},{"key":"jsn_mobile_tool_container_effectColor","value":""},{"key":"jsn_mobile_tool_container_opacity","value":"0.1"},{"key":"jsn_mobile_tool_container_imageWidth","value":"100%"},{"key":"jsn_mobile_tool_container_imageHeight","value":"100%"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_title_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_text_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_mobile_tool_container_link_linkColor","value":""},{"key":"jsn_mobile_tool_container_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_container_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_container_bo_borderColor","value":""},{"key":"jsn_mobile_tool_container_sp_marginleft","value":""},{"key":"jsn_mobile_tool_container_sp_marginright","value":""},{"key":"jsn_mobile_tool_container_sp_marginbottom","value":""},{"key":"jsn_mobile_tool_container_sp_margintop","value":""},{"key":"jsn_mobile_tool_container_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_container_sp_paddingright","value":""},{"key":"jsn_mobile_tool_container_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_container_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_mobile_tool_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_margintop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_container_rtl","value":""}]',
							'jsn_content_top':'[{"key":"jsn_content_top_container_ba_backgroundType","value":"img"},{"key":"jsn_content_top_container_ba_soildColor","value":"#404040"},{"key":"jsn_content_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/modernCt.jpg"},{"key":"jsn_content_top_container_effectColor","value":""},{"key":"jsn_content_top_container_opacity","value":"0.1"},{"key":"jsn_content_top_container_imageWidth","value":"100%"},{"key":"jsn_content_top_container_imageHeight","value":"100%"},{"value":""},{"value":""},{"key":"jsn_content_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_container_title_fo_fontSize","value":""},{"key":"jsn_content_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_container_text_fo_fontSize","value":""},{"key":"jsn_content_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_top_container_link_linkColor","value":""},{"key":"jsn_content_top_container_bo_borderThickness","value":"0"},{"key":"jsn_content_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_container_bo_borderColor","value":""},{"key":"jsn_content_top_container_sp_marginleft","value":""},{"key":"jsn_content_top_container_sp_marginright","value":""},{"key":"jsn_content_top_container_sp_marginbottom","value":""},{"key":"jsn_content_top_container_sp_margintop","value":""},{"key":"jsn_content_top_container_sp_paddingleft","value":""},{"key":"jsn_content_top_container_sp_paddingright","value":""},{"key":"jsn_content_top_container_sp_paddingbottom","value":""},{"key":"jsn_content_top_container_sp_paddingtop","value":""},{"key":"jsn_content_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_top_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginright","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_content_top_module_tabContainer_sp_margintop","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_user_top':'[{"key":"jsn_user_top_container_ba_backgroundType","value":"img"},{"key":"jsn_user_top_container_ba_soildColor","value":""},{"key":"jsn_user_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/modernUt.jpg"},{"key":"jsn_user_top_container_effectColor","value":""},{"key":"jsn_user_top_container_opacity","value":"0.1"},{"key":"jsn_user_top_container_imageWidth","value":"100%"},{"key":"jsn_user_top_container_imageHeight","value":"100%"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_title_fo_fontSize","value":""},{"key":"jsn_user_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_text_fo_fontSize","value":""},{"key":"jsn_user_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_top_container_link_linkColor","value":""},{"key":"jsn_user_top_container_bo_borderThickness","value":"0"},{"key":"jsn_user_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_container_bo_borderColor","value":""},{"key":"jsn_user_top_container_sp_marginleft","value":""},{"key":"jsn_user_top_container_sp_marginright","value":""},{"key":"jsn_user_top_container_sp_marginbottom","value":""},{"key":"jsn_user_top_container_sp_margintop","value":""},{"key":"jsn_user_top_container_sp_paddingleft","value":""},{"key":"jsn_user_top_container_sp_paddingright","value":""},{"key":"jsn_user_top_container_sp_paddingbottom","value":""},{"key":"jsn_user_top_container_sp_paddingtop","value":""},{"key":"jsn_user_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_top_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginright","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_user_top_module_tabContainer_sp_margintop","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_mainbody':'[{"key":"jsn_mainbody_container_bo_borderThickness","value":"0"},{"key":"jsn_mainbody_container_bo_borderStyle","value":"dotted"},{"key":"jsn_mainbody_container_bo_borderColor","value":"#ffffff"},{"key":"jsn_mainbody_container_ba_backgroundType","value":"Solid"},{"key":"jsn_mainbody_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_mainbody_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mainbody_container_sh_shadowSpread","value":""},{"key":"jsn_mainbody_container_sh_shadowColor","value":"#ffffff"},{"key":"jsn_mainbody_container_bo_roundedCornerRadius","value":""},{"key":"jsn_mainbody_container_sp_paddingleft","value":"20"},{"key":"jsn_mainbody_container_sp_paddingright","value":"20"},{"key":"jsn_mainbody_container_sp_paddingbottom","value":"10"},{"key":"jsn_mainbody_container_sp_paddingtop","value":"10"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFaceType","value":"google fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFace","value":"Open Sans"},{"key":"jsn_mainbody_content_title_fo_fontSize","value":"20"},{"key":"jsn_mainbody_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFace","value":"Verdana"},{"key":"jsn_mainbody_content_body_fo_fontSize","value":""},{"key":"jsn_mainbody_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_mainbody_content_link_linkColor","value":""}]',
							'jsn_user_bottom':'[{"key":"jsn_user_bottom_container_ba_backgroundType","value":"img"},{"key":"jsn_user_bottom_container_ba_soildColor","value":"#d9d9d9"},{"key":"jsn_user_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/modernUb.jpg"},{"key":"jsn_user_bottom_container_effectColor","value":""},{"key":"jsn_user_bottom_container_opacity","value":"0.1"},{"key":"jsn_user_bottom_container_imageWidth","value":"100%"},{"key":"jsn_user_bottom_container_imageHeight","value":"100%"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_bottom_container_link_linkColor","value":""},{"key":"jsn_user_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_container_bo_borderColor","value":""},{"key":"jsn_user_bottom_container_sp_marginleft","value":""},{"key":"jsn_user_bottom_container_sp_marginright","value":""},{"key":"jsn_user_bottom_container_sp_marginbottom","value":""},{"key":"jsn_user_bottom_container_sp_margintop","value":""},{"key":"jsn_user_bottom_container_sp_paddingleft","value":""},{"key":"jsn_user_bottom_container_sp_paddingright","value":""},{"key":"jsn_user_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_user_bottom_container_sp_paddingtop","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginright","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_margintop","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_content_bottom':'[{"key":"jsn_content_bottom_container_ba_backgroundType","value":"img"},{"key":"jsn_content_bottom_container_ba_soildColor","value":"#d9d9d9"},{"key":"jsn_content_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/modernCb.jpg"},{"key":"jsn_content_bottom_container_effectColor","value":""},{"key":"jsn_content_bottom_container_opacity","value":"0.1"},{"key":"jsn_content_bottom_container_imageWidth","value":"100%"},{"key":"jsn_content_bottom_container_imageHeight","value":"100%"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_bottom_container_link_linkColor","value":""},{"key":"jsn_content_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_container_bo_borderColor","value":""},{"key":"jsn_content_bottom_container_sp_marginleft","value":""},{"key":"jsn_content_bottom_container_sp_marginright","value":""},{"key":"jsn_content_bottom_container_sp_marginbottom","value":""},{"key":"jsn_content_bottom_container_sp_margintop","value":""},{"key":"jsn_content_bottom_container_sp_paddingleft","value":""},{"key":"jsn_content_bottom_container_sp_paddingright","value":""},{"key":"jsn_content_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_content_bottom_container_sp_paddingtop","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginright","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_margintop","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_footer':'[{"key":"jsn_footer_container_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_container_ba_soildColor","value":"#111111"},{"key":"jsn_footer_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_container_image","value":""},{"key":"jsn_footer_container_effectnColor","value":""},{"key":"jsn_footer_container_opacity","value":"0.1"},{"key":"jsn_footer_container_imageWidth","value":"100%"},{"key":"jsn_footer_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_title_fo_fontSize","value":""},{"key":"jsn_footer_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_text_fo_fontSize","value":""},{"key":"jsn_footer_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_footer_container_link_linkColor","value":""},{"key":"jsn_footer_container_bo_borderThickness","value":"0"},{"key":"jsn_footer_container_bo_borderStyle","value":"solid"},{"key":"jsn_footer_container_bo_borderColor","value":""},{"key":"jsn_footer_container_sp_marginleft","value":""},{"key":"jsn_footer_container_sp_marginright","value":""},{"key":"jsn_footer_container_sp_marginbottom","value":""},{"key":"jsn_footer_container_sp_margintop","value":""},{"key":"jsn_footer_container_sp_paddingleft","value":""},{"key":"jsn_footer_container_sp_paddingright","value":""},{"key":"jsn_footer_container_sp_paddingbottom","value":""},{"key":"jsn_footer_container_sp_paddingtop","value":""},{"key":"jsn_footer_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_footer_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_footer_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_footer_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginright","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_margintop","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_switcher':'[{"key":"jsn_switcher_container_bo_borderThickness","value":"0"},{"key":"jsn_switcher_container_bo_borderStyle","value":"solid"},{"key":"jsn_switcher_container_bo_borderColor","value":""},{"key":"jsn_switcher_container_ba_backgroundType","value":"Solid"},{"key":"jsn_switcher_container_ba_soildColor","value":""},{"key":"jsn_switcher_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_switcher_container_sp_paddingleft","value":""},{"key":"jsn_switcher_container_sp_paddingright","value":""},{"key":"jsn_switcher_container_sp_paddingbottom","value":""},{"key":"jsn_switcher_container_sp_paddingtop","value":""}]'
						}
					},
					solid:{
						title:"Solid",
						thumbnail:"components/com_mobilize/assets/images/thumbnail/solid.png",
						style:{
							'jsn_template':'[{"key":"jsn_template_container_ba_backgroundType","value":"img"},{"key":"jsn_template_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_template_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_template_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/solidBg.jpg"},{"key":"jsn_template_container_effectColor","value":""},{"key":"jsn_template_container_opacity","value":"0.1"},{"key":"jsn_template_container_imageWidth","value":"100%"},{"key":"jsn_template_container_imageHeight","value":"100%"},{"key":"jsn_template_container_sp_paddingleft","value":"20"},{"key":"jsn_template_container_sp_paddingright","value":"20"},{"key":"jsn_template_container_sp_paddingbottom","value":"20"},{"key":"jsn_template_container_sp_paddingtop","value":"20"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_title_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_title_fo_fontSize","value":""},{"key":"jsn_template_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_template_content_body_fo_fontFace","value":"Verdana"},{"key":"jsn_template_content_body_fo_fontSize","value":""},{"key":"jsn_template_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_template_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_template_content_link_linkColor","value":""}]',
							'jsn_menu':'[{"key":"jsn_menu_container_bo_borderThickness","value":"0"},{"key":"jsn_menu_container_bo_borderStyle","value":"solid"},{"key":"jsn_menu_container_bo_borderColor","value":""},{"key":"jsn_menu_container_ba_backgroundType","value":"Solid"},{"key":"jsn_menu_container_ba_soildColor","value":"#000000"},{"key":"jsn_menu_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_menu_container_ba_activeColor","value":"#404040"},{"key":"jsn_menu_container_ic_iconColor","value":"white"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderThickness","value":"0"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderStyle","value":"solid"},{"key":"jsn_menu_sublevel1_bo_sublevel1_bo_borderColor","value":""},{"key":"jsn_menu_sublevel1_ba_normalColor","value":"#333333"},{"key":"jsn_menu_sublevel1_ba_activeColor","value":""},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel1_fo_fontSize","value":""},{"key":"jsn_menu_sublevel1_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel1_fo_fontColor","value":"#000000"},{"key":"jsn_menu_sublevel2_ba_normalColor","value":""},{"key":"jsn_menu_sublevel2_ba_activeColor","value":""},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel2_fo_fontSize","value":""},{"key":"jsn_menu_sublevel2_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel2_fo_fontColor","value":"#000000"}]',
							'jsn_mobile_tool':'[{"key":"jsn_mobile_tool_container_ba_backgroundType","value":"Solid"},{"key":"jsn_mobile_tool_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_mobile_tool_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_container_image","value":""},{"key":"jsn_mobile_tool_container_effectColor","value":""},{"key":"jsn_mobile_tool_container_opacity","value":"0.1"},{"key":"jsn_mobile_tool_container_imageWidth","value":"100%"},{"key":"jsn_mobile_tool_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_title_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mobile_tool_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_mobile_tool_container_text_fo_fontSize","value":""},{"key":"jsn_mobile_tool_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_mobile_tool_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_mobile_tool_container_link_linkColor","value":""},{"key":"jsn_mobile_tool_container_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_container_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_container_bo_borderColor","value":""},{"key":"jsn_mobile_tool_container_sp_marginleft","value":""},{"key":"jsn_mobile_tool_container_sp_marginright","value":""},{"key":"jsn_mobile_tool_container_sp_marginbottom","value":""},{"key":"jsn_mobile_tool_container_sp_margintop","value":""},{"key":"jsn_mobile_tool_container_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_container_sp_paddingright","value":""},{"key":"jsn_mobile_tool_container_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_container_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_mobile_tool_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_mobile_tool_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_margintop","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_mobile_tool_module_tabContainer_sp_paddingtop","value":""},{"key":"jsn_mobile_tool_container_rtl","value":"left"}]',
							'jsn_content_top':'[{"key":"jsn_content_top_container_ba_backgroundType","value":"Solid"},{"key":"jsn_content_top_container_ba_soildColor","value":"#111111"},{"key":"jsn_content_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_container_image","value":""},{"key":"jsn_content_top_container_effectColor","value":""},{"key":"jsn_content_top_container_opacity","value":"0.1"},{"key":"jsn_content_top_container_imageWidth","value":"100%"},{"key":"jsn_content_top_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_content_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_container_title_fo_fontSize","value":""},{"key":"jsn_content_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_container_text_fo_fontSize","value":""},{"key":"jsn_content_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_top_container_link_linkColor","value":""},{"key":"jsn_content_top_container_bo_borderThickness","value":"0"},{"key":"jsn_content_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_container_bo_borderColor","value":""},{"key":"jsn_content_top_container_sp_marginleft","value":""},{"key":"jsn_content_top_container_sp_marginright","value":""},{"key":"jsn_content_top_container_sp_marginbottom","value":""},{"key":"jsn_content_top_container_sp_margintop","value":""},{"key":"jsn_content_top_container_sp_paddingleft","value":""},{"key":"jsn_content_top_container_sp_paddingright","value":""},{"key":"jsn_content_top_container_sp_paddingbottom","value":""},{"key":"jsn_content_top_container_sp_paddingtop","value":""},{"key":"jsn_content_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_top_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginright","value":""},{"key":"jsn_content_top_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_content_top_module_tabContainer_sp_margintop","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_content_top_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_user_top':'[{"key":"jsn_user_top_container_ba_backgroundType","value":"img"},{"key":"jsn_user_top_container_ba_soildColor","value":""},{"key":"jsn_user_top_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/solidUt.png"},{"key":"jsn_user_top_container_effectColor","value":""},{"key":"jsn_user_top_container_opacity","value":"0.1"},{"key":"jsn_user_top_container_imageWidth","value":"auto"},{"key":"jsn_user_top_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_title_fo_fontSize","value":""},{"key":"jsn_user_top_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_top_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_top_container_text_fo_fontSize","value":""},{"key":"jsn_user_top_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_top_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_top_container_link_linkColor","value":""},{"key":"jsn_user_top_container_bo_borderThickness","value":"0"},{"key":"jsn_user_top_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_container_bo_borderColor","value":""},{"key":"jsn_user_top_container_sp_marginleft","value":""},{"key":"jsn_user_top_container_sp_marginright","value":""},{"key":"jsn_user_top_container_sp_marginbottom","value":""},{"key":"jsn_user_top_container_sp_margintop","value":""},{"key":"jsn_user_top_container_sp_paddingleft","value":""},{"key":"jsn_user_top_container_sp_paddingright","value":""},{"key":"jsn_user_top_container_sp_paddingbottom","value":""},{"key":"jsn_user_top_container_sp_paddingtop","value":""},{"key":"jsn_user_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_top_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_top_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_top_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_top_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginright","value":""},{"key":"jsn_user_top_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_user_top_module_tabContainer_sp_margintop","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_user_top_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_mainbody':'[{"key":"jsn_mainbody_container_bo_borderThickness","value":"0"},{"key":"jsn_mainbody_container_bo_borderStyle","value":"solid"},{"key":"jsn_mainbody_container_bo_borderColor","value":""},{"key":"jsn_mainbody_container_ba_backgroundType","value":"Solid"},{"key":"jsn_mainbody_container_ba_soildColor","value":"#ffffff"},{"key":"jsn_mainbody_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_mainbody_container_sh_shadowSpread","value":""},{"key":"jsn_mainbody_container_sh_shadowColor","value":""},{"key":"jsn_mainbody_container_bo_roundedCornerRadius","value":""},{"key":"jsn_mainbody_container_sp_paddingleft","value":"20"},{"key":"jsn_mainbody_container_sp_paddingright","value":"20"},{"key":"jsn_mainbody_container_sp_paddingbottom","value":"20"},{"key":"jsn_mainbody_container_sp_paddingtop","value":"20"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFaceType","value":"google fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_title_fo_fontFace","value":"Roboto"},{"key":"jsn_mainbody_content_title_fo_fontSize","value":"20"},{"key":"jsn_mainbody_content_title_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_mainbody_content_body_fo_fontFace","value":"Verdana"},{"key":"jsn_mainbody_content_body_fo_fontSize","value":""},{"key":"jsn_mainbody_content_body_fo_fontStyle","value":"inherit"},{"key":"jsn_mainbody_content_body_fo_fontColor","value":"#000000"},{"key":"jsn_mainbody_content_link_linkColor","value":""}]',
							'jsn_user_bottom':'[{"key":"jsn_user_bottom_container_ba_backgroundType","value":"img"},{"key":"jsn_user_bottom_container_ba_soildColor","value":"#d9d9d9"},{"key":"jsn_user_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/solidUb.png"},{"key":"jsn_user_bottom_container_effectColor","value":""},{"key":"jsn_user_bottom_container_opacity","value":"0.1"},{"key":"jsn_user_bottom_container_imageWidth","value":"auto"},{"key":"jsn_user_bottom_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_user_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_user_bottom_container_link_linkColor","value":""},{"key":"jsn_user_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_container_bo_borderColor","value":""},{"key":"jsn_user_bottom_container_sp_marginleft","value":""},{"key":"jsn_user_bottom_container_sp_marginright","value":""},{"key":"jsn_user_bottom_container_sp_marginbottom","value":""},{"key":"jsn_user_bottom_container_sp_margintop","value":""},{"key":"jsn_user_bottom_container_sp_paddingleft","value":""},{"key":"jsn_user_bottom_container_sp_paddingright","value":""},{"key":"jsn_user_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_user_bottom_container_sp_paddingtop","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginright","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_margintop","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_content_bottom':'[{"key":"jsn_content_bottom_container_ba_backgroundType","value":"img"},{"key":"jsn_content_bottom_container_ba_soildColor","value":"#d9d9d9"},{"key":"jsn_content_bottom_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_container_image","value":"plugins/system/jsnmobilize/assets/images/thumbnail/solidCb.png"},{"key":"jsn_content_bottom_container_effectColor","value":""},{"key":"jsn_content_bottom_container_opacity","value":"0.1"},{"key":"jsn_content_bottom_container_imageWidth","value":"auto"},{"key":"jsn_content_bottom_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_title_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_container_text_fo_fontSize","value":""},{"key":"jsn_content_bottom_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_content_bottom_container_link_linkColor","value":""},{"key":"jsn_content_bottom_container_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_container_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_container_bo_borderColor","value":""},{"key":"jsn_content_bottom_container_sp_marginleft","value":""},{"key":"jsn_content_bottom_container_sp_marginright","value":""},{"key":"jsn_content_bottom_container_sp_marginbottom","value":""},{"key":"jsn_content_bottom_container_sp_margintop","value":""},{"key":"jsn_content_bottom_container_sp_paddingleft","value":""},{"key":"jsn_content_bottom_container_sp_paddingright","value":""},{"key":"jsn_content_bottom_container_sp_paddingbottom","value":""},{"key":"jsn_content_bottom_container_sp_paddingtop","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginright","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_margintop","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_footer':'[{"key":"jsn_footer_container_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_container_ba_soildColor","value":""},{"key":"jsn_footer_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_container_image","value":""},{"key":"jsn_footer_container_effectColor","value":""},{"key":"jsn_footer_container_opacity","value":"0.1"},{"key":"jsn_footer_container_imageWidth","value":"100%"},{"key":"jsn_footer_container_imageHeight","value":"auto"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_title_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_title_fo_fontSize","value":""},{"key":"jsn_footer_container_title_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_title_fo_fontColor","value":"#000000"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_footer_container_text_fo_fontFace","value":"Verdana"},{"key":"jsn_footer_container_text_fo_fontSize","value":""},{"key":"jsn_footer_container_text_fo_fontStyle","value":"inherit"},{"key":"jsn_footer_container_text_fo_fontColor","value":"#000000"},{"key":"jsn_footer_container_link_linkColor","value":""},{"key":"jsn_footer_container_bo_borderThickness","value":"0"},{"key":"jsn_footer_container_bo_borderStyle","value":"solid"},{"key":"jsn_footer_container_bo_borderColor","value":""},{"key":"jsn_footer_container_sp_marginleft","value":""},{"key":"jsn_footer_container_sp_marginright","value":""},{"key":"jsn_footer_container_sp_marginbottom","value":""},{"key":"jsn_footer_container_sp_margintop","value":""},{"key":"jsn_footer_container_sp_paddingleft","value":""},{"key":"jsn_footer_container_sp_paddingright","value":""},{"key":"jsn_footer_container_sp_paddingbottom","value":""},{"key":"jsn_footer_container_sp_paddingtop","value":""},{"key":"jsn_footer_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_footer_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_footer_module_tabContainer_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_footer_module_tabContainer_bo_borderThickness","value":"0"},{"key":"jsn_footer_module_tabContainer_bo_borderStyle","value":"solid"},{"key":"jsn_footer_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginright","value":""},{"key":"jsn_footer_module_tabContainer_sp_marginbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_margintop","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingleft","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingright","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingbottom","value":""},{"key":"jsn_footer_module_tabContainer_sp_paddingtop","value":""}]',
							'jsn_switcher':'[{"key":"jsn_switcher_container_bo_borderThickness","value":"0"},{"key":"jsn_switcher_container_bo_borderStyle","value":"solid"},{"key":"jsn_switcher_container_bo_borderColor","value":""},{"key":"jsn_switcher_container_ba_backgroundType","value":"Solid"},{"key":"jsn_switcher_container_ba_soildColor","value":""},{"key":"jsn_switcher_container_ba_gradientColor","value":"-webkit-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_switcher_container_sp_paddingleft","value":""},{"key":"jsn_switcher_container_sp_paddingright","value":""},{"key":"jsn_switcher_container_sp_paddingbottom","value":""},{"key":"jsn_switcher_container_sp_paddingtop","value":""}]'
						}
					}
				}
                return style;
            }
        }
        return JSNMobilizeStyle;
    })