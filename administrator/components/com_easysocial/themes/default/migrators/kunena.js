<?php defined( '_JEXEC' ) or die( 'Unauthorized Access' ); ?>

EasySocial.require()
.script( 'admin/migrators/migrator' )
.done(function($){
	// Implement discover controller.
	$( '.migratorsForm' ).implement(
		"EasySocial.Controller.Migrators.Migrator",
		{
			component: "kunena"
		});

    // Handle submit button.
    $.Joomla( 'submitbutton' , function( action )
    {
        if (action == 'purgeKunenaHistory') {
            EasySocial.dialog(
            {
                content     : EasySocial.ajax( 'admin/views/migrators/confirmPurge', {"type": "kunena"} ),
                bindings    :
                {
                    "{submitButton} click" : function()
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
