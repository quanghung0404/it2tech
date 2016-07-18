EasySocial.module('admin/groups/users', function($) {
    var module = this;

    EasySocial
        .require()
        .language('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')
        .done(function($) {
            EasySocial.Controller('Groups.Users', {
                defaultOptions: {
                    groupid: null,

                    '{addMember}': '[data-group-add-member]',
                    '{removeMember}': '[data-group-remove-member]',
                    '{approveMember}': '[data-group-approve-member]',
                    '{promoteMember}': '[data-group-promote-member]',
                    '{demoteMember}': '[data-group-demote-member]'
                }
            }, function(self) {
                return {
                    init: function() {

                    },

                    '{addMember} click': function(el, ev) {
                        var members = {};

                        window.addMembers = function(obj) {
                            if (obj.state) {
                                members[obj.id] = obj;
                            } else {
                                delete members[obj.id];
                            }
                        };

                        var confirmAddMembers = function() {
                            EasySocial.dialog({
                                content: EasySocial.ajax('admin/views/groups/confirmAddMembers', {
                                    members: members,
                                    groupid: self.options.groupid
                                }),
                                bindings: {
                                    '{submitButton} click': function() {
                                        this.addMembersForm().submit();
                                    }
                                }
                            });
                        };

                        EasySocial.dialog({
                            content: EasySocial.ajax('admin/views/groups/addMembers'),
                            bindings: {
                                '{submitButton} click': function() {
                                    confirmAddMembers();
                                }
                            }
                        });
                    },

                    '{removeMember} click': function(el, ev) {
                        if(document.adminForm.boxchecked.value == 0) {
                            alert($.language('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
                        } else {
                            $.Joomla('submitform', ['removeMembers']);
                        }
                    },

                    '{approveMember} click': function(el, ev) {
                        if(document.adminForm.boxchecked.value == 0) {
                            alert($.language('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
                        } else {
                            $.Joomla('submitform', ['publishUser']);
                        }
                    },

                    '{promoteMember} click': function(el, ev) {
                        if(document.adminForm.boxchecked.value == 0) {
                            alert($.language('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
                        } else {
                            $.Joomla('submitform', ['promoteMembers']);
                        }
                    },

                    '{demoteMember} click': function(el, ev) {
                        if(document.adminForm.boxchecked.value == 0) {
                            alert($.language('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
                        } else {
                            $.Joomla('submitform', ['demoteMembers']);
                        }
                    }
                }
            });

            module.resolve();
        });
});
