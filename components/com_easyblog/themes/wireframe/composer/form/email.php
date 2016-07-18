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
<div class="eb-composer-field row">
    <label
        for="send_notification_emails"
        class="eb-composer-field-label col-sm-5"
        data-eb-provide="popover"
        data-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_NOTIFY_SUBSCRIBERS'); ?>"
        data-content="<?php echo JText::_('COM_EASYBLOG_COMPOSER_NOTIFY_SUBSCRIBERS_HELP'); ?>"
        data-placement="left"
        data-html="true">
        <?php echo JText::_('COM_EASYBLOG_COMPOSER_NOTIFY_SUBSCRIBERS'); ?>
    </label>
    <div class="eb-composer-field-content col-sm-7">
        <?php echo $this->html('grid.boolean', 'send_notification_emails', $post->send_notification_emails); ?>
    </div>
</div>
