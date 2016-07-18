/**
 * @package         Tooltips
 * @version         4.1.5PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

var nnTooltipsPopup = null;

(function($) {
	"use strict";

	$(document).ready(function() {
		setTimeout(function() {
			nnTooltipsPopup.init();
		}, 1000);
	});

	nnTooltipsPopup = {
		params   : {
			tip_type : ['text', 'image'],
			link_type: ['text', 'image'],
			mode     : ['hover', 'sticky', 'click'],
			position : ['top', 'bottom', 'left', 'right'],
		},
		internals: ['tip_type', 'link_type'],

		init: function() {
			this.fillFromSelection();

			$('.nn_overlay').css('cursor', '').fadeOut();
		},

		setDefault: function(el) {
			$('.' + $(el).parent().attr('id') + '_icon').show();
			this.closeOtherDefaults(el);
		},

		closeOtherDefaults: function(el) {
			var self = this;
			var $el = $(el);

			$('input[name$="[default]"][value="1"]').each(function($i, input) {
				if ($(input).attr('name') == $el.attr('name')) {
					return;
				}

				$('.' + $(input).parent().attr('id') + '_icon').hide();
				self.setRadioOption($(input).attr('name'), 0);
			});
		},

		fillFromSelection: function() {
			var self = this;
			var selection = this.getSelection();

			if (!selection) {
				return;
			}

			var form = document.getElementById('tooltipsForm');

			var params = this.getParamsFromSelection(selection);

			if (!params) {
				return;
			}

			var id = 'tooltip';

			$('input[type="text"],textarea,input[type="hidden"]').each(function(i, input) {
				var param_type = $(input).attr('id').substr(id.length + 1);

				if (typeof params[param_type] == "undefined") {
					return;
				}

				form[$(input).attr('id')].value = params[param_type];
			});

			if (params.content) {
				$('#' + id + '_content').html(params.content).show();
			}

			if (typeof params.default != "undefined") {
				this.setRadioOption(id + '[default]', (params.default ? 1 : 0));
			}

			if (params.nested_id) {
				this.setRadioOption(id + '[nested]', 1);
				form[id + '[nested_id]'].value = params.nested_id;
			}

			$.each(this.params, function(param_type, param_set) {
				if (typeof params[param_type] == "undefined") {
					return;
				}

				self.setRadioOption(id + '[' + param_type + ']', params[param_type]);
			});
		},

		getSelection: function() {
			var editor_textarea = window.parent.document.getElementById(tooltips_editorname);
			if (!editor_textarea) {
				return false;
			}

			var iframes = editor_textarea.parentNode.getElementsByTagName('iframe');
			if (!iframes.length) {
				return false;
			}

			var editor_frame = iframes[0];
			var contentWindow = editor_frame.contentWindow;
			var selection = '';

			if (typeof contentWindow.getSelection != "undefined") {
				var sel = contentWindow.getSelection();
				if (sel.rangeCount) {
					var container = contentWindow.document.createElement("div");
					var len = sel.rangeCount
					for (var i = 0; i < len; ++i) {
						container.appendChild(sel.getRangeAt(i).cloneContents());
					}
					selection = container.innerHTML;
				}
			} else if (typeof contentWindow.document.selection != "undefined") {
				if (contentWindow.document.selection.type == "Text") {
					selection = contentWindow.document.selection.createRange().htmlText;
				}
			}

			return selection;
		},

		getParamsFromSelection: function(selection) {
			var self = this;

			if (selection.indexOf(tooltips_tag_characters[0] + tooltips_tag) == -1
				|| selection.indexOf(tooltips_tag_characters[0] + '/' + tooltips_tag) == -1) {
				return false;
			}

			var pos = selection.indexOf(tooltips_tag_characters[0] + tooltips_tag) + (tooltips_tag_characters[0] + tooltips_tag).length;
			selection = selection.substr(pos, selection.indexOf(tooltips_tag_characters[0] + '/' + tooltips_tag) - pos).trim();

			params = {};

			var tip = selection.substr(0, selection.indexOf('}'));
			tip = tip.replace(/\s*\|\s*/g, '|');

			params.link_text = selection.substr(selection.indexOf('}') + 1);

			$.each(this.params, function(param_type, param_set) {
				if (self.internals.indexOf(param_type) != -1) {
					return;
				}

				$.each(param_set, function(p, param) {
					var regex = new RegExp('\\|' + param + '(\\||$)');
					if (!tip.match(regex)) {
						return;
					}

					params[param_type] = param;
					tip = tip.replace(regex, '$1');
				});
			});

			if (tip.indexOf('|') != -1) {
				var classes = tip.split('|');
				tip = classes.shift();
				params.class = classes.join(' ');
			}

			params.tip_text = tip;

			var image_regex = new RegExp('^<img ([^>]*src="([^"]*)"[^>]*?)\s*/?>$');

			$.each(['tip', 'link'], function(i, tip_link) {
				var image_check = params[tip_link + '_text'].trim().replace('&lt;', '<').replace('&gt;', '>').replace('&quot;', '"');
				var image = image_check.match(image_regex);

				if (!image) {
					return;
				}

				params[tip_link + '_type'] = 'image';
				params[tip_link + '_image'] = image[2];
				params[tip_link + '_image_attribs'] = image[1]
					.replace('src="' + params[tip_link + '_image'] + '"', '')
					.replace(/data-mce-src=".*?"/, '')
					.replace(/\s+/, ' ').trim();
			});

			if (params.tip_type != 'image') {
				if (tip.indexOf('::') != -1) {
					params.tip_title = tip.substr(0, selection.indexOf('::'));
					tip = tip.substr(selection.indexOf('::') + 2)
				}

				params.tip_text = tip;
			}

			return params;
		},

		insertText: function() {
			var self = this;

			var tip = this.getTip();

			if (tip == '') {
				alert(window['tooltips_error_empty_' + this.getValue('tip_type')]);
				return false;
			}

			var html = tooltips_tag_characters[0] + tooltips_tag + ' ' + tip + tooltips_tag_characters[1]
				+ this.getLink()
				+ tooltips_tag_characters[0] + '/' + tooltips_tag + tooltips_tag_characters[1];

			window.parent.jInsertEditorText(html, tooltips_editorname);

			return true;
		},

		getTypeValue: function(group) {
			var type = this.getValue(group + '_type');

			if (type == 'image') {
				return this.getValue(group + '_image');
			}

			return this.getValue(group + '_text');
		},

		getTip: function() {
			var tip = this.getTypeValue('tip');

			if (tip == '') {
				return '';
			}

			var tip_type = this.getValue('tip_type');

			if (tip_type == 'image') {
				var tip_image_attribs = this.getValue('tip_image_attribs');

				tip = '<img ' + ('src="' + tip + '" ' + tip_image_attribs).trim() + ' />';
				tip = tip.replace('<', '&lt;').replace('>', '&gt;');
			} else if (this.getValue('tip_title')) {
				tip = this.getValue('tip_title') + '::' + tip;
			}

			var tip_class = this.getValue('class');

			if (tip_class) {
				tip += '|' + tip_class;
			}

			$.each(this.params, function(param_type, param_set) {
				if (self.internals.indexOf(param_type) != -1) {
					return;
				}

				var val = self.getValue(param_type);

				if (!val) {
					return;
				}

				tip += '|' + val;
			});

			return tip;
		},

		getLink: function() {
			var link = this.getTypeValue('link');

			if (link == '') {
				return tooltips_text_placeholder;
			}

			if (this.getValue('link_type') != 'image') {
				return link;
			}

			return link = '<img src="' + link + '" ' + this.getValue('link_image_attribs') + ' />';
		},

		getValue: function(name) {
			var field = $('[name="tooltip\\[' + name + '\\]"]');

			if (field.attr('type') == 'radio') {
				field = $('[name="tooltip\\[' + name + '\\]"]:checked');
			}

			return field.val().trim();
		},

		setRadioOption: function(name, value) {
			var name = name.replace('[', '\\[').replace(']', '\\]');
			var inputs = $('input[name="' + name + '"]');
			var input = $('input[name="' + name + '"][value="' + value + '"]');

			$('label[for="' + input.attr('id') + '"]').click();
			inputs.attr('checked', false);
			input.attr('checked', true).click();
		},

		setSelectOption: function(name, value) {
			var name = name.replace('[', '\\[').replace(']', '\\]');
			var select = $('select[name="' + name + '"]');
			var option = $('select[name="' + name + '"] option[value="' + value + '"]');

			select.attr('value', value).click();
			select.attr('selected', true).click();
		}
	}
})
(jQuery);
