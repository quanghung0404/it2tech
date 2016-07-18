EasySocial
.require()
.script('avatar', 'cover')
.done(function($) {
    $('[data-event-avatar]').addController('EasySocial.Controller.Avatar', {
            uid: "<?php echo $event->id;?>",
            type: "<?php echo SOCIAL_TYPE_EVENT;?>",
            redirectUrl: "<?php echo $event->getPermalink(false);?>"
        }
   );

    $('[data-event-cover]').addController('EasySocial.Controller.Cover', {
            uid: "<?php echo $event->id;?>",
            type: "<?php echo SOCIAL_TYPE_EVENT;?>"
        }
   );

    $('[data-event-delete]').on('click', function() {
        EasySocial.dialog( {
            content: EasySocial.ajax('site/views/events/confirmDeleteEvent', {
                id: "<?php echo $event->id;?>"
            })
        });
    });

    $('[data-event-unpublish]').on('click', function() {
        EasySocial.dialog( {
            content: EasySocial.ajax('site/views/events/confirmUnpublishEvent', {
                id: "<?php echo $event->id;?>"
            })
        });
    });
});
