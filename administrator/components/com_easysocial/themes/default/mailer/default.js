
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/

EasySocial.require()
.script( 'admin/mailer/mailer' , 'admin/grid/grid' )
.library( 'dialog' )
.done( function($){

	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );

	$( '[data-mailer-list]' ).implement( EasySocial.Controller.Mailer )

	// Handle submit button.
	$.Joomla( 'submitbutton' , function( action )
	{
		if( action == 'purgeAll' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/mailer/confirmPurgeAll' ),
				bindings 	:
				{
					"{purgeButton} click" : function()
					{
						Joomla.submitform( [action ] );
					}
				}
			});

			return false;
		}

		if( action == 'purgeSent' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/mailer/confirmPurgeSent' ),
				bindings 	:
				{
					"{purgeButton} click" : function()
					{
						Joomla.submitform( [action ] );
					}
				}
			});
			return false;
		}

		if( action == 'purgePending' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/mailer/confirmPurgePending' ),
				bindings 	:
				{
					"{purgeButton} click" : function()
					{
						Joomla.submitform( [action ] );
					}
				}
			});
			return false;
		}

		$.Joomla( 'submitform' , [ action ] );
	});


});
