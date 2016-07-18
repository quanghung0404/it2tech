EasyBlog.module("composer/blocks/handlers/links", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Links", {

        defaultOptions: {

            // Loader
            "{loader}": "> [data-link-loader]",

            // Form Items
            "{form}": "> [data-link-form]",
            "{addLink}": "[data-link-add]",
            "{linkInput}": "[data-link-input]",
            "{errorMessage}": "[data-link-error]",

            // Preview items
            "{preview}": "> [data-link-preview]",
            "{previewTitle}": "> [data-link-preview] [data-preview-title]",
            "{previewContent}": "> [data-link-preview] [data-preview-content]",
            "{previewLink}": "> [data-link-preview] [data-preview-link]",
            "{previewImage}": "> [data-link-preview] [data-preview-image]",
            "{imageWrapper}": "> [data-link-preview] [data-preview-image-wrapper]",

            // Fieldset
            "{settings}": "[data-eb-composer-block-links-image]",
            "{imageList}": "[data-eb-composer-block-links-image] [data-images]",
            "{imagePlaceholder}": "[data-eb-composer-block-links-image] [data-image-placeholder]",
            "{currentIndex}": "[data-eb-composer-block-links-image] [data-image-current-index]",
            "{totalImages}": "[data-eb-composer-block-links-image] [data-images-total]",
            "{previousImage}": "[data-eb-composer-block-links-image] [data-images-previous]",
            "{nextImage}": "[data-eb-composer-block-links-image] [data-images-next]",
            "{showImage}": "[data-links-image]"
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
                // Set as current block
                currentBlock = block

                // Populate fieldset
                self.populate(block);
            },

            deactivate: function(block) {

            },

            construct: function(block) {
            },

            toText: function(block) {
                var blockContent = blocks.getBlockContent(block),
                    url = self.previewLink.inside(blockContent).html();

                return url;
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toHTML: function(block) {

                var data = blocks.data(block);
                if (!data.url) return "";

                var clone = block.clone();
                var deconstructedBlock = self.deconstruct(clone);
                var content = blocks.getBlockContent(deconstructedBlock);

                return content.html();
            },

            makeEditable: function(block) {

                // Make link title editable
                var blockContent = blocks.getBlockContent(block);

                self.previewTitle
                    .inside(blockContent)
                    .editable(true)
                    .on('click', function(event) {
                        event.preventDefault();
                    });

                // Make preview content editable
                self.previewContent
                    .inside(blockContent)
                    .editable(true);


                //disable image wrapper and links clickable
                self.previewLink
                    .inside(blockContent)
                    .on('click', function(event) {
                        event.preventDefault();
                    });

                //disable image wrapper and links clickable
                self.imageWrapper
                    .inside(blockContent)
                    .on('click', function(event) {
                        event.preventDefault();
                    });
            },

            reconstruct: function(block) {
                var data = blocks.data(block);
                var blockContent = blocks.getBlockContent(block);

                // If previous data is empty, we should ensure that the link's form is visible again
                if (!data.url && !data.title) {
                    blockContent.html(meta.html);
                }

                // Make items editable again
                self.makeEditable(block);
            },

            deconstruct: function(block) {

                var blockContent = blocks.getBlockContent(block);
                var data = blocks.data(block);
                var parent = self.preview.inside(blockContent);

                // Remove the image if necessary
                if (!data.showImage) {
                    self.imageWrapper.inside(blockContent).remove();
                }

                // Disallow editable
                self.previewTitle.inside(blockContent).editable(false);
                self.previewContent.inside(blockContent).editable(false);

                // remove the form and loader
                self.loader.inside(blockContent).remove();

                // Remove the form
                self.form.inside(blockContent).remove();

                // Remove attributes
                self.preview.inside(blockContent).removeAttr('data-link-preview');
                self.previewImage.inside(blockContent).removeAttr('data-preview-image');
                self.previewTitle.inside(blockContent).removeAttr('data-preview-title')
                self.previewContent.inside(blockContent).removeAttr('data-preview-content');

                // Remove the parent.
                blockContent.html(parent.html());
                parent.remove();

                return block;
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block) {
                var data = blocks.data(block);

                // Repopulate the images on the fieldset
                if (data.images && data.images.length > 0) {
                    self.populateImages(data.images, data.image);
                } else {
                    self.populateImages('', '');
                }

                // always show the settings;
                self.settings().removeClass('hide');

                // if (data.images.length >= 1) {
                //     self.settings().removeClass('hide');
                // } else {
                //     self.settings().addClass('hide');
                // }

                self.showImage().val(data.showImage ? 1 : 0);
                self.showImage().trigger('change');
            },

            populateImages: function(images, selectedImage) {

                // Get the selected index
                var selectedIndex = $.inArray(selectedImage, images);

                // Set the current index numbering. It should always increment by 1 since the array starts with 0
                self.currentIndex().html(selectedIndex + 1);

                // Set the total number of images
                self.totalImages().html(images.length);

                // Remove all existing image list
                self.imageList().children('img').remove();

                if (!images) {
                    return false;
                }

                $.each(images, function(i, source) {
                    var newImage = $(new Image());

                    newImage.attr('src', source);

                    // Hide placeholders
                    self.imagePlaceholder().addClass('hidden');

                    // Hide other items apart from the first item
                    newImage.addClass('hidden');

                    if (i == $.inArray(selectedImage, images)) {
                        newImage.removeClass('hidden');
                    }

                    self.imageList().append(newImage);
                });
            },

            updatePreview: function() {
                var blockContent = blocks.getBlockContent(currentBlock);
                var data = blocks.data(currentBlock);

                self.previewTitle
                    .inside(blockContent)
                    .html(data.title)
                    .attr('href', data.url);

                self.previewContent
                    .inside(blockContent)
                    .html(data.content);

                self.previewLink
                    .inside(blockContent)
                    .html(data.url)
                    .attr('href', data.url);

                self.previewImage
                    .inside(blockContent)
                    .attr('src', data.image);

                self.imageWrapper
                    .inside(blockContent)
                    .attr('href', data.url);

            },

            getSuggestions: function(url, block, callback) {

                // Run an ajax call to retrieve url suggestions
                EasyBlog.ajax('site/views/crawler/crawl', {
                    "url": url
                })
                .done(callback)
                .fail(function(message){

                    // Show form
                    self.showForm(block);

                    // Show error message
                    self.errorMessage().html(message).removeClass('hide');
                });
            },

            hideForm: function(block) {
                var blockContent = blocks.getBlockContent(block);

                // Hide the form and display the loader
                self.form.inside(blockContent).addClass('hidden');

                self.loader.inside(blockContent).removeClass('hidden');
            },

            showForm: function(block) {
                var blockContent = blocks.getBlockContent(block);

                self.form.inside(blockContent).removeClass('hidden');

                self.loader.inside(blockContent).addClass('hidden');
            },

            "{showImage} change": function(el, event) {
                var blockContent = blocks.getBlockContent(currentBlock);
                var data = blocks.data(currentBlock);
                var showImage = el.val();

                if (showImage == 0) {
                    data.showImage = false;

                    self.imageWrapper.inside(blockContent).addClass('hide');
                } else {

                    data.showImage = true;

                    self.imageWrapper.inside(blockContent).removeClass('hide');
                }
            },

            "{previousImage} click": function(el, event) {
                var images = self.imageList().children('img');
                var activeImage = self.imageList().children('img:not(.hidden)');
                var previousImage = $(activeImage).prev('img');
                var currentIndex = parseInt(self.currentIndex().html());
                var data = blocks.data(currentBlock);

                if (previousImage.length > 0) {

                    // Update the preview
                    data.image = previousImage.attr('src');
                    self.updatePreview();

                    // Add hidden class on all images
                    $(images).addClass('hidden');

                    // Remove hidden on the next image
                    $(previousImage).removeClass('hidden');


                    var index = currentIndex - 1;

                    // console.log(index);

                    // Update the index
                    self.currentIndex().html(index);
                }

                // The next button could be disabled
                self.nextImage().disabled(false);

                // We need to add disabled attribute on the next icon when there's nothing more
                if ((currentIndex - 1) == 1) {
                    $(el).disabled(true);
                } else {
                    $(el).disabled(false);
                }
            },

            "{nextImage} click": function(el, event)  {
                var images = self.imageList().children('img');
                var activeImage = self.imageList().children('img:not(.hidden)');
                var nextImage = $(activeImage).next('img');
                var currentIndex = parseInt(self.currentIndex().html());
                var data = blocks.data(currentBlock);

                if (nextImage.length > 0) {

                    // Update the preview
                    data.image = nextImage.attr('src');
                    self.updatePreview();

                    // Add hidden class on all images
                    $(images).addClass('hidden');

                    // Remove hidden on the next image
                    $(nextImage).removeClass('hidden');

                    // Update the index
                    self.currentIndex().html(currentIndex + 1);
                }

                // The next button could be disabled
                self.previousImage().disabled(false);

                // We need to add disabled attribute on the next icon when there's nothing more
                if ((currentIndex + 1) == images.length) {
                    $(el).disabled(true);
                } else {
                    $(el).disabled(false);
                }

            },

            "{addLink} click": function(el) {

                var blockContent = blocks.getBlockContent(currentBlock);
                var url = self.linkInput.inside(blockContent).val();
                var data = blocks.data(currentBlock);

                if (!url) {
                    self.errorMessage().removeClass('hide');
                    return;
                }

                // Hide the form
                self.hideForm(currentBlock);

                self.getSuggestions(url, currentBlock, function(results){

                    // Get the result of the share
                    var result = results[url];

                    // Update the blocks data
                    data.title = result.title;
                    data.content = result.description;
                    data.url = url;
                    data.images = result.images;
                    data.image = result.images[0];
                    data.showImage = true;

                    // Update the preview
                    self.updatePreview();

                    // Display the preview and hide the form
                    self.loader.inside(blockContent).addClass('hidden');
                    self.preview.inside(blockContent).removeClass('hidden');

                    self.makeEditable();

                    // Repopulate the images on the fieldset
                    self.populate(currentBlock);
                })
            }
        }
    });

    module.resolve();

});
