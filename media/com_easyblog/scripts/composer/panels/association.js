EasyBlog.module("composer/panels/association", function($){

    var module = this;

    EasyBlog.require()
    .done(function($) {

        EasyBlog.Controller("Composer.Panels.Association", {
            defaultOptions: {

                // "{twitterCheckbox}": "[data-autopost-twitter]",
                // "{facebookCheckbox}": "[data-autopost-facebook]",
                // "{linkedinCheckbox}": "[data-autopost-linkedin]"

                "{selectButton}": "[data-assoc-select]",
                "{clearButton}": "[data-assoc-clear]",
            }
        }, function(self, opts, base) {

            return {
                init: function()
                {
                    //
                },

                "{clearButton} click": function(el, ev) {
                    var langid = $(el).data("id");

                    $('input#assoc-postname' + langid).val('');
                    $('input#assoc-postid' + langid).val('');
                }
            }
        });

        module.resolve();

    });

});
