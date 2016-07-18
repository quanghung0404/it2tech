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
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_GENERAL_TITLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_GENERAL_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_TITLE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_TITLE'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_TITLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" name="main_title" class="form-control" value="<?php echo $this->escape($this->config->get('main_title'));?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_DESCRIPTION'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_DESCRIPTION'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_DESCRIPTION_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <textarea name="main_description" rows="5" class="form-control" cols="35"><?php echo $this->config->get('main_description');?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEO_AUTOMATIC_APPEND_BLOG_TITLE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEO_AUTOMATIC_APPEND_BLOG_TITLE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEO_AUTOMATIC_APPEND_BLOG_TITLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_pagetitle_autoappend', $this->config->get('main_pagetitle_autoappend')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEO_AUTOMATIC_APPEND_BLOG_TITLE_ENTRY_VIEW'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEO_AUTOMATIC_APPEND_BLOG_TITLE_ENTRY_VIEW'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEO_AUTOMATIC_APPEND_BLOG_TITLE_ENTRY_VIEW_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_pagetitle_autoappend_entry', $this->config->get('main_pagetitle_autoappend_entry')); ?>
                    </div>
                </div>                

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REQUIRE_LOGIN_TO_READ_FULL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REQUIRE_LOGIN_TO_READ_FULL'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REQUIRE_LOGIN_TO_READ_FULL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_login_read', $this->config->get('main_login_read'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_COPYRIGHTS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_COPYRIGHTS'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_COPYRIGHTS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_copyrights', $this->config->get('main_copyrights')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_PASSWORD_PROTECTION'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_PASSWORD_PROTECTION'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_PASSWORD_PROTECTION_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_password_protect', $this->config->get('main_password_protect')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MICROBLOG_ENABLE_MICROBLOG'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MICROBLOG_ENABLE_MICROBLOG'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MICROBLOG_ENABLE_MICROBLOG_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_microblog', $this->config->get('main_microblog'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_GENERAL_ENABLE_MULTI_LANGUAGE_POSTS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_GENERAL_ENABLE_MULTI_LANGUAGE_POSTS'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_GENERAL_ENABLE_MULTI_LANGUAGE_POSTS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_multi_language', $this->config->get('main_multi_language')); ?>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_CALENDAR');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_CALENDAR_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_CALENDAR_START_OF_WEEK'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_CALENDAR_START_OF_WEEK'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_CALENDAR_START_OF_WEEK_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <select name="main_start_of_week" class="form-control">
                            <option value="monday"<?php echo $this->config->get('main_start_of_week') == 'monday' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_CALENDAR_MONDAY'); ?></option>
                            <option value="sunday"<?php echo $this->config->get('main_start_of_week') == 'sunday' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_CALENDAR_SUNDAY'); ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_HITS');?></b>
                <div class="panel-info"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_HITS_DESC' ); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_SESSION_TRACKING_HITS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_SESSION_TRACKING_HITS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_SESSION_TRACKING_HITS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_hits_session', $this->config->get('main_hits_session')); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_OPTIONS'); ?></b>
                <div class="panel-info"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_INFO' );?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_NEW_ENTRY_ON_FRONTPAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_NEW_ENTRY_ON_FRONTPAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_NEW_ENTRY_ON_FRONTPAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_newblogonfrontpage', $this->config->get('main_newblogonfrontpage')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_ALLOW_COMMENT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_ALLOW_COMMENT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_ALLOW_COMMENT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_defaultallowcomment', $this->config->get('main_defaultallowcomment')); ?>
                    </div>
                </div>                

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_BLOG_PRIVACY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_BLOG_PRIVACY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_BLOG_PRIVACY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php
                            $nameFormat = EasyBlogHelper::getHelper('privacy')->getOptions();
                            $showdet = JHTML::_('select.genericlist', $nameFormat, 'main_blogprivacy', 'class="form-control"', 'value', 'text', $this->config->get('main_blogprivacy' ) );
                            echo $showdet;
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_SEND_NOTIFICATION_EMAILS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_SEND_NOTIFICATION_EMAILS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_SEND_NOTIFICATION_EMAILS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_sendemailnotifications', $this->config->get('main_sendemailnotifications')); ?>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>