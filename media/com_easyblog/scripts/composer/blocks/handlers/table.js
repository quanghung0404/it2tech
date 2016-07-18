EasyBlog.module("composer/blocks/handlers/table", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Table", {

        defaultOptions: {

            "{tableContainer}": "> .table-container",
            "{table}": "table",
            "{row}"  : "tr",
            "{cell}" : "td",
            "{cellContent}": "td div[contenteditable]",

            "{loading}": "[data-table-loading]",

            // Fieldset options
            "{tableStriped}": "[data-table-striped]",
            "{tableBordered}": "[data-table-bordered]",
            "{tableHover}": "[data-table-hover]",
            "{tableCondensed}": "[data-table-condensed]",
            "{tableRowFieldset}": "[data-table-rows]",
            "{addRow}": "[data-table-rows-add]",
            "{removeRow}": "[data-table-rows-remove]",
            "{tableColumns}": "[data-table-columns]",
            "{tableColumnsAdd}": "[data-table-columns-add]",
            "{tableColumnsRemove}": "[data-table-columns-remove]"
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

            activate: function(block) {

                // Set as current block
                currentBlock = block;

                // Populate fielset
                self.populate(block);

                block.on("keydown.tab", self.cellContent.selector, function(event){

                    if (event.which==9) {
                        var cell = $(event.target).parent(),
                            cells = self.cell.inside(currentBlock),
                            content = cells.eq(Math.min(cells.index(cell) + 1, cells.length - 1)).find('> div[contenteditable]');

                        // Note: If this contenteditable has no childNodes, it will break.
                        // Redactor will restore an invisibleSpace when user hits backspace
                        // until this div is empty. So this will only break if someone
                        // explicitly empty out this contenteditable.
                        composer.editor.caret.setAfter(content[0].lastChild);

                        // This prevents redactor from calling buildEventKeydownTab
                        // which executes event.preventDefault and causes caret to
                        // to get stuck in the beginning on FF.
                        event.stopPropagation();
                    }
                });
            },

            deactivate: function(block) {

                block.off("keydown.tab");
            },

            construct: function(data) {
                return content;
            },

            deconstruct: function(block) {

            },

            reconstruct: function(block) {

            },

            refocus: function(block) {
                var blockContent = blocks.getBlockContent(block),
                    activeCell = block.data("lastFocused") || self.cell.inside(blockContent).filter(":first");

                // TODO: Capture lastFocused column
                activeCell.focus();
            },

            reset: function(block) {
            },

            populate: function(block) {
                var data = blocks.data(block);

                self.tableStriped().val(data.striped).trigger('change');
                self.tableBordered().val(data.bordered).trigger('change');
                self.tableHover().val(data.hover).trigger('change');
                self.tableCondensed().val(data.condensed).trigger('change');

                self.tableRowFieldset().val(data.rows);
                self.tableColumns().val(data.columns);
            },

            toHTML: function(block) {
                self.loader(false);

                var blockContent = blocks.getBlockContent(block),
                    table = self.table.inside(blockContent).clone();

                $.each(table.find('td'), function(i, cell) {
                    cell = $(cell);

                    cell.html(cell.find('div[contenteditable]').html());
                });

                return table[0].outerHTML;
            },

            toData: function(block) {
                var data = blocks.data(block);

                return data;
            },

            toText: function(block) {
                var blockContent = blocks.getBlockContent(block);

                return self.table.inside(blockContent).text();
            },

            // Fieldset methods
            "{tableStriped} change": function(el, ev) {
                var blockContent = blocks.getBlockContent(currentBlock),
                    val = el.val();

                self.table
                    .inside(blockContent)
                    .toggleClass('table-striped', val == 1);

                blocks.data(currentBlock, 'striped', val == 1 ? 1 : 0);
            },

            "{tableBordered} change": function(el, ev) {

                var blockContent = blocks.getBlockContent(currentBlock),
                    val = el.val();

                self.table
                    .inside(blockContent)
                    .toggleClass('table-bordered', val == 1);

                blocks.data(currentBlock, 'bordered', val == 1 ? 1 : 0);
            },

            "{tableHover} change": function(el, ev) {

                var blockContent = blocks.getBlockContent(currentBlock),
                    val = el.val();

                self.table
                    .inside(blockContent)
                    .toggleClass('table-hover', val == 1);

                blocks.data(currentBlock, 'hover', val == 1 ? 1 : 0);
            },

            "{tableCondensed} change": function(el, ev) {

                var blockContent = blocks.getBlockContent(currentBlock),
                    val = el.val();

                self.table
                    .inside(blockContent)
                    .toggleClass('table-condensed', val == 1);

                blocks.data(currentBlock, 'condensed', val == 1 ? 1 : 0);
            },

            loader: function(state) {
               
                var content = blocks.getBlockContent(currentBlock);
                var container = self.tableContainer.inside(content);

                if (state === false) {
                    self.loading
                        .inside(content)
                        .remove();

                    return;
                }

                if (self.loading.inside(content).length > 0) {
                    return;
                }

                var table = self.table.inside(container);

                var placeholder = $.create('div')
                        .attr('data-table-loading', '')
                        .css({
                            top: 0,
                            left: 0,
                            width: '100%',
                            height: '50px',
                            position: 'absolute',
                            background: 'rgba(0,0,0,0.5)',
                        })
                        .html(
                            $.create('div')
                                .css({
                                    textAlign: 'center',
                                    position: 'relative',
                                    top: '50%',
                                    lineHeight: '20px',
                                    marginTop: '-10px'
                                })
                                .html('<i class="fa fa-circle-o-notch fa-spin"></i>')
                        );

                container.append(placeholder);
            },

            "{tableRowFieldset} change": function(el, ev) {
                var content = blocks.getBlockContent(currentBlock);
                var value = el.val();
                var data = blocks.data(currentBlock);

                if (isNaN(value) || parseInt(value) < 1 || $.isEmpty(value)) {
                    el.val(value = 1);
                }

                value = parseInt(value);

                var diff = value - self.row.inside(content).length;

                if (diff == 0) {
                    return;
                }

                data.rows = value;

                // Why should we detach the table?
                // var table = self.table.inside(blockContent).detach();
                var table = self.table.inside(content);

                // Detach the table.
                // table.detach();

                setTimeout(function() {

                    if (diff < 0) {
                        self.row.inside(table).slice(diff).remove();
                    }

                    if (diff > 0) {
                        var totalColumns = parseInt(self.tableColumns().val());
                        var html = Array(diff + 1).join("<tr>" + Array(totalColumns + 1).join('<td><div contenteditable="true">&#8203;</div></td>') + "</tr>");

                        table.append(html);
                    }

                    table.appendTo(content);
                    // content.html(table);

                    // self.loader(false);
                }, 50);
            },

            "{tableColumns} change": function(columnFieldset, ev) {
                var content = blocks.getBlockContent(currentBlock);
                var value = columnFieldset.val();
                var data = blocks.data(currentBlock);

                // Ensure that there's a value in the column
                if (isNaN(value) || parseInt(value) < 1 || $.isEmpty(value)) {
                    value = 1;

                    columnFieldset.val(value);
                }

                value = parseInt(value);

                // Get the first row
                var firstRow = self.row.inside(content).filter(':first-child');

                var diff = value - self.cell.inside(firstRow).length;

                if (diff == 0) {
                    return;
                }

                // Set the number of columns on the block data.
                data.columns = value;

                var table = self.table.inside(content);

                // Why do we need to detach the table?
                // var table = self.table.inside(content).detach();

                // Add a loader in the block
                // self.loader();

                setTimeout(function() {
                    var rows = self.row.inside(table);

                    // If there's lesser columns, it means we should substract
                    if (diff < 0) {
                        $.each(rows, function(i, row) {
                            self.cell.inside(row).slice(diff).remove();
                        });
                    }

                    // If there's more columns, it means we should add
                    if (diff > 0) {
                        var html = Array(diff + 1).join('<td><div contenteditable="true">&#8203;</div></td>');

                        rows.append(html);
                    }

                    table.appendTo(content);
                }, 50);
            },

            "{addRow} click": function(el, ev) {
                var content = blocks.getBlockContent(currentBlock);
                var total = parseInt(self.tableRowFieldset().val()) + 1;
                
                self.tableRowFieldset().val(total)
                    .trigger('change');
            },

            "{removeRow} click": function(el, ev) {
                var content = blocks.getBlockContent(currentBlock);
                var value = Math.max(parseInt(self.tableRowFieldset().val()) - 1, 1);

                self.tableRowFieldset().val(value)
                    .trigger('change');
            },

            "{tableColumnsAdd} click": function(el, ev) {

                var columns = parseInt(self.tableColumns().val()) + 1;

                self.tableColumns()
                    .val(columns)
                    .trigger('change');
            },

            "{tableColumnsRemove} click": function(el, ev) {
                self.tableColumns().val(Math.max(parseInt(self.tableColumns().val()) - 1, 1)).trigger('change');
            }
        }
    });

    module.resolve();

});
