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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-table-grid>

	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<strong><?php echo JText::_( 'COM_EASYSOCIAL_FILTER_BY' ); ?> :</strong>
			<div>
				<select name="component" class="form-control input-sm" onchange="this.form.submit();">
					<option value=""<?php echo empty( $component ) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYSOCIAL_INDEXER_FILTER_COMPONENT');?></option>
					<?php if( isset( $filterItem['component'] ) ) { ?>

						<?php foreach( $filterItem['component'] as $cp ) { ?>
							<option value="<?php echo $cp; ?>"<?php echo ( $component == $cp ) ? ' selected="selected"' : '';?>><?php echo $cp; ?></option>
						<?php } ?>

					<?php } ?>
				</select>
				<select name="type" class="form-control input-sm" onchange="this.form.submit();">
					<option value=""<?php echo empty( $type ) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYSOCIAL_INDEXER_FILTER_TYPE');?></option>
					<?php if( isset( $filterItem['type'] ) ) { ?>
						<?php foreach( $filterItem['type'] as $ty ) {
							$lang = JText::_( 'COM_EASYSOCIAL_INDEXER_TYPE_' . strtoupper( $ty ) );
						?>
							<option value="<?php echo $ty; ?>"<?php echo ( $type == $ty ) ? ' selected="selected"' : '';?> >
								<?php echo ( strpos( $lang, 'COM_EASYSOCIAL_INDEXER_TYPE_') !== false ) ? ucfirst( $ty ) : $lang ; ?>
							</option>
						<?php } ?>
					<?php } ?>
				</select>
			</div>
		</div>
	</div>

	<div id="appsTable" class="panel-table">
		<table class="app-table table table-eb table-striped">
			<thead>
				<tr>
					<th width="1%" class="center">
						<input type="checkbox" name="toggle" data-table-grid-checkall />
					</th>

					<th style="text-align: left;" width="10%">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_TITLE' ); ?>
					</th>

					<th class="text-align: left;">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_CONTENT' ); ?>
					</th>

					<th class="center" width="10%">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_TYPE' ); ?>
					</th>

					<th class="center" width="10%">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_COMPONENT' ); ?>
					</th>

					<th class="center" width="10%">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_LAST_UPDATE' ); ?>
					</th>

					<th width="1%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ID' );?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if( $items ){ ?>
					<?php $i = 0; ?>
					<?php foreach( $items as $item ){ ?>
					<tr>
						<td class="center">
							<?php echo $this->html( 'grid.id' , $i++ , $item->id ); ?>
						</td>

						<td>
							<?php echo $item->title; ?>
						</td>

						<td>
							<?php echo $item->content; ?>
						</td>

						<td class="center">
							<?php echo ( $item->component == 'com_easysocial' ) ? JText::_( 'COM_EASYSOCIAL_INDEXER_TYPE_' . strtoupper( $item->utype ) ) : ucfirst( $item->utype ) ; ?>
						</td>

						<td class="center">
							<?php echo ( $item->component == 'com_easysocial' ) ? JText::_( 'COM_EASYSOCIAL_INDEXER_EXTENSION_' . strtoupper( $item->component ) ) : $item->component; ?>
						</td>

						<td class="center">
							<?php
								$date = FD::date( $item->last_update );
								echo $date->toFormat( 'Y-m-d H:i' );
							?>
						</td>

						<td class="center">
							<?php echo $item->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="7" class="center">
							<?php echo JText::_( 'COM_EASYSOCIAL_NO_RECORD_FOUND' );?>
						</td>
					</tr>
				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="7" class="center">
						<div class="footer-pagination"><?php echo $pagination->getListFooter(); ?></div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="view" value="indexer" />
	<input type="hidden" name="controller" value="indexer" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>
