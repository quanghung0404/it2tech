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
<form name="adminForm" id="adminForm" class="eventsForm" method="post" enctype="multipart/form-data" data-events-form data-table-grid>
    <div class="es-user-form">
        <div class="wrapper accordion">
        <?php if (!$isNew) { ?>

            <div class="tab-box tab-box-alt">
                <div class="tabbable">
                    <ul id="userForm" class="nav nav-tabs nav-tabs-icons nav-tabs-side">
                        <li class="tabItem <?php if(empty($activeTab) || $activeTab == 'event') { ?>active<?php } ?>" data-tabnav data-for="event">
                            <a href="#event" data-bs-toggle="tab">
                                <?php echo JText::_('COM_EASYSOCIAL_EVENTS_FORM_EVENT_DETAILS');?>
                            </a>
                        </li>
                        <li class="tabItem <?php if($activeTab == 'guests') { ?>active<?php } ?>" data-tabnav data-for="guests">
                            <a href="#guests" data-bs-toggle="tab">
                                <?php echo JText::_('COM_EASYSOCIAL_EVENTS_FORM_EVENT_GUESTS');?>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content tab-content-side">
                        <div id="event" class="tab-pane <?php if(empty($activeTab) || $activeTab == 'event') { ?>active<?php } ?>" data-tabcontent data-for="event">
                            <?php echo $this->includeTemplate('admin/events/form.fields'); ?>
                        </div>

                        <div id="guests" class="tab-pane <?php if($activeTab == 'guests') { ?>active<?php } ?> inactive" data-tabcontent data-for="guests">
                            <?php echo $this->includeTemplate('admin/events/form.users'); ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php } else { ?>
            <?php echo $this->includeTemplate('admin/events/form.fields'); ?>
        <?php } ?>
        </div>
    </div>

    <input type="hidden" name="option" value="com_easysocial" />
    <input type="hidden" name="controller" value="events" />
    <input type="hidden" name="task" value="" data-table-grid-task />
    <input type="hidden" name="ordering" value="<?php echo $ordering;?>" data-table-grid-ordering />
    <input type="hidden" name="direction" value="<?php echo $direction;?>" data-table-grid-direction />
    <input type="hidden" name="id" value="<?php echo $event->id ? $event->id : ''; ?>" />
    <input type="hidden" name="boxchecked" value="0" data-table-grid-box-checked />
    <input type="hidden" name="activeTab" data-active-tab value="<?php echo $activeTab; ?>" />
    <input type="hidden" name="applyRecurring" value="0" />
    <?php echo JHTML::_('form.token');?>
</form>
