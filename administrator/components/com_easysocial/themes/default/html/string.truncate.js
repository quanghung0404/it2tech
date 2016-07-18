EasySocial.ready(function($) {
    var selector = "[data-truncater-<?php echo $uid;?>]";

    $(selector).find('[data-truncater-more]').on('click', function() {

        // Hide the truncated items
        $(selector).find('[data-truncater-truncated]').addClass('hide');

        // Show the original untruncated text
        $(selector).find('[data-truncater-original]').removeClass('hide');

        // Hide the more
        $(this).addClass('hide');
    });

});
