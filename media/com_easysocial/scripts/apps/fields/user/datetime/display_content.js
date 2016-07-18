EasySocial.module('apps/fields/user/datetime/display_content', function($) {
    var module = this;

    EasySocial.require().library('popbox', 'chosen').language('FIELDS_USER_DATETIME_LOCAL_TIMEZONE').done(function($) {

        EasySocial.Controller('Field.Datetime.Display', {
            defaultOptions: {
                userid: null,

                date: null,

                timezone: null,

                local: null,

                '{toggle}': '[data-popbox]',

                '{content}': '[data-popbox-content]',

                '{date}': '[data-date]',

                '{timezone}': '[data-timezone]',

                '{loading}': '[data-loading]'
            }
        }, function(self) {
            return {
                init: function() {
                    self.options.timezone = self.timezone().data('timezone');

                    self.options.date = self.date().data('date-utc');

                    // Set the selected timezone with the displayed date
                    self.datetime(self.options.timezone, self.date().html());

                    // Get the local timezone first through client browser
                    self.options.local = -new Date().getTimezoneOffset()/60;

                    var content = self.content().html(),
                        position = self.toggle().data('popbox-position');

                    self.toggle().popbox({
                        content: content,
                        id: 'fd',
                        component: 'es',
                        type: 'timezone',
                        toggle: 'click',
                        position: position
                    }).attr('data-popbox', '');
                },

                '{toggle} popboxActivate': function(el, event, popbox) {
                    $(popbox.tooltip).addController('EasySocial.Controller.Field.Datetime.Display.Timezone', {
                        '{parent}': self
                    });
                },

                data: {},

                datetime: function(tz, value) {
                    // Getter
                    if (value === undefined) {
                        var dfd = $.Deferred();

                        if (self.data[tz] === undefined) {
                            self.loading().show();

                            EasySocial.ajax('fields/user/datetime/getDatetime', {
                                id: self.options.id,
                                userid: self.options.userid,
                                tz: tz,
                                local: self.options.local,
                                datetime: self.options.date
                            }).done(function(datetime) {

                                self.loading().hide();

                                dfd.resolve(self.datetime(tz, datetime));
                            });
                        } else {
                            dfd.resolve(self.data[tz]);
                        }

                        return dfd;
                    }

                    // Setter
                    self.data[tz] = value;
                    return value;
                },

                showDatetime: function(tz, datetime) {
                    if (tz === 'local') {
                        tz = $.language('FIELDS_USER_DATETIME_LOCAL_TIMEZONE');
                    }

                    self.timezone().html(tz);
                    self.date().html(datetime);
                }
            }
        });

        EasySocial.Controller('Field.Datetime.Display.Timezone', {
            defaultOptions: {
                '{timezones}': '[data-timezone-select]',
                '{reset}': '[data-timezone-reset]',
                '{local}': '[data-timezone-local]'
            }
        }, function(self) {
            return {
                init: function() {
                    self.timezones().chosen({
                        search_contains: true
                    });
                },

                '{timezones} change': function(el, event) {
                    var key = el.val();

                    self.parent.date().hide();
                    self.parent.timezone().hide();

                    self.parent.datetime(key).done(function(value) {
                        self.parent.showDatetime(key, value);

                        self.parent.date().show();
                        self.parent.timezone().show();
                    });
                },

                '{reset} click': function() {
                    self.setTimezone(self.parent.options.timezone);
                },

                '{local} click': function() {
                    self.setTimezone('local')
                },

                setTimezone: function(tz) {
                    self.timezones()
                        .val(tz)
                        .trigger('liszt:updated')
                        .trigger('change');
                }
            }
        });
        module.resolve();
    });
});
