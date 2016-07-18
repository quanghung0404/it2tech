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


class TasksControllerTasks extends SocialAppsController
{
	/**
	 * Unresolve a task
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unresolve()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Get the ajax object.
		$ajax = FD::ajax();

		// Load the task ORM
		$id = $this->input->get('id', 0, 'int');
		$task = FD::table('Task');
		$state = $task->load($id);

		// Title should never be empty.
		if (!$id || !$state) {
			return $ajax->reject( JText::_( 'APP_USER_TASKS_INVALID_ID_PROVIDED' ) );
		}

		// Title should never be empty.
		if ($task->user_id != $this->my->id) {
			return $ajax->reject( JText::_( 'APP_USER_TASKS_NO_ACCESS' ) );
		}

		if (!$task->unresolve()) {
			return $ajax->reject($task->getError());
		}

		// Return the ajax response.
		return $ajax->resolve();
	}

	/**
	 * When a note is stored, this method would be invoked.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function resolve()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Get the ajax object.
		$ajax 		= FD::ajax();

		// Load the task ORM
		$id = $this->input->get('id', 0, 'int');
		$task = FD::table('Task');
		$state = $task->load($id);

		// Title should never be empty.
		if (!$id || !$state) {
			return $ajax->reject( JText::_( 'APP_USER_TASKS_INVALID_ID_PROVIDED' ) );
		}

		// Title should never be empty.
		if ($task->user_id != $this->my->id) {
			return $ajax->reject(JText::_('APP_USER_TASKS_NO_ACCESS'));
		}

		if (!$task->resolve()) {
			return $ajax->reject($task->getError());
		}

		// Return the ajax response.
		return $ajax->resolve();
	}

	/**
	 * When a note is stored, this method would be invoked.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function save()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Get the ajax object.
		$ajax = FD::ajax();

		$title = JRequest::getVar( 'title' );

		// Title should never be empty.
		if (empty($title)) {
			return $ajax->reject(JText::_('APP_USER_TASKS_EMPTY_TITLE'));
		}

		$task = FD::table('Task');
		$task->title = $title;
		$task->user_id = $this->my->id;

		// By default the state is not done.
		$task->state = 0;

		// Store the note.
		if ($task->store()) {
			// Add stream.
			$stream	= FD::stream();

			$data = $stream->getTemplate();
			$data->setActor($this->my->id, SOCIAL_STREAM_ACTOR_TYPE_USER );
			$data->setContext( $task->id, SOCIAL_STREAM_CONTEXT_TASKS);
			$data->setVerb( 'add' );
			$data->setType( 'mini' );

			$data->setAccess('core.view');
			
			$stream->add($data);
		}

		// Get the theme
		$theme = FD::themes();
		$theme->set('task', $task);
		$contents = $theme->output('apps/user/tasks/dashboard/item');

		// Return the ajax response.
		return $ajax->resolve($contents);
	}

	/**
	 * When a note is stored, this method would be invoked.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function remove()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Get the ajax object.
		$ajax = FD::ajax();

		// Load the task ORM
		$id = $this->input->get('id', 0, 'int');
		$task = FD::table('Task');
		$state = $task->load($id);

		// Title should never be empty.
		if (!$id || !$state) {
			return $ajax->reject( JText::_( 'APP_USER_TASKS_INVALID_ID_PROVIDED' ) );
		}

		// Title should never be empty.
		if ($task->user_id != $this->my->id) {
			return $ajax->reject(JText::_('APP_USER_TASKS_NO_ACCESS'));
		}

		if (!$task->delete()) {
			return $ajax->reject( $task->getError() );
		}

		// Return the ajax response.
		return $ajax->resolve();
	}
}
