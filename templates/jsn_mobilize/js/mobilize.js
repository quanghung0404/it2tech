/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($) {
    $(function () {
        $.mobilizeInit = function () {
            $("#jsn-menu .jsn-menu-mobile").removeClass("jsn-menu-mobile");
            $("ul.jsn-menu-mobile").each(function () {
                if (!$(this).hasClass("jsn-toggle")) {
                    $(this).find("li.parent>ul").hide();
                }
            });
            $.setMoblieMenu($("#jsn-menu ul.jsn-mainmenu>li>span.jsn-menu-toggle"));
            $.setMoblieMenu($("#jsn-menu ul.jsn-sidetool>li>span.jsn-menu-toggle"));
            $.jsnMenuToggle();
            //this.stickyMenu();
			
        };
        $.setMoblieMenu = function (menuClass) {
            var self = this;
            var offsetTopMenu = $("#jsn-menu").offset();
            $(menuClass).click(function () {
                if ($(this).hasClass("active")) {
                    $(this).removeClass("active");
                    $(this).next().removeClass("jsn-menu-mobile");
                    if ($("#jsn-menu").css("position") == "relative") {
                        $("#jsn-menu").removeAttr("style");
                    } else if ($("#jsn-menu").offset().top < offsetTopMenu.top) {
                        var topMenu = offsetTopMenu.top, menu = $("#jsn-menu");
                        menu.clearQueue();
                        $(".jsn-menu-placeholder").remove();
                        menu.removeAttr("style");
                        menu.css({top:-offsetTopMenu.top}).animate({ top:"0px"}, 300, 'linear');
                    }
                }
                else {
                    $("ul.mobilize-menu .active").removeClass("active");
                    $("ul.mobilize-menu .sub-menu-active").removeClass("sub-menu-active");
                    $("ul.mobilize-menu .jsn-menu-mobile").removeClass("jsn-menu-mobile");
                    $("ul.mobilize-menu li.parent ul").removeAttr("style");
                    $(this).addClass("active");
                    $(this).next().addClass("jsn-menu-mobile");

                }
            });
        };
        $.jsnMenuToggle = function () {
            var self = this;
            $("ul.jsn-mainmenu > li > ul > li.parent,.jsn-modulecontent ul.jsn-toggle>li.parent").each(function () {
                var MenuParent = $(this).find("ul").first();
                $(MenuParent).hide();
                $(MenuParent).before("<span class=\"jsn-menu-toggle\"></span>");
                var subMenuToggle = $(this).find("span.jsn-menu-toggle").first();
                $(subMenuToggle).click(function () {
                    if ($(this).hasClass("active")) {
                        $(this).parent().removeClass("sub-menu-active");
                        $(this).removeClass("active");
                        $(this).next("ul").hide();
                    } else {
                        $(this).parent().addClass("sub-menu-active");
                        $(this).height($(this).parent().height());
                        $(this).addClass("active");
                        $(this).next("ul").show();
                    }
                });
            });
        };
		var cmd=0;
		$('#jsn-menu').find('.jsn-sidetool').each(function (){
			if(!$.trim($(this).html())){
				cmd=1;
				$(this).hide();
			}
		});
		if(cmd === 1){
			$('#jsn-menu').find('.jsn-mainmenu').each(function (){
				cmd =2;
			});
			if(cmd===1){
				$('#jsn-menu').hide();
			}
		}
        $('img.jsn-mobilize-image-loading').each(function(i, img) {
            $.ajax({
                url: $(img).attr('_src'),
                complete: $.proxy(function(jqXHR, textStatus) {
                    // Check response header for optimized image link
                    if (jqXHR.responseText.match(/http(s?):/i)) {
                        $(this).attr('_src', jqXHR.responseText);

                        $.ajax({
                            url: jqXHR.responseText,
                            complete: $.proxy(function() {
                                $(this)
                                    .attr('alt', $(this).attr('_alt') || '')
                                    .attr('class', $(this).attr('_class') || '')
                                    .attr('src', $(this).attr('_src'))
                                    .removeAttr('_alt')
                                    .removeAttr('_class')
                                    .removeAttr('_src');
                            }, this)
                        });
                    } else {
                        $(this).attr('alt', jqXHR.responseText).attr('class', 'label label-important');
                    }
                }, img)
            });
        });
        jQuery(document).ready(function () {
            $.mobilizeInit();
        });
    });
})(jQuery);
