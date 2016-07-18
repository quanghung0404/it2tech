EasyBlog.module("composer/blocks/handlers/dailymotion", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Dailymotion", {
        elements: [
            '[data-dailymotion-fieldset-{url|fluid|width-fieldset|height-fieldset|width|height|update-url}]'
        ],
        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-dailymotion-form]",
            "{insert}": "[data-dailymotion-insert]",
            "{source}": "[data-dailymotion-source]",
            "{loader}": "> [data-dailymotion-loader]",

            // video player
            "{player}": "iframe",

            // Template wrapper.
            "{wrapper}": "[data-dailymotion-wrapper]"

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
                var overlay = self.getOverlay(block);

                // The overlay might not be there if the user did not enter any urls
                if (!overlay) {
                    return data;
                }

                // Get the iframe url
                var player = self.player.inside(overlay.element());

                // We need to get the source of the iframe so we can generate our own html codes during the display
                data.source = player.attr('src');

                return data;
            },

            toText: function(block) {
                var data = blocks.data(block);
                var overlay = self.getOverlay(block);

                // The overlay might not be there if the user did not enter any urls
                if (!overlay) {
                    return data;
                }

                // Get the iframe url
                var player = self.player.inside(overlay.element());

                // We need to get the source of the iframe so we can generate our own html codes during the display
                data.source = player.attr('src');

                return data.source;
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

            getWrapper: function(block) {
                var wrapper = $(meta.wrapper);

                return wrapper;
            },

            reconstruct: function(block) {
                var data = blocks.data(block);
                var overlay = block.data('overlay');
                var content = blocks.getBlockContent(block);

                // If this is an edited post which has url to the gist, we need to attach the overlay again if it doesn't exist yet.
                if (data.source && !overlay) {
                    self.setOverlay(block);
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
                // var data = blocks.data(block);

                // Update the url in fieldset
                self.updateFieldset(block);
            },

            updateFieldset: function(block) {
                var data = blocks.data(block);

                // Update the url
                self.url().val(data.url);

                // Update the width
                self.width().val(data.width);

                // Update the height
                self.height().val(data.height);

                // Update the fluid settings
                self.fluid().val(data.fluid ? '1' : '0')
                    .trigger('change');
            },

            getOverlay: function(block) {
                var overlay = block.data('overlay');
                var data = blocks.data(block);
                var content = blocks.getBlockContent(block);

                if (!overlay) {
                    overlay = composer.document.overlay.create(block);

                    var wrapper = self.getWrapper();

                    // Insert placeholder inside wrapper
                    overlay.placeholder()
                        .appendTo(wrapper);

                    // Insert wrapper inside block content
                    content.empty()
                        .append(wrapper);

                    // Attach the overlay items on the dom
                    overlay.attach();
                }

                return overlay;
            },

            setWrapperLayout: function(block) {
                var wrapper = self.wrapper.inside(block);
                var data = blocks.data(block);

                // If it's a fluid width, append the is-responsive class to it
                if (data.fluid) {
                    wrapper
                        .addClass("is-responsive")
                        .css({
                            width: "",
                            height: ""
                        });
                } else {

                    // If this was switch from fluid to non fluid, we need to update with the appropriate width / height so video doesn't go crazy
                    if (wrapper.hasClass('is-responsive')) {
                        data.width = wrapper.width();
                        data.height = wrapper.outerHeight();
                    }

                    self.width().val(data.width);
                    self.height().val(data.height);

                    // If this is not a fluid layout, we should set a width / height of the wrapper
                    wrapper
                        .css({
                            width: data.width,
                            height: data.height
                        })
                        .removeClass("is-responsive");
                }
            },

            setOverlay: function(block) {
                var data = blocks.data(block);
                var overlay = self.getOverlay(block);

                // Clear the element if necessary
                if (overlay.element().length > 0) {
                    overlay.element().empty();
                }

                // Set the wrapper layout
                self.setWrapperLayout(block);

                // Append the embed codes
                overlay.element().append(data.embed);

                // Refresh the overlay
                overlay.refresh();

                // Set the overlay data so we don't create overlays all the time
                block.data('overlay', overlay);
            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // http://www.dailymotion.com/video/x1mxoxx_a-baby-play-with-cat-really-very-very-funny-and-cute-vedio_fun
                var regex = /^(http|https):\/\/www\.dailymotion\.com\/video\/(.*)$/
                var valid = regex.test(url);

                return valid;
            },

            loading: function() {
                var content = blocks.getBlockContent(currentBlock);
                var loader = self.loader.inside(content);
                var form = self.form.inside(content);

                if (!self.isLoading) {
                    loader.removeClass('hidden');
                    form.addClass('hidden');

                    self.isLoading = true;
                } else {
                    loader.addClass('hidden');
                    form.removeClass('hidden');

                    self.isLoading = false;
                }
            },

            crawl: function(url, block) {
                var data = blocks.data(block);
                var content = blocks.getBlockContent(block);

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

                    // Set block's data here
                    data.url = url;
                    data.author = {"name": result.oembed.author_name, "url": result.oembed.author_url};
                    data.embed = result.oembed.html;
                    data.width = result.oembed.width;
                    data.height = result.oembed.height;

                    // Set the overlay
                    self.setOverlay(block);

                    // Update fieldset attributes
                    self.updateFieldset(block);

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

                // Add overlay when the user add's a new link.
                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();
                var data = blocks.data(currentBlock);

                // Ensure that the url is valid
                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return;
                }

                // Create an iframe, append it to this document where specified
                self.crawl(url, currentBlock);
            },

            "{fluid} change": function(el, event) {

                var enabled = el.val() == 1 ? true : false;

                var data = blocks.data(currentBlock);
                data.fluid = enabled;

                self.setWrapperLayout(currentBlock);

                // If this is using a fluid layout, do not allow them to set the width and height
                self.widthFieldset()
                    .toggleClass('hide', enabled);
                self.heightFieldset()
                    .toggleClass('hide', enabled);

                // Refresh the overlay
                var overlay = self.getOverlay(currentBlock);
                overlay.refresh();
            },

            "{width} keyup": $.debounce(function(el, event){
                var data = blocks.data(currentBlock);
                var width = el.val();

                // If there's no value at all, don't resize the video's width
                if (width == 0) {
                    return;
                }

                // Set the width
                data.width = width;

                // Get the overlay
                var overlay = self.getOverlay(currentBlock);

                // Update the wrapper's width
                var wrapper = self.wrapper.inside(currentBlock);
                wrapper.css('width', width);

                // Refresh the overlay
                var overlay = self.getOverlay(currentBlock);
                overlay.refresh();
            }, 250),

            "{height} keyup": $.debounce(function(el, event){
                var data = blocks.data(currentBlock);
                var height = el.val();

                // If there's no value at all, don't resize the video's width
                if (height == 0) {
                    return;
                }

                // Set the height
                data.height = height;

                // Update the placeholder's width
                var wrapper = self.wrapper.inside(currentBlock);
                wrapper.css('height', height);

                // Refresh the overlay
                var overlay = self.getOverlay(currentBlock);
                overlay.refresh();

            }, 250),

            "{updateUrl} click": function(el, event) {
                var url = self.url().val();
                var data = blocks.data(currentBlock);
                var content = blocks.getBlockContent(currentBlock);

                // Verify the source url
                if (!self.isUrlValid(url)) {
                    self.errorMessage.inside(content).removeClass('hide');
                    return;
                }

                // Crawl and inject contents
                self.crawl(url, currentBlock);
            }
        }
    });

    module.resolve();

});
