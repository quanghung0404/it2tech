EasySocial.require()
.script( 'admin/grid/grid' )
.done(function($){

	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );

	$.Joomla( 'submitbutton' ,function(task)
	{
		if( task == 'uninstall' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/apps/confirmUninstall' ),
				bindings 	:
				{
					"{proceedButton} click" : function()
					{
						$.Joomla( 'submitform' , [task] );
					}
				}
			});

			return false;
		}

		$.Joomla( 'submitform' , [task] );
	});

});
