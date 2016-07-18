EasySocial.module( 'admin/mailer/mailer' , function($) {

	var module = this;

	EasySocial.Controller(
		'Mailer',
		{
			defaultOptions :
			{
				"{item}"	: "[data-mailer-item]"
			}
		},
		function( self )
		{
			return {
				init : function()
				{
					self.item().implement( EasySocial.Controller.Mailer.Item );
				}
			}
		});

	EasySocial.Controller(
		'Mailer.Item',
		{
			defaultOptions :
			{
				"{preview}"	: "[data-mailer-item-preview]"
			}
		},
		function( self )
		{
			return {
				init : function()
				{
					self.options.id 	= self.element.data( 'id' );
				},

				"{preview} click" : function( el , event )
				{
					EasySocial.dialog(
					{
						content 	: EasySocial.ajax( 'admin/views/mailer/preview' , { 'id' : self.options.id } )
					})

				}
			}
		});

	module.resolve();

});
