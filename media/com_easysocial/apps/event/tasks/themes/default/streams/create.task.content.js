EasySocial.ready(function($) {

    $('[data-task-<?php echo $stream->uid; ?>-checkbox]').on('change', function() {
        var taskId = $(this).val(),
            parentItem = $(this).parents('li');

        if ($(this).is(':checked')) {
            EasySocial.ajax('apps/event/tasks/controllers/tasks/resolve', {
                "id": taskId,
                "eventId": "<?php echo $event->id; ?>"
            })
            .done(function() {
                $(parentItem).addClass('completed');
            });
        } else {
            EasySocial.ajax('apps/event/tasks/controllers/tasks/unresolve', {
                "id": taskId,
                "eventId": "<?php echo $event->id; ?>"
            })
            .done(function() {
                $(parentItem).removeClass('completed');
            });
        }
    });
});
