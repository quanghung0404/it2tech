EasyBlog.module('layout/responsive', function($) {

    var module = this;

    $(function(){

        $.responsive('.eb-responsive', [
        {
            "at": 1200,
            "switchTo": "wide"
        },
        {
            "at": 960,
            "switchTo": "wide w960"
        },
        {
            "at": 818,
            "switchTo": "wide w960 w768"
        },
        {
            "at": 600,
            "switchTo": "wide w960 w768 w600"
        },
        {
            "at": 560,
            "switchTo": "wide w960 w768 w600 w480"
        },
        {
            "at": 480,
            "switchTo": "wide w960 w768 w600 w480 w320"
        }
        ]);
    });


    //
    // New data-responsive API
    // <div class="myelement" data-responsive="800,600,400,300"></div>

    // Okay, look like some 3rd party template provider is also using data-responsive their their 'responsive' feature.
    // We need to have more specify selector for EB's blocks related data-resposive.
    var responsiveElement_ = "#fd.eb [data-responsive]";

    var setResponsiveLayout = function() {

        $(responsiveElement_).each(function(){

            var responsiveElement = $(this);

            var elementWidth = responsiveElement.outerWidth();
            var widths = responsiveElement.data("responsive").split(",");
            var width;
            var classnamesToDiscard = []
            var classnamesToUse = [];

            while (width = widths.shift()) {
                (elementWidth <= width ? classnamesToUse : classnamesToDiscard).push("w" + width);
            }

            responsiveElement
                .removeClass(classnamesToDiscard.join(" "))
                .addClass(classnamesToUse.join(" "));
        });
    }

    // Set responsive layout on document ready
    $(document).ready(setResponsiveLayout);

    // Set responsive layout on window load and resize
    $(window)
        .on("load.responsive", setResponsiveLayout)
        .on("resize.responsive_", $.debounce(setResponsiveLayout, 350));


    module.resolve();

});
