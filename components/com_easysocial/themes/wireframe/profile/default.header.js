

EasySocial.require()
	.script( 'avatar' , 'cover' )
	.done(function($)
	{
		$( '[data-profile-avatar]' ).implement( EasySocial.Controller.Avatar ,
			{
				"uid"	: "<?php echo $user->id;?>",
				"type"	: "<?php echo SOCIAL_TYPE_USER;?>",
				"redirectUrl" : "<?php echo $user->getPermalink( false );?>"
			}
		);

		$( '[data-profile-cover]' ).implement( EasySocial.Controller.Cover , 
			{
				"uid"	: "<?php echo $user->id;?>",
				"type"	: "<?php echo SOCIAL_TYPE_USER;?>"
			}
		);
	});