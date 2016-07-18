EasySocial.module("site/photos/photos", function($){

var module = this;

// Non-essential dependencies
EasySocial.require()
	.script(
		"site/photos/popup",
		"site/photos/dialog",
		"site/photos/avatar"
	)
	.done();

EasySocial.Controller("Photos",
{
	defaultOptions: {
	}
},
function(self) { return {

	init: function() {

		// Extend EasySocial object
		EasySocial.photos = self;

		// Popup plugin
		EasySocial.module("site/photos/popup")
			.done(function(PopupController){
				self.popup = self.addPlugin("popup", PopupController);
			});

		// Dialog plugin
		EasySocial.module("site/photos/dialog")
			.done(function(DialogController){
				self.dialog = self.addPlugin("dialog", DialogController);
			});

		// Avatar plugin
		EasySocial.module("site/photos/avatar")
			.done(function(AvatarController){
				self.avatar = self.addPlugin("avatar", AvatarController);
			});
	}

}});

// Add this controller to the html body;
$(function(){
	$("body").addController("EasySocial.Controller.Photos");
});

module.resolve();

});