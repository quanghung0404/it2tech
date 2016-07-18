EasyBlog.module("composer/blocks/handlers/spotify", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Spotify", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-spotify-form]",
            "{insert}": "[data-spotify-insert]",
            "{source}": "[data-spotify-source]",
            "{loader}": "> [data-spotify-loader]",

            // Preview
            "{errorMessage}": "[data-spotify-error]",
            "{preview}": "> [data-spotify-preview]",

            //fieldset
            "{fsSource}": "[data-fs-spotify-source]",
            "{fsRefreshButton}": "[data-fs-spotify-refresh]"
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

                self.fsSource().val(data.url);
            },

            isLoading: false,

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

                    // Append the placeholder into the block
                    overlay
                        .placeholder()
                        .css('height', '400px')
                        .appendTo(content);

                    // Append the embed codes into the overlay now
                    overlay.element().append(embed);

                    // Attach the overlay
                    overlay.attach();
                } else {

                    // If overlay already exist, just empty and add the embed codes again
                    overlay.element().empty();
                    overlay.element().append(embed);
                }

                // Set the overlay data now.
                block.data('overlay', overlay);
            },

            showForm: function(block) {
                var content = blocks.getBlockContent(block);

                self.form.inside(content).removeClass('hidden');
            },

            hideForm: function(block) {
                var content = blocks.getBlockContent(block);

                self.form.inside(content).addClass('hidden');
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

                // Display loading screen
                self.loading();


                // Crawl to get the correct spotify embed codes
                EasyBlog.ajax('site/views/crawler/crawl', {
                    "url": url
                })
                .done(function(results) {
                    // Trigger loading again
                    self.loading();

                    var result = results[url];

                    // Set the data
                    data.url = url;
                    data.embed = result.oembed.html;

                    // Hide the form
                    self.hideForm(block);

                    // Attach the overlay
                    self.setOverlay(block, data.embed);
                })
                .fail(function(message) {
                    // Toggle the loading again
                    self.loading();

                    self.errorMessage().html(message).removeClass('hide');
                });
            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // http://open.spotify.com/album/5X3IU4MDu4t0ErDR4VrPBW
                // http://open.spotify.com/artist/7CajNmpbOovFoOoasH2HaY
                // http://open.spotify.com/track/3WfITvoURyCrAal5xYMyz0
                var regex = /^http(s):\/\/open\.spotify\.com\/(track|artist|album)\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();

                // Update the url in the fieldset
                self.fsSource().val(url);

                // Crawl now
                self.crawl(currentBlock, url);
            },

            "{fsRefreshButton} click": function() {

                var url = self.fsSource().val();

                // Crawl now
                self.crawl(currentBlock, url);
            }


        }
    });

    module.resolve();

});
