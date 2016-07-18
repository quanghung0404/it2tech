EasyBlog.module("quickpost/standard", function($){

	var module = this;

	EasyBlog.Controller('Quickpost.Form.Standard', {
		defaultOptions: {

			"{title}" : "[data-quickpost-title]",
			"{content}": "[data-quickpost-content]",
			"{tags}": "[data-quickpost-tags]",
			"{privacy}": "[data-quickpost-privacy]",
			"{category}": "[data-quickpost-category]"
		}
	}, function(self, opts, base) {

		return {
			init: function()
			{
			},

			"{self} onPublishQuickPost": function(el, event, save, type, form)
			{
				if (type != 'standard') {
					return;
				}

				// Get the values
				save.data = {
								"title": $(form).find(self.title).val(),
								"type": "text",
								"content": $(form).find(self.content).val(),
								"tags": $(form).find(self.tags).val(),
								"privacy": $(form).find(self.privacy).val(),
								"category": $(form).find(self.category).val()
							};
			},

			"{self} onClearForm": function(el, event, save, type, form)
			{
				if (type != 'standard') {
					return;
				}

				$(form).find(self.title.selector).val('');
				$(form).find(self.content.selector).val('');
				$(form).find(self.tags.selector).val('');

			}
		}
	});

	module.resolve();

});
