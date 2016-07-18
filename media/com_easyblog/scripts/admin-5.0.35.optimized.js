FD50.installer("EasyBlog", "definitions", function($){
$.module(["easyblog/easyblog","easyblog/layout/template","easyblog/layout/responsive","easyblog/layout/dialog","easyblog/layout/elements","easyblog/layout/launcher","easyblog/layout/placeholder","easyblog/layout/image/popup","easyblog/layout/image/gallery","easyblog/layout/image/legacy","easyblog/subscribe","easyblog/admin/grid","easyblog/admin/sort","easyblog/admin/publishing","easyblog/admin/maintenance/database","easyblog/admin/maintenance/form","easyblog/admin/ordering","easyblog/layout/image/caption"]);
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

EasyBlog.module('admin/grid', function($) {

	var module = this;

	EasyBlog.require()
	.script('admin/sort', 'admin/publishing')
	.done(function($)
	{
		EasyBlog.Controller(
			'Grid',
			{
				defaultOptions : 
				{
					"{sortColumns}"		: "[data-table-grid-sort]",
					"{ordering}"		: "[data-table-grid-ordering]",
					"{direction}"		: "[data-table-grid-direction]",

					"{task}"			: "[data-table-grid-task]",
					
					"{searchInput}"		: "[data-table-grid-search-input]",
					"{search}"			: "[data-table-grid-search]",
					"{resetSearch}"		: "[data-table-grid-search-reset]",

					"{checkAll}"		: "[data-table-grid-checkall]",
					"{checkboxes}"		: "[data-table-grid-id]",

					"{publishItems}"	: "[data-table-grid-publishing]",

					"{itemRow}"			: "tr",

					"{boxChecked}"		: "[data-table-grid-box-checked]",
					"{filters}"			: "[data-table-grid-filter]"
				}
			}, function(self) {
				
				return {

					init: function() {
						
						// Implement sortable items.
						self.implementSortable();

						// Implement publish / unpublish
						self.implementPublishing();
					},

					"{filters} change" : function()
					{
						// Always reset the task before submitting.
						self.setTask( '' );

						self.submitForm();
					},

					"{search} click" : function()
					{
						self.submitForm();
					},

					"{resetSearch} click" : function()
					{
						self.searchInput().val( '' );
						self.submitForm();
					},

					submitForm: function()
					{
						self.element.submit();
					},

					setTask: function( task )
					{
						self.task().val( task );
					},

					setOrdering: function( ordering )
					{
						self.ordering().val( ordering );
					},

					setDirection: function( direction )
					{
						self.direction().val( direction );
					},

					setTotalChecked: function( total )
					{
						self.boxChecked().val( total );
					},

					toggleSelectRow: function( row )
					{
						var checkbox 	= row.find( 'input[name=cid\\[\\]]' );

						if( $( checkbox ).prop( 'checked' ) == true )
						{
							$( checkbox ).prop( 'checked' , false );	
						}
						else
						{
							$( checkbox ).prop( 'checked' , true );
						}
						
					},
					selectRow: function( row )
					{
						var checkbox 	= row.find( 'input[name=cid\\[\\]]' );

						$( checkbox ).prop( 'checked' , true );
					},

					implementSortable: function()
					{
						self.sortColumns().implement(EasyBlog.Controller.Grid.Sort ,
						{
							"{parent}" 	: self
						});
					},

					implementPublishing: function()
					{
						self.publishItems().implement(EasyBlog.Controller.Grid.Publishing,
						{
							"{parent}"	: self
						});
					},

					"{checkAll} change": function( element , event )
					{
						// Find all checkboxes in the grid.
						self.checkboxes().prop( 'checked' , $( element ).is( ':checked' ) );

						// Update the total number of checkboxes checked.
						var total 	= $( element ).is( ':checked' ) ? self.checkboxes().length : 0;


						self.setTotalChecked( total );
					}
				}
			}
		);
			
		module.resolve();
	});


});
EasyBlog.module('admin/sort' , function($) {

	var module = this;

	EasyBlog.Controller(
		'Grid.Sort',
		{
			defaultOptions : 
			{
				items 	: "[data-grid-sort-item]"
			}
		},
		function( self )
		{
			return {

				init : function()
				{
				},

				"{self} click": function()
				{
					var direction 	= self.element.data( 'direction' ),
						column 		= self.element.data( 'sort' );

					// Set the ordering
					self.parent.setOrdering( column );

					// Set the direction
					self.parent.setDirection( direction );

					// Remove any task associated to the form.
					self.parent.setTask( '' );
					
					// Submit the form.
					self.parent.submitForm();
				}
			}
		}
	);
		
	module.resolve();

});
EasyBlog.module('admin/publishing' , function($) {

	var module = this;

	EasyBlog.Controller(
		'Grid.Publishing',
		{
			defaultOptions : 
			{
			}
		},
		function( self )
		{
			return {

				init : function()
				{
				},

				"{self} click": function( el )
				{
					var row 	= self.element.parents( 'tr' ),
						task 	= self.element.data( 'task' );

					self.parent.selectRow(row);

					self.parent.setTask( task );

					self.parent.submitForm();
				}
			}
		}
	);
		
	module.resolve();

});
EasyBlog.module('admin/maintenance/database', function($) {
    var module = this;

    EasyBlog.Controller('Maintenance.Database', {
        defaultOptions: {
            '{start}': '[data-start]',

            '{progress}': '[data-progress]',

            '{progressBox}': '[data-progress-box]',

            '{progressBar}': '[data-progress-bar]',

            '{progressPercentage}': '[data-progress-percentage]',

            '{errorBox}': '[data-error]',

            '{successBox}': '[data-success]'
        }
    }, function(self) {
        return {
            init: function() {

            },

            '{start} click': function(el) {
                el.hide();

                self.progress().show();

                self.process();
            },

            counter: 0,

            versions: [],

            process: function() {
                self.getStats().done(function(versions) {
                    self.versions = versions;

                    self.execute();
                });
            },

            getStats: function() {
                return EasyBlog.ajax('admin/controllers/maintenance/getDatabaseStats');
            },

            execute: function() {
                if (self.versions[self.counter] === undefined) {
                    return self.completed();
                }

                self.successBox().hide();

                EasyBlog.ajax('admin/controllers/maintenance/synchronizeDatabase', {
                    version: self.versions[self.counter]
                }).fail(function(msg) {
                    self.errorBox().show();

                    self.errorBox().append('<div class="alert alert-danger">' + msg + '</div>');

                }).always(function() {
                    self.counter++;

                    var percentage = Math.floor((self.counter/self.versions.length) * 100) + '%';

                    self.progressBar().css('width', percentage);

                    self.progressPercentage().text(percentage);

                    self.execute();
                });
            },

            completed: function() {
                self.progressBar().css('width', '100%');

                self.progressPercentage().text('100%');

                self.progressBox()
                    .removeClass('progress-info')
                    .addClass('progress-success');

                self.successBox().delay(1000).fadeIn(400);
            }
        }
    });

    module.resolve();
});

EasyBlog.module('admin/maintenance/form', function($) {
    var module = this;

    EasyBlog.Controller('Maintenance.Form', {
        defaultOptions: {
            '{row}': '[data-row]'
        }
    }, function(self) {
        return {
            init: function() {
                self.runscript();
            },

            counter: 0,

            success: 0,

            fail: 0,

            runscript: function() {
                var row = self.row().eq(self.counter);

                if (row.length === 0) {
                    return self.completed();
                }

                var key = row.data('key');

                EasyBlog.ajax('admin/controllers/maintenance/runscript', {
                    key: key
                }).done(function() {
                    self.setStatus(row, 1);
                    self.success++;
                }).fail(function() {
                    self.setStatus(row, 0);
                    self.fail++;
                }).always(function() {
                    self.counter++;
                    self.runscript();
                });
            },

            completed: function() {
                if (self.fail < 1) {
                    window.location = 'index.php?option=com_easyblog&view=maintenance&success=' + self.success;
                }
            },

            setStatus: function(row, state) {
                var status = row.find('[data-status]'),
                    icon = row.find('[data-icon]'),
                    statuses = ['label-danger', 'label-success', 'label-warning'],
                    icons = ['ies-warning-2', 'ies-checkmark', 'ies-wrench-3'];

                for (i = 0; i < 3; i++) {
                    status.toggleClass(statuses[i], state == i);
                    icon.toggleClass(icons[i], state == i);
                }
            }
        }
    });

    module.resolve();
});

EasyBlog.module('admin/grid/ordering' , function($) {

	var module = this;

	EasyBlog.Controller(
		'Grid.Ordering',
		{
			defaultOptions : 
			{
				"{moveUp}" 		: "[data-grid-order-up]",
				"{moveDown}"	: "[data-grid-order-down]",
				row 	: null
			}
		},
		function( self )
		{
			return {

				init : function()
				{
					// Get the parent row
					self.options.row 	= self.element.parents( 'tr' );
				},

				selectRow : function()
				{
					var checkbox 	= self.options.row.find('input[name=cid\\[\\]]' );

					// Ensure that the checkbox is checked
					$( checkbox ).prop( 'checked' , true );
				},

				"{moveUp} click" : function()
				{
					self.selectRow();
					$.Joomla( 'submitform' , [ 'moveUp' ] );
				},

				"{moveDown} click" : function()
				{
					self.selectRow();
					$.Joomla( 'submitform' , ['moveDown' ] );
				}
			}
		}
	);
		
	module.resolve();

});
});
