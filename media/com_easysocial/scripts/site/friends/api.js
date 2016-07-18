EasySocial.module('site/friends/api', function($){

	var module = this;

	EasySocial.require()
		.library('dialog', 'popbox' )
		.done(function(){

			$( document )
				.on( 'click.es.friends.cancel' , '[data-es-friends-cancel]', function()
				{
					var element 	= $(this),
						friendId 	= element.data( 'es-friends-id' );

						// Show confirmation dialog
						EasySocial.dialog({
							content: EasySocial.ajax( 'site/views/friends/confirmCancel' ),
							bindings:
							{
								"{confirmButton} click": function()
								{
									EasySocial.ajax( 'site/controllers/friends/cancelRequest' ,
									{
										"id"	: friendId
									})
									.done( function()
									{
										// Hide the dialog once the request has been cancelled.
										EasySocial.dialog().close();
									});
								}
							}
						});
				});

			// Data API
			$(document)
				.on('click.es.friends.add', '[data-es-friends-add]', function(){

					var element = $(this);
					var userId = element.data( 'es-friends-id');
					var popboxContent = $.Deferred();
					var popboxOptions = {
						"content": popboxContent,
						"id" : "fd",
						"component": "es",
						"type": "api-friends",
						"toggle": "click"
					};

					// Display the popbox
					element.popbox('destroy');

					// Generate a new popbox instance
					element.popbox(popboxOptions);

					// Display the popbox
					element.popbox('show');

					// Run an ajax call now to perform the add friend request.
					EasySocial.ajax( 'site/controllers/friends/request' , {
						"viewCallback": "popboxRequest",
						"id": userId
					}).done(function(content) {

						popboxContent.resolve(content);

					});
				})

			module.resolve();
		});
});
