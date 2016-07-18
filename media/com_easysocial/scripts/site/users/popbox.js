EasySocial.module("site/users/popbox", function($) {

	var module = this;

	EasySocial.require()
		.library("popbox")
		.done(function(){

			EasySocial.module("users/popbox", function($) {

				this.resolve(function(popbox){

					var ids = popbox.button.data("ids"),
						position = popbox.button.attr("data-popbox-position") || "top-left";

					return {
						content: EasySocial.ajax("site/views/users/popbox", {ids: ids}),
						id: "fd",
						component: "es",
						type: "users",
						position: position
					}
				})
			});

		});

	module.resolve();

});
