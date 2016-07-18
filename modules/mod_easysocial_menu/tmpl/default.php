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
<div id="fd" class="es mod-es-menu module-menu<?php echo $suffix;?>">
	<div class="es-user">
		<div class="media">
			<?php if( $params->get( 'show_avatar' , true ) ){ ?>
			<div class="media-object pull-left">
				<div class="es-avatar pull-left">
					<img src="<?php echo $my->getAvatar( SOCIAL_AVATAR_MEDIUM );?>" alt="<?php echo $modules->html( 'string.escape' , $my->getName() );?>" />
				</div>
			</div>
			<?php } ?>

			<div class="media-body">
				<div class="user-info">
					<?php if ($params->get('show_name', true)) { ?>
					<div class="user-name">
						<a href="<?php echo $my->getPermalink();?>" class="user-name-link"><?php echo $my->getName();?></a>
					</div>
					<?php } ?>

					<?php if ($params->get('show_points', true)) { ?>
					<div class="user-points">
						<div>
							<a href="<?php echo FRoute::points( array( 'layout' => 'history' , 'userid' => $my->getAlias() ) );?>"><?php echo $my->getPoints();?> <?php echo JText::_( 'MOD_EASYSOCIAL_MENU_POINTS' );?></a>
						</div>
					</div>
					<?php } ?>

					<?php if ($params->get('show_edit', true)) { ?>
					<div class="user-edit">
						<a href="<?php echo FRoute::profile( array( 'layout' => 'edit' ) );?>"><i class="fa fa-pencil"></i></a>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<?php if ($params->get('show_notifications', true)) { ?>
	<div class="es-notification">
		<div class="es-menu-items">
			<?php if( $params->get( 'show_system_notifications' , true ) ){ ?>
			<div class="es-menu-item notice-recent has-notice"
			     data-original-title="<?php echo JText::_( 'MOD_EASYSOCIAL_MENU_NOTIFICATIONS' );?>"
				 data-es-provide="tooltip"
				 data-placement="bottom">

				<a href="javascript:void(0);"
				   class="<?php echo $my->getTotalNewNotifications() > 0 ? 'has-notice' : '';?>"
				   data-popbox="module://easysocial/notifications/popbox"
				   data-popbox-toggle="click"
				   data-module-esmenu-system
				   data-interval="<?php echo $params->get('interval_notifications_system', 60 );?>"
				   data-popbox-position="<?php echo $params->get('popbox_position', 'bottom'); ?>"
				   data-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"
				   data-user-id="43">
					<i class="fa fa-globe"></i>
					<span class="badge badge-notification" data-notificationSystem-counter><?php echo $my->getTotalNewNotifications();?></span>
				</a>
			</div>
			<?php } ?>

			<?php if( $params->get( 'show_friends_notifications' , true ) ){ ?>
			<div class="es-menu-item notice-friend has-notice"
				 data-original-title="<?php echo JText::_( 'MOD_EASYSOCIAL_MENU_FRIEND_REQUESTS' );?>"
				 data-es-provide="tooltip"
				 data-placement="bottom">

				<a href="javascript:void(0);"
				   class="<?php echo $my->getTotalFriendRequests() > 0 ? 'has-notice' : '';?>"
				   data-popbox="module://easysocial/friends/popbox"
				   data-popbox-toggle="click"
				   data-module-esmenu-friends
				   data-interval="<?php echo $params->get('interval_notifications_friends', 60 );?>"
				   data-popbox-position="<?php echo $params->get('popbox_position', 'bottom'); ?>"
				   data-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"
				   >
					<i class="fa fa-users"></i>
					<span class="badge badge-notification" data-notificationFriends-counter><?php echo $my->getTotalFriendRequests();?></span>
				</a>
			</div>
			<?php } ?>

			<?php if( $params->get( 'show_conversation_notifications' , true ) ){ ?>
			<div class="es-menu-item notice-message has-notice"
				 data-original-title="<?php echo JText::_( 'MOD_EASYSOCIAL_MENU_CONVERSATIONS' );?>"
				 data-es-provide="tooltip"
				 data-placement="bottom">

				<a href="javascript:void(0);"
				   class="<?php echo $my->getTotalNewConversations() > 0 ? 'has-notice' : '';?>"
				   data-popbox="module://easysocial/conversations/popbox"
				   data-popbox-toggle="click"
				   data-module-esmenu-conversations
				   data-interval="<?php echo $params->get('interval_notifications_conversations', 60 );?>"
				   data-popbox-position="<?php echo $params->get('popbox_position', 'bottom'); ?>"
				   data-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"
				   data-user-id="43">
					<i class="fa fa-envelope-o"></i>
					<span class="badge badge-notification" data-notificationConversation-counter><?php echo $my->getTotalNewConversations();?></span>
				</a>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<?php if( $params->get( 'show_achievements' , true ) ){ ?>
	<div class="es-badges">
		<div class="es-title"><?php echo JText::_( 'MOD_EASYSOCIAL_MENU_ACHIEVEMENTS' );?> <span class="total-badges">(<?php echo $my->getTotalBadges();?>)</span></div>
		<ul class="">
			<?php if( $my->getBadges() ){ ?>
				<?php foreach( $my->getBadges() as $badge ){ ?>
				<li>
					<a href="<?php echo $badge->getPermalink();?>"
						data-original-title="<?php echo $modules->html( 'string.escape' , $badge->get( 'title' ) );?>"
						data-placement="bottom"
						data-es-provide="tooltip"><img src="<?php echo $badge->getAvatar();?>" width="24" /></a>
				</li>
				<?php } ?>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>

	<?php if( $params->get( 'show_navigation' , true ) ){ ?>
	<div class="es-menu">
		<div class="es-title"><?php echo JText::_( 'MOD_EASYSOCIAL_MENU_NAVIGATION' );?>:</div>

		<ul class="es-menu-list">

			<?php if( $params->get( 'show_conversation' , true ) ){ ?>
			<li>
				<a href="<?php echo FRoute::conversations();?>">
					<span>
						<i class="fa fa-comments"></i>
						<?php echo JText::_( 'MOD_EASYSOCIAL_MENU_CONVERSATIONS' );?>
					</span>

					<?php if( $my->getTotalNewConversations() ){ ?>
					<span class="badge badge-notification"><?php echo $my->getTotalNewConversations();?></span>
					<?php } ?>
				</a>
			</li>
			<?php } ?>

			<?php if ($params->get('show_friends', true)) { ?>
			<li>
				<a href="<?php echo FRoute::friends();?>">
					<i class="fa fa-users"></i> <span><?php echo JText::_( 'MOD_EASYSOCIAL_MENU_FRIENDS' );?></span>
				</a>
			</li>
			<?php } ?>
				
			<?php if ($params->get('show_mygroups', true)) { ?>
			<li>
				<a href="<?php echo FRoute::groups(array('filter' => 'mine'));?>"><i class="fa fa-users"></i> <span><?php echo JText::_('MOD_EASYSOCIAL_MENU_MY_GROUPS');?></span></a>
			</li>
			<?php } ?>

			<?php if ($params->get('show_myevents', true)) { ?>
			<li>
				<a href="<?php echo FRoute::events(array('filter' => 'mine'));?>"><i class="fa fa-calendar"></i> <span><?php echo JText::_('MOD_EASYSOCIAL_MENU_MY_EVENTS');?></span></a>
			</li>
			<?php } ?>

			<?php if( $params->get( 'show_followers' , true ) ){ ?>
			<li>
				<a href="<?php echo FRoute::followers();?>">
					<i class="fa fa-share-alt "></i> <span><?php echo JText::_( 'MOD_EASYSOCIAL_MENU_FOLLOWERS' );?></span>
				</a>
			</li>
			<?php } ?>

			<?php if( $params->get( 'show_photos' , true ) ){ ?>
			<li>
				<a href="<?php echo FRoute::albums( array( 'uid' => $my->getAlias() , 'type' => SOCIAL_TYPE_USER ) );?>">
					<i class="fa fa-photo "></i> <span><?php echo JText::_( 'MOD_EASYSOCIAL_MENU_PHOTOS' );?></span>
				</a>
			</li>
			<?php } ?>

			<?php if ($params->get('show_videos', true)) { ?>
			<li>
				<a href="<?php echo FRoute::videos();?>">
					<i class="fa fa-film"></i> <span><?php echo JText::_('MOD_EASYSOCIAL_MENU_VIDEOS');?></span>
				</a>
			</li>
			<?php } ?>

			<?php if( $params->get( 'show_apps' , true ) ){ ?>
			<li>
				<a href="<?php echo FRoute::apps();?>">
					<i class="fa fa-cube"></i> <span><?php echo JText::_( 'MOD_EASYSOCIAL_MENU_APPS' );?></span>
				</a>
			</li>
			<?php } ?>

			<?php if( $params->get( 'show_activity' , true ) ){ ?>
			<li>
				<a href="<?php echo FRoute::activities();?>">
					<i class="fa fa-file-text-o"></i> <span><?php echo JText::_( 'MOD_EASYSOCIAL_MENU_ACTIVITY_LOG' );?></span>
				</a>
			</li>
			<?php } ?>


			<!-- EasyBlog Integrations -->
			<?php if ($params->get('integrate_easyblog', true) && $eblogExists) { ?>
				<?php if (EB::acl()->get('add_entry')) { ?>
					<li>
						<a href="<?php echo EBR::_( 'index.php?option=com_easyblog&view=dashboard&layout=write&Itemid=' . EBR::getItemId( 'dashboard' ) );?>">
							<i class="fa fa-pencil-square-o"></i>
							<span><?php echo JText::_( 'MOD_EASYSOCIAL_MENU_EASYBLOG_WRITE_NEW' );?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo EBR::_( 'index.php?option=com_easyblog&view=dashboard&layout=entries&Itemid=' . EBR::getItemId( 'dashboard' ) );?>">
							<i class="fa fa-list-alt"></i>
							<span><?php echo JText::_('MOD_EASYSOCIAL_MENU_EASYBLOG_POSTS');?></span>
						</a>
					</li>
				<?php } ?>
			<?php } ?>

			<?php if( $params->get( 'show_signout' , true ) ){ ?>
			<li>
				<form action="<?php echo JRoute::_('index.php');?>" id="es-mod-login-signout" method="post" data-es-menu-signout-form>
					<a href="javascript:void(0);" onclick="document.getElementById( 'es-mod-login-signout' ).submit();" data-es-menu-signout>
						<i class="fa fa-power-off"></i>
						<span><?php echo JText::_( 'MOD_EASYSOCIAL_MENU_SIGN_OUT' );?></span>
					</a>

					<input type="hidden" name="return" value="<?php echo $logoutReturn;?>" />
					<input type="hidden" name="option" value="com_easysocial" />
					<input type="hidden" name="controller" value="account" />
					<input type="hidden" name="task" value="logout" />
					<?php echo $modules->html( 'form.token' ); ?>
				</form>
			</li>
			<?php } ?>

		</ul>
	</div>
	<?php } ?>

</div>
