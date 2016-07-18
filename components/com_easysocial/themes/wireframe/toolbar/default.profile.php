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
<li class="toolbarItem toolbar-profile" data-toolbar-profile
    data-popbox
    data-popbox-id="fd"
    data-popbox-component="es"
    data-popbox-type="toolbar"
    data-popbox-toggle="click"
    data-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>"
    data-popbox-target=".toobar-profile-popbox"
>

	<a href="javascript:void(0);" class="dropdown-toggle_ login-link loginLink">
		<span class="es-avatar">
			<img src="<?php echo $this->my->getAvatar();?>" alt="<?php echo $this->html('string.escape' , $this->my->getName());?>" />
		</span>
		<span class="toolbar-user-name"><?php echo $this->my->getName();?></span>
		<b class="caret"></b>
	</a>

	<div style="display:none;" class="toobar-profile-popbox" data-toolbar-profile-dropdown>
		<ul class="popbox-dropdown-menu dropdown-menu-user" style="display: block;">
			<?php if ($this->my->hasCommunityAccess()) { ?>
				<li>
					<h5 class="ml-10">
						<i class="fa fa-home"></i> <?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_HEADING_ACCOUNT');?>
					</h5>
				</li>
				<li class="divider"></li>

				<li>
					<a href="<?php echo FRoute::profile();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_VIEW_YOUR_PROFILE');?>
					</a>
				</li>
				<li>
					<a href="<?php echo FRoute::friends();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_FRIENDS');?>
					</a>
				</li>

				<?php if ($this->config->get('friends.invites.enabled')) { ?>
				<li>
					<a href="<?php echo FRoute::friends(array('layout' => 'invite'));?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_INVITE_FRIENDS');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->config->get('followers.enabled')){ ?>
				<li>
					<a href="<?php echo FRoute::followers();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_FOLLOWERS');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->config->get('photos.enabled')){ ?>
				<li>
					<a href="<?php echo FRoute::albums(array('uid' => $this->my->getAlias() , 'type' => SOCIAL_TYPE_USER));?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_PHOTOS');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->config->get('video.enabled')) { ?>
				<li>
					<a href="<?php echo FRoute::videos();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_VIDEOS');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->config->get('groups.enabled')){ ?>
				<li>
					<a href="<?php echo FRoute::groups();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_GROUPS');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->config->get('events.enabled')){ ?>
				<li>
					<a href="<?php echo FRoute::events();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_EVENTS');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->config->get('badges.enabled')){ ?>
				<li>
					<a href="<?php echo FRoute::badges(array('layout' => 'achievements'));?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_ACHIEVEMENTS');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->config->get('points.enabled')){ ?>
				<li>
					<a href="<?php echo FRoute::points(array('layout' => 'history' , 'userid' => $this->my->getAlias()));?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_POINTS_HISTORY');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->config->get('conversations.enabled')){ ?>
				<li>
					<a href="<?php echo FRoute::conversations();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_CONVERSATIONS');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->config->get('apps.browser')) { ?>
				<li>
					<a href="<?php echo FRoute::apps();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_APPS');?>
					</a>
				</li>
				<?php } ?>
				
				<li class="divider"></li>

				<li>
					<h5 class="ml-10">
						<i class="fa fa-cloud"></i> <?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_DISCOVER'); ?>
					</h5>
				</li>
				<li class="divider"></li>

				<?php if ($this->template->get('show_browse_users', true)) { ?>
				<li>
					<a href="<?php echo FRoute::users();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_BROWSE_USERS');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->template->get('show_advanced_search', true)) { ?>				
				<li>
					<a href="<?php echo FRoute::search(array('layout' => 'advanced'));?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_ADVANCED_SEARCH');?>
					</a>
				</li>
				<?php } ?>
				<li class="divider"></li>
			<?php } ?>

			<li>
				<h5 class="ml-10">
					<i class="fa fa-cog"></i> <?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_HEADING_PREFERENCES'); ?>
				</h5>
			</li>
			<li class="divider"></li>
			<li>
				<a href="<?php echo FRoute::profile(array('layout' => 'edit'));?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_ACCOUNT_SETTINGS');?>
				</a>
			</li>

			<?php if ($this->my->hasCommunityAccess()) { ?>
				<li>
					<a href="<?php echo FRoute::profile(array('layout' => 'editPrivacy'));?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PRIVACY_SETTINGS');?>
					</a>
				</li>
				<li>
					<a href="<?php echo FRoute::profile(array('layout' => 'editNotifications'));?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_NOTIFICATION_SETTINGS');?>
					</a>
				</li>
				<li>
					<a href="<?php echo FRoute::activities();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_ACTIVITIES');?>
					</a>
				</li>
			<?php } ?>

			<li class="divider"></li>
			<li>
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
			</li>
		</ul>
	</div>
</li>
