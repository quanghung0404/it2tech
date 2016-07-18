EasyBlog.module("quickpost/link", function($){

	var module = this;

	EasyBlog.Controller('Quickpost.Form.Link', {
		defaultOptions: {

			"{crawl}": "[data-quickpost-crawl-link]",
			"{loader}": "[data-quickpost-crawl-loader]",
			"{form}": "[data-quickpost-form]",
			"{link}": "[data-quickpost-link]",
			"{preview}": "[data-quickpost-link-preview]",

			"{title}" : "[data-quickpost-title]",
			"{content}": "[data-quickpost-content]",
			"{tags}": "[data-quickpost-tags]",
			"{privacy}": "[data-quickpost-privacy]",
			"{category}": "[data-quickpost-category]"
		}
	}, function(self) {
		return {
			init: function()
			{
			},

			"{crawl} click": function(el, event)
			{
				event.preventDefault();

				var url = self.link().val();
					form = $(el).parents(self.form.selector);

				// Display the loader
				self.loader().removeClass('hidden');

				EasyBlog.ajax('site/views/crawler/crawl', {
					"url": url
				}).done(function(result) {

					// Hide the loader.
					self.loader().addClass('hidden');

					var data = result[url],
						description = data.description,
						title = data.title;

					form.find(self.title.selector).val(title);
					form.find(self.content.selector).val(description);

					// Show the preview area
					self.preview().removeClass('hide');

				});
			},

			"{self} onPublishQuickPost": function(el, event, save, type, form)
			{
				if (type != 'link') {
					return;
				}

				// Get the values
				save.data = {
								"title": $(form).find(self.title.selector).val(),
								"type": "link",
								"content": $(form).find(self.content.selector).val(),
								"link": $(form).find(self.link.selector).val(),
								"tags": $(form).find(self.tags.selector).val(),
								"privacy": $(form).find(self.privacy.selector).val(),
								"category": $(form).find(self.category.selector).val()
							};
			},

			"{self} onClearForm": function(el, event, save, type, form)
			{
				if (type != 'link') {
					return;
				}

				$(form).find(self.title.selector).val('');
				$(form).find(self.content.selector).val('');
				$(form).find(self.link.selector).val('');
				$(form).find(self.tags.selector).val('');

				self.preview().addClass('hide');

			}
		}
	});

	module.resolve();

});
