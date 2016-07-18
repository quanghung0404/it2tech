
EasyBlog.require()
.script('https://ws.sharethis.com/button/buttons.js')
.done(function($)
{
    stLight.options({
        publisher: "<?php echo $code;?>"
    });
});