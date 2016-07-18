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
	<div class="view-heading" data-dashboard-heading>
		<h3 data-heading-title><?php echo JText::_( 'COM_EASYSOCIAL_POINTS_HEADING' );?></h3>
		<p data-heading-desc><?php echo JText::_( 'COM_EASYSOCIAL_POINTS_HEADING_DESC' ); ?></p>
	</div>

	<div class="es-container pt-20">
		<ul class="fd-reset-list points-list">
			<?php foreach( $points as $point ){ ?>
			<li class="es-widget es-point<?php echo $point->points < 0 ? ' es-point-red' : '';?><?php echo $point->points > 10 ? ' es-point-green' : '';?>">
				<div class="es-widget-head">
					<a href="<?php echo $point->getPermalink();?>">
						<h5><?php echo JText::_( $point->title );?></h5>
					</a>
				</div>

				<div class="es-widget-body">

					<div class="point-result">
						<div class="point-result-inner">
							<?php echo $point->points; ?>
						</div>
					</div>
					<div class="point-desp"><?php echo $point->get( 'description' ); ?></div>
				</div>

				<div class="es-widget-foot">
					<h6><?php echo JText::_( 'COM_EASYSOCIAL_POINTS_ACHIEVERS' );?>:</h6>
					<span><?php echo $point->getTotalAchievers();?></span>
				</div>
			</li>
			<?php } ?>
		</ul>
        <div class="mt-20 pagination-wrapper text-center">
		  <?php echo $pagination->getListFooter( 'site' ); ?>
        </div>
	</div>
</div>
