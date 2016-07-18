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
<div id="fd" class="es mod-es-events module-register<?php echo $suffix;?> es-responsive">

    <ul class="es-events-list fd-reset-list">
        <?php foreach ($events as $event) { ?>

        <li>
            <?php if ($params->get('display_avatar' , true)) { ?>
            <div class="es-event-avatar es-avatar es-avatar-sm es-avatar-border-sm">
                <img src="<?php echo $event->getAvatar();?>" alt="<?php echo $modules->html('string.escape' , $event->getName());?>" />
            </div>
            <?php } ?>

            <div class="es-event-object">
                <a href="<?php echo $event->getPermalink();?>" class="event-title"><?php echo $event->getName();?></a>
            </div>

            <div class="es-event-meta">
                <?php echo $event->getStartEndDisplay(array('end' => false)); ?>
            </div>

            <div class="es-event-meta">
                <?php if ($params->get('display_category' , true)) { ?>
                <span>
                    <a href="<?php echo FRoute::events(array('layout' => 'category' , 'id' => $event->getCategory()->getAlias()));?>" alt="<?php echo $modules->html('string.escape' , $event->getCategory()->get('title'));?>" class="event-category">
                        <i class="fa fa-database"></i> <?php echo $modules->html('string.escape' , $event->getCategory()->get('title'));?>
                    </a>
                </span>
                <?php } ?>

                <?php if ($params->get('display_member_counter', true)) { ?>
                <span class="hit-counter">
                    <i class="fa fa-users"></i> <?php echo JText::sprintf(FD::string()->computeNoun('MOD_EASYSOCIAL_EVENTS_GUEST_COUNT' , $event->getTotalGuests()) , $event->getTotalGuests()); ?>
                </span>
                <?php } ?>
            </div>

            <?php if ($params->get('display_rsvp', true)) { ?>
                <?php echo $event->showRsvpButton(); ?>
            <?php } ?>

        </li>
        <?php } ?>
    </ul>

    <div class="fd-small">
        <a href="<?php echo FRoute::events(); ?>"><?php echo JText::_('MOD_EASYSOCIAL_EVENTS_ALL_EVENT'); ?></a>
    </div>
</div>
