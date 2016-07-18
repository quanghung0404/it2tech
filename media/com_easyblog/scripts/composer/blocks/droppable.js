EasyBlog.module("composer/blocks/droppable", function($) {

    var module = this,
        isNested = "is-nested",
        isReceiving = "is-receiving";

    EasyBlog.require()
    .library(
        "ui/draggable",
        "ui/droppable"
    )
    .done(function(){

        EasyBlog.Controller("Composer.Blocks.Droppable", {
            defaultOptions: $.extend({

                "{dropzones}": "[data-ebd-dropzone]",
                "{dropzonePlaceholder}": "[data-ebd-dropzone-placeholder]",
                "{mediaFile}": "[data-eb-mm-file]"

            }, EBD.selectors),
        }, function(self, opts, base, composer, blocks, currentBlock) { return {

            init: function() {
                blocks = self.blocks;
                composer = blocks.composer;
                currentBlock = $();

                // Detach from DOM, replace method.
                self.dropzonePlaceholder =
                    self.dropzonePlaceholder()
                        .detach()
                        .removeClass("hide")[0];
            },

            "{self} composerReady": function() {

                // Generate draggable options
                self.draggableOptions = {

                    connectWith: EBD.dropzone,

                    // Helper
                    helper: "clone",
                    appendTo: composer.document.ghosts(),

                    // Placeholder
                    placeholder: "ebd-block is-placeholder",

                    // Behaviour
                    refreshPositions: true,

                    // Handle
                    handle: EBD.immediateBlockSortHandle
                };

                // Generate droppable options
                self.droppableOptions = {

                    // Block & media manager file
                    accept: [EBD.block, ".eb-mm-file"].join(","),

                    // Behaviour
                    tolerance: "pointer"
                };

                // Init droppable
                self.enable();
            },

            enable: function() {

                // Get all blocks and implement draggable
                blocks.getAllBlocks()
                    .draggable(self.draggableOptions);
            },

            disable: function() {

                // Get blocks with draggable and destroy draggable
                self.block(".ui-draggable")
                    .draggable("destroy");
            },

            selectedBlock: null,

            "{self} composerBlockMove": function(base, event, block) {
                self.selectedBlock = block;
            },

            "{self} composerBlockMenuSelected": function(base, event, menu) {
                self.selectedBlock = menu;
            },

            "{self} composerBlockInit": function(base, event, block) {
                block.draggable(self.draggableOptions);
            },

            populateDropzones: function() {

                // Populate dropzones within blocks that supports nested
                self.nest()
                    .each(function() {

                        var nest = $(this),
                            type = nest.data('type');

                        // Top/down nesting
                        if (type=="block") {

                            // First dropzone
                            nest.prepend(self.createNestedDropzone());

                            // Subsequent dropzones
                            nest.find(EBD.childBlock)
                                .each(function() {
                                    var block = $(this);
                                    block.after(self.createNestedDropzone());
                                });
                        }

                        // Left/right nesting
                        if (type=="content") {

                            // Get available positions of content nest
                            var positions = blocks.nestable.availablePositions(nest);

                            $.each(positions, function(i, position){

                                nest.prepend(self.createNestedDropzone(position));
                            });
                        }
                    });

                // Subsequent root dropzones
                var rootBlocks = blocks.getRootBlocks();

                rootBlocks
                    .each(function(i){

                        var block = $(this);

                        // First dropzone
                        if (i==0) {
                            block.before(self.createDropzone());
                        }

                        // Skip block being sorted
                        if (block.hasClass("is-sort-item")) return;

                        // Subsequent dropzones
                        block.after(self.createDropzone());
                    });

                // If this document is empty, create one dropzone
                if (rootBlocks.length < 1) {

                    self.root()
                        .append(self.createDropzone());
                }

                // Implement droppable on dropzones
                self.dropzone()
                    .droppable(self.droppableOptions);
            },

            createDropzone: function() {

                return $(self.dropzonePlaceholder).clone();
            },

            createNestedDropzone: function(position) {

                var dropzone =
                    self.createDropzone()
                        .addClass("is-nested");

                if (position) {

                    dropzone
                        .addClass("nest-" + position)
                        .data("position", position);
                }

                return dropzone;
            },

            destroyDropzones: function() {
                self.dropzone().remove();
            },

            // If we're dragging an existing block
            "{block} dragstart": function(block, event, ui) {

                // This prevents draggable from parent block from executing.
                event.stopPropagation();

                // Ensure helper has the same
                // width and height of the original block.
                ui.helper
                    .addClass("is-helper")
                    .css({
                        width: block.width(),
                        height: block.height()
                    });

                // Deactivate all blocks
                blocks.deactivateBlock();

                // Tell block host we're dragging this block
                blocks.drag(block);

                // Hide the block that we're dragging
                block.addClass("hide");

                // Set as current block
                currentBlock = block;

                self.populateDropzones();

                composer.manager()
                    .addClass("is-resizing");
            },

            // If we're dragging from block menu
            "{blocks.menu} dragstart": function(block, event, ui) {

                self.populateDropzones();

                // Set as current block
                currentBlock = block;

                composer.manager()
                    .addClass("is-resizing");
            },

            // If we're dragging from media manager
            "{mediaFile} dragstart": function(mediaFile, event, ui) {

                // Populate dropzones
                self.populateDropzones();

                currentBlock = $();

                composer.manager()
                    .addClass("is-resizing");
            },

            // When block's dropzone is clicked
            "{dropzone} click": function(dropzone, event) {

                // When this is being dragged, don't do anything
                if (composer.manager().hasClass("is-dragging-block")) {
                    return;
                }

                // When a dropzone is clicked, we need to insert the new block
                self.isDropping = true;

                // Get item being dropped
                var item = self.selectedBlock;

                item
                    .removeClass('hide')
                    .removeClass('is-sort-item');

                // Drop item
                self.dropBlock(dropzone, item);

                // Resets the state so that dragstop wouldn't get triggered
                setTimeout(function() {
                    self.isDropping = false;

                    self.dropStop();

                    // Reset the selection
                    self.selectedMenu = null;
                    self.selectedBlock = null;

                    // Remove the class on the manager
                    composer.manager().removeClass("is-dropping-block");
                    composer.manager().removeClass('is-moving-block');
                }, 0);
            },

            "{dropzone} mouseenter": function(dropzone, event) {

                dropzone.addClass("is-receiving");
            },

            "{dropzone} mouseleave": function(dropzone, event) {

                dropzone.removeClass("is-receiving");
            },

            // When block is hovering over a dropzone
            "{dropzone} dropover": function(dropzone, event, ui) {

                blocks.over(dropzone);

                // Set the last known dropzone
                self.lastDropzone = dropzone;

                // Only dropzone of content nest has position
                var position = dropzone.data("position");

                // Add is-sending class on helper
                ui.helper.addClass("is-sending");

                if (position) {
                    self.nest.of(dropzone)
                        .data("snappedPosition", position);
                }
            },

            // When block is hovering out of a dropzone
            "{dropzone} dropout": function(dropzone, event, ui) {

                // Reset the last dropzone
                self.lastDropzone = null;

                blocks.out(dropzone);

                // Remove is-sending class on helper
                ui.helper.removeClass("is-sending");
            },

            isDropping: false,
            lastDropzone: null,

            dropBlock: function(dropzone, item) {

                // Block
                var block = item;

                // Block Menu
                var isBlockMenu = item.is(blocks.menu);
                if (isBlockMenu) {
                    block = blocks.createBlockFromMenu(item);
                }

                // Media File
                var isMediaFile = item.is(self.mediaFile);
                if (isMediaFile) {
                    block = blocks.createBlockFromMediaFile(item);
                }

                var isMovingBlock = composer.manager().hasClass('is-moving-block');
                var isDroppingBlock = composer.manager().hasClass('is-dropping-block');

                if (isMovingBlock || isDroppingBlock) {
                    var nest = blocks.getBlockNest(dropzone);
                    var position = dropzone.data('position');

                    nest.data('snappedPosition', position);
                }

                // Replace dropzone with block
                dropzone.replaceWith(block);

                // Block menu needs to be dropped/released/outted here.
                // This is because block menu's dragstop listener can not
                // receive the newly created block from the block menu.
                if (isBlockMenu || isMediaFile || isMovingBlock || isDroppingBlock) {

                    blocks.drop(block);
                    blocks.release(block);
                    blocks.out(block);
                }
            },

            dropStop: function() {

                // Destroy all dropzones.
                setTimeout(function() {
                    self.destroyDropzones();
                }, 1);

                composer.manager()
                    .removeClass("is-resizing");
            },

            // When block/blockMenu is dropped on a dropzone
            "{dropzone} drop": function(dropzone, event, ui) {

                // If it's being dropped, skip this
                if (self.isDropping) {
                    return;
                }

                self.isDropping = true;

                // Get item being dropped
                var item = ui.draggable;

                // Drop item
                self.dropBlock(dropzone, item);

                // Resets the state so that dragstop wouldn't get triggered
                setTimeout(function() {
                    self.isDropping = false;
                }, 0);
            },

            // After an existing block is dropped on a dropzone,
            // or did not drop on a dropzone so it is returning
            // to its original position.
            "{block} dragstop": function(block, event, ui) {

                // Display the block once it is dropped on a dropzone
                block.removeClass("hide");

                // Let block host drop, release and out this block.
                blocks.drop(block);
                blocks.release(block);
                blocks.out(block);

                self.dropStop();
            },

            // After a block menu is dropped on a dropzone,
            // or did not drop on a dropzone so no new block
            // is created.
            "{blocks.menu} dragstop": function(block, event, ui) {

                // If it's being dropped, skip this
                if (self.isDropping || !self.lastDropzone) {
                    self.dropStop();
                    return;
                }

                var item = ui.helper;

                // Drop on to the last active dropzone.
                self.dropBlock(self.lastDropzone, item);

                self.dropStop();
            },

            // After a media file is dropped on a dropzone,
            // or did not drop on a dropzone so no new block
            // is created.
            "{mediaFile} dragstop": function(mediaFile, event, ui) {

                // If it's being dropped, skip this
                if (self.isDropping || !self.lastDropzone) {
                    self.dropStop();
                    return;
                }

                var item = ui.helper;

                self.dropBlock(self.lastDropzone, item);

                self.dropStop();
            },

            "{self} composerDocumentScroll": function(base, event) {

                var draggable = currentBlock.data("ui-draggable");

                $.ui.ddmanager.prepareOffsets(draggable, event);
            },

            "{dropzone} dropdeactivate": function(dropzone, event, ui) {

                blocks.out(dropzone);
            }

        }});

        module.resolve();

    });
});
