EasySocial.module('site/sidebar/sidebar', function($) {

	var module = this;

	$(document).on("click.es.sidebar", "[data-sidebar-toggle]", function(){

		// Prefer sidebar from siblings
		var button = $(this),
			selector = "[data-sidebar]",
			sidebar = button.siblings(selector);

		// If not find closest sidebar
		if (sidebar.length < 1) {
			sidebar = button.closest(selector);
		}

		// If not find any sidebar
		if (sidebar.length < 1) {
			sidebar = $(selector);
		}

		sidebar
			.toggleClass("sidebar-open")
			.trigger("sidebarToggle");
	});

	module.resolve();
});
