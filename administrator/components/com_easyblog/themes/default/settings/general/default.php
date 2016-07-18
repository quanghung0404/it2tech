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
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_GENERAL');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#ratings" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_RATINGS');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#rss" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_RSS');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#subscriptions" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_SUBSCRIPTIONS');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#location" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_LOCATION');?>
                </a>
            </li>
            <li class="tabItem">
                <a data-bp-toggle="tab" href="#antispam" data-form-tabs>
                <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_ANTISPAM');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#composer" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_COMPOSER');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#reporting" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_REPORTING');?>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div id="settings" class="tab-pane active in">
            <?php echo $this->output('admin/settings/general/general'); ?>
        </div>

        <div id="ratings" class="tab-pane">
            <?php echo $this->output('admin/settings/general/ratings'); ?>
        </div>

        <div id="rss" class="tab-pane">
            <?php echo $this->output('admin/settings/general/rss'); ?>
        </div>

        <div id="subscriptions" class="tab-pane">
            <?php echo $this->output('admin/settings/general/subscriptions'); ?>
        </div>

        <div id="location" class="tab-pane">
            <?php echo $this->output('admin/settings/general/location'); ?>
        </div>

        <div id="moderation" class="tab-pane">
            <?php echo $this->output('admin/settings/comments/moderation'); ?>
        </div>

        <div id="antispam" class="tab-pane">
            <?php echo $this->output('admin/settings/general/antispam'); ?>
        </div>

        <div id="composer" class="tab-pane">
            <?php echo $this->output('admin/settings/general/composer'); ?>
        </div>

        <div id="reporting" class="tab-pane">
            <?php echo $this->output('admin/settings/general/reporting'); ?>
        </div>
    </div>

    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="page" value="general" />
    <input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-settings-active />
</form>