EasyBlog.module("composer/category", function($){

    var module = this;

    EasyBlog.Controller("Composer.Category", {
        defaultOptions: {

            // view: {
            //     itemGroup: "site/composer/form/category/itemGroup",
            //     itemParent: "site/composer/form/category/itemParent",
            //     item: "site/composer/form/category/item"
            // },

            // Templates
            "{itemGroupTemplate}": "[data-category-item-group-template]",
            "{itemTemplate}": "[data-category-item-template]",

            "{viewport}"  : "[data-eb-composer-category-viewport]",
            "{tree}"      : "[data-eb-composer-category-tree]",
            "{itemGroup}" : "[data-eb-composer-category-item-group]",
            "{itemGroupHeader}" : "[data-eb-composer-category-item-group-header]",
            "{item}"      : "[data-eb-composer-category-item]",
            "{itemCount}": "[data-eb-composer-category-item-count]",
            "{itemCheckbox}": "[data-eb-composer-category-item-checkbox]",

            "{count}": "[data-eb-composer-category-count]",

            "{searchTextfield}": "[data-eb-composer-category-search-textfield]",

            "{jsondata}": "[data-eb-composer-category-jsondata]"
        }
    },
    function(self, opts, base, composer) { return {

        init: function() {

            composer = self.composer;

            // Initialize the templates first
            self.initializeTemplates();

            self.viewport()
                .on("mousewheel", function(event){
                    event.stopPropagation();
                });

            var i = 0;
            var categories = JSON.parse(self.jsondata().val());


            // Build the categories
            self.build(categories);

            while (category = categories[i++]) {
                category.selected && self.selectCategory(category.id);
            }

            self.go(0);

            // Remove the textarea
            self.jsondata().remove();

            window.catz = self;
        },

        initializeTemplates: function() {
            // Detach the templates first
            opts.templates = {};

            opts.templates.itemGroup = self.itemGroupTemplate().detach().html();
            opts.templates.item = self.itemTemplate().detach().html();
        },

        // This is the categoryById object map,
        // used for fast category retrieval.
        categories: {

            // Root
            "0": {
                id: 0,
                title: "",
                parent_id: null
            },

            // Pseudo-parent. This reduces extra logic when searching.
            "search": {
                id: "search",
                title: "",
                parent_id: null
            }
        },

        // This is the childCategoriesByParentId object map,
        // used for fast child categories retrieval.
        children: {
            "search": [] // Pseudo-parent. This reduces extra logic when searching.
        },

        // This is the categoryByKey object map,
        // used for fast category searching.
        keys: {},

        // This is used to speed up displaying of search results.
        clones: {},

        // Use $.has(selected, id) to quickly determine if an id is selected.
        // Use $.values(selected) to retrieve array of selected ids.
        selected: {},

        build: function(data) {

            var i = 0,
                category;

            while (category = data[i++]) {

                var id = parseInt(category.id),
                    parentId = parseInt(category.parent_id);

                // Store in categories object
                self.categories[id] = category;

                // Store category as a children of a parent
                (self.children[parentId] || (self.children[parentId]=[])).push(category);

                // Store category by key.
                var key = category.key = category.title.toLowerCase() + " [#" + id + "]";
                self.keys[key] = category;
            }
        },

        childrens: $.memoize(function(id, deep) {

            var children = self.children[id];

            if (!children) return [];

            // If recursive children retrieval is called
            // before immediate children retrieval on the
            // same id, doing this will let us cache the value
            // of immediate children retrieval so we won't
            // need to go through this operation again.
            var ids = deep ? [].concat(self.childrens(id)) : $.flatten(children, 'id'),
                length = ids.length,
                i;

            if (deep) {
                for (i=0; i<length; i++) {
                    ids = ids.concat(self.childrens(ids[i], deep));
                }
            }

            return ids;

        }, function(id, deep) {

            return id + (deep ? "_deep" : "");
        }),

        parents: $.memoize(function(id) {

            var category = self.categories[id];

            if (!category) return;

            if (id==0) return [0];

            var parents = [];

            while (category = self.categories[category.parent_id]) {
                parents.unshift(category.id);
            }

            return parents;
        }),

        itemGroup: {

            get: $.memoize(function(id) {

                // Get children of this category
                var children = self.children[id];

                // If this category does not
                // contain any children, stop.
                if (!children) return;

                // Get category and create item group
                var category = self.categories[id];
                var itemGroup = self.getItemGroupTemplate(category);

                // Collect child item in an array
                // then only append it onto itemGroup
                // for performance reasons.
                var child, items = [], i = 0;

                while (child = children[i++]) {
                    items.push(self.item.get(child.id));
                }

                $(itemGroup)
                    .locate("viewport")
                    .append(items);

                return itemGroup;
            })
        },

        item: {

            get: $.memoize(function(id) {

                // Get category
                var category = self.categories[id];

                // If category does not exist, stop.
                if (!category) return;

                // Get children
                var children = self.children[id];

                var item = self.getItemTemplate(category, children || null);

                return item;
            })
        },

        getItemGroupTemplate: function(category) {
            var template = $(opts.templates.itemGroup);

            // Set the options here.
            template.attr('data-id', category.id);
            template.find('[data-title]').html(category.title);

            return template[0];
        },

        getItemTemplate: function(category, children) {
            var template = $(opts.templates.item);

            template.attr('data-id', category.id);
            template.find('[data-title]').html(category.title);

            if (children) {
                template.addClass('has-children');
            }

            return template[0];
        },

        itemCount: {

            get: $.memoize(function(id) {
                return $(self.item.get(id)).find(self.itemCount.selector)[0];
            }),

            set: function(id, count) {
                $(self.itemCount.get(id)).find("span").html(count);
            }
        },

        populate: function(parentId) {

            var children = self.children[parentId];

            if (!children) return;

            // Remove active class on header
            $(self.itemGroup.get(parentId))
                .locate("header")
                .removeClass("active");


            // Process child items
            var i = 0,
                category,
                selected = self.selected,
                selectedIds = $.values(selected);

            while (category = children[i++]) {

                var id = category.id,
                    allChildIds = self.childrens(id, true),
                    selectedChildIds = $.intersection(selectedIds, allChildIds),
                    immediateChildIds = self.childrens(id),
                    selectedImmediateChildIds = $.intersection(selectedChildIds, immediateChildIds);

                // Add/remove selected class from item
                $(self.item.get(id))
                    .toggleClass("selected", $.has(selected, id))
                    .toggleClass("has-selected-children", selectedChildIds.length > 0)
                    .toggleClass("is-primary", category.isprimary)
                    .removeClass("active");

                // Update count
                if (immediateChildIds.length > 0) {
                    var count = selectedImmediateChildIds.length + '/' + immediateChildIds.length;
                    self.itemCount.set(id, count);
                }
            }
        },

        query: $.memoize(function(keyword) {

            var keys = self.keys,
                results = {},
                re = new RegExp("^" + $.regExpEscape(keyword), 'i');

            for (key in keys) {
                if (key.match(re)) {
                    var category = keys[key],
                        parentId = category.parent_id;
                    (results[parentId] || (results[parentId] = [])).push(category);
                }
            }

            return results;
        }),

        search: function(keyword) {

            var results = self.query(keyword),
                clones = self.clones,
                itemGroups = [],
                items = [];

            for (parentId in results) {

                // Get item group
                var category = self.categories[parentId];
                var itemGroup = self.getItemGroupTemplate(category);

                // Get items
                var children = results[parentId],
                    child, items = [], i = 0;

                while (child = children[i++]) {

                    var id = child.id,
                        clone = clones[id] || (clones[id]=$(self.item.get(id)).clone()[0]);

                    items.push(clone);
                }

                // Add items to item group
                $(itemGroup)
                    .locate("viewport")
                    .append(items);

                // Add itemGroup to itemGroups.
                // Always add root itemGroup in the beginning of the aray.
                itemGroups[parentId==0 ? "unshift" : "push"](itemGroup);
            }

            self.itemGroup().detach();

            $(self.itemGroup.get("search"))
                .empty()
                .append(itemGroups)
                .appendTo(
                    self.tree()
                        .switchClass("level-0")
                        .data("level", 0)
                );
        },

        currentTree: [],

        go: function(id) {

            var category = self.categories[id];

            if (!category) return;

            // Get parents
            var tree = self.tree(),
                parents = self.parents(id),
                level = parents.length - 1,

                // Get item groups
                itemGroups =
                    $.map(parents, function(id) {
                        return self.itemGroup.get(id);
                    });

            // Maintain seamless transition
            // when moving backwards by
            // retaining the curren item group.
            var lastLevel = tree.data("level") || 0;
            if (lastLevel > level) {
                itemGroups.push(
                    self.itemGroup().eq(lastLevel)
                );
            }

            // Detach all item groups on the screen
            self.itemGroup().detach();

            // Populate count and selected items
            // on items of the current item group.
            self.populate($.last(parents));

            // Append new item groups
            tree.append(itemGroups)
                .switchClass("level-" + level)
                .data("level", level);

            // Clear search text field
            self.searchTextfield().val("");
        },

        open: function(id) {

            var children = self.children[id];

            // If this category has child items
            if (children) {

                // Add active class
                $(self.item.get(id)).addClass("active");

                // Open it
                self.go(children[0].id);
            }
        },

        selectCategory: function(id) {
            self.toggle(id, true);
        },

        deselect: function(id) {
            self.toggle(id, false);
        },

        toggle: function(id, toggle) {

            var item = $(self.item.get(id)),
                clone = $(self.clones[id]),
                selected = self.selected;

            // Decide to select or deselect if no value was passed in
            toggle ===undefined && (toggle = !item.hasClass("selected"));

            // If select, add to selected map.
            // If deselect, remove from selected map.
            toggle ? (selected[id] = parseInt(id)) : (delete selected[id]);

            // Toggle selected class on item
            var addOrRemoveClass = (toggle ? "add" : "remove") + "Class";
            item[addOrRemoveClass]("selected");
            clone[addOrRemoveClass]("selected");

            if ($.keys(self.selected).length == 0) {
                var addOrRemoveClass = (toggle ? "remove" : "add") + "Class";
                item[addOrRemoveClass]("selected");
                clone[addOrRemoveClass]("selected");

                EasyBlog.dialog(
                    {
                        content :'You must have at least one category assigned to this blog post.',

                    });

                // reverse the toggling process
                toggle ? (delete selected[id]) : (selected[id] = parseInt(id));

                return;
            }
            // Trigger categoryselect/categorydeselect event
            self.trigger("category" + (toggle ? "select" : "deselect"), [self.categories[id]]);

            // Update count
            self.count().html($.keys(self.selected).length);
        },

        enableKeyboardNavigation: function() {


            $(document)
                .off("keydown.composer.category")
                .on("keydown.composer.category", function(event){

                    var keyCode = event.keyCode,
                        direction = {38: "up", 40: "down", 37: "left", 39: "right"};

                    switch (keyCode) {
                        case 38:
                        case 40:
                        case 37:
                        case 39:
                            self.navigate(direction[keyCode]);
                            break;
                        // space
                        case 32:
                            break;
                    }
                });
        },

        disableKeyboardNavigation: function() {

            $(document).off("keydown.composer.category");
        },

        navigate: function(direction) {

            console.log(direction);

            var item = self.currentClickedItem;

            switch (direction) {

                case "up":
                    item.prev().click();
                    break;

                case "down":
                    item.next().click();
                    break;

                case "left":
                    self.go(item.data("id"));
                    break;

                case "right":
                    break;
            }
        },

        toArray: function() {

            if (!self.selected) return [];

            var categories = [];

            for (id in self.selected) {
                var category = self.categories[id];
                categories.push(category);
            }

            return categories;
        },

        lastClickedItem: $(),

        currentClickedItem: $(),

        "{item} click": function(item) {

            self.lastClickedItem = self.currentClickedItem;
            self.currentClickedItem = item;
        },

        "{self} composerSave": function(el, event, save)
        {
            save.data.categories = $.values(self.selected);
        },

        "{self} categorysetprimary": function(el, event, category)
        {
            // let unset all the is-primary class from the selections.
            self.item().removeClass('is-primary');

            $(self.item.get(category.id))
                .addClass('is-primary');
        },

        "{itemGroup} click": function(itemGroup, event) {

            var items = itemGroup.find(self.item.selector),
                lastItem = self.lastClickedItem,
                currentItem = self.currentClickedItem;

            if (!(event.metaKey || event.ctrlKey)) {
                items.removeClass("active");
            }

            // Toggle active class on element
            currentItem.toggleClass("active");

            // If user is holding shift, perform a range select.
            // Mac users: CMD+LSHIFT or RSHIFT
            // TODO: This has bugs. Refine when have time.
            if (event.shiftKey && lastItem.length > 0) {

                var last    = items.index(lastItem),
                    current = items.index(currentItem);

                // Retrieve range of items
                items.slice
                    .apply(items, last < current ? [last, current] : [current, last])
                    .addClass("active");
            }

            // If there are active items, enable keyboard navigation
            if (items.filter(".active").length > 0) {
                self.enableKeyboardNavigation();
            } else {
                self.disableKeyboardNavigation();
            }
        },

        "{item} dblclick": function(item) {

            var id = item.data("id");
            self.open(id);
        },

        "{itemCount} click": function(itemCount) {

            var id = self.item.of(itemCount).data("id");
            self.open(id);
        },

        "{itemCheckbox} click": function(itemCheckbox, event) {

            var id = self.item.of(itemCheckbox).data("id");
            self.toggle(id);

            // Prevent item traversal
            event.stopPropagation();
        },

        "{itemGroupHeader} click": function(itemGroupHeader) {

            var id =
                self.itemGroup
                    .of(itemGroupHeader)
                    .data("id");

            itemGroupHeader.addClass("active");

            self.go(id);
        },

        "{searchTextfield} keyup": $.debounce(function(textfield) {

            var keyword = $.trim(textfield.val());

            if (keyword=="") {
                self.go(0);
            } else {
                self.search(keyword);
            }

        }, 250),

        "{self} composerValidate": function(composer, event, validator) {
        }


    }});

    module.resolve();

});
