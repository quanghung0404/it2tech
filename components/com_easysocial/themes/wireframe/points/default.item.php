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
	<div class="es-container pt-20">

		<div class="es-point-wrapper">
			<div class="es-widget es-point">
				<div class="es-widget-head">
					<h5><?php echo JText::_( $point->title ); ?></h5>
				</div>

				<div class="es-widget-body">

					<div class="point-result">
						<div class="point-result-inner"><?php echo $point->points;?></div>
					</div>
					<div class="point-desp">
						<?php echo JText::_( $point->description );?>
					</div>
				</div>

				<div class="es-widget-foot">
					<h6>
						<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_ACHIEVERS' );?>:
					</h6>
					<span>
						<?php echo $point->getTotalAchievers();?>
					</span>
				</div>
			</div>
		</div>

		<div class="es-point-achievers">
			<h5 class="h3 es-point-achievers-title"><?php echo JText::_( 'COM_EASYSOCIAL_POINTS_THE_ACHIEVERS' );?></h5>
			<hr />

			<?php if( $achievers ){ ?>
			<ul class="fd-reset-list es-avatar-list">
				<?php foreach( $achievers as $achiever ){ ?>
				<li data-es-provide="tooltip"
					data-original-title="<?php echo $this->html( 'string.escape' , $achiever->getName() );?>">
					<a href="<?php echo $achiever->getPermalink();?>" class="es-avatar es-avatar-rounded pull-left mr-10">
						<img src="<?php echo $achiever->getAvatar( SOCIAL_AVATAR_SMALL );?>" alt="<?php echo $this->html( 'string.escape' , $achiever->getName() );?>" />
					</a>
				</li>
				<?php } ?>
			</ul>
			<?php } else { ?>
			<div class="empty">
				<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_NO_ACHIEVERS_YET' ); ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
