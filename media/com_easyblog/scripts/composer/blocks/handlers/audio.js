EasyBlog.module("composer/blocks/handlers/audio", function($){

    var module = this;

    EasyBlog.require()
    .library('plupload2', 'audiojs')
    .done(function($) {

        EasyBlog.Controller("Composer.Blocks.Handlers.Audio", {
            elements: [
                "[data-eb-{file-error}]"
            ],
            defaultOptions: {

                // Browse button in placeholder
                "{browseButton}": ".eb-composer-placeholder-audio [data-eb-mm-browse-button]",

                "{audio}": "audio",
                "{placeholder}": "[data-eb-composer-audio-placeholder]",

                // Preview area
                "{infoBox}": "[data-audio-infobox]",
                "{artist}": "[data-audio-artist]",
                "{track}": "[data-audio-track]",
                "{trackSeparator}": "[data-audio-track-separator]",
                "{download}": "[data-audio-download]",

                // Fieldset area
                "{displayArtist}": "[data-audio-fieldset-artist]",
                "{displayTrack}": "[data-audio-fieldset-track]",
                "{displayDownload}": "[data-audio-fieldset-download]",
                "{autoplay}": "[data-audio-fieldset-autoplay]",
                "{loop}": "[data-audio-fieldset-loop]"
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

                toText: function(block) {
                    return;
                },

                toData: function(block) {
                    var data = blocks.data(block);
                    var content = blocks.getBlockContent(block);

                    // Set the download url
                    data.download = self.download.inside(content).attr('href');

                    // Set the artist
                    data.artist = self.artist.inside(content).text();

                    // Set the track
                    data.track = self.track.inside(content).text();

                    return data;
                },

                toHTML: function(block) {
                    var block = block.clone();
                },

                toLegacyShortcode: function(meta, block) {

                    var obj = {
                        "uri": meta.uri
                    };

                    var str = '[embed=audio]' + JSON.stringify(obj) + '[/embed]';

                    return str;
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

                    var block = blocks.createBlockContainer('audio');
                    var blockData = blocks.data(block);

                    $.extend(blockData, data);

                    return block;
                },

                constructFromMediaFile: function(mediaFile) {

                    var key = mediaFile.data("key");
                    var uri = mediaManager.getUri(key);

                    // Create block container first
                    var block = blocks.createBlockContainer("audio");
                    var blockContent = blocks.getBlockContent(block);
                    var data = blocks.data(block);

                    // Add loading indicator
                    block.addClass("is-loading");

                    // Get media meta
                    mediaManager.getMedia(uri)
                        .done(function(media){

                            var mediaMeta = media.meta;

                            self.createPlayer(block, mediaMeta.url, mediaMeta.title);
                        })
                        .fail(function(){
                        })
                        .always(function(){
                            block.removeClass("is-loading");
                        });

                    return block;
                },

                reconstruct: function(block) {
                    var placeholder = self.placeholder.inside(block);
                    var data = blocks.data(block);

                    if (data.url) {
                        self.createPlayer(block, data.url, data.track);
                    }

                    // Register the placeholder with mediamanager
                    if (placeholder.length > 0) {
                        EasyBlog.MediaManager.uploader.register(placeholder);
                    }
                },

                deconstruct: function(block) {
                },

                refocus: function(block) {
                },

                reset: function(block) {
                    var content = blocks.getBlockContent(block);
                },

                populate: function(block) {

                    // When populating the fieldset for a block, reset the values
                    var data = blocks.data(block);

                    self.updateFieldset(block);
                },

                updateFieldset: function(block) {
                    // When populating the fieldset for a block, reset the values
                    var data = blocks.data(block);

                    self.autoplay()
                        .val(data.autoplay ? '1' : '0')
                        .trigger('change');

                    self.loop()
                        .val(data.loop ? '1' : '0')
                        .trigger('change');

                    self.displayArtist()
                        .val(data.showArtist ? '1' : '0')
                        .trigger('change');

                    self.displayDownload()
                        .val(data.showDownload ? '1' : '0')
                        .trigger('change');

                    self.displayTrack()
                        .val(data.showTrack ? '1' : '0')
                        .trigger('change');
                },

                getPlayerTemplate: function() {
                    return $(meta.player);
                },

                createPlayer: function(block, url, fileName) {

                    var blockContent = blocks.getBlockContent(block);
                    var template = self.getPlayerTemplate();
                    var data = blocks.data(block);
                    var uid = data.uid || (data.uid = $.uid("audio-"));
                    var url = data.url || url;
                    var track = fileName || '';
                    var artist = data.artist;

                    // Set the data url
                    data.url = url;
                    data.track = track;
                    data.artist = artist;
                    data.uid = uid;

                    self.audio.inside(template)
                        .attr('id', uid)
                        .attr('src', url);

                    self.artist.inside(template)
                        .html(data.artist)
                        .editable(true);

                    self.track.inside(template)
                        .html(data.track)
                        .editable(true);

                    self.download.inside(template)
                        .attr('href', url)
                        .attr('target', 'blank')
                        .on('click', function(event){
                            event.preventDefault();

                            // Do not allow click to happen here on composer.
                        });


                    // Append the template into the block content
                    blockContent.html(template);

                    $.audiojs.events.ready(function(){
                        $.audiojs.create(blockContent.find('audio'));
                    });

                    // Update the output based on the data
                    if (!data.showDownload) {
                        self.download.inside(blockContent).addClass('hide');
                    }

                    if (!data.showArtist) {
                        self.artist.inside(blockContent).addClass('hide');
                    }

                    if (!data.showTrack) {
                        self.track.inside(blockContent).addClass('hide');
                    }

                    self.updateInfoBox(block);
                },

                // "{artist} keypress": function(el, event) {

                //     var data = blocks.data(currentBlock);

                //     data.artist = $(el).text();

                // },

                updateInfoBox: function(block) {

                    var data = blocks.data(block);
                    var content = blocks.getBlockContent(block);
                    var infoBox = self.infoBox.inside(content);

                    if (!data.showDownload && !data.showTrack && !data.showArtist) {
                        infoBox.addClass('disabled');
                        return;
                    }

                    infoBox.removeClass('disabled');
                },

                "{displayDownload} change": function(el, event){
                    var content = blocks.getBlockContent(currentBlock);
                    var enabled = el.val() == 1 ? true : false;

                    var data = blocks.data(currentBlock);
                    data.showDownload = enabled;

                    // Update the infobox
                    self.updateInfoBox(currentBlock);

                    if (!enabled) {
                        self.download.inside(currentBlock)
                            .addClass('hide');

                        return;
                    }

                    self.download.inside(currentBlock)
                        .removeClass('hide');
                },

                "{displayArtist} change": function(el, event) {
                    var content = blocks.getBlockContent(currentBlock);
                    var enabled = el.val() == 1 ? true : false;
                    var artist = self.artist.inside(content);

                    var data = blocks.data(currentBlock);
                    data.showArtist = enabled;

                    // Update the infobox
                    self.updateInfoBox(currentBlock);

                    if (!enabled) {
                        artist.addClass('hide');

                        return;
                    }

                    artist.removeClass('hide');
                },

                "{displayTrack} change": function(el, event) {
                    var content = blocks.getBlockContent(currentBlock);
                    var enabled = el.val() == 1 ? true : false;
                    var track = self.track.inside(content);
                    var trackSeparator = self.trackSeparator.inside(content);

                    var data = blocks.data(currentBlock);
                    data.showTrack = enabled;

                    // Update the infobox
                    self.updateInfoBox(currentBlock);

                    if (!enabled) {
                        trackSeparator.addClass('hide');
                        track.addClass('hide');

                        return;
                    }

                    trackSeparator.removeClass('hide');
                    track.removeClass('hide');
                },

                "{autoplay} change": function(el, event) {
                    var data = blocks.data(currentBlock);
                    var enabled = el.val() == 1 ? true : false;

                    data.autoplay = enabled;
                },

                "{loop} change": function(el, event) {
                    var data = blocks.data(currentBlock);
                    var enabled = el.val() == 1 ? true : false;

                    data.loop = enabled;
                },

                "{placeholder} mediaUploaderFilesAdded": function(placeholder, event, uploader, files) {
                    EasyBlog.MediaManager.uploader.addItem(files[0], placeholder);
                },

                "{placeholder} mediaUploaderFileUploaded": function(placeholder, event, uploader, file, data) {

                    var response = data.response;
                    var mediaItem = response.media;
                    var mediaMeta = mediaItem.meta;

                    var block = blocks.block.of(placeholder);

                    setTimeout(function() {
                        self.createPlayer(block, mediaMeta.url, file.name);

                        if (block.hasClass("active")) {
                            self.populate(block);
                        }

                    }, 600);

                },

                "{browseButton} mediaSelect": function(browseButton, event, media) {

                    var block = blocks.block.of(browseButton);

                    if (media.meta.type != "audio") {
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
                        self.createPlayer(block, mediaMeta.url, mediaMeta.title);
                    }
                },

                "{placeholder} mediaUploaderFileError": function(placeholder, event, uploader, error) {
                    if (error.code == $.plupload2.FILE_EXTENSION_ERROR) {
                        self.fileError.inside(currentBlock).removeClass('hide');
                    }
                },

                "{self} mediaInsert": function(el, event, media, block) {

                    if (media.meta.type != 'audio') {
                        return;
                    }

                    var composerDocument = composer.document;
                    var isLegacy = composerDocument.isLegacy();

                    // Legacy
                    if (isLegacy) {
                        content = self.toLegacyShortcode(media, block);
                        composerDocument.insertContent(content);
                    } else {

                        // EBD
                        // Construct a new post block and insert into the document
                        var block = blocks.constructBlock('audio', {
                            "url": media.meta.url,
                            "track": media.meta.title
                        });

                        blocks.addBlock(block);
                        blocks.activateBlock(block);
                    }

                }
            }
        });

        module.resolve();
    });
});
