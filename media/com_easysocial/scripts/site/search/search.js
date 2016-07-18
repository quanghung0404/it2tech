EasySocial.module( 'site/search/search' , function($){

	var module	= this;

	EasySocial.require()
	.library( 'history' )
	.script( 'site/search/sidebar', 'site/profile/friends' )
	.view( 'site/loading/small' )
	.done(function($){

		EasySocial.Controller(
		'Search',
		{
			defaultOptions:
			{
				// Properties
				items		: null,

				// Elements
				"{container}"	: "[data-search]",

				"{contentTitle}": "[data-search-content-title]",
				"{content}"		: "[data-search-content]",
				"{sidebar}"		: "[data-search-sidebar]",


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
					self.sidebar().implement( EasySocial.Controller.Search.Sidebar ,
					{
						"{parent}"	: self
					});

					self.sidebarItem().implement( EasySocial.Controller.Search.Sidebar.Item ,
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

				updateContent: function( content )
				{
					self.content().html( content );
				}

			}
		});

		module.resolve();
	});

});
