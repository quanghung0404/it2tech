
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/

EasySocial.require()
.script( 'admin/polls/polls' , 'admin/grid/grid' )
.done( function($){

	$( '[data-polls]' ).implement( EasySocial.Controller.Polls );
	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );

	$.Joomla( 'submitbutton' , function( task )
	{
		if( task == 'remove' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/polls/confirmDelete' ),
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
