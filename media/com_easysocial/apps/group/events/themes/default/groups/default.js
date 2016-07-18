EasySocial.require().script('site/events/browser').done(function($) {
    $('[data-group-events-list]').addController('EasySocial.Controller.Events.Browser', {
        group: '<?php echo $group->id; ?>'
    });
});
