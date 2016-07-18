EasyBlog.module("quickpost/quote", function($){

	var module = this;

	EasyBlog.Controller('Quickpost.Form.Quote', {
		defaultOptions: {

			"{quote}": "[data-quickpost-content]",
			"{content}": "[data-quickpost-source]",
			"{tags}": "[data-quickpost-tags]",
			"{privacy}": "[data-quickpost-privacy]",
			"{category}": "[data-quickpost-category]"
		}
	}, function(self) {
		return {
			init: function()
			{
			},
			"{self} onPublishQuickPost": function(el, event, save, type, form)
			{
				if (type != 'quote') {
					return;
				}

				// Get the values
				save.data = {
								"quote": $(form).find(self.quote).val(),
								"type": "quote",
								"source": $(form).find(self.content).val(),
								"tags": $(form).find(self.tags).val(),
								"privacy": $(form).find(self.privacy).val(),
								"category": $(form).find(self.category).val()
							};
			},

			"{self} onClearForm": function(el, event, save, type, form)
			{
				if (type != 'quote') {
					return;
				}

				$(form).find(self.quote).val('');
				$(form).find(self.content.selector).val('');
				$(form).find(self.tags.selector).val('');

			}
		}
	});

	module.resolve();

});
