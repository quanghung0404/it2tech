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
