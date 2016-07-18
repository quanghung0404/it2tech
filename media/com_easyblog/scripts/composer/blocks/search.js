EasyBlog.module("composer/blocks/search", function($){

var module = this;

var isSearching = "is-searching";
var isEmpty = "is-empty";

EasyBlog.Controller("Composer.Blocks.Search",
{
    elements: [
        "[data-eb-blocks-{search-input|search-toggle-button|search-panel}]"
    ],

    defaultOptions: {
    }
},
function(self, opts, base, composer, blocks) { return {

    init: function() {

        blocks = self.blocks;
        composer = blocks.composer;
    },

    toggleSearch: function() {

        blocks.blocks().hasClass(isSearching) ?
            self.deactivateSearch() :
            self.activateSearch();
    },

    activateSearch: function() {

        blocks.view()
            .addClass(isSearching)

        self.searchInput()
            .val("")
            .focus();
    },

    deactivateSearch: function() {

        blocks.view()
            .removeClass(isSearching);

        self.resetSearch();
    },

    resetSearch: function() {

        blocks.view()
            .removeClass(isEmpty);

        self.searchInput().val("");

        // Show all menu items
        blocks.menu().show();

        // Quick hack to show back all menu group
        blocks.menuGroup()
            .each(function(){
                $(this).parents(".eb-composer-fieldset").show();
            });
    },

    search: function(keyword) {

        var keyword   = $.trim(keyword.toLowerCase()),
            menus     = blocks.menu(),
            results   = menus.filter("[data-keywords*='" + keyword + "']"),
            noKeyword = keyword=="",
            noResults = !noKeyword && results.length < 1;

        // If no keyword given, show all results
        noKeyword ?
            menus.show():
            menus.hide();

        // Show results
        results.show();

        blocks.view()
            .toggleClass(isEmpty, noResults);

        // Quick hack to hide menu group with no results
        blocks.menuGroup()
            .each(function(){

                var menuGroup = $(this),
                    fieldset = menuGroup.parents(".eb-composer-fieldset").show();

                if (menuGroup.height() < 1) {
                    fieldset.hide();
                }
            });
    },

    "{searchToggleButton} click": function(button) {
        self.toggleSearch();
    },

    "{searchInput} keyup": function(searchInput, event) {

        var keyword = searchInput.val();

        // Escape
        if (event.keyCode===27) {

            // When user hits escape for the first time, clear search input.
            // When user hits escape for the second time, hide search bar.
            if (keyword=="") {
                self.deactivateSearch();
            } else {
                self.resetSearch();
            }
        }
    },

    "{searchInput} input": $.debounce(function(searchInput, event) {

        self.search(searchInput.val());

    }, 150)

}});

module.resolve();

});
