EasyBlog.module("composer/blocks/handlers/buttons", function($) {

    var module = this;

    EasyBlog.require()
    .library(
        "rangeslider"
    )
    .done(function() {

        EasyBlog.Controller("Composer.Blocks.Handlers.Buttons", {
            defaultOptions: {

                "{button}": "> a.btn",
                "{textWrapper}": "> span",
                "{buttonSize}": "[data-eb-composer-block-button-size] [data-size]",
                "{buttonHyperlink}": "[data-button-link]",
                "{buttonNofollow}": "[data-button-nofollow]",
                "{buttonTarget}": "[data-button-target]",

                "{buttonSwatchItem}": "[data-eb-composer-button-swatch-item]"
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

                selectionState: {},

                toData: function(block) {

                    var data = blocks.data(block);

                    return data;
                },

                // Returns the text that is within the block
                toText: function(block) {
                    var blockContent = blocks.getBlockContent(block),
                        button = self.button.inside(blockContent);

                    // since this button work similar like a link, we should return the href text.
                    text = blocks.data(block).link;

                    return text;
                },

                toHTML: function(block) {

                    var clone = block.clone();
                    var deconstructedBlock = self.deconstruct(clone);
                    var content = blocks.getBlockContent(deconstructedBlock).html();

                    return content;
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
                },

                reconstruct: function(block) {

                    // Make the button editable
                    var blockContent = blocks.getBlockContent(block),
                        button = self.button.inside(blockContent);

                    // The button should not be clickable
                    button.on('click', function(event) {
                        event.stopPropagation();
                        event.preventDefault();
                    });
                },

                deconstruct: function(block) {

                    var blockContent = blocks.getBlockContent(block);
                    var button = self.button.inside(blockContent);

                    self.textWrapper.inside(button)
                        .removeAttr('contenteditable')
                        .editable(false);

                    return block;
                },

                refocus: function(block) {
                },

                reset: function(block) {
                },

                // When the active element is focused, we want to populate the fieldset
                populate: function(block) {

                    // Get the params for the current block
                    var data = blocks.data(block);

                    self.buttonSwatchItem()
                        .removeClass("active")
                        .where("style", data.style)
                        .addClass("active");

                    var buttonSize = self.buttonSize().filter('[data-size="' + data.size + '"]');

                    buttonSize.siblings().removeClass('active');
                    buttonSize.addClass('active');

                    self.buttonHyperlink().val(data.link);

                    self.buttonNofollow().val(data.nofollow).trigger('change');

                    self.buttonTarget().val(data.target);
                },

                "{buttonSwatchItem} click": function(buttonSwatchItem) {

                    var style = buttonSwatchItem.data("style"),
                        blockContent = blocks.getBlockContent(currentBlock);

                    self.buttonSwatchItem()
                        .removeClass("active")
                        .where("style", style)
                        .addClass("active");

                    self.button.inside(blockContent)
                        .removeClass('btn-info btn-primary btn-success btn-warning btn-danger')
                        .addClass(style);

                    blocks.data(currentBlock).style = style;
                },

                "{buttonSwatchItem} mouseover": function(buttonSwatchItem) {

                    clearTimeout(self.previewTimer);

                    var style = buttonSwatchItem.data('style'),
                        blockContent = blocks.getBlockContent(currentBlock);

                    self.button.inside(blockContent)
                        .removeClass('btn-info btn-primary btn-success btn-warning btn-danger')
                        .addClass(style);
                },

                "{buttonSwatchItem} mouseout": function(buttonSwatchItem) {

                    clearTimeout(self.previewTimer);

                    var blockContent = blocks.getBlockContent(currentBlock);

                    self.previewTimer = setTimeout(function() {
                        self.button.inside(blockContent)
                            .removeClass('btn-info btn-primary btn-success btn-warning btn-danger')
                            .addClass(blocks.data(currentBlock).style);
                    }, 50);
                },

                "{buttonSize} click": function(el) {

                    var size = el.data('size'),
                        blockContent = blocks.getBlockContent(currentBlock);

                    el.addClass('active')
                      .siblings()
                      .removeClass('active');

                    self.button.inside(blockContent)
                        .removeClass('btn-sm btn-xs btn-lg btn-xlg')
                        .addClass(size);

                    blocks.data(currentBlock).size = size;
                },

                "{buttonSize} mouseover": function(el) {
                    clearTimeout(self.previewTimer);

                    var size = el.data('size'),
                        blockContent = blocks.getBlockContent(currentBlock);

                    self.button.inside(blockContent)
                        .removeClass('btn-sm btn-xs btn-lg btn-xlg')
                        .addClass(size);
                },

                "{buttonSize} mouseout": function(el) {
                    clearTimeout(self.previewTimer);

                    var blockContent = blocks.getBlockContent(currentBlock);

                    self.previewTimer = setTimeout(function() {
                        self.button.inside(blockContent)
                            .removeClass('btn-sm btn-xs btn-lg btn-xlg')
                            .addClass(blocks.data(currentBlock).size);
                    }, 50);
                },

                "{buttonHyperlink} keyup": $.debounce(function(el) {

                    var blockContent = blocks.getBlockContent(currentBlock),
                        data = blocks.data(currentBlock);

                    data.link = el.val();

                    self.button.inside(blockContent).attr('href', el.val());
                }, 250),

                "{buttonNofollow} change": function(el) {

                    var blockContent = blocks.getBlockContent(currentBlock),
                        data = blocks.data(currentBlock);

                    data.nofollow = el.val() == 1 ? 1 : 0;

                    self.button
                        .inside(blockContent)
                        .attr('rel', el.val() == 1 ? 'nofollow' : '');
                },

                "{buttonTarget} change": function(el) {

                    var blockContent = blocks.getBlockContent(currentBlock),
                        data = blocks.data(currentBlock);

                    data.target = el.val();

                    self.button
                        .inside(blockContent)
                        .attr('target', el.val());
                }
            }
        });

        module.resolve();
    });

});
