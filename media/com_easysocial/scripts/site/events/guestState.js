EasySocial.module('site/events/guestState', function($) {
    var module = this;

    EasySocial
    .require()
    .language('COM_EASYSOCIAL_EVENTS_GUEST_PENDING')
    .done(function($) {

        EasySocial.Controller('Events.GuestState', {
            defaultOptions: {
                id: null,

                allowMaybe: 1,

                allowNotGoingGuest: 1,

                hidetext: 1,

                refresh: false,

                '{guestAction}': '[data-guest-action]',

                '{guestState}': '[data-guest-state]',

                '{request}': '[data-guest-request]',

                '{withdraw}': '[data-guest-withdraw]',

                '{respond}': '[data-guest-respond]'
            }
        }, function(self) {
            return {
                init: function() {
                    self.options.id = self.element.data('id');

                    self.options.allowMaybe = self.element.data('allowmaybe');
                    self.options.allowNotGoingGuest = self.element.data('allownotgoingguest');
                    self.options.hidetext = self.element.data('hidetext');

                    // Determines if the page requires a refresh
                    // If this is a item page, then the element will have a data-refresh flag
                    // If this is a listing page, then no refresh is required
                    self.options.refresh = self.element.is('[data-refresh]');

                    // self.initPopbox();
                },

                showError: function(msg) {
                    EasySocial.dialog({
                        content: msg
                    });
                },

                stateClasses: {
                    'going': 'btn-es-success',
                    'maybe': 'btn-es-info',
                    'notgoing': 'btn-es-danger'
                },

                '{guestAction} click': function(el) {
                    // Depending on the action

                    var action = el.data('guestAction');

                    if (action === 'state') {
                        var state = el.data('guestState');

                        self.guestAction().removeClass('btn-es-success btn-es-info btn-es-danger');

                        el.addClass(self.stateClasses[state]);

                        if (state === 'notgoing' && !self.options.allowNotGoingGuest) {
                            EasySocial.dialog({
                                content: EasySocial.ajax('site/views/events/notGoingDialog', {
                                    id: self.options.id
                                }),
                                bindings: {
                                    '{closeButton} click': function() {
                                        EasySocial.ajax('site/views/events/refreshGuestState', {
                                            id: self.options.id,
                                            hidetext: self.options.hidetext
                                        }).done(function(html) {
                                            self.element.html(html);

                                            EasySocial.dialog().close();
                                        });
                                    },
                                    '{submitButton} click': function() {
                                        self.response('notgoing')
                                            .done(function() {
                                                if (self.options.refresh) {
                                                    return location.reload();
                                                }

                                                EasySocial.ajax('site/views/events/refreshGuestState', {
                                                    id: self.options.id,
                                                    hidetext: self.options.hidetext
                                                }).done(function(html) {
                                                    self.element.html(html);

                                                    EasySocial.dialog().close();
                                                });
                                            });
                                    }
                                }
                            });
                        } else {
                            self.response(state)
                                .done(function() {
                                    if (self.options.refresh) {
                                        return location.reload();
                                    }
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
                                            EasySocial.ajax('site/views/events/refreshGuestState', {
                                                id: self.options.id,
                                                hidetext: self.options.hidetext
                                            }).done(function(html) {
                                                self.element.html(html);

                                                EasySocial.dialog().close();
                                            });
                                        });
                                }
                            }
                        });
                    }

                    if (action === 'attend') {
                        self.response('going').done(function() {
                            EasySocial.ajax('site/views/events/refreshGuestState', {
                                id: self.options.id,
                                hidetext: self.options.hidetext
                            }).done(function(html) {
                                if (self.options.refresh) {
                                    return location.reload();
                                }

                                if (html !== undefined) {
                                    self.element.html(html);

                                    EasySocial.dialog().close();
                                }
                            });
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

        module.resolve();
    });
});
