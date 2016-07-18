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
<div class="es-profile es-events-item page-item" data-event-item data-id="<?php echo $event->id;?>">

    <?php if (!empty($group)) { ?>
        <div class="mb-10">
        <?php echo $this->loadTemplate('site/groups/mini.header', array('group' => $group)); ?>
        </div>
    <?php } ?>

    <?php echo $this->loadTemplate('site/events/item.header', array('event' => $event, 'guest' => $guest)); ?>

    <div class="es-container">
        <a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
            <i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_SIDEBAR_TOGGLE');?>
        </a>

        <div class="es-sidebar" data-sidebar>

            <?php echo $this->render('module', 'es-events-item-sidebar-top' , 'site/dashboard/sidebar.module.wrapper'); ?>

            <?php echo $this->render('widgets', SOCIAL_TYPE_EVENT, 'events', 'sidebarTop', array('uid' => $event->id, 'event' => $event)); ?>

            <div class="es-widget">
                <div class="es-widget-head">
                    <div class="pull-left widget-title">
                        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_MENU_SIDEBAR_TITLE');?>
                    </div>
                </div>

                <div class="es-widget-body">

                    <ul class="widget-list fd-nav fd-nav-stacked" data-filter-stream-list>
                        <li data-sidebar-item>
                            <a href="<?php echo FRoute::events(array('layout' => 'item', 'id' => $event->getAlias(), 'type' => 'info')); ?>" data-info <?php if (!empty($infoSteps)) { ?>data-loaded="1"<?php } ?>>
                                <?php echo JText::_('COM_EASYSOCIAL_EVENTS_SIDEBAR_INFO'); ?>
                            </a>
                        </li>

                        <?php if (!empty($infoSteps)) { ?>
                            <?php foreach ($infoSteps as $step) { ?>
                                <?php if (!$step->hide) { ?>
                                <li data-sidebar-item class="<?php if ($step->active) { ?>active<?php } ?>">
                                    <a class="ml-20" href="<?php echo $step->url; ?>" title="<?php echo $step->title; ?>" data-info-item data-info-index="<?php echo $step->index; ?>">
                                        <?php echo $step->title; ?>
                                    </a>
                                </li>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>

                        <li class="<?php echo empty($contents) && empty($context) && empty($filterId) ? 'active' : '';?>" data-sidebar-item>
                            <a href="<?php echo FRoute::events(array('layout' => 'item', 'id' => $event->getAlias(), 'type' => 'timeline')); ?>"
                                data-filter-stream
                                data-dashboardSidebar-menu
                                data-dashboardFeeds-item
                                data-type="<?php echo  SOCIAL_TYPE_EVENT; ?>"
                                data-id="<?php echo $event->id; ?>"
                                data-fid="0"
                                title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_SIDEBAR_MOST_RECENT_ITEMS'); ?>"
                                class="<?php echo empty($contents) ? 'active' : '';?>"
                            >
                                <i class="fa fa-globe mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_SIDEBAR_MOST_RECENT_ITEMS'); ?>
                                <div class="label label-notification pull-right mr-20" data-stream-counter-<?php echo SOCIAL_TYPE_EVENT; ?>>0</div>
                            </a>
                        </li>

                        <?php if (!empty($filters)) { ?>
                        <li class="widget-filter-group">
                            <span><?php echo JText::_('COM_EASYSOCIAL_EVENTS_SIDEBAR_FILTER_BY_HASHTAGS'); ?></span>
                        </li>

                            <?php foreach ($filters as $filter) { ?>
                                <?php echo $this->includeTemplate('site/events/item.filter', array('filter' => $filter, 'filterId' => $filterId, 'event' => $event)); ?>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($guest->isGuest()) { ?>
                        <li class="<?php if ($type == 'filterForm' && empty($filterId)) { ?>active<?php } ?>" data-sidebar-item>
                            <a href="<?php echo FRoute::events(array('layout' => 'item', 'id' => $event->getAlias(), 'type' => 'filterForm'));?>" data-filter-add>
                                <i class="fa fa-plus mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_SIDEBAR_ADD_FILTER'); ?>
                            </a>
                        </li>
                        <?php } ?>

                        <?php if (!empty($appFilters) && $this->template->get('events_feeds_apps', true)) { ?>
                        <li class="widget-filter-group">
                            <span><?php echo JText::_('COM_EASYSOCIAL_EVENTS_SIDEBAR_FILTER_BY_APPS'); ?></span>
                        </li>

                            <?php $i = 1; ?>
                            <?php foreach ($appFilters as $appFilter) { ?>
                                <?php echo $this->includeTemplate('site/events/item.filter.app', array('filter' => $appFilter, 'hide' => $i > $this->template->get('events_feeds_apps_total') && $this->template->get('events_feeds_apps_total') != 0, 'context' => $context)); ?>
                                <?php $i++; ?>
                            <?php } ?>


                            <?php if ($this->template->get('events_feeds_apps_total') > 0 && count($appFilters) > ((int) $this->template->get('events_feeds_apps_total'))) { ?>
                            <li>
                                <a href="javascript:void(0);" class="filter-more" data-filter-showall><?php echo JText::_('COM_EASYSOCIAL_DASHBOARD_SIDEBAR_SHOW_MORE_FILTERS'); ?></a>
                            </li>
                            <?php } ?>
                        <?php } ?>

                        <?php if (!empty($hashtag)) { ?>
                            <li class="widget-filter active"
                                style="display:none;"
                                data-sidebar-item
                                data-dashboardSidebar-menu
                                data-dashboardFeeds-item
                                data-type="<?php echo  SOCIAL_TYPE_EVENT; ?>"
                                data-id="<?php echo $event->id; ?>"
                                data-tag="<?php echo $hashtag ?>"
                            >
                                <a href="javascript:void(0);">
                                    <i class="fa fa-tag mr-5"></i> <?php echo '#' . $hashtag; ?>
                                </a>
                            </li>
                        <?php } ?>

                    </ul>
                </div>
            </div>

            <div class="es-widget">
                <div class="es-widget-head">
                    <div class="pull-left widget-title"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_APPS_SIDEBAR_TITLE');?></div>
                </div>

                <div class="es-widget-body">
                    <ul class="widget-list fd-nav fd-nav-stacked">

                        <?php foreach ($apps as $app) { ?>
                        <li class="<?php echo $appId == $app->id ? 'active' : '';?>" data-sidebar-item>
                            <a
                                href="<?php echo FRoute::events(array('layout' => 'item', 'id' => $event->getAlias(), 'appId' => $app->getAlias()));?>"
                                title="<?php echo $this->html('string.escape', $event->getName());?> - <?php echo $app->get('title');?>"
                                data-app-item
                                data-app-id="<?php echo $app->id;?>"
                            >
                                <img src="<?php echo $app->getIcon();?>" class="app-icon-small mr-5" /> <?php echo $app->getAppTitle(); ?>
                            </a>
                        </li>
                        <?php } ?>

                    </ul>
                </div>
            </div>

            <?php echo $this->render('widgets', SOCIAL_TYPE_EVENT, 'events', 'sidebarMiddle', array('uid' => $event->id, 'event' => $event)); ?>

            <?php echo $this->render('widgets', SOCIAL_TYPE_EVENT, 'events', 'sidebarBottom', array('uid' => $event->id, 'event' => $event)); ?>

            <?php echo $this->render('module', 'es-events-item-sidebar-bottom' , 'site/dashboard/sidebar.module.wrapper'); ?>
        </div>

        <div class="es-content">
            <i class="loading-indicator fd-small"></i>
            <?php echo $this->render('module', 'es-events-before-contents'); ?>

            <div class="es-content-wrap" data-content>
                <?php if (!empty($contents)) { ?>
                    <?php echo $contents; ?>
                <?php } else { ?>
                    <?php if (!empty($hashtag)) { ?>
                    <div class="es-streams">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="javascript:void(0);"
                                   class="fd-small mt-10 pull-right"
                                   data-hashtag-filter-save
                                   data-tag="<?php echo $hashtag; ?>"
                                ><i class="icon-es-create"></i> <?php echo JText::_('COM_EASYSOCIAL_STREAM_SAVE_FILTER');?></a>

                                <h3 class="pull-left">
                                    <a href="<?php echo FRoute::events(array('layout' => 'item' , 'id' => $event->getAlias(), 'tag' => $hashtagAlias));?>">#<?php echo $hashtag; ?></a>
                                </h3>
                            </div>
                        </div>
                        <p class="fd-small">
                            <?php echo JText::sprintf('COM_EASYSOCIAL_STREAM_HASHTAG_CURRENTLY_FILTERING' , '<a href="' . FRoute::events(array('layout' => 'item' , 'id' => $event->getAlias(), 'tag' => $hashtagAlias)) . '">#' . $hashtag . '</a>'); ?>
                        </p>
                    </div>
                    <hr />
                    <?php } ?>

                    <?php echo $this->includeTemplate('site/events/item.feeds'); ?>

                    <?php if ($this->my->guest) { ?>
                        <?php echo $this->includeTemplate('site/dashboard/default.stream.login'); ?>
                    <?php } ?>
                <?php } ?>
            </div>

            <?php echo $this->render('module', 'es-events-after-contents'); ?>
        </div>
    </div>
</div>
