EasySocial.module( 'site/activities/sidebar.item' , function($){

	var module	= this;

	EasySocial.require()
	.library( 'history' )
	.done(function($){

		EasySocial.Controller(
			'Activities.Sidebar.Item',
			{
				defaultOptions:
				{
				}
			},
			function( self ){
				return {

					init: function()
					{
					},

					"{self} click" : function( el , event )
					{

						var type 	= self.element.data( 'type' ),
							url 	= self.element.data( 'url' ),
							title 	= self.element.data( 'title' ),
							desc 	= self.element.data( 'description' );

						// If this is an embedded layout, we need to play around with the push state.
						History.pushState( {state:1} , title , url );

						self.parent.updatingContents();

						//ajax call here.
						EasySocial.ajax( 'site/controllers/activities/getActivities',
						{
							"type"		: type
						})
						.done(function( html )
						{
							self.parent.updateContent( html, title );	
						})
						.fail(function( message ){
							console.log( message );
						});

						self.parent.updateContent();
					}
				}
			});

		module.resolve();
	});

});