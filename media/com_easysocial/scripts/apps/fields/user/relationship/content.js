EasySocial.module('apps/fields/user/relationship/content', function($) {
    var module = this;

    EasySocial
        .require()
        .script( 'site/friends/suggest' )
        .language(
            'PLG_FIELDS_RELATIONSHIP_APPROVE_CONFIRM',
            'PLG_FIELDS_RELATIONSHIP_ACTION_APPROVE',
            'COM_EASYSOCIAL_CANCEL_BUTTON')
        .done(function($) {

            EasySocial.Controller('Field.Relationship', {
                defaultOptions: {
                    required: false,

                    id: null,

                    types: null,

                    fieldname: null,

                    actor: null,
                    target: null,

                    '{field}'   : '[data-field-relationship]',

                    '{display}' : '[data-relationship-display]',
                    '{form}'    : '[data-relationship-form]',

                    '{confirm}' : '[data-relationship-display-confirm]',
                    '{pending}' : '[data-relationship-display-pending]',

                    '{actions}' : '[data-relationship-display-actions]',

                    '{pendingTitle}'    : '[data-relationship-pending-title]'
                }
            }, function(self) {
                return {
                    init: function() {
                        EasySocial.module('field.relationship/' + self.options.id).done(function(types) {
                            self.options.types = types;
                        });

                        self.display().addController('EasySocial.Controller.Field.Relationship.Display', {
                            controller: {
                                parent: self
                            }
                        });

                        self.addPlugin('form');
                    },

                    '{self} relationshipDeleted': function() {
                        self.confirm().hide();

                        self.form().show();
                    }
                }
            });

            EasySocial.Controller('Field.Relationship.Form', {
                defaultOptions: {
                    origType            : null,
                    origTarget          : null,
                    origApproved        : null,

                    '{form}'            : '[data-relationship-form]',

                    '{type}'            : '[data-relationship-form-type]',
                    '{connectWord}'     : '[data-relationship-form-connectwords]',
                    '{connectWords}'    : '[data-relationship-form-connectwords] span',

                    '{input}'           : '[data-relationship-form-input]',

                    '{target}'          : '[data-relationship-form-target]',

                    '{targetAvatar}'    : '[data-relationship-form-target-avatar]',
                    '{targetName}'      : '[data-relationship-form-target-name]',

                    '{targetPending}'   : '[data-relationship-form-target-pending]',

                    '{targetDelete}'    : '[data-relationship-form-target-delete]',

                    '{textboxlistDelete}': '[data-textboxlist-itemRemoveButton]'
                }
            }, function(self) {
                return {
                    init: function() {
                        self.input().addController(EasySocial.Controller.Friends.Suggest, {
                            max: 1,
                            name: self.parent.options.fieldname + '[target][]'
                        });

                        self.options.origType = self.form().data('orig-type');
                        self.options.origTarget = self.form().data('orig-target');
                        self.options.origApproved = self.form().data('orig-approved');
                    },

                    '{type} change': function(el) {
                        var name = el.val();

                        var isConnect = self.parent.options.types[name].connect;

                        var items = self.input().controller('Textboxlist').getAddedItems();

                        var isSelected = items.length > 0;

                        var element = isSelected ? self.target() : self.input();

                        var selected = isSelected ? items[0] : false;

                        if(isConnect) {
                            element.show();

                            if(isSelected && self.options.origApproved && selected.id == self.options.origTarget && name == self.options.origType) {
                                self.targetPending().hide();
                            } else {
                                self.targetPending().show();
                            }
                        } else {
                            element.hide();
                        }

                        self.connectWords().hide();

                        self.connectWords().filterBy('relationship-form-connectword', name).show();
                    },

                    '{input} addItem': function(el, ev, item) {
                        if(item.avatar) {
                            self.targetAvatar().attr('src', item.avatar);
                        }

                        if(item.screenName) {
                            self.targetName().text(item.screenName);
                        }

                        if(item.id) {
                            self.targetDelete().data('id', item.id);
                        }

                        self.input().hide();

                        self.target().show();

                        if(self.options.origApproved && item.id == self.options.origTarget && self.type().val() == self.options.origType) {
                            self.targetPending().hide();
                        } else {
                            self.targetPending().show();
                        }
                    },

                    '{targetDelete} click': function(el, ev) {
                        var id = el.data('id');

                        self.input().controller('Textboxlist').removeItem(id);

                        self.input().show();

                        self.target().hide();
                    },

                    '{parent} relationshipApproved': function(el, ev, target) {

                        self.type().val(target.type);

                        var item = {
                            avatar: target.avatar,
                            html: target.name + '<input type="hidden" name="' + self.parent.options.fieldname + '[target][]" value="' + target.id + '"/>',
                            id: target.id,
                            key: target.name,
                            menuHtml: undefined,
                            name: self.parent.options.fieldname + '[target]',
                            screenName: target.name,
                            title: target.name
                        }

                        self.input().controller('Textboxlist').addItem(item);

                        self.connectWords().hide();

                        self.connectWords().filterBy('relationshipFormConnectword', target.type);
                    }
                }
            });

            EasySocial.Controller('Field.Relationship.Display', {
                defaultOptions: {
                    id                  : null,

                    '{pendingFrame}'    : '[data-relationship-display-pending-text]',
                    '{loadingFrame}'    : '[data-relationship-display-loading]',
                    '{errorFrame}'      : '[data-relationship-display-error]',

                    '{actionsFrame}'    : '[data-relationship-display-actions]',

                    '{deleteButton}'    : '[data-relationship-display-actions-delete]',

                    '{approveButton}'   : '[data-relationship-display-actions-approve]',
                    '{rejectButton}'    : '[data-relationship-display-actions-reject]'
                }
            }, function(self) {
                return {
                    init: function() {

                        self.options.id = self.element.data('id');

                    },

                    '{deleteButton} click': function() {

                        self.parent.trigger('relationshipDeleted');

                        self.element.hide();
                    },

                    '{approveButton} click': function() {
                        EasySocial.dialog({
                            width: 500,
                            content: $.language('PLG_FIELDS_RELATIONSHIP_APPROVE_CONFIRM'),
                            selectors: {
                                '{approveButton}': '[data-approve-button]',
                                '{cancelButton}': '[data-cancel-button]'
                            },
                            bindings: {
                                '{approveButton} click': function() {
                                    this.parent.close();

                                    self.approveRelationship();
                                },

                                '{cancelButton} click': function() {
                                    this.parent.close();
                                }
                            },
                            buttons: '<button data-cancel-button type="button" class="btn btn-es">' + $.language('COM_EASYSOCIAL_CANCEL_BUTTON') + '</button><button data-approve-button type="button" class="btn btn-es-primary">' + $.language('PLG_FIELDS_RELATIONSHIP_ACTION_APPROVE') + '</button>'
                        })
                    },

                    approveRelationship: function() {
                        self.actionsFrame().hide();

                        self.loadingFrame().show();

                        EasySocial.ajax('fields/user/relationship/approve', {
                            id: self.parent.options.id,
                            relid: self.options.id
                        })
                            .done(function(target) {
                                self.parent.display().hide();

                                self.element.show();

                                self.element.removeAttr('data-relationship-display-pending');
                                self.element.attr('data-relationship-display-confirm', '');

                                self.parent.form().before(self.element);

                                self.parent.form().hide();

                                if(self.parent.pending().length < 1) {
                                    self.parent.pendingTitle().hide();
                                }

                                self.loadingFrame().remove();

                                self.pendingFrame().remove();

                                self.approveButton().remove();

                                self.rejectButton().remove();

                                self.actionsFrame().show();

                                self.deleteButton().show();

                                self.parent.trigger('relationshipApproved', [target]);
                            })
                            .fail(function(msg) {
                                self.loadingFrame().hide();

                                self.errorFrame().show().find('span').text(msg);
                            });
                    },

                    '{rejectButton} click': function() {
                        self.actionsFrame().hide();

                        self.loadingFrame().show();

                        EasySocial.ajax('fields/user/relationship/reject', {
                            id: self.parent.options.id,
                            relid: self.options.id
                        })
                            .done(function() {
                                // self.parent.trigger('relationshipRejected');

                                self.element.remove();

                                if(self.parent.pending().length < 1) {
                                    self.parent.pendingTitle().hide();
                                }
                            })
                            .fail(function(msg) {
                                self.parent.trigger('relationshipActionError', [msg]);
                            });
                    }
                }
            });

            module.resolve();

        });
});
