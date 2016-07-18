EasySocial.module('apps/fields/user/multitextbox/content', function($) {
    var module = this;

    EasySocial
        .require()
        .library('ui/sortable')
        .language('PLG_FIELDS_MULTITEXTBOX_VALIDATION_REQUIRED_FIELD')
        .done(function() {
            EasySocial.Controller('Field.Multitextbox', {
                defaultOptions: {
                    required: false,

                    id: null,

                    inputName: '',

                    max: 0,

                    '{field}': '[data-field-multitextbox]',

                    '{list}': '[data-field-multitextbox-list]',

                    '{item}': '[data-field-multitextbox-item]',

                    '{input}': '[data-field-multitextbox-input]',

                    '{add}': '[data-field-multitextbox-add]',

                    '{delete}': '[data-field-multitextbox-delete]',

                    '{move}': '[data-field-multitextbox-move]'
                }
            }, function(self) {
                return {
                    init: function() {
                        self.options.max = self.field().data('max');

                        self.initSortable();
                    },

                    initSortable: function() {
                        self.list().sortable({
                            items: self.item.selector,
                            handle: self.move.selector
                        });
                    },

                    '{add} click': function(el) {
                        if (self.options.max < 1 || self.item().length < self.options.max)
                        {
                            var item = self.item().eq(0).clone();

                            item.find(self.input.selector)
                                .attr('value', '')
                                .val('');

                            self.list().append(item);
                        }

                        if(self.options.max > 0 && self.item().length >= self.options.max)
                        {
                            el.hide();
                        }
                    },

                    '{delete} click': function(el) {
                        var item = el.parents(self.item.selector);

                        if (self.item().length > 1) {
                            item.remove();
                        } else {
                            item.find(self.input.selector).val('');
                        }

                        if (self.options.max > 0 && self.item().length < self.options.max) {
                            self.add().show();
                        }
                    },

                    '{self} onConfigChange': function(el, ev, name, value) {
                        switch (name) {
                            case 'add_button_text':
                                self.add().text(value);
                            break;
                        }
                    },

                    raiseError: function() {
                        self.trigger('error', [$.language('PLG_FIELDS_MULTITEXTBOX_VALIDATION_REQUIRED_FIELD')]);
                    },

                    '{self} onSubmit': function(el, ev, register) {
                        if(!self.options.required) {
                            register.push(true);
                            return;
                        }

                        var state = false;

                        $.each(self.input(), function(i, element) {
                            if(!$.isEmpty($(element).val())) {
                                state = true;

                                return false;
                            }
                        });

                        if (!state) {
                            self.raiseError();
                        }

                        register.push(state);
                    }
                }
            });

            module.resolve();
        });
});
