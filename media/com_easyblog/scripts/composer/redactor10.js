EasyBlog.module("composer/redactor10", function($){

var module = this;

/*
	Redactor v10.0.6
	Updated: January 7, 2015

	http://imperavi.com/redactor/

	Copyright (c) 2009-2015, Imperavi LLC.
	License: http://imperavi.com/redactor/license/

	Usage: $('#content').redactor();
*/

	'use strict';

	if (!Function.prototype.bind)
	{
		Function.prototype.bind = function(scope)
		{
			var fn = this;
			return function()
			{
				return fn.apply(scope);
			};
		};
	}

	var uuid = 0;

	// Plugin
	$.fn.composer = function(options)
	{
		var val = [];
		var args = Array.prototype.slice.call(arguments, 1);

		if (typeof options === 'string')
		{
			this.each(function()
			{
				var instance = $.data(this, 'redactor');
				var func;

				if (options.search(/\./) != '-1')
				{
					func = options.split('.');
					if (typeof instance[func[0]] != 'undefined')
					{
						func = instance[func[0]][func[1]];
					}
				}
				else
				{
					func = instance[options];
				}

				if (typeof instance !== 'undefined' && $.isFunction(func))
				{
					var methodVal = func.apply(instance, args);
					if (methodVal !== undefined && methodVal !== instance)
					{
						val.push(methodVal);
					}
				}
				else
				{
					$.error('No such method "' + options + '" for Redactor');
				}
			});
		}
		else
		{
			this.each(function()
			{
				$.data(this, 'redactor', {});
				$.data(this, 'redactor', Redactor(this, options));
			});
		}

		if (val.length === 0) return this;
		else if (val.length === 1) return val[0];
		else return val;

	};

	// Initialization
	function Redactor(el, options)
	{
		return new Redactor.prototype.init(el, options);
	}

	// Functionality
	$.Redactor = Redactor;
	$.Redactor.VERSION = '10.0.6';
	$.Redactor.modules = ['alignment', 'block', 'buffer', 'build',
						  'caret', 'clean', 'core', 'focus',
						  'indent', 'inline', 'insert', 'keydown', 'keyup',
						  'list', 'paragraphize',
						  'paste', 'selection', 'shortcuts',
						  'tabifier', 'tidy', 'utils'];

	$.Redactor.opts = {

		// settings
		direction: 'ltr', // ltr or rtl

		plugins: false, // array

		focus: false,
		focusEnd: false,

		visual: true,
		tabindex: false,

		linebreaks: false,
		replaceDivs: true,
		paragraphize: true,
		cleanStyleOnEnter: false,
		enterKey: true,

		cleanOnPaste: true,
		cleanSpaces: true,
		pastePlainText: false,

		preSpaces: 4, // or false
		tabAsSpaces: false, // true or number of spaces
		tabKey: true,

		scrollTarget: false,

		formatting: ['p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
		formattingAdd: false,

		tabifier: true,

		deniedTags: ['html', 'head', 'link', 'body', 'meta', 'script', 'style', 'applet'],
		allowedTags: false, // or array

		removeComments: false,
		replaceTags: [
			['strike', 'del']
		],
		replaceStyles: [
			['font-weight:\\s?bold', "strong"],
			['font-style:\\s?italic', "em"],
			['text-decoration:\\s?underline', "u"],
			['text-decoration:\\s?line-through', 'del']
		],
		removeDataAttr: false,

		removeAttr: false, // or multi array
		allowedAttr: false, // or multi array

		removeWithoutAttr: ['span'], // or false
		removeEmpty: ['p'], // or false;

		shortcuts: {
			'ctrl+shift+m, meta+shift+m': { func: 'inline.removeFormat' },
			'ctrl+b, meta+b': { func: 'inline.format', params: ['bold'] },
			'ctrl+i, meta+i': { func: 'inline.format', params: ['italic'] },
			'ctrl+h, meta+h': { func: 'inline.format', params: ['superscript'] },
			'ctrl+l, meta+l': { func: 'inline.format', params: ['subscript'] },
			'ctrl+shift+7':   { func: 'list.toggle', params: ['orderedlist'] },
			'ctrl+shift+8':   { func: 'list.toggle', params: ['unorderedlist'] }
		},
		shortcutsAdd: false,

		// private
		buffer: [],
		rebuffer: [],
		emptyHtml: '<p>&#x200b;</p>',
		invisibleSpace: '&#x200b;',
		imageTypes: ['image/png', 'image/jpeg', 'image/gif'],
		indentValue: 20,
		verifiedTags: 		['a', 'img', 'b', 'strong', 'sub', 'sup', 'i', 'em', 'u', 'small', 'strike', 'del', 'cite', 'ul', 'ol', 'li'], // and for span tag special rule
		inlineTags: 		['strong', 'b', 'u', 'em', 'i', 'code', 'del', 'ins', 'samp', 'kbd', 'sup', 'sub', 'mark', 'var', 'cite', 'small'],
		alignmentTags: 		['P', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6',  'DL', 'DT', 'DD', 'DIV', 'TD', 'BLOCKQUOTE', 'OUTPUT', 'FIGCAPTION', 'ADDRESS', 'SECTION', 'HEADER', 'FOOTER', 'ASIDE', 'ARTICLE'],
		blockLevelElements: ['PRE', 'UL', 'OL', 'LI'],
	};

	// Functionality
	Redactor.fn = $.Redactor.prototype = {

		keyCode: {
			BACKSPACE: 8,
			DELETE: 46,
			DOWN: 40,
			ENTER: 13,
			SPACE: 32,
			ESC: 27,
			TAB: 9,
			CTRL: 17,
			META: 91,
			SHIFT: 16,
			ALT: 18,
			RIGHT: 39,
			LEFT: 37,
			LEFT_WIN: 91
		},

		// Initialization
		init: function(el, options)
		{
			this.$element = $(el);
			this.uuid = uuid++;

			// if paste event detected = true
			this.rtePaste = false;
			this.$pasteBox = false;

			this.loadOptions(options);
			this.loadModules();

			// formatting storage
			this.formatting = {};

			// block level tags
			$.merge(this.opts.blockLevelElements, this.opts.alignmentTags);
			this.reIsBlock = new RegExp('^(' + this.opts.blockLevelElements.join('|' ) + ')$', 'i');

			// setup allowed and denied tags
			this.tidy.setupAllowed();

			// extend shortcuts
			$.extend(this.opts.shortcuts, this.opts.shortcutsAdd);

			// start callback
			this.core.setCallback('start');

			// build
			this.start = true;
			this.build.run();
		},

		loadOptions: function(options)
		{
			this.opts = $.extend(
				{},
				$.extend(true, {}, $.Redactor.opts),
				this.$element.data(),
				options
			);
		},
		getModuleMethods: function(object)
		{
			return Object.getOwnPropertyNames(object).filter(function(property)
			{
				return typeof object[property] == 'function';
			});
		},
		loadModules: function()
		{
			var len = $.Redactor.modules.length;
			for (var i = 0; i < len; i++)
			{
				this.bindModuleMethods($.Redactor.modules[i]);
			}
		},
		bindModuleMethods: function(module)
		{
			if (typeof this[module] == 'undefined') return;

			// init module
			this[module] = this[module]();

			var methods = this.getModuleMethods(this[module]);
			var len = methods.length;

			// bind methods
			for (var z = 0; z < len; z++)
			{
				this[module][methods[z]] = this[module][methods[z]].bind(this);
			}
		},
		block: function()
		{
			return {
				formatting: function(name)
				{
					this.block.clearStyle = false;
					var type, value;

					if (typeof this.formatting[name].data != 'undefined') type = 'data';
					else if (typeof this.formatting[name].attr != 'undefined') type = 'attr';
					else if (typeof this.formatting[name].class != 'undefined') type = 'class';

					if (typeof this.formatting[name].clear != 'undefined')
					{
						this.block.clearStyle = true;
					}

					if (type) value = this.formatting[name][type];

					this.block.format(this.formatting[name].tag, type, value);

				},
				format: function(tag, type, value)
				{
					if (tag == 'quote') tag = 'blockquote';

					var formatTags = ['p', 'pre', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
					if ($.inArray(tag, formatTags) == -1) return;

					this.block.isRemoveInline = (tag == 'pre' || tag.search(/h[1-6]/i) != -1);

					// focus
					if (!this.utils.browser('msie')) this.$editor.focus();

					this.block.blocks = this.selection.getBlocks();

					this.block.blocksSize = this.block.blocks.length;
					this.block.type = type;
					this.block.value = value;

					this.buffer.set();
					this.selection.save();

					this.block.set(tag);

					this.selection.restore();
				},
				set: function(tag)
				{
					this.selection.get();
					this.block.containerTag = this.range.commonAncestorContainer.tagName;

					if (this.range.collapsed)
					{
						this.block.setCollapsed(tag);
					}
					else
					{
						this.block.setMultiple(tag);
					}
				},
				setCollapsed: function(tag)
				{
					var block = this.block.blocks[0];
					if (block === false) return;

					if (block.tagName == 'LI')
					{
						if (tag != 'blockquote') return;

						this.block.formatListToBlockquote();
						return;
					}

					var isContainerTable = (this.block.containerTag  == 'TD' || this.block.containerTag  == 'TH');
					if (isContainerTable && !this.opts.linebreaks)
					{

						document.execCommand('formatblock', false, '<' + tag + '>');

						block = this.selection.getBlock();
						this.block.toggle($(block));

					}
					else if (block.tagName.toLowerCase() != tag)
					{
						if (this.opts.linebreaks && tag == 'p')
						{
							$(block).prepend('<br>').append('<br>');
							this.utils.replaceWithContents(block);
						}
						else
						{
							var $formatted = this.utils.replaceToTag(block, tag);

							this.block.toggle($formatted);

							if (tag != 'p' && tag != 'blockquote') $formatted.find('img').remove();
							if (this.block.isRemoveInline) this.utils.removeInlineTags($formatted);
							if (tag == 'p' || this.block.headTag) $formatted.find('p').contents().unwrap();

							this.block.formatTableWrapping($formatted);
						}
					}
					else if (tag == 'blockquote' && block.tagName.toLowerCase() == tag)
					{
						// blockquote off
						if (this.opts.linebreaks)
						{
							$(block).prepend('<br>').append('<br>');
							this.utils.replaceWithContents(block);
						}
						else
						{
							var $el = this.utils.replaceToTag(block, 'p');
							this.block.toggle($el);
						}
					}
					else if (block.tagName.toLowerCase() == tag)
					{
						this.block.toggle($(block));
					}

				},
				setMultiple: function(tag)
				{
					var block = this.block.blocks[0];
					var isContainerTable = (this.block.containerTag  == 'TD' || this.block.containerTag  == 'TH');

					if (block !== false && this.block.blocksSize === 1)
					{
						if (block.tagName.toLowerCase() == tag &&  tag == 'blockquote')
						{
							// blockquote off
							if (this.opts.linebreaks)
							{
								$(block).prepend('<br>').append('<br>');
								this.utils.replaceWithContents(block);
							}
							else
							{
								var $el = this.utils.replaceToTag(block, 'p');
								this.block.toggle($el);
							}
						}
						else if (block.tagName == 'LI')
						{
							if (tag != 'blockquote') return;

							this.block.formatListToBlockquote();
						}
						else if (this.block.containerTag == 'BLOCKQUOTE')
						{
							this.block.formatBlockquote(tag);
						}
						else if (this.opts.linebreaks && ((isContainerTable) || (this.range.commonAncestorContainer != block)))
						{
							this.block.formatWrap(tag);
						}
						else
						{
							if (this.opts.linebreaks && tag == 'p')
							{
								$(block).prepend('<br>').append('<br>');
								this.utils.replaceWithContents(block);
							}
							else if (block.tagName === 'TD')
							{
								this.block.formatWrap(tag);
							}
							else
							{
								var $formatted = this.utils.replaceToTag(block, tag);

								this.block.toggle($formatted);

								if (this.block.isRemoveInline) this.utils.removeInlineTags($formatted);
								if (tag == 'p' || this.block.headTag) $formatted.find('p').contents().unwrap();
							}
						}
					}
					else
					{
						if (this.opts.linebreaks || tag != 'p')
						{
							if (tag == 'blockquote')
							{
								var count = 0;
								for (var i = 0; i < this.block.blocksSize; i++)
								{
									if (this.block.blocks[i].tagName == 'BLOCKQUOTE') count++;
								}

								// only blockquote selected
								if (count == this.block.blocksSize)
								{
									$.each(this.block.blocks, $.proxy(function(i,s)
									{
										if (this.opts.linebreaks)
										{
											$(s).prepend('<br>').append('<br>');
											this.utils.replaceWithContents(s);
										}
										else
										{
											this.utils.replaceToTag(s, 'p');
										}

									}, this));

									return;
								}

							}

							this.block.formatWrap(tag);
						}
						else
						{
							var classSize = 0;
							var toggleType = false;
							if (this.block.type == 'class')
							{
								toggleType = 'toggle';
								classSize = $(this.block.blocks).filter('.' + this.block.value).size();

								if (this.block.blocksSize == classSize) toggleType = 'toggle';
								else if (this.block.blocksSize > classSize) toggleType = 'set';
								else if (classSize === 0) toggleType = 'set';

							}

							var exceptTags = ['ul', 'ol', 'li', 'td', 'th', 'dl', 'dt', 'dd'];
							$.each(this.block.blocks, $.proxy(function(i,s)
							{
								if ($.inArray(s.tagName.toLowerCase(), exceptTags) != -1) return;

								var $formatted = this.utils.replaceToTag(s, tag);

								if (toggleType)
								{
									if (toggleType == 'toggle') this.block.toggle($formatted);
									else if (toggleType == 'remove') this.block.remove($formatted);
									else if (toggleType == 'set') this.block.setForce($formatted);
								}
								else this.block.toggle($formatted);

								if (tag != 'p' && tag != 'blockquote') $formatted.find('img').remove();
								if (this.block.isRemoveInline) this.utils.removeInlineTags($formatted);
								if (tag == 'p' || this.block.headTag) $formatted.find('p').contents().unwrap();


							}, this));
						}
					}
				},
				setForce: function($el)
				{
					// remove style and class if the specified setting
					if (this.block.clearStyle)
					{
						$el.removeAttr('class').removeAttr('style');
					}

					if (this.block.type == 'class')
					{
						$el.addClass(this.block.value);
						return;
					}
					else if (this.block.type == 'attr' || this.block.type == 'data')
					{
						$el.attr(this.block.value.name, this.block.value.value);
						return;
					}
				},
				toggle: function($el)
				{
					// remove style and class if the specified setting
					if (this.block.clearStyle)
					{
						$el.removeAttr('class').removeAttr('style');
					}

					if (this.block.type == 'class')
					{
						$el.toggleClass(this.block.value);
						return;
					}
					else if (this.block.type == 'attr' || this.block.type == 'data')
					{
						if ($el.attr(this.block.value.name) == this.block.value.value)
						{
							$el.removeAttr(this.block.value.name);
						}
						else
						{
							$el.attr(this.block.value.name, this.block.value.value);
						}

						return;
					}
					else
					{
						$el.removeAttr('style class');
						return;
					}
				},
				remove: function($el)
				{
					$el.removeClass(this.block.value);
				},
				formatListToBlockquote: function()
				{
					var block = $(this.block.blocks[0]).closest('ul, ol');

					$(block).find('ul, ol').contents().unwrap();
					$(block).find('li').append($('<br>')).contents().unwrap();

					var $el = this.utils.replaceToTag(block, 'blockquote');
					this.block.toggle($el);
				},
				formatBlockquote: function(tag)
				{
					document.execCommand('outdent');
					document.execCommand('formatblock', false, tag);

					this.clean.clearUnverified();
					this.$editor.find('p:empty').remove();

					var formatted = this.selection.getBlock();

					if (tag != 'p')
					{
						$(formatted).find('img').remove();
					}

					if (!this.opts.linebreaks)
					{
						this.block.toggle($(formatted));
					}

					this.$editor.find('ul, ol, tr, blockquote, p').each($.proxy(this.utils.removeEmpty, this));

					if (this.opts.linebreaks && tag == 'p')
					{
						this.utils.replaceWithContents(formatted);
					}

				},
				formatWrap: function(tag)
				{
					if (this.block.containerTag == 'UL' || this.block.containerTag == 'OL')
					{
						if (tag == 'blockquote')
						{
							this.block.formatListToBlockquote();
						}
						else
						{
							return;
						}
					}

					var formatted = this.selection.wrap(tag);
					if (formatted === false) return;

					var $formatted = $(formatted);

					this.block.formatTableWrapping($formatted);

					var $elements = $formatted.find(this.opts.blockLevelElements.join(',') + ', td, table, thead, tbody, tfoot, th, tr');

					if ((this.opts.linebreaks && tag == 'p') || tag == 'pre' || tag == 'blockquote')
					{
						$elements.append('<br />');
					}

					$elements.contents().unwrap();

					if (tag != 'p' && tag != 'blockquote') $formatted.find('img').remove();

					$.each(this.block.blocks, $.proxy(this.utils.removeEmpty, this));

					$formatted.append(this.selection.getMarker(2));

					if (!this.opts.linebreaks)
					{
						this.block.toggle($formatted);
					}

					this.$editor.find('ul, ol, tr, blockquote, p').each($.proxy(this.utils.removeEmpty, this));
					$formatted.find('blockquote:empty').remove();

					if (this.block.isRemoveInline)
					{
						this.utils.removeInlineTags($formatted);
					}

					if (this.opts.linebreaks && tag == 'p')
					{
						this.utils.replaceWithContents($formatted);
					}

				},
				formatTableWrapping: function($formatted)
				{
					if ($formatted.closest('table').size() === 0) return;

					if ($formatted.closest('tr').size() === 0) $formatted.wrap('<tr>');
					if ($formatted.closest('td').size() === 0 && $formatted.closest('th').size() === 0)
					{
						$formatted.wrap('<td>');
					}
				},
				removeData: function(name, value)
				{
					var blocks = this.selection.getBlocks();
					$(blocks).removeAttr('data-' + name);
				},
				setData: function(name, value)
				{
					var blocks = this.selection.getBlocks();
					$(blocks).attr('data-' + name, value);
				},
				toggleData: function(name, value)
				{
					var blocks = this.selection.getBlocks();
					$.each(blocks, function()
					{
						if ($(this).attr('data-' + name))
						{
							$(this).removeAttr('data-' + name);
						}
						else
						{
							$(this).attr('data-' + name, value);
						}
					});
				},
				removeAttr: function(attr, value)
				{
					var blocks = this.selection.getBlocks();
					$(blocks).removeAttr(attr);
				},
				setAttr: function(attr, value)
				{
					var blocks = this.selection.getBlocks();
					$(blocks).attr(attr, value);
				},
				toggleAttr: function(attr, value)
				{
					var blocks = this.selection.getBlocks();
					$.each(blocks, function()
					{
						if ($(this).attr(name))
						{
							$(this).removeAttr(name);
						}
						else
						{
							$(this).attr(name, value);
						}
					});
				},
				removeClass: function(className)
				{
					var blocks = this.selection.getBlocks();
					$(blocks).removeClass(className);

					this.utils.removeEmptyAttr(blocks, 'class');
				},
				setClass: function(className)
				{
					var blocks = this.selection.getBlocks();
					$(blocks).addClass(className);
				},
				toggleClass: function(className)
				{
					var blocks = this.selection.getBlocks();
					$(blocks).toggleClass(className);
				}
			};
		},
		buffer: function()
		{
			return {
				set: function(type)
				{
					if (typeof type == 'undefined' || type == 'undo')
					{
						this.buffer.setUndo();
					}
					else
					{
						this.buffer.setRedo();
					}
				},
				setUndo: function()
				{
					this.selection.save();
					this.opts.buffer.push(this.$editor.html());
					this.selection.restore();
				},
				setRedo: function()
				{
					this.selection.save();
					this.opts.rebuffer.push(this.$editor.html());
					this.selection.restore();
				},
				getUndo: function()
				{
					this.$editor.html(this.opts.buffer.pop());
				},
				getRedo: function()
				{
					this.$editor.html(this.opts.rebuffer.pop());
				},
				add: function()
				{
					this.opts.buffer.push(this.$editor.html());
				},
				undo: function()
				{
					if (this.opts.buffer.length === 0) return;

					this.buffer.set('redo');
					this.buffer.getUndo();

					this.selection.restore();

					// setTimeout($.proxy(this.observe.load, this), 50);
				},
				redo: function()
				{
					if (this.opts.rebuffer.length === 0) return;

					this.buffer.set('undo');
					this.buffer.getRedo();

					this.selection.restore();

					// setTimeout($.proxy(this.observe.load, this), 50);
				}
			};
		},
		build: function()
		{
			return {
				run: function()
				{
					this.build.createContainerBox();
					this.build.loadEditor();
					this.build.enableEditor();
					this.build.setCodeAndCall();
				},
				createContainerBox: function()
				{
					// COMPOSER_HACK
					// Composer workarea is the container
					// this.$box = $('<div class="redactor-box" />');
					this.$box = this.$element;
				},
				enableEditor: function()
				{
					// COMPOSER_HACK
					// Do not make container contenteditable
					// this.$editor.attr({ 'contenteditable': true, 'dir': this.opts.direction });
					this.$editor
						.attr({'dir': this.opts.direction});
				},
				loadEditor: function()
				{
					// COMPOSER_HACK
					// Just reference editor to the workarea itself
					this.$editor = this.$element;
				},
				setCodeAndCall: function()
				{
					this.build.setOptions();
					this.build.callEditor();
				},
				callEditor: function()
				{
					this.build.disableMozillaEditing();
					this.build.setEvents();
					this.build.setHelpers();

					// load toolbar
					if (this.opts.toolbar)
					{
						this.opts.toolbar = this.toolbar.init();
						this.toolbar.build();
					}

					// plugins
					this.build.plugins();

					// observers
					// setTimeout($.proxy(this.observe.load, this), 4);

					// init callback
					this.core.setCallback('init');
				},
				setOptions: function()
				{
					if (this.opts.linebreaks) this.$editor.addClass('redactor-linebreaks');

					if (this.opts.tabindex) this.$editor.attr('tabindex', this.opts.tabindex);
				},
				setEvents: function()
				{
					// drop
					this.$editor.on('drop.redactor', $.proxy(function(e)
					{
						e = e.originalEvent || e;

						if (window.FormData === undefined || !e.dataTransfer) return true;

						var length = e.dataTransfer.files.length;
						if (length === 0)
						{
							setTimeout($.proxy(this.clean.clearUnverified, this), 1);
							this.core.setCallback('drop', e);

							return true;
						}
						else
						{
							// Prevent browser from opening the file
							e.preventDefault();
						}

						setTimeout($.proxy(this.clean.clearUnverified, this), 1);

						this.core.setCallback('drop', e);

					}, this));


					// click
					this.$editor.on('click.redactor', $.proxy(function(e)
					{
						var type = 'click';
						if ((this.core.getEvent() == 'click' || this.core.getEvent() == 'arrow'))
						{
							type = false;
						}

						this.core.addEvent(type);
						this.core.setCallback('click', e);

					}, this));

					// paste
					this.$editor.on('paste.redactor', $.proxy(this.paste.init, this));

					// keydown
					this.$editor.on('keydown.redactor', $.proxy(this.keydown.init, this));

					// keyup
					this.$editor.on('keyup.redactor', $.proxy(this.keyup.init, this));

					// COMPOSER_HACK
					// Prevent native dragstart event from firing when dragging
					// elements like image element in the workarea.
					this.$editor.on('dragstart.redactor', function(event, ui){
						// If this is not coming from ui/draggable, stop.
						if (!ui) {
							event.preventDefault();
							event.stopPropagation();
						}
					});

					// textarea keydown
					// if ($.isFunction(this.opts.codeKeydownCallback))
					// {
					// 	this.$textarea.on('keydown.redactor-textarea', $.proxy(this.opts.codeKeydownCallback, this));
					// }

					// textarea keyup
					// if ($.isFunction(this.opts.codeKeyupCallback))
					// {
					// 	this.$textarea.on('keyup.redactor-textarea', $.proxy(this.opts.codeKeyupCallback, this));
					// }

					// focus
					if ($.isFunction(this.opts.focusCallback))
					{
						this.$editor.on('focus.redactor', $.proxy(this.opts.focusCallback, this));
					}

					var clickedElement;
					$(document).on('mousedown', function(e) {
						clickedElement = $(e.target);
					});
				},
				setHelpers: function()
				{
					// focus
					if (this.opts.focus) setTimeout($.proxy(this.focus.setStart, this), 100);
					if (this.opts.focusEnd) setTimeout($.proxy(this.focus.setEnd, this), 100);
				},
				plugins: function()
				{
					if (!this.opts.plugins) return;
					if (!RedactorPlugins) return;

					$.each(this.opts.plugins, $.proxy(function(i, s)
					{
						if (typeof RedactorPlugins[s] === 'undefined') return;

						if ($.inArray(s, $.Redactor.modules) !== -1)
						{
							$.error('Plugin name "' + s + '" matches the name of the Redactor\'s module.');
							return;
						}

						if (!$.isFunction(RedactorPlugins[s])) return;

						this[s] = RedactorPlugins[s]();

						var methods = this.getModuleMethods(this[s]);
						var len = methods.length;

						// bind methods
						for (var z = 0; z < len; z++)
						{
							this[s][methods[z]] = this[s][methods[z]].bind(this);
						}

						if ($.isFunction(this[s].init)) this[s].init();


					}, this));


				},
				disableMozillaEditing: function()
				{
					if (!this.utils.browser('mozilla')) return;

					// COMPOSER HACK
					// Need to enable design mode so the following commands
					// can execute without errors.
					document.designMode = "on";

					// FF fix
					try {
						document.execCommand('enableObjectResizing', false, false);
						document.execCommand('enableInlineTableEditing', false, false);
					} catch (e) {}

					// COMPOSER HACK
					// Disable design mode after executing those commands above.
					document.designMode = "off";
				}
			};
		},
		caret: function()
		{
			return {
				setStart: function(node)
				{
					// inline tag
					if (!this.utils.isBlock(node))
					{
						var space = this.utils.createSpaceElement();

						$(node).prepend(space);
						this.caret.setEnd(space);
					}
					else
					{
						this.caret.set(node, 0, node, 0);
					}
				},
				setEnd: function(node)
				{
					this.caret.set(node, 1, node, 1);
				},
				set: function(orgn, orgo, focn, foco)
				{
					// focus
					if (!this.utils.browser('msie')) this.$editor.focus();

					orgn = orgn[0] || orgn;
					focn = focn[0] || focn;

					if (this.utils.isBlockTag(orgn.tagName) && orgn.innerHTML === '')
					{
						orgn.innerHTML = this.opts.invisibleSpace;
					}

					if (orgn.tagName == 'BR' && this.opts.linebreaks === false)
					{
						var par = $(this.opts.emptyHtml)[0];
						$(orgn).replaceWith(par);
						orgn = par;
						focn = orgn;
					}

					this.selection.get();

					try {
						this.range.setStart(orgn, orgo);
						this.range.setEnd(focn, foco);
					}
					catch (e) {}

					this.selection.addRange();
				},
				setAfter: function(node)
				{
					try {
						var tag = $(node)[0].tagName;

						// inline tag
						if (tag != 'BR' && !this.utils.isBlock(node))
						{
							var space = this.utils.createSpaceElement();

							$(node).after(space);
							this.caret.setEnd(space);
						}
						else
						{
							if (tag != 'BR' && this.utils.browser('msie'))
							{
								this.caret.setStart($(node).next());
							}
							else
							{
								this.caret.setAfterOrBefore(node, 'after');
							}
						}
					}
					catch (e) {
						var space = this.utils.createSpaceElement();
						$(node).after(space);
						this.caret.setEnd(space);
					}
				},
				setBefore: function(node)
				{
					// block tag
					if (this.utils.isBlock(node))
					{
						this.caret.setEnd($(node).prev());
					}
					else
					{
						this.caret.setAfterOrBefore(node, 'before');
					}
				},
				setAfterOrBefore: function(node, type)
				{
					// focus
					if (!this.utils.browser('msie')) this.$editor.focus();

					node = node[0] || node;

					this.selection.get();

					if (type == 'after')
					{
						try {

							this.range.setStartAfter(node);
							this.range.setEndAfter(node);
						}
						catch (e) {}
					}
					else
					{
						try {
							this.range.setStartBefore(node);
							this.range.setEndBefore(node);
						}
						catch (e) {}
					}


					this.range.collapse(false);
					this.selection.addRange();
				},
				getOffsetOfElement: function(node)
				{
					node = node[0] || node;

					this.selection.get();

					var cloned = this.range.cloneRange();
					cloned.selectNodeContents(node);
					cloned.setEnd(this.range.endContainer, this.range.endOffset);

					return $.trim(cloned.toString()).length;
				},
				getOffset: function()
				{
					var offset = 0;
					var sel = window.getSelection();

					if (sel.rangeCount > 0)
					{
						var range = window.getSelection().getRangeAt(0);
						var caretRange = range.cloneRange();
						caretRange.selectNodeContents(this.$editor[0]);
						caretRange.setEnd(range.endContainer, range.endOffset);
						offset = caretRange.toString().length;
					}

					return offset;
				},
				setOffset: function(start, end)
				{
					if (typeof end == 'undefined') end = start;
					if (!this.focus.isFocused()) this.focus.setStart();

					var sel = this.selection.get();
					var node, offset = 0;
					var walker = document.createTreeWalker(this.$editor[0], NodeFilter.SHOW_TEXT, null, null);

					while (node = walker.nextNode())
					{
						offset += node.nodeValue.length;
						if (offset > start)
						{
							this.range.setStart(node, node.nodeValue.length + start - offset);
							start = Infinity;
						}

						if (offset >= end)
						{
							this.range.setEnd(node, node.nodeValue.length + end - offset);
							break;
						}
					}

					this.range.collapse(false);
					this.selection.addRange();
				},
				setToPoint: function(start, end)
				{
					this.caret.setOffset(start, end);
				},
				getCoords: function()
				{
					return this.caret.getOffset();
				}
			};
		},
		clean: function()
		{
			return {
				onSet: function(html)
				{
					html = this.clean.savePreCode(html);

					// convert script tag
					html = html.replace(/<script(.*?[^>]?)>([\w\W]*?)<\/script>/gi, '<pre class="redactor-script-tag" style="display: none;" $1>$2</pre>');

					// replace dollar sign to entity
					html = html.replace(/\$/g, '&#36;');
					html = html.replace(/”/g, '"');
					html = html.replace(/‘/g, '\'');
					html = html.replace(/’/g, '\'');

					if (this.opts.replaceDivs) html = this.clean.replaceDivs(html);
					if (this.opts.linebreaks)  html = this.clean.replaceParagraphsToBr(html);

					// save form tag
					html = this.clean.saveFormTags(html);

					// convert font tag to span
					var $div = $('<div>');
					$div.html(html);
					var fonts = $div.find('font[style]');
					if (fonts.length !== 0)
					{
						fonts.replaceWith(function()
						{
							var $el = $(this);
							var $span = $('<span>').attr('style', $el.attr('style'));
							return $span.append($el.contents());
						});

						html = $div.html();
					}
					$div.remove();

					// remove font tag
					html = html.replace(/<font(.*?[^<])>/gi, '');
					html = html.replace(/<\/font>/gi, '');

					// tidy html
					html = this.tidy.load(html);

					// paragraphize
					if (this.opts.paragraphize) html = this.paragraphize.load(html);

					// verified
					html = this.clean.setVerified(html);

					// convert inline tags
					html = this.clean.convertInline(html);

					return html;
				},
				onSync: function(html)
				{
					// remove spaces
					html = html.replace(/[\u200B-\u200D\uFEFF]/g, '');
					html = html.replace(/&#x200b;/gi, '');

					if (this.opts.cleanSpaces)
					{
						html = html.replace(/&nbsp;/gi, ' ');
					}

					if (html.search(/^<p>(||\s||<br\s?\/?>||&nbsp;)<\/p>$/i) != -1)
					{
						return '';
					}

					// reconvert script tag
					html = html.replace(/<pre class="redactor-script-tag" style="display: none;"(.*?[^>]?)>([\w\W]*?)<\/pre>/gi, '<script$1>$2</script>');

					// restore form tag
					html = this.clean.restoreFormTags(html);

					var chars = {
						'\u2122': '&trade;',
						'\u00a9': '&copy;',
						'\u2026': '&hellip;',
						'\u2014': '&mdash;',
						'\u2010': '&dash;'
					};
					// replace special characters
					$.each(chars, function(i,s)
					{
						html = html.replace(new RegExp(i, 'g'), s);
					});

					// remove br in the of li
					html = html.replace(new RegExp('<br\\s?/?></li>', 'gi'), '</li>');
					html = html.replace(new RegExp('</li><br\\s?/?>', 'gi'), '</li>');
					// remove verified
					html = html.replace(new RegExp('<div(.*?[^>]) data-tagblock="redactor"(.*?[^>])>', 'gi'), '<div$1$2>');
					html = html.replace(new RegExp('<(.*?) data-verified="redactor"(.*?[^>])>', 'gi'), '<$1$2>');
					html = html.replace(new RegExp('<span(.*?[^>])\srel="(.*?[^>])"(.*?[^>])>', 'gi'), '<span$1$3>');
					html = html.replace(new RegExp('<img(.*?[^>])\srel="(.*?[^>])"(.*?[^>])>', 'gi'), '<img$1$3>');
					html = html.replace(new RegExp('<img(.*?[^>])\sstyle="" (.*?[^>])>', 'gi'), '<img$1 $2>');
					html = html.replace(new RegExp('<img(.*?[^>])\sstyle (.*?[^>])>', 'gi'), '<img$1 $2>');
					html = html.replace(new RegExp('<span class="redactor-invisible-space">(.*?)</span>', 'gi'), '$1');
					html = html.replace(/ data-save-url="(.*?[^>])"/gi, '');

					// remove image resize
					html = html.replace(/<span(.*?)id="redactor-image-box"(.*?[^>])>([\w\W]*?)<img(.*?)><\/span>/gi, '$3<img$4>');
					html = html.replace(/<span(.*?)id="redactor-image-resizer"(.*?[^>])>(.*?)<\/span>/gi, '');
					html = html.replace(/<span(.*?)id="redactor-image-editter"(.*?[^>])>(.*?)<\/span>/gi, '');

					// remove font tag
					html = html.replace(/<font(.*?[^<])>/gi, '');
					html = html.replace(/<\/font>/gi, '');

					// tidy html
					html = this.tidy.load(html);

					// link nofollow
					if (this.opts.linkNofollow)
					{
						html = html.replace(/<a(.*?)rel="nofollow"(.*?[^>])>/gi, '<a$1$2>');
						html = html.replace(/<a(.*?[^>])>/gi, '<a$1 rel="nofollow">');
					}

					// reconvert inline
					html = html.replace(/<(.*?) data-redactor-tag="(.*?)"(.*?[^>])>/gi, '<$1$3>');
					html = html.replace(/<(.*?) data-redactor-class="(.*?)"(.*?[^>])>/gi, '<$1$3>');
					html = html.replace(/<(.*?) data-redactor-style="(.*?)"(.*?[^>])>/gi, '<$1$3>');
					html = html.replace(new RegExp('<(.*?) data-verified="redactor"(.*?[^>])>', 'gi'), '<$1$2>');
					html = html.replace(new RegExp('<(.*?) data-verified="redactor">', 'gi'), '<$1>');

					return html;
				},
				onPaste: function(html, setMode)
				{
					html = $.trim(html);

					html = html.replace(/\$/g, '&#36;');
					html = html.replace(/”/g, '"');
					html = html.replace(/“/g, '"');
					html = html.replace(/‘/g, '\'');
					html = html.replace(/’/g, '\'');

					// convert dirty spaces
					html = html.replace(/<span class="Apple-converted-space">&nbsp;<\/span>/gi, ' ');
					html = html.replace(/<span class="Apple-tab-span"[^>]*>\t<\/span>/gi, '\t');
					html = html.replace(/<span[^>]*>(\s|&nbsp;)<\/span>/gi, ' ');

					if (this.opts.pastePlainText)
					{
						return this.clean.getPlainText(html);
					}

					if (typeof setMode == 'undefined')
					{
						if (this.utils.isCurrentOrParent(['FIGCAPTION', 'A']))
						{
							return this.clean.getPlainText(html, false);
						}

						if (this.utils.isCurrentOrParent('PRE'))
						{
							return this.clean.getPreCode(html);
						}

						if (this.utils.isCurrentOrParent(['BLOCKQUOTE', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6']))
						{
							html = this.clean.getOnlyImages(html);

							if (!this.utils.browser('msie'))
							{
								var block = this.selection.getBlock();
								if (block && block.tagName == 'P')
								{
									html = html.replace(/<img(.*?)>/gi, '<p><img$1></p>');
								}
							}

							return html;
						}

						if (this.utils.isCurrentOrParent(['TD']))
						{
							html = this.clean.onPasteTidy(html, 'td');

							if (this.opts.linebreaks) html = this.clean.replaceParagraphsToBr(html);

							html = this.clean.replaceDivsToBr(html);

							return html;
						}


						if (this.utils.isCurrentOrParent(['LI']))
						{
							return this.clean.onPasteTidy(html, 'li');
						}
					}


					html = this.clean.isSingleLine(html, setMode);

					if (!this.clean.singleLine)
					{
						if (this.opts.linebreaks)  html = this.clean.replaceParagraphsToBr(html);
						if (this.opts.replaceDivs) html = this.clean.replaceDivs(html);

						html = this.clean.saveFormTags(html);
					}


					html = this.clean.onPasteWord(html);
					html = this.clean.onPasteExtra(html);

					html = this.clean.onPasteTidy(html, 'all');


					// paragraphize
					if (!this.clean.singleLine && this.opts.paragraphize)
					{
						html = this.paragraphize.load(html);
					}

					html = this.clean.removeDirtyStyles(html);
					html = this.clean.onPasteRemoveSpans(html);
					html = this.clean.onPasteRemoveEmpty(html);

					html = this.clean.convertInline(html);

					return html;
				},
				onPasteWord: function(html)
				{
					// comments
					html = html.replace(/<!--[\s\S]*?-->/gi, '');

					// style
					html = html.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '');

					if (/(class=\"?Mso|style=\"[^\"]*\bmso\-|w:WordDocument)/.test(html))
					{
						html = this.clean.onPasteIeFixLinks(html);

						// shapes
						html = html.replace(/<img(.*?)v:shapes=(.*?)>/gi, '');
						html = html.replace(/src="file\:\/\/(.*?)"/, 'src=""');

						// list
						html = html.replace(/<p(.*?)class="MsoListParagraphCxSpFirst"([\w\W]*?)<\/p>/gi, '<ul><li$2</li>');
						html = html.replace(/<p(.*?)class="MsoListParagraphCxSpMiddle"([\w\W]*?)<\/p>/gi, '<li$2</li>');
						html = html.replace(/<p(.*?)class="MsoListParagraphCxSpLast"([\w\W]*?)<\/p>/gi, '<li$2</li></ul>');
						// one line
						html = html.replace(/<p(.*?)class="MsoListParagraph"([\w\W]*?)<\/p>/gi, '<ul><li$2</li></ul>');
						// remove ms word's bullet
						html = html.replace(/·/g, '');
						html = html.replace(/<p class="Mso(.*?)"/gi, '<p');

						// classes
						html = html.replace(/ class=\"(mso[^\"]*)\"/gi,	"");
						html = html.replace(/ class=(mso\w+)/gi, "");

						// remove ms word tags
						html = html.replace(/<o:p(.*?)>([\w\W]*?)<\/o:p>/gi, '$2');

						// ms word break lines
						html = html.replace(/\n/g, ' ');

						// ms word lists break lines
						html = html.replace(/<p>\n?<li>/gi, '<li>');
					}

					// remove nbsp
					if (this.opts.cleanSpaces)
					{
						html = html.replace(/(\s|&nbsp;)+/g, ' ');
					}

					return html;
				},
				onPasteExtra: function(html)
				{
					// remove google docs markers
					html = html.replace(/<b\sid="internal-source-marker(.*?)">([\w\W]*?)<\/b>/gi, "$2");
					html = html.replace(/<b(.*?)id="docs-internal-guid(.*?)">([\w\W]*?)<\/b>/gi, "$3");

					// google docs styles
					html = html.replace(/<span[^>]*(font-style: italic; font-weight: bold|font-weight: bold; font-style: italic)[^>]*>/gi, '<span style="font-weight: bold;"><span style="font-style: italic;">');
					html = html.replace(/<span[^>]*font-style: italic[^>]*>/gi, '<span style="font-style: italic;">');
					html = html.replace(/<span[^>]*font-weight: bold[^>]*>/gi, '<span style="font-weight: bold;">');
					html = html.replace(/<span[^>]*text-decoration: underline[^>]*>/gi, '<span style="text-decoration: underline;">');

					html = html.replace(/<img>/gi, '');
					html = html.replace(/\n{3,}/gi, '\n');
					html = html.replace(/<font(.*?)>([\w\W]*?)<\/font>/gi, '$2');

					// remove dirty p
					html = html.replace(/<p><p>/gi, '<p>');
					html = html.replace(/<\/p><\/p>/gi, '</p>');
					html = html.replace(/<li>(\s*|\t*|\n*)<p>/gi, '<li>');
					html = html.replace(/<\/p>(\s*|\t*|\n*)<\/li>/gi, '</li>');

					// remove space between paragraphs
					html = html.replace(/<\/p>\s<p/gi, '<\/p><p');

					// remove safari local images
					html = html.replace(/<img src="webkit-fake-url\:\/\/(.*?)"(.*?)>/gi, '');

					// bullets
					html = html.replace(/<p>•([\w\W]*?)<\/p>/gi, '<li>$1</li>');

					// FF fix
					if (this.utils.browser('mozilla'))
					{
						html = html.replace(/<br\s?\/?>$/gi, '');
					}

					return html;
				},
				onPasteTidy: function(html, type)
				{
					// remove all tags except these
					var tags = ['span', 'a', 'pre', 'blockquote', 'small', 'em', 'strong', 'code', 'kbd', 'mark', 'address', 'cite', 'var', 'samp', 'dfn', 'sup', 'sub', 'b', 'i', 'u', 'del',
								'ol', 'ul', 'li', 'dl', 'dt', 'dd', 'p', 'br', 'video', 'audio', 'iframe', 'embed', 'param', 'object', 'img', 'table',
								'td', 'th', 'tr', 'tbody', 'tfoot', 'thead', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
					var tagsEmpty = false;
					var attrAllowed =  [
							['a', '*'],
							['img', ['src', 'alt']],
							['span', ['class', 'rel', 'data-verified']],
							['iframe', '*'],
							['video', '*'],
							['audio', '*'],
							['embed', '*'],
							['object', '*'],
							['param', '*'],
							['source', '*']
						];

					if (type == 'all')
					{
						tagsEmpty = ['p', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
						attrAllowed =  [
							['table', 'class'],
							['td', ['colspan', 'rowspan']],
							['a', '*'],
							['img', ['src', 'alt', 'data-redactor-inserted-image']],
							['span', ['class', 'rel', 'data-verified']],
							['iframe', '*'],
							['video', '*'],
							['audio', '*'],
							['embed', '*'],
							['object', '*'],
							['param', '*'],
							['source', '*']
						];
					}
					else if (type == 'td')
					{
						// remove all tags except these and remove all table tags: tr, td etc
						tags = ['ul', 'ol', 'li', 'span', 'a', 'small', 'em', 'strong', 'code', 'kbd', 'mark', 'cite', 'var', 'samp', 'dfn', 'sup', 'sub', 'b', 'i', 'u', 'del',
								'ol', 'ul', 'li', 'dl', 'dt', 'dd', 'br', 'iframe', 'video', 'audio', 'embed', 'param', 'object', 'img', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

					}
					else if (type == 'li')
					{
						// only inline tags and ul, ol, li
						tags = ['ul', 'ol', 'li', 'span', 'a', 'small', 'em', 'strong', 'code', 'kbd', 'mark', 'cite', 'var', 'samp', 'dfn', 'sup', 'sub', 'b', 'i', 'u', 'del', 'br',
								'iframe', 'video', 'audio', 'embed', 'param', 'object', 'img'];
					}

					var options = {
						deniedTags: false,
						allowedTags: tags,
						removeComments: true,
						removePhp: true,
						removeAttr: false,
						allowedAttr: attrAllowed,
						removeEmpty: tagsEmpty
					};


					return this.tidy.load(html, options);

				},
				onPasteRemoveEmpty: function(html)
				{
					html = html.replace(/<(p|h[1-6])>(|\s|\n|\t|<br\s?\/?>)<\/(p|h[1-6])>/gi, '');

					// remove br in the end
					if (!this.opts.linebreaks) html = html.replace(/<br>$/i, '');

					return html;
				},
				onPasteRemoveSpans: function(html)
				{
					html = html.replace(/<span>(.*?)<\/span>/gi, '$1');
					html = html.replace(/<span[^>]*>\s|&nbsp;<\/span>/gi, ' ');

					return html;
				},
				onPasteIeFixLinks: function(html)
				{
					if (!this.utils.browser('msie')) return html;

					var tmp = $.trim(html);
					if (tmp.search(/^<a(.*?)>(.*?)<\/a>$/i) === 0)
					{
						html = html.replace(/^<a(.*?)>(.*?)<\/a>$/i, "$2");
					}

					return html;
				},
				isSingleLine: function(html, setMode)
				{
					this.clean.singleLine = false;

					if (typeof setMode == 'undefined')
					{
						var blocks = this.opts.blockLevelElements.join('|').replace('P|', '').replace('DIV|', '');

						var matchBlocks = html.match(new RegExp('</(' + blocks + ')>', 'gi'));
						var matchContainers = html.match(/<\/(p|div)>/gi);

						if (!matchBlocks && (matchContainers === null || (matchContainers && matchContainers.length <= 1)))
						{
							var matchBR = html.match(/<br\s?\/?>/gi);
							var matchIMG = html.match(/<img(.*?[^>])>/gi);
							if (!matchBR && !matchIMG)
							{
								this.clean.singleLine = true;
								html = html.replace(/<\/?(p|div)(.*?)>/gi, '');
							}
						}
					}

					return html;
				},
				stripTags: function(input, allowed)
				{
					allowed = (((allowed || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
					var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;

					return input.replace(tags, function ($0, $1) {
						return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
					});
				},
				savePreCode: function(html)
				{
					var pre = html.match(/<(pre|code)(.*?)>([\w\W]*?)<\/(pre|code)>/gi);
					if (pre !== null)
					{
						$.each(pre, $.proxy(function(i,s)
						{
							var arr = s.match(/<(pre|code)(.*?)>([\w\W]*?)<\/(pre|code)>/i);

							arr[3] = arr[3].replace(/<br\s?\/?>/g, '\n');
							arr[3] = arr[3].replace(/&nbsp;/g, ' ');

							if (this.opts.preSpaces)
							{
								arr[3] = arr[3].replace(/\t/g, Array(this.opts.preSpaces + 1).join(' '));
							}

							arr[3] = this.clean.encodeEntities(arr[3]);

							// $ fix
							arr[3] = arr[3].replace(/\$/g, '&#36;');

							html = html.replace(s, '<' + arr[1] + arr[2] + '>' + arr[3] + '</' + arr[1] + '>');

						}, this));
					}

					return html;
				},
				getTextFromHtml: function(html)
				{
					html = html.replace(/<br\s?\/?>|<\/H[1-6]>|<\/p>|<\/div>|<\/li>|<\/td>/gi, '\n');

					var tmp = document.createElement('div');
					tmp.innerHTML = html;
					html = tmp.textContent || tmp.innerText;

					return $.trim(html);
				},
				getPlainText: function(html, paragraphize)
				{
					html = this.clean.getTextFromHtml(html);
					html = html.replace(/\n/g, '<br />');

					if (this.opts.paragraphize && typeof paragraphize == 'undefined')
					{
						html = this.paragraphize.load(html);
					}

					return html;
				},
				getPreCode: function(html)
				{
					html = html.replace(/<img(.*?) style="(.*?)"(.*?[^>])>/gi, '<img$1$3>');
					html = html.replace(/<img(.*?)>/gi, '&lt;img$1&gt;');
					html = this.clean.getTextFromHtml(html);

					if (this.opts.preSpaces)
					{
						html = html.replace(/\t/g, Array(this.opts.preSpaces + 1).join(' '));
					}

					html = this.clean.encodeEntities(html);

					return html;
				},
				getOnlyImages: function(html)
				{
					html = html.replace(/<img(.*?)>/gi, '[img$1]');

					// remove all tags
					html = html.replace(/<([Ss]*?)>/gi, '');

					html = html.replace(/\[img(.*?)\]/gi, '<img$1>');

					return html;
				},
				getOnlyLinksAndImages: function(html)
				{
					html = html.replace(/<a(.*?)href="(.*?)"(.*?)>([\w\W]*?)<\/a>/gi, '[a href="$2"]$4[/a]');
					html = html.replace(/<img(.*?)>/gi, '[img$1]');

					// remove all tags
					html = html.replace(/<(.*?)>/gi, '');

					html = html.replace(/\[a href="(.*?)"\]([\w\W]*?)\[\/a\]/gi, '<a href="$1">$2</a>');
					html = html.replace(/\[img(.*?)\]/gi, '<img$1>');

					return html;
				},
				encodeEntities: function(str)
				{
					str = String(str).replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
					return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
				},
				removeDirtyStyles: function(html)
				{
					if (this.utils.browser('msie')) return html;

					var div = document.createElement('div');
					div.innerHTML = html;

					this.clean.clearUnverifiedRemove($(div));

					html = div.innerHTML;
					$(div).remove();

					return html;
				},
				clearUnverified: function()
				{
					if (this.utils.browser('msie')) return;

					this.clean.clearUnverifiedRemove(this.$editor);

					var headers = this.$editor.find('h1, h2, h3, h4, h5, h6');
					headers.find('span').removeAttr('style');
					headers.find(this.opts.verifiedTags.join(', ')).removeAttr('style');
				},
				clearUnverifiedRemove: function($editor)
				{
					$editor.find(this.opts.verifiedTags.join(', ')).removeAttr('style');
					$editor.find('span').not('[data-verified="redactor"]').removeAttr('style');

					$editor.find('span[data-verified="redactor"], img[data-verified="redactor"]').each(function(i, s)
					{
						var $s = $(s);
						$s.attr('style', $s.attr('rel'));
					});

				},
				setVerified: function(html)
				{
					if (this.utils.browser('msie')) return html;

					html = html.replace(new RegExp('<img(.*?[^>])>', 'gi'), '<img$1 data-verified="redactor">');
					html = html.replace(new RegExp('<span(.*?)>', 'gi'), '<span$1 data-verified="redactor">');

					var matches = html.match(new RegExp('<(span|img)(.*?)style="(.*?)"(.*?[^>])>', 'gi'));
					if (matches)
					{
						var len = matches.length;
						for (var i = 0; i < len; i++)
						{
							try {
								var newTag = matches[i].replace(/style="(.*?)"/i, 'style="$1" rel="$1"');
								html = html.replace(new RegExp(matches[i], 'gi'), newTag);
							}
							catch (e) {}
						}
					}

					return html;
				},
				convertInline: function(html)
				{
					var $div = $('<div />').html(html);

					var tags = this.opts.inlineTags;
					tags.push('span');

					$div.find(tags.join(',')).each(function()
					{
						var $el = $(this);
						var tag = this.tagName.toLowerCase();
						$el.attr('data-redactor-tag', tag);

						if (tag == 'span')
						{
							if ($el.attr('style')) $el.attr('data-redactor-style', $el.attr('style'));
							else if ($el.attr('class')) $el.attr('data-redactor-class', $el.attr('class'));
						}

					});

					html = $div.html();
					$div.remove();

					return html;
				},
				normalizeLists: function()
				{
					this.$editor.find('li').each(function(i,s)
					{
						var $next = $(s).next();
						if ($next.length !== 0 && ($next[0].tagName == 'UL' || $next[0].tagName == 'OL'))
						{
							$(s).append($next);
						}

					});
				},
				removeSpaces: function(html)
				{
					html = html.replace(/\n/g, '');
					html = html.replace(/[\t]*/g, '');
					html = html.replace(/\n\s*\n/g, "\n");
					html = html.replace(/^[\s\n]*/g, ' ');
					html = html.replace(/[\s\n]*$/g, ' ');
					html = html.replace( />\s{2,}</g, '> <'); // between inline tags can be only one space
					html = html.replace(/\n\n/g, "\n");
					html = html.replace(/[\u200B-\u200D\uFEFF]/g, '');

					return html;
				},
				replaceDivs: function(html)
				{
					if (this.opts.linebreaks)
					{
						html = html.replace(/<div><br\s?\/?><\/div>/gi, '<br />');
						html = html.replace(/<div(.*?)>([\w\W]*?)<\/div>/gi, '$2<br />');
					}
					else
					{
						html = html.replace(/<div(.*?)>([\w\W]*?)<\/div>/gi, '<p$1>$2</p>');
					}

					return html;
				},
				replaceDivsToBr: function(html)
				{
					html = html.replace(/<div\s(.*?)>/gi, '<p>');
					html = html.replace(/<div><br\s?\/?><\/div>/gi, '<br /><br />');
					html = html.replace(/<div>([\w\W]*?)<\/div>/gi, '$1<br /><br />');

					return html;
				},
				replaceParagraphsToBr: function(html)
				{
					html = html.replace(/<p\s(.*?)>/gi, '<p>');
					html = html.replace(/<p><br\s?\/?><\/p>/gi, '<br />');
					html = html.replace(/<p>([\w\W]*?)<\/p>/gi, '$1<br /><br />');
					html = html.replace(/(<br\s?\/?>){1,}\n?<\/blockquote>/gi, '</blockquote>');

					return html;
				},
				saveFormTags: function(html)
				{
					return html.replace(/<form(.*?)>([\w\W]*?)<\/form>/gi, '<section$1 rel="redactor-form-tag">$2</section>');
				},
				restoreFormTags: function(html)
				{
					return html.replace(/<section(.*?) rel="redactor-form-tag"(.*?)>([\w\W]*?)<\/section>/gi, '<form$1$2>$3</form>');
				}
			};
		},
		core: function()
		{
			return {
				getObject: function()
				{
					return $.extend({}, this);
				},
				getEditor: function()
				{
					return this.$editor;
				},
				getBox: function()
				{
					return this.$box;
				},
				getElement: function()
				{
					return this.$element;
				},
				addEvent: function(name)
				{
					this.core.event = name;
				},
				getEvent: function()
				{
					return this.core.event;
				},
				setCallback: function(type, e, data)
				{
					var callback = this.opts[type + 'Callback'];
					if ($.isFunction(callback))
					{
						return (typeof data == 'undefined') ? callback.call(this, e) : callback.call(this, e, data);
					}
					else
					{
						return (typeof data == 'undefined') ? e : data;
					}
				},
				destroy: function()
				{
					this.core.setCallback('destroy');

					// off events and remove data
					this.$element.off('.redactor').removeData('redactor');
					this.$editor.off('.redactor');

					// COMPOSER_HACK
					// No textarea or container to destroy here.

					// paste box
					if (this.$pasteBox) this.$pasteBox.remove();

					// buttons tooltip
					$('.redactor-toolbar-tooltip').remove();
				}
			};
		},
		focus: function()
		{
			return {
				setStart: function()
				{
					this.$editor.focus();

					var first = this.$editor.children().first();

					if (first.size() === 0) return;
					if (first[0].length === 0 || first[0].tagName == 'BR' || first[0].nodeType == 3)
					{
						return;
					}

					if (first[0].tagName == 'UL' || first[0].tagName == 'OL')
					{
						first = first.find('li').first();
						var child = first.children().first();
						if (!this.utils.isBlock(child) && child.text() === '')
						{
							// empty inline tag in li
							this.caret.setStart(child);
							return;
						}
					}

					if (this.opts.linebreaks && !this.utils.isBlockTag(first[0].tagName))
					{
						this.selection.get();
						this.range.setStart(this.$editor[0], 0);
						this.range.setEnd(this.$editor[0], 0);
						this.selection.addRange();

						return;
					}

					// if node is tag
					this.caret.setStart(first);
				},
				setEnd: function()
				{
					if (this.utils.browser('mozilla') || this.utils.browser('msie'))
					{
						var last = this.$editor.children().last();
						this.caret.setEnd(last);
					}
					else
					{
						this.selection.get();

						try {
							this.range.selectNodeContents(this.$editor[0]);
							this.range.collapse(false);

							this.selection.addRange();
						}
						catch (e) {}
					}

				},
				isFocused: function()
				{
					var focusNode = document.getSelection().focusNode;
					if (focusNode === null) return false;

					// COMPOSER_HACK
					// Do not check against linebreaks
					// if (this.opts.linebreaks && $(focusNode.parentNode).hasClass('redactor-linebreaks')) return true;
					// else if (!this.utils.isRedactorParent(focusNode.parentNode)) return false;
					if (!this.utils.isRedactorParent(focusNode.parentNode)) return false;

					return this.$editor.is(':focus');
				}
			};
		},
		indent: function()
		{
			return {
				increase: function()
				{
					// focus
					if (!this.utils.browser('msie')) this.$editor.focus();

					this.buffer.set();
					this.selection.save();

					var block = this.selection.getBlock();

					if (block && block.tagName == 'LI')
					{
						this.indent.increaseLists();
					}
					else if (block === false && this.opts.linebreaks)
					{
						// COMPOSER_HACK
						// Disable text indentation.
						// this.indent.increaseText();
					}
					else
					{
						// COMPOSER_HACK
						// Disable block indentation.
						// this.indent.increaseBlocks();
					}

					setTimeout($.proxy(function(){
						this.selection.restore();
					}, this), 1);
				},
				increaseLists: function()
				{
					document.execCommand('indent');

					this.indent.fixEmptyIndent();
					this.clean.normalizeLists();
					this.clean.clearUnverified();
				},
				increaseBlocks: function()
				{
					$.each(this.selection.getBlocks(), $.proxy(function(i, elem)
					{
						if (elem.tagName === 'TD' || elem.tagName === 'TH') return;

						var $el = this.utils.getAlignmentElement(elem);

						var left = this.utils.normalize($el.css('margin-left')) + this.opts.indentValue;
						$el.css('margin-left', left + 'px');

					}, this));
				},
				increaseText: function()
				{
					var wrapper = this.selection.wrap('div');
					$(wrapper).attr('data-tagblock', 'redactor');
					$(wrapper).css('margin-left', this.opts.indentValue + 'px');
				},
				decrease: function()
				{
					this.buffer.set();
					this.selection.save();

					var block = this.selection.getBlock();
					if (block && block.tagName == 'LI')
					{
						this.indent.decreaseLists();
					}
					else
					{
						// COMPOSER_HACK
						// Disable block indentation.
						// this.indent.decreaseBlocks();
					}

					this.selection.restore();
				},
				decreaseLists: function ()
				{
					document.execCommand('outdent');

					var current = this.selection.getCurrent();

					var $item = $(current).closest('li');
					var $parent = $item.parent();
					if ($item.size() !== 0 && $parent.size() !== 0 && $parent[0].tagName == 'LI')
					{
						$parent.after($item);
					}

					this.indent.fixEmptyIndent();

					if (!this.opts.linebreaks && $item.size() === 0)
					{
						document.execCommand('formatblock', false, 'p');
						this.$editor.find('ul, ol, blockquote, p').each($.proxy(this.utils.removeEmpty, this));
					}

					this.clean.clearUnverified();
				},
				decreaseBlocks: function()
				{
					$.each(this.selection.getBlocks(), $.proxy(function(i, elem)
					{
						var $el = this.utils.getAlignmentElement(elem);
						var left = this.utils.normalize($el.css('margin-left')) - this.opts.indentValue;

						if (left <= 0)
						{
							if (this.opts.linebreaks && typeof($el.data('tagblock')) !== 'undefined')
							{
								$el.replaceWith($el.html() + '<br />');
							}
							else
							{
								$el.css('margin-left', '');
								this.utils.removeEmptyAttr($el, 'style');
							}
						}
						else
						{
							$el.css('margin-left', left + 'px');
						}

					}, this));
				},
				fixEmptyIndent: function()
				{
					var block = this.selection.getBlock();

					if (this.range.collapsed && block && block.tagName == 'LI' && this.utils.isEmpty($(block).text()))
					{
						var $block = $(block);
						$block.find('span').not('.redactor-selection-marker').contents().unwrap();
						$block.append('<br>');
					}
				}
			};
		},
		inline: function()
		{
			return {
				formatting: function(name)
				{
					var type, value;

					if (typeof this.formatting[name].style != 'undefined') type = 'style';
					else if (typeof this.formatting[name].class != 'undefined') type = 'class';

					if (type) value = this.formatting[name][type];

					this.inline.format(this.formatting[name].tag, type, value);

				},
				format: function(tag, type, value)
				{
					// Stop formatting pre and headers
					if (this.utils.isCurrentOrParent('PRE') || this.utils.isCurrentOrParentHeader()) return;

					var tags = ['b', 'bold', 'i', 'italic', 'underline', 'strikethrough', 'deleted', 'superscript', 'subscript'];
					var replaced = ['strong', 'strong', 'em', 'em', 'u', 'del', 'del', 'sup', 'sub'];

					for (var i = 0; i < tags.length; i++)
					{
						if (tag == tags[i]) tag = replaced[i];
					}

					this.inline.type = type || false;
					this.inline.value = value || false;

					this.buffer.set();
					this.$editor.focus();

					this.selection.get();

					if (this.range.collapsed)
					{
						this.inline.formatCollapsed(tag);
					}
					else
					{
						this.inline.formatMultiple(tag);
					}
				},
				formatCollapsed: function(tag)
				{
					var current = this.selection.getCurrent();
					var $parent = $(current).closest(tag + '[data-redactor-tag=' + tag + ']');

					// inline there is
					if ($parent.size() !== 0)
					{
						this.caret.setAfter($parent[0]);

						// remove empty
						if (this.utils.isEmpty($parent.text())) $parent.remove();



						return;
					}

					// create empty inline
					var node = $('<' + tag + '>').attr('data-verified', 'redactor').attr('data-redactor-tag', tag);
					node.html(this.opts.invisibleSpace);

					node = this.inline.setFormat(node);

					var node = this.insert.node(node);
					this.caret.setEnd(node);


				},
				formatMultiple: function(tag)
				{
					this.inline.formatConvert(tag);

					this.selection.save();
					document.execCommand('strikethrough');


					this.$editor.find('strike').each($.proxy(function(i,s)
					{
						var $el = $(s);

						this.inline.formatRemoveSameChildren($el, tag);

						var $span;
						if (this.inline.type)
						{
							$span = $('<span>').attr('data-redactor-tag', tag).attr('data-verified', 'redactor');
							$span = this.inline.setFormat($span);
						}
						else
						{
							$span = $('<' + tag + '>').attr('data-redactor-tag', tag).attr('data-verified', 'redactor');
						}

						$el.replaceWith($span.html($el.contents()));

						if (tag == 'span')
						{
							var $parent = $span.parent();
							if ($parent && $parent[0].tagName == 'SPAN' && this.inline.type == 'style')
							{
								var arr = this.inline.value.split(';');

								for (var z = 0; z < arr.length; z++)
								{
									if (arr[z] === '') return;
									var style = arr[z].split(':');
									$parent.css(style[0], '');

									if (this.utils.removeEmptyAttr($parent, 'style'))
									{
										$parent.replaceWith($parent.contents());
									}

								}

							}
						}

					}, this));

					// clear text decoration
					if (tag != 'span')
					{
						this.$editor.find(this.opts.inlineTags.join(', ')).each($.proxy(function(i,s)
						{
							var $el = $(s);
							var property = $el.css('text-decoration');
							if (property == 'line-through')
							{
								$el.css('text-decoration', '');
								this.utils.removeEmptyAttr($el, 'style');
							}
						}, this));
					}

					if (tag != 'del')
					{
						var _this = this;
						this.$editor.find('inline').each(function(i,s)
						{
							_this.utils.replaceToTag(s, 'del');
						});
					}

					this.selection.restore();


				},
				formatRemoveSameChildren: function($el, tag)
				{
					$el.children(tag).each(function()
					{
						var $child = $(this);
						if (!$child.hasClass('redactor-selection-marker'))
						{
							$child.contents().unwrap();
						}
					});
				},
				formatConvert: function(tag)
				{
					this.selection.save();

					var find = '';
					if (this.inline.type == 'class') find = '[data-redactor-class=' + this.inline.value + ']';
					else if (this.inline.type == 'style')
					{
						find = '[data-redactor-style="' + this.inline.value + '"]';
					}

					if (tag != 'del')
					{
						var self = this;
						this.$editor.find('del').each(function(i,s)
						{
							self.utils.replaceToTag(s, 'inline');
						});
					}

					this.$editor.find('[data-redactor-tag="' + tag + '"]' + find).each(function()
					{
						if (find === '' && tag == 'span' && this.tagName.toLowerCase() == tag) return;

						var $el = $(this);
						$el.replaceWith($('<strike />').html($el.contents()));

					});

					this.selection.restore();
				},
				setFormat: function(node)
				{
					switch (this.inline.type)
					{
						case 'class':

							if (node.hasClass(this.inline.value))
							{
								node.removeClass(this.inline.value);
								node.removeAttr('data-redactor-class');
							}
							else
							{
								node.addClass(this.inline.value);
								node.attr('data-redactor-class', this.inline.value);
							}


						break;
						case 'style':

							node[0].style.cssText = this.inline.value;
							node.attr('data-redactor-style', this.inline.value);

						break;
					}

					return node;
				},
				removeStyle: function()
				{
					this.buffer.set();
					var current = this.selection.getCurrent();
					var nodes = this.selection.getInlines();

					this.selection.save();

					if (current && current.tagName === 'SPAN')
					{
						var $s = $(current);

						$s.removeAttr('style');
						if ($s[0].attributes.length === 0)
						{
							$s.replaceWith($s.contents());
						}
					}

					$.each(nodes, $.proxy(function(i,s)
					{
						var $s = $(s);
						if ($.inArray(s.tagName.toLowerCase(), this.opts.inlineTags) != -1 && !$s.hasClass('redactor-selection-marker'))
						{
							$s.removeAttr('style');
							if ($s[0].attributes.length === 0)
							{
								$s.replaceWith($s.contents());
							}
						}
					}, this));

					this.selection.restore();


				},
				removeStyleRule: function(name)
				{
					this.buffer.set();
					var parent = this.selection.getParent();
					var nodes = this.selection.getInlines();

					this.selection.save();

					if (parent && parent.tagName === 'SPAN')
					{
						var $s = $(parent);

						$s.css(name, '');
						this.utils.removeEmptyAttr($s, 'style');
						if ($s[0].attributes.length === 0)
						{
							$s.replaceWith($s.contents());
						}
					}

					$.each(nodes, $.proxy(function(i,s)
					{
						var $s = $(s);
						if ($.inArray(s.tagName.toLowerCase(), this.opts.inlineTags) != -1 && !$s.hasClass('redactor-selection-marker'))
						{
							$s.css(name, '');
							this.utils.removeEmptyAttr($s, 'style');
							if ($s[0].attributes.length === 0)
							{
								$s.replaceWith($s.contents());
							}
						}
					}, this));

					this.selection.restore();

				},
				removeFormat: function()
				{
					this.buffer.set();
					var current = this.selection.getCurrent();

					this.selection.save();

					document.execCommand('removeFormat');

					if (current && current.tagName === 'SPAN')
					{
						$(current).replaceWith($(current).contents());
					}


					$.each(this.selection.getNodes(), $.proxy(function(i,s)
					{
						var $s = $(s);
						if ($.inArray(s.tagName.toLowerCase(), this.opts.inlineTags) != -1 && !$s.hasClass('redactor-selection-marker'))
						{
							$s.replaceWith($s.contents());
						}
					}, this));

					this.selection.restore();


				},
				toggleClass: function(className)
				{
					this.inline.format('span', 'class', className);
				},
				toggleStyle: function(value)
				{
					this.inline.format('span', 'style', value);
				}
			};
		},
		insert: function()
		{
			return {
				set: function(html, clean)
				{
					html = this.clean.setVerified(html);

					if (typeof clean == 'undefined')
					{
						html = this.clean.onPaste(html, false);
					}

					this.$editor.html(html);
					this.selection.remove();
					this.focus.setEnd();
					this.clean.normalizeLists();

					// this.observe.load();

					if (typeof clean == 'undefined')
					{
						setTimeout($.proxy(this.clean.clearUnverified, this), 10);
					}
				},
				text: function(text)
				{
					text = text.toString();
					text = $.trim(text);
					text = this.clean.getPlainText(text, false);

					this.$editor.focus();

					if (this.utils.browser('msie'))
					{
						this.insert.htmlIe(text);
					}
					else
					{
						this.selection.get();

						this.range.deleteContents();
						var el = document.createElement("div");
						el.innerHTML = text;
						var frag = document.createDocumentFragment(), node, lastNode;
						while ((node = el.firstChild))
						{
							lastNode = frag.appendChild(node);
						}

						this.range.insertNode(frag);

						if (lastNode)
						{
							var range = this.range.cloneRange();
							range.setStartAfter(lastNode);
							range.collapse(true);
							this.sel.removeAllRanges();
							this.sel.addRange(range);
						}
					}


					this.clean.clearUnverified();
				},
				htmlWithoutClean: function(html)
				{
					this.insert.html(html, false);
				},
				html: function(html, clean)
				{
					if (typeof clean == 'undefined') clean = true;

					this.$editor.focus();

					html = this.clean.setVerified(html);

					if (clean)
					{
						html = this.clean.onPaste(html);
					}

					if (this.utils.browser('msie'))
					{
						this.insert.htmlIe(html);
					}
					else
					{
						if (this.clean.singleLine) this.insert.execHtml(html);
						else document.execCommand('insertHTML', false, html);

						this.insert.htmlFixMozilla();

					}

					this.clean.normalizeLists();

					// remove empty paragraphs finaly
					if (!this.opts.linebreaks)
					{
						this.$editor.find('p').each($.proxy(this.utils.removeEmpty, this));
					}


					// this.observe.load();

					if (clean)
					{
						this.clean.clearUnverified();
					}

				},
				htmlFixMozilla: function()
				{
					// FF inserts empty p when content was selected dblclick
					if (!this.utils.browser('mozilla')) return;

					var $next = $(this.selection.getBlock()).next();
					if ($next.length > 0 && $next[0].tagName == 'P' && $next.html() === '')
					{
						$next.remove();
					}

				},
				htmlIe: function(html)
				{
					if (this.utils.isIe11())
					{
						var parent = this.utils.isCurrentOrParent('P');
						var $html = $('<div>').append(html);
						var blocksMatch = $html.contents().is('p, :header, dl, ul, ol, div, table, td, blockquote, pre, address, section, header, footer, aside, article');

						if (parent && blocksMatch) this.insert.ie11FixInserting(parent, html);
						else this.insert.ie11PasteFrag(html);

						return;
					}

					document.selection.createRange().pasteHTML(html);

				},
				execHtml: function(html)
				{
					html = this.clean.setVerified(html);

					this.selection.get();

					this.range.deleteContents();

					var el = document.createElement('div');
					el.innerHTML = html;

					var frag = document.createDocumentFragment(), node, lastNode;
					while ((node = el.firstChild))
					{
						lastNode = frag.appendChild(node);
					}

					this.range.insertNode(frag);

					this.range.collapse(true);
					this.caret.setAfter(lastNode);

				},
				node: function(node, deleteContents)
				{
					node = node[0] || node;

					var html = this.utils.getOuterHtml(node);
					html = this.clean.setVerified(html);

					node = $(html)[0];

					this.selection.get();

					if (deleteContents !== false)
					{
						this.range.deleteContents();
					}

					this.range.insertNode(node);
					this.range.collapse(false);
					this.selection.addRange();

					return node;
				},
				nodeToPoint: function(node, x, y)
				{
					node = node[0] || node;

					this.selection.get();

					var range;
					if (document.caretPositionFromPoint)
					{
						var pos = document.caretPositionFromPoint(x, y);

						this.range.setStart(pos.offsetNode, pos.offset);
						this.range.collapse(true);
						this.range.insertNode(node);
					}
					else if (document.caretRangeFromPoint)
					{
						range = document.caretRangeFromPoint(x, y);
						range.insertNode(node);
					}
					else if (typeof document.body.createTextRange != "undefined")
					{
						range = document.body.createTextRange();
						range.moveToPoint(x, y);
						var endRange = range.duplicate();
						endRange.moveToPoint(x, y);
						range.setEndPoint("EndToEnd", endRange);
						range.select();
					}
				},
				nodeToCaretPositionFromPoint: function(e, node)
				{
					node = node[0] || node;

					var range;
					var x = e.clientX, y = e.clientY;
					if (document.caretPositionFromPoint)
					{
						var pos = document.caretPositionFromPoint(x, y);
						var sel = document.getSelection();
						range = sel.getRangeAt(0);
						range.setStart(pos.offsetNode, pos.offset);
						range.collapse(true);
						range.insertNode(node);
					}
					else if (document.caretRangeFromPoint)
					{
						range = document.caretRangeFromPoint(x, y);
						range.insertNode(node);
					}
					else if (typeof document.body.createTextRange != "undefined")
					{
						range = document.body.createTextRange();
						range.moveToPoint(x, y);
						var endRange = range.duplicate();
						endRange.moveToPoint(x, y);
						range.setEndPoint("EndToEnd", endRange);
						range.select();
					}

				},
				ie11FixInserting: function(parent, html)
				{
					var node = document.createElement('span');
					node.className = 'redactor-ie-paste';
					this.insert.node(node);

					var parHtml = $(parent).html();

					parHtml = '<p>' + parHtml.replace(/<span class="redactor-ie-paste"><\/span>/gi, '</p>' + html + '<p>') + '</p>';
					$(parent).replaceWith(parHtml);
				},
				ie11PasteFrag: function(html)
				{
					this.selection.get();
					this.range.deleteContents();

					var el = document.createElement("div");
					el.innerHTML = html;

					var frag = document.createDocumentFragment(), node, lastNode;
					while ((node = el.firstChild))
					{
						lastNode = frag.appendChild(node);
					}

					this.range.insertNode(frag);
				}
			};
		},
		keydown: function()
		{
			return {
				init: function(e)
				{
					// COMPOSER_HACK
					// Don't do anything when typing on input or textarea.
					if ($(e.target).is("input, textarea")) return;

					if (this.rtePaste) return;

					var key = e.which;
					var arrow = (key >= 37 && key <= 40);

					this.keydown.ctrl = e.ctrlKey || e.metaKey;
					this.keydown.current = this.selection.getCurrent();
					this.keydown.parent = this.selection.getParent();
					this.keydown.block = this.selection.getBlock();

					// detect tags
					this.keydown.pre = this.utils.isTag(this.keydown.current, 'pre');
					this.keydown.blockquote = this.utils.isTag(this.keydown.current, 'blockquote');
					this.keydown.figcaption = this.utils.isTag(this.keydown.current, 'figcaption');

					// shortcuts setup
					this.shortcuts.init(e, key);

					this.keydown.checkEvents(arrow, key);
					this.keydown.setupBuffer(e, key);
					this.keydown.addArrowsEvent(arrow);

					// callback
					var keydownStop = this.core.setCallback('keydown', e);
					if (keydownStop === false)
					{
						e.preventDefault();
						return false;
					}

					// ie and ff exit from table
					if (this.opts.enterKey && (this.utils.browser('msie') || this.utils.browser('mozilla')) && (key === this.keyCode.DOWN || key === this.keyCode.RIGHT))
					{
						var isEndOfTable = false;
						var $table = false;
						if (this.keydown.block && this.keydown.block.tagName === 'TD')
						{
							$table = $(this.keydown.block).closest('table');
						}

						if ($table && $table.find('td').last()[0] === this.keydown.block)
						{
							isEndOfTable = true;
						}

						if (this.utils.isEndOfElement() && isEndOfTable)
						{
							var node = $(this.opts.emptyHtml);
							$table.after(node);
							this.caret.setStart(node);
						}
					}

					// down
					if (this.opts.enterKey && key === this.keyCode.DOWN)
					{
						this.keydown.onArrowDown();
					}

					// turn off enter key
					if (!this.opts.enterKey && key === this.keyCode.ENTER)
					{
						e.preventDefault();
						// remove selected
						if (!this.range.collapsed) this.range.deleteContents();
						return;
					}

					// on enter
					if (key == this.keyCode.ENTER && !e.shiftKey && !e.ctrlKey && !e.metaKey)
					{
						var stop = this.core.setCallback('enter', e);
						if (stop === false)
						{
							e.preventDefault();
							return false;
						}

						// COMPOSER_HACK
						// Blockquote doesn't exit to paragraph tags
						// if (this.keydown.blockquote && this.keydown.exitFromBlockquote(e) === true)
						// {
						// 	return false;
						// }

						var current, $next;
						if (this.keydown.pre)
						{
							return this.keydown.insertNewLine(e);
						}
						// else if (this.keydown.blockquote || this.keydown.figcaption)
						// COMPOSER_HACK
						// Apply breakline behaviour to all non html editable container.
						else if (this.utils.getEditableContentType(this.keydown.block)=="text")
						{

							// COMPOSER_HACK
							// Improve sibling br tag detection by using range end node
							// instead of range start container. This fixes multiplying
							// breaklines when pressing enter.
							// current = this.selection.getCurrent();
							current = this.selection.getEndNode();
							$next = $(current).next();

							if ($next.size() !== 0 && $next[0].tagName == 'BR')
							{
								return this.keydown.insertBreakLine(e);
							}
							else if (this.utils.isEndOfElement() && (current && current != 'SPAN'))
							{
								return this.keydown.insertDblBreakLine(e);
							}
							else
							{
								return this.keydown.insertBreakLine(e);
							}
						}
						else if (this.opts.linebreaks && !this.keydown.block)
						{
							current = this.selection.getCurrent();
							$next = $(this.keydown.current).next();

							if (current !== false && $(current).hasClass('redactor-invisible-space'))
							{
								$(current).remove();
								return this.keydown.insertDblBreakLine(e);
							}
							else
							{
								if ($next.length === 0 && current === false && typeof $next.context != 'undefined')
								{
									return this.keydown.insertDblBreakLine(e);
								}

								return this.keydown.insertBreakLine(e);
							}
						}
						else if (this.opts.linebreaks && this.keydown.block)
						{
							setTimeout($.proxy(this.keydown.replaceDivToBreakLine, this), 1);
						}
						// paragraphs
						else if (!this.opts.linebreaks && this.keydown.block && this.keydown.block.tagName !== 'LI')
						{
							setTimeout($.proxy(this.keydown.replaceDivToParagraph, this), 1);
						}
						else if (!this.opts.linebreaks && !this.keydown.block)
						{
							return this.keydown.insertParagraph(e);
						}
					}


					// Shift+Enter or Ctrl+Enter
					if (key === this.keyCode.ENTER && (e.ctrlKey || e.shiftKey))
					{
						return this.keydown.onShiftEnter(e);
					}


					// tab or cmd + [
					if (key === this.keyCode.TAB || e.metaKey && key === 221 || e.metaKey && key === 219)
					{
						return this.keydown.onTab(e, key);
					}

					// COMPOSER_HACK
					// Special handling for image tag removal is not used in Composer.

					// image delete and backspace
					// if (key === this.keyCode.BACKSPACE || key === this.keyCode.DELETE)
					// {
					// 	var nodes = this.selection.getNodes();
					// 	if (nodes)
					// 	{
					// 		var len = nodes.length;
					// 		var last;
					// 		for (var i = 0; i < len; i++)
					// 		{
					// 			var children = $(nodes[i]).children('img');
					// 			if (children.size() !== 0)
					// 			{
					// 				var self = this;
					// 				$.each(children, function(z,s)
					// 				{
					// 					var $s = $(s);
					// 					if ($s.css('float') != 'none') return;

					// 					// image delete callback
					// 					self.core.setCallback('imageDelete', s.src, $s);
					// 					last = s;
					// 				});
					// 			}
					// 			else if (nodes[i].tagName == 'IMG')
					// 			{
					// 				if (last != nodes[i])
					// 				{
					// 					// image delete callback
					// 					this.core.setCallback('imageDelete', nodes[i].src, $(nodes[i]));
					// 					last = nodes[i];
					// 				}
					// 			}
					// 		}
					// 	}
					// }

					// backspace
					if (key === this.keyCode.BACKSPACE)
					{
						this.keydown.removeInvisibleSpace();
						this.keydown.removeEmptyListInTable(e);
					}
				},
				checkEvents: function(arrow, key)
				{
					if (!arrow && (this.core.getEvent() == 'click' || this.core.getEvent() == 'arrow'))
					{
						this.core.addEvent(false);

						if (this.keydown.checkKeyEvents(key))
						{
							this.buffer.set();
						}
					}
				},
				checkKeyEvents: function(key)
				{
					var k = this.keyCode;
					var keys = [k.BACKSPACE, k.DELETE, k.ENTER, k.SPACE, k.ESC, k.TAB, k.CTRL, k.META, k.ALT, k.SHIFT];

					return ($.inArray(key, keys) == -1) ? true : false;

				},
				addArrowsEvent: function(arrow)
				{
					if (!arrow) return;

					if ((this.core.getEvent() == 'click' || this.core.getEvent() == 'arrow'))
					{
						this.core.addEvent(false);
						return;
					}

					this.core.addEvent('arrow');
				},
				setupBuffer: function(e, key)
				{
					if (this.keydown.ctrl && key === 90 && !e.shiftKey && !e.altKey && this.opts.buffer.length) // z key
					{
						e.preventDefault();
						this.buffer.undo();
						return;
					}
					// undo
					else if (this.keydown.ctrl && key === 90 && e.shiftKey && !e.altKey && this.opts.rebuffer.length !== 0)
					{
						e.preventDefault();
						this.buffer.redo();
						return;
					}
					else if (!this.keydown.ctrl)
					{
						if (key == this.keyCode.BACKSPACE || key == this.keyCode.DELETE || (key == this.keyCode.ENTER && !e.ctrlKey && !e.shiftKey) || key == this.keyCode.SPACE)
						{
							this.buffer.set();
						}
					}
				},
				onArrowDown: function()
				{
					var tags = [this.keydown.blockquote, this.keydown.pre, this.keydown.figcaption];

					for (var i = 0; i < tags.length; i++)
					{
						if (tags[i])
						{
							this.keydown.insertAfterLastElement(tags[i]);
							return false;
						}
					}
				},
				onShiftEnter: function(e)
				{
					this.buffer.set();

					if (this.utils.isEndOfElement())
					{
						return this.keydown.insertDblBreakLine(e);
					}

					return this.keydown.insertBreakLine(e);
				},
				onTab: function(e, key)
				{
					if (!this.opts.tabKey) return true;

					// COMPOSER_HACK
					// This is preventing tabs to work
					// if (this.opts.tabAsSpaces === false) return true;

					e.preventDefault();

					var node;
					if (this.keydown.pre && !e.shiftKey)
					{
						// COMPOSER_HACK
						// Don't do this
						return;
						node = (this.opts.preSpaces) ? document.createTextNode(Array(this.opts.preSpaces + 1).join('\u00a0')) : document.createTextNode('\t');
						this.insert.node(node);

					}
					else if (this.opts.tabAsSpaces !== false)
					{
						// COMPOSER_HACK
						// Don't do this
						return;
						node = document.createTextNode(Array(this.opts.tabAsSpaces + 1).join('\u00a0'));
						this.insert.node(node);
					}
					else
					{
						if (e.metaKey && key === 219) this.indent.decrease();
						else if (e.metaKey && key === 221) this.indent.increase();
						else if (!e.shiftKey) this.indent.increase();
						else this.indent.decrease();
					}

					return false;
				},
				replaceDivToBreakLine: function()
				{
					var blockElem = this.selection.getBlock();
					var blockHtml = blockElem.innerHTML.replace(/<br\s?\/?>/gi, '');
					if ((blockElem.tagName === 'DIV' || blockElem.tagName === 'P') && blockHtml === '' && !$(blockElem).hasClass('redactor-editor'))
					{
						var br = document.createElement('br');

						$(blockElem).replaceWith(br);
						this.caret.setBefore(br);



						return false;
					}
				},
				replaceDivToParagraph: function()
				{
					var blockElem = this.selection.getBlock();
					var blockHtml = blockElem.innerHTML.replace(/<br\s?\/?>/gi, '');
					if (blockElem.tagName === 'DIV' && blockHtml === '' && !$(blockElem).hasClass('redactor-editor'))
					{
						var p = document.createElement('p');
						p.innerHTML = this.opts.invisibleSpace;

						$(blockElem).replaceWith(p);
						this.caret.setStart(p);



						return false;
					}
					else if (this.opts.cleanStyleOnEnter && blockElem.tagName == 'P')
					{
						$(blockElem).removeAttr('class').removeAttr('style');
					}
				},
				insertParagraph: function(e)
				{
					e.preventDefault();

					this.selection.get();

					var p = document.createElement('p');
					p.innerHTML = this.opts.invisibleSpace;

					this.range.deleteContents();
					this.range.insertNode(p);

					this.caret.setStart(p);



					return false;
				},
				exitFromBlockquote: function(e)
				{
					if (!this.utils.isEndOfElement()) return;

					var tmp = $.trim($(this.keydown.block).html());
					if (tmp.search(/(<br\s?\/?>){2}$/i) != -1)
					{
						e.preventDefault();

						if (this.opts.linebreaks)
						{
							var br = document.createElement('br');
							$(this.keydown.blockquote).after(br);

							this.caret.setBefore(br);
							$(this.keydown.block).html(tmp.replace(/<br\s?\/?>$/i, ''));
						}
						else
						{
							var node = $(this.opts.emptyHtml);
							$(this.keydown.blockquote).after(node);
							this.caret.setStart(node);
						}

						return true;

					}

					return;

				},
				insertAfterLastElement: function(element)
				{
					if (!this.utils.isEndOfElement()) return;

					this.buffer.set();

					if (this.opts.linebreaks)
					{
						var contents = $('<div>').append($.trim(this.$editor.html())).contents();
						var last = contents.last()[0];
						if (last.tagName == 'SPAN' && last.innerHTML === '')
						{
							last = contents.prev()[0];
						}

						if (this.utils.getOuterHtml(last) != this.utils.getOuterHtml(element)) return;

						var br = document.createElement('br');
						$(element).after(br);
						this.caret.setAfter(br);

					}
					else
					{
						if (this.$editor.contents().last()[0] !== element) return;

						var node = $(this.opts.emptyHtml);
						$(element).after(node);
						this.caret.setStart(node);
					}
				},
				insertNewLine: function(e)
				{
					e.preventDefault();

					var node = document.createTextNode('\n');

					this.selection.get();

					this.range.deleteContents();
					this.range.insertNode(node);

					this.caret.setAfter(node);



					return false;
				},
				insertBreakLine: function(e)
				{
					return this.keydown.insertBreakLineProcessing(e);
				},
				insertDblBreakLine: function(e)
				{
					return this.keydown.insertBreakLineProcessing(e, true);
				},
				insertBreakLineProcessing: function(e, dbl)
				{
					e.stopPropagation();

					this.selection.get();
					var br1 = document.createElement('br');

					this.range.deleteContents();
					this.range.insertNode(br1);

					if (dbl === true)
					{
						var br2 = document.createElement('br');
						this.range.insertNode(br2);
						this.caret.setAfter(br2);
					}
					else
					{
						this.caret.setAfter(br1);
					}



					return false;
				},
				removeInvisibleSpace: function()
				{
					var $current = $(this.keydown.current);

					// COMPOSER HACK
					// Only remove if current node is not contenteditable
					if ($current.text().search(/^\u200B$/g) === 0 && $current.is(":not([contenteditable])"))
					{
						$current.remove();
					}

					// COMPOSER_HACK
					// Content editable should always have invisible space when empty
					// var parent = $current.closest('[contenteditable]:not([data-paragraph])')[0];
					// if (parent && parent.childNodes.length <= 1) {
					// 	var node = parent.childNodes[0];
					// 	if (node && node.nodeType==3 && node.nodeValue.length <= 1) {
					// 		parent.innerHTML = this.opts.invisibleSpace;
					// 	}
					// }
				},
				removeEmptyListInTable: function(e)
				{
					var $current = $(this.keydown.current);
					var $parent = $(this.keydown.parent);
					var td = $current.closest('td');

					if (td.size() !== 0 && $current.closest('li') && $parent.children('li').size() === 1)
					{
						if (!this.utils.isEmpty($current.text())) return;

						e.preventDefault();

						$current.remove();
						$parent.remove();

						this.caret.setStart(td);
					}
				}
			};
		},
		keyup: function()
		{
			return {
				init: function(e)
				{
					// COMPOSER_HACK
					// Don't do anything when typing on input or textarea.
					if ($(e.target).is("input, textarea")) return;

					if (this.rtePaste) return;

					var key = e.which;

					this.keyup.current = this.selection.getCurrent();
					this.keyup.parent = this.selection.getParent();
					var $parent = this.utils.isRedactorParent($(this.keyup.parent).parent());

					// callback
					var keyupStop = this.core.setCallback('keyup', e);
					if (keyupStop === false)
					{
						e.preventDefault();
						return false;
					}

					// replace to p before / after the table or body
					if (!this.opts.linebreaks && this.keyup.current.nodeType == 3 && this.keyup.current.length <= 1 && (this.keyup.parent === false || this.keyup.parent.tagName == 'BODY'))
					{
						this.keyup.replaceToParagraph();
					}

					// replace div after lists
					if (!this.opts.linebreaks && this.utils.isRedactorParent(this.keyup.current) && this.keyup.current.tagName === 'DIV')
					{
						this.keyup.replaceToParagraph(false);
					}


					if (!this.opts.linebreaks && $(this.keyup.parent).hasClass('redactor-invisible-space') && ($parent === false || $parent[0].tagName == 'BODY'))
					{
						$(this.keyup.parent).contents().unwrap();
						this.keyup.replaceToParagraph();
					}

					if (key === this.keyCode.DELETE || key === this.keyCode.BACKSPACE)
					{
						// clear unverified
						this.clean.clearUnverified();

						// remove empty paragraphs
						// COMPOSER_HACK
						// this.$editor.find('p').each($.proxy(this.utils.removeEmpty, this));
						this.$editor.find('p:not([contenteditable])').each($.proxy(this.utils.removeEmpty, this));

						// remove invisible space
						if (this.keyup.current && this.keyup.current.tagName == 'DIV' && this.utils.isEmpty(this.keyup.current.innerHTML))
						{
							if (this.opts.linebreaks)
							{
								$(this.keyup.current).after(this.selection.getMarkerAsHtml());
								this.selection.restore();
								$(this.keyup.current).remove();
							}
						}

						// if empty
						return this.keyup.formatEmpty(e);
					}

					var container = this.utils.getEditableContainer(e.target);
					var contentType = this.utils.getEditableContentType(e.target);
					var $container = $(container);

					// COMPOSER_HACK
					// Content editable should always have invisible space when empty
					if (contentType=="text" && $(container).text()=="") {
						container.innerHTML = this.opts.invisibleSpace;
						this.caret.setEnd(container);
					}
				},
				replaceToParagraph: function(clone)
				{
					// COMPOSER_HACK
					// This is not needed for composer
					return;

					var $current = $(this.keyup.current);

					var node;
					if (clone === false)
					{
						node = $('<p>').append($current.html());
					}
					else
					{
						node = $('<p>').append($current.clone());
					}

					$current.replaceWith(node);
					var next = $(node).next();
					if (typeof(next[0]) !== 'undefined' && next[0].tagName == 'BR')
					{
						next.remove();
					}

					this.caret.setEnd(node);
				},
				formatEmpty: function(e)
				{
					// COMPOSER_HACK
					// formatEmpty works on editable container,
					// not entire editor.

					var container = this.utils.getEditableContainer(e.target);
					var contentType = this.utils.getEditableContentType(e.target);
					var $container = $(container);

					// Only for html content editables
					if (contentType!=="html") return;

					// var html = $.trim(this.$editor.html());
					var html = $.trim($container.html());

					if (!this.utils.isEmpty(html)) return;

					e.preventDefault();

					if (this.opts.linebreaks)
					{
						//this.$editor.html(this.selection.getMarkerAsHtml());
						$container.html(this.selection.getMarkerAsHtml());
						this.selection.restore();
					}
					else
					{
						// html = '<p><br /></p>';
						// this.$editor.html(html);
						// this.focus.setStart();

						// COMPOSER_HACK
						// Focus directly on the p element itself.
						var p = document.createElement("p");
						p.innerHTML = this.opts.invisibleSpace;
						$container.empty().append(p);
						this.caret.setEnd(p);
					}

					return false;
				}
			};
		},
		list: function()
		{
			return {
				toggle: function(cmd)
				{
					if (!this.utils.browser('msie')) this.$editor.focus();

					this.buffer.set();
					this.selection.save();

					var parent = this.selection.getParent();
					var $list = $(parent).closest('ol, ul');

					// COMPOSER_HACK
					// Also get block of parent
					var block = $(parent).closest(EBD.block);

					if (!this.utils.isRedactorParent($list) && $list.size() !== 0)
					{
						$list = false;
					}

					var isUnorderedCmdOrdered, isOrderedCmdUnordered;
					var remove = false;
					if ($list && $list.length)
					{
						remove = true;
						var listTag = $list[0].tagName;

						isUnorderedCmdOrdered = (cmd === 'orderedlist' && listTag === 'UL');
						isOrderedCmdUnordered = (cmd === 'unorderedlist' && listTag === 'OL');
					}

					if (isUnorderedCmdOrdered)
					{
						this.utils.replaceToTag($list, 'ol');
					}
					else if (isOrderedCmdUnordered)
					{
						this.utils.replaceToTag($list, 'ul');
					}
					else
					{
						if (remove)
						{
							this.list.remove(cmd);
						}
						else
						{
							this.list.insert(cmd);
						}
					}

					// COMPOSER_HACK
					// Trigger composerListFormat event
					this.$editor.trigger("composerListFormat", [block]);

					this.selection.restore();

				},
				insert: function(cmd)
				{
					var parent = this.selection.getParent();
					var current = this.selection.getCurrent();
					var $td = $(current).closest('td, th');

					if (this.utils.browser('msie') && this.opts.linebreaks)
					{
						this.list.insertInIe(cmd);
					}
					else
					{
						document.execCommand('insert' + cmd);
					}

					var $list = $(this.selection.getParent()).closest('ol, ul');

					if ($td.size() !== 0)
					{
						var prev = $td.prev();
						var html = $td.html();
						$td.html('');
						if (prev && prev.length === 1 && (prev[0].tagName === 'TD' || prev[0].tagName === 'TH'))
						{
							$(prev).after($td);
						}
						else
						{
							$(parent).prepend($td);
						}

						$td.html(html);
					}

					if (this.utils.isEmpty($list.find('li').text()))
					{
						var $children = $list.children('li');
						$children.find('br').remove();
						$children.append(this.selection.getMarkerAsHtml());
					}

					if ($list.length)
					{
						// remove block-element list wrapper
						var $listParent = $list.parent();
						if (this.utils.isRedactorParent($listParent) && $listParent[0].tagName != 'LI' && this.utils.isBlock($listParent[0]))
						{
							$listParent.replaceWith($listParent.contents());
						}
					}

					if (!this.utils.browser('msie'))
					{
						this.$editor.focus();
					}

					this.clean.clearUnverified();
				},
				insertInIe: function(cmd)
				{
					var wrapper = this.selection.wrap('div');
					var wrapperHtml = $(wrapper).html();

					var tmpList = (cmd == 'orderedlist') ? $('<ol>') : $('<ul>');
					var tmpLi = $('<li>');

					if ($.trim(wrapperHtml) === '')
					{
						tmpLi.append(this.selection.getMarkerAsHtml());
						tmpList.append(tmpLi);
						this.$editor.find('#selection-marker-1').replaceWith(tmpList);
					}
					else
					{
						var items = wrapperHtml.split(/<br\s?\/?>/gi);
						if (items)
						{
							for (var i = 0; i < items.length; i++)
							{
								if ($.trim(items[i]) !== '')
								{
									tmpList.append($('<li>').html(items[i]));
								}
							}
						}
						else
						{
							tmpLi.append(wrapperHtml);
							tmpList.append(tmpLi);
						}

						$(wrapper).replaceWith(tmpList);
					}
				},
				remove: function(cmd)
				{
					document.execCommand('insert' + cmd);

					var $current = $(this.selection.getCurrent());

					this.indent.fixEmptyIndent();

					if (!this.opts.linebreaks && $current.closest('li, th, td').size() === 0)
					{
						document.execCommand('formatblock', false, 'p');
						this.$editor.find('ul, ol, blockquote').each($.proxy(this.utils.removeEmpty, this));
					}

					var $table = $(this.selection.getCurrent()).closest('table');
					var $prev = $table.prev();
					if (!this.opts.linebreaks && $table.size() !== 0 && $prev.size() !== 0 && $prev[0].tagName == 'BR')
					{
						$prev.remove();
					}

					this.clean.clearUnverified();

				}
			};
		},
		paragraphize: function()
		{
			return {
				load: function(html)
				{
					if (this.opts.linebreaks) return html;
					if (html === '' || html === '<p></p>') return this.opts.emptyHtml;

					this.paragraphize.blocks = ['table', 'div', 'pre', 'form', 'ul', 'ol', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'dl', 'blockquote', 'figcaption',
					'address', 'section', 'header', 'footer', 'aside', 'article', 'object', 'style', 'script', 'iframe', 'select', 'input', 'textarea',
					'button', 'option', 'map', 'area', 'math', 'hr', 'fieldset', 'legend', 'hgroup', 'nav', 'figure', 'details', 'menu', 'summary', 'p'];

					html = html + "\n";

					this.paragraphize.safes = [];
					this.paragraphize.z = 0;

					html = html.replace(/(<br\s?\/?>){1,}\n?<\/blockquote>/gi, '</blockquote>');

					html = this.paragraphize.getSafes(html);
					html = this.paragraphize.getSafesComments(html);
					html = this.paragraphize.replaceBreaksToNewLines(html);
					html = this.paragraphize.replaceBreaksToParagraphs(html);
					html = this.paragraphize.clear(html);
					html = this.paragraphize.restoreSafes(html);

					html = html.replace(new RegExp('<br\\s?/?>\n?<(' + this.paragraphize.blocks.join('|') + ')(.*?[^>])>', 'gi'), '<p><br /></p>\n<$1$2>');

					return $.trim(html);
				},
				getSafes: function(html)
				{
					var $div = $('<div />').append(html);

					// remove paragraphs in blockquotes
					$div.find('blockquote p').replaceWith(function()
					{
						return $(this).append('<br />').contents();
					});

					html = $div.html();

					$div.find(this.paragraphize.blocks.join(', ')).each($.proxy(function(i,s)
					{
						this.paragraphize.z++;
						this.paragraphize.safes[this.paragraphize.z] = s.outerHTML;
						html = html.replace(s.outerHTML, '\n{replace' + this.paragraphize.z + '}');

					}, this));

					return html;
				},
				getSafesComments: function(html)
				{
					var commentsMatches = html.match(/<!--([\w\W]*?)-->/gi);

					if (!commentsMatches) return html;

					$.each(commentsMatches, $.proxy(function(i,s)
					{
						this.paragraphize.z++;
						this.paragraphize.safes[this.paragraphize.z] = s;
						html = html.replace(s, '\n{replace' + this.paragraphize.z + '}');
					}, this));

					return html;
				},
				restoreSafes: function(html)
				{
					$.each(this.paragraphize.safes, function(i,s)
					{
						html = html.replace('{replace' + i + '}', s);
					});

					return html;
				},
				replaceBreaksToParagraphs: function(html)
				{
					var htmls = html.split(new RegExp('\n', 'g'), -1);

					html = '';
					if (htmls)
					{
						var len = htmls.length;
						for (var i = 0; i < len; i++)
						{
							if (!htmls.hasOwnProperty(i)) return;

							if (htmls[i].search('{replace') == -1)
							{
								htmls[i] = htmls[i].replace(/<p>\n\t?<\/p>/gi, '');
								htmls[i] = htmls[i].replace(/<p><\/p>/gi, '');

								if (htmls[i] !== '')
								{
									html += '<p>' +  htmls[i].replace(/^\n+|\n+$/g, "") + "</p>";
								}
							}
							else html += htmls[i];
						}
					}

					return html;
				},
				replaceBreaksToNewLines: function(html)
				{
					html = html.replace(/<br \/>\s*<br \/>/gi, "\n\n");
					html = html.replace(/<br\s?\/?>\n?<br\s?\/?>/gi, "\n<br /><br />");

					html = html.replace(new RegExp("\r\n", 'g'), "\n");
					html = html.replace(new RegExp("\r", 'g'), "\n");
					html = html.replace(new RegExp("/\n\n+/"), 'g', "\n\n");

					return html;
				},
				clear: function(html)
				{
					html = html.replace(new RegExp('</blockquote></p>', 'gi'), '</blockquote>');
					html = html.replace(new RegExp('<p></blockquote>', 'gi'), '</blockquote>');
					html = html.replace(new RegExp('<p><blockquote>', 'gi'), '<blockquote>');
					html = html.replace(new RegExp('<blockquote></p>', 'gi'), '<blockquote>');

					html = html.replace(new RegExp('<p><p ', 'gi'), '<p ');
					html = html.replace(new RegExp('<p><p>', 'gi'), '<p>');
					html = html.replace(new RegExp('</p></p>', 'gi'), '</p>');
					html = html.replace(new RegExp('<p>\\s?</p>', 'gi'), '');
					html = html.replace(new RegExp("\n</p>", 'gi'), '</p>');
					html = html.replace(new RegExp('<p>\t?\t?\n?<p>', 'gi'), '<p>');
					html = html.replace(new RegExp('<p>\t*</p>', 'gi'), '');

					return html;
				}
			};
		},
		paste: function()
		{
			return {
				init: function(e)
				{
					// COMPOSER_HACK
					// Don't do anything when pasting on input or textarea.
					if ($(e.target).is("input, textarea")) return;

					if (!this.opts.cleanOnPaste) return;

					this.rtePaste = true;

					this.buffer.set();
					this.selection.save();

					// COMPOSER HACK
					// For browsers that support clipboardData (Chrome/Firefox/Safari),
					// we'll get the content from the clipboard.
					var clipboardData = e.originalEvent.clipboardData;

					if (clipboardData) {

						var types = clipboardData.types;
						var html = "";

						// Chrome/Firefox supplies both text/html & text/plain even if content is plain text.
						// Safari supplies text/html if content is html, and text/plain if content is plain text.
						// So we won't have to worry if the pasted content was actually html converted into plain text.
						if ($.indexOf(types, "text/html") > -1) {
							html = clipboardData.getData("text/html");
						} else if ($.indexOf(types, "text/plain") > -1) {
							html = clipboardData.getData("text/plain");
						}

						// Restore scroll & selection
						this.selection.restore();

						// COMPOSER_HACK
						// Only allow a limited set of tags, the rest convert to text.
						var container = $("<div>").html(html);
						container.find(":not(p, br, ol, ul, li, span, b, i, u, a, small, strong, sup, sub, pre)")
							.each(function(){
								$(this).replaceWith($(this).text());
							});
						html = container.html();

						// Paste html
						this.paste.insert(html);

						// Prevent default pasting behaviour from happening.
						e.preventDefault();

					} else {

						this.utils.saveScroll();

						this.paste.createPasteBox();

						$(window).on('scroll.redactor-freeze', $.proxy(function()
						{
							$(window).scrollTop(this.saveBodyScroll);

						}, this));

						setTimeout($.proxy(function()
						{
							var html = this.$pasteBox.html();

							this.$pasteBox.remove();

							this.selection.restore();
							this.utils.restoreScroll();

							this.paste.insert(html);

							$(window).off('scroll.redactor-freeze');

						}, this), 1);
					}
				},
				createPasteBox: function()
				{
					this.$pasteBox = $('<div>').html('').attr('contenteditable', 'true').css({ position: 'fixed', width: 0, top: 0, left: '-9999px' });

					this.$box.parent().append(this.$pasteBox);
					this.$pasteBox.focus();
				},

				insert: function(html)
				{
					html = this.core.setCallback('pasteBefore', html);

					// COMPOSER_HACK
					// Determine content type of editable container and switch to
					// plain text pasting if necessary.
					var current = this.selection.getCurrent(),
						contentType = this.utils.getEditableContentType(current);

					// Switch to plain text pasting if editable container
					// only supports plain text.
					if (contentType=="text") {
						this.opts.pastePlainText = true;
					}

					// clean
					html = this.clean.onPaste(html);

					// For plain text pasting, replace all p tags with br.
					if (contentType=="text") {
						html = this.clean.replaceParagraphsToBr(html);
					}

					// TODO: Listen to this event and trigger composerBlockPaste
					// for block handler to further manipulate content.
					html = this.core.setCallback('paste', html);

					// Insert html with no further cleaning required
					this.insert.html(html, false);

					// Revert pastePlainText flag
					this.opts.pastePlainText = false;

					this.rtePaste = false;

					setTimeout($.proxy(this.clean.clearUnverified, this), 10);

					// clean empty spans
					setTimeout($.proxy(function()
					{
						var spans = this.$editor.find('span');
						$.each(spans, function(i,s)
						{
							var html = s.innerHTML.replace(/[\u200B-\u200D\uFEFF]/, '');
							if (html === '' && s.attributes.length === 0) $(s).remove();

						});

					}, this), 10);
				}
			};
		},
		selection: function()
		{
			return {
				get: function()
				{
					this.sel = document.getSelection();

					if (document.getSelection && this.sel.getRangeAt && this.sel.rangeCount)
					{
						this.range = this.sel.getRangeAt(0);
					}
					else
					{
						this.range = document.createRange();
					}

					// COMPOSER_HACK
					// Return selection
					return this.sel;
				},
				addRange: function()
				{
					try {
						this.sel.removeAllRanges();
					} catch (e) {}

					this.sel.addRange(this.range);
				},
				getCurrent: function()
				{
					var el = false;
					this.selection.get();

					if (this.sel && this.sel.rangeCount > 0)
					{
						el = this.sel.getRangeAt(0).startContainer;
					}

					return this.utils.isRedactorParent(el);
				},
				// COMPOSER_HACK
				// Ability to get startNode from range
				getStartNode: function() {

					var el = false;
					this.selection.get();

					if (this.sel && this.sel.rangeCount > 0)
					{
						el = this.range.startContainer;

						// If not Text, CDATA, Comment, get actual node.
						if (!/3|4|8/.test(el.nodeType)) {
							el = el.childNodes[this.range.startOffset];
						}
					}

					return this.utils.isRedactorParent(el);
				},
				// COMPOSER_HACK
				// Ability to get endNode from range
				getEndNode: function() {

					var el = false;

					if (this.sel && this.sel.rangeCount > 0)
					{
						el = this.range.endContainer;

						// If not Text, CDATA, Comment, get actual node.
						if (!/3|4|8/.test(el.nodeType)) {
							el = el.childNodes[this.range.endOffset];
						}
					}

					return this.utils.isRedactorParent(el);
				},
				getParent: function(elem)
				{
					elem = elem || this.selection.getCurrent();
					if (elem)
					{
						return this.utils.isRedactorParent($(elem).parent()[0]);
					}

					return false;
				},
				getBlock: function(node)
				{
					node = node || this.selection.getCurrent();

					while (node)
					{
						if (this.utils.isBlockTag(node.tagName))
						{
							// COMPOSER_HACK
							// Test hasClass against .ebd classname
							// return ($(node).hasClass('redactor-editor')) ? false : node;
							return ($(node).hasClass('ebd')) ? false : node;
						}

						node = node.parentNode;
					}

					return false;
				},
				getInlines: function(nodes)
				{
					this.selection.get();

					if (this.range && this.range.collapsed)
					{
						return false;
					}

					var inlines = [];
					nodes = (typeof nodes == 'undefined') ? this.selection.getNodes() : nodes;
					var inlineTags = this.opts.inlineTags;
					inlineTags.push('span');
					$.each(nodes, $.proxy(function(i,node)
					{
						if ($.inArray(node.tagName.toLowerCase(), inlineTags) != -1)
						{
							inlines.push(node);
						}

					}, this));

					return (inlines.length === 0) ? false : inlines;
				},
				getBlocks: function(nodes)
				{
					this.selection.get();

					if (this.range && this.range.collapsed)
					{
						return [this.selection.getBlock()];
					}

					var blocks = [];
					nodes = (typeof nodes == 'undefined') ? this.selection.getNodes() : nodes;
					$.each(nodes, $.proxy(function(i,node)
					{
						// COMPOSER_HACK
						// Test against .ebd classname
						if ($(node).parents('div.ebd').size() == 0) return false;

						if (this.utils.isBlock(node))
						{
							this.selection.lastBlock = node;
							blocks.push(node);
						}

					}, this));

					return (blocks.length === 0) ? [this.selection.getBlock()] : blocks;
				},
				getLastBlock: function()
				{
					return this.selection.lastBlock;
				},
				getNodes: function()
				{
					this.selection.get();

					var startNode = this.selection.getNodesMarker(1);
					var endNode = this.selection.getNodesMarker(2);

					this.selection.setNodesMarker(this.range, startNode, true);

					if (this.range.collapsed === false)
					{
						this.selection.setNodesMarker(this.range, endNode, false);
					}
					else
					{
						endNode = startNode;
					}

					var nodes = [];
					var counter = 0;

					var self = this;
					this.$editor.find('*').each(function()
					{
						if (this == startNode)
						{
							var parent = $(this).parent();
							if (parent.length !== 0 && parent[0].tagName != 'BODY' && self.utils.isRedactorParent(parent[0]))
							{
								nodes.push(parent[0]);
							}

							nodes.push(this);
							counter = 1;
						}
						else
						{
							if (counter > 0)
							{
								nodes.push(this);
								counter = counter + 1;
							}
						}

						if (this == endNode)
						{
							return false;
						}

					});

					var finalNodes = [];
					var len = nodes.length;
					for (var i = 0; i < len; i++)
					{
						if (nodes[i].id != 'nodes-marker-1' && nodes[i].id != 'nodes-marker-2')
						{
							finalNodes.push(nodes[i]);
						}
					}

					this.selection.removeNodesMarkers();

					return finalNodes;

				},
				// COMPOSER_HACK
				// An alternative method to getNodes
				// that doesn't insert node markers.
				getNodesInRange: function(range) {

					function getNextNode(node) {
						if (node.firstChild)
							return node.firstChild;

						while (node) {
							if (node.nextSibling) return node.nextSibling;
							node = node.parentNode;
						}
					}

					var range = range || this.range;

					// Temporary fix
					var startEndNormalized = range.startContainer.childNodes[range.startOffset] || range.endContainer.childNodes[range.endOffset];
					var start = range.startContainer.childNodes[range.startOffset] || range.startContainer;
					var end = range.endContainer.childNodes[range.endOffset] || range.endContainer;
					var commonAncestor = range.commonAncestorContainer;
					var nodes = [];
					var node;

					// walk parent nodes from start to common ancestor
					for (node = start.parentNode; node; node = node.parentNode) {
						nodes.push(node);
						if (node == commonAncestor) break;
					}
					nodes.reverse();

					// walk children and siblings from start until end is found
					for (node = start; node; node = getNextNode(node)) {
						// Temporary fix
						if (startEndNormalized) {
							if (!$(node.parentNode).closest(commonAncestor)[0]) break;
						}
						nodes.push(node);
						if (node == end) break;
					}

					return nodes;
				},
				getNodesMarker: function(num)
				{
					return $('<span id="nodes-marker-' + num + '" class="redactor-nodes-marker" data-verified="redactor">' + this.opts.invisibleSpace + '</span>')[0];
				},
				setNodesMarker: function(range, node, type)
				{
					range = range.cloneRange();

					try {
						range.collapse(type);
						range.insertNode(node);
					}
					catch (e) {}
				},
				removeNodesMarkers: function()
				{
					$(document).find('span.redactor-nodes-marker').remove();
					this.$editor.find('span.redactor-nodes-marker').remove();
				},
				fromPoint: function(start, end)
				{
					this.caret.setOffset(start, end);
				},
				wrap: function(tag)
				{
					this.selection.get();

					if (this.range.collapsed) return false;

					var wrapper = document.createElement(tag);
					wrapper.appendChild(this.range.extractContents());
					this.range.insertNode(wrapper);

					return wrapper;
				},
				selectElement: function(node)
				{
					this.caret.set(node, 0, node, 1);
				},
				selectAll: function()
				{
					this.selection.get();
					this.range.selectNodeContents(this.$editor[0]);
					this.selection.addRange();
				},
				remove: function()
				{
					this.selection.get();
					this.sel.removeAllRanges();
				},
				save: function()
				{
					this.selection.createMarkers();
				},
				createMarkers: function()
				{
					this.selection.get();

					var node1 = this.selection.getMarker(1);

					this.selection.setMarker(this.range, node1, true);

					if (this.range.collapsed === false)
					{
						var node2 = this.selection.getMarker(2);
						this.selection.setMarker(this.range, node2, false);
					}

					this.savedSel = this.$editor.html();
				},
				getMarker: function(num)
				{
					if (typeof num == 'undefined') num = 1;

					return $('<span id="selection-marker-' + num + '" class="redactor-selection-marker"  data-verified="redactor">' + this.opts.invisibleSpace + '</span>')[0];
				},
				getMarkerAsHtml: function(num)
				{
					return this.utils.getOuterHtml(this.selection.getMarker(num));
				},
				setMarker: function(range, node, type)
				{
					range = range.cloneRange();

					try {
						range.collapse(type);
						range.insertNode(node);
					}
					catch (e)
					{
						this.focus.setStart();
					}
				},
				restore: function()
				{
					var node1 = this.$editor.find('span#selection-marker-1');
					var node2 = this.$editor.find('span#selection-marker-2');

					if (node1.length !== 0 && node2.length !== 0)
					{
						this.caret.set(node1, 0, node2, 0);
					}
					else if (node1.length !== 0)
					{
						this.caret.set(node1, 0, node1, 0);
					}
					else
					{
						this.$editor.focus();
					}

					this.selection.removeMarkers();
					this.savedSel = false;

				},
				removeMarkers: function()
				{
					this.$editor.find('span.redactor-selection-marker').remove();
				},
				getText: function()
				{
					this.selection.get();

					return this.sel.toString();
				},
				getHtml: function()
				{
					var html = '';

					this.selection.get();
					if (this.sel.rangeCount)
					{
						var container = document.createElement('div');
						var len = this.sel.rangeCount;
						for (var i = 0; i < len; ++i)
						{
							container.appendChild(this.sel.getRangeAt(i).cloneContents());
						}

						html = container.innerHTML;
					}

					return this.clean.onSync(html);
				}
			};
		},
		shortcuts: function()
		{
			return {
				init: function(e, key)
				{
					// disable browser's hot keys for bold and italic
					if (!this.opts.shortcuts)
					{
						if ((e.ctrlKey || e.metaKey) && (key === 66 || key === 73)) e.preventDefault();
						return false;
					}

					$.each(this.opts.shortcuts, $.proxy(function(str, command)
					{
						var keys = str.split(',');
						var len = keys.length;
						for (var i = 0; i < len; i++)
						{
							if (typeof keys[i] === 'string')
							{
								this.shortcuts.handler(e, $.trim(keys[i]), $.proxy(function()
								{
									var func;
									if (command.func.search(/\./) != '-1')
									{
										func = command.func.split('.');
										if (typeof this[func[0]] != 'undefined')
										{
											this[func[0]][func[1]].apply(this, command.params);
										}
									}
									else
									{
										this[command.func].apply(this, command.params);
									}

								}, this));
							}

						}

					}, this));
				},
				handler: function(e, keys, origHandler)
				{
					// based on https://github.com/jeresig/jquery.hotkeys
					var hotkeysSpecialKeys =
					{
						8: "backspace", 9: "tab", 10: "return", 13: "return", 16: "shift", 17: "ctrl", 18: "alt", 19: "pause",
						20: "capslock", 27: "esc", 32: "space", 33: "pageup", 34: "pagedown", 35: "end", 36: "home",
						37: "left", 38: "up", 39: "right", 40: "down", 45: "insert", 46: "del", 59: ";", 61: "=",
						96: "0", 97: "1", 98: "2", 99: "3", 100: "4", 101: "5", 102: "6", 103: "7",
						104: "8", 105: "9", 106: "*", 107: "+", 109: "-", 110: ".", 111 : "/",
						112: "f1", 113: "f2", 114: "f3", 115: "f4", 116: "f5", 117: "f6", 118: "f7", 119: "f8",
						120: "f9", 121: "f10", 122: "f11", 123: "f12", 144: "numlock", 145: "scroll", 173: "-", 186: ";", 187: "=",
						188: ",", 189: "-", 190: ".", 191: "/", 192: "`", 219: "[", 220: "\\", 221: "]", 222: "'"
					};


					var hotkeysShiftNums =
					{
						"`": "~", "1": "!", "2": "@", "3": "#", "4": "$", "5": "%", "6": "^", "7": "&",
						"8": "*", "9": "(", "0": ")", "-": "_", "=": "+", ";": ": ", "'": "\"", ",": "<",
						".": ">",  "/": "?",  "\\": "|"
					};

					keys = keys.toLowerCase().split(" ");
					var special = hotkeysSpecialKeys[e.keyCode],
						character = String.fromCharCode( e.which ).toLowerCase(),
						modif = "", possible = {};

					$.each([ "alt", "ctrl", "meta", "shift"], function(index, specialKey)
					{
						if (e[specialKey + 'Key'] && special !== specialKey)
						{
							modif += specialKey + '+';
						}
					});


					if (special) possible[modif + special] = true;
					if (character)
					{
						possible[modif + character] = true;
						possible[modif + hotkeysShiftNums[character]] = true;

						// "$" can be triggered as "Shift+4" or "Shift+$" or just "$"
						if (modif === "shift+")
						{
							possible[hotkeysShiftNums[character]] = true;
						}
					}

					for (var i = 0, len = keys.length; i < len; i++)
					{
						if (possible[keys[i]])
						{
							e.preventDefault();
							return origHandler.apply(this, arguments);
						}
					}
				}
			};
		},
		tabifier: function()
		{
			return {
				get: function(code)
				{
					if (!this.opts.tabifier) return code;

					// clean setup
					var ownLine = ['area', 'body', 'head', 'hr', 'i?frame', 'link', 'meta', 'noscript', 'style', 'script', 'table', 'tbody', 'thead', 'tfoot'];
					var contOwnLine = ['li', 'dt', 'dt', 'h[1-6]', 'option', 'script'];
					var newLevel = ['blockquote', 'div', 'dl', 'fieldset', 'form', 'frameset', 'map', 'ol', 'p', 'pre', 'select', 'td', 'th', 'tr', 'ul'];

					this.tabifier.lineBefore = new RegExp('^<(/?' + ownLine.join('|/?' ) + '|' + contOwnLine.join('|') + ')[ >]');
					this.tabifier.lineAfter = new RegExp('^<(br|/?' + ownLine.join('|/?' ) + '|/' + contOwnLine.join('|/') + ')[ >]');
					this.tabifier.newLevel = new RegExp('^</?(' + newLevel.join('|' ) + ')[ >]');

					var i = 0,
					codeLength = code.length,
					point = 0,
					start = null,
					end = null,
					tag = '',
					out = '',
					cont = '';

					this.tabifier.cleanlevel = 0;

					for (; i < codeLength; i++)
					{
						point = i;

						// if no more tags, copy and exit
						if (-1 == code.substr(i).indexOf( '<' ))
						{
							out += code.substr(i);

							return this.tabifier.finish(out);
						}

						// copy verbatim until a tag
						while (point < codeLength && code.charAt(point) != '<')
						{
							point++;
						}

						if (i != point)
						{
							cont = code.substr(i, point - i);
							if (!cont.match(/^\s{2,}$/g))
							{
								if ('\n' == out.charAt(out.length - 1)) out += this.tabifier.getTabs();
								else if ('\n' == cont.charAt(0))
								{
									out += '\n' + this.tabifier.getTabs();
									cont = cont.replace(/^\s+/, '');
								}

								out += cont;
							}

							if (cont.match(/\n/)) out += '\n' + this.tabifier.getTabs();
						}

						start = point;

						// find the end of the tag
						while (point < codeLength && '>' != code.charAt(point))
						{
							point++;
						}

						tag = code.substr(start, point - start);
						i = point;

						var t;

						if ('!--' == tag.substr(1, 3))
						{
							if (!tag.match(/--$/))
							{
								while ('-->' != code.substr(point, 3))
								{
									point++;
								}
								point += 2;
								tag = code.substr(start, point - start);
								i = point;
							}

							if ('\n' != out.charAt(out.length - 1)) out += '\n';

							out += this.tabifier.getTabs();
							out += tag + '>\n';
						}
						else if ('!' == tag[1])
						{
							out = this.tabifier.placeTag(tag + '>', out);
						}
						else if ('?' == tag[1])
						{
							out += tag + '>\n';
						}
						else if (t = tag.match(/^<(script|style|pre)/i))
						{
							t[1] = t[1].toLowerCase();
							tag = this.tabifier.cleanTag(tag);
							out = this.tabifier.placeTag(tag, out);
							end = String(code.substr(i + 1)).toLowerCase().indexOf('</' + t[1]);

							if (end)
							{
								cont = code.substr(i + 1, end);
								i += end;
								out += cont;
							}
						}
						else
						{
							tag = this.tabifier.cleanTag(tag);
							out = this.tabifier.placeTag(tag, out);
						}
					}

					return this.tabifier.finish(out);
				},
				getTabs: function()
				{
					var s = '';
					for ( var j = 0; j < this.tabifier.cleanlevel; j++ )
					{
						s += '\t';
					}

					return s;
				},
				finish: function(code)
				{
					code = code.replace(/\n\s*\n/g, '\n');
					code = code.replace(/^[\s\n]*/, '');
					code = code.replace(/[\s\n]*$/, '');
					code = code.replace(/<script(.*?)>\n<\/script>/gi, '<script$1></script>');

					this.tabifier.cleanlevel = 0;

					return code;
				},
				cleanTag: function (tag)
				{
					var tagout = '';
					tag = tag.replace(/\n/g, ' ');
					tag = tag.replace(/\s{2,}/g, ' ');
					tag = tag.replace(/^\s+|\s+$/g, ' ');

					var suffix = '';
					if (tag.match(/\/$/))
					{
						suffix = '/';
						tag = tag.replace(/\/+$/, '');
					}

					var m;
					while (m = /\s*([^= ]+)(?:=((['"']).*?\3|[^ ]+))?/.exec(tag))
					{
						if (m[2]) tagout += m[1].toLowerCase() + '=' + m[2];
						else if (m[1]) tagout += m[1].toLowerCase();

						tagout += ' ';
						tag = tag.substr(m[0].length);
					}

					return tagout.replace(/\s*$/, '') + suffix + '>';
				},
				placeTag: function (tag, out)
				{
					var nl = tag.match(this.tabifier.newLevel);
					if (tag.match(this.tabifier.lineBefore) || nl)
					{
						out = out.replace(/\s*$/, '');
						out += '\n';
					}

					if (nl && '/' == tag.charAt(1)) this.tabifier.cleanlevel--;
					if ('\n' == out.charAt(out.length - 1)) out += this.tabifier.getTabs();
					if (nl && '/' != tag.charAt(1)) this.tabifier.cleanlevel++;

					out += tag;

					if (tag.match(this.tabifier.lineAfter) || tag.match(this.tabifier.newLevel))
					{
						out = out.replace(/ *$/, '');
						out += '\n';
					}

					return out;
				}
			};
		},
		tidy: function()
		{
			return {
				setupAllowed: function()
				{
					if (this.opts.allowedTags) this.opts.deniedTags = false;
					if (this.opts.allowedAttr) this.opts.removeAttr = false;

					if (this.opts.linebreaks) return;

					var tags = ['p', 'section'];
					if (this.opts.allowedTags) this.tidy.addToAllowed(tags);
					if (this.opts.deniedTags) this.tidy.removeFromDenied(tags);

				},
				addToAllowed: function(tags)
				{
					var len = tags.length;
					for (var i = 0; i < len; i++)
					{
						if ($.inArray(tags[i], this.opts.allowedTags) == -1)
						{
							this.opts.allowedTags.push(tags[i]);
						}
					}
				},
				removeFromDenied: function(tags)
				{
					var len = tags.length;
					for (var i = 0; i < len; i++)
					{
						var pos = $.inArray(tags[i], this.opts.deniedTags);
						if (pos != -1)
						{
							this.opts.deniedTags.splice(pos, 1);
						}
					}
				},
				load: function(html, options)
				{
					this.tidy.settings = {
						deniedTags: this.opts.deniedTags,
						allowedTags: this.opts.allowedTags,
						removeComments: this.opts.removeComments,
						replaceTags: this.opts.replaceTags,
						replaceStyles: this.opts.replaceStyles,
						removeDataAttr: this.opts.removeDataAttr,
						removeAttr: this.opts.removeAttr,
						allowedAttr: this.opts.allowedAttr,
						removeWithoutAttr: this.opts.removeWithoutAttr,
						removeEmpty: this.opts.removeEmpty
					};

					$.extend(this.tidy.settings, options);

					html = this.tidy.removeComments(html);

					// create container
					this.tidy.$div = $('<div />').append(html);

					// clean
					this.tidy.replaceTags();
					this.tidy.replaceStyles();
					this.tidy.removeTags();

					this.tidy.removeAttr();
					this.tidy.removeEmpty();
					this.tidy.removeParagraphsInLists();
					this.tidy.removeDataAttr();
					this.tidy.removeWithoutAttr();

					html = this.tidy.$div.html();
					this.tidy.$div.remove();

					return html;
				},
				removeComments: function(html)
				{
					if (!this.tidy.settings.removeComments) return html;

					return html.replace(/<!--[\s\S]*?-->/gi, '');
				},
				replaceTags: function(html)
				{
					if (!this.tidy.settings.replaceTags) return html;

					var len = this.tidy.settings.replaceTags.length;
					var replacement = [], rTags = [];
					for (var i = 0; i < len; i++)
					{
						rTags.push(this.tidy.settings.replaceTags[i][1]);
						replacement.push(this.tidy.settings.replaceTags[i][0]);
					}

					this.tidy.$div.find(replacement.join(',')).each($.proxy(function(n,s)
					{
						var tag = rTags[n];
						$(s).replaceWith(function()
						{
							var replaced = $('<' + tag + ' />').append($(this).contents());

							for (var i = 0; i < this.attributes.length; i++)
							{
								replaced.attr(this.attributes[i].name, this.attributes[i].value);
							}

							return replaced;
						});

					}, this));

					return html;
				},
				replaceStyles: function()
				{
					if (!this.tidy.settings.replaceStyles) return;

					var len = this.tidy.settings.replaceStyles.length;
					this.tidy.$div.find('span').each($.proxy(function(n,s)
					{
						var $el = $(s);
						var style = $el.attr('style');
						for (var i = 0; i < len; i++)
						{
							if (style && style.match(new RegExp('^' + this.tidy.settings.replaceStyles[i][0], 'i')))
							{
								var tagName = this.tidy.settings.replaceStyles[i][1];
								$el.replaceWith(function()
								{
									var tag = document.createElement(tagName);
									return $(tag).append($(this).contents());
								});
							}
						}

					}, this));

				},
				removeTags: function()
				{
					if (!this.tidy.settings.deniedTags && this.tidy.settings.allowedTags)
					{
						this.tidy.$div.find('*').not(this.tidy.settings.allowedTags.join(',')).each(function(i, s)
						{
							if (s.innerHTML === '') $(s).remove();
							else $(s).contents().unwrap();
						});
					}

					if (this.tidy.settings.deniedTags)
					{
						this.tidy.$div.find(this.tidy.settings.deniedTags.join(',')).each(function(i, s)
						{
							if (s.innerHTML === '') $(s).remove();
							else $(s).contents().unwrap();
						});
					}
				},
				removeAttr: function()
				{
					var len;
					if (!this.tidy.settings.removeAttr && this.tidy.settings.allowedAttr)
					{

						var allowedAttrTags = [], allowedAttrData = [];
						len = this.tidy.settings.allowedAttr.length;
						for (var i = 0; i < len; i++)
						{
							allowedAttrTags.push(this.tidy.settings.allowedAttr[i][0]);
							allowedAttrData.push(this.tidy.settings.allowedAttr[i][1]);
						}


						this.tidy.$div.find('*').each($.proxy(function(n,s)
						{
							var $el = $(s);
							var pos = $.inArray($el[0].tagName.toLowerCase(), allowedAttrTags);
							var attributesRemove = this.tidy.removeAttrGetRemoves(pos, allowedAttrData, $el);

							if (attributesRemove)
							{
								$.each(attributesRemove, function(z,f) {
									$el.removeAttr(f);
								});
							}
						}, this));
					}

					if (this.tidy.settings.removeAttr)
					{
						len = this.tidy.settings.removeAttr.length;
						for (var i = 0; i < len; i++)
						{
							var attrs = this.tidy.settings.removeAttr[i][1];
							if ($.isArray(attrs)) attrs = attrs.join(' ');

							this.tidy.$div.find(this.tidy.settings.removeAttr[i][0]).removeAttr(attrs);
						}
					}

				},
				removeAttrGetRemoves: function(pos, allowed, $el)
				{
					var attributesRemove = [];

					// remove all attrs
					if (pos == -1)
					{
						$.each($el[0].attributes, function(i, item)
						{
							attributesRemove.push(item.name);
						});

					}
					// allow all attrs
					else if (allowed[pos] == '*')
					{
						attributesRemove = [];
					}
					// allow specific attrs
					else
					{
						$.each($el[0].attributes, function(i, item)
						{
							if ($.isArray(allowed[pos]))
							{
								if ($.inArray(item.name, allowed[pos]) == -1)
								{
									attributesRemove.push(item.name);
								}
							}
							else if (allowed[pos] != item.name)
							{
								attributesRemove.push(item.name);
							}

						});
					}

					return attributesRemove;
				},
				removeAttrs: function (el, regex)
				{
					regex = new RegExp(regex, "g");
					return el.each(function()
					{
						var self = $(this);
						var len = this.attributes.length - 1;
						for (var i = len; i >= 0; i--)
						{
							var item = this.attributes[i];
							if (item && item.specified && item.name.search(regex)>=0)
							{
								self.removeAttr(item.name);
							}
						}
					});
				},
				removeEmpty: function()
				{
					if (!this.tidy.settings.removeEmpty) return;

					this.tidy.$div.find(this.tidy.settings.removeEmpty.join(',')).each(function()
					{
						var $el = $(this);
						var text = $el.text();
						text = text.replace(/[\u200B-\u200D\uFEFF]/g, '');
						text = text.replace(/&nbsp;/gi, '');
						text = text.replace(/\s/g, '');

						if (text === '' && $el.children().length === 0)
						{
							$el.remove();
						}
					});
				},
				removeParagraphsInLists: function()
				{
					this.tidy.$div.find('li p').contents().unwrap();
				},
				removeDataAttr: function()
				{
					if (!this.tidy.settings.removeDataAttr) return;

					var tags = this.tidy.settings.removeDataAttr;
					if ($.isArray(this.tidy.settings.removeDataAttr)) tags = this.tidy.settings.removeDataAttr.join(',');

					this.tidy.removeAttrs(this.tidy.$div.find(tags), '^(data-)');

				},
				removeWithoutAttr: function()
				{
					if (!this.tidy.settings.removeWithoutAttr) return;

					this.tidy.$div.find(this.tidy.settings.removeWithoutAttr.join(',')).each(function()
					{
						if (this.attributes.length === 0)
						{
							$(this).contents().unwrap();
						}
					});
				}
			};
		},
		utils: function()
		{
			return {
				isMobile: function()
				{
					return /(iPhone|iPod|BlackBerry|Android)/.test(navigator.userAgent);
				},
				isDesktop: function()
				{
					return !/(iPhone|iPod|iPad|BlackBerry|Android)/.test(navigator.userAgent);
				},
				isString: function(obj)
				{
					return Object.prototype.toString.call(obj) == '[object String]';
				},
				isEmpty: function(html, removeEmptyTags)
				{
					html = html.replace(/[\u200B-\u200D\uFEFF]/g, '');
					html = html.replace(/&nbsp;/gi, '');
					html = html.replace(/<\/?br\s?\/?>/g, '');
					html = html.replace(/\s/g, '');
					html = html.replace(/^<p>[^\W\w\D\d]*?<\/p>$/i, '');
					html = html.replace(/<iframe(.*?[^>])>$/i, 'iframe');

					// remove empty tags
					if (removeEmptyTags !== false)
					{
						html = html.replace(/<[^\/>][^>]*><\/[^>]+>/gi, '');
						html = html.replace(/<[^\/>][^>]*><\/[^>]+>/gi, '');
					}

					html = $.trim(html);

					return html === '';
				},
				normalize: function(str)
				{
					if (typeof(str) === 'undefined') return 0;
					return parseInt(str.replace('px',''), 10);
				},
				hexToRgb: function(hex)
				{
					if (typeof hex == 'undefined') return;
					if (hex.search(/^#/) == -1) return hex;

					var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
					hex = hex.replace(shorthandRegex, function(m, r, g, b)
					{
						return r + r + g + g + b + b;
					});

					var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
					return 'rgb(' + parseInt(result[1], 16) + ', ' + parseInt(result[2], 16) + ', ' + parseInt(result[3], 16) + ')';
				},
				getOuterHtml: function(el)
				{
					return $('<div>').append($(el).eq(0).clone()).html();
				},
				getAlignmentElement: function(el)
				{
					if ($.inArray(el.tagName, this.opts.alignmentTags) !== -1)
					{
						return $(el);
					}
					else
					{
						return $(el).closest(this.opts.alignmentTags.toString().toLowerCase(), this.$editor[0]);
					}
				},
				removeEmptyAttr: function(el, attr)
				{
					var $el = $(el);
					if (typeof $el.attr(attr) == 'undefined')
					{
						return true;
					}

					if ($el.attr(attr) === '')
					{
						$el.removeAttr(attr);
						return true;
					}

					return false;
				},
				removeEmpty: function(i, s)
				{
					var $s = $(s);

					$s.find('.redactor-invisible-space').removeAttr('style').removeAttr('class');

					if ($s.find('hr, br, img, iframe').length !== 0) return;
					var text = $.trim($s.text());
					if (this.utils.isEmpty(text, false))
					{
						$s.remove();
					}
				},

				// save and restore scroll
				saveScroll: function()
				{
					this.saveEditorScroll = this.$editor.scrollTop();
					this.saveBodyScroll = $(window).scrollTop();

					if (this.opts.scrollTarget) this.saveTargetScroll = $(this.opts.scrollTarget).scrollTop();
				},
				restoreScroll: function()
				{
					if (typeof this.saveScroll === 'undefined' && typeof this.saveBodyScroll === 'undefined') return;

					$(window).scrollTop(this.saveBodyScroll);
					this.$editor.scrollTop(this.saveEditorScroll);

					if (this.opts.scrollTarget) $(this.opts.scrollTarget).scrollTop(this.saveTargetScroll);
				},

				// get invisible space element
				createSpaceElement: function()
				{
					var space = document.createElement('span');
					space.className = 'redactor-invisible-space';
					space.innerHTML = this.opts.invisibleSpace;

					return space;
				},

				// replace
				removeInlineTags: function(node)
				{
					var tags = this.opts.inlineTags;
					tags.push('span');

					if (node.tagName == 'PRE') tags.push('a');

					$(node).find(tags.join(',')).not('span.redactor-selection-marker').contents().unwrap();
				},
				replaceWithContents: function(node, removeInlineTags)
				{
					var self = this;
					$(node).replaceWith(function()
					{
						if (removeInlineTags === true) self.utils.removeInlineTags(this);

						return $(this).contents();
					});
				},
				replaceToTag: function(node, tag, removeInlineTags)
				{
					var replacement;
					var self = this;
					$(node).replaceWith(function()
					{
						replacement = $('<' + tag + ' />').append($(this).contents());

						for (var i = 0; i < this.attributes.length; i++)
						{
							replacement.attr(this.attributes[i].name, this.attributes[i].value);
						}

						if (removeInlineTags === true) self.utils.removeInlineTags(replacement);

						return replacement;
					});

					return replacement;
				},

				// start and end of element
				isStartOfElement: function()
				{
					var block = this.selection.getBlock();
					if (!block) return false;

					var offset = this.caret.getOffsetOfElement(block);

					return (offset === 0) ? true : false;
				},
				isEndOfElement: function()
				{
					var block = this.selection.getBlock();
					if (!block) return false;

					var offset = this.caret.getOffsetOfElement(block);

					var text = $.trim($(block).text()).replace(/\n\r\n/g, '');

					return (offset == text.length) ? true : false;
				},

				// test blocks
				isBlock: function(block)
				{
					block = block[0] || block;

					return block && this.utils.isBlockTag(block.tagName);
				},
				isBlockTag: function(tag)
				{
					if (typeof tag == 'undefined') return false;

					return this.reIsBlock.test(tag);
				},

				// tag detection
				isTag: function(current, tag)
				{
					var element = $(current).closest(tag);
					if (element.size() == 1)
					{
						return element[0];
					}

					return false;
				},
				// parents detection
				isRedactorParent: function(el)
				{
					if (!el)
					{
						return false;
					}

					// COMPOSER_HACK
					// Test against .ebd classname
					// if ($(el).parents('.redactor-editor').length === 0 || $(el).hasClass('redactor-editor'))
					if ($(el).parents('.ebd').length === 0 || $(el).hasClass('ebd'))
					{
						return false;
					}

					return el;
				},
				isCurrentOrParentHeader: function()
				{
					return this.utils.isCurrentOrParent(['H1', 'H2', 'H3', 'H4', 'H5', 'H6']);
				},
				isCurrentOrParent: function(tagName)
				{
					var parent = this.selection.getParent();
					var current = this.selection.getCurrent();

					if ($.isArray(tagName))
					{
						var matched = 0;
						$.each(tagName, $.proxy(function(i, s)
						{
							if (this.utils.isCurrentOrParentOne(current, parent, s))
							{
								matched++;
							}
						}, this));

						return (matched === 0) ? false : true;
					}
					else
					{
						return this.utils.isCurrentOrParentOne(current, parent, tagName);
					}
				},
				isCurrentOrParentOne: function(current, parent, tagName)
				{
					tagName = tagName.toUpperCase();

					return parent && parent.tagName === tagName ? parent : current && current.tagName === tagName ? current : false;
				},


				// browsers detection
				isOldIe: function()
				{
					return (this.utils.browser('msie') && parseInt(this.utils.browser('version'), 10) < 9) ? true : false;
				},
				isLessIe10: function()
				{
					return (this.utils.browser('msie') && parseInt(this.utils.browser('version'), 10) < 10) ? true : false;
				},
				isIe11: function()
				{
					return !!navigator.userAgent.match(/Trident\/7\./);
				},
				browser: function(browser)
				{
					var ua = navigator.userAgent.toLowerCase();
					var match = /(opr)[\/]([\w.]+)/.exec( ua ) ||
					/(chrome)[ \/]([\w.]+)/.exec( ua ) ||
					/(webkit)[ \/]([\w.]+).*(safari)[ \/]([\w.]+)/.exec(ua) ||
					/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
					/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
					/(msie) ([\w.]+)/.exec( ua ) ||
					ua.indexOf("trident") >= 0 && /(rv)(?::| )([\w.]+)/.exec( ua ) ||
					ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
					[];

					if (browser == 'version') return match[2];
					if (browser == 'webkit') return (match[1] == 'chrome' || match[1] == 'webkit');
					if (match[1] == 'rv') return browser == 'msie';
					if (match[1] == 'opr') return browser == 'webkit';

					return browser == match[1];
				},


				// COMPOSER_HACK
				// Get editable container of a given node.
				getEditableContainer: function(node) {
					var container = $(node).parentsUntil(this.$editor).andSelf().closest("[contenteditable=true]")[0];
					return this.utils.isRedactorParent(container);
				},

				// COMPOSER_HACK
				// Method to check if a node is editable.
				isEditableContent: function(node) {
					return !!this.utils.getEditableContainer(node);
				},

				// COMPOSER_HACK
				// Return the supported content type of the editable container
				getEditableContentType: function(node) {
					var container = this.utils.getEditableContainer(node);
					var type = $(container).attr('data-content-type');

					if (type == 'html') {
						return 'html';
					}

					if (type == 'text') {
						return 'text';
					}

					return 'text';
				}
			};
		}
	};

	// constructor
	Redactor.prototype.init.prototype = Redactor.prototype;

module.resolve();

});