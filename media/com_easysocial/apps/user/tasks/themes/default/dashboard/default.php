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
<div class="app-tasks" data-tasks>
	<div class="es-filterbar row-table">
		<div class="col-cell filterbar-title"><?php echo JText::_( 'APP_USER_TASKS_MANAGE_TASKS' ); ?></div>

		<div class="col-cell cell-tight">
			<a class="btn btn-es-primary btn-sm pull-right" href="javascript:void(0);" data-tasks-create><?php echo JText::_( 'APP_USER_TASKS_NEW_TASK_BUTTON' ); ?></a>
		</div>
	</div>

	<div class="app-contents<?php echo !$tasks ? ' is-empty' : '';?>" data-app-contents>
		<p class="app-info">
			<?php echo JText::_( 'APP_USER_TASKS_DASHBOARD_INFO' ); ?>
		</p>

		<ul class="fd-nav tab-pills">
			<li data-tasks-filter data-filter="all">
				<a href="javascript:void(0);" class="active"><?php echo JText::_( 'APP_USER_TASKS_FILTER_ALL' ); ?></a>
			</li>
			<li data-tasks-filter data-filter="is-resolved">
				<a href="javascript:void(0);"><?php echo JText::_( 'APP_USER_TASKS_FILTER_RESOLVED' ); ?></a>
			</li>
			<li data-tasks-filter data-filter="is-unresolved">
				<a href="javascript:void(0);"><?php echo JText::_( 'APP_USER_TASKS_FILTER_UNRESOLVED' ); ?></a>
			</li>
		</ul>


		<div class="app-contents-data">
			<ul class="list-unstyled tasks-list mt-20 ml-0" data-tasks-lists>
				<?php if( $tasks ){ ?>
					<?php foreach( $tasks as $task ){ ?>
						<?php echo $this->loadTemplate( 'themes:/apps/user/tasks/dashboard/item' , array( 'task' => $task ) ); ?>
					<?php } ?>
				<?php } ?>
			</ul>
		</div>

		<div class="empty" data-tasks-empty>
			<i class="fa fa-checkbox"></i>
			<?php echo JText::_( 'APP_USER_TASKS_NO_TASKS_YET' ); ?>
		</div>
	</div>

</div>
