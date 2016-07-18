<?php defined( '_JEXEC' ) or die( 'Unauthorized Access' ); ?>

EasySocial.require()
.script( 'admin/migrators/migrator' )
.done(function($){
	// Implement discover controller.
	$( '.migratorsForm' ).implement(
		"EasySocial.Controller.Migrators.Migrator",
		{
			component: "jomsocialgroup"
		});

    // Handle submit button.
    $.Joomla( 'submitbutton' , function( action )
    {
        if (action == 'purgeJomsocialGroupHistory') {
            EasySocial.dialog(
            {
                content     : EasySocial.ajax( 'admin/views/migrators/confirmPurge', {"type": "jomsocialgroup"} ),
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
