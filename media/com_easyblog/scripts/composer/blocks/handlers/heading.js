EasyBlog.module("composer/blocks/handlers/heading", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Heading", {
        defaultOptions: {
            "{headingElement}": "h1, h2, h3, h4, h5, h6",
            "{levelSelection}": "[data-eb-composer-block-heading-level] [data-level]"
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {

                // Globals
                blocks       = self.blocks;
                composer     = blocks.composer;
                meta         = opts.meta;
                currentBlock = $();
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function() {

            },

            construct: function(data)
            {
                return $.create(data.level).html(data.content);
            },

            reconstruct: function(block) {
            },

            deconstruct: function(block, clone) {

                if (clone) {
                    block = block.clone();
                }

                // Stop making it editable to prevent the output to be editable
                self.headingElement
                    .inside(block)
                    .editable(false);

                return block;
            },

            refocus: function(block) {

                // Get heading
                var heading = self.headingElement.inside(block);

                // Focus on heading
                heading.focus();

                // If block is new
                if (block.hasClass("is-new")) {

                    // Set caret at the end of heading
                    composer.editor.caret.setEnd(heading[0]);
                }
            },

            reset: function(block) {

                block.html(meta.content);
            },

            populate: function(block) {

                // Get level
                var level = self.level(block);

                // Update fieldset
                self.levelSelection()
                    .where("level", level)
                    .activateClass("active");
            },

            toData: function(block) {
                return blocks.getData(block);
            },

            toHTML: function(block) {
                var block = blocks.getBlockContent(block);

                return self.deconstruct(block, true).html();
            },

            toText: function(block) {
                return block.text();
            },

            heading: {

                inside: function(block) {
                    return block.children(":first");
                }
            },

            // This is an internal function
            // and should not be called externally.
            level: function(block, level) {

                var blockContent = blocks.getBlockContent(block),
                    heading = self.headingElement.inside(blockContent),
                    currentLevel = heading.tagName();

                if (level && currentLevel!==level) {

                    // Construct new block content
                    blockContent.html(self.construct({
                        level: level,
                        content: heading.html()
                    }));

                    // Make heading editable
                    self.headingElement.inside(block)
                        .editable(true);

                    // Update current level
                    currentLevel = level;
                }

                return currentLevel;
            },

            setLevel: function(block, level) {

                var level = self.level(block, level),
                    heading = self.headingElement.inside(block);

                // Trigger necessary events
                var args = [block, self, heading];
                self.trigger("composerBlockHeadingSetLevel", args);
                self.trigger("composerBlockChange", args);
            },

            previewLevel: function(block, level) {

                clearTimeout(self.previewTimer);

                // Get heading and level
                var heading = self.headingElement.inside(block),
                    originalLevel = heading.tagName();

                // Remember the original level before it was switched
                block
                    .defineData("originalLevel", originalLevel)
                    .addClass("is-preview");

                // Set heading level
                self.level(block, level);

                // Trigger necessary events
                var args = [block, self, heading];
                self.trigger("composerBlockHeadingPreviewLevel", args);
                self.trigger("composerBlockChange", args);
            },

            previewTimer: null,

            "{levelSelection} mouseover": function(levelSelection) {

                // Set heading level to the one being hovered on
                var level = levelSelection.data("level");

                // Preview level on current block
                self.previewLevel(currentBlock, level);
            },

            "{levelSelection} mouseout": function(levelSelection) {

                clearTimeout(self.previewTimer);

                // Delay before reverting to original level
                self.previewTimer = setTimeout(function () {

                    var originalLevel = currentBlock.data("originalLevel");

                    if (originalLevel) {
                        self.setLevel(currentBlock, originalLevel);
                    }

                }, 50);
            },

            "{levelSelection} click": function(levelSelection) {

                // Get level from level selection
                var level = levelSelection.data("level");

                // Set level on current block
                self.setLevel(currentBlock, level);

                // Refocus on heading
                self.refocus(currentBlock);
            },

            "{self} composerBlockHeadingSetLevel": function(base, event, block, handler, heading) {

                // Stop any preview timer
                clearTimeout(self.previewTimer);

                // Remove original level
                block
                    .removeClass("is-preview")
                    .removeData("originalLevel");

                // Repopulate fieldset if block is current block
                if (block.is(currentBlock)) {
                    self.populate(block);
                }
            }
        }
    });

    module.resolve();

});
