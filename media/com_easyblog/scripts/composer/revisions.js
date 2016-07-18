EasyBlog.module("composer/revisions", function($){

    var module = this;

    EasyBlog.Controller("Composer.Revisions", {

        defaultOptions: {

            "{revisionsFieldset}": "[data-eb-revisions-fieldset]",

            "{revisionToggle}"  : "[data-eb-revisions-dropdown-toggle]",
            "{revisionDropdown}"  : "[data-eb-pilot-dropdown]",
            "{revisionHandler}"  : "[data-eb-revisions-handler]",
            "{revisionList}": "[data-eb-revisions-list]",

            "{closeComparison}": "[data-revisions-close-comparison]",
            "{compareScreen}": "[data-eb-composer-revisions-compare-screen]",

            // Revision items
            "{item}": "[data-eb-composer-revisions-item]",
            "{compareRevision}": "[data-eb-composer-revisions-compare]",
            "{openRevision}": "[data-eb-composer-revisions-open]",
            "{useRevision}": "[data-eb-composer-revisions-use]",
            "{deleteRevision}": "[data-eb-composer-revisions-delete]",

            // revisions blocks
            "{revisionBlocks}" : ".eb-composer-revisions .ebd-block"
        }
    }, function(self, opts, base, composer, blocks, panels) { return {

        init: function() {
            composer = self.composer;
            panels = composer.panels;
            blocks = composer.blocks;

            self.preventParentScrolling();
        },

        preventParentScrolling: function() {

            self.revisionList()
                .on("mousewheel", function(event){
                    event.stopPropagation();
                });
        },

        "{item} click": function(item) {

            var hasActiveClass = item.hasClass("active");

            if (item.hasClass("is-current")) {
                return;
            }

            self.item().removeClass("active");

            item.toggleClass("active", !hasActiveClass);
        },

        revisionsLoaded: false,

        getRevisionItem: function(el) {
            var item = $(el).parents(self.item.selector);

            return item;
        },

        "{revisionBlocks} mouseover": function (el, ev) {

            var uid = $(el).data('uid');
            var block = blocks.getBlock(uid);

            blocks.highlight(block);
        },

        "{revisionBlocks} mouseout": function (el, ev) {

            var uid = $(el).data('uid');
            var block = blocks.getBlock(uid);

            blocks.unhighlight(block);
        },

        "{closeComparison} click": function(el, event) {

            composer.views.show("document");

            self.compareScreen().html('');
        },

        "{compareRevision} click": function(el, event) {

            composer.views.show("revisions");

            var item = self.getRevisionItem(el),
                targetRevision = $(item).data('id'),
                currentRevision = EasyBlog.Composer.getRevisionId();

            EasyBlog.ajax('site/views/revisions/compare', {
                "current": currentRevision,
                "target": targetRevision
            }).done(function(output){
                self.compareScreen().html(output);
            });
        },

        "{useRevision} click": function(el, event) {

            var item = self.getRevisionItem(el),
                id = $(item).data('id');

                EasyBlog.dialog({
                    content: EasyBlog.ajax('site/views/revisions/confirmUseRevision', {"uid": EasyBlog.Composer.getPostId() + '.' + id })
                });
        },

        "{deleteRevision} click": function(el, event) {
            var item = $(el).parents(self.item.selector),
                id = $(item).data('id');

            EasyBlog.dialog({
                content: EasyBlog.ajax('site/views/revisions/deleteRevision', {"id": id}),
                bindings: {

                    "{submitButton} click": function() {

                        EasyBlog.ajax('site/controllers/posts/deleteRevision', {
                            "id": id
                        }).done(function(){
                            // Remove the item from the list.
                            $(item).remove();

                            // Close the dialog
                            EasyBlog.dialog().close();
                        });
                    }
                }
            });
        },

        "{revisionToggle} click": function(element) {

            self.revisionsFieldset()
                .toggleClass("show-revision-list");


                EasyBlog.ajax('site/views/revisions/getRevisions', {
                    "uid" : EasyBlog.Composer.getPostUid()
                }).done(function(output) {
                    self.revisionsLoaded = true;
                    // TODO: add class is-loading
                    // self.revisionLoader().addClass('hide');

                    self.revisionList().html(output);
                });
        },

        "{composer} composerSaveSuccess": function() {

            // We want to re-initialize the listing again.
            self.revisionsLoaded = false;
        }
    }});

    module.resolve();

});
