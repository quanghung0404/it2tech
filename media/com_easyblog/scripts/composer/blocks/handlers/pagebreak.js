EasyBlog.module("composer/blocks/handlers/pagebreak", function($){

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Pagebreak", {
        elements: [
            '[data-pagebreak-fieldset-{title|alt}]'
        ],
        defaultOptions: {

            "{spanTitle}": "[data-pagebreak-title]",
            "{spanAlt}": "[data-pagebreak-alt]",

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

            toData: function(block) {
                var data = blocks.data(block);
                return data;
            },

            toText: function(block) {
                return '';
            },

            toHTML: function(block) {

                var data = blocks.data(block);


                // We don't want to return the html codes for the read more
                var hr = $.create('hr');

                if (data.alt) {
                    hr.attr('alt', data.alt);
                }

                if (data.title) {
                    hr.attr('title', data.title);
                }

                hr.attr('class', 'system-pagebreak');

                return hr.toHTML();
            },

            deactivate: function(block) {

            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fielset
                self.populate(block);

            },

            deconstruct: function(data) {
            },

            construct: function(data) {
            },

            reconstruct: function(block) {
                var data = blocks.data(block),
                    blockContent = blocks.getBlockContent(block);

                // Update the blocks content with the appropriate html codes
                blockContent.html(meta.html);

                // update the fieldset
                self.updateFieldset(block);
            },

            refocus: function(block) {
            },

            reset: function(block) {
            },

            populate: function(block) {
                // var data = blocks.data(block);

                self.updateFieldset(block);
            },

            updateFieldset: function(block) {
                var data = blocks.data(block);
                var content = blocks.getBlockContent(block);

                // Update the title
                self.title().val(data.title);

                // Update the alt text
                self.alt().val(data.alt);

                // update spanTitle an spanAlt
                // TITLE text
                var displayText = data.title.length > 0 ? ' - ' + data.title : '';
                self.spanTitle.inside(content)
                    .text(displayText);


                // ALT text
                var displayText = data.alt.length > 0 ? ' - ' + data.alt : '';
                self.spanAlt.inside(content)
                    .text(displayText);


            },

            "{title} keyup": $.debounce(function(el, event){
                var data = blocks.data(currentBlock);
                var content = blocks.getBlockContent(currentBlock);
                var title = el.val();

                // Set the width
                data.title = title;

                var displayText = title.length > 0 ? ' - ' + title : '';
                self.spanTitle.inside(content)
                    .text(displayText);

            }, 250),

            "{alt} keyup": $.debounce(function(el, event){
                var data = blocks.data(currentBlock);
                var content = blocks.getBlockContent(currentBlock);
                var alt = el.val();

                // Set the width
                data.alt = alt;

                var displayText = alt.length > 0 ? ' - ' + alt : '';
                self.spanAlt.inside(content)
                    .text(displayText);

            }, 250)

        }
    });

    module.resolve();
});
