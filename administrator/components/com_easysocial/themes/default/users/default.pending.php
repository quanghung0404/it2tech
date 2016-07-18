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
			<?php echo $this->html( 'filter.search' , $search ); ?>
		</div>

		<div class="form-group">
			<strong><?php echo JText::_( 'COM_EASYSOCIAL_FILTER_BY' ); ?> :</strong>
			<div>
				<select class="form-control input-sm" name="filter" class="select" onchange="this.form.submit();">
					<option value=""><?php echo JText::_( 'COM_EASYSOCIAL_FILTER_SELECT_PENDING_STATE' ); ?></option>
					<option value="all"<?php echo empty( $filter ) ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_FILTER_ALL_PENDING_ACCOUNTS' );?></option>
					<option value="pending"<?php echo $filter == 'pending' ? ' selected="selected"' : '';?>>
						<?php echo JText::_( 'COM_EASYSOCIAL_FILTER_PENDING_APPROVAL' ); ?>
					</option>
					<option value="verify"<?php echo $filter == 'verify' ? ' selected="selected"' : '';?>>
						<?php echo JText::_( 'COM_EASYSOCIAL_FILTER_PENDING_ACTIVIATION' ); ?>
					</option>
				</select>
				<?php echo $this->html( 'filter.profiles' , 'profile' , $profile , array( 'class="form-control input-sm"' ) ); ?>
			</div>
		</div>

		<div class="pull-right">
			<?php echo $this->html( 'filter.limit' , $limit ); ?>
		</div>
	</div>

	<div id="pendingUsersTable" class="panel-table">
		<table class="app-table table table-eb table-striped" data-pending-users>
			<thead>
				<tr>
					<th width="5">
						<input type="checkbox" name="toggle" value="" data-table-grid-checkall />
					</th>
					<th style="text-align: left;">
						<?php echo $this->html( 'grid.sort' , 'name' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_NAME' ) , $ordering , $direction ); ?>
					</th>
					<th width="20%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ACTIONS' ); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_PROFILE_TYPE' ); ?>
					</th>
					<th width="10%" class="center">
						<?php echo $this->html( 'grid.sort' , 'block' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_REGISTRATION_DATE' ) , $ordering , $direction ); ?>
						<?php echo JText::_( '' ); ?>
					</th>
					<th width="15%" class="center">
						<?php echo $this->html( 'grid.sort' , 'email' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_EMAIL' ) , $ordering , $direction ); ?>
					</th>
					
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYSOCIAL_TABLE_COLUMN_ACCOUNT_TYPE'); ?>
					</th>

					<th width="5%" class="center">
						<?php echo $this->html( 'grid.sort' , 'id' , JText::_( 'COM_EASYSOCIAL_USERS_ID' ) , $ordering , $direction ); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if( $users ){ ?>
				<?php $i = 0; ?>
				<?php foreach( $users as $user ){ ?>
				<tr data-pending-item
					data-name="<?php echo $user->getName();?>"
					data-id="<?php echo $user->id;?>"
					data-avatar="<?php echo $user->getAvatar();?>"
					data-email="<?php echo $user->email;?>">
					<td>
						<?php echo $this->html( 'grid.id' , $i++ , $user->id ); ?>
					</td>
					<td align="left">
						<a href="<?php echo FRoute::_( 'index.php?option=com_easysocial&view=users&layout=form&id=' . $user->id );?>" data-user-item-insertLink>
							<?php echo $user->name;?>
						</a>
						<div class="fd-small">
							<?php echo JText::sprintf( 'COM_EASYSOCIAL_USERS_REGISTERED_ON' , FD::date( $user->registerDate )->toFormat( JText::_('COM_EASYSOCIAL_DATE_DMY') ) ); ?>
						</div>
					</td>
					<td class="center">
						<a href="javascript:void(0);" class="btn btn-sm btn-primary" data-pending-approve><?php echo JText::_('COM_EASYSOCIAL_USER_APPROVE_BUTTON'); ?></a>

						<a href="javascript:void(0);" class="btn btn-sm btn-es-danger" data-pending-reject><?php echo JText::_('COM_EASYSOCIAL_USER_REJECT_BUTTON'); ?></a>
					</td>
					<td style="text-align: center;">
						<a href="<?php echo JRoute::_( 'index.php?option=com_easysocial&view=profiles&layout=form&id=' . $user->getProfile()->id );?>">
							<?php echo $user->getProfile()->get( 'title' ); ?>
						</a>
					</td>
					<td class="center">
						<?php echo $user->registerDate; ?>
					</td>
					<td class="center">
						<a href="mailto:<?php echo $user->email;?>" target="_blank"><?php echo $user->email;?></a>
					</td>

					<td class="center">
						<i class="icon-es-<?php echo $user->type;?>-16 mr-5 mt-5"
							data-original-title="<?php echo $this->html( 'string.escape' , JText::sprintf( 'COM_EASYSOCIAL_USERS_USER_ACCOUNT_TYPE' , $user->type ) );?>"
							data-es-provide="tooltip"
						></i>
					</td>

					<td class="center">
						<?php echo $user->id;?>
					</td>
				</tr>
				<?php } ?>

			<?php } else { ?>
				<tr class="is-empty">
					<td colspan="8" class="center empty">
						<div>
							<?php echo JText::_( 'COM_EASYSOCIAL_USERS_NO_PENDING_USERS' ); ?>
						</div>
					</td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="8">
						<div class="footer-pagination">
							<?php echo $pagination->getListFooter(); ?>
						</div>
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
	<input type="hidden" name="view" value="users" />
	<input type="hidden" name="layout" value="pending" />
	<input type="hidden" name="controller" value="users" />
</form>
