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

jimport('joomla.filesystem.file');

require_once(dirname(__FILE__) . '/MCAPI.class.php');

class EasyBlogMailchimp extends EasyBlog
{
	public $key = null;
	public $url	= 'api.mailchimp.com/1.3/';

	public function __construct()
	{
		parent::__construct();

		$this->key = $this->config->get('subscription_mailchimp_key');

		if ($this->key) {
			$datacenter = explode('-', $this->key);
			$this->url = 'http://' . $datacenter[1] . '.' . $this->url;
		}
	}

	/**
	 * Creates a new campaign and send it immediately.
	 *
	 * @since	3.7
	 * @access	public
	 */
	public function notify( $emailTitle , $emailData , &$blog )
	{
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );
		$config = EasyBlogHelper::getConfig();

		if( !function_exists( 'curl_init' ) )
		{
			echo JText::_( 'COM_EASYBLOG_CURL_DOES_NOT_EXIST' );
		}

		if( !$config->get( 'subscription_mailchimp' ) )
		{
			return;
		}

		$listId	= $config->get( 'subscription_mailchimp_listid' );

		if( !$listId )
		{
			return;
		}



		$api = new MCAPI( $this->key );
		$type = 'regular';

		$jConfig 			= EasyBlogHelper::getJConfig();
		$defaultEmailFrom  	= ( EasyBlogHelper::getJoomlaVersion() >= '1.6' ) ? $jConfig->get( 'mailfrom') : $jConfig->get( 'mailfrom');
		$defaultFromName  	= ( EasyBlogHelper::getJoomlaVersion() >= '1.6' ) ? $jConfig->get( 'fromname') : $jConfig->get( 'fromname');

		$fromEmail 	= $config->get( 'mailchimp_from_email' 	, $defaultEmailFrom );
		$fromName	= $config->get( 'mailchimp_from_name'	, $defaultFromName );

		$opts 					= array();
		$opts[ 'list_id' ]		= $listId;
		$opts[ 'from_email' ]	= $fromEmail;
		$opts[ 'from_name' ]	= $fromName;
		$opts[ 'subject' ]		= $emailTitle;
		$opts[ 'tracking' ]		= array( 'opens' => true , 'html_clicks' => true , 'text_clicks' => false );
		$opts[ 'authenticate' ]	= true;

		$opts[ 'title' ]		= $blog->title;

		$content = array(
							'html'	=> self::getTemplateContents('post.new', $emailData, 'html'),
		  					'text'	=> self::getTemplateContents('post.new', $emailData, 'text')
						);

		$cid 	= $api->campaignCreate( $type , $opts , $content );

		// Send this now!
		if( !$api->errorCode )
		{
			$api->campaignSendNow( $cid );
		}
	}

	/**
	 * Retrieves the template contents.
	 *
	 **/
	public function getTemplateContents( $template, $data , $type )
	{
		// Load front end's language file.
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		$config = EasyBlogHelper::getConfig();

		// @rule: Detect what type of emails that we should process.
		$theme	= EB::template();

		// Fetch the child theme first.
		foreach( $data as $key => $val ) {
			$theme->set( $key , $val );
		}

		$contents	= $theme->output( 'site/emails/' . $type . '/' . $template);

		// @rule: Now we need to process the main template holder.
		$title	= $config->get( 'notifications_title' );

		$theme 	= EB::template();
		$theme->set( 'unsubscribe'	, '' );
		$theme->set( 'emailTitle'	, $title );
		$theme->set( 'contents'		, $contents );

		$output = $theme->output( 'site/emails/' . $type . '/template');


		return $output;
	}

	/**
	 * Allows caller to send a subscribe IPN to mailchimp
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function subscribe($email, $firstName, $lastName = '')
	{
		EB::loadLanguages();

		if (!function_exists('curl_init')) {
			return false;
		}

		if (!$this->config->get('subscription_mailchimp')) {
			return false;
		}

		// Get the list id
		$listId	= $this->config->get('subscription_mailchimp_listid');

		if (!$listId) {
			return false;
		}

		$firstName = urlencode($firstName);
		$lastName = urlencode($lastName);

		// Determines if we should send the welcome email
		$sendWelcome = $this->config->get('subscription_mailchimp_welcome') ? 'true' : 'false';

		$url = $this->url . '?method=listSubscribe';
		$url = $url . '&apikey=' . $this->key;
		$url = $url . '&id=' . $listId;
		$url = $url . '&output=json';
		$url = $url . '&email_address=' . $email;
		$url = $url . '&merge_vars[FNAME]=' . $firstName;
		$url = $url . '&merge_vars[LNAME]=' . $lastName;
		$url = $url . '&merge_vars[email_type]=html';
		$url = $url . '&merge_vars[send_welcome]=' . $sendWelcome;

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0 );
		$result = curl_exec($ch);
		curl_close($ch);

		return true;
	}
}
