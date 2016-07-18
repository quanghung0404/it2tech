
// module: start
EasyBlog.module("dashboard/posts", function($){

	var module = this;

	EasyBlog.require()
	.script('dashboard/form')
	.script('layout/dialog')
	.done(function($) {
		
		EasyBlog.Controller("Dashboard.Posts", {		
			defaultOptions: {
				"{item}": "[data-eb-post-item]"
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
				"{delete}": "[data-post-delete]",
				"{autopost}": "[data-post-autopost]"
			}

		}, function(self) { 

			return {

				init: function() {
					self.options.id 	= self.element.data('id');
				},

				"{delete} click" : function() {
					EasyBlog.dialog({
						content: EasyBlog.ajax('site/views/dashboard/confirmDelete', { "ids" : [self.options.id]})
					});					
				},

				"{publish} click" : function() {

					EasyBlog.dialog({
						content: EasyBlog.ajax('site/views/dashboard/confirmPublish', { "ids" : [self.options.id]})
					});
				},

				"{unpublish} click" : function() {
					EasyBlog.dialog({
						content: EasyBlog.ajax('site/views/dashboard/confirmUnpublish', {"ids" : [self.options.id]})
					});
				},

				"{feature} click" : function() {
					EasyBlog.dialog({
						content: EasyBlog.ajax('site/views/dashboard/confirmFeature', {"id" : self.options.id})
					});
				},

				"{unfeature} click" : function() {
					EasyBlog.dialog({
						content: EasyBlog.ajax('site/views/dashboard/confirmUnfeature', {"id" : self.options.id})
					});
				},

				"{autopost} click": function(el) {
					var type = el.data('autopost-type');

					EasyBlog.dialog({
						content: EasyBlog.ajax('site/views/dashboard/confirmAutopost', {
							"id": self.options.id,
							"type": type
						})
					});
				}
			}
		});


		module.resolve();
	});
});
