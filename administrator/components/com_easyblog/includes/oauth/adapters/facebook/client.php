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

require_once(__DIR__ . '/consumer.php');

class EasyBlogClientFacebook extends EasyBlogFacebookConsumer
{
	public $callback 	= '';
	public $token 		= '';
	public $apiKey 		= '';
	public $apiSecret 	= '';

	public function __construct()
	{
		$this->jConfig = EB::jConfig();
		$this->app = JFactory::getApplication();
		$this->input = EB::request();
		$this->config = EB::config();
		$this->apiKey = $this->config->get('integrations_facebook_api_key');
		$this->apiSecret = $this->config->get('integrations_facebook_secret_key');

		// Default redirection url
		$this->redirect = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&task=facebook.grant';

		// Determines if there's a "system" in the url
		$system = $this->input->get('system', false, 'bool');

		if ($system) {
			$this->redirect .= '&system=1';
		}

		// Determines if there's a "userId" in the url
		$userId = $this->input->get('userId', null, 'default');

		if ($userId) {
			$this->redirect .= '&userId=' . $userId;
		}

		parent::__construct(array('appId' => $this->apiKey, 'secret' => $this->apiSecret));
	}

	public function setCallback($url)
	{
		$this->redirect = $url;
	}

	/**
	 * Facebook does not need the request tokens
	 *
	 **/
	public function getRequestToken()
	{
		$obj = new stdClass();
		$obj->token = 'facebook';
		$obj->secret = 'facebook';

		return $obj;
	}

	/**
	 * Returns the verifier option. Since Facebook does not have oauth_verifier,
	 * The only way to validate this is through the 'code' query
	 *
	 * @return string	$verifier	Any string representation that we can verify it isn't empty.
	 **/
	public function getVerifier()
	{
		$verifier	= JRequest::getVar( 'code' , '' );
		return $verifier;
	}

	/**
	 * Retrieves the revoke access button
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRevokeButton($return, $system = false, $userId = false)
	{
		$theme = EB::template();

		$uid = uniqid();

		// Generate the authorize url
		$url = JURI::root() . 'administrator/index.php?option=com_easyblog&task=facebook.revoke';

		if ($system) {
			$url .= '&system=1';
		}

		$url .= '&return=' . base64_encode($return);

		if ($userId) {
			$url .= '&userId=' . $userId;
		}

		$theme->set('url', $url);
		$theme->set('system', $system);
		$theme->set('uid', $uid);

		$output = $theme->output('admin/oauth/facebook/revoke');

		return $output;
	}

	/**
	 * Retrieves the loggin button for Facebook
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLoginButton($return, $system = false, $userId = false)
	{
		$theme = EB::template();

		$uid = uniqid();

		// Generate the authorize url
		$url = JURI::root() . 'administrator/index.php?option=com_easyblog&task=facebook.facebookAuthorize';

		if ($system) {
			$url .= '&system=1';
		}

		if ($userId) {
			$url .= '&userId=' . $userId;
		}

		$theme->set('url', $url);
		$theme->set('system', $system);
		$theme->set('uid', $uid);
		$theme->set('return', $return);

		$output = $theme->output('admin/oauth/facebook/button');

		return $output;
	}

	/**
	 * Returns the authorization url.
	 *
	 * @return string	$url	A link to Facebook's login URL.
	 **/
	public function getAuthorizeURL()
	{
		$scopes = array('publish_actions', 'manage_pages', 'publish_pages', 'user_managed_groups');
		$scopes = implode(',', $scopes);

		$redirect = $this->redirect;

		$redirect = urlencode($redirect);
		$from = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog';

		$url = 'https://facebook.com/dialog/oauth?scope=' . $scopes . '&client_id=' . $this->apiKey . '&redirect_uri=' . $redirect . '&response_type=code&display=popup';

		return $url;
	}

