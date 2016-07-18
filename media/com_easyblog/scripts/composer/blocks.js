EasyBlog.module("composer/blocks", function($){

var module = this,

    // Block States
    isNew        = "is-new",
    isEditable   = "is-editable",
    isReceiving  = "is-receiving",
    isDropping   = "is-dropping",
    isReleasing  = "is-releasing",
    isRefocusing = "is-refocusing",
    isHighlighting = "is-highlighting",

    isSorting   = "is-sorting",
    isSortItem  = "is-sort-item",
    isNested    = "is-nested",

    isDraggingBlock = "is-dragging-block",
    isDroppingBlock = "is-dropping-block",

    // Workarea States
    isHighlightingBlock = "is-highlighting-block";

EasyBlog.require()
.library(
    "ui/draggable",
    "scrollTo"
)
.script(
    "composer/blocks/panel",
    "composer/blocks/guide",
    "composer/blocks/nestable",
    "composer/blocks/droppable",
    "composer/blocks/resizable",
    "composer/blocks/scrollable",
    "composer/blocks/font",
    "composer/blocks/dimensions",
    "composer/blocks/text",
    "composer/blocks/removal",
    "composer/blocks/tree",
    "composer/blocks/search",
    "composer/blocks/mobile",
    "composer/blocks/media",
    "composer/blocks/toolbar"
)
.done(function(){

EasyBlog.Controller("Composer.Blocks", {
    hostname: "blocks",

    pluginExtendsInstance: true,

    elements: [
        "[data-eb-composer-{blocks}]",
        "[data-eb-composer-block-{menu|menu-group|meta}]",
        "[data-eb-composer-{editor}]",
        "[data-ebd-block-{viewport|content}]"
    ],

    defaultOptions: $.extend({
        "{view}": "[data-eb-composer-blocks]",
        "{editableContent}": EBD.editableContent,
        "{immediateBlockViewport}": EBD.immediateBlockViewport,
        "{immediateBlockContent}": EBD.immediateBlockContent,
        "{sidebar}": "[data-eb-composer-blocks]",
        "{mechswitch}": ".mechswitch"
    }, EBD.selectors)
},
function(self, opts, base, composer) { return {

    init: function() {

        // Globals
        composer = self.composer;

        // Block plugins
        var plugins = [
            "panel",
            "guide",
            "nestable",
            "droppable",
            "resizable",
            "scrollable",
            "font",
            "dimensions",
            "text",
            "removal",
            "tree",
            "search",
            "mobile",
            "media",
            "toolbar"
        ];

        // Legacy post loads only panel & dimensions
        if (composer.getDoctype()=="legacy") {
            var plugins = [
                "panel",
                "tree",
                "dimensions",
                "media"
            ];
        }

        $.each(plugins, function(i, plugin){
            self.addPlugin(plugin);
        });

        // Preload the following blocks
        var preloadBlocks = [
            "post",
            "image",
            "video",
            "file",
            "audio"
        ];

        $.each(preloadBlocks, function(i, blockType) {
            self.loadBlockHandler(blockType);
        });
    },

    getAllBlocks: function()  {
        return self.root().find(EBD.block);
    },

    getCurrentBlock: function() {
        return self.root().find(EBD.block + ".active");
    },

    getParentBlock: function(block) {
        if (!block.is(EBD.nestedBlock)) return $();
        return $(block).parents(EBD.block).eq(0);
    },

    getAllParentBlocks: function(block) {
        return block.parentsUntil(EBD.root).filter(EBD.block);
    },

    getBlockTree: function(block) {
        return self.getAllParentBlocks(block).add(self.root());
    },

    getRootBlocks: function() {
        return self.root().find(EBD.childBlock);
    },

    getChildBlocks: function(block) {
        return block.is(EBD.root) ?
            block.find(EBD.childBlock) :
            block.find(EBD.nestedBlock + ":not(.ebd-block[data-uid=" + block.data("uid") + "] " + EBD.nest + " " + EBD.nest + " " + EBD.nestedBlock + ")");
    },

    getAllChildBlocks: function(block) {
        return block.find(EBD.block);
    },

    getBlocksByType: function(type) {
        return self.root().find(EBD.block + "[data-type=" + type + "]");
    },

    getBlock: function(uid) {
        var block = base.find(EBD.block + "[data-uid=" + uid + "]");
        return block;
    },

    getBlockUid: function(block) {
        // If block has no uid, set one.
        return block.attr("data-uid") || self.setBlockUid(block);
    },

    setBlockUid: function(block) {

        // If block already has a uid, just return it.
        return block.attr("data-uid") || (function(){
            var uid = $.uid();
            block.attr("data-uid", uid);
            return uid;
        })();
    },

    getBlockType: function(block) {
        return $(block).attr("data-type");
    },

    metas: {},

    getBlockMeta: function(blockType) {

        if (blockType instanceof $) {
            blockType = self.getBlockType(blockType);
        }

        return self.metas[blockType] || (function(){

            // Extract inline block meta within block menu
            var inlineBlockMeta = self.meta().where("type", blockType).val()

            // If inline block meta exists
            if (inlineBlockMeta) {
                // Parse, cache and return it.
                return self.metas[blockType] = JSON.parse(inlineBlockMeta);

            // If inline block meta doesn't exist, return null.
            } else {
                return null;
            }

        })();
    },

    getBlockViewport: function(block) {

        return self.immediateBlockViewport.inside(block);
    },

    getBlockContent: function(block) {

        return self.immediateBlockContent.inside(block);
    },

    getBlockFragment: function(block) {

        var getFragment = function(block) {

            var blockType = self.getBlockType(block),
                blockHandler = self.getBlockHandler(blockType),
                blockFragment = self.createBlockContainer(blockType),
                blockHTML = blockHandler.toHTML(block);

            if (block.is(EBD.nested)) {
                block.addClass(isNested);
            }

            blockFragment.append(blockHTML);

            return blockFragment;
        }

        var blockFragment = getFragment(block);

        blockFragment.find(EBD.nestedBlock)
            .each(function(){

                var nestedBlock = $(this),
                    nestedBlockFragment = getFragment(nestedBlock);

                nestedBlock.replaceWith(nestedBlockFragment);
            });

        return blockFragment;
    },

    getBlockHTML: function(block) {

        return self.getBlockFragment(block).toHTML();
    },

    getBlockText: function(block) {

        // TODO: See document.getText() for more info.
    },

    getBlockNest: function(block) {
        return block.closest(EBD.nest);
    },

    getBlockNestType: function(block) {
        return self.getBlockNest(block).data("type");
    },

    isBlock: function(block) {
        return block.is(EBD.block);
    },

    isNestedBlock: function(block) {
        return block.is(EBD.nestedBlock);
    },

    isRootBlock: function(block) {
        return !self.isNestedBlock(block);
    },

    isStandaloneBlock: function(block) {
        return block.is(EBD.standaloneBlock);
    },

    restoreInlineBlockData: function(block) {

        // If there is inline block data
        var inlineBlockData = block.next();

        if (inlineBlockData.is("textarea[data-block]")) {

            // Extract block data from inline block data
            var rawBlockData = inlineBlockData.val();
            var blockData = JSON.parse(rawBlockData);

            // Attach block data into dataset by block uid
            self.dataset[self.getBlockUid(block)] = blockData;

            // Remove inline block data
            inlineBlockData.remove();
        }
    },

    initBlock: function(block) {

        // For compressibility
        var args = arguments;
        var type = self.getBlockType(block);

        // Assign a block id if necessary
        self.setBlockUid(block);

        // Restore inline block data if necessary
        self.restoreInlineBlockData(block);

        // Load block handler
        // If block handler has been loaded, the operation here is synchronous.
        // If block handler hasn't been loaded, the operation here is asynchronous.
        self.loadBlockHandler(type)
            .done(function(handler){

                // If this is a new block
                if (block.hasClass(isNew)) {

                    // Trigger composerBlockBeforeAdd
                    self.trigger("composerBlockBeforeAdd", args);

                    try {
                        // Reset, reconstruct and refocus on block
                        handler.reset(block);
                        handler.reconstruct(block);
                    } catch(ex) {
                        EasyBlog.debug && console.error("Error initializing new block of type '%s'.", type, ex);
                    }

                    // Trigger composerBlockAdd
                    self.trigger("composerBlockAdd", args);

                    // Simulate composerBlockRelease
                    self.release(block);

                    // Remove new block flag
                    block
                        .removeClass(isNew)
                        .addClass(isEditable);

                    self.trigger("composerBlockInit", args);

                // If this is a viewable block
                } else if (!block.hasClass(isEditable)) {

                    try {
                        // Reconstruct block to convert into editable block
                        handler.reconstruct(block);
                    } catch(ex) {
                        EasyBlog.debug && console.error("Error initializing existing block of type '%s'.", type, ex);
                    }

                    // Add editable block flag
                    block.addClass(isEditable);

                    self.trigger("composerBlockInit", args);
                }
            });

        // Get child blocks
        self.getChildBlocks(block)
            .each(function(){
                // Initialize all child blocks
                var childBlock = $(this);
                self.initBlock(childBlock);
            });
    },

    createBlockContainer: function(blockType) {

        var blockMeta = self.getBlockMeta(blockType);
        var blockContainer = $($('[data-eb-block-template]').clone().html());

        blockContainer
            .attr("data-type", blockType);

        return blockContainer;
    },

    createBlock: function(blockType) {
        // Get block meta
        var blockMeta = self.getBlockMeta(blockType);

        // If no block meta found, do not create block.
        if (!blockMeta) {
            return;
        }

        // Create block
        var block = $(blockMeta.block);

        // Assign block uid
        self.setBlockUid(block);

        // Trigger composerBlockCreate
        self.trigger("composerBlockCreate", [block, blockMeta]);

        return block;
    },

    createBlockFromMenu: function(menu) {

        // Get block type
        var blockType = menu.attr("data-type"),

            // Create block and set new block flag
            block = self.createBlock(blockType).addClass(isNew);

        return block;
    },

    createBlockFromMediaFile: function(mediaFile) {

        // Determine block type by media file type
        var blockType = mediaFile.data("type");

        // Get block handler
        var blockHandler = self.getBlockHandler(blockType);

        // Construct block from media file
        var block = blockHandler.constructFromMediaFile(mediaFile);

        return block;
    },

    constructBlock: function(blockType, blockData) {

        var blockHandler = self.getBlockHandler(blockType);
        var block = blockHandler.construct(blockData);

        // Assign block uid
        self.setBlockUid(block);

        self.trigger("composerBlockConstruct", [block]);

        return block;
    },

    constructNestedBlock: function(blockType, blockData) {

        // Construct block
        var block = self.constructBlock(blockType, blockData);

        // Add is-nested class
        block.addClass(isNested);

        return block;
    },

    constructIsolatedBlock: function(blockType, blockData) {

        // Construct block
        var block = self.constructBlock(blockType, blockData);

        // Add is-nested & is-isolated class
        block
            .addClass(isNested)
            .addClass(isIsolated);

        return block;
    },

    createBlockNest: function() {

        return $('<div class="ebd-nest" data-type="block">');
    },

    createContentNest: function(options) {

        // Create nest
        var nest = $('<div class="ebd-nest" data-type="content">'),

            // Normalize options
            defaultOptions = {
                // paragraph: true,
                editable: true
            },

            options = $.extend(defaultOptions, options);

        // This makes nest editable by default
        options.editable && nest.editable(true);

        // This ensure content are always wrapped in <p> tags.
        // options.paragraph && nest.attr("data-paragraph", "true");

        return nest;
    },

    exportBlock: function(block) {

        var blockUid     = self.getBlockUid(block),
            blockType    = self.getBlockType(block),
            blockHandler = self.getBlockHandler(blockType),
            isNested     = block.is(EBD.nestedBlock),
            isIsolated   = block.is(EBD.isolatedBlock);

        // Create block manifest
        var blockManifest = {
            uid: blockUid,
            type: blockType,
            html: "",
            data: {},
            blocks: [],
            nested: isNested,
            isolated: isIsolated,
            style: block.attr('style')
        };

        // @debug: Verify if handler has toData, toText, toHTML methods.
        EasyBlog.debug && self.verifyBlockHandler(blockHandler, ["toData", "toText", "toHTML"]);

        // Data, text, html
        blockHandler.toData && (blockManifest.data = blockHandler.toData(block) || {});
        blockHandler.toText && (blockManifest.text = blockHandler.toText(block) || "");
        blockHandler.toHTML && (blockManifest.html = blockHandler.toHTML(block) || "");

        if (blockHandler.toEditableHTML) {
            blockManifest.editableHtml = blockHandler.toEditableHTML(block) || "";
        } else {
            var blockContent = blocks.getBlockContent(block);
            blockManifest.editableHtml = blockContent.html();
        }

        // Trigger ComposerBlockExport event for plugins to further decorate this block.
        self.trigger("composerBlockExport", [block, blockManifest]);

        // Return block manifest
        return blockManifest;
    },

    addBlock: function(block) {

        self.root().append(block);

        self.initBlock(block);
    },

    removeBlock: function(block) {

        self.trigger("composerBlockBeforeRemove", [block]);

        // Find nested block and remove them
        block.find(EBD.nestedBlock)
            .each(function(){

                var nestedBlock = $(this);

                // Remove nested block
                self.removeBlock(nestedBlock);
            });

        // Remove block
        block.remove();

        // Trigger composerBlockRemove event
        self.trigger("composerBlockRemove", [block]);
    },

    scrollTo: function(block) {

        var viewport = composer.viewport();

        composer.viewportContent()
            .stop(true)
            .scrollTo(block, 500, {
                axis: 'y',
                offset: {
                    top: (viewport.height() - block.height()) / -2,
                    left: 0
                }
            });
    },

    blockHandlers: {},

    loadBlockHandler: $.memoize(function(blockType) {

        // Reject invalid handler type
        if (!blockType) {
            return $.Deferred().reject($.Exception("Invalid block type given."));
        }

        // Get block meta
        var blockMeta = self.getBlockMeta(blockType);
        if (!blockMeta) {
            return $.Deferred().reject($.Exception("Block of type '" + blockType + "' is not installed!"));
        }

        var loader = $.Deferred();

        EasyBlog.require()
            .script("composer/blocks/handlers/" + blockType)
            .done(function(){

                // Construct block handler plugin namespace
                var pluginName = "handler/" + blockType;
                var controllerName = "EasyBlog.Controller.Composer.Blocks.Handlers." + $.String.capitalize(blockType);
                var pluginProps = {meta: blockMeta};

                // Install block handler plugin
                var blockHandler = self.addPlugin(pluginName, controllerName, pluginProps);

                // Keep a reference to this block handler in the blockHandlers registry
                self.blockHandlers[blockType] = blockHandler;

                // If we're debugging, verify handler completeness.
                EasyBlog.debug && self.verifyBlockHandler(blockHandler);

                // Resolve loader
                loader.resolve(blockHandler, blockMeta);
            })
            .fail(function(){

                // Do not memoize if loading of handler failed
                self.loadBlockHandler.reset(blockType);

                // Reject loader
                loader.reject($.Exception("Could not load block handler for " + blockType + "."));
            });

        return loader;
    }),

    getBlockHandler: function(blockType) {

        // Also accept block as first argument
        if (blockType instanceof $) {
            blockType = self.getBlockType(blockType);
        }

        return self.blockHandlers[blockType];
    },

    verifyBlockHandler: function(blockHandler, methods) {

        var methods = methods || ["activate", "deactivate", "construct", "reconstruct", "deconstruct", "refocus", "reset", "populate", "toHTML", "toData", "toText"],
            method,
            missing = [];

        while (method = methods.shift()) {
            if (!$.isFunction(blockHandler[method])) {
                missing.push(method);
            }
        }

        missing.length > 0 && console.warn("Block handler of type '%s' is missing the following method: %s.", blockHandler.options.meta.type, missing.join(", "));
    },

    activateBlock: function(block) {

        // Do not activate block when we're removing block
        if (self.workarea().hasClass("is-removing")) return;

        // Deactivate any current block
        self.deactivateBlock();

        var type = self.getBlockType(block);

        // Load block handler
        self.loadBlockHandler(type)
            .done(function(handler){

                var args = [block, handler];

                // Trigger composerBlockBeforeActivate
                self.trigger("composerBlockBeforeActivate", args);

                // Initialize block
                self.initBlock(block);

                // Activate block handler
                handler.activate && handler.activate(block);

                // Refocus block
                handler.refocus(block);

                // Trigger composerBlockActivate
                self.trigger("composerBlockActivate", args);
            })
            .fail(function(exception){

                self.trigger("composerBlockActivateError", [exception]);
            });
    },

    deactivateBlock: function(block) {

        block = block || self.getCurrentBlock();

        // If no block found, stop.
        if (!block.length) return;

        // Get block handler
        var blockHandler = self.getBlockHandler(block);

        // Deactivate block handler
        blockHandler && blockHandler.deactivate && blockHandler.deactivate(block);

        // Trigger composerBlockDeactivate
        self.trigger("composerBlockDeactivate", [block, blockHandler]);
    },

    "{menu} mouseover": function(menu) {

        // Only initialize draggable on mouseover
        if (!menu.data("uiDraggable")) {

            // Prepare lightweight helper
            var helper = menu.clone();

            // Remove unnecessary inline block meta
            helper
                .find(self.meta.selector)
                .remove();

            menu.draggable({
                helper: function() {
                    return helper.css({
                        width: menu.outerWidth(),
                        height: menu.outerHeight()
                    })
                },
                appendTo: composer.document.ghosts(),
                connectToSortable: EBD.root
            });
        }
    },

    selectBlock: function(menu) {

        // Hide the blocks menu
        composer.views.hide('blocks');

        // Add state is-dropping-block
        composer.manager()
            .addClass(isDroppingBlock);

        // Trigger composerBlockMenuSelected so the world can listen to this
        self.trigger("composerBlockMenuSelected", [menu]);

        // Show the drop zones
        composer.blocks.droppable.populateDropzones();
    },

    "{menu} touchend": function(menu) {
    },

    "{menu} touchstart": function(menu) {
    },

    "{menu} click": function(menu) {
        self.selectBlock(menu);
    },

    "{menu} dragstart": function(menu) {

        // Tell block host we're dragging this menu
        self.drag(menu);

        // When a block menu is being dragged we want to add a class on the manager
        composer.manager()
            .addClass(isDraggingBlock);

        // Hide block view
        composer.views.hide("blocks");
    },

    "{menu} dragstop": function(menu) {

        // Tell block host we're done with this menu
        self.release(menu);

        composer.manager()
            .removeClass(isDraggingBlock);
    },

    "{menu} dblclick": function(menu) {
        // TODO: Double click to insert block
    },

    drag: function(block) {

        // Add is-sorting class to workarea
        self.workarea()
            .addClass(isSorting);

        // Skip new block
        if (block.is(self.menu)) {
            return;
        }

        // Add is-sort-item class to block
        block.addClass(isSortItem);

        // Carets will go out of place after sorting an existing block,
        // this removes carets from editor when sorting starts.
        composer.editor.selection.remove();

        // Trigger composerBlockDrop event
        self.trigger("composerBlockDrag", [block]);
    },

    drop: function(block) {

        // Skip new block
        if (block.is(self.menu)) {
            return;
        }

        // Trigger composerBlockBeforeRelease event
        self.trigger("composerBlockBeforeDrop", [block]);

        // Add is-dropping class
        block.addClass(isDropping);

        // Trigger composerBlockDrop event
        self.trigger("composerBlockDrop", [block]);

        // Activate block
        self.activateBlock(block);
    },

    release: function(block) {

        // Remove is-sorting class from workarea
        self.workarea().removeClass(isSorting);

        // Skip new block
        if (block.is(self.menu)) {
            return;
        }

        // This is for the zoom effect on existing block
        block
            .removeClass(isDropping)
            .addClass(isReleasing)
            .removeClassAfter(isReleasing + " " + isSortItem);

        // Trigger composerBlockBeforeRelease event
        self.trigger("composerBlockBeforeRelease", [block]);

        setTimeout(function(){

            // Trigger composerBlockReleased event
            self.trigger("composerBlockRelease", [block]);

        }, 1000);
    },

    over: function(block) {

        // Add is-receiving class on dropzone
        block.addClass(isReceiving);

        // Add is-receiving class on parent nest
        if (block.hasClass(isNested)) {
            self.nest.of(block)
                .addClass(isReceiving);
        }

        // Add is-receiving class on parent block
        self.block.of(block)
            .addClass(isReceiving);
    },

    out: function(block) {

        // Add is-receiving class on dropzone
        block.removeClass(isReceiving);

        // Add is-receiving class on parent nest
        if (block.hasClass(isNested)) {
            self.nest.of(block)
                .removeClass(isReceiving);
        }

        // Add is-receiving class on parent block
        self.block.of(block)
            .removeClass(isReceiving);
    },

    highlight: function(block) {

        self.root()
            .addClass(isHighlightingBlock);

        block.addClass(isHighlighting);
    },

    unhighlight: function(block) {

        self.root()
            .removeClass(isHighlightingBlock)

        block.removeClass(isHighlighting);
    },

    dataset: {},

    "{block} click": function(block, event) {

        // Do not activate block if:
        // - Nested block has been activated (because click event propagates from nested block to parent block).
        // - User is resizing block.
        if (event.clickHandled || self.refocusing || self.workarea().hasClass("is-resizing")) {
            return;
        }

        // This stops click event from propagating to parent block
        event.clickHandled = true;

        // Only activate if it hasn't been activated before
        if (!block.is(self.menu) && !block.hasClass("active")) {
            self.activateBlock(block);
        }
    },

    data: function(block, key, value) {
        if (block === undefined) {
            return self.dataset;
        }

        var uid = parseInt(block);

        if (isNaN(uid)) {
            // If block cannot be parsed as a number, means it is a jQuery object
            // We then extract the uid from it
            uid = self.getBlockUid(block);
        } else {
            // If uid is a number, means block is not a jQuery object
            // Also means that it is safe to override block variable with jQuery object since block is initially string/number, which is not by reference
            block = self.getBlock(uid);
        }

        var handler = self.getBlockHandler(self.getBlockType(block));
        var meta = blocks.getBlockMeta(block);
        var data = self.dataset[uid];

        self.dataset[uid] =
            handler.normalize ?
                handler.normalize(data) :
                $.extend({}, meta.data, data);

        if (arguments.length == 0 || arguments.length == 1 || (arguments.length == 2 && $.isString(key))) {
            return self.getData(block, key);
        }

        return self.setData.apply(self, arguments);
    },

    getData: function(block, key) {

        var dataset = self.dataset;

        if (block === undefined) {
            return dataset;
        }

        var uid = parseInt(block);

        if (isNaN(uid)) {
            // If block cannot be parsed as a number, means it is a jQuery object
            // We then extract the uid from it
            uid = self.getBlockUid(block);
        } else {
            // If uid is a number, means block is not a jQuery object
            // Also means that it is safe to override block variable with jQuery object since block is initially string/number, which is not by reference
            block = self.getBlock(block);
        }

        var blockDataset = dataset[uid];

        if (key === undefined) {
            return blockDataset;
        }

        return blockDataset[key];
    },

    setData: function(block, key, value) {

        if (block === undefined) {
            return self.dataset;
        }

        var uid = parseInt(block);

        if (isNaN(uid)) {
            // If block cannot be parsed as a number, means it is a jQuery object
            // We then extract the uid from it
            uid = self.getBlockUid(block);
        } else {
            // If uid is a number, means block is not a jQuery object
            // Also means that it is safe to override block variable with jQuery object since block is initially string/number, which is not by reference
            block = self.getBlock(block);
        }

        if ($.isPlainObject(key) && value === undefined) {
            return $.extend(self.dataset[uid], key);
        }

        return self.dataset[uid][key] = value;
    },

    getMechanics: function() {

        return composer.settings.get("blocks.mechanics");
    },

    setMechanics: function(type) {

        composer.settings.set("blocks.mechanics", type);

        self.trigger("composerBlockMechanicsChange", type);
    },

    "{mechswitch} click": function(mechswitch) {

        // These are all temporary for now
        self.mechswitch().removeClass("btn-primary");
        mechswitch.addClass("btn-primary");

        self.setMechanics(mechswitch.attr("data-value"));
    },

    "{block} mouseover": function(block, event) {

        // This ensures that only the top most block gets hovered.
        if (event.hovered) {
            return;
        }

        event.hovered = true;

        self.trigger("composerBlockHoverIn", [block]);
    },

    "{block} mouseout": function(block, event) {
        self.trigger("composerBlockHoverOut", [block]);
    },

    "{self} composerDocumentInit": function() {

        // Get all blocks
        var allBlocks = self.getAllBlocks();

        // Initialize all blocks
        allBlocks.each(function(){
            var block = $(this);
            self.initBlock(block);
        });
    },

    "{self} composerDocumentBlur": function() {

        if (self.refocusing) {
            // Negate refocusing after the first blur event
            self.refocusing = false;
            return;
        }

        self.deactivateBlock();
    },

    "{editableContent} focusin": function(editableContent, event) {

        if (event.focusHandled || self.refocusing) return;

        // Not application for legacy document
        if (composer.document.isLegacy()) return;

        self.refocusing = true;

        // This prevents focusin from propagating to parent block
        event.focusHandled = true;

        // Needs to be in a set timeout so we can
        // get the actual node where the caret is
        // focusing on.
        setTimeout(function(){

            var workarea = self.workarea();

            // Get focus node
            var selection = composer.editor.selection.get();
            var node = selection.focusNode;

            // If no focus node, stop.
            if (!node) {
                self.refocusing = false;
                return;
            }

            // Get block
            var block = self.block.of(node);

            // If no block found, stop.
            if (!block.length) {
                self.refocusing = false;
                return;
            }

            // If block is being refocused, stop.
            if (block.hasClass(isRefocusing)) return;

            // Add refocusing class
            // This is because when activating/refocusing block,
            // block handler may trigger focus event anywhere within
            // the editable content, this flag prevents recursion.
            block.addClass(isRefocusing);

            // Putting this in a try..catch so user can
            // continue editing when there is a failure
            // in activating or refocusing.
            try {

                // Activate block
                if (!block.hasClass("active")) {
                    self.activateBlock(block);

                // Refocus block
                } else {
                    var blockHandler = self.getBlockHandler(block);
                    blockHandler.refocus(block);
                }

            } catch(e) {}

            // Remove refocusing class
            block.removeClass(isRefocusing);

            // This needs to be delayed because when blocks are
            // being activated, focusing may happen causing
            // recursion in this event handler.
            setTimeout(function(){
                self.refocusing = false;
            }, 100);

        }, 1);
    }

}});

module.resolve();

});
});

