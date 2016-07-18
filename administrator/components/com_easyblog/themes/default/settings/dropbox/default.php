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
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_DROPBOX_APPLICATION_SETTINGS');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_DROPBOX_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'dropbox_default', 'COM_EASYBLOG_DROPBOX_USE_DEFAULT_APP'); ?>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_DROPBOX_APP_ID'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_DROPBOX_APP_ID'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_DROPBOX_APP_ID_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                            <input type="text" name="subscription_mailchimp_key" class="form-control " value="<?php echo $this->config->get('subscription_mailchimp_key');?>" size="5" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_DROPBOX_APP_SECRET'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_DROPBOX_APP_SECRET'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_DROPBOX_APP_SECRET_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                            <input type="text" name="subscription_mailchimp_listid" class="form-control " value="<?php echo $this->config->get('subscription_mailchimp_listid');?>" size="5" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $this->html('form.action'); ?>
<input type="hidden" name="page" value="dropbox" />
<input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-settings-active />
</form>
