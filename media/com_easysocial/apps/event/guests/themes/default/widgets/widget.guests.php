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
<div class="es-widget es-widget-guests">
    <div class="es-widget-head">
        <div class="pull-left widget-title"><?php echo JText::_('APP_EVENT_GUESTS_WIDGET_GUESTS_TITLE'); ?></div>

        <?php if (!empty($goingGuests) || !empty($maybeGuests) || !empty($notGoingGuests)) { ?>
            <a class="fd-small pull-right" href="<?php echo $link; ?>"><?php echo JText::_('APP_EVENT_GUESTS_VIEW_ALL_GUESTS');?></a>
        <?php } ?>
    </div>
    <div class="es-widget-body">

        <ul class="fd-nav es-widget-tab" role="tablist">
            <li class="active"><a href="#es-going-guests" role="tab" data-bs-toggle="tab"><?php echo JText::_('APP_EVENT_GUESTS_WIDGET_GUESTS_TAB_GOING'); ?>
                <?php if ($totalGoing > 0){ ?>
                <span class="widget-label">(<?php echo $totalGoing;?>)</span>
                <?php } ?>
            </a></li>
            <?php if ($allowMaybe) { ?>
            <li><a href="#es-maybe-guests" role="tab" data-bs-toggle="tab"><?php echo JText::_('APP_EVENT_GUESTS_WIDGET_GUESTS_TAB_MAYBE'); ?>
                <?php if ($totalMaybe > 0){ ?>
                <span class="widget-label">(<?php echo $totalMaybe;?>)</span>
                <?php } ?>
            </a></li>
            <?php } ?>
            <?php if ($allowNotGoing) { ?>
            <li><a href="#es-notgoing-guests" role="tab" data-bs-toggle="tab"><?php echo JText::_('APP_EVENT_GUESTS_WIDGET_GUESTS_TAB_NOTGOING'); ?>
                <?php if ($totalNotGoing > 0){ ?>
                <span class="widget-label">(<?php echo $totalNotGoing;?>)</span>
                <?php } ?>
            </a></li>
            <?php } ?>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="es-going-guests">
                <ul class="widget-list-grid">
                    <?php if (!empty($goingGuests)) { ?>
                        <?php foreach ($goingGuests as $goingGuest) { ?>
                        <li><?php echo $this->html('html.user', $goingGuest->uid, true, 'top-left', true); ?></li>
                        <?php } ?>
                    <?php } else { ?>
                    <li>
                        <div class="fd-small">
                            <?php echo JText::_('APP_EVENT_GUESTS_WIDGET_GUESTS_NO_GUESTS_YET'); ?>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="tab-pane" id="es-maybe-guests">
                <ul class="widget-list-grid">
                    <?php if (!empty($maybeGuests)) { ?>
                        <?php foreach ($maybeGuests as $maybeGuest) { ?>
                        <li><?php echo $this->html('html.user', $maybeGuest->uid, true, 'top-left', true); ?></li>
                        <?php } ?>
                    <?php } else { ?>
                    <li>
                        <div class="fd-small">
                            <?php echo JText::_('APP_EVENT_GUESTS_WIDGET_GUESTS_NO_GUESTS_YET'); ?>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="tab-pane" id="es-notgoing-guests">
                <ul class="widget-list-grid">
                    <?php if (!empty($notGoingGuests)) { ?>
                        <?php foreach ($notGoingGuests as $notGoingGuest) { ?>
                        <li><?php echo $this->html('html.user', $notGoingGuest->uid, true, 'top-left', true); ?></li>
                        <?php } ?>
                    <?php } else { ?>
                    <li>
                        <div class="fd-small">
                            <?php echo JText::_('APP_EVENT_GUESTS_WIDGET_GUESTS_NO_GUESTS_YET'); ?>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
