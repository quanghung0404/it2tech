
EasyBlog.require()
.script('admin/grid')
.done(function($){
    
    // Implement controller on the form
    $('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

    $.Joomla('submitbutton', function(task)
    {
        if (task == 'fields.addGroup') {
            window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=fields&layout=groupForm'; 

            return false;
        }
        
        $.Joomla('submitform', [task]);

    });
});