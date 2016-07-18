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


require_once(dirname(__FILE__) . '/consumer.php');

class EasyBlogClientTwitter extends EasyBlogTwitterOAuth
{
	public $callback = '';

	public function __construct()
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->config = EB::config();
		$this->apiKey = $this->config->get('integrations_twitter_api_key');
		$this->apiSecret = $this->config->get('integrations_twitter_secret_key');

		// Set the redirection callback for Twitter
		$this->redirect	= rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&task=twitter.grant';

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

		parent::__construct($this->apiKey, $this->apiSecret);
	}

	public function setCallback($url)
	{
		$this->redirect = $url;
	}

	public function getRequestToken($oauth_callback = NULL)
	{
		$request = parent::getRequestToken($this->redirect);

		$obj = new stdClass();
		$obj->token = $request['oauth_token'];
		$obj->secret = $request['oauth_token_secret'];

		return $obj;
	}

	/**
	 * Retrieves the authorization url for twitter
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAuthorizeURL($token, $autoSignIn = false)
	{
		$url = parent::getAuthorizeURL($token, $autoSignIn);

		return $url;
	}

	/**
	 * Retrieves the verifier code
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getVerifier()
	{
		$verifier = $this->input->get('oauth_verifier', '');

		return $verifier;
	}

	/**
	 * Exchanges the request token with the access token
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAccess()
	{
		$verifier = $this->getVerifier();
		$access = parent::getAccessToken($verifier);

		// If we can't get it, don't do anything
		if (empty($access['oauth_token']) && empty($access['oauth_token_secret'])) {
			return false;
		}

		$obj = new stdClass();

		$obj->token	= $access['oauth_token'];
		$obj->secret = $access['oauth_token_secret'];

		$param = EB::registry();
		$param->set('user_id', $access['user_id']);
		$param->set('screen_name', $access['screen_name']);

		$obj->params = $param->toString();

		return $obj;
	}

	/**
	 * Shares a new content on twitter
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function share(EasyBlogPost &$post, EasyBlogTableOAuth &$oauth)
	{
		// Format the content
		$message = $this->formatMessage($post, $oauth);
		$params = array('status' => $message);

		// Try to post to Twitter now
		$result = $this->post('statuses/update', $params);

 		// For issues with unable to authenticate error, somehow they return errors instead of error.
		if (isset($result->errors[0]->message)) {
			return false;
		}

		//for others error that is not authentication issue.
		if (isset($result->error)) {
			return false;
		}

		return true;
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
		$url = JURI::root() . 'administrator/index.php?option=com_easyblog&task=twitter.revoke';

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

		$output = $theme->output('admin/oauth/twitter/revoke');

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
		$url = JURI::root() . 'administrator/index.php?option=com_easyblog&task=twitter.twitterAuthorize';

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

		$output = $theme->output('admin/oauth/twitter/button');

		return $output;
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
		$this->token = new OAuthConsumer($token, $secret);
	}

	/**
	 * Search for tweets given the hash tags
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function search($hashtags, $lastImport = null)
	{
		$params = array();

		if ($lastImport) {
			$params['since_id'] = $lastImport;
		}

		$result = $this->get('statuses/user_timeline', $params);
		$tweets = array();
		$processed = array();

		foreach ($result as $tweet) {
			foreach ($hashtags as $hashtag) {
				if (JString::stristr($tweet->text, $hashtag) !== false && !in_array($tweet->id_str, $processed)) {
					$tweets[] = $tweet;

					$processed[] = $tweet->id_str;
				}
			}
		}

		return $tweets;
	}

	public function setAccess($access)
	{
		$access = EB::registry($access);
		$this->token = new OAuthConsumer($access->get('token'), $access->get( 'secret'));
		return $this->token;
	}

	/**
	 * Revokes twitter access
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function revoke()
	{
		// At this point of time, twitter doesn't have any way to revoke the access tokens that was provided to the system
		return true;
	}

	/**
	 * Formats the message to be published on Twitter
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function formatMessage(EasyBlogPost &$post, EasyBlogTableOAuth &$oauth)
	{
		$config = EB::config();

		// Get the message template to use to push to Twitter
		$content = !empty($oauth->message) ? $oauth->message : $this->config->get('main_twitter_message');

		// Default vars to search / replace
		$search = array();
		$replace = array();

		// Replace the {title} tag
		if (JString::stristr($content, '{title}') !== false) {
			$search[] = '{title}';
			$replace[] = $post->title;
		}

		// Replace the {introtext} tag
		if (JString::stristr($content, '{introtext}') !== false) {
			$search[] = '{introtext}';
			$introText = $post->getIntro(EASYBLOG_STRIP_TAGS);
			// Prevent the introtext character more than 140
			$introText = JString::substr(strip_tags($introText), 0, 50) . JText::_('COM_EASYBLOG_ELLIPSES');
		    $replace[] = $introText;
		}

		// Replace the {category} tag
		if (JString::stristr($content, '{category}') !== false) {

			// Get the primary category of the blog post
			$category = $post->getPrimaryCategory();

			$search[] = '{category}';
		    $replace[] = $category->title;
		}

		// Get the final content
		$content = JString::str_ireplace($search, $replace, $content);

		// Replace the {link} tag
		if (JString::stristr($content, '{link}') !== false) {

			// Twitter will automatically shorten urls and a link will have a maximum of 22 chars
			// which leaves us with an offset of 118 characters
			$length = 22;
			$link = EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $post->id, false, true);

			// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
			$sh404exists = EBR::isSh404Enabled();

			if ($this->app->isAdmin() && $sh404exists) {
				$link = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=entry&id=' . $post->id;
			}

			if ($config->get('integrations_twitter_shorten_url')) {
				$shortenerApiKey = $config->get('integrations_twitter_urlshortener_apikey');
				$shortenerApiKey = trim($shortenerApiKey);

				if (!empty($shortenerApiKey)) {
					$result = EB::UrlShortener()->make_short_url($shortenerApiKey, $link);

					if ($result !== false && $result) {
						$link = $result;
						$length	= strlen($link);
					}
				}
			}

			// Get the remaining length that we can use.
			// Avoid 'error 186' at 136 characters or more
			$remaining = 135 - $length;

			// Split the message
			$parts = explode('{link}', $content);

			for ($i = 0; $i < count($parts); $i++) {

				$tmp =& $parts[$i];
				$tmpLength = JString::strlen($tmp);

				if ($tmpLength > $remaining) {
					if ($remaining <= 0) {
						$tmp = JString::substr($tmp, 0, 0);
					} else {
						if ($remaining < 6) {
							$tmp = JString::substr($tmp, 0, $remaining);
						} else {
							$tmp = JString::substr($tmp, 0, $remaining - 3) . JText::_('COM_EASYBLOG_ELLIPSES');
						}

						$remaining = 0;
					}
				} else {
					$remaining -= $tmpLength;
				}
			}

			$content = implode($link, $parts);
		} else {
			$content = JString::substr($content, 0, 136) . JText::_('COM_EASYBLOG_ELLIPSES');
		}

		return $content;
	}
}
