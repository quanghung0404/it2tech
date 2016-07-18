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
<div class="es-events-category" data-events-category>
    <div class="es-category-header">
        <div class="row">
            <div class="col-md-8">
                <div class="media">
                    <div class="media-object pull-left">
                        <img src="<?php echo $category->getAvatar();?>" class="es-avatar" />
                    </div>

                    <div class="media-body">
                        <h2 class="category-title"><?php echo $category->get('title'); ?></h2>
                    </div>
                </div>

                <p class="category-desc"><?php echo $category->get('description'); ?></p>
            </div>
            <div class="col-md-4">
                <div class="category-graph">
                    <div class="h5"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_CREATED_PAST_WEEK'); ?></div>

                    <span data-category-gravity-chart>
                        <!--<?php echo implode(',', $stats); ?>-->
                    </span>

                </div>
            </div>
        </div>
        <div class="es-category-meta">
            <ul class="fd-reset-list category-stats pull-left">
                <li>
                    <i class="fa fa-users"></i> <?php echo JText::sprintf(FD::string()->computeNoun('COM_EASYSOCIAL_EVENTS_TOTAL_EVENTS', $totalEvents), $totalEvents); ?>
                </li>
                <li>
                    <i class="fa fa-photo"></i> <?php echo JText::sprintf(FD::string()->computeNoun('COM_EASYSOCIAL_EVENTS_TOTAL_ALBUMS', $totalAlbums), $totalAlbums); ?>
                </li>
            </ul>

            <?php if($this->access->allowed('events.create', true) && !$this->access->intervalExceeded('events.limit', $this->my->id)){ ?>
            <a href="<?php echo FRoute::events(array('controller' => 'events', 'task' => 'selectCategory', 'category_id' => $category->id));?>" class="btn btn-es-primary pull-right">
                <?php echo JText::_('COM_EASYSOCIAL_EVENTS_CREATE_EVENT'); ?> &rarr;
            </a>
            <?php } ?>
        </div>
    </div>


    <div class="es-container">

        <div class="es-sidebar" data-sidebar>

            <!-- do not remove this element. This element is needed for the stream loodmore to work properly -->
            <div data-dashboardSidebar-menu data-type="eventcategory" data-id="<?php echo $category->id;?>" class="active"></div>

            <?php echo $this->render('module', 'es-events-sidebar-top'); ?>

            <div class="es-widget">
                <div class="es-widget-head">
                    <div class="pull-left widget-title"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_RANDOM_EVENTS');?></div>
                </div>

                <div class="es-widget-body">
                    <?php if($events){ ?>
                    <ul class="widget-list fd-nav fd-nav-stacked random-events">
                        <?php foreach($events as $event){ ?>
                        <li>
                            <img src="<?php echo $event->getAvatar();?>" class="pull-left mr-10 es-avatar" title="<?php echo $this->html('string.escape', $event->getName());?>" />
                            <a href="<?php echo FRoute::events(array('layout' => 'item', 'id' => $event->getAlias()));?>"><?php echo $event->getName(); ?></a>
                            <div class="fd-small event-meta">
                                <i class="fa fa-users"></i> <?php echo JText::sprintf(FD::string()->computeNoun('COM_EASYSOCIAL_EVENTS_GUESTS', $event->getTotalGuests()), $event->getTotalGuests());?>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } else { ?>
                    <div class="fd-small">
                        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_NO_EVENTS_FOUND'); ?>
                    </div>
                    <?php } ?>

                </div>
            </div>

            <div class="es-widget">
                <div class="es-widget-head">
                    <div class="pull-left widget-title"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_RANDOM_ALBUMS');?></div>
                </div>

                <div class="es-widget-body">
                    <?php if ($randomAlbums) { ?>
                        <ul class="widget-list-grid">
                        <?php foreach($randomAlbums as $album){ ?>
                            <li>
                                <div class="es-avatar-wrap">
                                    <a href="<?php echo $album->getPermalink();?>" class="es-avatar es-avatar-sm"
                                        data-original-title="<?php echo $this->html('string.escape', $album->get('title'));?>"
                                        data-es-provide="tooltip"
                                        data-placement="bottom"
                                    >
                                        <img alt="<?php echo $this->html('string.escape', $album->get('title'));?>" src="<?php echo $album->getCover('square');?>" />
                                    </a>
                                </div>
                            </li>
                        <?php } ?>
                        </ul>
                    <?php } else { ?>
                    <div class="fd-small">
                        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_NO_ALBUMS_FOUND'); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="es-widget">
                <div class="es-widget-head">
                    <div class="pull-left widget-title"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_RANDOM_GUESTS');?></div>
                </div>

                <div class="es-widget-body">
                    <?php if ($randomGuests) { ?>
                    <ul class="widget-list-grid">
                        <?php foreach($randomGuests as $user){ ?>
                        <li>
                            <div class="es-avatar-wrap">
                                <a href="<?php echo $user->getPermalink();?>"
                                    class="es-avatar es-avatar-sm "
                                    data-popbox="module://easysocial/profile/popbox"
                                    data-user-id="<?php echo $user->id;?>"
                                >
                                    <img alt="<?php echo $this->html('string.escape', $user->getName());?>" src="<?php echo $user->getAvatar();?>" />
                                </a>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } else { ?>
                    <div class="fd-small">
                        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_NO_GUESTS_FOUND'); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <?php echo $this->render('module', 'es-events-sidebar-bottom'); ?>
        </div>

        <div class="es-content">
            <div class="es-filterbar">
                <div class="filterbar-title h5 pull-left"><?php echo JText::_( 'COM_EASYSOCIAL_EVENTS_RECENT_UPDATES' ); ?></div>
            </div>

            <div class="es-content-wrap" data-event-item-content>
                <?php echo $stream->html();?>
            </div>
        </div>
    </div>

</div>
