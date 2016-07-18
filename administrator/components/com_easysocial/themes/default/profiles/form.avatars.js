EasySocial
.require()
.script( 'admin/profiles/avatar' )
.done(function($){

	$( '[data-profile-avatars]' ).implement(
		'EasySocial.Controller.Profiles.Avatar',
		{
			"token"		: "<?php echo FD::token();?>"
		});
});
