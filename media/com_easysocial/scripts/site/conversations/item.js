EasySocial.module( 'site/conversations/item' , function($){

	var module 	= this;


	EasySocial.require()
	.script( 'site/conversations/mailbox' )
	.done( function($){

		EasySocial.Controller(
			'Conversations.Item',
			{
				defaultOptions:
				{
					"{checkbox}"	: "[data-conversationItem-checkbox]"
				}
			},
			function( self ){

				return {

					init: function()
					{
					},

					"{checkbox} change": function( el ){

						var checked = $( el ).is( ':checked' );

						if( checked )
						{
							return self.element.addClass( 'selected' );
						}

						return self.element.removeClass( 'selected' );
					}
				}
			}
		);

		module.resolve();
	});

});

