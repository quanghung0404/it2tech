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
<div class="es-leaderboard">
	<div class="view-heading">
		<h3><?php echo JText::_( 'COM_EASYSOCIAL_POINTS_LEADERBOARD' );?></h3>
		<p><?php echo JText::_( 'COM_EASYSOCIAL_POINTS_LEADERBOARD_DESC' ); ?></p>
	</div>

	<?php echo $this->render( 'module' , 'es-leaderboard-before-contents' ); ?>

	<ul class="fd-reset-list es-item-list mt-20">
		<?php $i = 1; ?>
		<?php foreach( $users as $user ){ ?>
		<?php echo $this->render( 'module' , 'es-leaderboard-between-users' ); ?>
		<li>
			<div class="es-item">

				<div class="es-avatar-wrap">
					<a href="<?php echo $user->getPermalink();?>" class="es-avatar es-avatar-md pull-left mr-10">
						<img src="<?php echo $user->getAvatar( SOCIAL_AVATAR_MEDIUM );?>" alt="<?php echo $this->html( 'string.escape' , $user->getName() ); ?>" />
					</a>

					<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $user->isOnline() ) ); ?>
				</div>

				<div class="es-item-body">
					<div class="es-item-detail pull-left">
						<ul class="fd-reset-list">
							<li>
								<span class="es-item-title">
									<a href="<?php echo $user->getPermalink();?>"><?php echo $user->getName();?></a>
								</span>
							</li>
							<?php if( $user->getBadges() ){ ?>
							<li class="mt-10">
								<?php foreach( $user->getBadges() as $badge ){ ?>
									<a href="<?php echo $badge->getPermalink();?>" class="badge-link" data-es-provide="tooltip" data-placement="top" data-original-title="<?php echo $this->html( 'string.escape' , $badge->get( 'title' ) );?>">
									<img class="es-badge-icon" alt="<?php echo $this->html( 'string.escape' , $badge->get( 'title' ) );?>" src="<?php echo $badge->getAvatar();?>" width="16" /></a>
								<?php } ?>
							</li>
							<?php } ?>
							<li class="mt-10">
								<a href="<?php echo FRoute::friends( array( 'userid' => $user->getAlias() ) );?>" class="fd-small muted mr-10">
									<i class="fa fa-users"></i>  <?php echo $user->getTotalFriends();?> <?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS' ); ?>
								</a>


								<?php if( $this->config->get( 'followers.enabled' ) ) { ?>
								<a href="<?php echo FRoute::followers( array( 'userid' => $user->getAlias() ) );?>" class="fd-small muted mr-10">
									<i class="fa fa-heart "></i>
									<?php if( $user->getTotalFollowers() ){ ?>
										<?php echo $user->getTotalFollowers();?> <?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS' ); ?>
									<?php } else { ?>
										<?php echo JText::_( 'COM_EASYSOCIAL_NO_FOLLOWERS_YET' ); ?>
									<?php } ?>
								</a>
								<?php } ?>

							</li>
						</ul>
					</div>

					<div class="es-rank-item pull-right">
						<div class="es-rank-no">
							<span><?php echo $i++;?></span>
						</div>
						<div class="es-rank-point center">
							<div><?php echo $user->getPoints();?></div>
							<small><?php echo JText::_( 'COM_EASYSOCIAL_POINTS_POINTS' );?></small>
						</div>
					</div>

				</div>
			</div>
		</li>
		<?php } ?>
	</ul>

	<?php echo $this->render( 'module' , 'es-leaderboard-after-contents' ); ?>
</div>
