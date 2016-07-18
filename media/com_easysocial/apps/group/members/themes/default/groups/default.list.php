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

<ul class="fd-reset-list group-members app-contents-list">
	<?php foreach ($users as $user) { ?>

	<li class="member-item
				<?php echo $group->isPendingInvitationApproval( $user->id ) ? ' is-pending-invitation' : '';?>
				<?php echo $group->isPendingMember( $user->id ) ? ' is-pending' : '';?>
				<?php echo $group->isOwner( $user->id ) ? ' is-owner' : '';?>
				<?php echo $group->isAdmin( $user->id ) && !$group->isOwner( $user->id ) ? ' is-admin' : '';?>
				<?php echo $group->isMember( $user->id ) && !$group->isOwner( $user->id ) && !$group->isAdmin( $user->id ) ? ' is-member' : '';?>"
				data-group-members-item
				data-id="<?php echo $user->id;?>"
				data-groupid="<?php echo $group->id;?>"
				data-redirect="<?php echo $redirect;?>"
	>
		<?php if( ( $group->isAdmin() || $group->isOwner() ) && !$group->isOwner( $user->id ) || $this->my->isSiteAdmin()){ ?>
		<div class="pull-right btn-group">
			<a class="dropdown-toggle_ loginLink btn btn-es btn-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);">
				<i class="icon-es-dropdown"></i>
			</a>
			<ul class="dropdown-menu dropdown-menu-user messageDropDown">
				<?php if ($group->isPendingInvitationApproval($user->id)) { ?>
				<li>
					<a href="javascript:void(0);" data-members-cancel-invitation><?php echo JText::_('APP_GROUP_MEMBERS_CANCEL_INVITATION'); ?></a>
				</li>
				<?php } ?>

				<?php if( $group->isPendingMember( $user->id ) ){ ?>
				<li>
					<a href="javascript:void(0);" data-members-approve><?php echo JText::_('APP_GROUP_MEMBERS_APPROVE'); ?></a>
				</li>
				<li>
					<a href="javascript:void(0);" data-members-reject><?php echo JText::_('APP_GROUP_MEMBERS_REJECT'); ?></a>
				</li>
				<?php } ?>

				<?php if ($group->isAdmin($user->id) && ($group->isOwner() || $this->my->isSiteAdmin())) { ?>
				<li>
					<a href="javascript:void(0);" data-members-revoke-admin><?php echo JText::_('APP_GROUP_MEMBERS_REVOKE_ADMIN');?></a>
				</li>
				<?php } ?>

				<?php if( !$group->isAdmin( $user->id ) && $group->isMember( $user->id ) ){ ?>
				<li>
					<a href="javascript:void(0);" data-members-make-admin><?php echo JText::_( 'APP_GROUP_MEMBERS_MAKE_ADMIN' );?></a>
				</li>
				<?php } ?>

				<?php if( $group->isMember( $user->id ) ){ ?>
				<li>
					<a href="javascript:void(0);" data-members-remove><?php echo JText::_( 'APP_GROUP_MEMBERS_REMOVE_FROM_GROUP' );?></a>
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php } ?>

		<?php echo $this->loadTemplate('site/avatar/default', array('user' => $user)); ?>
		<h5>

			<?php echo $this->html('html.user', $user->id, false); ?>
            &#8207;
			<span class="label label-danger label-owner"><?php echo JText::_( 'APP_GROUP_MEMBERS_OWNER' ); ?></span>

			<span class="label label-success label-admin"><?php echo JText::_( 'APP_GROUP_MEMBERS_ADMIN' ); ?></span>

			<span class="label label-info label-member"><?php echo JText::_( 'APP_GROUP_MEMBERS_MEMBER' ); ?></span>

			<span class="label label-warning label-pending"><?php echo JText::_( 'APP_GROUP_MEMBERS_PENDING' );?></span>

			<span class="label label-warning label-pending-invitation"><?php echo JText::_( 'APP_GROUP_MEMBERS_INVITED' );?></span>

		</h5>

		<div class="desc">
			<?php if( $group->isInvited( $user->id ) ){ ?>
				<?php echo JText::sprintf( 'APP_GROUP_MEMBERS_INVITED_BY' , $this->html( 'html.user' , $group->getInvitor( $user->id )->id , true ) , $group->getJoinedDate( $user->id )->toLapsed() ); ?>
			<?php } ?>

			<?php if( $group->isMember( $user->id ) && !$group->isInvited( $user->id ) ){ ?>
				<?php echo JText::sprintf( 'APP_GROUP_MEMBERS_JOINED' , $group->getJoinedDate( $user->id )->toLapsed() ); ?>
			<?php } ?>

			<?php if( $group->isPendingMember( $user->id ) ){ ?>
				<?php echo JText::sprintf( 'APP_GROUP_MEMBERS_REQUESTED' , $group->getJoinedDate( $user->id )->toLapsed() ); ?>
			<?php } ?>
		</div>
	</li>
	<?php } ?>

</ul>



<div class="empty empty-hero">
	<?php echo JText::_( 'APP_GROUP_MEMBERS_EMPTY' ); ?>
</div>
