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
<?php if( $items ){ ?>
	<?php foreach( $items as $group => $notifications ) { ?>
	<div class="es-notifications">
		<h4 class="notification-date"><?php echo $group;?></h4>

		<ul class="fd-reset-list es-item-list">
			<?php foreach( $notifications as $item ){ ?>

			<li class="is-<?php echo $item->type;?><?php echo $item->state == SOCIAL_NOTIFICATION_STATE_READ ? ' is-read' : '';?><?php echo $item->state == SOCIAL_NOTIFICATION_STATE_HIDDEN ? ' is-hidden' : '';?><?php echo $item->state == SOCIAL_NOTIFICATION_STATE_UNREAD ? ' is-unread' : '';?>"
				data-notifications-list-item
				data-id="<?php echo $item->id;?>"
			>
				<div class="es-notification">
					<div class="is-noclick"></div>
					<a href="<?php echo FRoute::notifications( array( 'id' => $item->id , 'layout' => 'route' ) );?>" class="es-time"><?php echo $item->title;?></a>

					<?php if( $item->image || $item->content ){ ?>
					<div class="media">
						<?php if( $item->image ){ ?>
						<div class="media-object pull-left">
							<a href="<?php echo FRoute::notifications( array( 'id' => $item->id , 'layout' => 'route' ) );?>" class="es-time"><img src="<?php echo $item->image;?>" class="es-image" /></a>
						</div>
						<?php } ?>

						<?php if( $item->content ){ ?>
						<div class="media-body">
							<div class="es-notice-content"><?php echo $item->content; ?></div>
						</div>
						<?php } ?>
					</div>
					<?php } ?>

					<a href="<?php echo FRoute::notifications( array( 'id' => $item->id , 'layout' => 'route') );?>" class="es-time"><time><?php echo $item->since;?></time></a>

					<div class="pull-right es-notice-action">
						<a href="javascript:void(0);" class="btn btn-mini btn-es" data-bs-toggle="dropdown">
							<i class="fa fa-caret-down "></i>
						</a>
						<ul class="dropdown-menu dropdown-menu-lists dropdown-arrow-topright small">
							<li>
								<a href="javascript:void(0);" data-notifications-list-item-unread><?php echo JText::_( 'COM_EASYSOCIAL_NOTIFICATIONS_MARK_UNREAD' );?></a>
							</li>
							<li>
								<a href="javascript:void(0);" data-notifications-list-item-read><?php echo JText::_( 'COM_EASYSOCIAL_NOTIFICATIONS_MARK_READ' );?></a>
							</li>
							<li>
								<a href="javascript:void(0);" data-notifications-list-item-delete><?php echo JText::_( 'COM_EASYSOCIAL_NOTIFICATIONS_DELETE_ITEM' );?></a>
							</li>
						</ul>
					</div>

				</div>
			</li>
			<?php } ?>
		</ul>

	</div>
	<?php } ?>
<?php } ?>
