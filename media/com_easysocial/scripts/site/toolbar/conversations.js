EasySocial.module( 'site/toolbar/conversations' , function($){

	var module = this;

	EasySocial.require()
	.library('tinyscrollbar')
	.done(function($){

		EasySocial.Controller(
			'Notifications.Conversations',
			{
				defaultOptions:
				{
					// Check every 10 seconds by default.
					interval : 30,

					// Elements within this container.
					"{counter}" : "[data-notificationConversation-counter]"
				}
			},
			function(self){ return{

				init: function()
				{
					// Start the automatic checking of new notifications.
					self.startMonitoring();
				},

				/**
				 * Start running checks.
				 */
				startMonitoring: function()
				{
					var interval 	= self.options.interval * 1000;

					// Debug
					if( EasySocial.debug )
					{
						// console.info( 'Start monitoring conversation notifications at interval of ' + self.options.interval + ' seconds.' );
					}

					self.options.state	= setTimeout( self.check , interval );
				},

				/**
				 * Stop running any checks.
				 */
				stopMonitoring: function()
				{
					// Debug
					if( EasySocial.debug )
					{
						// console.info( 'Stop monitoring conversation notifications.' );
					}

					clearTimeout( self.options.state );
				},

				/**
				 * Check for new updates
				 */
				check: function()
				{

					// Stop monitoring so that there wont be double calls at once.
					self.stopMonitoring();

					var interval 	= self.options.interval * 1000;

					// Needs to run in a loop since we need to keep checking for new notification items.
					setTimeout( function(){

						EasySocial.ajax( 'site/controllers/notifications/getConversationCounter')
						.done( function( total ){

							if( total > 0 )
							{
								// Add new notice on the toolbar
								self.element.addClass( 'has-notice' );

								// Update the counter's count.
								self.counter().html( total );
							}
							else
							{
								self.element.removeClass( 'has-notice' );
							}

							// Continue monitoring.
							self.startMonitoring();
						});

					}, interval );

				}
			}}
		);

		module.resolve();
	});

});
