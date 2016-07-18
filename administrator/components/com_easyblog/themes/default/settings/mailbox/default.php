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
defined('_JEXEC') or die('Unauthorized Access');
?>
<form method="post" action="<?php echo JRoute::_('index.php');?>" id="adminForm">
    <div class="app-tabs">
        <ul class="app-tabs-list list-unstyled">
            <li class="tabItem active">
                <a data-bp-toggle="tab" href="#general" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_TAB_GENERAL');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#publishing" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_TAB_PUBLISHING_OPTIONS');?>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div id="general" class="tab-pane active in">
            <?php echo $this->output('admin/settings/mailbox/general'); ?>
        </div>

        <div id="publishing" class="tab-pane">
            <?php echo $this->output('admin/settings/mailbox/publishing'); ?>
        </div>
    </div>
    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="page" value="mailbox" />
    <input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-settings-active />
</form>
