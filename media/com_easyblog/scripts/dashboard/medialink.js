// module: start
EasyBlog.module("dashboard/medialink", function($) {

var module = this;

EasyBlog.Controller(
	"Dashboard.MediaLink",
	{
		defaultOptions: {
			"{menu}": ".ui-togmenu",
			"{content}": ".ui-togbox"
		}
	},
	function(self){ return {

		init: function() {

		},

		"{menu} click": function(el) {

			var hiding = el.hasClass("active");

			self.menu().removeClass("active");

			self.content().removeClass("active");

			if (!hiding) {

				el.addClass("active");

				self.content("." + el.attr("togbox")).addClass("active");
			}
		}
	}}
);

module.resolve();

});
// module: end
