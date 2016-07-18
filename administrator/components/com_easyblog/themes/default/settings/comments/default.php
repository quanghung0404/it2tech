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
                <a data-bp-toggle="tab" href="#settings" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SUBTAB_GENERAL');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#antispam" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SUBTAB_ANTISPAM');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#moderation" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SUBTAB_MODERATION');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#integrations" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SUBTAB_INTEGRATIONS');?>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div id="settings" class="tab-pane active in">
            <?php echo $this->output('admin/settings/comments/general'); ?>
        </div>

        <div id="antispam" class="tab-pane">
            <?php echo $this->output('admin/settings/comments/antispam'); ?>
        </div>

        <div id="moderation" class="tab-pane">
            <?php echo $this->output('admin/settings/comments/moderation'); ?>
        </div>

        <div id="integrations" class="tab-pane">
            <?php echo $this->output('admin/settings/comments/integrations'); ?>
        </div>
    </div>
    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="page" value="comments" />
    <input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-settings-active />
</form>
