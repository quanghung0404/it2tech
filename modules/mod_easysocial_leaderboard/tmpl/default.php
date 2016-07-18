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
<div id="fd" class="es mod-es-leaderboard module-leaderboard<?php echo $suffix;?>">
	<ul class="es-item-list fd-reset-list">
		<?php $i = 1; ?>
		<?php foreach( $users as $user ){ ?>
		<li>
			<div class="media">
				<div class="media-object pull-left">
					<a href="<?php echo $user->getPermalink();?>">
						<img src="<?php echo $user->getAvatar();?>"<?php echo $params->get( 'popover' , true ) ? ' data-popbox="module://easysocial/profile/popbox" data-popbox-position="' . $params->get( 'popover_position' , 'top-left' ) . '" data-user-id="' . $user->id . '"' : '';?> />
					</a>
				</div>
				<div class="media-body">
					<div class="leader-rank"><?php echo $i++;?></div>
					<div class="leader-info">
						<span class="username">
							<a href="<?php echo $user->getPermalink();?>"><?php echo $user->getName();?></a>
						</span>
						<span class="points"><?php echo JText::sprintf( 'MOD_EASYSOCIAL_LEADERBOARD_USER_POINTS' , $user->getPoints() );?></span>
					</div>
				</div>
			</div>
		</li>
		<?php } ?>
	</ul>

	<?php if( $params->get( 'showall_link' , true ) ){ ?>
	<div class="fd-small center">
		<a href="<?php echo FRoute::leaderboard();?>"><?php echo JText::_( 'MOD_EASYSOCIAL_LEADERBOARD_VIEW_LEADERBOARD' ); ?></a>
	</div>
	<?php } ?>
</div>
