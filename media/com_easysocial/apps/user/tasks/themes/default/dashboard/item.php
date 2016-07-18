<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<li class="taskItem all<?php echo $task->state == 2 ? ' is-resolved' : ' is-unresolved';?>" data-tasks-item data-id="<?php echo $task->id;?>">
	<div class="clearfix">
		<div class="pull-left">
			<input type="checkbox" class="mr-10" id="task-<?php echo $task->id;?>" data-tasks-item-checkbox <?php echo $task->state == 2 ? 'checked="checked" ' : '';?>/>

			<span class="task-title"><?php echo $task->get( 'title' ); ?></span>

		</div>

		<div class="pull-right task-stats">
			<span class="btn-group">
				<a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_ btn btn-dropdown">
					<i class="icon-es-dropdown"></i>
				</a>
				<ul class="dropdown-menu dropdown-menu-user messageDropDown">
					<li>
						<a href="javascript:void(0);" data-tasks-item-remove><?php echo JText::_( 'APP_USER_TASKS_DELETE_TASK' );?></a>
					</li>
				</ul>
			</span>

			<span class="label label-success"><?php echo JText::_( 'APP_USER_TASKS_RESOLVED' ); ?></span>

			<span class="task-time">
				<i class="fa fa-clock-o "></i> <?php echo FD::date( $task->created )->toLapsed(); ?>
			</span>

		</div>
	</div>
</li>
