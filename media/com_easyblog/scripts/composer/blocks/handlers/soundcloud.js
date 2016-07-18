EasyBlog.module("composer/blocks/handlers/soundcloud", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Soundcloud", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-soundcloud-form]",
            "{insert}": "[data-soundcloud-insert]",
            "{source}": "[data-soundcloud-source]",
            "{loader}": "> [data-soundcloud-loader]",

            // Preview
            "{preview}": "> [data-soundcloud-preview]",

            "{fsSource}": "[data-fs-soundcloud-source]",
            "{fsUpdate}": "[data-fs-soundcloud-update]",
            "{errorMessage}": "[data-soundcloud-error]"

        }, EBD.selectors)
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
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

            normalize: function(data) {
                return $.extend({}, meta.data, data);
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

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

                // So redactor won't receive it.
                block.find(self.source.selector).off("paste").on("paste", function(event){
                    event.stopPropagation();
                });

            },

            deconstruct: function(block) {
                var content = blocks.getBlockContent(block);

                // Remove unecessary items
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

                // Update the fieldset url
                self.fsSource().val(data.url);
            },

            isLoading: false,

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
                var blockContent = blocks.getBlockContent(block);

                if (!overlay) {
                    overlay = composer.document.overlay.create(block);

                    // Append the placeholder first
                    overlay.placeholder().css('height', '350px')
                        .appendTo(blockContent);

                    overlay.element().append(embed);

                    overlay.attach();
                } else {
                    // Remove existing data from overlay
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

                // https://soundcloud.com/theweeknd
                // https://soundcloud.com/theweeknd/mike-will-made-it-drinks-on-us-feat-the-weeknd-swae-lee-future
                var regex = /^https:\/\/soundcloud\.com\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            crawl: function(block, url) {

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
                    "url": url
                }).done(function(results) {

                    var result = results[url];

                    // Set the data back
                    data.url = url;
                    data.embed = result.oembed.html;

                    self.setOverlay(block, data.embed);
                })
                .fail(function(message) {
                    self.errorMessage()
                        .removeClass('hide')
                        .html(message);
                })
                .always(function(){
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

                // Set the source value
                self.fsSource().val(url);

                // Crawl the site now.
                self.crawl(currentBlock, url);
            },

            "{fsUpdate} click": function() {
                var url = self.fsSource().val();

                self.crawl(currentBlock, url);
            }
        }
    });

    module.resolve();

});
