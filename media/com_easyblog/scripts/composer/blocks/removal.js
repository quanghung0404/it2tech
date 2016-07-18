EasyBlog.module("composer/blocks/removal", function($){

var module = this;
var isRemoving = "is-removing";

EasyBlog.require()
.library(
    "ui/droppable"
)
.done(function(){

EasyBlog.Controller("Composer.Blocks.Removal",
{
    defaultOptions: {
        "{workarea}": EBD.workarea,
        "{dropzone}": "[data-eb-composer-blocks-removal-subpanel]"
    }
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = blocks.composer;
    },

    "{self} composerBlockDrag": function(base, event, block) {

        // Set as current block
        currentBlock = block;

        // Activate blocks panel
        composer.panels.activate("blocks");

        // Open removal subpanel
        blocks.panel.openPanel("removal");

        // Get dropzone
        var dropzone = self.dropzone();

        // If dropzone has never been initialized before,
        // initalized it now.
        // TODO: Find alternative way to determine this
        if (!dropzone.data("inited")) {
            dropzone.droppable({
                accept: EBD.block,
                tolerance: "pointer"
            });
            dropzone.data("inited", true);
        }
    },

    "{self} composerBlockDrop": function(base, event, block) {

        if (self.workarea().hasClass(isRemoving)) return;

        // Open block subpanel
        blocks.panel.openPanel("block");
    },

    "{dropzone} drop": function(dropzone, event, ui) {

        // Get panel
        var block = ui.draggable;
        var workarea = self.workarea();

        // Add is-removing class to workarea
        workarea.addClass(isRemoving);

        // Start block removal transition
        block.addClass(isRemoving);
        dropzone.addClass(isRemoving);

        // Remove block after a slight deley
        setTimeout(function(){

            blocks.removeBlock(block);

            // After animation is done
            setTimeout(function(){

                // Open block panel
                blocks.panel.openPanel("block");

                workarea.removeClass("is-removing");

                setTimeout(function(){
                    // Remove block transition
                    dropzone.removeClass("is-removing active");
                }, 250);

            }, 250);


        }, 100);
    },

    "{dropzone} dropout": function(dropzone, event, ui) {

        dropzone.removeClass("active");
    },

    "{dropzone} dropover": function(dropzone, event, ui) {

        dropzone.addClass("active");
    }

}});

module.resolve();

});

});
