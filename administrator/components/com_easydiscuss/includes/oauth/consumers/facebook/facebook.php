<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/consumer.php');

class EasyDiscussFacebook extends Facebook
{
	public $key = null;
	public $secret = null;
	public $callback = null;

	public $userAccessToken = null;

	public function __construct($key = '', $secret = '', $callback = '')
	{
		$config = ED::config();

		if (!$key) {
			$key = $config->get('main_autopost_facebook_id');
		}

		if (!$secret) {
			$secret = $config->get('main_autopost_facebook_secret');
		}

		if (!$callback) {
			$callback = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easydiscuss&controller=autoposting&task=grant&type=facebook';
		}

		$this->key = $key;
		$this->secret = $secret;
		$this->callback	= $callback;

		$options = array('appId' => $key, 'secret' => $secret);

		parent::__construct($options);
	}

	/**
	 * Retrieves the callback url
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCallbackUrl()
	{
		return $this->callback;
	}

	/**
	 * Generates a request token
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRequestToken()
	{
		$obj = new stdClass();
		$obj->token = 'facebook';
		$obj->secret = 'facebook';

		return $obj;
	}

	/**
	 * Get the verifier code that is sent back by Facebook
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getVerifier()
	{
		$verifier = JRequest::getVar('code', '');

		return $verifier;
	}

	/**
	 * Retrieves the authorization end point url
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAuthorizationURL()
	{
		$scopes = array('publish_actions', 'manage_pages', 'publish_pages', 'user_managed_groups');
		$scopes = implode(',', $scopes);

		$url = 'https://facebook.com/dialog/oauth?scope=' . $scopes . '&client_id=' . $this->key . '&redirect_uri=' . urlencode($this->callback) . '&response_type=code&display=popup';
		return $url;
	}

	/**
	 * Retrieves the access token given the request token, secret and verifier code.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAccessTokens($token, $secret, $verifier)
	{
		$options = array('client_id' => $this->key, 'client_secret' => $this->secret, 'redirect_uri' => $this->callback, 'code' => $verifier);

		// Make a request to Facebook to get the access tokens
		$accessToken = parent::_oauthRequest(parent::getUrl('graph', '/oauth/access_token'), $options);

		// The result could be a json string with error message
		$result = json_decode($accessToken);

		if ($result && isset($result->error)) {
			return false;
		}

		// Split the response because it will be access_token=xxx&expires=xxx
		$token = explode('&', $accessToken);

		// Check if there is any values.
		if (!isset($token[0])) {
			return false;
		}

		// Get the access token
		$access = $token[0];

		// Remove unecessary codes
		$access = str_ireplace('access_token=', '', $access);

		// Get expiry date
		$expires = isset($token[1]) ? $token[1] : '';

		// If the expiry date is given
		if ($expires) {
			$expires = str_ireplace('expires=', '', $expires);

			// Set the expiry date with proper date data
			$expires = ED::date(strtotime('now') + $expires)->toSql();
		}

		$obj = new stdClass();
		$obj->token	= $access;
		$obj->secret = true;
		$obj->expires = $expires;
		$obj->params = '';

		return $obj;
	}

	/**
	 * Shares a content on facebook
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function share(EasyDiscussPost $post)
	{
		$config = ED::config();

		// Get the data
		$data = $this->getContentObject($post);

		$params = array(
						'link' => $data->permalink,
						'name' => $data->title,
						'actions' => '{"name": "' . JText::_('COM_EASYDISCUSS_AUTOPOST_FB_VIEWON_BUTTON') . '", "link" : "' . $data->permalink . '"}',
						'description' => $data->contents,
						'message' => $data->contents . '...',
						'access_token' => $this->userAccessToken,
						'picture' => $data->image
		);

		// If there is no image for this post
		if (!$params['picture']) {
			$params['picture'] = DISCUSS_JURIROOT . '/media/com_easydiscuss/images/default_facebook.png';
			$params['source'] = rtrim(JURI::root() , '/' ) . '/media/com_easydiscuss/images/default_facebook.png';
		}

		// Autopost to user's normal account
		if (!$config->get('main_autopost_facebook_page_id') && !$config->get('main_autopost_facebook_group')) {

			$result = parent::api('/me/feed', 'post', $params);
			$success = isset($result['id']) ? true : false;

			return $success;
		}

		// If it passes here, we know that it will autopost to groups or page.
		// Let's check for group first.
		if ($config->get('main_autopost_facebook_group')) {

			$groups = $config->get('main_autopost_facebook_group_id');
			$groups = explode(',', $groups);

			// Get a list of groups the user can access
			$groupAccess = parent::api('/me/groups', 'GET', array('access_token' => $this->userAccessToken, 'limit' => 500));

			// We now need to find the acccess for the particular group that they want to share
			if (isset($groupAccess['data']) && $groupAccess) {

				// We need to ensure that the user really has access to the group
				foreach ($groups as $group) {
					foreach ($groupAccess['data'] as $access) {
						if ($access['id'] == $group) {
							$result = parent::api('/' . $group . '/feed', 'post', $params);
						}
					}
				}
			}
		}

		// Let's check for the facebook pages.
		if ($config->get( 'main_autopost_facebook_page')) {
			$pages = $config->get('main_autopost_facebook_page_id');
			$pages = explode(',', $pages);

			// @rule: Test if there are any pages at all the user can access
			$accounts = parent::api('/me/accounts', array('access_token' => $this->userAccessToken));

			foreach ($pages as $page) {
				foreach ($accounts['data'] as $access) {
					if ($access['id'] == $page) {

						// We need to set the access now to the page's access
						$params['access_token'] = $access['access_token'];

						// Let's autopost to page  now.
						$result = parent::api('/' . $page . '/feed', 'post', $params);
					}
				}
			}
		}

		$success = isset($result['id']) ? true : false;

		return $success;
	}

	/**
	 * Formats the content for facebook post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getContentObject(EasyDiscussPost $post)
	{
		$config = ED::config();

		$obj = new stdClass();

		// Get the title to use
		$obj->title = $post->title;

		// Get the content to be posted on Facebook
		$obj->contents = $post->getContent();

		// Try to find an image from the post
		$obj->image = ED::string()->getImage($obj->contents);

		// Truncate the content based on the maximum length
		$maxLength = (int) $config->get('main_autopost_facebook_max_content', 200);

		$obj->contents = strip_tags($obj->contents);
		$obj->contents = JString::substr($obj->contents, 0, $maxLength);

		$obj->contents .= JText::_('COM_EASYDISCUSS_ELLIPSES');

		$obj->permalink = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $post->id, false, true);

		return $obj;
	}

	/**
	 * Set the access tokens
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setAccess($access)
	{
		$access = json_decode($access);

		$this->userAccessToken = $access->token;
	}

	/**
	 * Allows caller to revoke the access which was given by Facebook
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function revokeApp()
	{
		try {
			$result = parent::api('/me/permissions', 'DELETE', array('access_token' => $this->userAccessToken));
		} catch(Exception $e) {

			$result = false;
		}

		return $result;
	}

	/**
	 * Retrieves a list of groups the user owns.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getGroups()
	{
		// Get a list of accounts associated to this user
		$result	= parent::api('/me/groups', array('access_token' => $this->userAccessToken, 'limit' => 999999));

		$groups = array();

		if (!$result) {
			return $groups;
		}

		foreach ($result['data'] as $group) {
			$groups[] = (object) $group;
		}

		return $groups;
	}

	/**
	 * Retrieves a list of pages a user owns.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPages()
	{
		// Get a list of pages associated to this user
		$result = parent::api('/me/accounts', array('access_token' => $this->userAccessToken, 'limit' => 999999));

		$pages = array();

		if (!$result) {
			return $pages;
		}

		foreach ($result['data'] as $page) {
			$pages[] = (object) $page;
		}

		return $pages;
	}
}
