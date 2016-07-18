EasySocial.module('site/followers/api', function($){

	var module = this;

	EasySocial.require()
		.library('dialog', 'popbox')
		.done(function(){


			// Data API
			$(document)
				.on('click.es.followers.follow', '[data-es-followers-follow]', function(){

					var element 		= $(this),
						userId 			= element.data('es-followers-id'),
						popboxContent 	= $.Deferred(),
						showPopbox 		= element.data('es-show-popbox') == undefined || element.data('es-show-popbox') == "true" ? true : false;


						if (showPopbox) {
							element.popbox(
							{
								content	: popboxContent,
								id 		: "fd",
								component   : "es",
								type 	: "followers",
								toggle 	: "click"
							});

							element.popbox( 'show' );
						}

						// Let's do an ajax call to follow the user.
						EasySocial.ajax( 'site/controllers/profile/follow' ,
						{
							"id"	: userId,
							"type"	: 'user'
						})
						.done(function( button )
						{
							$(element).trigger('following', button);

							if (showPopbox) {
								EasySocial.ajax( 'site/views/profile/popboxFollow' , { "id" : userId } )
								.done(function(content)
								{
									popboxContent.resolve( content );
								});
							}
						});
				})

			// Data API
			$(document)
				.on('click.es.followers.unfollow', '[data-es-followers-unfollow]', function(){

					var element 		= $(this),
						userId 			= element.data( 'es-followers-id'),
						popboxContent 	= $.Deferred();

						element.popbox(
						{
							content	: popboxContent,
							id 		: "fd",
							component   : "es",
							type 	: "followers",
							toggle 	: "click"
						});

						element.popbox( 'show' );

						// Let's do an ajax call to follow the user.
						EasySocial.ajax( 'site/controllers/profile/unfollow' ,
						{
							"id"	: userId,
							"type"	: 'user'
						})
						.done(function( button )
						{
							EasySocial.ajax( 'site/views/profile/popboxUnfollow' , { "id" : userId } )
							.done(function(content)
							{
								popboxContent.resolve( content );
							});

						});
				});

			module.resolve();
		});
});
