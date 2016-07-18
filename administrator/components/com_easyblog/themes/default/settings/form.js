
EasyBlog.ready(function($){

    $.Joomla("submitbutton", function(task) {

        if (task == 'export') {
            window.location.href    = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=settings&format=raw&layout=export&tmpl=component';
            return;
        }

        if (task == 'import') {

            EasyBlog.dialog({
                "content": EasyBlog.ajax('admin/views/settings/import')
            });

            return;
        }

        $.Joomla("submitform", [task]);
    });

    window.switchFBPosition = function() {
        if( $('#main_facebook_like_position').val() == '1' )
        {
            $('#fb-likes-standard').hide();
            if( $('#standard').attr('checked') == true)
                $('#button_count').attr('checked', true);
        }
        else
        {
            $('#fb-likes-standard').show();
        }
    }

    <?php if ($activeTab) { ?>
        $('[data-form-tabs][href=#<?php echo $activeTab;?>]')
            .click();
    <?php } ?>

});