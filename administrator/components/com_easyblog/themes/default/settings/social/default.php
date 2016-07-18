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
        <ul id="userForm" class="app-tabs-list list-unstyled">
            <li class="tabItem active">
                <a data-bp-toggle="tab" href="#general" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_GENERAL');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#twitter" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_TWITTER');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#facebook" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_FACEBOOK');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#google" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_GOOGLE');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#xing" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_XING');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#linkedin" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_LINKEDIN');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#vk" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_VK');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#stumble" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_STUMBLEUPON');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#pinterest" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_PINTEREST');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#reddit" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_REDDIT');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#pocket" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_POCKET');?>
                </a>
            </li>


            <li class="tabItem">
                <a data-bp-toggle="tab" href="#addthis" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_ADDTHIS');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#sharethis" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_SHARETHIS');?>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div id="general" class="tab-pane active in">
            <?php echo $this->output('admin/settings/social/general'); ?>
        </div>

        <div id="twitter" class="tab-pane">
            <?php echo $this->output('admin/settings/social/twitter'); ?>
        </div>

        <div id="facebook" class="tab-pane">
            <?php echo $this->output('admin/settings/social/facebook'); ?>
        </div>

        <div id="google" class="tab-pane">
            <?php echo $this->output('admin/settings/social/google'); ?>
        </div>

        <div id="xing" class="tab-pane">
            <?php echo $this->output('admin/settings/social/xing'); ?>
        </div>

        <div id="linkedin" class="tab-pane">
            <?php echo $this->output('admin/settings/social/linkedin'); ?>
        </div>

        <div id="reddit" class="tab-pane">
            <?php echo $this->output('admin/settings/social/reddit'); ?>
        </div>


        <div id="vk" class="tab-pane">
            <?php echo $this->output('admin/settings/social/vk'); ?>
        </div>

        <div id="stumble" class="tab-pane">
            <?php echo $this->output('admin/settings/social/stumble'); ?>
        </div>

         <div id="pinterest" class="tab-pane">
            <?php echo $this->output('admin/settings/social/pinterest'); ?>
        </div>

        <div id="line" class="tab-pane">
            <?php echo $this->output('admin/settings/social/line'); ?>
        </div>

        <div id="pocket" class="tab-pane">
            <?php echo $this->output('admin/settings/social/pocket'); ?>
        </div>

        <div id="addthis" class="tab-pane">
            <?php echo $this->output('admin/settings/social/addthis'); ?>
        </div>

        <div id="sharethis" class="tab-pane">
            <?php echo $this->output('admin/settings/social/sharethis'); ?>
        </div>
    </div>

    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="page" value="social" />
    <input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-settings-active />    
</form>
