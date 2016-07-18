<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerLinkedIn extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Retrieves the authorization url and redirect accordingly
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function linkedinAuthorize()
	{
		// Get the client
		$client = EB::oauth()->getClient('LinkedIn');

		// Determines if this is for the centralized section
		$system = $this->input->get('system', false, 'bool');

		// Get the user id
		$userId = $this->input->get('userId', null, 'default');

		if (is_null($userId)) {
			$userId = $this->my->id;
		}

		// Get the redirection url
		$token = $client->getRequestToken();
		$url = $client->getAuthorizeURL($token->token);

		// Because twitter is being a PITA, we need to store these request tokens locally first.
		// Create a new table for record
		$table = EB::table('OAuth');

		if ($system) {
			$table->load(array('type' => 'linkedin', 'system' => true));
		} else {
			$table->load(array('type' => 'linkedin', 'user_id' => $userId, 'system' => false));
		}

		$table->user_id = $userId;
		$table->type = 'linkedin';
		$table->created = EB::date()->toSql();

		if ($system) {
			$table->system = 1;
		}

		// Store the request tokens here
		$table->request_token = json_encode($token);
		$table->store();

		$this->app->redirect($url);
	}

	/**
	 * Method to process redirections from google
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function grant()
	{
		// Get the twitter client
		$client = EB::oauth()->getClient('Linkedin');

		// Now we need to load up the stored request tokens
		$table = EB::table('OAuth');

		// Determines if this is a system request
		$system = $this->input->get('system', false, 'bool');

		if ($system) {
			$table->load(array('type' => 'linkedin', 'system' => 1));
		} else {
			$userId = $this->input->get('userId', null, 'default');

			if (!$userId) {
				$userId = $this->my->id;
			}

			$table->load(array('type' => 'linkedin', 'user_id' => $userId, 'system' => false));
		}

		$requestTokens = json_decode($table->request_token);

		// Get the access token now
		$client->setRequestToken($requestTokens->token, $requestTokens->secret);

		// Get the access token now
		$result = $client->getAccess();

		$accessToken = new stdClass();
		$accessToken->token  = $result->token;
		$accessToken->secret = $result->secret;

		// Set the access token now
		$table->access_token = json_encode($accessToken);

		// Set the params
		$table->params = json_encode($result);


		$table->store();

		// Since the page that is redirected to here is a popup, we need to close the window
		EB::info()->set(JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_AUTHORIZED_SUCCESS'), 'success');

		echo '<script type="text/javascript">window.opener.doneLogin();window.close();</script>';
		exit;
	}

	/**
	 * Revokes the access
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function revoke()
	{
		// Check for acl rules.
		$this->checkAccess('autoposting');

		$table 	= EB::table('OAuth');

		// Determines if this request is a system request
		$system = $this->input->get('system', false, 'bool');

		// Determines if this request is to revoke a user's access
		$userId = $this->input->get('userId', null, 'default');

		$table = EB::table('OAuth');

		if ($system) {
			$table->load(array('type' => 'linkedin', 'system' => true));
		} else {
			$table->load(array('type' => 'linkedin', 'user_id' => $userId, 'system' => false));
		}

		// Get the return url
		$return = $this->input->get('return', '', 'default');
		$return = base64_decode($return);

		// Get the client
		$client = EB::oauth()->getClient('Linkedin');
		$client->setAccess($table->access_token);

		// Revoke the access
		$state	= $client->revoke();

		// Regardless of the state, delete the record.
		$table->delete();

		// If there's a problem revoking the app, just delete the record and let the user know
		EB::info()->set(JText::_('COM_EASYBLOG_AUTOPOST_LINKEDIN_SUCCESS_REVOKING_ACCESS'), 'success');


		$redirect = 'index.php?option=com_easyblog&view=autoposting&layout=linkedin';

		if ($return) {
			$redirect = $return;
		}

		$this->app->redirect($redirect);
	}

	/**
	 * Saves the google auto posting settings
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the client id
		$post = $this->input->getArray('post');

		unset($post['task']);
		unset($post['option']);
		unset($post[EB::getToken()]);

		// Get the model so that we can store the settings
		$model = EB::model('Settings');
		$model->save($post);

		// Redirect the user
		EB::info()->set(JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_SAVE_SUCCESS'), 'success');

		$this->app->redirect('index.php?option=com_easyblog&view=autoposting&layout=linkedin');
	}
}
