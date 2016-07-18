
EasySocial.require()
.script('videos/process')
.done(function($){

    // Implement the processing controller here.
    $('[data-video-process]').implement(EasySocial.Controller.Videos.Process);
});