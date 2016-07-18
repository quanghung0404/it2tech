
EasyBlog.ready(function($){

    var checkRules = function(type) {

        var value   = type == 'yes' ? 1 : 0;

        $('.btn-group-yesno .btn').removeClass('active');
        $('.btn-group-yesno .btn-' + type).addClass('active');

        $('.btn-group-yesno input[type="hidden"]').val(value);
    }

    $.Joomla("submitbutton", function(action) {

        if (action == 'acl.enable') {
            checkRules('yes');

            return false;
        } else if(action == 'acl.disable') {
            checkRules('no');

            return false;
        }

        if (action == 'cancel') {
            window.location = '<?php echo JRoute::_('index.php?option=com_easyblog&view=acls', false);?>';
            return;
        }

        $.Joomla("submitform", [action]);
    });

    window.insertMember = function(id, name) {
        $('#cid').val(id);
        $('#aclid').html(id);
        $('#aclname').val(name);
        $.Joomla("squeezebox").close();
    }

    $('.btn-group-yesno').on('change', function()
    {
        var value       = $(this).children('input[type="hidden"]').val();
            aclClass    = value == "1" ? 'acl-yes' : 'acl-no';

        $(this).parent()
            .removeClass('acl-yes')
            .removeClass('acl-no')
            .addClass(aclClass);
    });
});