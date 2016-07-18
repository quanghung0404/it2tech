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

	<div class="app-filter filter-bar form-inline<?php echo $callback ? ' mt-20' : '';?>">
		<div class="form-group">
			<?php echo $this->html( 'filter.search' , $search ); ?>
		</div>

		<?php if( !$callback ){ ?>
		<div class="form-group">
			<strong><?php echo JText::_( 'COM_EASYSOCIAL_FILTER_BY' ); ?> :</strong>
			<div><?php echo $this->html( 'filter.published' , 'state' , $state ); ?></div>
		</div>

		<div class="form-group pull-right">
			<?php echo $this->html( 'filter.limit' , $limit ); ?>
		</div>
		<?php } ?>
	</div>

	<?php if ($orphanCount) { ?>
	<div class="mt-20 filter-bar">
		<span class="label label-danger small"><?php echo JText::_('COM_EASYSOCIAL_FOOTPRINT_NOTE');?></span>
		<?php echo JText::sprintf( 'COM_EASYSOCIAL_PROFILES_ORPHAN_ITEMS_NOTICE', $orphanCount );?>
	</div>
	<br />
	<?php } ?>

	<div id="profilesTable" class="panel-table" data-profiles>
		<table class="app-table table table-eb table-striped">
			<thead>
				<tr>
					<?php if( !$callback ){ ?>
					<th width="1%" class="center">
						<input type="checkbox" name="toggle" data-table-grid-checkall />
					</th>
					<?php } ?>

					<th style="text-align: left;">
						<?php echo $this->html( 'grid.sort' , 'title' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_TITLE' ) , $ordering , $direction ); ?>
					</th>

					<?php if( !$callback ){ ?>
					<th class="center" width="5%">
						<?php echo $this->html( 'grid.sort' , 'default' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_DEFAULT' ) , $ordering , $direction ); ?>
					</th>

					<th class="center" width="5%">
						<?php echo $this->html( 'grid.sort' , 'state' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_STATUS' ) , $ordering , $direction ); ?>
					</th>
					<?php } ?>

					<th width="5%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_USERS' ); ?>
					</th>

					<?php if( !$callback ){ ?>
					<th class="center" width="10%">
						<?php echo $this->html('grid.sort' , 'ordering' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ORDERING' ) , $ordering , $direction ); ?>
						<?php echo $this->html('grid.order' , $profiles); ?>
					</th>
					<th width="10%" class="center">
						<?php echo $this->html( 'grid.sort' , 'created' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_CREATED' ) , $ordering , $direction ); ?>
					</th>
					<th width="10%" class="center">
						<?php echo $this->html( 'grid.sort' , 'modified' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_MODIFIED' ) , $ordering , $direction ); ?>
					</th>
					<?php } ?>

					<th width="<?php echo $callback ? '10%' : '5%';?>" class="center">
						<?php echo $this->html( 'grid.sort' , 'id' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ID' ) , $ordering , $direction ); ?>
					</th>
				</tr>
			</thead>
			<tbody>

				<?php if ($profiles) { ?>
					<?php $i = 0; ?>

					<?php foreach ($profiles as $profile) { ?>
					<tr class="row<?php echo $i; ?>"
						data-profiles-item
						data-grid-row
						data-title="<?php echo $this->html('string.escape', $profile->get('title'));?>"
						data-id="<?php echo $profile->id;?>"
						data-avatar="<?php echo $profile->getAvatar();?>"
						data-alias="<?php echo $profile->alias;?>"
					>
						<?php if (!$callback) { ?>
						<td align="center">
							<?php echo $this->html( 'grid.id' , $i , $profile->id ); ?>
						</td>
						<?php } ?>

						<td>
							<a href="<?php echo FRoute::_( 'index.php?option=com_easysocial&view=profiles&layout=form&id=' . $profile->id );?>"
								data-profile-insert
								data-title="<?php echo $this->html( 'string.escape' , $profile->get( 'title' ) );?>"
								data-id="<?php echo $profile->id;?>"
								data-avatar="<?php echo $profile->getAvatar();?>"
								data-alias="<?php echo $profile->alias;?>"
							><?php echo $profile->get( 'title' ); ?></a>
						</td>

						<?php if (!$callback) { ?>
						<td class="center">
							<?php echo $this->html( 'grid.featured' , $profile , 'profiles' ); ?>
						</td>
						<td class="center">
							<?php echo $this->html( 'grid.published' , $profile , 'profiles' ); ?>
						</td>
						<?php } ?>

						<td class="center">
							<?php if (!$callback) { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_easysocial&view=users&profile=' . $profile->id);?>">
							<?php } ?>

								<?php echo $profile->getMembersCount(false); ?>

							<?php if (!$callback) { ?>
							</a>
							<?php } ?>
						</td>

						<?php if (!$callback) { ?>
						<td class="order center">
							<?php echo $this->html( 'grid.ordering' , count( $profiles ) , ( $i + 1 ) , $ordering == 'ordering' ,  $profile->ordering ); ?>
						</td>

						<td class="center">
							<?php echo $profile->created; ?>
						</td>

						<td class="center">
							<?php echo $profile->modified; ?>
						</td>
						<?php } ?>

						<td class="center">
							<?php echo $profile->id;?>
						</td>
					</tr>
						<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
					<tr class="is-empty">
						<td colspan="9" class="center empty">
							<?php echo JText::_( 'COM_EASYSOCIAL_NO_PROFILES_AVAILABLE_CURRENTLY' );?>
						</td>
					</tr>
				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="9" class="center">
						<div class="footer-pagination"><?php echo $pagination->getListFooter(); ?></div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<?php if ($this->tmpl == 'component') { ?>
	<input type="hidden" name="tmpl" value="component" />
	<?php } ?>
	<input type="hidden" name="jscallback" value="<?php echo $this->html('string.escape', JRequest::getWord('jscallback'));?>" />
	<input type="hidden" name="callback" value="<?php echo $this->html('string.escape', JRequest::getWord('callback'));?>" />
	<input type="hidden" name="ordering" value="<?php echo $ordering;?>" data-table-grid-ordering />
	<input type="hidden" name="direction" value="<?php echo $direction;?>" data-table-grid-direction />
	<input type="hidden" name="boxchecked" value="0" data-table-grid-box-checked />
	<input type="hidden" name="task" value="" data-table-grid-task />
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="view" value="profiles" />
	<input type="hidden" name="controller" value="profiles" />
</form>
