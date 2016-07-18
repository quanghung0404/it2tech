EasySocial
.require()
.script('site/events/item')
.done(function($){
    $('[data-event-item]').addController('EasySocial.Controller.Events.Item', {
        id: <?php echo $event->id; ?>
    });
});
