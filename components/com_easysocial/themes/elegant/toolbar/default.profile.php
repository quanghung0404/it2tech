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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<li class="toolbarItem toolbar-profile dropdown_" data-toolbar-profile>
	<a href="#" class="es-nav-dropdown-toggle dropdown-toggle_" data-bs-toggle="dropdown">
		<i class="fa fa-cog"></i>
	</a>

	<div class="es-nav-dropdown for-menu dropdown-menu" role="menu" data-toolbar-profile-dropdown>
		<?php if ($this->my->hasCommunityAccess()) { ?>
			<a href="<?php echo FRoute::profile();?>" class="es-nav-dropdown-cover" style="background-image: url('<?php echo $this->my->getCover();?>');">
				<div class="row-table">
					<div class="col-cell cell-thumb">
						<img src="<?php echo $this->my->getAvatar();?>">
					</div>

					<div class="col-cell cell-bio">
						<div class="cell-name text-overflow"><?php echo $this->my->getName();?></div>
						<div class="fd-small">
							<?php if ($this->config->get('points.enabled')) { ?>
								<span><?php echo $this->my->getPoints();?> <?php echo JText::_('COM_EASYSOCIAL_PROFILE_POINTS');?></span>
							<?php } ?>
							<span><?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GENERIC_FRIENDS', $this->my->getTotalFriends()), $this->my->getTotalFriends()); ?></span>
						</div>
					</div>
				</div>
			</a>
			<div>
				<a href="<?php echo FRoute::friends();?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_FRIENDS');?>
				</a>
			</div>

			<?php if ($this->config->get('friends.invites.enabled')) { ?>
			<div>
				<a href="<?php echo FRoute::friends(array('layout' => 'invite'));?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_INVITE_FRIENDS');?>
				</a>
			</div>
			<?php } ?>

			<?php if ($this->config->get('followers.enabled')){ ?>
			<div>
				<a href="<?php echo FRoute::followers();?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_FOLLOWERS');?>
				</a>
			</div>
			<?php } ?>

			<hr />

			<?php if ($this->template->get('show_browse_users', true)) { ?>
			<div>
				<a href="<?php echo FRoute::users();?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_BROWSE_USERS');?>
				</a>
			</div>
			<?php } ?>

			<?php if ($this->template->get('show_advanced_search', true)) { ?>				
			<div>
				<a href="<?php echo FRoute::search(array('layout' => 'advanced'));?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_ADVANCED_SEARCH');?>
				</a>
			</div>
			<?php } ?>
			<hr />
		<?php } ?>

		<div>
			<a href="<?php echo FRoute::profile(array('layout' => 'edit'));?>">
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_ACCOUNT_SETTINGS');?>
			</a>
		</div>

		<?php if ($this->my->hasCommunityAccess()) { ?>
			<div>
				<a href="<?php echo FRoute::profile(array('layout' => 'editPrivacy'));?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PRIVACY_SETTINGS');?>
				</a>
			</div>
			<div>
				<a href="<?php echo FRoute::profile(array('layout' => 'editNotifications'));?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_NOTIFICATION_SETTINGS');?>
				</a>
			</div>
			<div>
				<a href="<?php echo FRoute::activities();?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_ACTIVITIES');?>
				</a>
			</div>
		<?php } ?>

		<hr />
		<div>
			<a href="javascript:void(0);" class="logout-link" data-toolbar-logout-button>
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_LOGOUT');?>
			</a>

			<form class="logout-form" action="<?php echo JRoute::_('index.php');?>" data-toolbar-logout-form method="post">
				<input type="hidden" name="return" value="<?php echo $logoutReturn;?>" />
				<input type="hidden" name="option" value="com_easysocial" />
				<input type="hidden" name="controller" value="account" />
				<input type="hidden" name="task" value="logout" />
				<input type="hidden" name="view" value="" />
				<?php echo $this->html('form.token'); ?>
			</form>
		</div>
	</div>
</li>
