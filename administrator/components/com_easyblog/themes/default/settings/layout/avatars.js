
EasyBlog.ready(function($){

    var avatarSource = $('[data-avatar-source]');

    if (avatarSource.val() == 'phpbb') {
        $('[data-phpbb-path]').removeClass('hidden');
    }

    avatarSource.on('change', function(){
        var source = $(this).val();

        if (source == 'phpbb') {
            $('[data-phpbb-path]').removeClass('hidden');

            return;
        }

        $('[data-phpbb-path]').addClass('hidden');
    });
});