EasyBlog.module("composer/media", function($) {

var module = this;

EasyBlog.require()
.script("mediamanager")
.done(function(){

    EasyBlog.Controller("Composer.Media", {

        defaultOptions: {
        }
    }, function(self, opts, base, composer) { return {

        init: function() {

            composer = self.composer;

            // Load media configuration via ajax
            // TODO: Find an alternative way of passing in media configuration.
            // EasyBlog.ajax("site/views/dashboard/mediaConfiguration")
            //     .done(function(html){
            //         $("body").append(html);
            //     });
        },

        // disabled: true

        // "{composer} sidebarActivate": function(base, event, id) {

        //     if (id!=="media" || self.disabled) return;

        //     EasyBlog.mediaManager.browse();
        // },

        // "{composer} sidebarDeactivate": function(base, event, id) {

        //     if (id!=="media" || self.disabled) return;

        //     EasyBlog.mediaManager.hide();
        // }

    }});

    module.resolve();

});

});
