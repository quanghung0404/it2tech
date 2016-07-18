
// module: start
EasyBlog.module("dashboard/revisions", function($){

	var module = this;

	EasyBlog.require()
	.script('dashboard/form')
	.script('layout/dialog')
	.done(function($) {

		EasyBlog.Controller("Dashboard.Revisions", {
			defaultOptions: {

				"{item}"	: "[data-eb-post-item]"
			}

		}, function(self) {

			return {

				init: function() {

					// Implement basic form features
					self.element.implement(EasyBlog.Controller.Dashboard.Form);

					// Implement posts items
					self.item().implement(EasyBlog.Controller.Dashboard.Posts.Item);
				}
			}
		});

		EasyBlog.Controller("Dashboard.Posts.Item", {


			defaultOptions: {

				id 	: null,

				"{unpublish}" : "[data-post-unpublish]",
				"{publish}" : "[data-post-publish]",
				"{feature}" : "[data-post-feature]",
				"{unfeature}": "[data-post-unfeature]",
				"{delete}": "[data-post-delete]"
			}

		}, function(self) {

			return {

				init: function() {

					self.options.id 	= self.element.data('id');
				},

				"{delete} click" : function()
				{
					EasyBlog.dialog({
						content: EasyBlog.ajax('site/views/dashboard/confirmRevisionDelete', { "ids" : [self.options.id]})
					});
				}
			}
		});


		module.resolve();
	});
});
