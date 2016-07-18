EasySocial.module("site/photos/dialog", function($){

var module = this;

var controller =
EasySocial.Controller("Photos.Dialog",
{
	defaultOptions: {
	}
},
function(self) { return {

	init: function() {

		// Legacy compatiblity
		// Old: EasySocial.photos.selectPhoto()
		// New: EasySocial.photos.dialog.show();
		EasySocial.photos.selectPhoto = self.show;
	},

	show: function(options) {

		var task	= $.Deferred(),
			dialog	= EasySocial.ajax( "site/views/albums/dialog" , { "uid" : options.uid , "type" : options.type }),
			browser = EasySocial.require().script( "albums/browser" ).done();

		// Show a loading indicator first
		EasySocial.dialog(
			$.extend({
			    content: task
			}, options)
		);

		$.when(browser, dialog)
			.done(function(){
				dialog.done(function(html){
					task.resolve(html);
				});
			});
	}

}});

module.resolve(controller);

});
