EasySocial.module( 'site/dashboard/apps' , function($){

	var module 				= this;

	EasySocial.require()
	.library( 'history' )
	.done(function($){

		EasySocial.Controller(
			'Dashboard.Apps',
			{
				defaultOptions:
				{
					parent		: null,
					pageTitle	: null,
					"{item}"	: "[data-dashboardApps-item]"
				}
			},
			function(self){

				return{

					init : function()
					{
						self.item().implement( EasySocial.Controller.Dashboard.Apps.Item ,
						{
							"{parent}"		: self,
							"{dashboard}"	: self.parent,
							pageTitle 		: self.options.pageTitle
						});
					}
				}
			});

		EasySocial.Controller(
			'Dashboard.Apps.Item',
			{
				defaultOptions:
				{
				}
			}, function(self){

				return{

					init : function()
					{
					},

					"{self} click" : function( el , event )
					{
						// Prevent from bubbling up.
						event.preventDefault();

						// Get the layout meta.
						var layout 	= self.element.data( 'layout' ),
							url 	= self.element.data( layout + '-url' ),
							title 	= self.element.data( 'title' ),
							desc 	= self.element.data( 'description' ),
							appId 	= self.element.data( 'id' );

						// If this is a canvas layout, redirect the user to the canvas view.
						if( layout == 'canvas' )
						{
							window.location 	= url;
							return;
						}

						title 	= $._.isEmpty( self.options.pageTitle ) ? title : self.options.pageTitle;

						// If this is an embedded layout, we need to play around with the push state.
						History.pushState( {state:1} , title , url );

						// Notify the dashboard that it's starting to fetch the contents.
						self.dashboard.content().html("");
						self.dashboard.updatingContents();

						self.element.addClass( 'loading' );

						// Send a request to the dashboard to update the content from the specific app.
						EasySocial.ajax( 'site/controllers/dashboard/getAppContents' ,
						{
							"appId"		: appId
						})
						.done( function( contents )
						{
							self.dashboard.updateHeading( title , desc );

							self.dashboard.updateContents( contents );

						})
						.fail(function( messageObj ){

							return messageObj;

						})
						.always(function(){

							self.element.removeClass( 'loading' );

						});

					}


				}
			});
		module.resolve();
	});

});
