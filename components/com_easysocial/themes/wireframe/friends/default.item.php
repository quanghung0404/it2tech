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

$privacy = $this->my->getPrivacy();
?>
<li class="friendItem"
	data-id="<?php echo $user->id;?>"
	data-friendId="<?php echo $this->my->getFriend( $user->id )->id; ?>"
	data-name="<?php echo $this->html( 'string.escape' , $user->getName() );?>"
	data-avatar="<?php echo $user->getAvatar();?>"
	data-friends-item
	data-friendItem-<?php echo $user->id;?>>
	<div class="es-item<?php echo ( $filter == 'request' && $user->getFriend( $this->my->id )->state == SOCIAL_FRIENDS_STATE_PENDING ) || ( $filter == 'pending' ) ? ' es-item-180' : ' es-item-150';?>">

		<div class="es-avatar-wrap pull-left">
			<a href="<?php echo $user->getPermalink();?>" class="es-avatar pull-left">
				<img src="<?php echo $user->getAvatar( SOCIAL_AVATAR_MEDIUM );?>" alt="<?php echo $this->html( 'string.escape' , $user->getName() );?>" />
			</a>
			<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $user->isOnline() , 'size' => 'small' ) ); ?>
		</div>

		<div class="es-item-body">
			<?php if( $this->access->allowed( 'reports.submit' ) || $filter == 'list' || $filter == 'all' || ( $filter == 'suggest' && $privacy->validate( 'friends.request' , $user->id ) ) ){ ?>
			<div class="pull-right btn-group">
				<a class="dropdown-toggle_ btn btn-es btn-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);">
					<i class="icon-es-dropdown"></i>
				</a>

				<ul class="dropdown-menu dropdown-menu-user messageDropDown">
					<?php if ($filter == 'list') { ?>
						<li data-lists-removeFriend>
							<a href="javascript:void(0);">
								<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_REMOVE_FROM_LIST' );?>
							</a>
						</li>
						<li class="divider">
							<hr />
						</li>
					<?php } ?>

					<?php if( $filter == 'all' && $activeUser->isViewer() ) { ?>
						<li data-friends-unfriend>
							<a href="javascript:void(0);">
								<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_UNFRIEND' );?>
							</a>
						</li>
						<li class="divider">
							<hr />
						</li>
					<?php } ?>

					<?php if ($this->access->allowed('reports.submit')) { ?>
					<li>
						<?php echo FD::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_USER , $user->id , $user->getName() , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER' ) , '' , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER_DESC' ) , $user->getPermalink( true , true ) ); ?>
					</li>
					<?php } ?>
				</ul>

			</div>
			<?php } ?>

			<div class="es-item-detail">
				<div class="es-item-title">
					<a href="<?php echo $user->getPermalink();?>"><?php echo $user->getName();?></a>
				</div>
				<ul class="fd-reset-list es-friends-links list-inline">
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

					<?php if( $this->config->get( 'followers.enabled' ) ) { ?>
					<li>
						<a href="<?php echo FRoute::followers( array( 'userid' => $user->getAlias() ) );?>" class="fd-small muted">
							<i class="fa fa-share-alt "></i>
							<?php if( $user->getTotalFollowers() ){ ?>
								<?php echo $user->getTotalFollowers();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_FOLLOWERS' , $user->getTotalFollowers() ) ); ?>
							<?php } else { ?>
								<?php echo JText::_( 'COM_EASYSOCIAL_NO_FOLLOWERS_YET' ); ?>
							<?php } ?>
						</a>
					</li>
					<?php } ?>

					<?php if ( $this->config->get( 'badges.enabled' ) ) { ?>
					<li>
						<a href="<?php echo FRoute::badges( array( 'userid' => $user->getAlias() , 'layout' => 'achievements' ) );?>" class="fd-small muted">
							<i class="fa fa-trophy "></i>
							<?php if( $user->getTotalbadges() ){ ?>
								<?php echo $user->getTotalbadges();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_BADGES' , $user->getTotalbadges() ) ); ?>
							<?php } else { ?>
								<?php echo JText::_( 'COM_EASYSOCIAL_NO_BADGES_YET' ); ?>
							<?php } ?>
						</a>
					</li>
					<?php } ?>

					<?php if( $this->config->get('conversations.enabled') && $this->my->id != $user->id && $this->access->allowed( 'conversations.create' ) ){ ?>
						<?php if( FD::privacy( $this->my->id )->validate( 'profiles.post.message' , $user->id ) ){ ?>
						<li data-friendItem-message>
							<a href="javascript:void(0);" class="fd-small muted">
								<i class="fa fa-envelope "></i> <?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_SEND_MESSAGE' ); ?>
							</a>
						</li>
						<?php } ?>
					<?php } ?>
				</ul>

				<?php if ($activeUser->isViewer()) { ?>
				<div class="friend-actions">

					<?php if ($filter == 'suggest' && $user->getFriend( $this->my->id )->state != SOCIAL_FRIENDS_STATE_FRIENDS) { ?>
						<div class="fd-small total-no" data-friendItem-addbutton>
							<a href="javascript:void(0);" data-friendItem-add class="btn btn-es-primary btn-sm mt-20">
								<?php echo JText::_( 'APP_FRIENDS_SUGGEST_FRIENDS_ADD_FRIEND' ); ?>
							</a>
						</div>
					<?php } ?>


					<?php if ($filter != 'request' && $filter != 'suggest' && $user->getFriend( $this->my->id )->state == SOCIAL_FRIENDS_STATE_PENDING) { ?>
                    <div class="mt-20">
    					<a class="btn btn-es-danger btn-sm" data-friendItem-reject>
    						<?php echo JText::_('COM_EASYSOCIAL_REJECT_BUTTON'); ?>
    					</a>

    					<a href="javascript:void(0);" data-friendItem-approve class="btn btn-es-primary btn-sm">
    						<?php echo JText::_( 'COM_EASYSOCIAL_APPROVE_BUTTON' ); ?>
    					</a>
                    </div>
					<?php } else if( $filter == 'request' && $user->getFriend( $this->my->id )->state == SOCIAL_FRIENDS_STATE_PENDING ) { ?>
					<a class="btn btn-es-danger btn-sm mt-20" data-friendItem-cancel-request>
						<?php echo JText::_( 'COM_EASYSOCIAL_CANCEL_REQUEST_BUTTON' ); ?>
					</a>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</li>
