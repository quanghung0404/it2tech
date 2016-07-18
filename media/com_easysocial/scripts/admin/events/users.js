EasySocial.module('admin/events/users', function($) {
    var module = this;

    EasySocial
        .require()
        .language('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')
        .done(function($) {
            EasySocial.Controller('Events.Users', {
                defaultOptions: {
                    eventid: null,

                    '{inviteGuest}': '[data-event-invite-guest]',
                    '{removeGuest}': '[data-event-remove-guest]',
                    '{approveGuest}': '[data-event-approve-guest]',
                    '{promoteGuest}': '[data-event-promote-guest]',
                    '{demoteGuest}': '[data-event-demote-guest]'
                }
            }, function(self) {
                return {
                    init: function() {
                    },

                    '{inviteGuest} click': function(el, ev) {
                        var guests = {};

                        window.inviteGuests = function(guest) {
                            if (guest.state) {
                                guests[guest.id] = guest
                            } else {
                                delete guests[guest.id];
                            }
                        };

                        var confirmInviteGuests = function() {
                            EasySocial.dialog({
                                content: EasySocial.ajax('admin/views/events/confirmInviteGuests', {
                                    guests: guests,
                                    eventid: self.options.eventid
                                }),
                                bindings: {
                                    '{submitButton} click': function() {
                                        this.inviteGuestsForm().submit();
                                    }
                                }
                            });
                        };

                        EasySocial.dialog({
                            content: EasySocial.ajax('admin/views/events/inviteGuests'),
                            bindings: {
                                '{submitButton} click': function() {
                                    confirmInviteGuests();
                                }
                            }
                        });
                    },

                    '{removeGuest} click': function(el, ev) {
                        if(document.adminForm.boxchecked.value == 0) {
                            alert($.language('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
                        } else {
                            $.Joomla('submitform', ['removeGuests']);
                        }
                    },

                    '{approveGuest} click': function(el, ev) {
                        if(document.adminForm.boxchecked.value == 0) {
                            alert($.language('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
                        } else {
                            $.Joomla('submitform', ['approveGuests']);
                        }
                    },

                    '{promoteGuest} click': function(el, ev) {
                        if(document.adminForm.boxchecked.value == 0) {
                            alert($.language('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
                        } else {
                            $.Joomla('submitform', ['promoteGuests']);
                        }
                    },

                    '{demoteGuest} click': function(el, ev) {
                        if(document.adminForm.boxchecked.value == 0) {
                            alert($.language('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
                        } else {
                            $.Joomla('submitform', ['demoteGuests']);
                        }
                    }
                }
            });

            module.resolve();
        });
});
