
EasySocial.require()
.script('videos/list')
.done(function($){

    // Implement videos listing controller
    $('[data-videos-listing]').implement(EasySocial.Controller.Videos.List, {
        "uid": "<?php echo $uid;?>",
        "type": "<?php echo $type;?>"
    });
});