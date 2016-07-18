EasySocial.module('site/events/create', function($) {
    var module = this;

    EasySocial.require().script('validate', 'field').done(function() {
        EasySocial.Controller('Events.Create', {
            defaultOptions: {
                'previousLink': null,

                '{fields}': '[data-create-field]',

                '{previous}': '[data-create-previous]',

                '{next}': '[data-create-submit]'
            }
        }, function(self) {
            return {
                init: function() {
                    self.fields().addController('EasySocial.Controller.Field.Base');
                },

                '{previous} click': function() {
                    window.location = self.options.previousLink;
                },

                '{next} click': function(el) {
                    if (el.enabled()) {
                        el.disabled(true);

                        el.addClass('btn-loading');

                        self.element.validate()
                            .done(function() {
                                el.removeClass('btn-loading');
                                el.enabled(true);

                                self.element.submit();
                            })
                            .fail(function() {
                                el.removeClass('btn-loading');
                                el.enabled(true);

                                EasySocial.dialog({
                                    content: EasySocial.ajax('site/views/profile/showFormError')
                                });
                            });
                    }
                }
            }
        });

        module.resolve();
    });
});
