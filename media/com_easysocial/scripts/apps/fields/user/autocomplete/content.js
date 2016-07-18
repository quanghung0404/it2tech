EasySocial.module('apps/fields/user/autocomplete/content', function($) {
    var module = this;

    EasySocial
    .require()
    .library('textboxlist')
    .done(function($) {

        EasySocial.Controller('Field.Autocomplete', {
            defaultOptions: {

                required: false,
                id: null,
                types: null,
                fieldname: null,
                actor: null,
                target: null,

                // Determines pre-selected items
                selectedItems: [],

                // Suggest properties
                max: null,
                exclusive: true,
                exclusion: [],
                minLength: 1,
                highlight: true,
                name: "uid[]",
                type: "",

                "{suggest}": "[data-field-suggest]"
            }
        }, function(self, opts, base) {

            return {
                init: function() {

                    // Set the input's name.
                    opts.name = opts.fieldname + '[]';

                    self.initSuggest();
                },

                // Implement the textbox list on the implemented element.
                initSuggest: function() {
                    
                    self.suggest()
                        .textboxlist({
                            component: 'es',
                            name: opts.name,
                            max: opts.max,
                            plugin: {
                                autocomplete: {
                                    exclusive: opts.exclusive,
                                    minLength: opts.minLength,
                                    highlight: opts.highlight,
                                    showLoadingHint: true,
                                    showEmptyHint: true,

                                    query: function(keyword) {

                                        var result = EasySocial.ajax('fields/user/autocomplete/suggest', {
                                                                        "search": keyword,
                                                                        "id": opts.id
                                                                    });
                                        return result;
                                    }
                                }
                            }
                        })
                        .textboxlist("enable");
                },

                "{suggest} removeItem": function(el, event, menu) {
                    
                    // When an item is removed, remove it from the exclusion list
                    var isExcluded = $.inArray(menu.id.toString(), opts.exclusion) > -1;

                    if (isExcluded) {
                        opts.exclusion.splice(opts.exclusion.indexOf(menu.id.toString()), 1);
                    }
                },

                "{suggest} filterMenu": function(el, event, menu, menuItems, autocomplete, textboxlist) {

                    // Get list of items that are already added into the bucket
                    var selected = textboxlist.getAddedItems();
                    var selected = $.pluck(selected, "id");

                    // Add the items into the 
                    var exclude = selected.concat(opts.exclusion);

                    menuItems.each(function(){

                        var menuItem = $(this);
                        var item = menuItem.data("item");

                        var isSelected = $.inArray(item.id.toString(), exclude) > -1;
                        menuItem.toggleClass('hidden', isSelected);
                        
                    });
                }
            }
        });

        module.resolve();

    });
});
