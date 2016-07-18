

EasyBlog.require()
.script('admin/grid')
.done(function($)
{
    // Implement controller on the form
    $('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

    $.Joomla('submitbutton', function(task) {


        if (task == 'subscriptions.form') {
            EasyBlog.dialog({
                content: EasyBlog.ajax('admin/views/subscriptions/form', {"type" : "<?php echo $filter;?>"})
            });

            return;
        }

        $.Joomla('submitform', [task]);
    });
});
