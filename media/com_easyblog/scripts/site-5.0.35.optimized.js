FD50.installer("EasyBlog", "definitions", function($){
$.module(["easyblog/easyblog","easyblog/layout/template","easyblog/layout/responsive","easyblog/layout/dialog","easyblog/layout/elements","easyblog/layout/launcher","easyblog/layout/placeholder","easyblog/layout/image/popup","easyblog/layout/image/gallery","easyblog/layout/image/legacy","easyblog/subscribe","easyblog/layout/image/caption","easyblog/authors","easyblog/comments/captcha","easyblog/comments/comments","easyblog/comments/form","easyblog/comments/list","easyblog/composer/document/artboard","easyblog/posts/posts","easyblog/posts/tools","easyblog/posts/reports","easyblog/ratings","easyblog/bookmarklet","easyblog/tag","easyblog/teamblogs"]);
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


EasyBlog.module("authors", function($) {

var module = this;

// require: start
EasyBlog.require()
	.done(function($){

	// controller: start

	EasyBlog.Controller('Authors.Item', {
		defaultOptions: {
			"{subscribe}" : "[data-author-subscribe]",
			"{unsubscribe}" : "[data-author-unsubscribe]"
		}
	}, function(self) {
		return {
			init: function()
			{
				self.options.id = self.element.data('id');
			},

			"{subscribe} click" : function()
			{
				EasyBlog.dialog({
					content: EasyBlog.ajax('site/views/subscription/form', {
						"id" : self.options.id,
						"type" : "blogger"
					})
				});
			},
			"{unsubscribe} click" : function(el, event)
			{
				EasyBlog.dialog({
					content: EasyBlog.ajax('site/views/subscription/unsubscribe', {
						"id" : self.options.id,
						"type" : "blogger",
						"email" : $(el).data('email')
					})
				});
			}
		}
	});

	EasyBlog.Controller('Authors.Listing', {
		defaultOptions: {
			"{item}" : "[data-author-item]"
		}
	}, function(self) {
		return {

			init: function()
			{
				self.implementAuthor();
			},

			implementAuthor: function()
			{
				self.item().implement(EasyBlog.Controller.Authors.Listing.Item);
			}
		}
	});

	EasyBlog.Controller('Authors.Listing.Item', {
		defaultOptions: {

			"{feature}" : "[data-author-feature]",
			"{unfeature}" : "[data-author-unfeature]",
			"{featuredTag}" : "[data-featured-tag]",
			"{subscribe}" : "[data-author-subscribe]",
			"{unsubscribe}" : "[data-author-unsubscribe]"
		}
	}, function(self) {
		return {

			init: function()
			{
				self.options.id = self.element.data('id');
			},
			featureItem: function()
			{
				EasyBlog.ajax('site/views/featured/makeFeatured', {
					"type" : "blogger",
					"id": self.options.id
				}).done(function(){
					// Switch the button
					self.feature().addClass('hide');
					self.unfeature().removeClass('hide');

					// Display the star icon
					self.featuredTag().removeClass('hide');
				});
			},

			"{subscribe} click" : function(el, event)
			{
				EasyBlog.dialog({
					content: EasyBlog.ajax('site/views/subscription/form', {
						"id" : self.options.id,
						"type" : "blogger"
					})
				});
			},
			"{unsubscribe} click" : function(el, event)
			{
				EasyBlog.dialog({
					content: EasyBlog.ajax('site/views/subscription/unsubscribe', {
						"id" : self.options.id,
						"type" : "blogger",
						"email" : $(el).data('email')
					})
				});
			},

			"{feature} click" : function(el, event)
			{
				EasyBlog.dialog(
				{
					content: EasyBlog.ajax('site/views/featured/confirm', {
						"type": "blogger",
						"id": self.options.id
					}),
					bindings: {
						"{submitButton} click" : function()
						{
							self.featureItem();

							// Hide dialog now
							EasyBlog.dialog().close();
						}
					}
				});
			},
			"{unfeature} click" : function(el, event)
			{
				EasyBlog.dialog(
				{
					content: EasyBlog.ajax('site/views/featured/removeFeatured', {
						"type": "blogger",
						"id": self.options.id
					}),
					bindings: {
						"{closeButton} click" : function()
						{
							self.unfeature().addClass('hide');
							self.feature().removeClass('hide');

							self.featuredTag().addClass('hide');

							EasyBlog.dialog().close();
						}
					}
				});
			}
		}
	});

	module.resolve();

	// controller: end

	});

	// require: end
});

EasyBlog.module('comments/captcha', function($) {

    var module = this;

    EasyBlog.require()
    .done(function($) {

        EasyBlog.Controller('Comments.Form.Captcha', {

            defaultOptions: {
                "{input}": "[data-captcha-input]",
                "{reload}": "[data-captcha-reload]",
                "{captchaId}": "[data-captcha-id]",
                "{image}": "[data-captcha-image]"
            }
        }, function(self, opts, base) {

            return {

                init: function() {
                },

                "{self} submitComment": function(el, event, data) {
                    data['captcha-response'] = self.input().val();
                    data['captcha-id'] = self.captchaId().val();
                },

                "{self} resetForm": function() {
                    self.input().val('');
                    
                    self.reload().click();
                },

                "{self} reloadCaptcha": function() {

                    EasyBlog.ajax('site/views/comments/reloadCaptcha', {
                        "previousId": self.captchaId().val()
                    })
                    .done(function(newImage, newCaptchaId) {
                        self.image().attr('src', newImage);

                        self.captchaId().val(newCaptchaId);
                    });
                },

                "{reload} click": function() {
                    self.trigger('reloadCaptcha');
                }
            }
        });

        module.resolve();
    });
});
EasyBlog.module('comments/comments', function($) {

    var module = this;

    EasyBlog.require()
    .library('markitup')
    .script('comments/form', 'comments/list')
    .done(function($) {

        EasyBlog.Controller('Comments', {
            defaultOptions: {
                "{item}" : "[data-comment-item]"
            }
        }, function(self) {
            return {
                init: function()
                {
                    self.commentForm = self.addPlugin('form');
                    self.commentList = self.addPlugin('list');
                }
            }
        });

        module.resolve();
    });
});
EasyBlog.module('comments/form', function($) {

    var module = this;

    EasyBlog.require()
    .script('comments/captcha', 'comments/list')
    .library('markitup')
    .done(function($) {

        EasyBlog.Controller('Comments.Form', {
            defaultOptions: {

                "{formWrapper}": "[data-comment-form-wrapper]",
                "{form}": "[data-comment-form]",
                "{title}": "[data-comment-title]",
                "{name}": "[data-comment-name]",
                "{username}": "[data-comment-username]",
                "{password}": "[data-comment-password]",
                "{email}": "[data-comment-email]",
                "{register}": "[data-comment-register]",
                "{website}": "[data-comment-website]",
                "{counter}": "[data-comment-counter]",
                "{subscribe}" : "[data-comment-subscribe]",
                "{terms}": "[data-comment-terms]",
                "{tncLink}": "[data-comment-tnc]",
                "{parentId}" : "[data-comment-parent-id]",
                "{commentDepth}": "[data-comment-depth]",
                "{blogId}" : "[data-comment-blog-id]",
                "{depth}": "[data-comment-depth]",
                "{notice}": "[data-comment-notice]",
                "{editor}": "[data-comment-editor]",
                "{submit}": "[data-comment-submit]",
                "{formToken}": "[data-comment-token]",

                "{recaptcha}": "[data-recaptcha-item]"
            }
        }, function(self, opts, base) {

            return {

                init: function() {

                    self.initEditor();

                    self.list = self.addPlugin('list');

                    // If recaptcha is enabled, we should skip the normal captcha
                    var recaptcha = self.recaptcha.inside(self.element).length;

                    if (recaptcha < 1) {
                        self.captcha = self.addPlugin('captcha');
                    }
                },

                initEditor: function() {
                    if (self.editor().data('comment-bbcode') == 1) {
                        self.editor().markItUp(window.EasyBlogBBCodeSettings);
                    }
                },

                setNotice: function(message, type) {
                    var className = '';

                    if (type == 'error') {
                        className = 'alert-danger';
                    }

                    if (type == 'success') {
                        className = 'alert-success';
                    }

                    if (type == 'info') {
                        className = 'alert-info';
                    }

                    self.notice()
                        .removeClass('hide')
                        .addClass('alert ' + className)
                        .html(message);
                },

                resetForm: function() {
                    // If the comment form has a parent id, we need to reposition the comment form back.
                    var parentId = self.parentId().val();

                    if (parentId != 0) {
                        self.form().appendTo(self.formWrapper());
                    }

                    // Reset the form
                    self.username().val('');
                    self.password().val('');
                    self.subscribe().attr('checked', false);
                    self.editor().val('');
                    self.website().val('');
                    self.name().val('');
                    self.depth().val(0);
                    self.parentId().val(0);

                    self.trigger('resetForm');

                    // Determine if recaptcha is available
                    var recaptcha = self.recaptcha.inside(self.element);

                    // Get recaptcha's response
                    if (recaptcha.length > 0) {
                        grecaptcha.reset();
                    }
                },

                resetNotice: function() {
                    self.notice()
                        .removeClass('info error')
                        .html('');
                },

                "{self} replyComment": function(el, event, commentItem, commentId, commentDepth) {
                    // Hide notices in the reply form
                    self.notice().addClass('hide');

                    // When user tries to reply to an existing comment, move the form next to the level of the comment item
                    commentItem.after(self.form());

                    self.depth().val(commentDepth);

                    // Set the new parent id to the comment's id
                    self.parentId().val(commentId);
                },

                "{self} cancelReply": function(el, event, commentItem, commentId) {
                    // Set the parent id to 0
                    self.parentId().val(0);

                    // Reset the comment depth back to 0
                    self.depth().val(0);

                    // Relocate the form back to it's origin
                    self.formWrapper().html(self.form());
                },

                "{self} commentAdded": function()
                {
                    // Increment the counter
                    var count = self.counter().html();
                        count = parseInt(count) + 1;

                    self.counter().html(count.toString());
                    self.resetForm();
                },

                getValues: function() {

                    var data = {

                        title: self.title().val(),
                        name: self.name().val(),
                        email: self.email().val(),
                        username: self.username().val(),
                        password: self.password().val(),
                        website: self.website().val(),
                        subscribe: self.subscribe().is(':checked') ? 1 : 0,
                        register: self.register().is(':checked') ? 1 : 0,
                        comment: self.editor().val(),
                        terms: self.terms().is(':checked') ? 1 : 0,
                        depth: self.depth().val(),
                        parentId: self.parentId().val(),
                        blogId: self.blogId().val()
                    };

                    // token
                    // data[self.formToken().attr('name')] = 1;

                    // Determine if recaptcha is available
                    var recaptcha = self.recaptcha.inside(self.element);

                    // Get recaptcha's response
                    if (recaptcha.length > 0) {
                        data.recaptcha = grecaptcha.getResponse();
                    }

                    self.trigger('submitComment', [data]);
                    return data;
                },

                "{tncLink} click": function() {
                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/comments/terms')
                    })
                },

                "{submit} click" : function(el, event) {

                    event.preventDefault();

                    // Reset notices
                    self.resetNotice();

                    // Add loading indicator on the button
                    $(el).attr('disabled', true);

                    var tmp = $(el).html();

                    $(el).html('<i class="fa fa-repeat fa-spin"></i>');

                    // Get the form values
                    var data = self.getValues();

                    // Perform an ajax call to submit the comment
                    EasyBlog.ajax('site/views/comments/save', data)
                        .done(function(output, message, state) {

                            self.setNotice(message, state);
                            self.trigger('commentAdded',[output, data]);
                        })
                        .fail(function(message) {
                            self.setNotice(message, 'error');
                        })
                        .always(function(){
                            $(el).removeAttr('disabled');
                            $(el).html(tmp);

                            self.trigger('reloadCaptcha');
                        });

                    return false;
                }
            }
        });

        module.resolve();
    });
});

