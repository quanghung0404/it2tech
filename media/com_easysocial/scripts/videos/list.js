EasySocial.module('videos/list', function($) {

	var module = this;

	EasySocial.require()
	.done(function($) {

	EasySocial.Controller('Videos.List', {
		defaultOptions: {

			// Video filters
			"{filter}": "[data-videos-filter]",
			"{sorting}": "[data-videos-sorting]",


			"{activeFilter}": ".filter-item.active a",

			// Videos result
			"{result}": "[data-videos-result]",

			// Video actions
			"{item}": "[data-video-item]",
			"{deleteButton}": "[data-video-delete]",
			"{featureButton}": "[data-video-feature]",
			"{unfeatureButton}": "[data-video-unfeature]"
		}
	}, function(self, opts, base) { return {

		init: function() {
		},

		// Default filter
		currentFilter: "",
		currentSorting: "",
		categoryId: null,

		setActiveFilter: function(filter) {

			// Remove all active classes.
			self.filter().parent().removeClass('active');

			// Set the active class to the filter's parent.
			filter.parent().addClass('active');
		},

		getVideos: function() {

			if (!self.currentSorting) {
				// Set the current sorting
				self.currentSorting = self.sorting().val();
			}

			if (!self.currentFilter) {
				// Set the current sorting
				self.currentFilter = self.activeFilter().data('type');
			}

			// if still empty the filter, just set to all.
			if (!self.currentFilter) {
				self.currentFilter = "all";
			}

			// Add loading class to the wrapper
			self.element.addClass('is-loading');

			EasySocial.ajax('site/controllers/videos/getVideos',{
				"filter": self.currentFilter,
				"categoryId": self.categoryId,
				"sort": self.currentSorting,
				"uid": opts.uid,
				"type": opts.type
			}).done(function(output) {

				// Stop the loading
				self.element.removeClass('is-loading');

				self.result().html(output);
			});
		},

		"{sorting} change": function(sorting, event) {

			// Set the current sorting
			self.currentSorting = sorting.val();

			if (sorting.val() != '') {
				var url = self.activeFilter().prop('href');

				if (url.indexOf('?') >= 0) {
					url = url + '&sort=' + sorting.val();
				} else {
					url = url + '?sort=' + sorting.val();
				}

				History.pushState( {state:1} , self.activeFilter().prop('title'), url );
			}

			// Get the videos
			self.getVideos();
		},

		"{filter} click": function(filter, event) {
			// Prevent bubbling up
			event.preventDefault();
			event.stopPropagation();

			var type = filter.data('type');

			// Route the inner filter links
			filter.route();

			// Add an active state to the parent
			self.setActiveFilter(filter);

			// Filter by category
			var categoryId = null;

			if (type == 'category') {
				type = 'all';
				categoryId = filter.data('id');
			}

			// Set the current filter
			self.currentFilter = type;
			self.categoryId = categoryId;

			self.getVideos(type, categoryId);
		},

		"{deleteButton} click": function(deleteButton, event) {

			var item = deleteButton.parents(self.item.selector);
			var id = item.data('id');

			EasySocial.dialog({
				content: EasySocial.ajax('site/views/videos/confirmDelete', {
					"id": id
				})
			});
		},

		"{unfeatureButton} click": function(unfeatureButton, event) {
			var item = unfeatureButton.parents(self.item.selector);
			var id = item.data('id');
			var returnUrl = unfeatureButton.data('return');

			var options = {
				"id": id
			};

			if (returnUrl.length > 0) {
				options["callbackUrl"] = returnUrl;
			}

			EasySocial.dialog({
				content: EasySocial.ajax('site/views/videos/confirmUnfeature', options)
			});
		},

		"{featureButton} click": function(featureButton, event) {
			var item = featureButton.parents(self.item.selector);
			var id = item.data('id');
			var returnUrl = featureButton.data('return');

			var options = {
				"id": id
			};

			if (returnUrl) {
				options["callbackUrl"] = returnUrl;
			}

			EasySocial.dialog({
				content: EasySocial.ajax('site/views/videos/confirmFeature', options)
			});
		}
	}});

	module.resolve();


	});

});
