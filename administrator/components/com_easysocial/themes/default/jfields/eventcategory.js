EasySocial.require()
.library('dialog')
.done(function($)
{
    <?php if (FD::version()->getVersion() < 3) { ?>
        $('body').addClass('com_easysocial25');
    <?php } ?>

    window.selectEventCategory  = function(obj) {
        $('[data-jfield-eventcategory-title]').val(obj.title);

        $('[data-jfield-eventcategory-value]').val(obj.id + ':' + obj.alias);

        EasySocial.dialog().close();
    }

    // Remove event category
    $('[data-jfield-eventcategory-remove]').on('click', function() {

        // Reset the category value
        $('[data-jfield-eventcategory-value]').val('');
        $('[data-jfield-eventcategory-title]').val('');

    });

    // Browse for event category button
    $('[data-jfield-eventcategory]').on('click', function() {
        EasySocial.dialog({
            content: EasySocial.ajax('admin/views/events/browseCategory', {
                'jscallback': 'selectEventCategory'
            })
        });
    });
});
