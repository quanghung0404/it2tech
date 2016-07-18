define([
    'jquery'],

    function ($) {
        var JSNMobilizeModulesView = function () {
            this.init();
        };
        var moduleList = window.parent.jQuery.getModuleList();
        JSNMobilizeModulesView.prototype = {
            init:function () {
                var self = this;
                $("table.table-popup tr.jsnhover").click(function () {
                    if (self.getParameterByName("modulesAction") == "menu") {
                        var getFunction = self.getParameterByName("function");
                        if (getFunction == "changeModuleMenuText") {
                            window.parent.jQuery.changeModuleMenuText($(this).attr("data-id"), $(this).attr("data-title"), 'update');
                        } else {
                            window.parent.jQuery.changeModuleMenuIcon($(this).attr("data-id"), $(this).attr("data-title"), 'update');
                        }
                    } else {
                        window.parent.jQuery.jSelectModules($(this).attr("data-id"), $(this).attr("data-title"), 'update');
                    }
                })
				$("table.table-popup tr th").find('input').each(function (){
					if($(this).attr('class') === 'checkall'){
						$(this).change(function (){
							if ($(this).is(':checked') == true) {
								$(this).prop("checked", true);
								$(".checkbox-items input[type=checkbox]").each(function () {
									if ($(this).is(':checked') == false) {
										$(this).prop("checked", true);
										self.changeModule(1, $(this));
									}
								})
							} else {
								$(this).prop("checked", false);
								$(".checkbox-items input[type=checkbox]").each(function () {
									$(this).prop("checked", false);
									self.changeModule(0, $(this));
								})
							}
						})
					}
				})
                $(".checkbox-items input[type=checkbox]").each(function () {
                  var thisCheckbox = $(this)
                    $.each(moduleList, function (i, val) {
                        if ($(thisCheckbox).val() == val.value) {
                            $(thisCheckbox).prop("checked", true);
                        }
                    });
                })
                $(".checkbox-items input[type=checkbox]").change(function (e) {
                    var state = 0;
                    if ($(this).is(':checked') == true) {
                        state = 1;
                    } else {
                        state = 0;
                    }
                    self.changeModule(state, $(this));
                })
            },
            getParameterByName:function (name) {
                name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
                var regexS = "[\\?&]" + name + "=([^&#]*)";
                var regex = new RegExp(regexS);
                var results = regex.exec(window.location.search);
                if (results == null) return "";
                else return decodeURIComponent(results[1].replace(/\+/g, " "));
            },

            changeModule:function (state, _this) {
                if (state == 1) {
                    var optionsModule = new Object();
                    optionsModule.value = $(_this).val();
                    optionsModule.name = $(_this).attr("data-title");
                    moduleList.push(optionsModule);
                    window.parent.jQuery.setModuleList(moduleList)
                }
                if (state == 0) {
                    var tmpList = [];
                    $.each(moduleList, function (i, val) {
                        if ($(_this).val() != val.value) {
                            tmpList.push(val);
                        }
                    });
                    moduleList = tmpList;
                    window.parent.jQuery.setModuleList(moduleList);
                }
            }
        }
        return JSNMobilizeModulesView;
    });