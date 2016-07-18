EasySocial.module('apps/event/discussions', function($) {

    var module  = this;

    EasySocial.Controller(
        'Events.Item.Discussions',
        {
            defaultOptions:
            {
                "{filter}"  : "[data-event-discussions-filter]",
                "{contents}": "[data-event-discussion-contents]"
            }
        },
        function(self)
        {
            return {
                init: function()
                {
                    self.options.id     = self.element.data( 'id' );
                },

                setContent: function( html )
                {
                    // Remove loading class since we already have the content.
                    self.contents().removeClass( 'is-loading' );

                    self.contents().html( html );
                },

                setActiveFilter: function( el )
                {
                    // Remove active class.
                    self.filter().removeClass( 'active' );

                    // Add active class to the current element
                    el.addClass( 'active' );
                },

                "{filter} click" : function( el , event )
                {
                    var filter = el.data( 'filter' );

                    // Add loader for the contents area
                    self.contents().html( '&nbsp;' ).addClass( 'is-loading' );

                    // Set active filter
                    self.setActiveFilter( el );

                    // Run the ajax call now
                    EasySocial.ajax( 'apps/event/discussions/controllers/discussion/getDiscussions' ,
                    {
                        "id"        : self.options.id,
                        "filter"    : filter
                    })
                    .done(function( contents , empty )
                    {
                        if( empty )
                        {
                            self.contents().addClass( 'is-empty' );
                        }
                        else
                        {
                            self.contents().removeClass( 'is-empty' );
                        }
                        // Set the contents
                        self.setContent( contents );
                    });
                }
            }
        }
    );
    EasySocial.Controller(
        'Events.Item.Discussion',
        {
            defaultOptions:
            {
                "{form}"        : "[data-reply-form]",
                "{list}"        : "[data-reply-list]",
                "{replies}"     : "[data-reply-item]",
                "{repliesWrap}" : "[data-replies-wrapper]",

                "{replyCounter}": "[data-reply-count]",

                "{lock}"        : "[data-discussion-lock]",
                "{unlock}"      : "[data-discussion-unlock]",
                "{delete}"      : "[data-discussion-delete]"
            }
        },
        function( self )
        {
            return {
                init: function()
                {
                    self.options.id = self.element.data('id');
                    self.options.eventId = self.element.data('eventid');

                    self.implementReply(self.replies());

                    self.form().implement(EasySocial.Controller.Events.Item.Discussion.Form,{
                        "{parent}": self
                    });
                },

                implementReply: function()
                {
                    self.replies().implement(EasySocial.Controller.Events.Item.Discussion.Reply, {
                        "{parent}": self
                    });
                },

                insertReply: function(html)
                {
                    // Since we know that we need to append the reply item, we need to remove is-unanswered
                    self.element.removeClass( 'is-unanswered' );

                    // Since an item is added, we want to remove the empty class.
                    self.repliesWrap().removeClass( 'is-empty' );

                    // Append the new item
                    self.list().append( html );

                    // Implement the controller again
                    self.implementReply();
                },

                updateReplyCounter: function( total )
                {
                    if( total == 0 )
                    {
                        self.repliesWrap().addClass( 'is-empty' );
                    }
                    self.replyCounter().html( total );
                },

                setResolved: function()
                {
                    self.element.addClass( 'is-resolved' );
                },

                "{unlock} click" : function( el , event )
                {
                    EasySocial.ajax('apps/event/discussions/controllers/discussion/unlock', {
                        "id" : self.options.id
                    }).done(function() {
                        // Add lock element
                        self.element.removeClass('is-locked');
                    });
                },

                "{delete} click" : function(el, event)
                {
                    EasySocial.dialog({
                        content : EasySocial.ajax( 'apps/event/discussions/controllers/discussion/confirmDelete' , { "id" : self.options.id , "eventId" : self.options.eventId })
                    });
                },

                "{lock} click" : function( el , event )
                {
                    EasySocial.dialog(
                    {
                        content : EasySocial.ajax( 'apps/event/discussions/controllers/discussion/confirmLock' ),
                        bindings:
                        {
                            "{lockButton} click" : function()
                            {
                                EasySocial.ajax( 'apps/event/discussions/controllers/discussion/lock' ,
                                {
                                    "id" : self.options.id
                                })
                                .done(function()
                                {
                                    // Hide the dialog
                                    EasySocial.dialog().close();

                                    // Add lock element
                                    self.element.addClass( 'is-locked' );
                                });
                            }
                        }
                    });
                }
            }
        }
    );

    EasySocial.Controller(
        'Events.Item.Discussion.Reply',
        {
            defaultOptions:
            {
                "{acceptAnswer}"    : "[data-reply-accept-answer]",
                "{delete}"          : "[data-reply-delete]",
                "{edit}"            : "[data-reply-edit]",
                "{cancelEdit}"      : "[data-reply-edit-cancel]",
                "{update}"          : "[data-reply-edit-update]",
                "{textarea}"        : "[data-reply-content]",
                "{content}"         : "[data-reply-display-content]",
                "{alertDiv}"        : "div.alert-error"
            }
        },
        function( self )
        {
            return {
                init: function()
                {
                    console.log(self.element)
                    self.options.id     = self.element.data( 'id' );
                },
                "{acceptAnswer} click" : function()
                {
                    EasySocial.ajax( 'apps/event/discussions/controllers/reply/accept' ,
                    {
                        "id" : self.options.id
                    })
                    .done(function() {
                        self.parent.setResolved();
                    });
                },

                cancelEditing : function()
                {
                    self.element.removeClass( 'is-editing' );
                },

                "{cancelEdit} click" : function()
                {
                    self.cancelEditing();
                },

                "{edit} click" : function()
                {
                    self.element.addClass( 'is-editing' );
                },

                "{update} click" : function()
                {
                    var content     = self.textarea().val();

                    // console.log( self.element);

                    // If content is empty, throw some errors
                    if (content == '') {
                        self.element.addClass('is-empty');
                        self.alertDiv().show();
                        return false;
                    }

                    EasySocial.ajax( 'apps/event/discussions/controllers/reply/update' , {
                        "id": self.options.id,
                        "eventId": self.parent.options.eventId,
                        "content": content
                    })
                    .done(function(content) {
                        // Update the content
                        self.content().html( content );

                        self.element.removeClass('is-empty');
                        self.alertDiv().hide();


                        // Hide the textarea
                        self.cancelEditing();
                    });
                },

                "{delete} click" : function()
                {
                    EasySocial.dialog(
                    {
                        content     : EasySocial.ajax( 'apps/event/discussions/controllers/reply/confirmDelete' , { "id"    : self.options.id } ),
                        bindings    :
                        {
                            "{deleteButton} click" : function()
                            {
                                EasySocial.ajax( 'apps/event/discussions/controllers/reply/delete',
                                {
                                    "id"    : self.options.id
                                })
                                .done(function(discussion) {

                                    // Update the counter
                                    self.parent.updateReplyCounter( discussion.total_replies );

                                    // Hide the dialog
                                    EasySocial.dialog().close();

                                    // Remove the element
                                    self.element.remove();
                                });
                            }
                        }
                    });
                }
            }
        }
    );

    EasySocial.Controller(
        'Events.Item.Discussion.Form',
        {
            defaultOptions:
            {
                "{textarea}"    : "[data-reply-content]",
                "{submitReply}" : "[data-reply-submit]"
            }
        },
        function( self )
        {
            return {
                init: function()
                {
                },

                "{submitReply} click" : function( el , event )
                {
                    var content     = self.textarea().val();

                    // If content is empty, throw some errors
                    if ( content == '' ) {
                        self.element.addClass( 'is-empty' );
                        return false;
                    }

                    EasySocial.ajax('apps/event/discussions/controllers/reply/submit', {
                        "id"        : self.parent.options.id,
                        "eventId"   : self.parent.options.eventId,
                        "content"   : content
                    })
                    .done(function(html) {
                        // Inser the new node back.
                        self.parent.insertReply(html);

                        // Update the textarea
                        self.textarea().val('');
                    });

                }
            }
        }
    );

    module.resolve();
});

