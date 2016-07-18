EasyBlog.module("composer/blocks/handlers/rule", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Rule", {
        defaultOptions: {
            "{rule}": "hr",
            "{styleSelection}": "[data-eb-composer-rule-style] [data-style]"
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

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fielset
                self.populate(block);
            },

            construct: function(data) {

                var data = $.extend({}, opts.data, data),
                    content = $("<hr/>");

                return content;
            },

            toText: function(block) {
                return;
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toHTML: function(block) {
                var data = blocks.data(block),
                    blockContent = blocks.getBlockContent(block);

                return blockContent.html();
            },

            reconstruct: function(block) {
                var data = blocks.data(block);
            },

            refocus: function(block) {
                var data = blocks.data(block);

                if (data.style) {
                    self.setStyle(block, data.style);
                }
            },

            reset: function(block) {
                var blockContent = blocks.getBlockContent(block);

                blockContent.html(self.construct());
            },

            populate: function(block) {
                var data = blocks.data(block);

                self.styleSelection('[data-style=' + data.style + ']')
                    .activateClass('active');
            },

            setStyle: function(block, style) {

                // Set the current style for fallback
                blocks.data(block, 'current', style);

                // Remove all selected class from the selection
                self.styleSelection()
                    .removeClass('selected');

                self.styleSelection()
                    .where('style', style)
                    .addClass('selected');

                // Trigger necessary events
                var args = [block, self, style];

                self.trigger('composerBlockRuleSetStyle', args);
                self.trigger('composerBlockChange', args);
            },

            previewType: function(block, style) {

                clearTimeout(self.previewTimer);

                // Trigger necessary events
                var args = [block, self, style];

                self.trigger("composerBlockRuleSetStyle", args);
                self.trigger("composerBlockChange", args);
            },

            "{styleSelection} mouseover": function(el) {

                // Set heading level to the one being hovered on
                var style = el.data('style');

                // Preview level on current block
                self.previewType(currentBlock, style);
            },

            "{styleSelection} mouseout": function(el) {

                clearTimeout(self.previewTimer);

                // Delay before reverting to original level
                self.previewTimer = $.delay(function(){

                    var currentStyle = blocks.data(currentBlock).current;

                    if (currentStyle) {
                        self.setStyle(currentBlock, currentStyle);
                    }

                }, 50);
            },

            "{styleSelection} click": function(el) {
                // Get the alert type
                var style = el.data('style'),
                    data = blocks.data(currentBlock);

                data.style = style;

                // Set the alert type
                self.setStyle(currentBlock, style);

                // Refocus on the note
                self.refocus(currentBlock);
            },

            "{self} composerBlockRuleSetStyle": function(base, event, block, handler, style) {
                // Stop any preview timer
                clearTimeout(self.previewTimer);

                // Remove all classes
                var blockContent = blocks.getBlockContent(block);

                self.rule
                    .inside(blockContent)
                    .switchClass(style);

                // Repopulate the fieldset
                self.populate(block);
            }
        }
    });

    module.resolve();

});
