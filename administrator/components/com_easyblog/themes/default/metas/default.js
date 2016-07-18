EasyBlog.require()
.script('admin/grid')
.done(function($)
{
	// Implement controller on the form
	$('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

	$.Joomla( 'submitbutton' , function( action ) {

        if (action == 'meta.cancel') {
            alert('test');
            window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog';
            return false;
        }

		$.Joomla( 'submitform' , [ action ] );
	});

});
