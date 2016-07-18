
EasyBlog.require()
.library('datetimepicker')
.done(function($) {

    $('[data-date-picker-<?php echo $hash;?>]')._datetimepicker({
        component: "eb"
    });
});