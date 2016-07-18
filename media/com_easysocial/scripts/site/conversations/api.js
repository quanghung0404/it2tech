EasySocial.module('site/conversations/api', function($){

	var module = this;

	EasySocial.require()
		.library('dialog')
		.done(function(){

			// Data API
			$(document)
				.on('click.es.conversations.compose', '[data-es-conversations-compose]', function(){

					

					var element 	= $(this),
						userId 		= element.data( 'es-conversations-id'),
						listId 		= element.data( 'es-conversations-listid' );


					EasySocial.dialog(
					{
						"content"	: EasySocial.ajax( 'site/views/conversations/composer' , { "id" : userId , "listId" : listId } ),
						"bindings"	:
						{
							"{sendButton} click" : function()
							{
								var recipients 	= $( 'input[name=recipient\\[\\]]' ),
									message 	= $( '[data-composer-message]' ).val(),
									uids 		= new Array,
									dialog 		= this.parent;

								$( recipients ).each( function()
								{
									uids.push( $( this ).val() );
								});
								
								EasySocial.ajax( 'site/controllers/conversations/store' ,
								{
									"uid"		: uids,
									"message"	: message
								})
								.done(function( link )
								{
									if( userId )
									{
										EasySocial.dialog(
										{
											"content"	: EasySocial.ajax( 'site/views/conversations/sent' , { "id" : userId }),
											"bindings"	:
											{
												"{viewButton} click" : function()
												{
													document.location 	= link;
												}
											}
										});
									}

									if( listId )
									{
										EasySocial.dialog(
										{
											"content"	: EasySocial.ajax( 'site/views/conversations/sentList' , { "id" : listId }),
											"bindings"	:
											{
												"{viewButton} click" : function()
												{
													document.location 	= link;
												}
											}
										});
									}
								})
								.fail(function(message) {
									dialog.setMessage(message);
								});
							}
						}
					});
				});

			module.resolve();
		});
});