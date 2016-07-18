EasySocial.require().script('apps/fields/event/recurring/content').done(function($) {

    $('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Event.Recurring', {
        value: <?php echo FD::json()->encode($original); ?>,
        allday: <?php echo $allday ? 1 : 0; ?>,
        showWarningMessages: <?php echo $showWarningMessages; ?>,
        eventId: <?php echo isset($eventId) ? $eventId : 'null'; ?>
    });

});
