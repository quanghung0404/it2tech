EasySocial.module('apps/fields/user/permalink/content', function($) {
    var module = this;

    EasySocial
        .require()
        .language(
            'PLG_FIELDS_PERMALINK_EXCEEDED_MAX_LENGTH',
            'PLG_FIELDS_PERMALINK_REQUIRED')
        .done(function($) {
            EasySocial.Controller('Field.Permalink', {
                defaultOptions:
                {
                    required: false,

                    max     : 0,

                    id      : null,
                    userid  : null,

                    '{field}'           : '[data-field-permalink]',

                    '{checkButton}'     : '[data-permalink-check]',
                    '{input}'           : '[data-permalink-input]',
                    '{available}'       : '[data-permalink-available]'
                }
            },
            function(self)
            {
                return {
                    state: false,

                    init: function()
                    {
                        self.options.max = self.field().data('max');
                    },

                    "{checkButton} click" : function()
                    {
                        self.delayedCheck();
                    },

                    "{input} keyup" : function()
                    {
                        self.delayedCheck();
                    },

                    delayedCheck: $.debounce(function() {
                        self.checkPermalink();
                    }, 250),

                    checkPermalink: function()
                    {
                        self.clearError();

                        var permalink   = self.input().val();

                        self.available().hide();

                        if(self.options.max > 0 && permalink.length > self.options.max) {
                            self.raiseError($.language('PLG_FIELDS_PERMALINK_EXCEEDED_MAX_LENGTH'));
                            return false;
                        }

                        if(!$.isEmpty(permalink))
                        {
                            self.checkButton().addClass('btn-loading');

                            var state = $.Deferred();

                            EasySocial.ajax('fields/user/permalink/isValid',
                            {
                                "id"        : self.options.id,
                                "userid"    : self.options.userid,
                                "permalink" : permalink
                            })
                            .done(function(msg)
                            {
                                self.checkButton().removeClass( 'btn-loading' );

                                self.available().show();

                                state.resolve();
                            })
                            .fail(function(msg)
                            {
                                self.raiseError(msg);

                                self.checkButton().removeClass('btn-loading');

                                self.available().hide();

                                state.reject();
                            });

                            return state;
                        }

                        if(self.options.required && $.isEmpty(permalink))
                        {
                            self.available().hide();

                            self.raiseError($.language('PLG_FIELDS_PERMALINK_REQUIRED'));
                            return false;
                        }

                        return true;
                    },

                    raiseError: function(msg)
                    {
                        self.trigger('error', [msg]);
                    },

                    clearError: function()
                    {
                        self.trigger('clear');
                    },

                    '{self} onSubmit': function(el, ev, register)
                    {
                        register.push(self.checkPermalink());
                    }
                }
            });

            module.resolve();
        });
});
