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
<li class="followerItem"
	data-id="<?php echo $user->id;?>"
	data-followers-item
>
	<div class="es-item">
		<div class="es-avatar-wrap pull-left">
			<a href="<?php echo $user->getPermalink();?>" class="es-avatar pull-left">
				<img src="<?php echo $user->getAvatar( SOCIAL_AVATAR_MEDIUM );?>" alt="<?php echo $this->html( 'string.escape' , $user->getName() );?>" />
			</a>
			<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $user->isOnline() , 'size' => 'small' ) ); ?>
		</div>

		<div class="es-item-body">

			<div class="pull-right btn-group">
				<a class="dropdown-toggle_ btn btn-es btn-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);">
					<i class="icon-es-dropdown"></i>
				</a>

				<?php if( $this->access->allowed( 'reports.submit' ) || ($this->my->id == $currentUser->id && $active != 'followers' ) ){ ?>
				<ul class="dropdown-menu dropdown-menu-user messageDropDown">

					<?php if( $active != 'followers' ){ ?>

						<?php if( $active == 'following' ){ ?>
						<li data-followers-item-unfollow>
							<a href="javascript:void(0);">
								<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_UNFOLLOW' );?>
							</a>
						</li>
						<?php } ?>

						<?php if( $active == 'suggest' ){ ?>
						<li data-followers-item-follow>
							<a href="javascript:void(0);">
								<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_FOLLOW' );?>
							</a>
						</li>
						<?php } ?>

					<li class="divider">
						<hr />
					</li>
					<?php } ?>

					<?php if( $this->access->allowed( 'reports.submit' ) ){ ?>
					<li>
						<?php echo FD::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_USER , $user->id , $user->getName() , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER' ) , '' , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER_DESC' ) , $user->getPermalink() ); ?>
					</li>
					<?php } ?>
				</ul>
				<?php } ?>

			</div>

			<div class="es-item-detail">
				<div class="es-item-title">
					<a href="<?php echo $user->getPermalink();?>"><?php echo $user->getName();?></a>
				</div>
				<ul class="es-friends-links fd-reset-list list-inline">
					<li>
						<a href="<?php echo FRoute::friends( array( 'userid' => $user->getAlias() ) );?>" class="fd-small muted">
							<i class="fa fa-users"></i>

							<?php if( $user->getTotalFriends() ){ ?>
								<?php echo $user->getTotalFriends();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_FRIENDS' , $user->getTotalFriends() ) ); ?>
							<?php } else { ?>
								<?php echo JText::_( 'COM_EASYSOCIAL_NO_FRIENDS_YET' ); ?>
							<?php } ?>
						</a>
					</li>
					<li>
						<a href="<?php echo FRoute::followers( array( 'userid' => $user->getAlias() ) );?>" class="fd-small muted">
							<i class="fa fa-heart "></i>
							<?php if( $user->getTotalFollowers() ){ ?>
								<?php echo $user->getTotalFollowers();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_FOLLOWERS' , $user->getTotalFollowers() ) ); ?>
							<?php } else { ?>
								<?php echo JText::_( 'COM_EASYSOCIAL_NO_FOLLOWERS_YET' ); ?>
							<?php } ?>
						</a>
					</li>

					<?php if ( $this->config->get( 'badges.enabled' ) ) { ?>
					<li>
						<a href="<?php echo FRoute::badges( array( 'layout' => 'achievements' , 'userid' => $user->getAlias() ) );?>" class="fd-small muted">
							<i class="fa fa-trophy "></i>
							<?php if( $user->getTotalbadges() ){ ?>
								<?php echo $user->getTotalbadges();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_BADGES' , $user->getTotalbadges() ) ); ?>
							<?php } else { ?>
								<?php echo JText::_( 'COM_EASYSOCIAL_NO_BADGES_YET' ); ?>
							<?php } ?>
						</a>
					</li>
					<?php } ?>

					<?php if( $this->config->get( 'conversations.enabled' ) && $user->id && $this->my->id != $user->id && $this->access->allowed( 'conversations.create' ) ){ ?>
						<?php
						if( FD::privacy( $this->my->id )->validate( 'profiles.post.message' , $user->id ) ){ ?>
						<li data-followers-item-compose>
							<a href="javascript:void(0);" class="fd-small muted">
								<i class="fa fa-envelope "></i> <?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_SEND_MESSAGE' ); ?>
							</a>
						</li>
						<?php } ?>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</li>
