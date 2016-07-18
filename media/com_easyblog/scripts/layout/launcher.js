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



