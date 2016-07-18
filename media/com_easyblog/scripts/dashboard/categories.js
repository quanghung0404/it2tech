
// module: start
EasyBlog.module("dashboard/categories", function($){

	var module = this;

	EasyBlog.require()
	.script('dashboard/form')
	.done(function($)
	{
		EasyBlog.Controller("Dashboard.Categories", {		
			defaultOptions: {

				"{item}"	: "[data-eb-category-item]",
				"{create}"	: "[data-eb-categories-create]"
			}

		}, function(self) { 

			return {

				init: function() {

					// Implement basic form features
					self.element.implement(EasyBlog.Controller.Dashboard.Form);

					// Implement posts items
					self.item().implement(EasyBlog.Controller.Dashboard.Categories.Item);
				},

				"{create} click" : function(el, data)
				{
					EasyBlog.dialog({
						content: EasyBlog.ajax('site/views/dashboard/categoryForm')
					});
				}
			}
		});

		EasyBlog.Controller("Dashboard.Categories.Item", {
			

			defaultOptions: {

				id 	: null,

				"{action}"	: "[data-action]"
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
