EasySocial.require().script('site/events/createRecurring').done(function($) {
    $('[data-events-create]').addController('EasySocial.Controller.Events.CreateRecurring', {
        schedule: <?php echo FD::json()->encode($schedule); ?>,
        eventId: '<?php echo $event->id; ?>'
    });
});
