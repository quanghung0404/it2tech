EasyBlog.module("composer/tags", function($) {

    var module = this;

    // Constants
    var KEYCODE = {
        BACKSPACE: 8,
        COMMA: 188,
        DELETE: 46,
        DOWN: 40,
        ENTER: 13,
        ESCAPE: 27,
        LEFT: 37,
        RIGHT: 39,
        SPACE: 32,
        TAB: 9,
        UP: 38
    };

    var DIRECTIONS = {
        38: "up",
        40: "down",
        37: "left",
        39: "right"
    }

    EasyBlog.require()
    .library(
        "textboxlist",
        "nearest",
        "scrollTo"
    ).done(function() {

        EasyBlog.Controller("Composer.Tags", {
            elements: [
                "[data-eb-composer-tags-{textboxlist}]"
            ],

            defaultOptions: {

                // Templates
                templates: {},
                "{tagTemplate}": "[data-tag-template]",

                pagination: 30,

                "{tags}": "[data-eb-composer-tags]",
                "{tag}": ".textboxlist-item",
                "{total}": "[data-eb-composer-tags-total]",
                "{count}": "[data-eb-composer-tags-count]",

                "{suggestions}": ".eb-composer-tags-suggestions",
                "{selection}": ".eb-composer-tags-selection",
                "{toggleButton}": "[data-eb-composer-tags-toggle-button]",
                "{autofillButton}": "[data-eb-composer-tags-autofill-button]",

                "{itemgroup}": ".eb-composer-tags-selection-itemgroup",
                "{item}": ".eb-composer-tags-selection-itemgroup > div",

                "{textfield}": "[data-textboxlist-textField]",

                "{jsondata}": "[data-eb-composer-tags-jsondata]"
            }
        }, function(self, opts, base, suggestions, selection, tagger) {

            return {

                init: function() {

                    if (EasyBlog.Composer.options.tags.enabled == "0") {
                        return;
                    }

                    // Get the tag template
                    // opts.templates.tag = self.tagTemplate().detach().html();
                    $.template("composer/textboxlist/item", self.tagTemplate().detach().html());

                    // Globals
                    selection = self.selection;
                    suggestions = self.suggestions;

                    // Get max count
                    var max = parseInt(self.tags().attr("data-eb-composer-tags-max")) || null;

                    // Implement textboxlist
                    self.textboxlist()
                        .textboxlist({
                            component: "eb",
                            max: max,
                            ignoreLocked: true,
                            view: {
                                item: 'composer/textboxlist/item'
                            },
                            filterItem: function(item) {

                                if ($.isString(item)) {
                                    return tagger.sanitize(item);
                                }

                                item.title = tagger.sanitize(item.title);
                                return item;
                            }
                        });

                    // Extend tagger with additional methods
                    tagger = self.tagger = $.extend(self.textboxlist().textboxlist("controller"), self.tagger);

                    // Get all tags quietly
                    suggestions.populate("", true);

                    var i = 0;
                    var existingTags = JSON.parse(self.jsondata().val());

                    while (existingTag = existingTags[i++]) {

                        tagger.addItem({
                            id: existingTag.id,
                            title: existingTag.title,
                            key: tagger.getItemKey(existingTag.title),
                            locked: false
                        });
                    }

                    window.tagz = self;
                },

                tagger: {

                    sanitize: function(title) {
                        // Remove whitespace and comma
                        return $.trim(title).replace(/,/g,"");
                    },

                    addLockedItem: function(title) {

                        // Sanitize the tag's title to ensure no trailing spaces
                        title = $.trim(title);

                        var existingTag = tagger.get(title);
                        var userAssignedTag = existingTag && !existingTag.locked;

                        // Remove from list
                        if (existingTag) {
                            tagger.deleteItem(existingTag.id);
                        }

                        tagger.addItem({
                            id: $.uid("item-"),
                            title: title,
                            key: tagger.getItemKey(title),
                            locked: true,
                            assigned: userAssignedTag
                        }, true);
                    }
                },

                "{textfield} keydown": function(textfield, event) {

                    var key = event.which,
                        activeItem = selection.activeItem;

                    switch (key) {

                        case KEYCODE.UP:
                        case KEYCODE.DOWN:
                        case KEYCODE.LEFT:
                        case KEYCODE.RIGHT:

                            var direction = DIRECTIONS[key];

                            // If there is an active item
                            if (activeItem.length > 0 || direction=="down") {

                                // Navigate through the suggestions
                                suggestions.navigate(direction);

                                // Prevent cursor from moving in textfield
                                event.preventDefault();
                            }
                            break;

                        case KEYCODE.ESCAPE:

                            // If there is an active item
                            if (activeItem.length > 0) {

                                // Deactivate the active item
                                selection.deactivate(activeItem);

                            // If suggestions are showing
                            } else if (self.suggestions().hasClass("is-showing")) {

                                // Collapse suggestions
                                suggestions.collapse();

                            // Else clear off textfield
                            } else {
                                textfield.val("");
                            }
                            break;

                        case KEYCODE.ENTER:

                            // If there is an active item
                            if (activeItem.length > 0) {

                                // Use the active item
                                selection.use(activeItem);

                                // Stop any further processing from textboxlist
                                event.preventDefault();
                            }
                            break;

                        default:
                            // Populate suggestions from textfield
                            suggestions.populateFromTextfield();
                            break;
                    }
                },

                "{item} mouseover": function(item) {

                    selection.deactivate();

                    selection.activate(item);
                },

                "{item} mouseout": function(item) {

                    selection.deactivate();

                },

                "{item} click": function(item) {

                    selection.use(item);
                },

                "{textboxlist} addItem": function(textboxlist, event, item) {

                    // Add is-used classname to selection item
                    $(selection.get(item.title)).addClass("is-used");
                },

                "{textboxlist} removeItem": function(textboxlist, event, item) {

                    // If this is an assigned tag
                    if (item.assigned) {

                        // Add this back as a regular item
                        tagger.addItem(item.title);

                    // Else show it back on tag suggestions
                    } else {
                        $(selection.get(item.title)).removeClass("is-used");
                    }
                },

                // Slightly debounced so mass removal only gets executed once
                "{textboxlist} listChange": $.debounce(function() {

                    // Refresh suggestions
                    suggestions.refresh();

                    // Update count
                    var count = self.tagger.getAddedItems().length;
                    self.count().text(count);
                }, 15),

                "{self} categoryselect": function(el, event, category) {

                    if (category.tags.length <= 0) {
                        return;
                    }

                    var tags = (category.tags || "").split(",");

                    $.each(tags, function(i, title){
                        tagger.addLockedItem(title);
                    });
                },

                "{self} categorydeselect": function(el, event, category) {

                    if (category.tags.length <= 0) {
                        return;
                    }

                    var categories = self.composer.category.toArray();
                    var tagsToRemove = (category.tags || '').split(',');
                    var tagsToRetain = $.pluck(categories, 'tags').join(',').split(',');
                    var tags = $.without.apply(null, [tagsToRemove].concat(tagsToRetain));

                    $.each(tags, function(i, title){
                        var tag = tagger.get(title);
                        tag && tagger.removeItem(tag.id);
                    });
                },

                "{toggleButton} click": function(toggleButton) {

                    suggestions[self.suggestions().hasClass("is-showing") ? "collapse" : "expand"]();
                },

                "{autofillButton} click": function(autofillButton) {
                    var content = self.composer.document.getText(),
                        parent = $(autofillButton).parent();

                    // Show loading on parent
                    $(parent).addClass('is-loading');

                    EasyBlog.ajax('site/views/composer/suggestKeywords', {
                        "data": content
                    }).done(function(keywords){

                        // Remove loading class
                        $(parent).removeClass('is-loading');

                        if (keywords) {
                            $.each(keywords, function(i, tag) {
                                tagger.addItem(tag.title);
                            });
                        }
                    });
                },

                suggestions: {

                    index: 0,

                    tags: [],

                    expand: function() {

                        suggestions.tick++;

                        self.suggestions()
                            .addClass("is-showing");
                    },

                    collapse: function() {

                        suggestions.tick++;

                        self.suggestions()
                            .removeClass("is-showing");
                    },

                    refresh: function() {

                        // See if there are available selection items
                        var items = self.item(":not(.is-used)");

                        // Show empty hint if there are no items
                        self.suggestions()
                            .toggleClass("is-empty", items.length < 1);

                        suggestions.count();
                    },

                    show: function() {

                        suggestions.tick++;

                        suggestions.refresh();

                        // Expand suggestions
                        suggestions.expand();
                    },

                    hide: function() {

                        var tick = ++suggestions.tick;

                        // Collapse suggestions
                        suggestions.collapse();

                        setTimeout(function(){

                            if (tick!==suggestions.tick) return;

                            // Reset suggestions
                            suggestions.reset();
                        }, 500);
                    },

                    set: function(tags) {

                        if (tags.length < 1) {

                            suggestions.hide();

                            suggestions.tags = [];

                        } else {

                            // Reset suggestions
                            suggestions.reset();

                            // Assign new tags dataset
                            suggestions.tags = tags;
                        }
                    },

                    reset: function() {

                        // Clear tags
                        suggestions.tags = [];

                        // Reset index
                        suggestions.i = 0;

                        // Clear out selection items
                        self.itemgroup().empty();
                    },

                    pid: 0,

                    tick: 0,

                    currentQuery: null,

                    query: $.memoize(function(keyword) {

                        return EasyBlog.ajax(
                            "site/views/tags/suggest",
                            {
                                search: keyword
                            })
                            .fail(function(){
                                suggestions.query.reset(keyword);
                            });
                    }),

                    populate: function(keyword, quiet) {

                        suggestions.tick++;

                        var pid = ++suggestions.pid;

                        // If there is an active query, abort it.
                        suggestions.currentQuery && suggestions.currentQuery.abort();

                        // Santiize keyword
                        keyword = tagger.sanitize(keyword);

                        // Show loading indicator
                        self.suggestions()
                            .addClass("is-busy");

                        // Get tags
                        suggestions.currentQuery =
                            suggestions.query(keyword)
                                .done(function(tags){

                                    // If this populate task has expired, stop.
                                    if (suggestions.pid!==pid) return;

                                    // Set this as new tag dataset
                                    suggestions.set(tags);

                                    // Show tags
                                    suggestions.suggest(quiet);
                                })
                                .fail(function(){

                                    // If this populate task has expired, stop.
                                    if (suggestions.pid!==pid) return;
                                })
                                .always(function(){

                                    // Remove loading indicator
                                    self.suggestions()
                                        .removeClass("is-busy");

                                    suggestions.currentQuery = null;
                                });
                    },

                    populateTimer: null,

                    populateFromTextfield: function() {

                        suggestions.tick++;

                        clearTimeout(suggestions.populateTimer);

                        setTimeout(function(){

                            var textfield = self.textfield(),
                                keyword   = tagger.sanitize(textfield.val());

                            // If there are no keywords
                            if (keyword==="") {

                                // Hide suggestions
                                suggestions.hide();

                                // Quietly populate all keywords
                                suggestions.populate("", true);

                            } else {

                                // If this query has been made before
                                if (suggestions.query.cache.hasOwnProperty(keyword)) {

                                    // Populate immediately
                                    suggestions.populate(keyword);

                                // Else populate after 250ms delay
                                // to ensure user has finished typing
                                } else {

                                    suggestions.populateTimer =
                                        setTimeout(function(){
                                            suggestions.populate(keyword);
                                        }, 250);
                                }
                            }
                        }, 1);
                    },

                    suggest: function(silent) {

                        // Get current dataset
                        var tags = suggestions.tags;

                        // If there are no tags
                        if (tags.length < 1) return;

                        var count = 0,
                            max = opts.pagination,
                            tag, title, items = [], item;

                        // Fill up suggestions until it reach
                        // the max amount tags per pagination.
                        while (count < max) {

                            // Get tag from current index
                            tag = tags[suggestions.i];

                            // Stop if there are no more tags
                            if (!tag) break;

                            // Get tag title
                            title = tag.title;

                            // Get or create item
                            item = selection.get(title) || selection.create(title);

                            // Get selection item and put it in an array
                            items.push(item);

                            // If this tag has been used
                            if (tagger.get(title)) {

                                // Add is-used class
                                $(item).addClass("is-used");

                            // Only increase count if the tag hasn't been used
                            } else {
                                count++;
                            }

                            // Go to next index
                            suggestions.i++;
                        }

                        // Append selection items to itemgroup
                        self.itemgroup()
                            .append(items);

                        if (silent) {
                            suggestions.refresh();
                        } else {
                            suggestions.show();
                        }
                    },

                    navigate: function(direction) {

                        // Get all rendered items
                        var items = self.item(),
                            itemgroup = self.itemgroup(),
                            activeItem = selection.activeItem,
                            nearestItem =
                                // Find nearest item from the current active item
                                // or from the textfield if there are no active item
                                $(activeItem[0] || self.textfield()[0])
                                    .nearest(items, direction);

                        // If a nearest item was found
                        if (nearestItem.length > 0) {

                            // Activate nearest item
                            selection.activate(nearestItem);

                            // Scroll to nearest item
                            // TODO: Polish quirky scrollIntoView
                            itemgroup.scrollIntoView(nearestItem);

                        // If a nearest item was not found
                        } else if (activeItem.length > 0) {


                            // If we are at the end of the list
                            if (direction=="down") {

                                // Load more suggestions
                                suggestions.suggest();

                                // Scroll down
                                // TODO: Polish quirky scrollIntoView
                                itemgroup.scrollIntoView(activeItem);
                            }

                            //  If we are at the top of the list
                            if (direction=="up") {

                                // Focus back on textboxlist
                                selection.deactivate();
                            }
                        }
                    },

                    count: function() {

                        var allTags = $.pluck(suggestions.tags, "title"),
                            usedTags = $.keys(tagger.itemsByTitle),
                            availableTags = $.without.apply(null, [allTags].concat(usedTags)),
                            count = availableTags.length;

                        // Update count
                        self.total().text(count + "");
                    }
                },

                selection: {

                    items: {},

                    activeItem: $(),

                    create: function(title) {

                        var item = document.createElement("div");
                            item.innerHTML = title;

                        return selection.items[title] = item;
                    },

                    get: function(title) {

                        return selection.items[title];
                    },

                    activate: function(item) {

                        // Deactivate any current active item
                        selection.deactivate();

                        // Set this as the new active item
                        selection.activeItem = item.addClass("active");
                    },

                    deactivate: function(item) {

                        // Remove active class from this item
                        selection.activeItem.removeClass("active");

                        // No more active item
                        selection.activeItem = $();
                    },

                    use: function(item) {

                        var item = $(item),
                            title = item.text();

                        // Add item
                        var tag = tagger.addItem(title);

                        if (!tag) return;

                        // Refocus on the textfield
                        self.textfield().focus();

                        // If the item being added is an active item
                        if (item.is(selection.activeItem)) {

                            // Change the active item to the
                            // nearest candidate
                            nextItem = item.next(self.item)[0] || item.prev(self.item)[0];

                            selection.deactivate(item);

                            if (nextItem) {
                                selection.activate($(nextItem));
                            }
                        }

                        suggestions.refresh();
                    }
                },

                "{self} composerSave": function(base, event, save) {

                    if (EasyBlog.Composer.options.tags.enabled == "0") {
                        return;
                    }
                    
                    // Get all added tags
                    var tags = self.tagger.getAddedItems();

                    // Add array of tags to save data
                    save.data.tags = $.pluck(tags, 'title').join(",");
                }

           }
        });

        module.resolve();
    });

});