EasyBlog.module('comments/list', function($) {

    var module = this;

    EasyBlog.require()
    .library('markitup')
    .done(function($) {

        EasyBlog.Controller('Comments.List', {
            defaultOptions: {
                "{item}": "[data-comment-item]",
                "{list}": "[data-comment-list]",

                "{edit}": "[data-comment-edit]",
                
                "{editor}" : "[data-comment-edit-editor]",
                "{body}": "[data-comment-body]",
                "{cancelEdit}": "[data-comment-edit-cancel]",
                "{cancelReply}": "[data-comment-reply-cancel]", 
                "{update}": "[data-comment-edit-update]",
                "{preview}": "[data-comment-preview]",
                "{reply}": "[data-comment-reply]",
                "{like}": "[data-comment-like]",
                "{unlike}": "[data-comment-unlike]",

                "{deleteButton}": "[data-comment-delete]",
                "{likeCounter}": "[data-comment-like-counter]",
                "{likeTooltip}": "[data-comment-like-tooltip]",
                "{rawContent}": "[data-comment-edit-raw]",
                "{empty}": "[data-comment-empty]"
            }
        }, function(self, opts, base) {
            return {
                init: function()
                {
                },
                updatePreview: function(html)
                {
                    self.preview().html(html);
                },
                "{self} cancelEdit": function(el, event, item, formattedContents, rawContents)
                {
                    self.options.editing = false;
                    
                    // Hide the editor
                    item.find(self.editor.selector).addClass('hide');

                    // Display the body
                    item.find(self.body.selector).removeClass('hide');

                    // Restore the default value
                    item.find(self.editor.selector).find('textarea').val(item.find(self.rawContent.selector).html());
                },

                "{self} updateComment": function(el, event, item, formattedContents, rawContents)
                {
                    self.options.editing = false;

                    // Update the raw contents
                    item.find(self.rawContent.selector).html(rawContents);

                    // Update the textarea
                    item.find(self.editor.selector + ' textarea').val(rawContents);

                    // Update the preview
                    item.find(self.preview.selector).html(formattedContents);

                    // Hide the editor
                    item.find(self.editor.selector).addClass('hide');

                    // Display the body
                    item.find(self.body.selector).removeClass('hide');
                },

                "{self} commentAdded": function(el, event, commentItem, data)
                {
                    // Always hide the empty comment placeholder.
                    self.empty().hide();

                    // Check if this comment is a reply
                    if (data.parentId != 0) {

                        // Append this item to be after the parent.
                        var selector = self.item.selector + '[data-id="' + data.parentId + '"]';
                        
                        // Trigger the cancel reply because the comment was already posted on the site
                        $(selector).find(self.cancelReply.selector).click();

                        $(selector).after(commentItem);

                        return;
                    }

                    self.list().append(commentItem);
                },

                "{edit} click": function(editButton)
                {
                    var item = self.item.of(editButton),
                        itemId = item.data("id");

                    if (self.options.editing) {
                        self.options.editing = false;
                        item.find(self.editor.selector).addClass('hide');
                        item.find(self.body.selector).removeClass('hide');
                        return;
                    }

                    self.options.editing = true;
                    item.find(self.body.selector).addClass('hide');
                    item.find(self.editor.selector).removeClass('hide');

                    // Implement mark it up on editor
                    if (!item.find(self.editor.selector + ' textarea').hasClass('markItUpEditor')) {
                        item.find(self.editor.selector + ' textarea').show().markItUp(window.EasyBlogBBCodeSettings);    
                    }
                },

                "{update} click": function(updateButton)
                {
                    var item = self.item.of(updateButton),
                        itemId = item.data("id"),
                        value = item.find(self.editor.selector).find('textarea').val();

                    EasyBlog.ajax('site/views/comments/update', {"id" : itemId, "message" : value})
                    .done(function(formattedContents, rawContents)
                    {
                        item.find(self.preview.selector).html(formattedContents);
                        
                        self.trigger('updateComment', [item, formattedContents, rawContents]);
                    });
                },

                "{cancelReply} click": function(cancelReply)
                {
                    var item = self.item.of(cancelReply),
                        itemId = item.data("id");


                    // Hide the reply button
                    $(cancelReply).addClass('hide');

                    // Show the cancel reply button
                    item.find(self.reply.selector).removeClass('hide');

                    self.trigger('cancelReply', [item, itemId]);
                },

                "{cancelEdit} click": function(cancelButton)
                {
                    var item = self.item.of(cancelButton),
                        itemId = item.data("id");

                    self.trigger('cancelEdit', [item]);
                },

                "{reply} click": function(replyButton)
                {
                    var item = self.item.of(replyButton),
                        itemId = item.data("id"),
                        commentDepth = replyButton.data('depth');

                    self.trigger('replyComment', [item, itemId, commentDepth]);

                    // Hide the reply button
                    $(replyButton).addClass('hide');

                    // Show the cancel reply button
                    item.find(self.cancelReply.selector).removeClass('hide');
                },
                
                "{deleteButton} click": function(deleteButton) {

                    var item = self.item.of(deleteButton),
                        itemId = item.data("id");


                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/comments/confirmDelete', {"id" : itemId})
                    });
                },

                "{like} click": function(likeButton) {
                    var item = self.item.of(likeButton),
                        itemId = item.data('id');

                    EasyBlog.ajax('site/views/comments/like', {"id": itemId})
                    .done(function(str, count) {
                        
                        // Add liked class on the item
                        item.addClass('is-like');

                        // Update the tooltip
                        var counter = item.find(self.likeCounter.selector);

                        // Update the counter
                        counter.html(count);

                        // self.likeTooltip().data('original-title', str);
                    });
                },

                "{unlike} click": function(unlikeButton)
                {
                    var item = self.item.of(unlikeButton),
                        itemId = item.data('id');

                    EasyBlog.ajax('site/views/comments/unlike', {"id": itemId})
                    .done(function(str, count) {
                            
                        // Remove like class from the comment wrapper
                        item.removeClass('is-like');

                        // Update the tooltip
                        // self.likeTooltip().data('original-title', str);

                        // Update the tooltip
                        var counter = item.find(self.likeCounter.selector);

                        // Update the counter
                        counter.html(count);

                        // Update the counter
                        // self.likeCounter().html(count);
                    });
                    
                }
            }
        });

        module.resolve();
    });
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

