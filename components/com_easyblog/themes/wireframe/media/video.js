
EasyBlog.require()
    .library("videojs")
    .done(function($){

        videojs('<?php echo $id;?>', {"controls": true}, function() {

        });
    });