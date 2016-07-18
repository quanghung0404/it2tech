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
<div class="app-tasks app-groups" data-group-tasks-milestones data-groupid="<?php echo $group->id;?>">

	<div class="es-filterbar row-table">
		<div class="col-cell filterbar-title"><?php echo JText::_( 'APP_GROUP_TASKS_TITLE' ); ?></div>

		<?php if( $group->isMember() ){ ?>
		<div class="col-cell cell-tight">
			<a href="<?php echo FRoute::apps( array( 'layout' => 'canvas' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'customView' => 'form' ) );?>" class="btn btn-es-primary btn-sm pull-right">
				<?php echo JText::_( 'APP_GROUP_TASKS_NEW_MILESTONE' ); ?>
			</a>
		</div>
		<?php } ?>
	</div>

	<div class="app-contents-wrap">
		<div class="milestone-browser app-contents<?php echo !$milestones ? ' is-empty' : '';?>">
			<?php echo $this->loadTemplate( 'apps/group/tasks/views/default.list' , array( 'milestones' => $milestones , 'group' => $group , 'app' => $app , 'params' => $params ) ); ?>
		</div>
	</div>

</div>
