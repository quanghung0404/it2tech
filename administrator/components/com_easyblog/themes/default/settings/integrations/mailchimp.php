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
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SUBSCRIPTIONS_MAILCHIMP_INTEGRATIONS');?></b>
                <div class="panel-info">
                    <?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_INFO' ); ?><br><br>
                    <a class="btn btn-primary" target="_blank" href="http://eepurl.com/ori65"><?php echo JText::_( 'COM_EASYBLOG_SIGNUP_WITH_MAILCHIMP' );?></a>
                </div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'subscription_mailchimp', 'COM_EASYBLOG_MAILCHIMP_ENABLE'); ?>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_MAILCHIMP_APIKEY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_MAILCHIMP_APIKEY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_MAILCHIMP_APIKEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                            <input type="text" name="subscription_mailchimp_key" class="form-control " value="<?php echo $this->config->get('subscription_mailchimp_key');?>" size="5" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_MAILCHIMP_LISTID'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_MAILCHIMP_LISTID'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_MAILCHIMP_LISTID_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                            <input type="text" name="subscription_mailchimp_listid" class="form-control " value="<?php echo $this->config->get('subscription_mailchimp_listid');?>" size="5" />
                    </div>
                </div>

                <?php echo $this->html('settings.toggle', 'subscription_mailchimp_welcome', 'COM_EASYBLOG_MAILCHIMP_SEND_WELCOME_EMAIL'); ?>

                <?php echo $this->html('settings.toggle', 'mailchimp_campaign', 'COM_EASYBLOG_MAILCHIMP_SEND_NOTIFICATION'); ?>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_MAILCHIMP_SENDER_NAME'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_MAILCHIMP_SENDER_NAME'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_MAILCHIMP_SENDER_NAME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                                <input type="text" name="mailchimp_from_name" class="form-control " value="<?php echo $this->config->get('mailchimp_from_name');?>" size="5" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_MAILCHIMP_SENDER_EMAIL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_MAILCHIMP_SENDER_EMAIL'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_MAILCHIMP_SENDER_EMAIL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                                <input type="text" name="mailchimp_from_email" class="form-control " value="<?php echo $this->config->get('mailchimp_from_email');?>" size="5" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
