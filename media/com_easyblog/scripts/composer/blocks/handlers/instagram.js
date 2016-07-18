EasyBlog.module("composer/blocks/handlers/instagram", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Instagram", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-instagram-form]",
            "{insert}": "[data-instagram-insert]",
            "{source}": "[data-instagram-source]",

            // Preview
            "{preview}": "[data-instagram-preview]",

            //fieldset
            "{fsSource}": "[data-fs-instagram-source]",
            "{fsRefreshButton}": "[data-fs-instagram-refresh]",
            "{errorMessage}": "[data-instagram-error]"
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

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toText: function(block) {

                // we should return the photo URL as text.
                var data = blocks.data(block);

                return data.source;
            },

            toEditableHTML: function(block) {
                return '';
            },
            
            toHTML: function(block) {
                // We need to get the data from the overlay instead
                var overlay = block.data('overlay');

                if (overlay) {
                    return overlay.element().html();
                }
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

            deconstruct: function(block) {
                var content = blocks.getBlockContent(block);

                // Remove the form from the content
                self.form.inside(content).remove();

                return block;
            },

            reconstruct: function(block) {
                var data = blocks.data(block);
                var overlay = block.data('overlay');
                var content = blocks.getBlockContent(block);

                if (data.source && !overlay) {
                    self.createIframe(block, data.source);
                } else {
                    content.html(meta.html);
                }

                // So redactor won't receive it.
                block.find(self.source.selector).off("paste").on("paste", function(event){
                    event.stopPropagation();
                });
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block) {
                // When populating the fieldset for a block, reset the values
                var data = blocks.data(block);

                // update the fieldset url
                self.fsSource().val(data.source);
            },

            createIframe: function(block, url) {
                var iframe = $.create('iframe');
                var data = blocks.data(block);

                // Ensure that the url is properly sanitized
                url = self.sanitizeUrl(url);

                data.source = url;

                iframe.attr('src', url);

                self.setOverlay(block, iframe);
            },

            sanitizeUrl: function(url) {

                // Ensure that the prepended ?modal=true is removed
                url = url.replace('?modal=true', '');

                // remove the ending slash if there is any
                url = url.replace(/\/$/, '');

                // Prepend the embed
                if (!url.match(/\/embed/)) {
                    url += '/embed/';
                }

                return url;
            },

            setOverlay: function(block, embed) {

                var overlay = block.data('overlay');

                // If overlay didn't exist, create one first
                if (!overlay) {
                    overlay = composer.document.overlay.create(block);
                    
                    // Get the block's content
                    var content = blocks.getBlockContent(block);

                    // Append the placeholder first
                    overlay
                        .placeholder()
                        .css('height', '400px')
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

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // http://instagram.com/p/xxxx/
                var regex = /^(?:http(?:s)?:\/\/)?www\.instagram\.com\/p\/(.*)\/$/;
                var valid = regex.test(url);

                return valid;
            },

            "{insert} click": function(button, event) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();

                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return;
                }

                // update the fieldset url
                self.fsSource().val(url);

                // Create the iframe
                self.createIframe(currentBlock, url);

                // Hide the form
                self.form.inside(content).addClass('hidden');
            },

            "{fsRefreshButton} click": function() {
                // Add overlay when the user add's a new link.
                var content = blocks.getBlockContent(currentBlock);
                var url = self.fsSource().val();

                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return;
                }

                // Create the iframe
                self.createIframe(currentBlock, url);

                // Hide the form
                self.form.inside(content).addClass('hidden');
            }
        }
    });

    module.resolve();
});
