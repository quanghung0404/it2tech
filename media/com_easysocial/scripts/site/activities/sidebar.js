EasySocial.module( 'site/activities/sidebar' , function($){

	var module	= this;

	EasySocial.require()
	.done(function($){

		EasySocial.Controller(
			'Activities.Sidebar',
			{
				defaultOptions:
				{
					"{menuItem}"	: "[data-sidebar-menu]"
				}
			},
			function( self ){
				return {

					init: function()
					{
					},

					"{menuItem} click" : function( el , event )
					{
						// Remove all active class.
						self.menuItem().removeClass( 'active' );

						// Add active class on this item.
						$( el ).addClass( 'active' );
					}
				}
			});

		module.resolve();
	});

});