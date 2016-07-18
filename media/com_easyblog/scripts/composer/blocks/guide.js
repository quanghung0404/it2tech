EasyBlog.module("composer/blocks/guide", function($){

var module = this;

EasyBlog.Controller("Composer.Blocks.Guide",
{
    defaultOptions: $.extend({}, EBD.selectors)
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = self.blocks.composer;
    },

    "{self} composerBlockHoverIn": function(base, event, block) {

        // Hover block
        block.addClass("hover");
    },

    "{self} composerBlockHoverOut": function(base, event, block) {

        // Unhover block
        block.removeClass("hover");
    },

    "{self} composerBlockDrop": function(base, event, block) {


    },

    "{self} composerBlockActivate": function(base, event, block, handler) {

        // Add active class only to current block
        block.addClass("active");

        var isNestedBlock = block.is(EBD.nestedBlock);

        // If block is a nestedBlock
        if (isNestedBlock) {

            // Get nest of block and add active class
            self.nest.of(block)
                .addClass("active");

            // Get parent block and add has-active-child class
            blocks.getAllParentBlocks(block)
                .addClass("has-active-child");
        }

        // Get workarea and add has-active-nest class
        // if activating nestedBlock
        self.workarea()
            .toggleClass("has-active-nest", isNestedBlock);

        // Glow block
        block.addClass("is-glowing");

        setTimeout(function(){
            block.removeTransitionClass("is-glowing", 2000);
        }, 10);
    },

    "{self} composerBlockDeactivate": function(base, event, block) {

        // Remove active class from all blocks
        self.block()
            .removeClass("active has-active-child");

        // Remove active class from all nest
        self.nest()
            .removeClass("active");
    }

}});

module.resolve();

});
