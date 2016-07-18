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

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerOAuth extends EasyBlogController
{
	public function __construct($options = array())
	{
		parent::__construct($options);
	}

	/**
	 * Requests a token from the respective oauth client
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function request()
	{
		// Ensure that the user needs to be logged in
		EB::requireLogin();

		// Default redirect url
		$url = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false);

		// Get the redirection url
		$redirect = $this->input->get('redirect', '', 'default');

		if ($redirect) {
			$redirect = '&redirect=' . $redirect;
		}

		// Get the client type
		$client = $this->input->get('client', '', 'cmd');

		// Get the callback url
		$callback = EBR::getRoutedURL('index.php?option=com_easyblog&task=oauth.grant&client=' . $client . $redirect, false, true);

		// Get the consumer
		$consumer = EB::oauth()->getClient($client);
		$consumer->setCallback($callback);

		// Generate a request token
		$request = $consumer->getRequestToken();

		// Ensure that we are getting the request tokens and secret
		if (!$request->token || !$request->secret) {
			$this->info->set(JText::_('COM_EASYBLOG_OAUTH_KEY_INVALID'), 'error');

			return $this->app->redirect($url, false);
		}

		$table = EB::table('OAuth');
		$table->user_id = $this->my->id;
		$table->type = $client;
		$table->created = EB::date()->toSql();
		$table->request_token = json_encode($request);

		// Store the tokens now
		$table->store();

		// Get the request permissions dialog url
		$url = $consumer->getAuthorizeURL($request->token, false, 'popup');

		return $this->app->redirect($url, false);
	}

	/**
	 * Responsible to receive the incoming redirection from the respective oauth sites
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function grant()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Default redirect url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false);

		// Get the client
		$client = $this->input->get('client', '', 'cmd');

		// Get the redirection url
		$redirect = $this->input->get('redirect', '', 'default');

		// Get the redirection url 
		$redirectUri = !empty( $redirect ) ? '&redirect=' . $redirect : '';

		// Let's see if caller wants us to go to any specific location or not.
		if ($redirect) {
			$redirect = base64_decode($redirect);
		}

		// Load the oauth object
		$table = EB::table('OAuth');
		$table->loadByUser($this->my->id, $client);

		if (!$table->id) {
			$this->info->set('COM_EASYBLOG_OAUTH_UNABLE_TO_LOCATE_RECORD', 'error');

			return $this->app->redirect($return);
		}

		// Detect if there's any errors
		$denied = $this->input->get('denied', '', 'default');

		// When there's an error, delete the oauth data
		if ($denied) {
			$table->delete();

			$this->info->set('COM_EASYBLOG_OAUTH_DENIED_ERROR', 'error');
			return $this->app->redirect($return);
		}

		// Get the request token
		$request = json_decode($table->request_token);

		// Get the callback url
		$callback = EBR::getRoutedURL('index.php?option=com_easyblog&task=oauth.grant&client=' . $client . $redirect, false, true);

		// Get the client
		$consumer = EB::oauth()->getClient($client);
		$consumer->setCallback($callback);

		// Get the verifier
		$verifier = $consumer->getVerifier();

		if (!$verifier) {
			$table->delete();

			return $this->app->redirect($return);
		}

		// Get the access token
		$consumer->setRequestToken($request->token, $request->secret);
		$access = $consumer->getAccess($verifier);

		// Since there is a problem with the oauth authentication, we need to delete the existing record.
		if (!$access || !$access->token || !$access->secret) {
			
			$table->delete();

			$this->info->set('COM_EASYBLOG_OAUTH_ACCESS_TOKEN_ERROR', 'error');
	
			return $this->app->redirect($return);
		}

		// Once we have the token, we need to map it back
		$params = EB::registry();
		$params->set('token', $access->token);
		$params->set('secret', $access->secret);

		// Set the expiration date
		if (isset($access->expires)) {
			$table->expires = $access->expires;
		}

		$table->access_token = $params->toString();
		$table->params = $access->params;

		// Store the oauth table now
		$table->store();

		$this->info->set(JText::sprintf('COM_EASYBLOG_OAUTH_SUCCESS_' . strtoupper($client)), 'success');

		echo '<script type="text/javascript">window.opener.doneLogin();window.close();</script>';
		exit;
	}

	/**
	 * Revokes the user's oauth access
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function revoke()
	{
		// Require the user to be logged in
		EB::requireLogin();

		// The default url
		$url = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false);

		// Get any redirection url
		$redirect = $this->input->get('redirect', '', 'default');

		// Get the oauth client
		$type = $this->input->get('client', '', 'cmd');

		// If redirect url is provided, we know for sure where to redirect the user to
		if ($redirect) {
			$url = base64_decode($redirect);
		}

		// Load up the oauth object
		$table = EB::table('OAuth');
		$table->loadByUser($this->my->id, $type);

		// Load up the oauth client and set the access
		$client = EB::oauth()->getClient($type);
		$client->setAccess($table->access_token);

		// Get the callback url
		$callback = EBR::getRoutedURL('index.php?option=com_easyblog&task=oauth.grant&client=' . $type, false, true);

		// Get the consumer and secret key
		$key = $this->config->get('integrations_' . $type . '_api_key');
		$secret = $this->config->get('integrations_' . $type . '_secret_key');

		// Try to revoke the app
		$state = $client->revoke();

		// After revoking the access, delete the oauth record
		$table->delete();

		$this->info->set(JText::_('COM_EASYBLOG_APPLICATION_REVOKED_SUCCESSFULLY'), 'success');
		return $this->app->redirect($url, false);
	}
}
