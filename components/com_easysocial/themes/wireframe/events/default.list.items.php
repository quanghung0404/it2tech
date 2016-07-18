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
<?php if ($events){ ?>
<ul class="fd-reset-list" data-es-events-list>
    <?php foreach($events as $event){ ?>
    <li
        class="mb-10"
        data-id="<?php echo $event->id;?>"
        data-events-item
        data-events-item-id="<?php echo $event->id;?>"
        data-events-item-type="<?php echo $event->isOpen() ? 'open' : 'closed';?>"
    >
        <?php echo $this->loadTemplate('site/events/default.item', array('event' => $event, 'guest' => $event->getGuest($this->my->id), 'owner' => $event->getOwner(), 'guestApp' => $guestApp, 'showDistance' => $showDistance, 'isGroupOwner' => isset($isGroupOwner) ? $isGroupOwner : false)); ?>
    </li>
    <?php } ?>
</ul>

<div class="text-center">
    <div class="list-pagination">
        <?php echo $pagination->getListFooter('site'); ?>
    </div>
</div>

<?php } else { ?>
    <?php if ($filter == 'featured'){ ?>
    <div class="empty empty-hero">
        <i class="fa fa-calendar mb-10"></i>
        <div><?php echo JText::_('COM_EASYSOCIAL_EVENTS_NO_FEATURED_EVENTS_FOUND');?></div>
    </div>
    <?php } ?>

    <?php if ($filter == 'invited'){ ?>
    <div class="empty empty-hero">
        <i class="fa fa-calendar mb-10"></i>
        <div><?php echo JText::_('COM_EASYSOCIAL_EVENTS_NO_INVITED_EVENTS_FOUND');?></div>
    </div>
    <?php } ?>

    <?php if ($filter != 'featured' && $filter != 'invited' && empty($featuredEvents)) { ?>
    <div class="empty empty-hero">
        <i class="fa fa-calendar"></i>
        <div><?php echo JText::_('COM_EASYSOCIAL_EVENTS_NO_EVENTS_FOUND'); ?></div>
    </div>
    <?php } ?>

    <?php if ($delayed && $filter === 'nearby') { ?>
    <div class="es-detecting-location">
        <i class="fa fa-globe es-muted"></i>
        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_DETECTING_LOCATION'); ?>
        <i class="icon-loader"></i>
    </div>
    <?php } ?>
<?php }
