EasySocial.module('admin/regions/form', function($) {
    var module = this;

    EasySocial.require().language('COM_EASYSOCIAL_REGIONS_FORM_INCOMPLETE').done(function() {

        $.template('easysocial/parents.select', '<select name="parent_uid" data-parent-uid></select>');
        $.template('easysocial/parents.option', '<option value="[%= uid %]">[%= name %]</option>');

        EasySocial.Controller('Regions.Form', {
            defaultOptions: {
                '{type}': '[data-type]',
                '{parentBase}': '[data-parent-base]',
                '{parentContent}': '[data-parent-content]',
                '{parentUid}': '[data-parent-uid]',
                '{parentType}': '[data-parent-type]',

                view: {
                    parentsSelect: 'parents.select',
                    parentsOption: 'parents.option'
                }
            }
        }, function(self) {
            return {
                init: function() {
                    self.element.find('input[type="text"]').prop('disabled', !self.type().val());

                    self.element.find('[data-bs-toggle="radio-buttons"]').toggleClass('disabled', !self.type().val());

                    $.Joomla('submitbutton', function(task) {

                        if (task == 'cancel') {
                            window.location = 'index.php?option=com_easysocial&view=regions';
                            return false;
                        }

                        if (self.validate()) {
                            $.Joomla('submitform', [task]);
                        } else {
                            alert($.language('COM_EASYSOCIAL_REGIONS_FORM_INCOMPLETE'));
                        }
                    });
                },

                '{type} change': function(el) {
                    var parentType = el.find(':selected').data('parent');

                    self.parentType().val(parentType);

                    self.element.find('input[type="text"]').prop('disabled', !el.val());

                    self.element.find('[data-bs-toggle="radio-buttons"]').toggleClass('disabled', !el.val());

                    if (parentType) {
                        self.parentBase().show();

                        !self.parentContent().data('loaded') &&
                        self.getParents(parentType)
                            .done(function(parents) {
                                var base = $(self.view.parentsSelect());

                                $.each(parents, function(i, parent) {
                                    self.view.parentsOption({
                                        uid: parent.uid,
                                        name: parent.name
                                    }).appendTo(base);
                                });

                                self.parentContent().html(base);
                            });
                    } else {
                        self.parentBase().hide();
                    }
                },

                getParents: $.memoize(function(key) {
                    return EasySocial.ajax('admin/controllers/regions/getParents', {
                        type: key
                    });
                }),

                validate: function() {
                    return self.type().val() && self.element.find('input[name="name"]').val() && self.element.find('input[name="code"]').val();
                }
            }
        });

        module.resolve();
    });
});
