EasyBlog.module("composer/panels/authorship", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Panels.Authorship", {
        defaultOptions: {

            // Author section
            "{authorArea}": "[data-eb-composer-author]",
            "{currentAuthor}": "[data-eb-composer-current-author]",

            "{searchAssociates}": "[data-associates-search]",
            "{cancelSearchAssociates}": "[data-associates-search-cancel]",

            "{searchAuthor}": "[data-author-search]",
            "{cancelSearchAuthor}": "[data-author-search-cancel]",

            // Tabs
            "{tab}": "[data-author-type]",
            "{tabContent}": "[data-tab-content]",

            // Author stuffs
            "{changeAuthor}": "[data-eb-composer-switch-author]",
            "{authorPicker}": "[data-eb-composer-author-picker]",

            "{authorItem}": "[data-eb-author-item]",
            "{authorList}": "[data-eb-composer-author-list]",
            "{authorCheckbox}": "[data-eb-composer-authoritem]",
            "{activeAuthorId}": "[data-eb-composer-authorid]",
            "{activeAuthorAvatar}": "[data-eb-composer-authoravatar]",
            "{activeAuthorName}": "[data-eb-composer-authorname]",

            // author pagination
            "{authorPrevBtn}": "[data-eb-author-prev]",
            "{authorNextBtn}": "[data-eb-author-next]",

            // Associates
            "{associatesList}": "[data-eb-composer-associates-list]",
            "{associateItem}": "[data-associates-item]",
            "{associateCheckbox}": "[data-associates-checkbox]",
            "{activeAssociateName}" : "[data-eb-composer-associatename]",
            "{activeAssociateId}": "[data-eb-composer-associateid]",
            "{activeAssociateType}": "[data-eb-composer-associatetype]",
            "{activeAssociateAvatar}": "[data-eb-composer-associateavatar]",

        }
    }, function(self, opts, base) {

        return {

            init: function() {
            },

            "{cancelSearchAssociates} click": function() {
                // Show all items
                self.associateItem()
                    .removeClass('hide');

                // Empty the search textbox
                self.searchAssociates().val('');
            },

            "{cancelSearchAuthor} click": function() {

                // Show all items
                self.authorItem()
                    .removeClass('hide');

                var search = $.trim(self.searchAuthor().val());

                // Empty the search textbox
                self.searchAuthor().val('');

                if (search) {
                    self.loadAuthors(0, '');
                }
            },

            "{searchAssociates} keydown": $.debounce(function(el, event) {
                var value = $.trim(el.val()).toLowerCase();

                if (!value) {
                    self.associateItem()
                        .removeClass('hide');

                    return;
                }

                var associates = self.associateItem().filter(function() {

                    var title = $.trim($(this).data('title')).toLowerCase();

                    if (title.indexOf(value) == 0) {
                        return true;
                    }

                    return false;
                });


                if (associates) {
                    self.associateItem()
                        .addClass('hide');


                    associates.removeClass('hide');
                }

            }, 300),

            // "{searchAuthor} keydown": $.debounce(function(el, event) {
            //     var value = $.trim(el.val()).toLowerCase();

            //     if (!value) {
            //         self.authorItem()
            //             .removeClass('hide');

            //         return;
            //     }

            //     var authors = self.authorItem().filter(function() {

            //         var title = $.trim($(this).data('title')).toLowerCase();

            //         if (title.indexOf(value) == 0) {
            //             return true;
            //         }

            //         return false;
            //     });


            //     if (authors) {
            //         self.authorItem()
            //             .addClass('hide');


            //         authors.removeClass('hide');
            //     }

            // }, 300),

            "{searchAuthor} keydown": $.debounce(function(el, event) {

                var value = $.trim(el.val()).toLowerCase();

                if (value != "") {
                    self.loadAuthors(0, value);
                }

            }, 300),


            associatesLoaded: false,

            "{tab} click": function(el, event) {

                var type = el.data('type');

                // Only associates needs to be rendered initially
                if (type == 'associates') {
                    self.loadAssociates();
                }

                // Set the active tab
                self.tab().removeClass('active');
                self.tab().where('type', type).addClass('active');

                // Update the tab content
                self.tabContent().removeClass('active');
                self.tabContent()
                    .where('type', type)
                    .addClass('active');
            },

            "{changeAuthor} click": function(element, event) {

                // Pull down the author form.
                self.authorArea().toggleClass('is-opened');

                self.authorPicker().toggleClass('pulled');

                // There could be possibility that the user only sees the "Team" tab
                // if they do not have access to switch authors.
                if (self.tab().length == 1) {
                    self.loadAssociates();
                    return;
                }

                // Perform an ajax call to retrieve a list of authors.
                // EasyBlog.ajax('site/views/composer/listAuthors', {
                //     'selected': self.activeAuthorId().val()
                // })
                // .done(function(output){

                //     // Append to the author list
                //     self.authorList().html(output);

                // });
                self.loadAuthors('0', '');

            },

            "{authorPrevBtn} click": function(el, event) {
                event.preventDefault();

                var search = $.trim(self.searchAuthor().val()).toLowerCase();
                var start = $(el).data('start');
                start = parseInt(start);

                if (start < 0) {
                    start = 0;
                }

                self.loadAuthors(start, search);
            },

            "{authorNextBtn} click": function(el, event) {
                event.preventDefault();

                var start = $(el).data('start');
                var search = $.trim(self.searchAuthor().val()).toLowerCase();

                self.loadAuthors(start, search);
            },


            loadAuthors: function(start, search) {

                //loading
                var loadingDiv = $('<div />', {
                    class: "loading-authors"
                });
                loadingDiv.html('<i class="fa fa-circle-o-notch fa-spin"></i>');
                self.authorList().html(loadingDiv);

                // Perform an ajax call to retrieve a list of authors.
                EasyBlog.ajax('site/views/composer/listAuthors', {
                    'selected': self.activeAuthorId().val(),
                    'limitstart': start,
                    'search': search
                })
                .done(function(output){
                    // Append to the author list
                    self.authorList().html(output);
                });

            },


            loadAssociates: function() {

                // If the list of item has already been loaded, skip this
                if (self.associatesLoaded) {
                    return;
                }

                EasyBlog.ajax('site/views/composer/listAssociates', {
                    source_id: self.activeAssociateId().val(),
                    source_type: self.activeAssociateType().val()
                }).done(function(output) {
                    self.associatesLoaded = true;

                    self.associatesList()
                        .html(output);
                });
            },

            // Trigger for selected author radio
            "{authorCheckbox} change": function(element, event) {
                var avatar = element.parents(self.authorItem.selector).data('avatar');
                var id = element.parents(self.authorItem.selector).data('id');
                var name = element.parents(self.authorItem.selector).data('title');

                // Update the active author
                self.setActiveAuthor(id, name, avatar);
            },

            "{associateCheckbox} change": function(element, event) {

                var avatar = element.parents(self.associateItem.selector).data('avatar');
                var id = element.parents(self.associateItem.selector).data('id');
                var title = element.parents(self.associateItem.selector).data('title');
                var type = element.parents(self.associateItem.selector).data('type');

                // If user tries to select easyblog.sitewide, we shouldn't update anything
                if (type == 'easyblog.sitewide') {
                    self.removeAssociate();
                    return;
                }

                // Update the active associate item
                self.setActiveAssociate(id, type, title, avatar);
            },

            removeAssociate: function() {
                self.activeAssociateId().val(0);
                self.activeAssociateType().val('easyblog.sitewide');

                self.activeAssociateName()
                    .addClass('hidden');
                self.activeAssociateAvatar()
                    .addClass('hide');
            },

            setActiveAssociate: function(id, type, title, avatar) {
                self.activeAssociateId().val(id);

                // Update the team name
                self.activeAssociateName()
                    .html(title)
                    .removeClass('hidden');
                self.activeAssociateType().val(type);

                // Update the team avatar
                self.activeAssociateAvatar()
                    .attr('src', avatar)
                    .removeClass('hide');
            },

            setActiveAuthor: function(id, name, avatar) {
                self.activeAuthorId().val(id);
                self.activeAuthorName().html(name);
                self.activeAuthorAvatar().attr('src', avatar);

                // Display the "You are" section if the selection is not the same
                if (composer.options.authorId != id) {
                    self.currentAuthor().removeClass('hide');
                } else {
                    self.currentAuthor().addClass('hide');
                }

            },

            "{self} composerValidate": function(composer, event, validator) {
            }
        }
    });

    module.resolve();
});
