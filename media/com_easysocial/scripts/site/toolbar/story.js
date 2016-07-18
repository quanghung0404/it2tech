EasySocial.module( 'site/toolbar/story' , function($){

	var module 				= this;

	EasySocial.require()
	.done(function($){

		EasySocial.Controller(
			'Notifications.Story',
			{
				defaultOptions:
				{
					"{loadFormButton}"	: ".loadFormButton",
					"{dropdown}"		: ".dropdown-menu"
				}
			},
			function(self){ return{ 

				init: function()
				{
				},

				"{dropdown} click" : function( el , event )
				{
					// event.stopPropagation();
				},

				"{self} hideDropdown" : function()
				{
					// self.element.removeClass( 'open' );
				},

				"{dropdown} click" : function( el , event )
				{
					// Disallow clicking of events to trigger parent items.
					event.stopPropagation();
				}
			}}
		);

		module.resolve();
	});

});
