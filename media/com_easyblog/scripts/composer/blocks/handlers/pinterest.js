EasyBlog.module("composer/blocks/handlers/pinterest", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Pinterest", {

        defaultOptions: $.extend({

            // Form
            "{form}": "> [data-pinterest-form]",
            "{insert}": "[data-pinterest-insert]",
            "{source}": "[data-pinterest-source]",
            "{loader}": "> [data-pinterest-loader]",

            // Preview
            "{preview}": "> [data-pinterest-preview]",
            "{errorMessage}": "[data-pinterest-error]",
            "{fsSource}": "[data-fs-pinterest-source]",
            "{fsUpdate}": "[data-fs-pinterest-update]"
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
                var content = blocks.getBlockContent(block);


                // Set the overlay
                if (data.embed) {

                    // Load the scripts
                    self.loadScript();

                    // Append the embed codes
                    content.html(data.embed);
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

                if (data.url) {
                    self.fsSource().val(data.url);
                }

            },

            isUrlValid: function(url) {

                if (url == '') {
                    return false;
                }

                // https://www.pinterest.com/pin/436075176395864062/
                var regex = /^https:\/\/www\.pinterest\.com\/pin\/(.*)$/;
                var valid = regex.test(url);

                return valid;
            },

            insertEmbed: function(block, url) {

                var content = blocks.getBlockContent(block);
                var data = blocks.data(block);
                var template = $('<a>');

                template
                    .attr('data-pin-do', 'embedPin')
                    .attr('href', url);

                // Set the url so that during export, we can send it back
                data.url = url;
                data.embed = template.toHTML();

                if (!self.isUrlValid(url)) {
                    self.errorMessage().removeClass('hide');
                    return false;
                }

                // Inject the codes into the block content
                content.html(template.toHTML());

                // Hide the form
                self.form.inside(content).addClass('hidden');
            },

            scriptLoaded: false,
            loadScript: function() {

                if (!self.scriptLoaded) {
                    $('<script>')
                        .attr('type', 'text/javascript')
                        .attr('src', '//assets.pinterest.com/js/pinit.js')
                        .appendTo('head');

                    self.scriptLoaded = true;
                }
            },

            "{insert} click": function(button) {

                if (currentBlock.length < 1) {
                    var block = self.block.of(button);
                    blocks.activateBlock(block);
                }

                var content = blocks.getBlockContent(currentBlock);
                var url = self.source.inside(content).val();

                // Load the script
                self.loadScript();

                // Update the source in the fieldset
                self.fsSource().val(url);

                // Insert the embeded object
                self.insertEmbed(currentBlock, url);
            },

            "{fsUpdate} click": function() {

                // Add overlay when the user add's a new link.
                var content = blocks.getBlockContent(currentBlock);
                var url = self.fsSource().val();

                self.insertEmbed(currentBlock, url);
            }
        }
    });

    module.resolve();

});
