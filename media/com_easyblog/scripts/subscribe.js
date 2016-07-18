EasyBlog.module('subscribe', function($){

	var module = this;

	$(document).on('click.eb.subscribe', '[data-blog-subscribe]', function() {

		var type = $(this).data('type');
		var id = $(this).data('id');

		EasyBlog.dialog({
			content: EasyBlog.ajax('site/views/subscription/form', {"type": type, "id": id})
		});
	});

	$(document).on('click.eb.unsubscribe', '[data-blog-unsubscribe]', function() {

		// Get the subscription id
		var id = $(this).data('subscription-id');
		var redirect = $(this).data('return');


		console.log(id, redirect);

		// Ask for confirmation
		EasyBlog.dialog({
			content: EasyBlog.ajax('site/views/subscription/confirmUnsubscribe', {
				"id": id,
				"return": redirect
			}),
			bindings: {
				"{submitButton} click": function() {
					this.form().submit();
				}
			}
		})
	});

	module.resolve();

});
