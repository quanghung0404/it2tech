
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/

EasySocial.require()
.script( 'admin/reports/reports' , 'admin/grid/grid' )
.done( function($){

	$( '[data-reports]' ).implement( EasySocial.Controller.Reports );
	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );

	$.Joomla( 'submitbutton' , function( task )
	{
		if( task == 'purge' )
		{
			EasySocial.dialog({
				content: EasySocial.ajax( 'admin/views/reports/confirmPurge' ),
				bindings:
				{
					"{confirmButton} click": function()
					{
						return $.Joomla( 'submitform' , [task] );
					}
				}
			});
		}

		if( task == 'remove' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/reports/confirmDelete' ),
				bindings	:
				{
					"{confirmButton} click" : function()
					{
						return $.Joomla( 'submitform' , [task] );
					}
				}
			})
		}

		return false;

	});
});
