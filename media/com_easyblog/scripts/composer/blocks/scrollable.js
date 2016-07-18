EasyBlog.module("composer/blocks/scrollable", function($) {

var module = this;

EasyBlog.Controller("Composer.Blocks.Scrollable", {
    defaultOptions: $.extend({
    }, EBD.selectors),
}, function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {
        blocks = self.blocks;
        composer = blocks.composer;
    },

    "{block} dragstart": function() {

        self.scrollstart();
    },

    "{blocks.menu} dragstart": function() {

        self.scrollstart();
    },

    "{block} dragstop": function() {

        self.scrollstop();
    },

    "{blocks.menu} dragstop": function() {

        self.scrollstop();
    },

    "{self} composerBlockDrop": function() {

        self.scrollstop();
    },

    stop: false,

    scrollTimer: null,

    scrollstart: function() {

        var viewport = composer.viewport(),
            viewportContent = composer.document.viewportContent()[0],
            viewportHeight = viewport.height(),
            topToleranceArea = 50,
            bottomToleranceArea = viewportHeight - 50,
            position,

            autoScroll = function() {

                // This would allow the next scroll event to happens
                self.stop = false;

                // Determines if the scroll event is hovering within the tolerance area
                if (position.y > bottomToleranceArea || position.y < topToleranceArea) {

                    // Prevents the next event from being executed
                    self.stop = true;

                    if (position.y > bottomToleranceArea) {
                        viewportContent.scrollTop += (viewportHeight / 2);
                    }

                    if (position.y < topToleranceArea) {
                        viewportContent.scrollTop -= (viewportHeight / 2);
                    }

                    clearTimeout(self.scrollTimer);

                    self.scrollTimer = setTimeout(autoScroll, 1000);

                    self.trigger("composerDocumentScroll");
                }
            };

        $(document).on($.ns("mousemove touchmove", ".scrollable"), function(event){

            position = $.getPointerPosition(event);

            // Reset the top
            if (self.stop && position.y > topToleranceArea && position.y < bottomToleranceArea) {
                self.stop = false;
            }

            // Determines if we should trigger this
            if (self.stop) {
                return;
            }

            // If user tries to place the block 50 pixels from the bottom, we want it to scroll automatically.
            autoScroll();
        });
    },

    scrollstop: function() {

        clearTimeout(self.scrollTimer);

        $(document).off($.ns("mousemove touchmove", ".scrollable"));
    }

}});

module.resolve();

});
