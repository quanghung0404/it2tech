EasyBlog.module("composer/panels/category", function($){

    var module = this;

    EasyBlog.require()
    .done(function($) {

        EasyBlog.Controller("Composer.Panels.Category", {
            defaultOptions: {
                fields: null,
                templates: {},

                // Templates
                "{template}": "[data-category-primary-template]",

                // Panel
                "{fieldsTab}": "[data-eb-composer-panel-tab][data-id=fields]",
                "{fieldsPanel}": "[data-eb-composer-panel][data-id=fields]",
                "{fieldsPanelForm}": "[data-eb-composer-panel][data-id=fields] [data-eb-composer-form]",
                "{fieldsForm}": "[data-eb-composer-panel-fields]",
                "{empty}": "[data-eb-composer-fields-empty]",

                // Categories
                "{categories}": "[data-category-primary-items]",
                "{category}": "[data-category-primary-item]",
                "{categoryText}": "[data-title-text]",

                // Primary category selection
                "{primaryCategoryInput}": "[data-category-primary-input]",
                "{primaryCategoryTitle}": "[data-category-primary-title]"
            }
        },
        function(self, opts, base, composer) { return {

            init: function() {
                // Get the primary category template
                opts.templates['primaryCategory'] = self.template().detach().html();

                composer = EasyBlog.Composer;
            },

            getTemplate: function(type) {
                var item = $(opts.templates[type]);

                return item;
            },

            insertCategory: function(category) {

                var item = self.getTemplate('primaryCategory');

                item.attr('data-title', category.title)
                    .attr('data-id', category.id)
                    .find(self.categoryText)
                    .html(category.title);


                // Append the item into the list
                self.categories().append(item);
            },

            removeCategory: function(category) {

                if (self.categories().children().length == 1) {
                    // dont do anything.
                    return;
                }

                self.categories()
                    .children('[data-id="' + category.id + '"]')
                    .remove();

                // now we check if the list remain one last item? if yes, mark that as primary category, OR
                // if the current deselect category is a primary category or not.
                // if yes, we need to pre-select other category
                if (self.categories().children().length == 1 || category.id == self.primaryCategoryInput().val()) {
                    var newprimarycat = $(self.categories().children()[0]);

                    var id = newprimarycat.data("id");
                    var title = newprimarycat.data("title");

                    self.assignPrimaryCategory(id, title);
                }
            },

            assignPrimaryCategory: function(id, title) {
                // Set the input with the proper id
                self.primaryCategoryInput().val(id);

                // Set the title
                self.primaryCategoryTitle().html(title);

                var category = {
                   id: id,
                   title: title
                }

                self.trigger("categorysetprimary", category);

            },

            categoryExists: function(category) {
                var category = self.categories().children('[data-id="' + category.id + '"]');

                return category.length > 0;
            },

            "{category} click": function(category, event) {

                var id = category.data("id");
                var title = category.data("title");

                self.assignPrimaryCategory(id, title);
            },

            "{self} categorydeselect": function(el, event, category) {

                // Find the custom fields form in the fields panel and remove it
                // Since the category has been de-selected already.
                self.fieldsPanel()
                    .find('[data-category-id="' + category.id + '"]')
                    .remove();

                // Display the empty message if there is no fields available
                if (self.fieldsPanel().find('[data-panel-field]').length == 0) {
                    self.empty().show();
                }

                // Remove the primary category child item once a category is de-selected
                self.removeCategory(category);
            },

            "{self} categoryselect": function(el, event, category) {

                // If category does not exist in the list, add it to the list
                if (!self.categoryExists(category)) {
                    self.insertCategory(category);
                }

                // When a category is selected, ensure that we retrieve the custom fields associated with the category
                EasyBlog.ajax("site/views/categories/getCustomFields", {
                    "id": category.id,
                    "postId": EasyBlog.Composer.getPostId()
                }).done(function(form) {

                    if (form.length > 0) {
                        
                        // Ensure that the fields tab is shown
                        var tab = composer.panels.panelTab.get('fields');

                        // Ensure that the tab is shown
                        tab.removeClass('hide');

                        // Hide the empty message in the custom fields area
                        self.empty().hide();

                        // Append the custom fields form into the field panel
                        self.fieldsPanelForm().append(form);
                    }
                });

            },

            "{self} composerSaveError": function(el, event, exception) {
                
                // Show errors on the fields tab
                if (exception.customCode && exception.customCode == -500) {
                    self.fieldsTab().addClass('has-error');
                } else {
                    self.fieldsTab().removeClass('has-error');
                }
            }
        }});

        module.resolve();

    });

});
