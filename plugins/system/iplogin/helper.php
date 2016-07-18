<?php
/**
 * Plugin Helper File
 *
 * @package         IP Login
 * @version         2.1.1PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

NNFrameworkFunctions::loadLanguage('plg_system_iplogin');

/**
 * Plugin to log in automatically by IP address
 */
class PlgSystemIPLoginHelper
{
	public function __construct(&$params)
	{
		JFormHelper::addFieldPath(__DIR__ . '/fields');

		$this->params = $params;
	}

	public function logIn()
	{
		$url_query = $this->getUrlQuery();

		// Return if there is no URL query
		if (empty($url_query))
		{
			return;
		}

		$user = JFactory::getUser();
		if (!$user->guest)
		{
			// If logged in, remove key from URL
			$this->removeKeyFromLoggedInURL($user->params);

			return;
		}

		// If not logged in, try to log in and remove key from URL
		$this->logInUser();
	}

	public function removeKeyFromLoggedInURL($user_params)
	{
		if (
			!$this->params->remove_key
			|| ($this->params->remove_key == 'admin' && !JFactory::getApplication()->isAdmin())
			|| ($this->params->remove_key == 'site' && !JFactory::getApplication()->isSite())
		)
		{
			return;
		}

		$url_query = $this->getUrlQuery();

		$user_params = (array) json_decode($user_params, true);

		$max = 5;
		for ($i = 1; $i <= $max; $i++)
		{
			$id = 'ip' . $i;

			// Check if the user key for this IP is present in the URL
			if (!isset($user_params[$id . '_key']) || trim($user_params[$id . '_key']) == '' || !in_array(trim($user_params[$id . '_key']), $url_query))
			{
				continue;
			}

			// Remove the key from the url
			$url = $this->removeKeyFromURL($user_params[$id . '_key']);

			// Redirect
			JFactory::getApplication()->redirect($url);

			return;
		}
	}

	public function removeKeyFromURL($key)
	{

		$uri       = JUri::getInstance();
		$url       = $uri->current();
		$url_query = $this->getUrlQuery();

		// Remove key from query array if settings allow it
		if (
			$this->params->remove_key
			|| ($this->params->remove_key == 'admin' && JFactory::getApplication()->isAdmin())
			|| ($this->params->remove_key == 'site' && JFactory::getApplication()->isSite())
		)
		{
			$url_query = array_diff($url_query, array($key));
		}

		// Add query to url
		if (!empty($url_query))
		{
			$url .= '?' . implode('&', $url_query);
		}

		return $url;
	}

	public function logInUser()
	{
		$ip = $_SERVER['REMOTE_ADDR'];

		// Return if no IP address can be found (shouldn't happen, but who knows)
		if (empty($ip))
		{
			return;
		}

		list($user, $key) = $this->findUserByIPKey($ip);

		if (!$user)
		{
			return;
		}

		if (!$user->id > 0)
		{
			return;
		}

		// Remove the key from the url
		$url = $this->removeKeyFromURL($key);
		$this->redirect($url, $user);
	}

	function redirect($url, $user)
	{
		// Construct the options
		$options             = array();
		$options['remember'] = true;
		$options['return']   = $url;
		$options['action']   = 'core.login.' . JFactory::getApplication()->getName();

		// Construct a response
		jimport('joomla.user.authentication');
		JPluginHelper::importPlugin('authentication');
		JPluginHelper::importPlugin('user');
		$authenticate = JAuthentication::getInstance();

		// Construct the response-object
		$response                = new JAuthenticationResponse;
		$response->type          = 'Joomla';
		$response->email         = $user->email;
		$response->fullname      = $user->name;
		$response->username      = $user->username;
		$response->password      = $user->username;
		$response->language      = $user->getParam('language');
		$response->status        = JAuthentication::STATUS_SUCCESS;
		$response->error_message = null;

		// Authorise this response
		$authenticate->authorise($response, $options);

		// Run the login-event
		JFactory::getApplication()->triggerEvent('onUserLogin', array((array) $response, $options));

		// Redirect
		JFactory::getApplication()->redirect($url);
	}

	public function getUsersByIP($ip)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('u.id, u.params')
			->from('#__users AS u')
			->where('u.block = 0')
			->where('u.activation = 0')
			->where('u.params LIKE ' . $db->quote('%_ip":"' . $ip . '"%'));
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function findUserByIPKey($ip)
	{
		$users = $this->getUsersByIP($ip);

		$user_id = 0;

		foreach ($users as $user)
		{
			list($user_id, $key) = $this->userPassesIPKey($user, $ip);

			if ($user_id)
			{
				break;
			}
		}

		if (!$user_id)
		{
			return array(0, 0);
		}

		// Load the user
		$user = JFactory::getUser();
		$user->load($user_id);

		return array($user, $key);
	}

	public function userPassesIPKey($user, $ip)
	{
		$params = (array) json_decode($user->params, true);

		$i = 1;
		for ($i; $i <= 5; $i++)
		{
			$user_ip  = isset($params['ip' . $i . '_ip']) ? trim($params['ip' . $i . '_ip']) : '';
			$user_key = isset($params['ip' . $i . '_key']) ? trim($params['ip' . $i . '_key']) : '';

			$pass = $this->userSettingPassesIPKey($user_ip, $user_key, $ip);

			if ($pass)
			{
				return array($user->id, $user_key);
			}
		}

		return array(0, 0);
	}

	public function userSettingPassesIPKey($user_ip, $user_key, $ip)
	{
		// Check if the user IP setting matches the visitors IP
		if ($user_ip != $ip)
		{
			return false;
		}

		// Check if the user key for this IP is present in the URL
		if (!in_array(trim($user_key), array_keys(JFactory::getApplication()->input->getArray($_REQUEST))))
		{
			return false;
		}

		return true;
	}

	public function addFieldsToUserForm($form)
	{
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}

		// Check we are manipulating a valid form.
		$name = $form->getName();
		if (!in_array($name, array('com_admin.profile', 'com_users.user', 'com_users.profile', 'com_users.registration')))
		{
			return true;
		}

		// load the admin language file
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('plg_system_iplogin');

		// Add the registration fields to the form.
		JForm::addFormPath(__DIR__ . '/form');
		$form->loadFile('ips', false);

		return true;
	}

	public function getUrlQuery()
	{
		return explode('&', JUri::getInstance()->getQuery());
	}
}
