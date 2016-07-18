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
<?php if (!empty($activeCategory) && $this->template->get('events_category_header', true)) { ?>
<div class="category-listed-header">
    <div class="media">
        <div class="media-object pull-left">
            <img src="<?php echo $activeCategory->getAvatar();?>" class="es-avatar" title="<?php echo $this->html('string.escape', $activeCategory->get('title'));?>" />
        </div>
        <div class="media-body">
            <h3 class="h3 es-title-font mt-10"><?php echo $activeCategory->get('title'); ?></h3>
        </div>

        <?php if (!empty($activeCategory->description)) { ?>
        <p class="fd-small">
            <?php echo $activeCategory->get('description'); ?>
        </p>
        <?php } ?>

        <div class="mt-15">
            <a href="<?php echo FRoute::events(array('layout' => 'category', 'id' => $activeCategory->getAlias()));?>" class="btn btn-es-primary btn-sm"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_VIEW_CATEGORY'); ?> &rarr;</a>
        </div>

    </div>
</div>
<hr />
<?php } ?>

<?php if ($filter === 'date') { ?>
<div class="btn-group btn-group-justified es-btn-group-date">
    <a role="button" class="btn btn-es" href="<?php echo $prevLink; ?>" data-events-nav-prevdate="<?php echo $prevDate; ?>" title="<?php echo $prevTitle; ?>">&#171;</a>
    <?php if ($isToday) { ?>
    <span class="btn btn-es"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_TODAY'); ?> - <?php echo $date->format(JText::_($dateFormat)); ?></span>
    <?php } else if ($isTomorrow) { ?>
    <span class="btn btn-es"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_TOMORROW'); ?> - <?php echo $date->format(JText::_($dateFormat)); ?></span>
    <?php } else { ?>
    <span class="btn btn-es"><?php echo $date->format(JText::_($dateFormat)); ?></span>
    <?php } ?>
    <a role="button" class="btn btn-es" href="<?php echo $nextLink; ?>" data-events-nav-nextdate="<?php echo $nextDate; ?>" title="<?php echo $nextTitle; ?>">&#187;</a>
</div>
<hr />
<?php } ?>

<?php if (!$delayed && $filter === 'nearby') { ?>
<h3 class="h3 es-title-font mt-5 mb-20" data-events-nearby-title><?php echo JText::sprintf('COM_EASYSOCIAL_EVENTS_IN_DISTANCE_RADIUS', $distance, $distanceUnit); ?></h3>
<hr />
<?php } ?>

<?php if (!empty($featuredEvents)) { ?>
<div class="media-featured <?php echo !$featuredEvents ? 'is-empty' : '';?>">
    <ul class="fd-reset-list">
        <?php foreach ($featuredEvents as $event){ ?>
        <li class="is-featured"
            data-id="<?php echo $event->id;?>"
            data-events-item-featured
            data-events-item
            data-events-item-id="<?php echo $event->id;?>"
            data-events-item-type="<?php echo $event->isOpen() ? 'open' : 'closed';?>"
        >
            <?php echo $this->loadTemplate('site/events/default.item', array('event' => $event, 'owner' => $event->getOwner(), 'guest' => $event->getGuest($this->my->id), 'guestApp' => $guestApp)); ?>
        </li>
        <?php } ?>
    </ul>
</div>
<?php } ?>

