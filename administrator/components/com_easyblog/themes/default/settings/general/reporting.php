<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REPORTING');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REPORTING_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'main_reporting', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_REPORTING'); ?>

                <?php echo $this->html('settings.toggle', 'main_reporting_guests', 'COM_EASYBLOG_REPORTS_ALLOW_GUEST_TO_REPORT'); ?>

                <?php echo $this->html('settings.smalltext', 'main_reporting_maxip', 'COM_EASYBLOG_REPORTS_MAX_REPORTS_PER_IP', '', 'COM_EASYBLOG_REPORTS_REPORTS'); ?>
            </div>
        </div>
    </div>
</div>