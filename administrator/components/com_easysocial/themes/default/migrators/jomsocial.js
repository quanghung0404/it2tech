<?php defined( '_JEXEC' ) or die( 'Unauthorized Access' ); ?>

EasySocial.require()
.script( 'admin/migrators/migrator' )
.done(function($){
	// Implement discover controller.
	$( '.migratorsForm' ).implement(
		"EasySocial.Controller.Migrators.Migrator",
		{
			component: "jomsocial"
		});

    // Handle submit button.
    $.Joomla( 'submitbutton' , function( action )
    {
        if (action == 'purgeJomsocialHistory') {
            EasySocial.dialog(
            {
                content     : EasySocial.ajax( 'admin/views/migrators/confirmPurge', {"type": "jomsocial"} ),
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
