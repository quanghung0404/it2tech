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

	<?php if( $this->tmpl != 'component' ){ ?>
	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<?php echo $this->html( 'filter.search' , $search ); ?>
		</div>

		<div class="form-group">
			<strong><?php echo JText::_( 'COM_EASYSOCIAL_FILTER_BY' ); ?> :</strong>
			<div>
				<?php echo $this->html('filter.published', 'published', $published); ?>
				<?php echo $this->html('filter.usergroups', 'group' , $group); ?>
				<?php echo $this->html('filter.profiles', 'profile' , $profile); ?>
			</div>
		</div>

		<div class="form-group pull-right">
			<?php echo $this->html( 'filter.limit' , $limit ); ?>
		</div>
	</div>
	<?php } ?>

	<?php if( $this->tmpl == 'component' ){ ?>
	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<input type="text" name="search" class="form-control input-sm" />
			<button class="btn btn-es btn-medium"><?php echo JText::_( 'COM_EASYSOCIAL_SEARCH_BUTTON' ); ?></button>
		</div>
	</div>
	<?php } ?>


	<div id="usersTable" class="panel-table" data-users>
		<table class="app-table table table-eb table-striped">
			<thead>
				<tr>
					<?php if( $multiple ){ ?>
					<th width="1%" class="center">
						<input type="checkbox" name="toggle" class="checkAll" data-table-grid-checkall />
					</th>
					<?php } ?>

					<th>
						<?php echo $this->html( 'grid.sort' , 'name' , JText::_( 'COM_EASYSOCIAL_USERS_NAME' ) , $ordering , $direction ); ?>
					</th>

					<th width="18%" class="center">
						<?php echo $this->html( 'grid.sort' , 'username' , JText::_( 'COM_EASYSOCIAL_USERS_USERNAME' ) , $ordering , $direction ); ?>
					</th>

					<?php if( $this->tmpl != 'component' ){ ?>
					<th width="5%" class="center">
						<?php echo $this->html( 'grid.sort' , 'block' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ENABLED' ) , $ordering , $direction ); ?>
					</th>
					<th width="5%" class="center">
						<?php echo $this->html( 'grid.sort' , 'block' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ACTIVATED' ) , $ordering , $direction ); ?>
					</th>

					<th width="5%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_FRIENDS' ); ?>
					</th>
					<th width="5%" class="center">
						<?php echo $this->html( 'grid.sort' , 'points' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_POINTS' ) , $ordering , $direction ); ?>
					</th>

					<th width="10%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_PROFILE_TYPE' ); ?>
					</th>

					<th width="10%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_USER_GROUPS' ); ?>
					</th>
					<?php } ?>

					<th width="10%" class="center">
						<?php echo $this->html( 'grid.sort' , 'email' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_EMAIL' ) , $ordering , $direction ); ?>
					</th>

					<?php if ($this->tmpl != 'component') { ?>
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYSOCIAL_TABLE_COLUMN_ACCOUNT_TYPE'); ?>
					</th>
					<?php } ?>

					<th width="<?php echo $this->tmpl == 'component' ? '10%' : '5%';?>" class="center">
						<?php echo $this->html( 'grid.sort' , 'id' , JText::_( 'COM_EASYSOCIAL_USERS_ID' ) , $ordering , $direction ); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if( $users ){ ?>
				<?php $i = 0; ?>
				<?php
				foreach( $users as $user )
				{
					$userObj	= FD::user( $user->id );
				?>
				<tr data-user-item
					data-name="<?php echo $userObj->getName();?>"
					data-title="<?php echo $this->html( 'string.escape' , $userObj->name );?>"
					data-alias="<?php echo $userObj->getAlias(true, true);?>"
					data-avatar="<?php echo $userObj->getAvatar(SOCIAL_AVATAR_MEDIUM);?>"
					data-email="<?php echo $userObj->email;?>"
					data-id="<?php echo $userObj->id;?>">
					<?php if( $multiple ){ ?>
					<td>
						<?php echo $this->html( 'grid.id' , $i , $users[ $i ]->id ); ?>
					</td>
					<?php } ?>

					<td style="text-align:left;">
						<a href="<?php echo FRoute::_( 'index.php?option=com_easysocial&view=users&layout=form&id=' . $user->id );?>"
							data-user-insert
							data-id="<?php echo $user->id;?>"
							data-alias="<?php echo $userObj->getAlias(true, true);?>"
							data-title="<?php echo $this->html( 'string.escape' , $userObj->name );?>"
							data-avatar="<?php echo $this->html( 'string.escape' , $userObj->getAvatar( SOCIAL_AVATAR_MEDIUM ) );?>"
						>
							<?php echo $userObj->name;?>
						</a>

						<?php if ($userObj->require_reset) { ?>
							<span class="fd-small label label-warning">Reset password required</span>
						<?php } ?>

						<?php if( $this->tmpl != 'component' ){ ?>
						<div class="fd-small">
							<?php if( $userObj->getLastVisitDate() == '0000-00-00 00:00:00' ){ ?>
								<?php echo JText::_( 'COM_EASYSOCIAL_USERS_NEVER_LOGGED_IN' ); ?>
							<?php } else { ?>
								<?php echo JText::sprintf( 'COM_EASYSOCIAL_USERS_LAST_LOGGED_IN' , $userObj->getLastVisitDate( 'lapsed' ) ); ?>
							<?php } ?>
						</div>
						<?php } ?>

					</td>
					<td class="center">
						<span><?php echo $userObj->username;?></span>
					</td>

					<?php if ($this->tmpl != 'component') { ?>
					<td class="center">
						<?php if( $userObj->state == SOCIAL_USER_STATE_PENDING ){ ?>
							<a class="es-state-locked" href="javascript:void(0);"
							data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_USERS_USER_IS_PENDING_MODERATION' );?>"
							data-es-provide="tooltip"
							></a>
						<?php } else { ?>
							<?php echo $this->html( 'grid.userPublished' , $this->my->id != $userObj->id , $userObj , 'users' ); ?>
						<?php }?>
					</td>
					<td class="center">
						<?php if( $userObj->activation ){ ?>
							<a href="javascript:void(0);" class="es-state-unactivated" data-activate-user data-es-provide="tooltip" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_USERS_USER_IS_PENDING_ACTIVATION' );?>"></a>
						<?php } else { ?>
							<a href="javascript:void(0);" class="es-state-publish" data-es-provide="tooltip" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_USERS_USER_IS_ACTIVATED' );?>"></a>
						<?php } ?>
					</td>

					<td class="center">
						<?php echo $userObj->getTotalFriends(); ?>
					</td>
					<td class="center">
						<?php echo $userObj->points; ?>
					</td>

					<td class="center">
						<?php $title = $userObj->getProfile()->title; ?>

						<?php if( $title ){ ?>
							<a href="<?php echo FRoute::_( 'index.php?option=com_easysocial&view=profiles&layout=form&id=' . $userObj->getProfile()->id );?>"><?php echo JText::_($title); ?></a>
						<?php } else { ?>
							<?php echo JText::_( 'COM_EASYSOCIAL_NOT_AVAILABLE' ); ?>
						<?php } ?>
					</td>
					<td class="center">
						<?php foreach( $userObj->getUserGroups() as $groupId => $groupTitle ){ ?>
							<div><?php echo JText::_($usergroups[$groupId]); ?></div>
						<?php } ?>
					</td>
					<?php } ?>

					<td class="center">
						<?php if( $this->tmpl != 'component' ){ ?>
						<a href="mailto:<?php echo $this->html( 'string.escape' , $user->email ); ?>" target="_blank">
						<?php } ?>

							<?php echo $user->email;?>

						<?php if( $this->tmpl != 'component' ){ ?>
						</a>
						<?php } ?>
					</td>

					<?php if ($this->tmpl != 'component') { ?>
					<td class="center">
						<i class="icon-es-<?php echo $userObj->type;?>-16 mr-5 mt-5"
							data-original-title="<?php echo $this->html( 'string.escape' , JText::sprintf( 'COM_EASYSOCIAL_USERS_USER_ACCOUNT_TYPE' , $userObj->type ) );?>"
							data-es-provide="tooltip"
						></i>
					</td>
					<?php } ?>

					<td class="center">
						<?php echo $userObj->id;?>
					</td>
				</tr>
				<?php $i++; ?>
				<?php } ?>

			<?php } else { ?>
			<tr>
				<td class="empty center" colspan="12">
					<div><?php echo JText::_( 'COM_EASYSOCIAL_USERS_NO_USERS_FOUND_BASED_ON_SEARCH_RESULT' ); ?></div>
				</td>
			</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="12">
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
	<input type="hidden" name="controller" value="users" />
</form>
