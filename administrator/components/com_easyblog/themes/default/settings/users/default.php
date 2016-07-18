<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="row">
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_USERS');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_USERS_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_JOOMLA_USER_PARAMETERS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_JOOMLA_USER_PARAMETERS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_JOOMLA_USER_PARAMETERS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_joomlauserparams', $this->config->get('main_joomlauserparams')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_EDIT_ACCOUNT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_EDIT_ACCOUNT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_EDIT_ACCOUNT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_dashboard_editaccount', $this->config->get('main_dashboard_editaccount')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOGIN_PROVIDER'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOGIN_PROVIDER'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_DESCRIPTION_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <select class="form-control" name="main_login_provider">
                            <option value="easysocial"<?php echo $this->config->get( 'main_login_provider' ) == 'easysocial' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_EASYSOCIAL' );?></option>
                            <option value="joomla"<?php echo $this->config->get( 'main_login_provider' ) == 'joomla' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_JOOMLA' );?></option>
                            <option value="cb"<?php echo $this->config->get( 'main_login_provider' ) == 'cb' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_CB' );?></option>
                            <option value="jomsocial"<?php echo $this->config->get( 'main_login_provider' ) == 'jomsocial' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_JOMSOCIAL' );?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_NON_BLOGGER_PROFILE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_NON_BLOGGER_PROFILE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_NON_BLOGGER_PROFILE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_nonblogger_profile', $this->config->get('main_nonblogger_profile')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_EXCLUDE_USERS_FROM_BLOGGER_LISTINGS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_EXCLUDE_USERS_FROM_BLOGGER_LISTINGS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_EXCLUDE_USERS_FROM_BLOGGER_LISTINGS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" name="layout_exclude_bloggers" class="form-control" value="<?php echo $this->config->get('layout_exclude_bloggers');?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_EXCLUDE_CATEGORIES_FROM_FRONTPAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_EXCLUDE_CATEGORIES_FROM_FRONTPAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_EXCLUDE_CATEGORIES_FROM_FRONTPAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" name="layout_exclude_categories" class="form-control" value="<?php echo $this->config->get( 'layout_exclude_categories' );?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_BLOGGER_TO_SWITCH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_BLOGGER_TO_SWITCH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_BLOGGER_TO_SWITCH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_blogprivacy_override', $this->config->get('main_blogprivacy_override')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_LISTINGS_OPTION'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_LISTINGS_OPTION'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_LISTINGS_OPTION_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_bloggerlistingoption', $this->config->get('main_bloggerlistingoption')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_USERS_AUTOMATION');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_USERS_AUTOMATION_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_AUTOMATIC_FEATURE_BLOG_POST'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_AUTOMATIC_FEATURE_BLOG_POST'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_AUTOMATIC_FEATURE_BLOG_POST_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_autofeatured', $this->config->get('main_autofeatured')); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
 <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="page" value="users" />
    <input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-settings-active />    
</form>
