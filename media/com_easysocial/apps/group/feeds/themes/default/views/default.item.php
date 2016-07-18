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
			<?php if ($rss->parser) { ?>
				<a href="<?php echo @$rss->parser->get_link();?>" target="_blank">
					<?php echo $rss->title; ?>
					<i class="fa fa-new-tab "></i>
				</a>
			<?php } else { ?>
				<a href="javascript:void(0);">
					<?php echo $rss->title;?>
				</a>
			<?php } ?>

			<span class="btn-group mr-10 pull-right">
				<a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_ btn btn-dropdown">
					<i class="icon-es-dropdown"></i>
				</a>
				<ul class="dropdown-menu dropdown-menu-user messageDropDown small">
					<li>
						<a href="javascript:void(0);" class="fd-small" data-feeds-remove data-id="<?php echo $rss->id;?>">
							<?php echo JText::_( 'APP_FEEDS_REMOVE_ITEM' );?>
						</a>
					</li>
				</ul>
			</span>
		</h5>

		<p class="fd-small">
			<?php echo $rss->description;?>
		</p>

		<?php if ($rss->parser && $rss->total) { ?>
		<div class="fd-small">
			<?php echo JText::sprintf('APP_FEEDS_PAGINATION_NOTE' , $totalDisplayed , $rss->total);?>
			<a href="<?php echo $rss->parser->get_link();?>" target="_blank" class="view-all"><?php echo JText::_( 'APP_FEEDS_VIEW_ALL' ); ?></a>
		</div>
		<?php } ?>
	</div>

	<?php if ($rss->parser && $rss->items) { ?>
	<ul class="list-unstyled feed-item-list" data-feeds-list>
		<?php foreach ($rss->items as $item ){ ?>
		<li data-feed-item>
			<div class="clearfix">
				<a href="javascript:void(0);" data-feed-open><?php echo @$item->get_title();?></a>

				<div class="feed-item-meta pull-right">
					<i class="fa fa-clock-o"></i> <span class="fd-small"><?php echo @$item->get_date( JText::_('COM_EASYSOCIAL_DATE_DMY') ); ?></span>
				</div>
			</div>

			<div class="feed-item-content hide" data-feed-preview>
				<?php echo @$item->get_content();?>
				<br /><br />
				<a href="<?php echo @$item->get_link();?>" target="_blank" class="btn btn-sm btn-primary"><?php echo JText::_('View Item');?></a>
			</div>
		</li>
		<?php } ?>
	</ul>
	<?php } else { ?>
	<div class="empty">
		<?php echo JText::_('APP_FEEDS_EMPTY_FEED_RESULT'); ?>
	</div>
	<?php } ?>
</li>
