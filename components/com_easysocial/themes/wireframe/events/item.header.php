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
<div class="es-profile-header"
    data-id="<?php echo $event->id;?>"
    data-name="<?php echo $this->html('string.escape', $event->getName());?>"
    data-avatar="<?php echo $event->getAvatar();?>">

    <div class="es-profile-header-heading with-cover">
        <?php echo $this->includeTemplate('site/events/cover', array('cover' => $event->getCoverData())); ?>
        <?php echo $this->includeTemplate('site/events/avatar'); ?>
        <?php echo $this->render('widgets', 'event', 'item', 'afterAvatar', array($event)); ?>
    </div>

    <div class="es-profile-header-body fd-cf">
        <div class="es-profile-header-action pull-right">
            <?php echo $this->render('module', 'es-events-before-actions'); ?>
            <?php echo $this->render('widgets', 'event', 'item', 'beforeActions', array($event)); ?>

            <?php if ($guest->isGuest() && !$event->isOver()) { ?>
            <div>
                <a class="btn btn-block btn-es btn-sm" href="javascript:void(0);" data-action-invite>
                    <i class="fa fa-paper-plane-o mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_INVITE_FRIENDS');?>
                </a>
            </div>
            <?php } ?>

            <?php if ($this->my->isSiteAdmin() || $guest->isOwner() || $guest->isAdmin()) { ?>
            <div class="dropdown_">
                <a class="btn btn-block btn-es-primary btn-sm" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="fa fa-cog mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_MANAGE_EVENT');?> <i class="fa fa-caret-down"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-user messageDropDown">
                    <?php echo $this->render('widgets', 'event', 'events', 'eventAdminStart', array($event)); ?>

                    <li>
                        <a href="<?php echo FRoute::events(array('layout' => 'edit', 'id' => $event->getAlias()));?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_EDIT_EVENT');?></a>
                    </li>

                    <?php if ($this->my->isSiteAdmin() || $guest->isOwner()) { ?>
                        <li class="divider"></li>
                        <?php if ($this->my->isSiteAdmin()) { ?>
                        <li>
                            <a href="javascript:void(0);" data-action-unpublish><?php echo JText::_('COM_EASYSOCIAL_EVENTS_UNPUBLISH_EVENT');?></a>
                        </li>
                        <?php } ?>

                        <li>
                            <a href="javascript:void(0);" data-action-delete><?php echo JText::_('COM_EASYSOCIAL_EVENTS_DELETE_EVENT');?></a>
                        </li>
                        <?php echo $this->render('widgets', 'event', 'events', 'eventAdminEnd', array($event)); ?>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>

            <?php echo $this->render('module', 'es-events-after-actions'); ?>
            <?php echo $this->render('widgets', 'event', 'item', 'afterActions', array($event)); ?>
        </div>

        <div>
            <?php echo $this->render('module', 'es-events-before-name'); ?>

            <h2 class="es-profile-header-title">
                <?php echo $this->html('html.event', $event); ?>

                <?php if ($event->isGroupEvent()) { ?>
                    <span class="fd-small">
                    <?php echo JText::sprintf('COM_EASYSOCIAL_EVENTS_EVENT_OF_GROUP_TITLE', '<i class="fa fa-users"></i> ' . $this->html('html.group', $event->getGroup())); ?>
                    </span>
                <?php } ?>
            </h2>

            <?php echo $this->render('module', 'es-events-after-name'); ?>

            <nav class="es-list-vertical-divider mt-10">

                <?php if (!$guest->isOwner()) { ?>
                <span>
                    <i class="fa fa-user muted"></i>
                    <?php echo $this->html('html.user', $event->creator_uid, true); ?>
                </span>
                <?php } ?>

                <?php if (!$event->isGroupEvent()) { ?>

                    <?php if ($event->isOpen()) { ?>
                    <span data-original-title="<?php echo FD::_('COM_EASYSOCIAL_EVENTS_OPEN_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="bottom">
                        <i class="fa fa-globe muted"></i>
                        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_OPEN_EVENT'); ?>
                    </span>
                    <?php } ?>

                    <?php if ($event->isClosed()) { ?>
                    <span data-original-title="<?php echo FD::_('COM_EASYSOCIAL_EVENTS_PRIVATE_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="bottom">
                        <i class="fa fa-lock muted"></i>
                        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_PRIVATE_EVENT'); ?>
                    </span>
                    <?php } ?>

                    <?php if ($event->isInviteOnly()) { ?>
                    <span data-original-title="<?php echo FD::_('COM_EASYSOCIAL_EVENTS_INVITE_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="bottom">
                        <i class="fa fa-lock muted"></i>
                        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_INVITE_EVENT'); ?>
                    </span>
                    <?php } ?>
                <?php } ?>

                <span>
                    <i class="fa fa-folder muted"></i>
                    <a href="<?php echo FRoute::events(array('layout' => 'category', 'id' => $event->getCategory()->getAlias()));?>">
                        <?php echo $event->getCategory()->get('title'); ?>
                    </a>
                </span>

                <?php echo $this->render('widgets', 'event', 'events', 'afterCategory', array($event)); ?>

                <?php if ($this->config->get('events.ical', true)) { ?>
                <span>
                    <i class="ies-download muted"></i>
                    <?php
                        $icalLink = FRoute::events(array('layout' => 'export', 'id' => $event->getAlias()));
                        if (strpos($icalLink, '?') !== false) {
                            $icalLink .= '&format=ical';
                        } else {
                            $icalLink .= '?format=ical';
                        }
                    ?>
                    <a href="<?php echo $icalLink; ?>" target="_blank"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_EXPORT_TO_ICAL');?></a>
                </span>
                <?php } ?>



            </nav>

            <div class="mt-5">
                <i class="fa fa-calendar mr-5"></i>
                <?php echo $event->getStartEndDisplay(); ?>
            </div>

            <?php if ($this->template->get('events_address', true) && !empty($event->address)) { ?>
            <div class="mt-5">
                <i class="fa fa-map-marker mr-5"></i>
                <a href="<?php echo $event->getAddressLink(); ?>" target="_blank"><?php echo $event->address; ?></a>
            </div>
            <?php } ?>

            <?php if ($this->template->get('events_seatsleft', true) && $event->seatsLeft() >= 0) { ?>
            <div class="mt-5 btn btn-es btn-xs"><?php echo JText::sprintf('COM_EASYSOCIAL_EVENTS_SEATS_LEFT', $event->seatsLeft()); ?></div>
            <?php } ?>

            <?php if (!$guest->isOwner() && $this->access->allowed('reports.submit') && $this->config->get('reports.enabled')) { ?>
            <div class="page-more">
                <?php echo FD::reports()->getForm('com_easysocial', SOCIAL_TYPE_EVENT, $event->id, $event->getName(), JText::_('COM_EASYSOCIAL_EVENTS_REPORT_EVENT')); ?>
            </div>
            <?php } ?>

        </div>
    </div>

    <div class="es-profile-header-footer">

        <nav class="es-list-vertical-divider pull-left pa-5">
            <?php echo $this->render('widgets', 'event', 'events', 'eventStatsStart', array($event)); ?>

            <?php if ($this->config->get('video.enabled', true) && $event->getParams()->get('videos', true) && $event->getCategory()->getAcl()->get('videos.create', true)) { ?>
            <span>
                <a href="<?php echo FRoute::videos(array('uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT));?>">

                    <i class="fa fa-film"></i>
                    &#8207;
                    <?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_EVENTS_VIDEOS' , $event->getTotalVideos()), $event->getTotalVideos()); ?>
                </a>
            </span>
            <?php } ?>

            <?php if ($event->getCategory()->getAcl()->get('photos.enabled', true) && $event->getParams()->get('photo.albums', true)) { ?>
            <span>
                <a href="<?php echo FRoute::albums(array('uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT));?>">
                    <i class="fa fa-photo"></i>
                    &#8207;
                    <?php echo JText::sprintf(FD::string()->computeNoun('COM_EASYSOCIAL_EVENTS_TOTAL_ALBUMS', $event->getTotalAlbums()), $event->getTotalAlbums()); ?>
                </a>
            </span>
            <?php } ?>

            <span>
                <i class="fa fa-graph"></i>
                &#8207;
                <?php echo JText::sprintf(FD::string()->computeNoun('COM_EASYSOCIAL_EVENTS_TOTAL_VIEWS', $event->hits), $event->hits); ?>
            </span>
            <span>
            &#8207;
                <?php echo FD::sharing(array('url' => $event->getPermalink(false, true), 'display' => 'dialog', 'text' => JText::_('COM_EASYSOCIAL_STREAM_SOCIAL'), 'css' => 'fd-small'))->getHTML(true); ?>
            </span>
            <?php echo $this->render('widgets', 'event', 'events', 'eventStatsEnd', array($event)); ?>
        </nav>

        <div data-guest-state-wrap
            data-id="<?php echo $event->id; ?>"
            data-allowmaybe="<?php echo (int) $event->getParams()->get('allowmaybe'); ?>"
            data-allownotgoingguest="<?php echo (int) $event->getGuest()->isOwner() || $event->getParams()->get('allownotgoingguest'); ?>"
            data-hidetext="1"
            <?php if (!$this->my->guest) { ?>data-refresh<?php } ?>
        >
            <?php echo $this->loadTemplate('site/events/guestState.content', array('event' => $event, 'guest' => $guest, 'hideText' => true)); ?>
        </div>
    </div>
</div>
