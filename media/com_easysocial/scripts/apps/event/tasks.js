EasySocial.module('apps/event/tasks', function($)
{
    var module = this;

    EasySocial.Controller('Events.Apps.Tasks.Milestones.Browse', {
        defaultOptions: {
            eventId: null,

            "{milestone}": "[data-tasks-milestone-item]"
        }
    }, function(self) {
        return {
            init: function()
            {
                self.options.eventId = self.element.data('eventid');

                self.milestone().addController(EasySocial.Controller.Events.Apps.Tasks.Milestones.Item, {
                    "{parent}"  : self
                });
            }
        }
    });

    EasySocial.Controller('Events.Apps.Tasks.Milestones.Item', {
        defaultOptions: {
            "{complete}": "[data-milestone-mark-complete]",
            "{incomplete}": "[data-milestone-mark-incomplete]",
            "{delete}": "[data-milestone-delete]",
            "{milestone}": "[data-event-tasks-milestone-item]"
        }
    }, function(self) {
        return {
            init: function()
            {
                self.options.id = self.element.data('id');
            },

            "{incomplete} click" : function(el)
            {
                EasySocial.ajax('apps/event/tasks/controllers/milestone/unresolve',
                {
                    id: self.options.id,
                    eventId: self.parent.options.eventId
                })
                .done(function() {
                    self.element.removeClass('is-due').removeClass('is-completed');

                    el.hide();

                    self.complete().show();
                });
            },

            "{complete} click" : function(el)
            {
                EasySocial.ajax('apps/event/tasks/controllers/milestone/resolve', {
                    id: self.options.id,
                    eventId: self.parent.options.eventId
                })
                .done(function() {
                    self.element.removeClass('is-due').addClass('is-completed');

                    el.hide();

                    self.incomplete().show();
                });
            },

            "{delete} click" : function()
            {
                EasySocial.dialog( {
                    content : EasySocial.ajax('apps/event/tasks/controllers/milestone/confirmDelete', {
                        id: self.options.id,
                        eventId: self.parent.options.eventId
                    }),
                    bindings: {
                        '{deleteButton} click' : function() {
                            EasySocial.ajax('apps/event/tasks/controllers/milestone/delete', {
                                id: self.options.id,
                                eventId: self.parent.options.eventId
                            })
                            .done(function() {
                                EasySocial.dialog().close();

                                self.element.remove();
                            });
                        }
                    }
                });
            }
        }
    });

    EasySocial.Controller('Events.Apps.Tasks', {
        defaultOptions: {
            '{form}': '[data-tasks-form]',
            '{formWrapper}': '[data-tasks-form-wrapper]',
            '{taskList}': '[data-tasks-list]',
            '{item}': '[data-tasks-list-item]',
            '{completedList}': '[data-tasks-completed]',
            '{openCounter}': '[data-tasks-open-counter]',
            '{closedCounter}': '[data-tasks-closed-counter]',
            "{completeMilestone}": "[data-milestone-mark-complete]",
            "{incompleteMilestone}": "[data-milestone-mark-incomplete]",
            "{deleteMilestone}": "[data-milestone-delete]",
            "{wrapper}": "[data-tasks-wrapper]"
        }
    }, function(self) {
        return {
            init: function()
            {
                self.options.id = self.element.data('id');
                self.options.eventId = self.element.data('eventid');
                self.options.milestoneId = self.element.data('milestoneid');

                // Implement form controller
                self.form().addController(EasySocial.Controller.Events.Apps.Tasks.Form, {
                    "{parent}": self
                });

                self.implementItemController();
            },
            implementItemController: function()
            {
                // Implement task item controller
                self.item().addController(EasySocial.Controller.Events.Apps.Tasks.Item, {
                    "{parent}": self
                });
            },
            updateOpenCounter: function(total)
            {
                self.openCounter().html(total);
            },
            updateClosedCounter: function(total)
            {
                self.closedCounter().html(total);
            },
            insertCompleted: function(taskItem)
            {
                $(taskItem).appendTo(self.completedList());
            },
            insertTask: function(taskItem)
            {
                self.formWrapper().after(taskItem);

                // Implement item controller on the tasks
                self.implementItemController();
            },
            "{uncompleteMilestone} click" : function()
            {
                EasySocial.ajax('apps/event/tasks/controllers/milestone/unresolve',
                {
                    id: self.options.milestoneId,
                    eventId: self.options.eventId
                })
                .done(function()
                {
                    self.wrapper().removeClass('is-due').removeClass('is-completed');
                });
            },
            "{completeMilestone} click" : function()
            {
                EasySocial.ajax('apps/event/tasks/controllers/milestone/resolve', {
                    id: self.options.milestoneId,
                    eventId: self.options.eventId
                })
                .done(function() {
                    self.wrapper().removeClass('is-due').addClass('is-completed');
                });
            },

            "{deleteMilestone} click" : function()
            {
                EasySocial.dialog( {
                    content : EasySocial.ajax('apps/event/tasks/controllers/milestone/confirmDelete', {
                        id: self.options.milestoneId,
                        eventId: self.options.eventId
                    }),
                    bindings: {
                        '{deleteButton} click' : function() {
                            EasySocial.ajax('apps/event/tasks/controllers/milestone/delete', {
                                id: self.options.id,
                                eventId: self.options.eventId
                            })
                            .done(function() {
                                EasySocial.dialog().close();

                                window.location = self.options.redirect;
                            });
                        }
                    }
                });
            }
        }
    });

    EasySocial.Controller('Events.Apps.Tasks.Item', {
        defaultOptions: {
            '{checkbox}': '[data-item-checkbox]',
            '{delete}': '[data-tasks-item-remove]'
        }
    }, function(self) {
        return {
            init: function()
            {
                self.options.id = self.element.data('id');
            },
            '{delete} click' : function()
            {
                EasySocial.dialog({
                    content: EasySocial.ajax('apps/event/tasks/controllers/tasks/confirmDelete', {
                        'eventId': self.parent.options.eventId
                    }),
                    bindings: {
                        '{deleteButton} click' : function() {
                            EasySocial.ajax('apps/event/tasks/controllers/tasks/delete', {
                                id: self.options.id,
                                eventId: self.parent.options.eventId
                            })
                            .done(function() {
                                EasySocial.dialog().close();

                                var total = parseInt(self.parent.openCounter().html());

                                self.parent.updateOpenCounter(total - 1);

                                self.element.remove();
                            });
                        }
                    }
                });
            },
            '{checkbox} change': function(el, event)
            {
                var checked = $(el).is(':checked');

                if (checked) {
                    EasySocial.ajax('apps/event/tasks/controllers/tasks/resolve', {
                        id: self.options.id,
                        eventId: self.parent.options.eventId
                    })
                    .done(function() {
                        // Decrease the open counter
                        var total = parseInt(self.parent.openCounter().html());

                        self.parent.updateOpenCounter(total - 1);

                        var total = parseInt(self.parent.closedCounter().html());

                        self.parent.updateClosedCounter(total + 1);

                        self.parent.insertCompleted(self.element);
                    });

                } else {
                    EasySocial.ajax('apps/event/tasks/controllers/tasks/unresolve', {
                        id: self.options.id,
                        eventId: self.parent.options.eventId
                    })
                    .done(function($) {
                        // Decrease the open counter
                        var total = parseInt(self.parent.openCounter().html());

                        self.parent.updateOpenCounter(total + 1);

                        var total = parseInt(self.parent.closedCounter().html());

                        self.parent.updateClosedCounter(total - 1);

                        self.parent.insertTask(self.element);
                    });
                }
            }
        }
    });

    EasySocial.Controller('Events.Apps.Tasks.Form', {
        defaultOptions: {
            '{title}': "[data-form-tasks-title]",
            '{create}': "[data-form-tasks-create]",
            '{assignee}': "[data-form-tasks-assignee]",
            '{due}': "[data-form-tasks-due]",
            '{error}': "[data-tasks-form-error]"
        }
    }, function(self) {
        return {
            init: function()
            {

            },

            resetForm: function()
            {
                self.element[0].reset();
            },

            "{title} keyup" : function(el, event)
            {
                // Enter key
                if(event.keyCode == 13) {
                    self.create().click();
                }
            },

            "{create} click" : function()
            {
                if(self.title().val() == '') {
                    self.error().removeClass('hide');

                    return false;
                }

                self.error().addClass('hide');

                EasySocial.ajax('apps/event/tasks/controllers/tasks/save', {
                    title: self.title().val(),
                    assignee: self.assignee().val(),
                    due: self.due().val(),
                    eventId: self.parent.options.eventId,
                    milestoneId: self.parent.options.milestoneId
                })
                .done(function(content) {

                    // Reset the form
                    self.resetForm();

                    // Increment the counter
                    var total = parseInt(self.parent.openCounter().html());

                    self.parent.updateOpenCounter(total + 1);

                    self.parent.insertTask(content);
                });
            }
        }
    });

    module.resolve();
});

