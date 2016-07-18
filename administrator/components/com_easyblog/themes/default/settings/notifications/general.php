<?php
/**
* @package		EasyBlog
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
<div class="row">
	<div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_SETTINGS_TITLE');?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_EXAMPLE');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<input type="text" class="form-control" name="notification_email" value="<?php echo $this->config->get('notification_email');?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_USE_CUSTOM_EMAILS_AS_ADMIN'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_USE_CUSTOM_EMAILS_AS_ADMIN'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_USE_CUSTOM_EMAILS_AS_ADMIN_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'custom_email_as_admin', $this->config->get('custom_email_as_admin')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_MAILSPOOL_SENDMAIL_HTML'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_MAILSPOOL_SENDMAIL_HTML'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_MAILSPOOL_SENDMAIL_HTML_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_mailqueuehtmlformat', $this->config->get('main_mailqueuehtmlformat')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TITLE_LENGTH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TITLE_LENGTH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TITLE_LENGTH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7 form-inline">
                        <div class="form-inline">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" name="main_mailtitle_length" class="form-control text-center" value="<?php echo $this->config->get('main_mailtitle_length');?>" style="text-align:center;" size="5" />
                                    <span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_CHARACTERS' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>

	<div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_SENDER_SETTINGS');?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_SEND_FROM_NAME'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_SEND_FROM_NAME'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_SEND_FROM_NAME_INFO');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" class="form-control" name="notification_from_name" value="<?php echo $this->config->get('notification_from_name' , $this->jconfig->get( 'fromname' ) );?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_SEND_FROM_EMAIL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_SEND_FROM_EMAIL'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_SEND_FROM_EMAIL_INFO');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" class="form-control" name="notification_from_email" value="<?php echo $this->config->get('notification_from_email' , $this->jconfig->get( 'mailfrom' ) );?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_TITLE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_TITLE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_TITLE_INFO');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" class="form-control" name="notifications_title" value="<?php echo $this->config->get('notifications_title' , $this->jconfig->get('sitename'));?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_PROCESSING_TITLE');?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_MAILSPOOL_SENDMAIL_ON_PAGE_LOAD'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_MAILSPOOL_SENDMAIL_ON_PAGE_LOAD'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_MAILSPOOL_SENDMAIL_ON_PAGE_LOAD_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_mailqueueonpageload', $this->config->get('main_mailqueueonpageload')); ?>
                        &nbsp;&nbsp;
                        <a href="http://stackideas.com/docs/easyblog/administrators/cronjobs" target="_blank" class="btn btn-default">
                            <?php echo JText::_('COM_EASYBLOG_SETTINGS_HELP_CRON'); ?>
                        </a>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_TOTAL_EMAILS_AT_A_TIME'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_TOTAL_EMAILS_AT_A_TIME'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_TOTAL_EMAILS_AT_A_TIME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="form-inline">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" name="main_mail_total" class="form-control text-center" value="<?php echo $this->config->get( 'main_mail_total' );?>" />
                                    <span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_EMAILS' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>