EasySocial.module('site/layout/responsive', function($){

    var module = this;

    $(function(){

        $.responsive(".es-responsive", [
            {at: 2500, switchTo: 'extra-wide wide'}, // http://stackideas.com/forums/profile-layout-breaks-when-resizing-back-up
            {at: 1200, switchTo: 'wide'},
            {at: 960,  switchTo: 'wide w960'},
            {at: 818,  switchTo: 'wide w960 w768'},
            {at: 600,  switchTo: 'wide w960 w768 w600'},
            {at: 560,  switchTo: 'wide w960 w768 w600 w480'},
            {at: 480,  switchTo: 'wide w960 w768 w600 w480 w320'}
        ]);

        $(document).on("responsive", $.debounce(function(){
            ESImageRefresh();
        }, 500));
    });

    module.resolve();

});
