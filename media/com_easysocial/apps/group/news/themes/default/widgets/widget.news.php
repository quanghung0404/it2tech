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
<div class="es-widget">
	<div class="es-widget-head">
		<div class="pull-left widget-title"><?php echo JText::_( 'APP_GROUP_NEWS_WIDGET_TITLE' );?></div>
		<span class="widget-label">(<?php echo $total;?>)</span>
	</div>

	<div class="es-widget-body">
		<ul class="fd-nav fd-nav-stacked">
			<?php if ($items) { ?>
				<?php foreach ($items as $item) { ?>
				<li>
					<div class="mb-5">
						<strong><a href="<?php echo FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'newsId' => $item->id ) , false );?>"><?php echo $item->title; ?></a></strong>
					</div>
					<div class="fd-small"><i class="fa fa-calendar "></i>&nbsp; <?php echo FD::date( $item->created )->format( JText::_( 'DATE_FORMAT_LC4' ) ); ?></div>
				</li>
				<?php } ?>
			<?php } else { ?>
				<?php echo JText::_('APP_GROUP_NEWS_WIDGET_NO_ANNOUNCEMENTS_YET'); ?>
			<?php } ?>
		</ul>
		
	</div>
</div>
