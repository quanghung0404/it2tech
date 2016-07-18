
EasyBlog.ready(function($){

    // Cropping settings for listings
    $('[data-cover-crop]').on('change', function() {
        var value = $(this).val();

        // If cropping is disabled, we shouldn't display the height settings.
        if (value == 0) {
            $('[data-cover-height]').addClass('hide');

            return;
        }

        $('[data-cover-height]').removeClass('hide');
    });

    // Cropping settings for entry
    $('[data-cover-crop-entry]').on('change', function() {
        var value = $(this).val();

        // If cropping is disabled, we shouldn't display the height settings.
        if (value == 0) {
            $('[data-cover-height-entry]').addClass('hide');

            return;
        }

        $('[data-cover-height-entry]').removeClass('hide');
    });

    // When full width is checked
    $('[data-cover-full-width]').on('change', function() {

        var checked = $(this).is(':checked');
        var widthSettings = $('[data-cover-width]');
        var alignSettings = $('[data-cover-alignment]');

        if (checked) {
            widthSettings.attr('disabled', 'disabled');
            alignSettings.addClass('hide');

            return;
        }

        alignSettings.removeClass('hide');
        widthSettings.removeAttr('disabled');
    });


    $('[data-cover-full-width-entry]').on('change', function() {
        var checked = $(this).is(':checked');
        var widthSettings = $('[data-cover-width-entry]');
        var alignSettings = $('[data-cover-alignment-entry]');

        if (checked) {
            widthSettings.attr('disabled', 'disabled');
            alignSettings.addClass('hide');

            return;
        }

        alignSettings.removeClass('hide');
        widthSettings.removeAttr('disabled');
    });
});