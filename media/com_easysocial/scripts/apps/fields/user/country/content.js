EasySocial.module('apps/fields/user/country/content', function($) {
    var module = this;

    EasySocial
        .require()
        .library('textboxlist')
        .language(
            'PLG_FIELDS_COUNTRY_VALIDATION_REQUIRED',
            'PLG_FIELDS_COUNTRY_VALIDATION_MINIMUM_ERROR',
            'PLG_FIELDS_COUNTRY_VALIDATION_MAXIMUM_ERROR'
        )
        .done(function() {
            EasySocial.Controller('Field.Country', {
                defaultOptions: {
                    fieldname       : '',

                    required        : false,

                    id              : null,

                    min             : null,

                    max             : null,

                    selecttype      : 'textboxlist',

                    '{field}'       : '[data-field-country]',

                    '{inputTextboxlist}'    : '[data-country-select-textboxlist]',

                    '{inputMultilist}'      : '[data-country-select-multilist]',

                    '{inputCheckbox}'       : '[data-country-select-checkbox]',

                    '{inputCheckboxes}'     : '[data-country-select-checkbox] input',

                    '{inputDropdown}'       : '[data-country-select-dropdown]'
                }
            }, function(self) {
                return {
                    init: function() {

                        self.options.max = self.field().data('max');
                        self.options.min = self.field().data('min');

                        self.options.selecttype = self.field().data('select-type');

                        if(self.options.selecttype === 'textboxlist') {
                            EasySocial.module('field.country/' + self.options.id).done(function(data) {
                                self.inputTextboxlist().textboxlist({
                                    component: 'es',
                                    name: self.options.fieldname + '[]',
                                    max: self.options.max < 1 ? null : self.options.max,
                                    plugin: {
                                        autocomplete: {
                                            exclusive: true,
                                            query: data
                                        }
                                    }
                                });
                            });
                        }
                    },

                    '{inputMultilist} change': function(el, ev) {
                        if(self.options.max > 0 && el.val().length > self.options.max) {
                            el.val(self.lastValidSelection ? self.lastValidSelection : '');

                            return false;
                        }

                        self.lastValidSelection = el.val();
                    },

                    '{inputCheckboxes} change': function(el, ev) {
                        var count = self.inputCheckboxes(':checked').length;

                        if(self.options.max > 0 && count > self.options.max) {
                            el.removeAttr('checked');
                            return false;
                        }
                    },

                    validateInput: function() {
                        var items = null;

                        if(self.options.selecttype === 'textboxlist') {
                            items = self.inputTextboxlist().controller('textboxlist').getAddedItems();
                        }

                        if(self.options.selecttype === 'multilist') {
                            items = self.inputMultilist().val();
                        }

                        if(self.options.selecttype === 'checkbox') {
                            items = self.inputCheckboxes(':checked');
                        }

                        if(self.options.selecttype === 'dropdown') {
                            var value = self.inputDropdown().val();

                            if(!$.isEmpty(value)) {
                                items = [value];
                            }
                        }

                        var count = items ? items.length : 0;

                        // If it is not required and no selection is made, then we pass this check.
                        // If there is selection made, then we have to check against the minimum and maximum count
                        if (!self.options.required && count === 0) {
                            return true;
                        }

                        if(self.options.required && count < 1) {
                            self.raiseError($.language('PLG_FIELDS_COUNTRY_VALIDATION_REQUIRED'));
                            return false;
                        }

                        if(self.options.min > 0 && count < self.options.min) {
                            self.raiseError($.language('PLG_FIELDS_COUNTRY_VALIDATION_MINIMUM_ERROR'));
                            return false;
                        }

                        if(self.options.max > 0 && count > self.options.max) {
                            self.raiseError($.language('PLG_FIELDS_COUNTRY_VALIDATION_MAXIMUM_ERROR'));
                            return false;
                        }

                        return true;
                    },

                    raiseError: function(msg) {
                        self.trigger('error', [msg]);
                    },

                    '{self} onSubmit': function(el, ev, register) {
                        register.push(self.validateInput());
                    }
                }
            });

            module.resolve();
        });
});
