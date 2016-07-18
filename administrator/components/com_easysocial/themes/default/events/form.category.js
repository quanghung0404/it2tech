EasySocial.ready(function($) {
    $('[data-form-tabs]').on('click', function(){

        // Check to see if there's any data-tab-active input
        var currentInput = $('[data-tab-active]');

        if (currentInput) {
            var selected = $(this).data('item');

            currentInput.val(selected);
        }
    });

    $.Joomla('submitbutton', function(task) {
        if (task === 'cancel') {
            window.location = "<?php echo FRoute::url(array('view' => 'events', 'layout' => 'categories')); ?>";
            return false;
        }

        var title = $('input[name="title"]').val();

        if ($.isEmpty(title)) {
            EasySocial.dialog({
                content: '<?php echo JText::_('COM_EASYSOCIAL_EVENTS_CATEGORY_TITLE_REQUIRED'); ?>',
                width: 400,
                height: 100
            });

            return;
        }

        <?php if( $category->id ) { ?>
        if (task == 'applyCategory' || task == 'saveCategory' || task == 'saveCategoryNew') {
            (function(id) {
                var result = [];

                // Define all custom saving process here

                // Prepare data to save fields
                result.push($('.profileFieldForm').controller().save(task));

                if (result.length > 0) {
                    $.when.apply(null, result).done(function() {
                        $.Joomla('submitform', [task]);
                    });

                    return;
                }

                $.Joomla('submitform', [task]);

                return;
            })(<?php echo $category->id; ?>);

            return false;
        }
        <?php } ?>

        $.Joomla('submitform', [task]);
    });

    $('[data-category-avatar-remove-button]').on('click', function() {
        var button = $(this),
            id = button.data('id'),
            image = $('[data-category-avatar-image]'),
            defaultAvatar = $('[data-category-avatar]').data('defaultavatar'),
            removeWrap = $('[data-category-avatar-remove-wrap]');

        EasySocial.dialog({
            content: EasySocial.ajax('admin/views/groups/confirmRemoveCategoryAvatar', {
                id: id
            }),
            bindings: {
                '{deleteButton} click': function() {
                    EasySocial.ajax('admin/controllers/events/removeCategoryAvatar', {
                        id: id
                    }).done(function() {
                        image.attr('src', defaultAvatar);

                        removeWrap.remove();

                        EasySocial.dialog().close();
                    });
                }
            }
        })
    });
});
