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
			<strong><?php echo JText::_( 'COM_EASYSOCIAL_FILTER_BY' ); ?> :</strong>
			<div>
				<?php echo $this->html( 'filter.published' , 'published' , $published ); ?>
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
					<input type="checkbox" name="toggle" data-table-grid-checkall />
				</th>
				<th>
					<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_TITLE' ); ?>
				</th>
				<th class="center" width="5%">
					<?php echo $this->html( 'grid.sort' , 'published' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_STATUS' ) , $ordering , $direction ); ?>
				</th>
				<th width="10%" class="center">
					<?php echo $this->html( 'grid.sort' , 'email' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_EMAIL_NOTIFICATIONS' ) , $ordering , $direction ); ?>
				</th>
				<th width="10%" class="center">
					<?php echo $this->html( 'grid.sort' , 'system' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_SYSTEM_NOTIFICATIONS' ) , $ordering , $direction ); ?>
				</th>
				<th width="25%" class="center">
					<?php echo $this->html( 'grid.sort' , 'created' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_CREATED' ) , $ordering , $direction ); ?>
				</th>
				<th width="5%" class="center">
					<?php echo $this->html( 'grid.sort' , 'id' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ID' ) , $ordering , $direction ); ?>
				</th>
			</thead>

			<tbody>
			<?php if( $alerts ){ ?>
				<?php $i = 0; ?>
				<?php foreach( $alerts as $alert ){ ?>
				<tr>
					<td>
						<?php echo $this->html( 'grid.id' , $i , $alert->id ); ?>
					</td>
					<td>
						<div><?php echo ucfirst( $alert->element ); ?> - <?php echo $alert->getTitle();?></div>

						<div class="fd-small"><?php echo $alert->getDescription();?></div>
					</td>
					<td class="center">
						<?php echo $this->html( 'grid.published' , $alert , '' , 'published' ); ?>
					</td>
					<td class="center">
						<?php if( $alert->email >= 0 ) { ?>
							<?php echo $this->html( 'grid.published' , $alert , 'alerts' , 'email' , array( 'emailPublish' , 'emailUnpublish' ) ); ?>
						<?php } else { ?>
							<?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_NOTIFICATION_NOT_APPLICABLE' ); ?>
						<?php } ?>
					</td>
					<td class="center">
						<?php if( $alert->system >= 0 ) { ?>
							<?php echo $this->html( 'grid.published' , $alert , 'alerts' , 'system' , array( 'systemPublish' , 'systemUnpublish' ) ); ?>
						<?php } else { ?>
							<?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_NOTIFICATION_NOT_APPLICABLE' ); ?>
						<?php } ?>
					</td>
					<td class="center">
						<?php echo $alert->created;?>
					</td>
					<td class="center">
						<?php echo $alert->id;?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr class="is-empty">
					<td colspan="7" class="empty center">
						<div>
							<?php echo JText::_( 'COM_EASYSOCIAL_ALERTS_LIST_EMPTY' ); ?>
						</div>
					</td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="7">
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
	<input type="hidden" name="view" value="alerts" />
	<input type="hidden" name="controller" value="alerts" />

</form>

