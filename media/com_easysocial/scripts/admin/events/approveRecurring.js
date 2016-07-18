EasySocial.module('admin/events/approveRecurring', function($) {
    var module = this;

    EasySocial.Controller('Events.ApproveRecurring', {
        defaultOptions: {
            postdatas: {},
            schedules: {},
            eventids: [],

            '{progress}': '[data-progress-bar]',

            '{form}': '[data-form]'
        }
    }, function(self) {
        return {
            init: function() {
                // Calculate the total things to do
                var length = 0;

                $.each(self.options.schedules, function(i, s) {
                    length += s.length;
                });

                self.total = length;

                self.startCreate();
            },

            total: 0,
            doneCounter: 0,
            eventCounter: 0,
            createCounter: 0,

            updateProgressBar: function() {
                var percentage = Math.ceil((self.doneCounter / self.total) * 100);

                self.progress().css({
                    width: percentage + '%'
                });
            },

            startCreate: function() {
                if (self.options.eventids[self.eventCounter] === undefined) {
                    return self.completed();
                }

                self.create()
                    .done(function() {
                        self.doneCounter++;

                        self.createCounter++;

                        if (self.options.schedules[self.options.eventids[self.eventCounter]][self.createCounter] === undefined) {
                            self.eventCounter++;
                            self.createCounter = 0;
                        }

                        self.updateProgressBar();

                        self.startCreate();
                    })
                    .fail(function(msg, errors) {
                        console.log(msg, errors);
                    });
            },

            create: function() {
                var eventId = self.options.eventids[self.eventCounter],
                    datetime = self.options.schedules[eventId][self.createCounter],
                    postdata = self.options.postdatas[eventId];

                return EasySocial.ajax('admin/controllers/events/createRecurring', {
                    eventId: eventId,
                    datetime: datetime,
                    postdata: postdata
                });
            },

            completed: function() {
                self.progress().parent().removeClass('progress-info').addClass('progress-success');
                self.form().submit();
            }
        }
    });

    module.resolve();
});
