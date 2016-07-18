define([
        'jquery',
        'jsn/libs/modal',
        'mobilize/style',
        'mobilize/dialogedition',
        'jquery.tipsy',
        'codemirror',
        'codemirror.mode.css',
        'codemirror.selection.markselection',
        'codemirror.selection.activeline',
        'codemirror.edit.matchbrackets',
        'jquery.ui',
        'jquery.cookie',
        'jquery.json'
    ],
    
    function ($, JSNModal, JSNStyle, JSNMobilizeDialogEdition) {
        var JSNMobilizeProfileView = function (params) {
            $(".jsn-modal-overlay,.jsn-modal-indicator").remove();
            $("body").append($("<div/>", {
                "class":"jsn-modal-overlay",
                "style":"z-index: 1000; display: inline;"
            })).append($("<div/>", {
                "class":"jsn-modal-indicator",
                "style":"display:block"
            })).addClass("jsn-loading-page");
            this.params = params;
            this.lang = params.language;
            this.listMenu = params.listMenu;
            this.listModule = params.listModule;
            this.defaultTemplate = params.defaultTemplate;
            this.token = params.token;
            this.pathRoot = params.pathRoot;
            this.configuration = params.configuration;
            this.init();
            // $(".jsn-modal-overlay,.jsn-modal-indicator").remove();
        }

        var updateItem = false;
        var listModuleInPosition = [];
        JSNMobilizeProfileView.prototype = {
            init:function () {
                this.JSNMobilizeDialogEdition = new JSNMobilizeDialogEdition(this.params);
                this.JSNStyle = new JSNStyle(this.params);
                var self = this;
                var edition = this.params.editions;
                var mobilizeSelectedTab = 0;
                if ($.cookie('jsn_mobilize_tabs_' + $("#jform_profile_id").val()) == "li-design") {
                    mobilizeSelectedTab = 1;
                }
                $(window).scroll(function() {
                    $('.mobilize-dialog').hide();
                })
                $(".jsn-iconbar-trigger .jsn-iconbar a.element-delete").click(function () {
                    self.deleteElement($(this));
                    return false;
                })
                $(".jsn-iconbar-trigger .jsn-iconbar a.coppyright").click(function () {
                    self.JSNMobilizeDialogEdition = new JSNMobilizeDialogEdition(self.params);
                    JSNMobilizeDialogEdition.createDialogLimitation($(this), self.lang["JSN_MOBILIZE_YOU_CAN_NOT_HIDE_THE_COPYLINK"]);
                    return false;
                });
                $(".mobilize-menu .link-menu-mobilize").each(function (){
                    if($(this).attr('data-type') === 'mobilize-menu'){
                        if($(this).attr("data-value") === ''){
                            var url = "index.php?option=com_mobilize&view=menus&layout=default&tmpl=component&jsnaction=update&" + this.token + "=1";
                            var strAttr = 'Main Menu';
                            var dataType ='mobilize-menu';
                            self.loadMenus(url,strAttr,dataType);
                        }
                    }
                    if($(this).attr('data-type') === 'mobilize-search'){
                        if($(this).attr("data-value") === ''){
                            var url = "index.php?option=com_mobilize&view=modules&layout=default&tmpl=component&function=changeModuleMenuIcon&filter_client_id=0&filter_state=1&filter_module=mod_search&jsnaction=update&modulesAction=menu&" + this.token + "=1";
                            var strAttr = 'Search';
                            var dataType ='mobilize-search'
                            self.loadMenus(url,strAttr,dataType);
                        }
                    }
                    if($(this).attr('data-type') === 'mobilize-login'){
                        if($(this).attr("data-value") === ''){
                            var url = "index.php?option=com_mobilize&view=modules&layout=default&tmpl=component&function=changeModuleMenuIcon&filter_client_id=0&filter_state=1&filter_module=mod_login&jsnaction=update&modulesAction=menu&" + this.token + "=1";
                            var strAttr = 'Login';
                            var dataType ='mobilize-login'
                            self.loadMenus(url,strAttr,dataType);
                        }
                    }
                })
                self.addText();
                self.showmore();
                self.choise();
                self.loadFooter();
                $(".jsn-tabs").tabs({
                    selected:mobilizeSelectedTab,
                    select:function (event, ui) {
                        $.cookie('jsn_mobilize_tabs_' + $("#jform_profile_id").val(), '', {
                            expires:-1
                        });
                        $.cookie('jsn_mobilize_tabs_' + $("#jform_profile_id").val(), $(ui.tab).attr("id"));
                    }
                });
                $('.jsn-tipsy').tipsy({
                    gravity:'w',
                    fade:true
                });
                this.registerEvent();
                $("a.jsn-add-more").click(function (e) {
                    var thisParents = $(this).parents('.jsn-column-container').attr("id");
                    if (edition.toLowerCase() != "free") {
                        self.addElement($(this));
                        $(".ui-state-edit").removeClass("ui-state-edit");
                        e.stopPropagation();
                    } else {
                        if (thisParents != "jsn-content-top" && thisParents != "jsn-user-top" && thisParents != "jsn-user-bottom" && thisParents != "jsn-content-bottom") {
                            self.addElement($(this));
                            $(".ui-state-edit").removeClass("ui-state-edit");
                            e.stopPropagation();
                        } else {
                            JSNMobilizeDialogEdition.createDialogLimitation($(this), self.lang["JSN_MOBILIZE_ADD_ELEMENT_IS_AVAILABLE_ONLY_IN_PRO_EDITION"]);
                        }
                    }
                });

                $("#jsn-logo a.element-edit").click(function (e) {
                    self.createPopupDialogLogo($(this));
                    e.stopPropagation();
                });
                $("ul.mobilize-menu li a").click(function (e) {
                    if ($(this).attr('data-type') == 'mobilize-menu')
                    {
                        self.dialogChangeMenus();
                    }
                    else
                    {
                        self.createPopupDialogMenuIcon($(this));
                    }

                    e.stopPropagation();
                });
                $("#jsn-switcher button.btn-switcher").click(function (e) {
                    self.createPopupDialogMenuIcon($(this));
                    e.stopPropagation();
                });
                $.getModuleList = function () {
                    return listModuleInPosition;
                };
                $.setModuleList = function (modules) {
                    listModuleInPosition = modules;
                };
                //Select layout Mobile or Tablet
                $('#jform_profile_device').change(function (){
                    var slD = $('#jform_profile_device').val();
                    if(slD === 'jsn_mobile'){
                        $("#mobile_ui_enabled").click();
                        $('#mobilize-mobile-tool-left').css({'display':'block'});
                        $('#mobilize-content-top-left').css({'display':'block'});
                        $('#mobilize-user-top-left').css({'display':'block'});
                        $('#mobilize-user-bottom-left').css({'display':'block'});
                        $('#mobilize-content-bottom-left').css({'display':'block'});
                        $('#mobilize-footer-left').css({'display':'block'});
                    }
                    if(slD === 'jsn_tablet'){
                        $("#tablet_ui_enabled").click();
                        $('#mobilize-mobile-tool-left').css({'display':'block'});
                        $('#mobilize-content-top-left').css({'display':'block'});
                        $('#mobilize-user-top-left').css({'display':'block'});
                        $('#mobilize-user-bottom-left').css({'display':'block'});
                        $('#mobilize-content-bottom-left').css({'display':'block'});
                        $('#mobilize-footer-left').css({'display':'block'});
                    }
                });
                $("#design .jsn-mobilize .btn-group.jsn-inline .mobilize_view_layout").click(function () {
                    if (!$(this).hasClass("active")) {
                        $("#design .jsn-mobilize .btn-group.jsn-inline .mobilize_view_layout").removeClass("active");
                        $(this).addClass("active");
                        self.changeViewMobilize();
                    }
                });
                this.changeViewMobilize('default');
                window.changeLogo = function (src, modal) {
                    var dataInput = {};
                    var mobilizeLogo = $("#jsn-logo");
                    $(".mobilize-dialog .popover-content input.logo-url").val(src);
                    dataInput[src] = $(".mobilize-dialog .popover-content input.logo-alt").val();
                    mobilizeLogo.find("input.data-mobilize").val($.toJSON(dataInput));
                    mobilizeLogo.find("input.data-mobilize").attr("data-id", src);
                    if (modal !== false) {
                        mobilizeLogo.find("img").attr("src", self.pathRoot + src).load(function () {
                            $("#jsn-logo a.element-edit").click();
                        });
                        mobilizeLogo.find("span.jsn-select-logo").hide();
                        $("#jsn-logo a.element-edit").removeClass("jsn-logo-null");
                        self.modalchangeLogo.close();
                    } else {
                        mobilizeLogo.find("span.jsn-select-logo").show();
                        mobilizeLogo.find("img").attr({"src":"", "alt":""});
                        $("#jsn-logo a.element-edit").addClass("jsn-logo-null").click();
                    }
                };
                $.jSelectPosition = function (postion) {
                    var options = new Object();
                    options.value = postion;
                    options.type = "position";
                    options.name = postion;
                    options.label = self.lang["JSN_MOBILIZE_TYPE_POSITION"];
                    self.saveItem(options);
                    self.closeModalBox();
                    $(".mobilize-dialog").remove();
                };
                $.changeModuleMenuIcon = function (id, title) {
                    self.changeModuleMenuIcon(id, title);
                };
                $.jSelectModules = function (id, title, action) {
                    var options = new Object();
                    options.value = id;
                    options.type = "module";
                    options.name = title;
                    options.label = self.lang["JSN_MOBILIZE_TYPE_MODULE"];
                    self.saveItem(options);
                    if (action == "update") {
                        self.closeModalBox();
                    }
                    $(".mobilize-dialog").remove();
                };
                $("button.mobilize-preview").click(function () {
                    $(document).trigger("click");
                    if ($(this).hasClass("active")) {
                        $("#mobilize-design #jsn-mobilize").show();
                        $("#mobilize-design .jsn-mobilize-preview").remove();
                        $("#mobilize-design .jsn-bgloading").remove();
                        $(this).removeClass("active");
                        $(this).text('');
                        $(this).append('<i class="icon-eye-open"></i>' + $(this).attr("text-enable"));
                        $('.divopacity').remove();
                        self.loadStyleModul(1);
                    } else {
                        $('head').find('style').each(function() {
                            if ($(this).attr('data-title') === 'loadstyle') {
                                $(this).remove();
                            }
                        })
                        $('.divopacity').remove();
                        $("body").append($("<div/>", {
                            "class":"jsn-modal-overlay",
                            "style":"z-index: 1000; display: inline;"
                        })).append($("<div/>", {
                            "class":"jsn-modal-indicator",
                            "style":"display:block"
                        })).addClass("jsn-loading-page");
                        $(this).addClass("active");
                        $(this).text('');
                        $(this).append('<i class="icon-eye-open"></i> ' + $(this).attr("text-disable"));
                        self.createPreview();
                    }
                    return false;
                });
                $(document).click(function () {
                    $(".mobilize-dialog").remove();
                    $(".jsn-element-active").removeClass("jsn-element-active");
                    $(".ui-state-edit").removeClass("ui-state-edit");
                });
                Joomla.submitbutton = function (pressedButton) {
                    if (/^profile\.(save|apply)/.test(pressedButton)) {
                        if ($("#jform_profile_title").val() == "") {
                            $(".jsn-tabs").tabs({
                                selected:0
                            });
                            $("#jform_profile_title").parent().parent().addClass("error");
                            $("#jform_profile_title").focus();
                            alert('Please correct the errors in the Form');
                            return false;
                        }
                    }
                    submitform(pressedButton);
                };
                $("#select_profile_style").click(function (e) {
                    self.overflowHid();
                    $(dialogLoadStyle).dialog("open");
                    $('.thumbnail').click(function (){
                        self.selectStyle($(this));
                    })
//                    self.dialogLoadStyleProfile($(this));
                    e.stopPropagation();
                });
                this.setDefaultCss();

                //Show dialog load style
                var dialogLoadStyle = $("#container-load-style");
                var hieght = $( window ).height();
                var width = $( window ).width();
                var self = this;
                var styleList = this.JSNStyle.profileStyleList();
                var alt = $('#input_style_jsn_style').val();
                if(typeof alt !== 'undefined' && alt !== ''){
                    alt = $.evalJSON($('#input_style_jsn_style').val());
                }
                $.each(styleList, function (i, val) {
                    $("#profile-style-list").append(
                        $("<div/>", {"class":"jsn-column-item"}).append(
                            $("<a/>", {"class":"thumbnail"+ " " +val.title, "href":"javascript:void(0);"}).append(
                                $("<img/>", {"src":val.thumbnail, "alt":val.title})
                            ).append(
                                $("<div/>", {"class":"caption",'data-title':$.toJSON(val.style)}).append(
                                    $("<h3/>").append(val.title)
                                )
                            )
                        )
                    )
                });
                if(typeof alt !== 'undefined' && alt !== ''){
                    $('#profile-style-list').find('.'+alt[0]).each(function (){
                        $(this).find('.caption').append($('<img>',{'src':'components/com_mobilize/assets/images/thumbnail/choised.png'}));
                    })
                }
                $(dialogLoadStyle).dialog({
                    height:hieght * (90 / 100),
                    width:width * (70 / 100),
                    modal:true,
                    autoOpen: false,
                    resizable:false,
                    title:'Load Style',
                    buttons:{
                        Save:function () {
                            $('.jsn-total').removeAttr('style');
                            $('#jsn-footer').removeAttr('style');
                            $('#jsn-switcher').removeAttr('style');
                            $('#jsn-template').removeAttr('style');
                            $('#jsn-template h2').removeAttr('style');
                            $('#jsn-template p').removeAttr('style');
                            $('head').find('style').each(function (){
                                if($(this).attr('data-title') === 'loadstyle'){
                                    $(this).remove();
                                }
                            })
                            $("#profile-style-list").find('.thumbnail').find('.caption').find('img').each(function (){
                                var val = $.evalJSON($(this).parent().attr('data-title'));
                                if (confirm(self.lang['JSN_MOBILIZE_CONFIRM_LOAD_STYLE'])) {
                                    var textTitle = $(this).parent().find('h3').text();
                                    $('#input_style_jsn_typestyle').val($.toJSON(textTitle));
                                    if(textTitle === 'Simple' || textTitle === 'Retro' || textTitle === 'Flat' || textTitle === 'Modern'){
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

                                    if(textTitle === 'Metro' || textTitle === 'Glass' || textTitle === 'Solid'){
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
                                    if(textTitle === 'Retro'){
                                        $('#jsn-mainbody').append('<style data="jsn_menu">#jsn-mainbody h2{font-weight:bold;text-transform:uppercase}</style>');
                                    }
                                    $.each(val, function (j, k) {
                                        $("#input_style_" + j).val(k);
                                        self.JSNStyle.changeStyle($("#input_style_" + j));
                                    });
                                }
                            })
                            $(this).dialog("close");
                            self.loadStyleModul();
                            self.removeCss();
                            $('body').css('overflow','auto');
                        },
                        Close:function () {
                            $(this).dialog("close");
                            $('body').css('overflow','auto');
                        }
                    }
                });
                setTimeout(function () {
                    $(".jsn-modal-overlay,.jsn-modal-indicator").remove();
                    $(".jsn-mobilize-form").removeClass("hide");
                }, 500);
                var dialogCustomCss = $("#container-custom-css");
                $(dialogCustomCss).dialog({
                    height:700,
                    width:800,
                    modal:true,
                    autoOpen: false,
                    resizable:false,
                    title:'Custom CSS',
                    buttons:{
                        Save:function () {
                            $("#container-custom-css-hide ul#custom-css-list-file").empty();
                            $(dialogCustomCss).find(".css-files-container li").each(function () {
                                $("#container-custom-css-hide ul#custom-css-list-file").append($(this).html());
                            });
                            $("#container-custom-css-hide #custom-css-code").val($(dialogCustomCss).find("textarea#custom-css").val());
                            $(this).dialog("close");
                        },
                        Close:function () {
                            $(this).dialog("close");
                        }
                    }
                });
                $(dialogCustomCss).find("#items-list-edit").click(function () {
                    $(dialogCustomCss).find(".css-files-container").hide();
                    $(dialogCustomCss).find(".items-list-edit-content").show();
                    $(this).hide();
                    $(dialogCustomCss).find("#items-list-save").show();
                    var contentCss = [];
                    $(dialogCustomCss).find(".css-files-container li").each(function () {
                        contentCss.push($(this).find("input").val());
                    });
                    $(dialogCustomCss).find(".items-list-edit-content textarea").val(contentCss.join("\n"));
                });
                $(dialogCustomCss).find("#items-list-save").click(function () {
                    $(this).hide();
                    $(dialogCustomCss).find("#items-list-edit").show();
                    var contentCss = $(dialogCustomCss).find(".items-list-edit-content textarea").val();
                    contentCss = contentCss.split("\n");
                    $(dialogCustomCss).find(".jsn-items-list").empty();
                    if (contentCss.length > 0) {
                        var addedItems = [];
                        contentCss.each(function (val, i) {
                            if (val && addedItems.indexOf(val) == -1) {
                                addedItems.push(val);
                                $(dialogCustomCss).find(".jsn-items-list").append('<li class="jsn-item ui-state-default" ><label class="checkbox"><input type="hidden" value="' + val + '" name="mobilize_custom_css_files[]">' + val + '</label></li>');
                            }
                        });
                    }
                    $(dialogCustomCss).find(".css-files-container").sortable({
                        connectWith:".jsn-element-container",
                        placeholder:'ui-state-highlight',
                        forcePlaceholderSize:true
                    });
                    $(dialogCustomCss).find(".jsn-items-list").show();
                    $(dialogCustomCss).find(".items-list-edit-content").hide();
                });
                $(dialogCustomCss).find(".css-files-container").sortable({
                    connectWith:".jsn-element-container",
                    placeholder:'ui-state-highlight',
                    forcePlaceholderSize:true
                });
                $("#select_profile_css").click(function () {
                    $(dialogCustomCss).dialog("open");
                    $("#container-custom-css #custom-css").show();
                    $("#container-custom-css .CodeMirror").remove();
                    var editor = CodeMirror.fromTextArea(document.getElementById('custom-css'), {
                        mode:"text/css",
                        styleActiveLine:true,
                        lineNumbers:true,
                        lineWrapping:true
                    });
                    editor.on("keydown", function (cm, change) {
                        $("#container-custom-css #custom-css").val(cm.getValue());
                    });
                    editor.on("keyup", function (cm, change) {
                        $("#container-custom-css #custom-css").val(cm.getValue());
                    });
                });
                //Show dialog social setting
                var dialogCustomSocial = $("#container-custom-social");
                var hieght = $( window ).height() * 0.62;
                var width = $( window ).width() * 0.60;
                $(dialogCustomSocial).dialog({
                    height:hieght,
                    width:width,
                    modal:true,
                    autoOpen: false,
                    resizable:false,
                    title:'Social Setting ',
                    buttons:{
                        Save:function () {
                            var dataInput = {};
                            var cm = 0;
                            var cdt='';
                            $(".social_div").children().hide();
                            $('.jsn_social').find('input').each(function (){
                                if($(this).attr('type') === 'text'){
                                    var vl = $(this).val();
                                    var id = $(this).attr('data-title');
                                    if(vl !== ''){
                                        if(self.checkLink($(this).val())){
                                            $(".social_div").find('a').each(function (){
                                                if($(this).attr('id') === id){
                                                    $(this).remove();
                                                }
                                            });
                                            cdt = 1;
                                            var dataVlStt = {};
                                            dataVlStt[0] = vl;
                                            dataVlStt[1] = self.showStatus(this);
                                            dataInput[id]= dataVlStt;
                                            var social = $("<a/>", {"id":id,class:'font-icon ' +dataVlStt[1][0],href:vl,style:'margin:0px 5px 5px 0px',target:'_blank'}).append(
                                                $("<i/>", { "class":"fa " + id }));
                                            $('.social_div').append(social);
                                            $('.error-'+id).hide();
                                        }else{
                                            cm = 1;
                                            $('.error-'+id).css({'display':'block','padding':'3px','color':'red','text-decoration':'blink','font-size':'14px'});
                                            $('.error-'+id).html(self.lang['JSN_MOBILIZE_LINK_SOCIAL']);
                                            $('.error-'+id).show();
                                        }
                                    }else{
                                        var dataVlStt = {};
                                        dataVlStt[0] = vl;
                                        dataVlStt[1] = self.showStatus(this);
                                        dataInput[id]=dataVlStt;
                                    }
                                }
                            })
                            $('#social_input').val($.toJSON(dataInput));

                            if(cm !==1 && cdt === 1){
                                $(this).dialog("close");
                            }else{
                                $(".social_div").children().show();
                            }
                            self.showmore();
                            $('body').css('overflow','auto');
                        },
                        Close:function () {
                            var emp=0;
                            $('.jsn_social').find('input').each(function (){
                                if($(this).attr('type') === 'text'){
                                    var vl = $(this).val();
                                    if(vl !== ''){
                                        emp =1;
                                    }
                                }
                            })
                            if(emp === 0){
                                $(".social_div").children().show();
                                $(this).dialog("close");
                            }else{
                                $(this).dialog("close");
                            }
                            self.showmore();
                            $('body').css('overflow','auto');
                        }
                    }
                });

                $('#showmore').click(function (){
                    $('.jsn_social').find('.jsn_block_social').show();
                    $('.social_more').hide();
                })
                $('.jsn_social').find('input').each(function (){
                    $(this).parent().find('span').hide();
                    $(this).change(function (){
                        var vl = $(this).val();
                        var id = $(this).attr('data-title');
                        if(self.checkLink(vl)){
                            $('.error-'+id).hide();
                            $(this).parent().find('br').hide();
                        }
                    })
                })
                $("#select_profile_social").click(function () {
                    $(dialogCustomSocial).dialog("open");
                    self.overflowHid();
                });
                $('#template_style').click(function (){
                    $('#jsn_template_click').click();
                })
                self.loadStyleModul();
            },
            addText: function() {
                $('#jsn-mobile-tool').find('#mobilize-mobile-tool-right').find('.jsn-element-container').each(function() {
                    if(!$.trim($(this).html())){
                        $(this).append('<p class="addTextPM">Position & Module here</p>');
                    }else{
                        $('.addTextPM').hide();
                    }
                })
            },
            //Remove "Add customer css" then "load style"
            removeCss:function (){
                $('#container-custom-css-hide').find('input').val('');
                $('#custom-css').val('');
                $('#container-custom-css').find('.controls').each(function (){
                    $(this).find('ul').empty();
                    $(this).find('.CodeMirror').remove();
                    $(this).find('textarea').each(function (){
                        $(this).empty();
                    });
                })
            },
            selectStyle:function (e){
                var alt={};
                e.parent().parent().find('.caption img').remove();
                e.parent().find('img').each(function (){
                    alt[0] = $(this).attr('alt');
                })
                $('#input_style_jsn_style').val($.toJSON(alt));
                e.find('.caption').append($('<img>',{'src':'components/com_mobilize/assets/images/thumbnail/choised.png'}));
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
                                            if (v.key === prefix +'_container_bo_borderrgb'){
                                                arr[7] = v.value;
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
                            var cssbg = 'background: url('+ self.pathRoot + v[0] +') !important;background-size: '+ v[3] +' '+ v[4]+'  !important;';
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
                                    var borderrgb ='';
                                    if(typeof v[6] !=='undefined' && v[6]!==''){
                                        radius = v[6];
                                    }
                                    if(typeof v[7] !=='undefined' && v[7]!==''){
                                        borderrgb =' border:'+ v[7] +';';
                                    }
                                    $('<div class="divopacity" style="'+borderrgb+' border-radius:'+ radius +';width: 100%;height: 100%;position: absolute;top: 0;left: 0;background-color: rgba'+ color +';"></div>').insertBefore(fst);
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
                    totalCss = '<style data-title="loadstyle"> '+iconbartpl+' .jsn-master #jsn-mobilize{text-align:'+rtl+'}.jsn-master .jsn-bootstrap #mobilize .jsn-section-content{position:relative;' + e +''+cssBg + '}.jsn-master #mobilize-design a{color:' + cssLink + '} .jsn-master #mobilize-design p{' + cssPra + '} .jsn-master #mobilize-design h1, .jsn-master #mobilize-design h2,.jsn-master #mobilize-design h3,.jsn-master #mobilize-design h4,.jsn-master #mobilize-design h5,.jsn-master #mobilize-design h6{' + cssTitle + '} </style>';
                    $('head').append(totalCss);
                }
            },
            showmore:function(){
                var count=0;
                var vl=0;
                $('.social_more').show();
                $('.jsn_social').find('.jsn_block_social').hide();
                $('.jsn_social').find('.jsn_block_social').each(function (){
                    count++;
                    $(this).find('input').each(function (){
                        if($(this).val() !== ''){
                            vl++;
                            $(this).parent().parent().parent().parent().parent().show();
                        }
                    })
                    if(vl === 0){
                        $(this).show();
                        if(count === 3){return false;}
                    }
                })
                if(vl === 14){
                    $('.social_more').hide();
                }
            },
            overflowHid:function(){
                $('body').css('overflow','hidden');
            },
            choise:function (){
                var self = this;
                $('.jsn_social').find('button').each(function (){
                    $(this).parent().css({'margin-left':'0px'});
                    $(this).parent().parent().css({'padding-bottom':'18px'});
                    $(this).click(function (){
                        if($(this).text()==='Hide'){
                            $(this).addClass('btnhide');
                            $('.btnhide').css({'background':'#bd362f'});
                        }else{
                            $(this).parent().find('.btnhide').each(function (){
                                $(this).attr('style','');
                                $(this).removeClass('btnhide');
                            });
                        }
                        $(this).parent().find('button').each(function (){
                            if($(this).attr('id') === 'choised'){
                                $(this).prop('id','');
                            }
                        })
                        $(this).prop('id','choised');
                        self.choise;
                    })
                    if($(this).attr('id') === 'choised'){
                        if($(this).text()==='Hide'){
                            $(this).addClass('btnhide');
                            $('.btnhide').css({'background':'#bd362f'});
                        }
                    }
                })
            },
            showStatus:function(e){
                var stt = {};
                $(e).parent().parent().parent().find('button').each(function (){
                    if($(this).attr('id') === 'choised'){
                        if($(this).text() === 'Show'){
                            stt[0] = 'choised';
                            stt[1] = 'none';
                        }
                        if($(this).text() === 'Hide'){
                            stt[0] = 'none';
                            stt[1] = 'choised';
                        }
                    }
                })
                return stt;
            },
            checkLink:function(str){
                var urlregex = new RegExp("^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$");
                return urlregex.test(str);
            },
            loadMenus:function (url,strAttr,dataType) {
                var createIframe = $("<iframe/>"+ url, {
                    "class":"hid",
                    "src":url,
                    height:600 * (90 / 100),
                    width:'100%'
                });
                $("body").append(createIframe);
                createIframe.load(function () {
                    $(this).contents().find("table").each(function (){
                        $(this).find('.jsnhover').each(function (){
                            var vlAt = $(this).attr('data-title');
                            var vlId = $(this).attr('data-id');
                            if(vlAt === strAttr){
                                var dataInput = {};
                                $(".mobilize-menu .link-menu-mobilize").each(function (){
                                    if(dataType == 'mobilize-menu'){
                                        $(this).attr("data-value", vlId);
                                        $(this).parent().find('input.data-mobilize').attr("data-id", vlId);
                                        dataInput[vlId] = vlAt;
                                        $(this).parent().find('input.data-mobilize').val($.toJSON(dataInput));
                                    }
                                    else
                                    {
                                        $(this).attr("data-value", vlId);
                                        $(this).parent().find('input').attr("data-id", vlId);
                                        dataInput[vlId] = vlAt;
                                        $(this).parent().find('input').val($.toJSON(dataInput));
                                    }
                                })
                                return false;
                            }
                        })
                    })
                })
            },
            //Load postion footer
            loadFooter:function (){
                var self = this;
                var vlLabel = self.lang["JSN_MOBILIZE_TYPE_POSITION"];
                var url ='index.php?option=com_mobilize&view=positions&tmpl=component&function=jSelectPosition&filter_template=' + this.defaultTemplate + '&filter_type=template&filter_state=1&' + this.token + '=1';
                var createIframe = $("<iframe/>"+ url, {
                    "class":"hid",
                    "src":url,
                    height:600 * (90 / 100),
                    width:'100%'
                });
                $("body").append(createIframe);
                $('#mobilize-footer-right').addClass('mobilize-list-edit');
                createIframe.load(function (){
                    /*$(this).contents().find("#jsn-footer").each(function (){
                     $(this).find('.jsn-position').each(function (){
                     var txt = $(this).find('p').text();
                     if(txt === 'footer'){
                     var vl = txt;
                     var vlType = "position";
                     var vlName = txt;
                     var check = true;
                     $(".mobilize-list-edit .jsn-element-container div.jsn-element").each(function () {
                     if ($(this).attr("data-value") === vl && $(this).attr("data-type") === vlType) {
                     $(this).effect("highlight", {}, 3000);
                     check = false;
                     }
                     });
                     //create element for load postion
                     if (check) {
                     var name = 'mobilize-footer-right';
                     var dataInput = {};
                     dataInput[vl] = vlType;
                     var moduleStyle = $("#" + name).attr("data-module-style");
                     var moduleTitleStyle = $("#" + name).attr("data-title-style");
                     var contentItem = $("<div/>", {"class":"jsn-element ui-state-default jsn-iconbar-trigger ui-state-edit", "style":moduleStyle, "data-value":vl, "data-type":vlType
                     }).append(
                     $("<div/>", {"class":"jsn-element-content", style:moduleTitleStyle}).append(
                     $("<span/>", {"class":"type-element", "text":vlLabel + ": "})
                     ).append(
                     $("<span/>", {"class":"name-element", "text":vlName})
                     ).append(
                     $("<input/>", {"type":"hidden", "class":"data-block-mobilize", "name":"jsnmobilize[" + name + "][]", "value":$.toJSON(dataInput)})
                     )
                     ).append(
                     $("<div/>", { "class":"jsn-iconbar"}).append(
                     $("<a/>", {"class":"element-edit", "href":"javascript:void(0)"}).click(function (e) {
                     self.editItem(e);
                     }).append('<i class="icon-pencil"></i>')).append(" ").append(
                     $("<a/>", {"class":"element-delete", "href":"javascript:void(0)"}).click(function () {
                     self.deleteElement($(this));
                     }).append('<i class="icon-trash"></i>')));
                     if (updateItem) {
                     $("#" + name + ".mobilize-list-edit .jsn-element-container .jsn-element.mobilize-update-jsn-element").before(contentItem).remove();
                     } else {
                     $("#" + name + ".mobilize-list-edit .jsn-element-container").append(contentItem);
                     }
                     self.registerEvent();
                     }
                     }
                     })
                     })*/
                })
            },
            setDefaultCss:function () {
                var self = this;
                $("#jsn-mobilize .jsn-iconbar a").each(function () {
                    self.JSNStyle.changeStyle($(this));
                });
            },
            //Load dialog "Load style"
//            dialogLoadStyleProfile:function (_this) {
//                var self = this;
//                var dialog = $("#container-select-style"), parentDialog = $("#container-select-style").parent();
//                var styleList = this.JSNStyle.profileStyleList();
//                $(dialog).find("#profile-style-list").empty();
//                $.each(styleList, function (i, val) {
//                    $(dialog).find("#profile-style-list").append(
//                        $("<div/>", {"class":"jsn-column-item"}).append(
//                            $("<a/>", {"class":"thumbnail", "href":"javascript:void(0);"}).append(
//                                $("<img/>", {"src":val.thumbnail, "alt":val.title})
//                            ).append(
//                                $("<div/>", {"class":"caption"}).append(
//                                    $("<h3/>").append(val.title)
//                                )
//                            ).click(function () {
//                                    if (confirm(self.lang['JSN_MOBILIZE_CONFIRM_LOAD_STYLE'])) {
//                                        $.each(val.style, function (j, k) {
//                                            $("#input_style_" + j).val(k);
//                                            self.JSNStyle.changeStyle($("#input_style_" + j));
//                                        });
//                                        $(dialog).hide();
//                                    }
//                                })
//                        )
//                    )
//                });
//				//Style for dialog "Load style"
//                dialog.width("600");
//                $(dialog).appendTo('body');
//                var elmStyle = self.getBoxStyle($(dialog)),
//                    parentStyle = self.getBoxStyle($(_this)),
//                    position = {};
//				var rt = elmStyle.outerWidth - (parentStyle.offset.left + parentStyle.outerWidth);
//                position.left = elmStyle.outerWidth - (rt+ parentStyle.outerWidth);
//                position.top = parentStyle.offset.top + parentStyle.outerHeight;
//                $(dialog).find(".arrow").css("left", parentStyle.outerWidth / 2);
//                dialog.css(position).click(function (e) {
//                    e.stopPropagation();
//                });
//                $(dialog).show();
//                $("#container-select-style .popover").show();
//				
//                $(document).click(function (e) {
//                    $(dialog).hide();
//                });
//            },
            changeViewMobilize:function (load) {
                var self = this,
                    cookieView = $.cookie('jsn_profile_' + $("#jform_profile_id").val()),
                    profile_device = $('select[id=jform_profile_device] option:selected').val();

                if (load == "default") {
                    if(cookieView == null){
                        if(profile_device == 'jsn_tablet'){
                            cookieView = 'tablet_ui_enabled';
                        }else{
                            cookieView = 'mobile_ui_enabled';
                        }
                    }
                    if (cookieView && $("#jform_profile_id").val()) {
                        if (cookieView == "tablet_ui_enabled") {
                            $('#jform_profile_device').find('option').each(function (){
                                if($(this).attr('value') === 'jsn_tablet'){
                                    $(this).attr('selected',true);
                                }
                                if($(this).attr('value') === 'jsn_mobile'){
                                    $(this).attr('selected',false);
                                }
                            })
                            $("#mobile_ui_enabled").removeClass("active");
                            $("#tablet_ui_enabled").addClass("active");

                            $('#mobilize-mobile-tool-left').css({'display':'block'});
                            $('#mobilize-content-top-left').css({'display':'block'});
                            $('#mobilize-user-top-left').css({'display':'block'});
                            $('#mobilize-user-bottom-left').css({'display':'block'});
                            $('#mobilize-content-bottom-left').css({'display':'block'});
                            $('#mobilize-footer-left').css({'display':'block'});
                        } else {
                            $('#jform_profile_device').find('option').each(function (){
                                if($(this).attr('value') === 'jsn_tablet'){
                                    $(this).attr('selected',false);
                                }
                                if($(this).attr('value') === 'jsn_mobile'){
                                    $(this).attr('selected',true);
                                }
                            })
                            $("#mobile_ui_enabled").addClass("active");
                            $("#tablet_ui_enabled").removeClass("active");

                            $('#mobilize-mobile-tool-left').css({'display':'block'});
                            $('#mobilize-content-top-left').css({'display':'block'});
                            $('#mobilize-user-top-left').css({'display':'block'});
                            $('#mobilize-user-bottom-left').css({'display':'block'});
                            $('#mobilize-content-bottom-left').css({'display':'block'});
                            $('#mobilize-footer-left').css({'display':'block'});
                        }
                    } else {
                        $("#mobile_ui_enabled").addClass("active");
                        $("#tablet_ui_enabled").removeClass("active");
                        $('#mobilize-mobile-tool-left').css({'display':'block'});
                        $('#mobilize-content-top-left').css({'display':'block'});
                        $('#mobilize-user-top-left').css({'display':'block'});
                        $('#mobilize-user-bottom-left').css({'display':'block'});
                        $('#mobilize-content-bottom-left').css({'display':'block'});
                        $('#mobilize-footer-left').css({'display':'block'});
                    }
                }
                $("#design .jsn-mobilize .btn-group.jsn-inline .mobilize_view_layout").each(function () {
                    if ($(this).hasClass("active")) {
                        if ($(this).attr("id") == "mobile_ui_enabled") {
                            $("#mobilize .mobilize-title.jsn-section-header h1").text(self.lang['JSN_MOBILIZE_TITLE_SMARTPHONE']);
                            $("#jsn-mobilize").addClass("jsn-layout-mobile");
                            $("#mobilize").animate({
                                width:"550px"
                            }, "easein");
                        } else {
                            $("#mobilize .mobilize-title.jsn-section-header h1").text(self.lang['JSN_MOBILIZE_TITLE_TABLET']);
                            $("#jsn-mobilize").removeClass("jsn-layout-mobile");
                            $("#jsn-mobilize").removeClass("jsn-layout-mobile");
                            $("#mobilize").animate({
                                width:"850px"
                            }, "easein");
                        }
                        $.cookie('jsn_profile_' + $("#jform_profile_id").val(), '', {
                            expires:-1
                        });
                        $.cookie('jsn_profile_' + $("#jform_profile_id").val(), $(this).attr("id"));
                    }
                });
            },
            changeSwitcherSettings:function (title) {
                if (!title) {
                    title = "Switch to Desktop";
                }
                var dataInput = {};
                var dataCheckEnable = $(".mobilize-dialog .radio input.jsn-check-enable:checked").val();
                $("#jsn-switcher button.btn-switcher").attr("data-value", title);
                $("#jsn-switcher button.btn-switcher").attr("data-state", dataCheckEnable);
                $("#jsn-switcher button.btn-switcher").html(title);
                $("#jsn-switcher input.data-mobilize").attr("data-id", title);
                dataInput[title] = dataCheckEnable;
                $("#jsn-switcher input.data-mobilize").val($.toJSON(dataInput));
            },
            //Change Module Menu Popup
            changeModuleMenuIcon:function (id, title) {

                var menuChangeModule = $(".mobilize-menu .mobilize-edit").parent();
                var dataInput = {};
                menuChangeModule.find("a.link-menu-mobilize").attr("data-value", id);
                menuChangeModule.find("input.data-mobilize").attr("data-id", id);
                dataInput[id] = title;
                menuChangeModule.find("input.data-mobilize").val($.toJSON(dataInput));
                $(".mobilize-menu .mobilize-edit").click();
                this.closeModalBox();
            },
            addElement:function (_this) {
                $(".mobilize-dialog").remove();
                $(".mobilize-list-edit").removeClass("mobilize-list-edit");
                $(_this).parent().parent().addClass("mobilize-list-edit");
                var self = this;
                // create html dialog container
                var dialog = $("<div/>", {"class":"mobilize-dialog jsn-bootstrap"}).append(
                    $("<div/>", {"class":"popover top"}).css("display", "block").append(
                        $("<div/>", { "class":"arrow" })).append(
                        $("<h3/>", { "class":"popover-title", "text":"Select Element"})).append(
                        $("<div/>", {"class":"popover-content" }).append(
                            $("<div/>", { "class":"jsn-columns-container jsn-columns-count-two" }).append(
                                $("<div/>", {"class":"jsn-mobilize-element" }).append(
                                    $("<div/>", { "class":"jsn-column-item"}).append(
                                        $("<div/>", { "class":"jsn-element-module" }).append(
                                            $("<button/>", {"class":"btn", "name":self.lang['JSN_MOBILIZE_ADD_MODULE'], "text":self.lang['JSN_MOBILIZE_ADD_MODULE']}).click(function () {
                                                updateItem = false;
                                                listModuleInPosition = [];
                                                $(".mobilize-list-edit .jsn-element-container div.jsn-element").each(function () {
                                                    if ($(this).attr("data-type") == "module") {
                                                        var optionsModule = new Object();
                                                        optionsModule.value = $(this).attr("data-value");
                                                        optionsModule.name = $(this).find("span.name-jsn-element").html();
                                                        listModuleInPosition.push(optionsModule);
                                                    }
                                                });
                                                self.actionAddModule('new');
                                                return false;
                                            })))).append(
                                    $("<div/>", { "class":"jsn-column-item"}).append(
                                        $("<div/>", {"class":"jsn-element-position" }).append(
                                            $("<button/>", { "class":"btn", "title":self.lang['JSN_MOBILIZE_ADD_POSITION'], "text":self.lang['JSN_MOBILIZE_ADD_POSITION']}).click(function () {
                                                updateItem = false;
                                                self.actionAddPosition();
                                                return false;
                                            })
                                        )
                                    )
                                )
                            )
                        )
                    )
                );
                $("body").append(dialog);
                var elmStyle = self.getBoxStyle($(dialog).find(".popover")),
                    parentStyle = self.getBoxStyle($(_this)),
                    position = {};
                position.left = parentStyle.offset.left - elmStyle.outerWidth / 2 + parentStyle.outerWidth / 2;
                position.top = parentStyle.offset.top - elmStyle.outerHeight;
                dialog.css(position).click(function (e) {
                    e.stopPropagation();
                });
            },
            actionChangeLogo:function (_this, mobilizeMenuItem) {
                var dataInput = {};
                dataInput[mobilizeMenuItem.find("input.data-mobilize").attr("data-id")] = $(_this).val();
                mobilizeMenuItem.find("a.element-edit").attr("data-state", $(_this).val());
                if ($(_this).val()) {
                    mobilizeMenuItem.find("img").attr("alt", $(_this).val());
                } else {
                    mobilizeMenuItem.find("img").attr("alt", "Select Logo");
                }
                mobilizeMenuItem.find("input.data-mobilize").val($.toJSON(dataInput));
            },
            createPopupDialogLogo:function (_this) {
                $(".mobilize-dialog").remove();
                $(".mobilize-edit").removeClass("mobilize-edit");
                var self = this;
                var title = $(_this).attr("title");
                var mobilizeMenuItem = $(_this).parent().parent();
                $(_this).addClass("mobilize-edit");
                //content dialog Alignment

                //content dialog type logo
                var contentDialogLogoSlogan = $("<div/>", {"class":"control-group"}).append(
                    $("<label/>", { "class":"control-label", text:self.lang['JSN_MOBILIZE_IMAGE_ALT']})).append(
                    $("<div/>", {"class":"controls" }).append(
                        $("<input/>", { "type":"text", "name":"logo_alt", "class":"logo-alt jsn-input-xlarge-fluid", "value":mobilizeMenuItem.find("a.element-edit").attr("data-state") }).bind('keyup',function () {
                            self.actionChangeLogo($(this), mobilizeMenuItem);
                        }).change(function () {
                            self.actionChangeLogo($(this), mobilizeMenuItem);
                        })
                    )
                );
                // Html content dialog type Logo
                var cotentDialogLogoSrc = $("<div/>", { "class":"control-group"}).append(
                    $("<label/>", {"class":"control-label", text:self.lang['JSN_MOBILIZE_IMAGE_FILE']})).append(
                    $("<div/>", {"class":"controls" }).append(
                        $("<div/>", { "class":"row-fluid input-append" }).append(
                            $("<input/>", { "type":"text", "name":"logo_url", "class":"logo-url jsn-input-large-fluid", "disabled":"disabled", "value":mobilizeMenuItem.find("input.data-mobilize").attr("data-id")})).append(
                            $("<button/>", { "class":"btn", "onclick":"return false;", text:"..."}).click(function () {
                                self.changeLogo();
                            })).append(
                            $("<button/>", {"class":"btn btn-icon", "onclick":"return false;"}).click(function (e) {
                                $(".mobilize-dialog input.logo-url").val("");
                                window.changeLogo("", false);

                                e.stopPropagation();
                            }).append('<i class="icon-remove"></i>'))));
                // create html dialog container
                var dialog = $("<div/>", {"class":"mobilize-dialog jsn-bootstrap" }).append(
                    $("<div/>", { "class":"popover bottom" }).css("display", "block").append(
                        $("<div/>", {"class":"arrow"})).append(
                        $("<h3/>", { "class":"popover-title", "text":title + " Settings"
                        })).append(
                        $("<div/>", { "class":"popover-content"}).append(cotentDialogLogoSrc).append(contentDialogLogoSlogan)));
                $("body").append(dialog);
                $("#jsn-alignment option").each(function () {
                    if ($(this).val() == mobilizeMenuItem.find("input.data-mobilize-alignment").val()) {
                        $(this).prop('selected', true);
                    }
                });
                var elmStyle = self.getBoxStyle($(dialog)),
                    parentStyle = self.getBoxStyle($(_this)),
                    parentStyleImg = self.getBoxStyle($("#jsn-logo img")),
                    position = {};
                position.left = parentStyle.offset.left - elmStyle.outerWidth / 2 + parentStyle.outerWidth / 2;
                if (parentStyleImg.height > parentStyle.height) {
                    position.top = parentStyle.offset.top + elmStyle.outerHeight + parentStyleImg.outerHeight / 2;
                } else {
                    position.top = parentStyle.offset.top + elmStyle.outerHeight + parentStyle.outerHeight;
                }
                dialog.css(position).click(function (e) {
                    e.stopPropagation();
                });
            },
            createPopupDialogMenuIcon:function (_this) {
                $('#container-select-style').css({'display':'none'});
                $(".mobilize-dialog").remove();
                $(".mobilize-edit").removeClass("mobilize-edit");
                var self = this;
                var dataValue = $(_this).attr("data-value");
                var dataType = $(_this).attr("data-type");
                var dataState = $(_this).attr("data-state");
                var contentDialogCheckEnable;
                var contentDialog = "";
                var title = $(_this).attr("title");
                var mobilizeMenuItem = $(_this).parent();
                $(_this).addClass("mobilize-edit");
                var filterModule = "";
                if (dataType == "mobilize-login") {
                    filterModule = "mod_login";
                }
                if (dataType == "mobilize-search") {
                    filterModule = "mod_search";
                }
                // content dialog type select module
                if (dataType != "mobilize-switcher") {
                    var mobilizeSelect = "JSN_MOBILIZE_SELECT_MODULE",
                        menuTitle = "";
                    if (dataType == "mobilize-menu") {
                        $.each(self.listMenu, function (i, item) {
                            if (item.id == dataValue) {
                                menuTitle = item.title;
                            }
                        });
                        mobilizeSelect = "JSN_MOBILIZE_SELECT_MENU";
                    } else {
                        menuTitle = self.listModule['getById'][dataValue];
                    }
                    contentDialog = $("<div/>", { "class":"control-group"}).append(
                        $("<label/>", { "class":"control-label", "text":self.lang[mobilizeSelect]})).append(
                        $("<div/>", {"class":"controls"}).append(
                            $("<div/>", {"class":"row-fluid input-append"}).append(
                                $("<input/>", { "class":"menu-title jsn-input-large-fluid", "disabled":"disabled", "type":"text", "value":menuTitle})
                            ).append(
                                $("<button/>", { "class":"btn", "onclick":"return false;", text:"..."}).click(function () {
                                    if (dataType == "mobilize-menu") {
                                        self.dialogChangeMenus();
                                    } else {
                                        self.dialogChangeModule(filterModule, 'changeModuleMenuIcon');
                                    }
                                })
                            ).append(
                                $("<button/>", { "class":"btn btn-icon", "onclick":"return false;"}).click(function (e) {
                                    var menu = mobilizeMenuItem.find("a.link-menu-mobilize.mobilize-edit");
                                    $(menu).attr("data-value", "");
                                    $(menu).next("input").attr({"data-id":"", "value":""});
                                    $(menu).trigger("click");
                                    e.stopPropagation();
                                }).append('<i class="icon-remove"></i>')
                            )
                        )
                    )
                } else {
                    contentDialog = $("<div/>", {"class":"control-group" }).append(
                        $("<label/>", {  "class":"control-label", "text":self.lang['JSN_MOBILIZE_SWITCHER_TITLE'] })).append(
                        $("<div/>", { "class":"controls" }).append(
                            $("<input/>", { "class":"switcher-title jsn-input-xlarge-fluid", "type":"text", "value":mobilizeMenuItem.find("button.btn-switcher").attr("data-value") }).bind('keyup',function () {
                                self.changeSwitcherSettings($(this).val());
                            }).change(function () {
                                self.changeSwitcherSettings($(this).val());
                            })
                        )
                    );
                    var labelEnable = "JSN_MOBILIZE_ENABLE_" + dataType.toUpperCase().replace("-", "_") + "_LINK";
                    contentDialogCheckEnable = $("<div/>", {
                        "class":"control-group"
                    }).append(
                        $("<label/>", {
                            "class":"control-label",
                            text:self.lang[labelEnable]
                        })).append(
                        $("<div/>", {
                            "class":"controls"
                        }).append(
                            $("<label/>", {
                                "class":"radio inline"
                            }).append(
                                $("<input/>", {
                                    "type":"radio",
                                    "name":$(_this).attr("data-type"),
                                    "value":"0",
                                    "class":"jsn-check-enable"
                                })).append(self.lang["JSN_MOBILIZE_NO"])).append(
                            $("<label/>", {
                                "class":"radio inline"
                            }).append(
                                $("<input/>", {"type":"radio", "name":$(_this).attr("data-type"), "value":"1", "class":"jsn-check-enable"})).append(self.lang["JSN_MOBILIZE_YES"])
                        )
                    );
                    dataState = dataState ? dataState : 0;
                    $(contentDialogCheckEnable).find("input.jsn-check-enable").each(function () {
                        if ($(this).val() == dataState) {
                            $(this).prop("checked", true);
                        } else {
                            $(this).prop("checked", false);
                        }
                    });
                    $(contentDialogCheckEnable).find("input.jsn-check-enable").change(function () {
                        var dataInput = {};
                        dataInput[ mobilizeMenuItem.find("button.btn-switcher").attr("data-value")] = $(this).val();
                        mobilizeMenuItem.find("input.data-mobilize").val($.toJSON(dataInput));
                        mobilizeMenuItem.find("button.btn-switcher").attr("data-state", $(this).val());
                    });
                }
                // create html dialog container
                var dialog = $("<div/>", {"class":"mobilize-dialog jsn-bootstrap" }).append(
                    $("<div/>", {"class":"popover bottom"}).css("display", "block").append(
                        $("<div/>", { "class":"arrow" })).append(
                        $("<h3/>", {"class":"popover-title", "text":title + " Settings"})).append(
                        $("<div/>", { "class":"popover-content"}).append(contentDialogCheckEnable).append(contentDialog)));
                $("body").append(dialog);

                var elmStyle = self.getBoxStyle($(dialog)),
                    elmStylePopover = self.getBoxStyle($(dialog).find(".popover")),
                    parentStyle = self.getBoxStyle($(_this)),
                    position = {};

                if (dataType != "mobilize-switcher") {
                    if (parentStyle.offset.left > elmStyle.outerWidth / 2 + 50 && ($(window).width() - parentStyle.offset.left) > elmStyle.outerWidth / 2 + 50) {
                        position.left = parentStyle.offset.left - elmStyle.outerWidth / 2 + parentStyle.outerWidth / 2;
                        position.top = parentStyle.offset.top + parentStyle.outerHeight;
                    } else if (($(window).width() - parentStyle.offset.left) < elmStyle.outerWidth) {
                        position.left = parentStyle.offset.left - elmStyle.outerWidth + parentStyle.outerWidth;
                        position.top = parentStyle.offset.top + parentStyle.outerHeight;
                        $(dialog).find(".arrow").css("left", elmStyle.outerWidth - (parentStyle.outerWidth) / 2);
                    } else {
                        position.left = parentStyle.offset.left - parentStyle.outerWidth / 2;
                        position.top = parentStyle.offset.top + parentStyle.outerHeight;
                        $(dialog).find(".arrow").css("left", parentStyle.outerWidth);
                    }
                } else {
                    position.left = parentStyle.offset.left - elmStyle.outerWidth / 2 + parentStyle.outerWidth / 2;
                    position.top = parentStyle.offset.top - elmStylePopover.height - parentStyle.height / 2;
                    $(dialog).find(".popover").attr("class", "popover top");
                }
                dialog.css(position).click(function (e) {
                    e.stopPropagation();
                });
            },
            deleteElement:function (e){
                var self = this;
                var cof = confirm(self.lang['JSN_MOBILIZE_DELETE']);
                if(cof){
                    e.parent().parent().remove();
                    return false;
                }else{
                    return false;
                }
            },
            registerEvent:function () {
                var self = this;
                $(".jsn-element-container div.jsn-element .element-edit").click(function (e) {
                    $(".ui-state-edit").removeClass("ui-state-edit");
                    var item = $(this).parent().parent();
                    $(item).addClass("ui-state-edit");
                    self.editItem(item);
                    e.stopPropagation();
                });

                $(".jsn-element-container").sortable({
                    connectWith:".jsn-element-container",
                    placeholder:'ui-state-highlight',
                    forcePlaceholderSize:true,
                    update:function (event, ui) {
                        if (ui.sender) {
                            var check = true,
                                active = "";
                            $(ui.item).attr("action", "move");
                            $(this).find("div.jsn-element").each(function () {
                                if ($(this).attr("data-value") == $(ui.item).attr("data-value") && $(this).attr("data-type") == $(ui.item).attr("data-type") && $(this).attr("action") != "move") {
                                    check = false;
                                    active = $(this);
                                }
                            });
                            $(ui.item).removeAttr("action");
                            if (check) {
                                var blockId = $(this).parent().attr("id");
                                var moduleStyle = $(this).parent().attr("data-module-style");
                                var moduleTitleStyle = $(this).parent().attr("data-title-style");
                                $(this).find(".jsn-element input.data-block-mobilize").each(function () {
                                    $(this).attr("name", "jsnmobilize[" + blockId + "][]");
                                });
                                $(this).find(".jsn-element").removeAttr("style");
                                $(this).find(".jsn-element").attr("style", moduleStyle);
                                $(this).find(".jsn-element .jsn-element-content").removeAttr("style");
                                $(this).find(".jsn-element .jsn-element-content").attr("style", moduleTitleStyle);

                            } else {
                                $(active).effect("highlight", {}, 3000);
                                $(ui.item).effect("highlight", {}, 3000);
                                $(ui.sender).append($(ui.item));
                                return false;
                            }
                        }
                    }
                }).disableSelection();
            },
            editItem:function (_this) {
                var self = this;
                $(".mobilize-list-edit").removeClass("mobilize-list-edit");
                $(".container-fluid .mobilize-update-jsn-element").removeClass("mobilize-update-jsn-element");
                $(_this).parent().parent().addClass("mobilize-list-edit");
                $(_this).addClass("mobilize-update-jsn-element");
                if ($(_this).attr("data-type") == "position") {
                    self.actionAddPosition();
                }
                if ($(_this).attr("data-type") == "module") {
                    self.actionAddModule('update');
                }
                updateItem = true;
            },
            //Save position
            saveItem:function (options) {
                var check = true;
                var self = this;
                var idContentAdd = $(".container-fluid .mobilize-list-edit").attr("id");
                var exIdContentAdd = idContentAdd.split('-');
                $(".mobilize-list-edit .jsn-element-container div.jsn-element").each(function () {
                    if ($(this).attr("data-value") == options.value && $(this).attr("data-type") == options.type) {
                        $(this).effect("highlight", {}, 3000);
                        check = false;
                    }
                });
                if (check) {
                    var name = $(".mobilize-list-edit").attr("id");
                    var idContetnBlock = "";
                    self.itemContent(options, name);
                    this.registerEvent();
                }
            },
            itemContent:function (options, name) {
                var self = this;
                var dataInput = {};
                dataInput[options.value] = options.type;
                var moduleStyle = $("#" + name).attr("data-module-style");
                var moduleTitleStyle = $("#" + name).attr("data-title-style");
                var contentItem = $("<div/>", {"class":"jsn-element ui-state-default jsn-iconbar-trigger ui-state-edit", "style":moduleStyle, "data-value":options.value, "data-type":options.type
                }).append(
                    $("<div/>", {"class":"jsn-element-content", style:moduleTitleStyle}).append(
                        $("<span/>", {"class":"type-element", "text":options.label + ": "})
                    ).append(
                        $("<span/>", {"class":"name-element", "text":options.name})
                    ).append(
                        $("<input/>", {"type":"hidden", "class":"data-block-mobilize", "name":"jsnmobilize[" + name + "][]", "value":$.toJSON(dataInput)})
                    )
                ).append(
                    $("<div/>", { "class":"jsn-iconbar"}).append(
                        $("<a/>", {"class":"element-edit", "href":"javascript:void(0)"}).click(function () {
                            self.editItem($(this).parent());
                        }).append('<i class="icon-pencil"></i>')).append(" ").append(
                        $("<a/>", {"class":"element-delete", "href":"javascript:void(0)"}).click(function () {
                            self.deleteElement($(this));
                        }).append('<i class="icon-trash"></i>')));
                if (updateItem) {
                    $("#" + name + ".mobilize-list-edit .jsn-element-container .jsn-element.mobilize-update-jsn-element").before(contentItem).remove();
                } else {
                    $("#" + name + ".mobilize-list-edit .jsn-element-container").append(contentItem);
                }
            },

            //Change logo
            changeLogo:function () {
                var mobilizeLogo = $(".mobilize-dialog").parent();
                var value = mobilizeLogo.find("input.data-mobilize").attr("data-id");
                var element = mobilizeLogo.find("a.element-edit").attr("data-type");
                var buttons = {};
                buttons[this.lang['JSN_MOBILIZE_CLOSE']] = $.proxy(function () {
                    this.modalchangeLogo.close();
                }, this);
                this.modalchangeLogo = new JSNModal({
                    url:this.pathRoot + "plugins/system/jsnframework/libraries/joomlashine/choosers/media/index.php?component=com_mobilize&root=&current=" + value + "&element=" + element + "&handler=changeLogo",
                    title:this.lang['JSN_MOBILIZE_SELECT_LOGO'],
                    buttons:buttons,
                    height:parent.document.body.clientHeight * 0.55,
                    width: parent.document.body.clientWidth * 0.7,
                    loaded:function (modalchangeLogo) {
                        modalchangeLogo.options.loaded = null;
                        modalchangeLogo.iframe[0].contentWindow.location.reload();
                    }
                });
                this.modalchangeLogo.show();
            },
            // Create preview modal window
            createPreview:function () {
                // Save all parameters to cookie
                var self = this;
                var jsnmobilize = {}, profileStyle = {},profileCustomCss=[],
                    parseValue = function (value) {
                        return (value.substr(0, 1) == '{' && value.substr(-1) == '}') ? $.evalJSON(value) : value;
                    };
                $("#design input").each(function () {
                    var i = $(this).attr("name");
                    if (i.indexOf("jsnmobilize") > -1) {
                        var k, v, tmp;
                        k = i.replace('jsnmobilize', '').replace(/(\[|\])/g, '');
                        if (document.adminForm[i].length) {
                            v = {};
                            // for (var i2 = 0; i2 < document.adminForm[i].length; i2++) {
                            for (var i2 = document.adminForm[i].length - 1; i2 >= 0; i2--) {
                                tmp = parseValue(document.adminForm[i][i2].value);
                                for (var i3 in tmp) {
                                    v[i3] = tmp[i3];
                                }
                            }
                        } else {
                            if (k != 'mobilize-menu-language')
                            {
                                v = parseValue(document.adminForm[i].value);
                            }
                            else
                            {
                                v = document.adminForm[i].value;
                            }
                        }
                        jsnmobilize[k] = v;
                    }
                });
                jsnmobilize['logo-alignment'] = $("input.data-mobilize-alignment").val();

                jsnmobilize['custom-css-code'] = $("#container-custom-css-hide #custom-css-code").val();
                $("input.jsn-input-style").each(function () {
                    var key = $(this).attr("id");
                    key = key.replace("input_style_", "");
                    if ($(this).val()) {
                        profileStyle[key] = $.evalJSON($(this).val());
                    }
                });

                $("#container-custom-css-hide #custom-css-list-file li input").each(function(){
                    profileCustomCss.push($(this).val());
                });
                jsnmobilize['mobilize-profile-style'] = profileStyle;
                jsnmobilize['custom-css-files'] = profileCustomCss;
                $.ajax({
                    type:"POST",
                    async:true,
                    url:"index.php?option=com_mobilize&view=profiles&task=profiles.saveSessionStyle&tmpl=component&" + this.token + "=1",
                    data:{
                        mobilize:$.toJSON(jsnmobilize)
                    },
                    success:function (msg) {
                        var height = $(window).height();
                        var buttons = {};
                        buttons[self.lang['JSN_MOBILIZE_CLOSE']] = $.proxy(function () {
                            self.closeModalBox();
                        }, this);
                        var url = self.params['pathRoot'] + (self.params['pathRoot'].indexOf('?') > -1 ? '&' : '?') + 'jsn_mobilize_preview=1';
                        var createIframe = $("<iframe/>"+ url, {
                            "class":"jsn-mobilize-preview hide",
                            "src":url,
                            height:height * (90 / 100),
                            width:'100%'
                        }).load(function () {
                            // $(".jsn-bgloading").remove();
                            self.changeViewMobilize();
                            $(createIframe).removeClass("hide");
                            $("#mobilize-design #jsn-mobilize").hide();
                            $(".jsn-mobilize-preview").show();
                            $(".jsn-modal-overlay,.jsn-modal-indicator").remove();
                        });
                        /*
                         $("#mobilize-design").append($("<div/>", {
                         "class":"jsn-bgloading"
                         }).append($("<i/>", {
                         "class":"jsn-icon32 jsn-icon-loading"
                         })));*/
                        $("#mobilize-design .jsn-mobilize-preview").remove();
                        //  $("#mobilize-design #jsn-mobilize").hide();
                        // $("#mobilize-design").append(createIframe);
                        $("#mobilize-design").append(createIframe);
                        $(".jsn-mobilize-preview").hide();
                    }
                });
            },
            //Close modal window
            closeModalBox:function () {
                var self = this;
                self.modalMobilize.close();
                $(".jsn-modal").remove();
            },
            // Action add postion
            actionAddPosition:function () {
                var height = $(window).height();
                var width = $(window).width();
                var buttons = {};
                var self = this;
                buttons[this.lang['JSN_MOBILIZE_CLOSE']] = $.proxy(function () {
                    self.closeModalBox();
                }, this);
                this.modalMobilize = new JSNModal({
                    url:'index.php?option=com_mobilize&view=positions&tmpl=component&function=jSelectPosition&filter_template=' + this.defaultTemplate + '&filter_type=template&filter_state=1&' + this.token + '=1',
                    title:this.lang['JSN_MOBILIZE_SELECT_POSITION'],
                    buttons:buttons,
                    height:height * (95 / 100),
                    width:width * (95 / 100),
                    scrollable:true
                });
                this.modalMobilize.show();
            },
            dialogChangeMenus:function () {
                var self = this;
                var height = $(window).height();
                var width = $(window).width();
                var frameId = 'mobilize-chosen-language';
                var buttons = {};
                var dataInput = {};
                var dataLanguage = {};
                var list = [];
                buttons[this.lang['JSN_MOBILIZE_SAVE']] = $.proxy(function () {
                    $(".container-fluid .mobilize-list-edit").removeClass("mobilize-list-edit");
                    var jParent = window.parent.jQuery.noConflict();
                    var iframe_content = jParent('#' + frameId).contents();
                    iframe_content.find('table.table-popup tr.jsnhover[data-language]').each(function(){
                        var vlAt = $(this).attr('data-title');
                        var vlId = $(this).attr('data-id');
                        var vlLg =  $(this).attr('data-language');
                        if(vlLg == $('.data-language').val())
                        {
                            dataInput[vlId] = vlAt;
                        }
                        if(vlLg == 'all'){
                            dataInput[vlId] = vlAt;
                        }
                        dataLanguage['jsn_menu_id'] = vlId;
                        dataLanguage['jsn_menu_title'] = vlAt;
                        dataLanguage['jsn_menu_language'] = vlLg;
                        if(dataLanguage['jsn_menu_language'] != null && dataLanguage['jsn_menu_language'] != '')
                        {
                            list.push(JSON.stringify(dataLanguage));
                        }
                    });
                    var val = $.toJSON(dataInput);

                    $(".mobilize-menu .link-menu-mobilize").parent().find("[name^='jsnmobilize[mobilize-menu]']").val(val.toString());
                    $(".mobilize-menu .link-menu-mobilize").parent().find("[name^='jsnmobilize[mobilize-menu-language]']").val('['+ list.join(',') +']');
                    self.closeModalBox();
                }, this);
                buttons[this.lang['JSN_MOBILIZE_CANCEL']] = $.proxy(function () {
                    $(".container-fluid .mobilize-list-edit").removeClass("mobilize-list-edit");
                    self.closeModalBox();
                }, this);
                $('#container-select-style').css({'display':'none'});
                this.modalMobilize = new JSNModal({
                    frameId: frameId,
                    url:"index.php?option=com_mobilize&view=menus&layout=default&tmpl=component&jsnaction=update&" + this.token + "=1",
                    title:this.lang['JSN_MOBILIZE_SELECT_MENU'],
                    buttons:buttons,
                    height:height * (95 / 100),
                    width:width * (95 / 100),
                    scrollable:true,
                    open: function(){
                        var jParent = window.parent.jQuery.noConflict();
                        var iframe_content = jParent('#' + frameId).contents();
                        var languagerow = iframe_content.find('table.table-popup tr.jsnhover');
                        var mobilizeLanguage = iframe_content.find('table.table-popup tr.jsnhover[data-id]').find('.mobilize-language');
                        //var result = $.parseJSON($('.data-mobilize-language').val());
                        var result = $('.data-mobilize-language').val();
                        var id, lang;
                        if (result != '"[]"') {
                            result = JSON.parse(result);
                            $(result).each(function (i, value) {
                                id = value['jsn_menu_id'];
                                lang = value['jsn_menu_language'];
                                iframe_content.find('table.table-popup tr.jsnhover').attr('data-id')
                                mobilizeLanguage.each(function () {
                                    var self = this;
                                    if ($(this).parents('tr.jsnhover').attr('data-id') == id) {
                                        $(this).parents('tr.jsnhover').find('.mobilize-language').val(lang);
                                        $(this).parents('tr.jsnhover').attr('data-language', lang);
                                        mobilizeLanguage
                                            .filter(function () {
                                                return self == this ? false : true;
                                            })
                                            .find('option[value=' + lang + ']')
                                            .attr('disabled', 'disabled');
                                    }
                                    if ($(this).parents('tr.jsnhover').attr('data-language') == 'all') {
                                        mobilizeLanguage
                                            .filter(function () {
                                                return self == this ? false : true;
                                            })
                                            .attr('disabled', 'disabled')
                                    }
                                });
                            });
                        }
                        mobilizeLanguage.change(function(){
                            var self = this;
                            var optionValue = $(this).val();
                            var oldValue = $(this).parents('tr.jsnhover').attr('data-language');

                            mobilizeLanguage.each(function(){
                                $(this).children().each(function(){
                                    if($(this).val() == oldValue && $(this).val() != optionValue)
                                    {

                                        $(this).removeAttr('disabled');
                                    }
                                });
                            });

                            $(this).parents('tr.jsnhover').attr('data-language', $(this).val());

                            if(optionValue == 'all')
                            {
                                mobilizeLanguage
                                    .filter(function() {
                                        return self == this ? false : true;
                                    })
                                    .attr('disabled', 'disabled')
                                    .val('')
                                    .parents('tr.jsnhover')
                                    .removeAttr('data-language');
                                mobilizeLanguage.find('option').removeAttr('disabled');
                            }
                            else
                            {
                                mobilizeLanguage.removeAttr('disabled');
                            }

                            if(optionValue != '')
                            {


                                mobilizeLanguage
                                    .filter(function(){
                                        return self == this ? false : true;
                                    })
                                    .find('option[value=' + optionValue + ']')
                                    .attr('disabled', 'disabled');
                            }

                        });
                    }
                });
                this.modalMobilize.show();
            },
            dialogChangeModule:function (filter, getfunction) {
                var self = this;
                var height = $(window).height();
                var width = $(window).width();
                var buttons = {};
                buttons[this.lang['JSN_MOBILIZE_CANCEL']] = $.proxy(function () {
                    $(".container-fluid .mobilize-list-edit").removeClass("mobilize-list-edit");
                    self.closeModalBox();
                }, this);
                this.modalMobilize = new JSNModal({
                    url:'index.php?option=com_mobilize&view=modules&layout=default&tmpl=component&function=' + getfunction + '&filter_client_id=0&filter_state=1&filter_module=' + filter + "&jsnaction=update&modulesAction=menu&" + this.token + "=1",
                    title:this.lang['JSN_MOBILIZE_SELECT_MODULE'],
                    buttons:buttons,
                    height:height * (95 / 100),
                    width:width * (95 / 100),
                    scrollable:true
                });
                this.modalMobilize.show();
            },
            // Action add module
            actionAddModule:function (action) {
                var self = this;
                var height = $(window).height();
                var width = $(window).width();
                var buttons = {};
                if (action != "update") {
                    buttons[this.lang['JSN_MOBILIZE_SELECT']] = $.proxy(function () {
                        var moduleList = $.getModuleList(),
                            blackList = [];
                        $(".mobilize-list-edit .jsn-element-container .jsn-element").each(function () {
                            if ($(this).attr("data-type") == "module") {
                                var check = true,
                                    _this = this;
                                $.each(moduleList, function (i, val) {
                                    if (val.value == $(_this).attr("data-value")) {
                                        check = false;
                                        blackList.push(val.value);
                                    }
                                });
                                if (check) {
                                    $(this).remove();
                                }
                            }
                        });
                        $.each(moduleList, function (i, val) {
                            if ($.inArray(val.value, blackList) < 0) {
                                val.type = "module";
                                val.label = self.lang["JSN_MOBILIZE_TYPE_MODULE"];
                                self.saveItem(val);
                            }
                        });
                        $(".mobilize-dialog").remove();
                        self.addText();
                        self.closeModalBox();
                    }, this);
                }
                buttons[this.lang['JSN_MOBILIZE_CLOSE']] = $.proxy(function () {
                    self.closeModalBox();
                }, this);
                this.modalMobilize = new JSNModal({
                    url:'index.php?option=com_mobilize&view=modules&layout=default&tmpl=component&function=jSelectModules&filter_client_id=0&filter_state=1&filter_module=&jsnaction=' + action + '&' + this.token + '=1',
                    title:this.lang['JSN_MOBILIZE_SELECT_MODULE'],
                    buttons:buttons,
                    height:height * (95 / 100),
                    width:width * (95 / 100),
                    scrollable:true
                });
                this.modalMobilize.show();
            },
            getBoxStyle:function (element) {
                var style = {
                    width:element.width(),
                    height:element.height(),
                    outerHeight:element.outerHeight(),
                    outerWidth:element.outerWidth(),
                    offset:element.offset(),
                    margin:{
                        left:parseInt(element.css('margin-left')),
                        right:parseInt(element.css('margin-right')),
                        top:parseInt(element.css('margin-top')),
                        bottom:parseInt(element.css('margin-bottom'))
                    },
                    padding:{
                        left:parseInt(element.css('padding-left')),
                        right:parseInt(element.css('padding-right')),
                        top:parseInt(element.css('padding-top')),
                        bottom:parseInt(element.css('padding-bottom'))
                    }
                };
                return style;
            }
        }
        return JSNMobilizeProfileView;
    })