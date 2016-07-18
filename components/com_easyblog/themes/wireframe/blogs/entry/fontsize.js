
EasyBlog.ready(function($){

    // Bind event's on the font size changer.
    $('[data-font-resize]').on('click', function() {
        var text = $('[data-blog-content]'),
            current = $(text).css('font-size'),
            num = parseFloat(current, 10),
            unit = current.slice(-2),
            operation = $(this).data('operation');

        if (operation == 'increase') {
            num = num * 1.4;
        }

        if (operation == 'decrease') {
            num = num / 1.4;
        }

        $(text).css('font-size', num + unit);
    });
});