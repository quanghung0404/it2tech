EasyBlog.module("composer/blocks/handlers/post", function($){

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Post", {

        defaultOptions: {
            "{block}": ".ebd-block[data-type=text]",
            "{wrapper}": "> div[data-text-wrapper]",
            "{lastParagraph}": "> div > p:last",

            // Items in a post block
            "{mediaPreview}": "[data-post-media]",
            "{introPreview}": "[data-post-intro]",
            "{linkPreview}": "[data-post-link-preview]",
            "{titlePreview}": "[data-preview-title]",

            // Post options in fieldset
            "{showImage}": "[data-post-option-image]",
            "{showIntro}": "[data-post-option-intro]",
            "{showHyperlink}": "[data-post-option-link]"

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
                var data = blocks.data(block);

                // Set as current block
                currentBlock = block;

                // Determines if the image property should be enabled or disabled
                self.showImage()
                    .val(data.show_image == 0 ? 0 : 1)
                    .trigger('change');

                // Determines if the show intro should be enabled or disabled
                self.showIntro()
                    .val(data.show_intro == 0 ? 0 : 1)
                    .trigger('change');

                self.showHyperlink()
                    .val(data.show_link == 0 ? 0 : 1)
                    .trigger('change');

                // Populate fieldset
                self.populate();
            },

            deactivate: function() {
            },

            construct: function(data) {

                var block = blocks.createBlockContainer("post"),
                    data = $.extend({}, meta.data, data),
                    content = $(data.content);

                // Set the title
                content
                    .find('[data-post-title]').html(data.title);

                // Set the anchor link
                content
                    .find('[data-post-link]')
                    .attr('href', data.url);

                content
                    .on('click', function(event) {
                        event.preventDefault();
                    });

                // Set the preview link
                content
                    .find('[data-post-link-preview]')
                    .html(data.url);

                // Set the intro code
                content
                    .find('[data-post-intro]')
                    .html(data.intro);

                // When there is no image, we should remove the image tag from the preview
                if (!data.image) {
                    content
                        .find('[data-post-image]')
                        .remove();
                } else {
                    content
                        .find('[data-post-image]')
                        .attr('src', data.image);
                }

                // Create block content
                blocks.content.inside(block)
                    .append(content);

                // Set block data
                block.data('block', data);

                return block;
            },

            reconstruct: function(block) {

                var blockContent = blocks.getBlockContent(block),
                    data = blocks.data(block);

                blockContent.find('[data-post-link]')
                    .on('click', function(event) {
                        event.preventDefault();
                    });
            },

            deconstruct: function(block) {
                var wrapper = self.wrapper.inside(block),
                    parent = wrapper.parent();

                wrapper.children().appendTo(parent);

                wrapper.remove();

                return block;
            },

            refocus: function(block) {

                // Get last paragraph
                var wrapper = self.wrapper.inside(block),
                    lastParagraph = self.lastParagraph.inside(block);

                // Focus on wrapper because
                // that's where contenteditable is.
                wrapper.focus();

                // But the selection should be made
                // on the paragraph itself.
                composer.editor.caret.setEnd(lastParagraph);
            },

            reset: function(block) {

                block = blocks.getBlockContent(block);

                // Replace block content with default content
                block.html(meta.content);
            },

            populate: function(block) {
            },

            recover: function(block) {
            },

            revert: function(block) {
            },

            toText: function(block) {
                var block = blocks.getBlockContent(block);

                return block.text();
            },

            toHTML: function(block) {
                var block = blocks.getBlockContent(block),
                    cloned = block.clone(),
                    html = self.deconstruct(cloned).html();

                // Based on the meta settings, remove those unwanted html codes

                return self.deconstruct(cloned).html();
            },

            toData: function(block) {
                var data = blocks.data(block);

                data.content = self.toHTML(block);

                return data;
            },

            "{showImage} change": function(el, event) {
                var blockContent = blocks.getBlockContent(currentBlock),
                    enabled = el.val() == 0 ? false : true,
                    data = blocks.data(currentBlock);

                data.show_image = enabled;

                if (!enabled) {
                    self.mediaPreview.inside(blockContent).hide();
                    return;
                }

                self.mediaPreview.inside(blockContent).show();
            },

            "{showIntro} change": function(el, event) {
                var blockContent = blocks.getBlockContent(currentBlock),
                    enabled = el.val() == 0 ? false : true,
                    data = blocks.data(currentBlock);

                data.show_intro = enabled;

                if (!enabled) {
                    self.introPreview.inside(blockContent).hide();
                    return;
                }

                self.introPreview.inside(blockContent).show();
            },

            "{showHyperlink} change": function(el, event) {
                var blockContent = blocks.getBlockContent(currentBlock),
                    enabled = el.val() == 0 ? false : true,
                    data = blocks.data(currentBlock);

                data.show_link = enabled;

                if (!enabled) {
                    self.linkPreview
                        .inside(blockContent)
                        .hide();

                    return;
                }

                self.linkPreview.inside(blockContent).show();
            }
        }
    });

    module.resolve();
});
