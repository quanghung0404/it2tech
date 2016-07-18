EasyBlog.module("composer/document", function($){

var module = this;

EasyBlog.require()
.script(
    "composer/document/toolbar",
    "composer/document/artboard"
)
.done(function(){

EasyBlog.Controller("Composer.Document",
{
    hostname: "document",

    pluginExtendsInstance: true,

    elements: [
        "[data-eb-composer-document] .eb-composer-{viewport|viewport-content}",
        "[data-eb-composer-{page|page-viewport|page-header|page-body}]",
        "[data-ebd-{workarea|textarea}]",
        "[data-ebd-workarea-{ghosts}]"
    ],

    defaultOptions: {

        "{root}": "[data-ebd-workarea] " + EBD.root,
        "{block}": EBD.block,
        "{nest}": EBD.nest,

        "{titleField}": "[data-eb-composer-form=page] [name=title]"
    }
},
function(self, opts, base, editor, blocks) { return {

    init: function() {

        composer = self.composer;

        self.initPlugins();
        self.initTitlebar();

        // Document
        if (self.isLegacy()) {
            self.initLegacyDocument();
        } else {
            self.initEasyBlogDocument();
        }
    },

    initPlugins: function() {

        self.addPlugin("toolbar");
        self.addPlugin("artboard");
    },

    initTitlebar: function() {

        // Title bar
        EasyBlog.require()
            .library(
                "expanding"
            )
            .done(function(){
                // When title field gets focused for the first time,
                // implement expanding textarea.
                var titleField =
                    self.titleField()
                        .one("focus", function(){
                            titleField.expandingTextarea();
                        });
            });
    },

    initLegacyDocument: function() {

        // TinyMCE
        if (window.tinyMCE) {

            var setupTinyMCE = function() {

                // Wait until tinyMCE editor is ready
                if (tinyMCE.activeEditor) {

                    var editorContainer = tinyMCE.activeEditor.editorContainer;

                    // In Joomla 2.5, editorContain is a string containing the id
                    // to the tinyMCE container. It already has 100% width, so
                    // there's nothing more to do.
                    if (!$.isString(editorContainer)) {

                        // Ensure tinyMCE has 100% width because this value
                        // could not be set via $editor->display().
                        editorContainer.style.width = "100%";

                        // This ensure the entire tinyMCE body is focusable.
                        $(tinyMCE.activeEditor.contentDocument)
                            .find("html")
                                .css({
                                    height: "100%"
                                })
                                .end()
                            .find("body")
                                .css({
                                    height: "100%",
                                    margin: "1em"
                                });
                    }

                    self.setLayout();

                } else {
                    setTimeout(setupTinyMCE, 500);
                }
            }

            setupTinyMCE();
        }

        self.trigger("composerDocumentReady");
    },

    initEasyBlogDocument: function() {

        EasyBlog.require()
            .library(
                "selectionchange"
            )
            .script(
                "composer/redactor10",
                "composer/document/overlay"
            )
            .done(function(){

                // Initialize redactor
                editor = self.composer.editor =
                    self.workarea()
                        .composer({
                            replaceDivs: false,
                            toolbar: false
                        })
                        .data("redactor");

                // Expose blocks
                blocks = self.composer.blocks;

                // Initialize document.
                self.initDocument();

                // Set document layout.
                self.setLayout();

                // Install document plugins
                self.addPlugin("overlay");

                self.initSelectionChange();

                self.trigger("composerDocumentReady");
            });
    },

    isLegacy: function() {

        return composer.getDoctype()=="legacy";
    },

    initDocument: function() {

        self.trigger("composerDocumentInit");

        self.workarea()
            .removeClass("is-loading");
    },

    loadDocument: function(html) {
        self.trigger("composerDocumentLoad");

        self.root().html(html);
        self.initDocument();
    },

    "{window} resize": $.throttle(function() {

        self.setLayout();

    }, 250),

    setLayout: function() {

        self.updateEditorHeight();

        // Trigger composerDocumentRefresh
        // Overlay plugin listens to this event to to reposition overlays.
        self.trigger("composerDocumentRefresh");
    },

    updateEditorHeight: function() {

        // Set page height to auto
        self.page()
            .css("height", "auto");

        var viewportHeight = self.viewport().height();
        var pageViewportHeight = self.pageViewport().height();
        var pageHeaderHeight = self.pageHeader().height();
        var pageBodyHeight = pageViewportHeight - pageHeaderHeight;
        var pageViewportVerticalPadding = 48 + 60;
        var pageVerticalPadding = 30 * 2;
        var toolbarHeight = 50;

        var pageBodyMinHeight =
                viewportHeight -
                toolbarHeight -
                pageVerticalPadding -
                pageViewportVerticalPadding -
                pageHeaderHeight;

        if (window.tinyMCE) {

            if (!tinyMCE.activeEditor) return;

            // Get editor container
            var editorContainer = tinyMCE.activeEditor.editorContainer;

            // Joomla 2.5
            if ($.isString(editorContainer)) {

                var pageBody = self.pageBody();

                // Get editor container
                var editorContainer = $("#" + editorContainer);

                // Remove enforced height on content table
                editorContainer.find("#content_tbl").css("height", "auto");

                // Set iframe height
                var editorIframe = $(tinyMCE.activeEditor.contentAreaContainer).find("iframe");
                var editorHeightWithoutIframe = pageBody.height() - editorIframe.height();
                var editorIframeHeight = pageBodyHeight - editorHeightWithoutIframe;
                editorIframe.css("height", editorIframeHeight);

                // Set textarea height
                var editorTextarea = $(tinyMCE.activeEditor.getElement());
                var editorTextareaHeight = pageBodyHeight - pageBody.find(".toggle-editor").height();
                editorTextarea.css("height", editorTextareaHeight);

            // Joomla 3.x
            } else {

                var editorContainer = $(editorContainer).parent();
                var editorIframe = $(editorContainer).find("iframe");
                var editorHeightWithoutIframe = editorContainer.height() - editorIframe.height();
                var editorIframeHeight = pageBodyHeight - editorHeightWithoutIframe;

                // Set iframe height
                editorIframe.css("height", editorIframeHeight);
            }

        } else {

            // Adding a min-height to fill up available vertical area
            // of the page viewport allow user to drag & drop on a
            // wider area of whitespace.
            self.root()
                .css({
                    minHeight: pageBodyMinHeight
                });
        }
    },

    setTitle: function(title) {
        self.titleField().val(title);
    },

    insertContent: function(html) {

        // Legacy
        if (self.isLegacy()) {
            EasyBlog.LegacyEditor.insert(html);

        // EBD
        } else {

            // If html passed in is a block, add block to document.
            var block = $(html);

            if (block.is(EBD.block)) {
                blocks.addBlock(block);

            // If html is plain html, put it inside custom html block.
            } else {
                // TODO: Not sure if it's a good idea to create a custom html block for this.
            }
        }
    },

    setContent: function(html) {

        if (self.isLegacy()) {
            return EasyBlog.LegacyEditor.setContent(html);
        } else {
            // Disabled for EBD document. Use blocks API instead.
        }
    },

    getContent: function() {

        // Legacy
        if (self.isLegacy()) {
            return EasyBlog.LegacyEditor.getContent();

        // EBD
        } else {
            var html = "";

            blocks.getRootBlocks().each(function(){
                var block = $(this);
                html += blocks.getBlockHTML(block);
            });
            return html;
        }
    },

    getText: function() {

        // Legacy
        if (self.isLegacy()) {

            return $(self.getContent()).text();

        // EBD
        } else {

            // TODO: Use blocks.getBlockText instead.
            var text = [];
            blocks.getRootBlocks().each(function() {
                var block = $(this),
                    blockType = blocks.getBlockType(block);
                    blockHandler = blocks.getBlockHandler(blockType);
                blockHandler.toText && text.push( $.trim(blockHandler.toText(block)) );
            });

            return text.join("\n");
        }
    },

    initSelectionChange: function() {

        // Enable selectionchange polyfill
        $.selectionchange.start();

        // This determines if composerTextDeselect was called before.
        var deselected;

        var eventHandler = $.debounce(function() {

            // If we're on legacy editor, stop.
            if (!editor) return;

            var selection = editor.selection.get(),
                text = selection.toString(),
                hasSelection = false;

            // If there is text selected
            if (text!='') {

                // Get parent block
                var node = selection.focusNode;
                var parentBlock = self.block.of(node);

                // No text select event on standalone block/workarea
                if (parentBlock.closest(".is-standalone").length) {
                    return;
                }

                // If parent block is activated
                if (parentBlock.hasClass("active")) {

                    // Crawl up every node up til the parent block
                    while (node != parentBlock[0]) {

                        var $node = $(node);

                        // And find out if text selection is
                        // inside an editable element.
                        if ($node.editable()) {
                            hasSelection = true;
                            break;
                        }

                        node = node.parentNode;
                    }
                }
            }

            if (hasSelection) {
                self.trigger("composerTextSelect", [selection, parentBlock, editor]);
                deselected = false;
            } else {
                !deselected && self.trigger("composerTextDeselect", [editor]);
            }

        }, 100);

        $(document).on("selectionchange", eventHandler);
    },

    saveLegacyDocument: function(save) {

        // Get save data
        var saveData = save.data;

        // Get content
        var content = $.sanitizeHTML(EasyBlog.LegacyEditor.getContent());

        // If we're on IE 8, restore double quotes on html attributes.
        if ($.IE < 9) {
            content = $.toXHTML(content);
        }

        // Set intro and content into save data.
        saveData.intro = '';
        saveData.content = content;

        // If there is a read more divider,
        // place content before it as intro,
        // place content after it as content.
        var parts = content.split('<hr id="system-readmore" />');
        if (parts.length > 1) {
            saveData.intro = parts[0];
            saveData.content = parts[1];
        }
    },

    saveEasyBlogDocument: function(save) {

        var rootBlocks = blocks.getRootBlocks();
        var blockManifests = [];
        var tasks = [];
        var master = save.add('Saving document');

        // Construct document manifest
        var documentManifest = {
            title: save.data.title,
            permalink: save.data.permalink,
            type: composer.getDoctype(),
            version: "1.0"
        };

        // When all tasks are done
        $.when.apply(null, tasks)
            .always(function(){

                // Generate block items
                rootBlocks.each(function() {

                    // Get block manifest
                    var block = $(this);
                    var blockManifest = blocks.exportBlock(block);

                    // Add to array of block manifests
                    blockManifests.push(blockManifest);
                });

                // Construct document manifest
                documentManifest.blocks = blockManifests;

                // Output to console
                // EasyBlog.debug && console.info("Document:", documentManifest);
                // EasyBlog.debug && console.log(self.toHTML(documentManifest));
                save.data["document"] = JSON.stringify(documentManifest);
            })
            .done(function(){
                master.resolve();
            })
            .fail(function(){
                master.reject($.Exception("Error saving document."));
            });
    },

    "{self} composerSave": function(el, event, save) {

        // Legacy Document
        if (composer.getDoctype() == 'legacy') {
            self.saveLegacyDocument(save);

        // EasyBlog Document
        } else {
            self.saveEasyBlogDocument(save);
        }
    },

    "{titleField} keydown": function(titleField, event) {

        // Do not allow next line on blog title
        if (event.keyCode==13) {
            event.preventDefault();
        }
    },

    "{titleField} keyup": function(titleField, event) {

        var title = titleField.val();
        self.trigger("composerTitleChange", [title]);
    },

    "{self} composerDebugActivate": function() {

        // self.workarea()
        //     .addClass("is-debugging");
    },

    "{self} composerDebugDeactivate": function() {

        // self.workarea()
        //     .removeClass("is-debugging");
    },

    "{viewport} click": function(viewport, event) {

        // Skip when on legacy document
        if (self.isLegacy()) return;

        // Skip when resizing block
        var workarea = self.workarea();

        if (workarea.hasClass("is-resizing")) {
            return;
        }

        var blocks = $(event.target).parentsUntil(viewport).andSelf().filter(EBD.block);

        if (!blocks.length) {
            self.trigger("composerDocumentBlur");
        }
    },

    "{self} composerValidate": function(composer, event, validator) {
    }

}});

module.resolve();

});

});
