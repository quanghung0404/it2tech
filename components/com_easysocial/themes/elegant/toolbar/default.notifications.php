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
<li class="toolbarItem<?php echo $newNotifications > 0 ? ' has-notice' : '';?>"
	data-popbox="module://easysocial/notifications/popbox"
	data-popbox-toggle="click"
	data-popbox-position="<?php echo $popboxPosition;?>"
	data-popbox-collision="<?php echo $popboxCollision;?>"
	data-autoread="<?php echo $this->config->get('notifications.system.autoread');?>"
	data-user-id="<?php echo $this->my->id;?>"
	data-notifications-system
	>

	<a href="javascript:void(0);"
		data-original-title="<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_RECENT_NOTIFICATIONS', true);?>"
		data-placement="top"
		data-es-provide="tooltip"
	>
		<i class="fa fa-globe"></i>
		<span class="label label-notification" data-notificationSystem-counter><?php echo $newNotifications > 0 ? $newNotifications : '';?></span>
	</a>
</li>
