EasySocial.module( 'site/activities/activities' , function($){

	var module	= this;

	EasySocial.require()
	.script( 'site/activities/sidebar', 'site/activities/sidebar.item' )
	.view( 'site/loading/small' )
	.done(function($){

		EasySocial.Controller(
		'Activities',
		{
			defaultOptions:
			{
				// Properties
				items		: null,

				// Elements
				"{container}"	: "[data-activities]",

				"{contentTitle}": "[data-activities-content-title]",
				"{content}"		: "[data-activities-content]",
				"{sidebar}"		: "[data-activities-sidebar]",


				"{sidebarItem}"	: "[data-sidebar-item]",

				view :
				{
					loadingContent 	: "site/loading/small"
				}
			}
		},
		function( self ){
			return {

				init : function()
				{
					// Implement sidebar controller.
					self.sidebar().implement( EasySocial.Controller.Activities.Sidebar ,
					{
						"{parent}"	: self
					});

					self.sidebarItem().implement( EasySocial.Controller.Activities.Sidebar.Item ,
					{
						"{parent}"	: self
					});					
				},


				/**
				 * Add a loading icon on the content layer.
				 */
				updatingContents: function()
				{
					self.content().html( self.view.loadingContent() );
				},				

				updateContent: function( content, title )
				{
					self.content().html( content );
					self.contentTitle().html( title );
				}
							
			}
		});

		module.resolve();
	});

});