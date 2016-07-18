<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<?php echo $this->html('filter.search', $search); ?>
		</div>

		<div class="form-group">
			<?php echo $categoryFilter;?>
		</div>

		<div class="form-group pull-right">
			<?php echo $pagination->getLimitBox(); ?>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table table table-striped table-eb table-hover">
			<thead>
				<tr>
					<td width="1%" class="center">
						<input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
					</td>
					<td>
						<?php echo JHTML::_('grid.sort', 'Title', 'a.title', $orderDirection, $order ); ?>
					</td>
					<td width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_CATEGORY'); ?>
					</td>
					<td width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_AUTHOR'); ?>
					</td>
					<td width="15%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYBLOG_TABLE_HEADING_CREATED'), 'a.created', $orderDirection, $order ); ?>
					</th>
					<td width="1%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYBLOG_TABLE_HEADING_ID'), 'a.entry_id', $orderDirection, $order ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($posts) { ?>
					<?php $i = 0; ?>
					<?php foreach ($posts as $post) { ?>
					<tr>
						<td>
							<?php echo $this->html('grid.id', $i++, $post->uid); ?>
						</td>
						<td>
							<div style="max-width: 450px; overflow: hidden; white-space: nowrap; font-size: 14px; font-weight: bold; text-overflow: ellipsis;">
								<a href="<?php echo $post->getEditLink();?>" target="_blank" data-eb-composer><?php echo $post->title;?></a>
							</div>

							<div style="margin-top: 10px;">
								<a class="btn btn-default btn-xs" href="<?php echo $post->getEditLink();?>" target="_blank" data-eb-composer>
									<i class="fa fa-times-circle"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_REVIEW_BUTTON');?>
								</a>
								&nbsp;
								<a class="btn btn-danger btn-xs" href="javascript:void(0);" data-blog-reject data-id="<?php echo $post->uid;?>">
									<i class="fa fa-times-circle"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_REJECT_BUTTON');?>
								</a>
								&nbsp;
								<a class="btn btn-primary btn-xs" href="javascript:void(0);" data-blog-accept data-id="<?php echo $post->uid;?>">
									<i class="fa fa-check-circle"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_APPROVE_BUTTON');?>
								</a>
							</div>
						</td>

						<td class="center">
							<?php echo $post->getPrimaryCategory()->title;?>
						</td>
						<td class="center">
							<?php echo $post->getAuthor()->getName();?>
						</td>
						<td class="center">
							<?php echo $post->getCreationDate()->format(JText::_('DATE_FORMAT_LC1'));?>
						</td>
						<td class="center">
							<?php echo $post->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="7" class="empty">
						<?php echo JText::_('COM_EASYBLOG_PENDING_EMPTY'); ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</td>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="view" value="blogs" />
	<input type="hidden" name="layout" value="pending" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
