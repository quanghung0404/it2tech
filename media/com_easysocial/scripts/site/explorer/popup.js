EasySocial.module('site/explorer/popup', function($) {

var module = this;

EasySocial.require()
.view("site/explorer/popup")
.done(function(){

	$.Controller("Explorer/Popup",
	{
		defaultOptions: {

			view: {
				popup: "easysocial/site/explorer/popup"
			},

			"{popup}"   : "[data-explorer-popup]",
			"{viewport}": "[data-popup-viewport]",
			"{explorer}": "[data-explorer-popup] .fd-explorer",
			"{closeButton}": ".fd-explorer .close-button"
		}
	},
	function(self, opts, base) { return {

		init: function() {

		},

		"{window} resize": $.debounce(function() {

		}, 100),

		show: function() {

			var popup,
				node = self.popup.node;

			// Create node if not exists
			if (!node) {
				popup = self.view.popup();
				node  = self.popup.node = popup[0];
			}

			// Append node if detached
			if (!$.contains(base, node)) {
				popup = $(node).appendTo(base);
			}

			if (!popup.is(":visible")) {
				popup.show().trigger("show");
			}
		},

		hide: function() {

			self.popup()
				.hide()
				.trigger("hide")
				.detach();
		},

		// options: uid, type, url
		open: function(options) {

			self.show();

			var task = $.Deferred();

			var existingExplorer = self.explorer();

			if (existingExplorer.length > 0 &&
				existingExplorer.data("uid")===options.uid &&
				existingExplorer.data("type")===options.type) {
				return task.resolve(existingExplorer.explorer("controller"), self);
			}

			EasySocial.ajax("site/views/explorer/browser", options)
				.done(function(html){

					var browser = $.buildHTML(html);

					self.viewport()
						.empty()
						.append(browser);

					var explorer = browser.filter(".fd-explorer").explorer("controller");

					task.resolve(explorer, self);
				})
				.fail(function(){

					task.reject();
				});

			return task;
		},

		"{self} click": function(el, event) {

			if (event.target===self.popup()[0]) {
				self.hide();
			}
		},

		"{closeButton} click": function() {
			self.hide();
		}

	}});

	var instance = EasySocial.explorer = $("body").addController("Explorer/Popup");

	module.resolve(instance);
});

});
