EasyBlog.module("composer/blocks/handlers/text", function($) {

var module = this;

EasyBlog.Controller("Composer.Blocks.Handlers.Text", {
    defaultOptions: {

        "{block}": ".ebd-block[data-type=text]",
        "{blockWrapper}": "> [data-eb-text-block-wrapper]",
        "{contentWrapper}": "> [data-eb-text-block-wrapper] > [data-eb-text-content-wrapper]",
        "{allContentWrapper}": "[data-eb-text-content-wrapper]",
        "{lastParagraph}": "> p:last"
    }
}, function(self, opts, base, composer, blocks, meta, currentBlock) { return {

    init: function() {
        // Globals
        blocks = self.blocks;
        composer = blocks.composer;
        meta = opts.meta;
        currentBlock = $();
    },

    activateKeys: function() {
        // I want to disable the pg up and down keys
        $(document).off('keydown.blocks.text');
    },

    deactivateKeys: function() {

        // I want to disable the pg up and down keys
        $(document).on('keydown.blocks.text', function(event) {
            var key = event.which;

            if (key == 33 || key == 34) {
                event.preventDefault();
                return false;
            }

            return true;
        });

    },

    activate: function(block) {

        self.deactivateKeys();

        // Set as current block
        currentBlock = block;

        // Populate fieldset
        self.populate();
    },

    deactivate: function(block) {
        // I need to re-activate the keys here.
        self.activateKeys();
    },

    construct: function(data) {

        var block = blocks.createBlock("text");

        // Create block content
        var content = blocks.getBlockContent(block);

        content.html(meta.data.content);

        return block;
    },

    reconstruct: function(block) {

        var blockContent = blocks.getBlockContent(block);

        // If block wrapper does not exist
        var blockWrapper = self.blockWrapper.inside(blockContent);

        if (!blockWrapper.length) {

            // Create block wrapper
            blockWrapper =

                $(meta.blockWrapper)
                    // Wrap child nodes in block wrapper
                    .append(blockContent[0].childNodes)
                    // Append block wrapper to block content
                    .appendTo(blockContent);
        }

        // If content wrapper does not exist
        var contentWrapper = self.contentWrapper.inside(blockContent);

        if (!contentWrapper.length) {

            // Collect content nodes
            var contentNodes = [];
            $.each(blockWrapper[0].childNodes, function() {
                if ($(this).is(EBD.block)) return;
                contentNodes.push(this);
            });

            // Create content wrapper
            contentWrapper =
                $(meta.contentWrapper)
                    // Wrap content nodes in content wrapper
                    .append(contentNodes)
                    // Append content wrapper to block wrapper
                    .appendTo(blockWrapper);
        }
    },

    //
    // Deconstruct the text block
    //
    deconstruct: function(block) {

        // Get block content
        var blockContent = blocks.getBlockContent(block);

        // Get content wrapper
        var contentWrapper = self.contentWrapper.inside(blockContent);

        // If we can't find any content wrapper, we'll assume that this is empty
        if (contentWrapper.length == 0) {
            return block;    
        }
        
        // Get content nodes & nested blocks
        var contentNodes = contentWrapper[0].childNodes;
        var nestedBlocks = blockContent.find(EBD.immediateNestedBlock);

        // Empty out block content, append nested blocks & append content nodes
        blockContent
            .empty()
            .append(nestedBlocks)
            .append(contentNodes);

        return block;
    },

    refocus: function(block) {

        // var blockContent = blocks.getBlockContent(block);

        // Focus on wrapper
        // var wrapper = self.wrapper.inside(blockContent);
        // wrapper.focus();

        // Set caret to last paragraph
        // var lastParagraph = self.lastParagraph.inside(wrapper);
        // composer.editor.caret.setEnd(lastParagraph);
    },

    reset: function(block) {
    },

    populate: function(block) {
    },

    recover: function(block) {
    },

    revert: function(block) {
    },

    toText: function(block) {

        var clone = block.clone();
        var deconstructedBlock = self.deconstruct(clone);
        var content = blocks.getBlockContent(deconstructedBlock).text();

        return content;
    },

    toHTML: function(block) {

        var clone = block.clone();
        var deconstructedBlock = self.deconstruct(clone);
        var content = blocks.getBlockContent(deconstructedBlock).html();

        return content;
    },

    toData: function(block) {
    },

    "{self} composerListFormat": function(base, event, block) {

        var blockType = blocks.getBlockType(block);

        if (blockType=="text") {

            // FF fix: When formatting list, wrapper may disappear.
            self.reconstruct(block);

            // Chrome fix: List falls inside marker, need to move it out.
            block.find(".redactor-selection-marker")
                .each(function(){
                    var marker = $(this);
                    if (marker.find("ul, ol").length) {
                        marker.children().insertBefore(marker);
                    }
                });
        }
    },

    "{allContentWrapper} mouseup": function(allContentWrapper) {

        var block = blocks.block.of(allContentWrapper);
        blocks.font.populateFontFormatting(block);
    },

    "{allContentWrapper} keyup": $.debounce(function(allContentWrapper) {

        var block = blocks.block.of(allContentWrapper);
        blocks.font.populateFontFormatting(block);

    }, 100)

}});

module.resolve();

});
