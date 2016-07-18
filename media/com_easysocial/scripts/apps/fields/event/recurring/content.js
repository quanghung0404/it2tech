EasySocial.module('apps/fields/event/recurring/content', function($) {
    var module = this;

    EasySocial.require().library('datetimepicker').done(function() {

        EasySocial.Controller('Field.Event.Recurring', {
            defaultOptions: {
                id: null,

                value: {},

                allday: 0,

                showWarningMessages: 0,

                eventId: null,

                '{type}': '[data-recurring-type]',

                '{endBlock}': '[data-recurring-end-block]',

                '{picker}': '[data-recurring-end-picker]',

                '{toggle}': '[data-recurring-end-toggle]',

                '{result}': '[data-recurring-end-result]',

                '{dailyBlock}': '[data-recurring-daily-block]',

                '{dailyInput}': '[data-recurring-daily-block] input',

                '{summaryBlock}': '[data-recurring-summary-block]',

                '{scheduleToggle}': '[data-recurring-schedule-toggle]',

                '{scheduleBlock}': '[data-recurring-schedule-block]',

                '{scheduleLoadingBlock}': '[data-recurring-schedule-loading-block]',

                '{deleteRecurringButton}': '[data-recurring-delete]'
            }
        }, function(self) {
            return {
                init: function() {
                    self.picker()._datetimepicker({
                        pickTime: false,
                        component: "es",
                        useCurrent: false
                    });

                    var value = self.result().val();

                    if (!$.isEmpty(value)) {
                        var dateObj = $.moment(value);

                        self.datetimepicker('setDate', dateObj);
                    }

                    self.calculateTotalRecur();
                },

                changed: 0,

                '{window} easysocial.fields.allday.change': function(el, ev, value) {
                    self.options.allday = value;

                    self.calculateTotalRecur();
                },

                '{window} easysocial.fields.startend.start.change': function(el, ev, date) {
                    self.calculateTotalRecur();
                },

                '{toggle} click': function() {
                    self.picker().focus();
                },

                '{picker} dp.change': function(el, ev) {
                    self.setDateValue(ev.date.toDate());

                    self.detectChanges();

                    self.calculateTotalRecur();
                },

                '{type} change': function(el, ev) {
                    var value = el.val();

                    self.endBlock()[value === 'none' ? 'hide' : 'show']();

                    self.dailyBlock()[value === 'daily' ? 'show': 'hide']();

                    self.detectChanges();

                    self.calculateTotalRecur();
                },

                '{dailyInput} change': function(el, ev) {
                    self.detectChanges();

                    self.calculateTotalRecur();
                },

                calculateTotalRecur: function() {
                    self.summaryBlock().hide();

                    self.clearError();

                    var start = $('[data-event-start]').find('[data-datetime]').val(),
                        timezone = $('[data-event-timezone]').val(),
                        end = self.result().val(),
                        type = self.type().val(),
                        daily = [];

                    if (type == 'none' && !self.options.showWarningMessages) {
                        return;
                    }

                    if ($.isEmpty(start) || $.isEmpty(end) || $.isEmpty(type)) {
                        return;
                    }

                    $.each(self.dailyBlock().find('input'), function(i, input) {
                        el = $(input);
                        if (el.is(':checked')) {
                            daily.push(el.val());
                        }
                    });

                    self.scheduleLoadingBlock().show();

                    self.getTotalRecur({
                        start: start,
                        timezone: timezone,
                        end: end,
                        type: type,
                        daily: daily
                    });
                },

                getTotalRecur: $.debounce(function(options) {
                    self.clearError();

                    EasySocial.ajax('fields/event/recurring/calculateTotalRecur', {
                        id: self.options.id,
                        start: options.start,
                        timezone: options.timezone,
                        allday: self.options.allday,
                        end: options.end,
                        type: options.type,
                        daily: options.daily,
                        eventId: self.options.eventId,
                        changed: self.changed,
                        showWarningMessages: self.options.showWarningMessages
                    }).done(function(html) {
                        self.summaryBlock().html(html).show();
                    }).fail(function(msg) {
                        self.raiseError(msg);
                    }).always(function() {
                        self.scheduleLoadingBlock().hide();
                    });
                }, 500),

                detectChanges: function() {
                    var end = self.result().val(),
                        type = self.type().val(),
                        daily = [],
                        changed = false;

                    $.each(self.dailyBlock().find('input'), function(i, input) {
                        el = $(input);
                        if (el.is(':checked')) {
                            daily.push(el.val());
                        }
                    });

                    if (type != self.options.value.type || end != self.options.value.end || daily.length != self.options.value.daily.length) {
                        changed = true;
                    }

                    $.each(daily, function(i, d) {
                        if ($.inArray(d, self.options.value.daily) == -1) {
                            changed = true;
                            return false;
                        }
                    });

                    $.each(self.options.value.daily, function(i, d) {
                        if ($.inArray(d, daily) == -1) {
                            changed = true;
                            return false;
                        }
                    });

                    self.changed = changed ? 1 : 0;

                    $(window).trigger('easysocial.fields.recurring.changed', [changed]);
                },

                '{scheduleToggle} click': function(el, ev) {
                    self.scheduleBlock().toggle();
                },

                '{deleteRecurringButton} click': function(el, ev) {
                    EasySocial.dialog({
                        content: EasySocial.ajax('site/views/events/deleteRecurringDialog', {
                            id: self.options.eventId
                        }),
                        bindings: {
                            "{submitButton} click": function()
                            {
                                var dialog = this.parent;

                                dialog.loading(true);

                                self.deleteRecurring()
                                    .done(function() {
                                        dialog.loading(false);

                                        dialog.close();

                                        self.calculateTotalRecur();
                                    });
                            }
                        }
                    })
                },

                deleteRecurring: function() {
                    return EasySocial.ajax('site/controllers/events/deleteRecurring', {
                        eventId: self.options.eventId
                    })
                },

                datetimepicker: function(name, value) {
                    return self.picker().data('DateTimePicker')[name](value);
                },

                setDateValue: function(date) {
                    // Convert the date object into sql format and set it into the input
                    self.result().val(date.getFullYear() + '-' +
                                        ('00' + (date.getMonth()+1)).slice(-2) + '-' +
                                        ('00' + date.getDate()).slice(-2) + ' ' +
                                        ('00' + date.getHours()).slice(-2) + ':' +
                                        ('00' + date.getMinutes()).slice(-2) + ':' +
                                        ('00' + date.getSeconds()).slice(-2));
                },

                '{self} onSubmit': function(el, ev, register) {
                    register.push(true);
                },

                raiseError: function(msg) {
                    self.trigger('error', [msg]);
                },

                clearError: function() {
                    self.trigger('clear');
                }
            }
        });

        module.resolve();
    });
});
