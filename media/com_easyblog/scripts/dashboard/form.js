
// module: start
EasyBlog.module("dashboard/form", function($){

	var module = this;

	EasyBlog.Controller("Dashboard.Form", {
		
		defaultOptions: {

			"{checkAll}" : "[data-eb-form-checkall]",
			"{actions}": "[data-eb-form-actions]",
			"{checkboxes}" : "[data-eb-form-checkbox]",
			"{applyButton}" : "[data-eb-form-apply]",
			"{taskSelection}" : "[data-eb-form-task]",
			"{taskInput}" : "[data-table-grid-task]",

			"{filter}" : "[data-eb-form-filter]",
			"{search}" : "[data-eb-form-search]"
		}

	}, function(self) { 

		return {

			init: function() {

			},

			submitForm: function() {
				// We should just do a submit on this page.
				self.element.submit();
			},

			getSelectedCheckboxes: function()
			{
				var items = [],
					selected = self.checkboxes(':checked');

				selected.each(function(i, el) {
					items.push($(el).val());
				})

				return items;
			},

			"{checkboxes} change" : function(el, event)
			{
				var anyChecked = self.checkboxes(':checked').length > 0;

				if (anyChecked) {
					self.actions().removeClass('hide');
				} else {
					self.actions().addClass('hide');
				}
			},

			"{search} click" : function() {
				self.submitForm();
			},

			"{filter} click" : function() {
				// Set the task to empty
				self.taskInput().val('');

				// We should just do a submit on this page.
				self.submitForm();
			},

			"{applyButton} click" : function(el, event) {
				// Get the task
				var task = self.taskSelection().val();
				var confirmation = self.taskSelection().find(':selected').data('confirmation');

				// If task is empty, skipt this altogether
				if (task == '') {
					return false;
				}

				if (confirmation) {
					var items = this.getSelectedCheckboxes();
					
					if (items.length <= 0) {
						return false;
					}

					EasyBlog.dialog({
						content : EasyBlog.ajax(confirmation, {"ids": items}),
						bindings: {
							"{submitButton} click" : function()
							{
								// Set the task
								self.taskInput().val(task);

								// Submit the form
								self.submitForm();
							}
						}
					});

					return false;
				}

				// Set the task
				self.taskInput().val(task);

				// Submit the form now
				self.element.submit();
			},

			"{checkAll} change" : function(el, event)
			{
				var checked = self.checkAll().is(':checked');

				// Display actions
				if (checked) {
					self.actions().removeClass('hide');
				} else {
					self.actions().addClass('hide');
				}

				// Check / Uncheck all checkboxes
				self.checkboxes().prop('checked', checked);
			}
		}
	});

	module.resolve();

});
