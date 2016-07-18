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
<div class="es-container es-events" data-events data-filter="<?php echo $activeCategory ? 'category' : $filter; ?>" data-categoryid="<?php echo $activeCategory ? $activeCategory->id : 0; ?>">
    <a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
        <i class="fa fa-grid-view mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_SIDEBAR_TOGGLE'); ?>
    </a>

    <div class="es-sidebar" data-sidebar>

        <?php echo $this->render('module', 'es-events-sidebar-top' , 'site/dashboard/sidebar.module.wrapper'); ?>

        <div class="es-widget">
            <div class="es-widget-create mr-10">
                <?php if ($this->my->isSiteAdmin() || $this->access->allowed('events.create') && !$this->access->intervalExceeded('events.limit', $this->my->id)) { ?>
                <a href="<?php echo FRoute::events(array('layout' => 'create')); ?>" class="btn btn-es-primary btn-create btn-block"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_CREATE_EVENT'); ?></a>
                <?php } ?>
            </div>

            <hr class="es-hr mt-15 mb-10" />

            <div class="es-widget-body">
                <ul class="widget-list widget-list-with-count fd-nav fd-nav-stacked" data-events-filters>
                    <li
                        data-events-filters-type="all"
                        class="<?php echo $filter == 'all' && !$activeCategory ? 'active' : ''; ?>">
                        <a href="<?php echo FRoute::events(array('filter' => 'all')); ?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_ALL', true); ?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_ALL'); ?></a>

                        <span class="es-count-no pull-right"><?php echo $totalEvents; ?></span>
                    </li>
                    <li
                        data-events-filters-type="featured"
                        class="<?php echo $filter == 'featured' && !$activeCategory ? 'active' : ''; ?>">
                        <a href="<?php echo FRoute::events(array('filter' => 'featured')); ?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_FEATURED', true); ?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_FEATURED'); ?></a>

                        <span class="es-count-no pull-right"><?php echo $totalFeaturedEvents; ?></span>
                    </li>
                    <?php if (!$this->my->guest) { ?>
                    <li
                        data-events-filters-type="mine"
                        class="<?php echo $filter == 'mine' && !$activeCategory ? 'active' : ''; ?>">
                        <a href="<?php echo FRoute::events(array('filter' => 'mine')); ?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_MINE', true); ?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_MINE'); ?></a>

                        <span class="es-count-no pull-right"><?php echo $totalCreatedEvents; ?></span>
                    </li>
                    <li
                        data-events-filters-type="invited"
                        class="<?php echo $filter == 'invited' && !$activeCategory ? 'active' : ''; ?>">
                        <a href="<?php echo FRoute::events(array('filter' => 'invited')); ?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_INVITED', true); ?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_INVITED'); ?></a>
                        <span class="es-count-no pull-right"><?php echo $totalInvitedEvents; ?></span>
                    </li>
                    <?php } ?>
                    <li
                        data-events-filters-type="nearby"
                        class="<?php echo $filter == 'nearby' && !$activeCategory ? 'active' : ''; ?>">
                        <a href="<?php echo FRoute::events(array('filter' => 'nearby')); ?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_NEARBY', true); ?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_NEARBY'); ?></a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="es-widget">
            <div class="es-widget-head">
                <div class="pull-left widget-title"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_BY_DATE_SIDEBAR_TITLE'); ?></div>
            </div>

            <div class="es-widget-body">
                <ul class="widget-list widget-list-with-count fd-nav fd-nav-stacked" data-events-filters>
                    <li
                        data-events-filters-type="date"
                        class="<?php echo $filter == 'date' && $isToday && !$activeCategory ? 'active' : ''; ?>">
                        <a href="<?php echo FRoute::events(array('filter' => 'date')); ?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_TODAY'); ?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_TODAY'); ?></a>
                        <span class="es-count-no pull-right"><?php echo $totalTodayEvents; ?></span>
                    </li>
                    <li
                        data-events-filters-type="tomorrow"
                        class="<?php echo $filter == 'date' && $isTomorrow && !$activeCategory ? 'active' : ''; ?>">
                        <a href="<?php echo FRoute::events(array('filter' => 'date', 'date' => $tomorrow)); ?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_TOMORROW'); ?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_TOMORROW'); ?></a>
                        <span class="es-count-no pull-right"><?php echo $totalTomorrowEvents; ?></span>
                    </li>
                    <li
                        data-events-filters-type="week1"
                        class="<?php echo $filter == 'week1' && !$activeCategory ? 'active' : ''; ?>">
                        <a href="<?php echo FRoute::events(array('filter' => 'week1')); ?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_UPCOMING_1WEEK', true); ?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_WEEK1'); ?></a>
                        <span class="es-count-no pull-right"><?php echo $totalWeek1Events; ?></span>
                    </li>
                    <li
                        data-events-filters-type="week2"
                        class="<?php echo $filter == 'week2' && !$activeCategory ? 'active' : ''; ?>">
                        <a href="<?php echo FRoute::events(array('filter' => 'week2')); ?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_UPCOMING_2WEEK', true); ?>" ><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_WEEK2'); ?></a>
                        <span class="es-count-no pull-right"><?php echo $totalWeek2Events; ?></span>
                    </li>
                    <li
                        data-events-filters-type="month"
                        class="<?php echo $filter == 'date' && $isCurrentMonth && !$activeCategory ? 'active' : ''; ?>">
                        <a href="<?php echo FRoute::events(array('filter' => 'date', 'date' => $currentYear . '-' . $currentMonth)); ?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_MONTH', true); ?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_MONTH'); ?></a>
                        <span class="es-count-no pull-right"><?php echo $totalMonthEvents; ?></span>
                    </li>

                    <li
                        data-events-filters-type="year"
                        class="<?php echo $filter == 'date' && $isCurrentYear && !$activeCategory ? 'active' : ''; ?>">
                        <a href="<?php echo FRoute::events(array('filter' => 'date', 'date' => $currentYear)); ?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_YEAR', true); ?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_YEAR'); ?></a>
                        <span class="es-count-no pull-right"><?php echo $totalYearEvents; ?></span>
                    </li>
                    <li
                        data-events-filters-type="past"
                        class="<?php echo $filter == 'past' && !$activeCategory ? 'active' : ''; ?>">
                        <a href="<?php echo FRoute::events(array('filter' => 'past')); ?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_PAST', true); ?>"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_FILTER_PAST'); ?></a>
                        <span class="es-count-no pull-right"><?php echo $totalPastEvents; ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="es-widget">
            <div class="es-widget-head">
                <div class="pull-left widget-title"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_CATEGORIES_SIDEBAR_TITLE'); ?></div>
            </div>

            <div class="es-widget-body">
                <?php if ($categories){ ?>
                <ul class="widget-list widget-list-with-count fd-nav fd-nav-stacked" data-events-categories data-events-filters>
                    <?php foreach ($categories as $category){ ?>
                    <li
                        data-events-filters-type="category"
                        data-events-filters-categoryid="<?php echo $category->id; ?>"
                        class="<?php echo $activeCategory && $activeCategory->id == $category->id ? 'active' : ''; ?>"
                    >
                        <a href="<?php echo FRoute::events(array('categoryid' => $category->getAlias())); ?>" title="<?php echo $this->html('string.escape', $category->get('title')); ?>"><?php echo $category->get('title'); ?></a>
                        <span data-total-events="<?php echo $category->getTotalEvents(array('start-after' => $now, 'type' => $this->my->isSiteAdmin() ? 'all' : 'user')); ?>" class="es-count-no pull-right"><?php echo $category->getTotalEvents(array('ongoing' => true, 'upcoming' => true, 'type' => $this->my->isSiteAdmin() ? 'all' : 'user')); ?></span>
                    </li>
                    <?php } ?>
                </ul>
                <?php } else { ?>
                <div class="empty empty-hero">
                    <i class="fa fa-users"></i>
                    <div class="small"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_NO_CATEGORY_CREATED_YET'); ?></div>
                </div>
                <?php } ?>
            </div>
        </div>

        <div class="es-widget">
            <div class="es-widget-head">
                <div class="pull-left widget-title"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_CALENDAR_WIDGET_TITLE'); ?></div>
            </div>
            <div class="es-widget-body" data-events-calendar>

            </div>
        </div>

        <?php echo $this->render('module', 'es-events-sidebar-bottom' , 'site/dashboard/sidebar.module.wrapper'); ?>
    </div>


    <div class="es-content pl-20">
        <i class="loading-indicator fd-small"></i>

        <?php echo $this->render('module', 'es-events-before-contents'); ?>
        <div class="events-content-wrapper es-responsive" data-events-content>
            <?php echo $this->includeTemplate('site/events/default.list'); ?>
        </div>
        <?php echo $this->render('module', 'es-events-after-contents'); ?>
    </div>
</div>
