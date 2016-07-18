
EasyBlog.ready(function($){

    $.Joomla("submitbutton", function(action) {

        if (action == 'tags.cancel') {
            window.location = '/administrator/index.php?option=com_easyblog&view=tags';

            return;
        }

        $.Joomla("submitform", [action]);
    });

});