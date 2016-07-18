EasyBlog.module("composer/blocks/handlers/quotes", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Quotes", {
        defaultOptions: {

            "{styleSelection}": "[data-eb-composer-block-quotes-style] [data-style]",
            "{citation}": "[data-quotes-citation]",

            // Preview html
            "{blockquote}": "> blockquote",
            "{cite}": "> blockquote > cite",
            "{text}": "> blockquote > p"
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock)  {

        return {

            init: function() {

                // Globals
                blocks       = self.blocks;
                composer     = blocks.composer;
                meta         = opts.meta;
                currentBlock = $();
            },


            reconstruct: function(block) {
            },

            deconstruct: function(block) {

                var blockContent = blocks.getBlockContent(block);

                // Make text container non-editable
                self.text.inside(blockContent).editable(false);

                // Make cite container non-editable
                self.cite.inside(blockContent).editable(false);

                return block;
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toText: function(block) {

            },

            toHTML: function(block) {

                var cloned = block.clone(),
                    deconstructedBlock = self.deconstruct(cloned),
                    blockContent = blocks.getBlockContent(deconstructedBlock);

                return blockContent.html();
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fielset
                self.populate(block);
            },

            deactivate: function(block) {

            },

            construct: function(data) {
                var data = $.extend({}, opts.data, data);

                return content;
            },

            refocus: function(block) {

                var data = blocks.data(block);

                if (data.style) {
                    self.setStyle(block, data.style);
                }
            },

            reset: function(block) {

                // New block doesn't need resetting.
                if (block.hasClass("is-new")) {
                    return;
                }

                var blockContent = blocks.getBlockContent(block);

                blockContent.html(self.construct());
            },

            populate: function(block) {
                var data = blocks.data(block);

                self.styleSelection('[data-style=' + data.style + ']')
                    .activateClass('active');

                // Set the default citation value
                self.citation().val(data.citation).trigger('change');
            },

            setStyle: function(block, style) {

                // Set the current style for fallback
                blocks.data(block, 'current', style);

                // Set the 'selected' class on the fieldset
                self.styleSelection()
                    .removeClass('selected');

                self.styleSelection()
                    .where('style', style)
                    .addClass('selected');

                // Trigger necessary events
                var args = [block, self, style];

                self.trigger("composerBlockQuotesSetStyle", args);
                self.trigger("composerBlockChange", args);
            },

            previewType: function(block, style) {

                clearTimeout(self.previewTimer);

                // Trigger necessary events
                var args = [block, self, style];
                self.trigger("composerBlockQuotesSetStyle", args);
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
            },

            "{self} composerBlockQuotesSetStyle": function(base, event, block, handler, style) {

                // Stop any preview timer
                clearTimeout(self.previewTimer);

                var content = blocks.getBlockContent(currentBlock);

                self.blockquote
                    .inside(content)
                    .switchClass(style);

                // Repopulate the fieldset
                self.populate(block);
            },

            "{citation} change": function(el, event) {
                var data = blocks.data(currentBlock),
                    enabled = $(el).val() == 1 ? 1 : 0,
                    addHiddenClass = enabled ? false : true,
                    blockContent = blocks.getBlockContent(currentBlock);

                data.citation = enabled;

                self.cite
                    .inside(blockContent)
                    .toggleClass('hidden', addHiddenClass);
            }
        }
    });

    module.resolve();

});