EasyBlog.module('posts/posts', function($) {

    var module = this;

    EasyBlog.require()
    .script('posts/tools', 'posts/reports', 'ratings')
    .done(function($) {

        EasyBlog.Controller('Posts', {
            defaultOptions: {

                "{item}": "[data-blog-posts-item]",

                // Moderation tools
                "{approvePost}": "[data-blog-moderate-approve]",
                "{rejectPost}": "[data-blog-moderate-reject]",

                // Preview tools
                "{publishPost}": "[data-blog-preview-publish]",
                "{useRevision}": "[data-blog-preview-userevision]",

                // Ratings
                "{ratings}": "[data-rating-form]"
            }
        }, function(self) {
            return {
                init: function() {
                    self.tools = self.addPlugin('tools');
                    self.reports = self.addPlugin('reports');
                    self.id = self.item().data('id');
                    self.uid = self.item().data('uid');

                    self.initializeRatings();
                },

                initializeRatings: function() {
                    self.ratings().implement('EasyBlog.Controller.Ratings');
                },

                "{approvePost} click": function(el, event) {
                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/entry/confirmApprove', {"id": self.id})
                    });
                },

                "{rejectPost} click": function(el, event) {
                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/entry/confirmReject', {"id": self.id})
                    });
                },

                "{publishPost} click": function(el, event) {
                    
                    // Display a confirmation
                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/entry/confirmPublish', { "id": self.id })
                    });
                },

                "{useRevision} click": function(el, event) {
                    // Display a confirmation
                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/entry/confirmUseRevision', { "uid": self.uid })
                    });
                }
            }
        });

        module.resolve();
    });
});
EasyBlog.module('posts/tools', function($) {

var module = this;

EasyBlog.Controller('Posts.Tools', {
    defaultOptions: {

        "{delete}": "[data-entry-delete]",
        "{publish}": "[data-entry-publish]",
        "{unpublish}": "[data-entry-unpublish]",
        "{feature}": "[data-entry-feature]",
        "{unfeature}": "[data-entry-unfeature]",
        "{unarchive}": "[data-entry-unarchive]",
        "{archive}": "[data-entry-archive]"
    }
}, function(self) { return {

    init: function() {
    },

    "{unarchive} click": function(button)
    {
        var item = self.parent.item.of(button);
        var id = item.data('id');
        var returnUrl = button.data('return');

        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/confirmUnarchive', {
                "id": id,
                "return": returnUrl
            })
        });
    },

    "{archive} click": function(archiveButton)
    {
        var item = self.parent.item.of(archiveButton),
            id = item.data('id'),
            returnUrl = $(archiveButton).data('return');

        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/confirmArchive', {
                "id": id,
                "return": returnUrl
            })
        });
    },

    "{delete} click": function(deleteButton)
    {
        var item = self.parent.item.of(deleteButton),
            itemId = item.data("id"),
            returnUrl = $(deleteButton).data('return');

        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/confirmDelete', {"id" : itemId, "return": returnUrl})
        });
    },

    "{feature} click": function(featureButton)
    {
        var item = self.parent.item.of(featureButton),
            itemId = item.data("id"),
            returnUrl = $(featureButton).data('return');


        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/featurePost', {"id": itemId, "return": returnUrl})
        });
    },

    "{unfeature} click": function(unfeatureButton) {
        var item = self.parent.item.of(unfeatureButton),
            id = item.data("id"),
            returnUrl = $(unfeatureButton).data('return');


        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/unfeaturePost', {"id": id, "return": returnUrl})
        });
    },

    "{publish} click": function(button) {
        var item = self.parent.item.of(button);
        var id = item.data('id');
        var returnUrl = button.data('return');

        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/confirmPublish', {"id" : id, "return": returnUrl})
        });
    },

    "{unpublish} click": function(button) {
        var item = self.parent.item.of(button);
        var id = item.data('id');
        var returnUrl = button.data('return');

        EasyBlog.dialog({
            content: EasyBlog.ajax('site/views/entry/confirmUnpublish', {"id" : id, "return": returnUrl})
        });
    }
}
});

