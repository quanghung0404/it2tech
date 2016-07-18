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
<form action="index.php" method="post" name="adminForm" class="esForm" id="adminForm" data-table-grid>

	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<?php echo $this->html('filter.search', $search); ?>
		</div>

		<div class="form-group">
			<strong><?php echo JText::_('COM_EASYSOCIAL_FILTER_BY'); ?> :</strong>
			<div>
				<?php echo $this->html('filter.published', 'state', $state); ?>

				<select name="group" class="form-control input-sm mr-5" data-table-grid-filter>
					<option value="all"<?php echo $group == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_GRID_SELECT_GROUP' );?></option>
					<option value="group"<?php echo $group == 'group' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_GRID_FILTER_TYPE_GROUPS' );?></option>
					<option value="user"<?php echo $group == 'user' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_GRID_FILTER_TYPE_USERS' );?></option>
					<option value="event"<?php echo $group == 'event' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_GRID_FILTER_TYPE_EVENT' );?></option>
				</select>
			</div>
		</div>

		<div class="form-group pull-right">
			<div><?php echo $this->html( 'filter.limit' , $limit ); ?></div>
		</div>
	</div>

	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<strong>
				<i class="icon-es-help mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_APPS_FIELDS_INFO_NOTE'); ?>
				<a href="http://stackideas.com/apps" class="btn btn-sm btn-primary ml-20" target="_blank"><i class="ies-basket"></i>&nbsp; <?php echo JText::_( 'COM_EASYSOCIAL_APPS_BROWSER' ); ?></a>
			</strong>
		</div>
	</div>

	<div id="appsTable" class="panel-table">
		<table class="app-table table table-eb table-striped">
			<thead>
				<tr>
					<th width="1%" class="center">
						<input type="checkbox" name="toggle" data-table-grid-checkall />
					</th>
					<th style="text-align: left;">
						<?php echo $this->html('grid.sort', 'title' , JText::_('COM_EASYSOCIAL_TABLE_COLUMN_TITLE' ) , $ordering , $direction ); ?>
					</th>
					<th class="center" width="10%">
						<?php echo $this->html('grid.sort', 'state' , JText::_('COM_EASYSOCIAL_TABLE_COLUMN_STATUS' ) , $ordering , $direction ); ?>
					</th>
					<th class="center" width="10%">
						<?php echo $this->html('grid.sort', 'group' , JText::_('COM_EASYSOCIAL_TABLE_COLUMN_GROUP' ) , $ordering , $direction ); ?>
					</th>

					<th width="10%" class="center">
						<?php echo $this->html('grid.sort', 'created' , JText::_('COM_EASYSOCIAL_TABLE_COLUMN_ELEMENT'), $ordering , $direction ); ?>
					</th>

					<th width="5%" class="center">
						<?php echo $this->html('grid.sort', 'id', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_ID' ) , $ordering , $direction ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if( $apps ){ ?>
					<?php $i = 0; ?>
					<?php foreach( $apps as $app ){ ?>
					<tr>
						<td class="center">
							<?php echo $this->html('grid.id', $i++, $app->id); ?>
						</td>
						<td>
							<div>
								<a href="<?php echo FRoute::_( 'index.php?option=com_easysocial&view=apps&layout=form&id=' . $app->id );?>"><?php echo $app->get('title'); ?></a>
							</div>
						</td>
						<td class="center">
							<?php echo $this->html('grid.published', $app, 'apps'); ?>
						</td>
						<td class="center">
							<?php echo $app->group; ?>
						</td>

						<td class="center">
							<?php echo $app->element; ?>
						</td>

						<td class="center">
							<?php echo $app->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr class="is-empty">
						<td colspan="8" class="center empty">
							<?php echo JText::_( 'COM_EASYSOCIAL_APPS_NO_APPS_FOUND' );?>
						</td>
					</tr>
				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="8" class="center">
						<div class="footer-pagination"><?php echo $pagination->getListFooter(); ?></div>
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
	<input type="hidden" name="view" value="apps" />
	<input type="hidden" name="layout" value="fields" />
	<input type="hidden" name="controller" value="apps" />
</form>
