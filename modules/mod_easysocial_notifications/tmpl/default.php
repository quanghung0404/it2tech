<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div id="fd" class="es mod-es-notifications module-menu<?php echo $suffix;?>">
	<div class="es-notification">

		<div class="es-menu-items">

			<?php if( $params->get( 'show_system_notifications' , true ) ){ ?>
			<div class="es-menu-item notice-recent"
				data-original-title="<?php echo JText::_( 'MOD_EASYSOCIAL_NOTIFICATIONS_NOTIFICATIONS' );?>"
				data-es-provide="tooltip"
				data-placement="bottom"
			>
				<a href="javascript:void(0);" data-popbox="module://easysocial/notifications/popbox"
				   class="<?php echo $my->getTotalNewNotifications() > 0 ? 'has-notice' : '';?>"
				   data-module-notifications-system
				   data-interval="<?php echo $params->get('interval_notifications_system', 60 );?>"
				   data-popbox-toggle="click"
				   data-popbox-position="<?php echo $params->get('popbox_position', 'bottom'); ?>"
                   data-popbox-offset="<?php echo $params->get('popbox_offset', 10); ?>"
                   >
					<i class="fa fa-globe"></i>
					<span class="badge badge-notification" data-notificationSystem-counter><?php echo $my->getTotalNewNotifications();?></span>
				</a>
			</div>
			<?php } ?>

			<?php if( $params->get( 'show_friends_notifications' , true ) ){ ?>
			<div class="es-menu-item notice-friend"
				data-original-title="<?php echo JText::_( 'MOD_EASYSOCIAL_NOTIFICATIONS_FRIEND_REQUESTS' );?>"
				data-es-provide="tooltip"
				data-placement="bottom"
			>
				<a href="javascript:void(0);"
				   class="<?php echo $my->getTotalFriendRequests() > 0 ? 'has-notice' : '';?>"
				   data-module-notifications-friends
				   data-interval="<?php echo $params->get('interval_notifications_friends', 60 );?>"
				   data-popbox="module://easysocial/friends/popbox"
				   data-popbox-toggle="click"
				   data-popbox-position="<?php echo $params->get('popbox_position', 'bottom'); ?>"
				   data-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"
                   data-popbox-offset="<?php echo $params->get('popbox_offset', 10); ?>"
                   >
					<i class="fa fa-users"></i>
					<span class="badge badge-notification" data-notificationFriends-counter><?php echo $my->getTotalFriendRequests();?></span>
				</a>
			</div>
			<?php } ?>

			<?php if( $params->get( 'show_conversation_notifications' , true ) ){ ?>
			<div class="es-menu-item notice-message"
				data-original-title="<?php echo JText::_( 'MOD_EASYSOCIAL_NOTIFICATIONS_CONVERSATIONS' );?>"
				data-es-provide="tooltip"
				data-placement="bottom"
			>
				<a href="javascript:void(0);" data-popbox="module://easysocial/conversations/popbox"
				   class="<?php echo $my->getTotalNewConversations() > 0 ? 'has-notice' : '';?>"
				   data-module-notifications-conversations
				   data-interval="<?php echo $params->get('interval_notifications_conversations', 60 );?>"
				   data-popbox-toggle="click"
				   data-popbox-position="<?php echo $params->get('popbox_position', 'bottom'); ?>"
				   data-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"
                   data-popbox-offset="<?php echo $params->get('popbox_offset', 10); ?>"
                   >
					<i class="fa fa-envelope"></i>
					<span class="badge badge-notification" data-notificationConversation-counter><?php echo $my->getTotalNewConversations();?></span>
				</a>
			</div>
			<?php } ?>
		</div>
	</div>

</div>
