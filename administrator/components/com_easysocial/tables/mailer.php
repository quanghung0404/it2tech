<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'admin:/tables/table' );

/**
 * Object mapping from table `#__social_mailer`
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialTableMailer extends SocialTable
{
	/**
	 * The unique id of the mailer item.
	 * @var	int
	 */
	public $id				= null;

	/**
	 * The sender's name which can be customized for each queue item.
	 * @var	string
	 */
	public $sender_name 	= null;

	/**
	 * The sender's email which can be customized for each queue item.
	 * @var	string
	 */
	public $sender_email    = null;

	/**
	 * The recipient's name.
	 * @var	string
	 */
	public $recipient_name  = null;

	/**
	 * The recipient's email address
	 * @var	string
	 */
	public $recipient_email = null;

	/**
	 * The title of the email.
	 * @var	string
	 */
	public $title           = null;

	/**
	 * The content of the email (optional if $template is supplied )
	 * @var	string
	 */
	public $content         = null;

	/**
	 * The path to the email template (optional if $content is already supplied)
	 * @var	string
	 */
	public $template        = null;

	/**
	 * Determines if the email should send in html format.
	 * @var	int
	 */
	public $html            = null;

	/**
	 * Determines if the email is already send. (e.g: 1 - Sent , 0 - Pending , 2 - Sending )
	 * @var	int
	 */
	public $state           = null;

	/**
	 * The creation date time of the queue.
	 * @var	datetime
	 */
	public $created         = null;

	/**
	 * JSON string parameter.
	 * @var	string
	 */
	public $params          = null;

	/**
	 * The priority of the item. (E.g: 4 - Highest , 3 - High , 2 - Medium , 1 - Low )
	 * @var	int
	 */
	public $priority        = null;

	/**
	 * The language that should be use for the email.
	 * @var	int
	 */
	public $language        = null;

	/**
	 * Class Constructor
	 *
	 * @since	1.0
	 * @param	JDatabase
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function __construct( $db )
	{
		parent::__construct('#__social_mailer', 'id', $db);
	}

	public function bind( $data , $ignore = array() )
	{
		$state  	= parent::bind( $data );
		$jConfig	= FD::jconfig();

	    // @TODO: Admin can create multiple emails from in the future.
		if( is_null( $this->sender_name ) )
		{
		    $this->sender_name  = $jConfig->getValue( 'fromname' );
		}

		if( is_null( $this->sender_email ) )
		{
		    $this->sender_email = $jConfig->getValue( 'mailfrom' );
		}

		if( is_null( $this->created ) )
		{
		    $this->created  = FD::get( 'Date' )->toMySQL();
		}

		if( is_null( $this->priority ) )
		{
		    $this->priority = SOCIAL_MAILER_PRIORITY_NORMAL;
		}

		return $state;
	}

	public function getParams()
	{
	    if( empty( $this->params ) || is_null( $this->params ) )
	    {
	        return false;
		}

		return FD::get( 'Parameter' , $this->params );
	}

	/**
	 * Retrieves the absolute path to the icon for the current state
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	string	The absolute path to the image file.
	 *
	 */
	public function getIcon()
	{
		$uri 	= SOCIAL_MEDIA_URI . '/assets/images/icons/mailer_priority_' . $this->priority . '.png';

		return $uri;
	}

	/**
	 * Formats the content for preview.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preview()
	{
		$mailer 	= FD::mailer();
		return $mailer->getEmailContents( $this );
	}

	public function loadLanguage()
	{
		if (!empty($this->template)) {

			$parts = explode('/', $this->template);

			$location = array_shift($parts);

			if ($location === 'site' || $location == 'apps') {
				FD::language()->loadSite();
			}

			if ($location === 'admin' || $location == 'apps') {
				FD::language()->loadAdmin();
			}
		}
	}
}
