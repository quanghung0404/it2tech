EasyBlog.module("composer/posts", function($){

var module = this;

EasyBlog.Controller("Composer.Posts", {

    elements: [
        "[data-eb-posts-{close-button}]",
        "[data-eb-composer-{posts}]",
        "[data-eb-composer-posts-{search-toggle-button|search-cancel-button}]"
    ],

    defaultOptions: {

        // Search items
        "{searchPanel}": "[data-eb-composer-sidebar-search-panel]",
        "{hideSearch}": "[data-eb-composer-sidebar-search-cancel-button]",

        // Post items
        "{wrapper}": "[data-eb-composer-posts]",
        "{result}": "[data-eb-composer-posts-result]",

        "{searchTextfield}": "[data-eb-composer-posts-search-textfield]",
        "{openPostButton}": "[data-eb-composer-open-post]",
        "{insertLinkButton}": "[data-eb-composer-insert-link]",
        "{openMediaButton}": "[data-eb-composer-open-media]",

        "{postTitle}": "[data-eb-composer-posts-item-title]"
    }
}, function(self, opts, base, composer) { return {

    loaded: false,
    initialResult: null,

    init: function() {
        // Globals
        composer = self.composer;
        blocks = composer.blocks;

        // Render the post block initially.
        blocks.loadBlockHandler('post');
    },

    "{closeButton} click": function() {
        self.hideViewport();
    },

    "{searchTextfield} keydown": $.debounce(function(textbox){

        var keyword = $.trim(textbox.val());

        if (keyword.length < 1) {
            self.wrapper().removeClass("searching");
            return;
        }

        // Reset the states to searching
        self.wrapper().removeClass('empty');
        self.wrapper().addClass("searching");

        EasyBlog.ajax("site/views/search/search", {query: keyword})
            .done(function(html) {

                // Remove the searching class
                self.wrapper().removeClass("searching");

                if (html.length == 0) {
                    self.wrapper().addClass('empty');
                    return;
                }

                // Append the result
                self.result().append(html);
            })
            .fail(function() {

            });

    }, 500),

    insertPostLink: function() {
        //
        // EasyBlog.Composer.insertContent(link);
    },

    hideViewport: function() {
        composer.views.hide("posts");
    },

    "{searchToggleButton} click": function(el, event) {
        self.posts().addClass('is-searching');
    },

    "{searchCancelButton} click": function(cancelButton, event) {
        self.posts().removeClass('is-searching');
    },

    "{toggleSearch} click": function(el, event) {
        self.searchPanel().toggleClass('active');
    },

    "{hideSearch} click": function(el, event) {

        // Reset the states
        self.searchPanel().toggleClass('active');
        self.wrapper().removeClass('empty');

        // Reset the textbox
        self.searchTextfield().val('');

        // Restore the initial result when search is cancelled.
        self.result().html(self.initialResult);
    },

    "{insertLinkButton} click": function(el, event) {

        // Determines if this is on the legacy editor or not
        var doctype = EasyBlog.Composer.getDoctype();
        var title = el.data('title');
        var url = el.data('permalink');
        var image = el.data('image');
        var content = el.data('content');

        if (doctype == 'ebd') {

            // Construct a new post block and insert into the document
            var block = blocks.constructBlock('post', {
                'title': title,
                'url': url,
                'image': image,
                'intro': content
            });

            blocks.addBlock(block);

        } else {
            // Perform normal insertion
            link = $.create("a").attr({
                        href: url, title: title
                    }).html(title);

            // console.log(EasyBlog.Composer);

            // EasyBlog.Composer.insertContent(link.toHTML());
            EasyBlog.LegacyEditor.insert(link.toHTML());
        }

        // Hide the viewport now
        self.hideViewport();
    },

    "{postTitle} click": function(el, event) {

        var title = $(el).data('title'),
            value = $(el).data('permalink');

        self.insert(value, title);
    },

    "{composer} composerViewShow": function(base, event, id) {

        if (id !== "posts" || self.loaded) {
            return;
        }

        // Add loading indicator
        self.posts().addClass('is-loading');

        EasyBlog.ajax("site/views/composer/listArticles",{
            "exclude": composer.getPostId()
        }).done(function(html) {

            // Remove loading
            self.posts().removeClass('is-loading');

            self.loaded = true;

            if (html.length == 0) {
                self.posts().addClass('is-empty');
                return;
            }

            // Add is ready class
            self.posts().addClass('is-ready');

            // Append the result
            self.initialResult = html;

            // Append the html codes on the result
            self.result().append(html);
        })
        .fail(function(){

        });
    },

    "{composer} sidebarDeactivate": function(base, event, id) {

        if (id!=="posts") {
            return;
        }
    }
}});

module.resolve();

});
