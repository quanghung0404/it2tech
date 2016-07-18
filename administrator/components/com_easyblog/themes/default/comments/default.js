
EasyBlog.require()
.script('admin/grid')
.done(function($) {

    $('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

    $.Joomla("submitbutton", function(task) {
        $.Joomla('submitform', [task]);
    });
});