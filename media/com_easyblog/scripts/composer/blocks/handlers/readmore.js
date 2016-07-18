EasyBlog.module("composer/blocks/handlers/readmore", function($){

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Readmore", {
        defaultOptions: {
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {

                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            toData: function(block) {
                var data = blocks.data(block);
                return data;
            },

            toText: function(block) {
                return;
            },

            toHTML: function(block) {

                // We don't want to return the html codes for the read more
                var hr = $.create('hr');

                hr.attr('id', 'system-readmore');

                return hr.toHTML();
            },

            deactivate: function(block) {

            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fielset
                self.populate(block);
            },

            deconstruct: function(data) {

            },

            construct: function(data) {
            },

            reconstruct: function(block) {
                var data = blocks.data(block),
                    blockContent = blocks.getBlockContent(block);

                // If there's a readmore block already present in the content, we need to remove it from the menu
                self.hideMenu();

                // Update the blocks content with the appropriate html codes
                blockContent.html(meta.html);
            },

            refocus: function(block) {
            },

            reset: function(block) {

            },

            populate: function(block) {
            },

            showMenu: function() {
                var menu = blocks.menu().where('type', 'readmore');

                // Hide the menu since we only want to allow this to happen once
                menu.removeClass('hide');
            },

            hideMenu: function() {
                var menu = blocks.menu().where('type', 'readmore');

                // Hide the menu since we only want to allow this to happen once
                menu.addClass('hide');
            },

            "{self} composerBlockAdd": function(el, event, block) {

                var type = blocks.getBlockType(block);

                if (type == 'readmore') {
                    self.hideMenu();
                }
            },

            "{self} composerBlockRemove": function(el, event, block) {

                var type = blocks.getBlockType(block);

                if (type == 'readmore') {
                    self.showMenu();
                }
            }
        }
    });

    module.resolve();
});
