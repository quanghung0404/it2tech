
EasyBlog.require()
.script('admin/grid')
.done(function($) {

    $('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

    $.Joomla("submitbutton", function(action) {

        if (action == 'bloggers.create') {
            window.location     = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=bloggers&layout=form';
            return false;
        }

        $.Joomla('submitform', [action]);
    });
});
