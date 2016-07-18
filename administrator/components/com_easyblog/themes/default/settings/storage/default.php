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
                <a data-bp-toggle="tab" href="#s3" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_REMOTE_STORAGE_AMAZON_S3');?>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div id="s3" class="tab-pane active in">
            <?php echo $this->output('admin/settings/storage/s3'); ?>
        </div>
    </div>

    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="page" value="storage" />
    <input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-settings-active />
</form>