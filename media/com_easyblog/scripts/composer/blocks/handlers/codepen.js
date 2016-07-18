EasyBlog.module("composer/blocks/handlers/codepen", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Codepen", {

        defaultOptions: {

            // Form
            "{form}": "> [data-codepen-form]",
            "{insert}": "[data-codepen-insert]",
            "{source}": "[data-codepen-source]",
            "{loader}": "> [data-codepen-loader]",

            // Preview
            "{preview}": "> [data-codepen-preview]",

            "{fsSource}": "[data-fs-codepen-source]",
            "{fsUpdate}": "[data-fs-codepen-update]",
            "{errorMessage}": "[data-codepen-error]"
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

                if (data.embed && !overlay) {
                    self.setOverlay(block, data.embed);
                }

                if (!data.embed) {
                    content.html($(meta.html));
                }
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
                var content = blocks.getBlockContent(currentBlock);

                if (!self.isLoading) {
                    self.loader.inside(content).removeClass('hidden');
                    self.form.inside(content).addClass('hidden');

                    self.isLoading = true;
                } else {

                    self.loader.inside(content).addClass('hidden');
                    self.form.inside(content).removeClass('hidden');

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
                        .css('height', '300px')
                        .appendTo(content);

                    // Append the embed codes now
                    overlay.element().append(embed);

                    // Attach the overlay now
                    overlay.attach();
                } else {
                    overlay.element().empty();
                    overlay.element().append(embed);
                }

                // Set the overlay data so we don't create overlays all the time
                block.data('overlay', overlay);
            },

            getUrl: function(url) {
                return 'http://codepen.io/api/oembed?url=' + url
            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // http://codepen.io/gastonfig/pen/YPrqEj
                var regex = /^http:\/\/codepen\.io\/(.*)\/pen\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            crawl: function(block, url) {

                var crawlUrl = self.getUrl(url);
                var data = blocks.data(block);
                var content = blocks.getBlockContent(block);

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
                }).done(function(results) {

                    var result = results[crawlUrl];

                    data.url = url;
                    data.embed = result.oembed.html;

                    self.setOverlay(block, data.embed);

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

                var data = blocks.data(currentBlock);
                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();

                // Update the fieldset's source
                self.fsSource().val(url);

                // Crawl the site
                self.crawl(currentBlock, url);
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
