EasyBlog.module("composer/blocks/media", function($) {

var module = this;

EasyBlog.Controller("Composer.Blocks.Media", {
    defaultOptions: $.extend({

    }, EBD.selectors),
}, function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = blocks.composer;
    },

    "{self} mediaInfoShow": function(base, event, uri) {

        // Switch composer frame to media layout
        composer.frame().addClass("layout-media");

        // Display blocks panel
        composer.panels.activate("blocks");
    },

    "{self} mediaInfoDisplay": function(base, event, info, media) {

        var block = info.find(".ebd-block");

        blocks.activateBlock(block);

        // On legacy document, blocks/guide plugin is not installed.
        // So we'll need to add active class here.
        block.addClass("active");
    },

    "{self} mediaInfoHide": function(base, event) {

        // Deactivate panel
        blocks.panel.deactivatePanel();

        // Remove media layout from composer frame
        composer.frame().removeClass("layout-media");
    },

    "{self} mediaInsert": function() {

        // Deactivate panel
        blocks.panel.deactivatePanel();

        // Remove media layout from composer frame
        composer.frame().removeClass("layout-media");
    }
}});

module.resolve();

});
