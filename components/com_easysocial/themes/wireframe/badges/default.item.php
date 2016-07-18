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
<div data-dashboard>

	<div data-badge class="es-container pt-20" data-id="<?php echo $badge->id; ?>" data-total-achievers="<?php echo $totalAchievers; ?>">

		<div class="es-badge-wrapper">
			<div class="es-widget es-badge">
				<div class="es-widget-head">
					<h5><?php echo $badge->get( 'title' ); ?></h5>
				</div>

				<div class="es-widget-body">
					<img class="badge-icon" alt="<?php echo $this->html( 'string.escape' , $badge->get( 'title' ) );?>" src="<?php echo $badge->getAvatar();?>" />
					<div class="badge-desp">
						<?php echo $badge->get( 'description' ); ?>
					</div>
				</div>

				<div class="es-widget-foot">
					<h6><?php echo JText::_( 'COM_EASYSOCIAL_BADGES_ACHIEVERS' );?>:</h6>
					<span><?php echo $totalAchievers;?></span>
				</div>
			</div>
		</div>

		<div class="es-point-achievers mt-20">
			<h5 class="h3 es-point-achievers-title"><?php echo JText::_( 'COM_EASYSOCIAL_BADGES_TO_UNLOCK' );?></h5>
			<hr />
			<p class="center">
				<?php echo $badge->get( 'howto' ); ?>
			</p>
		</div>

		<div class="es-point-achievers mt-20">
			<h5 class="h3 es-point-achievers-title"><?php echo JText::_( 'COM_EASYSOCIAL_BADGES_THE_ACHIEVERS' );?></h5>
			<hr />
			<?php if( $achievers ){ ?>
			<ul data-badge-achievers-list class="es-avatar-list">
				<?php foreach( $achievers as $user ){ ?>
					<?php echo $this->loadTemplate( 'site/badges/default.item.achiever', array( 'user' => $user ) ); ?>
				<?php } ?>
			</ul>

			<span class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_BADGES_PRIVACY_NOTE' ); ?></span>

			<?php if( $totalAchievers > 0 && $totalAchievers > $this->template->get('achieverslimit' ) ) { ?>
			<div data-badge-achievers-loading class="fd-loading" style="display: none;"><span><?php echo JText::_( 'COM_EASYSOCIAL_BADGES_LOADING_MORE_ACHIEVERS' ); ?></span></div>
			<div data-badge-achievers-load class="es-achievers-load-button"><a href="javascript:void(0);" class="btn btn-es"><?php echo JText::_( 'COM_EASYSOCIAL_BADGES_LOAD_MORE_ACHIEVERS' ); ?></a></div>
			<?php } ?>
			<?php } else { ?>
			<div class="empty center">
				<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_EMPTY_ACHIEVERS' ); ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
