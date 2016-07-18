EasySocial.module( 'site/profile/subscriptions' , function($){

	var module 				= this;

	EasySocial.Controller(
		'Profile.Subscriptions',
		{
			defaultOptions:
			{
				// Properties
				id			: null,

				"{follow}"	: "[data-subscription-follow]",
				"{unfollow}": "[data-subscription-unfollow]",
				"{message}"	: "[data-subscription-message]",
				"{button}"	: "[data-subscription-button]"
			}
		},
		function(self)
		{
			return{

				init: function()
				{
					self.options.id 	= self.element.data( 'id' );
				},

				toggleDropDown : function()
				{
					self.element.toggleClass( 'open' );
				},

				"{unfollow} click" : function()
				{
					// Toggle dropdown.
					self.toggleDropDown();

					// Let's do an ajax call to follow the user.
					EasySocial.ajax( 'site/controllers/profile/unfollow' ,
					{
						"id"	: self.options.id,
						"type"	: 'user'
					})
					.done(function(button)
					{
						self.button().replaceWith( button );
					})
				},

				"{follow} click" : function()
				{
					// Toggle dropdown.
					self.toggleDropDown();

					// Let's do an ajax call to follow the user.
					EasySocial.ajax( 'site/controllers/profile/follow' ,
					{
						"id"	: self.options.id,
						"type"	: 'user'
					})
					.done(function( button )
					{
						self.button().replaceWith( button );
					});
				}
			}
		});

		module.resolve();

});
