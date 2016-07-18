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
<div class="app-tasks app-group">
    <div class="es-content create-task">
        <div class="es-content-wrap">
            <form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form">

                <?php if ($milestone->id) { ?>
                    <h3><?php echo JText::_('APP_EVENT_TASKS_EDITING_MILESTONE'); ?></h3>
                <?php } else { ?>
                    <h3><?php echo JText::_('APP_EVENT_TASKS_CREATE_MILESTONE'); ?></h3>
                <?php } ?>

                <hr />

                <div>
                    <div class="form-group">
                        <input type="text" name="title" value="<?php echo $this->html('string.escape', $milestone->title); ?>"
                            placeholder="<?php echo JText::_('APP_EVENT_TASKS_MILESTONE_TITLE_PLACEHOLDER', true); ?>" class="form-control input-sm "
                        />
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <select name="user_id" class="input-sm form-control">
                                <option value="0"><?php echo JText::_('APP_EVENT_TASKS_RESPONSIBILITY_OF'); ?></option>

                                <?php foreach ($members as $member) { ?>
                                <option value="<?php echo $member->uid; ?>"><?php echo $member->getName(); ?></option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="col-sm-6">
                            <?php echo $this->html('form.calendar', 'due', $milestone->due, 'due', array('placeholder="' . JText::_('APP_EVENT_TASKS_DUE_DATE_FOR_MILESTONE', true) . '"')); ?>
                        </div>
                    </div>

                    <div class="mt-15" style="clear:both;">
                        <div class="editor-wrap fd-cf">
                            <?php echo FD::bbcode()->editor('description', $milestone->description, array('uid' => $event->id, 'type' => SOCIAL_TYPE_EVENT)); ?>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?php echo FRoute::events(array('layout' => 'item', 'id' => $event->getAlias())); ?>" class="pull-left btn btn-es-danger btn-sm"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></a>
                    <button type="submit" class="pull-right btn btn-es-primary btn-sm"><?php echo JText::_('COM_EASYSOCIAL_SUBMIT_BUTTON'); ?> &rarr;</button>
                </div>

                <?php echo $this->html('form.token'); ?>
                <input type="hidden" name="controller" value="apps" />
                <input type="hidden" name="task" value="controller" />
                <input type="hidden" name="appController" value="milestone" />
                <input type="hidden" name="appTask" value="save" />
                <input type="hidden" name="appId" value="<?php echo $app->id; ?>" />
                <input type="hidden" name="cluster_id" value="<?php echo $event->id; ?>" />
                <input type="hidden" name="id" value="<?php echo $milestone->id; ?>" />
            </form>
        </div>
    </div>
</div>
