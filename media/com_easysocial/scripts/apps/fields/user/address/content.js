EasySocial.module('apps/fields/user/address/content', function($) {
    var module = this;

    EasySocial.require()
        .library('gmaps')
        .language(
            'PLG_FIELDS_ADDRESS_PLEASE_ENTER_ADDRESS1',
            'PLG_FIELDS_ADDRESS_PLEASE_ENTER_ADDRESS2',
            'PLG_FIELDS_ADDRESS_PLEASE_ENTER_CITY',
            'PLG_FIELDS_ADDRESS_PLEASE_ENTER_STATE',
            'PLG_FIELDS_ADDRESS_PLEASE_ENTER_ZIP',
            'PLG_FIELDS_ADDRESS_PLEASE_ENTER_COUNTRY')
        .done(function() {
            EasySocial.Controller('Field.Address', {
                defaultOptions: {
                    required        : {},
                    show            : {},

                    "{field}"       : "[data-field-address]",

                    "{address1}"    : "[data-field-address-address1]",
                    "{address2}"    : "[data-field-address-address2]",
                    "{city}"        : "[data-field-address-city]",
                    "{state}"       : "[data-field-address-state]",
                    "{country}"     : "[data-field-address-country]",
                    "{zip}"         : "[data-field-address-zip]",

                    '{required}'    : '[data-required]',

                    '{notice}'      : '[data-check-notice]'
                }
            }, function(self) {
                return {
                    init : function() {
                    },

                    fields: [
                        'address1',
                        'address2',
                        'city',
                        'state',
                        'zip',
                        'country'
                    ],

                    validateInput : function() {
                        self.clearError();

                        var errorRaised = false;

                        self.clearError();

                        $.each(self.fields, function(i, field) {
                            var el = self[field]();

                            el.removeClass('has-error');

                            var val = el.val();

                            if($.isEmpty(val) && self.options.required[field] && self.options.show[field])
                            {
                                el.addClass('has-error');

                                if(!errorRaised) {
                                    self.raiseError($.language('PLG_FIELDS_ADDRESS_PLEASE_ENTER_' + field.toUpperCase()));

                                    errorRaised = true;
                                }

                            }
                        });

                        return true;
                    },

                    '{address1}, {address2}, {zip}, {city}, {state} blur': function() {
                        self.validateInput();
                    },

                    '{country} change': function(el) {
                        self.validateInput();

                        if (self.state().is('select')) {
                            EasySocial.ajax('fields/user/address/getStates', {
                                id: self.options.id,
                                country: el.val()
                            }).done(function(states) {
                                self.state().html('');

                                $.each(states, function(code, name) {
                                    var option = $('<option></option>').html(name).val(name).appendTo(self.state());
                                });
                            });
                        }
                    },

                    raiseError: function(msg) {
                        // self.trigger('error', [msg]);

                        self.notice()
                            .css('color', '#a94442')
                            .text(msg)
                            .parent('.controls-error')
                            .show();
                    },

                    clearError: function() {
                        self.notice()
                            .parent('.controls-error')
                            .hide();
                    },

                    "{self} onSubmit" : function(el, event, register) {
                        register.push(self.validateInput());
                    },

                    "{self} onConfigChange": function(el, event, name, value) {
                        var requires = ['address1', 'address2', 'city', 'zip', 'state', 'country'];

                        if($.inArray(name, requires) >= 0) {
                            self.options.required[name] = !!value;
                        }

                        self.required().hide();

                        $.each(requires, function(i, t) {
                            if(self.options[t]) {
                                self.required().show();
                                return false;
                            }
                        });
                    }
                }
            });

            module.resolve();
        });
});
