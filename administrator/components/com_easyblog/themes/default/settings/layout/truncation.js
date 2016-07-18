
EasyBlog.ready(function($)
{
    $('[data-truncate-type]').on('change', function() {
        var val = $(this).val();
        
        if (val == 'chars' || val == 'words') {
            $('[data-max-chars]').removeClass('hide');
            $('[data-max-tag]').addClass('hide');
        } else {
            $('[data-max-tag]').removeClass('hide');
            $('[data-max-chars]').addClass('hide');
        }
    });
});
