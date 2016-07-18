EasyBlog.module("composer/blocks/handlers/file", function($){

var module = this;

EasyBlog.Controller("Composer.Blocks.Handlers.File", {

    defaultOptions: {

        // Browse button in placeholder
        "{browseButton}": ".eb-composer-placeholder-file [data-eb-mm-browse-button]",

        "{fileError}": "[data-eb-file-error]",

        "{placeholder}": "[data-eb-composer-file-placeholder]",
        "{player}": "[data-file-preview]",
        "{dropElement}": "[data-plupload-drop-element]",

        // Fieldset options
        "{showIcon}": "[data-file-fieldset-icon]",
        "{showSize}": "[data-file-fieldset-size]",

        // Template parameters
        "{fileName}": "[data-file-name]",
        "{fileType}": "[data-file-type]",
        "{fileIcon}": "[data-file-icon]",
        "{fileSize}": "[data-file-size]",
        "{fileUrl}": "[data-file-url]"

    }
}, function(self, opts, base, composer, blocks, meta, currentBlock, mediaManager) {

    return {

        init: function() {

            // Globals
            blocks = self.blocks;
            composer = blocks.composer;
            meta = opts.meta;
            currentBlock = $();
            mediaManager = EasyBlog.MediaManager;
        },

        toData: function(block) {

            var data = blocks.data(block);
            return data;
        },

        toText: function(block) {
            return;
        },

        toHTML: function(block) {

            var data = blocks.data(block);
            if (!data.url) return "";

            var cloned = block.clone();
            var deconstructedBlock = self.deconstruct(cloned);
            var content = blocks.getBlockContent(deconstructedBlock);

            return content.html();
        },

        constructFromMediaFile: function(mediaFile) {

            var key = mediaFile.data("key");
            var uri = mediaManager.getUri(key);

            // Create block container first
            var block = blocks.createBlockContainer("file");
            var blockContent = blocks.getBlockContent(block);
            var data = blocks.data(block);

            // Add loading indicator
            block.addClass("is-loading");

            // Get media meta
            mediaManager.getMedia(uri)
                .done(function(media){

                    var mediaMeta = media.meta;

                    self.createPreview(block, mediaMeta.url, mediaMeta.title, mediaMeta.extension, mediaMeta.size);
                    // self.createPlayer(block, mediaMeta.url, mediaMeta.title);
                })
                .fail(function(){
                })
                .always(function(){
                    block.removeClass("is-loading");
                });

            return block;
        },

        toLegacyHTML: function(meta, block) {
            var data = blocks.data(block);

            var link = $('<a>').attr({
                                "href": meta.url
                            }).html(meta.title);

            return link.prop('outerHTML');
        },

        activate: function(block) {

            // Set as current block
            currentBlock = block

            // Populate fieldset
            self.populate(block);
        },

        deactivate: function(block) {

        },

        construct: function(data) {
            var block = blocks.createBlockContainer('file');
            var blockData = blocks.data(block);

            $.extend(blockData, data);

            return block;
        },

        reconstruct: function(block) {

            var data = blocks.data(block);
            var placeholder = self.placeholder.inside(block);
            var dropElement = self.dropElement.inside(placeholder);

            // If this is an edited item, we need to reconstruct the player again
            if (data.url) {
                self.createPreview(block, data.url, data.name, data.type, data.size);
            }

            // Register the placeholder with mediamanager
            if (dropElement.length > 0) {
                EasyBlog.MediaManager.uploader.register(placeholder);
            }
        },

        deconstruct: function(block) {
            var content = blocks.getBlockContent(block);

            self.fileName.inside(content)
                .editable(false);

            return block;
        },

        refocus: function(block) {
        },

        reset: function(block) {
        },

        populate: function(block) {

            // When populating the fieldset for a block, reset the values
            var data = blocks.data(block);

            // Update the fieldsets
            self.showIcon().val(data.showicon ? 1 : 0)
                .trigger('change');

            self.showSize().val(data.showsize ? 1 : 0)
                .trigger('change');
        },

        getPreviewTemplate: function() {
            var template = $(meta.preview);

            return $(meta.preview);
        },

        createPreview: function(block, url, fileName, fileType, fileSize) {

            var blockContent = blocks.getBlockContent(block);
            var template = self.getPreviewTemplate();
            var data = blocks.data(block);

            data.url = url;
            data.name = fileName;
            data.type = fileType;
            data.size = fileSize;

            // Set a temporary id to the preview container.
            template.attr('id', data.uid);

            // set filename
            self.fileName
                .inside(template)
                .text(data.name)
                .editable(true);

            // set filetype
            self.fileType
                .inside(template)
                .text(data.type);

            // set filesize
            var size = $.plupload2.formatSize(data.size);

            self.fileSize
                .inside(template)
                .text(size);

            self.fileUrl.inside(template)
                .attr('href', data.url)
                .on('click', function(event) {
                    event.preventDefault();
                });

            // Replace the placeholder with the preview's template
            blockContent.html(template);

        },

        "{showIcon} change": function(el, event) {

            var content = blocks.getBlockContent(currentBlock);
            var data = blocks.data(currentBlock);

            data.showicon = el.val() == 1 ? true : false;

            if (data.showicon) {
                // remove hide class
                self.fileIcon.inside(content)
                    .removeClass('hide');

            } else {
                // add hide class
                self.fileIcon.inside(content)
                    .addClass('hide');
            }
        },

        "{showSize} change": function(el, event) {

            var content = blocks.getBlockContent(currentBlock);
            var data = blocks.data(currentBlock);

            data.showsize = el.val() == 1 ? true : false;

            if (data.showsize) {
                // remove hide class
                self.fileSize.inside(content)
                    .parent('div').removeClass('hide');

            } else {
                // add hide class
                self.fileSize.inside(content)
                    .parent('div').addClass('hide');
            }
        },


        "{placeholder} mediaUploaderFilesAdded": function(placeholder, event, uploader, files) {
            EasyBlog.MediaManager.uploader.addItem(files[0], placeholder);
        },

        "{placeholder} mediaUploaderFileUploaded": function(placeholder, event, uploader, file, data) {

            var response = data.response;
            var mediaItem = response.media;
            var mediaMeta = mediaItem.meta;

            // Get the current block hosting the placeholder
            var block = blocks.block.of(placeholder);

            setTimeout(function() {
                self.createPreview(block, mediaMeta.url, mediaMeta.title, mediaMeta.extension, mediaMeta.size);
            }, 600);
        },

        "{placeholder} mediaUploaderFileError": function(placeholder, event, uploader, error) {

            if (error.code == $.plupload2.FILE_EXTENSION_ERROR) {
                self.fileError.inside(currentBlock).removeClass('hide');
            }
        },

        "{placeholder} mediaUploaderError": function(placeholder, event, uploader, error) {
        },

        "{self} mediaInfoDisplay": function(el, event, info, media) {

            if (media.meta.type != 'file') {
                return;
            }

            // We need to disable the download link in the info.
            $(info).find('[data-file-url]').on('click', function(event) {
                event.preventDefault();
            })
        },


        "{browseButton} mediaSelect": function(browseButton, event, media) {

            var block = blocks.block.of(browseButton);

            if (media.meta.type != "file") {
                return;
            }

            var mediaMeta = media.meta;
            var composerDocument = composer.document;
            var isLegacy = composerDocument.isLegacy();

            // Legacy
            if (isLegacy) {
                content = self.toLegacyHTML(block);
                composerDocument.insertContent(content);

            // EBD
            } else {
                self.createPreview(block, mediaMeta.url, mediaMeta.title, mediaMeta.type, mediaMeta.size);
            }
        },

        "{self} mediaInsert": function(el, event, media, block) {

            if (media.meta.type != 'file') {
                return;
            }

            var composerDocument = composer.document;
            var isLegacy = composerDocument.isLegacy();

            if (isLegacy) {
                content = self.toLegacyHTML(media.meta, block);
                composerDocument.insertContent(content);
            } else {
                // Construct a new post block and insert into the document
                var block = blocks.constructBlock('file', {
                    "name": media.meta.title,
                    "type": media.meta.extension,
                    "size": media.meta.size,
                    "url": media.meta.url
                });

                blocks.addBlock(block);
                blocks.activateBlock(block);
            }
        }
    }
});

module.resolve();

});
