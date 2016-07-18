EasyBlog.module("composer/blocks/handlers/slideshare", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Slideshare", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-slideshare-form]",
            "{insert}": "[data-slideshare-insert]",
            "{source}": "[data-slideshare-source]",
            "{loader}": "> [data-slideshare-loader]",

            // Preview
            "{preview}": "> [data-slideshare-preview]",

            "{fsSource}": "[data-fs-slideshare-source]",
            "{fsUpdate}": "[data-fs-slideshare-update]",
            "{errorMessage}": "[data-slideshare-error]"
        }, EBD.selectors)
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            toText: function(block) {
                var data = blocks.data(block);

                return data.url;
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toEditableHTML: function(block) {
                return '';
            },

            toHTML: function(block) {
                var data = blocks.data(block);

                return data.embed;
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function(block) {
            },

            construct: function(block) {
            },

            reconstruct: function(block) {
                var data = blocks.data(block);
                var overlay = block.data('overlay');
                var content = blocks.getBlockContent(block);

                // Set the overlay if it hasn't exists yet.
                if (data.embed && !overlay) {
                    self.setOverlay(block, data.embed);
                }

                // If there's no embed codes, show the form instead
                if (!data.embed) {
                    content.html($(meta.html));
                }

                // So redactor won't receive it.
                block.find(self.source.selector).off("paste").on("paste", function(event){
                    event.stopPropagation();
                });

            },

            deconstruct: function(block) {

                var content = blocks.getBlockContent(block);

                // When saving, remove the form
                self.form.inside(content).remove();
                self.loader.inside(content).remove();

                return block;
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block) {

                // When populating the fieldset for a block, reset the values
                var data = blocks.data(block);

                self.fsSource().val(data.url);
            },

            loading: function() {
                var blockContent = blocks.getBlockContent(currentBlock);

                if (!self.isLoading) {
                    self.loader.inside(blockContent).removeClass('hidden');
                    self.form.inside(blockContent).addClass('hidden');

                    self.isLoading = true;
                } else {

                    self.loader.inside(blockContent).addClass('hidden');
                    self.form.inside(blockContent).removeClass('hidden');

                    self.isLoading = false;
                }

            },

            setOverlay: function(block, embed) {

                var overlay = block.data('overlay');

                if (!overlay) {
                    overlay = composer.document.overlay.create(block);
                    var content = blocks.getBlockContent(block);
                    
                    // Append the placeholder first
                    overlay
                        .placeholder()
                        .css('height', '600px')
                        .appendTo(content);

                    // Append the embed codes now
                    overlay
                        .element()
                        .append(embed);

                    // Attach the overlay now
                    overlay.attach();

                    // Set the overlay data so we don't create overlays all the time
                    block.data('overlay', overlay);

                    return;
                }

                // Clear the element's content
                overlay.element().empty();

                // Attach the new embed codes
                overlay.element().append(embed);
            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // http://www.slideshare.net/MarkLee26/business-model-42989542
                var regex = /^http:\/\/www\.slideshare\.net\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            crawl: function(block, url) {
                var content = blocks.getBlockContent(block);
                var data = blocks.data(block);

                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return false;
                }

                // If there's an overlay hide it
                var overlay = block.data('overlay');

                if (overlay) {
                    overlay.element().empty();
                }

                // Display the loader and hide the form
                self.loading();

                EasyBlog.ajax('site/views/crawler/crawl', {
                    "url": url
                }).done(function(results) {

                    var result = results[url];

                    data.url = url;
                    data.embed = result.oembed.html;

                    self.setOverlay(block, data.embed)
                }).fail(function(message) {

                    self.errorMessage().removeClass('hide').html(message);

                }).always(function() {
                    // When it's done trigger the loading again
                    self.loading();

                    // Hide the form and loader
                    self.form.inside(content).addClass('hidden');
                });
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();
                var data = blocks.data(currentBlock);

                // Crawl the url
                self.crawl(currentBlock, url);

                // Update the fieldset url
                self.fsSource().val(url);
            },

            "{fsUpdate} click": function() {
                var url = self.fsSource().val();
                var data = blocks.data(currentBlock);

                self.crawl(currentBlock, url);
            }
        }
    });

    module.resolve();

});
