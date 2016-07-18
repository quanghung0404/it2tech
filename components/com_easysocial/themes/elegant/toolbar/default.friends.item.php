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
<?php if( $connections ){ ?>
	<?php foreach( $connections as $connection ){ ?>
	<li class="pa-10" data-notification-friend-item>
		<div class="media notice-friend">
			<div class="media-object pull-left">
				<div class="es-avatar-wrap">
					<div class="es-avatar">
						<a href="<?php echo $connection->user->getPermalink();?>">
							<img src="<?php echo $connection->user->getAvatar();?>" alt="<?php echo $this->html( 'string.escape' , $connection->user->getName() );?>" />
							<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $connection->user->isOnline() , 'size' => 'mini' ) ); ?>
						</a>
					</div>
				</div>
			</div>
			<div class="media-body">
				<div class="pull-right object-action">
					<div class="es-action" data-friend-item-actions>

						<i class="loading-indicator fd-small"></i>

						<a class="btn btn-es-primary btn-small view-profile" href="<?php echo FRoute::profile( array( 'id' => $connection->user->getAlias() ) );?>" data-friend-item-action>
							<?php echo JText::_( 'COM_EASYSOCIAL_VIEW_PROFILE_BUTTON' ); ?> <i class="ies-arrow-right ies-small ies-white"></i>
						</a>
						<a class="btn btn-es btn-small reject-friend" href="javascript:void(0);" data-friend-item-reject data-friend-item-action data-id="<?php echo $connection->id;?>">
							<?php echo JText::_( 'COM_EASYSOCIAL_REJECT_BUTTON' );?>
						</a>
						<a class="btn btn-es-primary btn-small accept-friend" href="javascript:void(0);" data-friend-item-accept data-friend-item-action data-id="<?php echo $connection->id;?>">
							<?php echo JText::_( 'COM_EASYSOCIAL_ACCEPT_BUTTON' );?>
						</a>
					</div>
				</div>
				<div class="object-info">
					<div data-friend-item-title class="fd-small">
						<a href="<?php echo $connection->user->getPermalink();?>"><?php echo $connection->user->getName();?></a>
					</div>

					<span class="fd-small" data-friend-item-mutual>
						<?php echo $this->loadTemplate( 'site/toolbar/friends.mutual' , array( 'user' => $connection->user ) ); ?>
					</span>
				</div>

			</div>
		</div>
	</li>
	<?php } ?>
<?php } else { ?>
	<li class="requestItem empty center">
		<div class="mt-20 pt-20 small">
			<i class="ies-users ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_FRIENDS_NO_FRIENDS_YET' ); ?>
		</div>
	</li>
<?php } ?>
