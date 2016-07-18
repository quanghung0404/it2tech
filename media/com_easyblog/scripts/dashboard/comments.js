
// module: start
EasyBlog.module("dashboard/comments", function($){

	var module = this;

	EasyBlog.require()
	.script('dashboard/form')
	.done(function($)
	{
		EasyBlog.Controller("Dashboard.Comments", {		
			defaultOptions: {

				"{item}"	: "[data-eb-comment-item]"
			}

		}, function(self) { 

			return {

				init: function() {

					// Implement basic form features
					self.element.implement(EasyBlog.Controller.Dashboard.Form);

					// Implement posts items
					self.item().implement(EasyBlog.Controller.Dashboard.Comments.Item);
				}
			}
		});

		EasyBlog.Controller("Dashboard.Comments.Item", {
			

			defaultOptions: {

				id 	: null,

				"{action}"	: "[data-action]",

				"{unpublish}" : "[data-unpublish]",
				"{publish}" : "[data-publish]",
				"{edit}": "[data-edit]",
				"{delete}": "[data-delete]"
			}

		}, function(self) { 

			return {

				init: function() {

					self.options.id 	= self.element.data('id');
				},

				showDialog: function(action)
				{
					EasyBlog.dialog({
						content: EasyBlog.ajax(action, { "ids" : [self.options.id]}),
						bindings:
						{
							"{submitButton} click" : function()
							{
								this.form().submit();
							}
						}
					});	
				},

				"{action} click" : function(el, data)
				{
					var type = $(el).data('type'),
						action = $(el).data('action');

					if (type == 'dialog') {
						self.showDialog(action);
					}

				}
			}
		});


		module.resolve();
	});
});
