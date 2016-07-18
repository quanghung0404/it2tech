
EasyBlog.require()
.script('admin/grid')
.done(function($)
{
    $('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

    $.Joomla('submitbutton', function(task) {

        if (task == 'themes.recompile') {
            
            var selected = $('[data-table-grid-id]:checked');

            if (selected.length > 0) {
                $.Joomla('submitform', [task]);
                return;
            }
            
            alert('Please select a theme first.');
            return;
        }

        $.Joomla('submitform', [task]);
    });
});