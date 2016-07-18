EasyBlog.module("composer/document/artboard", function($){

var module = this;

EasyBlog.Controller("Composer.Document.Artboard",
{
    defaultOptions: {
        "{container}": ".eb-composer-artboard",
        "{viewport}": ".eb-composer-artboard-viewport",
        "{art}": "[data-eb-composer-art]",
        "{metaButton}": "[data-eb-composer-meta-button]"
    }
},
function(self, opts, base) { return {

    init: function() {
    },

    current: function() {
        return self.art(".active").data("id");
    },

    show: function(id) {

        // Get art from given id or current id
        var id = id || self.current();

        // Activate container
        self.container()
            .switchClass("show-" + id)
            .switchClass("state-expand")
            .addClassAfter("active", 1);

        // Activate art
        self.art()
            .removeClass("active")
            .where("id", id)
            .addClass("active");

        // Activate meta button
        self.metaButton()
            .removeClass("active")
            .where("id", id)
            .addClass("active");

        self.trigger("composerArtboardShow", [id]);
    },

    hide: function(id) {

        // Get art from given id or current id
        var id = id || self.current();

        // Deactivate container
        self.container()
            .toggleClass("active", !!self.art(".has-image").length)
            .switchClass("state-collapse");

        // Deactivate art
        self.art()
            .removeClass("active");

        // Activate meta button
        self.metaButton()
            .removeClass("active");

        self.trigger("composerArtboardHide", [id]);
    },

    "{metaButton} click": function(metaButton) {
        var id = metaButton.data("id");
        self[self.current()===id ? "hide" : "show"](id);
    }

}});

module.resolve();

});
