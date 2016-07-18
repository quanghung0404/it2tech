EasySocial.module( 'site/followers/followers' , function($){

	var module 	= this;

	EasySocial.require()
	.view( 'site/loading/small' )
	.script( 'site/conversations/composer' )
	.done(function($){

		EasySocial.Controller(
			'Followers',
			{
				defaultOptions :
				{
					"{content}"	: "[data-followers-content]",
					"{filter}"	: "[data-followers-filter]",
					"{items}"	: "[data-followers-item]",
					"{followingCounter}" : "[data-following-count]",
					'{suggestionCounter}' : "[data-suggest-count]",
					view :
					{
						loader 				: "site/loading/small"
					}
				}
			},
			function( self )
			{
				return {

					init : function()
					{
						self.initItemController();
					},

					initItemController: function()
					{
						self.items().implement( EasySocial.Controller.Followers.Item ,
						{
							"{parent}"	: self
						});
					},

					updateFollowingCounter: function( value )
					{
						var current 	= self.followingCounter().html(),
							updated		= parseInt( current ) + value;

						self.followingCounter().html( updated );
					},

					updateSuggestionCounter: function( value )
					{
						var current 	= self.suggestionCounter().html(),
							updated		= parseInt( current ) + value;

						self.suggestionCounter().html( updated );
					},

					updateContents : function( contents )
					{
						self.content().replaceWith( contents );
					},

					updatePagination : function( pagination )
					{
						$('[data-followers-pagination]').html( pagination );
					},

					"{filter} click" : function(filter, event) {

						var type 	= filter.data( 'followers-filter-type' ),
							title 	= filter.data( 'followers-filter-title' ),
							id 		= filter.data( 'followers-filter-id' ),
							url 	= filter.data( 'followers-filter-url' );

						// Remove active class on all filters
						self.filter().removeClass("active");

						// Add active class to current filter
						filter.addClass("active");

						History.pushState({state:1}, title, url);

						EasySocial.ajax(
							"site/controllers/followers/filter",
							{
								id: id,
								type: type
							})
							.done(function(contents, pagination){

								self.updateContents(contents);
								self.updatePagination(pagination);
								self.initItemController();
							});
					}
				}
			});

			EasySocial.Controller(
				'Followers.Item',
				{
					defaultOptions :
					{
						"{unfollowButton}"	: "[data-followers-item-unfollow]",
						"{followButton}"	: "[data-followers-item-follow]",
						"{composer}"		: "[data-followers-item-compose]"
					}
				},
				function( self )
				{
					return {
						init : function()
						{
							self.options.id 			= self.element.data( 'id' );

							self.initComposer();
						},

						initComposer: function()
						{
							self.composer().implement( EasySocial.Controller.Conversations.Composer.Dialog,
							{
								"recipient"	:
								{
									"id"	: self.options.id
								}
							});
						},

						"{unfollowButton} click" : function()
						{
							EasySocial.dialog(
							{
								content 	: EasySocial.ajax( 'site/views/followers/confirmUnfollow' , { 'id' : self.options.id }),
								bindings 	:
								{
									"{unfollowButton} click" : function()
									{
										EasySocial.ajax( 'site/controllers/followers/unfollow' , { "id" : self.options.id} )
										.done(function()
										{
											// Update the counter
											self.parent.updateFollowingCounter( -1 );

											// Remove this item
											self.element.remove();

											EasySocial.dialog().close();
										});
									}
								}
							});
						},

						"{followButton} click" : function()
						{
							EasySocial.ajax( 'site/controllers/followers/follow' , { "id" : self.options.id} )
							.done(function(content)
							{
								// Update the suggestion counter
								self.parent.updateSuggestionCounter(-1);

								//update following counter
								self.parent.updateFollowingCounter(1);

								// Remove this item
								self.element.html(content);

							})
				            .fail(function(messageObject) {
				                EasySocial.dialog({
				                    content: messageObject.message
				                });
				            });
						}
					}
				});
		module.resolve();
	});
});
