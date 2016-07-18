EasySocial.module('apps/fields/user/joomla_fullname/content', function($) {
    var module = this;

    EasySocial
        .require()
        .language('PLG_FIELDS_JOOMLA_FULLNAME_VALIDATION_EMPTY_NAME')
        .done(function($) {
            EasySocial.Controller('Field.Joomla_fullname', {
                defaultOptions: {
                    nameFormat      : 1,

                    max             : 0,

                    required        : true,

                    '{field}'       : '[data-field-joomla_fullname]',

                    '{firstName}'   : '[data-field-jname-first]',
                    '{middleName}'  : '[data-field-jname-middle]',
                    '{lastName}'    : '[data-field-jname-last]',
                    '{name}'        : '[data-field-jname-name]'
                }
            }, function(self) {
                return {
                    init : function()
                    {
                        self.options.nameFormat = self.field().data('name-format');
                        self.options.max = self.field().data('max');
                    },

                    validateInput : function()
                    {
                        self.clearError();

                        if(!self.options.required) {
                            return true;
                        }

                        // Name format
                        // 1 - first, middle, last
                        // 2 - last, middle, first
                        // 3 - single name
                        // 4 - first, last
                        // 5 - last, first

                        if(self.options.nameFormat == 3) {
                            if($.isEmpty(self.name().val())) {
                                self.raiseError();
                                return false;
                            }

                            return true;
                        }

                        if($.isEmpty(self.firstName().val())) {
                            self.raiseError();
                            return false;
                        }

                        return true;
                    },

                    raiseError: function() {
                        self.trigger('error', [$.language('PLG_FIELDS_JOOMLA_FULLNAME_VALIDATION_EMPTY_NAME')]);
                    },

                    clearError: function() {
                        self.trigger('clear');
                    },

                    "{firstName} blur" : function(el, event) {
                        self.validateInput();
                    },

                    "{name} blur": function(el, event) {
                        self.validateInput();
                    },

                    "{self} onError": function(el, event, type, field) {
                        self.raiseError();
                    },

                    "{self} onSubmit" : function(el, event, register) {
                        register.push(self.validateInput());

                        return;
                    }
                }
            });

            module.resolve();
        });
});
