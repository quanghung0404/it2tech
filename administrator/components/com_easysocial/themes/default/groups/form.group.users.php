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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-groups-form-members>
	<div class="fd-cf">
		<div class="btn-group btn-group-sm pull-left mb-15">
			<button type="button" class="btn btn-default ph-10" data-group-add-member>
				<i class="fa fa-user-plus"></i> <?php echo JText::_('COM_EASYSOCIAL_GROUPS_MEMBERS_ADD_MEMBER'); ?>
			</button>
			<button type="button" class="btn btn-default ph-10" data-group-remove-member>
				<i class="fa fa-user-remove"></i> <?php echo JText::_('COM_EASYSOCIAL_GROUPS_MEMBERS_REMOVE_MEMBER'); ?>
			</button>
			<button type="button" class="btn btn-default ph-10" data-group-approve-member>
				<i class="fa fa-check "></i> <?php echo JText::_('COM_EASYSOCIAL_GROUPS_MEMBERS_APPROVE_MEMBER'); ?>
			</button>
			<button type="button" class="btn btn-default ph-10" data-group-promote-member>
				<i class="fa fa-arrow-up-2 "></i> <?php echo JText::_('COM_EASYSOCIAL_GROUPS_MEMBERS_PROMOTE_TO_ADMIN'); ?>
			</button>
			<button type="button" class="btn btn-default ph-10" data-group-demote-member>
				<i class="fa fa-angle-down "></i> <?php echo JText::_('COM_EASYSOCIAL_GROUPS_MEMBERS_REMOVE_ADMIN'); ?>
			</button>
		</div>
		<div class="form-group pull-right mt-0">
			<div><?php echo $this->html( 'filter.limit' , $limit ); ?></div>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table table table-eb table-striped">
			<thead>
				<tr>
					<th width="1%" class="center">
						<input type="checkbox" name="toggle" data-table-grid-checkall />
					</th>

					<th>
						<?php echo $this->html('grid.sort', 'username', JText::_('COM_EASYSOCIAL_USERS_NAME'), $ordering, $direction); ?>
					</th>

					<th width="5%" class="center">
						<?php echo $this->html('grid.sort', 'state', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_ENABLED'), $ordering, $direction); ?>
					</th>

					<th width="18%" class="center">
						<?php echo $this->html('grid.sort', 'username', JText::_('COM_EASYSOCIAL_USERS_USERNAME'), $ordering, $direction); ?>
					</th>

					<th width="10%" class="center">
						<?php echo $this->html('grid.sort', 'id', JText::_('COM_EASYSOCIAL_USERS_ID'), $ordering, $direction); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if (!empty($members)) { ?>
				<?php $i = 0; ?>
				<?php foreach ($members as $member) { ?>
					<?php $user = FD::user($member->uid); ?>
					<tr>
						<td><?php echo $this->html('grid.id', $i, $member->id); ?></td>

						<td style="text-align: left;">
							<span class="es-avatar es-avatar-rounded pull-left mr-15 ml-5">
								<img src="<?php echo $user->getAvatar(SOCIAL_AVATAR_MEDIUM);?>" width="24" align="left" />
							</span>

							<a href="<?php echo FRoute::_('index.php?option=com_easysocial&view=users&layout=form&id=' . $user->id);?>"
								data-user-insert
								data-id="<?php echo $user->id;?>"
								data-alias="<?php echo $user->getAlias();?>"
								data-title="<?php echo $this->html('string.escape', $user->name);?>"
								data-avatar="<?php echo $this->html('string.escape', $user->getAvatar(SOCIAL_AVATAR_MEDIUM));?>"
							>
								<?php echo $user->name;?>
							</a>

							<?php if ($member->isOwner()) { ?>
							<span class="label label-info"><?php echo JText::_('COM_EASYSOCIAL_GROUPS_MEMBERS_OWNER'); ?></span>
							<?php } ?>

							<?php if (!$member->isOwner() && $member->isAdmin()) { ?>
							<span class="label label-warning"><?php echo JText::_('COM_EASYSOCIAL_GROUPS_MEMBERS_ADMIN'); ?></span>
							<?php } ?>

							<?php if($this->tmpl != 'component'){ ?>
							<div class="fd-small">
								<?php if($user->getLastVisitDate() == '0000-00-00 00:00:00'){ ?>
									<?php echo JText::_('COM_EASYSOCIAL_USERS_NEVER_LOGGED_IN'); ?>
								<?php } else { ?>
									<?php echo JText::sprintf('COM_EASYSOCIAL_USERS_LAST_LOGGED_IN', $user->getLastVisitDate('lapsed')); ?>
								<?php } ?>
							</div>
							<?php } ?>
						</td>

						<td class="center">
							<?php echo $this->html('grid.published', $member, 'groups', 'state', array('publishUser', 'unpublishUser')); ?>
						</td>

						<td class="center">
							<span><?php echo $user->username;?></span>
						</td>

						<td class="center">
							<?php echo $user->id;?>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="5">
						<div class="footer-pagination"><?php echo $pagination->getListFooter();?></div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
