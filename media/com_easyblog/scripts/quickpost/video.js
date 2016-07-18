EasyBlog.module("quickpost/video", function($){

	var module = this;

	EasyBlog.require()
	.done(function($) {

		EasyBlog.Controller('Quickpost.Form.Video', {
			defaultOptions: {

				"{form}": "[data-microblog-form]",
				"{loader}": "[data-quickpost-video-loader]",

				"{getVideo}": "[data-quickpost-video-retrieve]",
				"{title}" : "[data-quickpost-video-title]",
				"{content}": "[data-quickpost-video-source]",
				"{tags}": "[data-quickpost-tags]",
				"{privacy}": "[data-quickpost-privacy]",
				"{category}": "[data-quickpost-category]",

				"{preview}": "[data-quickpost-video-preview]"
			}
		}, function(self) {
			return {
				init: function() {
				},

				"{getVideo} click": function(btn, event) {
					event.preventDefault();

					self.loader().removeClass('hidden');

					EasyBlog.ajax('site/views/quickpost/getVideo', {
						link: self.content().val()
					}).done(function(embed){

						self.loader().addClass('hidden');

						self.preview().addClass('has-preview');
						self.preview().html(embed);
					});
				},

				"{self} onPublishQuickPost": function(el, event, save, type, form)
				{
					if (type != 'video') {
						return;
					}

					// Get the values
					save.data = {
									"title": $(form).find(self.title.selector).val(),
									"type": "video",
									"content": $(form).find(self.content.selector).val(),
									"tags": $(form).find(self.tags.selector).val(),
									"privacy": $(form).find(self.privacy.selector).val(),
									"category": $(form).find(self.category.selector).val()
								};
				},

				"{self} onClearForm": function(el, event, save, type, form)
				{
					if (type != 'video') {
						return;
					}

					$(form).find(self.title.selector).val('');
					$(form).find(self.content.selector).val('');
					$(form).find(self.tags.selector).val('');

					self.preview().removeClass('has-preview');
					self.preview().html('');

				}

			}
		});

		module.resolve();
	});

});
