
// module: start
EasyBlog.module("dashboard/moderate", function($){

	var module = this;

	EasyBlog.require()
	.script('dashboard/form')
	.done(function($)
	{
		EasyBlog.Controller("Dashboard.Moderate", {		
			defaultOptions: {

				"{item}"	: "[data-eb-moderate-item]"
			}

		}, function(self) { 

			return {

				init: function() {

					// Implement basic form features
					self.element.implement(EasyBlog.Controller.Dashboard.Form);

					// Implement posts items
					self.item().implement(EasyBlog.Controller.Dashboard.Moderate.Item);
				}
			}
		});

		EasyBlog.Controller("Dashboard.Moderate.Item", {
			

			defaultOptions: {

				id 	: null,

				"{action}"	: "[data-action]"
			}

		}, function(self) { 

			return {

				init: function() {

					self.options.id = self.element.data('id');
				},

				showDialog: function(action) {
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
