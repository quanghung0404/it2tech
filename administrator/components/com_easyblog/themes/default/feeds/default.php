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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-grid-eb>
	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<?php echo $this->html('filter.search', $search); ?>
		</div>

		<div class="form-group">
            <?php echo $state; ?>
        </div>

		<div class="form-group pull-right">
			<?php echo $pagination->getLimitBox(); ?>
		</div>
	</div>

	<div class="panel-table">
		<div class="alert alert-warning">
			<i class="fa fa-info-circle"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_FEEDS_CRON_INFO');?>
			<a href="http://stackideas.com/docs/easyblog/administrators/configuration/feeds-importer" target="_blank" class="btn btn-sm btn-default"><?php echo JText::_('COM_EASYBLOG_READMORE_HERE');?></a>
		</div>

		<table class="app-table table table-striped table-eb">
			<thead>
				<tr>
					<th width="1%" class="center nowrap">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th>
						<?php echo JText::_( 'COM_EASYBLOG_FEEDS_TITLE' ); ?>
					</th>
					<th width="25%" style="text-align: left;">
						<?php echo JText::_( 'COM_EASYBLOG_FEEDS_URL' ); ?>
					</th>
					<th width="5%" class="center">
						<?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_PUBLISHED' ); ?>
					</th>
					<th width="20%" class="center">
						<?php echo JText::_( 'COM_EASYBLOG_FEEDS_LAST_IMPORT' ); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYBLOG_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if( $feeds ){ ?>
					<?php $i = 0; ?>

					<?php foreach ($feeds as $row) { ?>
					<tr>
						<td>
							<?php echo $this->html('grid.id', $i++, $row->id); ?>
						</td>
						<td align="left">
							<a href="index.php?option=com_easyblog&view=feeds&layout=form&id=<?php echo $row->id;?>"><?php echo $row->title; ?></a>

							<div class="mt-10" data-feed-import-item>
								<a href="javascript:void(0);" class="btn btn-default btn-xs" data-feed-import data-id="<?php echo $row->id;?>"><?php echo JText::_('Test Import');?></a>
								<span class="ml-5" data-feed-import-log></span>
							</div>
						</td>
						<td>
							<a href="<?php echo $row->url; ?>" target="_blank"><?php echo $row->url; ?></a>
						</td>
						<td class="center">
							<?php echo $this->html('grid.published', $row, 'feeds', 'published'); ?>
						</td>
						<td class="center">
							<?php echo $row->import_text;?>
						</td>
						<td class="center nowrap">
							<?php echo $row->id;?>
						</td>
					</tr>
					<?php } ?>

				<?php } else { ?>
					<tr>
						<td colspan="6" class="empty">
							<?php echo JText::_('COM_EASYBLOG_FEEDS_NO_FEEDS_YET');?>
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
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="view" value="feeds" />
	<input type="hidden" name="task" value="" />
</form>
