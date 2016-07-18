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
        for="subscription"
        class="eb-composer-field-label col-sm-5"
        data-eb-provide="popover"
        data-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_ALLOW_SUBSCRIPTION'); ?>"
        data-content="<?php echo JText::_('COM_EASYBLOG_COMPOSER_ALLOW_SUBSCRIPTION_HELP'); ?>"
        data-placement="left"
        data-html="true">
        <?php echo JText::_('COM_EASYBLOG_COMPOSER_ALLOW_SUBSCRIPTION'); ?>
    </label>
    <div class="eb-composer-field-content col-sm-7">
        <?php echo $this->html('grid.boolean', 'subscription', $post->subscription); ?>
    </div>
</div>