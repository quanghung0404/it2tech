EasyBlog.module("composer/blocks/handlers/html", function($) {

    var module = this;

    EasyBlog.require()
    .library('ace')
    .done(function($) {

        EasyBlog.Controller("Composer.Blocks.Handlers.Html", {
            defaultOptions: {

                "{pre}": "[data-eb-composer-blocks-html-pre]"
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
                    // currentBlock = block

                    // Populate fieldset
                    self.populate(block);
                },

                deactivate: function(block) {
                    var blockContent = blocks.getBlockContent(block);

                    var contents = blockContent.html();

                    // If this is an empty content, we need to populate the placeholder text again.
                    if (contents == "") {
                        blockContent.html(meta.html);
                    }
                },

                construct: function(block) {
                },

                reconstruct: function(block) {
                },

                deconstruct: function(block) {

                },

                refocus: function(block) {
                },

                reset: function(block) {
                },

                toText: function(block) {

                    var data = blocks.data(block);
                    var clone = block.clone();
                    var content = blocks.getBlockContent(clone);

                    var text = content.text();

                    return text;
                },

                toData: function(block) {

                    var data = blocks.data(block);

                    return data;
                },

                toHTML: function(block) {

                    var data = blocks.data(block);
                    var clone = block.clone();
                    var content = blocks.getBlockContent(clone);

                    // Sanitize the html on the form
                    var editor = self.editor();
                    var session = editor.getSession();

                    session.setValue(content.html());

                    return content.html();
                },

                editor: $.memoize(function() {

                    // Setup ACE Editor
                    var pre = composer.find(self.pre)[0];


                    // There could be instances where the <pre> isn't loaded yet.
                    if (pre == undefined) {
                        pre = $.create('pre')[0];
                    }

                    var editor = ace.edit(pre);

                    // Configure editor
                    editor.setTheme("ace/theme/github");

                    // Set syntax highlighter to HTML
                    var session = editor.getSession();
                    
                    // Set the default mode to html
                    session.setMode("ace/mode/html");

                    // Automatically update html preview
                    // when user types on the editor.
                    session.on("change", $.debounce(self.sync, 150));

                    return editor;
                }),

                populate: function(block) {
                    var editor = self.editor();
                    var session = editor.getSession();
                    var blockContent = blocks.getBlockContent(block);
                    var html = $.trim(blockContent.html());

                    // Set the html value into the editor
                    session.setValue(html);

                    // Set current block
                    currentBlock = block;
                },

                sync: function() {
                    var editor = self.editor(composer);
                    var session = editor.getSession();
                    var blockContent = blocks.getBlockContent(currentBlock);
                    var html = session.getValue();

                    blockContent.html(html);
                },

                reset: function(block) {
                }
            }
        });

        module.resolve();
    });
});
