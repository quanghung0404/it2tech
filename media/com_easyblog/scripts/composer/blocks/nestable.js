EasyBlog.module("composer/blocks/nestable", function($){

var module = this,
    isNested = "is-nested",
    isSortingNest = "is-sorting-nest";

EasyBlog.require()
    .library(
        "ui/sortable"
    )
    .done(function(){

EasyBlog.Controller("Composer.Blocks.Nestable",
{
    defaultOptions: $.extend({}, EBD.selectors)
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = self.blocks.composer;
        currentNest = $();
    },

    enable: function() {

        self.nest()
            .each(function(){
                var nest = $(this);
                self.initNestable(nest);
            });
    },

    disable: function() {

        self.nest()
            .each(function(){
                var nest = $(this);
                self.destroyNestable(nest);
            });
    },

    initNestable: function(nest) {

        // If this nest has no sortable implemented yet.
        if (!nest.hasClass("ui-sortable")) {

            // Initialize sortable on nest
            nest.sortable({

                    // Items
                    items: EBD.childBlock,
                    connectWith: EBD.root + ", " + EBD.nest,

                    // Helper
                    helper: "clone",
                    appendTo: composer.document.ghosts(),

                    // Placeholder
                    placeholder: "ebd-block is-placeholder is-nested",

                    // Behaviour
                    tolerance: "pointer",
                    refreshPositions: true,

                    // Handler
                    handle: EBD.immediateBlockSortHandle
                });
        }
    },

    destroyNestable: function(nest) {

        if (!nest.hasClass("ui-sortable")) return;

        nest.sortable("destroy");
    },

    "{self} composerBlockMechanicsChange": function(base, event, mechanics) {

        mechanics=="sortable" ? self.enable() : self.disable();
    },

    isBlockNest: function(nest) {
        return nest.data("type")=="block";
    },

    isContentNest: function(nest) {
        return nest.data("type")=="content";
    },

    "{self} composerBlockCreate": function(el, event, block, meta) {
    },

    "{self} composerBlockInit": function(el, event, block, handler) {

        // Skip if this is not a nestable block,
        // or current block mechanics isn't set to sortable.
        if (!block.is(EBD.nestableBlock) || !blocks.getMechanics()=="sortable") return;

        // Find nests within block
        block.find(EBD.nest)
            .each(function(){

                var nest = $(this);

                // Initialize nest
                self.initNestable(nest);
            });
    },

    disableEditableNest: function(nest) {

        // If nest is editable,
        if (nest.editable()) {

            // Disable editable functionality
            nest.editable(false);

            // Remember that it is edtiable
            nest.data("editable-nest", true);
        }
    },

    enableEditableNest: function(nest) {

        // If nest is editable
        if (nest.data("editable-nest")) {

            // Restore editable functionality
            nest.editable(true);

            // Remove editable flag
            nest.removeData("editable-nest");
        }
    },

    // TODO: What's the touch event fallback for this?
    "{block} mouseover": function(block, event) {

        if (event.nestHandled) return;

        // If this is a nested block
        if (block.is(EBD.nestedBlock)) {

            // Get parent nest
            var nest = self.nest.of(EBD.nestedBlock);

            // Disable editability on nest
            self.disableEditableNest(nest);

        // If this is a block
        } else {

            // Get child nest if any
            var nest = self.nest.inside(block);

            // Stop if there are no nests.
            if (nest.length < 1) return;

            // Restore editability on nest
            self.enableEditableNest(nest);
        }

        // Flag this because we want the event to propagate
        // but we don't want to process this event on the parent block.
        event.nestHandled = true;
    },

    // TODO: What's the touch event fallback for this?
    "{block} mouseout": function(block, event) {

        // If this is a nested block
        if (block.is(EBD.nestedBlock)) {

            // Get parent nest
            var nest = self.nest.of(EBD.nestedBlock);

            // Restore editability on parent nest
            self.enableEditableNest(nest);
        }
    },

    // This methods decide whether a placeholder should
    // be snapped to the left or right of the nest.
    snap: function(x, y, nest, placeholder) {

        // If this is a block nest, just add is-nested class on placeholder.
        if (self.isBlockNest(nest)) {
            placeholder.addClass(isNested);
            return;
        }

        // Determine placeholder position
        var offset   = nest.offset(),
            width    = nest.width(),
            center   = offset.left + (width / 2),
            position = self.position(nest, x < center ? "left" : "right");

        placeholder
            // Set placeholder as nested
            .addClass(isNested)
            // Set nest placement
            .switchClass("nest-" + position);

        // Remember the last snapped position
        nest.data("snappedPosition", position);
    },

    unsnap: function(placeholder) {

        // If placeholder no longer nested,
        // remove nested properties from placeholder.
        placeholder
            .removeClass(isNested)
            .removeClass(function(index, css) {
                return (css.match(/(^|\s)nest-\S+/g) || []).join(' ');
            });
    },

    // Return supported positions
    positions: function(nest) {

        // If no positions provided, default to left & right.
        return (nest.data("positions") || "left,right").split(",");
    },

    availablePositions: function(nest) {

        var positions = self.positions(nest);

        nest.find(EBD.childBlock + ":not(.is-sort-item)")
            .each(function(){

                var position = self.extractPosition($(this).attr("class"));

                // Remove this position from available positions
                position && $.pull(positions, position);
            });

        return positions;
    },

    extractPosition: function(str) {

        // Also accept block as parameter
        if (str instanceof $) {
            str = str.attr("class");
        }

        // Note: This regex will mismatch classnames that has "nest-" in it like "birdnest-1".
        return ((str.match(/nest-\w+/g) || [])[0] || "").split("nest-")[1];
    },

    // If no position is given, return the most preferred available position.
    // If a position is given, determine if the position is available and return it.
    // If the position is unavailable, return the next preferred position in line.
    position: function(nest, position) {

        var positions = self.availablePositions(nest);

        return $.indexOf(positions, position) > -1 ? position : positions[0];
    },

    setTargetNest: function(nest) {

        self.clearTargetNest();
        blocks.over(nest);
    },

    unsetTargetNest: function(nest) {

        blocks.out(nest);
    },

    clearTargetNest: function() {

        // Get all nest and remove is-receiving flag
        self.nest()
            .each(function(){
                var nest = $(this);
                blocks.out(nest);
            });
    },

    trackNest: function(nest, placeholder) {

        // Bind to the mousemove event
        $(document)
            .off("mousemove.nestable")
            .on("mousemove.nestable", function(event) {

                // And when user glides along, decide the placement
                // of the placeholder based on the cursor position.
                self.snap(event.pageX, event.pageY, nest, placeholder);
            });
    },

    untrackNest: function() {

        $(document)
            .off("mousemove.nestable");
    },

    "{self} composerBlockBeforeDrop": function(base, event, block) {

        // Get nest
        var nest = block.closest(EBD.nest);

        // Get blocks' nest position
        var currentPosition = self.extractPosition(block);

        // If this block is inside a nest, add is-nested class.
        block.toggleClass(isNested, nest.length > 0);

        // If this is a content nest, determine position
        // and add nest position class.
        if (self.isContentNest(nest)) {

            // This will get the position intended by the user and
            // then pass it over to self.position to return the final
            // location after determining its availability.
            var position = self.position(nest, nest.data("snappedPosition"));

            // Add nest position class.
            block.switchClass("nest-" + position);

            // If this is a new block
            if (block.hasClass("is-new")) {

                // Set initial fluid width
                blocks.dimensions.toFluidWidth(block);
            }

            // If we're switching position, trigger composerBlockNestChange
            if (currentPosition && position!==currentPosition) {
                self.trigger("composerBlockNestChange", [block, position, currentPosition]);

            // If we're nesting this block, trigger composerBlockNestIn
            } else {
                self.trigger("composerBlockNestIn", [block, position]);
            }

        // If this block no longer belong in a content nest
        } else if (currentPosition) {

            self.unsnap(block);
            self.trigger("composerBlockNestOut", [block]);
        }
    },

    replaceWithCommentPlaceholder: function(blockManifest, property) {

        // Create a block fragment from the block's editable html code
        var blockFragment = $('<div>').html(blockManifest[property]);
        var blockList = blockFragment.find(EBD.immediateNestedBlock);

        // If there are nested blocks
        if (blockList.length <= 0) {
            return;
        }

        blockList.each(function() {
            var nestedBlockElement = $(this);
            var nestedBlockUid = blocks.getBlockUid(nestedBlockElement);

            // Create a placeholder
            var placeholder = document.createComment('block' + nestedBlockUid);

            // Replace the nested block with block placeholder within the block fragment
            nestedBlockElement.replaceWith(placeholder);

            var nestedBlock = blocks.getBlock(nestedBlockUid);
            var nestedBlockManifest = blocks.exportBlock(nestedBlock);
            var position = self.extractPosition(nestedBlock.attr('class'));

            if (position) {
                nestedBlockManifest.position = position;
            }

            blockManifest.blocks.push(nestedBlockManifest);
        });

        // Convert the block fragment into html after replacing nested blocks with placeholders
        var html = blockFragment.html();

        // Update parent block html to contain html with converted block placeholders
        blockManifest[property] = html;
    },

    "{self} composerBlockExport": function(base, event, block, blockManifest) {

        // Add blocks property to the block manifest which holds
        // an array of nested block manifests.
        blockManifest.blocks = [];

        // Replace block's editable html codes
        self.replaceWithCommentPlaceholder(blockManifest, 'editableHtml');

        // Replace html codes
        self.replaceWithCommentPlaceholder(blockManifest, 'html');
    }

}});

module.resolve();

});

});
