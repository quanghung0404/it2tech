EasyBlog.module("composer/panels/autopost", function($){

    var module = this;

    EasyBlog.Controller("Composer.Panels.AutoPost", {
        defaultOptions: {

            "{twitterCheckbox}": "[data-autopost-twitter]",
            "{facebookCheckbox}": "[data-autopost-facebook]",
            "{linkedinCheckbox}": "[data-autopost-linkedin]"
        }
    }, function(self, opts, base) { 

        return {
            init: function()
            {
            },

            itemUpdated: function(checkbox)
            {
                var checked = $(checkbox).is(':checked');

                if (checked) {
                    $(checkbox).parent().addClass('checked');

                    return;
                }

                $(checkbox).parent().removeClass('checked');
            },

            "{twitterCheckbox} change": function(el, event)
            {
                self.itemUpdated(el);
            },
            "{facebookCheckbox} change": function(el, event)
            {
                self.itemUpdated(el);
            },
            "{linkedinCheckbox} change": function(el, event)
            {
                self.itemUpdated(el);
            }
        }
    });
    
    module.resolve();

});
