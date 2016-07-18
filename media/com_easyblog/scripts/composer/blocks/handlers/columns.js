EasyBlog.module("composer/blocks/handlers/columns", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Columns", {
        defaultOptions: {

            "{columnsHeader}": ".row",
            "{columns}": ".row > div",
            "{wrapper}": "> div[data-col-wrapper]",
            "{columnRange}": "[data-eb-composer-block-column-range]",

            // Fieldset options
            "{control}": "[data-columns-control]",
            "{controlWidth}": "[data-select-width]",
            "{dropdown}":".eb-composer-manage-tab-name > select ",
            "{listboxItem}": "[data-listbox-item]"
        }
    }, function(self, opts, base, composer, blocks, meta, currentBlock) {

        return {

            init: function() {
                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();
            },

            matchWrapperHeight: function(block) {

                // Find the tallest wrapper and set it for all the wrapper.
                // This makes drag & drop operation smoother.
                var maxHeight = 0;

                self.wrapper
                    .inside(block)
                    // Remove wrapper height if enforced
                    .css("height", "")

                    // Find tallest height
                    .each(function(){

                        // Use wrapper height if it is taller
                        maxHeight = Math.max(maxHeight, $(this).height());
                    })

                    // Set the tallest height for all wrapper
                    .css("height", maxHeight);
            },

            removeWrapperHeight: function(block) {

                // Remove inline height styling from wrapper
                self.wrapper
                    .inside(block)
                    .css("height", "");
            },

            "{blocks.root} sortactivate": function() {

                // Get all column blocks
                blocks.getBlocksByType("columns")
                    .each(function(){
                        var block = blocks.getBlockContent($(this));

                        self.matchWrapperHeight(block);
                    });
            },

            "{blocks.root} sortdeactivate": function() {

                // Get all column blocks
                blocks.getBlocksByType("columns")
                    .each(function(){
                        var block = blocks.getBlockContent($(this));
                        self.removeWrapperHeight(block);
                    });
            },

            "{wrapper} sortchange": function(wrapper, event, ui) {

                var block = wrapper.closest(EBD.block);

                self.matchWrapperHeight(block);
            },

            "{wrapper} sortout": function(wrapper, event, ui) {

                var block = wrapper.closest(EBD.block);

                self.matchWrapperHeight(block);
            },

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fielset
                self.populate(block);
            },

            deactivate: function(block) {
            },

            construct: function(data) {
                var data = $.extend({}, opts.data, data);

                return content;
            },

            reconstruct: function(block) {
            },

            //
            // Deconstruct a block so that it is free from any strange formatting or decorated stuffs
            //
            deconstruct: function(block) {
                var content = blocks.getBlockContent(block);

                // Get a list of column wrappers available currently.
                var wrappers = content.find('> .row > .col > [data-col-wrapper]');

                wrappers.each(function() {
                    var wrapper = $(this);

                    // Get the column parent
                    var column = wrapper.parent();

                    // Append all the child items to the column
                    wrapper.children().appendTo(column);

                    // Remove any content editable stuffs
                    column.removeAttr('contenteditable');

                    // Remove the wrapper.
                    wrapper.remove();
                });

                return block;
            },

            refocus: function(block) {

                var content = blocks.getBlockContent(block);
                var activeColumn = block.data('lastFocused') || self.columns.inside(content).filter(':first');

                // TODO: Capture lastFocused column
                activeColumn.focus();
            },

            reset: function(block) {
            },

            populate: function(block) {

                var data = blocks.data(block);
                var uid = block.attr("data-uid");

                // Re-populate the listbox items
                self.control().listbox()
                    .listbox('populate', data.columns);

                // Go through each of the list boxes and set the values accordingly.
                var content = blocks.getBlockContent(block);

                // var columns = self.columns.inside(content);
                var columns = $('div[data-uid="' + uid + '"]').find('div.ebd-block-content:eq(0) > div').children();

                columns.each(function(i, column) {
                    var listboxItem = self.listboxItem().eq(i);
                    var dropdown = self.controlWidth.inside(listboxItem);

                    // Get the size of the column
                    var size = $(column).attr('data-size');

                    dropdown.val(size);
                });
            },

            toHTML: function(block) {

                var clone = block.clone();
                var deconstructedBlock = self.deconstruct(clone);
                var content = blocks.getBlockContent(deconstructedBlock);

                return content.html();
            },

            toData: function(block) {
                var data = blocks.data(block);
                return data;
            },

            toText: function(block) {

                var content = blocks.getBlockContent(block).clone();

                return content.text();
            },

            // custom methods
            column: {

                activate: function(index) {
                    var content = blocks.getBlockContent(currentBlock);

                    // Get the columns
                    var columns = self.columns.inside(content);

                    // Get the column to activate
                    columns.eq(index).addClass('active-column');

                    // console.log('activate');
                },

                deactivate: function(index) {
                    var content = blocks.getBlockContent(currentBlock);

                    // Get the columns
                    var columns = self.columns.inside(content);

                    // Get the column to activate
                    columns.eq(index).removeClass('active-column');
                },

                // Create the columns
                create: function(size) {

                    var content = blocks.getBlockContent(currentBlock);
                    var columns = self.columns.inside(content);
                    var columnClass = 'col col-md-' + size;

                    // Create a wrapper for default text block
                    var wrapper = blocks.createBlockNest();

                    wrapper.attr('data-col-wrapper', '')
                        .addClass('ui-sortable');

                    // Add the text block into the wrapper
                    var textBlock = blocks.constructNestedBlock('text');
                    wrapper.append(textBlock);

                    // now we need to create a column wrapper and append the text wrapper.
                    var column = $('<div></div>');

                    column.addClass(columnClass)
                        .attr('data-size', size)
                        .append(wrapper);

                    // adding the column wrapper into the column block.
                    self.columnsHeader.inside(content)
                        .append(column);
                },

                remove: function(index) {
                    var blockContent = blocks.getBlockContent(currentBlock);

                    self.columns
                        .inside(blockContent)
                        .eq(index)
                        .remove();

                    var currentColumnsCount = self.columns.inside(blockContent).length;
                    var width = Math.ceil(12/currentColumnsCount);
                    var offset = false;

                    if (currentColumnsCount == 5) {
                        var width = 2;
                        offset = true;
                    }

                    var columnClass = 'col col-md-' + width;

                    self.columnsHeader
                        .inside(blockContent)
                        .find('div.col')
                        .removeClass()
                        .addClass(columnClass);
                        //.css({ 'background-color': "#b0c4de", 'border': "2px solid", 'border-radius': "25px" });
                        // added css class for EasyDebug

                    if (offset) {
                        self.columns
                            .inside(blockContent)
                            .first()
                            .addClass('col-md-offset-1');

                        self.columns
                            .inside(blockContent)
                            .last()
                            .addClass('col-md-offset-0');
                    }

                },

                change: function(index, width) {

                    var blockContent = blocks.getBlockContent(currentBlock);
                    var data = blocks.data(currentBlock);


                    // where this is a nested blocks and it allow 'itself' to be placed inside, then we can no longer use below method
                    // var currentColumnsCount = self.columns.inside(blockContent).length;

                    // we need to get the current nested block using uid and using more specific selector, as below:
                    var uid = currentBlock.attr("data-uid");
                    var currentColumnsCount = $('div[data-uid="' + uid + '"]').find('div.ebd-block-content:eq(0) > div').children().length;


                    //TODO: throw error if reach limit
                    if (width > 4 && currentColumnsCount == 3) {
                        self.columns.inside(blockContent).last().addClass('col col-md-2');
                    }
                    if (width == 4 && currentColumnsCount == 4) {
                        self.columns.inside(blockContent).last().addClass('col col-md-2');
                    }

                    if (width > 4 && (currentColumnsCount == 4 || currentColumnsCount == 5)) {
                        return;
                    }
                    if (currentColumnsCount == 6) {
                        // throw error
                        self.dropdown().val('2');
                        return;
                    }

                    self.columns.inside(blockContent).first().removeClass('col-md-offset-1');
                    self.columns.inside(blockContent).last().removeClass('col-md-offset-0');

                    var columnClass = 'col col-md-' + width;

                    var curColumn = self.columns.inside(blockContent).eq(index);

                    // console.log(curColumn);

                    data.columns[index].size = width;

                    self.columns.inside(blockContent).eq(index)
                        .attr('data-size', width)
                        .removeClass()
                        .addClass(columnClass);
                }
            },

            // When someone hovers over the list box item we need to hover the active column
            "{listboxItem} mouseover": function(el, event) {
                var index = el.index();

                // Add hover state
                self.column.activate(index);
            },

            "{listboxItem} mouseout": function(el, event) {
                var index = el.index();

                self.column.deactivate(index);
            },

            "{control} listboxBeforeAddItem": function(el, ev, item) {
            },

            "{control} listboxAfterAddItem": function(el, ev, item) {
                blocks.data(currentBlock).columns = self.control().listbox('toData');
                self.column.create(1);
            },

            "{control} listboxBeforeRemoveItem": function(el, ev, item) {
                var index = item.index();

                self.column.remove(index);
            },

            "{control} listboxAfterRemoveItem": function(el, ev, item) {
                blocks.data(currentBlock).columns = self.control().listbox('toData');
            },

            "{controlWidth} change": function(el, event) {

                // Get the parent item
                var item = el.parents(self.listboxItem.selector);
                var index = item.index();
                var width = el.val();

                self.column.change(index, width);

                return;
            }

        }
    });

    module.resolve();

});
