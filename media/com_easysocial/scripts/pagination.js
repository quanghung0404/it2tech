EasySocial.module( 'pagination' , function(){

var module	= this;


// Module begins here.
EasySocial.require()
.done(function($){

	EasySocial.Controller(
		'Pagination',
		{
			defaultOptions:
			{
				"{pages}"		: ".pageItem",
				"{limitstart}"	: "#limitstart",
				"{previousItem}": ".previousItem",
				"{nextItem}"	: ".nextItem"
			}
		},
		function(self)
		{
			return {
				init: function()
				{
					// Implement page item controller.
					self.pages().implement( EasySocial.Controller.Pagination.Page , {
						pagination : self
					});
				},

				"{previousItem} click" : function( elem )
				{
					var limitstart 	= $( elem ).data( 'limitstart' );

					if( $( elem ).hasClass( 'disabled' ) )
					{
						return;
					}
					
					self.submitForm( limitstart );
				},

				"{nextItem} click" : function( elem )
				{
					var limitstart 	= $( elem ).data( 'limitstart' );

					if( $( elem ).hasClass( 'disabled' ) )
					{
						return;
					}

					self.submitForm( limitstart );
				},

				submitForm: function( limitstart )
				{
					// Update the limitstart value in the page.
					self.limitstart().val( limitstart );

					// Send a submit for the form.
					$.Joomla( 'submitform' , [] );
				}
			} }

		);


	EasySocial.Controller(
		'Pagination.Page',
		{
			defaultOptions:
			{
				pagination	: null,
				limitstart	: 0
			}
		},
		function( self )
		{
			return {
				init: function()
				{
					self.options.limitstart 	= self.element.data( 'limitstart' );
				},

				"{self} click" : function()
				{
					// If the page is currently active, we can just ignore this.
					if( self.element.hasClass( 'active' ) )
					{
						return false;
					}

					// Submit the form.
					self.options.pagination.submitForm( self.options.limitstart );
				}
			}
		}
	);

	// Once require is done, we mark this module as resolved.
	module.resolve();

});


});