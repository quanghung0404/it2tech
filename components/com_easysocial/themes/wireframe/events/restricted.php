<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-events-item" data-event-item data-id="<?php echo $event->id;?>">

    <?php echo $this->loadTemplate('site/events/item.header', array('event' => $event, 'guest' => $guest)); ?>

    <div class="well mt-20">
        <div class="well-title">
            <i class="icon-es-aircon-locked"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_CLOSED_EVENT_INFO'); ?>
        </div>

        <p class="well-text">
            <?php echo JText::_('COM_EASYSOCIAL_EVENTS_CLOSED_EVENT_INFO_DESC'); ?>
        </p>
    </div>

</div>
