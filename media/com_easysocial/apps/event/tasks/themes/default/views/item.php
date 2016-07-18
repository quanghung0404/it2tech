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
<div class="app-tasks app-groups" data-tasks-item data-id="<?php echo $milestone->id; ?>" data-eventid="<?php echo $event->id; ?>" data-milestoneid="<?php echo $milestone->id; ?>">
    <div class="es-content milestone-item-view <?php echo $milestone->isDue() ? 'is-due' : ''; ?> <?php echo $milestone->isCompleted() ? 'is-completed' : ''; ?>" data-tasks-wrapper>
        <div class="row">
            <div class="col-sm-8 milestone-content">
                <div class="mb-15">
                    <a href="<?php echo FRoute::events(array('layout' => 'item', 'id' => $event->getAlias())); ?>">&larr; <?php echo JText::_('APP_EVENT_TASKS_BACK_TO_EVENT'); ?></a>
                </div>

                <div class="clearfix milestone-title">
                    <h3 class="pull-left">
                        <?php echo $milestone->title; ?>
                    </h3>

                    <?php if ($event->isAdmin()) { ?>
                    <span class="btn-group pull-right">
                        <a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_ btn btn-dropdown">
                            <i class="icon-es-dropdown"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo FRoute::apps(array('layout' => 'canvas', 'customView' => 'form', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $app->getAlias(), 'milestoneId' => $milestone->id), false); ?>"><?php echo JText::_('APP_EVENT_TASKS_MILESTONE_EDIT'); ?></a>
                            </li>

                            <li class="mark-uncomplete">
                                <a href="javascript:void(0);" data-milestone-mark-incomplete><?php echo JText::_('APP_EVENT_TASKS_MILESTONE_MARK_INCOMPLETE'); ?></a>
                            </li>
                            <li class="mark-completed">
                                <a href="javascript:void(0);" data-milestone-mark-complete><?php echo JText::_('APP_EVENT_TASKS_MILESTONE_MARK_COMPLETE'); ?></a>
                            </li>

                            <li class="divider"></li>
                            <li>
                                <a href="javascript:void(0);" data-milestone-delete><?php echo JText::_('APP_EVENT_TASKS_MILESTONE_DELETE'); ?></a>
                            </li>
                        </ul>
                    </span>
                    <?php } ?>
                </div>
                <div>
                    <span class="label label-danger label-due milestone-labels"><?php echo JText::_('APP_EVENT_TASKS_OVERDUE'); ?></span>
                    <span class="label label-success label-completed milestone-labels"><?php echo JText::_('APP_EVENT_TASKS_COMPLETED'); ?></span>
                </div>
                <hr />

                <div class="milestone-desc">
                    <?php echo $milestone->getContent(); ?>
                </div>

                <ul class="nav nav-tabs mt-20">
                    <li class="active">
                        <a href="#open" data-bs-toggle="tab"><?php echo JText::sprintf('APP_EVENT_TASKS_TAB_OPEN_TASKS', '<span data-tasks-open-counter>' . $totalOpen . '</span>'); ?></a>
                    </li>
                    <li>
                        <a href="#closed" data-bs-toggle="tab"><?php echo JText::sprintf('APP_EVENT_TASKS_TAB_COMPLETED_TASKS', '<span data-tasks-closed-counter>' . $totalClosed . '</span>'); ?></a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="open">
                        <div class="milestone-tasks">
                            <ul class="fd-reset-list tasks-list" data-tasks-list>

                                <?php if ($event->getGuest()->isGuest()) { ?>
                                <li class="task-form" data-tasks-form-wrapper>
                                    <form data-tasks-form>
                                    <div class="alert alert-error hide" data-tasks-form-error>
                                        <?php echo JText::_('APP_EVENT_TASKS_EMPTY_TITLE_ERROR'); ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="input-group input-group-sm">
                                                <input class="input-sm form-control" name="" placeholder="<?php echo JText::_('APP_EVENT_TASKS_PLACEHOLDER_TASK_TITLE', true); ?>" data-form-tasks-title />

                                                <span class="input-group-btn">
                                                    <button class="btn btn-es-primary btn-sm" type="button" data-form-tasks-create><?php echo JText::_('APP_EVENT_TASKS_CREATE'); ?></button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row form-meta">
                                        <div class="col-sm-6">
                                            <select class="input-sm form-control" data-form-tasks-assignee>
                                                <option value="0"><?php echo JText::_('APP_EVENT_TASKS_RESPONSIBILITY_OF'); ?></option>

                                                <?php foreach ($members as $member) { ?>
                                                <option value="<?php echo $member->uid; ?>"><?php echo $member->getName(); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <?php echo $this->html('form.calendar', 'due', '', 'due', array('placeholder="' . JText::_('APP_EVENT_TASKS_DUE_DATE_PLACEHOLDER', true) . '"', 'data-form-tasks-due')); ?>
                                        </div>
                                    </div>
                                    </form>
                                </li>
                                <?php } ?>

                                <?php if ($openTasks) { ?>
                                    <?php foreach ($openTasks as $task) { ?>
                                        <?php echo $this->loadTemplate('apps/event/tasks/views/item.task', array('task' => $task, 'user' => FD::user($task->user_id), 'event' => $event)); ?>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-pane" id="closed">
                        <div class="milestone-tasks">
                            <ul class="fd-reset-list tasks-list completed-list" data-tasks-completed>
                                <?php if ($closedTasks) { ?>
                                    <?php foreach ($closedTasks as $task) { ?>
                                        <?php echo $this->loadTemplate('apps/event/tasks/views/item.task', array('task' => $task, 'user' => FD::user($task->user_id), 'event' => $event)); ?>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-sm-4">
                <div class="milestone-meta">
                    <h5><?php echo JText::_('APP_EVENT_TASKS_META_MILESTONE_DETAILS'); ?></h5>
                    <hr />
                    <ul class="fd-reset-list">
                        <?php if ($milestone->hasAssignee()) { ?>
                        <li>
                            <i class="fa fa-user"></i> <?php echo JText::sprintf('APP_EVENT_TASKS_META_CREATED_BY', $this->html('html.user', $milestone->owner_id)); ?>
                        </li>
                        <?php } ?>
                        <li>
                            <i class="fa fa-calendar"></i> <?php echo JText::sprintf('APP_EVENT_TASKS_META_CREATED_ON', FD::date($milestone->created)->format(JText::_('DATE_FORMAT_LC3'))); ?>
                        </li>

                        <?php if ($milestone->hasDueDate()) { ?>
                        <li>
                            <i class="fa fa-calendar"></i> <?php echo JText::sprintf('APP_EVENT_TASKS_META_DUE_ON', FD::date($milestone->due)->format(JText::_('DATE_FORMAT_LC3'))); ?>
                        </li>
                        <?php } ?>
                    </ul>
                </div>

                <?php if ($params->get('display_chart', true)) { ?>
                <div class="milestone-chart mt-20">
                    <h5><?php echo JText::_('APP_EVENT_TASKS_STATISTICS'); ?></h5>
                    <hr />

                    <?php if (!$totalOpen && !$totalClosed) { ?>
                    <div class="mt-10">
                        <?php echo JText::_('APP_EVENT_TASKS_NO_TASKS_YET'); ?>
                    </div>
                    <?php } else { ?>
                    <div class="text-center mt-10">
                        <span data-chart-milestone><?php echo $totalClosed; ?>,<?php echo $totalOpen; ?></span>
                    </div>
                    <div class="milestone-legend">
                        <div class="legend-title"><?php echo JText::_('APP_EVENT_TASKS_CHART_LEGEND'); ?></div>

                        <div class="legend-items">
                            <div class="legend-completed">
                                <span>&nbsp;</span> <?php echo JText::_('APP_EVENT_TASKS_CHART_LEGEND_CLOSED_TASKS'); ?>
                            </div>

                            <div class="legend-incomplete">
                                <span>&nbsp;</span> <?php echo JText::_('APP_EVENT_TASKS_CHART_LEGEND_OPEN_TASKS'); ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>
