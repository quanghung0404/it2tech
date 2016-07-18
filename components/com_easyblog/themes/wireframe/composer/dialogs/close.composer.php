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
<dialog>
    <width>400</width>
    <height>120</height>
    <selectors type="json">
    {
        "{cancelButton}" : "[data-cancel-button]",
        "{closeButton}": "[data-close-button]"
    }
    </selectors>
    <title><?php echo JText::_('COM_EASYBLOG_COMPOSER_CLOSE_CONFIRMATION'); ?></title>
    <content>
        <?php echo JText::_('COM_EASYBLOG_COMPOSER_CLOSE_CONFIRMATION_MESSAGE'); ?>
    </content>
    <buttons>
        <button data-cancel-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></button>
        <button data-close-button type="button" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYBLOG_CLOSE_BUTTON'); ?></button>
    </buttons>
</dialog>
