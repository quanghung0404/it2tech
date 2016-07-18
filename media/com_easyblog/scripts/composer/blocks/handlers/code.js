EasyBlog.module("composer/blocks/handlers/code", function($) {

    var module = this;

    // This is used to inject ACE Editor within iframe
    var aceEditorScriptPath = $.uri($.require.defaultOptions.path)
                                    .toPath('./ace' + ($.mode=='compressed' ? '.min.js' : '.js'))
                                    .toString();

    // This creates a pseudo FD50 object
    // for ACE Editor module factory to execute
    // when it is loaded inside an iframe.
    window.FD50_PSEUDO = {
        module: function(name, factory) {
            factory.call($.Deferred(), $);
        }
    };

    EasyBlog.Controller("Composer.Blocks.Handlers.Code", {

        defaultOptions: {

            "{pre}" : "> pre",

            "{readOnly}": "[data-code-readonly]",
            "{showGutter}": "[data-code-gutter]",
            "{fontsize}": "[data-code-fontsize]",
            "{themeSelection}": "[data-code-theme]",
            "{modeSelection}": "[data-code-mode]",

            theme: "github",
            mode: "html",
            css: {
                position: "fixed",
                width: "100%",
                height: "100%",
                top: 0,
                bottom: 0,
                left: 0,
                right: 0,
                margin: 0,
                padding: 0
            }
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

            toText: function(block) {
                return;
            },

            toData: function(block) {
                var data = blocks.data(block),
                    uid = block.data('uid');

                instance = self.editor.instances[block.data('uid')];
                contents = instance.editor.getSession().getValue();
                data.code = contents;

                return data;
            },

            toEditableHTML: function(block) {

                // Remove the overlay because we need to regenerate the overlay again later
                var clone = block.clone();
                var blockContent = blocks.getBlockContent(clone);

                blockContent.find('[data-ebd-overlay-placeholder]').remove();

                return blockContent.html();
            },

            toHTML: function(block) {

                // get the editor
               var data = blocks.data(block),
                    uid = block.data('uid');

                instance = self.editor.instances[block.data('uid')];
                contents = instance.editor.getSession().getValue();

                // now we need to put the content back into the pre element
                var cloned = block.clone(),
                    deconstructedBlock = self.deconstruct(cloned),
                    blockContent = blocks.getBlockContent(deconstructedBlock);

                var preEle = self.pre.inside(blockContent);

                preEle.html(contents);

                // we need to set the mode as well.
                preEle.attr('data-mode', data.mode);

                var output = preEle.html();

                return output;
            },

            // When block is focused, this method is triggered.
            // Useful when we need to update the panels for the block.
            activate: function(block) {

                // Set the current block
                currentBlock = block;

                self.populate(block);
            },

            // When a block loses focus, this method is triggered.
            // Useful if we need to perform specific controls over the block when it loses focus.
            deactivate: function() {
                // TODO: Iframe overlay should be placed here
            },

            // When a new block is created programatically from another block, this get's triggered
            construct: function(data) {
                var data = $.extend({}, opts.data, data);
            },

            // To convert a viewable block to an editable block
            reconstruct: function(block) {

                var blockContent = blocks.getBlockContent(block);
                var data = blocks.data(block);

                // Create block overlay
                if (!block.data("overlay")) {

                    // Create overlay
                    var overlay = composer.document.overlay.create(block);

                    // Append overlay placeholder in the
                    // beginning of the block.
                    overlay.placeholder()
                        .prependTo(blockContent);

                    // Attach overlay
                    overlay.attach();

                    block.data("overlay", overlay);
                }

                // This will create the editor if
                // the editor hasn't been created yet.
                self.editor.get(block);
            },

            deconstruct: function(block) {
                return block;
            },

            reset: function(block) {
            },

            refocus: function() {
            },

            populate: function(block) {
                // Populate fieldset values
                var data = blocks.data(block);

                // Update the values of those items on fieldset
                self.modeSelection().val(data.mode);
                self.themeSelection().val(data.theme);

                // Update the show gutter options
                self.showGutter().val(data.show_gutter ? 1 : 0);
                self.showGutter().trigger('change');
            },

            getEditor: function(callback) {
                self.editor.get(currentBlock).done(callback);
            },

            switchMode: function(mode) {
                var data = blocks.data(currentBlock);

                self.getEditor(function(editor, session, pre) {

                    session.setMode('ace/mode/' + mode);

                    data.mode = mode;
                });
            },

            switchTheme: function(theme) {
                var data = blocks.data(currentBlock);

                self.getEditor(function(editor, session,pre){

                    // Update the theme
                    editor.setTheme(theme);

                    data.theme = theme;
                });
            },

            switchInvisible: function(isInvisible) {
                var data = blocks.data(current);

                self.getEditor(function(editor, session, pre){
                    editor.setShowInvisibles(isInvisible);

                    data.show_invisible = isInvisible;
                });
            },

            toggleGutter: function(showGutter) {

                var data = blocks.data(currentBlock);

                self.getEditor(function(editor, session, pre){
                    editor.renderer.setShowGutter(showGutter);

                    data.show_gutter = showGutter;
                });
            },

            updateFontSize: function(size) {
                var data = blocks.data(currentBlock);

                self.getEditor(function(editor, session, pre){
                    editor.setFontSize(size);

                    data.fontsize = size;
                });
            },

            "{fontsize} change": function(el, event) {
                var size = parseInt($(el).val());

                self.updateFontSize(size);
            },

            "{readOnly} change": function(el, event) {
            },

            "{showGutter} change": function(el, event) {
                var showGutter = $(el).val() == 1 ? true : false;

                self.toggleGutter(showGutter);
            },

            "{modeSelection} change": function(el, event) {
                var mode = $(el).val();

                // Switch mode
                self.switchMode(mode);
            },

            "{themeSelection} change": function(el, event) {

                var theme = $(el).val();

                // Switch theme
                self.switchTheme(theme);
            },

            editor: {

                instances: {},

                get: function(block) {

                    // Get instance
                    var instance = self.editor.instances[block.data("uid")];

                    // Return instance if found, else create the instance.
                    return instance || self.editor.create(block);
                },

                create: function(block) {

                    // Create instance
                    var uid = block.data("uid");
                    var blockContent = blocks.getBlockContent(block);
                    var pre = self.pre.inside(blockContent);
                    var instance = self.editor.instances[uid] = $.Deferred();
                    var data = blocks.data(block);

                    // Store overlay within editor instance
                    var overlay = instance.overlay = block.data("overlay");

                    if (!overlay) {
                        overlay = composer.document.overlay.create(block);

                        // Append overlay placeholder in the
                        // beginning of the block.
                        overlay.placeholder()
                            .prependTo(blockContent);

                        // Attach overlay
                        overlay.attach();

                        block.data("overlay", overlay);
                    }

                    // Create iframe
                    var iframe = $.create("iframe");

                    // Set iframe to a path that share the same so
                    // we can inject scripts within iframe.
                    var source = $.rootPath + '/media/index.html';

                    iframe
                        .attr("src", source)
                        .one("load", function() {

                            // Create references to iframe
                            var iframeWindow = iframe[0].contentWindow,
                                iframeDocument = iframeWindow.document,
                                iframeHead = iframeDocument.head,
                                iframeBody = iframeDocument.body,

                                // Clone pre and put it inside iframe
                                iframePre =
                                    pre.clone()
                                        .css(opts.css)
                                        .appendTo(iframeBody)[0];

                                // Iframe has no FD50 bootloader, so we map it to a
                                // fake one so ACE Editor's module factory can execute.
                                iframeWindow.eval("window.FD50 = window.parent.FD50_PSEUDO;");

                                // Load ACE Editor within iframe
                                $.script({
                                        url: aceEditorScriptPath,
                                        head: iframeHead
                                    })
                                    .done(function(){

                                        // Iniitalize ACE Editor
                                        var ace = iframeWindow.ace;
                                        var editor = ace.edit(iframePre);
                                        var theme = data.theme || pre.data('theme') || opts.theme;
                                        var mode = data.mode || pre.data('mode') || opts.mode;
                                        var fontSize = data.fontsize || pre.data('fontsize') || opts.fontsize;
                                        var gutter = data.show_gutter || pre.data('gutter') || opts.show_gutter;

                                        // Set editor options
                                        editor.setTheme(theme);
                                        editor.setFontSize(fontSize);
                                        editor.renderer.setShowGutter(gutter);

                                        // Set the code
                                        if (data.code) {
                                            editor.setValue(data.code);
                                        }

                                        // Set editor's height
                                        $(iframePre).css('height', '100%');

                                        editor.resize();

                                        // Set syntax highlighter to HTML by default
                                        var session = editor.getSession();
                                            session.setMode("ace/mode/" + mode);

                                        instance.editor = editor;

                                        // Resolve instance with editor and session
                                        instance.resolve(editor, session, pre);
                                    })
                                    .fail(function() {

                                        // Reject instance with error message
                                        instance.reject($.Exception("ACE Editor could not be loaded."));

                                        // Do not store this instance so user can retry again
                                        delete self.instances[uid];
                                    });
                        });

                    // Append the iframe into the overlay first
                    overlay.element().append(iframe);

                    return instance;
                }
            }
        }
    });

    module.resolve();

});
