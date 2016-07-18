
EasySocial.require()
.library('videojs')
.done(function($) {

    videojs('video-<?php echo $uid;?>', {
        "controls": true,
        "poster": "<?php echo $video->getThumbnail();?>"
    }, function() {
    
    });
});