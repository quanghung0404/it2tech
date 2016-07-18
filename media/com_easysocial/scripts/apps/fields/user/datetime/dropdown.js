EasySocial.module('apps/fields/user/datetime/dropdown', function($) {
    var module = this;

    EasySocial.require().language('PLG_FIELDS_DATETIME_DAY').done(function() {
        EasySocial.Controller('Field.Datetime.Dropdown', {
            defaultOptions: {
                required: false,
                allowTime: false,
                allowTimezone: false,
                yearfrom: null,
                yearto: null,

                '{dateValue}': '[data-field-datetime-value]',

                '{year}': '[data-field-datetime-year]',
                '{month}': '[data-field-datetime-month]',
                '{day}': '[data-field-datetime-day]',

                '{hour}': '[data-field-datetime-hour]',
                '{minute}': '[data-field-datetime-minute]',
                '{ampm}': '[data-field-datetime-ampm]'
            }
        }, function(self) {
            return {
                init: function()
                {

                },

                '{year} change': function(el, ev)
                {
                    self.setValue();
                },

                '{month} change': function(el, ev)
                {
                    // If year and month is provided, then we need to find the max day
                    var year = self.year().val(),
                        month = self.month().val();

                    if (year !== '' && month !== '') {
                        var maxDay = new Date(year, month, 0).getDate();

                        // See if there are days originally selected
                        var day = self.day().val();

                        if (day !== '') {
                            // If day value is more than current month maxday, then we use maxday
                            day = Math.min(day, maxDay);
                        }

                        self.day().empty();

                        self.day().append($('<option value="">' + $.language('PLG_FIELDS_DATETIME_DAY') + '</option>'));

                        for (i = 1; i <= maxDay; i++) {
                            $('<option value="' + i + '">' + i + '</option>').appendTo(self.day());
                        }

                        // Set back the original value
                        if (day !== '') {
                            self.day().val(day);
                        }
                    }

                    self.setValue();
                },

                '{day} change': function(el, ev)
                {
                    self.setValue();
                },

                '{hour} change': function(el, ev)
                {
                    self.setValue();
                },

                '{minute} change': function(el, ev)
                {
                    self.setValue();
                },

                '{ampm} change': function(el, ev)
                {
                    self.setValue();
                },

                setValue: function()
                {
                    var string;

                    var year = self.year().val(),
                        month = self.month().val(),
                        day = self.day().val();

                    if (year !== '' && month !== '' && day !== '') {
                        string = year + '-' + month + '-' + day;

                        if (self.options.allowTime) {
                            var hour = self.hour().val(),
                                minute = self.minute().val();

                            // If there is ampm, then we need to readjust the time a little bit
                            if (hour !== '' && self.ampm().length > 0 && self.ampm().val() == 'pm') {
                                hour = (parseInt(hour) + 12).toString();

                                if (hour === '24') {
                                    hour = '0';
                                }
                            }

                            if (minute === '') {
                                minute = '00';
                            }

                            string += ' ' + ('00' + hour).slice(-2) + ':' + ('00' + minute).slice(-2) + ':00';
                        }

                        self.dateValue().val(string);
                    }
                }
            }
        });

        module.resolve();
    });
});