module.resolve();

});
EasyBlog.module('posts/reports', function($) {

var module = this;

EasyBlog.Controller('Posts.Reports', {
    defaultOptions: {
        "{report}" : "[data-blog-report]"
    }
}, function(self) {
    return {
        init: function()
        {
        },

        "{report} click": function(el)
        {
            var item = self.parent.item.of(el),
                id = item.data('id');

            EasyBlog.dialog({
                content: EasyBlog.ajax('site/views/reports/form', {"id" : id, "type": "post"}),
                bindings: {

                }
            });
        }
    }
});

module.resolve();

});
EasyBlog.module('ratings', function($){

	var module = this;

	EasyBlog
		.require()
		.library('ui/stars')
		.done(function(){

			EasyBlog.Controller('Ratings', {
				defaultOptions: {
					"{stars}": ".ui-stars-star",
					"{ratingValue}": "[data-rating-value]",
					"{ratingText}": "[data-rating-text]",
					"{showRating}": "[data-rating-voters]",
					"{totalRating}": "[data-rating-total]",
					"{starContainer}": ".star-location"
				}
			}, function(self) {
				return {

					init: function() {
						self.type = self.element.data('type');
						self.uid = self.element.data('id');
						self.locked = self.element.data('locked');

						var options = {
							'split': 2,
							'disabled': self.locked,
							'oneVoteOnly': true,
							'cancelShow': false,
							callback: self.onUserVote
						};

						// Implement star ratings
						self.starContainer().stars(options);
					},

					onUserVote: function(el) {
						var value = el.value();

						EasyBlog.ajax('site/views/ratings/vote', {
							"value": value,
							"type": self.type,
							"id": self.uid
						})
						.done(function(total, message, rating) {

							// Disable the selected stars
							self.stars().removeClass('ui-stars-star-on');

							// Hide the rate this text
							self.ratingText().html(message);

							// Add voted class
							self.element.addClass('voted');

							self.totalRating().text(total);

							// Enable specific stars
							self.stars().each(function(index) {
								if (index < rating) {
									$(this).addClass('ui-stars-star-on');
								} else {
									$(this).removeClass('ui-stars-star-on');
								}
							});
						});
					},

					"{showRating} click": function() {
						var total = parseInt(self.totalRating().text(), 10);
						if (total <= 0) {
							return;
						}

						EasyBlog.dialog({
							content: EasyBlog.ajax('site/views/ratings/voters', {"uid" : self.uid, "type" : self.type})
						});
					}
				}
			});

			module.resolve();
		});

});

