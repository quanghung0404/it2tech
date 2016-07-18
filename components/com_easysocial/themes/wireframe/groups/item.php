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
<div class="es-profile page-item" data-es-group-item data-id="<?php echo $group->id;?>" data-type="<?php echo $group->isOpen() ? 'open' : 'closed';?>">

    <!-- Group Header -->
    <?php echo $this->loadTemplate('site/groups/item.header', array('group' => $group)); ?>

    <div class="es-container">
        <a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
            <i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_SIDEBAR_TOGGLE');?>
        </a>

        <div class="es-sidebar" data-sidebar>

            <?php echo $this->render('module', 'es-groups-item-sidebar-top', 'site/dashboard/sidebar.module.wrapper'); ?>

            <?php echo $this->render('widgets', SOCIAL_TYPE_GROUP, 'groups', 'sidebarTop', array('uid' => $group->id, 'group' => $group)); ?>

            <div class="es-widget">
                <div class="es-widget-head">
                    <div class="pull-left widget-title">
                        <?php echo JText::_('COM_EASYSOCIAL_GROUP_MENU');?>
                    </div>
                </div>

                <div class="es-widget-body">

                    <ul class="widget-list fd-nav fd-nav-stacked" data-es-group-ul>
                        <li data-es-group-filter>
                            <a href="<?php echo FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias(), 'type' => 'info')); ?>" data-info <?php if (!empty($infoSteps)) { ?>data-loaded="1"<?php } ?>>
                                <?php echo JText::_('COM_EASYSOCIAL_GROUP_SIDEBAR_INFO'); ?>
                            </a>
                        </li>

                        <?php if (!empty($infoSteps)) { ?>
                            <?php foreach ($infoSteps as $step) { ?>
                                <?php if (!$step->hide) { ?>
                                <li data-es-group-filter class="<?php if ($step->active) { ?>active<?php } ?>">
                                    <a class="ml-20" href="<?php echo $step->url; ?>" title="<?php echo $step->title; ?>"
                                        data-info-item
                                        data-info-index="<?php echo $step->index; ?>"
                                    >
                                        <?php echo $step->title; ?>
                                    </a>
                                </li>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>

                        <li class="<?php echo empty($contents) && empty($context) && empty($filterId) ? 'active' : '';?>"
                            data-es-group-filter
                            data-dashboardSidebar-menu
                            data-type="<?php echo  SOCIAL_TYPE_GROUP; ?>"
                            data-id="<?php echo $group->id; ?>"
                            data-fid="0"
                        >
                            <a href="<?php echo FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias(), 'type' => 'timeline')); ?>" data-es-group-stream>
                                <?php echo JText::_('COM_EASYSOCIAL_GROUP_TIMELINE'); ?>
                                <div class="label label-notification pull-right mr-20" data-stream-counter-<?php echo  SOCIAL_TYPE_GROUP; ?>>0</div>
                            </a>
                        </li>

                        <?php if (!empty($filters)) { ?>
                        <li class="widget-filter-group">
                            <span><?php echo JText::_('COM_EASYSOCIAL_GROUP_SIDEBAR_FILTER_BY_HASHTAGS'); ?></span>
                        </li>

                            <?php foreach ($filters as $filter) { ?>
                                <?php echo $this->includeTemplate('site/groups/item.filter', array('filter' => $filter, 'filterId' => $filterId, 'group' => $group)); ?>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($group->isMember()) { ?>
                        <li class="<?php if ($type == 'filterForm' && empty($filterId)) { ?>active<?php } ?>" data-es-group-filter>
                            <a href="<?php echo FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias(), 'type' => 'filterForm'));?>" data-stream-filter-button>
                                <?php echo JText::_('COM_EASYSOCIAL_GROUP_FEED_ADD_FILTER'); ?>
                            </a>
                        </li>
                        <?php } ?>

                        <?php if (($group->isAdmin() || $group->isOwner() || $this->my->isSiteAdmin()) && $showPendingPostFilter) { ?>
                        <li class="<?php echo $type == 'moderation' ? ' active' : '';?>" data-es-group-filter>
                            <a href="<?php echo FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias(), 'type' => 'moderation'));?>" data-filter-moderation>
                                <?php echo JText::_('COM_EASYSOCIAL_GROUP_SIDEBAR_PENDING_POSTS'); ?>
                            </a>
                        </li>
                        <?php } ?>

                        <?php if ($appFilters && $this->template->get('groups_feeds_apps')) { ?>
                        <li class="widget-filter-group">
                            <span><?php echo JText::_('COM_EASYSOCIAL_GROUP_SIDEBAR_FILTER_BY_APPS'); ?></span>
                        </li>

                            <?php $i = 1; ?>
                            <?php foreach ($appFilters as $appFilter) { ?>
                                <?php echo $this->includeTemplate('site/groups/item.filter.app', array('filter' => $appFilter, 'hide' => $i > $this->template->get('groups_feeds_apps_total') && $this->template->get('groups_feeds_apps_total') != 0)); ?>
                                <?php $i++; ?>
                            <?php } ?>

                            <?php if (count($appFilters) > $this->template->get('groups_feeds_apps_total') && $this->template->get('groups_feeds_apps_total') != 0){ ?>
                            <li>
                                <a href="javascript:void(0);" class="filter-more" data-app-filters-showall><?php echo JText::_('COM_EASYSOCIAL_DASHBOARD_SIDEBAR_SHOW_MORE_FILTERS'); ?></a>
                            </li>
                            <?php } ?>
                        <?php } ?>

                        <?php if (!empty($hashtag)) { ?>
                            <li class="widget-filter active"
                                style="display:none;"
                                data-es-group-filter
                                data-dashboardSidebar-menu
                                data-dashboardFeeds-item
                                data-type="<?php echo  SOCIAL_TYPE_GROUP; ?>"
                                data-id="<?php echo $group->id; ?>"
                                data-tag="<?php echo $hashtag ?>"
                            >
                                <a href="javascript:void(0);"><?php echo '#' . $hashtag; ?></a>
                            </li>
                        <?php } ?>

                    </ul>
                </div>
            </div>

            <div class="es-widget">
                <div class="es-widget-head">
                    <div class="pull-left widget-title"><?php echo JText::_('COM_EASYSOCIAL_GROUPS_APPS_SIDEBAR_TITLE');?></div>
                </div>

                <div class="es-widget-body">
                    <ul class="widget-list fd-nav fd-nav-stacked">

                        <?php foreach ($apps as $app){ ?>
                        <li class="<?php echo $appId == $app->id ? 'active' : '';?>" data-es-group-filter>
                            <a href="<?php echo FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias(), 'appId' => $app->getAlias()));?>"
                                data-es-group-item-app
                                data-app-id="<?php echo $app->id;?>"
                                title="<?php echo $this->html('string.escape', $group->getName());?> - <?php echo $app->get('title');?>">
                                <?php echo $app->getAppTitle(); ?>
                            </a>
                        </li>
                        <?php } ?>

                    </ul>
                </div>
            </div>

            <?php echo $this->render('widgets', SOCIAL_TYPE_GROUP, 'groups', 'sidebarMiddle', array('uid' => $group->id, 'group' => $group)); ?>

            <?php echo $this->render('widgets', SOCIAL_TYPE_GROUP, 'groups', 'sidebarBottom', array('uid' => $group->id, 'group' => $group)); ?>

            <?php echo $this->render('module', 'es-groups-item-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
        </div>


        <div class="es-content">
            <i class="loading-indicator fd-small"></i>
            <?php echo $this->render('module', 'es-groups-before-contents'); ?>

            <div class="es-content-wrap" data-es-group-item-content>
                <?php if ($contents){ ?>
                    <?php echo $contents; ?>
                <?php } else { ?>
                    <?php echo $this->includeTemplate('site/groups/item.content'); ?>
                <?php } ?>
            </div>

            <?php echo $this->render('module', 'es-groups-after-contents'); ?>
        </div>

    </div>
</div>
