<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" id="adminForm" method="post" name="adminForm" data-table-grid>
	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<?php echo $this->html('filter.search', $search); ?>
		</div>

		<div class="pull-right">
			<?php echo $this->html('filter.limit', $limit); ?>
		</div>
	</div>

	<div id="pendingUsersTable" class="panel-table">
		<table class="app-table table table-eb table-striped" data-pending-users>
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
					<th width="10%" class="text-center">
						<?php echo JText::_('COM_EASYSOCIAL_TABLE_COLUMN_DEFAULT'); ?>
					</th>
					<th width="10%" class="text-center">
						<?php echo JText::_('COM_EASYSOCIAL_TABLE_COLUMN_STATE'); ?>
					</th>
					<?php } ?>

					<th width="10%" class="text-center">
						<?php echo JText::_('COM_EASYSOCIAL_TABLE_COLUMN_VIDEOS'); ?>
					</th>
					<th width="5%" class="text-center">
						<?php echo $this->html( 'grid.sort' , 'id' , JText::_( 'COM_EASYSOCIAL_USERS_ID' ) , $ordering , $direction ); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($categories) { ?>
				<?php $i = 0; ?>

				<?php foreach ($categories as $category) { ?>
				<tr class="row<?php echo $i; ?>" data-grid-row data-id="<?php echo $category->id;?>">
					<?php if (!$simple) { ?>
					<td>
						<?php echo $this->html('grid.id', $i++, $category->id); ?>
					</td>
					<?php } ?>


					<td>
						<a href="<?php echo JRoute::_('index.php?option=com_easysocial&view=videocategories&layout=form&id=' . $category->id);?>"
                                    data-category-insert
                                    data-id="<?php echo $category->id;?>"
                                    data-alias="<?php echo $category->getAlias();?>"
                                    data-title="<?php echo $this->html('string.escape', $category->get('title'));?>"
						><?php echo $category->title;?></a>
					</td>

					<?php if (!$simple) { ?>
					<td class="text-center">
						<?php echo $this->html('grid.featured', $category, 'videocategories'); ?>
					</td>
					<td class="text-center">
						<?php echo $this->html('grid.published', $category, 'state'); ?>
					</td>
					<?php } ?>

					<td class="text-center">
						<?php echo $category->getTotalVideos();?>
					</td>
					
					<td class="text-center">
						<?php echo $category->id;?>
					</td>
				</tr>
				<?php } ?>

			<?php } else { ?>
				<tr class="is-empty">
					<td colspan="6" class="empty">
						<div>
							<?php echo JText::_('COM_EASYSOCIAL_CATEGORIES_EMPTY'); ?>
						</div>
					</td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="6">
						<div class="footer-pagination">
							<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>

		</table>
	</div>

	<?php echo $this->html('form.action', 'videocategories'); ?>

	<?php if ($this->tmpl == 'component') { ?>
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="jscallback" value="<?php echo JRequest::getCmd('jscallback');?>" />
	<?php } ?>	
	
	<input type="hidden" name="ordering" value="<?php echo $ordering;?>" data-table-grid-ordering />
	<input type="hidden" name="direction" value="<?php echo $direction;?>" data-table-grid-direction />
	<input type="hidden" name="view" value="videocategories" />
</form>
