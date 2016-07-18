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

class EasyBlogCaptcha extends EasyBlog
{
	/**
	 * Retrieves the html codes for the ratings.
	 *
	 * @param	int	$uid	The unique id for the item that is being rated
	 * @param	string	$type	The unique type for the item that is being rated
	 * @param	string	$command	A textual representation to request user to vote for this item.
	 * @param	string	$element	A dom element id.
	 **/
	public function getHTML()
	{
		// @task: If no captcha is enabled, we always default to true.
		if (!$this->config->get('comment_recaptcha') && !$this->config->get('comment_captcha')) {
			return false;
		}

		// Test if captcha should be enabled for registered users.
		if (!$this->config->get('comment_captcha_registered') && $this->my->id > 0) {
			return false;
		}

		// If recaptcha, use recaptcha's form
		if ($this->config->get('comment_recaptcha') && $this->config->get('comment_recaptcha_public')) {
			$adapter = $this->getAdapter('recaptcha');

			return $adapter->getHTML();
		}

		$adapter = $this->getAdapter('Captcha');
		return $adapter->getHTML();
	}

	/**
	 * Retrieves the captcha adapter
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAdapter($type)
	{
		$folder	= dirname(__FILE__) . '/adapters';

		$file = $folder . '/' . strtolower($type) . '.php';

		require_once($file);

		$className = 'EasyBlogCaptchaAdapter' . ucfirst($type);

		$obj = new $className();

		return $obj;
	}

	/**
	 * Verifies the captcha codes
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function verify()
	{
		// @task: If no captcha is enabled, we always default to true.
		if (!$this->config->get('comment_recaptcha') && !$this->config->get('comment_captcha')) {
			return true;
		}

		// Captcha is not applied on registered users
		if (!$this->config->get('comment_captcha_registered') && $this->my->id > 0) {
			return true;
		}

		// Check if recaptcha is used
		if ($this->config->get('comment_recaptcha') && $this->config->get('comment_recaptcha_public')) {

			$adapter = $this->getAdapter('Recaptcha');
			$response = $this->input->get('recaptcha', '', 'default');

			$ip = @$_SERVER['REMOTE_ADDR'];

			$valid = $adapter->verifyResponse($ip, $response);

			return $valid;
		}

		// If recaptcha is not enabled, we assume that the built in captcha is used.
		$captcha  = $this->getAdapter('captcha');
		$response = $this->input->get('captcha-response', '', 'default');
		$id = $this->input->get('captcha-id', '', 'default');

		return $captcha->verify($response, $id);
	}

	/**
	 * Throws error message and reloads the captcha image.
	 * @param	Ejax	$ejax	Ejax object
	 * @return	string	The json output for ajax calls
	 **/
	public function getError( $ajax , $post )
	{
		$config		= EasyBlogHelper::getConfig();
		$adapters	= EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'captcha';

		if( $config->get( 'comment_recaptcha' ) && $config->get('comment_recaptcha') && $config->get('comment_recaptcha_public') )
		{
			require_once( $adapters . DIRECTORY_SEPARATOR . 'recaptcha.php' );
			return EasyBlogRecaptcha::getError( $ajax , $post );
		}

		require_once( $adapters . DIRECTORY_SEPARATOR . 'captcha.php' );
		return EasyBlogCaptcha::getError( $ajax , $post );
	}

	/**
	 * Reloads the captcha image
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function reload()
	{
		// dump(func_get_args());
		$config		= EasyBlogHelper::getConfig();
		$adapters	= EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'captcha';

		// If no captcha is enabled, ignore it.
		if( !$config->get('comment_recaptcha') && !$config->get( 'comment_captcha' ) )
		{
			return true;
		}

		$public		= $config->get( 'comment_recaptcha_public');
		$private	= $config->get( 'comment_recaptcha_private');

		if( $config->get( 'comment_recaptcha' ) && $config->get('comment_recaptcha') && $config->get('comment_recaptcha_public') )
		{
			require_once( $adapters . DIRECTORY_SEPARATOR . 'recaptcha.php' );
			$ajax->script( EasyBlogRecaptcha::getReloadScript( $ajax , $post ) );
			return true;
		}

		// @task: If recaptcha is not enabled, we assume that the built in captcha is used.
		// Generate a new captcha
		if( isset( $post[ 'captcha-id' ] ) )
		{
			$ref	= EB::table('Captcha');
			$ref->load( $post[ 'captcha-id' ] );

			$state = $ref->delete();
			if( $state )
			{
				// we need to unset this variable so that when calling EasyBlogCaptcha::getReloadScript, EB will not run the deletion again.
				unset( $post[ 'captcha-id' ] );
			}
		}

		require_once( $adapters . DIRECTORY_SEPARATOR . 'captcha.php' );
		$ajax->script( EasyBlogCaptcha::getReloadScript( $ajax , $post ) );
		return true;
	}
}
