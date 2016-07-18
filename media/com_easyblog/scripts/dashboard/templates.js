
// module: start
EasyBlog.module("dashboard/templates", function($){

	var module = this;

	EasyBlog.require()
	.done(function($)
	{
		EasyBlog.Controller("Dashboard.Templates", {		
			defaultOptions: {

				"{item}"	: "[data-template-item]",

				"{delete}": "[data-template-delete]"
			}

		}, function(self) { 

			return {

				init: function() {
				},

				"{delete} click": function(el, event)
				{
					var item = self.item.of(el),
						id = item.data('id');

					EasyBlog.dialog({
						content: EasyBlog.ajax('site/views/templates/confirmDeleteTemplate', {"ids" : [id]})
					});
				}
			}
		});

		module.resolve();
	});
});
