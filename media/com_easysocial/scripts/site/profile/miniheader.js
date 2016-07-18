EasySocial.module( 'site/profile/miniheader' , function($){

	var module = this;

	EasySocial.require()
	.library(
		'scrollTo'
	)
	.done(function($){

		EasySocial.Controller(
			'Profile.MiniHeader',
			{
				defaultOptions: {

					"{viewport}": "[data-appscroll-viewport]",
					"{content}": "[data-appscroll-content]",
					"{apps}": "[data-appscroll-content] li",
					"{buttons}": "[data-appscroll-buttons]",
					"{nextButton}": "[data-appscroll-next-button]",
					"{prevButton}": "[data-appscroll-prev-button]"
				}
			},
			function(self){ return {

				init: function() {

					self.setLayout();

					// When page is refreshed, scroll value might be retained.
					self.viewport().scrollTo(0);
				},

				"{window} resize": $.debounce(function(){

					self.setLayout();

				}, 300),

				setLayout: function() {

					var viewport = self.viewport(),
						width = 5;

					if ($(".es-main").hasClass("w480")) {

						self.content().css({
							width: "auto"
						});

						self.enabled = false;

						return;
					}

					self.apps().each(function(){ width += $(this).outerWidth(true) });

					if (width > viewport.width()) {

						self.content()
							.css({
								width: width,
								float: "none"
							});

						self.buttons()
							.css("opacity", 1);

						self.enabled = true;
					}
				},

				enabled: false,

				"{nextButton} click": function() {

					if (!self.enabled) return;

					var viewport = self.viewport(),
						width = viewport.width() - 80; // 80 offset

					viewport.scrollTo('+=' + width + 'px', 800, {axis: 'x', easing: 'easeInOutCubic'});
				},

				"{prevButton} click": function() {

					if (!self.enabled) return;

					var viewport = self.viewport(),
						width = viewport.width() - 80; // 80 offset

					viewport.scrollTo('-=' + width + 'px', 800, {axis: 'x', easing: 'easeInOutCubic'});
				}

			}});


		module.resolve();
	});


});
