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
<form name="adminForm" id="adminForm" method="post" data-table-grid>
	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<?php echo $this->html( 'filter.search' , $search ); ?>
		</div>

		<div class="form-group">
			<strong><?php echo JText::_( 'COM_EASYSOCIAL_FILTER_BY' ); ?> :</strong>
			<div>
				<?php echo $this->html('filter.lists', $extensions, 'extension', $extension, JText::_('COM_EASYSOCIAL_FILTER_SELECT_EXTENSION'), 'all'); ?>
				<?php echo $this->html('filter.published', 'published', $state); ?>
				<?php echo $this->html('filter.lists', $groups, 'group', $group, JText::_('COM_EASYSOCIAL_FILTER_SELECT_GROUP'), 'all'); ?>
			</div>
		</div>

		<div class="form-group pull-right">
			<div><?php echo $this->html( 'filter.limit' , $limit ); ?></div>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table table table-eb table-striped">
			<thead>
				<th width="1%" class="center">
					<input type="checkbox" name="toggle" class="checkAll" data-table-grid-checkall />
				</th>

				<th>
					<?php echo $this->html('grid.sort' , 'title' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_TITLE' ) , $ordering , $direction ); ?>
				</th>

				<th width="5%" class="center">
					<?php echo $this->html('grid.sort' , 'state' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_STATUS' ) , $ordering , $direction ); ?>
				</th>

				<th width="10%" class="center">
					<?php echo $this->html( 'grid.sort' , 'name' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ACCESS_NAME' ) , $ordering , $direction ); ?>
				</th>

				<th width="5%" class="center">
					<?php echo $this->html( 'grid.sort' , 'element' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ELEMENT' ) , $ordering , $direction ); ?>
				</th>

				<th width="5%" class="center">
					<?php echo $this->html( 'grid.sort' , 'group' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_GROUP' ) , $ordering , $direction ); ?>
				</th>

				<th width="5%" class="center">
					<?php echo $this->html( 'grid.sort' , 'extension' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_EXTENSION' ) , $ordering , $direction ); ?>
				</th>

				<th width="10%" class="center">
					<?php echo $this->html( 'grid.sort' , 'created' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_CREATED' ) , $ordering , $direction ); ?>
				</th>

				<th width="5%" class="center">
					<?php echo $this->html( 'grid.sort' , 'id' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ID' ) , $ordering , $direction ); ?>
				</th>
			</thead>

			<tbody>
			<?php if (!empty($access)) { ?>
				<?php $i = 0; ?>
				<?php foreach ($access as $acc) { ?>
				<tr>
					<td class="center">
						<?php echo $this->html('grid.id', $i, $acc->id); ?>
					</td>

					<td>
						<span data-es-provide="tooltip" data-placement="bottom" data-title="<?php echo $acc->get('description');?>">
							<?php echo $acc->get('title'); ?>
						</span>
					</td>

					<td class="center">
						<?php echo $this->html('grid.published', $acc, 'access'); ?>
					</td>

					<td class="center">
						<?php echo $acc->name; ?>
					</td>

					<td class="center">
						<?php echo $acc->get('element'); ?>
					</td>

					<td class="center">
						<?php echo $acc->get('group'); ?>
					</td>

					<td class="center">
						<?php echo $acc->get('extension'); ?>
					</td>

					<td class="center">
						<?php echo $acc->created; ?>
					</td>

					<td class="center">
						<?php echo $acc->id; ?>
					</td>
				</tr>
				<?php $i++; ?>
				<?php } ?>
			<?php } else { ?>
				<tr class="is-empty">
					<td colspan="9" class="empty center">
						<div><?php echo JText::_( 'COM_EASYSOCIAL_ACCESS_LIST_EMPTY' ); ?></div>
					</td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="9">
						<div class="footer-pagination"><?php echo $pagination->getListFooter();?></div>
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
	<input type="hidden" name="view" value="access" />
	<input type="hidden" name="controller" value="access" />
</form>
