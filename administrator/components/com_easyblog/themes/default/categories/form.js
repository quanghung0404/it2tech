
EasyBlog.ready(function($) {

    $.Joomla( 'submitbutton' , function(task){

        if (task == 'category.cancel') {
            window.location     = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=categories';
            return false;            
        }

        if( task == 'saveNew' )
        {
            $( '#savenew' ).val( '1' );
            task    = 'save';
        }

        $.Joomla( 'submitform', [task] );
    });

    window.insertMember = function(id, username) {

        $( '#author-name' ).html( username ).show();
        $('#created_by').val( id );
            
        // Hide the dialog
        EasyBlog.dialog().close();
    }

    $('#private').on('change', function() {
        var val = $(this).val(),
            el = $('[data-category-access]');

        if (val == 2) {
            $(el).removeClass('hide');
        } else {
            $(el).addClass('hide');
        }
    });

    $('[data-browse-user]').on('click', function() {
        EasyBlog.dialog({
            content: EasyBlog.ajax('admin/views/bloggers/browse')
        });
    });
});