<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php?option=com_easyblog" method="post" name="adminForm" id="adminForm" data-grid-eb>

	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<?php echo $this->html('filter.search', $search); ?>
		</div>

		<div class="form-group">
			<?php echo $this->getFilterState($filter_state);?>

			<?php echo $this->getFilterCategory($filter_category);?>

			<?php echo $this->getFilterBlogger($filterBlogger);?>
		</div>

		<div class="form-group pull-right">
			<?php echo $pagination->getLimitBox(); ?>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle table table-striped table-eb" data-table-grid>
			<thead>
				<tr>
					<th width="1%" class="nowrap hidden-phone center">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>

					<th>
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYBLOG_BLOGS_BLOG_TITLE'), 'a.title', $orderDirection, $order ); ?>
					</th>

					<?php if( !$browse ){ ?>

					<th class="nowrap hidden-phone center">
						<?php echo JText::_( 'COM_EASYBLOG_BLOGS_FEATURED' ); ?>
					</th>

					<th class="nowrap hidden-phone center">
						<?php echo JText::_( 'COM_EASYBLOG_STATUS' ); ?>
					</th>

					<th class="nowrap hidden-phone center">
						<?php echo JText::_( 'COM_EASYBLOG_BLOGS_FRONTPAGE' ); ?>
					</th>

					<th class="nowrap hidden-phone center">
						<?php echo JText::_( 'COM_EASYBLOG_BLOGS_HITS' ); ?>
					</th>

					<th width="10%" class="nowrap hidden-phone center"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_CONTRIBUTED_IN' ); ?></th>


					<th width="2%" class="nowrap hidden-phone center">
						<?php echo JText::_( 'COM_EASYBLOG_BLOGS_AUTOPOSTING' ); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone text-center">
						<?php echo JText::_( 'COM_EASYBLOG_BLOGS_NOTIFY' ); ?>
					</th>
					<?php } ?>

					<?php if( !$browse ){ ?>
					<th width="20%" class="nowrap center hidden-phone"><?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_DATE', 'a.created', $orderDirection, $order ); ?></th>
					<th width="1%" nowrap="nowrap center"><?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_ID', 'a.id', $orderDirection, $order ); ?></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php if( $blogs ){ ?>
					<?php $i = 0; ?>

					<?php foreach ($blogs as $row) { ?>
					<tr
						data-item
						data-id="<?php echo $row->id;?>"
						data-title="<?php echo $row->title;?>"
					>
						<td class="center hidden-iphone" valign="top">
							<?php echo $this->html('grid.id', $i++ , $row->id); ?>
						</td>

						<td class="nowrap has-context">

							<div style="max-width: 280px; overflow: hidden; white-space: nowrap; font-size: 14px; font-weight: bold; text-overflow: ellipsis;">
								<?php if ($row->isFromFeed()) { ?>
									<i class="fa fa-rss-square" data-eb-provide="tooltip" data-title="<?php echo JText::_('COM_EASYBLOG_BLOG_POST_IS_IMPORTED_FROM_FEEDS', true);?>" data-placement="bottom"></i>&nbsp;
								<?php } ?>

								<?php if ($browse) { ?>
									<a href="javascript:void(0);" data-post-title><?php echo $row->title;?></a>
								<?php } else { ?>
									<a href="<?php echo $row->getEditLink();?>" target="_blank" data-eb-composer><?php echo $row->title; ?></a>
								<?php } ?>
							</div>

							<div style="max-width: 280px; white-space: initial; text-overflow: ellipsis;">
								<span class="mr-10">
									<i class="fa fa-user text-muted"></i>&nbsp;
									<?php if( !$browse ){ ?>
										<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo JFactory::getUser($row->created_by)->name;?></a>
									<?php } else { ?>
										<?php echo JFactory::getUser($row->created_by)->name;?>
									<?php } ?>
								</span>

								<?php foreach ($row->getCategories() as $category) { ?>
								<span class="mr-10">
									<i class="fa fa-folder text-muted"></i>&nbsp; <?php echo $category->getTitle(); ?>
								</span>
								<?php }?>

								<span class="mr-10">
									<i class="fa fa-flag text-muted"></i>&nbsp;
									<?php if ($row->language=='*' || empty( $row->language) ){ ?>
										<?php echo JText::alt('JALL', 'language'); ?>
									<?php } else { ?>
										<?php echo $this->escape($this->getLanguageTitle($row->language)); ?>
									<?php } ?>
								</span>

								<?php if ($row->ip) { ?>
								<span class="mr-10">
									<i class="fa fa-desktop"></i> <?php echo $row->ip;?>
								</span>
								<?php } ?>

								<?php if ($row->locked) { ?>
								<span>
									<i class="fa fa-lock text-muted" data-eb-provide="tooltip" data-title="<?php echo JText::_('COM_EASYBLOG_BLOG_POST_IS_LOCKED');?>" data-placement="bottom"></i> <?php echo JText::_('COM_EASYBLOG_POST_LOCKED');?>
								</span>
								<?php } ?>
							</div>
						</td>

						<?php if( !$browse ){ ?>
						<td class="nowrap hidden-phone center">
							<?php echo $this->html('grid.featured', $row, 'blogs', 'featured', array('blogs.feature', 'blogs.unfeature')); ?>
						</td>

						<td class="nowrap hidden-phone center">
							<?php echo $this->html('grid.published', $row, 'blogs', 'published'); ?>
						</td>

						<td class="nowrap hidden-phone center">
							<?php echo $this->html('grid.published', $row, 'blogs', 'frontpage', array('blogs.setFrontpage', 'blogs.removeFrontpage')); ?>
						</td>

						<td class="nowrap hidden-phone text-center">
							<?php echo $row->hits;?>
						</td>

						<td class="nowrap hidden-tablet text-center">
							<?php echo $row->contributionDisplay;?>
						</td>

						<td class="center hidden-phone small">
							<div style="white-space: nowrap">
								<?php if ($row->isPublished() && $centralizedConfigured) { ?>
									<?php foreach ($consumers as $consumer) { ?>
										<a class="btn btn-social btn-<?php echo $consumer->type;?> btn-sm text-center <?php echo $consumer->isShared($row->id) ? ' is-sent' : '';?>"
											href="javascript:void(0);"
											data-post-autopost
											data-id="<?php echo $row->id;?>"
											data-type="<?php echo $consumer->type;?>"
											data-eb-provide="tooltip"
											data-original-title="<?php echo $consumer->isShared($row->id) ? JText::sprintf('COM_EASYBLOG_AUTOPOST_SHARED', $consumer->type) : JText::sprintf('COM_EASYBLOG_AUTOPOST_NOT_SHARED_YET', $consumer->type);?>"
										>
											<i class="fa fa-<?php echo $consumer->type;?> fa-14"></i>
										</a>
									<?php } ?>
								<?php } else { ?>
								<div data-eb-provide="tooltip" data-original-title="<?php echo (! $centralizedConfigured) ? JText::_('COM_EASYBLOG_AUTOPOST_NOT_AVAILABLE_BECAUSE_MISSING_CONFIGURATION') : JText::_('COM_EASYBLOG_AUTOPOST_NOT_AVAILABLE_BECAUSE_UNPUBLISHED', true ); ?>">
									<?php echo JText::_('COM_EASYBLOG_NOT_AVAILABLE'); ?>
								</div>
							<?php } ?>
							</div>
						</td>
						<td class="center hidden-phone small">
							<a class="btn btn-default btn-sm"
								data-notify-item
								data-blog-id="<?php echo $row->id;?>"
								data-eb-provide="tooltip"
								data-title="<?php echo JText::_('COM_EASYBLOG_BLOGS_NOTIFY_TOOLTIP');?>"
							>
								<i class="fa fa-envelope fa-14"></i>
							</a>
						</td>
						<?php } ?>

						<?php if( !$browse ){ ?>
						<td class="text-center">
							<div style="white-space: inherit">
								<?php echo EB::date($row->created)->format(JText::_('DATE_FORMAT_LC1')); ?>
							</div>
						</td>

						<td class="text-center">
							<?php echo $row->id; ?>
						</td>
						<?php } ?>
					</tr>
						<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="15" class="empty">
						<?php echo JText::_('COM_EASYBLOG_BLOGS_NO_ENTRIES');?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="15" class="text-center">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php if( $browse ){ ?>
	<input type="hidden" name="tmpl" value="component" />
	<?php } ?>

	<input type="hidden" name="autopost_type" value="" />
	<input type="hidden" name="autopost_selected" value="" />
	<input type="hidden" name="move_category_id" value="" data-move-category />
	<input type="hidden" name="move_author_id" value="" data-move-author />
	<input type="hidden" name="browse" value="<?php echo $browse;?>" />
	<input type="hidden" name="browseFunction" value="<?php echo $browseFunction;?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="view" value="blogs" />
	<input type="hidden" name="task" value="" data-table-grid-task />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $orderDirection; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
