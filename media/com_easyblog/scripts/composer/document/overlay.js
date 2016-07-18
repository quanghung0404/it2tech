EasyBlog.module("composer/document/overlay", function($){

var module = this;

EasyBlog.Controller("Composer.Document.Overlay",
{
    defaultOptions: $.extend({

        "{overlay}": "[data-ebd-overlay]",
        "{placeholder}": "[data-ebd-overlay-placeholder]",

        // Applies to document root and nest
        "{sortable}": ".ui-sortable",

        "{placeholderInsideSortHelper}": ".ebd-block.is-helper .ebd-overlay-placeholder"
    }, EBD.selectors)
},
function(self, opts, base, blocks) { return {

    id: 1,

    init: function() {

        composer = self.document.composer;
        blocks = composer.blocks;

        // Document Overlay class
        self.DocumentOverlay =  function(block) {

            this.id      = self.id++;
            this.uid     = block.data("uid");
            this.type    = block.data("type");

            this._element = $("<div>", {
                "class"    : "ebd-overlay",
                "data-id"  : this.id,
                "data-type": this.type,
                "data-ebd-overlay": ""
            })[0];

            this._placeholder = $("<div>", {
                "class"  : "ebd-overlay-placeholder",
                "data-id": this.id,
                "data-ebd-overlay-placeholder": ""
            })[0];
        }

        $.extend(self.DocumentOverlay.prototype, {

            block: function() {
                return blocks.getBlock(this.uid);
            },

            element: function() {
                return $(this._element);
            },

            placeholder: function() {
                return $(this._placeholder);
            },

            attach: function() {

                this.element()
                    .appendTo(self.document.workarea());

                // Position overlay
                this.reposition()
            },

            refresh: function() {

                // Do not refresh blocks that are being released
                if (this.element().hasClass("is-animating")) return;

                this.reposition();
            },

            reposition: function() {

                var element = this.element(),

                    placeholder =
                        // If block is currently being sorted,
                        // get placeholder inside sort helper.
                        element.hasClass("is-sorting") ?
                            // Sometimes the placholder from sort helper doesn't exist
                            // so we fallback to the overlay's original placeholder.
                            $(self.placeholderInsideSortHelper().where("id", this.id)[0] || this._placeholder) :
                            this.placeholder();

                // Update overlay size & position
                element
                    .css({
                        width:  placeholder.width(),
                        height: placeholder.height()
                    })
                    .position({
                        my: "left top",
                        at: "left top",
                        of: placeholder
                    });
            },

            emerge: function() {

                // Bring overlay in front of the document root
                this.element()
                    .addClass("hover");

                // Simulate hover behaviour on block
                this.block()
                    .addClass("hover");
            },

            submerge: function() {

                // Push overlay behind the document root
                this.element()
                    .removeClass("hover");

                // Remove simulated hover behaviour on block
                this.block()
                    .removeClass("hover");
            },

            remove: function() {

                self.remove(this.id);
            }
        });

        var refreshTwice = $.throttle(function(){
                self.refresh();
            }, 25, {leading: true, trailing: true})

        // Refresh overlay when user provides feedback
        var userEvents = $.ns("keydown keypress keyup input mousedown click mousemove mouseup touchstart touchmove touchend", ".overlay"),
            sortEvents = $.ns("sortactivate sortchange sortover sortout sortdeactivate sortstop", ".overlay");

        composer.document.root()
            .on(userEvents, refreshTwice);

        composer.views()
            .on("scrolly.overlay", refreshTwice);

        self.element
            .on(sortEvents, self.sortable.selector, refreshTwice);
    },

    blocks: {},

    instances: [],

    keys: {},

    get: function(id) {

        return self.keys[id];
    },

    getInstancesByBlock: function(block) {

        var uid = blocks.getBlockUid(block);

        return self.blocks[uid] || [];
    },

    create: function(block) {

        var instance = new self.DocumentOverlay(block);

        // Add to instances
        self.instances.push(instance);

        // Add to keys
        self.keys[instance.id] = instance;

        // Add to block-overlay map
        var blocks = self.blocks;
        (blocks[instance.uid] || (blocks[instance.uid] = [])).push(instance);

        return instance;
    },

    remove: function(id) {

        var instance = self.get(id);

        // Remove element & placeholder
        instance.element().remove();
        instance.placeholder().remove();

        // Remove from block-overlay map
        $.pull(self.blocks[id], instance);

        // Remove from keys
        delete self.keys[id];

        // Remove from instances
        $.pull(self.instances, instance);
    },

    of: function(block) {

        return self.blocks[block.data("uid")] || [];
    },

    refresh: function(block) {

        // Refresh instance
        var instances = block ? self.of(block) : self.instances;

        $.each(instances, function(i, instance){
            instance.refresh();
        });
    },

    // Add .is-sorting class to instance overlay
    // This will shrink the appearance of the overlay
    // just like a regular block item would when it
    // appears as a placeholder.
    "{self} composerBlockDrag": function(base, event, block) {

        // // If this is not a block, stop.
        // if (!block.is(EBD.block)) {
        //     return;
        // }
        setTimeout(function() {
            self.refresh();
        },1);

        // This is alternatively composerBlockDrag
        $.each(self.of(block), function(i, instance){
            instance.element()
                .addClass("is-sorting");
        });
    },

    "{dropzone} dropover": function(dropzone, event, ui) {
        setTimeout(function() {
            self.refresh();
        }, 1);
    },

    "{block} drag": function(block, event, ui) {

        // This is alternatively composerBlockDragging
        // Refresh overlay for this block
        self.refresh(block);
    },

    drop: function(block) {

        $.each(self.of(block), function(i, instance){
            instance.element()
                .removeClass("is-sorting")
                .addClass("is-dropping");
        });

        self.refresh();
    },

    // When a block is dropped, it is not yet visible on the screen.
    // This is the time to make block overlay (if any) invisible
    // and reposition it to the actual placeholder.
    "{self} composerBlockDrop": function(base, event, block) {
        self.drop(block);
    },

    // If this is a new block, reposition overlay after block
    // has been reconstructed, because overlay may not exist
    // during composerBlockDrop.
    "{self} composerBlockAdd": function(base, event, block) {
        self.drop(block);
    },

    // Once we have repositioned the overlay on top of the block,
    // we shrink the overlay just like how blocks are shrinked
    // before it is being released.
    "{self} composerBlockBeforeRelease": function(base, event, block) {

        $.each(self.of(block), function(i, instance){

            instance.element()
                .removeClass("is-dropping")
                .addClass("is-releasing is-animating")
                .removeClassAfter("is-releasing");
        });

        self.refresh();

        // Because we can't quite tell when reflow happens after block
        // is dropped, this is required to update position of overlays.
        setTimeout(function(){
            self.refresh();
        }, 50);
    },

    // Once a block is released, disable transition on overlay.
    "{self} composerBlockRelease": function(base, event, block) {

        $.each(self.of(block), function(i, instance){
            instance.element()
                .removeClass("is-animating");
        });

        self.refresh();
    },

    "{self} composerDocumentRefresh": function() {

        // Repositon all overlay
        self.refresh();
    },

    "{self} composerBlockRemove": function(base, event, block) {

        var instances = self.getInstancesByBlock(block);

        $.each(instances, function(i, instance){
            instance.remove();
        });
    },

    "{placeholder} click": function(placeholder) {

        var id = placeholder.data("id"),
            instance = self.get(id);

        instance.emerge();
    },

    "{placeholder} mouseover": function(placeholder) {

        var id = placeholder.data("id"),
            instance = self.get(id);

        // If this block is currently active, emerge overlay.
        if (instance.block().hasClass("active")) {
            instance.emerge();
        }
    },

    "{overlay} mouseout": function(overlay) {

        var id = overlay.data("id"),
            instance = self.get(id);

        instance.submerge();
    }

}});

module.resolve();

});
