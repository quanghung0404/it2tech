EasySocial.module( 'site/dashboard/groups' , function($){

	var module 				= this;

	EasySocial.require()
	.library( 'history' )
	.done(function($){

		EasySocial.Controller(
			'Dashboard.Groups',
			{
				defaultOptions:
				{
					"{item}"	: "[data-dashboard-group-item]",
					"{itemLink}": "[data-dashboard-group-item] > a"
				}
			},
			function(self)
			{

				return{

					init : function()
					{
						// console.log( 'here' , self.item() );
					},

					/**
					 * Fires when a feed link is clicked.
					 */
					"{item} click" : function( el , event )
					{
						event.preventDefault();

						$('.es-streams').removeClass( 'no-stream' );

						var type 	= $( el ).data( 'type' ),
							id		= $( el ).data( 'id' ),
							desc 	= $( el ).data( 'description' );

						// clear new feed counter
						self.element.removeClass( 'has-notice' );

						// If this is an embedded layout, we need to play around with the push state.
						$( el ).find( 'a' ).route();

						// Notify the dashboard that it's starting to fetch the contents.
						self.parent.content().html("");
						self.parent.updatingContents();

						self.element.addClass( 'loading' );

						EasySocial.ajax( 'site/controllers/dashboard/getStream' ,
						{
							"type"	: type,
							"id"	: id,
							"view" 	: "dashboard"
						})
						.done(function( contents )
						{
							self.parent.updateContents( contents );
						});
					}
				}
			});
		module.resolve();
	});

});
