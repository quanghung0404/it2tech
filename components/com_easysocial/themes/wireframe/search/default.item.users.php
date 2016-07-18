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

$user = FD::user( $item->uid );

?>
<li data-search-item
	data-search-item-id="<?php echo $item->id; ?>"
	data-search-item-type="<?php echo $item->utype; ?>"
	data-search-item-typeid="<?php echo $item->uid; ?>"
	data-search-custom-name="<?php echo $user->getName();?>"
	data-search-custom-avatar="<?php echo $user->getAvatar();?>"
	data-friend-uid="<?php echo $user->id; ?>"
	>
	<div class="es-item">
		<a class="es-avatar pull-left mr-10" href="<?php echo $user->getPermalink();?>">
			<img src="<?php echo $user->getAvatar();?>" title="<?php echo FD::get( 'String' )->escape( $user->getName() ); ?>" />
		</a>
		<div class="es-item-body">
			<?php if ($user->hasCommunityAccess()) { ?>
				<div class="pull-right">
					<?php if( $user->getFriend( $this->my->id )->state == SOCIAL_FRIENDS_STATE_PENDING ){ ?>
						<a href="javascript:void(0);" class="btn btn-clean small" data-search-friend-pending-button>
							<i class="icon-es-aircon-checkmark mr-10"></i>
							<span class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_REQUEST_SENT' );?></span>
						</a>
					<?php } else if( $user->getFriend( $this->my->id )->state != SOCIAL_FRIENDS_STATE_FRIENDS ) { ?>

						<?php if( FD::privacy( $this->my->id )->validate( 'friends.request' , $user->id ) && !$user->isViewer() ){ ?>
							<a href="javascript:void(0);" class="btn btn-clean small"
								data-search-friend-button
							>
								<i class="icon-es-aircon-user mr-10"></i>
								<span><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_ADD_AS_FRIEND' );?></span>
							</a>
						<?php } ?>
					<?php } ?>
				</div>
			<?php } ?>	

			<div class="es-item-detail">

				<div class="item-name">
					<span class="es-item-title">
						<i class="fa <?php echo $item->icon; ?> mr-5"></i>
						<a href="<?php echo $user->getPermalink();?>">
							<?php echo $user->getName(); ?>
						</a>
					</span>
				</div>

				<ul class="fd-reset-list list-inline">
					<?php if( isset( $item->description ) && $item->description ) { ?>
					<li class="item-friend item-meta small">
						<?php echo $item->description; ?>
					</li>
					<?php } ?>
					<li class="item-friend item-meta small">
						<a href="<?php echo FRoute::friends( array( 'userid' => $user->getAlias() ) );?>"> <?php echo FD::get( 'Language', 'COM_EASYSOCIAL_FRIENDS' )->pluralize( $user->getTotalFriends() , true ); ?></a>
					</li>

					<?php if ($this->template->get('users_joindate', true)) { ?>
					<li class="item-friend item-meta small">
						<span title="<?php echo JText::sprintf('COM_EASYSOCIAL_USER_LISTING_MEMBER_SINCE_TOOLSTIPS', FD::date($user->registerDate)->toFormat('d M Y')); ?>">
							<i class="fa fa-file-text-o"></i>
							<?php echo FD::date($user->registerDate)->toFormat('d M Y'); ?>
						</span>
					</li>
					<?php } ?>

					<?php if ($this->template->get('users_lastlogin', true)) { ?>
					<li class="item-friend item-meta small">
						<?php
							$tooltips = JText::sprintf('COM_EASYSOCIAL_USER_LISTING_LAST_LOGGED_IN_TOOLSTIPS', FD::date($user->lastvisitDate)->toLapsed());
							$showText = FD::date($user->lastvisitDate)->toLapsed();

							if ($user->lastvisitDate == '' || $user->lastvisitDate == '0000-00-00 00:00:00') {
								$tooltips = JText::_('COM_EASYSOCIAL_USER_LISTING_NEVER_LOGGED_IN');
								$showText = JText::_('COM_EASYSOCIAL_USER_LISTING_NEVER_LOGGED_IN');
							}
						?>
						<span title="<?php echo $tooltips; ?>">
							<i class="fa fa-sign-in"></i>
							<?php echo $showText; ?>
						</span>
					</li>
					<?php } ?>


				</ul>
			</div>
		</div>
	</div>
</li>
