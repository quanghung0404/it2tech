
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/

EasySocial.require()
.script(
	'site/profile/miniheader',
	'site/profile/friends',
	'site/profile/subscriptions',
	'site/conversations/composer'
)
.done(function($)
{
	$( '[data-appscroll]' ).addController("EasySocial.Controller.Profile.MiniHeader");

	// Apply friends controller
	$( '[data-profile-friends]' ).implement( EasySocial.Controller.Profile.Friends.Request );

	// Apply conversation controller
	$( '[data-profile-conversation]' ).implement( EasySocial.Controller.Conversations.Composer.Dialog ,
	{
		"recipient" :
		{
			"id"	: "<?php echo $user->id;?>",
			"name"	: "<?php echo $this->html( 'string.escape', $user->getName() );?>",
			"avatar": "<?php echo $user->getAvatar();?>"
		}
	});

	// Apply follow / unfollow controller
	$( '[data-profile-followers]' ).implement( EasySocial.Controller.Profile.Subscriptions );
});
