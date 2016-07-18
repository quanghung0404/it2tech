EasySocial.module( 'admin/polls/polls' , function($) {

	var module = this;

	EasySocial
	.require()
	.language( 
		'COM_EASYSOCIAL_CANCEL_BUTTON',
		'COM_EASYSOCIAL_CLOSE_BUTTON',
		'COM_EASYSOCIAL_POLLS_VIEW_POLLS_DIALOG_TITLE',
		'COM_EASYSOCIAL_POLLS_ACTIONS_DIALOG_TITLE'
	)
	.done( function($)
	{

		EasySocial.Controller(
			'Polls',
			{
				defaultOptions : 
				{
					"{item}"		: "[data-poll-item]"
				}
			},
			function( self )
			{
				return {
					init : function()
					{
						self.item().implement( EasySocial.Controller.Polls.Item )
					}
				}
			});

		EasySocial.Controller(
			'Polls.Item',
			{
				defaultOptions :
				{
					"{action}"		: "[data-polls-item-view-actions]"
				}
			},
			function( self )
			{
				return {
					init : function()
					{
						self.options.id 		= self.element.data( 'id' );
						self.options.extension	= self.element.data( 'extension' );
						self.options.uid 		= self.element.data( 'uid' );
						self.options.type 		= self.element.data( 'type' );
					},

					"{action} click" : function()
					{
						EasySocial.dialog( 
						{
							title 		: $.language( 'COM_EASYSOCIAL_REPORTS_ACTIONS_DIALOG_TITLE' ),
							content		: '<div>Perform some actions on the item</div>',
							width 		: 500,
							height 		: 250,
							buttons 	: 
							[
								{
									name 		: $.language( 'COM_EASYSOCIAL_CLOSE_BUTTON' ),
									classNames	: "btn btn-es",
									click 		: function()
									{
										EasySocial.dialog().close();
									}
								}
							]
						})
					}
				}
			})

		module.resolve();
	});

});