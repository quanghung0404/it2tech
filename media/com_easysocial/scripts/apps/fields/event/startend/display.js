EasySocial.module('apps/fields/event/startend/display', function($) {
    var module = this;

    EasySocial
    .require()
    .library('chosen', 'popbox')
    .language('FIELDS_USER_DATETIME_LOCAL_TIMEZONE', 'FIELDS_USER_DATETIME_TIMEZONE_CHECKING')
    .done(function($) {
        EasySocial.Controller('Field.Event.Startend.Display', {
            defaultOptions: {
                id: null,

                userid: null,

                '{box}': '[data-startend-box]'
            }
        }, function(self) {
            return {
                init: function() {
                    self.box().addController('EasySocial.Controller.Field.Event.Startend.Display.Box', {
                        '{parent}': self
                    });
                }
            }
        });

        EasySocial.Controller('Field.Event.Startend.Display.Box', {
            defaultOptions: {
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
                    popbox.tooltip.addController('EasySocial.Controller.Field.Event.Startend.Display.Timezone', {
                        '{parent}': self
                    });
                },

                datetime: $.memoize(function(tz) {
                    return EasySocial.ajax('fields/event/startend/getDatetime', {
                        id: self.parent.options.id,
                        userid: self.parent.options.userid,
                        tz: tz,
                        local: self.options.local,
                        datetime: self.options.date
                    });
                })
            }
        });

        EasySocial.Controller('Field.Event.Startend.Display.Timezone', {
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

                    self.parent.date().html($.language('FIELDS_USER_DATETIME_TIMEZONE_CHECKING'));
                    self.parent.timezone().html(key === 'local' ? $.language('FIELDS_USER_DATETIME_LOCAL_TIMEZONE') : key);

                    self.parent.datetime(key).done(function(value) {
                        self.parent.date().html(value);
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
