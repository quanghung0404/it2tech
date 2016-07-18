EasyBlog.module("composer/blocks/handlers/gist", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Gist", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-gist-form]",
            "{insert}": "[data-gist-insert]",
            "{source}": "[data-gist-source]",

            // Preview
            "{preview}": "[data-gist-preview]",

            //fieldset
            "{fsSource}": "[data-fs-gist-source]",
            "{fsRefreshButton}": "[data-fs-gist-refresh]",
            "{errorMessage}": "[data-gist-error]"
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

                return data.source;
            },

            toEditableHTML: function(block) {
                return '';
            },

            toHTML: function(block) {
                // We need to get the data from the overlay instead
                var data = blocks.data(block);
                var overlay = block.data('overlay');

                if (overlay) {
                    return overlay.element().html();
                }
            },

            activate: function(block) {
                var content = blocks.getBlockContent(block);
                var data = blocks.data(block);

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

                // If this is an edited post which has url to the gist, we need to attach the overlay again if it doesn't exist yet.
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

            deconstruct: function(block) {
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block)  {
                // When populating the fieldset for a block, reset the values
                var data = blocks.data(block);

                // Update the url in fieldset
                self.fsSource().val(data.source);
            },

            setOverlay: function(block, embed) {

                var overlay = block.data('overlay');

                if (!overlay) {
                    // Overlay element stores the real html stuffs for the block
                    var overlay = composer.document.overlay.create(block);
                    var content = blocks.getBlockContent(block);

                    // Overlay placeholder is just a placeholder so that the overlay element can be displayed within the placeholder region
                    // Append the placeholder first
                    overlay
                        .placeholder()
                        .css('height', '400px')
                        .appendTo(content);

                    // Append the embed codes now
                    overlay.element().append(embed);

                    // Attaching is just like execute.
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

                // https://gist.github.com/imarklee/49c07340f22122b384e1
                var regex = /^https:\/\/gist\.github\.com\/(.*)\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            createIframe: function(block, url) {

                // Create an iframe, append it to this document where specified
                var iframe = document.createElement('iframe');
                var content = blocks.getBlockContent(block);
                var data = blocks.data(block);

                data.source = url;

                // Set the iframe attributes
                iframe.setAttribute('width', '100%');
                iframe.id = 'gistFrame';

                // Create the necessary overlays
                self.setOverlay(block, iframe);

                var callback = $.callback(function(height) {
                    height += 'px';

                    content.find('iframe').css('height', height);
                });

                // Create the iframe's document
                var html = '<html><body onload="parent.' + callback + '(document.body.scrollHeight);"><scr' + 'ipt type="text/javascript" src="' + url + '.js"></sc'+'ript></body></html>';

                // Set iframe's document with a trigger for this document to adjust the height
                var doc = iframe.document;

                if (iframe.contentDocument) {
                    doc = iframe.contentDocument;
                } else if (iframe.contentWindow) {
                    doc = iframe.contentWindow.document;
                }

                doc.open();
                doc.writeln(html);
                doc.close();

                return iframe;
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                // Add overlay when the user add's a new link.
                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();
                var data = blocks.data(currentBlock);

                // Ensure that the url is valid
                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return;
                }

                // Update the fieldset url
                self.fsSource().val(url);

                // Create an iframe, append it to this document where specified
                self.createIframe(currentBlock, url);

                // Hide the form
                self.form.inside(content).addClass('hidden');
            },

            "{fsRefreshButton} click": function() {

                var content = blocks.getBlockContent(currentBlock);
                var url = self.fsSource().val();
                var data = blocks.data(currentBlock);

                // Verify the source url
                if (!self.isUrlValid(url)) {
                    self.errorMessage.inside(content).removeClass('hide');
                    return;
                }

                // Create an iframe, append it to this document where specified
                self.createIframe(currentBlock, url);

                // Hide the form
                self.form.inside(content).addClass('hidden');
            }
        }
    });

    module.resolve();

});
