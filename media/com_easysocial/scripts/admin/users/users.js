EasySocial.module( 'admin/users/users' , function($) {

	var module = this;

	EasySocial
	.require()
	.library( 'expanding' )
	.done( function($)
	{

		EasySocial.Controller(
			'Users',
			{
				defaultOptions : 
				{
					"{item}"	: "[data-user-item]"
				}
			},
			function( self )
			{
				return {
					init : function()
					{
						self.item().implement( EasySocial.Controller.Users.Item );
					}
				}
			});

		EasySocial.Controller(
			'Users.Item',
			{
				defaultOptions : 
				{
					"{insertLink}"	: "[data-user-item-insertLink]"
				}
			},
			function( self )
			{
				return {
					init : function()
					{
						self.options.name 	= self.element.data( 'name' );
						self.options.avatar	= self.element.data( 'avatar' );
						self.options.email	= self.element.data( 'email' );
						self.options.id 	= self.element.data( 'id' );
					},

					"{insertLink} click" : function()
					{
						self.trigger( 'userSelected' , [ self.options.id , self.options.name , self.options.avatar , self.options.email ] );
					}
				}
			});


		EasySocial.Controller(
			'Users.Pending',
			{
				defaultOptions : 
				{
					"{item}"	: "[data-pending-item]"
				}
			},
			function( self )
			{
				return {
					init : function()
					{
						self.item().implement( EasySocial.Controller.Users.Pending.Item );
					}
				}
			});


		EasySocial.Controller(
			'Users.Pending.Item',
			{
				defaultOptions : 
				{
					"{approve}" : "[data-pending-approve]",
					"{reject}"	: "[data-pending-reject]"
				}
			},
			function( self )
			{
				return {
					init : function()
					{
						self.options.id 	= self.element.data( 'id' );
					},

					"{approve} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'admin/views/users/confirmApprove' , { "id" : self.options.id } ),
							bindings 	:
							{
								"{approveButton} click" : function()
								{
									$( '[data-users-approve-form]' ).submit();
								}
							}
						});
					},

					"{reject} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'admin/views/users/confirmReject' , { "id" : self.options.id } )
						});

					}
				}
			})		
		module.resolve();

	});

});