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
<form method="post" action="<?php echo JRoute::_('index.php');?>" id="adminForm">
    <div class="app-tabs">
        <ul class="app-tabs-list list-unstyled">
            <li class="tabItem active">
                <a data-bp-toggle="tab" href="#general" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_SUBTAB_GENERAL');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#mediamanager" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_SUBTAB_MEDIAMANAGER');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#videos" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_SUBTAB_VIDEOS');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#flickr" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_FLICKR');?>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div id="general" class="tab-pane active in">
            <?php echo $this->output('admin/settings/media/general'); ?>
        </div>

        <div id="mediamanager" class="tab-pane">
            <?php echo $this->output('admin/settings/media/mediamanager'); ?>
        </div>

        <div id="videos" class="tab-pane">
            <?php echo $this->output('admin/settings/media/videos'); ?>
        </div>

        <div id="flickr" class="tab-pane">
            <?php echo $this->output('admin/settings/media/flickr'); ?>
        </div>
    </div>

    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="page" value="general" />
    <input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-settings-active />    
</form>