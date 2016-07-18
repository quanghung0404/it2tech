EasySocial.module('site/events/edit', function($) {
    var module = this;

    EasySocial.require()
    .script('validate', 'field')
    .done(function() {
        EasySocial.Controller('Events.Edit', {
            defaultOptions: {
                id: null,

                isRecurring: 0,
                hasRecurring: 0,

                '{form}': '[data-form]',

                '{nav}': '[data-step-nav]',
                '{content}': '[data-step-content]',
                '{fields}': '[data-edit-field]',
                '{saveButton}': '[data-edit-save]',

                '{saveApply}': 'input[name="applyRecurring"]'
            }
        }, function(self) {
            return {
                init: function() {
                    self.fields().addController('EasySocial.Controller.Field.Base', {
                        mode: 'edit'
                    });
                },

                errorFields: [],

                '{nav} click': function(el, ev) {
                    var id = $(el).data('for');

                    self.content().hide();

                    self.nav().removeClass('active');

                    el.addClass('active');

                    self.content().filterBy('id', id)
                        .show()
                        .find(self.fields.selector).trigger('show');
                },

                '{nav} error': function(el) {
                    el.addClass('error');
                },

                '{nav} clear': function(el) {
                    if (self.errorFields.length < 1) {
                        el.removeClass('error');
                    }
                },

                '{fields} error': function(el, ev) {
                    self.triggerStepError(el);
                },

                '{fields} clear': function(el, ev) {
                    self.clearStepError(el);
                },

                '{fieldItem} onError': function(el, ev) {
                    self.triggerStepError(el);
                },

                triggerStepError: function(el) {
                    var fieldid = el.data('id'),
                        stepid = el.parents(self.content.selector).data('id');

                    if ($.inArray(fieldid, self.errorFields) < 0) {
                        self.errorFields.push(fieldid);
                    }

                    self.nav().filterBy('for', stepid).trigger('error');
                },

                clearStepError: function(el) {
                    var fieldid = el.data('id'),
                        stepid = el.parents(self.content.selector).data('id');

                    self.errorFields = $.without(self.errorFields, fieldid);

                    self.nav().filterBy('for', stepid).trigger('clear');
                },

                '{saveButton} click': function(el, ev) {
                    ev.preventDefault();

                    el.addClass('btn-loading');

                    self.form().validate()
                        .done(function() {
                            // Check if this buttons has a value for data-edit-save to indicate if recurring should save all
                            if (el.data('editSave') === 'all') {
                                self.saveApply().val(1);
                            }

                            self.form().submit();
                        })
                        .fail(function() {
                            el.removeClass('btn-loading');
                            EasySocial.dialog({
                                content: EasySocial.ajax('site/views/profile/showFormError')
                            });
                        });
                }
            }
        });

        module.resolve();
    });
});
