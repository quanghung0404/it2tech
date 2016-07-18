
EasyBlog.ready(function($){

    // Cropping settings for listings
    $('[data-cover-featured-crop]').on('change', function() {
        var value = $(this).val();

        // If cropping is disabled, we shouldn't display the height settings.
        if (value == 0) {
            $('[data-cover-featured-height]').addClass('hide');

            return;
        }

        $('[data-cover-featured-height]').removeClass('hide');
    });
});