<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">

	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_GENERAL');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_GENERAL_INFO');?></div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_HEADER'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_HEADER'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_HEADER_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_headers', $this->config->get('layout_headers')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_DESCRIPTIONS_HEADER'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_DESCRIPTIONS_HEADER'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_DESCRIPTIONS_HEADER_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_header_description', $this->config->get('layout_header_description')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_HEADER_RESPECT_AUTHOR'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_HEADER_RESPECT_AUTHOR'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_HEADER_RESPECT_AUTHOR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_headers_respect_author', $this->config->get('layout_headers_respect_author')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_TOOLBAR'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_TOOLBAR'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_TOOLBAR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_toolbar', $this->config->get('layout_toolbar')); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_FRONTEND'); ?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_FRONTEND_INFO'); ?></div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_LATEST_POST'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_LATEST_POST'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_LATEST_POST_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_latest', $this->config->get('layout_latest')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BUTTON_IN_TOOLBAR'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BUTTON_IN_TOOLBAR'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BUTTON_IN_TOOLBAR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_option_toolbar', $this->config->get('layout_option_toolbar')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CATEGORIES'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CATEGORIES'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CATEGORIES_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_categories', $this->config->get('layout_categories')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TAGS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TAGS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TAGS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_tags', $this->config->get('layout_tags')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOGGERS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOGGERS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOGGERS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_bloggers', $this->config->get('layout_bloggers')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_SEARCH'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_SEARCH'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_SEARCH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_search', $this->config->get('layout_search')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TEAMBLOG'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TEAMBLOG'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TEAMBLOG_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_teamblog', $this->config->get('layout_teamblog')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_ARCHIVES'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_ARCHIVES'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_ARCHIVES_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_archives', $this->config->get('layout_archives')); ?>
					</div>
				</div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CALENDAR'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CALENDAR'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CALENDAR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'layout_calendar', $this->config->get('layout_calendar')); ?>
                    </div>
                </div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_LOGIN'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_LOGIN'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_LOGIN_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_login', $this->config->get('layout_login')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_EDIT_PROFILE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_EDIT_PROFILE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_EDIT_PROFILE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'toolbar_editprofile', $this->config->get('toolbar_editprofile')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_TEAM_REQUEST'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_TEAM_REQUEST'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_TEAM_REQUEST_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'toolbar_teamrequest', $this->config->get('toolbar_teamrequest')); ?>
					</div>
				</div>				

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_LOGOUT'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_LOGOUT'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_LOGOUT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'toolbar_logout', $this->config->get('toolbar_logout')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">


		<div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_ENABLE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_ENABLE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_ENABLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'layout_enabledashboardtoolbar', $this->config->get('layout_enabledashboardtoolbar')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_HOME'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_HOME'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_HOME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'layout_dashboardhome', $this->config->get('layout_dashboardhome')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_STATS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_STATS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_STATS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'layout_dashboardmain', $this->config->get('layout_dashboardmain')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_ENTRIES'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_ENTRIES'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_ENTRIES_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'layout_dashboardblogs', $this->config->get('layout_dashboardblogs')); ?>
                    </div>
                </div>



                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_COMMENTS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_COMMENTS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_COMMENTS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'layout_dashboardcomments', $this->config->get('layout_dashboardcomments')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_CATEGORIES'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_CATEGORIES'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TOOLBAR_DASHBOARD_CATEGORIES_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'layout_dashboardcategories', $this->config->get('layout_dashboardcategories')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_TAGS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_TAGS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_TAGS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'layout_dashboardtags', $this->config->get('layout_dashboardtags')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_TEAM_REQUEST'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_TEAM_REQUEST'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_TEAM_REQUEST_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'layout_dashboardteamrequest', $this->config->get('layout_dashboardteamrequest')); ?>
                    </div>
                </div>                

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_NEW_POST'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_NEW_POST'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_NEW_POST_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'layout_dashboardnewpost', $this->config->get('layout_dashboardnewpost')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_SETTINGS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_SETTINGS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_SETTINGS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'layout_dashboardsettings', $this->config->get('layout_dashboardsettings')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_LOGOUT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_LOGOUT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_LOGOUT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'layout_dashboardlogout', $this->config->get('layout_dashboardlogout')); ?>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>
