<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('site:/controllers/controller');

class EasySocialControllerUploader extends EasySocialController
{
	/**
	 * Allows caller to temporarily upload a file
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function uploadTemporary()
	{
		// Check for request forgeries.
		$this->checkToken();

		// Only registered users are allowed here.
		$this->requireLogin();

		// Get the type of storage
		$type = $this->input->get('type', '', 'word');

		// Get the limit
		$limit = $this->config->get($type . '.attachments.maxsize');

		// Set uploader options
		$options = array('name' => 'file', 'maxsize' => $limit . 'M');

		// Get uploaded file
		$uploader = FD::uploader($options);
		$data = $uploader->getFile();

		// If there was an error getting uploaded file, stop.
		if ($data instanceof SocialException) {
			$this->view->setMessage($data);
			return $this->view->call(__FUNCTION__);
		}

		if (!$data) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_UPLOADER_FILE_DID_NOT_GET_UPLOADED'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		// Let's get the temporary uploader table.
		$uploader = ES::table('Uploader');
		$uploader->user_id = $this->my->id;

		// Bind the data on the uploader
		$uploader->bindFile($data);

		// Try to save the uploader
		$state = $uploader->store();

		if (!$state) {
			$this->view->setMessage($uploader->getError(), SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__, $uploader);
		}

		return $this->view->call(__FUNCTION__, $uploader);
	}

	/**
	 * Deletes a file from the system.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function delete()
	{
		// Check for request forgeries
		ES::checkToken();

		// Only logged in users are allowed to delete anything
		ES::requireLogin();

		// Get the uploader id
		$id = $this->input->get('id', 0, 'int');

		$uploader = ES::table('Uploader');
		$uploader->load($id);

		// Check if the user is really permitted to delete the item
		if (!$id || !$uploader->id || $uploader->user_id != $this->my->id) {
			return $this->view->call(__FUNCTION__);
		}

		$state = $uploader->delete();

		return $this->view->call(__FUNCTION__);
	}

}
