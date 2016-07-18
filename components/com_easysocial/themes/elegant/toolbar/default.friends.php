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
<li class="toolbarItem<?php echo $requests > 0 ? ' has-notice' : '';?>"
	data-popbox="module://easysocial/friends/popbox"
	data-popbox-toggle="click"
	data-popbox-position="<?php echo $popboxPosition;?>"
	data-popbox-collision="<?php echo $popboxCollision;?>"
	data-notifications-friends
>
	<a href="javascript:void(0);" class="loadRequestsButton"
		data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_FRIEND_REQUESTS' , true );?>"
		data-placement="top"
		data-es-provide="tooltip"
	>
		<i class="fa fa-users"></i>
		<span class="label label-notification" data-notificationFriends-counter><?php if( $requests > 0 ){ ?><?php echo $requests; ?><?php } ?></span>
	</a>
</li>
