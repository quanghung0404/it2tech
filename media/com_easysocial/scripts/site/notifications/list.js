
EasySocial.module( "site/notifications/list", function($){

	var module = this;


	EasySocial.require()
	.done( function($)
	{
		EasySocial.Controller( 'NotificationsList',
		{
			defaultOptions:
			{
				"{item}"		: "[data-notifications-list-item]",
				"{list}" 		: "[data-notifications-list]",
				"{allread}" 	: "[data-notification-all-read]",
				"{allclear}" 	: "[data-notification-all-clear]",

				"{notiLoadMoreBtn}" : "[data-notification-loadmore-btn]"
			}
		},
		function( self )
		{
			return {
				init : function()
				{
					self.item().implement( EasySocial.Controller.NotificationsList.Item ,
						{
							"{parent}"	: self
						});
				},

				"{allread} click" : function()
				{
					EasySocial.ajax( 'site/controllers/notifications/setAllState' ,
					{
						"state"	: "read"
					})
					.done(function()
					{
						self.item().removeClass( 'is-read is-hidden is-unread' ).addClass( 'is-read' );
					});
				},

				"{allclear} click" : function()
				{
					// show dialog to get confimation from user.
					var dialog =
						EasySocial.dialog({
							content: EasySocial.ajax(
								"site/views/notifications/clearAllConfirm"
							),
							bindings: {
								"{clearButton} click": function() {

									EasySocial.ajax( 'site/controllers/notifications/setAllState' ,
									{
										"state"	: "clear"
									})
									.done(function()
									{
										self.item().removeClass( 'is-read is-hidden is-unread is-read' ).addClass( 'is-remove' );
										EasySocial.dialog().close();
									});

								}
							}
						});
				},

				"{notiLoadMoreBtn} click" : function( el, event )
				{
					var startlimit 	= $(el).data( 'startlimit' );
					if( startlimit < 0 )
					{
						return;
					}

					EasySocial.ajax( 'site/controllers/notifications/loadmore' ,
					{
						"startlimit" : startlimit
					})
					.done(function( contents, nextlimit )
					{
						// update next limit
						$(el).data( 'startlimit', nextlimit );

						if( contents.length > 0 )
						{
							$.buildHTML(contents)
							 	.insertBefore( self.notiLoadMoreBtn() );
							 	// .addController("NotificationsList.Item");

							 //add controller
							 self.item().implement( EasySocial.Controller.NotificationsList.Item );
						}

						if( nextlimit < 0)
						{
							// no more item. let hide the loadmore button.
							self.notiLoadMoreBtn().hide();
						}

					})
					.fail( function( messageObj ){
						return messageObj;
					})
					.always(function(){

						// self.loading = false;
					});


				}



			}
		});

		EasySocial.Controller( 'NotificationsList.Item' ,
		{
			defaultOptions :
			{
				"{unread}"	: "[data-notifications-list-item-unread]",
				"{read}"	: "[data-notifications-list-item-read]",
				"{delete}"	: "[data-notifications-list-item-delete]"
			}
		},
		function(self)
		{
			return {
				init : function()
				{
					self.options.id 	= self.element.data( 'id' );

				},

				"{unread} click" : function()
				{
					EasySocial.ajax( 'site/controllers/notifications/setState' ,
					{
						"id"	: self.options.id,
						"state"	: "unread"
					})
					.done(function()
					{
						self.element.removeClass( 'is-read is-hidden is-unread' ).addClass( 'is-unread	' );
					});
				},

				"{read} click" : function()
				{
					EasySocial.ajax( 'site/controllers/notifications/setState' ,
					{
						"id"	: self.options.id,
						"state"	: "read"
					})
					.done(function()
					{
						self.element.removeClass( 'is-read is-hidden is-unread' ).addClass( 'is-read' );
					});
				},

				"{delete} click" : function()
				{


					var dialog =
						EasySocial.dialog({
							content: EasySocial.ajax(
								"site/views/notifications/clearConfirm"
							),
							bindings: {
								"{clearButton} click": function() {

									EasySocial.ajax( 'site/controllers/notifications/setState' ,
									{
										"id"	: self.options.id,
										"state"	: "clear"
									})
									.done(function()
									{
										self.element.removeClass( 'is-read is-hidden is-unread is-read' ).addClass( 'is-remove' );
										EasySocial.dialog().close();
									})
									.fail(function( msg )
									{
										EasySocial.dialog({
											content: msg.message
										});
									});
								}
							}
						});



					// EasySocial.ajax( 'site/controllers/notifications/setState' ,
					// {
					// 	"id"	: self.options.id,
					// 	"state"	: "hidden"
					// })
					// .done(function()
					// {
					// 	self.element.removeClass( 'is-read is-hidden is-unread' ).addClass( 'is-hidden' );
					// });


				}
			}
		});

		module.resolve();
	});

});