	/**
	 * Javascript to close dialog when call=doneLogin is specified in the URI.
	 *
	 * @access	public
	 */
	public function doneLogin()
	{
		ob_start();
	?>
		<script type="text/javascript">
		window.opener.doneLogin();
		window.close();
		</script>
	<?php
		$contents 	= ob_get_contents();
		ob_end_clean();

		echo $contents;

		exit;
	}

	/**
	 * Exchanges the code with Facebook to get the access token
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exchangeToken($code)
	{
		$params = array( 'client_id' 	=> $this->apiKey,
						 'redirect_uri'	=> $this->redirect,
						 'client_secret'=> $this->apiSecret,
						 'code'			=> $code
						);

		$token = parent::_oauthRequest(parent::getUrl('graph', '/oauth/access_token'), $params);

		// Split the response because it will be access_token=xxx&expires=xxx
		$token = explode('&', $token);

		if (!isset($token[0])) {
			return false;
		}


		// Remove unecessary codes
		$access  = str_ireplace('access_token=', '', $token[0]);

		// Set the expiry date with proper date data
		$expires = EB::date('+2 months')->toSql();

		$obj = new stdClass();
		$obj->token = $access;
		$obj->expires = $expires;

		return $obj;
	}

	/**
	 * Retrieve the extracted content of a blog post that can be formatted to Facebook
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function extractPostData(EasyBlogPost &$post)
	{
		// Prepare the result data
		$data = new stdClass();

		// Get the content's source
		$source = $this->config->get('integrations_facebook_source');

		// Get the blog content
		$data->content = $post->getIntro(true);
		$data->content = strip_tags($data->content);

		// Get the blog's image to be pushed to Facebook
		$data->image = $post->getImage('thumbnail', false , true);

		// var_dump($data->image);exit;

		// If there's no blog image, try to get the image from the content
		if (!$data->image) {

			// lets get full content.
			$fullcontent = $post->getContent('entry');
			$data->image = EB::string()->getImage($fullcontent);
		}

		// If there's still no image, use author's avatar
		if (!$data->image) {
			$author = $post->getAuthor();
			$data->image = $author->getAvatar();
		}

		// if still no image. lets try to get from placeholder.
		if (!$data->image) {
			$data->image = EB::getPlaceholderImage();
		}

		// Format the content so that it respects the length
		$max = $this->config->get('integrations_facebook_blogs_length');

		// Remove adsense codes
		$data->content = EB::adsense()->strip($data->content);

		if ($max && (JString::strlen($data->content) > $max)) {
			$data->content = JString::substr($data->content, 0, $max) . JText::_('COM_EASYBLOG_ELLIPSES');
		}

		// Get the url to the blog
		$data->url = EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $post->id, false, true);

		// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
		$sh404exists = EBR::isSh404Enabled();

		if ($this->app->isAdmin() && $sh404exists) {
			$data->url = EB::getExternalLink('index.php?option=com_easyblog&view=entry&id='. $post->id);

			// We need to remove the /administrator/ from the link
			$data->url = str_ireplace('/administrator/', '/', $data->url);
		}

		return $data;
	}


	/**
	 * Exchanges the request token with the access token
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAccess($verifier = false)
	{
		// Get the code from request
		$code = $this->input->get('code', '', 'default');

		$params = array( 'client_id' => $this->apiKey,
						 'redirect_uri'	=> $this->redirect,
						 'client_secret'=> $this->apiSecret,
						 'code' => $code
						);

		$token = parent::_oauthRequest(parent::getUrl('graph', '/oauth/access_token'), $params);

		// Split the response because it will be access_token=xxx&expires=xxx
		$token = explode('&', $token);

		if (!isset($token[0])) {
			return false;
		}

		// Get the access token
		$access  = $token[0];
		$expires = isset($token[1]) ? $token[1] : '';

		// Remove unecessary codes
		$access  = str_ireplace('access_token=', '', $access);

		// If the expiry date is given
		if ($expires) {
			$expires = str_ireplace('expires=', '', $expires);

			// Set the expiry date with proper date data
			$expires = EB::date(strtotime('now') + $expires)->toSql();
		}

		$obj = new stdClass();
		$obj->token = $access;
		$obj->secret = true;
		$obj->expires = $expires;
		$obj->params = '';

		return $obj;
	}

	/**
	 * Shares the data to facebook
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function share(EasyBlogPost &$post, EasyBlogTableOAuth &$oauth)
	{
		// Get the post data
		$data = $this->extractPostData($post);

		// Construct the params that should be sent to facebook
		$params = array(
						'link' => $data->url,
						'name' => $post->title,
						'description' => $data->content,
						'message' => $post->title,
						'access_token' => $this->token,
						'picture' => $data->image,
						'source' => $data->image
					);

		// If this is not a system auto posting or
		// it's not impersonating anything, just push to the person's timeline
		if (!$oauth->system) {
			$state = parent::api('/me/feed', 'post', $params);

			$state = isset($state['id']) ? true : false;

			return $state;
		}

		// Just auto post the user's normal account
		if (!$this->config->get('integrations_facebook_impersonate_group') && !$this->config->get('integrations_facebook_impersonate_page')) {
			$state = parent::api('/me/feed', 'post', $params);

			$state = isset($state['id']) ? true : false;

			return $state;
		}

		// If it passes here, we know that this is a system posting already. Check if we should impersonate as a group
		if ($this->config->get('integrations_facebook_impersonate_group')) {

			$groups = $this->config->get('integrations_facebook_group_id');
			$groups = explode(',', $groups);

			// Get a list of groups the user can access
			$groupAccess = parent::api('/me/groups', 'GET', array('access_token' => $this->token, 'limit' => 500));


			// Now we need to find the access for the particular group that they want to share
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

		// Determines if we should auto post to a facebook page
		if ($this->config->get('integrations_facebook_impersonate_page')) {
			$pages = $this->config->get('integrations_facebook_page_id');
			$pages = explode(',', $pages);

			// Get a list of pages the user can access
			$pageAccess = parent::api('/me/accounts', array('access_token' => $this->token, 'limit' => 500));

			foreach ($pages as $page) {

				foreach ($pageAccess['data'] as $access) {
					if ($access['id'] == $page) {

						// We need to set the access now to the page's access
						$params['access_token'] = $access['access_token'];

						parent::api('/' . $page . '/feed', 'post', $params);
					}
				}
			}
		}


		return true;
	}

	/**
	 * Retrieves a list of pages
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPages()
	{
		// Get a list of accounts associated to this user
		$result	= parent::api('/me/accounts', array('access_token' => $this->token, 'limit' => 500));

		$pages 	= array();

		if (!$result) {
			return $pages;
		}


		foreach ($result['data'] as $page) {
			$pages[]	= (object) $page;
		}

		return $pages;
	}

	/**
	 * Retrieves a list of pages
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getGroups()
	{
		// Get a list of accounts associated to this user
		$result	= parent::api('/me/groups', array('access_token' => $this->token));
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
	 * Sets the request token
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setRequestToken($token, $secret)
	{
		// Facebook doesn't utilize this
	}

	/**
	 * Sets the access token
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setAccess($access)
	{
		$access = new JRegistry($access);
		$this->token = $access->get('token');
	}

	/**
	 * Revokes application access from Facebook
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */
	public function revoke()
	{
		try {
			$result = parent::api('/me/permissions', 'DELETE', array('access_token' => $this->token));
		} catch(Exception $e) {
			$result = false;
		}

		return $result;
	}

	/**
	 * Overrides the exception method so that we can silently fail
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	protected function throwAPIException($result)
	{
		$e = new EasyBlogFacebookApiException($result);

		$message = $e->getMessage();

		$exception = EB::exception($message);

		throw $exception;
		$this->error = $exception;
	}
}
