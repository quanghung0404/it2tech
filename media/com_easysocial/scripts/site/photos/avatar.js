EasySocial.module("site/photos/avatar", function($){

var module = this;

var controller =
EasySocial.Controller("Photos.Avatar",
{
	defaultOptions: {
	}
},
function(self) { return {

	init: function() {

		// Legacy compatiblity
		// Old: EasySocial.photos.createAvatar();
		// New: EasySocial.photos.avatar.crop();
		EasySocial.photos.createAvatar = self.crop;
	},

	crop: function(id, options) {

		if( id == undefined )
		{
			return;
		}

		if( !options )
		{
			options 	= {};
		}

		var avatarOptions = { "id" : id };

		if( options.uid )
		{
			avatarOptions.uid 	= options.uid;
			delete options.uid;
		}

		if( options.type )
		{
			avatarOptions.type 	= options.type;
			delete options.type;
		}

		if( options.redirect )
		{
			avatarOptions.redirect = options.redirect;
			delete options.redirect;
		}

		EasySocial.dialog(
			$.extend(
				{
					content: EasySocial.ajax( 'site/views/avatar/crop' , avatarOptions )
				},
				options
			)
		);
	}

}});

module.resolve(controller);

});
