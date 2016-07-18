<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
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
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_TITLE');?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_ADMIN'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_ADMIN'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_ADMIN_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'notification_blogadmin', $this->config->get('notification_blogadmin')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_ALL_MEMBERS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_ALL_MEMBERS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_ALL_MEMBERS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'notification_allmembers', $this->config->get('notification_allmembers')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_SUBSCRIBERS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_SUBSCRIBERS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_SUBSCRIBERS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'notification_blogsubscriber', $this->config->get('notification_blogsubscriber')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_CATEGORIES'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_CATEGORIES'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_CATEGORIES_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'notification_categorysubscriber', $this->config->get('notification_categorysubscriber')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_SITE_SUBSCRIBERS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_SITE_SUBSCRIBERS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_SITE_SUBSCRIBERS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'notification_sitesubscriber', $this->config->get('notification_sitesubscriber')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_TEAM_SUBSCRIBERS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_TEAM_SUBSCRIBERS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_TEAM_SUBSCRIBERS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'notification_teamsubscriber', $this->config->get('notification_teamsubscriber')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_BLOG_SETTINGS');?></b>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_BLOG_TRUNCATE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_BLOG_TRUNCATE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_BLOG_TRUNCATE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'notification_blog_truncate', $this->config->get('notification_blog_truncate')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_BLOG_TRUNCATE_LIMIT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_BLOG_TRUNCATE_LIMIT'); ?>"
                           data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_BLOG_TRUNCATE_LIMIT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>
                    <div class="col-md-7">                      
                        <input type="text" class="form-control" name="notification_blog_truncate_limit" value="<?php echo $this->config->get('notification_blog_truncate_limit', 300);?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>