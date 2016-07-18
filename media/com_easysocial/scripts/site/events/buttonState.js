EasySocial.module('site/events/buttonState', function($) {
    var module = this;

    EasySocial
    .require()
    .language('COM_EASYSOCIAL_EVENTS_GUEST_PENDING')
    .done(function($) {

        EasySocial.Controller('Events.ButtonState', {
            defaultOptions: {
                id: null,

                allowMaybe: 1,

                allowNotGoingGuest: 1,

                hidetext: 1,

                refresh: false,

                isPopbox: 0,

                '{guestAction}': '[data-guest-action]',

                '{guestState}': '[data-guest-state]',

                '{request}': '[data-guest-request]',

                '{withdraw}': '[data-guest-withdraw]',

                '{respond}': '[data-guest-respond]',


                "{rsvpButton}" : "[data-event-rsvp-button]",

            }
        }, function(self) {
            return {
                init: function() {
                    // event id
                    self.options.id = self.element.data('id');

                    self.options.allowMaybe = self.element.data('allowmaybe');
                    self.options.allowNotGoingGuest = self.element.data('allownotgoingguest');
                    self.options.hidetext = self.element.data('hidetext');
                    self.options.isPopbox = self.element.data('ispopbox');
                    // Determines if the page requires a refresh
                    // If this is a item page, then the element will have a data-refresh flag
                    // If this is a listing page, then no refresh is required
                    // self.options.refresh = self.element.is('[data-refresh]');

                    // self.initPopbox();
                },

                showError: function(msg) {
                    EasySocial.dialog({
                        content: msg.message
                    });
                },

                stateClasses: {
                    'going': 'btn-es-success',
                    'maybe': 'btn-es-info',
                    'notgoing': 'btn-es-danger'
                },

                refreshButton: function() {

                   EasySocial.ajax('site/views/events/refreshButtonState', {
                        id: self.options.id,
                        hidetext: self.options.hidetext,
                        isPopbox: self.options.isPopbox
                    }).done(function(html) {
                        self.element.replaceWith(html);
                    });
                },

                "{rsvpButton} popboxActivate": function(el, event, popbox) {
                    // popbox.content  or console.dir(popbox) to see what is inside

                    var selector = 'div#' + popbox.id  + '.popbox-' + popbox.type + ' [data-event-button-container]';
                    $(selector).addController('EasySocial.Controller.Events.ButtonState.Popbox', {
                        "{parent}": self
                    });

                },

                '{guestAction} click': function(el) {
                    self.doAction(el);
                },

                doAction: function(el) {

                    // Depending on the action
                    var action = el.data('guestAction');

                    if (action === 'state') {

                        var state = el.data('guestState');

                        if (state === 'notgoing' && !self.options.allowNotGoingGuest) {

                            EasySocial.dialog({
                                content: EasySocial.ajax('site/views/events/notGoingDialog', {
                                    id: self.options.id
                                }),
                                bindings: {
                                    '{closeButton} click': function() {
                                        self.refreshButton();
                                        EasySocial.dialog().close();
                                    },
                                    '{submitButton} click': function() {
                                        self.response('notgoing')
                                            .done(function() {
                                                self.refreshButton();
                                                EasySocial.dialog().close();
                                            });
                                    }
                                }
                            });
                        } else {

                            self.response(state)
                                .done(function() {
                                    self.refreshButton();
                                })
                                .fail(function(msg) {
                                    el.removeClass(self.stateClasses[action]);
                                    self.showError(msg);
                                });

                        }
                    }

                    if (action === 'request') {
                        EasySocial.dialog({
                            content: EasySocial.ajax('site/views/events/requestDialog', {
                                id: self.options.id
                            }),
                            bindings: {
                                '{submitButton} click': function() {
                                    el
                                        .attr('data-guest-action', 'withdraw')
                                        .data('guestAction', 'withdraw')
                                        .removeAttr('data-guest-request')
                                        .attr('data-guest-withdraw', '')
                                        .text($.language('COM_EASYSOCIAL_EVENTS_GUEST_PENDING'));

                                    self.response(action);

                                    EasySocial.dialog().close();
                                }
                            }
                        });
                    }

                    if (action === 'withdraw') {
                        EasySocial.dialog({
                            content: EasySocial.ajax('site/views/events/withdrawDialog', {
                                id: self.options.id
                            }),
                            bindings: {
                                '{submitButton} click': function() {
                                    self.response('withdraw')
                                        .done(function() {
                                            self.refreshButton();
                                            EasySocial.dialog().close();
                                        });
                                }
                            }
                        });
                    }

                    if (action === 'attend') {
                        self.response('going').done(function() {
                            self.refreshButton();
                            EasySocial.dialog().close();
                        });
                    }
                },

                response: function(action) {
                    return EasySocial.ajax('site/controllers/events/guestResponse', {
                        id: self.options.id,
                        state: action
                    });
                }
            }
        });


        EasySocial.Controller("Events.ButtonState.Popbox",
        {
            defaultOptions: {
                '{guestAction}': '[data-guest-action]'
            }
        }, function(self) {
             return {
                init: function() {
                    console.log('Events.ButtonState.Popbox');
                },

                "{guestAction} click": function(el) {
                    self.parent.doAction(el);
                },

             }
        });


        module.resolve();
    });
});