EasyBlog.module('bookmarklet', function($) {

	var module = this;

	$.bookmarklet = function(elem, type, options, callback) {
		var node = this[type].call($(elem), options);

	    // On IE9, addEventListener() does not necessary fire the onload event
	    // after the script is loaded, therefore we use the attachEvent() method,
	    // as it behaves correctly.
	    if (node.attachEvent && !$.browser.opera) {
	        node.attachEvent("onreadystatechange", callback);
	    } else {
	        node.addEventListener("load", callback, false);
	    }
	};

	$.fn.bookmarklet = function(type, options, callback) {
		var node = this;

		$(document).ready(function(){
			$.bookmarklet[type].call(node, options, callback);
		});
	};

	var linkedinLoaded = false;

	$.bookmarklet.linkedin = function(options) {
		var node = this[0];
		var parent = node.parentNode;
		var config = document.createElement('script');
		var script = document.createElement('script');

		var btnOptions = {
			"type": "in/share",
			"data-url": options.url,
			"data-counter": options.size == 'large' ? 'top' : 'right'
		};

		$(config).attr(btnOptions);

		parent.insertBefore(config, node);
		parent.removeChild(node);

		if (!linkedinLoaded) {
			var head = document.getElementsByTagName('head')[0];
			var script = document.createElement('script');

			head.appendChild(script);

			script.type = 'text/javascript';
			script.src = '//platform.linkedin.com/in.js';

			linkedinLoaded = true;
		}
	};

	var pocketLoaded = false;

	$.bookmarklet.pocket = function(options) {

		var node = this[0];
		var parent = node.parentNode;
		var button = document.createElement('a');
		var script = document.createElement('script');
		var btnOptions = {
			"data-pocket-label": "pocket",
			"data-pocket-count": options.size == 'large' ? 'vertical' : 'horizontal',
			"data-save-url": options.url,
			"data-lang": "en"
		};

		$(button)
			.addClass('pocket-btn')
			.attr(btnOptions);

		parent.insertBefore(button, node);
		parent.removeChild(node);

		if (!pocketLoaded) {
			var head = document.getElementsByTagName('head')[0];
			var script = document.createElement('script');

			head.appendChild(script);

			script.type = 'text/javascript';
			script.src = '//widgets.getpocket.com/v1/j/btn.js?v=1';

			pocketLoaded = true;
		}
	};

	var suLoaded = false;

	$.bookmarklet.stumbleupon = function(options) {
		var node = this[0];
		var parent = node.parentNode;
		var button = document.createElement('su:badge');

		var btnOptions = {
			"layout": options.size == 'large' ? 5 : 1,
			"location": options.url
		};

		$(button).attr(btnOptions);

		parent.insertBefore(button, node);
		parent.removeChild(node);

		if (!suLoaded) {
			var head = document.getElementsByTagName('head')[0];
			var script = document.createElement('script');

			head.appendChild(script);

			script.type = 'text/javascript';
			script.src = '//platform.stumbleupon.com/1/widgets.js';

			suLoaded = true;
		}
	};


	window.trackTwitter = function(intent_event) {
		if (intent_event) {
			var opt_pagePath;
			
			if (intent_event.target && intent_event.target.nodeName == 'IFRAME') {
				opt_pagePath = extractParamFromUri(intent_event.target.src, 'url');
			}
			
			_gaq.push(['_trackSocial', 'twitter', 'tweet', opt_pagePath]);					
		}
	}

	$.bookmarklet.twitter = function(options) {
		var node = this[0];
		var parent = node.parentNode;
		var button = document.createElement('a');
		var script = document.createElement('script');
		var layout = options.size == 'large' ? 'vertical' : 'horizontal';
		var btnOptions = {
				"class": "twitter-share-button",
				"href": "https://twitter.com/share",
				"data-url": options.url,
				"data-counturl": options.url,
				"data-count": layout,
				"data-via": options.via,
				"data-text": options.text			
			};

		// Update the button
		$(button).attr(btnOptions).html("Tweet");

		parent.insertBefore(button, node);
		parent.insertBefore(script, node);
		parent.removeChild(node);

		var twttr = window.twttr;

		if (!twttr) {

			$(script)
				.attr({
					type: "text/javascript",
					src: "https://platform.twitter.com/widgets.js"
				})
				.appendTo("head");

			twttr = window.twttr = {
				_e: [],
				ready: function(fn) {
					twttr._e.push(fn)
				}
			};

			if (options.tracking) {
				twttr.ready(function(intent_event){
					twttr.events.bind('tweet', window.trackTwitter);
				});
			}
		}

		return script;
	};

	var hasPlusOne,
		installPlusOne;

	$.bookmarklet.google = function(options) {
		var node = this[0];
		var parent = node.parentNode;
		var button = document.createElement('g:plusone');
		var btnOptions = {
			"size": options.size == 'large' ? 'tall' : 'medium',
			"href": options.href
		};

		$(button).attr(btnOptions);

		parent.insertBefore(button, node);
		parent.removeChild(node);

		if (!hasPlusOne) {

			clearTimeout(installPlusOne);

			installPlusOne = setTimeout(function(){

				var head = document.getElementsByTagName("head")[0],
					script = document.createElement("script");

					head.appendChild(script);
					script.type = "text/javascript";
					script.src = "//apis.google.com/js/plusone.js";

				hasPlusOne = true;

			}, 1000);

		} else if (gapi && gapi.plusone) {

			gapi.plusone.go(parent);
		}

		return node;
	};

	var hasFBSDK,
		FBInited,
		parseXFBMLTask,
		parseXFBML = function() {

			// Collect all the FB like calls first
			clearTimeout(parseXFBMLTask);

			parseXFBMLTask = setTimeout(function(){

				// Then finally parse it.
				try {
					FB.XFBML.parse();
				} catch(e) {};

			}, 1000);
		};

	$.bookmarklet.facebook = function(options) {

		var node = this[0];
		var parent = node.parentNode;
		var button = document.createElement('fb:like');
		var trackFB = function() {
				if (options.tracking) {
			        window.FB.Event.subscribe('edge.create', function(targetUrl) {
			          _gaq.push(['_trackSocial', 'facebook', 'like', targetUrl]);
			        });

			        window.FB.Event.subscribe('edge.remove', function(targetUrl) {
			          _gaq.push(['_trackSocial', 'facebook', 'unlike', targetUrl]);
			        });

			        window.FB.Event.subscribe('message.send', function(targetUrl) {
			          _gaq.push(['_trackSocial', 'facebook', 'send', targetUrl]);
			        });
			    }
			};

		var layout = options.size == 'large' ? 'box_count' : 'button_count';

		$(button)
			.attr({
				"class": "fb-like",
				"data-href": options.url,
				"data-send": options.send,
				"data-layout": layout,
				"data-action": options.verb,
				"data-locale": options.locale,
				"data-colorscheme": options.theme,
				"data-show-faces": false
			});

		parent.insertBefore(button, node);
		parent.removeChild(node);

		// If FBSDK isn't loaded, load it,
		// the social buttons will be parsed by itself.
		if (!window.FB) {

			if (!document.getElementById("fb-root")) {
				$("<div id='fb-root'></div>").prependTo("body");
			}

			var jssdk = document.getElementById("facebook-jssdk");

			// No JSSDK
			if (!jssdk) {

				var head = document.getElementsByTagName("head")[0],
					script = document.createElement("script");

					head.appendChild(script);
					script.id = "facebook-jssdk";
					script.src = "//connect.facebook.net/" + options.locale + "/all.js#xfbml=1";

			// Has JSSDK, but no XFBML support.
			} else if (!FBInited) {

				if (!/xfbml/.test(jssdk.src)) {

					var _fbAsyncInit = window.fbAsyncInit;

					window.fbAsyncInit = function(){

						if ($.isFunction(_fbAsyncInit)) _fbAsyncInit();


						parseXFBML();
						trackFB();
					}
				}

				FBInited = true;
			}

		// If FBSDK is already loaded
		} else {

			parseXFBML();
			trackFB();
		}



		return node;
	};

	var xingLoaded = false;

	$.bookmarklet.xing = function(options) {

		var node = this[0];
		var parent = node.parentNode;
		var button = document.createElement('div');
		var btnOptions = {
			"data-url": options.url,
			"data-counter": options.size == 'small' ? 'right' : 'top',
			"data-type": 'XING/Share'
		};
		
		$(button).attr(btnOptions).html("");

		parent.insertBefore(button, node);
		parent.removeChild(node);

		if (!xingLoaded) {
			var head = document.getElementsByTagName('head')[0];
			var script = document.createElement('script');

			head.appendChild(script);

			script.type = 'text/javascript';
			script.src = '//www.xing-share.com/js/external/share.js';

			xingLoaded = true;
		}
	};

	var vkLoaded = false;

	window.initVk = function(apiKey) {

		if (vkLoaded) {
			return;
		}

		VK.init({
			'apiId': apiKey,
			'onlyWidgets': true 
		});

		vkLoaded = true;
	};

	$.bookmarklet.vk = function(options) {

		var type = options.size == 'large' ? 'vertical' : 'button';

		// Init the script
		window.initVk(options.apiKey);

		VK.Widgets.Like(options.placeholder, {
				"type": type
			});
	};

	module.resolve();
});

