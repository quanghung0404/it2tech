EasySocial.module( 'admin/profiles/profiles' , function($) {

	var module = this;

	EasySocial
	.require()
	.done( function($)
	{

		EasySocial.Controller(
			'Profiles',
			{
				defaultOptions :
				{
					"{updateOrdering}"	: "[data-profiles-update-ordering]",
					"{item}"	: "[data-profiles-item]",

					view :
					{
						deleteConfirmation : 'admin/profiles/dialog.delete.confirm'
					}
				}
			},
			function(self)
			{
				return {

					init : function()
					{
						// Implement controller on each row.
						self.item().implement( EasySocial.Controller.Profiles.Item );
					},

					"{updateOrdering} click" : function()
					{
						// Check in all items
						$( '[data-table-checkall]' ).prop( 'checked' , true ).trigger( 'change' );

						$.Joomla( 'submitform' , [ 'updateOrdering' ] );
					}
				}
			});

		EasySocial.Controller(
		'Profiles.Item',
		{
			defaultOptions : 
			{
				"{insertLink}"		: "[data-profile-insert]"
			}
		},
		function( self )
		{
			return {
				init : function()
				{
					self.options.title 	= self.element.data( 'title' );
					self.options.id 	= self.element.data( 'id' );
				},

				"{insertLink} click" : function()
				{
					self.trigger( 'profileSelected' , [ self.options.id , self.options.title ] );
				}
			}
		});

		module.resolve();

	});

});