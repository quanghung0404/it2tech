<?php
/**
* @package        EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="app-members" data-event-guests data-id="<?php echo $event->id; ?>">

    <div class="es-filterbar row-table">
        <div class="col-cell filterbar-title"><?php echo JText::_('APP_EVENT_GUESTS_SUBTITLE'); ?></div>
    </div>

    <div class="app-contents-wrap">
        <ul class="fd-nav es-filter-nav">
            <li>
                <a <?php if ($type == 'admin') { ?>class="active"<?php } ?> href="<?php echo $permalinks['admin']; ?>" data-event-guests-filter data-filter="admin"><?php echo JText::_('APP_EVENT_GUESTS_FILTER_ADMINS'); ?></a>
            </li>
            <li>
                <a <?php if ($type == 'going') { ?>class="active"<?php } ?> href="<?php echo $permalinks['going']; ?>" data-event-guests-filter data-filter="going"><?php echo JText::_('APP_EVENT_GUESTS_FILTER_GOING'); ?></a>
            </li>
            <?php if ($event->getParams()->get('allowmaybe', true)) { ?>
            <li>
                <a <?php if ($type == 'maybe') { ?>class="active"<?php } ?> href="<?php echo $permalinks['maybe']; ?>" data-event-guests-filter data-filter="maybe"><?php echo JText::_('APP_EVENT_GUESTS_FILTER_MAYBE'); ?></a>
            </li>
            <?php } ?>
            <?php if ($event->getParams()->get('allownotgoingguest', true)) { ?>
            <li>
                <a <?php if ($type == 'notgoing') { ?>class="active"<?php } ?> href="<?php echo $permalinks['notgoing']; ?>" data-event-guests-filter data-filter="notgoing"><?php echo JText::_('APP_EVENT_GUESTS_FILTER_NOTGOING'); ?></a>
            </li>
            <?php } ?>
            <?php if ($event->isClosed()) { ?>
            <li>
                <a <?php if ($type == 'pending') { ?>class="active"<?php } ?> href="<?php echo $permalinks['pending']; ?>" data-event-guests-filter data-filter="pending"><?php echo JText::_('APP_EVENT_GUESTS_FILTER_PENDING'); ?></a>
            </li>
            <?php } ?>
        </ul>

        <div class="app-members-content app-contents <?php if (empty($guests)) { ?>is-empty<?php } ?>" data-event-guests-content>
            <?php echo $this->includeTemplate('apps/event/guests/events/default.list'); ?>
        </div>
    </div>
</div>