EasyBlog.module("teamblogs", function($) {

var module = this;

// require: start
EasyBlog.require()
	.done(function($){

	// controller: start

	EasyBlog.Controller('TeamBlogs.Item', {
		defaultOptions: {
			"{feature}" : "[data-team-feature]",
			"{unfeature}" : "[data-team-unfeature]",
			"{featuredTag}": "[data-featured-tag]",
			"{viewMemberBtn}": "[data-view-member]"
		}
	}, function(self) {
		return {
			init: function()
			{
				self.options.id = self.element.data('id');
			},
			featureItem: function() {

				EasyBlog.ajax('site/views/featured/makeFeatured', {
					"type" : "teamblog",
					"id": self.options.id
				}).done(function(){
					// Switch the button
					self.feature().addClass('hide');
					self.unfeature().removeClass('hide');

					// Display the star icon
					self.featuredTag().removeClass('hide');
				});
			},
			"{feature} click" : function(el, event) {
				EasyBlog.dialog(
				{
					content: EasyBlog.ajax('site/views/featured/confirm', {
						"type": "teamblog",
						"id": self.options.id
					}),
					bindings: {
						"{submitButton} click" : function()
						{
							self.featureItem();

							// Hide dialog now
							EasyBlog.dialog().close();
						}
					}
				});
			},
			"{unfeature} click" : function(el, event) {
				EasyBlog.dialog(
				{
					content: EasyBlog.ajax('site/views/featured/removeFeatured', {
						"type": "teamblog",
						"id": self.options.id
					}),
					bindings: {
						"{closeButton} click" : function()
						{
							self.unfeature().addClass('hide');
							self.feature().removeClass('hide');

							self.featuredTag().addClass('hide');

							EasyBlog.dialog().close();
						}
					}
				});
			},

			"{viewMemberBtn} click": function(el, event) {


				console.log(self.options.id);

				EasyBlog.dialog(
				{
					content: EasyBlog.ajax('site/views/teamblog/viewMembers', {
						"id": self.options.id
					}),
					bindings: {
						"{closeButton} click" : function()
						{
							EasyBlog.dialog().close();
						}
					}
				});
			}
		}
	});

	module.resolve();

	// controller: end

	});

	// require: end
});
});
