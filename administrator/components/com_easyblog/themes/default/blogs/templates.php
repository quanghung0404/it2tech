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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-grid-eb>

	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
		</div>

		<div class="form-group pull-right">
			<?php echo $pagination->getLimitBox(); ?>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table table table-striped table-eb table-hover">
			<thead>
				<tr>
					<th width="1%" class="center">
						<input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
					</th>
					<th>
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_TITLE'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATUS'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_GLOBAL'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_CORE'); ?>
					</th>
					<th width="15%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_AUTHOR'); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($templates) { ?>
					<?php $i = 0; ?>
					<?php foreach ($templates as $template) { ?>
					<tr>
						<td>
							<?php echo $this->html('grid.id', $i++, $template->id); ?>
						</td>
						<td>
							<a href="<?php echo rtrim(JURI::root(), '/'); ?>/administrator/index.php?option=com_easyblog&view=composer&tmpl=component&block_template=<?php echo $template->id;?>" data-eb-composer><?php echo JText::_($template->title);?></a>
						</td>
						<td class="center">
							<?php echo $this->html('grid.published', $template, 'templates', 'published', array('blogs.publishTemplate', 'blogs.unpublishTemplate'), array()); ?>
						</td>
						<td class="center">
							<?php $disabled = ($template->isBlank() || $template->isCore()) ? true : false; ?>
							<?php echo $this->html('grid.published', $template, 'templates', 'system', array('blogs.setGlobalTemplate', 'blogs.removeGlobalTemplate'), array(JText::_('COM_EASYBLOG_GRID_TOOLTIP_UNSET_AS_GLOBAL'), JText::_('COM_EASYBLOG_GRID_TOOLTIP_SET_AS_GLOBAL')), $disabled); ?>
						</td>
						<td class="center">
							<?php echo $this->html('grid.core', $template, 'core', array(JText::_('COM_EASYBLOG_GRID_TOOLTIP_IS_NOT_CORE'), JText::_('COM_EASYBLOG_GRID_TOOLTIP_IS_CORE'))); ?>
						</td>
						<td class="center">
							<?php echo $template->getAuthor()->getName();?>
						</td>
						<td class="center">
							<?php echo $template->id;?>
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
	<input type="hidden" name="layout" value="templates" />
	<input type="hidden" name="task" value="" data-table-grid-task />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
