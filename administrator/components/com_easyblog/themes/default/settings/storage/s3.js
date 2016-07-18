
EasyBlog.ready(function($)
{

    $('[data-amazon-bucket]').on('change', function()
    {
        var region = $(this).find(':selected').data('region');

        $('[data-amazon-region]').val(region);
    });
});