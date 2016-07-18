EasyBlog.module("composer/blocks/tree", function($){

var module = this;

EasyBlog.require()
.done(function(){

    EasyBlog.Controller("Composer.Blocks.Tree", {
        elements: [
            "[data-eb-{block|block-icon|block-title|block-level-count|block-child-count}]",
            "[data-eb-blocks-{tree-field|tree|tree-item|tree-item-template|tree-item-icon|tree-item-title|tree-item-group|tree-toggle-button}]"
        ]
    },
    function(self, opts, base, composer, blocks, createTreeItem) { return {

        init: function() {

            blocks = self.blocks;
            composer = blocks.composer;

            // For compression
            createTreeItem = self.createTreeItem;

            self.treeItemTemplate = self.treeItemTemplate().detach().html();
        },

        setCurrentBlock: function(block) {

            var type = blocks.getBlockType(block);
            var meta = blocks.getBlockMeta(type);

            // Title & Icon
            self.blockTitle()
                .text(meta.title);

            self.blockIcon()
                .attr("class", meta.icon);

            // Stat
            var parentBlocks = blocks.getAllParentBlocks(block);
            var childBlocks = blocks.getChildBlocks(block);
            var childBlocksCount = childBlocks.length;
            var blockLevel = parentBlocks.length + 1;

            self.blockChildCount()
                .text(childBlocksCount);

            self.blockLevelCount()
                .text(blockLevel);

            self.block()
                .toggleClass("has-child", childBlocksCount);
        },

        populate: function(block) {

            // If block not given, use current block.
            if (!block) {
                var block = blocks.getCurrentBlock();
            }

            if (!block.length) {
                return;
            }

            // Get tree field to determine what type of
            // tree that we should populate.
            var treeField = self.treeField();

            // Get parent block
            var parentBlock = blocks.getParentBlock(block);
            var hasParent = !!parentBlock.length;

            // Get child blocks
            var childBlocks = blocks.getChildBlocks(block);
            var hasChildren = !!childBlocks.length;

            // Toggle has-parent/has-children class
            treeField
                .toggleClass("has-parent", hasParent)
                .toggleClass("has-children", hasChildren);

            // Build tree items
            var treeItems = [];

            // Get tree display mode
            var treeDisplayMode = self.getTreeDisplayMode();

            // Miminal tree
            if (treeDisplayMode=="minimal") {

                // Parent block
                if (hasParent) {

                    treeItems.push(createTreeItem(parentBlock, 1));

                    // Generate sibling blocks
                    var siblingBlocks = blocks.getChildBlocks(parentBlock);

                    siblingBlocks.each(function(){

                        var siblingBlock = $(this);
                        treeItems.push(createTreeItem(siblingBlock, 2));

                        if (siblingBlock.is(block)) {

                            childBlocks.each(function(){
                                var childBlock = $(this);
                                treeItems.push(createTreeItem(childBlock, 3));
                            });
                        }
                    });

                // If there is no parent, generate current block and its child blocks
                } else {

                    // Current block
                    treeItems.push(createTreeItem(block, 1));

                    // Child blocks
                    childBlocks.each(function(){
                        var childBlock = $(this);
                        treeItems.push(createTreeItem(childBlock, 2));
                    });
                }

            // Full tree
            } else {

                var rootBlocks = blocks.getRootBlocks();

                var addTreeItem = function(block, level) {

                    var currentLevel = level;

                    // Create tree item
                    treeItems.push(createTreeItem(block, currentLevel));

                    // Get child blocks
                    var childBlocks = blocks.getChildBlocks(block);

                    // If there are child blocks, add tree item of this block.
                    childBlocks.each(function(){
                        var childBlock = $(this);
                        addTreeItem(childBlock, currentLevel + 1);
                    });
                }

                rootBlocks.each(function(){
                    addTreeItem($(this), 1);
                });
            }

            // Do not show tree item group when there's only a single item
            // on minmal tree display mode.
            self.tree()
                .toggle(treeDisplayMode=="full" || treeItems.length > 1)

            self.treeItemGroup()
                .empty()
                .append(treeItems);
        },

        createTreeItem: function(block, level) {

            var treeItem = block.data("treeItem");

            if (!treeItem) {
                treeItem = self.renderTreeItem(block);
                block.data("treeItem", treeItem);
            }

            treeItem
                .toggleClass("active", block.hasClass("active"))
                .toggleClass("is-nested", block.hasClass("is-nested"))
                .switchClass("level-" + level);

            return treeItem;
        },

        renderTreeItem: function(block) {

            var uid = blocks.getBlockUid(block);
            var type = blocks.getBlockType(block);
            var meta = blocks.getBlockMeta(type);
            var treeItem = $(self.treeItemTemplate);

            treeItem
                .attr({
                    "data-type": meta.type,
                    "data-uid": uid
                })
                .find(self.treeItemIcon)
                    .addClass(meta.icon)
                    .end()
                .find(self.treeItemTitle)
                    .text(meta.title);

            return treeItem;
        },

        getTreeDisplayMode: function() {

            return self.treeField().hasClass("tree-minimal") ? "minimal" : "full";
        },

        setTreeDisplayMode: function(treeDisplayMode) {

            self.treeField()
                .switchClass("tree-" + treeDisplayMode);

            self.populate();
        },

        "{self} composerBlockAdd": function(base, event, block, handler) {

            self.populate();
        },

        "{self} composerBlockActivate": function(base, event, block, handler) {

            self.setCurrentBlock(block);
            self.populate(block);
        },

        "{self} composerBlockRemove": function(base, event, block) {

            self.populate();
        },

        "{treeItem} click": function(treeItem) {

            var uid = treeItem.data("uid");
            var block = blocks.getBlock(uid);

            // Unhighlight block
            blocks.unhighlight(block);

            // Activate block
            blocks.activateBlock(block);
        },

        "{treeItem} mouseenter": function(treeItem) {

            var uid = treeItem.data("uid");
            var block = blocks.getBlock(uid);

            // Highlight block
            blocks.highlight(block);

            // Scroll to block
            blocks.scrollTo(block);
        },

        "{treeItem} mouseleave": function(treeItem) {

            var uid = treeItem.data("uid");
            var block = blocks.getBlock(uid);

            // Unhighlight block
            blocks.unhighlight(block);
        },

        "{treeToggleButton} click": function() {

            var treeField = self.treeField();
            var treeDisplayMode = self.getTreeDisplayMode();

            self.setTreeDisplayMode(treeDisplayMode=="minimal" ? "full" : "minimal");
        }

    }});

    module.resolve();

});

});
