EasySocial.module('site/stream/sidebar', function($) {

	var module = this;

	EasySocial.require()
	.done(function($){

		EasySocial.Controller('Stream.Filter.Sidebar', {
			defaultOptions: {
			}
		}, function(self) {

			return{

				init: function()
				{
				},
				"{self} click" : function()
				{
					$('[data-sidebar-item]').removeClass('active loading');
					self.element.addClass('active');

					var id = self.element.data('id'),
						url = self.element.data('url'),
						title = self.element.data('title');

					// If this is an embedded layout, we need to play around with the push state.
					History.pushState({state:1} , title , url);

					// Notify the dashboard that it's starting to fetch the contents.
					self.parent.content().html("");
					self.parent.updatingContents();

					self.element.addClass('loading');

					EasySocial.ajax('site/controllers/stream/getFilter', {
						"id": id
					})
					.done(function(contents) {
						self.parent.updateContents(contents);
					})
					.fail(function(messageObj) {
						return messageObj;
					})
					.always(function(){
						self.element.removeClass('loading');
					});
				}
			}
		});

		module.resolve();
	});

});
