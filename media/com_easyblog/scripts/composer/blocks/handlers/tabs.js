EasyBlog.module("composer/blocks/handlers/tabs", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Handlers.Tabs", {
        defaultOptions: {

            "{tabHeader}": ".nav-tabs",
            "{tabHeaderListItem}": ".nav-tabs > li",
            "{tabHeaderListItemContent}": ".nav-tabs > li a",
            "{tabContent}": ".tab-content",
            "{tabPane}": "> .tab-content > .tab-pane",

            "{titleItem}" : "[data-tab-header-item]",

            // Fieldset options
            "{control}": "[data-tabs-control]",

            "{controlItemContent}": "[data-tabs-control] [data-listbox-item-content]"
       }
    }, function(self, opts, base, composer, blocks, meta, currentBlock)  {

        return {
            init: function() {

                // Globals
                blocks = self.blocks;
                composer = blocks.composer;
                meta = opts.meta;
                currentBlock = $();

                // Update default data and extract template from meta content
                // var ref = $(meta.content);
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

                // TODO: Review this later
                // $.each(data.tabs, function(i, tab){

                //     var tab = self.tab.create(tab),
                //         tabPane = self.tabPane.create(tab);
                // });

                return content;
            },

            reconstruct: function(block) {
            },

            deconstruct: function(block) {
                $.each(block.find('[data-tab-wrapper]'), function(i, wrapper) {
                    wrapper = $(wrapper);

                    var parent = wrapper.parent();

                    parent.removeAttr('contenteditable');

                    wrapper.children().appendTo(parent);

                    wrapper.remove();

                    self.tabHeaderListItemContent.inside(block).eq(i).removeAttr('contenteditable');
                });

                return block;
            },

            refocus: function(block) {
                // Get active tab pane
                var activeTabPane = self.tabPane.inside(block).filter(".active");

                // Focus on active tab pane
                activeTabPane.focus();
            },

            reset: function(block) {
            },

            recover: function(block) {

            },

            populate: function(block) {
                var data = blocks.data(block);

                self.control()
                    .listbox()
                    .listbox('populate', data.tabs, function(item, content) {
                        item.listboxitem('content', content);
                    });
            },

            revert: function(block) {
            },

            toHTML: function(block) {

                var cloned = block.clone(),
                    deconstructedBlock = self.deconstruct(cloned),
                    blockContent = blocks.getBlockContent(deconstructedBlock)

                return blockContent.html();
            },

            toData: function(block) {
                var data = blocks.data(block);
                return data;
            },

            toText: function(block) {
                var cloned = block.clone();

                return self.deconstruct(cloned).text();
            },

            // custom methods
            tab: {
                create: function(count) {
                    var newId = $.uid('tab-'),
                        headerTabHtml = '<li><a href="#' + newId + '" role="tab" data-bp-toggle="tab" contenteditable="true"></a></li>';

                    // tab content wrapper
                    var tabHtml = $('<div></div>', {
                        'class': 'tab-pane',
                        'id': newId
                    });

                    // nested block wrapper
                    var wrapper = $('<div></div>', {
                        'class': 'ebd-nest',
                        'data-type': 'block',
                        'data-tab-wrapper': ''
                    });

                    // adding text block into nested block wrapper
                    var textBlock = blocks.constructNestedBlock("text");
                    wrapper.wrapInner(textBlock);

                    // adding nested block wrapper into tab content wrapper.
                    tabHtml.wrapInner(wrapper);

                    // adding tab content wrapper into tab block
                    self.tabContent.inside(currentBlock).append(tabHtml);
                    self.tabHeader.inside(currentBlock).append(headerTabHtml);

                    // self.tab.focusLast();
                    // self.tab.reCalTabs();
                },

                remove: function(index) {
                    self.tabPane.inside(currentBlock).eq(index).remove();
                    self.tabHeaderListItem.inside(currentBlock).eq(index).remove();
                },

                focus: function(index) {
                    self.tabHeaderListItem.inside(currentBlock).eq(index).find('a').tab('show');
                }
            },


            "{tabHeaderListItem} click": function(el) {
                el.find('a').tab('show');
            },

            "{tabHeaderListItemContent} keyup": $.debounce(function(el, ev) {

                var keycode = (ev.keyCode ? ev.keyCode : ev.which);

                // disallowed newline
                if(keycode != '13') {

                    var index = el.parents('.nav-tabs > li').index(),
                        content = el.html().replace(/(<br>\s*)+$/, '');

                    self.control().listbox('getItems').eq(index).listboxitem('content', content);

                    blocks.data(currentBlock).tabs[index].content = content;
                }

            }, 250),

            // prevent the tab header to redirect when user click on the tab
            "{tabHeaderListItemContent} click": function(el, ev) {
                ev.preventDefault();
            },

            "{tabHeaderListItemContent} keydown": function(el, ev) {
                var keycode = (ev.keyCode ? ev.keyCode : ev.which);

                // disallowed newline
                if(keycode == '13') {
                    ev.preventDefault();
                }
            },


            // Fieldset method
            "{control} listboxBeforeAddItem": function(el, ev, item) {
                self.tab.create();

                var data = item.listboxitem('toData');

                self.tabHeaderListItem.inside(currentBlock).filter(':last').find('a').html(data.content);
            },

            "{control} listboxAfterAddItem": function(el, ev, item) {
                blocks.data(currentBlock).tabs = self.control().listbox('toData');

                if (self.tabContent.inside(currentBlock).length == 1) {
                    self.tabHeaderListItem.inside(currentBlock).filter(':first').find('a').tab('show');
                }
            },

            "{control} listboxBeforeRemoveItem": function(el, ev, item) {
                var index = item.index();

                self.tab.remove(index);
            },

            "{control} listboxAfterRemoveItem": function(el, ev, item) {
                blocks.data(currentBlock).tabs = self.control().listbox('toData');

                if (self.tabContent.inside(currentBlock).length == 1) {
                    self.tabHeaderListItem.inside(currentBlock).filter(':first').find('a').tab('show');
                }
            },

            "{control} listboxAfterToggleDefault": function(el, ev, item) {
                blocks.data(currentBlock).tabs = self.control().listbox('toData');
            },

            "{controlItemContent} keyup": $.debounce(function(el, ev) {

                var keycode = (ev.keyCode ? ev.keyCode : ev.which);

                // disallowed newline
                if(keycode != '13') {
                    var item = el.parents('[data-listbox-item]'),
                        index = item.index(),
                        content = el.html().replace(/(<br>\s*)+$/, '');

                    self.tabHeaderListItem.inside(currentBlock).eq(index).find('a').html(content);

                    blocks.data(currentBlock).tabs[index].content = content;
                }
            }, 250),


            "{controlItemContent} keypress": function(el, ev) {
                var keycode = (ev.keyCode ? ev.keyCode : ev.which);

                // disallowed newline
                if(keycode == '13') {
                    ev.preventDefault();
                }
            }
        }
    });

    module.resolve();

});
