EasySocial.module( 'site/conversations/filter' , function($){

	var module 	= this;


	EasySocial.require()
	.done( function($){

		EasySocial.Controller(
			'Conversations.Filter',
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

					"{self} click" : function()
					{
						var type 		= self.element.data( 'filter' ),
							selector	= '.' + type,
							total 		= self.parent.item( selector ).length;

						if( $("[data-mailboxitem]").filter(".active").length == 1 )
						{
							var curActiveMenu = $("[data-mailboxitem]").filter(".active");

							var url 	= curActiveMenu.data( 'url' ),
								title 	= curActiveMenu.data( 'title' ),
								mailbox	= curActiveMenu.data( 'mailbox' );


							History.pushState( {state:1} , title , url );

							// Get contents via ajax.
							EasySocial.ajax( 'site/views/conversations/getItems' ,
							{
								"mailbox"	: mailbox,
								"filter" 	: type,
								"limitstart": 0
							},
							{
								beforeSend: function()
								{
									// Add loading indicator.
									self.parent.toggleLoading();
								}
							})
							.done(function( content , empty ){


								// Remove loading class on the content.
								self.parent.toggleLoading();

								if( content.length <= 0 )
								{
									// Empty the contents too to maintain the integrity of the checkbox
									self.parent.updateContent( '' );
									return self.parent.showEmpty();
								}

								// Hide empty class if it has items.
								self.parent.hideEmpty();

								// Now we'd need to update the content.
								self.parent.updateContent( content , mailbox );

							});



						}

						// if( type == 'all' )
						// {
						// 	if( self.parent.item().length == 0 )
						// 	{
						// 		self.parent.showEmpty();
						// 	}
						// 	else
						// 	{
						// 		self.parent.hideEmpty();
						// 	}

						// 	self.parent.item().show();
						// }
						// else
						// {

						// 	// Hide all conversations initially.
						// 	self.parent.item().hide();


						// 	if( total == 0 )
						// 	{
						// 		// Show empty.
						// 		self.parent.showEmpty();
						// 	}
						// 	else
						// 	{
						// 		// Always hide empty when there are items.
						// 		self.parent.hideEmpty();
						// 	}

						// 	// Only show the necessary item.
						// 	self.parent.item( "." + type ).show();
						// }
					}
				}
			}
		);

		module.resolve();
	});

});

