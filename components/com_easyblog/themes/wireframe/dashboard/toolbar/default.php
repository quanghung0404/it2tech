<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($this->config->get('layout_enabledashboardtoolbar')) { ?>
<div class="eb-toolbar row-table">
	<div class="col-cell dropdown_">
		<a href="#" data-bp-toggle="dropdown">
			<i class="fa fa-bars"></i>
		</a>
		<ul class="eb-toolbar-dropdown dropdown-menu reset-list text-left">
			<?php if ($this->config->get('layout_dashboardmain')) { ?>
			<li class="<?php echo $current == 'display' || $current == 'default' ? 'active' : '';?>">
				<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard');?>">
					<i class="fa fa-dashboard"></i>
					<span class="eb-text"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_STATS_TIPS');?></span>
				</a>
			</li>
			<?php } ?>

			<?php if ($this->config->get('layout_dashboardblogs') && $this->acl->get('add_entry')) { ?>
			<li class="<?php echo $current == 'entries' ? 'active' : '';?>" >
				<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries');?>">
					<i class="fa fa-file-text"></i>
					<span class="eb-text"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_BLOG_POSTS');?></span>
				</a>
			</li>
			<?php } ?>

			<?php if ($this->acl->get('publish_entry') || EB::isSiteAdmin()) { ?>
			<li class="<?php echo $current == 'moderate' ? 'active' : '';?>">
				<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=moderate');?>">
					<i class="fa fa-check-square"></i>
					<span class="eb-text"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_MODERATE');?></span>
					<b class="hide" data-moderate-counter>0</b>
				</a>
			</li>
			<?php } ?>

			<?php if ($this->config->get('layout_dashboardcategories') && $this->acl->get('create_category')) { ?>
			<li class="<?php echo $current == 'categories' ? 'active' : '';?>">
				<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=categories');?>">
					<i class="fa fa-folder-open"></i>
					<span class="eb-text"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_CATEGORIES');?></span>
				</a>
			</li>
			<?php } ?>

			<?php if ($this->config->get('layout_dashboardtags') && $this->acl->get('create_tag')) { ?>
			<li class="<?php echo $current == 'tags' ? 'active' : '';?>">
				<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=tags');?>">
					<i class="fa fa-tags"></i>
					<span class="eb-text"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_TAGS');?></span>
				</a>
			</li>
			<?php } ?>

			<?php if ($this->config->get('layout_dashboardcomments')) { ?>
			<li class="<?php echo $current == 'comments' ? 'active' : '';?>">
				<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=comments');?>">
					<i class="fa fa-comments"></i>
					<span class="eb-text"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_COMMENTS');?></span>
				</a>
			</li>
			<?php } ?>

			<?php if (!EB::isSiteAdmin() && !$this->acl->get('publish_entry')) { ?>
			<li class="<?php echo $current == 'pending' ? 'active' : '';?>">
				<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=pending');?>">
					<i class="fa fa-inbox"></i>
					<span class="eb-text"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_PENDING');?></span>
				</a>
			</li>
			<?php } ?>

			<?php if ((EB::isSiteAdmin() || EB::isTeamAdmin()) && $this->config->get('layout_dashboardteamrequest')) { ?>
			<li class="<?php echo $current == 'requests' ? 'active' : '';?>">
				<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=requests');?>">
					<i class="fa fa-users"></i>
					<span class="eb-text"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_TEAM_REQUESTS');?></span>
					<b class="hide" data-revisions-counter>0</b>
				</a>
			</li>
			<?php } ?>

			<li class="<?php echo $current == 'revisions' ? 'active' : '';?>">
				<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=revisions');?>">
					<i class="fa fa-files-o"></i>
					<span class="eb-text"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_REVISIONS');?></span>
					<b class="hide" data-revisions-counter>0</b>
				</a>
			</li>
		</ul>
	</div>
	<div class="col-cell">
		<?php if ($this->config->get('layout_dashboardhome')) { ?>
			<div>
				<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=latest');?>">
					<i class="fa fa-home" data-placement="bottom" data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYBLOG_BACK_TO_MY_BLOG');?>"></i>
				</a>
			</div>
		<?php } ?>
			
		<?php if ($this->acl->get('add_entry') && $this->config->get('layout_dashboardnewpost')) { ?>
			<div class="eb-toolbar-master" data-placement="bottom" data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_NEW_POST');?>">
				<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=write');;?>">
					<i class="fa fa-pencil"></i>
				</a>
			</div>

			<?php if ($this->config->get('main_microblog')) { ?>
				<div>
					<a href="#" class="dropdown-toggle_" data-bp-toggle="dropdown">
						<i class="fa fa-bolt" data-eb-provide="tooltip" data-placement="bottom" data-original-title="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST');?>"></i>
					</a>
					<ul class="eb-toolbar-dropdown dropdown-menu" role="menu">
						<li>
							<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=standard');?>">
								<i class="fa fa-pencil text-muted"></i>
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_STANDARD');?>
							</a>
						</li>
						<li>
							<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=photo');?>">
								<i class="fa fa-camera text-muted"></i>
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_PHOTO');?>
							</a>
						</li>
						<li>
							<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=video');?>">
								<i class="fa fa-video-camera text-muted"></i>
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_VIDEO');?>
							</a>
						</li>
						<li>
							<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=quote');?>">
								<i class="fa fa-quote-left text-muted"></i>
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_QUOTE');?>
							</a>
						</li>
						<li>
							<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=link');?>">
								<i class="fa fa-link text-muted"></i>
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_LINK');?>
							</a>
						</li>
					</ul>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
	<div class="col-cell dropdown_">
		<a data-bp-toggle="dropdown" href="#">
			<i class="fa fa-cog"></i>
		</a>
		<ul class="eb-toolbar-dropdown dropdown-menu reset-list text-left">
			<?php if ($this->config->get('layout_dashboardsettings')) { ?>			
				<li>
					<a href="<?php echo EB::getEditProfileLink();?>">
						<i class="fa fa-gear muted"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_ACCOUNT_SETTINGS');?>
					</a>
				</li>
			<?php } ?>
			
			<li role="presentation" class="divider"></li>

			<?php if ($this->config->get('layout_dashboardlogout')) { ?>
				<li>
	                <a href="javascript:void(0);" data-dashboard-sign-out>
	                    <i class="fa fa-unlock-alt muted"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_SIGN_OUT'); ?>
	                </a>
				</li>
			<?php } ?>
		</ul>

		<form id="eblog-logout" action="<?php echo JRoute::_('index.php'); ?>" method="post" data-dashboard-sign-out-form>
            <?php echo JHTML::_('form.token'); ?>
            <input type="hidden" value="<?php echo $logoutURL; ?>" name="return" />
            <input type="hidden" name="task" value="user.logout" />
            <input type="hidden" name="option" value="com_users" />
		</form>
	</div>
</div>
<?php } ?>
