<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="popbox-holiday-wrap">
    <i class="popbox-holiday-wrap__icon fa  fa-moon-o"></i>

    <div class="popbox-holiday-wrap__title"><?php echo JText::_('COM_EASYDISCUSS_WORK_SUPPORT_OFFLINE'); ?></div>
    <div class="popbox-holiday-wrap__subtitle">
        <?php echo JText::_('COM_EASYDISCUSS_WORK_SUPPORT_OFFLINE_DESC'); ?>
    </div>

    <div class="popbox-holiday-wrap__note t-lg-mt--md">
        <div class="popbox-holiday-wrap__note-title"><?php echo JText::_('COM_EASYDISCUSS_WORK_OFFICIAL_WORKING_HOURS'); ?></div>
        <div class="popbox-holiday-wrap__note-time">
            <?php echo $workDayLabel; ?> <?php echo ($workExceptionLabel) ? $workExceptionLabel : ''; ?><?php echo !$isEverydayWork ? '<br />' : ' '; ?>
            <?php echo $workTimeLabel; ?>
        </div>
    </div>
</div>
