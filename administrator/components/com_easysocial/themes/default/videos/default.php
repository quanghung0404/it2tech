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
<form action="index.php" id="adminForm" method="post" name="adminForm" data-table-grid>
	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<?php echo $this->html('filter.search', $search); ?>
		</div>

		<div class="form-group">
			<strong><?php echo JText::_('COM_EASYSOCIAL_FILTER_BY'); ?> :</strong>
			<div>
				<select class="form-control input-sm" name="filter" id="filterType" data-table-grid-filter>
					<option value="all"<?php echo $filter == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYSOCIAL_FILTER_ALL_VIDEOS'); ?></option>
					<option value="1"<?php echo $filter === 1 ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYSOCIAL_FILTER_PUBLISHED'); ?></option>
					<option value="0"<?php echo $filter === 0 ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYSOCIAL_FILTER_UNPUBLISHED'); ?></option>
					<option value="2"<?php echo $filter === 2 ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYSOCIAL_FILTER_PENDING'); ?></option>
				</select>
			</div>
		</div>

		<div class="pull-right">
			<?php echo $this->html('filter.limit', $limit); ?>
		</div>
	</div>

	<div id="videos" class="panel-table">
		<table class="app-table table table-eb table-striped" data-pending-videos>
			<thead>
				<tr>
					<?php if (!$simple) { ?>
					<th width="5">
						<input type="checkbox" name="toggle" value="" data-table-grid-checkall />
					</th>
					<?php } ?>

					<th style="text-align: left;">
						<?php echo $this->html('grid.sort', 'title', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_TITLE'), $ordering, $direction); ?>
					</th>

					<?php if (!$simple) { ?>
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYSOCIAL_TABLE_COLUMN_FEATURED'); ?>
					</th>
				
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYSOCIAL_TABLE_COLUMN_STATE'); ?>
					</th>

					<th width="20%" class="center">
						<?php echo JText::_('COM_EASYSOCIAL_TABLE_COLUMN_CATEGORY'); ?>
					</th>
					<?php } ?>

					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYSOCIAL_TABLE_COLUMN_CREATED'); ?>
					</th>
					<th width="5%" class="center">
						<?php echo $this->html('grid.sort', 'id', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_ID'), $ordering, $direction); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if ($videos) { ?>
				<?php $i = 0; ?>

				<?php foreach ($videos as $video) { ?>
				<tr>
					<?php if (!$simple) { ?>
					<td>
						<?php echo $this->html('grid.id', $i++, $video->id); ?>
					</td>
					<?php } ?>

					<td align="left">
						<div class="media">
							<?php if (!$simple) { ?>
							<div class="media-object pull-left">
								<img src="<?php echo $video->getThumbnail();?>" width="120" />
							</div>
							<?php } ?>

							<div class="media-body">
								<a href="<?php echo FRoute::_('index.php?option=com_easysocial&view=videos&layout=form&id=' . $video->id);?>"
                                    data-video-insert
                                    data-id="<?php echo $video->id;?>"
                                    data-alias="<?php echo $video->getAlias();?>"
                                    data-title="<?php echo $this->html('string.escape', $video->title);?>"
								><?php echo $video->title;?></a>

								<?php if (!$simple) { ?>
								<p><?php echo $video->getDescription();?></p>
								<?php } ?>
								
							</div>
						</div>
					</td>

					<?php if (!$simple) { ?>
					<td class="center">
						<?php if ($video->isPendingProcess() || $video->isProcessing()) { ?>
						&mdash;
						<?php } else { ?>
							<?php echo $this->html('grid.featured', $video, 'videos', 'featured'); ?>
						<?php } ?>
					</td>
					<td class="center">
						<?php if ($video->isPendingProcess() || $video->isProcessing()) { ?>
							<a class="es-state-pending" href="javascript:void(0);" data-es-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_CURRENTLY_PENDING');?>"></a>
						<?php } else { ?>
							<?php echo $this->html('grid.published', $video, 'videos'); ?>
						<?php } ?>
					</td>
					<td style="text-align: center;">
						<a href="<?php echo FRoute::_('index.php?option=com_easysocial&view=videocategories&layout=form&id=' . $video->getCategory()->id);?>"><?php echo $video->getCategory()->title;?></a>
					</td>

					<?php } ?>

					<td class="center">
						<?php echo $video->getCreatedDate()->toSql();?>
					</td>
					<td class="center">
						<?php echo $video->id;?>
					</td>
				</tr>
				<?php } ?>

			<?php } else { ?>
				<tr class="is-empty">
					<td colspan="7" class="center empty">
						<div>
							<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_EMPTY_MESSAGE'); ?>
						</div>
					</td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="7">
						<div class="footer-pagination">
							<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>

		</table>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="ordering" value="<?php echo $ordering;?>" data-table-grid-ordering />
	<input type="hidden" name="direction" value="<?php echo $direction;?>" data-table-grid-direction />
	<input type="hidden" name="boxchecked" value="0" data-table-grid-box-checked />
	<input type="hidden" name="task" value="" data-table-grid-task />
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="controller" value="videos" />
	<input type="hidden" name="view" value="videos" />
</form>
