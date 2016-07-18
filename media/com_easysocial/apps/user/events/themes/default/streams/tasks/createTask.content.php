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
<div class="tasks-stream">
    <p>
        <?php echo $stream->content; ?>
    </p>

    <h5><?php echo JText::_('APP_EVENT_TASKS_STREAM_CONTENT_TASKS'); ?></h5>
    <ul class="milestone-tasks fd-reset-list">
        <?php foreach ($tasks as $task) { ?>
        <li class="<?php echo $task->isCompleted() ? 'completed' : ''; ?>">
            <div class="checkbox">
                <input type="checkbox" value="<?php echo $task->id; ?>" data-task-<?php echo $stream->uid; ?>-checkbox <?php echo $task->isCompleted() ? ' checked="checked"' : ''; ?>/>
                <span><?php echo $task->title; ?></span>
            </div>
        </li>
        <?php } ?>
    </ul>
</div>
