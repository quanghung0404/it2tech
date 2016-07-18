<?php defined( '_JEXEC' ) or die( 'Unauthorized Access' ); ?>

EasySocial.require()
.script( 'admin/migrators/migrator' )
.done(function($)
{
	// Implement discover controller.
	$( '[data-cb-migrator-form]' ).implement(
		EasySocial.Controller.Migrators.Migrator,
		{
			component: "cb"
		});

    // Handle submit button.
    $.Joomla( 'submitbutton' , function( action )
    {
        if (action == 'purgeCbHistory') {
            EasySocial.dialog(
            {
                content     : EasySocial.ajax( 'admin/views/migrators/confirmPurge', {"type": "cb"} ),
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
