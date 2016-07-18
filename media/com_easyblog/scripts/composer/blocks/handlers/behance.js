EasyBlog.module("composer/blocks/handlers/behance", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Behance", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-behance-form]",
            "{insert}": "[data-behance-insert]",
            "{source}": "[data-behance-source]",
            "{loader}": "> [data-behance-loader]",

            // Preview
            "{preview}": "> [data-behance-preview]",
            "{errorMessage}": "[data-behance-error]",
            "{fsSource}": "[data-fs-behance-source]",
            "{fsUpdate}": "[data-fs-behance-update]"
        }, EBD.selectors)
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
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
                var data = blocks.data(block);

                return data.url;
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

                // Set the overlay
                if (data.embed && !overlay) {
                    self.setOverlay(block, data.embed);
                }

                // If there's no embed codes, we need to display the form
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

                if (data.url) {
                    self.fsSource().val(data.url);
                }

            },

            loading: function() {
                var content = blocks.getBlockContent(currentBlock);

                if (!self.isLoading) {
                    self.loader.inside(content).removeClass('hidden');
                    self.form.inside(content).addClass('hidden');

                    self.isLoading = true;

                    return;
                }

                self.loader.inside(content).addClass('hidden');
                self.form.inside(content).removeClass('hidden');

                self.isLoading = false;
            },

            setOverlay: function(block, embed) {
                var content = blocks.getBlockContent(block);
                var overlay = block.data('overlay');

                if (!overlay) {
                    overlay = composer.document.overlay.create(block);

                    overlay.placeholder()
                        .css('height', '450px')
                        .appendTo(content);

                    overlay.element().append(embed);

                    overlay.attach();
                } else {
                    // Remove the existing data from the overlay.
                    overlay.element().empty();

                    // Attach the embed codes on the overlay
                    overlay.element().append(embed);
                }

                block.data('overlay', overlay);

            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // https://www.behance.net/gallery/18940231/The-dining-room
                // https://www.behance.net/gallery/14305889/art-portraits
                var regex = /^https:\/\/www\.behance\.net\/gallery\/(.*)\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            getOembedUrl: function(url) {
                return 'http://www.behance.net/services/oembed?url=' + url;
            },

            crawl: function(block, url) {

                var content = blocks.getBlockContent(block);
                var data = blocks.data(block);
                var crawlUrl = self.getOembedUrl(url);

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
                    "url": crawlUrl
                })
                .done(function(results) {
                    // When it's done trigger the loading again
                    self.loading();

                    var result = results[crawlUrl];

                    // Set the data back
                    data.url = url;
                    data.embed = result.oembed.html;

                    self.setOverlay(block, data.embed);

                    // Hide the form and loader
                    self.form.inside(content).addClass('hidden');
                })
                .fail(function(message) {
                    self.loading();

                    self.errorMessage()
                        .removeClass('hide')
                        .html(message);
                });

                return task;
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();

                // Update the source in the fieldset
                self.fsSource().val(url);

                self.crawl(currentBlock, url);
            },

            "{fsUpdate} click": function() {

                // Add overlay when the user add's a new link.
                var content = blocks.getBlockContent(currentBlock);
                var url = self.fsSource().val();

                self.crawl(currentBlock, url);
            }
        }
    });

    module.resolve();

});
