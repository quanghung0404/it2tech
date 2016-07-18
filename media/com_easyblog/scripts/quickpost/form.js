EasyBlog.module("quickpost/form", function($) {

	var module = this;

	EasyBlog.require()
	.script('quickpost/standard')
	.script('quickpost/quote')
	.script('quickpost/link')
	.script('quickpost/photo')
	.script('quickpost/video')
	.done(function($) {

		EasyBlog.Controller('Quickpost.Form', {
			defaultOptions: {

				"{form}": "[data-quickpost-form]",
				"{publishButton}": "[data-quickpost-publish]",
				"{alert}": "[data-quickpost-alert]",
				"{toggleExtended}": "[data-quickpost-extended-toggle]",
				"{extended}": "[data-quickpost-extended]",
				"{extendedPanel}": "[data-quickpost-extended-panel]",
				"{autopost}": "[data-autopost-item]",
				"{loader}": "[data-quickpost-loader]"
			}
		}, function(self) {
			return {

				init: function() {
					self.addPlugin('standard', 'EasyBlog.Controller.Quickpost.Form.Standard');
					self.addPlugin('quote', 'EasyBlog.Controller.Quickpost.Form.Quote');
					self.addPlugin('link', 'EasyBlog.Controller.Quickpost.Form.Link');
					self.addPlugin('photo', 'EasyBlog.Controller.Quickpost.Form.Photo');
					self.addPlugin('video', 'EasyBlog.Controller.Quickpost.Form.Video');
				},

				"{toggleExtended} click": function(el, event) {
					var parent = self.extended.of(el),
						panel = parent.find(self.extendedPanel.selector);

					panel.toggleClass('hide');
				},

				"{publishButton} click": function(el, event) {
					var form = self.form.of(el),
						type = form.data('type'),
						save = $.Task();

					self.trigger('onPublishQuickPost', [save, type, form]);

					// Set the default autopost
					save.data.autopost = [];

					// Iterate through each checked auto post clients
					form.find(self.autopost.selector + ':checked').each(function(){
						var client = $(this).val();

						save.data.autopost.push(client);
					});

					// Show the loading indication
					self.loader().removeClass('hide');

					save.process()
						.done(function(){

							EasyBlog.ajax('site/views/quickpost/save', save.data)
								.done(function(exception){
									self.alert()
										.addClass('alert-success')
										.removeClass('hide alert-danger')
										.html(exception.message);


									self.trigger('onClearForm', [save, type, form]);

								})
								.fail(function(exception) {
									self.alert()
										.addClass('alert-danger')
										.removeClass('hide alert-success')
										.html(exception.message);
								})
								.always(function(){
									self.loader().addClass('hide');
								});

						});
				}
			}
		});

		module.resolve();
	});

});
