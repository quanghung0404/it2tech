EasyBlog.module("composer/blocks/panel", function($){

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Panel", {
        elements: [
            // Global blocks
            "[data-eb-composer-blocks-{props}]",
            "[data-eb-composer-blocks-{prop-group|prop-action}]",
            "[data-eb-composer-{panel}][data-id=blocks]",

            // Subpanels,
            "[data-eb-composer-blocks-{block-subpanel|removal-subpanel}]",

            // Individual blocks
            "[data-eb-composer-block-{fieldgroup}]",

            // Menu
            "[data-eb-composer-blocks-props-{block-menu|text-menu|block-content|text-content}]",
            "[data-eb-composer-blocks-{subpanel|subpanel-button}]"
        ],

        defaultOptions: {
            "{blockTitle}": "[data-block-property-title]",
            "{blockIcon}": "[data-block-property-icon]"
        }
    }, function(self, opts, base, composer, blocks, currentBlock) {

        return {

            init: function() {

                blocks = self.blocks;
                composer = blocks.composer;
                currentBlock = $();
            },

            "{self} composerBlockBeforeActivate": function(base, event, block) {

                // Activate blocks panel
                composer.panels.activate("blocks");

                // Set as current block
                currentBlock = block;

                // Show block props
                self.panel().switchClass("show-block-subpanel");

                // Activate fieldgroup on blocks panel
                self.fieldgroup.display(block);
            },

            fieldgroup: {

                get: $.memoize(function(blockType) {

                    var fieldgroup = composer.find(self.fieldgroup).where("type", blockType);

                    // If this fieldgroup hasn't been created, create it
                    if (fieldgroup.length < 1) {

                        // Get meta
                        var meta = blocks.getBlockMeta(blockType),

                            // Get fieldgroup html from meta
                            // and append it to blocks panel
                            fieldgroup = $(meta.fieldgroup);
                    }

                    return fieldgroup;

                }),

                hide: function() {

                    // Hides all fieldset, shows empty hint
                    self.panel()
                        .addClass("is-empty")
                        .find(self.fieldgroup)
                        .detach();
                },

                display: function(block) {

                    // Get type, panel
                    var type = block.attr("data-type");
                    var panel = self.panel();

                    // Detach existing fieldgroups
                    panel
                        .removeClass("is-empty")
                        .find(self.fieldgroup)
                        .detach();

                    // Get prop group
                    var propGroup =
                        panel
                            .find(self.propGroup)
                            .where("type", "specific");

                    // Append fieldgroup to tab content
                    self.fieldgroup
                        .get(type)
                        .toggleClass("is-standalone", blocks.isStandaloneBlock(block))
                        .appendTo(propGroup);
                }
            },

            openPanel: function(name) {

                if (name=="block" || name=="text") {

                    self.panel().switchClass("show-" + name + "-subpanel");

                    self.subpanelButton()
                        .where("name", name)
                        .activateClass("active");
                }

                if (name=="removal") {
                    self.panel().switchClass("show-removal");
                }
            },

            activatePanel: function(block) {

                clearTimeout(self.revertToPostPanel);

                var meta = blocks.getBlockMeta(block);
                var parents = blocks.getAllParentBlocks(block);
                var children = blocks.getChildBlocks(block);
                var items = [];

                // Always display the subpanel button
                self.subpanelButton().show();

                // Get the block's meta and see if we should display the text panel
                if (!meta.properties.textpanel) {
                    self.subpanelButton().hide();
                }

                // Update the block panel property
                self.blockIcon().attr('class', meta.icon);
                self.blockTitle().html(meta.title);
            },

            deactivatePanel: function() {

                self.revertToPostPanel = setTimeout(function(){

                    if (!composer.document.workarea().hasClass("is-sorting")) {
                        // Activate post options panel
                        composer.panels.activate("post-options");
                    }
                }, 1);

                // Show empty hint
                self.fieldgroup.hide();
            },

            "{self} composerBlockActivate": function(el, event, block) {

                self.activatePanel(block);
            },

            "{self} composerBlockDeactivate": function(el, event, block) {

                self.deactivatePanel(block);
            },

            "{self} composerBlockRemove": function(el, event, block) {

                // Show empty hint
                self.fieldgroup.hide(block);
            },

            "{subpanelButton} click": function(subpanelButton) {

                var name = subpanelButton.data("name");
                self.openPanel(name);
            }
        }
    });

    module.resolve();

});
;