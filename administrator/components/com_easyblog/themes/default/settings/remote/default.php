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
    <div class="row">
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-head">
                    <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_REMOTE_PUBLISHING_HEADING');?></b>
                    <div class="panel-info"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_REMOTE_PUBLISHING_INFO' ); ?></div>
                </div>

                <div class="panel-body">
                    <?php echo $this->html('settings.toggle', 'main_remotepublishing_xmlrpc', 'COM_EASYBLOG_SETTINGS_REMOTE_PUBLISHING_ENABLE'); ?>
                    <?php echo $this->html('settings.toggle', 'main_remotepublishing_xmlrpc_blogimage', 'COM_EASYBLOG_SETTINGS_REMOTE_PUBLISHING_BLOG_IMAGE'); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="page" value="remote" />
    <input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-settings-active />
</form>
