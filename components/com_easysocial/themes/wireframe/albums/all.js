
EasySocial.require()
.script('site/albums/all')
.done(function($) {
	
    $('[data-albums]').implement(EasySocial.Controller.Albums.All.Browser);
});