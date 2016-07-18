EasySocial.module( 'site/conversations/mailbox' , function($){

	var module 	= this;


	EasySocial.require()
	.library( 'history' )
	.done( function($){

		EasySocial.Controller(
			'Conversations.Mailbox',
			{
				defaultOptions:
				{
					"{item}"	: "[data-mailboxItem]"
				}
			},
			function( self ){

				return {

					init: function()
					{
						self.item().implement( EasySocial.Controller.Conversations.Mailbox.Item ,
						{
							"{parent}"	: self
						});
					},

					updateCounters: function()
					{
						self.item( '.active' ).controller().updateCounter();
					},

					updateContent : function( items , mailbox )
					{
						// Request the parent to update the contents.
						self.parent.updateContent( items , mailbox );
					},

					showEmpty: function()
					{
						self.parent.showEmpty();
					},

					hideEmpty: function()
					{
						self.parent.hideEmpty();
					},

					toggleLoading: function()
					{
						self.parent.toggleLoading();
					}
				}
			}
		);

		EasySocial.Controller(
			'Conversations.Mailbox.Item',
			{
				defaultOptions:
				{
					"{counter}"	: "[data-mailboxItem-counter]",

					view :
					{
						emptyTemplate : "site/conversations/default.item.empty"
					}
				}
			},
			function( self ){
				return {

					init: function()
					{

					},

					updateCounter: function()
					{
						EasySocial.ajax( 'site/controllers/conversations/getCount' ,
						{
							"mailbox"	: self.element.data( 'mailbox' )
						})
						.done(function( total ){

							// If there's no more new items, hide it.
							if( total <= 0 )
							{
								self.counter().html( '' );

								return;
							}

							self.counter().html( '(' + total + ')' );
						})
					},

					toggleLoading: function()
					{
						self.element.toggleClass( 'loading' );
					},

					"{self} click" : function()
					{
						var url 	= self.element.data( 'url' ),
							title 	= self.element.data( 'title' ),
							mailbox	= self.element.data( 'mailbox' );

						// Remove active class on all mailboxes.
						self.parent.item().removeClass( 'active' );

						// Add active class to this.
						self.element.addClass( 'active' );

						History.pushState( {state:1} , title , url );

						// Get contents via ajax.
						EasySocial.ajax( 'site/views/conversations/getItems' ,
						{
							"mailbox"	: mailbox,
							"limitstart" : 0
						},
						{
							beforeSend: function()
							{
								// Add loading indicator to the mailbox list.
								self.toggleLoading();

								// Add loading indicator.
								self.parent.toggleLoading();
							}
						})
						.done(function( content , empty ){

							// Remove loading class on the element.
							self.toggleLoading();

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
							self.parent.updateContent( content , self.element.data( 'mailbox' ) );

						});
					}
				}
		});

		module.resolve();
	});


});

