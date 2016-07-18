<?php
/**
* @package        EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<li data-tasks-list-item data-id="<?php echo $task->id; ?>">
    <div class="clearfix">
        <div class="checkbox">
            <input type="checkbox" id="task-item-<?php echo $task->id; ?>" data-item-checkbox <?php echo $task->state == 2 ? ' checked="checked"' : ''; ?> <?php echo !$event->getGuest()->isGuest() ? ' disabled="disabled"' : ''; ?>/>
            <img src="<?php echo $user->getAvatar(); ?>" title="<?php echo $this->html('string.escape', $user->getName()); ?>" class="owner-avatar"
                data-es-provide="tooltip" data-original-title="<?php echo JText::sprintf('APP_EVENT_TASKS_ASSIGNED_TO_USER', $user->getName()); ?>" data-placement="bottom" />
            <?php echo $task->title; ?>

            <?php if ($event->isAdmin() || $task->user_id == $this->my->id) { ?>
            <span class="btn-group">
                <a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_ btn btn-dropdown">
                    <i class="icon-es-dropdown"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-user messageDropDown">
                    <li>
                        <a href="javascript:void(0);" data-tasks-item-remove><?php echo JText::_('APP_EVENT_TASKS_DELETE_TASK'); ?></a>
                    </li>
                </ul>
            </span>
            <?php } ?>

            <span class="pull-right task-created">
                <i class="fa fa-clock-o"></i> <?php echo FD::date($task->created)->toLapsed(); ?>
            </span>
        </div>

        <?php if ($task->hasDueDate() && $task->due) { ?>
        <div class="task-meta">
            <ul class="list-unstyled task-actions">
                <li>
                    <i class="fa fa-warning-2"></i> <?php echo JText::sprintf('APP_EVENT_TASKS_DUE_ON', FD::date($task->due)->format(JText::_('DATE_FORMAT_LC1'))); ?>
                </li>
            </ul>
        </div>
        <?php } ?>
    </div>
</li>