<?php if (!$delayed && ($showSorting || $showPastFilter || $showDistanceSorting)) { ?>
<div class="row-table mt-15 mb-15">
    <?php if ($showDistanceSorting) { ?>
        <div class="col-cell cell-mid">
            <div class="form-inline pull-left">
                <div class="form-group">
                    <label class="sr-only" for=""><?php echo JText::_('COM_EASYSOCIAL_EVENTS_DISTANCE'); ?>: </label>
                    <select class="form-control input-sm" data-events-radius>
                        <option value="">10 <?php echo $distanceUnit; ?></option>
                        <option value="25" <?php if ($distance == 25) { ?>selected="selected"<?php } ?>>25 <?php echo $distanceUnit; ?></option>
                        <option value="50" <?php if ($distance == 50) { ?>selected="selected"<?php } ?>>50 <?php echo $distanceUnit; ?></option>
                        <option value="100" <?php if ($distance == 100) { ?>selected="selected"<?php } ?>>100 <?php echo $distanceUnit; ?></option>
                        <option value="200" <?php if ($distance == 200) { ?>selected="selected"<?php } ?>>200 <?php echo $distanceUnit; ?></option>
                        <option value="300" <?php if ($distance == 300) { ?>selected="selected"<?php } ?>>300 <?php echo $distanceUnit; ?></option>
                        <option value="400" <?php if ($distance == 400) { ?>selected="selected"<?php } ?>>400 <?php echo $distanceUnit; ?></option>
                        <option value="500" <?php if ($distance == 500) { ?>selected="selected"<?php } ?>>500 <?php echo $distanceUnit; ?></option>
                        <?php if (!empty($distance) && !in_array($distance, array(10, 25, 50, 100, 200, 300, 400, 500))) { ?>
                        <option value="<?php echo $distance; ?>" selected="selected"><?php echo $distance; ?> <?php echo $distanceUnit; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    <?php } else if ($showPastFilter) { ?>
        <div class="col-cell cell-mid">
            <div class="es-checkbox mr-5 mt-0">
                <input type="checkbox" class="pull-right ml-5" data-events-past <?php if ($includePast) { ?>checked="checked"<?php } ?>>
                <label>
                    <a
                        href="<?php echo $hrefs[$ordering][$includePast ? 'nopast' : 'past']; ?>"
                        data-start-past="<?php echo $hrefs['start']['past']; ?>"
                        data-start-nopast="<?php echo $hrefs['start']['nopast']; ?>"
                        <?php if ($showSorting) { ?>
                        data-created-past="<?php echo $hrefs['created']['past']; ?>"
                        data-created-nopast="<?php echo $hrefs['created']['nopast']; ?>"
                        <?php } ?>
                        <?php if ($showDistanceSorting) { ?>
                        data-distance-past="<?php echo $hrefs['distance']['past']; ?>"
                        data-distance-nopast="<?php echo $hrefs['distance']['nopast']; ?>"
                        <?php } ?>
                        data-events-past-link
                        title="<?php echo FD::page()->title; ?>"
                    ><?php echo JText::_('COM_EASYSOCIAL_EVENTS_INCLUDE_PAST_EVENTS'); ?></a>
                </label>
            </div>
        </div>
    <?php } ?>


    <?php if ($showSorting || $showDistanceSorting) { ?>
        <div class="col-cell cell-mid">
            <div class="btn-group btn-group-sort pull-right" data-events-sorting>
                <a class="btn btn-es trending <?php if ($ordering == 'start') { ?>active<?php } ?>" data-es-provide="tooltip" data-placement="bottom" data-original-title="" href="<?php echo $hrefs['start'][$showPastFilter && $includePast ? 'past' : 'nopast']; ?>" data-ordering="start" data-filter="<?php echo $activeCategory ? 'category' : $filter; ?>" data-categoryid="<?php echo $activeCategory ? $activeCategory->id : ''; ?>" title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_SORT_BY_EVENT_DATE', true);?>">
                    <i class="fa fa-clock-o"></i>
                    <?php echo JText::_('COM_EASYSOCIAL_EVENTS_SORTING_EVENT_DATE');?>
                </a>

                <?php if ($showSorting) { ?>
                <a class="btn btn-es recent <?php if ($ordering == 'created') { ?>active<?php } ?>" data-es-provide="tooltip" data-placement="bottom" data-original-title="" href="<?php echo $hrefs['created'][$showPastFilter && $includePast ? 'past' : 'nopast']; ?>" data-ordering="created" data-filter="<?php echo $activeCategory ? 'category' : $filter; ?>" data-categoryid="<?php echo $activeCategory ? $activeCategory->id : ''; ?>" title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_SORT_BY_CREATED_DATE', true);?>">
                    <i class="fa fa-calendar"></i>
                    <?php echo JText::_('COM_EASYSOCIAL_EVENTS_SORTING_EVENT_CREATED_DATE');?>
                </a>
                <?php } ?>

                <?php if ($showDistanceSorting) { ?>
                <a class="btn btn-es recent <?php if ($ordering == 'distance') { ?>active<?php } ?>" data-es-provide="tooltip" data-placement="bottom" data-original-title="" href="<?php echo $hrefs['distance'][$showPastFilter && $includePast ? 'past' : 'nopast']; ?>" data-ordering="distance" data-filter="<?php echo $activeCategory ? 'category' : $filter; ?>" data-categoryid="<?php echo $activeCategory ? $activeCategory->id : ''; ?>" title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_SORT_BY_DISTANCE', true);?>">
                    <i class="fa fa-map-marker"></i>
                    <?php echo JText::_('COM_EASYSOCIAL_EVENTS_SORTING_EVENT_DISTANCE');?>
                </a>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>

<?php if ($showPastFilter && $showDistanceSorting) { ?>
<div class="es-checkbox mr-5 mt-0">
    <input type="checkbox" class="pull-right ml-5" data-events-past <?php if ($includePast) { ?>checked="checked"<?php } ?>>
    <label>
        <a
            href="<?php echo $hrefs[$ordering][$includePast ? 'nopast' : 'past']; ?>"
            data-start-past="<?php echo $hrefs['start']['past']; ?>"
            data-start-nopast="<?php echo $hrefs['start']['nopast']; ?>"
            <?php if ($showSorting) { ?>
            data-created-past="<?php echo $hrefs['created']['past']; ?>"
            data-created-nopast="<?php echo $hrefs['created']['nopast']; ?>"
            <?php } ?>
            <?php if ($showDistanceSorting) { ?>
            data-distance-past="<?php echo $hrefs['distance']['past']; ?>"
            data-distance-nopast="<?php echo $hrefs['distance']['nopast']; ?>"
            <?php } ?>
            data-events-past-link
            title="<?php echo FD::page()->title; ?>"
        ><?php echo JText::_('COM_EASYSOCIAL_EVENTS_INCLUDE_PAST_EVENTS'); ?></a>
    </label>
</div>
<?php } ?>

<hr />
<?php } ?>

<div class="media- <?php echo !$delayed && !$events ? 'is-empty' : '';?>" data-events-list>
    <?php echo $this->includeTemplate('site/events/default.list.items'); ?>
</div>
