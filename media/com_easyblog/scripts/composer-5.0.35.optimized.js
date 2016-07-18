FD50.installer("EasyBlog", "definitions", function($){
$.module(["easyblog/easyblog","easyblog/layout/template","easyblog/layout/responsive","easyblog/layout/dialog","easyblog/layout/elements","easyblog/layout/launcher","easyblog/layout/placeholder","easyblog/layout/image/popup","easyblog/layout/image/gallery","easyblog/layout/image/legacy","easyblog/subscribe","easyblog/composer/document/artboard","easyblog/composer/blocks/dimensions","easyblog/composer/blocks/droppable","easyblog/composer/blocks/font","easyblog/composer/blocks/guide","easyblog/composer/blocks/handlers/audio","easyblog/composer/blocks/handlers/behance","easyblog/composer/blocks/handlers/buttons","easyblog/composer/blocks/handlers/code","easyblog/composer/blocks/handlers/codepen","easyblog/composer/blocks/handlers/columns","easyblog/composer/blocks/handlers/compare","easyblog/composer/blocks/handlers/file","easyblog/composer/blocks/handlers/gist","easyblog/composer/blocks/handlers/heading","easyblog/composer/blocks/handlers/html","easyblog/composer/blocks/handlers/image","easyblog/composer/blocks/handlers/instagram","easyblog/composer/blocks/handlers/links","easyblog/composer/blocks/handlers/list","easyblog/composer/blocks/handlers/note","easyblog/composer/blocks/handlers/post","easyblog/composer/blocks/handlers/quotes","easyblog/composer/blocks/handlers/readmore","easyblog/composer/blocks/handlers/rule","easyblog/composer/blocks/handlers/slideshare","easyblog/composer/blocks/handlers/soundcloud","easyblog/composer/blocks/handlers/spotify","easyblog/composer/blocks/handlers/table","easyblog/composer/blocks/handlers/tabs","easyblog/composer/blocks/handlers/text","easyblog/composer/blocks/handlers/tweet","easyblog/composer/blocks/handlers/video","easyblog/composer/blocks/mobile","easyblog/composer/blocks/nestable","easyblog/composer/blocks/panel","easyblog/composer/blocks/removal","easyblog/composer/blocks/resizable","easyblog/composer/blocks/scrollable","easyblog/composer/blocks/search","easyblog/composer/blocks/text","easyblog/composer/blocks/tree","easyblog/composer/blocks","easyblog/composer/blocks/media","easyblog/composer/blocks/toolbar","easyblog/","easyblog/composer/blogimage","easyblog/composer/category","easyblog/composer/datetime","easyblog/composer/debugger","easyblog/composer/document/overlay","easyblog/composer/document","easyblog/composer/document/toolbar","easyblog/composer/redactor10","easyblog/composer/location","easyblog/composer/manager","easyblog/composer/media","easyblog/mediamanager","easyblog/mediamanager/uploader","easyblog/composer/panels/authorship","easyblog/composer/panels/autopost","easyblog/composer/panels/fields","easyblog/composer/panels/post","easyblog/composer/panels/seo","easyblog/composer/panels","easyblog/composer/posts","easyblog/composer/redactor","easyblog/composer/tags","easyblog/composer/templates","easyblog/composer","easyblog/composer/revisions","easyblog/composer/panels/category","easyblog/layout/image/caption","easyblog/mediamanager/audio","easyblog/mediamanager/image","easyblog/mediamanager/video"]);
$.require.template.loader(["easyblog/site/composer/primary.category.item"]);
});
FD50.installer("EasyBlog", "scripts", function($){
EasyBlog.require()
	.library(
		"ui/position"
	)
	.script(
		"layout/template",
		"layout/responsive",
		"layout/dialog",
		"layout/elements",
		"layout/launcher",
		"layout/placeholder",
		"layout/image/popup",
		"layout/image/gallery",
		"layout/image/legacy",
		"subscribe"
	)
	.done();
EasyBlog.module('layout/template', function($) {

    var module = this;

    var self = EasyBlog.template = function(name) {

        if (!name) return;

        if (self.cache.hasOwnProperty(name)) {
            return self.cache[name];
        }

        var templateSelector = '.eb-template[data-name="' + name + '"]';
        var template = $.trim($(templateSelector).detach().html());

        if (template) {
            self.cache[name] = template;
        }

        return template;
    }

    self.cache = {};

    module.resolve();

});

EasyBlog.module('layout/responsive', function($) {

    var module = this;

    $(function(){

        $.responsive('.eb-responsive', [
        {
            "at": 1200,
            "switchTo": "wide"
        },
        {
            "at": 960,
            "switchTo": "wide w960"
        },
        {
            "at": 818,
            "switchTo": "wide w960 w768"
        },
        {
            "at": 600,
            "switchTo": "wide w960 w768 w600"
        },
        {
            "at": 560,
            "switchTo": "wide w960 w768 w600 w480"
        },
        {
            "at": 480,
            "switchTo": "wide w960 w768 w600 w480 w320"
        }
        ]);
    });


    //
    // New data-responsive API
    // <div class="myelement" data-responsive="800,600,400,300"></div>

    // Okay, look like some 3rd party template provider is also using data-responsive their their 'responsive' feature.
    // We need to have more specify selector for EB's blocks related data-resposive.
    var responsiveElement_ = "#fd.eb [data-responsive]";

    var setResponsiveLayout = function() {

        $(responsiveElement_).each(function(){

            var responsiveElement = $(this);

            var elementWidth = responsiveElement.outerWidth();
            var widths = responsiveElement.data("responsive").split(",");
            var width;
            var classnamesToDiscard = []
            var classnamesToUse = [];

            while (width = widths.shift()) {
                (elementWidth <= width ? classnamesToUse : classnamesToDiscard).push("w" + width);
            }

            responsiveElement
                .removeClass(classnamesToDiscard.join(" "))
                .addClass(classnamesToUse.join(" "));
        });
    }

    // Set responsive layout on document ready
    $(document).ready(setResponsiveLayout);

    // Set responsive layout on window load and resize
    $(window)
        .on("load.responsive", setResponsiveLayout)
        .on("resize.responsive_", $.debounce(setResponsiveLayout, 350));


    module.resolve();

});

EasyBlog.module("layout/dialog", function($) {

var module = this;

// var dialogHtml = EasyBlog.template("site/layout/dialog/default");
var dialogHtml = '<div id="fd" class="eb eb-dialog has-footer"><div class="eb-dialog-modal"><div class="eb-dialog-header"><div class="row-table"><div class="col-cell cell-ellipse"><span class="eb-dialog-title"></span></div><div class="col-cell cell-tight eb-dialog-close-button"><i class="fa fa-close"></i></div></div></div><div class="eb-dialog-body"><div class="eb-dialog-container"><div class="eb-dialog-content"></div><div class="eb-hint hint-loading layout-overlay style-gray size-sm"><div><i class="eb-hint-icon"><span class="eb-loader-o size-lg"></span></i></div></div><div class="eb-hint hint-failed layout-overlay style-gray size-sm"><div><i class="eb-hint-icon fa fa-warning"></i><span class="eb-hint-text"><span class="eb-dialog-error-message"></span></span></div></div></div></div><div class="eb-dialog-footer"><div class="row-table"><div class="col-cell eb-dialog-footer-content"></div></div></div></div></div>';
var dialog_ = ".eb-dialog";
var dialogModal_ = ".eb-dialog-modal";
var dialogContent_ = ".eb-dialog-content";
var dialogHeader_ = ".eb-dialog-header";
var dialogFooter_ = ".eb-dialog-footer";
var dialogFooterContent_ = ".eb-dialog-footer-content";
var dialogCloseButton_ = ".eb-dialog-close-button";
var dialogTitle_ = ".eb-dialog-title";
var dialogErrorMessage_ = ".eb-dialog-error-message";

var isFailed = "is-failed";
var isLoading = "is-loading";
var rxBraces = /\{|\}/gi;

var self = EasyBlog.dialog = function(options) {

    // For places calling EasyBlog.dialog().close();
    if (options===undefined) return self;

    // Normalize options
    if ($.isString(options)) {
        options = {content: options};
    }

    var method = self.open;

    // When dialog is loaded via iframe
    if (window.parentEasyBlogDialog) {
        method = window.parentEasyBlogDialog.open;
    }

    method.apply(self, [options]);

    return self;
}

$.extend(self, {

    defaultOptions: {
        title: "",
        content: "",
        buttons: "",
        classname: "",
        width: "auto",
        height: "auto",
        escapeKey: true
    },

    open: function(options) {

        // Get dialog
        var dialog = $(dialog_);
        if (dialog.length < 1) {
            dialog = $(dialogHtml).appendTo("body");
        }

        // Normalize options
        var options = $.extend({}, self.defaultOptions, options);

        // Set title
        var dialogTitle = $(dialogTitle_);
        dialogTitle.text(options.title);

        // Set buttons
        var dialogFooterContent = $(dialogFooterContent_);
        dialogFooterContent.html(options.buttons);
        dialog.toggleClass("has-footer", !!options.buttons)

        // Set bindings
        self.setBindings(options);

        // Set content
        var dialogContent = $(dialogContent_).empty();
        var content = options.content;
        var contentType = self.getContentType(content);
        dialog.switchClass("type-" + contentType)

        // Set width & height
        var dialogModal = $(dialogModal_);
        var dialogWidth = options.width;
        var dialogHeight = options.height;

        if ($.isNumeric(dialogHeight)) {
            var dialogHeader = $(dialogHeader_);
            var dialogFooter = $(dialogFooter_);
            dialogHeight += dialogHeader.height() + dialogFooter.height();
        }

        dialogModal.css({
            width: dialogWidth,
            height: dialogHeight
        });

        dialog.addClassAfter("active");

        // HTML
        switch (contentType) {

            case "html":
                dialogContent.html(content);
                break;

            case "iframe":
                var iframe = $("<iframe>");
                var iframeUrl = content;
                iframe
                    .appendTo(dialogContent)
                    .one("load", function(){
                        // Expose dialog object to iframe
                        // Inside a try catch because does not work on cross-site domain,
                        // and url checking takes a lot more code to write.
                        try { iframe[0].contentWindow.parentEasyBlogDialog = self; } catch(err) {};
                    })
                    .attr("src", iframeUrl);
                break;

            case "deferred":
                dialog.switchClass(isLoading);
                content
                    .done(function(content) {

                        // Options
                        if ($.isPlainObject(content)) {
                            self.reopen($.extend(true, options, content));
                        // Content
                        } else if ($.isString(content)) {
                            options.content = content;
                            self.reopen(options);
                        // Unknown
                        } else {
                            dialog.switchClass(isFailed);
                        }
                    })
                    .fail(function(exception){

                        dialog.switchClass(isFailed);

                        var dialogErrorMessage = $(dialogErrorMessage_);

                        // Error message
                        if ($.isString(exception)) {
                            dialogErrorMessage.html(exception);
                        }

                        // Exception object
                        if ($.isPlainObject(exception) && exception.message) {
                            dialogErrorMessage.html(exception.message);
                        }
                    });
                return;
                break;

            case "dialog":
                var xmlOptions = self.parseXMLOptions(content);
                self.open($.extend(true, options, xmlOptions));
                return;
                break;
        }
    },

    reopen: function(options) {
        self.close();
        self.open(options);
    },

    close: function() {

        // Unset bindings
        self.unsetBindings();

        // Remove dialog
        var dialog = $(dialog_);
        dialog.remove();
    },

    getContentType: function(content) {

        if (/<dialog>(.*?)/.test(content)) {
            return "dialog";
        }

        if ($.isUrl(content)) {
            return "iframe";
        }

        if ($.isDeferred(content)) {
            return "deferred";
        }

        return "html";
    },

    parseXMLOptions: function(xml) {

        var xmlOptions = $.buildHTML(xml);
        var newOptions = {};

        $.each(xmlOptions.children(), function(i, node){

            var node = $(node);
            var key  = $.String.camelize(this.nodeName.toLowerCase());
            var val  = node.html();
            var type = node.attr("type");

            switch (type) {
                case "json":
                    try {
                        val = $.parseJSON(val);
                    } catch(e) {};
                    break;

                case "javascript":
                    try {
                        val = eval('(function($){ return ' + $.trim(val) + ' })(' + $.globalNamespace + ')');
                    } catch(e) {};
                    break;

                case "text":
                    val = node.text();
                    break;
            }

            // Automatically convert numerical values
            if ($.isNumeric(val)) val = parseFloat(val);

            newOptions[key] = val;
        });

        return newOptions;
    },

    bindings: {},

    setBindings: function(options) {

        // Remove previous bindings
        self.unsetBindings();

        // Create new bindings
        var selectors = options.selectors;
        var bindings  = options.bindings;

        if (selectors && bindings) {

            // Simulate a controller instance
            var controller = {parent: self};
            var dialog = $(dialog_);

            $.each(selectors, function(element, selector){

                var element = element.replace(rxBraces, "");

                // Create selector fn
                var selectorFn = controller[element] = function() {
                    return dialog.find(selector);
                };
                selectorFn.selector = selector;
            });

            $.each(bindings, function(binder, eventHandler){

                // Get element and event name
                var parts = binder.split(" ");
                var element = parts[0].replace(rxBraces, "");
                var eventName = parts[1] + ".eb.dialog";

                // Get selector fn
                var selectorFn = controller[element];

                // No binding if selector fn is not found
                if (!selectorFn) return;

                // Bind event handler
                var selector = selectorFn.selector;
                dialog.on(eventName, selector, function(){
                    eventHandler.apply(controller, [this].concat(arguments));
                });

                // Add to bindings
                self.bindings[eventName] = eventHandler;
            });
        }

        if (options.escapeKey) {
            $(document).on("keydown.eb.dialog", function(event){
                if (event.keyCode==27) {
                    self.close();
                }
            });
        }
    },

    unsetBindings: function() {

        // Get dialog
        var dialog = $(dialog_);

        // Unbind bindings
        $.each(self.bindings, function(eventName, eventHandler){
            dialog.off(eventName);
        });

        // Unbind escape
        $(document).off("keydown.eb.dialog");
    }
});

$(document)
    .on("click", dialogCloseButton_, function(){
        self.close();
    })
    .on("click", dialog_, function(event){
        var dialog = $(dialog_);
        if (event.target==dialog[0]) {
            self.close();
        }
    })

module.resolve();

});

EasyBlog.module('layout/elements', function($){

	var module = this;

	// Initialize yes/no buttons.
	$(document).on('click.button.data-bp-api', '[data-bp-toggle-value]', function() {

		var button = $(this);
		var siblings = button.siblings("[data-bp-toggle-value]");
		var parent = button.parents('[data-bp-toggle="radio-buttons"]');

		if(parent.hasClass('disabled')) {
			return;
		}

		// This means that this toggle value belongs to a radio button
		if (parent.length > 0) {

			// Get the current button that's clicked.
			var value = button.data('bp-toggle-value');

			// Set the value here.
			// Have to manually trigger the change event on the input
			parent.find('input[type=hidden]').val(value).trigger('change');
			return;
		}
	});

	// Listen to change event on radio button group input
	$(document).on('change.data-bp-input', '[data-bp-toggle="radio-buttons"] input[type=hidden]', function() {
		var input = $(this);
		var siblings = input.siblings("[data-bp-toggle-value]");
		var value = input.val();

		siblings
			.removeClass('active')
			.filter('[data-bp-toggle-value="' + input.val() + '"]')
			.addClass('active');
	});


	// Tooltips
	// TODO: Update to [data-eb-provide=tooltip]
	$(document).on('mouseover.tooltip.data-eb-api', '[data-eb-provide=tooltip]', function() {

		$(this)
			.tooltip({
				delay: {
					show: 200,
					hide: 100
				},
				animation: false,
				template: '<div id="fd" class="eb tooltip tooltip-eb"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
				container: 'body'
			})
			.tooltip("show");
	});

	// Popovers
	// TODO: Update to [data-eb-provide=popover]
	$(document).on('mouseover.popover.data-eb-api', '[data-eb-provide=popover]', function() {
		$(this)
			.popover({
				delay: {
					show: 200,
					hide: 100
				},
				animation: false,
				trigger: 'hover',
				container: 'body'
			})
			.popover("show");
	});


	var ly = function(yr) { return (yr%400)?((yr%100)?((yr%4)?false:true):false):true; };

	$(document).on("keyup", "[data-date-form] [data-date-day]", function(){

		if (!$.trim($(this).val())) return;

		var year   = parseInt($(this).siblings("[data-date-year]").val()  || $(this).siblings("[data-date-year]").data("dateDefault")),

		    month  = parseInt($(this).siblings("[data-date-month]").val() || $(this).siblings("[data-date-month]").data("dateDefault")),

		    day    = parseInt($(this).val() || $(this).data("dateDefault")),

			maxDay = /1|3|5|7|8|10|12/.test(month) ? 31 : 30;

			if (month==2) maxDay = ly(year) ? 29 : 28;

			if (day < 1) day = 1;

			if (day > maxDay) day = maxDay;

			if ($.isNumeric(day)) {
				$(this).val(day);
			} else {
				$(this).val("");
			}
	});

	$(document).on("keyup", "[data-date-form] [data-date-year]", function(){

		if (!$.trim($(this).val())) return;

		var year = parseInt($(this).val());
		if (year < 1) year = 1;

		if ($.isNumeric(year)) {
			$(this).val(year);
		} else {
			$(this).val("");
		}
	});

	$.fn.listbox = function(options) {
		var elements = this;

		if ($.isPlainObject(options) || options === undefined) {
			elements.each(function() {
				var container = $(this),
					listbox = Listbox.get(container);

				if (listbox) {
					listbox.update(options);
				} else {
					listbox = new Listbox(container, options);
				}
			});

			return this;
		}

		if ($.isString(options)) {
			var container = $(this[0]),
				listbox = Listbox.get(container) || new Listbox(container),
				method = listbox[options],
				ret;

			if ($.isFunction(method)) {
				ret = method.apply(listbox, $.makeArray(arguments).slice(1));
			}

			return ret || this;
		}

		return this;
	};

	$.fn.listboxitem = function(options) {
		var elements = this;

		if (options === undefined) {
			elements.each(function() {
				var item = $(this);

				Listbox.Item.init(item);
			});

			return this;
		}

		if ($.isString(options)) {
			var item = $(this[0]);

			Listbox.Item.init(item);

			var listboxitem = item.data('listboxitem'),
				method = listboxitem[options],
				ret;

			if ($.isFunction(method)) {
				ret = method.apply(listboxitem, $.makeArray(arguments).slice(1));
			}

			return ret || this;
		}

		return this;
	};

	var Listbox = function(container, options, overrides) {
		var listbox = this,
			elementOptions = {};

		listbox.container = container;

		container.data("listbox", listbox);

		$.each(Listbox.defaultOptions, function(key, value) {
			var val = container.attr("data-listbox-" + key);

			if (val !== undefined) {
				elementOptions[key] = isNaN(parseInt(val)) ? val : parseInt(val);
			}
		});

		overrides && listbox.extend(overrides);

		listbox.update($.extend(true,
			{},
			Listbox.defaultOptions,
			elementOptions,
			options
		));

		listbox.init();
	};

	Listbox.defaultOptions = {
		toggleDefault: true,
		sortable: true,
		allowAdd: true,
		allowRemove: true,
		max: 0,
		min: 1,

		customHTML: '',
		itemTitle: 'Title',
	};

	Listbox.get = function(el) {

		var listbox = $(el).data("listbox");

		if (listbox instanceof Listbox) return listbox;
	};

	$.extend(Listbox.prototype, {
		options: {},

		addButton: function() {
			return this.container.find('[data-listbox-button-add]');
		},

		update: function(options) {
			var listbox = this;

			$.extend(true, listbox.options, options);
		},

		extend: function(overrides) {
			$.each(overrides, function(key, val) {
				if ($.isFunction(val)) {
					Listbox.prototype[key] = val;
				}
			});
		},

		getItems: function(filter) {
			var listbox = this,
				items = this.container.find('[data-listbox-item]');

			if (filter) {
				items = items.filter(filter);
			}

			Listbox.Item.init(items);

			return items;
		},

		getParentItem: function(el) {
			var item = $(el).closest('[data-listbox-item]');

			Listbox.Item.init(item);

			return item;
		},

		init: function() {
			var listbox = this,
				container = listbox.container,
				customHTMLBlock = container.find('[data-listbox-custom-html]');

			listbox.options.customHTML = customHTMLBlock.html();

			customHTMLBlock.remove();

			if (listbox.options.toggleDefault) {
				container.on('click.listbox.toggleDefault', '[data-listbox-button-default]', function() {
					return listbox.toggleDefault(this);
				});
			}

			if (listbox.options.allowAdd) {
				container.on('click.listbox.addItem', '[data-listbox-button-add]', function() {
					return listbox.add(this);
				});
			}

			if (listbox.options.allowRemove) {
				container.on('click.listbox.removeItem', '[data-listbox-button-remove]', function() {
					return listbox.remove(this);
				});
			}

			if (listbox.options.sortable) {
				this.initSortable();
			}

			!listbox.options.customHTML && this.getItems().find('[data-listbox-item-content]').editable(true);
		},

		initSortable: function() {

		},

		toggleDefault: function(el) {
			var items = this.getItems(),
				thisItem = this.getParentItem(el);

			if (thisItem.length == 0) {
				return;
			}

			this.container.trigger('listboxBeforeToggleDefault', [thisItem]);

			if (!thisItem) {
				return;
			}

			items.removeClass('is-default');
			thisItem.addClass('is-default');

			this.container.trigger('listboxAfterToggleDefault', [thisItem]);
		},

		isDefault: function(el) {
			return el.hasClass('is-default');
		},

		add: function() {

			var items = this.getItems();

			if (this.options.max > 0 && items.length >= this.options.max) {
				return;
			}

			var item = Listbox.Item.newItem(this.options);

			this.container.trigger('listboxBeforeAddItem', [item]);

			if (!item) {
				return;
			}

			this.addButton().before(item);
			this.container.trigger('listboxAfterAddItem', [item]);

			if (this.getItems('.is-default').length == 0) {
				this.toggleDefault(this.getItems(':first'));
			}
		},

		remove: function(el) {
			var items = this.getItems();

			if (this.options.min > 0 && items.length <= this.options.min) {
				return;
			}

			var item = this.getParentItem(el);

			this.container.trigger('listboxBeforeRemoveItem', [item]);

			if (!item) {
				return;
			}

			item.remove();

			this.container.trigger('listboxAfterRemoveItem', [item]);

			this.isDefault(item) && this.toggleDefault(this.getItems(':first'));
		},

		populate: function(items, itemHandler) {
			var listbox = this;

			listbox.getItems().remove();

			$.each(items, function(i, item) {
				var newItem = Listbox.Item.newItem(listbox.options);

				if ($.isFunction(itemHandler)) {
					itemHandler(newItem, item.content);
				};

				if (item.default) {
					newItem.addClass('is-default');
				}

				listbox.container.trigger('listboxBeforePopulateItem', [item]);

				listbox.addButton().before(newItem);

				listbox.container.trigger('listboxAfterPopulateItem', [item]);
			});
		},

		toData: function() {
			var data = [];

			$.each(this.getItems(), function(i, item) {
				data.push($(item).listboxitem('toData'));
			});

			return data;
		}
	});

	Listbox.Item = function(item) {
		this.item = item;
		this.parent = item.parents('[data-listbox]').data('listbox');
	};

	Listbox.Item.get = function(item) {
		var instance = $(item).data('listboxitem');

		if (instance instanceof Listbox.Item) return instance;
	};

	Listbox.Item.init = function(items) {
		items = $(items);

		$.each(items, function(i, item) {
			item = $(item);

			if (!Listbox.Item.get(item)) {
				item.data('listboxitem', new Listbox.Item(item));
			}
		});

		return items;
	};

	Listbox.Item.newItem = function(options) {
		var item = $('<div></div>', {
				'data-listbox-item': '',
				'class': 'eb-composer-manage-tab row-table'
			}),
			handler = $('<div></div>', {
				'class': 'col-cell eb-composer-manage-tab-handler'
			});

		if (options.sortable) {
			handler.append('<i class="fa fa-bars"></i>');
		}

		if (options.toggleDefault) {
			handler.append(' <i class="fa fa-star" data-listbox-button-default></i>');
		}

		item.append(handler);

		var content = $('<div></div>', {
			'data-listbox-item-content': '',
			'class': 'col-cell eb-composer-manage-tab-name'
		}).html(options.customHTML ? $.buildHTML(options.customHTML) : options.itemTitle);

		!options.customHTML && content.editable(true);

		item.append(content);

		if (options.allowRemove) {
			item.append($('<div></div>', {
				'data-listbox-button-remove': '',
				'class': 'col-cell eb-composer-manage-tab-remove'
			}).html('&times;'));
		}

		Listbox.Item.init(item);

		return item;
	};

	$.extend(Listbox.Item.prototype, {
		isDefault: function() {
			return this.item.hasClass('is-default');
		},

		content: function(html) {
			var content = this.item.find('[data-listbox-item-content]');

			if (html !== undefined) {
				content.html(html);
				return this;
			}

			return content.html();
		},

		remove: function() {
			this.parent.remove(this.item);
		},

		setDefault: function() {
			this.parent.toggleDefault(this.item);
		},

		toData: function() {
			var item = this;

			return {
				'default': item.isDefault() ? 1 : 0,
				'content': item.content()
			}
		}
	});

	// UI
	EasyBlog.UI = function(window) {

		var document = window.document;

		// Tabs
		$(document).on('click.ebtabs', '.eb-tabs-menu-item', function() {

			var menuItem = $(this),

				// Globals
				container    = menuItem.closest(".eb-tabs"),
				menu         = container.find("> .eb-tabs-menu"),
				menuItems    = menu.find("> .eb-tabs-menu-item"),
				content      = container.find("> .eb-tabs-content"),
				contentItems = content.find("> .eb-tabs-content-item");

				// Deactivate active id
				// var activeId = menuItems.filter(".active").data("id");

				// Toggle mode
				if (container.data("eb-tabs-mode")=="toggle" && menuItem.hasClass("active")) {

					menuItems.removeClass("active");
					contentItems.removeClass("active");

				// Expand mode
				} else {

					menuItems.removeClass("active");
					menuItem.addClass("active");

					contentItems
						.removeClass("active")
						.where("id", menuItem.data("id"))
						.addClass("active");
				}

				container[contentItems.filter(".active").length > 0 ? "addClassAfter" : "removeClassAfter"]("is-open", 1);
		});
	};

	// Initialize UI on this window
	EasyBlog.UI(window);

	module.resolve();
});

EasyBlog.module("layout/launcher", function($){

var module = this;

// var launcherHtml = EasyBlog.template("site/layout/composer/launcher");
var launcherHtml = '<div id="fd" class="eb eb-composer-launcher is-loading" data-eb-composer-launcher><div class="eb-composer-launcher-header"><div class="eb-composer-launcher-close-button" data-eb-composer-launcher-close-button><i class="fa fa-close"></i></div></div><div class="eb-composer-launcher-container" data-eb-composer-launcher-container><div class="eb-loader-o size-lg"></div></div></div>';

var iframeHtml = '<iframe class="eb-composer-launcher-instance" data-eb-composer-launcher-instance />';

var launcher_ = "[data-eb-composer-launcher]";
var launcherButton_ = "[data-eb-composer]";
var launcherCloseButton_ = "[data-eb-composer-launcher-close-button]";
var launcherContainer_ = "[data-eb-composer-launcher-container]";
var launcherInstance_ = "[data-eb-composer-launcher-instance]";

var self = EasyBlog.ComposerLauncher = {

	open: function(url) {

		// Destroy existing instance
		self.close();
		$("body").noscroll(true);

		var launcher = $(launcherHtml);
		var launcherContainer = launcher.find(launcherContainer_);

		var launcherInstance = $(iframeHtml)
									.attr("src", url)
									.one("load", self.ready)
									.appendTo(launcherContainer);

		// Append launcher to body
		launcher
			.appendTo("body")
			.addClassAfter("active");
	},

	close: function() {
		$(launcher_).remove();
		$("body").noscroll(false);
	},

	ready: function() {
		$(launcher_).removeClass("is-loading");
	},

	redirect: function(url) {
		// self.close();
		parent.window.location = url;
	}
};


$(document).on('composerSaveError', function(event, exception) {

});

$(document).on('composerSaveSuccess', function(event, data) {

});


$(document)
	.on("click", launcherButton_, function(event){

		// If user holds shift/ctrl/cmd key when clicking on the button,
		// opens composer in a new page instead.
		if (event.shiftKey || event.ctrlKey || event.metaKey || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
			return;
		}

		var button = $(this);
		var url = button.attr("href");
		self.open(url);
		event.preventDefault();
	})
	.on("click", launcherCloseButton_, function(){

		EasyBlog.dialog({
			"content": EasyBlog.ajax('site/views/composer/confirmClose'),
			"bindings": {
				"{cancelButton} click": function() {
					EasyBlog.dialog.close();
				},
				
				"{closeButton} click": function() {
					EasyBlog.dialog.close();
					self.close();
				},
			}
		});
	})

	module.resolve();

});




EasyBlog.module("layout/placeholder", function($) {

var module = this;

if ($.IE <= 9) {

    var inputWithPlaceholder = "textarea[placeholder]:not(.placeholder), :input[placeholder]:not(.placeholder)";

    EasyBlog.require()
        .library("placeholder")
        .done(function(){

            EasyBlog.fixPlaceholder = function(){
                $(inputWithPlaceholder).placeholder();
            };

            // Initialize placeholder on all input with placeholder
            $(EasyBlog.fixPlaceholder);

            // For input/textarea with placeholder that are rendered later,
            // initialize placeholder on focus
            $(document).on("mouseover", inputWithPlaceholder, function(){
                $(this).placeholder();
            });
        });
}

module.resolve();

});

EasyBlog.module("layout/image/popup", function($){

var module = this;

// Image templates
var imagePopupHtml = EasyBlog.template("site/layout/image/popup");
var imagePopupThumbHtml = EasyBlog.template("site/layout/image/popup/thumb");
var imageContainerHtml = EasyBlog.template("site/layout/image/container");

// Image popup selectors
var imagePopup_ = ".eb-image-popup";
var imagePopupButton_ = ".eb-image-popup-button";
var imagePopupCloseButton_ = ".eb-image-popup-close-button";
var imagePopupContainer_ = ".eb-image-popup-container";
var imagePopupFooter_ = ".eb-image-popup-footer";
var imagePopupThumbs_ = ".eb-image-popup-thumbs";
var imagePopupThumb_ = ".eb-image-popup-thumb";

// Image container selectors
var imageContainer_ = ".eb-image";
var imageViewport_ = ".eb-image-viewport";
var imageCaptionText_ = ".eb-image-caption > span";

// Thumbnail selectors
var thumbContainer_ = ".eb-thumbs";

var escapeToCloseEvent = "keyup.eb.imagepopup";
var clickToCloseEvent = "click.eb.imagepopup";
var windowResizeEvent = "resize.eb.imagepopup";
var keyNavigationEvent = "keydown.eb.imagepopup";

var self = EasyBlog.ImagePopup = {

	open: function() {

		// Destroy existing instance
		self.close();
		$("body").noscroll(true);

		$(window)
			.off(escapeToCloseEvent)
			.on(escapeToCloseEvent, function(event){

				// Escape
				if (event.which==27) {
					self.close();
				}
			})
			// Close when clicking outside of the image popup
			.off(clickToCloseEvent)
			.on(clickToCloseEvent, function(event){

				var imageContainer =
					$(event.target)
						.parentsUntil(imagePopup_)
						.andSelf()
						.filter(imageContainer_);

				if (!imageContainer.length) {
					self.close();
				}
			})
			.off(windowResizeEvent)
			.on(windowResizeEvent, function(event){
				self.refresh();
			})
			.off(keyNavigationEvent)
			.on(keyNavigationEvent, function(event){

				// If there are no popup thumbnails, stop.
				if (!$(imagePopupThumb_).length) return;

				var keyCode = event.which;

				// Don't do anything if it's not up down left right
				if (!/37|38|39|40/.test(keyCode)) return;

				var activeImagePopupThumb = $(imagePopupThumb_ + ".active");
				var nextImagePopupThumb;

				// up, left
				if (/37|38/.test(keyCode)) {
					nextImagePopupThumb = activeImagePopupThumb.prev(imagePopupThumb_);
				}

				// down, right
				if (/39|40/.test(keyCode)) {
					nextImagePopupThumb = activeImagePopupThumb.next(imagePopupThumb_);
				}

				if (nextImagePopupThumb.length) {
					self.openPopupThumb(nextImagePopupThumb);
				}

				event.preventDefault();
			});

		// Create image popup
		var imagePopup = $(imagePopupHtml);
		imagePopup.appendTo("body");
	},

	close: function() {

		$(window)
			.off(escapeToCloseEvent)
			.off(clickToCloseEvent)
			.off(windowResizeEvent)
			.off(keyNavigationEvent);

		$(imagePopup_)
			.data("destoyed", true)
			.remove();

		$("body").noscroll(false);
	},

	openThumbnails: function(thumbContainer, startingImageContainer) {

		// Open popup
		self.open();

		// Get image popup thumbs
		var imagePopupFooter = $(imagePopupFooter_);
		var imagePopupThumbs = $(imagePopupThumbs_);
		var imagePopupThumbsWidth = 0;
		var startingImagePopupThumb;

		// Show footer
		imagePopupFooter.show();

		// Generate thumbnails
		thumbContainer.find(imageContainer_)
			.each(function(){

				var imageContainer = $(this);
				var imageElement = imageContainer.find("img");
				var imagePopupButton = imageContainer.find(imagePopupButton_);
				var imageUrl = imageElement.attr("src");
				var imagePopupUrl = imagePopupButton.attr("href");

				var imagePopupThumb =
					$(imagePopupThumbHtml)
						.attr("data-url", imagePopupUrl)
						.find("img")
						.attr("src", imageUrl)
						.end()
						.appendTo(imagePopupThumbs);

				// Sum up thumb width
				imagePopupThumbsWidth += imagePopupThumb.outerWidth(true);

				// If this image is the starting image, remember it.
				if (imageContainer[0]==startingImageContainer[0]) {
					startingImagePopupThumb = imagePopupThumb;
				}
			});

		// Set thumbs width
		imagePopupThumbs.css("width", imagePopupThumbsWidth);

		// Open thumbnail
		self.openPopupThumb(startingImagePopupThumb);
	},

	openImage: function(imageContainer) {

		// Open popup
		self.open();

		var imageCaptionText = imageContainer.find(imageCaptionText_);
		var imagePopupButton = imageContainer.find(imagePopupButton_);

		// Get image url & caption
		var url = imagePopupButton.attr("href");
		var captionText = imageCaptionText.text();

		// If there is no text, get the title of the button
		if (!captionText) {
			captionText = imagePopupButton.attr('title');
		}

		// Show image
		self.showImage(url, captionText);
	},

	openPopupThumb: function(imagePopupThumb) {

		var url = imagePopupThumb.attr("data-url");

		// Toggle active class
		$(imagePopupThumb_).removeClass("active");
		imagePopupThumb.addClass("active");

		self.showImage(url, "");

		// Reposition thumbnails
		self._reposition();
	},

	showImage: function(url, captionText) {

		var imagePopup = $(imagePopup_);
		var imagePopupContainer = imagePopup.find(imagePopupContainer_);

		// Show loading indicator
		imagePopup.addClass("is-loading");

		// Remove existing image container
		imagePopup.find(imageContainer_).remove();

		// Create image container
		var imageContainer = $(imageContainerHtml);
		var imageViewport = imageContainer.find(imageViewport_);

		// Append image caption
		var imageCaptionText = imageContainer.find(imageCaptionText_);
		imageCaptionText
			.text(captionText)
			.css("display", !!captionText ? "block" : "none");

		// Append image container to image popup container
		imageContainer
			.addClass("style-popup")
			.appendTo(imagePopupContainer);

		// Create image element
		var imageElement = $("<img>");

		imageElement
			.on("load", function(){

				if (imagePopup.data("destroyed")) return;

				imagePopup
					.removeClass("is-loading")
					.addClass("is-preparing");

				self.resize();

				imagePopup.removeClassAfter("is-preparing");
			})
			.on("error", function(){

				imagePopup
					.removeClass("is-loading")
					.addClass("is-failed");
			})
			.appendTo(imageViewport)
			.attr("src", url);
	},

	refresh: function() {

		self.resize();
		self.reposition();
	},

	resize: function() {

		// Get image popup & container
		var imagePopup = $(imagePopup_);
		var imagePopupFooter = $(imagePopupFooter_);
		var imageContainer = imagePopup.find(imageContainer_);
		var imageElement = imageContainer.find("img");

		// Get dimensions
		var footerHeight = imagePopupFooter.height();
		var sourceWidth = imageElement.width();
		var sourceHeight = imageElement.height();
		var popupWidth = imagePopup.width();
		var popupHeight = imagePopup.height();
		var maxWidth = popupWidth * 0.75;
		var maxHeight = (popupHeight * 0.75) - footerHeight;

		// Resize the width first
		var ratio        = maxWidth / sourceWidth;
			targetWidth  = sourceWidth  * ratio;
			targetHeight = sourceHeight * ratio;

		// inner resize (default)
		var condition = targetHeight > maxHeight;

		if (condition) {
			ratio        = maxHeight / sourceHeight;
			targetWidth  = sourceWidth  * ratio;
			targetHeight = sourceHeight * ratio;
		}

		imageElement
			.css({
				width : targetWidth,
				height: targetHeight
			});

		var containerWidth = imageContainer.width();
		var containerHeight = imageContainer.height() - footerHeight;

		imageContainer
			.css({
				top: ((popupHeight - containerHeight) / 2) - footerHeight,
				left: (popupWidth - containerWidth) / 2
			});
	},

	reposition: function() {

		var imagePopupFooter = $(imagePopupFooter_);
		var imagePopupThumbs = $(imagePopupThumbs_)
		var activeImagePopupThumb = $(imagePopupThumb_ + ".active");

		var midPoint = imagePopupFooter.width() / 2;
		var thumbMidPoint = activeImagePopupThumb.position().left + (activeImagePopupThumb.width() / 2);
		var thumbsLeft = midPoint - thumbMidPoint;

		imagePopupThumbs.css("left", thumbsLeft);
	},

	_reposition: $.debounce(function() {
		self.reposition();
	}, 350)
};

$(document)
	.on("click", imageViewport_, function(event){

		var imageViewport = $(this);
		var url = imageViewport.attr("href");

		if (url!=="javascript:void(0)") return;

		// If there is no link but there is a popup button,
		// simulate clicking on the sibling popup button.
		imageViewport.siblings(imagePopupButton_).click();
	})
	.on("click", imagePopupButton_, function(event){

		// If user holds shift/ctrl/cmd key when clicking on the button,
		// open image in a new page.
		if (event.shiftKey || event.ctrlKey || event.metaKey) {
			return;
		}

		// Get image popup button, image container and image caption.
		var imagePopupButton = $(this);
		var imageContainer = imagePopupButton.closest(imageContainer_);

		// Thumbnails
		var thumbContainer = imageContainer.closest(thumbContainer_);
		if (thumbContainer.length) {
			self.openThumbnails(thumbContainer, imageContainer);

		// Single image
		} else {
			self.openImage(imageContainer);
		};

		event.stopPropagation();
		event.preventDefault();
	})
	.on("click", imageViewport_, function(event){

		var imageViewport = $(this);

		// If image viewport has no link,
		// but there is an image popup,
		// then show popup.
		if (!imageViewport.attr("href")) {

			var imageContainer = imageViewport.closest(imageContainer_);
			var imagePopupButton = imageContainer.find(imagePopupButton_);

			if (imagePopupButton.length) {
				imagePopupButton.click();
				event.stopPropagation();
				event.preventDefault();
			}
		}
	})
	.on("click", imagePopupThumb_, function(event){

		var imagePopupThumb = $(this);

		self.openPopupThumb(imagePopupThumb);

		event.stopPropagation();
		event.preventDefault();
	})
	.on("click", imagePopupCloseButton_, function(){

		// Close popup
		self.close();
	});

module.resolve();

});
EasyBlog.module("layout/image/gallery", function($){

var module = this;

// Gallery selectors
var galleryContainer_ = ".eb-gallery";
var galleryViewport_ = ".eb-gallery-viewport";
var galleryItem_ = ".eb-gallery-item";
var galleryStage_ = ".eb-gallery-stage";
var galleryNextButton_ = ".eb-gallery-next-button";
var galleryPrevButton_ = ".eb-gallery-prev-button";
var galleryButton_ = ".eb-gallery-button";
var galleryMenu_ = ".eb-gallery-menu";
var galleryMenuItem_ = ".eb-gallery-menu-item";

var self = EasyBlog.ImageGallery = {

	setLayout: function(galleryContainer) {

		// Get a list of items in the gallery
		var galleryItems = galleryContainer.find(galleryItem_);

		// Apply position to every gallery items
		galleryItems.each(function(i){
			var galleryItem = $(this);
			var left = 100 * i;
			galleryItem.css("left", left + "%");
		});

		// Determines if there's auto rotate
		var autoplay = galleryContainer.data('autoplay');

		if (autoplay) {
			this.autoplay.start(galleryContainer);
		}
	},

	autoplay: {
		start: function(galleryContainer) {
			var interval = galleryContainer.data('interval') * 1000;

			// Stop any existing autoplay first
			self.autoplay.stop(galleryContainer);

			var timerId = setTimeout(function() {

				self.next(galleryContainer);

				// Restart the autoplay again.
				self.autoplay.start(galleryContainer);
			}, interval);

			galleryContainer.data('timer', timerId);
		},

		stop: function(galleryContainer) {
			var timerId = galleryContainer.data('timer');

			clearTimeout(timerId);
		}
	},

	checkAutoplay: function(galleryContainer) {

		var interval = galleryContainer.data('interval') * 1000;

		// Clear the timer first
		this.stopMonitoringAutoplay(galleryContainer);

		setTimeout(function(){
			self.next(galleryContainer);

			self.startMonitoringAutoplay(galleryContainer);
		}, interval);
	},

	go: function(galleryContainer, index) {

		// If index exceeds max index, cycle back to 0.
		var maxIndex = self.getMenuItems(galleryContainer).length - 1;

		if (index < 0) index = maxIndex;
		if (index > maxIndex) index = 0;

		self.setActiveIndex(galleryContainer, index);

		var galleryViewport = galleryContainer.find(galleryViewport_);
		var left = 100 * -1 * index;
		galleryViewport.css("left", left + "%");
	},

	next: function(galleryContainer) {

		var activeIndex = self.getActiveIndex(galleryContainer);
		var nextIndex = activeIndex + 1;
		self.go(galleryContainer, nextIndex);
	},

	prev: function(galleryContainer) {

		var activeIndex = self.getActiveIndex(galleryContainer);
		var prevIndex = activeIndex - 1;
		self.go(galleryContainer, prevIndex);
	},

	setActiveIndex: function(galleryContainer, index) {

		var galleryMenuItems = self.getMenuItems(galleryContainer);
		galleryMenuItems
			.removeClass("active")
			.eq(index)
			.addClass("active");
	},

	getActiveIndex: function(galleryContainer) {
		var galleryMenuItems = self.getMenuItems(galleryContainer);
		var activeIndex = galleryMenuItems.filter(".active").index();
		if (activeIndex < 0) activeIndex = 0;
		return activeIndex;
	},

	getMenuItems: function(galleryContainer) {
		 return galleryContainer.find(galleryMenuItem_);
	}
};

$(document)
	.on("click.eb.gallery.button", galleryButton_, function(event){
		var galleryButton = $(this);
		var galleryContainer = galleryButton.closest(galleryContainer_);

		// If no gallery container found, stop.
		if (galleryContainer.length < 1) return;

		var direction = galleryButton.is(galleryNextButton_) ? "next" : "prev";

		self[direction](galleryContainer);
	})
	.on("click.eb.gallery.menuItem", galleryMenuItem_, function(event){

		var galleryMenuItem = $(this);
		var galleryContainer = galleryMenuItem.closest(galleryContainer_);

		// If no gallery container found, stop.
		if (galleryContainer.length < 1) return;

		// Get index from menu item
		var index =
			galleryContainer
				.find(galleryMenuItem_)
				.index(galleryMenuItem);

		// Go to gallery item
		self.go(galleryContainer, index);
	})
	.ready(function(event){

		$(galleryContainer_).each(function(){
			var galleryContainer = $(this);
			self.setLayout(galleryContainer);
		})
		// TODO: Autoplay on document ready?
	});

module.resolve();

});
EasyBlog.module("layout/image/legacy", function($){

var module = this;

function getImageAlignment(image) {

	var imageLink = image.parent();

	// Try image link float value
	var alignment = imageLink.css("float");

	// Try image float value
	if (alignment=="none") {
		alignment = image.css("float");
	}

	// Try image center value
	if (alignment=="none") {
		var imageStyle = image[0].style;
		if (imageStyle.marginLeft=="auto" && imageStyle.marginRight=="auto") {
			alignment = "center";
		}
	}

	// Try image align attribute
	if (alignment=="none") {

		// Try image align attribute from image link
		alignment = imageLink.attr("align");

		// Try image align attribute from image
		if (alignment===undefined || alignment=="none") {
			alignment  = image.attr("align");
		}
	}

	// If by now we could not get the alignment, use center alignment
	if (/none|middle/.test(alignment)) {
		alignment = "center";
	}

	return alignment;
}

// <a class="easyblog-thumb-preview" title="link_title_here" href="url_to_large_image"><img class="easyblog-image-caption" title="image_caption_text" alt="image_alt_text" src="url_to_thumb_image"></a>

var legacyImages = ".easyblog-thumb-preview img, img.easyblog-image-caption, img[data-popup], img[data-style]";

$(document).ready(function(){

	// Convert legacy image with popup to .eb-image
	$(legacyImages).each(function(){

		var image = $(this);
		var imageLink = image.parent();
		var imageUrl = image.attr("src");

		// Image Container
		var imageContainer = $('<div class="eb-image">');

		// Image Figure
		var imageFigure = $('<div class="eb-image-figure">');
		imageFigure.appendTo(imageContainer);

		// Image Link
		var imageViewport = $('<a class="eb-image-viewport"><img /></a>');
		imageViewport.appendTo(imageFigure);

		// Image Popup
		var hasOldPopup = imageLink.is(".easyblog-thumb-preview");
		var hasNewPopup = !!image.attr("data-popup");
		var hasPopup = hasOldPopup || hasNewPopup;
		var hasLink = imageLink.is("a:not(.easyblog-thumb-preview)");
		
		if (hasPopup) {

			// var imagePopup = $('<a class="eb-image-popup-button" target="_blank"><i class="fa fa-search"></i></a>');
			var popupUrl = hasOldPopup ? imageLink.attr("href") : image.attr("data-popup");
			imageViewport
				.addClass("eb-image-popup-button")
				.attr({
					href: popupUrl,
					title: imageLink.attr("title")
				});

		} else {
			
			imageViewport
				.attr({
					href: imageLink.attr("href"),
					title: imageLink.attr("title"),
					target: imageLink.attr("target")
				});
		}

		// Image Element
		var imageElement = imageContainer.find("img");
		imageElement
			.attr({
				src: imageUrl,
				width: image.attr("width"),
				height: image.attr("height"),
				alt: image.attr("alt")
			});

		// Image Caption
		var hasCaption = image.is(".easyblog-image-caption");

		if (hasCaption) {

			var imageCaption = $('<div class="eb-image-caption"><span></span></div>');
			var captionText = image.attr("title");

			imageCaption
				.appendTo(imageContainer)
				.find("span")
				.append(captionText);

			// If image width is readily available
			var imageWidth = image.attr("width");

			if (imageWidth) {

				// Set image width directly
				imageCaption.css("width", imageWidth);

			// If image width is not available
			} else {

				// Hide image caption first
				imageCaption.hide();

				// When image is loaded
				imageElement.on("load", function(){

					// Get image element width,
					// apply on image caption,
					// then show image caption.
					imageCaption
						.css("width", imageElement.width())
						.show();
				});
			}
		}

		// Image Style
		var imageStyle = image.attr("data-style") || (hasCaption ? "gray" : "");
		if (imageStyle) {
			imageContainer.addClass("style-" + imageStyle);
		}

		// Image alignment
		var imageAlignment = getImageAlignment(image);
		var blockContainer = $('<div class="ebd-block" data-type="image">');

		if (/left|right/.test(imageAlignment)) {
			blockContainer
				.addClass("is-nested nest-" + imageAlignment)
				.css("width", "auto");
		}

		if (/center/.test(imageAlignment)) {
			blockContainer
				.css("text-align", "center");
		}
		
		// Set image source
		blockContainer.append(imageContainer);
		
		// Replace old image with new image html
		if (hasPopup && !hasNewPopup) {
			imageLink.replaceWith(blockContainer);
		} else {
			image.replaceWith(blockContainer);
		}


	});

});


module.resolve();

});
EasyBlog.module('subscribe', function($){

	var module = this;

	$(document).on('click.eb.subscribe', '[data-blog-subscribe]', function() {

		var type = $(this).data('type');
		var id = $(this).data('id');

		EasyBlog.dialog({
			content: EasyBlog.ajax('site/views/subscription/form', {"type": type, "id": id})
		});
	});

	$(document).on('click.eb.unsubscribe', '[data-blog-unsubscribe]', function() {

		// Get the subscription id
		var id = $(this).data('subscription-id');
		var redirect = $(this).data('return');


		console.log(id, redirect);

		// Ask for confirmation
		EasyBlog.dialog({
			content: EasyBlog.ajax('site/views/subscription/confirmUnsubscribe', {
				"id": id,
				"return": redirect
			}),
			bindings: {
				"{submitButton} click": function() {
					this.form().submit();
				}
			}
		})
	});

	module.resolve();

});

EasyBlog.module("composer/document/artboard", function($){

var module = this;

EasyBlog.Controller("Composer.Document.Artboard",
{
    defaultOptions: {
        "{container}": ".eb-composer-artboard",
        "{viewport}": ".eb-composer-artboard-viewport",
        "{art}": "[data-eb-composer-art]",
        "{metaButton}": "[data-eb-composer-meta-button]"
    }
},
function(self, opts, base) { return {

    init: function() {
    },

    current: function() {
        return self.art(".active").data("id");
    },

    show: function(id) {

        // Get art from given id or current id
        var id = id || self.current();

        // Activate container
        self.container()
            .switchClass("show-" + id)
            .switchClass("state-expand")
            .addClassAfter("active", 1);

        // Activate art
        self.art()
            .removeClass("active")
            .where("id", id)
            .addClass("active");

        // Activate meta button
        self.metaButton()
            .removeClass("active")
            .where("id", id)
            .addClass("active");

        self.trigger("composerArtboardShow", [id]);
    },

    hide: function(id) {

        // Get art from given id or current id
        var id = id || self.current();

        // Deactivate container
        self.container()
            .toggleClass("active", !!self.art(".has-image").length)
            .switchClass("state-collapse");

        // Deactivate art
        self.art()
            .removeClass("active");

        // Activate meta button
        self.metaButton()
            .removeClass("active");

        self.trigger("composerArtboardHide", [id]);
    },

    "{metaButton} click": function(metaButton) {
        var id = metaButton.data("id");
        self[self.current()===id ? "hide" : "show"](id);
    }

}});

module.resolve();

});

EasyBlog.module("composer/blocks/dimensions", function($){

var module = this;

var parseUnit = function(val) {
    return val.toString().match("%") ? "%" : "px";
};

EasyBlog.require()
.library(
    "nouislider"
)
.done(function(){

EasyBlog.Controller("Composer.Blocks.Dimensions",
{
    elements: [
        "[data-eb-block-dimensions-{field}]",
        "[data-eb-block-dimensions-field-container] [data-eb-{numslider-toggle|numslider-widget|numslider-value|numslider-input|numslider-units|numslider-unit|numslider-current-unit}]",
    ],

    defaultOptions: $.extend({}, EBD.selectors)
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = blocks.composer;
        currentBlock = $();
    },

    "{self} composerBlockActivate": function(base, event, block) {
        currentBlock = block;
        self.populate(block);
    },

    isResizableNestedBlock: function(block) {
        if (!blocks.isNestedBlock(block)) return false;
        return blocks.getBlockNestType(block)=="content";
    },

    allowDimensionsFieldset: function(block) {
        return self.isResizableNestedBlock(block) &&
            self.getDimensionsSettings(block).enabled;
    },

    getDimensionsSettings: function(block) {
        return blocks.getBlockMeta(block).dimensions;
    },

    getDimensionsFieldset: function() {
        return composer.panels.fieldset.get("dimensions");
    },

    shouldRespectMinContentSize: function(block) {
        return self.isResizableNestedBlock(block) &&
            self.getDimensionsSettings(block).respectMinContentSize;
    },

    isInherited: function(value) {
        return !value || /auto|inherit/.test(value);
    },

    hasInheritedSize: function(block, prop) {
        var value = block[0].style[prop];
        return self.isInherited(value);
    },

    hasInheritedWidth: function(block) {
        return self.hasInheritedSize(block, "width");
    },

    hasInheritedHeight: function(block) {
        return self.hasInheritedSize(block, "height");
    },

    getSize: function(block, prop) {
        var value = block[0].style[prop];
        return self.isInherited(value) ? block.css(prop) : value;
    },

    getWidth: function(block) {
        return self.getSize(block, "width");
    },

    getHeight: function(block) {
        return self.getSize(block, "height");
    },

    getComputedWidth: function(block) {
        return block.width();
    },

    getComputedHeight: function(block) {

        // var blockViewport = blocks.getBlockViewport(block);
        // value =
        //     (block.height() -
        //     parseInt(blockViewport.css("padding-top")) -
        //     parseInt(blockViewport.css("padding-bottom"))) + "px";

        return block.height();
    },

    setWidth: function(block, width) {
        block.css("width", width);
    },

    setHeight: function(block, height) {
        block.css("height", height);
    },

    updateWidth: function(block, width) {
        self.setWidth(block, width);
        self.populateWidth(block);
    },

    updateHeight: function(block, height) {
        self.setHeight(block, height);
        self.populateHeight(block);
    },

    getFluidWidth: function(block) {
        // Secondary fallback is for isolated blocks (experimental)
        var nest = $(blocks.getBlockNest(block)[0] || block.parent()[0]);
        var width = Math.round(block.width() / nest.width() * 100) + "%";
        return width;
    },

    toFluidWidth: function(block) {

        // Only for nested block
        if (blocks.isRootBlock(block)) return;

        var width = self.getFluidWidth(block);
        self.updateWidth(block, width);
    },

    toFluidHeight: function(block) {
        self.updateHeight(block, "");
    },

    toFixedWidth: function(block, width) {
        self.updateWidth(block, block.width());
    },

    toFixedHeight: function(block, height) {
        self.updateHeight(block, block.height());
    },

    toAutoWidth: function(block) {
        self.updateWidth(block, "auto");
    },

    toAutoHeight: function(block) {
        self.updateHeight(block, "");
    },

    setUnit: function(block, prop, toUnit) {

        // Only applies to width
        if (prop!=="width") return;

        var fromUnit = self.getUnit(block, prop);

        // % to px
        if (fromUnit=="%" && toUnit=="px") {
            self.toFixedWidth(block);
        }

        // px to %
        if (fromUnit=="px" && toUnit=="%") {
            self.toFluidWidth(block);
        }
    },

    getUnit: function(block, prop) {
        return parseUnit(self.getSize(block, prop));
    },

    updateUnit: function(block, prop, unit) {

        self.setUnit(block, prop, unit);

        prop=="width"  && self.populateWidth(block);
        prop=="height" && self.populateHeight(block);
    },

    populate: function(block) {

        // Dimensions fieldset
        var dimensionsFieldset = self.getDimensionsFieldset();

        // Determine dimensions fieldset if allowed or not
        var allowDimensionsFieldset = self.allowDimensionsFieldset(block);

        // Show or hide dimensions fieldset
        dimensionsFieldset.toggle(allowDimensionsFieldset);

        // If dimensions fieldset is allowed, populate width & height.
        if (allowDimensionsFieldset) {
            self.populateWidth(block);
            self.populateHeight(block);
        }
    },

    populateWidth: function(block) {
        var width = self.getWidth(block);
        var checked = !self.hasInheritedWidth(block);
        self.populateField("width", width, checked);
    },

    populateHeight: function(block) {
        var height = self.getHeight(block);
        var checked = !self.hasInheritedHeight(block);
        self.populateField("height", height, checked);
    },

    populateField: function(prop, value, checked) {

        // Get field, number, unit
        var field = self.field().where("name", prop);
        var number = parseFloat(value);
        var unit = parseUnit(value);

        // Field toggle
        self.numsliderToggle.inside(field)
            .prop("checked", checked);

        // Numslider input
        self.numsliderInput.inside(field)
            .data("number", number)
            .val(number);

        // Numslider current unit
        self.numsliderCurrentUnit.inside(field)
            .html(unit);

        // Numslider unit dropdown
        self.numsliderUnit.inside(field)
            .where("unit", '"' + unit + '"')
            .activateClass("active");

        // Store unit data in field
        field.data("unit", unit);

        // Numslider widget
        if (self.resizingFromSlider!==prop) {

            // Pixel unit
            if (unit=="px") {
                var unitOptions = {
                    start: number,
                    step: 1,
                    range: {
                        min: 0,
                        max: 800
                    },
                    pips: {
                        mode: "values",
                        density: 4,
                        values: [0, 200, 400, 600, 800, 1000]
                    }
                }
            }

            // Percent unit
            if (unit=="%") {
                var unitOptions = {
                    start: number,
                    step: 1,
                    range: {
                        min: 0,
                        max: 100
                    },
                    pips: {
                        mode: "positions",
                        values: [0, 20, 40, 60, 80, 100],
                        density: 5
                    }
                }
            }

            self.numsliderWidget.inside(field)
                .find(".noUi-pips")
                    .remove()
                    .end()
                .noUiSlider(unitOptions, true)
                .noUiSlider_pips(unitOptions.pips);
        }
    },

    handleNumsliderWidget: function(numsliderWidget, number) {

        var workarea = self.workarea();

        var field = self.field.of(numsliderWidget);
        var prop  = field.data("name");
        var unit  = field.data("unit");
        var value = Math.round(number) + unit;

        // Declare that we are resizing the slider of this property
        self.resizingFromSlider = prop;

        // Disable transition so width/height changes is instantaneous
        clearTimeout(self.resizingTimer);
        workarea.addClass("is-resizing");

        prop=="width"  && self.updateWidth(currentBlock, value);
        prop=="height" && self.updateHeight(currentBlock, value);

        self.resizingTimer = setTimeout(function(){
            workarea.removeClass("is-resizing");
        }, 15);

        self.resizingFromSlider = null;
    },

    "{numsliderWidget} nouislide": function(numsliderWidget, event, number) {

        self.handleNumsliderWidget(numsliderWidget, number);
    },

    "{numsliderWidget} set": function(numsliderWidget, event, number) {

        self.handleNumsliderWidget(numsliderWidget, number);
    },

    "{numsliderInput} input": function(numsliderInput) {

        // Destroy any blur event handler
        numsliderInput.off("blur.numslider");

        function revertOnBlur(lastValidNumber) {
            numsliderInput
                .on("blur.numslider", function(){
                    numsliderInput.val(lastValidNumber);
                });
        }

        // Get prop
        var field = self.field.of(numsliderInput);
        var prop  = field.data("name");

        // Get number
        var number = $.trim(numsliderInput.val());

        // Get unit
        var numsliderUnit = self.numsliderUnit.inside(field);
        var unit = numsliderUnit.data("unit");

        // Get value
        var value = number + unit;

        if (number==0 || !$.isNumeric(number)) {
            var lastValidNumber = numsliderInput.data("number");
            return revertOnBlur(lastValidNumber);
        }

        prop=="width"  && self.updateWidth(currentBlock, value);
        prop=="height" && self.updateHeight(currentBlock, value);
    },

    "{numsliderUnit} click": function(numsliderUnit) {

        var field = self.field.of(numsliderUnit);
        var prop  = field.data("name");
        var unit  = numsliderUnit.data("unit");

        self.setUnit(currentBlock, prop, unit);
    },

    "{numsliderToggle} change": function(numsliderToggle) {

        var field = self.field.of(numsliderToggle);
        var prop  = field.data("name");

        // If we're disable font size, remove font size.
        if (!numsliderToggle.is(":checked")) {
            prop=="width"  && self.toAutoWidth(currentBlock);
            prop=="height" && self.toAutoHeight(currentBlock);
        }
    },

    // When a block is resized using resizable
    "{self} composerBlockResize": function(base, event, block) {

        self.populate(block);
    },

    // When a nested block is converted into a root block
    "{self} composerBlockNestOut": function(base, event, block) {

        // Remove width, height from block
        block.css({
            width: "",
            height: ""
        });
    }

}});

module.resolve();

});

})
EasyBlog.module("composer/blocks/droppable", function($) {

    var module = this,
        isNested = "is-nested",
        isReceiving = "is-receiving";

    EasyBlog.require()
    .library(
        "ui/draggable",
        "ui/droppable"
    )
    .done(function(){

        EasyBlog.Controller("Composer.Blocks.Droppable", {
            defaultOptions: $.extend({

                "{dropzones}": "[data-ebd-dropzone]",
                "{dropzonePlaceholder}": "[data-ebd-dropzone-placeholder]",
                "{mediaFile}": "[data-eb-mm-file]"

            }, EBD.selectors),
        }, function(self, opts, base, composer, blocks, currentBlock) { return {

            init: function() {
                blocks = self.blocks;
                composer = blocks.composer;
                currentBlock = $();

                // Detach from DOM, replace method.
                self.dropzonePlaceholder =
                    self.dropzonePlaceholder()
                        .detach()
                        .removeClass("hide")[0];
            },

            "{self} composerReady": function() {

                // Generate draggable options
                self.draggableOptions = {

                    connectWith: EBD.dropzone,

                    // Helper
                    helper: "clone",
                    appendTo: composer.document.ghosts(),

                    // Placeholder
                    placeholder: "ebd-block is-placeholder",

                    // Behaviour
                    refreshPositions: true,

                    // Handle
                    handle: EBD.immediateBlockSortHandle
                };

                // Generate droppable options
                self.droppableOptions = {

                    // Block & media manager file
                    accept: [EBD.block, ".eb-mm-file"].join(","),

                    // Behaviour
                    tolerance: "pointer"
                };

                // Init droppable
                self.enable();
            },

            enable: function() {

                // Get all blocks and implement draggable
                blocks.getAllBlocks()
                    .draggable(self.draggableOptions);
            },

            disable: function() {

                // Get blocks with draggable and destroy draggable
                self.block(".ui-draggable")
                    .draggable("destroy");
            },

            selectedBlock: null,

            "{self} composerBlockMove": function(base, event, block) {
                self.selectedBlock = block;
            },

            "{self} composerBlockMenuSelected": function(base, event, menu) {
                self.selectedBlock = menu;
            },

            "{self} composerBlockInit": function(base, event, block) {
                block.draggable(self.draggableOptions);
            },

            populateDropzones: function() {

                // Populate dropzones within blocks that supports nested
                self.nest()
                    .each(function() {

                        var nest = $(this),
                            type = nest.data('type');

                        // Top/down nesting
                        if (type=="block") {

                            // First dropzone
                            nest.prepend(self.createNestedDropzone());

                            // Subsequent dropzones
                            nest.find(EBD.childBlock)
                                .each(function() {
                                    var block = $(this);
                                    block.after(self.createNestedDropzone());
                                });
                        }

                        // Left/right nesting
                        if (type=="content") {

                            // Get available positions of content nest
                            var positions = blocks.nestable.availablePositions(nest);

                            $.each(positions, function(i, position){

                                nest.prepend(self.createNestedDropzone(position));
                            });
                        }
                    });

                // Subsequent root dropzones
                var rootBlocks = blocks.getRootBlocks();

                rootBlocks
                    .each(function(i){

                        var block = $(this);

                        // First dropzone
                        if (i==0) {
                            block.before(self.createDropzone());
                        }

                        // Skip block being sorted
                        if (block.hasClass("is-sort-item")) return;

                        // Subsequent dropzones
                        block.after(self.createDropzone());
                    });

                // If this document is empty, create one dropzone
                if (rootBlocks.length < 1) {

                    self.root()
                        .append(self.createDropzone());
                }

                // Implement droppable on dropzones
                self.dropzone()
                    .droppable(self.droppableOptions);
            },

            createDropzone: function() {

                return $(self.dropzonePlaceholder).clone();
            },

            createNestedDropzone: function(position) {

                var dropzone =
                    self.createDropzone()
                        .addClass("is-nested");

                if (position) {

                    dropzone
                        .addClass("nest-" + position)
                        .data("position", position);
                }

                return dropzone;
            },

            destroyDropzones: function() {
                self.dropzone().remove();
            },

            // If we're dragging an existing block
            "{block} dragstart": function(block, event, ui) {

                // This prevents draggable from parent block from executing.
                event.stopPropagation();

                // Ensure helper has the same
                // width and height of the original block.
                ui.helper
                    .addClass("is-helper")
                    .css({
                        width: block.width(),
                        height: block.height()
                    });

                // Deactivate all blocks
                blocks.deactivateBlock();

                // Tell block host we're dragging this block
                blocks.drag(block);

                // Hide the block that we're dragging
                block.addClass("hide");

                // Set as current block
                currentBlock = block;

                self.populateDropzones();

                composer.manager()
                    .addClass("is-resizing");
            },

            // If we're dragging from block menu
            "{blocks.menu} dragstart": function(block, event, ui) {

                self.populateDropzones();

                // Set as current block
                currentBlock = block;

                composer.manager()
                    .addClass("is-resizing");
            },

            // If we're dragging from media manager
            "{mediaFile} dragstart": function(mediaFile, event, ui) {

                // Populate dropzones
                self.populateDropzones();

                currentBlock = $();

                composer.manager()
                    .addClass("is-resizing");
            },

            // When block's dropzone is clicked
            "{dropzone} click": function(dropzone, event) {

                // When this is being dragged, don't do anything
                if (composer.manager().hasClass("is-dragging-block")) {
                    return;
                }

                // When a dropzone is clicked, we need to insert the new block
                self.isDropping = true;

                // Get item being dropped
                var item = self.selectedBlock;

                item
                    .removeClass('hide')
                    .removeClass('is-sort-item');

                // Drop item
                self.dropBlock(dropzone, item);

                // Resets the state so that dragstop wouldn't get triggered
                setTimeout(function() {
                    self.isDropping = false;

                    self.dropStop();

                    // Reset the selection
                    self.selectedMenu = null;
                    self.selectedBlock = null;

                    // Remove the class on the manager
                    composer.manager().removeClass("is-dropping-block");
                    composer.manager().removeClass('is-moving-block');
                }, 0);
            },

            "{dropzone} mouseenter": function(dropzone, event) {

                dropzone.addClass("is-receiving");
            },

            "{dropzone} mouseleave": function(dropzone, event) {

                dropzone.removeClass("is-receiving");
            },

            // When block is hovering over a dropzone
            "{dropzone} dropover": function(dropzone, event, ui) {

                blocks.over(dropzone);

                // Set the last known dropzone
                self.lastDropzone = dropzone;

                // Only dropzone of content nest has position
                var position = dropzone.data("position");

                // Add is-sending class on helper
                ui.helper.addClass("is-sending");

                if (position) {
                    self.nest.of(dropzone)
                        .data("snappedPosition", position);
                }
            },

            // When block is hovering out of a dropzone
            "{dropzone} dropout": function(dropzone, event, ui) {

                // Reset the last dropzone
                self.lastDropzone = null;

                blocks.out(dropzone);

                // Remove is-sending class on helper
                ui.helper.removeClass("is-sending");
            },

            isDropping: false,
            lastDropzone: null,

            dropBlock: function(dropzone, item) {

                // Block
                var block = item;

                // Block Menu
                var isBlockMenu = item.is(blocks.menu);
                if (isBlockMenu) {
                    block = blocks.createBlockFromMenu(item);
                }

                // Media File
                var isMediaFile = item.is(self.mediaFile);
                if (isMediaFile) {
                    block = blocks.createBlockFromMediaFile(item);
                }

                var isMovingBlock = composer.manager().hasClass('is-moving-block');
                var isDroppingBlock = composer.manager().hasClass('is-dropping-block');

                if (isMovingBlock || isDroppingBlock) {
                    var nest = blocks.getBlockNest(dropzone);
                    var position = dropzone.data('position');

                    nest.data('snappedPosition', position);
                }

                // Replace dropzone with block
                dropzone.replaceWith(block);

                // Block menu needs to be dropped/released/outted here.
                // This is because block menu's dragstop listener can not
                // receive the newly created block from the block menu.
                if (isBlockMenu || isMediaFile || isMovingBlock || isDroppingBlock) {

                    blocks.drop(block);
                    blocks.release(block);
                    blocks.out(block);
                }
            },

            dropStop: function() {

                // Destroy all dropzones.
                setTimeout(function() {
                    self.destroyDropzones();
                }, 1);

                composer.manager()
                    .removeClass("is-resizing");
            },

            // When block/blockMenu is dropped on a dropzone
            "{dropzone} drop": function(dropzone, event, ui) {

                // If it's being dropped, skip this
                if (self.isDropping) {
                    return;
                }

                self.isDropping = true;

                // Get item being dropped
                var item = ui.draggable;

                // Drop item
                self.dropBlock(dropzone, item);

                // Resets the state so that dragstop wouldn't get triggered
                setTimeout(function() {
                    self.isDropping = false;
                }, 0);
            },

            // After an existing block is dropped on a dropzone,
            // or did not drop on a dropzone so it is returning
            // to its original position.
            "{block} dragstop": function(block, event, ui) {

                // Display the block once it is dropped on a dropzone
                block.removeClass("hide");

                // Let block host drop, release and out this block.
                blocks.drop(block);
                blocks.release(block);
                blocks.out(block);

                self.dropStop();
            },

            // After a block menu is dropped on a dropzone,
            // or did not drop on a dropzone so no new block
            // is created.
            "{blocks.menu} dragstop": function(block, event, ui) {

                // If it's being dropped, skip this
                if (self.isDropping || !self.lastDropzone) {
                    self.dropStop();
                    return;
                }

                var item = ui.helper;

                // Drop on to the last active dropzone.
                self.dropBlock(self.lastDropzone, item);

                self.dropStop();
            },

            // After a media file is dropped on a dropzone,
            // or did not drop on a dropzone so no new block
            // is created.
            "{mediaFile} dragstop": function(mediaFile, event, ui) {

                // If it's being dropped, skip this
                if (self.isDropping || !self.lastDropzone) {
                    self.dropStop();
                    return;
                }

                var item = ui.helper;

                self.dropBlock(self.lastDropzone, item);

                self.dropStop();
            },

            "{self} composerDocumentScroll": function(base, event) {

                var draggable = currentBlock.data("ui-draggable");

                $.ui.ddmanager.prepareOffsets(draggable, event);
            },

            "{dropzone} dropdeactivate": function(dropzone, event, ui) {

                blocks.out(dropzone);
            }

        }});

        module.resolve();

    });
});

EasyBlog.module("composer/blocks/font", function($){

var module = this;

EasyBlog.require()
.library(
    "colorpicker",
    "nouislider"
)
.done(function(){

EasyBlog.Controller("Composer.Blocks.Font",
{
    elements: [
        ".eb-composer-fieldset[data-name=font] [data-eb-{font-color-menu|font-family-menu|font-size-menu|font-color-content|font-color-picker|font-family-content|font-size-content|font-color-caption|font-family-caption|font-size-caption|font-family-option|font-format-option}]",

        ".eb-composer-fieldset[data-name=font] [data-eb-{numslider-toggle|numslider-widget|numslider-value|numslider-input|numslider-units|numslider-unit|numslider-current-unit}]",

        ".eb-composer-fieldset[data-name=font] [data-eb-{colorpicker|colorpicker-toggle}]",
    ],

    defaultOptions: $.extend({

        fontSizeUnits: {

            "px": {
                start: 12,
                step: 2,
                range: {
                    min: 8,
                    max: 72
                },
                pips: {
                    mode: "values",
                    density: 4,
                    values: [8, 12, 18, 24, 48, 72]
                }
            },

            "%": {
                start: 100,
                step: 10,
                range: {
                    min: 0,
                    max: 200
                },
                pips: {
                    mode: "positions",
                    values: [0,50,100],
                    density: 10
                }
            }
        }

    }, EBD.selectors)
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = blocks.composer;
        currentBlock = $();

        self.initFontFormatting();
        self.initFontColor();
    },

    "{self} composerBlockActivate": function(base, event, block) {

        currentBlock = block;

        self.populate(block);
    },

    populate: function(block) {

        // Determine if we should show font fieldset
        var blockMeta = blocks.getBlockMeta(block),
            showFontFieldset = blockMeta.properties.fonts;

        // Show or hide font fieldset
        composer.panels.fieldset.get("font")
            .toggle(showFontFieldset);

        if (!showFontFieldset) {
            return;
        }

        self.populateFontColor(block);
        self.populateFontFamily(block);
        self.populateFontSize(block);
        self.populateFontFormatting(block);
    },

    //
    // Font Color API
    //

    initFontColor: function() {

        // Init colorpicker
        self.fontColorPicker()
            .colorpicker();
    },

    populateFontColor: function(block) {

        var fontColor = block.css('color');

        self.updateFontColorUI(fontColor);
    },

    setFontColor: function(block, fontColor) {

        // Update block font color
        block.css("color", fontColor);

        // Update font color UI
        self.updateFontColorUI(fontColor);
    },

    updateFontColorUI: function(fontColor) {

        self.updatingFontColorUI = true;

        // Defaults to black
        if (!fontColor) fontColor = currentBlock.css("color") || "#000";

        // Update color preview
        self.fontColorCaption()
            .css("backgroundColor", fontColor);

        self.fontColorPicker()
            .colorpicker("setColor", fontColor);

        self.updatingFontColorUI = false;
    },

    removeFontColor: function(block) {

        self.setFontColor(block, "");
    },

    //
    // Font Family API
    //

    populateFontFamily: function(block) {

        var fontFamily = block[0].style.fontFamily;

        // Update the font family
        self.updateFontFamilyUI(fontFamily);
    },

    setFontFamily: function(block, fontFamily) {

        // Remove any font preview
        self.unpreviewFontFamily(block);

        // Create change event
        var changeEvent = $.Event("composertBlockFontFamilyChange");
        changeEvent.fontFamily = fontFamily;

        // Trigger change event
        base.trigger(changeEvent, fontFamily);

        // Update font family UI
        self.updateFontFamilyUI(changeEvent.fontFamily);

        // If change event is not prevented, set font.
        if (!changeEvent.isDefaultPrevented()) {
            block.css("fontFamily", changeEvent.fontFamily);
        }
    },

    updateFontFamilyUI: function(fontFamily) {

        var fontFamilyOption =
            self.fontFamilyOption()
                .removeClass("active")
                .where("value", '"' + fontFamily + '"')
                .addClass("active");

        // Determine font family captiomn
        var fontFamilyCaption =
                fontFamilyOption.length > 0 ?
                    fontFamilyOption.html() :
                    fontFamily.split(",")[0];

        // Set font family caption
        self.fontFamilyCaption()
            .html(fontFamilyCaption);
    },

    previewFontFamily: function(block, fontFamily) {

        // Remember original font value
        var originalFontFamily =
                block.data("originalFontFamily") ||
                currentBlock[0].style.fontFamily;

        // Create preview event
        var previewEvent = $.Event("composerBlockFontFamilyPreview");
        previewEvent.originalFontFamily = originalFontFamily;
        previewEvent.fontFamily = fontFamily;

        // Trigger preview event
        base.trigger(previewEvent, fontFamily, originalFontFamily);

        // Store original font family
        block.data("originalFontFamily", previewEvent.originalFontFamily);

        // If event is not prevented, set font family from block.
        if (!previewEvent.isDefaultPrevented()) {
            block.css("fontFamily", previewEvent.fontFamily);
        }
    },

    unpreviewFontFamily: function(block) {

        // Get original font family
        var originalFontFamily = block.data("originalFontFamily");

        // Create unpreview event
        var unpreviewEvent = $.Event("composerBlockFontFamilyUnpreview");
        unpreviewEvent.originalFontFamily = originalFontFamily;

        // Trigger unpreview evetn
        base.trigger(unpreviewEvent, originalFontFamily);

        // Forget original font family
        block.removeData("originalFontFamily");

        // If event is not prevented, remove font family from block.
        if (!unpreviewEvent.isDefaultPrevented()) {
            block.css("fontFamily", unpreviewEvent.originalFontFamily);
            return;
        }
    },

    //
    // Font Size API
    //

    populateFontSize: function(block) {

        var fontSize = block.css("fontSize");

        // Update the fontsize
        self.updateFontSizeUI(fontSize);
    },

    setFontSize: function(block, fontSize) {

        // If number is given, add a unit.
        if ($.isNumeric(fontSize)) {
            var unit = self.getFontSizeUnit();
            fontSize = fontSize + unit;
        }

        // Update block font size
        block.css("fontSize", fontSize);

        // Update font size UI
        self.updateFontSizeUI(fontSize || block.css("fontSize"));

        // Automatically set line height whenever
        // font size is set.
        if (fontSize) {

            self.setLineHeight(block, "120%");

            self.numsliderToggle()
                .prop("checked", true);

        } else {

            self.removeLineHeight(block);

            self.numsliderToggle()
                .prop("checked", false);
        }
    },

    updateFontSizeUI: function(fontSize) {

        self.updatingFontSizeUI = true;

        // Get value & unit
        var value = Math.abs(fontSize.replace(/\%|px/gi, ""))
            unit = fontSize.match("%") ? "%" : "px";

        if (self.getFontSizeUnit()!==unit) {
            self.setFontSizeUnit(unit);
        }

        // Set caption
        self.fontSizeCaption()
            .html(fontSize);

        // Set dropdown toggle
        self.numsliderCurrentUnit()
            .html(unit);

        // Set dropdown
        self.numsliderUnit()
            .removeClass("active")
            .where("unit", '"' + unit + '"')
            .addClass("active");

        // Set slider value
        self.numsliderWidget()
            .val(value);

        // Set input value
        self.numsliderInput().val(value);

        self.updatingFontSizeUI = false;
    },

    removeFontSize: function(block) {

        self.setFontSize(block, "");
    },

    getFontSizeUnit: function() {

        return self.fontSizeContent().data("unit") || "%";
    },

    setFontSizeUnit: function(unit) {

        self.fontSizeContent().data("unit", unit);

        // Use percentage by default
        var unitOptions = opts.fontSizeUnits[unit];

        // Set up slider
        self.numsliderWidget()
            .find(".noUi-pips")
            .remove()
            .end()
            .noUiSlider(unitOptions, true)
            .noUiSlider_pips(unitOptions.pips);
    },

    setLineHeight: function(block, lineHeight) {

        block.css("lineHeight", lineHeight);
    },

    removeLineHeight: function(block) {

        self.setLineHeight(block, "");
    },

    //
    // Font Formatting API
    //

    fontFormatting: {

        bold: {
            key: "fontWeight",
            val: "bold"
        },

        italic: {
            key: "fontStyle",
            val: "italic"
        },

        underline: {
            key: "textDecoration",
            val: "underline"
        },

        strikethrough: {
            key: "textDecoration",
            val: "line-through"
        },

        alignleft: {
            key: "textAlign",
            val: "left"
        },

        alignright: {
            key: "textAlign",
            val: "right"
        },

        aligncenter: {
            key: "textAlign",
            val: "center"
        },

        justify: {
            key: "textAlign",
            val: "justify"
        }
    },

    initFontFormatting: function() {

        self.fontFormatOption()
            .on("touchstart click mousedown mouseup", function(event){

                // Prevent caret from losing focus
                event.preventDefault();
            })
            .on("touchstart click", function(event){
                var fontFormatOption = $(this);
                var format = fontFormatOption.data("format");
                self.setFontFormatting(currentBlock, format, fontFormatOption.hasClass("active"));
            });
    },

    populateFontFormatting: function(block) {

        var node = block[0];
        var style = node.style;

        var fontFormatOption =
            self.fontFormatOption().each(function(){

                var fontFormatOption = $(this);
                var format = fontFormatOption.data("format");

                if (/orderedlist|unorderedlist|indent|outdent/.test(format)) {
                    fontFormatOption.removeClass("active");
                    return;
                }

                var fontFormatting = self.fontFormatting[format];
                var hasFormatting = style[fontFormatting.key]==fontFormatting.val;

                fontFormatOption.toggleClass("active", hasFormatting);
            });

        var isTextBlock = block.data("type")=="text";

        if (isTextBlock) {

            // Quick hack to activate the same button on global font fieldset
            var current = composer.editor.selection.getCurrent();
            var list = $(current).parentsUntil(block).filter("ul, ol").eq(0);

            $.each({orderedlist: "ol", unorderedlist: "ul"}, function(format, formatTag) {

                var hasFormatting = list.is(formatTag);

                fontFormatOption.where("format", format)
                    .toggleClass("active", hasFormatting);
            });
        }

        // Limit list formating to only text block
        composer.panels.fieldset.get("font")
            .find(".eb-font-formatting.section-list")
            .toggle(isTextBlock);
    },

    setFontFormatting: function(block, format, removeFormatting) {

        var editor = composer.editor;

        switch (format) {

            case "orderedlist":
            case "unorderedlist":
                editor.list.toggle(format);
                break;

            case "indent":
                editor.indent.increase();
                break;

            case "outdent":
                editor.indent.decrease();
                break;

            default:
                var fontFormatting = self.fontFormatting[format];
                block.css(fontFormatting.key, removeFormatting ? "" : fontFormatting.val);
                break;
        }

        self.populateFontFormatting(block);
    },

    removeFontFormatting: function(block, format) {

        self.setFontFormatting(block, format, true);
    },

    //
    // Font Color UI
    //

    "{colorpicker} colorpickerChange": function(colorpicker, event, hex) {

        if (self.updatingFontColorUI) return;

        self.colorpickerToggle().prop("checked", true);

        self.setFontColor(currentBlock, hex);
    },

    "{colorpickerToggle} change": function(colorpickerToggle) {

        // If we're disable font color, remove font color.
        if (!colorpickerToggle.checked()) {
            self.removeFontColor(currentBlock);
        }
    },

    //
    // Font Family UI
    //

    "{fontFamilyOption} mouseover": function(fontFamilyOption) {

        var fontFamily = fontFamilyOption.data("value");

        self.previewFontFamily(currentBlock, fontFamily);
    },

    "{fontFamilyOption} mouseout": function() {

        self.unpreviewFontFamily(currentBlock);
    },

    "{fontFamilyOption} click": function(fontFamilyOption) {

        var fontFamily = fontFamilyOption.data("value");

        self.setFontFamily(currentBlock, fontFamily);
    },

    //
    // Font Size UI
    //

    "{numsliderWidget} nouislide": function(numsliderWidget, event, value) {

        // Sliding only updates input
        self.numsliderInput()
            .val(Math.abs(value));
    },

    "{numsliderWidget} set": function(numsliderWidget, event, value) {

        if (self.updatingFontSizeUI) return;

        self.setFontSize(currentBlock, Math.abs(value));
    },

    "{numsliderInput} input": function(numsliderInput) {

        if (self.updatingFontSizeUI) return;

        var fontSize = Math.abs($.trim(numsliderInput.val()));

        self.numsliderToggle().checked(true);

        self.setFontSize(currentBlock, fontSize);
    },

    "{numsliderUnit} click": function(numsliderUnit) {

        var unit = numsliderUnit.data("unit");

        self.setFontSizeUnit(unit);
    },

    "{numsliderToggle} change": function(numsliderToggle) {

        // If we're disable font size, remove font size.
        if (!numsliderToggle.checked()) {
            self.removeFontSize(currentBlock);
        }
    }

}});


module.resolve();

});

})
EasyBlog.module("composer/blocks/guide", function($){

var module = this;

EasyBlog.Controller("Composer.Blocks.Guide",
{
    defaultOptions: $.extend({}, EBD.selectors)
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = self.blocks.composer;
    },

    "{self} composerBlockHoverIn": function(base, event, block) {

        // Hover block
        block.addClass("hover");
    },

    "{self} composerBlockHoverOut": function(base, event, block) {

        // Unhover block
        block.removeClass("hover");
    },

    "{self} composerBlockDrop": function(base, event, block) {


    },

    "{self} composerBlockActivate": function(base, event, block, handler) {

        // Add active class only to current block
        block.addClass("active");

        var isNestedBlock = block.is(EBD.nestedBlock);

        // If block is a nestedBlock
        if (isNestedBlock) {

            // Get nest of block and add active class
            self.nest.of(block)
                .addClass("active");

            // Get parent block and add has-active-child class
            blocks.getAllParentBlocks(block)
                .addClass("has-active-child");
        }

        // Get workarea and add has-active-nest class
        // if activating nestedBlock
        self.workarea()
            .toggleClass("has-active-nest", isNestedBlock);

        // Glow block
        block.addClass("is-glowing");

        setTimeout(function(){
            block.removeTransitionClass("is-glowing", 2000);
        }, 10);
    },

    "{self} composerBlockDeactivate": function(base, event, block) {

        // Remove active class from all blocks
        self.block()
            .removeClass("active has-active-child");

        // Remove active class from all nest
        self.nest()
            .removeClass("active");
    }

}});

module.resolve();

});

EasyBlog.module("composer/blocks/handlers/audio", function($){

    var module = this;

    EasyBlog.require()
    .library('plupload2', 'audiojs')
    .done(function($) {

        EasyBlog.Controller("Composer.Blocks.Handlers.Audio", {
            elements: [
                "[data-eb-{file-error}]"
            ],
            defaultOptions: {

                // Browse button in placeholder
                "{browseButton}": ".eb-composer-placeholder-audio [data-eb-mm-browse-button]",

                "{audio}": "audio",
                "{placeholder}": "[data-eb-composer-audio-placeholder]",

                // Preview area
                "{infoBox}": "[data-audio-infobox]",
                "{artist}": "[data-audio-artist]",
                "{track}": "[data-audio-track]",
                "{trackSeparator}": "[data-audio-track-separator]",
                "{download}": "[data-audio-download]",

                // Fieldset area
                "{displayArtist}": "[data-audio-fieldset-artist]",
                "{displayTrack}": "[data-audio-fieldset-track]",
                "{displayDownload}": "[data-audio-fieldset-download]",
                "{autoplay}": "[data-audio-fieldset-autoplay]",
                "{loop}": "[data-audio-fieldset-loop]"
            }
        }, function(self, opts, base, composer, blocks, meta, currentBlock, mediaManager) {

            return {

                init: function() {
                    // Globals
                    blocks = self.blocks;
                    composer = blocks.composer;
                    meta = opts.meta;
                    currentBlock = $();
                    mediaManager = EasyBlog.MediaManager;
                },

                toText: function(block) {
                    return;
                },

                toData: function(block) {
                    var data = blocks.data(block);
                    var content = blocks.getBlockContent(block);

                    // Set the download url
                    data.download = self.download.inside(content).attr('href');

                    // Set the artist
                    data.artist = self.artist.inside(content).text();

                    // Set the track
                    data.track = self.track.inside(content).text();

                    return data;
                },

                toHTML: function(block) {
                    var block = block.clone();
                },

                toLegacyShortcode: function(meta, block) {

                    var obj = {
                        "uri": meta.uri
                    };

                    var str = '[embed=audio]' + JSON.stringify(obj) + '[/embed]';

                    return str;
                },

                activate: function(block) {
                    // Set as current block
                    currentBlock = block

                    // Populate fieldset
                    self.populate(block);
                },

                deactivate: function(block) {

                },

                construct: function(data) {

                    var block = blocks.createBlockContainer('audio');
                    var blockData = blocks.data(block);

                    $.extend(blockData, data);

                    return block;
                },

                constructFromMediaFile: function(mediaFile) {

                    var key = mediaFile.data("key");
                    var uri = mediaManager.getUri(key);

                    // Create block container first
                    var block = blocks.createBlockContainer("audio");
                    var blockContent = blocks.getBlockContent(block);
                    var data = blocks.data(block);

                    // Add loading indicator
                    block.addClass("is-loading");

                    // Get media meta
                    mediaManager.getMedia(uri)
                        .done(function(media){

                            var mediaMeta = media.meta;

                            self.createPlayer(block, mediaMeta.url, mediaMeta.title);
                        })
                        .fail(function(){
                        })
                        .always(function(){
                            block.removeClass("is-loading");
                        });

                    return block;
                },

                reconstruct: function(block) {
                    var placeholder = self.placeholder.inside(block);
                    var data = blocks.data(block);

                    if (data.url) {
                        self.createPlayer(block, data.url, data.track);
                    }

                    // Register the placeholder with mediamanager
                    if (placeholder.length > 0) {
                        EasyBlog.MediaManager.uploader.register(placeholder);
                    }
                },

                deconstruct: function(block) {
                },

                refocus: function(block) {
                },

                reset: function(block) {
                    var content = blocks.getBlockContent(block);
                },

                populate: function(block) {

                    // When populating the fieldset for a block, reset the values
                    var data = blocks.data(block);

                    self.updateFieldset(block);
                },

                updateFieldset: function(block) {
                    // When populating the fieldset for a block, reset the values
                    var data = blocks.data(block);

                    self.autoplay()
                        .val(data.autoplay ? '1' : '0')
                        .trigger('change');

                    self.loop()
                        .val(data.loop ? '1' : '0')
                        .trigger('change');

                    self.displayArtist()
                        .val(data.showArtist ? '1' : '0')
                        .trigger('change');

                    self.displayDownload()
                        .val(data.showDownload ? '1' : '0')
                        .trigger('change');

                    self.displayTrack()
                        .val(data.showTrack ? '1' : '0')
                        .trigger('change');
                },

                getPlayerTemplate: function() {
                    return $(meta.player);
                },

                createPlayer: function(block, url, fileName) {

                    var blockContent = blocks.getBlockContent(block);
                    var template = self.getPlayerTemplate();
                    var data = blocks.data(block);
                    var uid = data.uid || (data.uid = $.uid("audio-"));
                    var url = data.url || url;
                    var track = fileName || '';
                    var artist = data.artist;

                    // Set the data url
                    data.url = url;
                    data.track = track;
                    data.artist = artist;
                    data.uid = uid;

                    self.audio.inside(template)
                        .attr('id', uid)
                        .attr('src', url);

                    self.artist.inside(template)
                        .html(data.artist)
                        .editable(true);

                    self.track.inside(template)
                        .html(data.track)
                        .editable(true);

                    self.download.inside(template)
                        .attr('href', url)
                        .attr('target', 'blank')
                        .on('click', function(event){
                            event.preventDefault();

                            // Do not allow click to happen here on composer.
                        });


                    // Append the template into the block content
                    blockContent.html(template);

                    $.audiojs.events.ready(function(){
                        $.audiojs.create(blockContent.find('audio'));
                    });

                    // Update the output based on the data
                    if (!data.showDownload) {
                        self.download.inside(blockContent).addClass('hide');
                    }

                    if (!data.showArtist) {
                        self.artist.inside(blockContent).addClass('hide');
                    }

                    if (!data.showTrack) {
                        self.track.inside(blockContent).addClass('hide');
                    }

                    self.updateInfoBox(block);
                },

                // "{artist} keypress": function(el, event) {

                //     var data = blocks.data(currentBlock);

                //     data.artist = $(el).text();

                // },

                updateInfoBox: function(block) {

                    var data = blocks.data(block);
                    var content = blocks.getBlockContent(block);
                    var infoBox = self.infoBox.inside(content);

                    if (!data.showDownload && !data.showTrack && !data.showArtist) {
                        infoBox.addClass('disabled');
                        return;
                    }

                    infoBox.removeClass('disabled');
                },

                "{displayDownload} change": function(el, event){
                    var content = blocks.getBlockContent(currentBlock);
                    var enabled = el.val() == 1 ? true : false;

                    var data = blocks.data(currentBlock);
                    data.showDownload = enabled;

                    // Update the infobox
                    self.updateInfoBox(currentBlock);

                    if (!enabled) {
                        self.download.inside(currentBlock)
                            .addClass('hide');

                        return;
                    }

                    self.download.inside(currentBlock)
                        .removeClass('hide');
                },

                "{displayArtist} change": function(el, event) {
                    var content = blocks.getBlockContent(currentBlock);
                    var enabled = el.val() == 1 ? true : false;
                    var artist = self.artist.inside(content);

                    var data = blocks.data(currentBlock);
                    data.showArtist = enabled;

                    // Update the infobox
                    self.updateInfoBox(currentBlock);

                    if (!enabled) {
                        artist.addClass('hide');

                        return;
                    }

                    artist.removeClass('hide');
                },

                "{displayTrack} change": function(el, event) {
                    var content = blocks.getBlockContent(currentBlock);
                    var enabled = el.val() == 1 ? true : false;
                    var track = self.track.inside(content);
                    var trackSeparator = self.trackSeparator.inside(content);

                    var data = blocks.data(currentBlock);
                    data.showTrack = enabled;

                    // Update the infobox
                    self.updateInfoBox(currentBlock);

                    if (!enabled) {
                        trackSeparator.addClass('hide');
                        track.addClass('hide');

                        return;
                    }

                    trackSeparator.removeClass('hide');
                    track.removeClass('hide');
                },

                "{autoplay} change": function(el, event) {
                    var data = blocks.data(currentBlock);
                    var enabled = el.val() == 1 ? true : false;

                    data.autoplay = enabled;
                },

                "{loop} change": function(el, event) {
                    var data = blocks.data(currentBlock);
                    var enabled = el.val() == 1 ? true : false;

                    data.loop = enabled;
                },

                "{placeholder} mediaUploaderFilesAdded": function(placeholder, event, uploader, files) {
                    EasyBlog.MediaManager.uploader.addItem(files[0], placeholder);
                },

                "{placeholder} mediaUploaderFileUploaded": function(placeholder, event, uploader, file, data) {

                    var response = data.response;
                    var mediaItem = response.media;
                    var mediaMeta = mediaItem.meta;

                    var block = blocks.block.of(placeholder);

                    setTimeout(function() {
                        self.createPlayer(block, mediaMeta.url, file.name);

                        if (block.hasClass("active")) {
                            self.populate(block);
                        }

                    }, 600);

                },

                "{browseButton} mediaSelect": function(browseButton, event, media) {

                    var block = blocks.block.of(browseButton);

                    if (media.meta.type != "audio") {
                        return;
                    }

                    var mediaMeta = media.meta;
                    var composerDocument = composer.document;
                    var isLegacy = composerDocument.isLegacy();

                    // Legacy
                    if (isLegacy) {
                        content = self.toLegacyHTML(block);
                        composerDocument.insertContent(content);

                    // EBD
                    } else {
                        self.createPlayer(block, mediaMeta.url, mediaMeta.title);
                    }
                },

                "{placeholder} mediaUploaderFileError": function(placeholder, event, uploader, error) {
                    if (error.code == $.plupload2.FILE_EXTENSION_ERROR) {
                        self.fileError.inside(currentBlock).removeClass('hide');
                    }
                },

                "{self} mediaInsert": function(el, event, media, block) {

                    if (media.meta.type != 'audio') {
                        return;
                    }

                    var composerDocument = composer.document;
                    var isLegacy = composerDocument.isLegacy();

                    // Legacy
                    if (isLegacy) {
                        content = self.toLegacyShortcode(media, block);
                        composerDocument.insertContent(content);
                    } else {

                        // EBD
                        // Construct a new post block and insert into the document
                        var block = blocks.constructBlock('audio', {
                            "url": media.meta.url,
                            "track": media.meta.title
                        });

                        blocks.addBlock(block);
                        blocks.activateBlock(block);
                    }

                }
            }
        });

        module.resolve();
    });
});

EasyBlog.module("composer/blocks/handlers/behance", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Behance", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-behance-form]",
            "{insert}": "[data-behance-insert]",
            "{source}": "[data-behance-source]",
            "{loader}": "> [data-behance-loader]",

            // Preview
            "{preview}": "> [data-behance-preview]",
            "{errorMessage}": "[data-behance-error]",
            "{fsSource}": "[data-fs-behance-source]",
            "{fsUpdate}": "[data-fs-behance-update]"
        }, EBD.selectors)
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toText: function(block) {
                var data = blocks.data(block);

                return data.url;
            },

            toEditableHTML: function(block) {
                return '';
            },
            
            toHTML: function(block) {
                var data = blocks.data(block);

                return data.embed;
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function(block) {
            },

            construct: function(block) {
            },

            reconstruct: function(block) {

                var data = blocks.data(block);
                var overlay = block.data('overlay');
                var content = blocks.getBlockContent(block);

                // Set the overlay
                if (data.embed && !overlay) {
                    self.setOverlay(block, data.embed);
                }

                // If there's no embed codes, we need to display the form
                if (!data.embed) {
                    content.html($(meta.html));
                }

                // So redactor won't receive it.
                block.find(self.source.selector).off("paste").on("paste", function(event){
                    event.stopPropagation();
                });

            },

            deconstruct: function(block) {
                var content = blocks.getBlockContent(block);

                // When saving, remove the form
                self.form.inside(content).remove();

                self.loader.inside(content).remove();

                return block;
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block) {

                // When populating the fieldset for a block, reset the values
                var data = blocks.data(block);

                if (data.url) {
                    self.fsSource().val(data.url);
                }

            },

            loading: function() {
                var content = blocks.getBlockContent(currentBlock);

                if (!self.isLoading) {
                    self.loader.inside(content).removeClass('hidden');
                    self.form.inside(content).addClass('hidden');

                    self.isLoading = true;

                    return;
                }

                self.loader.inside(content).addClass('hidden');
                self.form.inside(content).removeClass('hidden');

                self.isLoading = false;
            },

            setOverlay: function(block, embed) {
                var content = blocks.getBlockContent(block);
                var overlay = block.data('overlay');

                if (!overlay) {
                    overlay = composer.document.overlay.create(block);

                    overlay.placeholder()
                        .css('height', '450px')
                        .appendTo(content);

                    overlay.element().append(embed);

                    overlay.attach();
                } else {
                    // Remove the existing data from the overlay.
                    overlay.element().empty();

                    // Attach the embed codes on the overlay
                    overlay.element().append(embed);
                }

                block.data('overlay', overlay);

            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // https://www.behance.net/gallery/18940231/The-dining-room
                // https://www.behance.net/gallery/14305889/art-portraits
                var regex = /^https:\/\/www\.behance\.net\/gallery\/(.*)\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            getOembedUrl: function(url) {
                return 'http://www.behance.net/services/oembed?url=' + url;
            },

            crawl: function(block, url) {

                var content = blocks.getBlockContent(block);
                var data = blocks.data(block);
                var crawlUrl = self.getOembedUrl(url);

                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return false;
                }

                // If there's an overlay hide it
                var overlay = block.data('overlay');

                if (overlay) {
                    overlay.element().empty();
                }

                // Display the loader and hide the form
                self.loading();

                EasyBlog.ajax('site/views/crawler/crawl', {
                    "url": crawlUrl
                })
                .done(function(results) {
                    // When it's done trigger the loading again
                    self.loading();

                    var result = results[crawlUrl];

                    // Set the data back
                    data.url = url;
                    data.embed = result.oembed.html;

                    self.setOverlay(block, data.embed);

                    // Hide the form and loader
                    self.form.inside(content).addClass('hidden');
                })
                .fail(function(message) {
                    self.loading();

                    self.errorMessage()
                        .removeClass('hide')
                        .html(message);
                });

                return task;
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();

                // Update the source in the fieldset
                self.fsSource().val(url);

                self.crawl(currentBlock, url);
            },

            "{fsUpdate} click": function() {

                // Add overlay when the user add's a new link.
                var content = blocks.getBlockContent(currentBlock);
                var url = self.fsSource().val();

                self.crawl(currentBlock, url);
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/buttons", function($) {

    var module = this;

    EasyBlog.require()
    .library(
        "rangeslider"
    )
    .done(function() {

        EasyBlog.Controller("Composer.Blocks.Handlers.Buttons", {
            defaultOptions: {

                "{button}": "> a.btn",
                "{textWrapper}": "> span",
                "{buttonSize}": "[data-eb-composer-block-button-size] [data-size]",
                "{buttonHyperlink}": "[data-button-link]",
                "{buttonNofollow}": "[data-button-nofollow]",
                "{buttonTarget}": "[data-button-target]",

                "{buttonSwatchItem}": "[data-eb-composer-button-swatch-item]"
            }
        }, function(self, opts, base, composer, blocks, meta, currentBlock) {

            return {

                init: function() {
                    // Globals
                    blocks = self.blocks;
                    composer = blocks.composer;
                    meta = opts.meta;
                    currentBlock = $();
                },

                selectionState: {},

                toData: function(block) {

                    var data = blocks.data(block);

                    return data;
                },

                // Returns the text that is within the block
                toText: function(block) {
                    var blockContent = blocks.getBlockContent(block),
                        button = self.button.inside(blockContent);

                    // since this button work similar like a link, we should return the href text.
                    text = blocks.data(block).link;

                    return text;
                },

                toHTML: function(block) {

                    var clone = block.clone();
                    var deconstructedBlock = self.deconstruct(clone);
                    var content = blocks.getBlockContent(deconstructedBlock).html();

                    return content;
                },

                activate: function(block) {

                    // Set as current block
                    currentBlock = block;

                    // Populate fielset
                    self.populate(block);
                },

                deactivate: function(block) {
                },

                construct: function(data) {
                },

                reconstruct: function(block) {

                    // Make the button editable
                    var blockContent = blocks.getBlockContent(block),
                        button = self.button.inside(blockContent);

                    // The button should not be clickable
                    button.on('click', function(event) {
                        event.stopPropagation();
                        event.preventDefault();
                    });
                },

                deconstruct: function(block) {

                    var blockContent = blocks.getBlockContent(block);
                    var button = self.button.inside(blockContent);

                    self.textWrapper.inside(button)
                        .removeAttr('contenteditable')
                        .editable(false);

                    return block;
                },

                refocus: function(block) {
                },

                reset: function(block) {
                },

                // When the active element is focused, we want to populate the fieldset
                populate: function(block) {

                    // Get the params for the current block
                    var data = blocks.data(block);

                    self.buttonSwatchItem()
                        .removeClass("active")
                        .where("style", data.style)
                        .addClass("active");

                    var buttonSize = self.buttonSize().filter('[data-size="' + data.size + '"]');

                    buttonSize.siblings().removeClass('active');
                    buttonSize.addClass('active');

                    self.buttonHyperlink().val(data.link);

                    self.buttonNofollow().val(data.nofollow).trigger('change');

                    self.buttonTarget().val(data.target);
                },

                "{buttonSwatchItem} click": function(buttonSwatchItem) {

                    var style = buttonSwatchItem.data("style"),
                        blockContent = blocks.getBlockContent(currentBlock);

                    self.buttonSwatchItem()
                        .removeClass("active")
                        .where("style", style)
                        .addClass("active");

                    self.button.inside(blockContent)
                        .removeClass('btn-info btn-primary btn-success btn-warning btn-danger')
                        .addClass(style);

                    blocks.data(currentBlock).style = style;
                },

                "{buttonSwatchItem} mouseover": function(buttonSwatchItem) {

                    clearTimeout(self.previewTimer);

                    var style = buttonSwatchItem.data('style'),
                        blockContent = blocks.getBlockContent(currentBlock);

                    self.button.inside(blockContent)
                        .removeClass('btn-info btn-primary btn-success btn-warning btn-danger')
                        .addClass(style);
                },

                "{buttonSwatchItem} mouseout": function(buttonSwatchItem) {

                    clearTimeout(self.previewTimer);

                    var blockContent = blocks.getBlockContent(currentBlock);

                    self.previewTimer = setTimeout(function() {
                        self.button.inside(blockContent)
                            .removeClass('btn-info btn-primary btn-success btn-warning btn-danger')
                            .addClass(blocks.data(currentBlock).style);
                    }, 50);
                },

                "{buttonSize} click": function(el) {

                    var size = el.data('size'),
                        blockContent = blocks.getBlockContent(currentBlock);

                    el.addClass('active')
                      .siblings()
                      .removeClass('active');

                    self.button.inside(blockContent)
                        .removeClass('btn-sm btn-xs btn-lg btn-xlg')
                        .addClass(size);

                    blocks.data(currentBlock).size = size;
                },

                "{buttonSize} mouseover": function(el) {
                    clearTimeout(self.previewTimer);

                    var size = el.data('size'),
                        blockContent = blocks.getBlockContent(currentBlock);

                    self.button.inside(blockContent)
                        .removeClass('btn-sm btn-xs btn-lg btn-xlg')
                        .addClass(size);
                },

                "{buttonSize} mouseout": function(el) {
                    clearTimeout(self.previewTimer);

                    var blockContent = blocks.getBlockContent(currentBlock);

                    self.previewTimer = setTimeout(function() {
                        self.button.inside(blockContent)
                            .removeClass('btn-sm btn-xs btn-lg btn-xlg')
                            .addClass(blocks.data(currentBlock).size);
                    }, 50);
                },

                "{buttonHyperlink} keyup": $.debounce(function(el) {

                    var blockContent = blocks.getBlockContent(currentBlock),
                        data = blocks.data(currentBlock);

                    data.link = el.val();

                    self.button.inside(blockContent).attr('href', el.val());
                }, 250),

                "{buttonNofollow} change": function(el) {

                    var blockContent = blocks.getBlockContent(currentBlock),
                        data = blocks.data(currentBlock);

                    data.nofollow = el.val() == 1 ? 1 : 0;

                    self.button
                        .inside(blockContent)
                        .attr('rel', el.val() == 1 ? 'nofollow' : '');
                },

                "{buttonTarget} change": function(el) {

                    var blockContent = blocks.getBlockContent(currentBlock),
                        data = blocks.data(currentBlock);

                    data.target = el.val();

                    self.button
                        .inside(blockContent)
                        .attr('target', el.val());
                }
            }
        });

        module.resolve();
    });

});

EasyBlog.module("composer/blocks/handlers/code", function($) {

    var module = this;

    // This is used to inject ACE Editor within iframe
    var aceEditorScriptPath = $.uri($.require.defaultOptions.path)
                                    .toPath('./ace' + ($.mode=='compressed' ? '.min.js' : '.js'))
                                    .toString();

    // This creates a pseudo FD50 object
    // for ACE Editor module factory to execute
    // when it is loaded inside an iframe.
    window.FD50_PSEUDO = {
        module: function(name, factory) {
            factory.call($.Deferred(), $);
        }
    };

    EasyBlog.Controller("Composer.Blocks.Handlers.Code", {

        defaultOptions: {

            "{pre}" : "> pre",

            "{readOnly}": "[data-code-readonly]",
            "{showGutter}": "[data-code-gutter]",
            "{fontsize}": "[data-code-fontsize]",
            "{themeSelection}": "[data-code-theme]",
            "{modeSelection}": "[data-code-mode]",

            theme: "github",
            mode: "html",
            css: {
                position: "fixed",
                width: "100%",
                height: "100%",
                top: 0,
                bottom: 0,
                left: 0,
                right: 0,
                margin: 0,
                padding: 0
            }
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {

                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            toText: function(block) {
                return;
            },

            toData: function(block) {
                var data = blocks.data(block),
                    uid = block.data('uid');

                instance = self.editor.instances[block.data('uid')];
                contents = instance.editor.getSession().getValue();
                data.code = contents;

                return data;
            },

            toEditableHTML: function(block) {

                // Remove the overlay because we need to regenerate the overlay again later
                var clone = block.clone();
                var blockContent = blocks.getBlockContent(clone);

                blockContent.find('[data-ebd-overlay-placeholder]').remove();

                return blockContent.html();
            },

            toHTML: function(block) {

                // get the editor
               var data = blocks.data(block),
                    uid = block.data('uid');

                instance = self.editor.instances[block.data('uid')];
                contents = instance.editor.getSession().getValue();

                // now we need to put the content back into the pre element
                var cloned = block.clone(),
                    deconstructedBlock = self.deconstruct(cloned),
                    blockContent = blocks.getBlockContent(deconstructedBlock);

                var preEle = self.pre.inside(blockContent);

                preEle.html(contents);

                // we need to set the mode as well.
                preEle.attr('data-mode', data.mode);

                var output = preEle.html();

                return output;
            },

            // When block is focused, this method is triggered.
            // Useful when we need to update the panels for the block.
            activate: function(block) {

                // Set the current block
                currentBlock = block;

                self.populate(block);
            },

            // When a block loses focus, this method is triggered.
            // Useful if we need to perform specific controls over the block when it loses focus.
            deactivate: function() {
                // TODO: Iframe overlay should be placed here
            },

            // When a new block is created programatically from another block, this get's triggered
            construct: function(data) {
                var data = $.extend({}, opts.data, data);
            },

            // To convert a viewable block to an editable block
            reconstruct: function(block) {

                var blockContent = blocks.getBlockContent(block);
                var data = blocks.data(block);

                // Create block overlay
                if (!block.data("overlay")) {

                    // Create overlay
                    var overlay = composer.document.overlay.create(block);

                    // Append overlay placeholder in the
                    // beginning of the block.
                    overlay.placeholder()
                        .prependTo(blockContent);

                    // Attach overlay
                    overlay.attach();

                    block.data("overlay", overlay);
                }

                // This will create the editor if
                // the editor hasn't been created yet.
                self.editor.get(block);
            },

            deconstruct: function(block) {
                return block;
            },

            reset: function(block) {
            },

            refocus: function() {
            },

            populate: function(block) {
                // Populate fieldset values
                var data = blocks.data(block);

                // Update the values of those items on fieldset
                self.modeSelection().val(data.mode);
                self.themeSelection().val(data.theme);

                // Update the show gutter options
                self.showGutter().val(data.show_gutter ? 1 : 0);
                self.showGutter().trigger('change');
            },

            getEditor: function(callback) {
                self.editor.get(currentBlock).done(callback);
            },

            switchMode: function(mode) {
                var data = blocks.data(currentBlock);

                self.getEditor(function(editor, session, pre) {

                    session.setMode('ace/mode/' + mode);

                    data.mode = mode;
                });
            },

            switchTheme: function(theme) {
                var data = blocks.data(currentBlock);

                self.getEditor(function(editor, session,pre){

                    // Update the theme
                    editor.setTheme(theme);

                    data.theme = theme;
                });
            },

            switchInvisible: function(isInvisible) {
                var data = blocks.data(current);

                self.getEditor(function(editor, session, pre){
                    editor.setShowInvisibles(isInvisible);

                    data.show_invisible = isInvisible;
                });
            },

            toggleGutter: function(showGutter) {

                var data = blocks.data(currentBlock);

                self.getEditor(function(editor, session, pre){
                    editor.renderer.setShowGutter(showGutter);

                    data.show_gutter = showGutter;
                });
            },

            updateFontSize: function(size) {
                var data = blocks.data(currentBlock);

                self.getEditor(function(editor, session, pre){
                    editor.setFontSize(size);

                    data.fontsize = size;
                });
            },

            "{fontsize} change": function(el, event) {
                var size = parseInt($(el).val());

                self.updateFontSize(size);
            },

            "{readOnly} change": function(el, event) {
            },

            "{showGutter} change": function(el, event) {
                var showGutter = $(el).val() == 1 ? true : false;

                self.toggleGutter(showGutter);
            },

            "{modeSelection} change": function(el, event) {
                var mode = $(el).val();

                // Switch mode
                self.switchMode(mode);
            },

            "{themeSelection} change": function(el, event) {

                var theme = $(el).val();

                // Switch theme
                self.switchTheme(theme);
            },

            editor: {

                instances: {},

                get: function(block) {

                    // Get instance
                    var instance = self.editor.instances[block.data("uid")];

                    // Return instance if found, else create the instance.
                    return instance || self.editor.create(block);
                },

                create: function(block) {

                    // Create instance
                    var uid = block.data("uid");
                    var blockContent = blocks.getBlockContent(block);
                    var pre = self.pre.inside(blockContent);
                    var instance = self.editor.instances[uid] = $.Deferred();
                    var data = blocks.data(block);

                    // Store overlay within editor instance
                    var overlay = instance.overlay = block.data("overlay");

                    if (!overlay) {
                        overlay = composer.document.overlay.create(block);

                        // Append overlay placeholder in the
                        // beginning of the block.
                        overlay.placeholder()
                            .prependTo(blockContent);

                        // Attach overlay
                        overlay.attach();

                        block.data("overlay", overlay);
                    }

                    // Create iframe
                    var iframe = $.create("iframe");

                    // Set iframe to a path that share the same so
                    // we can inject scripts within iframe.
                    var source = $.rootPath + '/media/index.html';

                    iframe
                        .attr("src", source)
                        .one("load", function() {

                            // Create references to iframe
                            var iframeWindow = iframe[0].contentWindow,
                                iframeDocument = iframeWindow.document,
                                iframeHead = iframeDocument.head,
                                iframeBody = iframeDocument.body,

                                // Clone pre and put it inside iframe
                                iframePre =
                                    pre.clone()
                                        .css(opts.css)
                                        .appendTo(iframeBody)[0];

                                // Iframe has no FD50 bootloader, so we map it to a
                                // fake one so ACE Editor's module factory can execute.
                                iframeWindow.eval("window.FD50 = window.parent.FD50_PSEUDO;");

                                // Load ACE Editor within iframe
                                $.script({
                                        url: aceEditorScriptPath,
                                        head: iframeHead
                                    })
                                    .done(function(){

                                        // Iniitalize ACE Editor
                                        var ace = iframeWindow.ace;
                                        var editor = ace.edit(iframePre);
                                        var theme = data.theme || pre.data('theme') || opts.theme;
                                        var mode = data.mode || pre.data('mode') || opts.mode;
                                        var fontSize = data.fontsize || pre.data('fontsize') || opts.fontsize;
                                        var gutter = data.show_gutter || pre.data('gutter') || opts.show_gutter;

                                        // Set editor options
                                        editor.setTheme(theme);
                                        editor.setFontSize(fontSize);
                                        editor.renderer.setShowGutter(gutter);

                                        // Set the code
                                        if (data.code) {
                                            editor.setValue(data.code);
                                        }

                                        // Set editor's height
                                        $(iframePre).css('height', '100%');

                                        editor.resize();

                                        // Set syntax highlighter to HTML by default
                                        var session = editor.getSession();
                                            session.setMode("ace/mode/" + mode);

                                        instance.editor = editor;

                                        // Resolve instance with editor and session
                                        instance.resolve(editor, session, pre);
                                    })
                                    .fail(function() {

                                        // Reject instance with error message
                                        instance.reject($.Exception("ACE Editor could not be loaded."));

                                        // Do not store this instance so user can retry again
                                        delete self.instances[uid];
                                    });
                        });

                    // Append the iframe into the overlay first
                    overlay.element().append(iframe);

                    return instance;
                }
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/codepen", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Codepen", {

        defaultOptions: {

            // Form
            "{form}": "> [data-codepen-form]",
            "{insert}": "[data-codepen-insert]",
            "{source}": "[data-codepen-source]",
            "{loader}": "> [data-codepen-loader]",

            // Preview
            "{preview}": "> [data-codepen-preview]",

            "{fsSource}": "[data-fs-codepen-source]",
            "{fsUpdate}": "[data-fs-codepen-update]",
            "{errorMessage}": "[data-codepen-error]"
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                // Globals
                blocks       = self.blocks;
                composer     = blocks.composer;
                meta         = opts.meta;
                currentBlock = $();
            },

            toText: function(block) {
                var data = blocks.data(block);

                return data.url;
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toEditableHTML: function(block) {
                return '';
            },

            toHTML: function(block) {
                var data = blocks.data(block);

                return data.embed;
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function(block) {
            },

            construct: function(block) {
            },

            reconstruct: function(block) {
                var data = blocks.data(block);
                var overlay = block.data('overlay');
                var content = blocks.getBlockContent(block);

                if (data.embed && !overlay) {
                    self.setOverlay(block, data.embed);
                }

                if (!data.embed) {
                    content.html($(meta.html));
                }
            },

            deconstruct: function(block) {
                var content = blocks.getBlockContent(block);

                // When saving, remove the form
                self.form.inside(content).remove();
                self.loader.inside(content).remove();

                return block;
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block) {

                // When populating the fieldset for a block, reset the values
                var data = blocks.data(block);

                self.fsSource().val(data.url);
            },

            loading: function() {
                var content = blocks.getBlockContent(currentBlock);

                if (!self.isLoading) {
                    self.loader.inside(content).removeClass('hidden');
                    self.form.inside(content).addClass('hidden');

                    self.isLoading = true;
                } else {

                    self.loader.inside(content).addClass('hidden');
                    self.form.inside(content).removeClass('hidden');

                    self.isLoading = false;
                }

            },

            setOverlay: function(block, embed) {
                var overlay = block.data('overlay');

                if (!overlay) {
                    overlay = composer.document.overlay.create(block);
                    var content = blocks.getBlockContent(block);
                    
                    // Append the placeholder first
                    overlay
                        .placeholder()
                        .css('height', '300px')
                        .appendTo(content);

                    // Append the embed codes now
                    overlay.element().append(embed);

                    // Attach the overlay now
                    overlay.attach();
                } else {
                    overlay.element().empty();
                    overlay.element().append(embed);
                }

                // Set the overlay data so we don't create overlays all the time
                block.data('overlay', overlay);
            },

            getUrl: function(url) {
                return 'http://codepen.io/api/oembed?url=' + url
            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // http://codepen.io/gastonfig/pen/YPrqEj
                var regex = /^http:\/\/codepen\.io\/(.*)\/pen\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            crawl: function(block, url) {

                var crawlUrl = self.getUrl(url);
                var data = blocks.data(block);
                var content = blocks.getBlockContent(block);

                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return false;
                }

                // If there's an overlay hide it
                var overlay = block.data('overlay');

                if (overlay) {
                    overlay.element().empty();
                }

                // Display the loader and hide the form
                self.loading();

                EasyBlog.ajax('site/views/crawler/crawl', {
                    "url": crawlUrl
                }).done(function(results) {

                    var result = results[crawlUrl];

                    data.url = url;
                    data.embed = result.oembed.html;

                    self.setOverlay(block, data.embed);

                }).fail(function(message) {

                    self.errorMessage().removeClass('hide').html(message);

                }).always(function() {
                    // When it's done trigger the loading again
                    self.loading();

                    // Hide the form and loader
                    self.form.inside(content).addClass('hidden');
                });
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                var data = blocks.data(currentBlock);
                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();

                // Update the fieldset's source
                self.fsSource().val(url);

                // Crawl the site
                self.crawl(currentBlock, url);
            },

            "{fsUpdate} click": function() {
                var url = self.fsSource().val();
                var data = blocks.data(currentBlock);

                self.crawl(currentBlock, url);
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/columns", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Columns", {
        defaultOptions: {

            "{columnsHeader}": ".row",
            "{columns}": ".row > div",
            "{wrapper}": "> div[data-col-wrapper]",
            "{columnRange}": "[data-eb-composer-block-column-range]",

            // Fieldset options
            "{control}": "[data-columns-control]",
            "{controlWidth}": "[data-select-width]",
            "{dropdown}":".eb-composer-manage-tab-name > select ",
            "{listboxItem}": "[data-listbox-item]"
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            matchWrapperHeight: function(block) {

                // Find the tallest wrapper and set it for all the wrapper.
                // This makes drag & drop operation smoother.
                var maxHeight = 0;

                self.wrapper
                    .inside(block)
                    // Remove wrapper height if enforced
                    .css("height", "")

                    // Find tallest height
                    .each(function(){

                        // Use wrapper height if it is taller
                        maxHeight = Math.max(maxHeight, $(this).height());
                    })

                    // Set the tallest height for all wrapper
                    .css("height", maxHeight);
            },

            removeWrapperHeight: function(block) {

                // Remove inline height styling from wrapper
                self.wrapper
                    .inside(block)
                    .css("height", "");
            },

            "{blocks.root} sortactivate": function() {

                // Get all column blocks
                blocks.getBlocksByType("columns")
                    .each(function(){
                        var block = blocks.getBlockContent($(this));

                        self.matchWrapperHeight(block);
                    });
            },

            "{blocks.root} sortdeactivate": function() {

                // Get all column blocks
                blocks.getBlocksByType("columns")
                    .each(function(){
                        var block = blocks.getBlockContent($(this));
                        self.removeWrapperHeight(block);
                    });
            },

            "{wrapper} sortchange": function(wrapper, event, ui) {

                var block = wrapper.closest(EBD.block);

                self.matchWrapperHeight(block);
            },

            "{wrapper} sortout": function(wrapper, event, ui) {

                var block = wrapper.closest(EBD.block);

                self.matchWrapperHeight(block);
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fielset
                self.populate(block);
            },

            deactivate: function(block) {
            },

            construct: function(data) {
                var data = $.extend({}, opts.data, data);

                return content;
            },

            reconstruct: function(block) {
            },

            //
            // Deconstruct a block so that it is free from any strange formatting or decorated stuffs
            //
            deconstruct: function(block) {
                var content = blocks.getBlockContent(block);

                // Get a list of column wrappers available currently.
                var wrappers = content.find('> .row > .col > [data-col-wrapper]');

                wrappers.each(function() {
                    var wrapper = $(this);

                    // Get the column parent
                    var column = wrapper.parent();

                    // Append all the child items to the column
                    wrapper.children().appendTo(column);

                    // Remove any content editable stuffs
                    column.removeAttr('contenteditable');

                    // Remove the wrapper.
                    wrapper.remove();
                });

                return block;
            },

            refocus: function(block) {

                var content = blocks.getBlockContent(block);
                var activeColumn = block.data('lastFocused') || self.columns.inside(content).filter(':first');

                // TODO: Capture lastFocused column
                activeColumn.focus();
            },

            reset: function(block) {
            },

            populate: function(block) {

                var data = blocks.data(block);
                var uid = block.attr("data-uid");

                // Re-populate the listbox items
                self.control().listbox()
                    .listbox('populate', data.columns);

                // Go through each of the list boxes and set the values accordingly.
                var content = blocks.getBlockContent(block);

                // var columns = self.columns.inside(content);
                var columns = $('div[data-uid="' + uid + '"]').find('div.ebd-block-content:eq(0) > div').children();

                columns.each(function(i, column) {
                    var listboxItem = self.listboxItem().eq(i);
                    var dropdown = self.controlWidth.inside(listboxItem);

                    // Get the size of the column
                    var size = $(column).attr('data-size');

                    dropdown.val(size);
                });
            },

            toHTML: function(block) {

                var clone = block.clone();
                var deconstructedBlock = self.deconstruct(clone);
                var content = blocks.getBlockContent(deconstructedBlock);

                return content.html();
            },

            toData: function(block) {
                var data = blocks.data(block);
                return data;
            },

            toText: function(block) {

                var content = blocks.getBlockContent(block).clone();

                return content.text();
            },

            // custom methods
            column: {

                activate: function(index) {
                    var content = blocks.getBlockContent(currentBlock);

                    // Get the columns
                    var columns = self.columns.inside(content);

                    // Get the column to activate
                    columns.eq(index).addClass('active-column');

                    // console.log('activate');
                },

                deactivate: function(index) {
                    var content = blocks.getBlockContent(currentBlock);

                    // Get the columns
                    var columns = self.columns.inside(content);

                    // Get the column to activate
                    columns.eq(index).removeClass('active-column');
                },

                // Create the columns
                create: function(size) {

                    var content = blocks.getBlockContent(currentBlock);
                    var columns = self.columns.inside(content);
                    var columnClass = 'col col-md-' + size;

                    // Create a wrapper for default text block
                    var wrapper = blocks.createBlockNest();

                    wrapper.attr('data-col-wrapper', '')
                        .addClass('ui-sortable');

                    // Add the text block into the wrapper
                    var textBlock = blocks.constructNestedBlock('text');
                    wrapper.append(textBlock);

                    // now we need to create a column wrapper and append the text wrapper.
                    var column = $('<div></div>');

                    column.addClass(columnClass)
                        .attr('data-size', size)
                        .append(wrapper);

                    // adding the column wrapper into the column block.
                    self.columnsHeader.inside(content)
                        .append(column);
                },

                remove: function(index) {
                    var blockContent = blocks.getBlockContent(currentBlock);

                    self.columns
                        .inside(blockContent)
                        .eq(index)
                        .remove();

                    var currentColumnsCount = self.columns.inside(blockContent).length;
                    var width = Math.ceil(12/currentColumnsCount);
                    var offset = false;

                    if (currentColumnsCount == 5) {
                        var width = 2;
                        offset = true;
                    }

                    var columnClass = 'col col-md-' + width;

                    self.columnsHeader
                        .inside(blockContent)
                        .find('div.col')
                        .removeClass()
                        .addClass(columnClass);
                        //.css({ 'background-color': "#b0c4de", 'border': "2px solid", 'border-radius': "25px" });
                        // added css class for EasyDebug

                    if (offset) {
                        self.columns
                            .inside(blockContent)
                            .first()
                            .addClass('col-md-offset-1');

                        self.columns
                            .inside(blockContent)
                            .last()
                            .addClass('col-md-offset-0');
                    }

                },

                change: function(index, width) {

                    var blockContent = blocks.getBlockContent(currentBlock);
                    var data = blocks.data(currentBlock);


                    // where this is a nested blocks and it allow 'itself' to be placed inside, then we can no longer use below method
                    // var currentColumnsCount = self.columns.inside(blockContent).length;

                    // we need to get the current nested block using uid and using more specific selector, as below:
                    var uid = currentBlock.attr("data-uid");
                    var currentColumnsCount = $('div[data-uid="' + uid + '"]').find('div.ebd-block-content:eq(0) > div').children().length;


                    //TODO: throw error if reach limit
                    if (width > 4 && currentColumnsCount == 3) {
                        self.columns.inside(blockContent).last().addClass('col col-md-2');
                    }
                    if (width == 4 && currentColumnsCount == 4) {
                        self.columns.inside(blockContent).last().addClass('col col-md-2');
                    }

                    if (width > 4 && (currentColumnsCount == 4 || currentColumnsCount == 5)) {
                        return;
                    }
                    if (currentColumnsCount == 6) {
                        // throw error
                        self.dropdown().val('2');
                        return;
                    }

                    self.columns.inside(blockContent).first().removeClass('col-md-offset-1');
                    self.columns.inside(blockContent).last().removeClass('col-md-offset-0');

                    var columnClass = 'col col-md-' + width;

                    var curColumn = self.columns.inside(blockContent).eq(index);

                    // console.log(curColumn);

                    data.columns[index].size = width;

                    self.columns.inside(blockContent).eq(index)
                        .attr('data-size', width)
                        .removeClass()
                        .addClass(columnClass);
                }
            },

            // When someone hovers over the list box item we need to hover the active column
            "{listboxItem} mouseover": function(el, event) {
                var index = el.index();

                // Add hover state
                self.column.activate(index);
            },

            "{listboxItem} mouseout": function(el, event) {
                var index = el.index();

                self.column.deactivate(index);
            },

            "{control} listboxBeforeAddItem": function(el, ev, item) {
            },

            "{control} listboxAfterAddItem": function(el, ev, item) {
                blocks.data(currentBlock).columns = self.control().listbox('toData');
                self.column.create(1);
            },

            "{control} listboxBeforeRemoveItem": function(el, ev, item) {
                var index = item.index();

                self.column.remove(index);
            },

            "{control} listboxAfterRemoveItem": function(el, ev, item) {
                blocks.data(currentBlock).columns = self.control().listbox('toData');
            },

            "{controlWidth} change": function(el, event) {

                // Get the parent item
                var item = el.parents(self.listboxItem.selector);
                var index = item.index();
                var width = el.val();

                self.column.change(index, width);

                return;
            }

        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/compare", function($){

var module = this;

EasyBlog.require()
.library(
    "imgareaselect"
)
.done(function(){

    EasyBlog.Controller("Composer.Blocks.Handlers.Compare",
    {
        defaultOptions: {

            image: "[data-type=compare] img"
        }
    },
    function(self, opts, base, composer, blocks, meta, currentBlock) { return {

        init: function() {

            // Globals
            blocks       = self.blocks;
            composer     = blocks.composer;
            meta         = opts.meta;
            currentBlock = $();
        },

        toData: function(block) {
            return blocks.getData(block);
        },

        activate: function(block) {

            // Set as current block
            currentBlock = block;

            // Populate fielset
            self.populate(block);

            currentBlock.find("img")
                .on("click.compare dragstart.compare", function(event){
                    event.preventDefault();
                    event.stopPropagation();
                })
                .on("mousedown.compare", function(event){

                    if (self.drawing) return;

                    event.stopPropagation();
                    event.preventDefault();

                    // Get block
                    var image = $(this),
                        block = image.parents(".ebd-block:first")
                        images = block.find("img");

                    console.log(block);

                    self.drawing = true;
                    base.addClass("active");

                    // Initial cover position
                    var oy = parseInt(image.css("top")),
                        ox = parseInt(image.css("left")),
                        w = image.width() - image.parent().width();
                        h = image.height() - image.parent().height();

                    // Initial cursor position
                    var ix = event.pageX,
                        iy = event.pageY;

                    $(document)
                        .on("mousemove.compare mouseup.compare", function(event) {

                            if (!self.drawing) return;

                            var dx = (ix - event.pageX) * -1,
                                dy = (iy - event.pageY) * -1,
                                x = (w==0) ? 0 : ox + (dx || 0),
                                y = (h==0) ? 0 : oy + (dy || 0);

                            // Always stay within boundaries
                            // if (x > 0) x = 0; if (x > w * -1) x = w * -1;
                            // if (y > 0) y = 0; if (y > h * -1) y = h * -1;

                            images.css({
                                top: (oy = y),
                                left: (ox = x)
                            });

                            ix = event.pageX;
                            iy = event.pageY;
                        })
                        .on("mouseup.compare", function() {

                            $(document).off("mousemove.compare mouseup.compare");

                            block.removeClass("active");

                            self.drawing = false;
                        });
                })
        },

        construct: function(data) {
        },

        reconstruct: function(block) {
        },

        refocus: function(block) {
        },

        reset: function(block) {
        },

        populate: function(block) {
        },

        drawing: false,

        "{image} mousedown": function(image) {


        }

    }});

    module.resolve();
});

});
EasyBlog.module("composer/blocks/handlers/file", function($){

var module = this;

EasyBlog.Controller("Composer.Blocks.Handlers.File", {

    defaultOptions: {

        // Browse button in placeholder
        "{browseButton}": ".eb-composer-placeholder-file [data-eb-mm-browse-button]",

        "{fileError}": "[data-eb-file-error]",

        "{placeholder}": "[data-eb-composer-file-placeholder]",
        "{player}": "[data-file-preview]",
        "{dropElement}": "[data-plupload-drop-element]",

        // Fieldset options
        "{showIcon}": "[data-file-fieldset-icon]",
        "{showSize}": "[data-file-fieldset-size]",

        // Template parameters
        "{fileName}": "[data-file-name]",
        "{fileType}": "[data-file-type]",
        "{fileIcon}": "[data-file-icon]",
        "{fileSize}": "[data-file-size]",
        "{fileUrl}": "[data-file-url]"

    }
}, function(self, opts, base, composer, blocks, meta, currentBlock, mediaManager) {

    return {

        init: function() {

            // Globals
            blocks = self.blocks;
            composer = blocks.composer;
            meta = opts.meta;
            currentBlock = $();
            mediaManager = EasyBlog.MediaManager;
        },

        toData: function(block) {

            var data = blocks.data(block);
            return data;
        },

        toText: function(block) {
            return;
        },

        toHTML: function(block) {

            var data = blocks.data(block);
            if (!data.url) return "";

            var cloned = block.clone();
            var deconstructedBlock = self.deconstruct(cloned);
            var content = blocks.getBlockContent(deconstructedBlock);

            return content.html();
        },

        constructFromMediaFile: function(mediaFile) {

            var key = mediaFile.data("key");
            var uri = mediaManager.getUri(key);

            // Create block container first
            var block = blocks.createBlockContainer("file");
            var blockContent = blocks.getBlockContent(block);
            var data = blocks.data(block);

            // Add loading indicator
            block.addClass("is-loading");

            // Get media meta
            mediaManager.getMedia(uri)
                .done(function(media){

                    var mediaMeta = media.meta;

                    self.createPreview(block, mediaMeta.url, mediaMeta.title, mediaMeta.extension, mediaMeta.size);
                    // self.createPlayer(block, mediaMeta.url, mediaMeta.title);
                })
                .fail(function(){
                })
                .always(function(){
                    block.removeClass("is-loading");
                });

            return block;
        },

        toLegacyHTML: function(meta, block) {
            var data = blocks.data(block);

            var link = $('<a>').attr({
                                "href": meta.url
                            }).html(meta.title);

            return link.prop('outerHTML');
        },

        activate: function(block) {

            // Set as current block
            currentBlock = block

            // Populate fieldset
            self.populate(block);
        },

        deactivate: function(block) {

        },

        construct: function(data) {
            var block = blocks.createBlockContainer('file');
            var blockData = blocks.data(block);

            $.extend(blockData, data);

            return block;
        },

        reconstruct: function(block) {

            var data = blocks.data(block);
            var placeholder = self.placeholder.inside(block);
            var dropElement = self.dropElement.inside(placeholder);

            // If this is an edited item, we need to reconstruct the player again
            if (data.url) {
                self.createPreview(block, data.url, data.name, data.type, data.size);
            }

            // Register the placeholder with mediamanager
            if (dropElement.length > 0) {
                EasyBlog.MediaManager.uploader.register(placeholder);
            }
        },

        deconstruct: function(block) {
            var content = blocks.getBlockContent(block);

            self.fileName.inside(content)
                .editable(false);

            return block;
        },

        refocus: function(block) {
        },

        reset: function(block) {
        },

        populate: function(block) {

            // When populating the fieldset for a block, reset the values
            var data = blocks.data(block);

            // Update the fieldsets
            self.showIcon().val(data.showicon ? 1 : 0)
                .trigger('change');

            self.showSize().val(data.showsize ? 1 : 0)
                .trigger('change');
        },

        getPreviewTemplate: function() {
            var template = $(meta.preview);

            return $(meta.preview);
        },

        createPreview: function(block, url, fileName, fileType, fileSize) {

            var blockContent = blocks.getBlockContent(block);
            var template = self.getPreviewTemplate();
            var data = blocks.data(block);

            data.url = url;
            data.name = fileName;
            data.type = fileType;
            data.size = fileSize;

            // Set a temporary id to the preview container.
            template.attr('id', data.uid);

            // set filename
            self.fileName
                .inside(template)
                .text(data.name)
                .editable(true);

            // set filetype
            self.fileType
                .inside(template)
                .text(data.type);

            // set filesize
            var size = $.plupload2.formatSize(data.size);

            self.fileSize
                .inside(template)
                .text(size);

            self.fileUrl.inside(template)
                .attr('href', data.url)
                .on('click', function(event) {
                    event.preventDefault();
                });

            // Replace the placeholder with the preview's template
            blockContent.html(template);

        },

        "{showIcon} change": function(el, event) {

            var content = blocks.getBlockContent(currentBlock);
            var data = blocks.data(currentBlock);

            data.showicon = el.val() == 1 ? true : false;

            if (data.showicon) {
                // remove hide class
                self.fileIcon.inside(content)
                    .removeClass('hide');

            } else {
                // add hide class
                self.fileIcon.inside(content)
                    .addClass('hide');
            }
        },

        "{showSize} change": function(el, event) {

            var content = blocks.getBlockContent(currentBlock);
            var data = blocks.data(currentBlock);

            data.showsize = el.val() == 1 ? true : false;

            if (data.showsize) {
                // remove hide class
                self.fileSize.inside(content)
                    .parent('div').removeClass('hide');

            } else {
                // add hide class
                self.fileSize.inside(content)
                    .parent('div').addClass('hide');
            }
        },


        "{placeholder} mediaUploaderFilesAdded": function(placeholder, event, uploader, files) {
            EasyBlog.MediaManager.uploader.addItem(files[0], placeholder);
        },

        "{placeholder} mediaUploaderFileUploaded": function(placeholder, event, uploader, file, data) {

            var response = data.response;
            var mediaItem = response.media;
            var mediaMeta = mediaItem.meta;

            // Get the current block hosting the placeholder
            var block = blocks.block.of(placeholder);

            setTimeout(function() {
                self.createPreview(block, mediaMeta.url, mediaMeta.title, mediaMeta.extension, mediaMeta.size);
            }, 600);
        },

        "{placeholder} mediaUploaderFileError": function(placeholder, event, uploader, error) {

            if (error.code == $.plupload2.FILE_EXTENSION_ERROR) {
                self.fileError.inside(currentBlock).removeClass('hide');
            }
        },

        "{placeholder} mediaUploaderError": function(placeholder, event, uploader, error) {
        },

        "{self} mediaInfoDisplay": function(el, event, info, media) {

            if (media.meta.type != 'file') {
                return;
            }

            // We need to disable the download link in the info.
            $(info).find('[data-file-url]').on('click', function(event) {
                event.preventDefault();
            })
        },


        "{browseButton} mediaSelect": function(browseButton, event, media) {

            var block = blocks.block.of(browseButton);

            if (media.meta.type != "file") {
                return;
            }

            var mediaMeta = media.meta;
            var composerDocument = composer.document;
            var isLegacy = composerDocument.isLegacy();

            // Legacy
            if (isLegacy) {
                content = self.toLegacyHTML(block);
                composerDocument.insertContent(content);

            // EBD
            } else {
                self.createPreview(block, mediaMeta.url, mediaMeta.title, mediaMeta.type, mediaMeta.size);
            }
        },

        "{self} mediaInsert": function(el, event, media, block) {

            if (media.meta.type != 'file') {
                return;
            }

            var composerDocument = composer.document;
            var isLegacy = composerDocument.isLegacy();

            if (isLegacy) {
                content = self.toLegacyHTML(media.meta, block);
                composerDocument.insertContent(content);
            } else {
                // Construct a new post block and insert into the document
                var block = blocks.constructBlock('file', {
                    "name": media.meta.title,
                    "type": media.meta.extension,
                    "size": media.meta.size,
                    "url": media.meta.url
                });

                blocks.addBlock(block);
                blocks.activateBlock(block);
            }
        }
    }
});

module.resolve();

});

EasyBlog.module("composer/blocks/handlers/gist", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Gist", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-gist-form]",
            "{insert}": "[data-gist-insert]",
            "{source}": "[data-gist-source]",

            // Preview
            "{preview}": "[data-gist-preview]",

            //fieldset
            "{fsSource}": "[data-fs-gist-source]",
            "{fsRefreshButton}": "[data-fs-gist-refresh]",
            "{errorMessage}": "[data-gist-error]"
        }, EBD.selectors)
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toText: function(block) {
                var data = blocks.data(block);

                return data.source;
            },

            toEditableHTML: function(block) {
                return '';
            },

            toHTML: function(block) {
                // We need to get the data from the overlay instead
                var data = blocks.data(block);
                var overlay = block.data('overlay');

                if (overlay) {
                    return overlay.element().html();
                }
            },

            activate: function(block) {
                var content = blocks.getBlockContent(block);
                var data = blocks.data(block);

                // Set as current block
                currentBlock = block;

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function(block) {
            },

            construct: function(block) {
            },

            reconstruct: function(block) {
                var data = blocks.data(block);
                var overlay = block.data('overlay');
                var content = blocks.getBlockContent(block);

                // If this is an edited post which has url to the gist, we need to attach the overlay again if it doesn't exist yet.
                if (data.source && !overlay) {
                    self.createIframe(block, data.source);
                } else {
                    content.html(meta.html);
                }

                // So redactor won't receive it.
                block.find(self.source.selector).off("paste").on("paste", function(event){
                    event.stopPropagation();
                });
            },

            deconstruct: function(block) {
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block)  {
                // When populating the fieldset for a block, reset the values
                var data = blocks.data(block);

                // Update the url in fieldset
                self.fsSource().val(data.source);
            },

            setOverlay: function(block, embed) {

                var overlay = block.data('overlay');

                if (!overlay) {
                    // Overlay element stores the real html stuffs for the block
                    var overlay = composer.document.overlay.create(block);
                    var content = blocks.getBlockContent(block);

                    // Overlay placeholder is just a placeholder so that the overlay element can be displayed within the placeholder region
                    // Append the placeholder first
                    overlay
                        .placeholder()
                        .css('height', '400px')
                        .appendTo(content);

                    // Append the embed codes now
                    overlay.element().append(embed);

                    // Attaching is just like execute.
                    // Attach the overlay now
                    overlay.attach();
                } else {
                    overlay.element().empty();
                    overlay.element().append(embed);
                }

                // Set the overlay data so we don't create overlays all the time
                block.data('overlay', overlay);
            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // https://gist.github.com/imarklee/49c07340f22122b384e1
                var regex = /^https:\/\/gist\.github\.com\/(.*)\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            createIframe: function(block, url) {

                // Create an iframe, append it to this document where specified
                var iframe = document.createElement('iframe');
                var content = blocks.getBlockContent(block);
                var data = blocks.data(block);

                data.source = url;

                // Set the iframe attributes
                iframe.setAttribute('width', '100%');
                iframe.id = 'gistFrame';

                // Create the necessary overlays
                self.setOverlay(block, iframe);

                var callback = $.callback(function(height) {
                    height += 'px';

                    content.find('iframe').css('height', height);
                });

                // Create the iframe's document
                var html = '<html><body onload="parent.' + callback + '(document.body.scrollHeight);"><scr' + 'ipt type="text/javascript" src="' + url + '.js"></sc'+'ript></body></html>';

                // Set iframe's document with a trigger for this document to adjust the height
                var doc = iframe.document;

                if (iframe.contentDocument) {
                    doc = iframe.contentDocument;
                } else if (iframe.contentWindow) {
                    doc = iframe.contentWindow.document;
                }

                doc.open();
                doc.writeln(html);
                doc.close();

                return iframe;
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                // Add overlay when the user add's a new link.
                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();
                var data = blocks.data(currentBlock);

                // Ensure that the url is valid
                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return;
                }

                // Update the fieldset url
                self.fsSource().val(url);

                // Create an iframe, append it to this document where specified
                self.createIframe(currentBlock, url);

                // Hide the form
                self.form.inside(content).addClass('hidden');
            },

            "{fsRefreshButton} click": function() {

                var content = blocks.getBlockContent(currentBlock);
                var url = self.fsSource().val();
                var data = blocks.data(currentBlock);

                // Verify the source url
                if (!self.isUrlValid(url)) {
                    self.errorMessage.inside(content).removeClass('hide');
                    return;
                }

                // Create an iframe, append it to this document where specified
                self.createIframe(currentBlock, url);

                // Hide the form
                self.form.inside(content).addClass('hidden');
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/heading", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Heading", {
        defaultOptions: {
            "{headingElement}": "h1, h2, h3, h4, h5, h6",
            "{levelSelection}": "[data-eb-composer-block-heading-level] [data-level]"
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {

                // Globals
                blocks       = self.blocks;
                composer     = blocks.composer;
                meta         = opts.meta;
                currentBlock = $();
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function() {

            },

            construct: function(data)
            {
                return $.create(data.level).html(data.content);
            },

            reconstruct: function(block) {
            },

            deconstruct: function(block, clone) {

                if (clone) {
                    block = block.clone();
                }

                // Stop making it editable to prevent the output to be editable
                self.headingElement
                    .inside(block)
                    .editable(false);

                return block;
            },

            refocus: function(block) {

                // Get heading
                var heading = self.headingElement.inside(block);

                // Focus on heading
                heading.focus();

                // If block is new
                if (block.hasClass("is-new")) {

                    // Set caret at the end of heading
                    composer.editor.caret.setEnd(heading[0]);
                }
            },

            reset: function(block) {

                block.html(meta.content);
            },

            populate: function(block) {

                // Get level
                var level = self.level(block);

                // Update fieldset
                self.levelSelection()
                    .where("level", level)
                    .activateClass("active");
            },

            toData: function(block) {
                return blocks.getData(block);
            },

            toHTML: function(block) {
                var block = blocks.getBlockContent(block);

                return self.deconstruct(block, true).html();
            },

            toText: function(block) {
                return block.text();
            },

            heading: {

                inside: function(block) {
                    return block.children(":first");
                }
            },

            // This is an internal function
            // and should not be called externally.
            level: function(block, level) {

                var blockContent = blocks.getBlockContent(block),
                    heading = self.headingElement.inside(blockContent),
                    currentLevel = heading.tagName();

                if (level && currentLevel!==level) {

                    // Construct new block content
                    blockContent.html(self.construct({
                        level: level,
                        content: heading.html()
                    }));

                    // Make heading editable
                    self.headingElement.inside(block)
                        .editable(true);

                    // Update current level
                    currentLevel = level;
                }

                return currentLevel;
            },

            setLevel: function(block, level) {

                var level = self.level(block, level),
                    heading = self.headingElement.inside(block);

                // Trigger necessary events
                var args = [block, self, heading];
                self.trigger("composerBlockHeadingSetLevel", args);
                self.trigger("composerBlockChange", args);
            },

            previewLevel: function(block, level) {

                clearTimeout(self.previewTimer);

                // Get heading and level
                var heading = self.headingElement.inside(block),
                    originalLevel = heading.tagName();

                // Remember the original level before it was switched
                block
                    .defineData("originalLevel", originalLevel)
                    .addClass("is-preview");

                // Set heading level
                self.level(block, level);

                // Trigger necessary events
                var args = [block, self, heading];
                self.trigger("composerBlockHeadingPreviewLevel", args);
                self.trigger("composerBlockChange", args);
            },

            previewTimer: null,

            "{levelSelection} mouseover": function(levelSelection) {

                // Set heading level to the one being hovered on
                var level = levelSelection.data("level");

                // Preview level on current block
                self.previewLevel(currentBlock, level);
            },

            "{levelSelection} mouseout": function(levelSelection) {

                clearTimeout(self.previewTimer);

                // Delay before reverting to original level
                self.previewTimer = setTimeout(function () {

                    var originalLevel = currentBlock.data("originalLevel");

                    if (originalLevel) {
                        self.setLevel(currentBlock, originalLevel);
                    }

                }, 50);
            },

            "{levelSelection} click": function(levelSelection) {

                // Get level from level selection
                var level = levelSelection.data("level");

                // Set level on current block
                self.setLevel(currentBlock, level);

                // Refocus on heading
                self.refocus(currentBlock);
            },

            "{self} composerBlockHeadingSetLevel": function(base, event, block, handler, heading) {

                // Stop any preview timer
                clearTimeout(self.previewTimer);

                // Remove original level
                block
                    .removeClass("is-preview")
                    .removeData("originalLevel");

                // Repopulate fieldset if block is current block
                if (block.is(currentBlock)) {
                    self.populate(block);
                }
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/html", function($) {

    var module = this;

    EasyBlog.require()
    .library('ace')
    .done(function($) {

        EasyBlog.Controller("Composer.Blocks.Handlers.Html", {
            defaultOptions: {

                "{pre}": "[data-eb-composer-blocks-html-pre]"
            }
        }, function(self, opts, base, composer, blocks, meta, currentBlock) {

            return {

                init: function() {
                    // Globals
                    blocks = self.blocks;
                    composer = blocks.composer;
                    meta = opts.meta;
                    currentBlock = $();
                },

                activate: function(block) {
                    // Set as current block
                    // currentBlock = block

                    // Populate fieldset
                    self.populate(block);
                },

                deactivate: function(block) {
                    var blockContent = blocks.getBlockContent(block);

                    var contents = blockContent.html();

                    // If this is an empty content, we need to populate the placeholder text again.
                    if (contents == "") {
                        blockContent.html(meta.html);
                    }
                },

                construct: function(block) {
                },

                reconstruct: function(block) {
                },

                deconstruct: function(block) {

                },

                refocus: function(block) {
                },

                reset: function(block) {
                },

                toText: function(block) {

                    var data = blocks.data(block);
                    var clone = block.clone();
                    var content = blocks.getBlockContent(clone);

                    var text = content.text();

                    return text;
                },

                toData: function(block) {

                    var data = blocks.data(block);

                    return data;
                },

                toHTML: function(block) {

                    var data = blocks.data(block);
                    var clone = block.clone();
                    var content = blocks.getBlockContent(clone);

                    // Sanitize the html on the form
                    var editor = self.editor();
                    var session = editor.getSession();

                    session.setValue(content.html());

                    return content.html();
                },

                editor: $.memoize(function() {

                    // Setup ACE Editor
                    var pre = composer.find(self.pre)[0];


                    // There could be instances where the <pre> isn't loaded yet.
                    if (pre == undefined) {
                        pre = $.create('pre')[0];
                    }

                    var editor = ace.edit(pre);

                    // Configure editor
                    editor.setTheme("ace/theme/github");

                    // Set syntax highlighter to HTML
                    var session = editor.getSession();
                    
                    // Set the default mode to html
                    session.setMode("ace/mode/html");

                    // Automatically update html preview
                    // when user types on the editor.
                    session.on("change", $.debounce(self.sync, 150));

                    return editor;
                }),

                populate: function(block) {
                    var editor = self.editor();
                    var session = editor.getSession();
                    var blockContent = blocks.getBlockContent(block);
                    var html = $.trim(blockContent.html());

                    // Set the html value into the editor
                    session.setValue(html);

                    // Set current block
                    currentBlock = block;
                },

                sync: function() {
                    var editor = self.editor(composer);
                    var session = editor.getSession();
                    var blockContent = blocks.getBlockContent(currentBlock);
                    var html = session.getValue();

                    blockContent.html(html);
                },

                reset: function(block) {
                }
            }
        });

        module.resolve();
    });
});

EasyBlog.module("composer/blocks/handlers/image", function($){

var module = this;

var selectRatioView = "view-selectratio";
var customRatioView = "view-customratio";

var fitOrFill = /fit|fill/;
var isFluid = "is-fluid";
var isLoading = "is-loading";
var isFailed = "is-failed";
var isDifferent = "is-different";
var invalidVariationError = "Variation could not be retrieved because media meta does not exist in library!";

var imageSizeProps = [
    "image-width",
    "image-height"
];

var numsliderElements = [
    "numslider",
    "numslider-toggle",
    "numslider-widget",
    "numslider-value",
    "numslider-input",
    "numslider-units",
    "numslider-unit",
    "numslider-current-unit"
];

// Helpers
function getCssProp(prop) {
    return prop.replace(/image-/,"");
}

function parseUnit(val) {
    return val.toString().match("%") ? "%" : "px";
};

function roundToDecimalPoint(value, decimalPlace) {
    var p = Math.pow(10, decimalPlace);
    return Math.round(value * p) / p;
};

function ratioDecimal(ratio) {
    // If decimal was given, just return the ratio.
    if ($.isNumeric(ratio)) return ratio;
    var parts = ratio.split(":");
    return parts[0] / parts[1];
};

function ratioPercent(ratio, unit, decimalPlace) {
    return roundToDecimalPoint(ratioDecimal(ratio) * 100, decimalPlace || 3) + (unit ? "%" : 0);
};

function ratioPadding(ratio, decimalPlace) {
    return roundToDecimalPoint(1 / ratioDecimal(ratio) * 100, decimalPlace || 3) + "%";
};

function decimalToPercent(val, decimalPlace) {
    return roundToDecimalPoint((val * 100), decimalPlace || 3) + "%";
};

function sanitizeRatio(ratio) {
    ratio = $.trim(ratio);
    if (/\:/.test(ratio)) {
        var parts = ratio.split(":");
        return parseInt(parts[0]) + ":" + parseInt(parts[1]);
    } else {
        return parseFloat(ratio) || 0;
    }
};

function setCSSWidth(el, val) {
    return el.css("width", val);
}

function setCSSTop(el, val) {
    return el.css("top", val);
}

function setCSSLeft(el, val) {
    return el.css("left", val);
}

function setCSSPaddingTop(el, val) {
    return el.css("padding-top", val);
}

function setCSSHeight(el, val) {
    return el.css("height", val);
}

var resizeToFit  = $.Image.resizeWithin;
var resizeToFill = $.Image.resizeToFill;

EasyBlog.require()
.library(
    "plupload2",
    "imgareaselect"
)
.done(function(){

EasyBlog.Controller("Composer.Blocks.Handlers.Image", {

    elements: [

        // Placeholder
        "[data-eb-composer-image-{placeholder|browse-button}]",

        // URL
        "[data-eb-image-{url-fieldset|url-field|url-field-text|url-field-update-button}]",

        // Source
        "[data-eb-image-{source-fieldset|source-field|source-thumbnail|source-title|source-size|source-url|source-change-button}]",

        // Source > Variation
        "[data-eb-image-{variation-field|variation-list-container|variation-new-button|variation-create-button|variation-rebuild-button|variation-delete-button|variation-cancel-button|variation-cancel-failed-button}]",
        "[data-eb-image-source-fieldset] [data-eb-mm-{variation-list|variation-list-item-group|variation-item}]",
        "[data-eb-image-{variation-name|variation-width|variation-height}]",

        // Size
        "[data-eb-{image-size-fieldset|image-size-simple-field|image-size-advanced-field}]",
        "^imageSize [data-eb-image-size-{preset-toggle|current-preset|preset|retry-button}]",

        // Size > Dimensions
        "[data-eb-image-size-fieldset] [data-eb-{" + numsliderElements.join("|") + "}]",
        "^imageSizeField .eb-composer-field[data-name={" + imageSizeProps.join("|") + "}]",

        // Size > Alignment
        "[data-eb-image-{alignment-selection}]",

        // Size > Ratio
        "[data-eb-image-{ratio-lock|ratio-button|ratio-label|ratio-customize-button|ratio-cancel-button|ratio-use-custom-button|ratio-cancel-custom-button|ratio-selection|ratio-preview|ratio-input}]",

        // Size > Advanced
        "[data-eb-image-{map|map-container|map-figure|map-viewport|map-preview|map-picker}]",
        "[data-eb-image-{strategy-menu-item|strategy-menu-content}]",
        "[data-eb-image-{resize-input-field|resize-ratio-lock|resize-reset-button}]",

        // Style
        "[data-eb-image-{style-toggle|style-selection}]",

        // Caption
        "[data-eb-image-{caption-toggle|caption-text-field}]",

        // Link
        "[data-eb-image-{link-toggle}]",
        "[data-eb-image-link-fieldset] [data-eb-{link-url-field|link-title-field|link-blank-option}]",

        // Popup
        "[data-eb-image-{popup-toggle|popup-fieldset|popup-field|popup-thumbnail|popup-title|popup-size|popup-url|popup-change-button}]",
        "[data-eb-image-{popup-variation-field|popup-variation-list-container}]",
        "^popup [data-eb-image-popup-fieldset] [data-eb-mm-{variation-list|variation-list-item-group|variation-item}]",
    ],

    defaultOptions: $.extend({

        uploader: {
            runtimes: "html5,flash",
            url: "/echo/json",
            max_file_size: '10mb',
            filters: [
                {
                    title: "Image files",
                    extensions: "jpg,gif,png"
                }
            ]
        },

        "{browseButton}": ".eb-composer-placeholder-image [data-eb-mm-browse-button]",
        "{imagePlaceholder}": "[data-eb-composer-image-placeholder]",
        "{imageContainer}": ".eb-image",
        "{imageFigure}": ".eb-image-figure",
        "{imageViewport}": ".eb-image-viewport",
        "{imageElement}": ".eb-image-figure img",
        "{imagePopupButton}": ".eb-image-popup-button",
        "{imageCaption}": ".eb-image-caption",
        "{imageCaptionText}": ".eb-image-caption > span",
        "{imageHint}": ".eb-image-hint",

        // via url
        "{imageUrlForm}" : "[data-eb-image-url-form]",
        "{imageUrlTextbox}" : "[data-eb-image-url-textbox]",
        "{imageUrlAdd}" : "[data-eb-image-url-add]",
        "{imageUrlCancel}" : "[data-eb-image-url-cancel]"

    }, EBD.selectors)
},
function(self, opts, base, composer, blocks, meta, currentBlock, mediaManager, panelsFieldset) {

    function isImageBlock(block) {
        return blocks.getBlockType(block)=="image";
    }

    return {

    init: function() {

        // Globals
        blocks       = self.blocks;
        composer     = blocks.composer;
        meta         = opts.meta;
        currentBlock = $();
        mediaManager = EasyBlog.MediaManager;
        panelsFieldset = composer.panels.fieldset;

        // INTERNAL HACK
        // Duckpunch .of() to accept prop
        $.each(numsliderElements, function(i, element){

            var method = $.camelize(element);
            var cache = {};

            self[method].of = function(prop){

                var numsliderElement = cache[prop];

                if (!numsliderElement) {
                    // Get numslider field of this prop and return
                    // numslider element under this numslider field
                    var numsliderField = self.getImageSizeField(prop);
                    numsliderElement = self[method].under(numsliderField);

                    if (numsliderElement.length) {
                        cache[prop] = numsliderElement;
                    }
                }

                return numsliderElement;
            }
        });
    },

    deactivate: function(block) {

    },

    activate: function(block) {

        // Set as current block
        currentBlock = block;

        // Always center align block
        if (block.hasClass("is-new")) {
            block.css("text-align", "center");
        }

        // Populate fieldset
        self.populate(block);
    },

    construct: function(data) {

        var block = blocks.createBlockContainer("image");
        var data = $.extend(blocks.data(block), data);

        var blockContent = blocks.getBlockContent(block).empty();
        var imageContainer = self.constructImage(data);

        // Append image container to block content
        blockContent.append(imageContainer);

        // Always center align block
        block.css("text-align", "center");

        return block;
    },

    constructFromMediaFile: function(mediaFile) {

        var key = mediaFile.data("key");
        var uri = mediaManager.getUri(key);

        // Create block container first
        var block = blocks.createBlockContainer("image");
        var blockContent = blocks.getBlockContent(block);
        var data = blocks.data(block);

        // Always center align block
        block.css("text-align", "center");

        // Add loading indicator
        block.addClass("is-loading");

        // Get media meta
        mediaManager.getMedia(uri)
            .done(function(media){

                // Get meta and variation
                var mediaMeta = media.meta;
                var variation = mediaManager.getVariation(mediaMeta.uri, "thumbnail");

                data.uri = mediaMeta.uri;
                data.url = variation.url;
                data.simple = 'simple';
                data.ratio_lock = true;
                data.variation = variation.key;

                var imageContainer = self.constructImage(data);

                // Append image container to block content
                blockContent.html(imageContainer);

                // If this block is still active, populate image block
                if (block.hasClass("active")) {
                    self.populate(block);
                }
            })
            .fail(function(){

                // If unable to get media meta, revert block to placeholder.
                var imagePlaceholder = $(meta.html);
                imagePlaceholder.addClass("state-failed"); // TODO: Might need another failed state for failed media file
                blockContent.html(imagePlaceholder);
            })
            .always(function(){
                block.removeClass("is-loading");
            });

        return block;
    },

    constructImage: function(data) {

        var imageContainer = $(meta.imageContainer);
        var imageFigure = self.imageFigure.inside(imageContainer);

        // Set image src
        var imageElement = self.imageElement.inside(imageContainer);
        imageElement.attr("src", data.url);

        // Add is-fluid class if necessary
        if (data.fluid) {
            imageContainer.addClass(isFluid);
        }

        // Set image style
        imageContainer
            .addClass("style-" + data.style);

        // Set image caption
        if (data.caption_text) {

            imageContainer
                .append(meta.imageCaption);

            self.imageCaptionText.inside(imageContainer)
                .html(data.caption_text);
        }

        // Set image popup
        if (data.popup_url) {
            var imagePopupButton = $(meta.imagePopupButton);

            imagePopupButton
                .attr("href", data.popup_url);

            imageFigure.append(imagePopupButton);
        }

        return imageContainer;
    },

    toData: function(block) {

        var data = blocks.data(block);

        return data;
    },

    toHTML: function(block) {

        if (!self.hasImage(block)) {
            return "";
        }

        var clone = block.clone();
        var deconstructedBlock = self.deconstruct(clone);
        var content = blocks.getBlockContent(deconstructedBlock);

        return content.html();
    },

    toLegacyHTML: function(block) {

        var data = self.toData(block);

        var image =
            $("<img>")
                .attr({
                    src: data.url,
                    width: data.width,
                    height: data.height
                });

        // If this image has caption, add caption text
        if (data.caption_text) {
            image
                .addClass("easyblog-image-caption")
                .attr("title", data.caption_text);
        }

        // If this image has popup, add data-popup attribute
        if (data.popup_url) {
            image.attr("data-popup", data.popup_url);
        }

        if (data.style) {
            image.attr("data-style", data.style);
        }

        // If this image has link, wrap in link.
        if (data.link_url) {

            var imageLink =
                $("<a>")
                    .attr({
                        href: data.link_url,
                        title: data.link_title,
                        target: data.link_target
                    })
                    .append(image);

            return imageLink.prop("outerHTML");
        }

        return image.prop("outerHTML");
    },

    toText: function(block) {

        var captionText = self.imageCaptionText.inside(block).text();
        var altText = self.imageElement.inside(block).attr("alt");

        return captionText + "\n" + altText;
    },

    reconstruct: function(block) {

        var imagePlaceholder = self.imagePlaceholder.inside(block);

        if (imagePlaceholder.length > 0) {
            EasyBlog.MediaManager.uploader.register(imagePlaceholder);
        }
    },

    hasImage: function(block) {

        return self.imageContainer.inside(block).length > 0;
    },

    "{imagePlaceholder} mediaUploaderFilesAdded": function(imagePlaceholder, event, uploader, files) {

        EasyBlog.MediaManager.uploader.addItem(files[0], imagePlaceholder);
    },

    "{imagePlaceholder} mediaUploaderFileUploaded": function(imagePlaceholder, event, uploader, file, data) {

        var response = data.response;
        var mediaItem = response.media;
        var mediaMeta = mediaItem.meta;

        var block = blocks.block.of(imagePlaceholder);

        setTimeout(function(){
            self.updateImageSource(block, mediaMeta);
        }, 600);
    },

    "{browseButton} mediaSelect": function(browseButton, event, media) {

        var block = blocks.block.of(browseButton);

        if (media.meta.type!="image") {
            return;
        }

        var mediaMeta = media.meta;

        var composerDocument = composer.document;
        var isLegacy = composerDocument.isLegacy();

        // Legacy
        if (isLegacy) {
            content = self.toLegacyHTML(block);
            composerDocument.insertContent(content);

        // EBD
        } else {

            self.setImageSource(block, mediaMeta);

            // Always center align block
            // block.css("text-align", "center");
        }
    },

    "{popupChangeButton} mediaSelectStart": function(popupChangeButton, event, media) {
        var uid = currentBlock.data("uid");

        // Set the block's uid so that we can retrieve it later when the media is selected
        popupChangeButton.data("uid", uid);
    },

    "{popupChangeButton} mediaSelect": function(popupChangeButton, event, media) {

        if (media.meta.type!="image") {
            return;
        }

        var currentUid = currentBlock.data('uid');
        var targetUid = popupChangeButton.data('uid');
        var mediaMeta = media.meta;
        var composerDocument = composer.document;
        var isLegacy = composerDocument.isLegacy();

        if (currentUid != targetUid) {
            var block = blocks.getBlock(targetUid);

            self.setImagePopup(block, mediaMeta.uri);
        } else {
            self.updateImagePopup(currentBlock, mediaMeta.uri);
        }
    },

    "{sourceChangeButton} mediaSelectStart": function(sourceChangeButton) {
        var uid = currentBlock.data("uid");

        // Set the block's uid so that we can retrieve it later when the media is selected
        sourceChangeButton.data("uid", uid);
    },

    "{sourceChangeButton} mediaSelect": function(sourceChangeButton, event, media) {

        if (media.meta.type!="image") {
            return;
        }

        var currentUid = currentBlock.data('uid');
        var targetUid = sourceChangeButton.data('uid');
        var mediaMeta = media.meta;
        var composerDocument = composer.document;
        var isLegacy = composerDocument.isLegacy();

        // Legacy
        if (isLegacy) {
            content = self.toLegacyHTML(block);
            composerDocument.insertContent(content);

        // EBD
        } else {

            if (currentUid != targetUid) {
                var block = blocks.getBlock(targetUid);

                self.setImageSource(block, mediaMeta);
            } else {
                self.updateImageSource(currentBlock, mediaMeta);
            }
        }
    },

    "{self} mediaInsert": function(el, event, media, block) {

        if (media.meta.type!="image") {
            return;
        }

        var composerDocument = composer.document;
        var isLegacy = composerDocument.isLegacy();

        // Normalize image size first
        self.normalizeImageSize(block);

        // Legacy
        if (isLegacy) {
            content = self.toLegacyHTML(block);
            composerDocument.insertContent(content);

        // EBD
        } else {
            var data = blocks.data(block);
            var block = blocks.constructBlock("image", data);
            blocks.addBlock(block);
            blocks.activateBlock(block);
        }
    },

    deconstruct: function(block) {

        // Nothing to deconstruct
        return block;
    },

    refocus: function(block) {
    },

    reset: function(block) {
    },

    populate: function(block) {

        var hasImage = self.hasImage(block);

        // Hide fieldgroup if there is no video
        var fieldgroup = blocks.panel.fieldgroup.get("image");
        fieldgroup.toggleClass("is-new", !hasImage);

        if (hasImage) {
            self.populateImageUrl(block);
            self.populateImageSource(block);
            self.populateImageSize(block);
            self.populateImageCaption(block);
            self.populateImageLink(block);
            self.populateImagePopup(block);
            self.populateImageStyle(block);
        }
    },

    //
    // Image Hint
    //
    showImageHint: function(block, content) {

        // Remove existing image hint
        self.imageHint.inside(block).remove()

        // Create image hint
        var imageHint = $(meta.imageHint);

        // Set image hint content
        imageHint.find(".eb-hint-text").html(content);

        // Get image viewport
        var imageViewport = self.imageViewport.inside(block);

        // Append image hint to image viewport
        imageViewport.append(imageHint);

        // This will initiate the slow-fading effect
        imageHint
            .removeClassAfter("is-new", 1000);

        // This will remove the image hint after 1.5s
        setTimeout(function(){
            imageHint.remove();
        }, 2500);
    },

    //
    // Image Source
    //
    setImageSource: function(block, mediaMeta) {

        //solo33/images/easyblog_images/605/b2ap3_icon_15_20150303-094940_1.jpg
        var data = blocks.data(block);

        var variation = mediaManager.getVariation(mediaMeta.uri, "thumbnail");

        // Set data from variation
        data.variation = variation.key;
        data.url = variation.url;
        data.uri = mediaMeta.uri;
        data.natural_width = variation.width;
        data.natural_height = variation.height;
        data.natural_ratio = variation.width / variation.height;

        // Construct image container
        var imageContainer = self.constructImage(data);

        // Append image container to block content
        blocks.getBlockContent(block)
            .empty()
            .append(imageContainer);
    },

    updateImageSource: function(block, mediaMeta) {

        self.setImageSource(block, mediaMeta);
        self.populate(block);
    },

    getSourceThumbnailImage: function(url) {

        return $("<img>").attr("src", url)[0];
    },

    populateImageUrl: function(block) {
        var data = blocks.data(block);

        var urlFieldset = self.urlFieldset();
        urlFieldset.removeClass('hidden');

        if (! data.isurl) {
            urlFieldset.addClass('hidden');
            return;
        }

        self.urlFieldText().val(data.url);
    },

    populateImageSource: function(block) {

        var data = blocks.data(block);

        var sourceFieldset = self.sourceFieldset();
        sourceFieldset.removeClass('hidden');

        if (data.isurl) {
            sourceFieldset.addClass('hidden');
            return;
        }

        var uri = data.uri;

        var sourceFieldset = self.sourceFieldset();

        sourceFieldset.addClass(isLoading);


        mediaManager.getMedia(uri)
            .done(function(media){

                // Get media meta
                var mediaMeta = media.meta;

                // Source thumbnail image
                var sourceThumbnailImage = self.getSourceThumbnailImage(mediaMeta.thumbnail);
                self.sourceThumbnail()
                    .empty()
                    .append(sourceThumbnailImage);

                // Source title
                self.sourceTitle()
                    .text(mediaMeta.title);

                // Get variation list
                var variationList = $(media.variations);

                // Append variation list to container
                self.variationListContainer()
                    .empty()
                    .append(variationList);

                self.populateImageVariation(block);
            })
            .fail(function(){
                sourceFieldset.addClass(isFailed);
            })
            .always(function(){
                sourceFieldset.removeClass(isLoading);
            });


    },

    populateImageVariation: function(block) {

        var data = blocks.data(block);
        var uri = data.uri;
        var variationKey = data.variation;
        var variation = mediaManager.getVariation(uri, variationKey);

        // If meta could not be retrieved, stop and throw error.
        if (!variation) {
            console.error(invalidVariationError);
            return;
        };

        // Activate the correct variation item
        var variationItem =
            self.variationItem()
                .where("key", '"' + variationKey + '"')
                .activateClass("active");

        // Variation Size
        self.sourceSize()
            .text($.plupload2.formatSize(variation.size || ""));

        // Variation Url
        self.sourceUrl()
            .text(variation.url || "");

        // Toggle variation delete button
        var isSystem = variationItem.hasClass("is-system");
        var isMissing = variationItem.hasClass("is-missing");

        self.variationField()
            .toggleClass("can-delete", !isSystem)
            .toggleClass("is-missing", isMissing);
    },

    setImageVariation: function(block, variationKey) {

        // Get block data
        var data = blocks.data(block);
        var uri = data.uri;
        var variation = mediaManager.getVariation(uri, variationKey);

        // If meta could not be retrieved, stop and throw error.
        if (!variation) return (EasyBlog.debug && console.error(invalidVariationError));

        // Set url to variation on image element
        self.imageElement.inside(block)
            .attr("src", variation.url);

        // Show hint
        self.showImageHint(block, $.String.capitalize(variation.name) + '<br/><span style="font-weight: normal;">' + variation.width + "x" + variation.height + '</span>');

        // Store variation key in block data
        data.variation = variationKey;

        // we need to set the data.url as well
        data.url = variation.url;
        // data.uri = variation.uri;
    },

    updateImageVariation: function(block, variationKey) {

        self.setImageVariation(block, variationKey);
        self.populateImageVariation(block);
    },

    "{variationItem} click": function(variationItem) {

        var variationKey = variationItem.data("key");

        self.updateImageVariation(currentBlock, variationKey);
    },

    "{variationNewButton} click": function(variationNewButton) {

        self.variationField()
            .addClass("show-create-form");

        var data = blocks.data(currentBlock);
        var uri = data.uri;

        // Get original variation
        var variation = mediaManager.getVariation(uri, "system/original");

        // Set default input values based on selected variation
        var width = variation.width;
        var height = variation.height;
        var ratio = width / height;

        self.variationWidth()
            .val(width)
            .data("value", width)
            .data("ratio", ratio);

        self.variationHeight()
            .val(height)
            .data("value", height)
            .data("ratio", ratio);
    },

    "{variationCancelButton} click": function(variationCancelButton) {

        self.variationField()
            .removeClass("show-create-form");
    },

    "{variationCreateButton} click": function(variationCreateButton) {

        // Get variation field
        var variationField = self.variationField();

        // Get variation name, width & height
        var name = $.trim(self.variationName().val());
        var width = self.variationWidth().val();
        var height = self.variationHeight().val();

        // Do not create when name is blank
        if ($.trim(name)=="") {
            return;
        }

        // Show loading indicator
        variationField
            .addClass("is-creating");

        var block = currentBlock;
        var data = blocks.data(block);
        var uri = data.uri;

        // Make an ajax call to create variation
        mediaManager.createVariation(uri, name, width, height)
            .done(function(media){

                // Get variation list
                var variationList = $(media.variations);

                // Append variation list to container
                self.variationListContainer()
                    .empty()
                    .append(variationList);

                // Populate image variation
                self.populateImageVariation(block);

                variationField
                    .removeClass("show-create-form");
            })
            .fail(function(){

                variationField
                    .removeClass("is-creating")
                    .addClass("is-failed");
            })
            .always(function(){

                variationField
                    .removeClass("is-creating");
            });
    },

    "{variationCancelFailedButton} click": function() {

        self.variationField()
            .removeClass("is-failed");
    },

    "{variationRebuildButton} click": function(variationRebuildButton) {

        var data = blocks.data(currentBlock);
        var uri = data.uri;

        var activeVariation = self.variationItem(".active");
        var variationKey = activeVariation.data("key");

        var variation = mediaManager.getVariation(uri, variationKey);
        var name = variation.name;
        var key = mediaManager.getKey(uri);

        // Make an ajax call to rebuild selected variation
        EasyBlog.ajax('site/views/mediamanager/rebuildVariation', {
            "name": name,
            "key": key
        }).done(function(media) {
                // Get variation list
                var variationList = $(media.variations);

                // Append variation list to container
                self.variationListContainer()
                    .empty()
                    .append(variationList);

                // Populate image variation
                self.populateImageVariation(currentBlock);

                // Update cache with update media object
                mediaManager.setMedia(uri, media);
        })
    },

    "{variationDeleteButton} click": function(variationDeleteButton) {

        var data = blocks.data(currentBlock);
        var uri = data.uri;
        var activeVariation = self.variationItem(".active");
        var variationKey = activeVariation.data("key");

        var variation = mediaManager.getVariation(uri, variationKey);
        var variationName = variation.name;

        mediaManager.removeVariation(uri, variationName)
            .done(function(media){

                // Append variation list to container
                var variationList = $(media.variations);
                self.variationListContainer()
                    .empty()
                    .append(variationList);

                // Use image variation
                var variation = mediaManager.getVariation(uri, "thumbnail");
                self.updateImageVariation(currentBlock, variation.key);
            });
    },

    "{variationWidth} input": function(variationWidth) {

        var ratio = variationWidth.data("ratio");
        var width = Math.round(parseFloat(variationWidth.val()));

        if ($.isNumeric(width)) {
            var height = Math.round(width / ratio);
            self.variationHeight().val(height);
        }
    },

    "{variationHeight} input": function(variationHeight) {

        var ratio = variationHeight.data("ratio");
        var height = Math.round(parseFloat(variationHeight.val()));

        if ($.isNumeric(height)) {
            var width = Math.round(height * ratio);
            self.variationWidth().val(width);
        }
    },

    "{variationWidth} focus": function(variationWidth) {

        variationWidth.select();
    },

    "{variationHeight} focus": function(variationWidth) {

        variationWidth.select();
    },

    "{variationWidth} blur": function(variationWidth) {

        var ratio = variationWidth.data("ratio");
        var width = Math.round(parseFloat(variationWidth.val()));
        variationWidth.val(width);

        if (!$.isNumeric(width)) {

            var variationHeight = self.variationHeight();
            var height = Math.round(parseFloat(variationHeight.val()));

            if ($.isNumeric(height)) {
                width = Math.round(height * ratio);
                variationWidth.val(width);
                variationHeight.val(height);
            } else {
                self.resetVariationFields();
            }
        }
    },

    "{variationHeight} blur": function(variationHeight) {

        var ratio = variationHeight.data("ratio");
        var height = Math.round(parseFloat(variationHeight.val()));
        variationHeight.val(height);

        if (!$.isNumeric(height)) {

            var variationWidth = self.variationWidth();
            var width = Math.round(parseFloat(variationWidth.val()));

            if ($.isNumeric(width)) {
                height = Math.round(width / ratio);
                variationWidth.val(width);
                variationHeight.val(height);
            } else {
                self.resetVariationFields();
            }
        }
    },

    resetVariationFields: function() {

        var variationWidth = self.variationWidth();
        var variationHeight = self.variationHeight();

        variationWidth.val(variationWidth.data("width"));
        variationHeight.val(variationHeight.data("height"));
    },

    //
    // Image Mode
    //
    populateImageMode: function(block) {

        // Get image mode
        var data = blocks.data(block);
        var mode = data.mode;
        var modeLock = data.mode_lock;

        // When on root block, lock to simple mode.
        // TODO: Fix issues related to advanced mode on root block and remove this restriction.
        if (blocks.isRootBlock(block)) {
            mode = "simple";
            modeLock = true;
        }

        // Show/hide mode dropdown
        self.imageSizeFieldset()
            .toggleClass("mode-lock", modeLock);

        // Set active dropdown item to preset to be selected
        var imageSizePreset =
            self.imageSizePreset()
                .removeClass("active")
                .where("type", mode)
                .addClass("active");

        // Update dropdown label to match preset name
        var imageSizePresetLabel = $.trim(imageSizePreset.text());
        self.imageSizeCurrentPreset()
            .html(imageSizePresetLabel);

        // Show image size fields relevant to this preset
        var presetClassname = "preset-" + mode;
        self.imageSizeFieldset()
            .switchClass(presetClassname);
    },

    setImageMode: function(block, mode) {

        if (mode=="simple") {
            self.toSimpleImageMode(block);
        }

        if (mode=="advanced") {
            self.toAdvancedImageMode(block);
        }
    },

    toSimpleImageMode: function(block) {

        var data = blocks.data(block);
        data.mode = "simple";

        // Reset data values
        data.ratio = data.natural_ratio;
        data.ratio_lock = true;
        data.element_width = "";
        data.element_height = "";
        data.element_top = "";
        data.element_left = "";

        // Clear image element css properties
        var imageElement = self.imageElement.inside(block);
        imageElement.css({
            width: "",
            height: "",
            top: "",
            left: ""
        });

        // Normalize image
        self.normalizeImageSize(block);
    },

    toAdvancedImageMode: function(block) {

        var data = blocks.data(block);
        data.mode = "advanced";

        // If ratio hasn't been assigned, use natural ratio.
        if (!data.ratio) {
            data.ratio = data.natural_ratio;
        }

        // If element ratio hasn't been assigned, use ratio.
        if (!data.element_ratio) {
            data.element_ratio = data.ratio;
        }

        // Normalize image
        self.normalizeImageSize(block);

        // Resize to fit viewport
        self.resizeToFitViewport(block);
    },

    updateImageMode: function(block, mode) {

        // Show fields relevant to image type
        self.setImageMode(block, mode);

        // Populate image size
        self.populateImageSize(block);
    },

    "{imageSizePreset} click": function(imageSizePreset) {

        var mode = imageSizePreset.data("type");

        self.updateImageMode(currentBlock, mode);
    },

    //
    // Image Size
    //
    populateImageSize: (function() {

        var populateImageSize = function(block) {

            // Do not populate if block is not current block
            // TODO: Not sure if this should be here because implementor
            // should be able to programmatically populate a block.
            if (!block.is(currentBlock)) return;

            // Populate slider, input & unit for image width & height
            var props = props || ["width", "height"];
            var prop;

            while (prop = props.shift()) {

                var value  = prop=="width" ? self.getImageWidth(block) : self.getImageHeight(block); // 1280
                var number = parseFloat(value); // 1280, 100
                var unit   = parseUnit(value); // px, %

                // Update numslider widget
                // only if user is not resizing from slider
                if (self.resizingFromSlider!==prop) {

                    // Pixel unit
                    if (unit=="px") {
                        var sliderOptions = {
                            start: number,
                            step: 1,
                            range: {
                                min: 1,
                                max: 1600
                            },
                            pips: {
                                mode: "values",
                                values: [64, 320, 640, 960, 1280, 1600],
                                density: 4
                            }
                        };
                    }

                    // Percent unit
                    if (unit=="%") {
                        var sliderOptions = {
                            start: number,
                            step: 1,
                            range: {
                                min: 1,
                                max: 100
                            },
                            pips: {
                                mode: "values",
                                values: [0, 20, 40, 60, 80, 100],
                                density: 5
                            }
                        }
                    }

                    // Set up slider
                    self.numsliderWidget.of(prop)
                        .find(".noUi-pips")
                        .remove()
                        .end()
                        .noUiSlider(sliderOptions, true)
                        .noUiSlider_pips(sliderOptions.pips);
                }

                // Update numslider input
                self.numsliderInput.of(prop)
                    .val(Math.round(number));

                // Update numslider current unit
                self.numsliderCurrentUnit.of(prop)
                    .html(unit);

                // Update numslider unit dropdown
                self.numsliderUnit.of(prop)
                    .removeClass("active")
                    .where("unit", '"' + unit + '"')
                    .addClass("active");
            }

            // Determine if simple field should be hidden
            // This is used for Thumbnails, Gallery & Comparison block.
            var data = blocks.data(block);

            var hideSimpleField = data.width_lock && data.height_lock;

            self.imageSizeFieldset()
                .toggleClass("hide-simple-field", hideSimpleField);

            // Also populate image mode, ratio, alignment & advanced fields.
            self.populateImageMode(block);
            self.populateImageRatio(block);
            self.populateImageAlignment(block);
            self.populateImageAdvancedFields(block);
        }

        return function(block) {

            var data = blocks.data(block);
            var imageSizeFieldset = self.imageSizeFieldset();
            var imageElement = self.imageElement.inside(block);

            // Show or hide image size fieldset
            var sizeEnabled = data.size_enabled;
            panelsFieldset.toggle("image-size", sizeEnabled);

            // If image has loaded, populate image size.
            if (imageElement[0].complete) {

                imageSizeFieldset.removeClass("is-loading is-failed");
                populateImageSize(block);

            // If image is not loaded, wait until it is loaded, then populate image size.
            } else {

                var imageUrl = imageElement.attr("src");
                imageSizeFieldset.addClass("is-loading");

                $.Image.get(imageUrl)
                    .done(function(){
                        populateImageSize(block);
                    })
                    .fail(function(){
                        imageSizeFieldset.switchClass("is-failed");
                    })
                    .always(function(){
                        imageSizeFieldset.removeClass("is-loading is-failed");
                    });
            }
        }
    })(),

    "{imageSizeRetryButton} click": function() {

        self.populateImageSize(currentBlock);
    },

    setImageSize: function(block, prop, val) {

        if (prop=="width") {
            self.setImageWidth(block, val);
        }

        if (prop=="height") {
            self.setImageHeight(block, val);
        }
    },

    updateImageSize: function(block, prop, val) {

        self.setImageSize(block, prop, val);
        self.populateImageSize(block);
    },

    normalizeImageSize: function(block) {

        var data = blocks.data(block);
        var isFluidImage = self.isFluidImage(block);

        // If this is a root block, get width from image figure.
        if (blocks.isRootBlock(block)) {
            var width = self.imageFigure.inside(block).width();
        }

        // If this is a nested block, get width from block.
        if (blocks.isNestedBlock(block)) {
            // var width = blocks.dimensions.getFluidWidth(block);
            // Gallery block have to 100% full width no matter is root or nested. #2793
            // var width = "100%";

            // Fixed locked image ratio not propogate the height correctly. #2827
            var width = self.imageFigure.inside(block).width();
        }

        // Set image width again.
        // This will automatically set image height.
        self.setImageWidth(block, width);

        // Convert to fluid image
        // There's only fluid image in advanced mode for now.
        if (isFluidImage || data.mode=="advanced") {
            self.toFluidImage(block);
        }
    },

    resetImageSize: function(block) {

        var data = blocks.data(block);
        data.mode = "simple";

        var imageContainer = self.imageContainer.inside(block);
        var imageFigure = self.imageFigure.inside(block);
        var imageElement = self.imageElement.inside(block);

        var removeCSSProperties = {
            width: "",
            height: "",
            top: "",
            left: "",
            paddingTop: "",
            float: ""
        };

        imageContainer.css(removeCSSProperties);
        imageFigure.css(removeCSSProperties);
        imageElement.css(removeCSSProperties);

        if (blocks.isRootBlock(block)) {
            self.setImageWidth(block, "100%");
        }

        if (blocks.isNestedBlock(block)) {
            self.setImageWidth(block, "30%");
        }
    },

    getImageWidth: function(block) {

        var imageFigure = self.imageFigure.inside(block);
        var imageFigureStyle = imageFigure[0].style;

        // Root block (%) - assigned image container width
        if (blocks.isRootBlock(block)) {
            var imageContainer = self.imageContainer.inside(block);
            var assignedImageContainerWidth = imageContainer[0].style.width;

            // If assigned image container width has a % on it, use it.
            if (/%/.test(assignedImageContainerWidth)) {
                return assignedImageContainerWidth;
            }
        }

        // Nested block (%) - assigned block width
        if (blocks.isNestedBlock(block)) {

            // Get assigned block width
            var assignedBlockWidth = block[0].style.width;

            // If assigned block width has a % on it, use it.
            if (/%/.test(assignedBlockWidth)) {
                return assignedBlockWidth;
            }
        }

        // Root block (px) or nested block (px)
        // Get assigned width, else get computed width.
        return imageFigureStyle.width || imageFigure.css("width");
    },

    setImageWidth: function(block, width) {

        // Get data & unit
        var data = blocks.getData(block);
        var unit = parseUnit(width);
        var num = parseFloat(width);
        var isPercentUnit = unit=="%";
        var isPixelUnit = unit=="px";
        var isSimpleImage = data.mode=="simple";
        var isAdvancedImage = data.mode=="advanced";

        //
        // Width
        //
        var imageContainer = self.imageContainer.inside(block);
        var imageElement   = self.imageElement.inside(block);
        var imageViewport  = self.imageViewport.inside(block);
        var imageFigure    = self.imageFigure.inside(block);

        // Get original computed width & height
        var originalComputedWidth = imageElement.width();
        var originalComputedHeight = imageElement.height();

        // Fluidity
        if (isPercentUnit) {
            data.fluid = true;
            imageContainer.addClass(isFluid);
        }

        if (isPixelUnit) {
            data.fluid = false;
            imageContainer.removeClass(isFluid);
        }

        // If on advanced image, convert to fixed element first.
        if (isAdvancedImage) {
            self.toFixedElement(block);
        }

        // Root block
        if (blocks.isRootBlock(block)) {

            block.css("width", "");

            if (isPercentUnit) {
                setCSSWidth(imageContainer, width);
                setCSSWidth(imageFigure, "100%");

                if (isSimpleImage) {
                    setCSSWidth(imageElement, "100%");
                    data.element_width = "100%";
                }
            }

            if (isPixelUnit) {
                setCSSWidth(imageContainer, "");
                setCSSWidth(imageFigure, width);

                if (isSimpleImage) {
                    setCSSWidth(imageElement, width);
                    data.element_width = parseFloat(width);
                }
            }
        }

        // Nested block
        if (blocks.isNestedBlock(block)) {

            // Percent
            if (isPercentUnit) {
                setCSSWidth(block, width);
                setCSSWidth(imageFigure, "");

                if (isSimpleImage) {
                    setCSSWidth(imageElement, "100%");
                    data.element_width = "100%";
                }
            }

            // Pixels
            if (isPixelUnit) {
                setCSSWidth(block, "auto");
                setCSSWidth(imageFigure, width);

                if (isSimpleImage) {
                    setCSSWidth(imageElement, width);
                    data.element_width = parseFloat(width);
                }
            }
        }

        // Set width to block data
        data.width = isPercentUnit ? width : num;

        //
        // Height & Ratio
        //
        var ratio = data.ratio;
        var ratioLock = data.ratio_lock;
        var naturalRatio = data.natural_ratio;

        if (ratioLock) {

            var ratioToUse = isSimpleImage ? naturalRatio : ratio;

            if (isPercentUnit) {

                setCSSHeight(imageFigure, "");
                setCSSPaddingTop(imageFigure, ratioPadding(ratioToUse));

                if (isSimpleImage) {
                    setCSSHeight(imageElement, "100%");
                    data.element_height = "100%";
                }

                data.height = "100%";
            }

            if (isPixelUnit) {

                var height = num / ratioDecimal(ratioToUse);

                setCSSHeight(imageFigure, height);
                setCSSPaddingTop(imageFigure, "");

                if (isSimpleImage) {
                    setCSSHeight(imageElement, height);
                    data.element_height = height;
                }

                data.height = height;
            }

            data.ratio = ratioToUse;

            // Also assign to element ratio if this is a simple image
            if (isSimpleImage) {
                data.element_ratio = ratioToUse;
            }

        } else {

            var computedWidth = imageElement.width();
            var ratio = computedWidth / originalComputedHeight;

            if (isPercentUnit) {
                setCSSPaddingTop(imageFigure, ratioPadding(ratio));
            }

            if (isPixelUnit) {
                setCSSPaddingTop(imageFigure, "");
            }

            data.ratio = ratio;

            // Also assign to element ratio if this is a simple image
            if (isSimpleImage) {
                data.element_ratio = ratio;
            }
        }

        // Convert back to fluid element
        if (isAdvancedImage) {
            self.toFluidElement(block);

            var strategy = data.strategy;
            if (/fit|fill/.test(strategy)) {
                self.resizeToViewport(block, strategy);
            }
        }

        //
        // Caption
        //
        var imageCaption = self.imageCaption.inside(block);

        if (isPercentUnit) {
            setCSSWidth(imageCaption, "100%");
        }

        if (isPixelUnit) {
            setCSSWidth(imageCaption, width);
        }
    },

    updateImageWidth: function(block, width) {

        self.setImageWidth(block, width);
        self.populateImageSize(block);
    },

    getImageHeight: function(block) {

        var imageFigure = self.imageFigure.inside(block);
        var imageFigureStyle = imageFigure[0].style;

        // Root/nested (%)  - computed figure height
        // Root/nested (px) - assigned figure height
        return imageFigureStyle.height || imageFigure.css("height");
    },

    setImageHeight: function(block, height) {

        var data = blocks.data(block);
        var height = parseFloat(height);

        var ratio = data.ratio;
        var ratioLock = data.ratio_lock;
        var naturalRatio = data.natural_ratio;
        var isSimpleImage = data.mode=="simple";
        var isAdvancedImage = data.mode=="advanced";

        //
        // Height
        //
        var imageElement = self.imageElement.inside(block);
        var imageViewport = self.imageViewport.inside(block);
        var imageFigure = self.imageFigure.inside(block);

        if (self.isFluidImage(block)) {

            // If ratio is locked, calculate width from height,
            // then convert fixed image to fluid image.
            if (ratioLock) {
                var ratioToUse = isSimpleImage ? naturalRatio : ratio;
                var width = height * ratioDecimal(ratioToUse);
                self.setImageWidth(block, width);
                self.toFluidImage(block);
                data.ratio = ratioToUse;

                if (isSimpleImage) {
                    data.element_ratio = ratioToUse;
                }

            // If ratio is unlocked, calculate ratio from height.
            } else {

                // If on advanced image, convert to fixed element first.
                if (isAdvancedImage) {
                    self.toFixedElement(block);
                }

                var computedWidth = imageElement.width();
                var ratio = computedWidth / height;
                setCSSPaddingTop(imageFigure, ratioPadding(ratio));
                data.ratio = ratio;

                if (isSimpleImage) {
                    data.element_ratio = ratio;
                }

                // Convert back to fluid element
                if (isAdvancedImage) {
                    self.toFluidElement(block);

                    var strategy = data.strategy;
                    if (/fit|fill/.test(strategy)) {
                        self.resizeToViewport(block, strategy);
                    }
                }
            }

            data.height = "100%";
        }

        if (self.isFixedImage(block)) {

            // If ratio is locked, calculate width from height
            // add let setImageWidth handle the rest.
            if (ratioLock) {
                var ratioToUse = isSimpleImage ? naturalRatio : ratio;
                var width = height * ratioDecimal(ratioToUse);
                self.setImageWidth(block, width);

            // If ratio is unlocked, set height directly.
            } else {
                setCSSHeight(imageFigure, height);
                setCSSHeight(imageElement, height);
                data.height = height;
                data.element_width = parseFloat(height);
            }
        }
    },

    updateImageHeight: function(block, height) {

        self.setImageHeight(block, height);
        self.populateImageSize(block);
    },

    isFluidImage: function(block) {

        return self.imageContainer.inside(block).hasClass(isFluid);
    },

    isFixedImage: function(block) {

        return !self.isFluidImage(block);
    },

    toFixedImage: function(block) {

        var data = blocks.data(block);
        var imageElement = self.imageElement.inside(block);

        // Get width & height
        var width = imageElement.width();
        var height = imageElement.height();

        // If ratio is locked, setting image width will
        // automatically set the correct image height.
        self.setImageWidth(block, width);

        // If ratio is unlocked, explicitly set image height.
        var ratioLock = data.ratio_lock;
        if (!ratioLock) {
            self.setImageHeight(block, height);
        }
    },

    toFluidImage: function(block) {

        var data = blocks.data(block);
        var imageContainer = self.imageContainer.inside(block);
        var imageElement = self.imageElement.inside(block);

        // Get width & height
        if (blocks.isRootBlock(block)) {
            var containerWidth = imageContainer.width();
            var blockWidth = block.width();
            var width = Math.round(containerWidth / blockWidth * 100) + "%";
        }

        if (blocks.isNestedBlock(block)) {
            // var width = blocks.dimensions.getFluidWidth(block);
            // Gallery block have to 100% full width no matter is root or nested. #2793
            var width = "100%";
        }

        var height = imageElement.height();

        // If ratio is locked, setting image width will
        // automatically set the correct image height.
        self.setImageWidth(block, width);

        // If ratio is unlocked, explicitly set image height.
        var ratioLock = data.ratio_lock;
        if (!ratioLock) {
            self.setImageHeight(block, height);
        }
    },

    getImageSizeField: function(prop) {

        var field = self["imageSizeFieldImage" + $.capitalize($.camelize(prop))]();
        return field;
    },

    getImageSizeProp: function(elem) {

        var numslider = elem.closest(self.numslider.selector);
        var prop = getCssProp(numslider.data("name"));
        return prop;
    },

    getImageSizeUnit: function(prop) {

        var field = self.getImageSizeField(prop);
        return $.trim(self.numsliderCurrentUnit.under(field).text());
    },

    handleNumsliderWidget: function(numsliderWidget, val) {

        // Get prop & val to update
        var prop = self.getImageSizeProp(numsliderWidget);
        var unit = self.getImageSizeUnit(prop);
        var val = Math.round(val) + unit;

        // Declare that we are resizing the slider of this property
        self.resizingFromSlider = prop;

        self.updateImageSize(currentBlock, prop, val);

        self.resizingFromSlider = null;
    },

    "{numsliderWidget} nouislide": function(numsliderWidget, event, val) {

        self.handleNumsliderWidget(numsliderWidget, val);
    },

    "{numsliderWidget} set": function(numsliderWidget, event, val) {

        self.handleNumsliderWidget(numsliderWidget, val);
    },

    "{numsliderInput} input": function(numsliderInput) {

        // Destroy any blur event handler
        numsliderInput.off("blur.numslider");

        function revertOnBlur(originalValue) {
            numsliderInput
                .on("blur.numslider", function(){
                    numsliderInput.val(originalValue);
                });
        }

        // Get image size, prop, val
        var imageSize = self.getImageSize(currentBlock);
        var prop = self.getImageSizeProperty(numsliderInput);
        var val = numsliderInput.val();

        // If value is invalid, don't do anything.
        if (!$.isNumeric(val)) {
            // Revert to original value when input is blurred.
            return revertOnBlur(imageSize[$.camelize(prop)]);
        }

        // Round value
        val = Math.round(val);

        // Update image size
        self.updateImageSize(currentBlock, prop, val);
    },

    "{numsliderUnit} click": function(numsliderUnit) {

        var unit = numsliderUnit.data("unit");

        if (unit=="px") {
            self.toFixedImage(currentBlock);
        }

        if (unit=="%") {
            self.toFluidImage(currentBlock);
        }

        self.populateImageSize(currentBlock);
    },

    "{self} composerBlockResizeStart": function(base, event, block, ui) {

        if (!isImageBlock(block) || !self.hasImage(block)) return;

        var imageFigure = self.imageFigure.inside(block);

        var initialImageSize = {
            width: imageFigure.width(),
            height: imageFigure.outerHeight(),
            fluid: self.isFluidImage(block)
        };

        block.data("initialImageSize", initialImageSize);
    },

    "{self} composerBlockBeforeResize": function(base, event, block, ui) {

        if (!isImageBlock(block) || !self.hasImage(block)) return;

        // Stop resizable from resizing block because
        // we want to resize the block ourselves.
        event.preventDefault();

        // Get image size, original block size and current block size.
        var imageSize = block.data("initialImageSize");
        var originalBlockSize = ui.originalSize;
        var currentBlockSize = ui.size;

        // Calculate width/height difference
        var dx = currentBlockSize.width  - originalBlockSize.width;
        var dy = currentBlockSize.height - originalBlockSize.height;

        function resizeImageWidth() {
            var newImageWidth = imageSize.width + dx;
            self.setImageWidth(block, newImageWidth);
        }

        function resizeImageHeight() {
            var newImageHeight = imageSize.height + dy;
            self.setImageHeight(block, newImageHeight);
        }

        // If image ratio is locked, resize either image width or height.
        var data = blocks.data(block);
        var ratioLock = data.ratio_lock;

        if (ratioLock) {
            dx==0 ? resizeImageHeight() : resizeImageWidth();

        // If image ratio is unlocked, resize both.
        } else {
            dx!==0 && resizeImageWidth();
            dy!==0 && resizeImageHeight();
        }

        // If this is a fluid image
        if (imageSize.fluid) {
            self.toFluidImage(block);
        }

        // Populate image size
        self.populateImageSize(block);
    },

    "{self} composerBlockResizeStop": function(base, event, block, ui) {

        if (!isImageBlock(block) || !self.hasImage(block)) return;

        block.removeData("initialImageSize");
    },

    "{self} composerBlockNestIn": function(base, event, block) {

        if (!isImageBlock(block) || !self.hasImage(block)) return;

        self.resetImageSize(block);
        self.populateImageSize(block);
    },

    "{self} composerBlockNestOut": function(base, event, block) {

        if (!isImageBlock(block) || !self.hasImage(block)) return;

        self.resetImageSize(block);
        self.populateImageSize(block);
    },

    //
    // Image Size > Ratio
    //

    populateImageRatio: function(block) {

        // Get ratio from data
        var data = blocks.data(block);
        var ratio = data.ratio;
        var ratioLock = data.ratio_lock;

        // Update ratio label
        self.ratioLabel()
            .html(ratio);

        // Set lock state on ratio button
        self.ratioButton()
            .toggleClass("ratio-unlocked", !ratioLock);

        // Get original ratio
        var originalRatio = ".ar-original";
        var naturalRatio = data.natural_ratio;
        var naturalRatioPadding = ratioPadding(naturalRatio);

        // Update original ratio selection
        self.ratioSelection(originalRatio)
            .attr("data-value", naturalRatio);

        self.ratioPreview(originalRatio)
            .find("> div")
            .css("padding-top", naturalRatioPadding)
            .find("span")
            .text(naturalRatio);

        // Hide select/custom ratio view when repopulating image size fields.
        self.imageSizeFieldset()
            .removeClass(customRatioView)
            .removeClass(selectRatioView);
    },

    lockImageRatio: function(block) {

        var data = blocks.data(block);
        data.ratio_lock = true;

        // Readjust image dimension
        self.normalizeImageSize(block);
    },

    unlockImageRatio: function(block) {

        var data = blocks.data(block);
        data.ratio_lock = false;
    },

    setImageRatio: function(block, ratio) {

        // Set new ratio onto block data
        var data = blocks.data(block);
        data.ratio = ratio;

        // Set ratio lock state
        var lockRatio = data.ratio_lock = ratio!==0;

        // Don't do anything when ratio is unlocked
        if (!lockRatio) return;

        var isSimpleImage = data.mode=="simple";
        var isAdvancedImage = data.mode=="advanced";

        if (isSimpleImage) {
            data.element_ratio = ratio;

            // Readjust image viewport & element
            self.normalizeImageSize(block);
        }

        if (isAdvancedImage) {

            var strategy = data.strategy;

            if (fitOrFill.test(strategy)) {

                // Readjust image viewport
                self.normalizeImageSize(block);

                // Readjust image element
                self.resizeToViewport(block, strategy);

            } else {

                // TODO: This will be different when we allow px unit in advanced mode.

                // Convert to fixed element
                self.toFixedElement(block);

                // Resize image dimension
                self.normalizeImageSize(block);

                // Convert to fluid element
                self.toFluidElement(block);
            }
        }
    },

    updateImageRatio: function(block, ratio) {

        self.setImageRatio(block, ratio);
        self.populateImageSize(block);
    },

    "{ratioLock} change": function(ratioLock) {

        if (ratioLock.is(":checked")) {
            self.lockImageRatio(currentBlock);
        } else {
            self.unlockImageRatio(currentBlock);
        }
    },

    "{ratioButton} click": function(ratioButton) {

        // Show ratio selection field
        self.imageSizeFieldset()
            .switchClass(selectRatioView);
    },

    "{ratioCustomizeButton} click": function(ratioCustomizeButton) {

        // Show custom ratio field
        self.imageSizeFieldset()
            .switchClass(customRatioView);
    },

    "{ratioCancelButton} click": function(ratioCancelButton) {

        // Hide ratio selection field
        self.imageSizeFieldset()
            .removeClass(selectRatioView);
    },

    "{ratioCancelCustomButton} click": function(ratioCancelCustomButton) {

        // Show ratio selection field
        self.imageSizeFieldset()
            .switchClass(selectRatioView);
    },

    "{ratioOkCustomButton} click": function(ratioOkCustomButton) {

        // Hide custom ratio field
        self.imageSizeFieldset()
            .removeClass(customRatioView);
    },

    "{ratioUseCustomButton} click": function(ratioUseCustomButton) {

        var ratioInput = self.ratioInput();
        var ratio = sanitizeRatio(ratioInput.val());

        // If ratio is invalid, do nothing.
        if (ratio==0) return;

        // Update video ratio
        self.updateImageRatio(currentBlock, ratio);

        // Deactivate all ratio selection
        self.ratioSelection()
            .removeClass("active");

        // Hide custom ratio field
        self.imageSizeFieldset()
            .removeClass(customRatioView);
    },

    "{ratioSelection} click": function(ratioSelection) {

        self.ratioSelection()
            .removeClass("active");

        ratioSelection.addClass("active");

        self.imageSizeFieldset()
            .removeClass(selectRatioView);

        var ratio = ratioSelection.data("value");

        self.updateImageRatio(currentBlock, ratio);
    },

    //
    // Image Size > Alignment
    //
    setImageAlignment: function(block, alignment) {

        var imageContainer = self.imageContainer.inside(block);

        if (/left|right/.test(alignment)) {
            imageContainer.css("float", alignment);
            block.css("text-align", "");
        }

        if (/center/.test(alignment)) {
            imageContainer.css("float", "");
            block.css("text-align", "center");
        }

        var data = blocks.data(block);
        data.alignment = alignment;
    },

    updateImageAlignment: function(block, alignment) {

        self.setImageAlignment(block, alignment);
        self.populateImageAlignment(block);
    },

    populateImageAlignment: function(block) {

        if (blocks.isNestedBlock(block)) {
            self.imageSizeFieldset()
                .addClass("no-alignment");
            return;
        }

        // Show alignment field
        self.imageSizeFieldset()
            .removeClass("no-alignment");

        // Set alignment
        var data = blocks.data(block);
        var alignment = data.alignment;
        self.alignmentSelection()
            .val(alignment);
    },

    "{alignmentSelection} change": function(alignmentSelection) {

        var alignment = alignmentSelection.val();
        self.updateImageAlignment(currentBlock, alignment);
    },

    //
    // Image Size > Advanced
    //

    populateImageAdvancedFields: function(block) {

        var data = blocks.data(block);
        if (data.mode!=="advanced") return;

        self.populateImageResizeStrategy(block);
        self.populateImageResizeMap(block);
        self.populateImageResizeFields(block);
    },

    //
    // Image Size > Advanced > Image Strategy
    //

    populateImageResizeStrategy: function(block) {

        var data = blocks.data(block);
        var strategy = data.strategy;

        self.strategyMenuItem()
            .where("strategy", strategy)
            .activateClass("active");

        self.strategyMenuContent()
            .where("strategy", strategy)
            .activateClass("active");
    },

    setImageResizeStrategy: function(block, strategy) {

        var data = blocks.data(block);
        data.strategy = strategy;

        if (fitOrFill.test(strategy)) {
            self.resizeToViewport(block, strategy);
        }
    },

    updateImageResizeStrategy: function(block, strategy) {

        self.setImageResizeStrategy(block, strategy);
        self.populateImageAdvancedFields(block);
    },

    resizeToViewport: function(block, strategy) {

        // Get image natural width & height
        var data = blocks.data(block);
        var imageNaturalWidth = data.natural_width;
        var imageNaturalHeight = data.natural_height;

        // Get image viewport width & height
        var imageViewport = self.imageViewport.inside(block);
        var imageViewportWidth = imageViewport.width();
        var imageViewportHeight = imageViewport.height();

        // Calculate final image size
        var imageElementSize =
            (strategy=="fit" ? resizeToFit : resizeToFill)(
                imageNaturalWidth,
                imageNaturalHeight,
                imageViewportWidth,
                imageViewportHeight
            );

        var elementWidth = data.element_width =
            decimalToPercent(imageElementSize.width / imageViewportWidth);

        var elementHeight = data.element_height =
            decimalToPercent(imageElementSize.height / imageViewportHeight);

        var elementTop = data.element_top =
            decimalToPercent(imageElementSize.top / imageViewportHeight);

        var elementLeft = data.element_left =
            decimalToPercent(imageElementSize.left / imageViewportWidth);

        // Convert image size to percentage values and set it on image element
        var imageElement = self.imageElement.inside(block);
        imageElement
            .css({
                width: elementWidth,
                height: elementHeight,
                top: elementTop,
                left: elementLeft
            });
    },

    resizeToFillViewport: function(block) {
        self.setImageResizeStrategy(block, "fill");
    },

    resizeToFitViewport: function(block) {
        self.setImageResizeStrategy(block, "fit");
    },

    "{strategyMenuItem} click": function(strategyMenuItem) {

        var strategy = strategyMenuItem.data("strategy");
        self.updateImageResizeStrategy(currentBlock, strategy);
    },

    //
    // Image Size > Advanced > Image Map
    //

    populateImageResizeMap: function(block) {

        var data = blocks.data(block);
        if (data.strategy!=="custom") return;

        // Get map figure width & height
        var mapFigure = self.mapFigure();
        var mapFigureWidth = mapFigure.width();
        var mapFigureHeight = mapFigure.height();

        // Resize map viewport to fit map figure
        // following the ratio of image viewport
        var imageViewport = self.imageViewport();
        var imageViewportWidth = imageViewport.width();
        var imageViewportHeight = imageViewport.height();

        var mapViewport = self.mapViewport();
        var mapViewportSize = resizeToFit(
                imageViewportWidth,
                imageViewportHeight,
                mapFigureWidth,
                mapFigureHeight
            );

        mapViewport.css(mapViewportSize);

        // Resize map preview according to image element
        var mapPreview = self.mapPreview();
        var resizeDirections = ["n", "s", "w", "e", "sw", "se", "nw", "ne"];
        var resizeHandleElements = self.createResizeHandleElements(resizeDirections);
        var resizeHandleSelectors = self.createResizeHandleSelectors(resizeDirections);

        // Destroy resizable
        if (mapPreview.hasClass("ui-resizable")) {
            mapPreview.resizable("destroy");
        }

        // Destroy draggable
        if (mapPreview.hasClass("ui-draggable")) {
            mapPreview.draggable("destroy");
        }

        mapPreview
            .css({
                backgroundImage: $.cssUrl(data.url), // TODO: Use smaller variation to reduce memory usage
                width: data.element_width,
                height: data.element_height,
                top: data.element_top,
                left: data.element_left
            })
            .empty()
            .append(resizeHandleElements)
            .resizable({
                handles: resizeHandleSelectors,
                aspectRatio: data.element_ratio_lock
            })
            .draggable();
    },

    createResizeHandleSelectors: function(directions) {

        var selectors = [];

        $.each(directions, function(i, direction){
            selectors[direction] = "> .ui-resizable-" + direction;
        });

        return selectors;
    },

    createResizeHandleElements: function(directions) {

        var elements = [];

        $.each(directions, function(i, direction){
            var element = $('<div class="ui-resizable-handle ui-resizable-' + direction + '"><div></div></div>')[0];
            elements.push(element);
        });

        return elements;
    },

    getElementSizeFromMap: function() {

        var mapViewport = self.mapViewport();
        var mapViewportWidth = mapViewport.width();
        var mapViewportHeight = mapViewport.height();

        var mapPreview = self.mapPreview();
        var mapPreviewPosition = mapPreview.position();
        var mapPreviewTop = mapPreviewPosition.top;
        var mapPreviewLeft = mapPreviewPosition.left;
        var mapPreviewWidth = mapPreview.width();
        var mapPreviewHeight = mapPreview.height();

        var elementTop = decimalToPercent(mapPreviewTop / mapViewportHeight);
        var elementLeft = decimalToPercent(mapPreviewLeft / mapViewportWidth);
        var elementWidth = decimalToPercent(mapPreviewWidth / mapViewportWidth);
        var elementHeight = decimalToPercent(mapPreviewHeight / mapViewportHeight);

        return {
            top: elementTop,
            left: elementLeft,
            width: elementWidth,
            height: elementHeight
        }
    },

    setElementSizeFromMap: function(block) {

        var data = blocks.data(block);

        // Get element size from map
        var elementSize = self.getElementSizeFromMap();

        // Set new size on image element
        var imageElement = self.imageElement.inside(block);
        imageElement.css(elementSize);

        // Set new size on block data
        self.setElementSizeOnBlockData(elementSize, data);
    },

    updateElementSizeFromMap: function(block) {

        // Set element size from maps
        self.setElementSizeFromMap(block);

        // Populate image resize fields
        self.populateImageResizeFields(block);
    },

    setElementSizeOnBlockData: function(elementSize, data) {

        var props = ["top", "left", "width", "height"];
        var prop;

        while (prop = props.shift()) {
            data["element_" + prop] = elementSize[prop];
        }
    },

    toFixedElement: function(block) {

        var imageElement = self.imageElement.inside(block);
        var imagePosition = imageElement.position();

        // TODO: Set to block data when we are allowed to switch to use px
        // Right now this is just an internal method.
        var elementTop = imagePosition.top;
        var elementLeft = imagePosition.left;
        var elementWidth = imageElement.width();
        var elementHeight = imageElement.height();

        imageElement.css({
            width: elementWidth,
            height: elementHeight,
            top: elementTop,
            left: elementLeft
        });
    },

    toFluidElement: function(block) {

        var data = blocks.data(block);

        // Image element
        var imageElement = self.imageElement.inside(block);
        var imagePosition = imageElement.position();

        var computedTop = imagePosition.top;
        var computedLeft = imagePosition.left;
        var computedWidth = imageElement.width();
        var computedHeight = imageElement.height();

        // Image viewport
        var imageViewport = self.imageViewport.inside(block);
        var viewportWidth = imageViewport.width();
        var viewportHeight = imageViewport.height();

        var elementTop = data.element_top = decimalToPercent(computedTop / viewportHeight);
        var elementLeft = data.element_left = decimalToPercent(computedLeft / viewportWidth);
        var elementWidth = data.element_width = decimalToPercent(computedWidth / viewportWidth);
        var elementHeight = data.element_height = decimalToPercent(computedHeight / viewportHeight);

        imageElement.css({
            top: elementTop,
            left: elementLeft,
            width: elementWidth,
            height: elementHeight
        });
    },

    "{mapPreview} resize": function() {
        self.updateElementSizeFromMap(currentBlock);
    },

    "{mapPreview} drag": function() {
        self.updateElementSizeFromMap(currentBlock);
    },

    //
    // Image Size > Advanced > Image Resize Fields
    //

    populateImageResizeFields: function(block) {

        var data = blocks.data(block);

        var resizeInputFields = self.resizeInputField();
        var props = ["width", "height", "top", "left"];
        var prop;

        while (prop = props.shift()) {

            // If we're resizing from input, skip this.
            if (self.resizingFromInput==prop) return;

            resizeInputFields.where("prop", prop)
                .val(data["element_" + prop]);
        }

        self.resizeRatioLock()
            .prop("checked", data.element_ratio_lock);
    },

    setElementSize: function(block, prop, val) {

        return self["setElement" + $.capitalize(prop)](block, val);
    },

    updateElementSize: function(block, prop, val) {

        self.setElementSize(block, prop, val);
        self.populateImageAdvancedFields(block);
    },

    setElementWidth: function(block, width) {

        var data = blocks.data(block);

        // Get num & unit
        var num = parseFloat(width);
        var unit = parseUnit(width);

        // Get ratio & ratioLock
        var ratio = data.element_ratio;
        var ratioLock = data.element_ratio_lock;

        // Get image viewport & element
        var imageViewport = self.imageViewport.inside(block);
        var imageElement = self.imageElement.inside(block);

        if (unit=="%") {

            // If ratio is locked,
            // set element width and
            // adjust and set element height.
            if (ratioLock) {

                // Get original element width in both px & %
                var originalElementWidth = data.element_width;
                var originalComputedElementWidth = imageElement.width();

                // Get new width in both px & %
                var elementWidth = width;
                var computedElementWidth = originalComputedElementWidth * (parseFloat(elementWidth) / parseFloat(originalElementWidth));

                // Calculate new height in px
                var computedElementHeight = computedElementWidth / ratio;

                // Calculate new height in %
                var imageViewportHeight = imageViewport.height();
                var elementHeight = decimalToPercent(computedElementHeight / imageViewportHeight);

                // Assign element width & height to data
                data.element_width = elementWidth;
                data.element_height = elementHeight;

                // Set to image element
                setCSSWidth(imageElement, elementWidth);
                setCSSHeight(imageElement, elementHeight);

            // If ratio is unlocked,
            // set element width only.
            } else {

                var elementWidth = width;
                data.element_width = parseFloat(elementWidth);

                // Set to image element
                setCSSWidth(imageElement, elementWidth);
            }
        }

        // TODO: Pixel unit
    },

    setElementHeight: function(block, height) {

        var data = blocks.data(block);

        // Get num & unit
        var num = parseFloat(height);
        var unit = parseUnit(height);

        // Get ratio & ratioLock
        var ratio = data.element_ratio;
        var ratioLock = data.element_ratio_lock;

        // Get image viewport & element
        var imageViewport = self.imageViewport.inside(block);
        var imageElement = self.imageElement.inside(block);

        if (unit=="%") {

            // If ratio is locked,
            // set element height and
            // adjust and set element height.
            if (ratioLock) {

                // Get original element height in both px & %
                var originalElementHeight = data.element_height;
                var originalComputedElementHeight = imageElement.height();

                // Get new height in both px & %
                var elementHeight = height;
                var computedElementHeight = originalComputedElementHeight * (parseFloat(elementHeight) / parseFloat(originalElementHeight));

                // Calculate new width in px
                var computedElementWidth = computedElementHeight * ratio;

                // Calculate new width in %
                var imageViewportWidth = imageViewport.width()
                var elementWidth = decimalToPercent(computedElementWidth / imageViewportWidth);

                // Assign element width & height to data
                data.element_width = elementWidth;
                data.element_height = elementHeight;

                // Set to image element
                setCSSWidth(imageElement, elementWidth);
                setCSSHeight(imageElement, elementHeight);

            // If ratio is unlocked,
            // set element width only.
            } else {

                var elementHeight = height;
                data.element_height = elementHeight;

                // Set to image element
                setCSSHeight(imageElement, elementHeight);
            }
        }
    },

    setElementTop: function(block, top) {

        var data = blocks.data(block);
        data.element_top = top;

        var imageElement = self.imageElement.inside(block);
        setCSSTop(imageElement, top);
    },

    setElementLeft: function(block, left) {

        var data = blocks.data(block);
        data.element_left = left;

        var imageElement = self.imageElement.inside(block);
        setCSSLeft(imageElement, left);
    },

    setElementRatio: function(block, ratio) {

        var data = blocks.data(block);
        data.element_ratio = ratio;
        self.setElementWidth(block, data.element_width);
    },

    updateElementRatio: function(block, ratio) {

        self.setElementRatio(block, ratio);
        self.populateImageAdvancedFields(block);
    },

    resetElementSize: function(block) {

        self.setImageResizeStrategy(block, "fit");
        self.setImageResizeStrategy(block, "custom");

        self.populateImageAdvancedFields(block);
    },

    lockElementRatio: function(block) {

        var data = blocks.data(block);
        data.element_ratio_lock = true;

        var naturalRatio = data.natural_ratio;
        self.updateElementRatio(block, naturalRatio);
    },

    unlockElementRatio: function(block) {

        var data = blocks.data(block);
        data.element_ratio_lock = false;

        self.populateImageAdvancedFields(block);
    },

    "{resizeInputField} input": function(resizeInputField, event) {

        var prop = resizeInputField.data("prop");
        var val = resizeInputField.val();
        var num = $.trim(val.replace(/\%/gi, ""));

        // If value is invalid, don't do anything
        if (!$.isNumeric(num)) return;

        // Restore % on num
        val = parseFloat(num) + "%";

        // Update element size
        self.resizingFromInput = prop;
        self.updateElementSize(currentBlock, prop, val);
        self.resizingFromInput = null;
    },

    "{resizeInputField} focus": function(resizeInputField) {

        // Select all when focus on input field
        resizeInputField.select();
    },

    "{resizeInputField} blur": function(resizeInputField) {

        // Restore to original or finalized value when input field is blurred
        var data = blocks.data(currentBlock);
        var prop = resizeInputField.data("prop");
        var val = data["element_" + prop];

        resizeInputField.val(val);
    },

    "{resizeResetButton} click": function() {

        self.resetElementSize(currentBlock);
    },

    "{resizeRatioLock} click": function(resizeRatioLock) {

        var ratioLock = resizeRatioLock.is(":checked");

        if (ratioLock) {
            self.lockElementRatio(currentBlock);
        } else {
            self.unlockElementRatio(currentBlock);
        }
    },

    //
    // Image Caption
    //
    populateImageCaption: function(block) {

        var data = blocks.data(block);

        // Show or hide image caption fieldset
        var captionEnabled = data.caption_enabled;
        panelsFieldset.toggle("image-caption", captionEnabled)

        // Check or uncheck image caption fieldset
        var hasImageCaption = self.imageCaption.inside(block).length;
        panelsFieldset.enable("image-caption", hasImageCaption);

        // Set image caption text on text field only if this block has a caption text.
        var captionText = data.caption_text;

        if (captionText) {
            self.captionTextField()
                .val(captionText);
        }
    },

    getImageCaption: function(block) {

        var data = blocks.data(block);
        return data.caption_text;
    },

    setImageCaption: function(block, captionText) {

        var imageCaption = self.imageCaption.inside(block);

        // If image caption does not exist
        if (!imageCaption.length) {

            // Create caption
            var imageCaption = $(meta.imageCaption);

            // Get image container
            var imageContainer = self.imageContainer.inside(block);

            // Append caption to contianer
            imageContainer
                .append(imageCaption);
        }

        var imageCaptionText = self.imageCaptionText.inside(block);
        imageCaptionText.text(captionText);

        // Store to block data
        var data = blocks.data(block);
        data.caption_text = captionText;
    },

    removeImageCaption: function(block) {

        var imageCaption = self.imageCaption.inside(block);

        // Remove image caption
        imageCaption.remove();
    },

    enableImageCaption: function(block) {

        // Set caption text
        var data = blocks.data(block);

        // If there's no caption text, get it from the fieldset
        if (!data.caption_text) {
            data.caption_text = self.captionTextField().val();
        }

        self.setImageCaption(block, data.caption_text);

        // Populate caption field
        self.populateImageCaption(block);
    },

    disableImageCaption: function(block) {

        // Remove image caption
        self.removeImageCaption(block);

        // Populate caption field
        self.populateImageCaption(block);
    },

    "{captionToggle} change": function(captionToggle, event) {

        if (captionToggle.is(":checked")) {
            self.enableImageCaption(currentBlock);
        } else {
            self.disableImageCaption(currentBlock);
        }
    },

    "{captionTextField} input": function(captionTextField, event) {

        var captionText = captionTextField.val();

        self.setImageCaption(currentBlock, captionText);
    },

    //
    // Image Style
    //
    populateImageStyle: function(block) {

        var data = blocks.data(block);

        // Show or hide image style fieldset
        var styleEnabled = data.style_enabled;
        panelsFieldset.toggle("image-style", styleEnabled);

        // Active caption selection
        self.styleSelection()
            .where("value", data.style)
            .activateClass("active");
    },

    setImageStyle: function(block, style) {

        var imageContainer = self.imageContainer.inside(block);
        imageContainer.switchClass("style-" + style);

        var data = blocks.data(block);
        data.style = style;
    },

    updateImageStyle: function(block, style) {

        self.setImageStyle(block, style);
        self.populateImageStyle(block);
    },

    "{styleSelection} click": function(styleSelection, event) {

        var style = styleSelection.data("value");
        self.updateImageStyle(currentBlock, style);
    },

    //
    // Image Link
    //
    populateImageLink: function(block) {

        var data = blocks.data(block);

        // Show or hide image link fieldset
        var linkEnabled = data.link_enabled;
        panelsFieldset.toggle("image-link", linkEnabled);

        // Set link field values
        self.linkUrlField()
            .val(data.link_url);

        self.linkTitleField()
            .val(data.link_title);

        self.linkBlankOption()
            .prop("checked", data.link_target=="_blank");

        // Chekc or uncheck image link fieldset
        var hasLink = data.link_url!=='';
        panelsFieldset.enable("image-link", hasLink);
    },

    setImageLinkUrl: function(block, url) {

        var data = blocks.data(block);
        data.link_url = url = $.trim(url);

        var imageViewport = self.imageViewport.inside(block);

        if (url) {
            imageViewport.attr("href", url);
        } else {
            imageViewport.removeAttr("href");
        }
    },

    setImageLinkTitle: function(block, title) {

        var data = blocks.data(block);
        data.link_title = title;

        self.imageViewport.inside(block)
            .attr("title", data.link_title);
    },

    setImageLinkTarget: function(block, target) {

        var data = blocks.data(block);
        data.link_target = target;

        var imageViewport = self.imageViewport.inside(block);
        if (target) {
            imageViewport.attr("target", target);
        } else {
            imageViewport.removeAttr("target");
        }
    },

    removeImageLink: function(block) {

        var data = blocks.data(block);
        data.link_url = '';
        data.link_title = '';
        data.link_target = '';

        var imageViewport = self.imageViewport.inside(block);
        imageViewport
            .removeAttr("href")
            .removeAttr("title")
            .removeAttr("target");

        self.populateImageLink(block);
    },

    "{linkUrlField} input": function(linkUrlField) {

        var url = linkUrlField.val();
        self.setImageLinkUrl(currentBlock, url);
    },

    "{linkTitleField} input": function(linkTitleField) {

        var title = linkTitleField.val();
        self.setImageLinkTitle(currentBlock, title);
    },

    "{linkBlankOption} change": function(linkBlankOption) {

        var checked = linkBlankOption.is(":checked");
        var target = checked ? "_blank" : "";
        self.setImageLinkTarget(currentBlock, target);
    },

    "{linkToggle} change": function(linkToggle) {

        if (!linkToggle.is(":checked")) {
            self.removeImageLink(currentBlock);
        }
    },

    "{imageViewport} click": function(imageViewport, event) {

        event.preventDefault();
    },

    //
    // Image Popup
    //
    setImagePopup: function(block, popupUri, popupVariationKey) {

        var data = blocks.data(block);

        // Remove existing popup button before adding new one
        self.imagePopupButton
            .inside(block)
            .remove();

        // Add image popup button
        // var imageFigure = self.imageFigure.inside(block);
        // var imagePopupButton = $(meta.imagePopupButton);
        // imageFigure.append(imagePopupButton);

        // If popup image source is not set yet, use original image source.
        var popupUri = data.popup_uri =
            popupUri || data.popup_uri || data.uri;

        var popupVariationKey = data.popup_variation =
            popupVariationKey || data.popup_variation;

        // Get media
        var task =
            mediaManager.getMedia(popupUri)
                .done(function(media){

                    // Get media meta
                    var mediaMeta = media.meta;

                    // Set variation and url
                    var variation = mediaManager.getVariation(popupUri, [popupVariationKey, "system/large", "system/original"]);

                    // Just in case it was fallback variation, set variation values again.
                    data.popup_url = variation.url;
                    data.popup_variation = variation.key;

                    // Set url to variation on image element
                    self.imagePopupButton.inside(block)
                        .attr("href", data.popup_url);
                });

        return task;
    },

    unsetImagePopup: function(block) {

        var data = blocks.data(block);
        data.popup_url = "";
        data.popup_uri = "";
        data.popup_variation = "";

        self.imagePopupButton.inside(block)
            .remove();
    },

    updateImagePopup: function(block, popupUri, popupVariationKey) {

        self.setImagePopup(block, popupUri, popupVariationKey)
            .always(function(){
                self.populateImagePopup(block);
            });
    },

    removeImagePopup: function(block) {
        self.unsetImagePopup(block);
        self.populateImagePopup(block);
    },

    populateImagePopup: function(block) {

        var data = blocks.data(block);

        self.popupFieldset().removeClass('hidden');

        if (data.isurl) {
            self.popupFieldset().addClass('hidden');
            return;
        }


        var popupUri = data.popup_uri;
        var popupVariationKey = data.popup_variation;

        // Show or hide image popup fieldset
        var popupEnabled = data.popup_enabled;
        panelsFieldset.toggle("image-popup", popupEnabled);

        // Check or uncheck image popup fieldset
        var hasPopup = !!popupUri;
        panelsFieldset.enable("image-popup", hasPopup);

        // If image uri & popup uri are two different source,
        // show different hint.
        var sourceUri = data.uri;
        var popupFieldset = self.popupFieldset();
        popupFieldset.toggleClass(isDifferent, hasPopup && (popupUri !== sourceUri));

        // If there is a popup, populate image popup fieldset.
        if (!hasPopup) return;

        // Get popup fieldset
        popupFieldset.addClass(isLoading);

        // Get media
        mediaManager.getMedia(popupUri)
            .done(function(media){

                // Get media meta
                var mediaMeta = media.meta;

                // Popup title
                self.popupTitle()
                    .text(mediaMeta.title);

                // Popup thumbnail image
                var popupThumbnailImage = self.getSourceThumbnailImage(mediaMeta.thumbnail);
                self.popupThumbnail()
                    .empty()
                    .append(popupThumbnailImage);

                // Popup variation list
                var popupVariationList = $(media.variations);
                self.popupVariationListContainer()
                    .empty()
                    .append(popupVariationList);

                // Popup variation
                var popupVariation = mediaManager.getVariation(popupUri, [popupVariationKey, "system/large", "system/original"]);

                // Popup variatian item
                var popupVariationItem =
                    self.popupVariationItem()
                        .where("key", '"' + popupVariation.key + '"')
                        .activateClass("active");

                // Popup size
                self.popupSize()
                    .text($.plupload2.formatSize(popupVariation.size));

                // Popup url
                self.popupUrl()
                    .text(popupVariation.url);
            })
            .fail(function(){
                popupFieldset.addClass(isFailed);
            })
            .always(function(){
                popupFieldset.removeClass(isLoading);
            });
    },

    "{popupToggle} change": function(popupToggle) {
        var checked = popupToggle.is(":checked");

        if (checked) {
            self.updateImagePopup(currentBlock);
        } else {
            self.removeImagePopup(currentBlock);
        }
    },

    "{popupVariationItem} click": function(popupVariationItem) {

        // Prevent user from selecting missing variation
        if (popupVariationItem.hasClass("is-missing")) return;

        // Set variation key
        var popupVariationKey = popupVariationItem.data("key");
        self.updateImagePopup(currentBlock, null, popupVariationKey);
    },


    "{imageUrlAdd} click" : function(el, event) {
        // "[data-eb-image-{url-form|url-textbox|url-add|url-cancel}]",
        //
        var imageUrl = self.imageUrlTextbox().val();

        if (!imageUrl) {
            return;
        }


        var blockContent = blocks.getBlockContent(currentBlock);
        var data = blocks.data(currentBlock);

        // Always center align block
        currentBlock.css("text-align", "center");

        // Add loading indicator
        // block.addClass("is-loading");

        data.uri = imageUrl;
        data.url = imageUrl;
        data.simple = 'simple';
        data.ratio_lock = true;
        data.isurl = true;

        var imageContainer = self.constructImage(data);

        // Append image container to block content
        // blockContent.html(imageContainer);

        blocks.getBlockContent(currentBlock)
            .empty()
            .append(imageContainer);

        // If this block is still active, populate image block
        if (currentBlock.hasClass("active")) {
            self.populate(currentBlock);
        }
    },

    "{urlFieldUpdateButton} click": function(el, event) {
        var newUrl = self.urlFieldText().val();

        if (newUrl) {
            var data = blocks.data(currentBlock);

            // update data url
            data.uri = newUrl;
            data.url = newUrl;

            // Construct image container
            var imageContainer = self.constructImage(data);

            // Append image container to block content
            blocks.getBlockContent(currentBlock)
                .empty()
                .append(imageContainer);
        }
    }



}});

module.resolve();

});

});

EasyBlog.module("composer/blocks/handlers/instagram", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Instagram", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-instagram-form]",
            "{insert}": "[data-instagram-insert]",
            "{source}": "[data-instagram-source]",

            // Preview
            "{preview}": "[data-instagram-preview]",

            //fieldset
            "{fsSource}": "[data-fs-instagram-source]",
            "{fsRefreshButton}": "[data-fs-instagram-refresh]",
            "{errorMessage}": "[data-instagram-error]"
        }, EBD.selectors)
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toText: function(block) {

                // we should return the photo URL as text.
                var data = blocks.data(block);

                return data.source;
            },

            toEditableHTML: function(block) {
                return '';
            },
            
            toHTML: function(block) {
                // We need to get the data from the overlay instead
                var overlay = block.data('overlay');

                if (overlay) {
                    return overlay.element().html();
                }
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function(block) {
            },

            construct: function(block) {
            },

            deconstruct: function(block) {
                var content = blocks.getBlockContent(block);

                // Remove the form from the content
                self.form.inside(content).remove();

                return block;
            },

            reconstruct: function(block) {
                var data = blocks.data(block);
                var overlay = block.data('overlay');
                var content = blocks.getBlockContent(block);

                if (data.source && !overlay) {
                    self.createIframe(block, data.source);
                } else {
                    content.html(meta.html);
                }

                // So redactor won't receive it.
                block.find(self.source.selector).off("paste").on("paste", function(event){
                    event.stopPropagation();
                });
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block) {
                // When populating the fieldset for a block, reset the values
                var data = blocks.data(block);

                // update the fieldset url
                self.fsSource().val(data.source);
            },

            createIframe: function(block, url) {
                var iframe = $.create('iframe');
                var data = blocks.data(block);

                // Ensure that the url is properly sanitized
                url = self.sanitizeUrl(url);

                data.source = url;

                iframe.attr('src', url);

                self.setOverlay(block, iframe);
            },

            sanitizeUrl: function(url) {

                // Ensure that the prepended ?modal=true is removed
                url = url.replace('?modal=true', '');

                // remove the ending slash if there is any
                url = url.replace(/\/$/, '');

                // Prepend the embed
                if (!url.match(/\/embed/)) {
                    url += '/embed/';
                }

                return url;
            },

            setOverlay: function(block, embed) {

                var overlay = block.data('overlay');

                // If overlay didn't exist, create one first
                if (!overlay) {
                    overlay = composer.document.overlay.create(block);
                    
                    // Get the block's content
                    var content = blocks.getBlockContent(block);

                    // Append the placeholder first
                    overlay
                        .placeholder()
                        .css('height', '400px')
                        .appendTo(content);

                    // Append the embed codes now
                    overlay.element().append(embed);

                    // Attach the overlay now
                    overlay.attach();
                } else {
                    overlay.element().empty();
                    overlay.element().append(embed);
                }

                // Set the overlay data so we don't create overlays all the time
                block.data('overlay', overlay);
            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // http://instagram.com/p/xxxx/
                var regex = /^(?:http(?:s)?:\/\/)?www\.instagram\.com\/p\/(.*)\/$/;
                var valid = regex.test(url);

                return valid;
            },

            "{insert} click": function(button, event) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();

                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return;
                }

                // update the fieldset url
                self.fsSource().val(url);

                // Create the iframe
                self.createIframe(currentBlock, url);

                // Hide the form
                self.form.inside(content).addClass('hidden');
            },

            "{fsRefreshButton} click": function() {
                // Add overlay when the user add's a new link.
                var content = blocks.getBlockContent(currentBlock);
                var url = self.fsSource().val();

                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return;
                }

                // Create the iframe
                self.createIframe(currentBlock, url);

                // Hide the form
                self.form.inside(content).addClass('hidden');
            }
        }
    });

    module.resolve();
});

EasyBlog.module("composer/blocks/handlers/links", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Links", {

        defaultOptions: {

            // Loader
            "{loader}": "> [data-link-loader]",

            // Form Items
            "{form}": "> [data-link-form]",
            "{addLink}": "[data-link-add]",
            "{linkInput}": "[data-link-input]",
            "{errorMessage}": "[data-link-error]",

            // Preview items
            "{preview}": "> [data-link-preview]",
            "{previewTitle}": "> [data-link-preview] [data-preview-title]",
            "{previewContent}": "> [data-link-preview] [data-preview-content]",
            "{previewLink}": "> [data-link-preview] [data-preview-link]",
            "{previewImage}": "> [data-link-preview] [data-preview-image]",
            "{imageWrapper}": "> [data-link-preview] [data-preview-image-wrapper]",

            // Fieldset
            "{settings}": "[data-eb-composer-block-links-image]",
            "{imageList}": "[data-eb-composer-block-links-image] [data-images]",
            "{imagePlaceholder}": "[data-eb-composer-block-links-image] [data-image-placeholder]",
            "{currentIndex}": "[data-eb-composer-block-links-image] [data-image-current-index]",
            "{totalImages}": "[data-eb-composer-block-links-image] [data-images-total]",
            "{previousImage}": "[data-eb-composer-block-links-image] [data-images-previous]",
            "{nextImage}": "[data-eb-composer-block-links-image] [data-images-next]",
            "{showImage}": "[data-links-image]"
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            activate: function(block) {
                // Set as current block
                currentBlock = block

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function(block) {

            },

            construct: function(block) {
            },

            toText: function(block) {
                var blockContent = blocks.getBlockContent(block),
                    url = self.previewLink.inside(blockContent).html();

                return url;
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toHTML: function(block) {

                var data = blocks.data(block);
                if (!data.url) return "";

                var clone = block.clone();
                var deconstructedBlock = self.deconstruct(clone);
                var content = blocks.getBlockContent(deconstructedBlock);

                return content.html();
            },

            makeEditable: function(block) {

                // Make link title editable
                var blockContent = blocks.getBlockContent(block);

                self.previewTitle
                    .inside(blockContent)
                    .editable(true)
                    .on('click', function(event) {
                        event.preventDefault();
                    });

                // Make preview content editable
                self.previewContent
                    .inside(blockContent)
                    .editable(true);


                //disable image wrapper and links clickable
                self.previewLink
                    .inside(blockContent)
                    .on('click', function(event) {
                        event.preventDefault();
                    });

                //disable image wrapper and links clickable
                self.imageWrapper
                    .inside(blockContent)
                    .on('click', function(event) {
                        event.preventDefault();
                    });
            },

            reconstruct: function(block) {
                var data = blocks.data(block);
                var blockContent = blocks.getBlockContent(block);

                // If previous data is empty, we should ensure that the link's form is visible again
                if (!data.url && !data.title) {
                    blockContent.html(meta.html);
                }

                // Make items editable again
                self.makeEditable(block);
            },

            deconstruct: function(block) {

                var blockContent = blocks.getBlockContent(block);
                var data = blocks.data(block);
                var parent = self.preview.inside(blockContent);

                // Remove the image if necessary
                if (!data.showImage) {
                    self.imageWrapper.inside(blockContent).remove();
                }

                // Disallow editable
                self.previewTitle.inside(blockContent).editable(false);
                self.previewContent.inside(blockContent).editable(false);

                // remove the form and loader
                self.loader.inside(blockContent).remove();

                // Remove the form
                self.form.inside(blockContent).remove();

                // Remove attributes
                self.preview.inside(blockContent).removeAttr('data-link-preview');
                self.previewImage.inside(blockContent).removeAttr('data-preview-image');
                self.previewTitle.inside(blockContent).removeAttr('data-preview-title')
                self.previewContent.inside(blockContent).removeAttr('data-preview-content');

                // Remove the parent.
                blockContent.html(parent.html());
                parent.remove();

                return block;
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block) {
                var data = blocks.data(block);

                // Repopulate the images on the fieldset
                if (data.images && data.images.length > 0) {
                    self.populateImages(data.images, data.image);
                } else {
                    self.populateImages('', '');
                }

                // always show the settings;
                self.settings().removeClass('hide');

                // if (data.images.length >= 1) {
                //     self.settings().removeClass('hide');
                // } else {
                //     self.settings().addClass('hide');
                // }

                self.showImage().val(data.showImage ? 1 : 0);
                self.showImage().trigger('change');
            },

            populateImages: function(images, selectedImage) {

                // Get the selected index
                var selectedIndex = $.inArray(selectedImage, images);

                // Set the current index numbering. It should always increment by 1 since the array starts with 0
                self.currentIndex().html(selectedIndex + 1);

                // Set the total number of images
                self.totalImages().html(images.length);

                // Remove all existing image list
                self.imageList().children('img').remove();

                if (!images) {
                    return false;
                }

                $.each(images, function(i, source) {
                    var newImage = $(new Image());

                    newImage.attr('src', source);

                    // Hide placeholders
                    self.imagePlaceholder().addClass('hidden');

                    // Hide other items apart from the first item
                    newImage.addClass('hidden');

                    if (i == $.inArray(selectedImage, images)) {
                        newImage.removeClass('hidden');
                    }

                    self.imageList().append(newImage);
                });
            },

            updatePreview: function() {
                var blockContent = blocks.getBlockContent(currentBlock);
                var data = blocks.data(currentBlock);

                self.previewTitle
                    .inside(blockContent)
                    .html(data.title)
                    .attr('href', data.url);

                self.previewContent
                    .inside(blockContent)
                    .html(data.content);

                self.previewLink
                    .inside(blockContent)
                    .html(data.url)
                    .attr('href', data.url);

                self.previewImage
                    .inside(blockContent)
                    .attr('src', data.image);

                self.imageWrapper
                    .inside(blockContent)
                    .attr('href', data.url);

            },

            getSuggestions: function(url, block, callback) {

                // Run an ajax call to retrieve url suggestions
                EasyBlog.ajax('site/views/crawler/crawl', {
                    "url": url
                })
                .done(callback)
                .fail(function(message){

                    // Show form
                    self.showForm(block);

                    // Show error message
                    self.errorMessage().html(message).removeClass('hide');
                });
            },

            hideForm: function(block) {
                var blockContent = blocks.getBlockContent(block);

                // Hide the form and display the loader
                self.form.inside(blockContent).addClass('hidden');

                self.loader.inside(blockContent).removeClass('hidden');
            },

            showForm: function(block) {
                var blockContent = blocks.getBlockContent(block);

                self.form.inside(blockContent).removeClass('hidden');

                self.loader.inside(blockContent).addClass('hidden');
            },

            "{showImage} change": function(el, event) {
                var blockContent = blocks.getBlockContent(currentBlock);
                var data = blocks.data(currentBlock);
                var showImage = el.val();

                if (showImage == 0) {
                    data.showImage = false;

                    self.imageWrapper.inside(blockContent).addClass('hide');
                } else {

                    data.showImage = true;

                    self.imageWrapper.inside(blockContent).removeClass('hide');
                }
            },

            "{previousImage} click": function(el, event) {
                var images = self.imageList().children('img');
                var activeImage = self.imageList().children('img:not(.hidden)');
                var previousImage = $(activeImage).prev('img');
                var currentIndex = parseInt(self.currentIndex().html());
                var data = blocks.data(currentBlock);

                if (previousImage.length > 0) {

                    // Update the preview
                    data.image = previousImage.attr('src');
                    self.updatePreview();

                    // Add hidden class on all images
                    $(images).addClass('hidden');

                    // Remove hidden on the next image
                    $(previousImage).removeClass('hidden');


                    var index = currentIndex - 1;

                    // console.log(index);

                    // Update the index
                    self.currentIndex().html(index);
                }

                // The next button could be disabled
                self.nextImage().disabled(false);

                // We need to add disabled attribute on the next icon when there's nothing more
                if ((currentIndex - 1) == 1) {
                    $(el).disabled(true);
                } else {
                    $(el).disabled(false);
                }
            },

            "{nextImage} click": function(el, event)  {
                var images = self.imageList().children('img');
                var activeImage = self.imageList().children('img:not(.hidden)');
                var nextImage = $(activeImage).next('img');
                var currentIndex = parseInt(self.currentIndex().html());
                var data = blocks.data(currentBlock);

                if (nextImage.length > 0) {

                    // Update the preview
                    data.image = nextImage.attr('src');
                    self.updatePreview();

                    // Add hidden class on all images
                    $(images).addClass('hidden');

                    // Remove hidden on the next image
                    $(nextImage).removeClass('hidden');

                    // Update the index
                    self.currentIndex().html(currentIndex + 1);
                }

                // The next button could be disabled
                self.previousImage().disabled(false);

                // We need to add disabled attribute on the next icon when there's nothing more
                if ((currentIndex + 1) == images.length) {
                    $(el).disabled(true);
                } else {
                    $(el).disabled(false);
                }

            },

            "{addLink} click": function(el) {

                var blockContent = blocks.getBlockContent(currentBlock);
                var url = self.linkInput.inside(blockContent).val();
                var data = blocks.data(currentBlock);

                if (!url) {
                    self.errorMessage().removeClass('hide');
                    return;
                }

                // Hide the form
                self.hideForm(currentBlock);

                self.getSuggestions(url, currentBlock, function(results){

                    // Get the result of the share
                    var result = results[url];

                    // Update the blocks data
                    data.title = result.title;
                    data.content = result.description;
                    data.url = url;
                    data.images = result.images;
                    data.image = result.images[0];
                    data.showImage = true;

                    // Update the preview
                    self.updatePreview();

                    // Display the preview and hide the form
                    self.loader.inside(blockContent).addClass('hidden');
                    self.preview.inside(blockContent).removeClass('hidden');

                    self.makeEditable();

                    // Repopulate the images on the fieldset
                    self.populate(currentBlock);
                })
            }
        }
    });

    module.resolve();

});


EasyBlog.module("composer/blocks/handlers/note", function($){

    var module = this;

    EasyBlog.require()
    .done(function(){

        EasyBlog.Controller("Composer.Blocks.Handlers.Note", {

            defaultOptions: {

                data: {
                    type: "warning",
                    content: "Enter an important note here" // This gets replaced with translated string when initialized
                },

                "{note}": ".alert",
                "{alertSelection}": "[data-eb-composer-block-alert-type] [data-type]"
            }
        }, function(self, opts, base, composer, blocks, meta, currentBlock) {

            return {

                init: function() {
                    // Globals
                    blocks = self.blocks;
                    composer = blocks.composer;
                    meta = opts.meta;
                    currentBlock = $();

                    // Update default data
                    opts.data.content = $(meta.content).html();
                },

                activate: function(block) {
                    // Set as current block
                    currentBlock = block;

                    // Populate fieldset
                    self.populate(block);
                },

                construct: function(data) {
                    var data = $.extend({}, opts.data, data),
                        content =
                            $(meta.content)
                                .switchClass("alert-" + data.type)
                                .html(data.content);

                    // return content;
                },

                reconstruct: function(block) {
                },

                deconstruct: function(block) {

                    var blockContent = blocks.getBlockContent(block);

                    // make note not editable
                    self.note.inside(blockContent).editable(false);

                    return block;
                },

                refocus: function(block) {
                    // Get note
                    var blockContent = blocks.getBlockContent(block),
                        note = self.note.inside(blockContent);

                    // Focus on note
                    note.focus();

                    // If block is new
                    if (block.hasClass("is-new")) {

                        // Set caret at the end of heading
                        composer.editor.caret.setEnd(note[0]);
                    }
                },

                // Returns the text that is within the block
                toText: function(block) {
                    var blockContent = blocks.getBlockContent(block),
                        text = self.note.inside(blockContent).text();

                    return text;
                },

                toData: function(block) {
                    var data = blocks.data(block);

                    return data;
                },

                toHTML: function(block)  {

                    var cloned = block.clone(),
                        deconstructedBlock = self.deconstruct(cloned),
                        blockContent = blocks.getBlockContent(deconstructedBlock);

                    return blockContent.html();

                },


                reset: function(block) {
                    var blockContent = blocks.getBlockContent(block);

                    blockContent.html(self.construct());
                },

                populate: function(block) {
                    var data = blocks.data(block);

                    // Update fieldset
                    self.alertSelection()
                        .where("type", data.type)
                        .activateClass("selected");
                },

                setAlertType: function(type) {
                    var blockContent = blocks.getBlockContent(currentBlock);
                    var note = blockContent.find(self.note);

                    // Set the note type
                    note.switchClass('alert-' + type);
                },

                previewType: function(type) {


                    // Get the current block note
                    var blockContent = blocks.getBlockContent(currentBlock);
                    var note = self.note.inside(blockContent);
                    var currentClass = note.attr('class');
                    var data = blocks.getData(currentBlock);

                    if (!currentBlock.hasClass('is-preview')) {

                        // Update the block's data with the original class
                        currentBlock.data('originalClass', currentClass);

                        // Add a preview class
                        currentBlock.addClass('is-preview');
                    }

                    // Update the alert type
                    self.setAlertType(type);

                    // Trigger necessary events
                    var args = [currentBlock, self, note];
                    self.trigger("composerBlockNotePreviewType", args);
                    self.trigger("composerBlockChange", args);
                },

                previewTimer: null,

                "{alertSelection} mouseover": function(alertSelection) {

                    // // Set heading level to the one being hovered on
                    // var type = alertSelection.data('type');

                    // // Preview level on current block
                    // self.previewType(type);
                },

                "{alertSelection} mouseout": function(alertSelection) {

                    // clearTimeout(self.previewTimer);

                    // // Delay before reverting to original level
                    // self.previewTimer = $.delay(function(){

                    //     var originalClass = currentBlock.data("originalClass");

                    //     console.log(originalClass);

                    //     if (originalClass) {
                    //         self.setAlertType(originalClass);
                    //     }

                    //     // Remove the is-preview class
                    //     currentBlock.removeClass('is-preview');
                    //     currentBlock.removeData('originalClass');

                    // }, 50);
                },

                "{alertSelection} click": function(el) {

                    // Get the alert type
                    var type = el.data('type');
                    var data = blocks.data(currentBlock);


                    // Remove all selected class
                    self.alertSelection().removeClass("selected");

                    // Add selected class on the selected item
                    $(el).addClass('selected');

                    // Remove the is-preview class
                    currentBlock.removeClass("is-preview");
                    currentBlock.removeData("originalClass");

                    // Set the alert type
                    self.setAlertType(type);

                    // Update the data
                    data.type = type;

                    self.populate(currentBlock);
                }
            }
        });

        module.resolve();
    });

});

EasyBlog.module("composer/blocks/handlers/post", function($){

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Post", {

        defaultOptions: {
            "{block}": ".ebd-block[data-type=text]",
            "{wrapper}": "> div[data-text-wrapper]",
            "{lastParagraph}": "> div > p:last",

            // Items in a post block
            "{mediaPreview}": "[data-post-media]",
            "{introPreview}": "[data-post-intro]",
            "{linkPreview}": "[data-post-link-preview]",
            "{titlePreview}": "[data-preview-title]",

            // Post options in fieldset
            "{showImage}": "[data-post-option-image]",
            "{showIntro}": "[data-post-option-intro]",
            "{showHyperlink}": "[data-post-option-link]"

        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            activate: function(block) {
                var data = blocks.data(block);

                // Set as current block
                currentBlock = block;

                // Determines if the image property should be enabled or disabled
                self.showImage()
                    .val(data.show_image == 0 ? 0 : 1)
                    .trigger('change');

                // Determines if the show intro should be enabled or disabled
                self.showIntro()
                    .val(data.show_intro == 0 ? 0 : 1)
                    .trigger('change');

                self.showHyperlink()
                    .val(data.show_link == 0 ? 0 : 1)
                    .trigger('change');

                // Populate fieldset
                self.populate();
            },

            deactivate: function() {
            },

            construct: function(data) {

                var block = blocks.createBlockContainer("post"),
                    data = $.extend({}, meta.data, data),
                    content = $(data.content);

                // Set the title
                content
                    .find('[data-post-title]').html(data.title);

                // Set the anchor link
                content
                    .find('[data-post-link]')
                    .attr('href', data.url);

                content
                    .on('click', function(event) {
                        event.preventDefault();
                    });

                // Set the preview link
                content
                    .find('[data-post-link-preview]')
                    .html(data.url);

                // Set the intro code
                content
                    .find('[data-post-intro]')
                    .html(data.intro);

                // When there is no image, we should remove the image tag from the preview
                if (!data.image) {
                    content
                        .find('[data-post-image]')
                        .remove();
                } else {
                    content
                        .find('[data-post-image]')
                        .attr('src', data.image);
                }

                // Create block content
                blocks.content.inside(block)
                    .append(content);

                // Set block data
                block.data('block', data);

                return block;
            },

            reconstruct: function(block) {

                var blockContent = blocks.getBlockContent(block),
                    data = blocks.data(block);

                blockContent.find('[data-post-link]')
                    .on('click', function(event) {
                        event.preventDefault();
                    });
            },

            deconstruct: function(block) {
                var wrapper = self.wrapper.inside(block),
                    parent = wrapper.parent();

                wrapper.children().appendTo(parent);

                wrapper.remove();

                return block;
            },

            refocus: function(block) {

                // Get last paragraph
                var wrapper = self.wrapper.inside(block),
                    lastParagraph = self.lastParagraph.inside(block);

                // Focus on wrapper because
                // that's where contenteditable is.
                wrapper.focus();

                // But the selection should be made
                // on the paragraph itself.
                composer.editor.caret.setEnd(lastParagraph);
            },

            reset: function(block) {

                block = blocks.getBlockContent(block);

                // Replace block content with default content
                block.html(meta.content);
            },

            populate: function(block) {
            },

            recover: function(block) {
            },

            revert: function(block) {
            },

            toText: function(block) {
                var block = blocks.getBlockContent(block);

                return block.text();
            },

            toHTML: function(block) {
                var block = blocks.getBlockContent(block),
                    cloned = block.clone(),
                    html = self.deconstruct(cloned).html();

                // Based on the meta settings, remove those unwanted html codes

                return self.deconstruct(cloned).html();
            },

            toData: function(block) {
                var data = blocks.data(block);

                data.content = self.toHTML(block);

                return data;
            },

            "{showImage} change": function(el, event) {
                var blockContent = blocks.getBlockContent(currentBlock),
                    enabled = el.val() == 0 ? false : true,
                    data = blocks.data(currentBlock);

                data.show_image = enabled;

                if (!enabled) {
                    self.mediaPreview.inside(blockContent).hide();
                    return;
                }

                self.mediaPreview.inside(blockContent).show();
            },

            "{showIntro} change": function(el, event) {
                var blockContent = blocks.getBlockContent(currentBlock),
                    enabled = el.val() == 0 ? false : true,
                    data = blocks.data(currentBlock);

                data.show_intro = enabled;

                if (!enabled) {
                    self.introPreview.inside(blockContent).hide();
                    return;
                }

                self.introPreview.inside(blockContent).show();
            },

            "{showHyperlink} change": function(el, event) {
                var blockContent = blocks.getBlockContent(currentBlock),
                    enabled = el.val() == 0 ? false : true,
                    data = blocks.data(currentBlock);

                data.show_link = enabled;

                if (!enabled) {
                    self.linkPreview
                        .inside(blockContent)
                        .hide();

                    return;
                }

                self.linkPreview.inside(blockContent).show();
            }
        }
    });

    module.resolve();
});

EasyBlog.module("composer/blocks/handlers/quotes", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Quotes", {
        defaultOptions: {

            "{styleSelection}": "[data-eb-composer-block-quotes-style] [data-style]",
            "{citation}": "[data-quotes-citation]",

            // Preview html
            "{blockquote}": "> blockquote",
            "{cite}": "> blockquote > cite",
            "{text}": "> blockquote > p"
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock)  {

        return {

            init: function() {

                // Globals
                blocks       = self.blocks;
                composer     = blocks.composer;
                meta         = opts.meta;
                currentBlock = $();
            },


            reconstruct: function(block) {
            },

            deconstruct: function(block) {

                var blockContent = blocks.getBlockContent(block);

                // Make text container non-editable
                self.text.inside(blockContent).editable(false);

                // Make cite container non-editable
                self.cite.inside(blockContent).editable(false);

                return block;
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toText: function(block) {

            },

            toHTML: function(block) {

                var cloned = block.clone(),
                    deconstructedBlock = self.deconstruct(cloned),
                    blockContent = blocks.getBlockContent(deconstructedBlock);

                return blockContent.html();
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fielset
                self.populate(block);
            },

            deactivate: function(block) {

            },

            construct: function(data) {
                var data = $.extend({}, opts.data, data);

                return content;
            },

            refocus: function(block) {

                var data = blocks.data(block);

                if (data.style) {
                    self.setStyle(block, data.style);
                }
            },

            reset: function(block) {

                // New block doesn't need resetting.
                if (block.hasClass("is-new")) {
                    return;
                }

                var blockContent = blocks.getBlockContent(block);

                blockContent.html(self.construct());
            },

            populate: function(block) {
                var data = blocks.data(block);

                self.styleSelection('[data-style=' + data.style + ']')
                    .activateClass('active');

                // Set the default citation value
                self.citation().val(data.citation).trigger('change');
            },

            setStyle: function(block, style) {

                // Set the current style for fallback
                blocks.data(block, 'current', style);

                // Set the 'selected' class on the fieldset
                self.styleSelection()
                    .removeClass('selected');

                self.styleSelection()
                    .where('style', style)
                    .addClass('selected');

                // Trigger necessary events
                var args = [block, self, style];

                self.trigger("composerBlockQuotesSetStyle", args);
                self.trigger("composerBlockChange", args);
            },

            previewType: function(block, style) {

                clearTimeout(self.previewTimer);

                // Trigger necessary events
                var args = [block, self, style];
                self.trigger("composerBlockQuotesSetStyle", args);
                self.trigger("composerBlockChange", args);
            },

            "{styleSelection} mouseover": function(el) {

                // Set heading level to the one being hovered on
                var style = el.data('style');

                // Preview level on current block
                self.previewType(currentBlock, style);
            },

            "{styleSelection} mouseout": function(el) {

                clearTimeout(self.previewTimer);

                // Delay before reverting to original level
                self.previewTimer = $.delay(function(){

                    var currentStyle = blocks.data(currentBlock).current;

                    if (currentStyle) {
                        self.setStyle(currentBlock, currentStyle);
                    }

                }, 50);
            },

            "{styleSelection} click": function(el) {
                // Get the alert type
                var style = el.data('style'),
                    data = blocks.data(currentBlock);

                data.style = style;

                // Set the alert type
                self.setStyle(currentBlock, style);
            },

            "{self} composerBlockQuotesSetStyle": function(base, event, block, handler, style) {

                // Stop any preview timer
                clearTimeout(self.previewTimer);

                var content = blocks.getBlockContent(currentBlock);

                self.blockquote
                    .inside(content)
                    .switchClass(style);

                // Repopulate the fieldset
                self.populate(block);
            },

            "{citation} change": function(el, event) {
                var data = blocks.data(currentBlock),
                    enabled = $(el).val() == 1 ? 1 : 0,
                    addHiddenClass = enabled ? false : true,
                    blockContent = blocks.getBlockContent(currentBlock);

                data.citation = enabled;

                self.cite
                    .inside(blockContent)
                    .toggleClass('hidden', addHiddenClass);
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/readmore", function($){

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Readmore", {
        defaultOptions: {
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {

                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            toData: function(block) {
                var data = blocks.data(block);
                return data;
            },

            toText: function(block) {
                return;
            },

            toHTML: function(block) {

                // We don't want to return the html codes for the read more
                var hr = $.create('hr');

                hr.attr('id', 'system-readmore');

                return hr.toHTML();
            },

            deactivate: function(block) {

            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fielset
                self.populate(block);
            },

            deconstruct: function(data) {

            },

            construct: function(data) {
            },

            reconstruct: function(block) {
                var data = blocks.data(block),
                    blockContent = blocks.getBlockContent(block);

                // If there's a readmore block already present in the content, we need to remove it from the menu
                self.hideMenu();

                // Update the blocks content with the appropriate html codes
                blockContent.html(meta.html);
            },

            refocus: function(block) {
            },

            reset: function(block) {

            },

            populate: function(block) {
            },

            showMenu: function() {
                var menu = blocks.menu().where('type', 'readmore');

                // Hide the menu since we only want to allow this to happen once
                menu.removeClass('hide');
            },

            hideMenu: function() {
                var menu = blocks.menu().where('type', 'readmore');

                // Hide the menu since we only want to allow this to happen once
                menu.addClass('hide');
            },

            "{self} composerBlockAdd": function(el, event, block) {

                var type = blocks.getBlockType(block);

                if (type == 'readmore') {
                    self.hideMenu();
                }
            },

            "{self} composerBlockRemove": function(el, event, block) {

                var type = blocks.getBlockType(block);

                if (type == 'readmore') {
                    self.showMenu();
                }
            }
        }
    });

    module.resolve();
});

EasyBlog.module("composer/blocks/handlers/rule", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Rule", {
        defaultOptions: {
            "{rule}": "hr",
            "{styleSelection}": "[data-eb-composer-rule-style] [data-style]"
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fielset
                self.populate(block);
            },

            construct: function(data) {

                var data = $.extend({}, opts.data, data),
                    content = $("<hr/>");

                return content;
            },

            toText: function(block) {
                return;
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toHTML: function(block) {
                var data = blocks.data(block),
                    blockContent = blocks.getBlockContent(block);

                return blockContent.html();
            },

            reconstruct: function(block) {
                var data = blocks.data(block);
            },

            refocus: function(block) {
                var data = blocks.data(block);

                if (data.style) {
                    self.setStyle(block, data.style);
                }
            },

            reset: function(block) {
                var blockContent = blocks.getBlockContent(block);

                blockContent.html(self.construct());
            },

            populate: function(block) {
                var data = blocks.data(block);

                self.styleSelection('[data-style=' + data.style + ']')
                    .activateClass('active');
            },

            setStyle: function(block, style) {

                // Set the current style for fallback
                blocks.data(block, 'current', style);

                // Remove all selected class from the selection
                self.styleSelection()
                    .removeClass('selected');

                self.styleSelection()
                    .where('style', style)
                    .addClass('selected');

                // Trigger necessary events
                var args = [block, self, style];

                self.trigger('composerBlockRuleSetStyle', args);
                self.trigger('composerBlockChange', args);
            },

            previewType: function(block, style) {

                clearTimeout(self.previewTimer);

                // Trigger necessary events
                var args = [block, self, style];

                self.trigger("composerBlockRuleSetStyle", args);
                self.trigger("composerBlockChange", args);
            },

            "{styleSelection} mouseover": function(el) {

                // Set heading level to the one being hovered on
                var style = el.data('style');

                // Preview level on current block
                self.previewType(currentBlock, style);
            },

            "{styleSelection} mouseout": function(el) {

                clearTimeout(self.previewTimer);

                // Delay before reverting to original level
                self.previewTimer = $.delay(function(){

                    var currentStyle = blocks.data(currentBlock).current;

                    if (currentStyle) {
                        self.setStyle(currentBlock, currentStyle);
                    }

                }, 50);
            },

            "{styleSelection} click": function(el) {
                // Get the alert type
                var style = el.data('style'),
                    data = blocks.data(currentBlock);

                data.style = style;

                // Set the alert type
                self.setStyle(currentBlock, style);

                // Refocus on the note
                self.refocus(currentBlock);
            },

            "{self} composerBlockRuleSetStyle": function(base, event, block, handler, style) {
                // Stop any preview timer
                clearTimeout(self.previewTimer);

                // Remove all classes
                var blockContent = blocks.getBlockContent(block);

                self.rule
                    .inside(blockContent)
                    .switchClass(style);

                // Repopulate the fieldset
                self.populate(block);
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/slideshare", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Slideshare", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-slideshare-form]",
            "{insert}": "[data-slideshare-insert]",
            "{source}": "[data-slideshare-source]",
            "{loader}": "> [data-slideshare-loader]",

            // Preview
            "{preview}": "> [data-slideshare-preview]",

            "{fsSource}": "[data-fs-slideshare-source]",
            "{fsUpdate}": "[data-fs-slideshare-update]",
            "{errorMessage}": "[data-slideshare-error]"
        }, EBD.selectors)
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            toText: function(block) {
                var data = blocks.data(block);

                return data.url;
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toEditableHTML: function(block) {
                return '';
            },

            toHTML: function(block) {
                var data = blocks.data(block);

                return data.embed;
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function(block) {
            },

            construct: function(block) {
            },

            reconstruct: function(block) {
                var data = blocks.data(block);
                var overlay = block.data('overlay');
                var content = blocks.getBlockContent(block);

                // Set the overlay if it hasn't exists yet.
                if (data.embed && !overlay) {
                    self.setOverlay(block, data.embed);
                }

                // If there's no embed codes, show the form instead
                if (!data.embed) {
                    content.html($(meta.html));
                }

                // So redactor won't receive it.
                block.find(self.source.selector).off("paste").on("paste", function(event){
                    event.stopPropagation();
                });

            },

            deconstruct: function(block) {

                var content = blocks.getBlockContent(block);

                // When saving, remove the form
                self.form.inside(content).remove();
                self.loader.inside(content).remove();

                return block;
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block) {

                // When populating the fieldset for a block, reset the values
                var data = blocks.data(block);

                self.fsSource().val(data.url);
            },

            loading: function() {
                var blockContent = blocks.getBlockContent(currentBlock);

                if (!self.isLoading) {
                    self.loader.inside(blockContent).removeClass('hidden');
                    self.form.inside(blockContent).addClass('hidden');

                    self.isLoading = true;
                } else {

                    self.loader.inside(blockContent).addClass('hidden');
                    self.form.inside(blockContent).removeClass('hidden');

                    self.isLoading = false;
                }

            },

            setOverlay: function(block, embed) {

                var overlay = block.data('overlay');

                if (!overlay) {
                    overlay = composer.document.overlay.create(block);
                    var content = blocks.getBlockContent(block);
                    
                    // Append the placeholder first
                    overlay
                        .placeholder()
                        .css('height', '600px')
                        .appendTo(content);

                    // Append the embed codes now
                    overlay
                        .element()
                        .append(embed);

                    // Attach the overlay now
                    overlay.attach();

                    // Set the overlay data so we don't create overlays all the time
                    block.data('overlay', overlay);

                    return;
                }

                // Clear the element's content
                overlay.element().empty();

                // Attach the new embed codes
                overlay.element().append(embed);
            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // http://www.slideshare.net/MarkLee26/business-model-42989542
                var regex = /^http:\/\/www\.slideshare\.net\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            crawl: function(block, url) {
                var content = blocks.getBlockContent(block);
                var data = blocks.data(block);

                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return false;
                }

                // If there's an overlay hide it
                var overlay = block.data('overlay');

                if (overlay) {
                    overlay.element().empty();
                }

                // Display the loader and hide the form
                self.loading();

                EasyBlog.ajax('site/views/crawler/crawl', {
                    "url": url
                }).done(function(results) {

                    var result = results[url];

                    data.url = url;
                    data.embed = result.oembed.html;

                    self.setOverlay(block, data.embed)
                }).fail(function(message) {

                    self.errorMessage().removeClass('hide').html(message);

                }).always(function() {
                    // When it's done trigger the loading again
                    self.loading();

                    // Hide the form and loader
                    self.form.inside(content).addClass('hidden');
                });
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();
                var data = blocks.data(currentBlock);

                // Crawl the url
                self.crawl(currentBlock, url);

                // Update the fieldset url
                self.fsSource().val(url);
            },

            "{fsUpdate} click": function() {
                var url = self.fsSource().val();
                var data = blocks.data(currentBlock);

                self.crawl(currentBlock, url);
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/soundcloud", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Soundcloud", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-soundcloud-form]",
            "{insert}": "[data-soundcloud-insert]",
            "{source}": "[data-soundcloud-source]",
            "{loader}": "> [data-soundcloud-loader]",

            // Preview
            "{preview}": "> [data-soundcloud-preview]",

            "{fsSource}": "[data-fs-soundcloud-source]",
            "{fsUpdate}": "[data-fs-soundcloud-update]",
            "{errorMessage}": "[data-soundcloud-error]"

        }, EBD.selectors)
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            toText: function(block) {
                var data = blocks.data(block);

                return data.url;
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toEditableHTML: function(block) {
                return '';
            },
            
            toHTML: function(block) {
                var data = blocks.data(block);

                return data.embed;
            },

            normalize: function(data) {
                return $.extend({}, meta.data, data);
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function(block) {
            },

            construct: function(block) {
            },

            reconstruct: function(block) {

                var data = blocks.data(block);
                var overlay = block.data('overlay');
                var content = blocks.getBlockContent(block);

                if (data.embed && !overlay) {
                    self.setOverlay(block, data.embed);
                }

                if (!data.embed) {
                    content.html($(meta.html));
                }

                // So redactor won't receive it.
                block.find(self.source.selector).off("paste").on("paste", function(event){
                    event.stopPropagation();
                });

            },

            deconstruct: function(block) {
                var content = blocks.getBlockContent(block);

                // Remove unecessary items
                self.form.inside(content).remove();
                self.loader.inside(content).remove();

                return block;
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block) {

                // When populating the fieldset for a block, reset the values
                var data = blocks.data(block);

                // Update the fieldset url
                self.fsSource().val(data.url);
            },

            isLoading: false,

            loading: function() {
                var content = blocks.getBlockContent(currentBlock);

                if (!self.isLoading) {
                    self.loader.inside(content).removeClass('hidden');
                    self.form.inside(content).addClass('hidden');

                    self.isLoading = true;
                } else {

                    self.loader.inside(content).addClass('hidden');
                    self.form.inside(content).removeClass('hidden');

                    self.isLoading = false;
                }

            },

            setOverlay: function(block, embed) {

                var overlay = block.data('overlay');
                var blockContent = blocks.getBlockContent(block);

                if (!overlay) {
                    overlay = composer.document.overlay.create(block);

                    // Append the placeholder first
                    overlay.placeholder().css('height', '350px')
                        .appendTo(blockContent);

                    overlay.element().append(embed);

                    overlay.attach();
                } else {
                    // Remove existing data from overlay
                    overlay.element().empty();

                    // Attach the embed codes on the overlay
                    overlay.element().append(embed);
                }

                block.data('overlay', overlay);
            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // https://soundcloud.com/theweeknd
                // https://soundcloud.com/theweeknd/mike-will-made-it-drinks-on-us-feat-the-weeknd-swae-lee-future
                var regex = /^https:\/\/soundcloud\.com\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            crawl: function(block, url) {

                var data = blocks.data(block);
                var content = blocks.getBlockContent(block);

                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return false;
                }

                // If there's an overlay hide it
                var overlay = block.data('overlay');

                if (overlay) {
                    overlay.element().empty();
                }

                // Display the loader and hide the form
                self.loading();

                EasyBlog.ajax('site/views/crawler/crawl', {
                    "url": url
                }).done(function(results) {

                    var result = results[url];

                    // Set the data back
                    data.url = url;
                    data.embed = result.oembed.html;

                    self.setOverlay(block, data.embed);
                })
                .fail(function(message) {
                    self.errorMessage()
                        .removeClass('hide')
                        .html(message);
                })
                .always(function(){
                    // When it's done trigger the loading again
                    self.loading();

                    // Hide the form and loader
                    self.form.inside(content).addClass('hidden');
                });
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();

                // Set the source value
                self.fsSource().val(url);

                // Crawl the site now.
                self.crawl(currentBlock, url);
            },

            "{fsUpdate} click": function() {
                var url = self.fsSource().val();

                self.crawl(currentBlock, url);
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/spotify", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Spotify", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-spotify-form]",
            "{insert}": "[data-spotify-insert]",
            "{source}": "[data-spotify-source]",
            "{loader}": "> [data-spotify-loader]",

            // Preview
            "{errorMessage}": "[data-spotify-error]",
            "{preview}": "> [data-spotify-preview]",

            //fieldset
            "{fsSource}": "[data-fs-spotify-source]",
            "{fsRefreshButton}": "[data-fs-spotify-refresh]"
        }, EBD.selectors)
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toText: function(block) {
                var data = blocks.data(block);

                return data.url;
            },

            toEditableHTML: function(block) {
                return '';
            },
            
            toHTML: function(block) {
                var data = blocks.data(block);

                return data.embed;
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function(block) {
            },

            construct: function(block) {
            },

            reconstruct: function(block) {
                
                var data = blocks.data(block);
                var overlay = block.data('overlay');
                var content = blocks.getBlockContent(block);

                if (data.embed && !overlay) {
                    self.setOverlay(block, data.embed);
                }

                // If there's no embed codes, we need to display the form
                if (!data.embed) {
                    content.html($(meta.html));
                }

                // So redactor won't receive it.
                block.find(self.source.selector).off("paste").on("paste", function(event){
                    event.stopPropagation();
                });

            },

            deconstruct: function(block) {
                var content = blocks.getBlockContent(block);

                // When saving, remove the form
                self.form.inside(content).remove();
                self.loader.inside(content).remove();

                return block;
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block) {
                // When populating the fieldset for a block, reset the values
                var data = blocks.data(block);

                self.fsSource().val(data.url);
            },

            isLoading: false,

            loading: function() {
                var blockContent = blocks.getBlockContent(currentBlock);

                if (!self.isLoading) {
                    self.loader.inside(blockContent).removeClass('hidden');
                    self.form.inside(blockContent).addClass('hidden');

                    self.isLoading = true;
                } else {

                    self.loader.inside(blockContent).addClass('hidden');
                    self.form.inside(blockContent).removeClass('hidden');

                    self.isLoading = false;
                }

            },

            setOverlay: function(block, embed) {
                var overlay = block.data('overlay');

                if (!overlay) {
                    overlay = composer.document.overlay.create(block);
                    var content = blocks.getBlockContent(block);

                    // Append the placeholder into the block
                    overlay
                        .placeholder()
                        .css('height', '400px')
                        .appendTo(content);

                    // Append the embed codes into the overlay now
                    overlay.element().append(embed);

                    // Attach the overlay
                    overlay.attach();
                } else {

                    // If overlay already exist, just empty and add the embed codes again
                    overlay.element().empty();
                    overlay.element().append(embed);
                }

                // Set the overlay data now.
                block.data('overlay', overlay);
            },

            showForm: function(block) {
                var content = blocks.getBlockContent(block);

                self.form.inside(content).removeClass('hidden');
            },

            hideForm: function(block) {
                var content = blocks.getBlockContent(block);

                self.form.inside(content).addClass('hidden');
            },

            crawl: function(block, url) {
                var content = blocks.getBlockContent(block);
                var data = blocks.data(block);

                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return false;
                }

                // If there's an overlay hide it
                var overlay = block.data('overlay');

                if (overlay) {
                    overlay.element().empty();
                }

                // Display loading screen
                self.loading();


                // Crawl to get the correct spotify embed codes
                EasyBlog.ajax('site/views/crawler/crawl', {
                    "url": url
                })
                .done(function(results) {
                    // Trigger loading again
                    self.loading();

                    var result = results[url];

                    // Set the data
                    data.url = url;
                    data.embed = result.oembed.html;

                    // Hide the form
                    self.hideForm(block);

                    // Attach the overlay
                    self.setOverlay(block, data.embed);
                })
                .fail(function(message) {
                    // Toggle the loading again
                    self.loading();

                    self.errorMessage().html(message).removeClass('hide');
                });
            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // http://open.spotify.com/album/5X3IU4MDu4t0ErDR4VrPBW
                // http://open.spotify.com/artist/7CajNmpbOovFoOoasH2HaY
                // http://open.spotify.com/track/3WfITvoURyCrAal5xYMyz0
                var regex = /^http(s):\/\/open\.spotify\.com\/(track|artist|album)\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();

                // Update the url in the fieldset
                self.fsSource().val(url);

                // Crawl now
                self.crawl(currentBlock, url);
            },

            "{fsRefreshButton} click": function() {

                var url = self.fsSource().val();

                // Crawl now
                self.crawl(currentBlock, url);
            }


        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/table", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Table", {

        defaultOptions: {

            "{tableContainer}": "> .table-container",
            "{table}": "table",
            "{row}"  : "tr",
            "{cell}" : "td",
            "{cellContent}": "td div[contenteditable]",

            "{loading}": "[data-table-loading]",

            // Fieldset options
            "{tableStriped}": "[data-table-striped]",
            "{tableBordered}": "[data-table-bordered]",
            "{tableHover}": "[data-table-hover]",
            "{tableCondensed}": "[data-table-condensed]",
            "{tableRowFieldset}": "[data-table-rows]",
            "{addRow}": "[data-table-rows-add]",
            "{removeRow}": "[data-table-rows-remove]",
            "{tableColumns}": "[data-table-columns]",
            "{tableColumnsAdd}": "[data-table-columns-add]",
            "{tableColumnsRemove}": "[data-table-columns-remove]"
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {

                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fielset
                self.populate(block);

                block.on("keydown.tab", self.cellContent.selector, function(event){

                    if (event.which==9) {
                        var cell = $(event.target).parent(),
                            cells = self.cell.inside(currentBlock),
                            content = cells.eq(Math.min(cells.index(cell) + 1, cells.length - 1)).find('> div[contenteditable]');

                        // Note: If this contenteditable has no childNodes, it will break.
                        // Redactor will restore an invisibleSpace when user hits backspace
                        // until this div is empty. So this will only break if someone
                        // explicitly empty out this contenteditable.
                        composer.editor.caret.setAfter(content[0].lastChild);

                        // This prevents redactor from calling buildEventKeydownTab
                        // which executes event.preventDefault and causes caret to
                        // to get stuck in the beginning on FF.
                        event.stopPropagation();
                    }
                });
            },

            deactivate: function(block) {

                block.off("keydown.tab");
            },

            construct: function(data) {
                return content;
            },

            deconstruct: function(block) {

            },

            reconstruct: function(block) {

            },

            refocus: function(block) {
                var blockContent = blocks.getBlockContent(block),
                    activeCell = block.data("lastFocused") || self.cell.inside(blockContent).filter(":first");

                // TODO: Capture lastFocused column
                activeCell.focus();
            },

            reset: function(block) {
            },

            populate: function(block) {
                var data = blocks.data(block);

                self.tableStriped().val(data.striped).trigger('change');
                self.tableBordered().val(data.bordered).trigger('change');
                self.tableHover().val(data.hover).trigger('change');
                self.tableCondensed().val(data.condensed).trigger('change');

                self.tableRowFieldset().val(data.rows);
                self.tableColumns().val(data.columns);
            },

            toHTML: function(block) {
                self.loader(false);

                var blockContent = blocks.getBlockContent(block),
                    table = self.table.inside(blockContent).clone();

                $.each(table.find('td'), function(i, cell) {
                    cell = $(cell);

                    cell.html(cell.find('div[contenteditable]').html());
                });

                return table[0].outerHTML;
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toText: function(block) {
                var blockContent = blocks.getBlockContent(block);

                return self.table.inside(blockContent).text();
            },

            // Fieldset methods
            "{tableStriped} change": function(el, ev) {
                var blockContent = blocks.getBlockContent(currentBlock),
                    val = el.val();

                self.table
                    .inside(blockContent)
                    .toggleClass('table-striped', val == 1);

                blocks.data(currentBlock, 'striped', val == 1 ? 1 : 0);
            },

            "{tableBordered} change": function(el, ev) {

                var blockContent = blocks.getBlockContent(currentBlock),
                    val = el.val();

                self.table
                    .inside(blockContent)
                    .toggleClass('table-bordered', val == 1);

                blocks.data(currentBlock, 'bordered', val == 1 ? 1 : 0);
            },

            "{tableHover} change": function(el, ev) {

                var blockContent = blocks.getBlockContent(currentBlock),
                    val = el.val();

                self.table
                    .inside(blockContent)
                    .toggleClass('table-hover', val == 1);

                blocks.data(currentBlock, 'hover', val == 1 ? 1 : 0);
            },

            "{tableCondensed} change": function(el, ev) {

                var blockContent = blocks.getBlockContent(currentBlock),
                    val = el.val();

                self.table
                    .inside(blockContent)
                    .toggleClass('table-condensed', val == 1);

                blocks.data(currentBlock, 'condensed', val == 1 ? 1 : 0);
            },

            loader: function(state) {
               
                var content = blocks.getBlockContent(currentBlock);
                var container = self.tableContainer.inside(content);

                if (state === false) {
                    self.loading
                        .inside(content)
                        .remove();

                    return;
                }

                if (self.loading.inside(content).length > 0) {
                    return;
                }

                var table = self.table.inside(container);

                var placeholder = $.create('div')
                        .attr('data-table-loading', '')
                        .css({
                            top: 0,
                            left: 0,
                            width: '100%',
                            height: '50px',
                            position: 'absolute',
                            background: 'rgba(0,0,0,0.5)',
                        })
                        .html(
                            $.create('div')
                                .css({
                                    textAlign: 'center',
                                    position: 'relative',
                                    top: '50%',
                                    lineHeight: '20px',
                                    marginTop: '-10px'
                                })
                                .html('<i class="fa fa-circle-o-notch fa-spin"></i>')
                        );

                container.append(placeholder);
            },

            "{tableRowFieldset} change": function(el, ev) {
                var content = blocks.getBlockContent(currentBlock);
                var value = el.val();
                var data = blocks.data(currentBlock);

                if (isNaN(value) || parseInt(value) < 1 || $.isEmpty(value)) {
                    el.val(value = 1);
                }

                value = parseInt(value);

                var diff = value - self.row.inside(content).length;

                if (diff == 0) {
                    return;
                }

                data.rows = value;

                // Why should we detach the table?
                // var table = self.table.inside(blockContent).detach();
                var table = self.table.inside(content);

                // Detach the table.
                // table.detach();

                setTimeout(function() {

                    if (diff < 0) {
                        self.row.inside(table).slice(diff).remove();
                    }

                    if (diff > 0) {
                        var totalColumns = parseInt(self.tableColumns().val());
                        var html = Array(diff + 1).join("<tr>" + Array(totalColumns + 1).join('<td><div contenteditable="true">&#8203;</div></td>') + "</tr>");

                        table.append(html);
                    }

                    table.appendTo(content);
                    // content.html(table);

                    // self.loader(false);
                }, 50);
            },

            "{tableColumns} change": function(columnFieldset, ev) {
                var content = blocks.getBlockContent(currentBlock);
                var value = columnFieldset.val();
                var data = blocks.data(currentBlock);

                // Ensure that there's a value in the column
                if (isNaN(value) || parseInt(value) < 1 || $.isEmpty(value)) {
                    value = 1;

                    columnFieldset.val(value);
                }

                value = parseInt(value);

                // Get the first row
                var firstRow = self.row.inside(content).filter(':first-child');

                var diff = value - self.cell.inside(firstRow).length;

                if (diff == 0) {
                    return;
                }

                // Set the number of columns on the block data.
                data.columns = value;

                var table = self.table.inside(content);

                // Why do we need to detach the table?
                // var table = self.table.inside(content).detach();

                // Add a loader in the block
                // self.loader();

                setTimeout(function() {
                    var rows = self.row.inside(table);

                    // If there's lesser columns, it means we should substract
                    if (diff < 0) {
                        $.each(rows, function(i, row) {
                            self.cell.inside(row).slice(diff).remove();
                        });
                    }

                    // If there's more columns, it means we should add
                    if (diff > 0) {
                        var html = Array(diff + 1).join('<td><div contenteditable="true">&#8203;</div></td>');

                        rows.append(html);
                    }

                    table.appendTo(content);
                }, 50);
            },

            "{addRow} click": function(el, ev) {
                var content = blocks.getBlockContent(currentBlock);
                var total = parseInt(self.tableRowFieldset().val()) + 1;
                
                self.tableRowFieldset().val(total)
                    .trigger('change');
            },

            "{removeRow} click": function(el, ev) {
                var content = blocks.getBlockContent(currentBlock);
                var value = Math.max(parseInt(self.tableRowFieldset().val()) - 1, 1);

                self.tableRowFieldset().val(value)
                    .trigger('change');
            },

            "{tableColumnsAdd} click": function(el, ev) {

                var columns = parseInt(self.tableColumns().val()) + 1;

                self.tableColumns()
                    .val(columns)
                    .trigger('change');
            },

            "{tableColumnsRemove} click": function(el, ev) {
                self.tableColumns().val(Math.max(parseInt(self.tableColumns().val()) - 1, 1)).trigger('change');
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/tabs", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Tabs", {
        defaultOptions: {

            "{tabHeader}": ".nav-tabs",
            "{tabHeaderListItem}": ".nav-tabs > li",
            "{tabHeaderListItemContent}": ".nav-tabs > li a",
            "{tabContent}": ".tab-content",
            "{tabPane}": "> .tab-content > .tab-pane",

            "{titleItem}" : "[data-tab-header-item]",

            // Fieldset options
            "{control}": "[data-tabs-control]",

            "{controlItemContent}": "[data-tabs-control] [data-listbox-item-content]"
       }
    }, function(self, opts, base, composer, blocks, meta, currentBlock)  {

        return {
            init: function() {

                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();

                // Update default data and extract template from meta content
                // var ref = $(meta.content);
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fielset
                self.populate(block);
            },

            deactivate: function(block) {

            },

            construct: function(data) {

                var data = $.extend({}, opts.data, data);

                // TODO: Review this later
                // $.each(data.tabs, function(i, tab){

                //     var tab = self.tab.create(tab),
                //         tabPane = self.tabPane.create(tab);
                // });

                return content;
            },

            reconstruct: function(block) {
            },

            deconstruct: function(block) {
                $.each(block.find('[data-tab-wrapper]'), function(i, wrapper) {
                    wrapper = $(wrapper);

                    var parent = wrapper.parent();

                    parent.removeAttr('contenteditable');

                    wrapper.children().appendTo(parent);

                    wrapper.remove();

                    self.tabHeaderListItemContent.inside(block).eq(i).removeAttr('contenteditable');
                });

                return block;
            },

            refocus: function(block) {
                // Get active tab pane
                var activeTabPane = self.tabPane.inside(block).filter(".active");

                // Focus on active tab pane
                activeTabPane.focus();
            },

            reset: function(block) {
            },

            recover: function(block) {

            },

            populate: function(block) {
                var data = blocks.data(block);

                self.control()
                    .listbox()
                    .listbox('populate', data.tabs, function(item, content) {
                        item.listboxitem('content', content);
                    });
            },

            revert: function(block) {
            },

            toHTML: function(block) {

                var cloned = block.clone(),
                    deconstructedBlock = self.deconstruct(cloned),
                    blockContent = blocks.getBlockContent(deconstructedBlock)

                return blockContent.html();
            },

            toData: function(block) {
                var data = blocks.data(block);
                return data;
            },

            toText: function(block) {
                var cloned = block.clone();

                return self.deconstruct(cloned).text();
            },

            // custom methods
            tab: {
                create: function(count) {
                    var newId = $.uid('tab-'),
                        headerTabHtml = '<li><a href="#' + newId + '" role="tab" data-bp-toggle="tab" contenteditable="true"></a></li>';

                    // tab content wrapper
                    var tabHtml = $('<div></div>', {
                        'class': 'tab-pane',
                        'id': newId
                    });

                    // nested block wrapper
                    var wrapper = $('<div></div>', {
                        'class': 'ebd-nest',
                        'data-type': 'block',
                        'data-tab-wrapper': ''
                    });

                    // adding text block into nested block wrapper
                    var textBlock = blocks.constructNestedBlock("text");
                    wrapper.wrapInner(textBlock);

                    // adding nested block wrapper into tab content wrapper.
                    tabHtml.wrapInner(wrapper);

                    // adding tab content wrapper into tab block
                    self.tabContent.inside(currentBlock).append(tabHtml);
                    self.tabHeader.inside(currentBlock).append(headerTabHtml);

                    // self.tab.focusLast();
                    // self.tab.reCalTabs();
                },

                remove: function(index) {
                    self.tabPane.inside(currentBlock).eq(index).remove();
                    self.tabHeaderListItem.inside(currentBlock).eq(index).remove();
                },

                focus: function(index) {
                    self.tabHeaderListItem.inside(currentBlock).eq(index).find('a').tab('show');
                }
            },


            "{tabHeaderListItem} click": function(el) {
                el.find('a').tab('show');
            },

            "{tabHeaderListItemContent} keyup": $.debounce(function(el, ev) {

                var keycode = (ev.keyCode ? ev.keyCode : ev.which);

                // disallowed newline
                if(keycode != '13') {

                    var index = el.parents('.nav-tabs > li').index(),
                        content = el.html().replace(/(<br>\s*)+$/, '');

                    self.control().listbox('getItems').eq(index).listboxitem('content', content);

                    blocks.data(currentBlock).tabs[index].content = content;
                }

            }, 250),

            // prevent the tab header to redirect when user click on the tab
            "{tabHeaderListItemContent} click": function(el, ev) {
                ev.preventDefault();
            },

            "{tabHeaderListItemContent} keydown": function(el, ev) {
                var keycode = (ev.keyCode ? ev.keyCode : ev.which);

                // disallowed newline
                if(keycode == '13') {
                    ev.preventDefault();
                }
            },


            // Fieldset method
            "{control} listboxBeforeAddItem": function(el, ev, item) {
                self.tab.create();

                var data = item.listboxitem('toData');

                self.tabHeaderListItem.inside(currentBlock).filter(':last').find('a').html(data.content);
            },

            "{control} listboxAfterAddItem": function(el, ev, item) {
                blocks.data(currentBlock).tabs = self.control().listbox('toData');

                if (self.tabContent.inside(currentBlock).length == 1) {
                    self.tabHeaderListItem.inside(currentBlock).filter(':first').find('a').tab('show');
                }
            },

            "{control} listboxBeforeRemoveItem": function(el, ev, item) {
                var index = item.index();

                self.tab.remove(index);
            },

            "{control} listboxAfterRemoveItem": function(el, ev, item) {
                blocks.data(currentBlock).tabs = self.control().listbox('toData');

                if (self.tabContent.inside(currentBlock).length == 1) {
                    self.tabHeaderListItem.inside(currentBlock).filter(':first').find('a').tab('show');
                }
            },

            "{control} listboxAfterToggleDefault": function(el, ev, item) {
                blocks.data(currentBlock).tabs = self.control().listbox('toData');
            },

            "{controlItemContent} keyup": $.debounce(function(el, ev) {

                var keycode = (ev.keyCode ? ev.keyCode : ev.which);

                // disallowed newline
                if(keycode != '13') {
                    var item = el.parents('[data-listbox-item]'),
                        index = item.index(),
                        content = el.html().replace(/(<br>\s*)+$/, '');

                    self.tabHeaderListItem.inside(currentBlock).eq(index).find('a').html(content);

                    blocks.data(currentBlock).tabs[index].content = content;
                }
            }, 250),


            "{controlItemContent} keypress": function(el, ev) {
                var keycode = (ev.keyCode ? ev.keyCode : ev.which);

                // disallowed newline
                if(keycode == '13') {
                    ev.preventDefault();
                }
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/text", function($) {

var module = this;

EasyBlog.Controller("Composer.Blocks.Handlers.Text", {
    defaultOptions: {

        "{block}": ".ebd-block[data-type=text]",
        "{blockWrapper}": "> [data-eb-text-block-wrapper]",
        "{contentWrapper}": "> [data-eb-text-block-wrapper] > [data-eb-text-content-wrapper]",
        "{allContentWrapper}": "[data-eb-text-content-wrapper]",
        "{lastParagraph}": "> p:last"
    }
}, function(self, opts, base, composer, blocks, meta, currentBlock) { return {

    init: function() {
        // Globals
        blocks = self.blocks;
        composer = blocks.composer;
        meta = opts.meta;
        currentBlock = $();
    },

    activateKeys: function() {
        // I want to disable the pg up and down keys
        $(document).off('keydown.blocks.text');
    },

    deactivateKeys: function() {

        // I want to disable the pg up and down keys
        $(document).on('keydown.blocks.text', function(event) {
            var key = event.which;

            if (key == 33 || key == 34) {
                event.preventDefault();
                return false;
            }

            return true;
        });

    },

    activate: function(block) {

        self.deactivateKeys();

        // Set as current block
        currentBlock = block;

        // Populate fieldset
        self.populate();
    },

    deactivate: function(block) {
        // I need to re-activate the keys here.
        self.activateKeys();
    },

    construct: function(data) {

        var block = blocks.createBlock("text");

        // Create block content
        var content = blocks.getBlockContent(block);

        content.html(meta.data.content);

        return block;
    },

    reconstruct: function(block) {

        var blockContent = blocks.getBlockContent(block);

        // If block wrapper does not exist
        var blockWrapper = self.blockWrapper.inside(blockContent);

        if (!blockWrapper.length) {

            // Create block wrapper
            blockWrapper =

                $(meta.blockWrapper)
                    // Wrap child nodes in block wrapper
                    .append(blockContent[0].childNodes)
                    // Append block wrapper to block content
                    .appendTo(blockContent);
        }

        // If content wrapper does not exist
        var contentWrapper = self.contentWrapper.inside(blockContent);

        if (!contentWrapper.length) {

            // Collect content nodes
            var contentNodes = [];
            $.each(blockWrapper[0].childNodes, function() {
                if ($(this).is(EBD.block)) return;
                contentNodes.push(this);
            });

            // Create content wrapper
            contentWrapper =
                $(meta.contentWrapper)
                    // Wrap content nodes in content wrapper
                    .append(contentNodes)
                    // Append content wrapper to block wrapper
                    .appendTo(blockWrapper);
        }
    },

    //
    // Deconstruct the text block
    //
    deconstruct: function(block) {

        // Get block content
        var blockContent = blocks.getBlockContent(block);

        // Get content wrapper
        var contentWrapper = self.contentWrapper.inside(blockContent);

        // If we can't find any content wrapper, we'll assume that this is empty
        if (contentWrapper.length == 0) {
            return block;    
        }
        
        // Get content nodes & nested blocks
        var contentNodes = contentWrapper[0].childNodes;
        var nestedBlocks = blockContent.find(EBD.immediateNestedBlock);

        // Empty out block content, append nested blocks & append content nodes
        blockContent
            .empty()
            .append(nestedBlocks)
            .append(contentNodes);

        return block;
    },

    refocus: function(block) {

        // var blockContent = blocks.getBlockContent(block);

        // Focus on wrapper
        // var wrapper = self.wrapper.inside(blockContent);
        // wrapper.focus();

        // Set caret to last paragraph
        // var lastParagraph = self.lastParagraph.inside(wrapper);
        // composer.editor.caret.setEnd(lastParagraph);
    },

    reset: function(block) {
    },

    populate: function(block) {
    },

    recover: function(block) {
    },

    revert: function(block) {
    },

    toText: function(block) {

        var clone = block.clone();
        var deconstructedBlock = self.deconstruct(clone);
        var content = blocks.getBlockContent(deconstructedBlock).text();

        return content;
    },

    toHTML: function(block) {

        var clone = block.clone();
        var deconstructedBlock = self.deconstruct(clone);
        var content = blocks.getBlockContent(deconstructedBlock).html();

        return content;
    },

    toData: function(block) {
    },

    "{self} composerListFormat": function(base, event, block) {

        var blockType = blocks.getBlockType(block);

        if (blockType=="text") {

            // FF fix: When formatting list, wrapper may disappear.
            self.reconstruct(block);

            // Chrome fix: List falls inside marker, need to move it out.
            block.find(".redactor-selection-marker")
                .each(function(){
                    var marker = $(this);
                    if (marker.find("ul, ol").length) {
                        marker.children().insertBefore(marker);
                    }
                });
        }
    },

    "{allContentWrapper} mouseup": function(allContentWrapper) {

        var block = blocks.block.of(allContentWrapper);
        blocks.font.populateFontFormatting(block);
    },

    "{allContentWrapper} keyup": $.debounce(function(allContentWrapper) {

        var block = blocks.block.of(allContentWrapper);
        blocks.font.populateFontFormatting(block);

    }, 100)

}});

module.resolve();

});

EasyBlog.module("composer/blocks/handlers/tweet", function($){

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Tweet", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-tweet-form]",
            "{insert}": "[data-tweet-insert]",
            "{source}": "[data-tweet-source]",
            "{loader}": "> [data-codepen-loader]",

            // Preview
            "{preview}": "> [data-tweet-preview]",

            "{fsSource}": "[data-fs-tweet-source]",
            "{fsUpdate}": "[data-fs-tweet-update]",
            "{errorMessage}": "[data-tweet-error]"
        }, EBD.selectors)
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                // Globals
                blocks       = self.blocks;
                composer     = blocks.composer;
                meta         = opts.meta;
                currentBlock = $();
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toText: function(block) {
                var data = blocks.data(block);

                return data.source;
            },

            toEditableHTML: function(block) {
                return '';
            },
            
            toHTML: function(block) {
                var data = blocks.data(block);

                return '<iframe src="' + data.source + '" />';
            },

            activate: function(block) {
                // Set as current block
                currentBlock = block

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function(block) {
            },

            construct: function(block) {

                // If we ever need to contstruct this block programmatically, we need to update this.
            },

            reconstruct: function(block) {

                var data = blocks.data(block);
                var content = blocks.getBlockContent(block);
                var overlay = block.data('overlay');

                if (data.embed && !overlay) {
                    self.setOverlay(block, data.embed);
                }

                if (!data.source) {
                    content.html($(meta.html));
                }

                // So redactor won't receive it.
                block.find(self.source.selector).off("paste").on("paste", function(event){
                    event.stopPropagation();
                });

            },

            deconstruct: function(block) {
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block)  {
                // When populating the fieldset for a block, reset the values
                var data = blocks.data(block);
            },

            loading: function() {
                var content = blocks.getBlockContent(currentBlock);

                if (!self.isLoading) {
                    self.loader.inside(content).removeClass('hidden');
                    self.form.inside(content).addClass('hidden');

                    self.isLoading = true;
                } else {

                    self.loader.inside(content).addClass('hidden');
                    self.form.inside(content).removeClass('hidden');

                    self.isLoading = false;
                }

            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // https://twitter.com/stackideas/status/562322053668167681
                var regex = /^https:\/\/twitter\.com\/(.*)\/status\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            crawl: function(block, url) {

                var content = blocks.getBlockContent(block);
                var data = blocks.data(block);


                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return false;
                }

                // If there's an overlay hide it
                var overlay = block.data('overlay');

                if (overlay) {
                    overlay.element().empty();
                }

                // When it's done trigger the loading again
                self.loading();

                EasyBlog.ajax('site/views/crawler/crawl', {
                    url: url
                }).done(function(results) {

                    // When it's done trigger the loading again
                    self.loading();

                    var result = results[url];

                    // Set the data back
                    data.url = url;
                    data.embed = result.oembed.html;
                    data.source = url;

                    self.setOverlay(block, data.embed);

                    // Hide the form and loader
                    self.form.inside(content).addClass('hidden');

                }).fail(function(message) {
                    // When it's done trigger the loading again
                    self.loading();

                    self.errorMessage().html(message).removeClass('hide');
                }).always(function() {


                });
            },

            setOverlay: function(block, embed) {
                var content = blocks.getBlockContent(block);
                var overlay = block.data('overlay');

                if (!overlay) {
                    overlay = composer.document.overlay.create(block);

                    // Overlay placeholder is just a placeholder so that the overlay element can be displayed within the placeholder region
                    // Append the placeholder first
                    overlay
                        .placeholder()
                        .css('height', '400px')
                        .appendTo(content);

                    // Append the embed codes now
                    overlay
                        .element()
                        .append(embed);

                    // Attaching is just like execute.
                    // Attach the overlay now
                    overlay.attach();

                } else {
                    overlay.element().empty();
                    overlay.element().append(embed);
                }

                block.data('overlay', overlay);
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                // Add overlay when the user add's a new link.
                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();

                // Update the ulr
                self.fsSource().val(url);

                self.crawl(currentBlock, url);
            },

            "{fsUpdate} click": function() {
                var url = self.fsSource().val();
                var data = blocks.data(currentBlock);

                self.crawl(currentBlock, url);
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/handlers/video", function($){

var module = this;

var selectRatioView = "view-selectratio";
var customRatioView = "view-customratio";

var videoSizeProps = [
    "video-width",
    "video-height"
];

var numsliderElements = [
    "numslider",
    "numslider-toggle",
    "numslider-widget",
    "numslider-value",
    "numslider-input",
    "numslider-units",
    "numslider-unit",
    "numslider-current-unit"
];

var getCssProp = function(prop) {
    return prop.replace(/video-/,"");
}

var parseUnit = function(val) {
    return val.toString().match("%") ? "%" : "px";
};

var roundToDecimalPoint = function(value, n) {
    var p = Math.pow(10, n);
    return Math.round(value * p) / p;
};

var getRatioInDecimal = ratioDecimal = function(ratio) {

    // If decimal was given, just return the ratio.
    if ($.isNumeric(ratio)) return ratio;
    var parts = ratio.split(":");

    return parts[0] / parts[1];
};

var getRatioInPercent = ratioPercent = function(ratio, unit) {
    return roundToDecimalPoint(getRatioInDecimal(ratio) * 100, 2) + (unit ? "%" : 0);
};

var getRatioInPadding = ratioPadding = function(ratio) {
    return roundToDecimalPoint(1 / getRatioInDecimal(ratio) * 100, 2) + "%";
};

var sanitizeRatio = function(ratio) {
    ratio = $.trim(ratio);
    if (/\:/.test(ratio)) {
        var parts = ratio.split(":");
        return parseInt(parts[0]) + ":" + parseInt(parts[1]);
    } else {
        return parseFloat(ratio) || 0;
    }
};

EasyBlog.require()
.library(
    'plupload2',
    'videojs'
).done(function(){

EasyBlog.Controller("Composer.Blocks.Handlers.Video", {
    elements: [
        "[data-eb-{file-error}]",

        "[data-eb-video-size-field] [data-eb-{" + numsliderElements.join("|") + "}]",
        "^videoSizeField .eb-composer-field[data-name={" + videoSizeProps.join("|") + "}]",
        "[data-eb-video-{ratio-button|ratio-label|ratio-customize-button|ratio-cancel-button|ratio-use-custom-button|ratio-cancel-custom-button|alignment-selection|size-field|ratio-selection|ratio-input}]"
    ],
    defaultOptions: {

        // Browse button in placeholder
        "{browseButton}": ".eb-composer-placeholder-video [data-eb-mm-browse-button]",

        "{placeholder}": "[data-eb-composer-video-placeholder]",
        "{player}": "[data-video-player]",
        "{dropElement}": "[data-plupload-drop-element]",

        // Fieldset options
        "{autoplay}": "[data-video-fieldset-autoplay]",
        "{loop}": "[data-video-fieldset-loop]",
        "{muted}": "[data-video-fieldset-muted]",

        "{controls}": "[data-video-controls]",

        "{videoContainer}": ".eb-video",
        "{videoViewport}": ".eb-video-viewport",
        "{videoPlayer}": "video",
        "{videoSource}": "source"
    }
}, function(self, opts, base, composer, blocks, meta, currentBlock, getVideoContainer, dimensions, mediaManager) { return {

    init: function() {

        // Globals
        blocks = self.blocks;
        composer = blocks.composer;
        meta = opts.meta;
        currentBlock = $();
        dimensions = blocks.dimensions;
        mediaManager = EasyBlog.MediaManager;

        // INTERNAL HACK
        // Duckpunch .of() to accept prop
        $.each(numsliderElements, function(i, element){
            var method = $.camelize(element);
            self[method].of = $.memoize(function(prop) {
                // Get numslider field of this prop and return
                // numslider element under this numslider field
                var numsliderField = self.getVideoSizeField(prop);
                return self[method].under(numsliderField);
            });
        });

        // Speed up retrieval of get video container
        var videoContainers = {};
        getVideoContainer = function(block){
            return $(
                videoContainers[block.data("uid")] ||
                (videoContainers[block.data("uid")] = self.videoContainer.inside(block)[0])
            );
        }

        // Speed up retrieval of get video viewport
        getVideoViewport = $.memoize(function(block){
            return self.videoViewport.inside(block);
        }, function(block){
            return block.data("uid");
        });
    },

    normalize: function(data) {
        return $.extend({}, meta.data, data);
    },

    activate: function(block) {

        // Set as current block
        currentBlock = block

        // Populate fieldset
        self.populate(block);
    },

    deactivate: function(block) {
    },

    construct: function(data) {

        var block = blocks.createBlockContainer('video');
        var blockData = blocks.data(block);

        $.extend(blockData, data);

        return block;
    },

    constructFromMediaFile: function(mediaFile) {

        var key = mediaFile.data("key");
        var uri = mediaManager.getUri(key);

        // Create block container first
        var block = blocks.createBlockContainer("video");
        var blockContent = blocks.getBlockContent(block);
        var data = blocks.data(block);

        // Add loading indicator
        block.addClass("is-loading");

        // Get media meta
        mediaManager.getMedia(uri)
            .done(function(media){

                // Give it some time for block to drop & release
                // before creating the video player.
                setTimeout(function(){
                    var mediaMeta = media.meta;
                    var url = mediaMeta.url;
                    self.createPlayer(block, url);
                }, 250);
            })
            .fail(function(){
            })
            .always(function(){
                block.removeClass("is-loading");
            });

        return block;
    },

    reconstruct: function(block) {

        // Disable content editable
        block.editable(false);

        var data = blocks.data(block);

        // Has Video
        if (self.hasVideo(block)) {
            var data = blocks.data(block);
            self.createPlayer(block, data.url);
        } else {

            // Has Placeholder
            var placeholder = self.placeholder.inside(block);

            if (placeholder.length > 0) {
                EasyBlog.MediaManager.uploader.register(placeholder);
            }
        }
    },

    deconstruct: function(block) {
    },

    refocus: function(block) {
    },

    reset: function(block) {
    },

    populate: function(block) {

        var hasVideoContainer = self.hasVideoContainer(block);

        // Hide fieldgroup if there is no video
        var fieldgroup = blocks.panel.fieldgroup.get("video");
        fieldgroup.toggleClass("is-new", !hasVideoContainer);

        // Populate fieldset if there is video
        if (hasVideoContainer) {
            self.populateVideoSize(block);
            self.populateVideoControls(block);
        }
    },

    toData: function(block) {

        var data = blocks.data(block);
        var videoContainer = getVideoContainer(block);

        if (videoContainer.length > 0) {
            var videoContainerStyle = videoContainer[0].style;

            data.width  = videoContainerStyle.width;
            data.height = videoContainerStyle.height;
        }

        return data;
    },

    toText: function(block) {
        return;
    },

    toHTML: function(block) {
        return;
    },

    toLegacyShortcode: function(meta, block) {
        var str = '[embed=video][/embed]';
        var width = self.getVideoSize(block, 'width');
        var height = self.getVideoSize(block, 'height');
        var data = blocks.data(block);

        var obj = {
            "width": width,
            "height": height,
            "uri": meta.uri,
            "autoplay": data.autoplay ? "1" : "0",
            "muted": data.muted ? "1" : "0",
            "loop": data.loop ? "1" : "0"
        };

        var str = '[embed=video]' + JSON.stringify(obj) + '[/embed]';

        return str;
    },

    toEditableHTML: function(block) {
        return;
    },

    //
    // Video Uploads
    //
    "{placeholder} mediaUploaderFilesAdded": function(placeholder, event, uploader, files) {
        EasyBlog.MediaManager.uploader.addItem(files[0], placeholder);
    },

    "{placeholder} mediaUploaderFileUploaded": function(placeholder, event, uploader, file, data) {

        var response = data.response;
        var mediaItem = response.media;
        var mediaMeta = mediaItem.meta;

        var block = blocks.block.of(placeholder);

        setTimeout(function() {

            self.createPlayer(block, mediaMeta.url);

            // Populate block again
            if (block.hasClass("active")) {
                self.populate(block);
            }

        }, 600);
    },

    "{placeholder} mediaUploaderFileError": function(placeholder, event, uploader, error) {

        if (error.code == $.plupload2.FILE_EXTENSION_ERROR) {
            self.fileError.inside(currentBlock).removeClass('hide');
        }
    },

    //
    // Video Player
    //
    createPlayer: function(block, url) {

        var data = blocks.data(block);

        var uid = data.uid || (data.uid = $.uid("video-"));
        var videoContainer = $(meta.player).clone();
        var videoPlayer = self.videoPlayer.inside(videoContainer);
        var videoSource = self.videoSource.inside(videoContainer);

        // Set id, width, height, url
        videoPlayer.attr("id", uid);
        videoSource.attr("src", url);

        // Set the url of the video on the data
        data.url = url;

        data.width  && videoContainer.css("width", data.width);
        data.height && videoContainer.css("height", data.height);

        // Remove any assigned width/height.
        dimensions.toFluidWidth(block);
        dimensions.toFluidHeight(block);

        content = blocks.getBlockContent(block);

        // Insert video container onto block content
        blocks.getBlockContent(block)
            .empty()
            .append(videoContainer);

        // Initialize videojs
        videojs(uid, {
            controls: true,
            autoplay: false
        }, function() {
            // Determines if the player should be muted.
            this.muted(data.muted);
        });
    },

    //
    // Video Controls
    //
    populateVideoControls: function(block) {

        // Get block data
        var data = blocks.data(block);

        // Update the fieldsets
        self.autoplay()
            .val(data.autoplay ? 1 : 0)
            .trigger("change");

        self.loop()
            .val(data.loop ? 1 : 0)
            .trigger("change");

        self.muted()
            .val(data.muted ? 1 : 0)
            .trigger("change");
    },

    "{muted} change": function(el, event) {

        var data = blocks.data(currentBlock);
        data.muted = el.val() == 1 ? true : false;
    },

    "{autoplay} change": function(el, event) {

        var data = blocks.data(currentBlock);
        data.autoplay = el.val() == 1 ? true : false;
    },

    "{loop} change": function(el, event) {

        var data = blocks.data(currentBlock);
        data.loop = el.val() == 1 ? true : false;
    },

    //
    // Video Size
    //
    populateVideoSize: function(block, props) {

        // Populate slider, input & unit for video width & height
        var props = props || ["width", "height"];
        var prop;

        while (prop = props.shift()) {

            var value  = prop=="width" ? self.getVideoWidth(block) : self.getVideoHeight(block); // 1280
            var number = parseFloat(value); // 1280, 100
            var unit   = parseUnit(value); // px, %

            // Update numslider widget
            // only if user is not resizing from slider
            if (self.resizingFromSlider!==prop) {

                // Pixel unit
                if (unit=="px") {
                    var sliderOptions = {
                        start: number,
                        step: 1,
                        range: {
                            min: 1,
                            max: 1600
                        },
                        pips: {
                            mode: "values",
                            values: [64, 320, 640, 960, 1280, 1600],
                            density: 4
                        }
                    };
                }

                // Percent unit
                if (unit=="%") {
                    var sliderOptions = {
                        start: number,
                        step: 1,
                        range: {
                            min: 1,
                            max: 100
                        },
                        pips: {
                            mode: "values",
                            values: [0, 20, 40, 60, 80, 100],
                            density: 5
                        }
                    }
                }

                // Set up slider
                self.numsliderWidget.of(prop)
                    .find(".noUi-pips")
                    .remove()
                    .end()
                    .noUiSlider(sliderOptions, true)
                    .noUiSlider_pips(sliderOptions.pips);
            }

            // Update numslider input
            self.numsliderInput.of(prop)
                .val(Math.round(number));

            // Update numslider current unit
            self.numsliderCurrentUnit.of(prop)
                .html(unit);

            // Update numslider unit dropdown
            self.numsliderUnit.of(prop)
                .removeClass("active")
                .where("unit", '"' + unit + '"')
                .addClass("active");
        }

        // Also populate video ratio and alignment
        self.populateVideoRatio(block);
        self.populateVideoAlignment(block);
    },

    getVideoWidth: function(block) {

        var videoContainer = getVideoContainer(block);
        var videoContainerStyle = videoContainer[0].style;

        // Nested block (%) - assigned block width
        if (blocks.isNestedBlock(block)) {

            // Get assigned block width
            var assignedBlockWidth = block[0].style.width;

            // If assigned block width has a % on it, use it.
            if (/%/.test(assignedBlockWidth)) {
                return assignedBlockWidth;
            }
        }

        // Root block (%/px) or nested block (px)
        // Get assigned width, else get computed width.
        return videoContainerStyle.width || videoContainer.css("width");
    },

    getVideoHeight: function(block) {

        var videoContainer = getVideoContainer(block);
        var videoContainerStyle = videoContainer[0].style;

        // Root/nested (%)  - computed container height
        // Root/nested (px) - assigned container height
        return videoContainerStyle.height || videoContainer.css("height");
    },

    getVideoSize: function(block, prop) {

        if (prop=="width") return self.getVideoWidth(block);
        if (prop=="height") return self.getVideoHeight(block);
    },

    getVideoComputedWidth: function(block) {

        return getVideoContainer(block).width();
    },

    getVideoComputedHeight: function(block) {

        return getVideoContainer(block).outerHeight();
    },

    setVideoWidth: function(block, width) {

        var videoContainer = getVideoContainer(block);
        var videoViewport = getVideoViewport(block);
        var ratio = self.getVideoRatio(block);
        var unit = parseUnit(width);

        // Get computed height before a new width assigned
        var computedHeight = self.getVideoComputedHeight(block);

        // Add is-responsive class if unit is %
        videoContainer.toggleClass("is-responsive", unit=="%");

        // Nested block
        if (blocks.isNestedBlock(block)) {

            if (unit=="%") {
                // Assign new width
                block.css("width", width);
                videoContainer.css("width", "");
            }

            if (unit=="px") {
                block.css("width", "auto");
                videoContainer.css("width", width);
            }

        // Root block
        } else {
            videoContainer.css("width", width);
        }

        // Fluid video will need a ratio even if its unlocked.
        // Passing in null value to adjustVideoRatio creates a new
        // ratio based on current computed width & height.
        if (ratio==0 && unit=="%") {
            ratio = null;
        }

        // Adjust video ratio
        self.adjustVideoRatio(block, ratio);
    },

    setVideoFluidWidth: function(block, width) {

        var videoContainer = getVideoContainer(block);

        if (blocks.isNestedBlock(block)) {

            // Convert back to fixed width then assign a width
            dimensions.toAutoWidth(block);
            videoContainer.css("width", width);

            // Then from the new fixed width, convert it back to fluid.
            dimensions.toFluidWidth(block);
            videoContainer.css("width", "");

        } else {

            // Calculate width percentage
            var blockContent = blocks.getBlockContent(block);
            var width = ((width / blockContent.width()) * 100) + "%";

            // Assign width percentage
            videoContainer.css("width", width);
        }
    },

    setVideoSize: function(block, prop, val) {

        if (prop=="width") {
            self.setVideoWidth(block, val);
        }

        if (prop=="height") {
            self.setVideoHeight(block, val);
        }
    },

    updateVideoSize: function(block, prop, val) {

        self.setVideoSize(block, prop, val);

        self.populateVideoSize(block);
    },

    setVideoHeight: function(block, height) {

        var videoContainer = getVideoContainer(block);
        var videoViewport = getVideoViewport(block);
        var ratio = self.getVideoRatio(block);

        // Fluid video
        if (self.isFluidVideo(block)) {

            // If ratio is unlocked, adjust padding ratio.
            if (ratio==0) {

                var width = self.getVideoComputedWidth(block);
                var height = parseFloat(height);
                ratio = width / height;

            // If ratio is locked, adjust width.
            } else {

                // Calculate video width
                var width = parseFloat(height) * ratio;
                self.setVideoFluidWidth(block, width);
            }

            videoContainer.css("height", "");
            videoViewport.css("padding-top", ratioPadding(ratio));

        // Fixed height
        } else {

            // Adjust height
            videoContainer.css("height", height);

            // If ratio is locked, adjust width
            if (ratio!==0) {

                var width = parseFloat(height) * ratioDecimal(ratio);

                videoContainer.css("width", width);
                videoViewport.css("padding-top", "");
            }
        }
    },

    setVideoUnit: function(block, prop, unit) {

        // Only applies to width
        if (prop!=="width") return;

        var videoContainer = getVideoContainer(block);
        var width = self.getVideoWidth(block);
        var computedWidth = self.getVideoComputedWidth(block);
        var computedHeight = self.getVideoComputedHeight(block);

        // % to px
        if (unit=="px" && /%/.test(width)) {
            self.setVideoWidth(block, computedWidth);
        }

        // px to %
        if (unit=="%" && /px/.test(width)) {
            self.setVideoFluidWidth(block, computedWidth);
        }
    },

    getVideoUnit: function(block, prop) {

        var val = self.getVideoSize(block, prop);

        return parseUnit(val)
    },

    updateVideoUnit: function(block, prop, unit) {

        self.setVideoUnit(block, prop, unit);

        self.populateVideoSize(block);
    },

    handleNumsliderWidget: function(numsliderWidget, val) {

        // Get prop & val to update
        var prop = self.getVideoSizeProp(numsliderWidget);
        var unit = self.getVideoSizeUnit(prop);
        var val  = Math.round(val) + unit;

        // Declare that we are resizing the slider of this property
        self.resizingFromSlider = prop;

        self.updateVideoSize(currentBlock, prop, val);

        self.resizingFromSlider = null;
    },

    "{numsliderWidget} nouislide": function(numsliderWidget, event, val) {

        self.handleNumsliderWidget(numsliderWidget, val);
    },

    "{numsliderWidget} set": function(numsliderWidget, event, val) {

        self.handleNumsliderWidget(numsliderWidget, val);
    },

    "{numsliderInput} input": function(numsliderInput) {

        // Destroy any blur event handler
        numsliderInput.off("blur.numslider");

        function revertOnBlur(originalValue) {
            numsliderInput
                .on("blur.numslider", function(){
                    numsliderInput.val(originalValue);
                });
        }

        var prop = self.getVideoSizeProp(numsliderInput);

        var oldVal = self.getVideoSize(currentBlock, prop);
        var oldNum = parseFloat(oldVal);

        var num  = numsliderInput.val();
        var unit = parseUnit(oldVal);
        var val  = num + unit;

        // If value is invalid, don't do anything.
        if (!$.isNumeric(num)) {
            // Revert to original value when input is blurred.
            return revertOnBlur(oldNum);
        }

        // Update video size
        self.updateVideoSize(currentBlock, prop, val);
    },

    "{numsliderUnit} click": function(numsliderUnit) {

        var prop = self.getVideoSizeProp(numsliderUnit);
        var unit = numsliderUnit.data("unit");

        self.updateVideoUnit(currentBlock, prop, unit);
    },


    "{self} composerBlockResizeStart": function(base, event, block, ui) {

        // Only handle video block
        if (blocks.getBlockType(block)!=="video") return;

        // Remember initial width & height
        var initialWidth  = self.getVideoComputedWidth(block);
        var initialHeight = self.getVideoComputedHeight(block);
        block.data("initialWidth" , initialWidth);
        block.data("initialHeight", initialHeight);
    },

    "{self} composerBlockBeforeResize": function(base, event, block, ui) {

        // Only handle video block
        if (blocks.getBlockType(block)!=="video" || !self.hasVideoContainer(block)) return;

        // Stop resizable from resizing block because
        // we want to resize the block ourselves.
        event.preventDefault();

        // Get image size, original block size and current block size.
        var imageSize = block.data("initialImageSize");
        var originalSize = ui.originalSize;
        var currentSize = ui.size;

        // Calculate width/height difference
        var dx = currentSize.width  - originalSize.width;
        var dy = currentSize.height - originalSize.height;
        var initialWidth  = block.data("initialWidth");
        var initialHeight = block.data("initialHeight");
        var newWidth  = initialWidth  + dx;
        var newHeight = initialHeight + dy;
        var ratio = self.getVideoRatio(block);

        function updateWidth() {
            self.isFluidVideo(block) ?
                self.setVideoFluidWidth(block, newWidth) :
                self.setVideoWidth(block, newWidth);
        };

        function updateHeight() {
            self.setVideoHeight(block, newHeight);
        };

        // If ratio is unlocked, update both width & height.
        if (ratio==0) {
            dx!==0 && updateWidth();
            dy!==0 && updateHeight();

        // If ratio is locked,
        // update width if there's change in width,
        // update height if there's a change in height.
        } else {
            dx==0 ? (dy!==0 && updateHeight()) : updateWidth();
        }

        // Populdate video size
        self.populateVideoSize(block);
    },

    //
    // Video Ratio
    //
    setVideoRatio: function(block, ratio) {

        // Set new ratio onto block data
        var data = blocks.data(block);
        data.ratio = ratio;

        // Sync video ratio
        self.adjustVideoRatio(block, ratio);
    },

    adjustVideoRatio: function(block, ratio) {

        var videoContainer = getVideoContainer(block);
        var videoViewport = getVideoViewport(block);
        var computedWidth = self.getVideoComputedWidth(block);
        var computedHeight = self.getVideoComputedHeight(block);
        var height = "";
        var paddingTop = "";

        // If no ratio given, get ratio from current computed width & height.
        if (ratio==null) {
            ratio = computedWidth / computedHeight;
        }

        if (self.isFluidVideo(block)) {
            // Note: Fluid video will need a ratio even if its unlocked
            paddingTop = ratioPadding(ratio);
        } else {
            // If ratio is unlocked, use computed height.
            // If ratio is locked, calculate new height.
            height = ratio==0 ? computedHeight : computedWidth / ratioDecimal(ratio);
        }

        videoContainer.css("height", height);
        videoViewport.css("padding-top", paddingTop);
    },

    populateVideoRatio: function(block) {

        // Get ratio from data
        var data = blocks.data(block);
        var ratio = data.ratio;

        // Toggle ratio-unlocked class
        self.ratioButton()
            .toggleClass("ratio-unlocked", ratio==0);

        // Update ratio label
        self.ratioLabel()
            .html(ratio);
    },

    updateVideoRatio: function(block, ratio) {

        self.setVideoRatio(block, ratio);

        self.populateVideoRatio(block);
    },

    getVideoRatio: function(block) {

        var data = blocks.data(block);
        var videoContainer = getVideoContainer(block);

        if (data.ratio===undefined) {
            var width  = self.getVideoComputedWidth(block);
            var height = self.getVideoComputedHeight(block);
            data.ratio = width / height;
        }

        return getRatioInDecimal(data.ratio);
    },

    "{browseButton} mediaSelect": function(browseButton, event, media) {

        var block = blocks.block.of(browseButton);

        if (media.meta.type != "video") {
            return;
        }

        var mediaMeta = media.meta;
        var composerDocument = composer.document;
        var isLegacy = composerDocument.isLegacy();

        // Legacy
        if (isLegacy) {
            content = self.toLegacyHTML(block);
            composerDocument.insertContent(content);

        // EBD
        } else {
            self.createPlayer(block, mediaMeta.url);
        }
    },

    // Triggers when the insert button of mediamanager info is clicked
    "{self} mediaInsert": function(el, event, media, block) {

        // Make sure we only process video blocks
        if (media.meta.type!="video") {
            return;
        }

        var composerDocument = composer.document;
        var isLegacy = composerDocument.isLegacy();

        if (isLegacy) {
            content = self.toLegacyShortcode(media.meta, block);
            composerDocument.insertContent(content);
        } else {

            var data = blocks.data(block);
            var block = blocks.constructBlock("video", {
                "url": media.meta.url
            });

            blocks.addBlock(block);
            blocks.activateBlock(block);

            // // Construct a new post block and insert into the document
            // var block = blocks.constructBlock('video', {
            //     "url": file.url
            // });

            // blocks.addBlock(block);
        }
    },

    "{self} mediaInfoDestroy": function(el, event, info, media) {

        if (media && media.meta.type!="video") {
            return;
        }

        // Get video player
        var videoPlayer = self.videoPlayer.inside(info);
        var videoId = videoPlayer.attr("id");

        // Destroy video player
        videojs(videoId).dispose();
    },

    "{ratioButton} click": function(ratioButton) {

        // Show ratio selection field
        self.sizeField()
            .switchClass(selectRatioView);
    },

    "{ratioCustomizeButton} click": function(ratioCustomizeButton) {

        // Show custom ratio field
        self.sizeField()
            .switchClass(customRatioView);
    },

    "{ratioCancelButton} click": function(ratioCancelButton) {

        // Hide ratio selection field
        self.sizeField()
            .removeClass(selectRatioView);
    },

    "{ratioCancelCustomButton} click": function(ratioCancelCustomButton) {

        // Show ratio selection field
        self.sizeField()
            .switchClass(selectRatioView);
    },

    "{ratioOkCustomButton} click": function(ratioOkCustomButton) {

        // Hide custom ratio field
        self.sizeField()
            .removeClass(customRatioView);
    },

    "{ratioUseCustomButton} click": function(ratioUseCustomButton) {

        var ratioInput = self.ratioInput();
        var ratio = sanitizeRatio(ratioInput.val());

        // If ratio is invalid, do nothing.
        if (ratio==0) return;

        // Update video ratio
        self.updateVideoRatio(currentBlock, ratio);

        // Deactivate all ratio selection
        self.ratioSelection()
            .removeClass("active");

        // Hide custom ratio field
        self.sizeField()
            .removeClass(customRatioView);
    },

    "{ratioSelection} click": function(ratioSelection) {

        self.ratioSelection()
            .removeClass("active");

        ratioSelection.addClass("active");

        self.sizeField()
            .removeClass(selectRatioView);

        var ratio = ratioSelection.data("value");

        self.updateVideoRatio(currentBlock, ratio);
    },

    //
    // Video Alignment
    //
    "{alignmentSelection} change": function(alignmentSelection) {

        var alignment = alignmentSelection.val();
        blocks.font.setFontFormatting(currentBlock, "align" + alignment);
    },

    populateVideoAlignment: function(block) {

        if (blocks.isNestedBlock(block)) {
            self.sizeField()
                .addClass("no-alignment");
            return;
        }

        var blockContent = blocks.getBlockContent(block);
        var width = self.getVideoComputedWidth(block);
        var hasAlignment = width < blockContent.width();

        // Toggle alignment field
        self.sizeField()
            .toggleClass("no-alignment", !hasAlignment);

        // Set alignment
        var alignment = block.css("text-align");
        self.alignmentSelection()
            .val(alignment);
    },

    //
    // Video Helpers
    //
    getVideoSizeField: function(prop) {

        var field = self["videoSizeFieldVideo" + $.capitalize(prop)]();
        return field;
    },

    getVideoSizeProp: function(elem) {

        var numslider = elem.closest(self.numslider.selector);
        var prop = getCssProp(numslider.data("name"));

        return prop;
    },

    getVideoSizeUnit: function(prop) {

        // Get field of this prop
        var field = self.getVideoSizeField(prop);
        return $.trim(self.numsliderCurrentUnit.under(field).text());
    },

    isFluidVideo: function(block) {
        var videoContainer = getVideoContainer(block);
        return videoContainer.hasClass("is-responsive");
    },

    hasVideo: function(block) {
        var data = blocks.data(block);

        return !!data.url;
    },

    hasVideoContainer: function(block) {
        return self.videoContainer.inside(block).length > 0;
    }

}});

module.resolve();

});

});

EasyBlog.module("composer/blocks/mobile", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Mobile", {
        defaultOptions: $.extend({
            "{blipp}": "[data-eb-blipp]",
            "{viewport}": ".eb-composer-viewport",
            "{actionBar}": "[data-eb-composer-actions]"
        }, EBD.selectors),
    }, function(self, opts, base, composer, blocks, currentBlock) { 

        return {

            init: function() {
                blocks = self.blocks;
                composer = blocks.composer;
            },

            "{blipp} mousedown": function(blipp, event) {

                event.stopPropagation();
                event.preventDefault();

                var action;

                $(document)
                    .off("mousemove.blip mouseup.blip")
                    .on("mousemove.blip", function(event){

                        var position = $.getPointerPosition(event),
                            viewport = self.viewport(),
                            viewportOffset = viewport.offset(),
                            topDropArea = 30,
                            rightDropArea = viewport.width() - 10,
                            bottomDropArea = viewport.height() - 30,
                            leftDropArea = viewportOffset.left + 30;

                        // console.log(self.viewport().offset().left, position);

                        // Position blip
                        var leftForBlip = position.x - viewportOffset.left;
                        var topForBlip = position.y - viewportOffset.top;
                        
                        blipp.css("left", leftForBlip);
                        blipp.css("top", topForBlip);

                        action = null;

                        // If the y axis is in the top region, we want to allow the user to create a block
                        if (position.y < topDropArea) {
                            action = 'addBlock';
                        }

                        // If the y axis is in the bottom region, we want to allow the user to remove a block
                        if (position.y > bottomDropArea) {
                            action = 'removeBlock';
                        }

                        // If the x axis is in the left area region, we want to allow the user to view the document explorer
                        if (position.x < leftDropArea) {
                            action = 'viewBlockTree';
                        }

                        if (position.x > rightDropArea) {
                            action = 'moveBlock';
                        }
                    })
                    .on("mouseup.blip", function(){

                        $(document).off('mousemove.blip');

                        // Reset
                        self.actionBar().show();
                        composer.blocks.droppable.destroyDropzones();
                        composer.sidebar.deactivate('blocks');
                        composer.sidebar.deactivate('explorer');

                        // Perform specific actions when blipp is dropped on the addBlock region
                        if (action == 'addBlock') {
                            composer.sidebar.activate('blocks');
                            composer.blocks.droppable.populateDropzones();

                            self.trigger('ComposerMobileAddBlock');

                            return;
                        }

                        // When moving block, we need to display the dropzones and remove the selected block that the user clicked
                        if (action == 'moveBlock') {

                            // Display the dropzones
                            composer.blocks.droppable.populateDropzones();

                            // Override the behavior when a block is clicked
                            blocks.getAllBlocks().on("click.tapremove", function(event){
                                event.stopPropagation(); 
                                var block = $(this);

                                // Hide the block
                                block.addClass('hide');
                            });

                            return;
                        }


                        // Perform specific actions when blipp is dropped on the removeBlock region
                        if (action == 'removeBlock') {

                            // Hide the action bar
                            self.actionBar().hide();

                            blocks.getAllBlocks().on("click.tapremove", function(event){
                                event.stopPropagation(); 
                                var block = $(this);
                                blocks.removeBlock(block);
                            });

                            self.trigger('ComposerMobileRemoveBlock');

                            return;
                        }

                        // Renders the document explorer
                        if (action == 'viewBlockTree') {
                            EasyBlog.Composer.sidebar.activate("explorer");

                            self.trigger('ComposerMobileViewTree');

                            return;
                        }
                    });
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/blocks/nestable", function($){

var module = this,
    isNested = "is-nested",
    isSortingNest = "is-sorting-nest";

EasyBlog.require()
    .library(
        "ui/sortable"
    )
    .done(function(){

EasyBlog.Controller("Composer.Blocks.Nestable",
{
    defaultOptions: $.extend({}, EBD.selectors)
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = self.blocks.composer;
        currentNest = $();
    },

    enable: function() {

        self.nest()
            .each(function(){
                var nest = $(this);
                self.initNestable(nest);
            });
    },

    disable: function() {

        self.nest()
            .each(function(){
                var nest = $(this);
                self.destroyNestable(nest);
            });
    },

    initNestable: function(nest) {

        // If this nest has no sortable implemented yet.
        if (!nest.hasClass("ui-sortable")) {

            // Initialize sortable on nest
            nest.sortable({

                    // Items
                    items: EBD.childBlock,
                    connectWith: EBD.root + ", " + EBD.nest,

                    // Helper
                    helper: "clone",
                    appendTo: composer.document.ghosts(),

                    // Placeholder
                    placeholder: "ebd-block is-placeholder is-nested",

                    // Behaviour
                    tolerance: "pointer",
                    refreshPositions: true,

                    // Handler
                    handle: EBD.immediateBlockSortHandle
                });
        }
    },

    destroyNestable: function(nest) {

        if (!nest.hasClass("ui-sortable")) return;

        nest.sortable("destroy");
    },

    "{self} composerBlockMechanicsChange": function(base, event, mechanics) {

        mechanics=="sortable" ? self.enable() : self.disable();
    },

    isBlockNest: function(nest) {
        return nest.data("type")=="block";
    },

    isContentNest: function(nest) {
        return nest.data("type")=="content";
    },

    "{self} composerBlockCreate": function(el, event, block, meta) {
    },

    "{self} composerBlockInit": function(el, event, block, handler) {

        // Skip if this is not a nestable block,
        // or current block mechanics isn't set to sortable.
        if (!block.is(EBD.nestableBlock) || !blocks.getMechanics()=="sortable") return;

        // Find nests within block
        block.find(EBD.nest)
            .each(function(){

                var nest = $(this);

                // Initialize nest
                self.initNestable(nest);
            });
    },

    disableEditableNest: function(nest) {

        // If nest is editable,
        if (nest.editable()) {

            // Disable editable functionality
            nest.editable(false);

            // Remember that it is edtiable
            nest.data("editable-nest", true);
        }
    },

    enableEditableNest: function(nest) {

        // If nest is editable
        if (nest.data("editable-nest")) {

            // Restore editable functionality
            nest.editable(true);

            // Remove editable flag
            nest.removeData("editable-nest");
        }
    },

    // TODO: What's the touch event fallback for this?
    "{block} mouseover": function(block, event) {

        if (event.nestHandled) return;

        // If this is a nested block
        if (block.is(EBD.nestedBlock)) {

            // Get parent nest
            var nest = self.nest.of(EBD.nestedBlock);

            // Disable editability on nest
            self.disableEditableNest(nest);

        // If this is a block
        } else {

            // Get child nest if any
            var nest = self.nest.inside(block);

            // Stop if there are no nests.
            if (nest.length < 1) return;

            // Restore editability on nest
            self.enableEditableNest(nest);
        }

        // Flag this because we want the event to propagate
        // but we don't want to process this event on the parent block.
        event.nestHandled = true;
    },

    // TODO: What's the touch event fallback for this?
    "{block} mouseout": function(block, event) {

        // If this is a nested block
        if (block.is(EBD.nestedBlock)) {

            // Get parent nest
            var nest = self.nest.of(EBD.nestedBlock);

            // Restore editability on parent nest
            self.enableEditableNest(nest);
        }
    },

    // This methods decide whether a placeholder should
    // be snapped to the left or right of the nest.
    snap: function(x, y, nest, placeholder) {

        // If this is a block nest, just add is-nested class on placeholder.
        if (self.isBlockNest(nest)) {
            placeholder.addClass(isNested);
            return;
        }

        // Determine placeholder position
        var offset   = nest.offset(),
            width    = nest.width(),
            center   = offset.left + (width / 2),
            position = self.position(nest, x < center ? "left" : "right");

        placeholder
            // Set placeholder as nested
            .addClass(isNested)
            // Set nest placement
            .switchClass("nest-" + position);

        // Remember the last snapped position
        nest.data("snappedPosition", position);
    },

    unsnap: function(placeholder) {

        // If placeholder no longer nested,
        // remove nested properties from placeholder.
        placeholder
            .removeClass(isNested)
            .removeClass(function(index, css) {
                return (css.match(/(^|\s)nest-\S+/g) || []).join(' ');
            });
    },

    // Return supported positions
    positions: function(nest) {

        // If no positions provided, default to left & right.
        return (nest.data("positions") || "left,right").split(",");
    },

    availablePositions: function(nest) {

        var positions = self.positions(nest);

        nest.find(EBD.childBlock + ":not(.is-sort-item)")
            .each(function(){

                var position = self.extractPosition($(this).attr("class"));

                // Remove this position from available positions
                position && $.pull(positions, position);
            });

        return positions;
    },

    extractPosition: function(str) {

        // Also accept block as parameter
        if (str instanceof $) {
            str = str.attr("class");
        }

        // Note: This regex will mismatch classnames that has "nest-" in it like "birdnest-1".
        return ((str.match(/nest-\w+/g) || [])[0] || "").split("nest-")[1];
    },

    // If no position is given, return the most preferred available position.
    // If a position is given, determine if the position is available and return it.
    // If the position is unavailable, return the next preferred position in line.
    position: function(nest, position) {

        var positions = self.availablePositions(nest);

        return $.indexOf(positions, position) > -1 ? position : positions[0];
    },

    setTargetNest: function(nest) {

        self.clearTargetNest();
        blocks.over(nest);
    },

    unsetTargetNest: function(nest) {

        blocks.out(nest);
    },

    clearTargetNest: function() {

        // Get all nest and remove is-receiving flag
        self.nest()
            .each(function(){
                var nest = $(this);
                blocks.out(nest);
            });
    },

    trackNest: function(nest, placeholder) {

        // Bind to the mousemove event
        $(document)
            .off("mousemove.nestable")
            .on("mousemove.nestable", function(event) {

                // And when user glides along, decide the placement
                // of the placeholder based on the cursor position.
                self.snap(event.pageX, event.pageY, nest, placeholder);
            });
    },

    untrackNest: function() {

        $(document)
            .off("mousemove.nestable");
    },

    "{self} composerBlockBeforeDrop": function(base, event, block) {

        // Get nest
        var nest = block.closest(EBD.nest);

        // Get blocks' nest position
        var currentPosition = self.extractPosition(block);

        // If this block is inside a nest, add is-nested class.
        block.toggleClass(isNested, nest.length > 0);

        // If this is a content nest, determine position
        // and add nest position class.
        if (self.isContentNest(nest)) {

            // This will get the position intended by the user and
            // then pass it over to self.position to return the final
            // location after determining its availability.
            var position = self.position(nest, nest.data("snappedPosition"));

            // Add nest position class.
            block.switchClass("nest-" + position);

            // If this is a new block
            if (block.hasClass("is-new")) {

                // Set initial fluid width
                blocks.dimensions.toFluidWidth(block);
            }

            // If we're switching position, trigger composerBlockNestChange
            if (currentPosition && position!==currentPosition) {
                self.trigger("composerBlockNestChange", [block, position, currentPosition]);

            // If we're nesting this block, trigger composerBlockNestIn
            } else {
                self.trigger("composerBlockNestIn", [block, position]);
            }

        // If this block no longer belong in a content nest
        } else if (currentPosition) {

            self.unsnap(block);
            self.trigger("composerBlockNestOut", [block]);
        }
    },

    replaceWithCommentPlaceholder: function(blockManifest, property) {

        // Create a block fragment from the block's editable html code
        var blockFragment = $('<div>').html(blockManifest[property]);
        var blockList = blockFragment.find(EBD.immediateNestedBlock);

        // If there are nested blocks
        if (blockList.length <= 0) {
            return;
        }

        blockList.each(function() {
            var nestedBlockElement = $(this);
            var nestedBlockUid = blocks.getBlockUid(nestedBlockElement);

            // Create a placeholder
            var placeholder = document.createComment('block' + nestedBlockUid);

            // Replace the nested block with block placeholder within the block fragment
            nestedBlockElement.replaceWith(placeholder);

            var nestedBlock = blocks.getBlock(nestedBlockUid);
            var nestedBlockManifest = blocks.exportBlock(nestedBlock);
            var position = self.extractPosition(nestedBlock.attr('class'));

            if (position) {
                nestedBlockManifest.position = position;
            }

            blockManifest.blocks.push(nestedBlockManifest);
        });

        // Convert the block fragment into html after replacing nested blocks with placeholders
        var html = blockFragment.html();

        // Update parent block html to contain html with converted block placeholders
        blockManifest[property] = html;
    },

    "{self} composerBlockExport": function(base, event, block, blockManifest) {

        // Add blocks property to the block manifest which holds
        // an array of nested block manifests.
        blockManifest.blocks = [];

        // Replace block's editable html codes
        self.replaceWithCommentPlaceholder(blockManifest, 'editableHtml');

        // Replace html codes
        self.replaceWithCommentPlaceholder(blockManifest, 'html');
    }

}});

module.resolve();

});

});

EasyBlog.module("composer/blocks/panel", function($){

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Panel", {
        elements: [
            // Global blocks
            "[data-eb-composer-blocks-{props}]",
            "[data-eb-composer-blocks-{prop-group|prop-action}]",
            "[data-eb-composer-{panel}][data-id=blocks]",

            // Subpanels,
            "[data-eb-composer-blocks-{block-subpanel|removal-subpanel}]",

            // Individual blocks
            "[data-eb-composer-block-{fieldgroup}]",

            // Menu
            "[data-eb-composer-blocks-props-{block-menu|text-menu|block-content|text-content}]",
            "[data-eb-composer-blocks-{subpanel|subpanel-button}]"
        ],

        defaultOptions: {
            "{blockTitle}": "[data-block-property-title]",
            "{blockIcon}": "[data-block-property-icon]"
        }
    }, function(self, opts, base, composer, blocks, currentBlock) {

        return {

            init: function() {

                blocks = self.blocks;
                composer = blocks.composer;
                currentBlock = $();
            },

            "{self} composerBlockBeforeActivate": function(base, event, block) {

                // Activate blocks panel
                composer.panels.activate("blocks");

                // Set as current block
                currentBlock = block;

                // Show block props
                self.panel().switchClass("show-block-subpanel");

                // Activate fieldgroup on blocks panel
                self.fieldgroup.display(block);
            },

            fieldgroup: {

                get: $.memoize(function(blockType) {

                    var fieldgroup = composer.find(self.fieldgroup).where("type", blockType);

                    // If this fieldgroup hasn't been created, create it
                    if (fieldgroup.length < 1) {

                        // Get meta
                        var meta = blocks.getBlockMeta(blockType),

                            // Get fieldgroup html from meta
                            // and append it to blocks panel
                            fieldgroup = $(meta.fieldgroup);
                    }

                    return fieldgroup;

                }),

                hide: function() {

                    // Hides all fieldset, shows empty hint
                    self.panel()
                        .addClass("is-empty")
                        .find(self.fieldgroup)
                        .detach();
                },

                display: function(block) {

                    // Get type, panel
                    var type = block.attr("data-type");
                    var panel = self.panel();

                    // Detach existing fieldgroups
                    panel
                        .removeClass("is-empty")
                        .find(self.fieldgroup)
                        .detach();

                    // Get prop group
                    var propGroup =
                        panel
                            .find(self.propGroup)
                            .where("type", "specific");

                    // Append fieldgroup to tab content
                    self.fieldgroup
                        .get(type)
                        .toggleClass("is-standalone", blocks.isStandaloneBlock(block))
                        .appendTo(propGroup);
                }
            },

            openPanel: function(name) {

                if (name=="block" || name=="text") {

                    self.panel().switchClass("show-" + name + "-subpanel");

                    self.subpanelButton()
                        .where("name", name)
                        .activateClass("active");
                }

                if (name=="removal") {
                    self.panel().switchClass("show-removal");
                }
            },

            activatePanel: function(block) {

                clearTimeout(self.revertToPostPanel);

                var meta = blocks.getBlockMeta(block);
                var parents = blocks.getAllParentBlocks(block);
                var children = blocks.getChildBlocks(block);
                var items = [];

                // Always display the subpanel button
                self.subpanelButton().show();

                // Get the block's meta and see if we should display the text panel
                if (!meta.properties.textpanel) {
                    self.subpanelButton().hide();
                }

                // Update the block panel property
                self.blockIcon().attr('class', meta.icon);
                self.blockTitle().html(meta.title);
            },

            deactivatePanel: function() {

                self.revertToPostPanel = setTimeout(function(){

                    if (!composer.document.workarea().hasClass("is-sorting")) {
                        // Activate post options panel
                        composer.panels.activate("post-options");
                    }
                }, 1);

                // Show empty hint
                self.fieldgroup.hide();
            },

            "{self} composerBlockActivate": function(el, event, block) {

                self.activatePanel(block);
            },

            "{self} composerBlockDeactivate": function(el, event, block) {

                self.deactivatePanel(block);
            },

            "{self} composerBlockRemove": function(el, event, block) {

                // Show empty hint
                self.fieldgroup.hide(block);
            },

            "{subpanelButton} click": function(subpanelButton) {

                var name = subpanelButton.data("name");
                self.openPanel(name);
            }
        }
    });

    module.resolve();

});
;
EasyBlog.module("composer/blocks/removal", function($){

var module = this;
var isRemoving = "is-removing";

EasyBlog.require()
.library(
    "ui/droppable"
)
.done(function(){

EasyBlog.Controller("Composer.Blocks.Removal",
{
    defaultOptions: {
        "{workarea}": EBD.workarea,
        "{dropzone}": "[data-eb-composer-blocks-removal-subpanel]"
    }
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = blocks.composer;
    },

    "{self} composerBlockDrag": function(base, event, block) {

        // Set as current block
        currentBlock = block;

        // Activate blocks panel
        composer.panels.activate("blocks");

        // Open removal subpanel
        blocks.panel.openPanel("removal");

        // Get dropzone
        var dropzone = self.dropzone();

        // If dropzone has never been initialized before,
        // initalized it now.
        // TODO: Find alternative way to determine this
        if (!dropzone.data("inited")) {
            dropzone.droppable({
                accept: EBD.block,
                tolerance: "pointer"
            });
            dropzone.data("inited", true);
        }
    },

    "{self} composerBlockDrop": function(base, event, block) {

        if (self.workarea().hasClass(isRemoving)) return;

        // Open block subpanel
        blocks.panel.openPanel("block");
    },

    "{dropzone} drop": function(dropzone, event, ui) {

        // Get panel
        var block = ui.draggable;
        var workarea = self.workarea();

        // Add is-removing class to workarea
        workarea.addClass(isRemoving);

        // Start block removal transition
        block.addClass(isRemoving);
        dropzone.addClass(isRemoving);

        // Remove block after a slight deley
        setTimeout(function(){

            blocks.removeBlock(block);

            // After animation is done
            setTimeout(function(){

                // Open block panel
                blocks.panel.openPanel("block");

                workarea.removeClass("is-removing");

                setTimeout(function(){
                    // Remove block transition
                    dropzone.removeClass("is-removing active");
                }, 250);

            }, 250);


        }, 100);
    },

    "{dropzone} dropout": function(dropzone, event, ui) {

        dropzone.removeClass("active");
    },

    "{dropzone} dropover": function(dropzone, event, ui) {

        dropzone.addClass("active");
    }

}});

module.resolve();

});

});

EasyBlog.module("composer/blocks/resizable", function($){

var module = this;

EasyBlog.require()
    .library(
        "ui/resizable"
    )
    .done(function(){

EasyBlog.Controller("Composer.Blocks.Resizable",
{
    defaultOptions: $.extend({
        "{resizeHandle}": ".ui-resizable-handle"
    }, EBD.selectors)
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = self.blocks.composer;
    },

    initResizable: function(block) {

        // Skip if resizable has been initialized
        if (self.hasResizable(block)) return;

        // Determine resize handles
        var handles = self.determineResizeHandles(block);

        // If there are no resize handles, stop.
        if (!handles) return;

        // Create resize handle elements and selectors
        var resizeHandleElements = self.createResizeHandleElements(handles),
            resizeHandleSelectors = self.createResizeHandleSelectors(handles);

        // Add resize handles to viewport
        var viewport =
            blocks.getBlockViewport(block)
                .append(resizeHandleElements)
                // Implement resizable
                .resizable({
                    handles: resizeHandleSelectors
                });

        // They are hidden by default until block is activated
        $(resizeHandleElements).hide();

        // Add is-resizable class on blocz
        block.addClass("is-resizable");
    },

    destroyResizable: function(block) {

        // Destroy resizable
        blocks.getBlockViewport(block)
            .resizable("destroy");

        // Remove is-resizable class
        block.removeClass("is-resizable");
    },

    hasResizable: function(block) {

        return blocks.getBlockViewport(block).hasClass("ui-resizable");
    },

    handles: {

        right: {
            w: "dimension",
            sw: "dimension",
            s: "dimension"
        },

        left: {
            e: "dimension",
            se: "dimension",
            s: "dimension"
        }
    },

    determineResizeHandles: function(block) {

        var nestPosition = blocks.nestable.extractPosition(block);

        return self.handles[nestPosition];
    },

    createResizeHandleElements: function(handles) {

        var elements = [];

        $.each(handles, function(direction, role){

            var element =
                $('<div class="ui-resizable-handle"><div></div></div>')
                    .addClass("ui-resizable-" + direction)
                    .attr({
                        "data-direction": direction,
                        "data-role": role
                    });

            elements.push(element[0]);
        });

        return elements;
    },

    createResizeHandleSelectors: function(handles) {

        var selectors = [];

        $.each(handles, function(direction, role){
            selectors[direction] = "> .ui-resizable-" + direction;
        });

        return selectors;
    },

    getResizeHandles: function(block) {

        return blocks.getBlockViewport(block)
            .children(self.resizeHandle.selector);
    },

    "{self} composerBlockActivate": function(base, event, block) {

        // Initialize resizable
        self.initResizable(block);

        // Show resize handles
        self.getResizeHandles(block).show();
    },

    "{self} composerBlockDeactivate": function(base, event, block) {

        // Hide resize handles
        self.getResizeHandles(block).hide();
    },

    "{self} composerBlockNestIn": function(base, event, block) {

        self.initResizable(block);
    },

    "{self} composerBlockNestOut": function(base, event, block) {

        self.destroyResizable(block);
    },

    "{self} composerBlockNestChange": function(base, event, block) {

        self.destroyResizable(block);

        self.initResizable(block);
    },

    "{blocks.viewport} resizestart": function(viewport, event, ui) {

        // Add is-sizing class to workarea.
        // This will disable block animation.
        self.workarea()
            .addClass("is-resizing");

        // Get block
        var block = self.block.of(viewport);
            parentBlocks = blocks.getAllParentBlocks(block);

        // Add has-resizing-child class to parent block.
        // This will disable block guide from showing on parent block.
        parentBlocks.addClass("has-resizing-child");

        self.trigger("composerBlockResizeStart", [block, ui]);
    },

    "{blocks.viewport} resize": function(viewport, event, ui) {

        // This prevents resizable from resizing the block
        viewport.css({top: "", left: "", width: "", height: ""});
        event.stopPropagation();

        // Get block
        var block = self.block.of(viewport);

        // Currently only for nested block of content nest
        if (!blocks.isNestedBlock(block) || blocks.getBlockNestType(block)!=="content") return;

        var beforeResizeEvent = self.trigger("composerBlockBeforeResize", [block, ui]);

        // If resize event is not prevented, continue with default resizing strategy.
        if (!beforeResizeEvent.isDefaultPrevented()) {

            // Get nest, original size and current size
            var originalSize = ui.originalSize,
                size = ui.size;

            // If width has changed
            if (originalSize.width !== size.width) {

                // Get nest
                var nest = blocks.getBlockNest(block);

                // Get width
                var width = size.width / nest.width();

                // Cap to 0 to 1 (0% to 100%)
                if (width < 0) width = 0; if (width > 1) width = 1;

                // Convert width to percentage
                width = Math.floor(width * 100) + "%";

                // Set width
                block.css("width", width);
            }

            // If height has changed
            if (originalSize.height !== size.height) {

                // Get natural height
                block.css("height", "");
                var naturalHeight = block.height(),

                    // Get new height
                    height = size.height;

                // If new height is shorter than natural height,
                // remove height override.
                if (height < naturalHeight) height = "";

                // Set height
                block.css("height", height);
            }
        }

        // Trigger composerBlockResize
        self.trigger("composerBlockResize", [block]);
    },

    "{blocks.viewport} resizestop": function(viewport, event, ui) {

        // Remove is-resizing class from workarea.
        // This will enable block animation.
        self.workarea()
            .removeClassAfter("is-resizing");

        // Get block
        var block = self.block.of(viewport);
            parentBlocks = blocks.getAllParentBlocks(block);

        // Add has-resizing-child class to parent block.
        // This will disable block guide from showing on parent block.
        parentBlocks.removeClass("has-resizing-child");

        self.trigger("composerBlockResizeStop", [block, ui]);
    }

}});

    });

module.resolve();

});

EasyBlog.module("composer/blocks/scrollable", function($) {

var module = this;

EasyBlog.Controller("Composer.Blocks.Scrollable", {
    defaultOptions: $.extend({
    }, EBD.selectors),
}, function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {
        blocks = self.blocks;
        composer = blocks.composer;
    },

    "{block} dragstart": function() {

        self.scrollstart();
    },

    "{blocks.menu} dragstart": function() {

        self.scrollstart();
    },

    "{block} dragstop": function() {

        self.scrollstop();
    },

    "{blocks.menu} dragstop": function() {

        self.scrollstop();
    },

    "{self} composerBlockDrop": function() {

        self.scrollstop();
    },

    stop: false,

    scrollTimer: null,

    scrollstart: function() {

        var viewport = composer.viewport(),
            viewportContent = composer.document.viewportContent()[0],
            viewportHeight = viewport.height(),
            topToleranceArea = 50,
            bottomToleranceArea = viewportHeight - 50,
            position,

            autoScroll = function() {

                // This would allow the next scroll event to happens
                self.stop = false;

                // Determines if the scroll event is hovering within the tolerance area
                if (position.y > bottomToleranceArea || position.y < topToleranceArea) {

                    // Prevents the next event from being executed
                    self.stop = true;

                    if (position.y > bottomToleranceArea) {
                        viewportContent.scrollTop += (viewportHeight / 2);
                    }

                    if (position.y < topToleranceArea) {
                        viewportContent.scrollTop -= (viewportHeight / 2);
                    }

                    clearTimeout(self.scrollTimer);

                    self.scrollTimer = setTimeout(autoScroll, 1000);

                    self.trigger("composerDocumentScroll");
                }
            };

        $(document).on($.ns("mousemove touchmove", ".scrollable"), function(event){

            position = $.getPointerPosition(event);

            // Reset the top
            if (self.stop && position.y > topToleranceArea && position.y < bottomToleranceArea) {
                self.stop = false;
            }

            // Determines if we should trigger this
            if (self.stop) {
                return;
            }

            // If user tries to place the block 50 pixels from the bottom, we want it to scroll automatically.
            autoScroll();
        });
    },

    scrollstop: function() {

        clearTimeout(self.scrollTimer);

        $(document).off($.ns("mousemove touchmove", ".scrollable"));
    }

}});

module.resolve();

});

EasyBlog.module("composer/blocks/search", function($){

var module = this;

var isSearching = "is-searching";
var isEmpty = "is-empty";

EasyBlog.Controller("Composer.Blocks.Search",
{
    elements: [
        "[data-eb-blocks-{search-input|search-toggle-button|search-panel}]"
    ],

    defaultOptions: {
    }
},
function(self, opts, base, composer, blocks) { return {

    init: function() {

        blocks = self.blocks;
        composer = blocks.composer;
    },

    toggleSearch: function() {

        blocks.blocks().hasClass(isSearching) ?
            self.deactivateSearch() :
            self.activateSearch();
    },

    activateSearch: function() {

        blocks.view()
            .addClass(isSearching)

        self.searchInput()
            .val("")
            .focus();
    },

    deactivateSearch: function() {

        blocks.view()
            .removeClass(isSearching);

        self.resetSearch();
    },

    resetSearch: function() {

        blocks.view()
            .removeClass(isEmpty);

        self.searchInput().val("");

        // Show all menu items
        blocks.menu().show();

        // Quick hack to show back all menu group
        blocks.menuGroup()
            .each(function(){
                $(this).parents(".eb-composer-fieldset").show();
            });
    },

    search: function(keyword) {

        var keyword   = $.trim(keyword.toLowerCase()),
            menus     = blocks.menu(),
            results   = menus.filter("[data-keywords*='" + keyword + "']"),
            noKeyword = keyword=="",
            noResults = !noKeyword && results.length < 1;

        // If no keyword given, show all results
        noKeyword ?
            menus.show():
            menus.hide();

        // Show results
        results.show();

        blocks.view()
            .toggleClass(isEmpty, noResults);

        // Quick hack to hide menu group with no results
        blocks.menuGroup()
            .each(function(){

                var menuGroup = $(this),
                    fieldset = menuGroup.parents(".eb-composer-fieldset").show();

                if (menuGroup.height() < 1) {
                    fieldset.hide();
                }
            });
    },

    "{searchToggleButton} click": function(button) {
        self.toggleSearch();
    },

    "{searchInput} keyup": function(searchInput, event) {

        var keyword = searchInput.val();

        // Escape
        if (event.keyCode===27) {

            // When user hits escape for the first time, clear search input.
            // When user hits escape for the second time, hide search bar.
            if (keyword=="") {
                self.deactivateSearch();
            } else {
                self.resetSearch();
            }
        }
    },

    "{searchInput} input": $.debounce(function(searchInput, event) {

        self.search(searchInput.val());

    }, 150)

}});

module.resolve();

});

EasyBlog.module("composer/blocks/text", function($){

var module = this;

EasyBlog.require()
.library(
    "colorpicker",
    "nouislider"
)
.script(
    "layout/elements"
)
.done(function(){

EasyBlog.Controller("Composer.Blocks.Text",
{
    elements: [
        ".eb-composer-fieldset[data-name=text] [data-eb-{font-color-menu|font-family-menu|font-size-menu|font-color-content|font-color-picker|font-family-content|font-size-content|font-color-caption|font-family-caption|font-size-caption|font-family-option|font-format-option}]",
        ".eb-composer-fieldset[data-name=text] [data-eb-{numslider-toggle|numslider-widget|numslider-value|numslider-input|numslider-units|numslider-unit|numslider-current-unit|numslider-unit-toggle}]",
        ".eb-composer-fieldset[data-name=text] [data-eb-{colorpicker|colorpicker-toggle}]",
        "[data-eb-{links|link-item-group|link|link-item|link-preview|link-preview-caption|link-url-field|link-title-field|link-blank-option|link-remove-button}]"
    ],

    defaultOptions: $.extend({

        fontSizeUnits: {

            "px": {
                start: 12,
                step: 2,
                range: {
                    min: 8,
                    max: 72
                },
                pips: {
                    mode: "values",
                    density: 4,
                    values: [8, 12, 18, 24, 48, 72]
                }
            },

            "%": {
                start: 100,
                step: 10,
                range: {
                    min: 0,
                    max: 200
                },
                pips: {
                    mode: "positions",
                    values: [0,50,100],
                    density: 10
                }
            }
        },

        "{textFieldset}": ".eb-composer-fieldset[data-name=text]",
        "{linksFieldset}": ".eb-composer-fieldset[data-name=links]",

        "{fontSizeCheckbox}": "[data-eb-font-size-content] .eb-numslider-toggle input",
        "{fontSizeToggle}": "[data-eb-font-size-content] .eb-numslider-toggle label",

        "{linkBlankOptionField}": ".eb-link-blank-option",

    }, EBD.selectors)
},
function(self, opts, base, composer, blocks, editor, iframe, iframeDocument, iframeWindow, isEditingSelection) { return {

    init: function() {

        blocks = self.blocks;
        composer = blocks.composer;

        self.textFieldset()
            .on("touchstart click mousedown mouseup", function(event){
                // Prevent caret from losing focus
                event.preventDefault();
            });

        self.linksFieldset()
            .on("touchstart click mousedown mouseup", function(event){
                // Prevent caret from losing focus
                event.preventDefault();
            });
    },

    "{self} composerDocumentReady": function() {

        editor = composer.editor;

        self.initFontColor();
        self.initLinks();
        self.initHandlers();
    },

    initHandlers: function() {

        // $.each(self.handlers, function(handlerName, handlerFunc){

        //     var parts = handlerName.match(/^\{(.+)\} (.+)/),
        //         eventTarget = parts[1],
        //         eventName   = parts[2],
        //         selector = self[eventTarget].selector;

        //     // Bind to iframe document
        //     $(iframeDocument)
        //         .on(eventName, selector, function(event){
        //             handlerFunc.apply(self, [$(this)].concat($.makeArray(arguments)));
        //         });
        // });
    },

    "{self} composerBlockActivate": function(base, event, block) {

        var meta = blocks.getBlockMeta(block);

        if (!meta.properties.fonts) {
            self.textFieldset().hide();
            self.linksFieldset().hide();
            return;
        }
    },

    "{self} composerTextSelect": function(base, event, selection, block, editor) {

        // Get the block meta and see if it should display the text panel
        var meta = blocks.getBlockMeta(block);

        if (!meta.properties.fonts) {
            self.textFieldset().hide();
            self.linksFieldset().hide();
            return;
        }

        if (isEditingSelection) return;

        // Show text fieldset
        blocks.panel.blockSubpanel().addClass("has-text-selection");
        self.textFieldset().show();
        self.linksFieldset().show();

        // Toggle list formatting
        self.textFieldset()
            .find(".eb-font-formatting.section-list")
            .toggle(block.data("type")=="text");

        // Populate fieldsets
        self.populateFont();
        self.populateLinks();
    },

    "{self} composerTextDeselect": function(base, event, editor) {

        if (isEditingSelection) return;

        // If user is sorting, don't do anything.
        if (self.workarea().hasClass("is-sorting")) return;

        // Show text fieldset
        blocks.panel.blockSubpanel().removeClass("has-text-selection");
        self.textFieldset().hide();
        self.linksFieldset().hide();

        // Remove text markers
        self.removeTextMarkers();

        // Clear out link items
        self.linkItemGroup().empty();
    },

    populateFont: function() {

        self.populateFontColor();
        self.populateFontFamily();
        self.populateFontSize();
        self.populateFontFormatting();
    },

    //
    // Font Color API
    //

    initFontColor: function() {

        self.fontColorPicker()
            .colorpicker();
    },

    populateFontColor: function() {

        if (self.lastUpdatedViaFontColorUI) {
            self.lastUpdatedViaFontColorUI = false;
            return;
        }

        var parent = editor.selection.getParent(),
            fontColor = $(parent).css("color");

        // Determine if this has color override by determining
        // if the style's color attribute has an actual value.
        self.colorpickerToggle().prop("checked", parent.style.color);

        self.updateFontColorUI(fontColor);
    },

    setFontColor: function(fontColor) {

        // Remove font color
        // self.removeFontColor();

        self.lastUpdatedViaFontColorUI = true;

        // Update text font color
        editor.inline.toggleStyle("color: " + fontColor);

        // Update font color UI
        self.updateFontColorUI(fontColor);

    },

    updateFontColorUI: function(fontColor) {

        self.updatingFontColorUI = true;

        // Fallback to black if no font color given
        !fontColor && (fontColor = "#000");

        // Update color preview
        self.fontColorCaption()
            .css("backgroundColor", fontColor);

        self.fontColorPicker()
            .colorpicker("setColor", fontColor);

        self.updatingFontColorUI = false;
    },

    removeFontColor: function() {

        editor.inline.removeStyleRule("color");
    },

    //
    // Font Family API
    //

    populateFontFamily: function() {

        if (self.lastUpdatedViaFontFamilyUI) {
            self.lastUpdatedViaFontFamilyUI = false;
            return;
        }

        var parent = editor.selection.getParent(),
            fontFamily = parent.style.fontFamily.replace(/\'\"/g, "");

        // Update the font family
        self.updateFontFamilyUI(fontFamily);
    },

    setFontFamily: function(fontFamily) {

        // Set font family on text selection
        self.lastUpdatedViaFontFamilyUI = true;
        editor.inline.format("span", "style", "font-family: " + fontFamily);

        // Update font family UI
        self.updateFontFamilyUI(fontFamily);
    },

    updateFontFamilyUI: function(fontFamily) {

        // If no font family given, use empty string.
        !fontFamily && (fontFamily = "");

        var fontFamilyOption =
            self.fontFamilyOption()
                .removeClass("active")
                .where("value", '"' + fontFamily + '"')
                .addClass("active");

        // Determine font family captiomn
        var fontFamilyCaption =
                fontFamilyOption.length > 0 ?
                    fontFamilyOption.html() :
                    fontFamily.split(",")[0];

        // Set font family caption
        self.fontFamilyCaption()
            .html(fontFamilyCaption);
    },

    removeFontFamily: function() {

        self.lastUpdatedViaFontFamilyUI = true;
        editor.inline.removeStyleRule("font-family");
    },

    //
    // Font Size API
    //

    populateFontSize: function() {

        if (self.lastUpdatedViaFontSizeUI) {
            self.lastUpdatedViaFontSizeUI = false;
            return;
        }

        var parent = editor.selection.getParent(),
            fontSize = $(parent).css("fontSize"),
            hasFontSize = !!parent.style.fontSize;

        // Update the fontsize
        self.updateFontSizeUI(fontSize);

        self.numsliderToggle().prop("checked", !!hasFontSize);
    },

    setFontSize: function(fontSize) {

        // If number is given, add a unit.
        if ($.isNumeric(fontSize)) {
            var unit = self.getFontSizeUnit();
            fontSize = fontSize + unit;
        }

        // Update block font size
        self.lastUpdatedViaFontSizeUI = true;
        editor.inline.format("span", "style", "font-size: " + fontSize);

        // Get fallback fontsize
        var parent = editor.selection.getParent(),
            fallbackFontSize = $(parent).css("fontSize");

        self.updateFontSizeUI(fontSize || fallbackFontSize);

        self.numsliderToggle().prop("checked", !!fontSize);
    },

    updateFontSizeUI: function(fontSize) {

        self.updatingFontSizeUI = true;

        // Get value & unit
        var value = Math.abs(fontSize.replace(/\%|px/gi, ""))
            unit = fontSize.match("%") ? "%" : "px";

        if (self.getFontSizeUnit()!==unit) {
            self.setFontSizeUnit(unit);
        }

        // Set caption
        self.fontSizeCaption()
            .html(fontSize);

        // Set dropdown toggle
        self.numsliderCurrentUnit()
            .html(unit);

        // Set dropdown
        self.numsliderUnit()
            .removeClass("active")
            .where("unit", '"' + unit + '"')
            .addClass("active");

        // Set slider value
        self.numsliderWidget()
            .val(value);

        // Set input value
        self.numsliderInput().val(value);

        self.updatingFontSizeUI = false;
    },

    removeFontSize: function() {

        editor.inline.removeStyleRule("font-size");
    },

    getFontSizeUnit: function() {

        return self.fontSizeContent().data("unit") || "%";
    },

    setFontSizeUnit: function(unit) {

        self.fontSizeContent().data("unit", unit);

        // Use percentage by default
        var unitOptions = opts.fontSizeUnits[unit];

        // Set up slider
        self.numsliderWidget()
            .find(".noUi-pips")
            .remove()
            .end()
            .noUiSlider($.extend({document: iframeDocument}, unitOptions), true)
            .noUiSlider_pips(unitOptions.pips);
    },

    //
    // Font Formatting API
    //

    formattingTags: {
        bold: "strong",
        italic: "em",
        underline: "u",
        strikethrough: "del",
        code: "code",
        superscript: "sup",
        subscript: "sub",
        orderedlist: "ol",
        unorderedlist: "ul"
    },

    populateFontFormatting: function() {

        if (self.lastUpdatedViaFontFormattingUI) {
            self.lastUpdatedViaFontFormattingUI = false;
        } else {
            self.removeTextMarkers();
        }

        var current = editor.selection.getCurrent();
        var list = $(current).parentsUntil(EBD.block).filter("ul, ol").eq(0);

        self.fontFormatOption().each(function(){

            var fontFormatOption = $(this),
                format = fontFormatOption.data("format"),
                formatTag = self.formattingTags[format];

            if (!/unorderedlist|orderedlist/.test(format)) {
                hasFormatting = $(current).closest(formatTag).length !== 0;
            } else {
                hasFormatting = list.is(formatTag);
            }

            fontFormatOption.toggleClass("active", hasFormatting);
        });
    },

    toggleFontFormatting: function(format) {

        switch (format) {

            case "orderedlist":
            case "unorderedlist":
                editor.list.toggle(format);
                break;

            case "indent":
                editor.indent.increase();
                break;

            case "outdent":
                editor.indent.decrease();
                break;

            case "clear":
                self.selectWithinTextMarkers();
                editor.inline.removeFormat();
                self.removeTextMarkers();
                break;

            default:
                // Create text markers
                !self.hasTextMarkers() && self.createTextMarkers();
                editor.inline.format(format);
                self.lastUpdatedViaFontFormattingUI = true;
                break;
        }
    },

    //
    // Text Marker API
    //

    hasTextMarkers: function() {

        return self.workarea().find(".composer-text-marker").length > 0;
    },

    createTextMarkers: function() {

        // Remove existing markers
        editor.selection.removeMarkers();

        // Create new markers
        editor.selection.createMarkers();

        // Create text marker
        var markers =
            $.makeArray(
                self.workarea()
                    .find(".redactor-selection-marker")
                    .each(function(){
                        $(this)
                            .removeClass("redactor-selection-marker")
                            .addClass("composer-text-marker")
                            .attr("id", this.id.replace("selection-", "text-"));
                    })
                );

        // Select within text markers
        self.selectWithinTextMarkers(markers);

        return markers;
    },

    getTextMarkers: function() {

        var markers =
            $.makeArray(
                    self.workarea()
                        .find("span#text-marker-1, span#text-marker-2")
                );

        if (markers[0] && markers[1]) return markers;
    },

    selectWithinTextMarkers: function(markers) {

        // Get markers
        markers || (markers = self.getTextMarkers());

        // Adjust selection to cover only contents within the marker
        markers && editor.caret.set(markers[0].nextSibling, 0, markers[1], 0);
    },

    removeTextMarkers: function() {

        self.workarea()
            .find(".composer-text-marker")
            .remove();
    },

    startEditingSelection: function() {

        isEditingSelection = true;

        // Remove text markers
        self.removeTextMarkers();

        // When user clicks anywhere in the workarea
        self.workarea()
            .off("mousedown.stopEditingSelection")
            .one("mousedown.stopEditingSelection", function(){

                // Stop editing selection
                self.stopEditingSelection();
            });
    },

    stopEditingSelection: function() {

        isEditingSelection = false;
    },

    //
    // Links API
    //

    initLinks: function() {

        // Keep a copy of the template
        self.linkItem.template =
             self.linkItem(".is-blank")
                .detach()
                .removeClass("is-blank")[0];
    },

    populateLinks: function() {

        // Get all nodes in text selection
        var nodes = editor.selection.getNodesInRange();

        // Find all anchor nodes within it
        var anchorNodes = $(nodes).filter("a");

        // Generate link item for existing anchor nodes
        var linkItemGroup = self.linkItemGroup().empty();

        $.each(anchorNodes, function(i, anchorNode){

            // Generate link item
            var linkItem = self.createLinkItem()
                                .appendTo(linkItemGroup);

            // Process link item
            self.processLinkItem(linkItem);

            // Update link item
            self.updateLinkItem(linkItem, anchorNode);
        });

        // Add .has-existing-links class on link item group if necessary
        self.linkItemGroup()
            .toggleClass("has-existing-links", anchorNodes.length > 0);

        // Create blank link item
        var linkItem =
                self.createLinkItem()
                    .addClass("is-new")
                    .appendTo(linkItemGroup);

        // Process link item
        self.processLinkItem(linkItem);

        // Get link caption from text selection
        var linkCaption = editor.selection.getText();

        // Set link caption on link preview
        self.linkPreviewCaption.inside(linkItem)
            .html(linkCaption);
    },

    createLinkItem: function() {

        var linkItem = $(self.linkItem.template).clone();

        return linkItem;
    },

    processLinkItem: function(linkItem) {

        // Get link url & title
        var linkUrlField = self.linkUrlField.inside(linkItem);
        var linkTitleField = self.linkTitleField.inside(linkItem);

        // Keep a reference to this element
        linkItem.data("linkUrlField", linkUrlField[0]);
        linkItem.data("linkTitleField", linkTitleField[0]);

        linkUrlField
            .data("linkItem", linkItem)
            .on("input", self.linkUrlFieldInputHandler)
            .on("mousedown", function(){
                if ($.IE) {
                    var anchorNode = linkItem.data("anchorNode");
                    if (!anchorNode) {
                       editor.selection.save();
                    }
                }
            });

        linkTitleField
            .data("linkItem", linkItem)
            .on("input", self.linkTitleFieldInputHandler)
            .on("mousedown", function(){
                if ($.IE) {
                    var anchorNode = linkItem.data("anchorNode");
                    if (!anchorNode) {
                       editor.selection.save();
                    }
                }
            });

        // Link input
        var linkInput = linkItem.find(".eb-link-input");

        linkUrlField.css({
            padding: "6px 12px",
            display: "block",
            fontSize: "13px",
            lineHeight: "24px",
            fontWeight: "bold",
            paddingBottom: "0px",
            color: "#555555",
            width: "100%",
            border: "none",
            outline: "none",
            position: "relative",
            zIndex: 2
        });

        if ($.IE) {
            linkUrlField.css({height: "28px"});
        }

        linkTitleField.css({
            border: "none",
            resize: "none",
            fontSize: "12px",
            padding: "12px",
            paddingTop: "32px",
            overflow: "hidden",
            outline: "none",
            position: "absolute",
            width: "100%",
            height: "100%",
            color: "#555555",
            fontFamily: "Arial, Helvetica, sans-serif",
            top: 0,
            left: 0,
            zIndex: 1
        });

        try {

            // Link Iframe
            var linkIframe = $("<iframe>");

            linkIframe.on("load", function(){

                $(linkIframe[0].contentWindow.document.body)
                    .css({
                        margin: 0,
                        overflow: "hidden"
                    })
                    .append(linkUrlField)
                    .append(linkTitleField);
            });

            linkInput.append(linkIframe);

        } catch(e) {
            console.error("There may a cross-iframe security issue. Unable to create text link item properly", e);
        }
    },

    updateLinkItem: function(linkItem, anchorNode)  {

        // Update link preview caption
        self.linkPreviewCaption.inside(linkItem)
            .html($(anchorNode).text());

        // Update link url field
        $(linkItem.data("linkUrlField"))
            .val(anchorNode.getAttribute("href"));

        // Update link title field
        $(linkItem.data("linkTitleField"))
            .val(anchorNode.title);

        // Update link blank option
        self.linkBlankOption.inside(linkItem)
            .prop("checked", anchorNode.target=="_blank");

        // Keep a reference to the <a> tag
        linkItem.data("anchorNode", anchorNode);
    },

    removeLinkItem: function(linkItem) {

        // Remove <a> associated to this link
        self.removeAnchorNode(linkItem);

        // If link item is new, just clear link fields.
        if (linkItem.hasClass("is-new")) {
            self.resetLinkItem(linkItem);
            return;
        }

        // Remove link item
        linkItem.remove();

        // If there are no more existing link item,
        // remove .has-existing-links
        if (self.linkItem(":not(.is-new)").length < 1) {
            self.linkItemGroup().removeClass("has-existing-links");
        }
    },

    resetLinkItem: function(linkItem) {

        // Clear link url field
        self.linkUrlField.inside(linkItem)
            .val("");

        // Clear link title field
        self.linkTitleField.inside(linkItem)
            .val("");

        // Uncheck link blank option
        self.linkBlankOption.inside(linkItem)
            .prop("checked", false);
    },

    createAnchorNode: function(linkItem) {

        // Get html of text selection
        var html = editor.selection.getHtml(),

            // Generate a temporary id for this <a> tag
            id = $.uid("link-"),

            // Create <a> tag with html of the text selection
            anchorNode = $("<a />").attr("id", id).html(html)[0];

        // Insert <a> tag into the editor.
        // Note: This <a> tag will also replace the existing text selection.
        editor.insert.node(anchorNode);

        // After inserting it seems we have lost reference to the <a> tag,
        // so we'll find it back again.
        anchorNode = $("#" + id).removeAttr("id")[0];

        // Update editor's text selection to select the <a> tag.
        editor.selection.selectElement(anchorNode);

        // Remove .is-new class from link item,
        // and store a reference to the <a> tag.
        linkItem
            .data("anchorNode", anchorNode);

        return anchorNode;
    },

    updateAnchorNode: function(linkItem) {

        // Get <a> tag associated to this link item.
        var anchorNode = linkItem.data("anchorNode");

        // Skip if no <a> tag associated to this link item.
        if (!anchorNode) return;

        var urlField    = $(linkItem.data("linkUrlField")),
            titleField  = $(linkItem.data("linkTitleField")),
            blankOption = self.linkBlankOption.inside(linkItem);

        // Set href & title attribute
        anchorNode.href  = $.trim(urlField.val());
        anchorNode.title = titleField.val();

        // Set target attribute.
        if (blankOption.is(":checked")) {
            anchorNode.target = "_blank";
        } else {
            anchorNode.removeAttribute("target");
        }
    },

    removeAnchorNode: function(linkItem) {

        // Get <a> tag associated to this link item.
        var anchorNode = linkItem.data("anchorNode");

        // Skip if no <a> associated to this link item.
        if (!anchorNode) return;

        // Save selection
        editor.selection.save();

        $anchorNode = $(anchorNode);

        // Take contents of <a> tag out and insert after it
        $anchorNode.contents()
            .insertAfter($anchorNode);

        // Remove <a> tag
        $anchorNode.remove();

        // Restore selection
        editor.selection.restore();

        // Remove association from link item
        linkItem.removeData("anchorNode");
    },

    //
    // Font Formatting UI
    "{fontFormatOption} click": function(fontFormatOption) {

        var format = fontFormatOption.data("format");

        if (format != 'clear') {
            fontFormatOption.toggleClass('active');
        }

        self.toggleFontFormatting(format);
    },

    //
    // Font Family UI
    //
    "{fontFamilyOption} click": function(fontFamilyOption) {

        var fontFamily = fontFamilyOption.data("value");

        self.setFontFamily(fontFamily);
    },

    //
    // Font Color UI
    //

    "{colorpicker} colorpickerChange": function(colorpicker, event, fontColor) {

        if (self.updatingFontColorUI) return;

        self.colorpickerToggle().prop("checked", true);

        self.setFontColor(fontColor);
    },

    "{colorpickerToggle} change": function(colorpickerToggle) {

        // If we're disable font color, remove font color.
        if (!colorpickerToggle.checked()) {
            self.removeFontColor();
        }
    },

    //
    // Font Size UI
    //
    "{fontSizeToggle} click": function(fontSizeToggle) {

        var fontSizeCheckbox = self.fontSizeCheckbox();

        if (fontSizeCheckbox.is(":checked")) {

            fontSizeCheckbox.prop("checked", false);

            // If we're disabling font size, remove font size.
            self.removeFontSize();
        } else {

            fontSizeCheckbox.prop("checked", true);
        }
    },

    "{numsliderWidget} nouislide": function(numsliderWidget, event, value) {

        // Sliding only updates input
        self.numsliderInput()
            .val(Math.abs(value));
    },

    "{numsliderWidget} set": function(numsliderWidget, event, value) {

        if (self.updatingFontSizeUI) return;

        self.setFontSize(Math.abs(value));
    },

    "{numsliderInput} input": function(numsliderInput) {

        if (self.updatingFontSizeUI) return;

        var fontSize = Math.abs($.trim(numsliderInput.val()));

        self.setFontSize(fontSize);
    },

    "{numsliderUnit} click": function(numsliderUnit) {

        var unit = numsliderUnit.data("unit");

        self.setFontSizeUnit(unit);

        self.numsliderUnits().removeClass("open");
    },

    "{numsliderUnitToggle} click": function(numsliderUnitToggle) {

        self.numsliderUnits().toggleClass("open");
    },

    //
    // Link UI
    //

    "{linkItem} mouseover": function(linkItem, event) {

        var anchorNode = linkItem.data("anchorNode");

        if (!anchorNode) return;

        $(anchorNode).addClass("is-highlighting");
    },

    "{linkItem} mouseout": function(linkItem, event) {

        var anchorNode = linkItem.data("anchorNode");

        if (!anchorNode) return;

        $(anchorNode).removeClass("is-highlighting");
    },

    linkUrlFieldInputHandler: function(event) {

        var linkUrlField = $(this);

        // Start editing selection
        self.startEditingSelection();

        // Get link item
        var linkItem = $(linkUrlField.data("linkItem")),

            // Get link href
            href = $.trim(linkUrlField.val()),

            // Get <a> tag
            anchorNode = linkItem.data("anchorNode");

        // If this is the first time we are creating this link,
        if (href!=="" && !anchorNode) {

            if ($.IE) {
                // Restore selection
                editor.selection.restore();
            }

            // wrap text selection in <a> tag.
            anchorNode = self.createAnchorNode(linkItem);

            if ($.IE) {
                // Focus back on link url field
                linkUrlField.focus().val(linkUrlField.val());
            }
        }

        // If we are removing this link,
        if (href=="" && anchorNode) {
            // unwrap <a> tag from text selection.
            self.removeAnchorNode(linkItem);
            return;
        }

        // Update <a> tag with the appropriate values.
        self.updateAnchorNode(linkItem);
    },

    linkTitleFieldInputHandler: function(event) {

        var linkTitleField = $(this);

        // Start editing selection
        self.startEditingSelection();

        // Get link item
        var linkItem = $(linkTitleField).data("linkItem");

        // Update <a> tag with the appropriate values.
        self.updateAnchorNode(linkItem);
    },

    "{linkBlankOptionField} click": function(linkBlankOptionField, event) {

        // We'll have to do this because prevent default is in place.
        var linkBlockOption = linkBlankOptionField.find("input");
        if (linkBlockOption.is(":checked")) {
            linkBlockOption.prop("checked", false);
        } else {
            linkBlockOption.prop("checked", true);
        }
        // Start editing selection
        self.startEditingSelection();

        // Get link item
        var linkItem = self.linkItem.of(linkBlankOptionField);

        // Update <a> tag with the appropriate values.
        self.updateAnchorNode(linkItem);
    },

    "{linkRemoveButton} click": function(linkRemoveButton, event) {

        // Start editing selection
        self.startEditingSelection();

        var linkItem = self.linkItem.of(linkRemoveButton);

        self.removeLinkItem(linkItem);
    }

}});


module.resolve();

});

});

EasyBlog.module("composer/blocks/tree", function($){

var module = this;

EasyBlog.require()
.done(function(){

    EasyBlog.Controller("Composer.Blocks.Tree", {
        elements: [
            "[data-eb-{block|block-icon|block-title|block-level-count|block-child-count}]",
            "[data-eb-blocks-{tree-field|tree|tree-item|tree-item-template|tree-item-icon|tree-item-title|tree-item-group|tree-toggle-button}]"
        ]
    },
    function(self, opts, base, composer, blocks, createTreeItem) { return {

        init: function() {

            blocks = self.blocks;
            composer = blocks.composer;

            // For compression
            createTreeItem = self.createTreeItem;

            self.treeItemTemplate = self.treeItemTemplate().detach().html();
        },

        setCurrentBlock: function(block) {

            var type = blocks.getBlockType(block);
            var meta = blocks.getBlockMeta(type);

            // Title & Icon
            self.blockTitle()
                .text(meta.title);

            self.blockIcon()
                .attr("class", meta.icon);

            // Stat
            var parentBlocks = blocks.getAllParentBlocks(block);
            var childBlocks = blocks.getChildBlocks(block);
            var childBlocksCount = childBlocks.length;
            var blockLevel = parentBlocks.length + 1;

            self.blockChildCount()
                .text(childBlocksCount);

            self.blockLevelCount()
                .text(blockLevel);

            self.block()
                .toggleClass("has-child", childBlocksCount);
        },

        populate: function(block) {

            // If block not given, use current block.
            if (!block) {
                var block = blocks.getCurrentBlock();
            }

            if (!block.length) {
                return;
            }

            // Get tree field to determine what type of
            // tree that we should populate.
            var treeField = self.treeField();

            // Get parent block
            var parentBlock = blocks.getParentBlock(block);
            var hasParent = !!parentBlock.length;

            // Get child blocks
            var childBlocks = blocks.getChildBlocks(block);
            var hasChildren = !!childBlocks.length;

            // Toggle has-parent/has-children class
            treeField
                .toggleClass("has-parent", hasParent)
                .toggleClass("has-children", hasChildren);

            // Build tree items
            var treeItems = [];

            // Get tree display mode
            var treeDisplayMode = self.getTreeDisplayMode();

            // Miminal tree
            if (treeDisplayMode=="minimal") {

                // Parent block
                if (hasParent) {

                    treeItems.push(createTreeItem(parentBlock, 1));

                    // Generate sibling blocks
                    var siblingBlocks = blocks.getChildBlocks(parentBlock);

                    siblingBlocks.each(function(){

                        var siblingBlock = $(this);
                        treeItems.push(createTreeItem(siblingBlock, 2));

                        if (siblingBlock.is(block)) {

                            childBlocks.each(function(){
                                var childBlock = $(this);
                                treeItems.push(createTreeItem(childBlock, 3));
                            });
                        }
                    });

                // If there is no parent, generate current block and its child blocks
                } else {

                    // Current block
                    treeItems.push(createTreeItem(block, 1));

                    // Child blocks
                    childBlocks.each(function(){
                        var childBlock = $(this);
                        treeItems.push(createTreeItem(childBlock, 2));
                    });
                }

            // Full tree
            } else {

                var rootBlocks = blocks.getRootBlocks();

                var addTreeItem = function(block, level) {

                    var currentLevel = level;

                    // Create tree item
                    treeItems.push(createTreeItem(block, currentLevel));

                    // Get child blocks
                    var childBlocks = blocks.getChildBlocks(block);

                    // If there are child blocks, add tree item of this block.
                    childBlocks.each(function(){
                        var childBlock = $(this);
                        addTreeItem(childBlock, currentLevel + 1);
                    });
                }

                rootBlocks.each(function(){
                    addTreeItem($(this), 1);
                });
            }

            // Do not show tree item group when there's only a single item
            // on minmal tree display mode.
            self.tree()
                .toggle(treeDisplayMode=="full" || treeItems.length > 1)

            self.treeItemGroup()
                .empty()
                .append(treeItems);
        },

        createTreeItem: function(block, level) {

            var treeItem = block.data("treeItem");

            if (!treeItem) {
                treeItem = self.renderTreeItem(block);
                block.data("treeItem", treeItem);
            }

            treeItem
                .toggleClass("active", block.hasClass("active"))
                .toggleClass("is-nested", block.hasClass("is-nested"))
                .switchClass("level-" + level);

            return treeItem;
        },

        renderTreeItem: function(block) {

            var uid = blocks.getBlockUid(block);
            var type = blocks.getBlockType(block);
            var meta = blocks.getBlockMeta(type);
            var treeItem = $(self.treeItemTemplate);

            treeItem
                .attr({
                    "data-type": meta.type,
                    "data-uid": uid
                })
                .find(self.treeItemIcon)
                    .addClass(meta.icon)
                    .end()
                .find(self.treeItemTitle)
                    .text(meta.title);

            return treeItem;
        },

        getTreeDisplayMode: function() {

            return self.treeField().hasClass("tree-minimal") ? "minimal" : "full";
        },

        setTreeDisplayMode: function(treeDisplayMode) {

            self.treeField()
                .switchClass("tree-" + treeDisplayMode);

            self.populate();
        },

        "{self} composerBlockAdd": function(base, event, block, handler) {

            self.populate();
        },

        "{self} composerBlockActivate": function(base, event, block, handler) {

            self.setCurrentBlock(block);
            self.populate(block);
        },

        "{self} composerBlockRemove": function(base, event, block) {

            self.populate();
        },

        "{treeItem} click": function(treeItem) {

            var uid = treeItem.data("uid");
            var block = blocks.getBlock(uid);

            // Unhighlight block
            blocks.unhighlight(block);

            // Activate block
            blocks.activateBlock(block);
        },

        "{treeItem} mouseenter": function(treeItem) {

            var uid = treeItem.data("uid");
            var block = blocks.getBlock(uid);

            // Highlight block
            blocks.highlight(block);

            // Scroll to block
            blocks.scrollTo(block);
        },

        "{treeItem} mouseleave": function(treeItem) {

            var uid = treeItem.data("uid");
            var block = blocks.getBlock(uid);

            // Unhighlight block
            blocks.unhighlight(block);
        },

        "{treeToggleButton} click": function() {

            var treeField = self.treeField();
            var treeDisplayMode = self.getTreeDisplayMode();

            self.setTreeDisplayMode(treeDisplayMode=="minimal" ? "full" : "minimal");
        }

    }});

    module.resolve();

});

});

EasyBlog.module("composer/blocks", function($){

var module = this,

    // Block States
    isNew        = "is-new",
    isEditable   = "is-editable",
    isReceiving  = "is-receiving",
    isDropping   = "is-dropping",
    isReleasing  = "is-releasing",
    isRefocusing = "is-refocusing",
    isHighlighting = "is-highlighting",

    isSorting   = "is-sorting",
    isSortItem  = "is-sort-item",
    isNested    = "is-nested",

    isDraggingBlock = "is-dragging-block",
    isDroppingBlock = "is-dropping-block",

    // Workarea States
    isHighlightingBlock = "is-highlighting-block";

EasyBlog.require()
.library(
    "ui/draggable",
    "scrollTo"
)
.script(
    "composer/blocks/panel",
    "composer/blocks/guide",
    "composer/blocks/nestable",
    "composer/blocks/droppable",
    "composer/blocks/resizable",
    "composer/blocks/scrollable",
    "composer/blocks/font",
    "composer/blocks/dimensions",
    "composer/blocks/text",
    "composer/blocks/removal",
    "composer/blocks/tree",
    "composer/blocks/search",
    "composer/blocks/mobile",
    "composer/blocks/media",
    "composer/blocks/toolbar"
)
.done(function(){

EasyBlog.Controller("Composer.Blocks", {
    hostname: "blocks",

    pluginExtendsInstance: true,

    elements: [
        "[data-eb-composer-{blocks}]",
        "[data-eb-composer-block-{menu|menu-group|meta}]",
        "[data-eb-composer-{editor}]",
        "[data-ebd-block-{viewport|content}]"
    ],

    defaultOptions: $.extend({
        "{view}": "[data-eb-composer-blocks]",
        "{editableContent}": EBD.editableContent,
        "{immediateBlockViewport}": EBD.immediateBlockViewport,
        "{immediateBlockContent}": EBD.immediateBlockContent,
        "{sidebar}": "[data-eb-composer-blocks]",
        "{mechswitch}": ".mechswitch"
    }, EBD.selectors)
},
function(self, opts, base, composer) { return {

    init: function() {

        // Globals
        composer = self.composer;

        // Block plugins
        var plugins = [
            "panel",
            "guide",
            "nestable",
            "droppable",
            "resizable",
            "scrollable",
            "font",
            "dimensions",
            "text",
            "removal",
            "tree",
            "search",
            "mobile",
            "media",
            "toolbar"
        ];

        // Legacy post loads only panel & dimensions
        if (composer.getDoctype()=="legacy") {
            var plugins = [
                "panel",
                "tree",
                "dimensions",
                "media"
            ];
        }

        $.each(plugins, function(i, plugin){
            self.addPlugin(plugin);
        });

        // Preload the following blocks
        var preloadBlocks = [
            "post",
            "image",
            "video",
            "file",
            "audio"
        ];

        $.each(preloadBlocks, function(i, blockType) {
            self.loadBlockHandler(blockType);
        });
    },

    getAllBlocks: function()  {
        return self.root().find(EBD.block);
    },

    getCurrentBlock: function() {
        return self.root().find(EBD.block + ".active");
    },

    getParentBlock: function(block) {
        if (!block.is(EBD.nestedBlock)) return $();
        return $(block).parents(EBD.block).eq(0);
    },

    getAllParentBlocks: function(block) {
        return block.parentsUntil(EBD.root).filter(EBD.block);
    },

    getBlockTree: function(block) {
        return self.getAllParentBlocks(block).add(self.root());
    },

    getRootBlocks: function() {
        return self.root().find(EBD.childBlock);
    },

    getChildBlocks: function(block) {
        return block.is(EBD.root) ?
            block.find(EBD.childBlock) :
            block.find(EBD.nestedBlock + ":not(.ebd-block[data-uid=" + block.data("uid") + "] " + EBD.nest + " " + EBD.nest + " " + EBD.nestedBlock + ")");
    },

    getAllChildBlocks: function(block) {
        return block.find(EBD.block);
    },

    getBlocksByType: function(type) {
        return self.root().find(EBD.block + "[data-type=" + type + "]");
    },

    getBlock: function(uid) {
        var block = base.find(EBD.block + "[data-uid=" + uid + "]");
        return block;
    },

    getBlockUid: function(block) {
        // If block has no uid, set one.
        return block.attr("data-uid") || self.setBlockUid(block);
    },

    setBlockUid: function(block) {

        // If block already has a uid, just return it.
        return block.attr("data-uid") || (function(){
            var uid = $.uid();
            block.attr("data-uid", uid);
            return uid;
        })();
    },

    getBlockType: function(block) {
        return $(block).attr("data-type");
    },

    metas: {},

    getBlockMeta: function(blockType) {

        if (blockType instanceof $) {
            blockType = self.getBlockType(blockType);
        }

        return self.metas[blockType] || (function(){

            // Extract inline block meta within block menu
            var inlineBlockMeta = self.meta().where("type", blockType).val()

            // If inline block meta exists
            if (inlineBlockMeta) {
                // Parse, cache and return it.
                return self.metas[blockType] = JSON.parse(inlineBlockMeta);

            // If inline block meta doesn't exist, return null.
            } else {
                return null;
            }

        })();
    },

    getBlockViewport: function(block) {

        return self.immediateBlockViewport.inside(block);
    },

    getBlockContent: function(block) {

        return self.immediateBlockContent.inside(block);
    },

    getBlockFragment: function(block) {

        var getFragment = function(block) {

            var blockType = self.getBlockType(block),
                blockHandler = self.getBlockHandler(blockType),
                blockFragment = self.createBlockContainer(blockType),
                blockHTML = blockHandler.toHTML(block);

            if (block.is(EBD.nested)) {
                block.addClass(isNested);
            }

            blockFragment.append(blockHTML);

            return blockFragment;
        }

        var blockFragment = getFragment(block);

        blockFragment.find(EBD.nestedBlock)
            .each(function(){

                var nestedBlock = $(this),
                    nestedBlockFragment = getFragment(nestedBlock);

                nestedBlock.replaceWith(nestedBlockFragment);
            });

        return blockFragment;
    },

    getBlockHTML: function(block) {

        return self.getBlockFragment(block).toHTML();
    },

    getBlockText: function(block) {

        // TODO: See document.getText() for more info.
    },

    getBlockNest: function(block) {
        return block.closest(EBD.nest);
    },

    getBlockNestType: function(block) {
        return self.getBlockNest(block).data("type");
    },

    isBlock: function(block) {
        return block.is(EBD.block);
    },

    isNestedBlock: function(block) {
        return block.is(EBD.nestedBlock);
    },

    isRootBlock: function(block) {
        return !self.isNestedBlock(block);
    },

    isStandaloneBlock: function(block) {
        return block.is(EBD.standaloneBlock);
    },

    restoreInlineBlockData: function(block) {

        // If there is inline block data
        var inlineBlockData = block.next();

        if (inlineBlockData.is("textarea[data-block]")) {

            // Extract block data from inline block data
            var rawBlockData = inlineBlockData.val();
            var blockData = JSON.parse(rawBlockData);

            // Attach block data into dataset by block uid
            self.dataset[self.getBlockUid(block)] = blockData;

            // Remove inline block data
            inlineBlockData.remove();
        }
    },

    initBlock: function(block) {

        // For compressibility
        var args = arguments;
        var type = self.getBlockType(block);

        // Assign a block id if necessary
        self.setBlockUid(block);

        // Restore inline block data if necessary
        self.restoreInlineBlockData(block);

        // Load block handler
        // If block handler has been loaded, the operation here is synchronous.
        // If block handler hasn't been loaded, the operation here is asynchronous.
        self.loadBlockHandler(type)
            .done(function(handler){

                // If this is a new block
                if (block.hasClass(isNew)) {

                    // Trigger composerBlockBeforeAdd
                    self.trigger("composerBlockBeforeAdd", args);

                    try {
                        // Reset, reconstruct and refocus on block
                        handler.reset(block);
                        handler.reconstruct(block);
                    } catch(ex) {
                        EasyBlog.debug && console.error("Error initializing new block of type '%s'.", type, ex);
                    }

                    // Trigger composerBlockAdd
                    self.trigger("composerBlockAdd", args);

                    // Simulate composerBlockRelease
                    self.release(block);

                    // Remove new block flag
                    block
                        .removeClass(isNew)
                        .addClass(isEditable);

                    self.trigger("composerBlockInit", args);

                // If this is a viewable block
                } else if (!block.hasClass(isEditable)) {

                    try {
                        // Reconstruct block to convert into editable block
                        handler.reconstruct(block);
                    } catch(ex) {
                        EasyBlog.debug && console.error("Error initializing existing block of type '%s'.", type, ex);
                    }

                    // Add editable block flag
                    block.addClass(isEditable);

                    self.trigger("composerBlockInit", args);
                }
            });

        // Get child blocks
        self.getChildBlocks(block)
            .each(function(){
                // Initialize all child blocks
                var childBlock = $(this);
                self.initBlock(childBlock);
            });
    },

    createBlockContainer: function(blockType) {

        var blockMeta = self.getBlockMeta(blockType);
        var blockContainer = $($('[data-eb-block-template]').clone().html());

        blockContainer
            .attr("data-type", blockType);

        return blockContainer;
    },

    createBlock: function(blockType) {
        // Get block meta
        var blockMeta = self.getBlockMeta(blockType);

        // If no block meta found, do not create block.
        if (!blockMeta) {
            return;
        }

        // Create block
        var block = $(blockMeta.block);

        // Assign block uid
        self.setBlockUid(block);

        // Trigger composerBlockCreate
        self.trigger("composerBlockCreate", [block, blockMeta]);

        return block;
    },

    createBlockFromMenu: function(menu) {

        // Get block type
        var blockType = menu.attr("data-type"),

            // Create block and set new block flag
            block = self.createBlock(blockType).addClass(isNew);

        return block;
    },

    createBlockFromMediaFile: function(mediaFile) {

        // Determine block type by media file type
        var blockType = mediaFile.data("type");

        // Get block handler
        var blockHandler = self.getBlockHandler(blockType);

        // Construct block from media file
        var block = blockHandler.constructFromMediaFile(mediaFile);

        return block;
    },

    constructBlock: function(blockType, blockData) {

        var blockHandler = self.getBlockHandler(blockType);
        var block = blockHandler.construct(blockData);

        // Assign block uid
        self.setBlockUid(block);

        self.trigger("composerBlockConstruct", [block]);

        return block;
    },

    constructNestedBlock: function(blockType, blockData) {

        // Construct block
        var block = self.constructBlock(blockType, blockData);

        // Add is-nested class
        block.addClass(isNested);

        return block;
    },

    constructIsolatedBlock: function(blockType, blockData) {

        // Construct block
        var block = self.constructBlock(blockType, blockData);

        // Add is-nested & is-isolated class
        block
            .addClass(isNested)
            .addClass(isIsolated);

        return block;
    },

    createBlockNest: function() {

        return $('<div class="ebd-nest" data-type="block">');
    },

    createContentNest: function(options) {

        // Create nest
        var nest = $('<div class="ebd-nest" data-type="content">'),

            // Normalize options
            defaultOptions = {
                // paragraph: true,
                editable: true
            },

            options = $.extend(defaultOptions, options);

        // This makes nest editable by default
        options.editable && nest.editable(true);

        // This ensure content are always wrapped in <p> tags.
        // options.paragraph && nest.attr("data-paragraph", "true");

        return nest;
    },

    exportBlock: function(block) {

        var blockUid     = self.getBlockUid(block),
            blockType    = self.getBlockType(block),
            blockHandler = self.getBlockHandler(blockType),
            isNested     = block.is(EBD.nestedBlock),
            isIsolated   = block.is(EBD.isolatedBlock);

        // Create block manifest
        var blockManifest = {
            uid: blockUid,
            type: blockType,
            html: "",
            data: {},
            blocks: [],
            nested: isNested,
            isolated: isIsolated,
            style: block.attr('style')
        };

        // @debug: Verify if handler has toData, toText, toHTML methods.
        EasyBlog.debug && self.verifyBlockHandler(blockHandler, ["toData", "toText", "toHTML"]);

        // Data, text, html
        blockHandler.toData && (blockManifest.data = blockHandler.toData(block) || {});
        blockHandler.toText && (blockManifest.text = blockHandler.toText(block) || "");
        blockHandler.toHTML && (blockManifest.html = blockHandler.toHTML(block) || "");

        if (blockHandler.toEditableHTML) {
            blockManifest.editableHtml = blockHandler.toEditableHTML(block) || "";
        } else {
            var blockContent = blocks.getBlockContent(block);
            blockManifest.editableHtml = blockContent.html();
        }

        // Trigger ComposerBlockExport event for plugins to further decorate this block.
        self.trigger("composerBlockExport", [block, blockManifest]);

        // Return block manifest
        return blockManifest;
    },

    addBlock: function(block) {

        self.root().append(block);

        self.initBlock(block);
    },

    removeBlock: function(block) {

        self.trigger("composerBlockBeforeRemove", [block]);

        // Find nested block and remove them
        block.find(EBD.nestedBlock)
            .each(function(){

                var nestedBlock = $(this);

                // Remove nested block
                self.removeBlock(nestedBlock);
            });

        // Remove block
        block.remove();

        // Trigger composerBlockRemove event
        self.trigger("composerBlockRemove", [block]);
    },

    scrollTo: function(block) {

        var viewport = composer.viewport();

        composer.viewportContent()
            .stop(true)
            .scrollTo(block, 500, {
                axis: 'y',
                offset: {
                    top: (viewport.height() - block.height()) / -2,
                    left: 0
                }
            });
    },

    blockHandlers: {},

    loadBlockHandler: $.memoize(function(blockType) {

        // Reject invalid handler type
        if (!blockType) {
            return $.Deferred().reject($.Exception("Invalid block type given."));
        }

        // Get block meta
        var blockMeta = self.getBlockMeta(blockType);
        if (!blockMeta) {
            return $.Deferred().reject($.Exception("Block of type '" + blockType + "' is not installed!"));
        }

        var loader = $.Deferred();

        EasyBlog.require()
            .script("composer/blocks/handlers/" + blockType)
            .done(function(){

                // Construct block handler plugin namespace
                var pluginName = "handler/" + blockType;
                var controllerName = "EasyBlog.Controller.Composer.Blocks.Handlers." + $.String.capitalize(blockType);
                var pluginProps = {meta: blockMeta};

                // Install block handler plugin
                var blockHandler = self.addPlugin(pluginName, controllerName, pluginProps);

                // Keep a reference to this block handler in the blockHandlers registry
                self.blockHandlers[blockType] = blockHandler;

                // If we're debugging, verify handler completeness.
                EasyBlog.debug && self.verifyBlockHandler(blockHandler);

                // Resolve loader
                loader.resolve(blockHandler, blockMeta);
            })
            .fail(function(){

                // Do not memoize if loading of handler failed
                self.loadBlockHandler.reset(blockType);

                // Reject loader
                loader.reject($.Exception("Could not load block handler for " + blockType + "."));
            });

        return loader;
    }),

    getBlockHandler: function(blockType) {

        // Also accept block as first argument
        if (blockType instanceof $) {
            blockType = self.getBlockType(blockType);
        }

        return self.blockHandlers[blockType];
    },

    verifyBlockHandler: function(blockHandler, methods) {

        var methods = methods || ["activate", "deactivate", "construct", "reconstruct", "deconstruct", "refocus", "reset", "populate", "toHTML", "toData", "toText"],
            method,
            missing = [];

        while (method = methods.shift()) {
            if (!$.isFunction(blockHandler[method])) {
                missing.push(method);
            }
        }

        missing.length > 0 && console.warn("Block handler of type '%s' is missing the following method: %s.", blockHandler.options.meta.type, missing.join(", "));
    },

    activateBlock: function(block) {

        // Do not activate block when we're removing block
        if (self.workarea().hasClass("is-removing")) return;

        // Deactivate any current block
        self.deactivateBlock();

        var type = self.getBlockType(block);

        // Load block handler
        self.loadBlockHandler(type)
            .done(function(handler){

                var args = [block, handler];

                // Trigger composerBlockBeforeActivate
                self.trigger("composerBlockBeforeActivate", args);

                // Initialize block
                self.initBlock(block);

                // Activate block handler
                handler.activate && handler.activate(block);

                // Refocus block
                handler.refocus(block);

                // Trigger composerBlockActivate
                self.trigger("composerBlockActivate", args);
            })
            .fail(function(exception){

                self.trigger("composerBlockActivateError", [exception]);
            });
    },

    deactivateBlock: function(block) {

        block = block || self.getCurrentBlock();

        // If no block found, stop.
        if (!block.length) return;

        // Get block handler
        var blockHandler = self.getBlockHandler(block);

        // Deactivate block handler
        blockHandler && blockHandler.deactivate && blockHandler.deactivate(block);

        // Trigger composerBlockDeactivate
        self.trigger("composerBlockDeactivate", [block, blockHandler]);
    },

    "{menu} mouseover": function(menu) {

        // Only initialize draggable on mouseover
        if (!menu.data("uiDraggable")) {

            // Prepare lightweight helper
            var helper = menu.clone();

            // Remove unnecessary inline block meta
            helper
                .find(self.meta.selector)
                .remove();

            menu.draggable({
                helper: function() {
                    return helper.css({
                        width: menu.outerWidth(),
                        height: menu.outerHeight()
                    })
                },
                appendTo: composer.document.ghosts(),
                connectToSortable: EBD.root
            });
        }
    },

    selectBlock: function(menu) {

        // Hide the blocks menu
        composer.views.hide('blocks');

        // Add state is-dropping-block
        composer.manager()
            .addClass(isDroppingBlock);

        // Trigger composerBlockMenuSelected so the world can listen to this
        self.trigger("composerBlockMenuSelected", [menu]);

        // Show the drop zones
        composer.blocks.droppable.populateDropzones();
    },

    "{menu} touchend": function(menu) {
    },

    "{menu} touchstart": function(menu) {
    },

    "{menu} click": function(menu) {
        self.selectBlock(menu);
    },

    "{menu} dragstart": function(menu) {

        // Tell block host we're dragging this menu
        self.drag(menu);

        // When a block menu is being dragged we want to add a class on the manager
        composer.manager()
            .addClass(isDraggingBlock);

        // Hide block view
        composer.views.hide("blocks");
    },

    "{menu} dragstop": function(menu) {

        // Tell block host we're done with this menu
        self.release(menu);

        composer.manager()
            .removeClass(isDraggingBlock);
    },

    "{menu} dblclick": function(menu) {
        // TODO: Double click to insert block
    },

    drag: function(block) {

        // Add is-sorting class to workarea
        self.workarea()
            .addClass(isSorting);

        // Skip new block
        if (block.is(self.menu)) {
            return;
        }

        // Add is-sort-item class to block
        block.addClass(isSortItem);

        // Carets will go out of place after sorting an existing block,
        // this removes carets from editor when sorting starts.
        composer.editor.selection.remove();

        // Trigger composerBlockDrop event
        self.trigger("composerBlockDrag", [block]);
    },

    drop: function(block) {

        // Skip new block
        if (block.is(self.menu)) {
            return;
        }

        // Trigger composerBlockBeforeRelease event
        self.trigger("composerBlockBeforeDrop", [block]);

        // Add is-dropping class
        block.addClass(isDropping);

        // Trigger composerBlockDrop event
        self.trigger("composerBlockDrop", [block]);

        // Activate block
        self.activateBlock(block);
    },

    release: function(block) {

        // Remove is-sorting class from workarea
        self.workarea().removeClass(isSorting);

        // Skip new block
        if (block.is(self.menu)) {
            return;
        }

        // This is for the zoom effect on existing block
        block
            .removeClass(isDropping)
            .addClass(isReleasing)
            .removeClassAfter(isReleasing + " " + isSortItem);

        // Trigger composerBlockBeforeRelease event
        self.trigger("composerBlockBeforeRelease", [block]);

        setTimeout(function(){

            // Trigger composerBlockReleased event
            self.trigger("composerBlockRelease", [block]);

        }, 1000);
    },

    over: function(block) {

        // Add is-receiving class on dropzone
        block.addClass(isReceiving);

        // Add is-receiving class on parent nest
        if (block.hasClass(isNested)) {
            self.nest.of(block)
                .addClass(isReceiving);
        }

        // Add is-receiving class on parent block
        self.block.of(block)
            .addClass(isReceiving);
    },

    out: function(block) {

        // Add is-receiving class on dropzone
        block.removeClass(isReceiving);

        // Add is-receiving class on parent nest
        if (block.hasClass(isNested)) {
            self.nest.of(block)
                .removeClass(isReceiving);
        }

        // Add is-receiving class on parent block
        self.block.of(block)
            .removeClass(isReceiving);
    },

    highlight: function(block) {

        self.root()
            .addClass(isHighlightingBlock);

        block.addClass(isHighlighting);
    },

    unhighlight: function(block) {

        self.root()
            .removeClass(isHighlightingBlock)

        block.removeClass(isHighlighting);
    },

    dataset: {},

    "{block} click": function(block, event) {

        // Do not activate block if:
        // - Nested block has been activated (because click event propagates from nested block to parent block).
        // - User is resizing block.
        if (event.clickHandled || self.refocusing || self.workarea().hasClass("is-resizing")) {
            return;
        }

        // This stops click event from propagating to parent block
        event.clickHandled = true;

        // Only activate if it hasn't been activated before
        if (!block.is(self.menu) && !block.hasClass("active")) {
            self.activateBlock(block);
        }
    },

    data: function(block, key, value) {
        if (block === undefined) {
            return self.dataset;
        }

        var uid = parseInt(block);

        if (isNaN(uid)) {
            // If block cannot be parsed as a number, means it is a jQuery object
            // We then extract the uid from it
            uid = self.getBlockUid(block);
        } else {
            // If uid is a number, means block is not a jQuery object
            // Also means that it is safe to override block variable with jQuery object since block is initially string/number, which is not by reference
            block = self.getBlock(uid);
        }

        var handler = self.getBlockHandler(self.getBlockType(block));
        var meta = blocks.getBlockMeta(block);
        var data = self.dataset[uid];

        self.dataset[uid] =
            handler.normalize ?
                handler.normalize(data) :
                $.extend({}, meta.data, data);

        if (arguments.length == 0 || arguments.length == 1 || (arguments.length == 2 && $.isString(key))) {
            return self.getData(block, key);
        }

        return self.setData.apply(self, arguments);
    },

    getData: function(block, key) {

        var dataset = self.dataset;

        if (block === undefined) {
            return dataset;
        }

        var uid = parseInt(block);

        if (isNaN(uid)) {
            // If block cannot be parsed as a number, means it is a jQuery object
            // We then extract the uid from it
            uid = self.getBlockUid(block);
        } else {
            // If uid is a number, means block is not a jQuery object
            // Also means that it is safe to override block variable with jQuery object since block is initially string/number, which is not by reference
            block = self.getBlock(block);
        }

        var blockDataset = dataset[uid];

        if (key === undefined) {
            return blockDataset;
        }

        return blockDataset[key];
    },

    setData: function(block, key, value) {

        if (block === undefined) {
            return self.dataset;
        }

        var uid = parseInt(block);

        if (isNaN(uid)) {
            // If block cannot be parsed as a number, means it is a jQuery object
            // We then extract the uid from it
            uid = self.getBlockUid(block);
        } else {
            // If uid is a number, means block is not a jQuery object
            // Also means that it is safe to override block variable with jQuery object since block is initially string/number, which is not by reference
            block = self.getBlock(block);
        }

        if ($.isPlainObject(key) && value === undefined) {
            return $.extend(self.dataset[uid], key);
        }

        return self.dataset[uid][key] = value;
    },

    getMechanics: function() {

        return composer.settings.get("blocks.mechanics");
    },

    setMechanics: function(type) {

        composer.settings.set("blocks.mechanics", type);

        self.trigger("composerBlockMechanicsChange", type);
    },

    "{mechswitch} click": function(mechswitch) {

        // These are all temporary for now
        self.mechswitch().removeClass("btn-primary");
        mechswitch.addClass("btn-primary");

        self.setMechanics(mechswitch.attr("data-value"));
    },

    "{block} mouseover": function(block, event) {

        // This ensures that only the top most block gets hovered.
        if (event.hovered) {
            return;
        }

        event.hovered = true;

        self.trigger("composerBlockHoverIn", [block]);
    },

    "{block} mouseout": function(block, event) {
        self.trigger("composerBlockHoverOut", [block]);
    },

    "{self} composerDocumentInit": function() {

        // Get all blocks
        var allBlocks = self.getAllBlocks();

        // Initialize all blocks
        allBlocks.each(function(){
            var block = $(this);
            self.initBlock(block);
        });
    },

    "{self} composerDocumentBlur": function() {

        if (self.refocusing) {
            // Negate refocusing after the first blur event
            self.refocusing = false;
            return;
        }

        self.deactivateBlock();
    },

    "{editableContent} focusin": function(editableContent, event) {

        if (event.focusHandled || self.refocusing) return;

        // Not application for legacy document
        if (composer.document.isLegacy()) return;

        self.refocusing = true;

        // This prevents focusin from propagating to parent block
        event.focusHandled = true;

        // Needs to be in a set timeout so we can
        // get the actual node where the caret is
        // focusing on.
        setTimeout(function(){

            var workarea = self.workarea();

            // Get focus node
            var selection = composer.editor.selection.get();
            var node = selection.focusNode;

            // If no focus node, stop.
            if (!node) {
                self.refocusing = false;
                return;
            }

            // Get block
            var block = self.block.of(node);

            // If no block found, stop.
            if (!block.length) {
                self.refocusing = false;
                return;
            }

            // If block is being refocused, stop.
            if (block.hasClass(isRefocusing)) return;

            // Add refocusing class
            // This is because when activating/refocusing block,
            // block handler may trigger focus event anywhere within
            // the editable content, this flag prevents recursion.
            block.addClass(isRefocusing);

            // Putting this in a try..catch so user can
            // continue editing when there is a failure
            // in activating or refocusing.
            try {

                // Activate block
                if (!block.hasClass("active")) {
                    self.activateBlock(block);

                // Refocus block
                } else {
                    var blockHandler = self.getBlockHandler(block);
                    blockHandler.refocus(block);
                }

            } catch(e) {}

            // Remove refocusing class
            block.removeClass(isRefocusing);

            // This needs to be delayed because when blocks are
            // being activated, focusing may happen causing
            // recursion in this event handler.
            setTimeout(function(){
                self.refocusing = false;
            }, 100);

        }, 1);
    }

}});

module.resolve();

});
});


EasyBlog.module("composer/blocks/media", function($) {

var module = this;

EasyBlog.Controller("Composer.Blocks.Media", {
    defaultOptions: $.extend({

    }, EBD.selectors),
}, function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = blocks.composer;
    },

    "{self} mediaInfoShow": function(base, event, uri) {

        // Switch composer frame to media layout
        composer.frame().addClass("layout-media");

        // Display blocks panel
        composer.panels.activate("blocks");
    },

    "{self} mediaInfoDisplay": function(base, event, info, media) {

        var block = info.find(".ebd-block");

        blocks.activateBlock(block);

        // On legacy document, blocks/guide plugin is not installed.
        // So we'll need to add active class here.
        block.addClass("active");
    },

    "{self} mediaInfoHide": function(base, event) {

        // Deactivate panel
        blocks.panel.deactivatePanel();

        // Remove media layout from composer frame
        composer.frame().removeClass("layout-media");
    },

    "{self} mediaInsert": function() {

        // Deactivate panel
        blocks.panel.deactivatePanel();

        // Remove media layout from composer frame
        composer.frame().removeClass("layout-media");
    }
}});

module.resolve();

});

EasyBlog.module("composer/blocks/toolbar", function($){

var module = this;

var isEditingBlock = "is-editing-block";
var isMovingBlock = "is-moving-block";

EasyBlog.Controller("Composer.Blocks.Toolbar",
{

    elements: [
        "[data-eb-blocks-{close-button|done-button|parent-button|cancel-drop-button|move-button|cancel-move-button|remove-button}]",
        "[data-ebd-block-{toolbar|sort-handle}]"
    ],

    defaultOptions: $.extend({}, EBD.selectors)
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {
        blocks = self.blocks;
        composer = self.blocks.composer;
    },

    "{self} composerBlockActivate": function(base, event, block) {

        // Not applicable for standalone blocks
        if (block.hasClass("is-standalone")) {
            return;
        }

        // For isolated blocks, do not show move button.
        self.moveButton().toggle(!block.hasClass("is-isolated"));

        composer.manager()
            .addClass(isEditingBlock);
    },

    "{self} composerBlockDeactivate": function() {

        composer.manager()
            .removeClass(isEditingBlock);
    },

    "{moveButton} click": function(moveButton) {
        // Populate dropzones
        composer.blocks.droppable.populateDropzones();

        // Add is-moving class
        composer.manager()
            .addClass(isMovingBlock);

        // Get the active block
        var currentBlock = blocks.getCurrentBlock();

        currentBlock
            .addClass('hide')
            .addClass('is-sort-item');

        self.trigger("composerBlockMove", [currentBlock]);
    },

    "{cancelMoveButton} click": function(cancelMoveButton) {

        // Remove the moving state
        composer.manager()  
            .removeClass(isMovingBlock);

        // Hide the dropzones
        composer
            .blocks
            .droppable
            .dropStop();

        // Show the current block
        var currentBlock = blocks.getCurrentBlock();
        currentBlock
            .removeClass('hide')
            .removeClass('is-sort-item');
    },

    "{doneButton} click": function(doneButton) {

        // TODO: How about revert to the previously activated block?
        blocks.deactivateBlock();
    },

    "{parentButton} click": function(parentButton) {

        var currentBlock = blocks.getCurrentBlock();

        if (currentBlock.length) {

            var parentBlock = blocks.getParentBlock(currentBlock);

            if (parentBlock.length) {
                blocks.activateBlock(parentBlock);
            } else {
                blocks.deactivateBlock();
            }
        }
    },

    "{cancelDropButton} click": function(cancelButton) {

        // Reset the selection
        composer.blocks.droppable.selectedMenu = null;
        composer.blocks.droppable.selectedBlock = null;

        // Destroy the dropzones
        composer.blocks.droppable.dropStop();

        // Remove the class on the manager
        composer.manager().removeClass("is-dropping-block");
    },

    "{closeButton} click": function(closeButton) {

        composer.views.hide("blocks");
    },

    "{toolbar} mouseenter": function(toolbar) {

        var block = blocks.block.of(toolbar);
        block.addClass("show-block-hint");
    },

    "{toolbar} mouseleave": function(toolbar, event) {

        var block = blocks.block.of(toolbar);
        block.removeClass("show-block-hint");
    },

    "{removeButton} click": function(removeButton, event) {
        var currentBlock = blocks.getCurrentBlock();

        // TODO: Display confirmation?

        // Remove the block from the composer
        blocks.removeBlock(currentBlock);

        // Remove the is-editing-block state
        composer.manager().removeClass('is-editing-block');
    }

}});

module.resolve();

});


EasyBlog.module("composer/blogimage", function($){

var module = this;

EasyBlog.require()
.library(
    "image",
    "plupload2",
    "ui/droppable"
)
.done(function() {

EasyBlog.Controller("Composer.Blogimage", {
    elements: [
        "[data-eb-composer-blogimage-{browse-button|remove-button|image|workarea}]",
        ".eb-composer-{field}-blogimage"
    ],

    defaultOptions: {
        "{browseButton}": "[data-eb-composer-blogimage-placeholder] [data-eb-mm-browse-button]",
        "{placeholder}": "[data-eb-composer-blogimage-placeholder]",
        "{data}": "[data-eb-composer-blogimage-value]",
        "{progress}": "[data-eb-mm-upload-progress]",
        "{addCoverButton}": ".eb-document-add-cover-button"
    }
}, function(self, opts, base) { return {

    defaultUri: null,

    init: function() {

        // Get the placeholder so that we can register this with mediamanager
        var placeholder = self.placeholder();

        if (placeholder.length > 0) {
            EasyBlog.MediaManager.uploader.register(placeholder);
        }

        // Get the current image url
        var uri = self.data().val();

        // Store the current image uri in case we need to add "Revert" functionality in the future
        self.defaultUri = uri;

        // Implement droppable
        self.workarea()
            .droppable({
                accept: ".eb-mm-file",
                tolerance: "pointer"
            });
    },

    "{browseButton} mediaSelect": function(browseButton, event, media) {

        // Set the data
        self.data().val(media.meta.uri);

        // Set the image
        self.setImage(media.meta.url);
    },

    "{placeholder} mediaUploaderFilesAdded": function(placeholder, event, uploader, files) {
        EasyBlog.MediaManager.uploader.addItem(files[0], placeholder);
    },

    "{placeholder} mediaUploaderFileUploaded": function(placeholder, event, uploader, file, data) {

        var response = data.response;
        var mediaItem = response.media;
        var mediaMeta = mediaItem.meta;

        // Set the data with the appropriate json string
        self.data().val(mediaMeta.uri);

        // Set the image now
        self.setImage(mediaMeta.url);
    },

    "{placeholder} mediaUploaderFileError": function(placeholder, event, uploader, error) {
    },

    "{placeholder} mediaUploaderError": function(placeholder, event, uploader, error) {
    },

    setImage: function(url) {

        self.image()
            .css("backgroundImage", $.cssUrl(url));

        setTimeout(function(){
            self.placeholder()
                .addClass("has-image has-art state-done");

            self.addCoverButton().addClass("has-cover");
        }, 250);
    },

    show: function() {
        self.composer.document.artboard.show("cover");
    },

    hide: function() {
        self.composer.document.artboard.hide("cover");
    },

    toggle: function(show) {
    },

    "{removeButton} click": function() {

        self.placeholder()
            .removeClass("has-image is-uploading has-art");

        self.addCoverButton().removeClass("has-cover");

        setTimeout(function(){

            self.image()
                .css("backgroundImage", "");

            // Remove the data
            self.data().val('');
        }, 750);
    }

}});

module.resolve();

});

});

EasyBlog.module("composer/category", function($){

    var module = this;

    EasyBlog.Controller("Composer.Category", {
        defaultOptions: {

            // view: {
            //     itemGroup: "site/composer/form/category/itemGroup",
            //     itemParent: "site/composer/form/category/itemParent",
            //     item: "site/composer/form/category/item"
            // },

            // Templates
            "{itemGroupTemplate}": "[data-category-item-group-template]",
            "{itemTemplate}": "[data-category-item-template]",

            "{viewport}"  : "[data-eb-composer-category-viewport]",
            "{tree}"      : "[data-eb-composer-category-tree]",
            "{itemGroup}" : "[data-eb-composer-category-item-group]",
            "{itemGroupHeader}" : "[data-eb-composer-category-item-group-header]",
            "{item}"      : "[data-eb-composer-category-item]",
            "{itemCount}": "[data-eb-composer-category-item-count]",
            "{itemCheckbox}": "[data-eb-composer-category-item-checkbox]",

            "{count}": "[data-eb-composer-category-count]",

            "{searchTextfield}": "[data-eb-composer-category-search-textfield]",

            "{jsondata}": "[data-eb-composer-category-jsondata]"
        }
    },
    function(self, opts, base, composer) { return {

        init: function() {

            composer = self.composer;

            // Initialize the templates first
            self.initializeTemplates();

            self.viewport()
                .on("mousewheel", function(event){
                    event.stopPropagation();
                });

            var i = 0;
            var categories = JSON.parse(self.jsondata().val());


            // Build the categories
            self.build(categories);

            while (category = categories[i++]) {
                category.selected && self.selectCategory(category.id);
            }

            self.go(0);

            // Remove the textarea
            self.jsondata().remove();

            window.catz = self;
        },

        initializeTemplates: function() {
            // Detach the templates first
            opts.templates = {};

            opts.templates.itemGroup = self.itemGroupTemplate().detach().html();
            opts.templates.item = self.itemTemplate().detach().html();
        },

        // This is the categoryById object map,
        // used for fast category retrieval.
        categories: {

            // Root
            "0": {
                id: 0,
                title: "",
                parent_id: null
            },

            // Pseudo-parent. This reduces extra logic when searching.
            "search": {
                id: "search",
                title: "",
                parent_id: null
            }
        },

        // This is the childCategoriesByParentId object map,
        // used for fast child categories retrieval.
        children: {
            "search": [] // Pseudo-parent. This reduces extra logic when searching.
        },

        // This is the categoryByKey object map,
        // used for fast category searching.
        keys: {},

        // This is used to speed up displaying of search results.
        clones: {},

        // Use $.has(selected, id) to quickly determine if an id is selected.
        // Use $.values(selected) to retrieve array of selected ids.
        selected: {},

        build: function(data) {

            var i = 0,
                category;

            while (category = data[i++]) {

                var id = parseInt(category.id),
                    parentId = parseInt(category.parent_id);

                // Store in categories object
                self.categories[id] = category;

                // Store category as a children of a parent
                (self.children[parentId] || (self.children[parentId]=[])).push(category);

                // Store category by key.
                var key = category.key = category.title.toLowerCase() + " [#" + id + "]";
                self.keys[key] = category;
            }
        },

        childrens: $.memoize(function(id, deep) {

            var children = self.children[id];

            if (!children) return [];

            // If recursive children retrieval is called
            // before immediate children retrieval on the
            // same id, doing this will let us cache the value
            // of immediate children retrieval so we won't
            // need to go through this operation again.
            var ids = deep ? [].concat(self.childrens(id)) : $.flatten(children, 'id'),
                length = ids.length,
                i;

            if (deep) {
                for (i=0; i<length; i++) {
                    ids = ids.concat(self.childrens(ids[i], deep));
                }
            }

            return ids;

        }, function(id, deep) {

            return id + (deep ? "_deep" : "");
        }),

        parents: $.memoize(function(id) {

            var category = self.categories[id];

            if (!category) return;

            if (id==0) return [0];

            var parents = [];

            while (category = self.categories[category.parent_id]) {
                parents.unshift(category.id);
            }

            return parents;
        }),

        itemGroup: {

            get: $.memoize(function(id) {

                // Get children of this category
                var children = self.children[id];

                // If this category does not
                // contain any children, stop.
                if (!children) return;

                // Get category and create item group
                var category = self.categories[id];
                var itemGroup = self.getItemGroupTemplate(category);

                // Collect child item in an array
                // then only append it onto itemGroup
                // for performance reasons.
                var child, items = [], i = 0;

                while (child = children[i++]) {
                    items.push(self.item.get(child.id));
                }

                $(itemGroup)
                    .locate("viewport")
                    .append(items);

                return itemGroup;
            })
        },

        item: {

            get: $.memoize(function(id) {

                // Get category
                var category = self.categories[id];

                // If category does not exist, stop.
                if (!category) return;

                // Get children
                var children = self.children[id];

                var item = self.getItemTemplate(category, children || null);

                return item;
            })
        },

        getItemGroupTemplate: function(category) {
            var template = $(opts.templates.itemGroup);

            // Set the options here.
            template.attr('data-id', category.id);
            template.find('[data-title]').html(category.title);

            return template[0];
        },

        getItemTemplate: function(category, children) {
            var template = $(opts.templates.item);

            template.attr('data-id', category.id);
            template.find('[data-title]').html(category.title);

            if (children) {
                template.addClass('has-children');
            }

            return template[0];
        },

        itemCount: {

            get: $.memoize(function(id) {
                return $(self.item.get(id)).find(self.itemCount.selector)[0];
            }),

            set: function(id, count) {
                $(self.itemCount.get(id)).find("span").html(count);
            }
        },

        populate: function(parentId) {

            var children = self.children[parentId];

            if (!children) return;

            // Remove active class on header
            $(self.itemGroup.get(parentId))
                .locate("header")
                .removeClass("active");


            // Process child items
            var i = 0,
                category,
                selected = self.selected,
                selectedIds = $.values(selected);

            while (category = children[i++]) {

                var id = category.id,
                    allChildIds = self.childrens(id, true),
                    selectedChildIds = $.intersection(selectedIds, allChildIds),
                    immediateChildIds = self.childrens(id),
                    selectedImmediateChildIds = $.intersection(selectedChildIds, immediateChildIds);

                // Add/remove selected class from item
                $(self.item.get(id))
                    .toggleClass("selected", $.has(selected, id))
                    .toggleClass("has-selected-children", selectedChildIds.length > 0)
                    .toggleClass("is-primary", category.isprimary)
                    .removeClass("active");

                // Update count
                if (immediateChildIds.length > 0) {
                    var count = selectedImmediateChildIds.length + '/' + immediateChildIds.length;
                    self.itemCount.set(id, count);
                }
            }
        },

        query: $.memoize(function(keyword) {

            var keys = self.keys,
                results = {},
                re = new RegExp("^" + $.regExpEscape(keyword), 'i');

            for (key in keys) {
                if (key.match(re)) {
                    var category = keys[key],
                        parentId = category.parent_id;
                    (results[parentId] || (results[parentId] = [])).push(category);
                }
            }

            return results;
        }),

        search: function(keyword) {

            var results = self.query(keyword),
                clones = self.clones,
                itemGroups = [],
                items = [];

            for (parentId in results) {

                // Get item group
                var category = self.categories[parentId];
                var itemGroup = self.getItemGroupTemplate(category);

                // Get items
                var children = results[parentId],
                    child, items = [], i = 0;

                while (child = children[i++]) {

                    var id = child.id,
                        clone = clones[id] || (clones[id]=$(self.item.get(id)).clone()[0]);

                    items.push(clone);
                }

                // Add items to item group
                $(itemGroup)
                    .locate("viewport")
                    .append(items);

                // Add itemGroup to itemGroups.
                // Always add root itemGroup in the beginning of the aray.
                itemGroups[parentId==0 ? "unshift" : "push"](itemGroup);
            }

            self.itemGroup().detach();

            $(self.itemGroup.get("search"))
                .empty()
                .append(itemGroups)
                .appendTo(
                    self.tree()
                        .switchClass("level-0")
                        .data("level", 0)
                );
        },

        currentTree: [],

        go: function(id) {

            var category = self.categories[id];

            if (!category) return;

            // Get parents
            var tree = self.tree(),
                parents = self.parents(id),
                level = parents.length - 1,

                // Get item groups
                itemGroups =
                    $.map(parents, function(id) {
                        return self.itemGroup.get(id);
                    });

            // Maintain seamless transition
            // when moving backwards by
            // retaining the curren item group.
            var lastLevel = tree.data("level") || 0;
            if (lastLevel > level) {
                itemGroups.push(
                    self.itemGroup().eq(lastLevel)
                );
            }

            // Detach all item groups on the screen
            self.itemGroup().detach();

            // Populate count and selected items
            // on items of the current item group.
            self.populate($.last(parents));

            // Append new item groups
            tree.append(itemGroups)
                .switchClass("level-" + level)
                .data("level", level);

            // Clear search text field
            self.searchTextfield().val("");
        },

        open: function(id) {

            var children = self.children[id];

            // If this category has child items
            if (children) {

                // Add active class
                $(self.item.get(id)).addClass("active");

                // Open it
                self.go(children[0].id);
            }
        },

        selectCategory: function(id) {
            self.toggle(id, true);
        },

        deselect: function(id) {
            self.toggle(id, false);
        },

        toggle: function(id, toggle) {

            var item = $(self.item.get(id)),
                clone = $(self.clones[id]),
                selected = self.selected;

            // Decide to select or deselect if no value was passed in
            toggle ===undefined && (toggle = !item.hasClass("selected"));

            // If select, add to selected map.
            // If deselect, remove from selected map.
            toggle ? (selected[id] = parseInt(id)) : (delete selected[id]);

            // Toggle selected class on item
            var addOrRemoveClass = (toggle ? "add" : "remove") + "Class";
            item[addOrRemoveClass]("selected");
            clone[addOrRemoveClass]("selected");

            if ($.keys(self.selected).length == 0) {
                var addOrRemoveClass = (toggle ? "remove" : "add") + "Class";
                item[addOrRemoveClass]("selected");
                clone[addOrRemoveClass]("selected");

                EasyBlog.dialog(
                    {
                        content :'You must have at least one category assigned to this blog post.',

                    });

                // reverse the toggling process
                toggle ? (delete selected[id]) : (selected[id] = parseInt(id));

                return;
            }
            // Trigger categoryselect/categorydeselect event
            self.trigger("category" + (toggle ? "select" : "deselect"), [self.categories[id]]);

            // Update count
            self.count().html($.keys(self.selected).length);
        },

        enableKeyboardNavigation: function() {


            $(document)
                .off("keydown.composer.category")
                .on("keydown.composer.category", function(event){

                    var keyCode = event.keyCode,
                        direction = {38: "up", 40: "down", 37: "left", 39: "right"};

                    switch (keyCode) {
                        case 38:
                        case 40:
                        case 37:
                        case 39:
                            self.navigate(direction[keyCode]);
                            break;
                        // space
                        case 32:
                            break;
                    }
                });
        },

        disableKeyboardNavigation: function() {

            $(document).off("keydown.composer.category");
        },

        navigate: function(direction) {

            console.log(direction);

            var item = self.currentClickedItem;

            switch (direction) {

                case "up":
                    item.prev().click();
                    break;

                case "down":
                    item.next().click();
                    break;

                case "left":
                    self.go(item.data("id"));
                    break;

                case "right":
                    break;
            }
        },

        toArray: function() {

            if (!self.selected) return [];

            var categories = [];

            for (id in self.selected) {
                var category = self.categories[id];
                categories.push(category);
            }

            return categories;
        },

        lastClickedItem: $(),

        currentClickedItem: $(),

        "{item} click": function(item) {

            self.lastClickedItem = self.currentClickedItem;
            self.currentClickedItem = item;
        },

        "{self} composerSave": function(el, event, save)
        {
            save.data.categories = $.values(self.selected);
        },

        "{self} categorysetprimary": function(el, event, category)
        {
            // let unset all the is-primary class from the selections.
            self.item().removeClass('is-primary');

            $(self.item.get(category.id))
                .addClass('is-primary');
        },

        "{itemGroup} click": function(itemGroup, event) {

            var items = itemGroup.find(self.item.selector),
                lastItem = self.lastClickedItem,
                currentItem = self.currentClickedItem;

            if (!(event.metaKey || event.ctrlKey)) {
                items.removeClass("active");
            }

            // Toggle active class on element
            currentItem.toggleClass("active");

            // If user is holding shift, perform a range select.
            // Mac users: CMD+LSHIFT or RSHIFT
            // TODO: This has bugs. Refine when have time.
            if (event.shiftKey && lastItem.length > 0) {

                var last    = items.index(lastItem),
                    current = items.index(currentItem);

                // Retrieve range of items
                items.slice
                    .apply(items, last < current ? [last, current] : [current, last])
                    .addClass("active");
            }

            // If there are active items, enable keyboard navigation
            if (items.filter(".active").length > 0) {
                self.enableKeyboardNavigation();
            } else {
                self.disableKeyboardNavigation();
            }
        },

        "{item} dblclick": function(item) {

            var id = item.data("id");
            self.open(id);
        },

        "{itemCount} click": function(itemCount) {

            var id = self.item.of(itemCount).data("id");
            self.open(id);
        },

        "{itemCheckbox} click": function(itemCheckbox, event) {

            var id = self.item.of(itemCheckbox).data("id");
            self.toggle(id);

            // Prevent item traversal
            event.stopPropagation();
        },

        "{itemGroupHeader} click": function(itemGroupHeader) {

            var id =
                self.itemGroup
                    .of(itemGroupHeader)
                    .data("id");

            itemGroupHeader.addClass("active");

            self.go(id);
        },

        "{searchTextfield} keyup": $.debounce(function(textfield) {

            var keyword = $.trim(textfield.val());

            if (keyword=="") {
                self.go(0);
            } else {
                self.search(keyword);
            }

        }, 250),

        "{self} composerValidate": function(composer, event, validator) {
        }


    }});

    module.resolve();

});

EasyBlog.module("composer/datetime", function($) {

var module = this;

EasyBlog.require()
.library(
    "moment",
    "datetimepicker"
)
.done(function(){

EasyBlog.Controller("Post.Datetime", {
    defaultOptions: {
        format: "Do MMM, YYYY HH:mm",
        language: "en",
        originalValue: "",

        "{preview}": "[data-preview]",
        "{calendar}": "[data-calendar]",
        "{cancel}": "[data-cancel]",
        "{datetime}": "[data-datetime]"
    }
}, function(self, opts, base) {

    return {
        init: function() {

            // For the language to load, we also need to load the language js file which is done by the implementer.
            self.calendar()._datetimepicker({
                component: "eb",
                format: opts.format,
                language: opts.language
            });

            self.datetimepicker = self.calendar().data("DateTimePicker");

            // Get the original value from input
            opts.originalValue = self.datetime().val();

            if (!$.isEmpty(opts.originalValue)) {
                self.datetimepicker.setDate($.moment(opts.originalValue));
            }
        },

        "{calendar} dp.change": function(el, ev) {

            // Preview needs to be in their language respectively
            self.preview().text(ev.date.format(opts.format));

            // Set the language to english so we can get an english version of the date
            ev.date.lang('en');

            var val = ev.date.format("YYYY-MM-DD HH:mm:ss");


            // Set the datetime as SQL format
            self.datetime().val(val);

            // Reset back the languge
            ev.date.lang(opts.language);

            var val = ev.date.format("YYYY-MM-DD HH:mm:ss");

            self.toggleCancelButton();
        },

        "{cancel} click": function() {
            var empty = $.isEmpty(opts.originalValue);

            if (empty || opts.originalValue == "0000-00-00 00:00:00") {
                self.preview().text(opts.emptyText);
                self.datetime().val("0000-00-00 00:00:00");
            } else {
                self.datetimepicker.setDate($.moment(opts.originalValue));
            }

            self.toggleCancelButton();
        },

        toggleCancelButton: function() {
            self.cancel()[self.datetime().val() == opts.originalValue ? "hide" : "show"]();
        }
    }
});

module.resolve();

});

});
EasyBlog.module("composer/debugger", function($){

var module = this;

EasyBlog.Controller("Composer.Debugger",
{
    defaultOptions: {

        "{nestableBlock}": EBD.nestableBlock
    }
},
function(self, opts, base, composer, console) { return {

    id: 1,

    init: function() {

        // Globals
        composer = self.composer;
        console = self.console;

        // Simulate console methods but with the ability
        // to mute them when debugger is not turned on.
        for (method in window.console) {
            (function(method){
                self.console[method] = function() {
                    self.active && window.console[method].apply(window.console, arguments);
                }
            })(method);
        }
return;
        var composerEvents = [

            "composerReady",
            "composerDocumentReady",
            "composerTitleChange",
            "composerValidate",
            "composerSave",
            "composerSaveSuccess",
            "composerSaveError",
            "composerSaveTemplate",

            // Workarea
            "composerArtboardShow",
            "composerArtboardHide",

            // Document
            "composerDocumentRefresh",
            "composerDocumentScroll",
            "composerDocumentBlur",

            // Block
            // "composerBlockHoverIn",
            // "composerBlockHoverOut",
            "composerBlockBeforeDrag",
            "composerBlockDrag",
            "composerBlockBeforeDrop",
            "composerBlockDrop",
            "composerBlockBeforeRelease",
            "composerBlockRelease",
            "composerBlockBeforeAdd",
            "composerBlockAdd",
            "composerBlockInit",
            "composerBlockCreate",
            "composerBlockConstruct",
            "composerBlockBeforeActivate",
            "composerBlockActivate",
            "composerBlockActivateError",
            "composerBlockDeactivate",
            "composerBlockChange",
            "composerBlockExport",
            "composerBlockRemove",
            "composerBlockMechanicsChange",
            "composerBlockNestIn",
            "composerBlockNestOut",
            "composerBlockNestChange",
            "composerBlockResizeStart",
            "composerBlockBeforeResize",
            "composerBlockResize",
            "composerBlockResizeStop",

            // Text
            "composerTextSelect",
            "composerTextDeselect",

            // Panel
            "composerPanelActivate",
            "composerPanelDeactivate",

            // Debugger
            "composerDebugActivate",
            "composerDebugDeactivate"
        ];

        var sortEvents = [
            // "sort",
            "sortstart",
            "sortchange",
            "sortactivate",
            "sortdeactivate",
            "sortout",
            "sortover",
            "sortupdate",
            "sortreceive",
            "sortremove",
            "sortstop"
        ];

        var dragEvents = [
            // "drag"
            "dragcreate",
            "dragstart",
            "dragstop"
        ];

        var dropEvents = [
            "drop",
            "dropactivate",
            "dropcreate",
            "dropdeactivate",
            "dropout",
            "dropover"
        ];

        var resizeEvents = [
            "resize",
            "resizecreate",
            "resizestart",
            "resizestop"
        ];

        // Composer events
        $.each(composerEvents, function(i, composerEvent) {
            base.on(composerEvent, function(){
                self.console.log(composerEvent, arguments);
            });
        });

        // Root sort events
        $.each(sortEvents, function(i, sortEvent) {
            base.on(sortEvent, EBD.root, function() {
                self.console.log("root/" + sortEvent, arguments);
            });
        });

        // Nest sort events
        $.each(sortEvents, function(i, sortEvent) {
            base.on(sortEvent, EBD.root, function() {
                self.console.log("nest/" + sortEvent, arguments);
            });
        });

        // Drag events
        $.each(dragEvents, function(i, dragEvent) {
            base.on(dragEvent, EBD.block, function(){
                self.console.log(dragEvent, arguments);
            });
        });

        // Drop events
        $.each(dropEvents, function(i, dropEvent) {
            base.on(dropEvent, EBD.dropzone, function(){
                self.console.log(dropEvent, arguments);
            });
        });

        // Resize events
        $.each(resizeEvents, function(i, resizeEvent) {
            base.on(resizeEvent, function(){
                self.console.log(resizeEvent, arguments);
            });
        });
    },

    console: {},

    active: false,

    activate: function() {

        self.active = true;

        // composer.frame().addClass("is-debugging");

        self.trigger("composerDebugActivate");
    },

    deactivate: function() {

        self.active = false;

        // composer.frame().removeClass("is-debugging");

        self.trigger("composerDebugDeactivate");
    },

    toggle: function() {

        self.active ? self.activate() : self.deactivate();
    }

}});

module.resolve();

});

EasyBlog.module("composer/document/overlay", function($){

var module = this;

EasyBlog.Controller("Composer.Document.Overlay",
{
    defaultOptions: $.extend({

        "{overlay}": "[data-ebd-overlay]",
        "{placeholder}": "[data-ebd-overlay-placeholder]",

        // Applies to document root and nest
        "{sortable}": ".ui-sortable",

        "{placeholderInsideSortHelper}": ".ebd-block.is-helper .ebd-overlay-placeholder"
    }, EBD.selectors)
},
function(self, opts, base, blocks) { return {

    id: 1,

    init: function() {

        composer = self.document.composer;
        blocks = composer.blocks;

        // Document Overlay class
        self.DocumentOverlay =  function(block) {

            this.id      = self.id++;
            this.uid     = block.data("uid");
            this.type    = block.data("type");

            this._element = $("<div>", {
                "class"    : "ebd-overlay",
                "data-id"  : this.id,
                "data-type": this.type,
                "data-ebd-overlay": ""
            })[0];

            this._placeholder = $("<div>", {
                "class"  : "ebd-overlay-placeholder",
                "data-id": this.id,
                "data-ebd-overlay-placeholder": ""
            })[0];
        }

        $.extend(self.DocumentOverlay.prototype, {

            block: function() {
                return blocks.getBlock(this.uid);
            },

            element: function() {
                return $(this._element);
            },

            placeholder: function() {
                return $(this._placeholder);
            },

            attach: function() {

                this.element()
                    .appendTo(self.document.workarea());

                // Position overlay
                this.reposition()
            },

            refresh: function() {

                // Do not refresh blocks that are being released
                if (this.element().hasClass("is-animating")) return;

                this.reposition();
            },

            reposition: function() {

                var element = this.element(),

                    placeholder =
                        // If block is currently being sorted,
                        // get placeholder inside sort helper.
                        element.hasClass("is-sorting") ?
                            // Sometimes the placholder from sort helper doesn't exist
                            // so we fallback to the overlay's original placeholder.
                            $(self.placeholderInsideSortHelper().where("id", this.id)[0] || this._placeholder) :
                            this.placeholder();

                // Update overlay size & position
                element
                    .css({
                        width:  placeholder.width(),
                        height: placeholder.height()
                    })
                    .position({
                        my: "left top",
                        at: "left top",
                        of: placeholder
                    });
            },

            emerge: function() {

                // Bring overlay in front of the document root
                this.element()
                    .addClass("hover");

                // Simulate hover behaviour on block
                this.block()
                    .addClass("hover");
            },

            submerge: function() {

                // Push overlay behind the document root
                this.element()
                    .removeClass("hover");

                // Remove simulated hover behaviour on block
                this.block()
                    .removeClass("hover");
            },

            remove: function() {

                self.remove(this.id);
            }
        });

        var refreshTwice = $.throttle(function(){
                self.refresh();
            }, 25, {leading: true, trailing: true})

        // Refresh overlay when user provides feedback
        var userEvents = $.ns("keydown keypress keyup input mousedown click mousemove mouseup touchstart touchmove touchend", ".overlay"),
            sortEvents = $.ns("sortactivate sortchange sortover sortout sortdeactivate sortstop", ".overlay");

        composer.document.root()
            .on(userEvents, refreshTwice);

        composer.views()
            .on("scrolly.overlay", refreshTwice);

        self.element
            .on(sortEvents, self.sortable.selector, refreshTwice);
    },

    blocks: {},

    instances: [],

    keys: {},

    get: function(id) {

        return self.keys[id];
    },

    getInstancesByBlock: function(block) {

        var uid = blocks.getBlockUid(block);

        return self.blocks[uid] || [];
    },

    create: function(block) {

        var instance = new self.DocumentOverlay(block);

        // Add to instances
        self.instances.push(instance);

        // Add to keys
        self.keys[instance.id] = instance;

        // Add to block-overlay map
        var blocks = self.blocks;
        (blocks[instance.uid] || (blocks[instance.uid] = [])).push(instance);

        return instance;
    },

    remove: function(id) {

        var instance = self.get(id);

        // Remove element & placeholder
        instance.element().remove();
        instance.placeholder().remove();

        // Remove from block-overlay map
        $.pull(self.blocks[id], instance);

        // Remove from keys
        delete self.keys[id];

        // Remove from instances
        $.pull(self.instances, instance);
    },

    of: function(block) {

        return self.blocks[block.data("uid")] || [];
    },

    refresh: function(block) {

        // Refresh instance
        var instances = block ? self.of(block) : self.instances;

        $.each(instances, function(i, instance){
            instance.refresh();
        });
    },

    // Add .is-sorting class to instance overlay
    // This will shrink the appearance of the overlay
    // just like a regular block item would when it
    // appears as a placeholder.
    "{self} composerBlockDrag": function(base, event, block) {

        // // If this is not a block, stop.
        // if (!block.is(EBD.block)) {
        //     return;
        // }
        setTimeout(function() {
            self.refresh();
        },1);

        // This is alternatively composerBlockDrag
        $.each(self.of(block), function(i, instance){
            instance.element()
                .addClass("is-sorting");
        });
    },

    "{dropzone} dropover": function(dropzone, event, ui) {
        setTimeout(function() {
            self.refresh();
        }, 1);
    },

    "{block} drag": function(block, event, ui) {

        // This is alternatively composerBlockDragging
        // Refresh overlay for this block
        self.refresh(block);
    },

    drop: function(block) {

        $.each(self.of(block), function(i, instance){
            instance.element()
                .removeClass("is-sorting")
                .addClass("is-dropping");
        });

        self.refresh();
    },

    // When a block is dropped, it is not yet visible on the screen.
    // This is the time to make block overlay (if any) invisible
    // and reposition it to the actual placeholder.
    "{self} composerBlockDrop": function(base, event, block) {
        self.drop(block);
    },

    // If this is a new block, reposition overlay after block
    // has been reconstructed, because overlay may not exist
    // during composerBlockDrop.
    "{self} composerBlockAdd": function(base, event, block) {
        self.drop(block);
    },

    // Once we have repositioned the overlay on top of the block,
    // we shrink the overlay just like how blocks are shrinked
    // before it is being released.
    "{self} composerBlockBeforeRelease": function(base, event, block) {

        $.each(self.of(block), function(i, instance){

            instance.element()
                .removeClass("is-dropping")
                .addClass("is-releasing is-animating")
                .removeClassAfter("is-releasing");
        });

        self.refresh();

        // Because we can't quite tell when reflow happens after block
        // is dropped, this is required to update position of overlays.
        setTimeout(function(){
            self.refresh();
        }, 50);
    },

    // Once a block is released, disable transition on overlay.
    "{self} composerBlockRelease": function(base, event, block) {

        $.each(self.of(block), function(i, instance){
            instance.element()
                .removeClass("is-animating");
        });

        self.refresh();
    },

    "{self} composerDocumentRefresh": function() {

        // Repositon all overlay
        self.refresh();
    },

    "{self} composerBlockRemove": function(base, event, block) {

        var instances = self.getInstancesByBlock(block);

        $.each(instances, function(i, instance){
            instance.remove();
        });
    },

    "{placeholder} click": function(placeholder) {

        var id = placeholder.data("id"),
            instance = self.get(id);

        instance.emerge();
    },

    "{placeholder} mouseover": function(placeholder) {

        var id = placeholder.data("id"),
            instance = self.get(id);

        // If this block is currently active, emerge overlay.
        if (instance.block().hasClass("active")) {
            instance.emerge();
        }
    },

    "{overlay} mouseout": function(overlay) {

        var id = overlay.data("id"),
            instance = self.get(id);

        instance.submerge();
    }

}});

module.resolve();

});

EasyBlog.module("composer/document", function($){

var module = this;

EasyBlog.require()
.script(
    "composer/document/toolbar",
    "composer/document/artboard"
)
.done(function(){

EasyBlog.Controller("Composer.Document",
{
    hostname: "document",

    pluginExtendsInstance: true,

    elements: [
        "[data-eb-composer-document] .eb-composer-{viewport|viewport-content}",
        "[data-eb-composer-{page|page-viewport|page-header|page-body}]",
        "[data-ebd-{workarea|textarea}]",
        "[data-ebd-workarea-{ghosts}]"
    ],

    defaultOptions: {

        "{root}": "[data-ebd-workarea] " + EBD.root,
        "{block}": EBD.block,
        "{nest}": EBD.nest,

        "{titleField}": "[data-eb-composer-form=page] [name=title]"
    }
},
function(self, opts, base, editor, blocks) { return {

    init: function() {

        composer = self.composer;

        self.initPlugins();
        self.initTitlebar();

        // Document
        if (self.isLegacy()) {
            self.initLegacyDocument();
        } else {
            self.initEasyBlogDocument();
        }
    },

    initPlugins: function() {

        self.addPlugin("toolbar");
        self.addPlugin("artboard");
    },

    initTitlebar: function() {

        // Title bar
        EasyBlog.require()
            .library(
                "expanding"
            )
            .done(function(){
                // When title field gets focused for the first time,
                // implement expanding textarea.
                var titleField =
                    self.titleField()
                        .one("focus", function(){
                            titleField.expandingTextarea();
                        });
            });
    },

    initLegacyDocument: function() {

        // TinyMCE
        if (window.tinyMCE) {

            var setupTinyMCE = function() {

                // Wait until tinyMCE editor is ready
                if (tinyMCE.activeEditor) {

                    var editorContainer = tinyMCE.activeEditor.editorContainer;

                    // In Joomla 2.5, editorContain is a string containing the id
                    // to the tinyMCE container. It already has 100% width, so
                    // there's nothing more to do.
                    if (!$.isString(editorContainer)) {

                        // Ensure tinyMCE has 100% width because this value
                        // could not be set via $editor->display().
                        editorContainer.style.width = "100%";

                        // This ensure the entire tinyMCE body is focusable.
                        $(tinyMCE.activeEditor.contentDocument)
                            .find("html")
                                .css({
                                    height: "100%"
                                })
                                .end()
                            .find("body")
                                .css({
                                    height: "100%",
                                    margin: "1em"
                                });
                    }

                    self.setLayout();

                } else {
                    setTimeout(setupTinyMCE, 500);
                }
            }

            setupTinyMCE();
        }

        self.trigger("composerDocumentReady");
    },

    initEasyBlogDocument: function() {

        EasyBlog.require()
            .library(
                "selectionchange"
            )
            .script(
                "composer/redactor10",
                "composer/document/overlay"
            )
            .done(function(){

                // Initialize redactor
                editor = self.composer.editor =
                    self.workarea()
                        .composer({
                            replaceDivs: false,
                            toolbar: false
                        })
                        .data("redactor");

                // Expose blocks
                blocks = self.composer.blocks;

                // Initialize document.
                self.initDocument();

                // Set document layout.
                self.setLayout();

                // Install document plugins
                self.addPlugin("overlay");

                self.initSelectionChange();

                self.trigger("composerDocumentReady");
            });
    },

    isLegacy: function() {

        return composer.getDoctype()=="legacy";
    },

    initDocument: function() {

        self.trigger("composerDocumentInit");

        self.workarea()
            .removeClass("is-loading");
    },

    loadDocument: function(html) {
        self.trigger("composerDocumentLoad");

        self.root().html(html);
        self.initDocument();
    },

    "{window} resize": $.throttle(function() {

        self.setLayout();

    }, 250),

    setLayout: function() {

        self.updateEditorHeight();

        // Trigger composerDocumentRefresh
        // Overlay plugin listens to this event to to reposition overlays.
        self.trigger("composerDocumentRefresh");
    },

    updateEditorHeight: function() {

        // Set page height to auto
        self.page()
            .css("height", "auto");

        var viewportHeight = self.viewport().height();
        var pageViewportHeight = self.pageViewport().height();
        var pageHeaderHeight = self.pageHeader().height();
        var pageBodyHeight = pageViewportHeight - pageHeaderHeight;
        var pageViewportVerticalPadding = 48 + 60;
        var pageVerticalPadding = 30 * 2;
        var toolbarHeight = 50;

        var pageBodyMinHeight =
                viewportHeight -
                toolbarHeight -
                pageVerticalPadding -
                pageViewportVerticalPadding -
                pageHeaderHeight;

        if (window.tinyMCE) {

            if (!tinyMCE.activeEditor) return;

            // Get editor container
            var editorContainer = tinyMCE.activeEditor.editorContainer;

            // Joomla 2.5
            if ($.isString(editorContainer)) {

                var pageBody = self.pageBody();

                // Get editor container
                var editorContainer = $("#" + editorContainer);

                // Remove enforced height on content table
                editorContainer.find("#content_tbl").css("height", "auto");

                // Set iframe height
                var editorIframe = $(tinyMCE.activeEditor.contentAreaContainer).find("iframe");
                var editorHeightWithoutIframe = pageBody.height() - editorIframe.height();
                var editorIframeHeight = pageBodyHeight - editorHeightWithoutIframe;
                editorIframe.css("height", editorIframeHeight);

                // Set textarea height
                var editorTextarea = $(tinyMCE.activeEditor.getElement());
                var editorTextareaHeight = pageBodyHeight - pageBody.find(".toggle-editor").height();
                editorTextarea.css("height", editorTextareaHeight);

            // Joomla 3.x
            } else {

                var editorContainer = $(editorContainer).parent();
                var editorIframe = $(editorContainer).find("iframe");
                var editorHeightWithoutIframe = editorContainer.height() - editorIframe.height();
                var editorIframeHeight = pageBodyHeight - editorHeightWithoutIframe;

                // Set iframe height
                editorIframe.css("height", editorIframeHeight);
            }

        } else {

            // Adding a min-height to fill up available vertical area
            // of the page viewport allow user to drag & drop on a
            // wider area of whitespace.
            self.root()
                .css({
                    minHeight: pageBodyMinHeight
                });
        }
    },

    setTitle: function(title) {
        self.titleField().val(title);
    },

    insertContent: function(html) {

        // Legacy
        if (self.isLegacy()) {
            EasyBlog.LegacyEditor.insert(html);

        // EBD
        } else {

            // If html passed in is a block, add block to document.
            var block = $(html);

            if (block.is(EBD.block)) {
                blocks.addBlock(block);

            // If html is plain html, put it inside custom html block.
            } else {
                // TODO: Not sure if it's a good idea to create a custom html block for this.
            }
        }
    },

    setContent: function(html) {

        if (self.isLegacy()) {
            return EasyBlog.LegacyEditor.setContent(html);
        } else {
            // Disabled for EBD document. Use blocks API instead.
        }
    },

    getContent: function() {

        // Legacy
        if (self.isLegacy()) {
            return EasyBlog.LegacyEditor.getContent();

        // EBD
        } else {
            var html = "";

            blocks.getRootBlocks().each(function(){
                var block = $(this);
                html += blocks.getBlockHTML(block);
            });
            return html;
        }
    },

    getText: function() {

        // Legacy
        if (self.isLegacy()) {

            return $(self.getContent()).text();

        // EBD
        } else {

            // TODO: Use blocks.getBlockText instead.
            var text = [];
            blocks.getRootBlocks().each(function() {
                var block = $(this),
                    blockType = blocks.getBlockType(block);
                    blockHandler = blocks.getBlockHandler(blockType);
                blockHandler.toText && text.push( $.trim(blockHandler.toText(block)) );
            });

            return text.join("\n");
        }
    },

    initSelectionChange: function() {

        // Enable selectionchange polyfill
        $.selectionchange.start();

        // This determines if composerTextDeselect was called before.
        var deselected;

        var eventHandler = $.debounce(function() {

            // If we're on legacy editor, stop.
            if (!editor) return;

            var selection = editor.selection.get(),
                text = selection.toString(),
                hasSelection = false;

            // If there is text selected
            if (text!='') {

                // Get parent block
                var node = selection.focusNode;
                var parentBlock = self.block.of(node);

                // No text select event on standalone block/workarea
                if (parentBlock.closest(".is-standalone").length) {
                    return;
                }

                // If parent block is activated
                if (parentBlock.hasClass("active")) {

                    // Crawl up every node up til the parent block
                    while (node != parentBlock[0]) {

                        var $node = $(node);

                        // And find out if text selection is
                        // inside an editable element.
                        if ($node.editable()) {
                            hasSelection = true;
                            break;
                        }

                        node = node.parentNode;
                    }
                }
            }

            if (hasSelection) {
                self.trigger("composerTextSelect", [selection, parentBlock, editor]);
                deselected = false;
            } else {
                !deselected && self.trigger("composerTextDeselect", [editor]);
            }

        }, 100);

        $(document).on("selectionchange", eventHandler);
    },

    saveLegacyDocument: function(save) {

        // Get save data
        var saveData = save.data;

        // Get content
        var content = $.sanitizeHTML(EasyBlog.LegacyEditor.getContent());

        // If we're on IE 8, restore double quotes on html attributes.
        if ($.IE < 9) {
            content = $.toXHTML(content);
        }

        // Set intro and content into save data.
        saveData.intro = '';
        saveData.content = content;

        // If there is a read more divider,
        // place content before it as intro,
        // place content after it as content.
        var parts = content.split('<hr id="system-readmore" />');
        if (parts.length > 1) {
            saveData.intro = parts[0];
            saveData.content = parts[1];
        }
    },

    saveEasyBlogDocument: function(save) {

        var rootBlocks = blocks.getRootBlocks();
        var blockManifests = [];
        var tasks = [];
        var master = save.add('Saving document');

        // Construct document manifest
        var documentManifest = {
            title: save.data.title,
            permalink: save.data.permalink,
            type: composer.getDoctype(),
            version: "1.0"
        };

        // When all tasks are done
        $.when.apply(null, tasks)
            .always(function(){

                // Generate block items
                rootBlocks.each(function() {

                    // Get block manifest
                    var block = $(this);
                    var blockManifest = blocks.exportBlock(block);

                    // Add to array of block manifests
                    blockManifests.push(blockManifest);
                });

                // Construct document manifest
                documentManifest.blocks = blockManifests;

                // Output to console
                // EasyBlog.debug && console.info("Document:", documentManifest);
                // EasyBlog.debug && console.log(self.toHTML(documentManifest));
                save.data["document"] = JSON.stringify(documentManifest);
            })
            .done(function(){
                master.resolve();
            })
            .fail(function(){
                master.reject($.Exception("Error saving document."));
            });
    },

    "{self} composerSave": function(el, event, save) {

        // Legacy Document
        if (composer.getDoctype() == 'legacy') {
            self.saveLegacyDocument(save);

        // EasyBlog Document
        } else {
            self.saveEasyBlogDocument(save);
        }
    },

    "{titleField} keydown": function(titleField, event) {

        // Do not allow next line on blog title
        if (event.keyCode==13) {
            event.preventDefault();
        }
    },

    "{titleField} keyup": function(titleField, event) {

        var title = titleField.val();
        self.trigger("composerTitleChange", [title]);
    },

    "{self} composerDebugActivate": function() {

        // self.workarea()
        //     .addClass("is-debugging");
    },

    "{self} composerDebugDeactivate": function() {

        // self.workarea()
        //     .removeClass("is-debugging");
    },

    "{viewport} click": function(viewport, event) {

        // Skip when on legacy document
        if (self.isLegacy()) return;

        // Skip when resizing block
        var workarea = self.workarea();

        if (workarea.hasClass("is-resizing")) {
            return;
        }

        var blocks = $(event.target).parentsUntil(viewport).andSelf().filter(EBD.block);

        if (!blocks.length) {
            self.trigger("composerDocumentBlur");
        }
    },

    "{self} composerValidate": function(composer, event, validator) {
    }

}});

module.resolve();

});

});

EasyBlog.module("composer/document/toolbar", function($){

    var module = this;

    EasyBlog.Controller("Composer.Document.Toolbar", {
        elements: [
            "[data-eb-composer-{add-block-button|add-media-button|add-post-button|show-drawer-button}]",
            "[data-eb-composer-{embed-video-button}]",
            "[data-eb-composer-{mobile-blip}]"
        ],
        defaultOptions: {
        }
    }, function(self, opts, base, composer) { return {

        init: function() {
            composer = self.document.composer;
        },

        activate: function() {
            self.mobileBlip().addClass('show-menu');
        },

        deactivate: function() {
            self.mobileBlip().removeClass('show-menu');
        },

        isActive: function() {
            return self.mobileBlip().hasClass('show-menu');
        },

        "{mobileBlip} click": function(mobileBlip, event) {
            
            if (self.isActive()) {
                self.deactivate();
                return;
            }

            self.activate();
        },

        "{addBlockButton} click": function() {
            self.deactivate();

            composer.views.show("blocks");
        },

        "{addMediaButton} click": function() {
            self.deactivate();

            composer.views.show("media");
        },

        "{embedVideoButton} click": function() {
            EasyBlog.dialog({
                content: EasyBlog.ajax('site/views/composer/embedVideoDialog'),
                bindings: {
                    "{insertButton} click": function() {
                        var url = this.videoUrl().val();
                        var width = this.videoWidth().val();
                        var height = this.videoHeight().val();

                        var data = '[embed=videolink]'
                                    + '{"video":"' + url + '","width":"' + width + '","height":"' + height + '"}'
                                    + '[/embed]';

                        EasyBlog.LegacyEditor.insert(data);

                        // After inserting the video, close the dialog
                        EasyBlog.dialog().close();

                        // Reset the input
                        this.videoUrl().val('');
                    }
                }
            });
        },

        "{addPostButton} click": function() {
            self.deactivate();

            composer.views.show("posts");
        },

        "{showDrawerButton} click": function() {
           $('[data-eb-composer-frame]').toggleClass('show-drawer');
        }

    }});

    module.resolve();

});

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
EasyBlog.module("composer/location", function($){

var module = this;

EasyBlog.Controller("Composer.Location",
{
    elements: [
        "[data-eb-composer-location-{remove-button|label|textfield|places|autocomplete|form|map|form-message|detect-button}]",
        ".eb-composer-{field}-location"
    ],

    defaultOptions: {

        foursquare: {
            client_id: null,
            client_secret: null,
            v: "20140905",
            m: "foursquare",
            intent: "browse",
            radius: 800
        },

        geomode: "native", // native, external
        searchmode: "detect",

        "{container}": "[data-eb-composer-location]",
        "{place}": "[data-eb-composer-location-places] li",
        "{addLocationButton}": ".eb-document-add-location-button"
    }
},
function(self, opts, base, autocomplete, places) { return {

    init: function() {

        base = self.container();

        // Detect if user browser supports geolocation
        if (!navigator.geolocation) {
            opts.geomode = "external";
        }

        self.setupAutocomplete();
    },

    setupAutocomplete: function() {

        // Prepare autocomplete dropdown
        autocomplete = self.autocomplete().detach();
        places = autocomplete.find(self.places);
        textfield = self.textfield();

        $(document).on("click", function(event){

            var elements = $(event.target).parents().andSelf();

            if (elements.filter(textfield).length < 1) {
                autocomplete.detach();
            }
        });
    },

    searching: function(isSearching) {

        self.form().toggleClass("is-searching", isSearching);

        var textfield = self.textfield();

        textfield
            .attr("placeholder",
                isSearching ?
                    textfield.data("placeholder-searching") :
                    textfield.data("placeholder")
            );
    },

    userCoords: null,

    getUserCoords: function() {

        var task = $.Deferred();
        var userCoords = self.userCoords;

        // If we have user lat & lng, automatically resolve this.
        if (userCoords) {
            task.resolve(userCoords);
            return task;
        }

        if (opts.geomode=="native") {

            navigator.geolocation.getCurrentPosition(

                // If successful
                function(position) {
                    var coords = position.coords;

                    task.resolve({
                        latitude: coords.latitude,
                        longitude: coords.longitude
                    });
                },

                // If failed, get user coords by IP.
                function() {

                    self.getUserCoordsByIP()
                        .done(task.resolve)
                        .fail(task.reject);
                }

            );

        } else {
            task = self.getUserCoordsByIP();
        }

        return task.done(function(coords){
            self.userCoords = coords;
        });
    },

    getUserCoordsByIP: function() {

        var request =
            $.getJSON("//www.telize.com/geoip?callback=?")
                .then(function(data) {
                    return {latitude: data.latitude, longitude: data.longitude};
                });
    },

    // search: $.memoize(function(query) {

    //     console.log('search');

    //     var task =
    //         self.getUserCoords()
    //             .then(function(coords){

    //                 self.searching(true);

    //                 var request = EasyBlog.ajax("site/views/composer/getLocations", {
    //                         latitude: coords.latitude,
    //                         longitude: coords.longitude,
    //                         query: query || ""
    //                     });

    //                 return request;
    //             })
    //             .fail(function(message){

    //                 // Add error message
    //                 self.container().addClass('has-errors');
    //                 self.formMessage().html(message);

    //                 self.search.reset(query);
    //             })
    //             .always(function(){

    //                 self.searching(false);
    //             });


    //     console.log(task);

    //     return task;
    // }),

    search: function(query) {

        var task =
            self.getUserCoords()
                .then(function(coords){

                    self.searching(true);

                    var request = EasyBlog.ajax("site/views/composer/getLocations", {
                            latitude: coords.latitude,
                            longitude: coords.longitude,
                            query: query || ""
                        });

                    return request;
                })
                .fail(function(message){

                    // Add error message
                    self.container().addClass('has-errors');
                    self.formMessage().html(message);

                    self.search.reset(query);
                })
                .always(function(){

                    self.searching(false);
                });

        return task;
    },


    searchManual: $.memoize(function(query) {

        var task = $.Deferred();

        self.searching(true);

        var request = EasyBlog.ajax("site/views/composer/getLocations", {
                "query": query
            }).done(function(){
                task.resolve();
            })
            .always(function(){
                self.searching(false);
            });

        return request;
    }),

    populate: function() {

        var textfield = self.textfield();
        var query = $.trim(textfield.val());

        var mode = self.searchmode;

        if (mode == "detect") {

            self.search(query)
                .done($.debounce(function(venues) {

                    // Generate suggestions
                    var list = [];

                    $.each(venues, function(i, venue){

                        var item =
                            $.create("li")
                                .html("<strong>" + venue.name + "</strong><small>" + venue.address + "</small>")
                                .data("venue", venue)[0];

                        list.push(item);
                    });

                    // Add to places
                    places.empty().append(list);

                    // Display & reposition autocomplete
                    self.reposition();

                }, 50))
                .fail(function(msg) {

                });
        } else {

            if (query == "") {
                return;
            }

            self.searchManual(query)
                .done($.debounce(function(venues) {

                    // Generate suggestions
                    var list = [];

                    $.each(venues, function(i, venue){

                        var item =
                            $.create("li")
                                .html("<strong>" + venue.name + "</strong><small>" + venue.address + "</small>")
                                .data("venue", venue)[0];

                        list.push(item);
                    });

                    // Add to places
                    places.empty().append(list);

                    // Display & reposition autocomplete
                    self.reposition();

                }, 100))
                .fail(function(msg) {

                });
        }

    },

    _populate: $.debounce(function() {
        self.populate();
    }, 350),

    show: function() {
        self.composer.document.artboard.show("location");
    },

    hide: function() {
        self.composer.document.artboard.hide("location");
    },

    reposition: function() {

        var textfield = self.textfield();

        // Display autocomplete
        autocomplete
            .appendTo(self.composer.document.artboard.container())
            .css({
                width: textfield.outerWidth()
            })
            .position({
                my: "left top",
                at: "left bottom",
                of: textfield
            });
    },

    currentLocation: null,

    setLocation: function(venue) {

        self.currentLocation = venue;

        var field = self.field();

        // Update fields
        field.addClass("has-location");
        field.find("[name=address]").val(venue.name);
        field.find("[name=latitude]").val(venue.latitude);
        field.find("[name=longitude]").val(venue.longitude);
        self.label().html(venue.name);

        self.addLocationButton().addClass("has-location");
        base.addClass("has-location is-loading has-art");

        // Construct map url
        var map = self.map();
        var coords = venue.latitude + "," + venue.longitude;

        // Note: 1280x1280 is the largest size Google Maps offers
        var params = $.param({size: "1280x1280", sensor: true, scale: 2, zoom: 15});
        var url = "//maps.googleapis.com/maps/api/staticmap?" + params + "&center="  + coords + "&markers=" + coords;

        // When map is loaded, fade in.
        $.Image.get(url)
            .done(function(){
                map.css("backgroundImage", $.cssUrl(url));
                setTimeout(function(){
                    map.addClass("is-ready");
                }, 1);
            }).always(function(){
                base.removeClass("is-loading");
            });

    },

    removeLocation: function() {

        var map = self.map();
        map.removeClass("is-ready");
        base.removeClass("has-location has-art");
        self.addLocationButton().removeClass("has-location");

        // update field
        var field = self.field();
        field.removeClass("has-location");
        field.find("[name=address]").val('');
        field.find("[name=latitude]").val('');
        field.find("[name=longitude]").val('');
        self.label().html('');
    },

    "{window} resize": function() {

        if (autocomplete.parent().length < 1) return;

        self.reposition();
    },

    "{label} click": function(label) {
        self[!self.field().hasClass("active") ? "show" : "hide"]();
    },

    "{self} composerArtboardShow": function(el, event, id) {
        self.field().toggleClass("active", id=="location");
    },

    "{self} composerArtboardHide": function(el, event, id) {
        self.field().removeClass("active");
    },

    // "{textfield} focus": function(textfield) {
    //     // self.populate();
    // },

    "{textfield} input": function() {
        self.searchmode = "manual";

        autocomplete.detach();
        self._populate();
    },

    "{detectButton} click": function() {

        // Remove the value from the textbox.
        self.textfield().val('');

        self.searchmode = "detect";
        autocomplete.detach();
        self.populate();
    },

    "{place} click": function(place) {

        var venue = place.data("venue");

        self.setLocation(venue);
    },

    "{removeButton} click": function() {
        self.removeLocation();
    }

}});

module.resolve();

});


EasyBlog.module("composer/media", function($) {

var module = this;

EasyBlog.require()
.script("mediamanager")
.done(function(){

    EasyBlog.Controller("Composer.Media", {

        defaultOptions: {
        }
    }, function(self, opts, base, composer) { return {

        init: function() {

            composer = self.composer;

            // Load media configuration via ajax
            // TODO: Find an alternative way of passing in media configuration.
            // EasyBlog.ajax("site/views/dashboard/mediaConfiguration")
            //     .done(function(html){
            //         $("body").append(html);
            //     });
        },

        // disabled: true

        // "{composer} sidebarActivate": function(base, event, id) {

        //     if (id!=="media" || self.disabled) return;

        //     EasyBlog.mediaManager.browse();
        // },

        // "{composer} sidebarDeactivate": function(base, event, id) {

        //     if (id!=="media" || self.disabled) return;

        //     EasyBlog.mediaManager.hide();
        // }

    }});

    module.resolve();

});

});

EasyBlog.module("mediamanager", function($) {

var module = this;

var isSearching = "is-searching";
var isSearchResult = "is-search-result";
var hasSearchResults = "has-search-results";
var isNotFound = "is-notfound";

var isLoading = "is-loading";
var isFailed = "is-failed";

EasyBlog.require()
.library(
	"pageslide",
	"plupload2",
	"ui/draggable"
)
.script(
	"mediamanager/uploader"
).done(function() {

var controller = EasyBlog.Controller("MediaManager", {

	hostname: "mediaManager",

	pluginExtendsInstance: true,

	elements: [
		"[data-eb-mm-{frame|close-button}]",
		"[data-eb-mm-{pages|places|place|hints}]",
		"[data-eb-mm-{folder|filegroup|filelist}]",
		"[data-eb-mm-{folder-back-button|folder-upload-button|folder-upload-dropzone}]",

		"[data-eb-mm-{file|filegroup|filegroup-header|file-title}]",
		"[data-eb-mm-{info-viewport|info-container|info|info-back-button|info-filename}]",
		"^media [data-eb-mm-{workarea|document}]",
		"[data-eb-mm-{show-new-files-button|show-all-files-button}]",

		"[data-eb-mm-{search-panel|search-input|search-toggle-button}]",
		"[data-eb-mm-{upload-thumbnail|upload-name-upload-size}]",
		"[data-eb-mm-{folder-upload-current-file|folder-upload-stat|folder-upload-completed|folder-upload-total|folder-upload-progress-bar}]",
		"[data-eb-mm-{new-file-count}]",

		"[data-eb-mm-{filegroup-show-all-button}]",
		"[data-eb-mm-{file-remove-button|file-insert-button|file-rename-button|file-move-button}]",

		"[data-eb-mm-{folder-content-panel}]",
		"[data-eb-mm-{foldertree|tree|tree-item}]",

		"[data-eb-mm-{show-move-dialog-button|move-filename}]",
		"[data-eb-mm-{folder-rename-button|folder-move-button|folder-remove-button}]",
		"[data-eb-mm-{open-button}]",
		"[data-eb-mm-{upload-template}]",

		"[data-eb-mm-{folder-title}]",

		"[data-eb-mm-{browse-button}]",
		"[data-eb-mm-{selecting-cancel}]",
		"[data-eb-mm-{go-to-folder|close-message-button}]",

		"[data-eb-mm-{insert-gallery}]",

		// Users pagination
		"[data-eb-mm-{pagination-previous|pagination-next|pagination-current|pagination-total}]"
	],

	defaultOptions: {

		types: {
			image: ["jpg","jpeg","png","gif"],
			video: ["3gp","m4v","mp4", "flv", "wmv", "mp3", "webm", "swf"],
			audio: ["mp3","m4a","aac","ogg"]
		},

		templates: {},

       	"{thumbnail}": "[data-eb-mm-file].type-image i[data-thumbnail]",
        "{flickrButton}": "[data-flickr-login]",
        "{createFolder}": "[data-eb-mm-create-folder]"
	}
}, function(self, opts, base, composer) { return {

	init: function() {

		// Get the upload template
		opts.templates.upload = self.uploadTemplate().detach().html();

		// Remap extension to type for fast lookup
		$.each(opts.types, function(key, values){
			var i = 0, value;
			while (value = values[i++]) {
				self.types[value] = key;
			}
		});

		// Add core plugins
		self.addPlugin("uploader");

		// Keep references to these elements
		self.places.node = self.places()[0];
		self.hints.node  = self.hints().detach()[0];

		// Expose myself
		window.ebmm = self;
		EasyBlog.MediaManager = self;
	},

	"{self} composerReady": function() {

		composer = EasyBlog.Composer;
	},

	//
	// URIs
	//

	types: {"": "file"},

	getExtension: function(uri) {

		var uri = self.getUri(uri),
			last = uri.lastIndexOf('.');

		return last > -1 ? uri.slice(last + 1).toLowerCase() : "";
	},

	getType: function(uri) {
		return self.types[self.getExtension(uri)] || "file";
	},

	getKey: function(uri) {

		// Key given, just return key.
		return uri.substring(0,1)=="_" ? uri :
			// Else convert to uri to key
			'_' + $.moxie.btoa(uri)
			.replace(/\+/g, ".")
			.replace(/\=/g, "-")
			.replace(/\//g, "~");
	},

	// getUri(key)
	// getUri(folder)
	getUri: function(key) {

		if (key instanceof $) {
			var folder = key;
			key = folder.data("key") || "";
		}

		// Uri given, just return uri.
		return key.substring(0,1)!=="_" ? key :
			// Else convert key to uri
			$.moxie.atob(key
				.slice(1)
				.replace(/\./g, "+")
				.replace(/\-/g, "="));
	},

	getParent: function(uri) {

		var uri = self.getUri(uri),
			last = uri.lastIndexOf("/");

		return last > -1 ? uri.slice(0, last) : null;
	},

	getFilename: function(uri) {

		var uri = self.getUri(uri);
		var last = uri.lastIndexOf("/");

		return last > -1 ? uri.slice(last + 1) : uri;
	},

	isRoot: function(uri) {

		return self.getUri(uri).indexOf("/") < 0;
	},

	getCurrentPostUri: function() {
		return "post:" + EasyBlog.Composer.getPostId();
	},

	//
	// Library
	//

	medias: {},

	mediaLoaders: {},

	getMedia: function(uri) {

		var key = self.getKey(uri);
		var mediaLoader = self.mediaLoaders[uri];

		if (!mediaLoader) {

			mediaLoader =
				EasyBlog.ajax('site/views/mediamanager/media', {key: key})
					.done(function(media) {
						var uri = media.uri;
						self.setMedia(uri, media);
					});
		}

		return mediaLoader;
	},

	setMedia: function(uri, media) {

		self.medias[uri] = media;
		self.mediaLoaders[uri] = $.Deferred().resolve(media);
	},

	getVariations: function(uri) {

		var media = self.medias[uri];

		if (!media) return;

		return media.meta.variations || {};
	},

	getVariation: function(uri, candidates) {

		// Convert presets into candidates
		if ($.isString(candidates)) {

			var variationKey = candidates;

			switch (variationKey) {

				case "icon":
					candidates = [
						"system/icon", // EB
						"system/small", // EB
						"system/small 320", // FK
						"system/thumbnail", // ES, JS
						"system/original"
					];
					break;

				case "small":
					candidates = [
						"system/small", // EB
						"system/small 320", // FK
						"system/thumbnail", // ES, JS
						"system/original"
					];
					break;

				case "medium":
					candidates = [
						"system/medium",
						"system/large",
						"system/original"
					];
					break;

				case "thumbnail":
					candidates = [
						"system/thumbnail", // EB, ES, JS, FK
						"system/original"
					];
					break;

				case "large":
					candidates = [
						"system/large", // EB, ES, FK
						"system/original" // JS
					];
					break;

				case "original":
					candidates = [
						"system/original"
					];
					break;

				default:
					candidates = [
						variationKey,
						"system/original"
					];
					break;
			}
		}

		// Get variation
		var variations = self.getVariations(uri);
		var variation;

		$.each(candidates, function(i, variationKey){

			if ($.has(variations, variationKey)) {
				variation = variations[variationKey];
				return false;
			}
		});

		return variation;
	},

	createVariation: function(uri, name, width, height) {

		var key = self.getKey(uri);

		var task =
			EasyBlog.ajax("site/views/mediamanager/createVariation", {
				key: key,
				name: name,
				width: width,
				height: height
			})
			.done(function(media){

				// Update cache with update media object
				self.setMedia(uri, media);
			});

		return task;
	},

	removeVariation: function(uri, name) {

		var key = self.getKey(uri);

		var task =
			EasyBlog.ajax("site/views/mediamanager/deleteVariation", {
				"key": key,
				"name": name
			})
			.done(function(media){

				// Update media cache
				self.setMedia(uri, media);
			});

		return task;
	},

	mode: 'standard',
	currentFilterType: 'all',
	currentBrowseButton: null,
	currentFolderUri: null,

	closeMediaManager: function() {

		composer.views.hide("media");
		composer.frame().removeClass("layout-media");

		// Always reset the current filter type
		self.currentFilterType = 'all';
		self.mode = 'standard';
		self.currentBrowseButton = null;

		// Remove any is-selecting from the container
		self.frame().removeClass('is-selecting');

		// Set the filter type
		self.frame().switchClass('filter-' + self.currentFilterType);
	},

	openMediaManager: function(uri, filter) {

		composer.views.show("media");

		// Set the filter
		if (filter) {
			self.currentFilterType = filter;
		}

		// If a start folder is provided, navigate to the respective url
		if (uri) {
			self.navigate(uri);
		}
	},

	//
	// Navigation
	//

	currentUri: "places",

	places: {
		node: null
	},

	"{place} click": function(place) {
		var id = place.data('id');
		var key = place.data('key');
		var uri = self.getUri(key);

		if (id == 'post') {
			uri = self.getCurrentPostUri();
		}

		self.navigate(uri);
	},

	open: function(uri) {

		self.navigate(uri);
	},

	navigate: function(uri) {

		// If currentUri is the uri to be opened, stop.
		var currentUri = self.currentUri;

		if (currentUri === uri) {
			return;
		}

		var content;

		// If we are going out from a subfolder, the direction is reversed.
		var direction = currentUri.indexOf(uri) === 0 ? "prev" : "next";

		// When a uri is opened from post or user list, it needs a referer.
		var referer = /post(s)*|user(s)*/.test(currentUri) ? currentUri : null;

		switch (uri) {

			case "places":
				content = self.places.node;
				direction = "prev";
				break;

			case "posts":

				content = self.getHint('loading');

				// If the user is coming from the users listings, direction should be previous
				if (/post\:(.*)/.test(referer)) {
					direction = "prev";
				}

				EasyBlog.ajax('site/views/mediamanager/posts', {})
					.done(function(html) {
						content.replaceWith(html);
					});
				break;

			case "users":
				content = self.getHint('loading');

				// If the user is coming from the users listings, direction should be previous
				if (/user\:(.*)/.test(referer)) {
					direction = "prev";
				}

				EasyBlog.ajax('site/views/mediamanager/users', {})
					.done(function(html){
						content.replaceWith(html);
					});

				break;

			case "Flickr":
			case "flickr":

				content = self.getHint('loading');

				EasyBlog.ajax('site/views/mediamanager/flickr', {})
					.done(function(html){
						content.replaceWith(html);

						self.revealThumbnails();
					});
				break;

			case "easysocial":
			case "jomsocial":

				// Get the loader
				content = self.getHint('loading');

				EasyBlog.ajax('site/views/mediamanager/folder', {
					"key": self.getKey(uri)
				}).done(function(html) {

					content.replaceWith(html);

					var folder = self.getFolder(uri);

					self.trigger("mediaFolderDisplay", [folder]);
				});
				break;

			// easysocial:1
			// jomsocial:1
			// post:32
			// user:64
			// shared
			default:

				// Get content from existing nodes
				var content = self.getFolder(uri);

				// If content does not exist, load it first.
				if (content.length < 1) {

					// Set loading hint as content to show.
					content = self.getHint("loading");

					contentNode = content[0];

					// Load folder content
					self.loadFolder(uri)
						.done(function(folder){
							// Replace hint with folder content
							$(contentNode).replaceWith(folder);

							self.revealThumbnails();

							// Mark that this content is
							// opened from a referer.
							if (referer) {
								folder.data("referer", referer);
							}
						});

				} else {
					self.refreshFolder(content);
				}

				break;
		}

		// Don't display anything if there's no content
		if (!content) {
			return;
		}

		// Set uri as current uri
		self.currentUri = uri;

		// Display content
		self.display(content, direction);

		// mediaNavigate (uri, fromUri, content)
		self.trigger("mediaNavigate", [uri, currentUri, content]);
	},

	display: function(content, direction) {

		self.pages()
			.pageslide(content, direction);

		// This shouldn't be here.
		// Fixes layout not reverted when going into move dialog
		self.destroyInfo();
	},

	"{folderBackButton} click": function(button) {

		// Get the folder
		var folder = self.folder.of(button);

		// Get the current folder's uri
		var uri = self.getUri(folder.data("key"));

		// If there is a referer, go to referer.
		var referer = folder.data("referer");

		// Always removed the moved message when user navigates to another location
		folder.removeClass('folder-moved')
			.removeClass('file-moved');

		if (referer) {
			targetUri = referer;

		} else if (self.isRoot(uri)) {
			// If this is root folder, go to places.
			targetUri = "places";
		} else {

			// If this is a subfolder, go to parent.
			targetUri = self.getParent(uri);
		}

		self.navigate(targetUri);
	},

	"{infoBackButton} click": function(button) {

		var info = self.info.of(button)

		var uri = self.getUri(info.data("key"));
		var parentUri = self.getParent(uri);

		// Destroy info
		self.destroyInfo();

		// Navigate to parent uri
		self.navigate(parentUri);
	},

	//
	// Hints
	//
	hints: {
		node: null
	},

	getHint: function(name) {
		var hint = $(self.hints.node).find(".hint-" + name).clone();

		// Monkey patch
		if (name=="loading" || name=="error") {
			self.folderBackButton.inside(hint)
				.attr("data-key", self.getKey(self.currentUri));
		}

		return hint;
	},

	getUploadTemplate: function(file) {
		var template = $(opts.templates.upload);

		template.attr('data-id', file.id);
		template.find('[data-eb-mm-upload-name]').html(file.name);


		return template[0];
	},

	//
	// Folder
	//
	folder: {

		// This object cache folder nodes
		nodes: {},

		// Fast alternative to self.folder().where("key", key);
		get: function(key) {
			var key = self.getKey(key);
			var selector = self.folder.selector + "[data-key='" + key + "']";
			var folder = base.find(selector);
			return folder;
		},

		insert: function(folder, html) {

            // Display folders on the current folder view.
            folder.removeClass("is-empty")
                .find(".eb-mm-filegroup.type-folder")
                .removeClass("is-empty");

			// Prepend the new folder html structure
			self.getFilelist(folder, 'folder')
				.prepend(html);
		}
	},

	getCurrentFolder: function() {

	},

	getFolder: function(uri) {
		return $(self.folder.nodes[uri]);
	},

	setFolder: function(uri, folder) {

		self.folder.nodes[uri] = folder[0];
	},

	folderLoaders: {},

	loadFolder: function(uri) {

		var folderLoader =
			self.getMedia(uri)
				.then(function(media){

					// There might be whitespace in folder html,
					// which we'll end up with a jquery element with 2 nodes,
					// one is the folder node, one is the text node,
					// this ensure we're really pointing to the folder node.
					var folder = $(media.folder).filter(self.folder.selector);

					// Store folder
					self.setFolder(uri, folder);

					// Init folder
					self.initFolder(folder);

					return folder;
				});

		// Cache folder loader
		self.folderLoaders[uri] = folderLoader;

		return folderLoader;
	},

	initFolder: function(folder) {

		var uri = self.getUri(folder);

		var initScript = $.Script(function(){

			// If this folder has been initialized, stop.
			if (folder.data("inited")) {
				return;
			}

			// If user can upload to this folder,
			// initialize uploader on this folder.
			if (folder.hasClass("can-upload")) {
				self.uploader.register(folder);
			}

			// Set inited flag
			folder.data("inited", true);

			// Trigger mediaFolderInit event.
			self.trigger("mediaFolderInit", [uri, folder]);

			// Refresh content
			self.refreshFolder(folder);
		});

		// When the folder is appended,
		// initialize the folder.
		folder.append(initScript);
	},

	renameFolder: function(key, newFileName) {

		EasyBlog.ajax("site/views/mediamanager/rename", {
				"key": key,
				"filename": newFileName
			}).done(function(fileHtml, infoHtml, folderHtml) {

				// When the folder is renamed, it's contents needs to be replaced.
				var oldFolder = self.folder.get(key);

				var folder = $(folderHtml);

				self.initFolder(folder);

				oldFolder.replaceWith(folder);

				var uri = self.getUri(folder);
				self.currentUri = uri;

				var parentUri = self.getParent(uri);
				var parentFolder = self.getFolder(parentUri);
				var oldFile = parentFolder.find(self.file.getSelector(key));
				oldFile.replaceWith(fileHtml);

			}).fail(function(file) {

			});
	},

	refreshFolder: function(folder) {

		// Sync upload items
		self.syncUploadItems(folder);

		self.trigger("mediaFolderRefresh", [folder]);
	},

	//file:
	// Upload Items
	//

	uploadItems: {},

	createUploadItem: function(file) {

		var uploadItem = self.getUploadTemplate(file);

		self.uploadItems[file.id] = uploadItem;

		// If file is an image
		if (file.type.match("image")) {

			var image = new $.moxie.Image();

			image.onload = function() {

				// Resize to ~100px
				image.downsize(100);

				// Set as thumbnail
				self.uploadThumbnail.inside(uploadItem)
					.css("backgroundImage", $.cssUrl(image.getAsDataURL()));
			}

			image.load(file.getSource());
		}

		// Register item to uploader so uploader
		// for automatic file to item data binding.
		self.uploader.addItem(file, uploadItem);

		return uploadItem;
	},

	addUploadItem: function(folder, file) {

		// Create item
		var uploadItem = self.uploadItems[file.id] || self.createUploadItem(file);
		var type = self.getType(file.name);

		// Add item to filelist
		// This will always add to the beginning.
		self.getFilelist(folder, type)
			.prepend(uploadItem);

		self.filegroup.inside(folder)
			.filter(".type-" + type)
			.addClass("has-new-files");
	},

	syncUploadItems: function(folder) {

		var uri = folder.data("uri"),
			files = self.uploader.getFiles(uri),
			uploadItems = {image: [], video: [], audio: [], file: []},
			uploadItem,
			i = 0, type;

		// This will populate an array of upload items.
		while (file = files[i++]) {

			uploadItem = self.uploadItems[file.id];

			if (!uploadItem) {
				uploadItem = self.createUploadItem(file);
			}

			type = self.getType(file.name);

			items[type].push(uploadItem);
		}

		// This will insert upload items in bulk
		// into the proper filelist on the folder.
		for (type in uploadItems) {

			var items = uploadItems[type],
				empty = items.length < 1,
				// Get filelist
				filelist =
					// Toggle is-uploading state
					self.getFilelist(folder, type)
						.toggleClass("is-uploading", !empty);

			if (!empty) {
				// Add upload items to filelist
				filelist.prepend(items);
			}
		}
	},

	"{selectingCancel} click": function(el, event) {

		// Hide the media manager
		self.closeMediaManager();
	},

	"{browseButton} click": function(browseButton, event) {
		var filter = browseButton.data('eb-mm-filter');
		var startKey = browseButton.data('eb-mm-start-uri');
		var startUri = self.getUri(startKey);
		var places = browseButton.data('eb-mm-browse-place');
		
		// We need to know what is the purpose of this browse button is for.
		// If this is for post cover, we should let the media manager know
		// that there shouldn't be any image properties.
		var browseType = browseButton.data('eb-mm-browse-type');

        if (startUri == 'post') {
            startUri = self.getCurrentPostUri();
        }

        // Let the world know that the current mode is selecting an image
        self.mode = 'select';
        self.currentBrowseType = browseType;
        self.currentBrowseButton = browseButton;

        // Trigger an event so listeners could bind their event when selecting image starts
        self.currentBrowseButton.trigger("mediaSelectStart", [self.currentBrowseButton]);

		// When this is invoked, load up the mediamanager
		self.openMediaManager(startUri, filter);

		// The media manager could already be opened at this state of time so it's safer to filter it again
		self.frame().switchClass('filter-' + self.currentFilterType);

		// Apply is-selecting class on the container
		self.frame().addClass('is-selecting');

		if (places == 'local') {
			self.frame().addClass('show-local');
		} else {
			self.frame().removeClass('show-local');
		}

	},

	"{self} mediaUploaderFileFiltered": function(base, event, uploader, file) {

		var uri = uploader.uri,
			folder = self.getFolder(uri);

		if (folder.length < 1) return;

		// Add is-uploading class;
		folder.addClass("is-uploading");

        // Cheap hack
        folder.removeClass("is-empty")
            .find(".eb-mm-filegroup")
            .removeClass("is-empty");

		// If this folder is the active folder,
		// add upload item straight away.
		if (uri==self.currentUri) {
			self.addUploadItem(folder, file);

			// Show only upload items
			// folder.addClass("filter-new");
		}
	},

	"{self} mediaUploaderFileUploaded": function(base, event, uploader, file, data) {

		var response = data.response;
		var media = response.media;

		// Store media in library
		self.setMedia(media.uri, media);

		// Delay replacing on file item so user can see the progress bar moving
		setTimeout(function(){

			var uploadItem = self.uploadItems[file.id];

			// If upload item exists, replace it with file item
			if (uploadItem) {

				// Create file item
				var fileItem = $(media.file).addClass("is-new");

				// Replace upload item with file item
				$(uploadItem).replaceWith(fileItem);
			}

		}, 600);
	},

	"{self} mediaUploaderUploadComplete": function(base, event, uploader) {
	},

	"{self} mediaUploaderChange": function(base, event, uploader) {


		var Plupload = $.plupload2,
			uri = uploader.uri,
			folder = self.getFolder(uri),
			files = self.uploader.getFiles(uri),
			currentFile,
			completed = 0,
			total = 0,
			percent = 0;

		// Get currentFile, total, and percent.
		$.each(files, function(i, file){

			// Skip files that have failed or finalized
			if (file.finalized || file.status==Plupload.FAILED) return;

			// If this file is being uploaded, mark as current file.
			if (file.status==Plupload.UPLOADING) {
				currentFile = file;
			}

			// If this file is done uploading, increase completed count.
			if (file.status==Plupload.DONE) {
				completed++;
			}

			// Accumulate progress
			percent += file.percent;

			// Increase total count
			total++;
		});

		// If upload is in progress
		if (total > 0 && total!==completed) {

			// Activate folder upload bar
			folder.addClass("is-uploading");

			if (currentFile) {
				self.folderUploadCurrentFile()
					.html(currentFile.name);
			}

			self.folderUploadTotal()
				.html(total);

			self.folderUploadCompleted()
				.html(completed);

			self.folderUploadProgressBar()
				.width((percent / (total * 100)) * 100 + "%");

		} else {

			// Deactivate folder upload bar
			folder.removeClass("is-uploading");

			$.each(files, function(i, file){
				file.finalized = true;
			});
		}

		// Update recent file count
		self.newFileCount()
			.html(self.file(".is-new").length);
	},

	getFilelist: function(folder, type) {

		var filelist =
			self.filegroup.inside(folder)
				.filter(".type-" + type)
				.find(self.filelist);

		return filelist;
	},

	//
	// Filegroup & Files
	//

	file: {
		active: $(),

		// This is a faster alternative than doing self.file().where("key", key);
		get: function(key) {

			var key = self.getKey(key),
				selector = self.file.getSelector(key);

			return base.find(selector);
		},

		getSelector: function(key) {

			return self.file.selector + "[data-key='" + key + "']";
		},

		activate: function(file) {

			// Remove any active file
			self.file.deactivate();

			// Activate the file and set the current active file
			self.file.active = file.addClass('active');
		},

		deactivate: function() {
			// Remove the current active file
			self.file.active.removeClass('active');
		},

		rename: function(key, filename) {

			EasyBlog.ajax("site/views/mediamanager/rename", {key: key, filename: filename})
				.done(function(fileHtml, infoHtml){

					// TODO: Renamed files needs to be deleted from cached filelist html.
					self.file.get(key)
						.replaceWith(fileHtml);

					self.info.get(key)
						.replaceWith(infoHtml);
				})
				.fail(function(file){

				});
		}
	},

	"{file} click": function(file) {

		var key = file.data("key");
		var uri = self.getUri(key);

		// Deactivate current activate file
		self.file.activate(file);

		// If this is a folder, navigate to the folder.
		if (file.hasClass("type-folder")) {
			self.navigate(uri);
			return;
		}

		// If this is a file, show info.
		var folder = self.folder.of(file);

		self.showInfo(uri);
	},

	"{filegroupHeader} click": function(filegroupHeader) {

		var filegroup = self.filegroup.of(filegroupHeader);

		filegroup[
			filegroup.hasClass("is-collapsed") ?
				"removeTransitionClass" :
				"addTransitionClass"
		]("is-collapsed", 500);
	},

	"{showNewFilesButton} click": function(button) {

		self.folder.of(button)
			.addClass("filter-new");
	},

	"{showAllFilesButton} click": function(button) {

		self.folder.of(button)
			.removeClass("filter-new");
	},

	//
	// Info
	//
	info: {

		get: function(uri) {

			// Unlike file.get, no optimization is necessary here.
			var key = self.getKey(uri);

			return self.info().where("key", key);
		}
	},

	showInfo: function(uri) {

		// Destroy existing info if necessary
		self.destroyInfo();

		// Trigger mediaInfoShow event
		self.trigger("mediaInfoShow", [uri]);

		// Get loading hint
		var loadingHint = self.getHint("loading");

		// Get the parent folder to set it on the
		// back button of the loading hint.
		var parentUri = self.getParent(uri);
		var parentKey = self.getKey(parentUri);

		self.folderBackButton.inside(loadingHint)
			.attr("data-key", parentKey);

		self.pages()
			.pageslide(loadingHint, "next");

		self.currentUri = uri;

		// When media is loaded
		self.getMedia(uri)
			.done(function(media){

				// Replace loading hint with info
				var info = $(media.info);
				loadingHint.replaceWith(info);

				// If we are browsing for post cover, media info shouldn't be displayed
				if (self.currentBrowseType != 'cover') {
					// Trigger mediaInfoDisplay event
					self.trigger("mediaInfoDisplay", [info, media]);
				}

			})
			.fail(function(){

				// Show error hint
				var errorHint = self.getHint("error");
				loadingHint.replaceWith(errorHint);
			});
	},

	destroyInfo: function() {

		// Get info and info viewport
		var info = self.info();

		// Get uri
		var uri = info.data("uri");

		// Trigger mediaInfoHide event
		self.trigger("mediaInfoHide", [uri]);

		// If no uri, nothing to destroy.
		if (!uri) return;

		// Get media
		var media = self.medias[uri];

		// Trigger mediaInfoDestroy event
		self.trigger("mediaInfoDestroy", [info, media]);
	},

	//
	// Sign in with Flickr button
	//

	"{flickrButton} click": function(button) {
		var url = button.data('url');
		var folder = self.folder.of(button);

		var width = 960;
		var height = 650;
        var left = (screen.width/2)-(width /2);
        var top = (screen.height/2)-(height /2);

		window.doneLogin = function() {

			var loading = self.getHint('loading');

			// Display the loading screen
			self.display(loading, 'next');

			// Display a loading screen
			EasyBlog.ajax('site/views/mediamanager/flickr')
				.done(function(output) {

					// Display flickr's contents
					self.display(output, 'next');
				});
		}

		// Open the sign in popup
        window.open(url, "", 'scrollbars=no,resizable=no, width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
	},

	//
	// Search
	//
	toggleSearch: function(folder) {
		if (folder.hasClass(isSearching)) {
			self.deactivateSearch(folder);
		} else {
			self.activateSearch(folder);
		}
	},

	activateSearch: function(folder) {

		folder.addClass(isSearching);

		// Clear search input
		self.searchInput.inside(folder)
			.val("")
			.focus();
	},

	deactivateSearch: function(folder) {

		folder.removeClass(isSearching);

		self.resetSearch(folder);
	},

	resetSearch: function(folder)  {

		// Clear search input
		self.searchInput.inside(folder)
			.val("");

		// Show everything
		self.search(folder, "");
	},

	search: function(folder, keyword) {

		var keyword = keyword.toUpperCase();
		var isSearching = keyword!=="";

		// If keyword is empty, show all results.
		folder
			.removeClass(isNotFound)
			.toggleClass(hasSearchResults, isSearching);

		// Get filegroups and files
		var filegroups = self.filegroup.inside(folder);
		var files = self.file.inside(folder);

		// If we're not searching, remove search classnames.
		if (!isSearching) {
			files.removeClass(isSearchResult);
			filegroups.removeClass(hasSearchResults);
			return;
		}

		// Search for files
		var types = {};
		var count = 0;

		files.each(function(){

			var file = $(this);
			var found = file.text().toUpperCase().indexOf(keyword) >= 0;

			// If this files matches, remember the occurence of this file type,
			// and also increase the result count.
			if (found) {
				types[file.data("type")] = true;
				count++;
			}

			file.toggleClass(isSearchResult, found);
		});

		// Display filegroups that has file matches
		$.each(types, function(type) {
			filegroups.filter(".type-" + type)
				.addClass(hasSearchResults);
		});

		// If no results found, show notfound hint.
		folder.toggleClass(isNotFound, count < 1);
	},

	"{searchToggleButton} click": function(button) {

		// Get folder of button
		var folder = self.folder.of(button);
		self.toggleSearch(folder);
	},

	"{searchInput} keyup": function(searchInput, event) {

		// Escape
		if (event.keyCode===27) {

			var folder = self.folder.of(searchInput);
			var keyword = searchInput.val();

			// Second ESC, deactivate search.
			if (keyword=="") {
				self.deactivateSearch(folder);

			// First ESC, reset search.
			} else {
				self.resetSearch(folder);
			}
		}
	},

	"{searchInput} input": $.debounce(function(searchInput){

		var folder = self.folder.of(searchInput),
			keyword = $.trim(searchInput.val()).toUpperCase();

		self.search(folder, keyword);

	}, 150),


	//
	// Actions
	//
	"{createFolder} click": function(button) {
		var folder = self.folder.of(button);
		var key = self.getKey(self.currentUri);

		EasyBlog.dialog({
			"content": EasyBlog.ajax("site/views/mediamanager/createFolderDialog"),
			bindings: {
				"{submitButton} click": function() {
					EasyBlog.ajax("site/views/mediamanager/createFolder", {
						"key": key,
						"folder": this.folderName().val()
					}).done(function(html){

						// Insert a new folder code
						self.folder.insert(folder, html);

						// Hide the dialog now
						EasyBlog.dialog().close();
					});
				}
			}
		});
	},

	"{self} mediaFolderDisplay": function(base, event, folder) {

	},

	"{self} mediaRemove": function(base, event, uri) {

		var key = self.getKey(uri);

		self.file()
			.where("key", key)
			.remove();

		self.infoViewport()
			.removeClass("active");

		var parentUri = self.getParent(uri);
		var folder = self.getFolder(parentUri);
		var detachedFile = folder.find("[data-eb-mm-file][data-key='" + key  + "']");

		detachedFile.remove();

		setTimeout(function(){
			self.info().where("key", key)
				.detach();
		}, 500);
	},

	"{filegroupShowAllButton} click": function(button) {

		self.filegroup.of(button)
			.addClass("show-all");

		self.revealThumbnails();
	},

	revealThumbnails: function() {

		var folderContentPanel = self.folderContentPanel();

		if (folderContentPanel.length < 1) return;

		var viewportTop = folderContentPanel.offset().top,
			viewportBottom = viewportTop + folderContentPanel.height();

		self.thumbnail.inside(folderContentPanel)
			.each(function(){

				var thumbnail = $(this);
				if (thumbnail.is(":hidden")) return;

				var thumbnailTop = thumbnail.offset().top,
					thumbnailBottom = thumbnailTop + thumbnail.height();


				// if (thumbnailBottom >= viewportTop && thumbnailTop <= viewportBottom) {
					thumbnail
						.css("backgroundImage", $.cssUrl(thumbnail.data("thumbnail")))
						.removeAttr("data-thumbnail");

					return true;
				// }
			});
	},

	"{pages} pageslidestop": function() {
		self.revealThumbnails();
	},

	"{folderContentPanel} scrolly": $.debounce(function(folderContentPanel) {
		self.revealThumbnails();
	}, 150),

	"{fileInsertButton} click": function(button) {

		// Get uri
		var info = self.info.of(button);
		var key = info.data("key");
		var uri = self.getUri(key);

		// Get block
		var mediaDocument = self.mediaDocument();
		var block = mediaDocument.find(EBD.standaloneBlock);

		// TODO: Show loading indicator

		// stop video player if there is any
		self.pauseVideoObject();

		// Get media
		self.getMedia(uri)
			.done(function(media){

				if (self.mode == 'select') {
					self.currentBrowseButton.trigger('mediaSelect', [media]);
					composer.blocks.panel.fieldgroup.hide();
				} else {
					// Trigger mediaInsert event
					self.trigger("mediaInsert", [media, block]);
				}

				// Close the media manager once it's selected
				self.closeMediaManager();
			})
			.fail(function() {

				// TODO: Show unable to insert media error
			})
			.always(function(media){

				// TODO: Hide loading indicator
			});
	},

	"{fileRemoveButton} click": function(button) {

		// Get uri
		var info = self.info.of(button);
		var key = info.data("key");
		var uri = self.getUri(key);

		// Get the media object
		self.getMedia(uri)
			.done(function(meta) {

				// Display confirmation to delete file first
				EasyBlog.dialog({
					"content": EasyBlog.ajax("site/views/mediamanager/deleteFileDialog", {
								"file": meta.title
								}),
					"bindings": {
						"{submitButton} click": function() {

							EasyBlog.ajax("site/views/mediamanager/delete", {key: key})
								.done(function() {

									// Hide the dialog
									EasyBlog.dialog().close();

									// Remove the media
									self.removeMedia(uri);

									// After removing the media, navigate to parent's folder.
									var parentUri = self.getParent(uri);
									self.navigate(parentUri);
								})
								.fail(function() {
								});
						}
					}
				});

			});

	},

	"{fileRenameButton} click": function(button) {

		var info = self.info.of(button);
		var key = info.data("key");
		var filename = $.trim(self.infoFilename.inside(info).text());

		EasyBlog.dialog({
			content: EasyBlog.ajax("site/views/mediamanager/renameFileDialog", {"current" : filename}),
			bindings: {
				"{submitButton} click": function() {
					// TODO: Prevent lousy renames (extension changed, or end up starting with dot)
					var newFilename = this.fileName().val();

					// If filename is empty, stop.
					if ($.trim(newFilename) == "") {
						return;
					}

					// Rename the file
					self.file.rename(key, newFilename);
				}
			}
		});
	},

	"{fileMoveButton} click": function(button) {

		var activeTreeItem = self.treeItem(".active");

		if (activeTreeItem.length < 1) {
			return;
		}


		var source = button.data("key");
		var sourceUri = self.getUri(source);

		var target = activeTreeItem.data("key");
		var targetUri = self.getUri(target);

		var parentUri = self.getParent(sourceUri);
		var folder = self.getFolder(parentUri);

		EasyBlog.dialog({
			content: EasyBlog.ajax('site/views/mediamanager/confirmMoveItem', {
				"source": source,
				"target": target
			}),
			bindings: {
				"{submitButton} click": function() {

					EasyBlog.ajax("site/views/mediamanager/move", {
						source: source,
						target: target
					}).done(function(fileHtml, infoHtml){

						// Remove the source item from the folder contents
						self.removeMedia(source);

						// Refresh the target folder
						self.refreshMedia(targetUri);

						// Hide the dialog now
						EasyBlog.dialog().close();

						// We need to test this media if it's a folder.
						var file = $(fileHtml);
						var type = file.data('type');

						// Add is-moved class on the folder
						if (type == 'folder') {
							folder.addClass('folder-moved');
							self.frame()
								.removeClass("show-foldermove-message show-filemove-message has-messages")
								.addClass("show-foldermove-message");
						} else {
							folder.addClass('file-moved');
							self.frame()
								.removeClass("show-foldermove-message show-filemove-message has-messages")
								.addClass("show-filemove-message");
						}

						// Allow user to quickly go into the target folder.
						self.goToFolder().on('click', function() {

							// Remove any moved class
							folder.removeClass('folder-moved')
								.removeClass('file-moved');

							// Navigate to the target folder.
							self.navigate(targetUri);
						});

						// Once a media item is removed, we need to navigate to the parent's folder
						self.navigate(parentUri);

						// // We should still display the info panel even after the file is moved
						// if (media.meta) {
						// 	self.showInfo(media.meta.uri);
						// }

					})
					.fail(function(){
						alert("Unable to move file.");
					});

				}
			}
		})
	},

	refreshMedia: function(uri) {

		// Ensure that the arguments is always an uri
		uri = self.getUri(uri);

		delete self.medias[uri];
		delete self.mediaLoaders[uri];
		delete self.folderLoaders[uri];
		delete self.folder.nodes[uri];
	},

	removeMedia: function(uri) {

		// Delete the item from the cache
		uri = self.getUri(uri);

		// Rrefresh the media item
		self.refreshMedia(uri);

		// Remove the media
		self.trigger('mediaRemove', [uri]);
	},

	"{showMoveDialogButton} click": function(button) {

		var moveDialog = self.getHint("move");
		var info = self.info.of(button);
		var folder = self.folder.of(button);
		var folderKey = folder.data("key");
		var fileKey = info.data("key");
		var fileUri = self.getUri(fileKey);
		var filename = self.infoFilename.inside(info).text();

		// Get the parent folder
		var parentUri = self.getParent(fileUri);
		var parentKey = self.getKey(parentUri);

		// Back button should open
		self.openButton.inside(moveDialog)
			.attr("data-key", parentKey);

		self.moveFilename.inside(moveDialog)
			.html(filename);

		self.fileMoveButton.inside(moveDialog)
			.attr("data-key", fileKey);

		self.pages()
			.pageslide(moveDialog, "next");

		self.destroyInfo();

		self.currentUri = fileUri + "/?move"
	},

	// Folder traversal
	"{treeItem} click": function(treeItem, event) {

		// Clicking on child item won't cause parent item to be clicked on.
		event.stopPropagation();

		// Highlight tree item
		self.treeItem().removeClass("active");

		treeItem.addClass("active").toggleClass("is-expanded");

		// If we're collapsing tree item, don't do anything.
		if (!treeItem.hasClass("is-expanded")) {
			return;
		}

		// If tree item already has child tree, don't load anymore.
		if (treeItem.data("childTree")) {
			return;
		}

		// Show loading indicator
		treeItem.addClass("is-loading");

		var key = treeItem.data("key");
		var uri = self.getUri(key);

		if (uri == "post") {
			uri = self.getCurrentPostUri();
			key = self.getKey(uri);

			treeItem.attr('data-key', key);
		}

		EasyBlog.ajax("site/views/mediamanager/tree", {key: key})
			.done(function(childTree){
				treeItem
					.append(childTree)
					.data("childTree", childTree);
			})
			.fail(function(content){
			})
			.always(function(){
				treeItem.removeClass("is-loading");
			});

		// TODO: If a folder was renamed/created, remove childTree.
	},

	"{openButton} click": function(button) {

		var key = button.data("key"),
			uri = self.getUri(key);

		self.navigate(uri);
	},

	"{insertGallery} click": function(insertGallery) {
		var folder = self.folder.of(insertGallery);
		var key = folder.data('key');
		var uri = self.getUri(key);
		var composer = EasyBlog.Composer;

		var obj = {"uri": uri};
		var output = "[embed=gallery]" + JSON.stringify(obj) + "[/embed]";

		// Insert the embedded contents into the editor
		composer.document.insertContent(output);

		// Close media manager
		self.closeMediaManager();
	},

	"{folderMoveButton} click": function(button) {

		var moveDialog = self.getHint("move"),
			folder = self.folder.of(button),
			key = folder.data("key"),
			uri = self.getUri(key),
			filename = self.getFilename(key);

		// Back button should open
		self.openButton.inside(moveDialog)
			.attr("data-key", key);

		// TODO: Update filename
		self.moveFilename.inside(moveDialog)
			.html(filename);

		// Set folder key
		self.fileMoveButton.inside(moveDialog)
			.attr("data-key", key);

		self.pages()
			.pageslide(moveDialog, "next");

		self.currentUri = uri + "/?move";
	},

	"{folderRenameButton} click": function(button) {

		var folder = self.folder.of(button);
		var key = folder.data("key");
		var filename = self.getFilename(key);

		EasyBlog.dialog({
			content: EasyBlog.ajax("site/views/mediamanager/renameFolderDialog", {"current" : filename}),
			bindings: {
				"{submitButton} click": function() {
					// TODO: Prevent lousy renames (extension changed, or end up starting with dot)
					var newFilename = this.folderName().val();

					// If filename is empty, stop.
					if ($.trim(newFilename) == "") {
						return;
					}

					// Rename the folder
					self.renameFolder(key, newFilename);

					// Close the dialog
					EasyBlog.dialog().close();
				}
			}
		});
	},

	"{folderRemoveButton} click": function(button) {
		var folder = self.folder.of(button);
		var key = folder.data('key');
		var uri = self.getUri(key);

		// Display a confirmation to delete this folder
		EasyBlog.dialog({
			content: EasyBlog.ajax("site/views/mediamanager/deleteFolderDialog"),
			bindings: {
				"{submitButton} click": function() {

					// Delete the folder now
					EasyBlog.ajax("site/views/mediamanager/delete", {key: key})
						.done(function(){

							// Close the dialog once it's completed.
							EasyBlog.dialog().close();

							// Navigate to the parent
							self.navigate(self.getParent(uri));

							// Go back to parent folder
							self.removeMedia(uri);
						})
						.fail(function(){
						});
				}
			}
		});
	},

	"{paginationPrevious} click, {paginationNext} click": function(el) {

		// Get the limitstart
		var page = el.data('page');
		var content = self.getHint('loading');

		EasyBlog.ajax('site/views/mediamanager/users', {
			"page": page
		}).done(function(html){
			content.replaceWith(html);
		});

		// Display content
		self.display(content);

		// mediaNavigate (uri, fromUri, content)
		// self.trigger("mediaNavigate", [uri, currentUri, content]);
	},

	//
	// Drag & Drop
	//
	"{file} mouseover": function(file) {

		// Not for folders
		if (file.data("type")=="folder") {
			return;
		}

		// Not for legacy document
		var composer = EasyBlog.Composer;
		if (composer.document.isLegacy()) return;

		// TODO: This should be loosely coupled from composer.
		// Only initialize dragable on mouseover
		if (!file.data("uiDraggable")) {

			file.draggable({

				helper: function() {

					// Prepare helper
					var helper = file.clone();

					if (file.data("type")=="image") {
						helper
							.addClass("layout-tile")
							.css({
								width: file.outerWidth(),
								height: file.outerHeight()
							});
					} else {
						helper.addClass("layout-list");
					}

					return helper;
				},

				start: function(event, ui) {

					var helper = ui.helper;

					if (helper.hasClass("layout-list")) {
						// Ensure helper show up in the middle of the cursor
						var offsetLeft = event.pageX - file.offset().left;
						var helperWidth = ui.helper.width();
						var helperLeft = offsetLeft - (helperWidth / 2);
						ui.helper.css("margin-left", helperLeft);
					}
				},

				appendTo: composer.ghosts(),
				connectToSortable: EBD.root
			});
		}
	},

	"{file} dragstart": function(file, ui) {

		// Preload media the moment user start dragging,
		// to speed up dropping of blocks.
		var key = file.data("key");
		var uri = self.getUri(key);

		self.getMedia(key);

		self.closeMediaManager();
	},

	"{closeButton} click": function() {

		self.pauseVideoObject();

		self.closeMediaManager();
	},


	"{closeMessageButton} click": function() {

		self.frame()
			.removeClass("show-foldermove-message show-filemove-message has-messages");
	},

	pauseVideoObject: function() {
		if ($("video").length > 0) {
			$("video").get(0).pause();
		}
	}


}});

$("body").addController(controller);

module.resolve(controller);
});

});


EasyBlog.module("mediamanager/uploader", function($){

var module = this;

EasyBlog.require()
.library(
    "plupload2"
)
.done(function(){

EasyBlog.Controller("MediaManager.Uploader", {
    elements: [
        "[data-eb-mm-{upload-progress|upload-name|upload-size|upload-progress-value|upload-progress-bar}]",

        "[data-eb-mm-upload-{error-text|error-retry}]"
    ],
    defaultOptions: {
    }
}, function(self, opts, base, mediaManager) { return {

    init: function() {

        // Globals
        mediaManager = self.mediaManager;

        var defaultUploadOptions = {
            runtimes: "html5, html4",
            autostart: true
        };

        var inlineDefaultUploadOptions = mediaManager.frame().htmlData("mm-uploader");

        self.defaultUploadOptions = $.extend(
            defaultUploadOptions,
            inlineDefaultUploadOptions
        );
    },

    instances: {},

    register: function(container, options) {

        // Normalize options
        var options = $.extend({}, self.defaultUploadOptions, options);

        // Set key in url
        var key = container.data("key");

        // We need to know if the uri is "post" so that we can translate it
        var uri = EasyBlog.MediaManager.getUri(key);

        if (uri == 'post') {
            key = EasyBlog.MediaManager.getKey(EasyBlog.MediaManager.getCurrentPostUri());
        }

        // Get the list of default allowed extensions
        var allowedExtensions = options.extensions.split(',');

        $.each(EasyBlog.MediaManager.options.types, function(i, extensions) {

            // Go through each of the allowed extension
            $(extensions).filter(function(index, extension){
                return $.inArray(extension, allowedExtensions);
            });
        });

        // Since there is no "file" extensions, add it into the types
        EasyBlog.MediaManager.options.types['file'] = allowedExtensions;

        // Get the type of service provided so that we can set which extensions are allowed
        var type = container.data('type') || 'file';

        // Set the default allowed extensions
        options.extensions = EasyBlog.MediaManager.options.types[type].join(',');

        // Get the upload url
        options.url = $.uri(options.url).replaceQueryParam("key", key).toString();

        // Create uploader instance but don't initialize it yet
        // because we need to bind event handlers first.
        var uploader = container.plupload2(options, false);
        var id = uploader.id;

        // Assign destination uri to uploader
        uploader.uri = mediaManager.getUri(key);

        // Keep a reference to the container
        uploader.container = container;

        // Keep a reference to our uploaders
        self.instances[id] = uploader;

        // Bind event handlers
        $.each(self.plupload, function(name, handler) {
            uploader.bind(name, function(){

                // EasyBlog.debug && console.info("mediaUploader" + name, arguments);

                // First, we handle plupload events.
                handler.apply(this, arguments);

                // Then, we forward them as mediaUploader events.
                self.trigger("mediaUploader" + name, arguments);
                container.trigger("mediaUploader" + name, arguments);

                // Always trigger mediaUploaderChange
                self.trigger("mediaUploaderChange", arguments);
                container.trigger("mediaUploaderChange", arguments);
            });
        });

        // Initialize uploader now
        uploader.init();

        // EasyBlog.debug && console.log("Register Uploader", container, uploader);

        return uploader;
    },

    //
    // Event Handlers
    //

    plupload: {

        FileFiltered: function(uploader, file) {

            // Extend file object with items
            file.items = [];
            file.addedDate = new Date().getTime();
        },

        FilesAdded: function(uploader, files) {

            // Start uploading if autostart is true
            // For blog image and image block, this is false
            // because we don't want to upload until user confirms it.
            if (uploader.settings.autostart) {
                uploader.start();
            }
        },

        BeforeUpload: function(uploader, file) {
            self.updateAllItems(file);
        },

        UploadFile: function(uploader, file) {
            self.updateAllItems(file);
        },

        UploadProgress: function(uploader, file) {
            self.updateAllItems(file);
        },

        ChunkUploaded: function(uploader, file) {
            self.updateAllItems(file);
        },

        FileUploaded: function(uploader, file, data) {

            try {
                // Convert response json into object
                var response = $.parseJSON(data.response);
                data.response = response;
            } catch (e) {};

            self.updateAllItems(file);
        },

        Error: function(uploader, error) {

            // If this is a file level error
            var file = error.file;

            if (file) {

                if (error.code == '-600') {
                    file.status = 4;
                    file.error = error.message;

                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/composer/cancelFileSizeWarning')
                    });

                    self.updateAllItems(file);

                    // Trigger specific FileError event
                    self.trigger("mediaUploaderFileError", arguments);
                    uploader.container.trigger("mediaUploaderFileError", arguments);

                    return;
                }

                // If we hit an error, display the error message
                file.status = 4;
                file.error = $.parseJSON(error.response);

                self.updateAllItems(file);

                // Trigger specific FileError event
                self.trigger("mediaUploaderFileError", arguments);
                uploader.container.trigger("mediaUploaderFileError", arguments);
            }
        },

        FilesRemoved: function(uploader, file) {

            // We will not remove file items. It is up to the
            // implementor to decide if they want to remove it.
            file.status = 6;
            self.updateAllItems(file);
        },

        Destroy: function(uploader) {
            delete self.instances[uploader.id];
        },

        // Unused events
        Init: $.noop,
        PostInit: $.noop,
        OptionChanged: $.noop,
        StateChanged: $.noop,
        QueueChanged: $.noop,
        Refresh: $.noop,
        UploadComplete: $.noop
    },

    //
    // Aggregated Uploader API
    //
    getInstances: function(uri) {

        return $.map(self.instances, function(uploader){
            return uploader.uri==uri ? uploader : null;
        });
    },

    getFiles: function(uri) {

        var instances = self.getInstances(uri),
            files =
                $.chain(instances)
                    .pluck("files")
                    .flatten(true)
                    .sortBy("addedDate")
                    .value();

        return files;
    },

    //
    // Item API
    //

    // The following status matches values of file.status.
    // Access using self.status[file.status]
    status: {
        "0": "idle",      // 0 - Non-standard
        "1": "queued",    // 1
        "2": "uploading", // 2
        "3": "unused",    // 3 - Unused
        "4": "failed",    // 4
        "5": "done",      // 5
        "6": "removed"    // 6 - Non-standard
    },

    addItem: function(file, item) {
        file.items.push(item);
        self.updateItem(file, item);
    },

    updateItem: function(file, item) {

        var item = $(item);

        // Update the state on the uploader element
        var state = self.status[file.status];
        item.switchClass("state-" + state);

        // If there's an error, display the error
        if (file.error) {

            item.find(self.errorText)
                .html(file.error.message);

            item.find(self.errorRetry)
                .off('click.mmupload.retry')
                .on('click.mmupload.retry', function(){
                    item.switchClass('state-idle');
                });

            return;
        }

        // Update title
        item.find(self.uploadName)
            .html(file.name);

        // Update size
        item.find(self.uploadSize)
            .html($.plupload2.formatSize(file.size));

        // Display the percentage value
        item.find(self.uploadProgressValue)
            .html(file.percent + '%');

        // Update progress bar
        item.find(self.uploadProgressBar)
            .width(file.percent + "%");
    },

    updateAllItems: function(file) {

        if (!file.items) {
            return;
        }

        $.each(file.items, function(i, item){

            self.updateItem(file, item);
        });
    },

    removeItem: function(file, item) {

        $.pull(file.items, item);
    }
}});

module.resolve();

});

});


EasyBlog.module("composer/panels/authorship", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Panels.Authorship", {
        defaultOptions: {

            // Author section
            "{authorArea}": "[data-eb-composer-author]",
            "{currentAuthor}": "[data-eb-composer-current-author]",

            "{searchAssociates}": "[data-associates-search]",
            "{cancelSearchAssociates}": "[data-associates-search-cancel]",

            "{searchAuthor}": "[data-author-search]",
            "{cancelSearchAuthor}": "[data-author-search-cancel]",

            // Tabs
            "{tab}": "[data-author-type]",
            "{tabContent}": "[data-tab-content]",

            // Author stuffs
            "{changeAuthor}": "[data-eb-composer-switch-author]",
            "{authorPicker}": "[data-eb-composer-author-picker]",

            "{authorItem}": "[data-eb-author-item]",
            "{authorList}": "[data-eb-composer-author-list]",
            "{authorCheckbox}": "[data-eb-composer-authoritem]",
            "{activeAuthorId}": "[data-eb-composer-authorid]",
            "{activeAuthorAvatar}": "[data-eb-composer-authoravatar]",
            "{activeAuthorName}": "[data-eb-composer-authorname]",

            // author pagination
            "{authorPrevBtn}": "[data-eb-author-prev]",
            "{authorNextBtn}": "[data-eb-author-next]",

            // Associates
            "{associatesList}": "[data-eb-composer-associates-list]",
            "{associateItem}": "[data-associates-item]",
            "{associateCheckbox}": "[data-associates-checkbox]",
            "{activeAssociateName}" : "[data-eb-composer-associatename]",
            "{activeAssociateId}": "[data-eb-composer-associateid]",
            "{activeAssociateType}": "[data-eb-composer-associatetype]",
            "{activeAssociateAvatar}": "[data-eb-composer-associateavatar]",

        }
    }, function(self, opts, base) {

        return {

            init: function() {
            },

            "{cancelSearchAssociates} click": function() {
                // Show all items
                self.associateItem()
                    .removeClass('hide');

                // Empty the search textbox
                self.searchAssociates().val('');
            },

            "{cancelSearchAuthor} click": function() {

                // Show all items
                self.authorItem()
                    .removeClass('hide');

                var search = $.trim(self.searchAuthor().val());

                // Empty the search textbox
                self.searchAuthor().val('');

                if (search) {
                    self.loadAuthors(0, '');
                }
            },

            "{searchAssociates} keydown": $.debounce(function(el, event) {
                var value = $.trim(el.val()).toLowerCase();

                if (!value) {
                    self.associateItem()
                        .removeClass('hide');

                    return;
                }

                var associates = self.associateItem().filter(function() {

                    var title = $.trim($(this).data('title')).toLowerCase();

                    if (title.indexOf(value) == 0) {
                        return true;
                    }

                    return false;
                });


                if (associates) {
                    self.associateItem()
                        .addClass('hide');


                    associates.removeClass('hide');
                }

            }, 300),

            // "{searchAuthor} keydown": $.debounce(function(el, event) {
            //     var value = $.trim(el.val()).toLowerCase();

            //     if (!value) {
            //         self.authorItem()
            //             .removeClass('hide');

            //         return;
            //     }

            //     var authors = self.authorItem().filter(function() {

            //         var title = $.trim($(this).data('title')).toLowerCase();

            //         if (title.indexOf(value) == 0) {
            //             return true;
            //         }

            //         return false;
            //     });


            //     if (authors) {
            //         self.authorItem()
            //             .addClass('hide');


            //         authors.removeClass('hide');
            //     }

            // }, 300),

            "{searchAuthor} keydown": $.debounce(function(el, event) {

                var value = $.trim(el.val()).toLowerCase();

                if (value != "") {
                    self.loadAuthors(0, value);
                }

            }, 300),


            associatesLoaded: false,

            "{tab} click": function(el, event) {

                var type = el.data('type');

                // Only associates needs to be rendered initially
                if (type == 'associates') {
                    self.loadAssociates();
                }

                // Set the active tab
                self.tab().removeClass('active');
                self.tab().where('type', type).addClass('active');

                // Update the tab content
                self.tabContent().removeClass('active');
                self.tabContent()
                    .where('type', type)
                    .addClass('active');
            },

            "{changeAuthor} click": function(element, event) {

                // Pull down the author form.
                self.authorArea().toggleClass('is-opened');

                self.authorPicker().toggleClass('pulled');

                // There could be possibility that the user only sees the "Team" tab
                // if they do not have access to switch authors.
                if (self.tab().length == 1) {
                    self.loadAssociates();
                    return;
                }

                // Perform an ajax call to retrieve a list of authors.
                // EasyBlog.ajax('site/views/composer/listAuthors', {
                //     'selected': self.activeAuthorId().val()
                // })
                // .done(function(output){

                //     // Append to the author list
                //     self.authorList().html(output);

                // });
                self.loadAuthors('0', '');

            },

            "{authorPrevBtn} click": function(el, event) {
                event.preventDefault();

                var search = $.trim(self.searchAuthor().val()).toLowerCase();
                var start = $(el).data('start');
                start = parseInt(start);

                if (start < 0) {
                    start = 0;
                }

                self.loadAuthors(start, search);
            },

            "{authorNextBtn} click": function(el, event) {
                event.preventDefault();

                var start = $(el).data('start');
                var search = $.trim(self.searchAuthor().val()).toLowerCase();

                self.loadAuthors(start, search);
            },


            loadAuthors: function(start, search) {

                //loading
                var loadingDiv = $('<div />', {
                    class: "loading-authors"
                });
                loadingDiv.html('<i class="fa fa-circle-o-notch fa-spin"></i>');
                self.authorList().html(loadingDiv);

                // Perform an ajax call to retrieve a list of authors.
                EasyBlog.ajax('site/views/composer/listAuthors', {
                    'selected': self.activeAuthorId().val(),
                    'limitstart': start,
                    'search': search
                })
                .done(function(output){
                    // Append to the author list
                    self.authorList().html(output);
                });

            },


            loadAssociates: function() {

                // If the list of item has already been loaded, skip this
                if (self.associatesLoaded) {
                    return;
                }

                EasyBlog.ajax('site/views/composer/listAssociates', {
                    source_id: self.activeAssociateId().val(),
                    source_type: self.activeAssociateType().val()
                }).done(function(output) {
                    self.associatesLoaded = true;

                    self.associatesList()
                        .html(output);
                });
            },

            // Trigger for selected author radio
            "{authorCheckbox} change": function(element, event) {
                var avatar = element.parents(self.authorItem.selector).data('avatar');
                var id = element.parents(self.authorItem.selector).data('id');
                var name = element.parents(self.authorItem.selector).data('title');

                // Update the active author
                self.setActiveAuthor(id, name, avatar);
            },

            "{associateCheckbox} change": function(element, event) {

                var avatar = element.parents(self.associateItem.selector).data('avatar');
                var id = element.parents(self.associateItem.selector).data('id');
                var title = element.parents(self.associateItem.selector).data('title');
                var type = element.parents(self.associateItem.selector).data('type');

                // If user tries to select easyblog.sitewide, we shouldn't update anything
                if (type == 'easyblog.sitewide') {
                    self.removeAssociate();
                    return;
                }

                // Update the active associate item
                self.setActiveAssociate(id, type, title, avatar);
            },

            removeAssociate: function() {
                self.activeAssociateId().val(0);
                self.activeAssociateType().val('easyblog.sitewide');

                self.activeAssociateName()
                    .addClass('hidden');
                self.activeAssociateAvatar()
                    .addClass('hide');
            },

            setActiveAssociate: function(id, type, title, avatar) {
                self.activeAssociateId().val(id);

                // Update the team name
                self.activeAssociateName()
                    .html(title)
                    .removeClass('hidden');
                self.activeAssociateType().val(type);

                // Update the team avatar
                self.activeAssociateAvatar()
                    .attr('src', avatar)
                    .removeClass('hide');
            },

            setActiveAuthor: function(id, name, avatar) {
                self.activeAuthorId().val(id);
                self.activeAuthorName().html(name);
                self.activeAuthorAvatar().attr('src', avatar);

                // Display the "You are" section if the selection is not the same
                if (composer.options.authorId != id) {
                    self.currentAuthor().removeClass('hide');
                } else {
                    self.currentAuthor().addClass('hide');
                }

            },

            "{self} composerValidate": function(composer, event, validator) {
            }
        }
    });

    module.resolve();
});

EasyBlog.module("composer/panels/autopost", function($){

    var module = this;

    EasyBlog.Controller("Composer.Panels.AutoPost", {
        defaultOptions: {

            "{twitterCheckbox}": "[data-autopost-twitter]",
            "{facebookCheckbox}": "[data-autopost-facebook]",
            "{linkedinCheckbox}": "[data-autopost-linkedin]"
        }
    }, function(self, opts, base) { 

        return {
            init: function()
            {
            },

            itemUpdated: function(checkbox)
            {
                var checked = $(checkbox).is(':checked');

                if (checked) {
                    $(checkbox).parent().addClass('checked');

                    return;
                }

                $(checkbox).parent().removeClass('checked');
            },

            "{twitterCheckbox} change": function(el, event)
            {
                self.itemUpdated(el);
            },
            "{facebookCheckbox} change": function(el, event)
            {
                self.itemUpdated(el);
            },
            "{linkedinCheckbox} change": function(el, event)
            {
                self.itemUpdated(el);
            }
        }
    });
    
    module.resolve();

});


EasyBlog.module("composer/panels/post", function($){

    var module = this;

    EasyBlog.Controller("Composer.Panels.Post", {
        defaultOptions: {

            // Title of the blog post
            "{title}": "[data-post-title]",

            // Permalink
            permalink: "",
            "{permalinkData}": "[data-permalink-data]",
            "{permalinkInput}": "[data-permalink-input]",
            "{savePermalink}": "[data-permalink-save]",
            "{editPermalink}": "[data-permalink-edit]",
            "{cancelEditPermalink}": "[data-permalink-edit-cancel]",
            "{permalinkEditor}": "[data-permalink-editor]",
            "{permalinkPreview}": "[data-permalink-preview]",

            "{languageSelect}": "[data-composer-language]"
        }
    }, function(self, opts, base, composer) {

        return {

            init: function() {
                // Initilize the permalink value
                opts.permalink = self.permalinkInput().val();
            },

            hidePermalinkForm: function() {
                // Hide the preview
                self.permalinkEditor().addClass('hide');

                // Show the editor
                self.permalinkPreview().removeClass('hide');
            },

            showPermalinkForm: function() {
                // Hide the preview
                self.permalinkPreview().addClass('hide');

                // Show the editor
                self.permalinkEditor().removeClass('hide');
            },

            savePermalinkForm: function() {
                // Generate a proper permalink given the edited permalink value
                var value = self.permalinkInput().val();

                // Request from the server
                EasyBlog.ajax('site/views/composer/normalizePermalink', {
                    "permalink": value
                }).done(function(permalink) {

                    opts.permalink = permalink;

                    // Ensure that the input is always the same as the modified version
                    self.permalinkInput().val(opts.permalink);

                    // Update the preview
                    self.permalinkData().html(permalink);

                    // Hide the form
                    self.hidePermalinkForm();
                });
            },

            "{languageSelect} change": function(el, ev){

                var selected = $(el).val();

                // now we need to hide the same language code association.
                if (selected == "*") {
                    $("[data-composer-association]").addClass("hide");
                } else {

                    $("[data-composer-association]").removeClass("hide");

                    $("[data-composer-association-item]").each(function() {
                        var curItem = $(this);

                        var langid = curItem.data("id");
                        var langcode = curItem.data("code");

                        if (langcode == selected) {
                            $(this).find("input#assoc-postname" + langid).val('');
                            $(this).find("input#assoc-postid" + langid).val('');

                            $(this).addClass('hide');
                        } else {
                            if ($(this).hasClass('hide')) {
                                $(this).removeClass('hide');
                            }
                        }
                    });
                }
            },

            "{cancelEditPermalink} click": function(el, event) {
                // Reset to the original value.
                self.permalinkInput().val(opts.permalink);

                self.hidePermalinkForm();
            },

            "{permalinkInput} keyup": function(el, event) {
                var code = event.keyCode ? event.keyCode : event.which;

                if (code == 13) {

                    self.savePermalinkForm();
                }
            },

            "{savePermalink} click": function(el, event) {
                self.savePermalinkForm();
            },

            "{editPermalink} click": function(el, event) {
                self.showPermalinkForm();
            },

            "{title} change": function(el, event) {
                var value = $(el).val();

                // Update the permalink only if this entry has not been edited before
                if (opts.permalink != '') {
                    return false;
                }

                // Set the title as the permalink value
                self.permalinkInput().val(value);

                // Validate the permalink
                self.savePermalinkForm();
            },

            "{self} composerSelectTemplate": function(el, event, templateId, title, permalink, documentHtml) {

                // If title is not empty, set it here
                if (title) {
                    self.title().val(title);
                }

                if (permalink) {
                    self.permalinkInput().val(permalink);
                    self.savePermalinkForm();
                }
            }
        }
    });

    module.resolve();

});

EasyBlog.module("composer/panels/seo", function($){

    var module = this;

    EasyBlog.require()
        .library("textboxlist")
        .done(function(){

            EasyBlog.Controller("Composer.Panels.Seo", {
                defaultOptions: {
                    // Templates
                    "{keywordTemplate}": "[data-keyword-template]",

                    // Meta description
                    "{metaInput}": "[data-meta-description]",
                    "{metaCounter}": "[data-meta-counter]",

                    // Meta keywords
                    "{keywordCounter}": "[data-keyword-counter]",
                    "{textboxlist}": "[data-eb-composer-seo-keywords-textboxlist]",
                    "{autofillButton}": "[data-eb-composer-seo-keywords-autofill-button]",
                    "{jsondata}": "[data-eb-composer-keywords-jsondata]"
                }
            }, function(self, opts, base, suggestions, selection, tagger) {

                return {

                    init: function() {

                        if (self.metaInput().length == 0) {
                            // this mean the seo panel disabled.
                            return;
                        }

                        // Get the tag template
                        $.template('composer/textboxlist/keywords', self.keywordTemplate().detach().html());

                        self.textboxlist()
                            .textboxlist({
                                component: "eb",
                                view: {
                                    item: 'composer/textboxlist/keywords'
                                }
                            });

                        self.tagger = self.textboxlist().textboxlist("controller");

                        var i = 0;
                        var keywords = JSON.parse(self.jsondata().val());

                        $.each(keywords, function(i, title) {
                            self.tagger.addItem(title);
                        });
                    },

                    // Slightly debounced so mass add/removal only gets executed once
                    "{textboxlist} listChange": $.debounce(function() {
                        self.keywordCounter()
                            .html(self.tagger.getAddedItems().length);
                    }, 15),

                    "{metaInput} keyup": function(el, event) {
                        var length = $(el).val().length;

                        self.metaCounter().html(length);
                    },

                    "{autofillButton} click": function(autofillButton) {
                        var tagger = self.textboxlist().textboxlist("controller");
                        var content = EasyBlog.Composer.document.getText();
                        var parent = $(autofillButton).parent();

                        // Show loading
                        $(parent).addClass('is-loading');

                        EasyBlog.ajax('site/views/composer/suggestKeywords', {
                            "data": content
                        }).done(function(keywords){

                            $(parent).removeClass('is-loading');

                            if (keywords) {
                                $.each(keywords, function(i, tag) {
                                    tagger.addItem(tag.title);
                                });

                                self.metaCounter().text(keywords.length);
                            }
                        });
                    },

                    "{self} composerSave": function(base, event, save) {

                        if (self.metaInput().length == 0) {
                            save.data.keywords = '';
                        } else {
                            save.data.keywords = $.pluck(self.tagger.getAddedItems(), "title").join(", ");
                        }
                    }
                }
            });

            module.resolve();

        });

});

EasyBlog.module("composer/panels", function($) {

var module = this;

EasyBlog.Controller("Composer.Panels",
{
    hostname: "panels",

    defaultOptions: {

        "{panel}": "[data-eb-composer-panel]",
        "{panelTab}": "[data-eb-composer-panel-tab]",
        "{showDrawer}": "[data-eb-composer-panel-show-drawer]",
        "{fieldset}": ".eb-composer-fieldset",
        "{fieldsetToggle}": ".eb-composer-fieldset-toggle input"
    }
},
function(self, opts, base) { return {

    init: function() {
        var plugins = [
            "autopost",
            "association",
            "seo",
            "post",
            "category",
            "authorship"
        ];

        // Install plugins
        $.each(plugins, function(i, plugin){
            self.addPlugin(plugin);
        });
    },

    panel: {

        get: $.memoize(function(panelId) {

            return self.panel().where("id", panelId);
        })
    },

    panelTab: {

        get: $.memoize(function(panelId){

            return self.panelTab().where("id", panelId);
        })
    },

    fieldset: {

        get: function(name) {

            return self.fieldset().where("name", name);
        },

        enable: function(name, val) {

            val===undefined && (val = true);

            self.fieldset.get(name)
                .toggleClass("is-disabled", !val)
                .find(self.fieldsetToggle)
                .prop("checked", !!val);
        },

        disable: function(name, val) {

            val===undefined && (val = false);
            self.fieldset.enable(name, val);
        },

        show: function(name) {

            self.fieldset.get(name)
                .removeClass("is-hidden");
        },

        hide: function(name) {

            self.fieldset.get(name)
                .addClass("is-hidden");
        },

        toggle: function(name, val) {

            self.fieldset.get(name)
                .toggleClass("is-hidden", val===undefined ? undefined : !val);
        }
    },

    activate: function(panelId) {

        self.deactivate();

        self.panel.get(panelId)
            .addClass("active");

        self.panelTab.get(panelId)
            .addClass("active");

        self.trigger("composerPanelActivate", [panelId]);
    },

    deactivate: function(panelId) {

        // If no panelId is given, deactivate current active panel.
        if (!panelId) {
            panelId = self.panel(".active").data("id");
        }

        self.panel.get(panelId)
            .removeClass("active");

        self.panelTab.get(panelId)
            .removeClass("active");

        self.trigger("composerPanelDeactivate", [panelId]);
    },

    "{panelTab} click": function(panelTab) {

        var panelId = panelTab.data("id");

        self.activate(panelId);
    },

    "{showDrawer} click": function() {

       $('[data-eb-composer-frame]').toggleClass('show-drawer');

    },

    "{fieldsetToggle} change": function(fieldsetToggle) {

        self.fieldset.of(fieldsetToggle)
            .toggleClass("is-disabled", !fieldsetToggle.is(":checked"));
    }

}});

module.resolve();

});

EasyBlog.module("composer/posts", function($){

var module = this;

EasyBlog.Controller("Composer.Posts", {

    elements: [
        "[data-eb-posts-{close-button}]",
        "[data-eb-composer-{posts}]",
        "[data-eb-composer-posts-{search-toggle-button|search-cancel-button}]"
    ],

    defaultOptions: {

        // Search items
        "{searchPanel}": "[data-eb-composer-sidebar-search-panel]",
        "{hideSearch}": "[data-eb-composer-sidebar-search-cancel-button]",

        // Post items
        "{wrapper}": "[data-eb-composer-posts]",
        "{result}": "[data-eb-composer-posts-result]",

        "{searchTextfield}": "[data-eb-composer-posts-search-textfield]",
        "{openPostButton}": "[data-eb-composer-open-post]",
        "{insertLinkButton}": "[data-eb-composer-insert-link]",
        "{openMediaButton}": "[data-eb-composer-open-media]",

        "{postTitle}": "[data-eb-composer-posts-item-title]"
    }
}, function(self, opts, base, composer) { return {

    loaded: false,
    initialResult: null,

    init: function() {
        // Globals
        composer = self.composer;
        blocks = composer.blocks;

        // Render the post block initially.
        blocks.loadBlockHandler('post');
    },

    "{closeButton} click": function() {
        self.hideViewport();
    },

    "{searchTextfield} keydown": $.debounce(function(textbox){

        var keyword = $.trim(textbox.val());

        if (keyword.length < 1) {
            self.wrapper().removeClass("searching");
            return;
        }

        // Reset the states to searching
        self.wrapper().removeClass('empty');
        self.wrapper().addClass("searching");

        EasyBlog.ajax("site/views/search/search", {query: keyword})
            .done(function(html) {

                // Remove the searching class
                self.wrapper().removeClass("searching");

                if (html.length == 0) {
                    self.wrapper().addClass('empty');
                    return;
                }

                // Append the result
                self.result().append(html);
            })
            .fail(function() {

            });

    }, 500),

    insertPostLink: function() {
        //
        // EasyBlog.Composer.insertContent(link);
    },

    hideViewport: function() {
        composer.views.hide("posts");
    },

    "{searchToggleButton} click": function(el, event) {
        self.posts().addClass('is-searching');
    },

    "{searchCancelButton} click": function(cancelButton, event) {
        self.posts().removeClass('is-searching');
    },

    "{toggleSearch} click": function(el, event) {
        self.searchPanel().toggleClass('active');
    },

    "{hideSearch} click": function(el, event) {

        // Reset the states
        self.searchPanel().toggleClass('active');
        self.wrapper().removeClass('empty');

        // Reset the textbox
        self.searchTextfield().val('');

        // Restore the initial result when search is cancelled.
        self.result().html(self.initialResult);
    },

    "{insertLinkButton} click": function(el, event) {

        // Determines if this is on the legacy editor or not
        var doctype = EasyBlog.Composer.getDoctype();
        var title = el.data('title');
        var url = el.data('permalink');
        var image = el.data('image');
        var content = el.data('content');

        if (doctype == 'ebd') {

            // Construct a new post block and insert into the document
            var block = blocks.constructBlock('post', {
                'title': title,
                'url': url,
                'image': image,
                'intro': content
            });

            blocks.addBlock(block);

        } else {
            // Perform normal insertion
            link = $.create("a").attr({
                        href: url, title: title
                    }).html(title);

            // console.log(EasyBlog.Composer);

            // EasyBlog.Composer.insertContent(link.toHTML());
            EasyBlog.LegacyEditor.insert(link.toHTML());
        }

        // Hide the viewport now
        self.hideViewport();
    },

    "{postTitle} click": function(el, event) {

        var title = $(el).data('title'),
            value = $(el).data('permalink');

        self.insert(value, title);
    },

    "{composer} composerViewShow": function(base, event, id) {

        if (id !== "posts" || self.loaded) {
            return;
        }

        // Add loading indicator
        self.posts().addClass('is-loading');

        EasyBlog.ajax("site/views/composer/listArticles",{
            "exclude": composer.getPostId()
        }).done(function(html) {

            // Remove loading
            self.posts().removeClass('is-loading');

            self.loaded = true;

            if (html.length == 0) {
                self.posts().addClass('is-empty');
                return;
            }

            // Add is ready class
            self.posts().addClass('is-ready');

            // Append the result
            self.initialResult = html;

            // Append the html codes on the result
            self.result().append(html);
        })
        .fail(function(){

        });
    },

    "{composer} sidebarDeactivate": function(base, event, id) {

        if (id!=="posts") {
            return;
        }
    }
}});

module.resolve();

});


EasyBlog.module("composer/tags", function($) {

    var module = this;

    // Constants
    var KEYCODE = {
        BACKSPACE: 8,
        COMMA: 188,
        DELETE: 46,
        DOWN: 40,
        ENTER: 13,
        ESCAPE: 27,
        LEFT: 37,
        RIGHT: 39,
        SPACE: 32,
        TAB: 9,
        UP: 38
    };

    var DIRECTIONS = {
        38: "up",
        40: "down",
        37: "left",
        39: "right"
    }

    EasyBlog.require()
    .library(
        "textboxlist",
        "nearest",
        "scrollTo"
    ).done(function() {

        EasyBlog.Controller("Composer.Tags", {
            elements: [
                "[data-eb-composer-tags-{textboxlist}]"
            ],

            defaultOptions: {

                // Templates
                templates: {},
                "{tagTemplate}": "[data-tag-template]",

                pagination: 30,

                "{tags}": "[data-eb-composer-tags]",
                "{tag}": ".textboxlist-item",
                "{total}": "[data-eb-composer-tags-total]",
                "{count}": "[data-eb-composer-tags-count]",

                "{suggestions}": ".eb-composer-tags-suggestions",
                "{selection}": ".eb-composer-tags-selection",
                "{toggleButton}": "[data-eb-composer-tags-toggle-button]",
                "{autofillButton}": "[data-eb-composer-tags-autofill-button]",

                "{itemgroup}": ".eb-composer-tags-selection-itemgroup",
                "{item}": ".eb-composer-tags-selection-itemgroup > div",

                "{textfield}": "[data-textboxlist-textField]",

                "{jsondata}": "[data-eb-composer-tags-jsondata]"
            }
        }, function(self, opts, base, suggestions, selection, tagger) {

            return {

                init: function() {

                    if (EasyBlog.Composer.options.tags.enabled == "0") {
                        return;
                    }

                    // Get the tag template
                    // opts.templates.tag = self.tagTemplate().detach().html();
                    $.template("composer/textboxlist/item", self.tagTemplate().detach().html());

                    // Globals
                    selection = self.selection;
                    suggestions = self.suggestions;

                    // Get max count
                    var max = parseInt(self.tags().attr("data-eb-composer-tags-max")) || null;

                    // Implement textboxlist
                    self.textboxlist()
                        .textboxlist({
                            component: "eb",
                            max: max,
                            ignoreLocked: true,
                            view: {
                                item: 'composer/textboxlist/item'
                            },
                            filterItem: function(item) {

                                if ($.isString(item)) {
                                    return tagger.sanitize(item);
                                }

                                item.title = tagger.sanitize(item.title);
                                return item;
                            }
                        });

                    // Extend tagger with additional methods
                    tagger = self.tagger = $.extend(self.textboxlist().textboxlist("controller"), self.tagger);

                    // Get all tags quietly
                    suggestions.populate("", true);

                    var i = 0;
                    var existingTags = JSON.parse(self.jsondata().val());

                    while (existingTag = existingTags[i++]) {

                        tagger.addItem({
                            id: existingTag.id,
                            title: existingTag.title,
                            key: tagger.getItemKey(existingTag.title),
                            locked: false
                        });
                    }

                    window.tagz = self;
                },

                tagger: {

                    sanitize: function(title) {
                        // Remove whitespace and comma
                        return $.trim(title).replace(/,/g,"");
                    },

                    addLockedItem: function(title) {

                        // Sanitize the tag's title to ensure no trailing spaces
                        title = $.trim(title);

                        var existingTag = tagger.get(title);
                        var userAssignedTag = existingTag && !existingTag.locked;

                        // Remove from list
                        if (existingTag) {
                            tagger.deleteItem(existingTag.id);
                        }

                        tagger.addItem({
                            id: $.uid("item-"),
                            title: title,
                            key: tagger.getItemKey(title),
                            locked: true,
                            assigned: userAssignedTag
                        }, true);
                    }
                },

                "{textfield} keydown": function(textfield, event) {

                    var key = event.which,
                        activeItem = selection.activeItem;

                    switch (key) {

                        case KEYCODE.UP:
                        case KEYCODE.DOWN:
                        case KEYCODE.LEFT:
                        case KEYCODE.RIGHT:

                            var direction = DIRECTIONS[key];

                            // If there is an active item
                            if (activeItem.length > 0 || direction=="down") {

                                // Navigate through the suggestions
                                suggestions.navigate(direction);

                                // Prevent cursor from moving in textfield
                                event.preventDefault();
                            }
                            break;

                        case KEYCODE.ESCAPE:

                            // If there is an active item
                            if (activeItem.length > 0) {

                                // Deactivate the active item
                                selection.deactivate(activeItem);

                            // If suggestions are showing
                            } else if (self.suggestions().hasClass("is-showing")) {

                                // Collapse suggestions
                                suggestions.collapse();

                            // Else clear off textfield
                            } else {
                                textfield.val("");
                            }
                            break;

                        case KEYCODE.ENTER:

                            // If there is an active item
                            if (activeItem.length > 0) {

                                // Use the active item
                                selection.use(activeItem);

                                // Stop any further processing from textboxlist
                                event.preventDefault();
                            }
                            break;

                        default:
                            // Populate suggestions from textfield
                            suggestions.populateFromTextfield();
                            break;
                    }
                },

                "{item} mouseover": function(item) {

                    selection.deactivate();

                    selection.activate(item);
                },

                "{item} mouseout": function(item) {

                    selection.deactivate();

                },

                "{item} click": function(item) {

                    selection.use(item);
                },

                "{textboxlist} addItem": function(textboxlist, event, item) {

                    // Add is-used classname to selection item
                    $(selection.get(item.title)).addClass("is-used");
                },

                "{textboxlist} removeItem": function(textboxlist, event, item) {

                    // If this is an assigned tag
                    if (item.assigned) {

                        // Add this back as a regular item
                        tagger.addItem(item.title);

                    // Else show it back on tag suggestions
                    } else {
                        $(selection.get(item.title)).removeClass("is-used");
                    }
                },

                // Slightly debounced so mass removal only gets executed once
                "{textboxlist} listChange": $.debounce(function() {

                    // Refresh suggestions
                    suggestions.refresh();

                    // Update count
                    var count = self.tagger.getAddedItems().length;
                    self.count().text(count);
                }, 15),

                "{self} categoryselect": function(el, event, category) {

                    if (category.tags.length <= 0) {
                        return;
                    }

                    var tags = (category.tags || "").split(",");

                    $.each(tags, function(i, title){
                        tagger.addLockedItem(title);
                    });
                },

                "{self} categorydeselect": function(el, event, category) {

                    if (category.tags.length <= 0) {
                        return;
                    }

                    var categories = self.composer.category.toArray();
                    var tagsToRemove = (category.tags || '').split(',');
                    var tagsToRetain = $.pluck(categories, 'tags').join(',').split(',');
                    var tags = $.without.apply(null, [tagsToRemove].concat(tagsToRetain));

                    $.each(tags, function(i, title){
                        var tag = tagger.get(title);
                        tag && tagger.removeItem(tag.id);
                    });
                },

                "{toggleButton} click": function(toggleButton) {

                    suggestions[self.suggestions().hasClass("is-showing") ? "collapse" : "expand"]();
                },

                "{autofillButton} click": function(autofillButton) {
                    var content = self.composer.document.getText(),
                        parent = $(autofillButton).parent();

                    // Show loading on parent
                    $(parent).addClass('is-loading');

                    EasyBlog.ajax('site/views/composer/suggestKeywords', {
                        "data": content
                    }).done(function(keywords){

                        // Remove loading class
                        $(parent).removeClass('is-loading');

                        if (keywords) {
                            $.each(keywords, function(i, tag) {
                                tagger.addItem(tag.title);
                            });
                        }
                    });
                },

                suggestions: {

                    index: 0,

                    tags: [],

                    expand: function() {

                        suggestions.tick++;

                        self.suggestions()
                            .addClass("is-showing");
                    },

                    collapse: function() {

                        suggestions.tick++;

                        self.suggestions()
                            .removeClass("is-showing");
                    },

                    refresh: function() {

                        // See if there are available selection items
                        var items = self.item(":not(.is-used)");

                        // Show empty hint if there are no items
                        self.suggestions()
                            .toggleClass("is-empty", items.length < 1);

                        suggestions.count();
                    },

                    show: function() {

                        suggestions.tick++;

                        suggestions.refresh();

                        // Expand suggestions
                        suggestions.expand();
                    },

                    hide: function() {

                        var tick = ++suggestions.tick;

                        // Collapse suggestions
                        suggestions.collapse();

                        setTimeout(function(){

                            if (tick!==suggestions.tick) return;

                            // Reset suggestions
                            suggestions.reset();
                        }, 500);
                    },

                    set: function(tags) {

                        if (tags.length < 1) {

                            suggestions.hide();

                            suggestions.tags = [];

                        } else {

                            // Reset suggestions
                            suggestions.reset();

                            // Assign new tags dataset
                            suggestions.tags = tags;
                        }
                    },

                    reset: function() {

                        // Clear tags
                        suggestions.tags = [];

                        // Reset index
                        suggestions.i = 0;

                        // Clear out selection items
                        self.itemgroup().empty();
                    },

                    pid: 0,

                    tick: 0,

                    currentQuery: null,

                    query: $.memoize(function(keyword) {

                        return EasyBlog.ajax(
                            "site/views/tags/suggest",
                            {
                                search: keyword
                            })
                            .fail(function(){
                                suggestions.query.reset(keyword);
                            });
                    }),

                    populate: function(keyword, quiet) {

                        suggestions.tick++;

                        var pid = ++suggestions.pid;

                        // If there is an active query, abort it.
                        suggestions.currentQuery && suggestions.currentQuery.abort();

                        // Santiize keyword
                        keyword = tagger.sanitize(keyword);

                        // Show loading indicator
                        self.suggestions()
                            .addClass("is-busy");

                        // Get tags
                        suggestions.currentQuery =
                            suggestions.query(keyword)
                                .done(function(tags){

                                    // If this populate task has expired, stop.
                                    if (suggestions.pid!==pid) return;

                                    // Set this as new tag dataset
                                    suggestions.set(tags);

                                    // Show tags
                                    suggestions.suggest(quiet);
                                })
                                .fail(function(){

                                    // If this populate task has expired, stop.
                                    if (suggestions.pid!==pid) return;
                                })
                                .always(function(){

                                    // Remove loading indicator
                                    self.suggestions()
                                        .removeClass("is-busy");

                                    suggestions.currentQuery = null;
                                });
                    },

                    populateTimer: null,

                    populateFromTextfield: function() {

                        suggestions.tick++;

                        clearTimeout(suggestions.populateTimer);

                        setTimeout(function(){

                            var textfield = self.textfield(),
                                keyword   = tagger.sanitize(textfield.val());

                            // If there are no keywords
                            if (keyword==="") {

                                // Hide suggestions
                                suggestions.hide();

                                // Quietly populate all keywords
                                suggestions.populate("", true);

                            } else {

                                // If this query has been made before
                                if (suggestions.query.cache.hasOwnProperty(keyword)) {

                                    // Populate immediately
                                    suggestions.populate(keyword);

                                // Else populate after 250ms delay
                                // to ensure user has finished typing
                                } else {

                                    suggestions.populateTimer =
                                        setTimeout(function(){
                                            suggestions.populate(keyword);
                                        }, 250);
                                }
                            }
                        }, 1);
                    },

                    suggest: function(silent) {

                        // Get current dataset
                        var tags = suggestions.tags;

                        // If there are no tags
                        if (tags.length < 1) return;

                        var count = 0,
                            max = opts.pagination,
                            tag, title, items = [], item;

                        // Fill up suggestions until it reach
                        // the max amount tags per pagination.
                        while (count < max) {

                            // Get tag from current index
                            tag = tags[suggestions.i];

                            // Stop if there are no more tags
                            if (!tag) break;

                            // Get tag title
                            title = tag.title;

                            // Get or create item
                            item = selection.get(title) || selection.create(title);

                            // Get selection item and put it in an array
                            items.push(item);

                            // If this tag has been used
                            if (tagger.get(title)) {

                                // Add is-used class
                                $(item).addClass("is-used");

                            // Only increase count if the tag hasn't been used
                            } else {
                                count++;
                            }

                            // Go to next index
                            suggestions.i++;
                        }

                        // Append selection items to itemgroup
                        self.itemgroup()
                            .append(items);

                        if (silent) {
                            suggestions.refresh();
                        } else {
                            suggestions.show();
                        }
                    },

                    navigate: function(direction) {

                        // Get all rendered items
                        var items = self.item(),
                            itemgroup = self.itemgroup(),
                            activeItem = selection.activeItem,
                            nearestItem =
                                // Find nearest item from the current active item
                                // or from the textfield if there are no active item
                                $(activeItem[0] || self.textfield()[0])
                                    .nearest(items, direction);

                        // If a nearest item was found
                        if (nearestItem.length > 0) {

                            // Activate nearest item
                            selection.activate(nearestItem);

                            // Scroll to nearest item
                            // TODO: Polish quirky scrollIntoView
                            itemgroup.scrollIntoView(nearestItem);

                        // If a nearest item was not found
                        } else if (activeItem.length > 0) {


                            // If we are at the end of the list
                            if (direction=="down") {

                                // Load more suggestions
                                suggestions.suggest();

                                // Scroll down
                                // TODO: Polish quirky scrollIntoView
                                itemgroup.scrollIntoView(activeItem);
                            }

                            //  If we are at the top of the list
                            if (direction=="up") {

                                // Focus back on textboxlist
                                selection.deactivate();
                            }
                        }
                    },

                    count: function() {

                        var allTags = $.pluck(suggestions.tags, "title"),
                            usedTags = $.keys(tagger.itemsByTitle),
                            availableTags = $.without.apply(null, [allTags].concat(usedTags)),
                            count = availableTags.length;

                        // Update count
                        self.total().text(count + "");
                    }
                },

                selection: {

                    items: {},

                    activeItem: $(),

                    create: function(title) {

                        var item = document.createElement("div");
                            item.innerHTML = title;

                        return selection.items[title] = item;
                    },

                    get: function(title) {

                        return selection.items[title];
                    },

                    activate: function(item) {

                        // Deactivate any current active item
                        selection.deactivate();

                        // Set this as the new active item
                        selection.activeItem = item.addClass("active");
                    },

                    deactivate: function(item) {

                        // Remove active class from this item
                        selection.activeItem.removeClass("active");

                        // No more active item
                        selection.activeItem = $();
                    },

                    use: function(item) {

                        var item = $(item),
                            title = item.text();

                        // Add item
                        var tag = tagger.addItem(title);

                        if (!tag) return;

                        // Refocus on the textfield
                        self.textfield().focus();

                        // If the item being added is an active item
                        if (item.is(selection.activeItem)) {

                            // Change the active item to the
                            // nearest candidate
                            nextItem = item.next(self.item)[0] || item.prev(self.item)[0];

                            selection.deactivate(item);

                            if (nextItem) {
                                selection.activate($(nextItem));
                            }
                        }

                        suggestions.refresh();
                    }
                },

                "{self} composerSave": function(base, event, save) {

                    if (EasyBlog.Composer.options.tags.enabled == "0") {
                        return;
                    }
                    
                    // Get all added tags
                    var tags = self.tagger.getAddedItems();

                    // Add array of tags to save data
                    save.data.tags = $.pluck(tags, 'title').join(",");
                }

           }
        });

        module.resolve();
    });

});

EasyBlog.module("composer/templates", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Templates", {

        defaultOptions: {

            // Actions
            "{updateTemplate}": "[data-eb-composer-update-template-button]",
            "{templateId}": "[data-eb-composer-template-id]",
            "{saveNewTemplate}": "[data-eb-composer-save-template-button]",

            // Templates listings
            "{selectTemplate}": "[data-template-item]",
            "{templateBlocks}": "[data-template-blocks]",
            "{deleteTemplate}": "[data-template-delete]",

            "{posts}": "[data-eb-composer-posts]",
            "{searchTextfield}": "[data-eb-composer-posts-search-textfield]",
            "{itemGroup}": "[data-eb-composer-posts-item-group]",

            // List items
            "{list}": "[data-eb-composer-templates-list]"
        }
    },
    function(self, opts, base, composer) { return {

        init: function() {
            composer = self.composer;
        },

        //
        // When a template is selected, we need to update the manager accordingly.
        //
        selectedTemplate: function(templateId) {
            // If the user selected a proper template, we need to update the frame with the appropriate class
            composer.manager().addClass('is-editing-template');
            self.templateId().val(templateId);
        },

        "{selectTemplate} click": function(el) {

            // Get the template that the user clicked
            var uid = el.data('uid');
            var isBlank = el.data('blank');

            // If this is a blank template, don't do anything
            if (isBlank) {
                composer.frame().removeClass('show-templates');

                // Need to kick in auto save in blank template 
                composer.autosave.start();

                return;
            }

            EasyBlog.ajax('site/views/composer/renderTemplate', {
                "uid" : uid
            }).done(function(title, permalink, html) {

                // If the user selected a proper template, we need to update the frame with the appropriate class
                self.selectedTemplate(uid);

                // Need to kick in auto save in other template 
                composer.autosave.start();

                // Append the blocks html
                if (html) {
                    composer.document.loadDocument(html);
                }

                // Hide the templates browser
                composer.frame().removeClass('show-templates');

                // Trigger so that others can know what to do when template is selected
                self.trigger('composerSelectTemplate', [uid, title, permalink, html]);
            });
        },

        "{deleteTemplate} click": function(button, event) {
            // Prevent the template from being selected when the button is clicked
            event.stopPropagation();

            // Get the template id
            var parent = button.parents(self.selectTemplate.selector);
            var uid = parent.data('uid');

            // Display a dialog for confirmation
            EasyBlog.dialog({
                content: EasyBlog.ajax('site/views/templates/confirmDelete', {"id": uid, "deleteAction": "ajax"}),
                bindings: {
                    "{submitButton} click" : function() {
                        EasyBlog.ajax('site/controllers/templates/delete', {
                            "ids": [uid]
                        }).done(function() {
                            // Upon deleting the template, hide the dialog
                            EasyBlog.dialog().close();

                            // Remove the parent item
                            parent.remove();
                        });
                    }
                }
            });
        },

        save: function(options) {

            var save = $.Task();

            // Trigger the save
            composer.getSaveData(save.data);

            // Need to trigger composerSave because the document.js is relying on this trigger to decorate the save data
            self.trigger("composerSave", [save, composer]);

            // If there's a template id, we need to set it here
            if (options.template_id) {
                $.extend(save.data, {'template_id': options.template_id});
            }

            if (options.title) {
                $.extend(save.data, {'template_title' : options.title});
            }

            if (options.system) {
                $.extend(save.data, {'system': options.system});
            }

            save.process()
                .done(function(){

                    EasyBlog.ajax('site/views/templates/save', save.data)
                        .done(function(exception, template) {

                            // If the user selected a proper template, we need to update the frame with the appropriate class
                            self.selectedTemplate(template.id);

                            self.trigger("composerSave", [save, composer, template]);

                            composer.setMessage(exception);
                        });
                });
        },

        "{updateTemplate} click": function(el) {
            var id = self.templateId().val();

            self.save({
                'template_id': id
            });
        },

        "{saveNewTemplate} click": function() {

            EasyBlog.dialog({
                'content': EasyBlog.ajax('site/views/templates/saveTemplateDialog'),
                'bindings': {
                    "{submitButton} click": function() {
                        var systemTemplate = this.system().is(':checked') ? 1 : 0;

                        self.save({
                            'template_id': '0',
                            "title": this.title().val(),
                            "system": systemTemplate
                        });

                        EasyBlog.dialog().close();
                    }
                }
            });
        },

        "{composer} composerSidebarActivate": function(base, event, id) {

            if (id !== 'templates') {
                return;
            }

            EasyBlog.ajax('site/views/templates/listTemplates', {})
                .done(function(templates) {

                    if (templates.length == 0) {
                        self.posts().addClass('empty');
                        return;
                    }

                    // Insert the templates to the list
                    self.insertTemplates(templates);
                })
                .fail(function(){

                });
        },

        // Triggered when a save template occurs so that we can inject the item template into the templates list
        "{composer} composerSaveTemplate": function(el, event, save, composer, template) {
        },

        "{composer} sidebarDeactivate": function(base, event, id) {

            if (id!=="templates") {
                return;
            }
        }

    }});

    module.resolve();

});

EasyBlog.module("composer", function($){

var module = this;

// Document selectors
var EBD = window["EBD"] = {};
EBD.root                 = ".ebd",
EBD.block                = ".ebd-block" + ":not(is-helper)" + ":not(.is-placeholder)" + ":not(.is-dropzone)";
EBD.childBlock           = "> " + EBD.block;
EBD.nest                 = ".ebd-nest",
EBD.nestedBlock          = EBD.block + ".is-nested";
EBD.immediateNestedBlock = EBD.nestedBlock + ":not(" + EBD.nest + " " + EBD.nest + " " + EBD.nestedBlock + ")";
EBD.standaloneBlock      = EBD.block + ".is-standalone";
EBD.isolatedBlock        = EBD.block + ".is-isolated";

// Workarea Selectors
EBD.workarea = "[data-ebd-workarea]";
EBD.dropzone = "[data-ebd-dropzone]";
EBD.blockToolbar = "[data-ebd-block-toolbar]";
EBD.blockSortHandle = "[data-ebd-block-sort-handle]";
EBD.blockViewport = "[data-ebd-block-viewport]";
EBD.blockContent = "[data-ebd-block-content]";
EBD.immediateBlockSortHandle = "> " + EBD.blockToolbar + " > " + EBD.blockSortHandle;
EBD.immediateBlockViewport = "> " + EBD.blockViewport;
EBD.immediateBlockContent = EBD.immediateBlockViewport + "> " + EBD.blockContent;
EBD.editableContent = EBD.block + ".is-editable [contenteditable=true]";

EBD.selectors = {
    "{workarea}": EBD.workarea,
    "{root}"    : EBD.workarea + " " + EBD.root,
    "{nest}"    : EBD.workarea + " " + EBD.root + " " + EBD.nest,
    "{block}"   : EBD.workarea + " " + EBD.root + " " + EBD.block,
    "{dropzone}": EBD.workarea + " " + EBD.root + " " + EBD.dropzone

};

// Post states
var POST_BLANK       = 9,
    POST_DRAFT       = 3,
    POST_PENDING     = 4,
    POST_PUBLISHED   = 1,
    POST_SCHEDULED   = 2,
    POST_UNPUBLISHED = 0;

EasyBlog.require()
.library(
    "scrolly",
    "history"
)
.script(
    "composer/debugger",

    "composer/document",
    "composer/blocks",
    "composer/category",
    "composer/tags",
    "composer/revisions",

    // Sidebar
    "composer/media",
    "composer/posts",
    "composer/templates",

    // Artboard
    "composer/blogimage",
    "composer/location",

    // Panels
    "composer/panels",
    "composer/panels/post",
    "composer/panels/authorship",
    "composer/panels/category",
    "composer/panels/seo",
    "composer/panels/autopost"
)
.done(function(){

    EasyBlog.Controller("Composer", {
        hostname: "composer",
        pluginExtendsInstance: true,
        elements: [
            "[data-eb-composer-{frame|manager|actions|form|ghosts|alerts|saving-redirect-message|saving-message|saving-info-message|saving-progress-bar|saving-close-button|saving-entry-button|apply-post-button|publish-post-button|update-post-button|submit-post-button|reject-post-button|approve-post-button|preview-post-button|save-post-button|unpublish-post-button|delete-post-button|published-field}]",
            "[data-eb-composer-{views|view|viewport|viewport-content}]",
            "[data-eb-composer-{autosave|autosave-message}]",
            "[data-eb-{alert-template}]",
            "[data-eb-composer-toolbar-{messages}]",
            "[data-eb-composer-{close-message}]"
        ],

        defaultOptions: {

            templates: {},

            // Basic post attributes
            postUid: null,

            // Determines the current author id
            authorId: null,

            "{retryButton}": "[data-eb-composer-instance-entry-button]"
        }
    },
    function(self, opts, base, frame) { return {

        saveOptions: {
            autosave: false,
            showSaveMessage: true,
            updateRevisionStatus: true,
            updateAddressBar: true
        },

        init: function() {

            // Tell parent launcher that we're almost ready
            // so loading indicator can go away. Playing
            // tricks with user perceived performance.
            // Using try..catch because it is less work.
            try {
                window.parent.EasyBlog.ComposerLauncher.ready();
            } catch(e) {};

            // Prevent user from going to another page
            $(window).on('beforeunload', function(event) {
                event.preventDefault();
                return false;
            });

            // Detach the alert template
            opts.templates.alert = self.alertTemplate().detach().html();

            // Get frame
            frame = self.frame();

            // Get the author id
            opts.authorId = frame.data('author-id');

            // Disable scrollbar on body
            base.noscroll();

            // Prevent browser from remember last scroll position
            // self.desktop()[0].scrollTop = 0;

            // Expose Composer
            EasyBlog.Composer = self;

            // Extend options with options from inline data attributes
            $.extend(opts, frame.htmlData("eb-composer"));

            // Install plugins
            self.installPlugins([
                "debugger",
                "media",
                "templates",
                "blocks",
                "posts",
                "panels",
                "document",
                "blogimage",
                "location",
                "artboard",
                "tags",
                "category",
                "revisions"
            ]);

            // Misc

            // Start the keepAlive if it's currently not displaying post templates.
            if (opts.keepalive.enabled == 1 && !self.frame().hasClass('show-templates')) {
                self.keepalive.start();
            }

            // Start the auto save if it's currently not displaying post templates.
            if (opts.autosave.enabled == 1 && !self.frame().hasClass('show-templates')) {
                self.autosave.start();
            }

            // Debug when EasyBlog.debug is on.
            EasyBlog.debug && self.debugger.activate();

            // Trigger composerReady event
            self.trigger("composerReady");
        },

        "{self} composerDocumentReady": function() {

            self.frame()
                .removeClass("is-loading");
        },

        installPlugins: function(plugins) {

            $.each(plugins, function(i, plugin){
                self.addPlugin(plugin);
            });
        },

        settings: {

            get: function(key) {

                return base.find("input[name='" + key + "']").val();
            },

            set: function(key, val) {

                base.find("input[name='" + key + "']").val(val);

                self.trigger("composerSettingsChange", [key, val]);

                return val;
            }
        },

        getPostId: function() {
            return frame.attr("data-post-id");
        },

        getPostUid: function() {
            return frame.attr("data-post-uid");
        },

        getRevisionId: function() {
            return self.getPostUid().split(".")[1];
        },

        getDoctype: function() {
            return frame.attr("data-post-doctype");
        },

        //
        // Views
        //
        views: {

            show: function(name) {

                frame.switchClass("view-" + name);

                self.view()
                    .removeClass("active")
                    .where("name", name)
                    .addClass("active");

                // Monkey fix
                if (name=="revisions") {
                    self.manager().removeClass("has-messages");
                }

                self.trigger("composerViewShow", [name]);
            },

            hide: function(name) {

                self.trigger("composerViewHide", [name]);

                // Revert to document view
                self.views.show("document");
            },
        },

        //
        // Keep Alive
        //
        keepalive: {

            timer: null,

            start: function(interval) {

                var keepalive = self.keepalive;
                var interval = interval || opts.keepalive.interval;

                // Stop existing timer
                keepalive.stop();

                // If interval is 0, don't run keepalive.
                if (interval < 1) {
                    return;
                }

                // Start new timer
                // keepalive.timer = $.delay(function(){
                //     keepalive.run(interval);
                // }, interval);
                //
                keepalive.timer = setTimeout(function() { keepalive.run(interval); }, interval);
            },

            run: function(interval) {

                EasyBlog.ajax('site/views/composer/keepAlive')
                    .always(function() {
                        self.keepalive.start(interval);
                    });
            },

            stop: function() {
                clearTimeout(self.keepalive.timer);
            }
        },

        //
        // Autosave
        //
        autosave: {

            timer: null,
            counter: 0,

            start: function(interval) {

                var autosave = self.autosave;
                var interval = interval || opts.autosave.interval;

                // Stop existing timer
                autosave.stop();

                // If interval is 0, don't run autosave
                if (interval < 1 || !interval) {
                    return;
                }
                // Start new timer
                // autosave.timer = $.delay(function(){
                //     autosave.run(interval);
                // }, interval);

                autosave.timer = setTimeout(function() { autosave.run(interval); }, interval);
            },

            run: function(interval) {

                // Autosave
                self.autosave.save();

                // Restart the autosave checking
                self.autosave.start(interval);
            },

            stop: function() {
                clearTimeout(self.autosave.timer);
            },

            save: function() {

                if (self.saving) {
                    return;
                }

                // We need to set the state to draft if this is executed the first time
                if (self.autosave.counter == 0) {
                    self.publishedField().val(POST_DRAFT);
                }

                // Increment the counter
                self.autosave.counter++;

                self.save({
                    autosave: true,
                    showSaveMessage: false
                }).done(function(data, exception) {

                    // Only display message if the state is success
                    if (exception.code == 200) {
                        // We should be running the save differently otherwise it would obstruct the user experience
                        // if we imitate the save for later button.
                        self.autosave().removeClass('hide');

                        // Update the autosave message.
                        self.autosaveMessage().html(exception.message);
                    }

                });
            }
        },

        validate: function() {

            var validator = $.Task();

            // Trigger composerValidate event
            self.trigger("composerValidate", [validator]);

            validator.process()
                .done(function(){
                })
                .fail(function(){
                    var taskList = validator.list;
                    var exceptions = [];

                    $.each(taskList, function(i, task) {

                        if (task.state()=="rejected") {
                            task.fail(function(exception){
                                exceptions.push(exception);
                            });
                        }
                    });

                    self.setMessage(exceptions);
                });

            return validator;
        },

        "{self} composerValidate": function(el, event, validator) {

            // // Resolve the validator
            // validator.resolve();

            // return validator;
        },

        saving: false,

        getSaveData: function(saveData) {

            // Composer scans through every form element
            // with data-eb-composer-form attribute on it,
            // serializes the form into an object, and then
            // merge all the objects into save data.
            self.form().each(function(){
                var data = $(this).serializeObject();

                $.extend(saveData, data);
            });

        },

        save: function(options) {

            if (self.saving) {
                return;
            }

            // return;

            self.saving = true;

            var fakeAjax = $.Deferred();

            base.addClass("is-saving");

            // Add saving class on the manager
            // self.manager().addClass('is-saving');

            // Run validation first
            self.validate()
                .done(function(){

                    options = $.extend(self.saveOptions, options);

                    var save = $.Task();

                    if (options.autosave) {
                        save.data.autosave = options.autosave;
                    } else {
                        save.data.autosave = 0;
                    }

                    self.initSaving(options.autosave);
                    self.updateProgressBar('15');

                    if (options.isapply) {
                        save.data.isapply = options.isapply;
                    } else {
                        save.data.isapply = 0;
                        options.updateAddressBar = 0;
                    }

                    // Get the save data
                    self.getSaveData(save.data);

                    // Trigger composerSave event
                    // Any handler that is listening to this event
                    // should decorate the save data or create a
                    // save task if it needs more time to decorate
                    // save data, e.g. if an image is still uploading
                    // but user already clicked save.
                    self.trigger("composerSave", [save, self]);

                    self.updateProgressBar('25');

                    save.process()
                        .done(function(){

                            self.updateProgressBar('35');

                            EasyBlog.ajax("site/controllers/posts/save", save.data)
                                .done(function(data, exception, revisionHTML, editLink){

                                    self.updateProgressBar('65');

                                    // Set the message
                                    if (!options.isapply && options.showSaveMessage) {
                                        self.setMessage(exception);
                                    }

                                    // Trigger the success state to everyone
                                    self.trigger("composerSaveSuccess", data);

                                    // Update the revision html codes
                                    if (options.updateRevisionStatus) {

                                        var revisionsFieldset = base.find('[data-eb-revisions-fieldset]');

                                        revisionsFieldset.children().html(revisionHTML);

                                        composer.revisions.preventParentScrolling();
                                    }

                                    // Update the address bar url so that if the user refreshes the page, the contents stay intact
                                    if (options.isapply && options.updateAddressBar) {
                                        History.pushState({state:1}, "", editLink);
                                    }

                                    // Update the state of the document so that it shows "update instead"
                                    if (self.publishedField().val() == POST_PUBLISHED) {
                                        self.manager()
                                            .removeClass('revision-draft')
                                            .addClass('revision-finalized');
                                    }

                                    self.updateProgressBar('85');

                                    // Quick hack
                                    base.find(".eb-composer-actions input[name=uid]").val(data.uid);
                                    base.find(".eb-composer-actions input[name=revision_id]").val(data.revision_id);
                                    frame.attr("data-post-uid", data.uid);

                                    self.doneSaving(options.isapply, options.autosave, exception.message);

                                })
                                .fail(function(exception){

                                    self.manager().removeClass('is-saving is-auto-saving');

                                    self.setMessage(exception);
                                    self.trigger("composerSaveError", [exception]);
                                })
                                .always(function(){

                                    // Remove saving class on the manager
                                    // self.manager().removeClass('is-saving');

                                    self.saving = false;
                                })
                                .done(fakeAjax.resolve)
                                .fail(fakeAjax.reject);
                        })
                        .fail(function(){

                            self.manager().removeClass('is-saving is-auto-saving');

                            self.trigger("composerSaveError");
                            self.saving = false;
                        })
                        .always(function(){
                            base.removeClass("is-saving is-auto-saving");
                        })
                })
                .fail(function() {
                    self.saving = false;
                    base.removeClass("is-saving");
                });

            return fakeAjax;
        },

        trash: function() {

            EasyBlog.dialog({
                content: EasyBlog.ajax('site/views/composer/confirmDelete', {"uid" : self.getPostUid()})
            });
        },

        setMessage: function(exceptions) {

            // Normalize arguments
            // Also accept array of exceptions
            if (!$.isArray(exceptions)) {
                exceptions = [exceptions];
            }


            $.each(exceptions, function(i, exception){

                // Show the messages toolbar set
                self.manager()
                    .addClass('has-messages');

                // Get the toolbar
                var color = 'green';

                /error|danger/.test(exception.type) && (color = "red");
                /success/.test(exception.type) && (color = "green");

                self.messages()
                    .switchClass('style-' + color);

                self.messages()
                    .find('[data-message]')
                    .html(exception.message);
            });
        },

        "{closeMessage} click": function() {
            self.manager()
                .removeClass("has-messages");
        },

        "{previewPostButton} click": function() {

            var curPublishState = self.publishedField().val();

            if (curPublishState != POST_PENDING) {
                // We need to save the post first to ensure that their contents are up to date.
                self.publishedField()
                    .val(POST_DRAFT);
            }

            // console.log(self.publishedField().val());
            // return;

            self.save({autosave: 0,isapply: 1})
                .done(function(data, exception, revisionHTML, editLink, previewLink){
                    window.open(previewLink);
                })
                .always(function() {

                });
        },

        "{savePostButton} click": function(saveButton) {

            var curPublishState = self.publishedField().val();

            if (curPublishState != POST_PENDING) {
                // We need to set the state to "draft"
                self.publishedField().val(POST_DRAFT);
            }

            saveButton.addClass('is-saving');

            self.save({autosave: 1, isapply: 0})
                .done(function(){

                })
                .always(function(){
                    saveButton.removeClass('is-saving');
                });
        },

        "{submitPostButton} click": function() {

            self.disableLeavePrompt();

            self.publishedField().val(POST_PENDING);
            self.save({autosave: 0, isapply: 0});
        },

        "{approvePostButton} click": function(approveButton) {
            self.publishedField().val(POST_PUBLISHED);

            approveButton.addClass('is-saving');

            self.disableLeavePrompt();

            self.save({autosave: 0,isapply: 0})
                .done(function() {

                })
                .always(function() {
                    approveButton.removeClass('is-saving');
                })
        },

        "{rejectPostButton} click": function(rejectButton) {

            // rejecting this post and set the published back to draft so that user will have to edit the post again.
            self.publishedField().val(POST_DRAFT);

            self.disableLeavePrompt();

            self.save({autosave: 0,isapply: 0})
                .done(function() {

                });
        },

        "{publishPostButton} click": function(publishButton) {
            self.publishedField().val(POST_PUBLISHED);

            publishButton.addClass('is-saving');

            self.disableLeavePrompt();

            self.save({autosave: 0,isapply: 0})
                .done(function() {

                })
                .always(function() {
                    publishButton.removeClass('is-saving');
                });
        },

        "{applyPostButton} click": function(applyButton) {
            self.publishedField().val(POST_PUBLISHED);

            applyButton.addClass('is-saving');

            self.save({autosave: 0,isapply: 1})
                .done(function() {

                })
                .always(function() {
                    applyButton.removeClass('is-saving');
                });
        },

        "{updatePostButton} click": function(updateButton) {
            self.publishedField().val(POST_PUBLISHED);

            self.disableLeavePrompt();

            updateButton.addClass('is-saving');

            self.save({autosave: 0, isapply: 0})
                .done(function() {

                })
                .always(function() {
                    updateButton.removeClass('is-saving');
                })
        },

        "{unpublishPostButton} click": function() {
            self.publishedField().val(POST_UNPUBLISHED);

            self.save({autosave: 0, isapply: 0});
        },

        "{deletePostButton} click": function() {
            self.trash();
        },

        "{self} composerSelectTemplate": function(composer, event, templateId) {

            // Give a buffer of 5 seconds before starting autosave.
            setTimeout(function() {
                self.autosave.start();
            }, 5000);
        },

        "{savingEntryButton} click": function () {

            // unbind the window event so that it will not prompt user
            // to choose 'stay' or leave.
            self.disableLeavePrompt();


            // simulate the click event
            var url = self.savingEntryButton().attr('href');
            EasyBlog.ComposerLauncher.redirect(url);

        },

        "{savingCloseButton} click": function () {
            self.manager().removeClass("is-saving");
        },

        "initSaving": function(isAutoSave) {

            if (isAutoSave) {
                self.manager().addClass("is-auto-saving");

            } else {
                self.manager().removeClass("is-auto-saving");

                self.savingEntryButton().addClass('hide');
                self.savingCloseButton().addClass('hide');

                // remove progress bar
                self.savingProgressBar().removeClass('hide');

                //remove info message.
                self.savingInfoMessage().text('');
                self.savingInfoMessage().addClass('hide');

                self.savingMessage().removeClass('hide');
                self.manager().addClass("is-saving");
            }
        },

        "doneSaving": function(isapply, isautosave, message) {

            self.updateProgressBar('100');

            self.savingMessage().addClass('hide');

            self.savingInfoMessage().text(message);
            self.savingInfoMessage().removeClass('hide');

            if (isautosave) {
                self.manager().removeClass("is-auto-saving");
            } else {

                if (isapply) {

                    self.savingEntryButton().removeClass('hide');
                    self.savingCloseButton().removeClass('hide');

                    self.savingMessage().addClass('hide');
                } else {

                    self.disableLeavePrompt();

                    self.savingRedirectMessage().removeClass('hide');

                    // simulate the click event
                    var url = self.savingEntryButton().attr('href');
                    EasyBlog.ComposerLauncher.redirect(url);
                }

            }
        },

        disableLeavePrompt: function() {

            // unbind the window event so that it will not prompt user
            // to choose 'stay' or leave.
            $(window).off('beforeunload');

            // some Joomla editor has the saving prompt feature. lets try to disable it.
            $(window).unbind('beforeunload');

            // for tinymce - cheap hack
            window.onbeforeunload = function() {};
        },


        "updateProgressBar": function(percentage) {
            self.savingProgressBar().children('.progress-bar').css("width", percentage + "%")
        }

    }});

    $("body").addController("EasyBlog.Controller.Composer");

    module.resolve();
});

});

EasyBlog.module("composer/revisions", function($){

    var module = this;

    EasyBlog.Controller("Composer.Revisions", {

        defaultOptions: {

            "{revisionsFieldset}": "[data-eb-revisions-fieldset]",

            "{revisionToggle}"  : "[data-eb-revisions-dropdown-toggle]",
            "{revisionDropdown}"  : "[data-eb-pilot-dropdown]",
            "{revisionHandler}"  : "[data-eb-revisions-handler]",
            "{revisionList}": "[data-eb-revisions-list]",

            "{closeComparison}": "[data-revisions-close-comparison]",
            "{compareScreen}": "[data-eb-composer-revisions-compare-screen]",

            // Revision items
            "{item}": "[data-eb-composer-revisions-item]",
            "{compareRevision}": "[data-eb-composer-revisions-compare]",
            "{openRevision}": "[data-eb-composer-revisions-open]",
            "{useRevision}": "[data-eb-composer-revisions-use]",
            "{deleteRevision}": "[data-eb-composer-revisions-delete]",

            // revisions blocks
            "{revisionBlocks}" : ".eb-composer-revisions .ebd-block"
        }
    }, function(self, opts, base, composer, blocks, panels) { return {

        init: function() {
            composer = self.composer;
            panels = composer.panels;
            blocks = composer.blocks;

            self.preventParentScrolling();
        },

        preventParentScrolling: function() {

            self.revisionList()
                .on("mousewheel", function(event){
                    event.stopPropagation();
                });
        },

        "{item} click": function(item) {

            var hasActiveClass = item.hasClass("active");

            if (item.hasClass("is-current")) {
                return;
            }

            self.item().removeClass("active");

            item.toggleClass("active", !hasActiveClass);
        },

        revisionsLoaded: false,

        getRevisionItem: function(el) {
            var item = $(el).parents(self.item.selector);

            return item;
        },

        "{revisionBlocks} mouseover": function (el, ev) {

            var uid = $(el).data('uid');
            var block = blocks.getBlock(uid);

            blocks.highlight(block);
        },

        "{revisionBlocks} mouseout": function (el, ev) {

            var uid = $(el).data('uid');
            var block = blocks.getBlock(uid);

            blocks.unhighlight(block);
        },

        "{closeComparison} click": function(el, event) {

            composer.views.show("document");

            self.compareScreen().html('');
        },

        "{compareRevision} click": function(el, event) {

            composer.views.show("revisions");

            var item = self.getRevisionItem(el),
                targetRevision = $(item).data('id'),
                currentRevision = EasyBlog.Composer.getRevisionId();

            EasyBlog.ajax('site/views/revisions/compare', {
                "current": currentRevision,
                "target": targetRevision
            }).done(function(output){
                self.compareScreen().html(output);
            });
        },

        "{useRevision} click": function(el, event) {

            var item = self.getRevisionItem(el),
                id = $(item).data('id');

                EasyBlog.dialog({
                    content: EasyBlog.ajax('site/views/revisions/confirmUseRevision', {"uid": EasyBlog.Composer.getPostId() + '.' + id })
                });
        },

        "{deleteRevision} click": function(el, event) {
            var item = $(el).parents(self.item.selector),
                id = $(item).data('id');

            EasyBlog.dialog({
                content: EasyBlog.ajax('site/views/revisions/deleteRevision', {"id": id}),
                bindings: {

                    "{submitButton} click": function() {

                        EasyBlog.ajax('site/controllers/posts/deleteRevision', {
                            "id": id
                        }).done(function(){
                            // Remove the item from the list.
                            $(item).remove();

                            // Close the dialog
                            EasyBlog.dialog().close();
                        });
                    }
                }
            });
        },

        "{revisionToggle} click": function(element) {

            self.revisionsFieldset()
                .toggleClass("show-revision-list");


                EasyBlog.ajax('site/views/revisions/getRevisions', {
                    "uid" : EasyBlog.Composer.getPostUid()
                }).done(function(output) {
                    self.revisionsLoaded = true;
                    // TODO: add class is-loading
                    // self.revisionLoader().addClass('hide');

                    self.revisionList().html(output);
                });
        },

        "{composer} composerSaveSuccess": function() {

            // We want to re-initialize the listing again.
            self.revisionsLoaded = false;
        }
    }});

    module.resolve();

});

EasyBlog.module("composer/panels/category", function($){

    var module = this;

    EasyBlog.require()
    .done(function($) {

        EasyBlog.Controller("Composer.Panels.Category", {
            defaultOptions: {
                fields: null,
                templates: {},

                // Templates
                "{template}": "[data-category-primary-template]",

                // Panel
                "{fieldsTab}": "[data-eb-composer-panel-tab][data-id=fields]",
                "{fieldsPanel}": "[data-eb-composer-panel][data-id=fields]",
                "{fieldsPanelForm}": "[data-eb-composer-panel][data-id=fields] [data-eb-composer-form]",
                "{fieldsForm}": "[data-eb-composer-panel-fields]",
                "{empty}": "[data-eb-composer-fields-empty]",

                // Categories
                "{categories}": "[data-category-primary-items]",
                "{category}": "[data-category-primary-item]",
                "{categoryText}": "[data-title-text]",

                // Primary category selection
                "{primaryCategoryInput}": "[data-category-primary-input]",
                "{primaryCategoryTitle}": "[data-category-primary-title]"
            }
        },
        function(self, opts, base, composer) { return {

            init: function() {
                // Get the primary category template
                opts.templates['primaryCategory'] = self.template().detach().html();

                composer = EasyBlog.Composer;
            },

            getTemplate: function(type) {
                var item = $(opts.templates[type]);

                return item;
            },

            insertCategory: function(category) {

                var item = self.getTemplate('primaryCategory');

                item.attr('data-title', category.title)
                    .attr('data-id', category.id)
                    .find(self.categoryText)
                    .html(category.title);


                // Append the item into the list
                self.categories().append(item);
            },

            removeCategory: function(category) {

                if (self.categories().children().length == 1) {
                    // dont do anything.
                    return;
                }

                self.categories()
                    .children('[data-id="' + category.id + '"]')
                    .remove();

                // now we check if the list remain one last item? if yes, mark that as primary category, OR
                // if the current deselect category is a primary category or not.
                // if yes, we need to pre-select other category
                if (self.categories().children().length == 1 || category.id == self.primaryCategoryInput().val()) {
                    var newprimarycat = $(self.categories().children()[0]);

                    var id = newprimarycat.data("id");
                    var title = newprimarycat.data("title");

                    self.assignPrimaryCategory(id, title);
                }
            },

            assignPrimaryCategory: function(id, title) {
                // Set the input with the proper id
                self.primaryCategoryInput().val(id);

                // Set the title
                self.primaryCategoryTitle().html(title);

                var category = {
                   id: id,
                   title: title
                }

                self.trigger("categorysetprimary", category);

            },

            categoryExists: function(category) {
                var category = self.categories().children('[data-id="' + category.id + '"]');

                return category.length > 0;
            },

            "{category} click": function(category, event) {

                var id = category.data("id");
                var title = category.data("title");

                self.assignPrimaryCategory(id, title);
            },

            "{self} categorydeselect": function(el, event, category) {

                // Find the custom fields form in the fields panel and remove it
                // Since the category has been de-selected already.
                self.fieldsPanel()
                    .find('[data-category-id="' + category.id + '"]')
                    .remove();

                // Display the empty message if there is no fields available
                if (self.fieldsPanel().find('[data-panel-field]').length == 0) {
                    self.empty().show();
                }

                // Remove the primary category child item once a category is de-selected
                self.removeCategory(category);
            },

            "{self} categoryselect": function(el, event, category) {

                // If category does not exist in the list, add it to the list
                if (!self.categoryExists(category)) {
                    self.insertCategory(category);
                }

                // When a category is selected, ensure that we retrieve the custom fields associated with the category
                EasyBlog.ajax("site/views/categories/getCustomFields", {
                    "id": category.id,
                    "postId": EasyBlog.Composer.getPostId()
                }).done(function(form) {

                    if (form.length > 0) {
                        
                        // Ensure that the fields tab is shown
                        var tab = composer.panels.panelTab.get('fields');

                        // Ensure that the tab is shown
                        tab.removeClass('hide');

                        // Hide the empty message in the custom fields area
                        self.empty().hide();

                        // Append the custom fields form into the field panel
                        self.fieldsPanelForm().append(form);
                    }
                });

            },

            "{self} composerSaveError": function(el, event, exception) {
                
                // Show errors on the fields tab
                if (exception.customCode && exception.customCode == -500) {
                    self.fieldsTab().addClass('has-error');
                } else {
                    self.fieldsTab().removeClass('has-error');
                }
            }
        }});

        module.resolve();

    });

});




});
