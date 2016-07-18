EasyBlog.module("composer/blocks/resizable", function($){

var module = this;

EasyBlog.require()
    .library(
        "ui/resizable"
    )
    .done(function(){

EasyBlog.Controller("Composer.Blocks.Resizable",
{
    defaultOptions: $.extend({
        "{resizeHandle}": ".ui-resizable-handle"
    }, EBD.selectors)
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = self.blocks.composer;
    },

    initResizable: function(block) {

        // Skip if resizable has been initialized
        if (self.hasResizable(block)) return;

        // Determine resize handles
        var handles = self.determineResizeHandles(block);

        // If there are no resize handles, stop.
        if (!handles) return;

        // Create resize handle elements and selectors
        var resizeHandleElements = self.createResizeHandleElements(handles),
            resizeHandleSelectors = self.createResizeHandleSelectors(handles);

        // Add resize handles to viewport
        var viewport =
            blocks.getBlockViewport(block)
                .append(resizeHandleElements)
                // Implement resizable
                .resizable({
                    handles: resizeHandleSelectors
                });

        // They are hidden by default until block is activated
        $(resizeHandleElements).hide();

        // Add is-resizable class on blocz
        block.addClass("is-resizable");
    },

    destroyResizable: function(block) {

        // Destroy resizable
        blocks.getBlockViewport(block)
            .resizable("destroy");

        // Remove is-resizable class
        block.removeClass("is-resizable");
    },

    hasResizable: function(block) {

        return blocks.getBlockViewport(block).hasClass("ui-resizable");
    },

    handles: {

        right: {
            w: "dimension",
            sw: "dimension",
            s: "dimension"
        },

        left: {
            e: "dimension",
            se: "dimension",
            s: "dimension"
        }
    },

    determineResizeHandles: function(block) {

        var nestPosition = blocks.nestable.extractPosition(block);

        return self.handles[nestPosition];
    },

    createResizeHandleElements: function(handles) {

        var elements = [];

        $.each(handles, function(direction, role){

            var element =
                $('<div class="ui-resizable-handle"><div></div></div>')
                    .addClass("ui-resizable-" + direction)
                    .attr({
                        "data-direction": direction,
                        "data-role": role
                    });

            elements.push(element[0]);
        });

        return elements;
    },

    createResizeHandleSelectors: function(handles) {

        var selectors = [];

        $.each(handles, function(direction, role){
            selectors[direction] = "> .ui-resizable-" + direction;
        });

        return selectors;
    },

    getResizeHandles: function(block) {

        return blocks.getBlockViewport(block)
            .children(self.resizeHandle.selector);
    },

    "{self} composerBlockActivate": function(base, event, block) {

        // Initialize resizable
        self.initResizable(block);

        // Show resize handles
        self.getResizeHandles(block).show();
    },

    "{self} composerBlockDeactivate": function(base, event, block) {

        // Hide resize handles
        self.getResizeHandles(block).hide();
    },

    "{self} composerBlockNestIn": function(base, event, block) {

        self.initResizable(block);
    },

    "{self} composerBlockNestOut": function(base, event, block) {

        self.destroyResizable(block);
    },

    "{self} composerBlockNestChange": function(base, event, block) {

        self.destroyResizable(block);

        self.initResizable(block);
    },

    "{blocks.viewport} resizestart": function(viewport, event, ui) {

        // Add is-sizing class to workarea.
        // This will disable block animation.
        self.workarea()
            .addClass("is-resizing");

        // Get block
        var block = self.block.of(viewport);
            parentBlocks = blocks.getAllParentBlocks(block);

        // Add has-resizing-child class to parent block.
        // This will disable block guide from showing on parent block.
        parentBlocks.addClass("has-resizing-child");

        self.trigger("composerBlockResizeStart", [block, ui]);
    },

    "{blocks.viewport} resize": function(viewport, event, ui) {

        // This prevents resizable from resizing the block
        viewport.css({top: "", left: "", width: "", height: ""});
        event.stopPropagation();

        // Get block
        var block = self.block.of(viewport);

        // Currently only for nested block of content nest
        if (!blocks.isNestedBlock(block) || blocks.getBlockNestType(block)!=="content") return;

        var beforeResizeEvent = self.trigger("composerBlockBeforeResize", [block, ui]);

        // If resize event is not prevented, continue with default resizing strategy.
        if (!beforeResizeEvent.isDefaultPrevented()) {

            // Get nest, original size and current size
            var originalSize = ui.originalSize,
                size = ui.size;

            // If width has changed
            if (originalSize.width !== size.width) {

                // Get nest
                var nest = blocks.getBlockNest(block);

                // Get width
                var width = size.width / nest.width();

                // Cap to 0 to 1 (0% to 100%)
                if (width < 0) width = 0; if (width > 1) width = 1;

                // Convert width to percentage
                width = Math.floor(width * 100) + "%";

                // Set width
                block.css("width", width);
            }

            // If height has changed
            if (originalSize.height !== size.height) {

                // Get natural height
                block.css("height", "");
                var naturalHeight = block.height(),

                    // Get new height
                    height = size.height;

                // If new height is shorter than natural height,
                // remove height override.
                if (height < naturalHeight) height = "";

                // Set height
                block.css("height", height);
            }
        }

        // Trigger composerBlockResize
        self.trigger("composerBlockResize", [block]);
    },

    "{blocks.viewport} resizestop": function(viewport, event, ui) {

        // Remove is-resizing class from workarea.
        // This will enable block animation.
        self.workarea()
            .removeClassAfter("is-resizing");

        // Get block
        var block = self.block.of(viewport);
            parentBlocks = blocks.getAllParentBlocks(block);

        // Add has-resizing-child class to parent block.
        // This will disable block guide from showing on parent block.
        parentBlocks.removeClass("has-resizing-child");

        self.trigger("composerBlockResizeStop", [block, ui]);
    }

}});

    });

module.resolve();

});
