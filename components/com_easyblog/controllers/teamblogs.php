<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerTeamBlogs extends EasyBlogController
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Processes requests to join the team
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function join()
	{
		// Check for request forgeries
		EB::checkToken();

		// Only allow registered users
		EB::requireLogin();

		$return = $this->input->get('return', '', 'default');

		if ($return) {
			$return = base64_decode($return);
		}

		// Default return url
		if (!$return) {
			$return = EB::_('index.php?option=com_easyblog&view=teamblog', false);
		}

		// Get the team data
		$id = $this->input->get('id', 0, 'int');

		$team = EB::table('TeamBlog');
		$team->load($id);

		if (!$id || !$team->id) {
			$this->info->set('COM_EASYBLOG_TEAMBLOG_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect($return);
		}

		$model = EB::model('TeamBlogs');
		$isMember = $model->isMember($team->id, $this->my->id);

		// Check if the user already exists
		if ($isMember) {
			$this->info->set('COM_EASYBLOG_TEAMBLOG_ALREADY_MEMBER', 'error');
			return $this->app->redirect($return);
		}

		// If the user is a site admin, they are free to do whatever they want
		if (EB::isSiteAdmin()) {
			$map = EB::table('TeamBlogUsers');
			$map->user_id = $this->my->id;
			$map->team_id = $team->id;
			$map->store();

			$this->info->set('COM_EASYBLOG_TEAMBLOG_REQUEST_JOINED', 'success');
		} else {
			// Create a new request
			$request = EB::table('TeamBlogRequest');
			$request->team_id = $team->id;
			$request->user_id = $this->my->id;
			$request->ispending = true;
			$request->created = EB::date()->toSql();

			// If request was already made previously, skip this
			if ($request->exists()) {
				$this->info->set('COM_EASYBLOG_TEAMBLOG_REQUEST_ALREADY_SENT', 'error');

				return $this->app->redirect($return);
			}

			// Store the request now
			$state = $request->store();

			if (!$state) {
				$this->info->set($request->getError(), 'error');

				return $this->app->redirect($return);
			}

			// Send moderation emails
			$request->sendModerationEmail();

			$this->info->set('COM_EASYBLOG_TEAMBLOG_REQUEST_SENT', 'success');
		}


		return $this->app->redirect($return);
	}

	/**
	 * Allows caller to approve a team request
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function approve()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Get the request id
		$id = $this->input->get('id', 0 , 'int');

		// Load the request
		$request = EB::table('TeamBlogRequest');
		$request->load($id);

		// Default redirection url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=requests', false);
	
		if (!$id || !$request->id) {
			$this->info->set('COM_EASYBLOG_TEAMBLOG_INVALID_ID_PROVIDED');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog', false));
		}

		// Ensure that the user has access to perform this
		if (!$request->canModerate()) {
			$this->info->set('COM_EASYBLOG_TEAMBLOG_MODERATE_NO_ACCESS');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog', false));
		}

		// Approve the request
		$state = $request->approve();

		if (!$state) {
			$this->info->set($request->getError(), 'error');
			return $this->app->redirect($return);
		}

		$this->info->set('COM_EASYBLOG_TEAMBLOG_APPROVAL_APPROVED', 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Allows caller to reject a team request
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function reject()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Get the request id
		$id = $this->input->get('id', 0 , 'int');

		// Load the request
		$request = EB::table('TeamBlogRequest');
		$request->load($id);

		// Default redirection url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=requests', false);
	
		if (!$id || !$request->id) {
			$this->info->set('COM_EASYBLOG_TEAMBLOG_INVALID_ID_PROVIDED');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog', false));
		}

		// Ensure that the user has access to perform this
		if (!$request->canModerate()) {
			$this->info->set('COM_EASYBLOG_TEAMBLOG_MODERATE_NO_ACCESS');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog', false));
		}
		
		// Approve the request
		$state = $request->reject();

		if (!$state) {
			$this->info->set($request->getError(), 'error');
			return $this->app->redirect($return);
		}

		$this->info->set('COM_EASYBLOG_TEAMBLOG_APPROVAL_REJECTED', 'success');
		return $this->app->redirect($return);
	}


	/**
	 * Allows caller to leave a team
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function leave()
    {
    	// Check for request forgeries
    	EB::checkToken();

    	// Ensure that the user is logged in first
    	EB::requireLogin();

		$return = $this->input->get('return', '', 'default');

		if ($return) {
			$return = base64_decode($return);
		}

		// Default return url
		if (!$return) {
			$return = EB::_('index.php?option=com_easyblog&view=teamblog', false);
		}

    	// Get the team object
    	$id = $this->input->get('id', 0, 'int');

    	$team = EB::table('TeamBlog');
    	$team->load($id);

		if (!$id || !$team->id) {
			$this->info->set('COM_EASYBLOG_TEAMBLOG_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect($return);
		}

		// Ensure that the current user requesting to leave the team is really a member of the team
		$model = EB::model('TeamBlogs');
		$isMember = $model->isMember($team->id, $this->my->id);

		if (!$isMember) {
			$this->info->set('COM_EASYBLOG_TEAMBLOG_NOT_MEMBER_OF_TEAM', 'error');
			return $this->app->redirect($return);
		}

		// Get the total members in the team because we do not want to allow empty team members in a team
		$count = $team->getMemberCount();

		if ($count <= 1) {
			$this->info->set('COM_EASYBLOG_TEAMBLOG_YOU_ARE_LAST_MEMBER', 'error');

			return $this->app->redirect($return);
		}

		// Delete the member now
		$team->deleteMembers($this->my->id);

		$this->info->set('COM_EASYBLOG_TEAMBLOG_LEAVE_TEAM_SUCCESS', 'success');
		return $this->app->redirect($return);
	}
}
