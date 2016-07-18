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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="es-header-mini"
    data-id="<?php echo $event->id;?>"
    data-name="<?php echo $this->html('string.escape' , $event->getName() );?>"
    data-avatar="<?php echo $event->getAvatar();?>"
    data-es-event-item
>
    <div class="es-header-mini-cover" style="background-image: url('<?php echo $event->getCover();?>');background-position: <?php echo $event->getCoverPosition();?>;">
        <b></b>
        <b></b>
    </div>

    <div class="es-header-mini-avatar">
        <a class="es-avatar es-avatar-md" href="<?php echo $event->getPermalink();?>">
            <img alt="<?php echo $this->html( 'string.escape' , $event->getName() );?>" src="<?php echo $event->getAvatar( SOCIAL_AVATAR_SQUARE );?>" />
        </a>
    </div>

    <div class="es-header-mini-body" data-appscroll>
        <div class="es-header-mini-meta">
            <ul class="fd-reset-list">
                <li>
                    <h2 class="h4 es-cover-title">
                        <?php echo $this->html('html.event', $event); ?>
                    </h2>

                    <?php if ($event->isOpen()) { ?>
                    <span class="label label-success" data-original-title="<?php echo FD::_('COM_EASYSOCIAL_EVENTS_OPEN_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="top">
                        <i class="fa fa-globe"></i>
                        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_OPEN_EVENT'); ?>
                    </span>
                    <?php } ?>

                    <?php if ($event->isClosed()) { ?>
                    <span class="label label-danger" data-original-title="<?php echo FD::_('COM_EASYSOCIAL_EVENTS_PRIVATE_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="top">
                        <i class="fa fa-lock"></i>
                        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_PRIVATE_EVENT'); ?>
                    </span>
                    <?php } ?>

                    <?php if ($event->isInviteOnly()) { ?>
                    <span data-original-title="<?php echo FD::_('COM_EASYSOCIAL_EVENTS_INVITE_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="top">
                        <i class="fa fa-lock"></i>
                        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_INVITE_EVENT'); ?>
                    </span>
                    <?php } ?>
                </li>

                <?php if ($event->isGroupEvent()) { ?>
                <li>
                    <?php echo JText::sprintf('COM_EASYSOCIAL_EVENTS_GROUP_EVENT_OF_GROUP', '<i class="fa fa-users"></i> ' . $this->html('html.group', $event->getGroup())); ?>
                </li>
                <?php } ?>
            </ul>

            <div class="fd-small info-actions">
                <a href="<?php echo FRoute::events(array('layout' => 'item', 'type' => 'info', 'id' => $event->getAlias()));?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_MORE_ABOUT_THIS_EVENT'); ?></a>

                <?php if( $this->access->allowed( 'reports.submit' ) ){ ?>
                &middot; <?php echo FD::reports()->getForm('com_easysocial', SOCIAL_TYPE_EVENT, $event->id, $event->getName(), JText::_('COM_EASYSOCIAL_EVENTS_REPORT_EVENT')); ?>
                <?php } ?>
            </div>

        </div>

        <?php if ( ( !isset($showApps) || (isset($showApps) && $showApps)) && $event->getApps() && ($event->getGuest()->isGuest() || $event->isOpen() ) ){ ?>
        <div class="btn- btn-scroll" data-appscroll-buttons>
            <a href="javascript:void(0);" class="btn btn-left" data-appscroll-prev-button>
                <i class="fa fa-caret-left"></i>
            </a>
            <a href="javascript:void(0);" class="btn btn-right" data-appscroll-next-button>
                <i class="fa fa-caret-right"></i>
            </a>
        </div>

        <div class="es-header-mini-apps-action" data-appscroll-viewport>
            <ul class="fd-nav fd-nav- es-nav-apps" data-appscroll-content>
                <?php foreach ($event->getApps() as $app) { ?>
                <li>
                    <a class="btn btn-clean" href="<?php echo FRoute::events( array( 'layout' => 'item' , 'id' => $event->getAlias() , 'appId' => $app->getAlias() ) );?>">
                        <span><?php echo $app->getAppTitle(); ?></span>
                        <img src="<?php echo $app->getIcon();?>" class="es-nav-apps-icons" />
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>

    </div>

    <div class="es-header-mini-footer">
        <div class="pull-left">
            <div class="es-list-vertical-divider mb-0 ml-0">
                <?php echo $this->render('widgets', 'event', 'events', 'miniEventStatsStart', array($event)); ?>
                <span>
                    <a href="<?php echo FRoute::events(array('layout' => 'category' , 'id' => $event->getCategory()->getAlias()));?>">
                        <i class="fa fa-database"></i> <?php echo $event->getCategory()->get('title'); ?>
                    </a>
                </span>

                <?php if ($this->config->get('video.enabled', true) && $event->getParams()->get('videos', true)) { ?>
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
                    <a href="<?php echo FRoute::albums(array( 'uid' => $event->getAlias() , 'type' => SOCIAL_TYPE_EVENT ) );?>">
                        <i class="fa fa-photo"></i> <?php echo JText::sprintf( FD::string()->computeNoun( 'COM_EASYSOCIAL_GROUPS_ALBUMS' , $event->getTotalAlbums() ) , $event->getTotalAlbums() ); ?>
                    </a>
                </span>
                <?php } ?>
                
                <span>
                    <i class="fa fa-eye"></i> <?php echo JText::sprintf(FD::string()->computeNoun( 'COM_EASYSOCIAL_GROUPS_VIEWS' , $event->hits ) , $event->hits ); ?></a>
                </span>
                <?php echo $this->render('widgets', 'event', 'events', 'miniEventStatsEnd', array($event)); ?>
                <span>
                    <?php echo FD::sharing( array('url' => $event->getPermalink(false, true), 'display' => 'dialog', 'text' => JText::_('COM_EASYSOCIAL_STREAM_SOCIAL') , 'css' => 'fd-small' ) )->getHTML(true); ?>
                </span>
            </div>
        </div>

        <div data-guest-state-wrap data-id="<?php echo $event->id; ?>" data-allowmaybe="<?php echo (int) $event->getParams()->get('allowmaybe'); ?>" data-allownotgoingguest="<?php echo (int) $event->getGuest()->isOwner() || $event->getParams()->get('allownotgoingguest'); ?>" data-hidetext="1" data-refresh class="mr-10">
            <?php echo $this->loadTemplate('site/events/guestState.content', array('event' => $event, 'guest' => $event->getGuest(), 'hideText' => true)); ?>
        </div>
    </div>
</div>
