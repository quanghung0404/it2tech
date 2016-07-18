EasyBlog.module("composer/blocks/handlers/note", function($){

    var module = this;

    EasyBlog.require()
    .done(function(){

        EasyBlog.Controller("Composer.Blocks.Handlers.Note", {

            defaultOptions: {

                data: {
                    type: "warning",
                    content: "Enter an important note here" // This gets replaced with translated string when initialized
                },

                "{note}": ".alert",
                "{alertSelection}": "[data-eb-composer-block-alert-type] [data-type]"
            }
        }, function(self, opts, base, composer, blocks, meta, currentBlock) {

            return {

                init: function() {
                    // Globals
                    blocks = self.blocks;
                    composer = blocks.composer;
                    meta = opts.meta;
                    currentBlock = $();

                    // Update default data
                    opts.data.content = $(meta.content).html();
                },

                activate: function(block) {
                    // Set as current block
                    currentBlock = block;

                    // Populate fieldset
                    self.populate(block);
                },

                construct: function(data) {
                    var data = $.extend({}, opts.data, data),
                        content =
                            $(meta.content)
                                .switchClass("alert-" + data.type)
                                .html(data.content);

                    // return content;
                },

                reconstruct: function(block) {
                },

                deconstruct: function(block) {

                    var blockContent = blocks.getBlockContent(block);

                    // make note not editable
                    self.note.inside(blockContent).editable(false);

                    return block;
                },

                refocus: function(block) {
                    // Get note
                    var blockContent = blocks.getBlockContent(block),
                        note = self.note.inside(blockContent);

                    // Focus on note
                    note.focus();

                    // If block is new
                    if (block.hasClass("is-new")) {

                        // Set caret at the end of heading
                        composer.editor.caret.setEnd(note[0]);
                    }
                },

                // Returns the text that is within the block
                toText: function(block) {
                    var blockContent = blocks.getBlockContent(block),
                        text = self.note.inside(blockContent).text();

                    return text;
                },

                toData: function(block) {
                    var data = blocks.data(block);

                    return data;
                },

                toHTML: function(block)  {

                    var cloned = block.clone(),
                        deconstructedBlock = self.deconstruct(cloned),
                        blockContent = blocks.getBlockContent(deconstructedBlock);

                    return blockContent.html();

                },


                reset: function(block) {
                    var blockContent = blocks.getBlockContent(block);

                    blockContent.html(self.construct());
                },

                populate: function(block) {
                    var data = blocks.data(block);

                    // Update fieldset
                    self.alertSelection()
                        .where("type", data.type)
                        .activateClass("selected");
                },

                setAlertType: function(type) {
                    var blockContent = blocks.getBlockContent(currentBlock);
                    var note = blockContent.find(self.note);

                    // Set the note type
                    note.switchClass('alert-' + type);
                },

                previewType: function(type) {


                    // Get the current block note
                    var blockContent = blocks.getBlockContent(currentBlock);
                    var note = self.note.inside(blockContent);
                    var currentClass = note.attr('class');
                    var data = blocks.getData(currentBlock);

                    if (!currentBlock.hasClass('is-preview')) {

                        // Update the block's data with the original class
                        currentBlock.data('originalClass', currentClass);

                        // Add a preview class
                        currentBlock.addClass('is-preview');
                    }

                    // Update the alert type
                    self.setAlertType(type);

                    // Trigger necessary events
                    var args = [currentBlock, self, note];
                    self.trigger("composerBlockNotePreviewType", args);
                    self.trigger("composerBlockChange", args);
                },

                previewTimer: null,

                "{alertSelection} mouseover": function(alertSelection) {

                    // // Set heading level to the one being hovered on
                    // var type = alertSelection.data('type');

                    // // Preview level on current block
                    // self.previewType(type);
                },

                "{alertSelection} mouseout": function(alertSelection) {

                    // clearTimeout(self.previewTimer);

                    // // Delay before reverting to original level
                    // self.previewTimer = $.delay(function(){

                    //     var originalClass = currentBlock.data("originalClass");

                    //     console.log(originalClass);

                    //     if (originalClass) {
                    //         self.setAlertType(originalClass);
                    //     }

                    //     // Remove the is-preview class
                    //     currentBlock.removeClass('is-preview');
                    //     currentBlock.removeData('originalClass');

                    // }, 50);
                },

                "{alertSelection} click": function(el) {

                    // Get the alert type
                    var type = el.data('type');
                    var data = blocks.data(currentBlock);


                    // Remove all selected class
                    self.alertSelection().removeClass("selected");

                    // Add selected class on the selected item
                    $(el).addClass('selected');

                    // Remove the is-preview class
                    currentBlock.removeClass("is-preview");
                    currentBlock.removeData("originalClass");

                    // Set the alert type
                    self.setAlertType(type);

                    // Update the data
                    data.type = type;

                    self.populate(currentBlock);
                }
            }
        });

        module.resolve();
    });

});
