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

/**
 * Dashboard view for Tasks app.
 *
 * @since	1.0
 * @access	public
 */
class TasksViewDashboard extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display($userId = null, $docType = null)
	{
		// Obtain the tasks model.
		$model = FD::model('Tasks');

		// Get the list of items
		$result = $model->getItems($userId);

		// If there are tasks, we need to bind them with the table.
		$tasks = array();

		if ($result) {
			
			foreach ($result as $row) {
				
				$task = FD::table('Task');
				$task->bind($row);

				$tasks[] = $task;
			}
		}

		$this->set('tasks', $tasks);

		echo parent::display('dashboard/default');
	}
}
