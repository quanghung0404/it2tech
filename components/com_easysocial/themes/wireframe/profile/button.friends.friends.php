<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<a href="javascript:void(0);" class="btn btn-es-primary btn-sm btn-action-friends is-friends"
	data-profileFriends-button
	data-profileFriends-manage
	data-bs-toggle="dropdown"
>
	<i class="fa fa-check mr-5"></i>
	<span><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_FRIENDS' );?></span>
	<i class="fa fa-caret-down "></i>
</a>

<ul class="dropdown-menu dropdown-arrow-topleft dropdown-friends" data-profileFriends-dropdown>
	<li data-friends-unfriend>
		<a href="javascript:void(0);" data-profile-friends-unfriend data-id="<?php echo $user->id;?>"><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_UNFRIEND' );?></a>
	</li>
</ul>
