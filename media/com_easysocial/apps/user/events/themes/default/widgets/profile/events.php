<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="es-widget">
    <div class="es-widget-head">
        <div class="pull-left widget-title">
            <?php echo JText::_('APP_USER_EVENTS_WIDGET_EVENTS'); ?>
        </div>
        <span class="widget-label">(<?php echo $total; ?>)</span>

        <?php if ($this->my->id == $user->id) { ?>
        <div class="pull-right fd-small">
            <a href="<?php echo FRoute::events(array('layout' => 'create'));?>">
                <i class="icon-es-add"></i><?php echo JText::_('COM_EASYSOCIAL_NEW_EVENT');?>
            </a>
        </div>
        <?php } ?>
    </div>
    <div class="es-widget-body">
        <?php if ($total > 0) { ?>
        <ul class="fd-nav es-widget-tab" role="tablist">
            <li class="active">
                <a href="#es-updated-events-attending" role="tab" data-toggle="tab">
                    <span class="widget-label"><?php echo JText::_('APP_USER_EVENTS_WIDGET_ATTENDING_EVENTS'); ?></span>
                </a>
            </li>
            <?php if (!empty($createdTotal) && $allowCreate) { ?>
            <li>
                <a href="#es-updated-events-default" role="tab" data-toggle="tab">
                    <span class="widget-label"><?php echo JText::_('APP_USER_EVENTS_WIDGET_CREATED_EVENTS'); ?></span>
                </a>
            </li>
            <?php } ?>
        </ul>
        <?php } ?>

        <div class="tab-content">

            <div class="tab-pane active" id="es-updated-events-attending">
                <?php if (!empty($attendingEvents)) { ?>
                <ul class="widget-list">
                    <?php foreach ($attendingEvents as $attendingEvent) {?>
                    <li class="mb-10">
                        <a href="<?php echo $attendingEvent->getPermalink();?>"><?php echo $attendingEvent->getName(); ?></a>
                        <div class="es-muted"><i class="fa fa-calendar"></i> <?php echo $attendingEvent->getStartEndDisplay(array('end' => false)); ?></div>
                        <?php if ($this->my->id != $user->id) { ?>
                            <?php echo $attendingEvent->showRsvpButton(); ?>
                        <?php } ?>
                    </li>
                    <?php } ?>
                </ul>
                <?php } else { ?>
                <div class="fd-small"><?php echo JText::_('APP_USER_EVENTS_WIDGET_NO_EVENTS'); ?></div>
                <?php } ?>
            </div>

            <?php if (!empty($createdTotal) && $allowCreate) { ?>
            <div class="tab-pane" id="es-updated-events-default">
                <?php if (!empty($createdEvents)) { ?>
                <ul class="widget-list">
                    <?php foreach ($createdEvents as $createdEvent) {?>
                    <li class="mb-10">
                        <a href="<?php echo $createdEvent->getPermalink();?>"><?php echo $createdEvent->getName(); ?></a>
                        <div class="es-muted"><i class="fa fa-calendar"></i> <?php echo $createdEvent->getStartEndDisplay(array('end' => false)); ?></div>
                        <?php if ($this->my->id != $user->id) { ?>
                            <?php echo $createdEvent->showRsvpButton(); ?>
                        <?php } ?>
                    </li>
                    <?php } ?>
                </ul>
                <?php } else { ?>
                <div class="fd-small"><?php echo JText::_('APP_USER_EVENTS_WIDGET_NO_EVENTS'); ?></div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>

    </div>
</div>
