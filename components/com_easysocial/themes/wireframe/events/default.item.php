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
<div class="es-profile-header ed-profile-header--event-list-item">

    <?php if ($this->my->isSiteAdmin() || $guest->isOwner() || $guest->isAdmin()) { ?>
    <div class="es-profile-header__options btn-group pull-right">
        <a class="dropdown-toggle_ loginLink btn btn-es btn-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);">
            <i class="icon-es-dropdown"></i>
        </a>

        <ul class="dropdown-menu dropdown-menu-user messageDropDown">
            <?php if ($this->my->isSiteAdmin()) { ?>
                <?php if ($event->isFeatured()) { ?>
                <li>
                    <a href="javascript:void(0);" data-item-action="unfeature" data-item-unfeature><?php echo JText::_('COM_EASYSOCIAL_EVENTS_REMOVE_FEATURED'); ?></a>
                </li>
                <?php } else { ?>
                <li>
                    <a href="javascript:void(0);"  data-item-action="feature" data-item-feature><?php echo JText::_('COM_EASYSOCIAL_EVENTS_SET_FEATURED'); ?></a>
                </li>
                <?php } ?>
            <?php } ?>
            <li>
                <a href="<?php echo FRoute::events(array('layout' => 'edit', 'id' => $event->getAlias())); ?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_EDIT_EVENT'); ?></a>
            </li>
            <?php if ($this->my->isSiteAdmin()) { ?>
            <li class="divider"></li>
            <li>
                <a href="javascript:void(0);"  data-item-action="unpublish" data-item-unpublish><?php echo JText::_('COM_EASYSOCIAL_EVENTS_UNPUBLISH_EVENT'); ?></a>
            </li>
            <li>
                <a href="javascript:void(0);" data-item-delete><?php echo JText::_('COM_EASYSOCIAL_EVENTS_DELETE_EVENT'); ?></a>
            </li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>

    <div class="es-profile-header-heading  with-cover">

        <div data-event-cover="" class="es-profile-header-cover" style="background-image: url('<?php echo $event->getCover(); ?>');background-position: <?php echo $event->getCoverPosition(); ?>;">
            <div class="es-cover-container">
                <div class="es-cover-viewport">
                </div>
            </div>
        </div>

        <div class="es-profile-header-calendar">
            <div class="es-profile-header-calendar__wrap">
                <div class="es-profile-header-calendar__date-mth"><?php echo $event->getEventStart()->format('M', true); ?></div>
                <div class="es-profile-header-calendar__date-day"><?php echo $event->getEventStart()->format('d', true); ?></div>
            </div>

        </div>

    </div>

    <div class="es-profile-header-body fd-cf">

        <div>

            <div class="media-body">
                <!-- Not sure if want to use this label or an overlay on the avatar -->
                <?php if ($event->isFeatured()) { ?>
                    <div class="label label-events-featured label-warning mb-5"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FEATURED_EVENT');?></div>
                <?php } ?>

                <div class="media-name">
                    <a class="es-profile-header-title" href="<?php echo $event->getPermalink(); ?>"><?php echo $event->getName(); ?></a>

                    <?php if (empty($isGroupOwner) && $event->isGroupEvent()) { ?>
                        <span class="label"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_GROUP_EVENT'); ?></span>
                    <?php } ?>

                    <?php if ($event->isRecurringEvent()) { ?>
                        <span class="label label-warning"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_RECURRING_EVENT'); ?></span>
                    <?php } ?>

                    <?php if ($guest->isOwner() || $guest->isAdmin()) { ?>
                        <?php if ($guest->isOwner()) { ?>
                        <span class="label label-info"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUEST_OWNER'); ?></span>
                        <?php } ?>
                        <?php if (!$guest->isOwner() && $guest->isAdmin()) { ?>
                        <span class="label label-info"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_GUEST_ADMIN'); ?></span>
                        <?php } ?>
                    <?php } ?>
                </div>

                <div class="media-meta mt-5 muted">
                    <div>
                        <span>
                            <i class="fa fa-calendar"></i>
                            <?php echo $event->getStartEndDisplay(); ?>
                        </span>
                        <?php if (!$guest->isOwner()) { ?>
                        &middot;
                        <span>
                            <i class="fa fa-user"></i>
                            <?php echo $this->html('html.user', $owner, true); ?>
                        </span>
                        <?php } ?>
                        <?php if (empty($isGroupOwner) && $event->isGroupEvent()) { ?>
                        &middot;
                        <span>
                            <i class="fa fa-users"></i>
                            <?php echo $this->html('html.group', $event->getMeta('group_id'), true); ?>
                        </span>
                        <?php } ?>
                    </div>

                    <?php if ($this->template->get('events_address', true) && !empty($event->address)) { ?>
                    <div>
                        <i class="fa fa-map-marker"></i>
                        <a href="<?php echo $event->getAddressLink(); ?>" target="_blank"><?php echo $event->address; ?></a>
                    </div>
                    <?php } ?>

                    <?php if (!empty($showDistance)) { ?>
                    <div>
                        <i class="fa fa-compass"></i>
                        <?php echo JText::sprintf('COM_EASYSOCIAL_EVENTS_EVENT_DISTANCE_AWAY', $event->distance, $this->config->get('general.location.proximity.unit', 'mile')); ?>
                    </div>
                    <?php } ?>

                    <?php if ($this->template->get('events_seatsleft', true) && $event->seatsLeft() >= 0) { ?>
                    <div class="btn btn-es btn-xs"><?php echo JText::sprintf('COM_EASYSOCIAL_EVENTS_SEATS_LEFT', $event->seatsLeft()); ?></div>
                    <?php } ?>
                </div>

                <?php if ($this->template->get('events_description', true)) { ?>
                <div class="media-brief mt-10">
                    <?php if (!empty($event->description)) { ?>
                        <?php echo $this->html('string.truncate', nl2br(strip_tags($event->description)), 350); ?>
                    <?php } ?>
                </div>
                <?php } ?>


                <nav class="pull- mb-10">
                    <?php if (! $event->isGroupEvent()) { ?>
                        <?php if ($event->isOpen()) { ?>
                        <span data-original-title="<?php echo FD::_('COM_EASYSOCIAL_EVENTS_OPEN_EVENT_TOOLTIP', true); ?>" data-es-provide="tooltip" data-placement="bottom">
                            <i class="fa fa-globe"></i>
                            <?php echo JText::_('COM_EASYSOCIAL_EVENTS_OPEN_EVENT'); ?>
                        </span>
                        <?php } ?>

                        <?php if ($event->isPrivate()) { ?>
                        <span data-original-title="<?php echo FD::_('COM_EASYSOCIAL_EVENTS_PRIVATE_EVENT_TOOLTIP', true); ?>" data-es-provide="tooltip" data-placement="bottom">
                            <i class="fa fa-lock"></i>
                            <?php echo JText::_('COM_EASYSOCIAL_EVENTS_PRIVATE_EVENT'); ?>
                        </span>
                        <?php } ?>

                        <?php if ($event->isInviteOnly()) { ?>
                        <span data-original-title="<?php echo FD::_('COM_EASYSOCIAL_EVENTS_INVITE_EVENT_TOOLTIP', true); ?>" data-es-provide="tooltip" data-placement="bottom">
                            <i class="fa fa-envelope"></i>
                            <?php echo JText::_('COM_EASYSOCIAL_EVENTS_INVITE_EVENT'); ?>
                        </span>
                        <?php } ?>
                    <?php } ?>

                    <span class="ml-10">
                        <i class="fa fa-folder"></i>
                        <a data-item-category data-item-category-id="<?php echo $event->getCategory()->id; ?>" href="<?php echo FRoute::events(array('layout' => 'category', 'id' => $event->getCategory()->getAlias())); ?>">
                            <?php echo $event->getCategory()->get('title'); ?>
                        </a>
                    </span>

                    <span class="ml-10">
                        <i class="fa fa-users"></i>
                        <a href="<?php echo FRoute::events(array('layout' => 'item', 'id' => $event->getAlias(), 'appId' => $guestApp->getAlias()));?>"><?php echo JText::sprintf(FD::string()->computeNoun('COM_EASYSOCIAL_EVENTS_GUESTS', $event->getTotalGuests()), $event->getTotalGuests()); ?></a>
                    </span>

                </nav>
            </div>

        </div>

    </div>

    <div class="es-profile-header-footer fd-cf">

        <?php if (($guest->isParticipant() && !$guest->isPending()) || (!$guest->isParticipant() && !$event->isOver())) { ?>
        <div class="fd-cf es-profile-header-footer__rsvp-wrap" data-guest-state-wrap data-id="<?php echo $event->id; ?>" data-allowmaybe="<?php echo (int) $event->getParams()->get('allowmaybe'); ?>" data-allownotgoingguest="<?php echo (int) $event->getGuest()->isOwner() || $event->getParams()->get('allownotgoingguest'); ?>" data-hidetext="0">
            <?php echo $this->loadTemplate('site/events/guestState.content', array('event' => $event, 'guest' => $guest)); ?>
        </div>
        <?php } ?>
    </div>


</div>


