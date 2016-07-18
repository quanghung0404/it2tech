EasySocial.module('apps/fields/user/datetime/content', function($) {
    var module = this;

    EasySocial.require()
    .library('datetimepicker', 'chosen', 'moment/' + EasySocial.options.momentLang)
    .language(
        'PLG_FIELDS_DATETIME_VALIDATION_INVALID_DATETIME_FORMAT',
        'PLG_FIELDS_DATETIME_VALIDATION_PLEASE_SELECT_DATETIME'
        )
    .done(function($){

        EasySocial.Controller(
            'Field.Datetime',
            {
                defaultOptions:
                {
                    required: false,

                    calendarDateFormat: null,

                    yearfrom: null,

                    yearto: null,

                    date: null,

                    lang: null,

                    allowTime: false,
                    allowTimezone: false,

                    calendarLanguage: 'english',

                    '{field}': '[data-field-datetime]',

                    '{date}': '[data-field-datetime-select]',

                    '{dateValue}': '[data-field-datetime-value]',

                    '{timezone}': '[date-field-datetime-timezone]',

                    '{form}': '[data-field-datetime-form]',

                    '{icon}': '[data-field-datetime-icon]',

                    '{clearButton}': '[data-clear]',
                }
            },
            function( self )
            {
                return {
                    init : function() {
                        // self.legacyInit();

                        self.options.yearfrom = self.options.yearfrom || 1930;

                        // There is an issue with yearto where if I set yearto = 2014, I won't be able to select 2014 dates. This is a bug in datetimepicker. Currently, temporarily, we manually add 1 to the value if there are value set.
                        if (!$.isEmpty(self.options.yearto)) {
                            self.options.yearto = parseInt(self.options.yearto) + 1;
                        } else {
                            self.options.yearto = new Date().getFullYear() + 100
                        }

                        self.date()._datetimepicker({
                            component: "es",
                            format: self.options.calendarDateFormat,
                            minDate: self.options.yearfrom + '-01-01',
                            maxDate: self.options.yearto + '-12-31',
                            icons: {
                                time: 'glyphicon glyphicon-time',
                                date: 'glyphicon glyphicon-calendar',
                                up: 'glyphicon glyphicon-chevron-up',
                                down: 'glyphicon glyphicon-chevron-down'
                            },
                            sideBySide: false,
                            pickTime: self.options.allowTime,
                            useCurrent: false,
                            language: self.options.calendarLanguage == 'english' ? 'en-gb' : EasySocial.options.momentLang
                        });

                        // date value should always be in mysql datetime format
                        // YYYY-MM-DD HH:MM:SS
                        self.options.date = self.dateValue().val();

                        // If there is a date value, then we set it into the datetimepicker
                        if (!$.isEmpty(self.options.date)) {
                            // Datetimepicker is using moment.js, hence here we manually create a moment object to pass in instead of passing in date time string
                            // This is because datetimepicker.setDate function passes along the format from self.options.calendarDateFormat to generate the date object, which will render moment.js to generate an invalid dateobject
                            // self.options.calendarDateFormat is only for display purposes
                            // Raw date object is always in SQL format
                            var dateObj = $.moment(self.options.date);

                            self.datetimepicker('setDate', dateObj);
                            // self.setDateValue(dateObj);
                        }

                        if (self.options.allowTimezone) {
                            self.timezone().chosen({
                                search_contains: true
                            });
                        }
                    },

                    '{icon} click': function() {
                        self.datetimepicker('show');
                    },

                    '{date} dp.change': function(el, ev) {
                        self.setDateValue(ev.date.toDate());

                        self.form().addClass('has-datetime');

                        // Custom hack to ensure that the input box is really blurred
                        if (!self.options.allowTime) {
                            self.date().blur();
                        }
                    },

                    // Alias method to call the datetimepicker instance
                    datetimepicker: function(method, value) {
                        return self.date().data('DateTimePicker')[method](value);
                    },

                    setDateValue: function(date) {
                        // Convert the date object into sql format and set it into the input
                        self.dateValue().val(date.getFullYear() + '-' +
                                            ('00' + (date.getMonth()+1)).slice(-2) + '-' +
                                            ('00' + date.getDate()).slice(-2) + ' ' +
                                            ('00' + date.getHours()).slice(-2) + ':' +
                                            ('00' + date.getMinutes()).slice(-2) + ':' +
                                            ('00' + date.getSeconds()).slice(-2));
                    },

                    '{date} blur': function() {
                        self.validateCalendar();
                    },

                    validateCalendar: function() {
                        self.clearError();

                        if(self.options.required && $.isEmpty(self.dateValue().val())) {
                            self.raiseError($.language('PLG_FIELDS_DATETIME_VALIDATION_PLEASE_SELECT_DATETIME'));
                            return false;
                        }

                        return true;
                    },

                    raiseError: function(msg) {
                        self.trigger('error', [msg]);
                    },

                    clearError: function() {
                        self.trigger('clear');
                    },

                    "{self} onSubmit" : function(el, event, register) {
                        register.push(self.validateCalendar());
                        return;

                    },

                    '{clearButton} click': function(el, ev) {
                        self.form().removeClass('has-datetime');

                        self.datetimepicker('setValue', new $.moment());

                        self.date().val('');

                        self.dateValue().val('');
                    }
                }
            });

        module.resolve();
    });
});
