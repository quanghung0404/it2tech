
EasyBlog.require()
.script('admin/grid')
.done(function($){

    // Implement the controller
    $('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

    $.Joomla("submitbutton", function( action ) {

        if (action == 'tags.new') {

            window.location = 'index.php?option=com_easyblog&view=tags&layout=form';
            return;
        }
        
        $.Joomla("submitform", [action]);
    });

});