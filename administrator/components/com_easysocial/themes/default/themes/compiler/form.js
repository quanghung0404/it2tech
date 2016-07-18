EasySocial.require()
.script("admin/themes/compiler")
.done(function($){
	$("[data-compiler=<?php echo $uuid; ?>]").addController("EasySocial.Controller.Themes.Compiler");
});