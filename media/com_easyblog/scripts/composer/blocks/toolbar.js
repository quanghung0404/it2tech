EasyBlog.module("composer/blocks/toolbar", function($){

var module = this;

var isEditingBlock = "is-editing-block";
var isMovingBlock = "is-moving-block";

EasyBlog.Controller("Composer.Blocks.Toolbar",
{

    elements: [
        "[data-eb-blocks-{close-button|done-button|parent-button|cancel-drop-button|move-button|cancel-move-button|remove-button}]",
        "[data-ebd-block-{toolbar|sort-handle}]"
    ],

    defaultOptions: $.extend({}, EBD.selectors)
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {
        blocks = self.blocks;
        composer = self.blocks.composer;
    },

    "{self} composerBlockActivate": function(base, event, block) {

        // Not applicable for standalone blocks
        if (block.hasClass("is-standalone")) {
            return;
        }

        // For isolated blocks, do not show move button.
        self.moveButton().toggle(!block.hasClass("is-isolated"));

        composer.manager()
            .addClass(isEditingBlock);
    },

    "{self} composerBlockDeactivate": function() {

        composer.manager()
            .removeClass(isEditingBlock);
    },

    "{moveButton} click": function(moveButton) {
        // Populate dropzones
        composer.blocks.droppable.populateDropzones();

        // Add is-moving class
        composer.manager()
            .addClass(isMovingBlock);

        // Get the active block
        var currentBlock = blocks.getCurrentBlock();

        currentBlock
            .addClass('hide')
            .addClass('is-sort-item');

        self.trigger("composerBlockMove", [currentBlock]);
    },

    "{cancelMoveButton} click": function(cancelMoveButton) {

        // Remove the moving state
        composer.manager()  
            .removeClass(isMovingBlock);

        // Hide the dropzones
        composer
            .blocks
            .droppable
            .dropStop();

        // Show the current block
        var currentBlock = blocks.getCurrentBlock();
        currentBlock
            .removeClass('hide')
            .removeClass('is-sort-item');
    },

    "{doneButton} click": function(doneButton) {

        // TODO: How about revert to the previously activated block?
        blocks.deactivateBlock();
    },

    "{parentButton} click": function(parentButton) {

        var currentBlock = blocks.getCurrentBlock();

        if (currentBlock.length) {

            var parentBlock = blocks.getParentBlock(currentBlock);

            if (parentBlock.length) {
                blocks.activateBlock(parentBlock);
            } else {
                blocks.deactivateBlock();
            }
        }
    },

    "{cancelDropButton} click": function(cancelButton) {

        // Reset the selection
        composer.blocks.droppable.selectedMenu = null;
        composer.blocks.droppable.selectedBlock = null;

        // Destroy the dropzones
        composer.blocks.droppable.dropStop();

        // Remove the class on the manager
        composer.manager().removeClass("is-dropping-block");
    },

    "{closeButton} click": function(closeButton) {

        composer.views.hide("blocks");
    },

    "{toolbar} mouseenter": function(toolbar) {

        var block = blocks.block.of(toolbar);
        block.addClass("show-block-hint");
    },

    "{toolbar} mouseleave": function(toolbar, event) {

        var block = blocks.block.of(toolbar);
        block.removeClass("show-block-hint");
    },

    "{removeButton} click": function(removeButton, event) {
        var currentBlock = blocks.getCurrentBlock();

        // TODO: Display confirmation?

        // Remove the block from the composer
        blocks.removeBlock(currentBlock);

        // Remove the is-editing-block state
        composer.manager().removeClass('is-editing-block');
    }

}});

module.resolve();

});
