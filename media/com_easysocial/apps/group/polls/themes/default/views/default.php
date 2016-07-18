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
<div class="app-tasks app-groups" data-group-tasks-polls data-groupid="<?php echo $group->id;?>">
    <div class="es-filterbar row-table">
        <div class="col-cell filterbar-title">Recent Polls</div>
    </div>

	<div class="app-contents-wrap">
		<div class="milestone-browser app-contents is-empty<?php echo !$polls ? ' is-empty' : '';?>">
			<?php echo $this->loadTemplate( 'apps/group/polls/views/default.list' , array( 'polls' => $polls , 'group' => $group , 'app' => $app , 'params' => $params ) ); ?>
		</div>
	</div>

</div>
