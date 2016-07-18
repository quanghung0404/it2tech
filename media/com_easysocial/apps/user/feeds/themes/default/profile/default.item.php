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
<li class="feed-item" data-feeds-list-item>
	<div class="feed-item-heading">
		<h5>
			<i class="icon-jar jar-social_rss mr-5"></i>
			<a href="<?php echo $feed->getParser()->get_link();?>" target="_blank">
				<?php echo $feed->title; ?>
				<i class="fa fa-new-tab "></i>
			</a>
		</h5>
		<p class="fd-small">
			<?php echo $feed->getParser()->get_description();?>
		</p>

		<?php if( $feed->total ){ ?>
		<div class="fd-small">
			<?php echo JText::sprintf( 'APP_FEEDS_PAGINATION_NOTE' , $totalDisplayed , $feed->total );?>
			<a href="<?php echo $feed->getParser()->get_link();?>" target="_blank" class="view-all"><?php echo JText::_( 'APP_FEEDS_VIEW_ALL' ); ?></a>
		</div>
		<?php } ?>
	</div>

	<?php if( $feed->items ){ ?>
	<ul class="list-unstyled">
		<?php foreach( $feed->items as $item ){ ?>
		<li>
			<h5>
				<a href="<?php echo $item->get_link();?>" target="_blank"><?php echo $item->get_title();?></a>
			</h5>
			<div class="feed-item-meta small">
				<i class="icon-es-calendar"></i> <span class="fd-small"><?php echo $item->get_date( JText::_('COM_EASYSOCIAL_DATE_DMY') ); ?></span>
			</div>

			<p class="feed-item-content">
				<?php echo JString::substr( strip_tags( $item->get_content() ) , 0 , 120 );?> <span class="feed-item-readmore">...</span>
			</p>
		</li>
		<?php } ?>
	</ul>
	<?php } else { ?>
	<div class="empty">
		<?php echo JText::_( 'APP_FEEDS_EMPTY_FEED_RESULT' ) ; ?>
	</div>
	<?php } ?>
</li>
