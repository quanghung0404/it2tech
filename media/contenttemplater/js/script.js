/**
 * Javascript file
 *
 * @package         Content Templater
 * @version         5.1.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function($) {
	ContentTemplater = {
		// private property
		_keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
		_timer : null,

		getXML: function(id, editorname, nocontent) {
			var overlay = $('<div/>').css({
				backgroundColor: 'black',
				position       : 'absolute',
				left           : 0,
				top            : 0,
				width          : '100%',
				height         : '100%',
				zIndex         : 5000,
				opacity        : .4
			}).hide().on('click', function() {
				window.parent.SqueezeBox.close();
			}).appendTo('#sbox-content');

			overlay.css('cursor', 'wait').fadeIn();

			if (!nocontent) {
				nocontent = 0;
			}
			var self = this;

			var url = 'index.php?nn_qp=1&folder=plugins.editors-xtd.contenttemplater&file=popup.php&id=' + id + '&nocontent=' + nocontent;
			nnScripts.loadajax(url, 'ContentTemplater._insertTexts( data, \'' + editorname + '\' )');
		},

		_fixTop: function() {
			this.overlay.style.top = document.documentElement.scrollTop + 'px';
		},

		_insertTexts: function(data, editorname) {
			var data = this._decode(data);
			data = data.split('[/CT]');

			var params = {};
			for (i = 0; i < data.length; i++) {
				if (data[i].indexOf('[CT]') != -1) {
					vals = data[i].split('[CT]');
					key = vals[1].trim();
					params[key] = {};
					params[key]['default'] = vals[2].trim();
					params[key]['value'] = vals[3].trim();
				}
			}

			var override = 0;
			var has_content = 0;

			// check if settings override is set and if template has content
			for (key in params) {
				var param = params[key];
				if (key == 'override_settings') {
					override = param['value'];
				} else if (key == 'content' && param['value'].length != 0) {
					has_content = 1;
				}
			}

			// set all content settings
			for (key in params) {
				if (key != 'content') {
					var param = params[key];
					var field_val = this._getValue(key);
					if (field_val == null) {
						field_val = '';
					}
					var pass = (field_val != null
						&& param['value'] != -1
						&& field_val != param['value']
						&& (override == 1
							|| field_val == param['default']
							|| param['default'] == 'customfield'
						)
					);
					if (pass == 1) {
						this._setValue(key, param['value']);
						if (key == 'sectionid' && document.adminForm && document.adminForm.sectionid && sectioncategories) {
							changeDynaList('catid', sectioncategories, document.adminForm.sectionid.options[document.adminForm.sectionid.selectedIndex].value, 0, 0);
						}
					}
				}
			}

			// insert content
			if (has_content) {
				for (key in params) {
					if (key == 'content' && params[key]['value'].length) {
						this._jInsertEditorText(params[key]['value'], editorname);
					}
				}
			}

			window.parent.SqueezeBox.close();
		},

		_jInsertEditorText: function(value, editor, count) {
			var self = this;
			var ed = document.getElementById(editor);
			var count = ( count == null ) ? 1 : ++count;
			var succes = 0;
			// check id the editor is finished loading for max 17.5 seconds
			// 5 * 500ms
			// 5 * 1000ms
			// 5 * 2000ms
			if (count < 15) {
				var wait = ( count > 10 ) ? 2000 : ( count > 5 ) ? 1000 : 500;
				try {
					var text = value;
					if (ed) {
						if (ed.className != '' && ed.className == 'mce_editable'
							&& text.substr(0, 3) == '<p>' && text.substr(text.length - 4, 4) == '</p>'
						) {
							text = text.substr(3, text.length - 7);
						}
						jInsertEditorText(text, editor);
						if (typeof( window['tinyMCE'] ) != "undefined") {
							var ed = tinyMCE.get(editor);
							if (ed) {
								ed.hide();
								window.parent.setTimeout(function() {
									ed.show();
								}, 5);
							}
						}
						succes = 1;
					}
				} catch (err) {
				}
				if (succes) {
					window.clearTimeout(this._timer);
				} else {
					this._timer = window.setTimeout(function() {
						self._jInsertEditorText(value, editor, count)
					}, wait);
				}
			} else {
				window.clearTimeout(this._timer);
				if (ed) {
					ed.value += value;
				} else {
					alert('Could not find the editor!');
				}
			}
		},

		_getValue: function(key) {
			var element = document.getElementById(key);
			if (!element && typeof(document.adminForm) != "undefined" && typeof(document.adminForm.elements) != "undefined") {
				element = document.adminForm.elements[key];
			}
			if (!element) {
				return null;
			}
			var elementLength = element.length;
			if (element.type == 'select-one' || !elementLength) {
				if (element.type == 'checkbox' && !element.checked) {
					return '';
				}
				return element.value;
			} else {
				for (var i = 0; i < elementLength; i++) {
					if (( element.type == 'checkbox' && element[i].checked ) || ( element.type != 'checkbox' && element[i].selected )) {
						return element[i].value;
					}
				}
			}
			return '';
		},

		_setValue: function(key, value) {
			if (value == '-empty-') {
				value = '';
			}

			var $els = this._getElements(key);

			$els.each(function(i, el) {
				var $el = $(el);

				if (el.type != 'text' && el.type != 'textarea' && el.type != 'url') {
					$el.removeAttr("selected").removeAttr("checked");
					$el.find("option:selected").removeAttr("selected");
				}
			});
			$els.each(function(i, el) {
				var $el = $(el);

				if (el.type == 'text' || el.type == 'textarea' || el.type == 'url') {
					$el.val(value.toString());
				} else {
					value = value.replace('\\,', '[:COMMA:]');
					var values = value.split(',');
					var valuesLength = values.length;
					for (var i = 0; i < valuesLength; i++) {
						val = values[i].toString().replace('[:COMMA:]', ',');
						if (el.type.substr(0, 6) == 'select') {
							$el.find('option[value="' + val + '"]').attr("selected", "selected");
							$el.trigger('liszt:updated');
						} else {
							if ($el.val() == val) {
								$('label[for="' + $el.attr('id') + '"]').trigger('click');
								$el.attr("checked", "checked");
							}
						}
					}
				}
				$el.change();
			});
		},

		_getElements: function(key) {
			var types = ['input', 'select', 'textarea'];
			var names = [key.replace(/\[/g, '\\[').replace(/\]/g, '\\]')];

			var frontendkey = this._getFrontendKey(key);
			if (frontendkey != key) {
				names.push(frontendkey.replace(/\[/g, '\\[').replace(/\]/g, '\\]'));
			}

			var cleankey = frontendkey.replace(/^.*\[(.*)\]$/g, '$1');
			if (cleankey != key && cleankey != frontendkey) {
				names.push(cleankey);
			}

			var selects = [];
			for (var t = 0, tlen = types.length; t < tlen; t++) {
				for (var n = 0, nlen = names.length; n < nlen; n++) {
					selects.push(types[t] + '[name=' + names[n] + ']');
					selects.push(types[t] + '[name=' + names[n] + '\\[\\]]');
				}
			}

			return $(selects.join(','));
		},

		_getFrontendKey: function(key) {
			if (key == 'metadescription') {
				return 'metadesc';
			}
			if (key == 'metakeywords') {
				return 'metakey';
			}

			return key.replace('details', '');
		},

		_decode: function(input) {
			var output = "";
			var chr1, chr2, chr3;
			var enc1, enc2, enc3, enc4;
			var i = 0;

			var input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

			while (i < input.length) {
				enc1 = this._keyStr.indexOf(input.charAt(i++));
				enc2 = this._keyStr.indexOf(input.charAt(i++));
				enc3 = this._keyStr.indexOf(input.charAt(i++));
				enc4 = this._keyStr.indexOf(input.charAt(i++));

				chr1 = (enc1 << 2) | (enc2 >> 4);
				chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
				chr3 = ((enc3 & 3) << 6) | enc4;

				output = output + String.fromCharCode(chr1);

				if (enc3 != 64) {
					output = output + String.fromCharCode(chr2);
				}
				if (enc4 != 64) {
					output = output + String.fromCharCode(chr3);
				}

			}

			return this._utf8_decode(output);
		},

		_utf8_decode: function(utftext) {
			var string = "";
			var i = 0;
			var c = c1 = c2 = 0;

			while (i < utftext.length) {
				c = utftext.charCodeAt(i);

				if (c < 128) {
					string += String.fromCharCode(c);
					i++;
				} else if ((c > 191) && (c < 224)) {
					c2 = utftext.charCodeAt(i + 1);
					string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
					i += 2;
				} else {
					c2 = utftext.charCodeAt(i + 1);
					c3 = utftext.charCodeAt(i + 2);
					string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
					i += 3;
				}
			}

			return string;
		}
	}
})(jQuery);
