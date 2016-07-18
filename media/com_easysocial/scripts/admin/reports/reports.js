EasySocial.module( 'admin/reports/reports' , function($) {

	var module = this;

	EasySocial
	.require()
	.language( 
		'COM_EASYSOCIAL_CANCEL_BUTTON',
		'COM_EASYSOCIAL_CLOSE_BUTTON',
		'COM_EASYSOCIAL_REPORTS_VIEW_REPORTS_DIALOG_TITLE',
		'COM_EASYSOCIAL_REPORTS_ACTIONS_DIALOG_TITLE'
	)
	.done( function($)
	{

		EasySocial.Controller(
			'Reports',
			{
				defaultOptions : 
				{
					"{item}"		: "[data-reports-item]"
				}
			},
			function( self )
			{
				return {
					init : function()
					{
						self.item().implement( EasySocial.Controller.Reports.Item )
					}
				}
			});

		EasySocial.Controller(
			'Reports.Item',
			{
				defaultOptions :
				{
					"{action}"		: "[data-reports-item-view-actions]",
					"{viewReports}"	: "[data-reports-item-view-reports]"
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

					"{viewReports} click" : function()
					{

						EasySocial.dialog(
						{
							title 		: $.language( 'COM_EASYSOCIAL_REPORTS_VIEW_REPORTS_DIALOG_TITLE' ),
							content 	: EasySocial.ajax( 'admin/controllers/reports/getReporters' , 
											{ 
												id 			: self.options.id
											}),
							width 		: 600,
							height 		: 450
						});

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