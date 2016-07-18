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
<form action="index.php" method="post" name="adminForm" class="esForm" id="adminForm" data-table-grid>

	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<?php echo $this->html( 'filter.search' , $search ); ?>
		</div>

		<div class="form-group">
			<strong><?php echo JText::_( 'COM_EASYSOCIAL_FILTER_BY' ); ?> :</strong>
			<div>
				<select name="filter" class="form-control input-sm" data-table-grid-filter>
					<option value="all"<?php echo $filter == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_GRID_SELECT_TYPE' );?></option>
					<option value="fields"<?php echo $filter == 'fields' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_GRID_FILTER_TYPE_FIELDS' );?></option>
					<option value="apps"<?php echo $filter == 'apps' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_GRID_FILTER_TYPE_APPS' );?></option>
				</select>
			</div>
		</div>

		<div class="form-group pull-right">
			<div><?php echo $this->html( 'filter.limit' , $limit ); ?></div>
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
						<?php echo $this->html( 'grid.sort' , 'title' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_TITLE' ) , $ordering , $direction ); ?>
					</th>

					<th class="center" width="10%">
						<?php echo $this->html( 'grid.sort' , 'type' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_TYPE' ) , $ordering , $direction ); ?>
					</th>

					<th class="center" width="10%">
						<?php echo $this->html( 'grid.sort' , 'group' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_GROUP' ) , $ordering , $direction ); ?>
					</th>

					<th width="5%" class="center">
						<?php echo $this->html( 'grid.sort' , 'id' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ID' ) , $ordering , $direction ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if( $apps ){ ?>
					<?php $i = 0; ?>
					<?php foreach( $apps as $app ){ ?>
					<tr>
						<td class="center">
							<?php echo $this->html( 'grid.id' , $i++ , $app->id ); ?>
						</td>

						<td>
							<strong><?php echo $app->get( 'title' ); ?></strong>
							<div class="fd-small">
								<?php echo JText::_( 'COM_EASYSOCIAL_APPS_AUTHOR' );?>: <strong><?php echo $app->getMeta()->author;?></strong>
								&bull;
								<?php echo JText::_( 'COM_EASYSOCIAL_APPS_VERSION' );?>: <strong><?php echo $app->version ? $app->version : JText::_( 'COM_EASYSOCIAL_NOT_AVAILABLE_SYMBOL' ) ; ?></strong>
							</div>
						</td>

						<td class="center">
							<?php echo JText::_( 'COM_EASYSOCIAL_APPS_TYPE_' . strtoupper( $app->type ) ); ?>
						</td>

						<td class="center">
							<?php echo $app->group; ?>
						</td>

						<td class="center">
							<?php echo $app->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr class="is-empty">
						<td colspan="8" class="center empty">
							<?php echo JText::_( 'COM_EASYSOCIAL_APPS_DISCOVER_NO_APPS_FOUND' );?>
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

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="ordering" value="<?php echo $ordering;?>" data-table-grid-ordering />
	<input type="hidden" name="direction" value="<?php echo $direction;?>" data-table-grid-direction />
	<input type="hidden" name="boxchecked" value="0" data-table-grid-box-checked />
	<input type="hidden" name="task" value="" data-table-grid-task />
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="view" value="apps" />
	<input type="hidden" name="layout" value="discover" />
	<input type="hidden" name="controller" value="apps" />
</form>
