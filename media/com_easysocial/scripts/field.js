EasySocial.module('field', function($) {
    var module = this;

    EasySocial.Controller('Field.Base', {
        defaultOptions: {
            regPrefix   : 'easysocial/',

            modPrefix   : 'field.',

            ctrlPrefix  : 'EasySocial.Controller.Field.',

            fieldname   : '',

            element     : null,

            id          : null,

            required    : false,

            mode        : 'edit',

            '{field}'   : '[data-field]',

            '{content}' : '[data-content]',

            '{notice}'  : '[data-check-notice]'
        }
    }, function(self) {
        return {
            init: function() {

                self.options.fieldname = self.element.data('fieldname');

                self.options.element = self.options.element || self.element.data('element');

                self.options.id = self.element.data('id');

                self.options.required = !!self.element.data('required');

                self.initMode();

                // Check if there are errors

                if (self.notice().text().trim().length > 0) {
                    self.content().popover({
                        animation: false,
                        content: self.notice().text().trim(),
                        html: true,
                        placement: 'left-top',
                        trigger: 'manual',
                        container: 'body',
                        template: '<div id="fd" class="fd-popover es es-field-error"><div class="arrow"></div><h3 class="fd-popover-title"></h3><div class="fd-popover-content"></div></div>'
                    });

                    self.content().popover('show');
                }
            },

            initMode: function() {
                // Trigger the necessary mode here for field to do necessary init
                switch(self.options.mode)
                {
                    case 'registermini':
                        self.field().trigger('onRegisterMini');
                        break;
                    case 'register':
                        self.field().trigger('onRegister');
                        break;
                    case 'edit':
                        self.field().trigger('onEdit');
                        break;
                    case 'adminedit':
                        self.field().trigger('onAdminEdit');
                        break;
                    case 'sample':
                        self.field().trigger('onSample');
                        break;
                    case 'display':
                        self.field().trigger('onDisplay');
                        break;
                }
            },

            // Some base triggers/functions
            '{field} error': function(el, ev, state, msg) {
                state = state !== undefined ? state : true;

                if($.isString(state)) {
                    msg = state;
                    state = true;
                }

                if($.isBoolean(state)) {
                    self.field().toggleClass('has-error', state);
                }

                if(msg !== undefined) {
                    self.content().popover({
                        animation: false,
                        content: msg,
                        html: true,
                        placement: 'left-top',
                        trigger: 'manual',
                        container: 'body',
                        template: '<div id="fd" class="fd-popover es es-field-error"><div class="arrow"></div><h3 class="fd-popover-title"></h3><div class="fd-popover-content"></div></div>'
                    });

                    self.content().popover('show');
                }
            },

            '{field} clear': function(el, ev) {
                self.field().removeClass('has-error');
                self.field().removeClass('is-loading');

                self.content().popover('destroy');
            },

            '{self} show': function() {
                self.field().trigger('onShow');
            },

            '{field} loading': function(el, ev, msg) {
                self.field().addClass('is-loading');

                self.notice().html(msg);
            },

            '{field} loaded': function(el, ev) {
                self.field().removeClass('is-loading');
            }
        }
    });

    module.resolve();
});
