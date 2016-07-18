EasySocial
.require()
.script('site/events/buttonState')
.done(function($){
    $('[data-event-rsvp-button-wapper]').addController('EasySocial.Controller.Events.ButtonState');
});
