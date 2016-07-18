EasySocial.module( 'oauth/facebook', function($) {
	
	var module = this;

	$(document).on("click", "[data-oauth-facebook-login]", function(){

		var button = $(this),
			parent = button.parents("[data-oauth-facebook]"),
			controller = "EasySocial.Controller.OAuth.Facebook";

		if (parent.length < 1) return;
		if (parent.hasController(controller)) return;

		parent
			.addController(controller, {
				url: button.data("oauth-facebook-url"),
				appId: button.data("oauth-facebook-appid")
			})
			.openDialog();
	});


	EasySocial.Controller( 'OAuth.Facebook',
	{
		defaultOptions :
		{
			appId: null,
			url: "",
			"{login}"	: "[data-oauth-facebook-login]",
			"{revoke}"	: "[data-oauth-facebook-revoke]",

			"{pushInput}"	: "[data-oauth-facebook-push]"
		}
	},
	function( self )
	{
		return {
			init : function()
			{
			},

			openDialog : function()
			{
				var url = self.options.url,
					left	= (screen.width/2)-( 300 /2),
					top		= (screen.height/2)-( 300 /2);
					
				window.open( url , "" , 'scrollbars=no,resizable=no,width=300,height=300,left=' + left + ',top=' + top );
			},

			"{pushInput} change" : function( el )
			{
				var enabled 	= $(el).val();
				
				if( enabled == 1 && self.options.requestPush )
				{
					self.openDialog( self.options.addPublishURL )
				}

				if( enabled == 0 )
				{
					self.openDialog( self.options.revokePublishURL );
				}
			},

			"{login} click" : function()
			{
				self.openDialog();
			},

			"{revoke} click" : function()
			{
				var callback 	= self.element.data( 'callback' );
				
				EasySocial.dialog(
				{
					content 	: EasySocial.ajax( 'site/views/oauth/confirmRevoke' , { "client" : 'facebook' , "callbackUrl" : callback } )
				});
			}
		}
	});

	module.resolve();

}); // module end
