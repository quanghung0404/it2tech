/**
 * Description for file (if any)...
 *
 * @category Functions
 * @package Unifom
 * @author JoomlaShine.com
 * @copyright JoomlaShine.com
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 * @version $Id: 
 * @link JoomlaShine.com
 */
define([
	'jquery',
	'jsn/libs/modal',
	'uniform/dialogedition',
	'jquery.ui',
	'jquery.zeroclipboard'],
function($, JSNModal, JSNUniformDialogEdition) {
	var edition = "";
	var JSNUniformLaunchpadView = function(params) {
		this.params = params;
		this.lang = params.language;
		this.baseZeroClipBoard = params.baseZeroClipBoard;
		edition = params.edition;
		this.init();
	}
	JSNUniformLaunchpadView.prototype = {
		init: function() {
			this.selectFormId = $("#filter_form_id");
			this.btnEditForm = $("#edit-form");
			this.btnGoLink = $("#jsn-go-link");
			this.btnNewForm = $("#new-form");
			this.selectPresentation = $("#presentation_method");
			this.selectMenuType = $("#menutype");
			this.btnUpgadeEdition = $("#btn-upgade-edition");
			var self = this;
			this.JSNUniformDialogEdition = new JSNUniformDialogEdition(this.params);
			this.selectFormId.change(function() {
				if($(this).val() == 0) {
					self.btnEditForm.addClass("disabled");
					self.btnEditForm.attr("title", self.lang['JSN_UNIFORM_YOU_MUST_SELECT_SOME_FORM']);
					self.selectPresentation.find("option").each(function(i) {
						if(i == 0) {
							$(this).prop('selected', true);
						}
					});
					self.selectPresentation.attr("disabled", "disabled").trigger("change");
				} else {
					self.btnEditForm.removeClass("disabled");
					self.btnEditForm.attr("title", self.lang['JSN_UNIFORM_EDIT_SELECTED_FORM']);
					self.selectPresentation.removeAttr("disabled");
				}
			}).trigger("change");
			this.selectPresentation.change(function() {
				if($(this).val() == "") {
					self.btnGoLink.addClass("disabled");
				} else {
					self.btnGoLink.removeClass("disabled");
				}
				if($(this).val() == "menu") {
					self.selectMenuType.show();
					self.selectMenuType.removeAttr("disabled");
					self.selectMenuType.change(function() {
						if($(this).val() == "") {
							self.btnGoLink.addClass("disabled");
						} else {
							self.btnGoLink.removeClass("disabled");
						}
					}).trigger("change");
				} else {
					self.selectMenuType.hide();
					self.selectMenuType.attr("disabled", "disabled");
				}
			}).trigger("change");
			this.btnUpgadeEdition.click(function() {
				window.location.href = 'index.php?option=com_uniform&view=upgrade';
				return false;
			});
			this.btnNewForm.click(function() {
				if(edition.toLowerCase() == "free" && $("select.#filter_form_id option").length > 3) {
					JSNUniformDialogEdition.createDialogLimitation($(this), self.lang["JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_FORM_IN_FREE_EDITION"]);
				} else {
					self.creatModalEditForm("index.php?option=com_uniform&view=form&task=form.edit&tmpl=component");
				}

				return false;

			});
			this.btnEditForm.click(function() {
				if(!$(this).hasClass("disabled")) {
					self.creatModalEditForm("index.php?option=com_uniform&view=form&task=form.edit&tmpl=component&form_id=" + $("#filter_form_id").val());
					return false;
				} else {
					$(this).attr("href", "javascript:void(0);");
					return false;
				}
			});
			$.getSetModal = function(formId) {
				self.loadListForm(formId);
			}
			// close modal box
			$.closeModalBox = function() {
				self.jsnUniformModal.close();
				$(".jsn-modal").remove();
			}

			this.btnGoLink.click(function() {
				if(!$(this).hasClass("disabled")) {
					var valuePresentation = $("#presentation_method").val();
					var valueMenu = $("#menutype").val();
					if(valuePresentation == "menu") {
						$(this).attr("href", 'index.php?option=com_uniform&task=launchAdapter&type=menu&menutype=' + valueMenu + '&' + 'form_id=' + $("#filter_form_id").val());
					}
					if(valuePresentation == "module") {
						$(this).attr("href", 'index.php?option=com_uniform&task=launchAdapter&type=module&form_id=' + $("#filter_form_id").val());
					}
					if(valuePresentation == "plugin") {
						$(this).attr("href", "javascript:void(0);");
						var valuePlugin = "{uniform form=" + $("#filter_form_id").val() + "/}";
						$("#syntax-plugin").val(valuePlugin);
						$("#dialog-plugin").dialog("open");
						ZeroClipboard.moviePath = self.baseZeroClipBoard;
						var clipboard = new ZeroClipboard.Client();
						clipboard.glue('jsn-clipboard-button', 'dialog-plugin', {
							"z-index": "9999"
						});
						clipboard.setText($("#syntax-plugin").val());
						$("#syntax-plugin").change(function() {
							clipboard.setText($("#syntax-plugin").val());
						});
						clipboard.addEventListener('complete', function(client, text) {
							if($("#syntax-plugin").val() != '') {
								$(".jsn-clipboard-checkicon").addClass('jsn-clipboard-coppied');
								setTimeout(function() {
									$(".jsn-clipboard-checkicon").delay(6000).removeClass('jsn-clipboard-coppied');
								}, 2000);
							}
						});
					}
				} else {
					$(this).attr("href", "javascript:void(0);");
					return false;
				}
			});
			$("#dialog-plugin").dialog({
				height: 300,
				width: 500,
				title: self.lang['JSN_UNIFORM_LAUNCHPAD_PLUGIN_SYNTAX'],
				draggable: false,
				resizable: false,
				autoOpen: false,
				modal: true,
				buttons: {
					Close: function() {
						$(this).dialog("close");
					}
				}
			});

		},
		loadListForm: function(formId) {
			$.ajax({
				type: "GET",
				async: true,
				url: "index.php?option=com_uniform&view=forms&task=forms.getListForm",
				dataType: 'json',
				success: function(response) {
					$("#filter_form_id").html("");
					$("select#filter_form_id").append($("<option/>", {
						"value": "",
						"text": "- Select Form -"
					}));
					$.each(response, function(index, value) {
						if(formId == value.form_id) {
							$("#filter_form_id").append(
							$("<option/>", {
								"value": value.form_id,
								"text": value.form_title,
								"selected": "selected"
							}))
						} else {
							$("#filter_form_id").append(
							$("<option/>", {
								"value": value.form_id,
								"text": value.form_title
							}))
						}

					})
					$("#filter_form_id").trigger("change");
				}
			});
		},
		//Create modal box Edit Form
		creatModalEditForm: function(src) {
			$(".jsn-modal").remove();
			var height = $(window).height();
			var width = $(window).width();
			var buttons = {};
			buttons["Save"] = $.proxy(function() {
				this.jsnUniformModal.iframe[0].contentWindow.parentSaveForm();
			}, this);
			buttons["Close"] = $.proxy(function() {
				$.closeModalBox();
			}, this);
			this.jsnUniformModal = new JSNModal({
				url: src,
				title: "Form Settings",
				buttons: buttons,
				height: height * (95 / 100),
				width: width * (95 / 100),
				scrollable: true
			});
			this.jsnUniformModal.show();
		}
	}
	return JSNUniformLaunchpadView;
});