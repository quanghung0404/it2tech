EasyBlog.module("composer/blocks/handlers/compare", function($){

var module = this;

EasyBlog.require()
.library(
    "imgareaselect"
)
.done(function(){

    EasyBlog.Controller("Composer.Blocks.Handlers.Compare",
    {
        defaultOptions: {

            image: "[data-type=compare] img"
        }
    },
    function(self, opts, base, composer, blocks, meta, currentBlock) { return {

        init: function() {

            // Globals
            blocks       = self.blocks;
            composer     = blocks.composer;
            meta         = opts.meta;
            currentBlock = $();
        },

        toData: function(block) {
            return blocks.getData(block);
        },

        activate: function(block) {

            // Set as current block
            currentBlock = block;

            // Populate fielset
            self.populate(block);

            currentBlock.find("img")
                .on("click.compare dragstart.compare", function(event){
                    event.preventDefault();
                    event.stopPropagation();
                })
                .on("mousedown.compare", function(event){

                    if (self.drawing) return;

                    event.stopPropagation();
                    event.preventDefault();

                    // Get block
                    var image = $(this),
                        block = image.parents(".ebd-block:first")
                        images = block.find("img");

                    console.log(block);

                    self.drawing = true;
                    base.addClass("active");

                    // Initial cover position
                    var oy = parseInt(image.css("top")),
                        ox = parseInt(image.css("left")),
                        w = image.width() - image.parent().width();
                        h = image.height() - image.parent().height();

                    // Initial cursor position
                    var ix = event.pageX,
                        iy = event.pageY;

                    $(document)
                        .on("mousemove.compare mouseup.compare", function(event) {

                            if (!self.drawing) return;

                            var dx = (ix - event.pageX) * -1,
                                dy = (iy - event.pageY) * -1,
                                x = (w==0) ? 0 : ox + (dx || 0),
                                y = (h==0) ? 0 : oy + (dy || 0);

                            // Always stay within boundaries
                            // if (x > 0) x = 0; if (x > w * -1) x = w * -1;
                            // if (y > 0) y = 0; if (y > h * -1) y = h * -1;

                            images.css({
                                top: (oy = y),
                                left: (ox = x)
                            });

                            ix = event.pageX;
                            iy = event.pageY;
                        })
                        .on("mouseup.compare", function() {

                            $(document).off("mousemove.compare mouseup.compare");

                            block.removeClass("active");

                            self.drawing = false;
                        });
                })
        },

        construct: function(data) {
        },

        reconstruct: function(block) {
        },

        refocus: function(block) {
        },

        reset: function(block) {
        },

        populate: function(block) {
        },

        drawing: false,

        "{image} mousedown": function(image) {


        }

    }});

    module.resolve();
});

});