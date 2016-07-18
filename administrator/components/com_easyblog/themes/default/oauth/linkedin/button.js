
EasyBlog.ready(function($) {

    $('[data-linkedin-login-<?php echo $uid;?>]').on('click', function(){
        var left    = (screen.width/2)-( 300 /2);
        var top     = (screen.height/2)-( 300 /2);

        var url = '<?php echo $url;?>';

        window.open(url, '', 'width=300,height=300,left=' + left + ',top=' + top);
    });

    window.doneLogin = function(){
        window.location.href = '<?php echo $return;?>';
    }

});