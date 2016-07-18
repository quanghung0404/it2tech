
<?php if (JRequest::getVar('layout') != 'composer') { ?>
EasyBlog.require()
.script('layout/responsive')
.done(function($){

	$('#eb-topbar').responsive({at: 540, switchTo: 'narrow'});

    // Apply counter checking
    <?php if ($this->acl->get('moderate_entry')) { ?>
    EasyBlog.ajax('site/views/dashboard/getModerationCount')
    .done(function(total){


        if (total != 0) {
            $('[data-moderate-counter]').html(total).removeClass('hide');
        }

    });
    <?php } ?>
    
    $('[data-dashboard-sign-out]').on('click', function(){ 
        $('[data-dashboard-sign-out-form]').submit();
    });
});
<?php } ?>