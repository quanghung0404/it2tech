EasySocial.module( 'apps/group/tasks' , function($)
{
    var module  = this;

    EasySocial.Controller(
        'Groups.Apps.Tasks.Milestones.Browse',
        {
            defaultOptions:
            {
                "{milestone}"   : "[data-group-tasks-milestone-item]"
            }
        },
        function(self)
        {
            return {
                init: function()
                {
                    self.options.groupId    = self.element.data( 'groupid' );

                    self.milestone().implement( EasySocial.Controller.Groups.Apps.Tasks.Milestones.Item ,
                        {
                            "{parent}"  : self
                        });
                }
            }
        });

    EasySocial.Controller(
        'Groups.Apps.Tasks.Milestones.Item',
        {
            defaultOptions:
            {
                "{complete}"    : "[data-milestone-mark-complete]",
                "{uncomplete}"  : "[data-milestone-mark-uncomplete]",
                "{delete}"      : "[data-milestone-delete]",
                "{milestone}"   : "[data-group-tasks-milestone-item]"
            }
        },
        function(self)
        {
            return {
                init: function()
                {
                    self.options.id     = self.element.data( 'id' );
                },

                "{uncomplete} click" : function()
                {
                    EasySocial.ajax( 'apps/group/tasks/controllers/milestone/unresolve' ,
                    {
                        id      : self.options.id,
                        groupId : self.parent.options.groupId
                    })
                    .done(function()
                    {
                        self.element.removeClass( 'is-due' ).removeClass( 'is-completed' );
                    });
                },

                "{complete} click" : function()
                {
                    EasySocial.ajax( 'apps/group/tasks/controllers/milestone/resolve' ,
                    {
                        id      : self.options.id,
                        groupId : self.parent.options.groupId
                    })
                    .done(function()
                    {
                        self.element.removeClass( 'is-due' ).addClass( 'is-completed' );
                    });
                },

                "{delete} click" : function()
                {
                    EasySocial.dialog(
                    {
                        content : EasySocial.ajax( 'apps/group/tasks/controllers/milestone/confirmDelete' ,
                        {
                            id          : self.options.id,
                            groupId     : self.parent.options.groupId
                        }),
                        bindings:
                        {
                            '{deleteButton} click' : function()
                            {
                                EasySocial.ajax( 'apps/group/tasks/controllers/milestone/delete' ,
                                {
                                    id  : self.options.id,
                                    groupId : self.parent.options.groupId
                                })
                                .done(function()
                                {
                                    EasySocial.dialog().close();

                                    self.element.remove();
                                });
                            }
                        }
                    });
                }
            }
        });

    EasySocial.Controller(
        'Groups.Apps.Tasks',
        {
            defaultOptions:
            {
                '{form}'    : '[data-group-tasks-form]',
                '{formWrapper}' : '[data-group-tasks-form-wrapper]',
                '{taskList}': '[data-group-tasks-list]',
                '{item}'    : '[data-group-tasks-list-item]',
                '{completedList}'   : '[data-group-tasks-completed]',
                '{openCounter}'     : '[data-tasks-open-counter]',
                '{closedCounter}'   : '[data-tasks-closed-counter]',
                "{completeMilestone}"   : "[data-milestone-mark-complete]",
                "{uncompleteMilestone}" : "[data-milestone-mark-uncomplete]",
                "{deleteMilestone}"     : "[data-milestone-delete]",
                "{wrapper}"             : "[data-group-tasks-wrapper]"
            }
        },
        function( self )
        {
            return {
                init: function()
                {
                    self.options.id         = self.element.data( 'id' );
                    self.options.groupId    = self.element.data( 'groupid' );
                    self.options.milestoneId    = self.element.data( 'milestoneid' );

                    // Implement form controller
                    self.form().implement( EasySocial.Controller.Groups.Apps.Tasks.Form , { "{parent}" : self });

                    // Implement task item controller
                    self.implementItemController();
                },
                implementItemController: function()
                {
                    self.item().implement( EasySocial.Controller.Groups.Apps.Tasks.Item , { "{parent}" : self } );
                },
                updateOpenCounter: function( total )
                {
                    self.openCounter().html( total );
                },
                updateClosedCounter: function( total )
                {
                    self.closedCounter().html( total );
                },
                insertCompleted: function( taskItem )
                {
                    $( taskItem ).appendTo( self.completedList() );
                },
                insertTask: function( taskItem )
                {
                    self.formWrapper().after(taskItem);

                    self.implementItemController();
                },
                "{uncompleteMilestone} click" : function()
                {
                    EasySocial.ajax( 'apps/group/tasks/controllers/milestone/unresolve' ,
                    {
                        id      : self.options.milestoneId,
                        groupId : self.options.groupId
                    })
                    .done(function()
                    {
                        self.wrapper().removeClass( 'is-due' ).removeClass( 'is-completed' );
                    });
                },
                "{completeMilestone} click" : function()
                {
                    EasySocial.ajax( 'apps/group/tasks/controllers/milestone/resolve' ,
                    {
                        id      : self.options.milestoneId,
                        groupId : self.options.groupId
                    })
                    .done(function()
                    {
                        self.wrapper().removeClass( 'is-due' ).addClass( 'is-completed' );
                    });
                },

                "{deleteMilestone} click" : function()
                {
                    EasySocial.dialog(
                    {
                        content : EasySocial.ajax( 'apps/group/tasks/controllers/milestone/confirmDelete' ,
                        {
                            id          : self.options.milestoneId,
                            groupId     : self.options.groupId
                        }),
                        bindings:
                        {
                            '{deleteButton} click' : function()
                            {
                                EasySocial.ajax( 'apps/group/tasks/controllers/milestone/delete' ,
                                {
                                    id  : self.options.id,
                                    groupId : self.options.groupId
                                })
                                .done(function()
                                {
                                    EasySocial.dialog().close();

                                    window.location = self.options.redirect;
                                });
                            }
                        }
                    });
                }
            }
        }
    );

    EasySocial.Controller(
        'Groups.Apps.Tasks.Item',
        {
            defaultOptions:
            {
                '{checkbox}'    : '[data-item-checkbox]',
                '{delete}'      : '[data-tasks-item-remove]'
            }
        },
        function( self )
        {
            return {
                init: function()
                {
                    self.options.id     = self.element.data( 'id' );
                },
                '{delete} click' : function()
                {
                    EasySocial.dialog(
                    {
                        content     : EasySocial.ajax( 'apps/group/tasks/controllers/tasks/confirmDelete' , { 'groupId' : self.parent.options.groupId }),
                        bindings    :
                        {
                            '{deleteButton} click' : function()
                            {
                                EasySocial.ajax( 'apps/group/tasks/controllers/tasks/delete' ,
                                {
                                    id  : self.options.id,
                                    groupId : self.parent.options.groupId
                                })
                                .done(function()
                                {
                                    EasySocial.dialog().close();

                                    var total   = parseInt( self.parent.openCounter().html() );

                                    self.parent.updateOpenCounter( total - 1 );

                                    self.element.remove();
                                });
                            }
                        }
                    });

                },
                '{checkbox} change': function( el , event )
                {
                    var checked = $( el ).is( ':checked' );

                    if( checked )
                    {
                        EasySocial.ajax( 'apps/group/tasks/controllers/tasks/resolve' ,
                        {
                            id          : self.options.id,
                            groupId     : self.parent.options.groupId
                        })
                        .done(function()
                        {
                            // Decrease the open counter
                            var total   = parseInt( self.parent.openCounter().html() );

                            self.parent.updateOpenCounter( total - 1 );

                            var total   = parseInt( self.parent.closedCounter().html() );

                            self.parent.updateClosedCounter( total + 1 );

                            self.parent.insertCompleted( self.element );
                        });

                    }
                    else
                    {
                        EasySocial.ajax( 'apps/group/tasks/controllers/tasks/unresolve' ,
                        {
                            id          : self.options.id,
                            groupId     : self.parent.options.groupId
                        })
                        .done(function($)
                        {
                            // Decrease the open counter
                            var total   = parseInt( self.parent.openCounter().html() );

                            self.parent.updateOpenCounter( total + 1 );

                            var total   = parseInt( self.parent.closedCounter().html() );

                            self.parent.updateClosedCounter( total - 1 );

                            self.parent.insertTask( self.element );
                        });
                    }
                }
            }
        });

    EasySocial.Controller(
        'Groups.Apps.Tasks.Form',
        {
            defaultOptions:
            {
                '{title}'   : "[data-form-tasks-title]",
                '{create}'  : "[data-form-tasks-create]",
                '{assignee}': "[data-form-tasks-assignee]",
                '{due}'     : "[data-form-tasks-due]",
                '{error}'   : "[data-group-tasks-form-error]"
            }
        },
        function(self)
        {
            return {
                init: function()
                {

                },

                resetForm: function()
                {
                    self.element[0].reset();
                },

                "{title} keyup" : function( el , event )
                {
                    // Enter key
                    if(event.keyCode == 13)
                    {
                        self.create().click();
                    }
                },

                "{create} click" : function()
                {
                    if( self.title().val() == '' )
                    {
                        self.error().removeClass( 'hide' );

                        return false;
                    }

                    self.error().addClass( 'hide' );

                    EasySocial.ajax( 'apps/group/tasks/controllers/tasks/save' ,
                    {
                        title       : self.title().val(),
                        assignee    : self.assignee().val(),
                        due         : self.due().val(),
                        groupId     : self.parent.options.groupId,
                        milestoneId : self.parent.options.id
                    })
                    .done(function( content )
                    {
                        // Reset the form
                        self.resetForm();

                        // Increment the counter
                        var total   = parseInt( self.parent.openCounter().html() );

                        self.parent.updateOpenCounter( total + 1 );

                        self.parent.insertTask( content );
                    });
                }
            }
        });

    module.resolve();
});

