EasyBlog.module("composer/datetime", function($) {

var module = this;

EasyBlog.require()
.library(
    "moment",
    "datetimepicker"
)
.done(function(){

EasyBlog.Controller("Post.Datetime", {
    defaultOptions: {
        format: "Do MMM, YYYY HH:mm",
        language: "en",
        originalValue: "",

        "{preview}": "[data-preview]",
        "{calendar}": "[data-calendar]",
        "{cancel}": "[data-cancel]",
        "{datetime}": "[data-datetime]"
    }
}, function(self, opts, base) {

    return {
        init: function() {

            // For the language to load, we also need to load the language js file which is done by the implementer.
            self.calendar()._datetimepicker({
                component: "eb",
                format: opts.format,
                language: opts.language
            });

            self.datetimepicker = self.calendar().data("DateTimePicker");

            // Get the original value from input
            opts.originalValue = self.datetime().val();

            if (!$.isEmpty(opts.originalValue)) {
                self.datetimepicker.setDate($.moment(opts.originalValue));
            }
        },

        "{calendar} dp.change": function(el, ev) {

            // Preview needs to be in their language respectively
            self.preview().text(ev.date.format(opts.format));

            // Set the language to english so we can get an english version of the date
            ev.date.lang('en');

            var val = ev.date.format("YYYY-MM-DD HH:mm:ss");


            // Set the datetime as SQL format
            self.datetime().val(val);

            // Reset back the languge
            ev.date.lang(opts.language);

            var val = ev.date.format("YYYY-MM-DD HH:mm:ss");

            self.toggleCancelButton();
        },

        "{cancel} click": function() {
            var empty = $.isEmpty(opts.originalValue);

            if (empty || opts.originalValue == "0000-00-00 00:00:00") {
                self.preview().text(opts.emptyText);
                self.datetime().val("0000-00-00 00:00:00");
            } else {
                self.datetimepicker.setDate($.moment(opts.originalValue));
            }

            self.toggleCancelButton();
        },

        toggleCancelButton: function() {
            self.cancel()[self.datetime().val() == opts.originalValue ? "hide" : "show"]();
        }
    }
});

module.resolve();

});

});