EasyBlog.module("composer/blocks/handlers/tweet", function($){

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Tweet", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-tweet-form]",
            "{insert}": "[data-tweet-insert]",
            "{source}": "[data-tweet-source]",
            "{loader}": "> [data-codepen-loader]",

            // Preview
            "{preview}": "> [data-tweet-preview]",

            "{fsSource}": "[data-fs-tweet-source]",
            "{fsUpdate}": "[data-fs-tweet-update]",
            "{errorMessage}": "[data-tweet-error]"
        }, EBD.selectors)
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                // Globals
                blocks       = self.blocks;
                composer     = blocks.composer;
                meta         = opts.meta;
                currentBlock = $();
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toText: function(block) {
                var data = blocks.data(block);

                return data.source;
            },

            toEditableHTML: function(block) {
                return '';
            },
            
            toHTML: function(block) {
                var data = blocks.data(block);

                return '<iframe src="' + data.source + '" />';
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

                // If we ever need to contstruct this block programmatically, we need to update this.
            },

            reconstruct: function(block) {

                var data = blocks.data(block);
                var content = blocks.getBlockContent(block);
                var overlay = block.data('overlay');

                if (data.embed && !overlay) {
                    self.setOverlay(block, data.embed);
                }

                if (!data.source) {
                    content.html($(meta.html));
                }

                // So redactor won't receive it.
                block.find(self.source.selector).off("paste").on("paste", function(event){
                    event.stopPropagation();
                });

            },

            deconstruct: function(block) {
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block)  {
                // When populating the fieldset for a block, reset the values
                var data = blocks.data(block);
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

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // https://twitter.com/stackideas/status/562322053668167681
                var regex = /^https:\/\/twitter\.com\/(.*)\/status\/(.*)$/;
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

                // When it's done trigger the loading again
                self.loading();

                EasyBlog.ajax('site/views/crawler/crawl', {
                    url: url
                }).done(function(results) {

                    // When it's done trigger the loading again
                    self.loading();

                    var result = results[url];

                    // Set the data back
                    data.url = url;
                    data.embed = result.oembed.html;
                    data.source = url;

                    self.setOverlay(block, data.embed);

                    // Hide the form and loader
                    self.form.inside(content).addClass('hidden');

                }).fail(function(message) {
                    // When it's done trigger the loading again
                    self.loading();

                    self.errorMessage().html(message).removeClass('hide');
                }).always(function() {


                });
            },

            setOverlay: function(block, embed) {
                var content = blocks.getBlockContent(block);
                var overlay = block.data('overlay');

                if (!overlay) {
                    overlay = composer.document.overlay.create(block);

                    // Overlay placeholder is just a placeholder so that the overlay element can be displayed within the placeholder region
                    // Append the placeholder first
                    overlay
                        .placeholder()
                        .css('height', '400px')
                        .appendTo(content);

                    // Append the embed codes now
                    overlay
                        .element()
                        .append(embed);

                    // Attaching is just like execute.
                    // Attach the overlay now
                    overlay.attach();

                } else {
                    overlay.element().empty();
                    overlay.element().append(embed);
                }

                block.data('overlay', overlay);
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                // Add overlay when the user add's a new link.
                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();

                // Update the ulr
                self.fsSource().val(url);

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
