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

// Import main table
FD::import( 'admin:/tables/table' );

/**
 * Object relation mapping for reports.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialTableReport extends SocialTable
{
	/**
	 * The unique identifier for the report.
	 * @var int
	 */
	public $id				= null;

	/**
	 * The title of the reported item.
	 * @var string
	 */
	public $title 			= null;

	/**
	 * The reported message.
	 * @var string
	 */
	public $message 		= null;

	/**
	 * The reported extension.
	 * @var string
	 */
	public $extension 		= null;

	/**
	 * The reported item id.
	 * @var int
	 */
	public $uid 			= null;

	/**
	 * The reported item type.
	 * @var string
	 */
	public $type 			= null;

	/**
	 * The reporter.
	 * @var int
	 */
	public $created_by		= null;

	/**
	 * The reporter's ip address.
	 * @var int
	 */
	public $ip 				= null;

	/**
	 * The reported date time
	 * @var datetime
	 */
	public $created			= null;

	/**
	 * The date of the report
	 * @var int
	 */
	public $state			= null;

	/**
	 * The reported item's URL.
	 * @var string
	 */
	public $url 			= null;

	/**
	 * The constructor
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct( $db )
	{
		parent::__construct('#__social_reports' , 'id' , $db );
	}

	/**
	 * Retrieves the user object for the current reporter.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialUser 	A social user object.
	 */
	public function getUser()
	{
		$user	= FD::user( $this->created_by );

		return $user;
	}

	/**
	 * Processes email notifications
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notify()
	{
		// Set all properties from this table into the mail template
		$params 				= FD::makeArray( $this );

		//remove this _tbl_keys
		unset($params['_tbl_keys']);

		// Additional parameters.
		$user 						= FD::user( $this->created_by );
		$params['reporter']			= $user->getName();
		$params['reporterLink']		= $user->getPermalink( true, true );
		$params['item']				= $this->title;

		// Get a list of super admins on the site.
		$usersModel = FD::model( 'Users' );

		// We need to merge admin and custom emails
		$admins 	= $usersModel->getSiteAdmins();
		$config 	= FD::config();
		$custom	 	= $config->get( 'reports.notifications.emails' , '' );
		$recipients = array();

		foreach ($admins as $user) {
			$recipients[] = $user->email;
		}

		if (!empty($custom)) {

			$custom = explode(',', $custom);

			foreach ($custom as $email) {
				$recipients[] = $email;
			}
		}

		// Ensure for uniqueness here.
		$recipients 	= array_unique( $recipients );

		// Get mailer object
		$mailer 	= FD::mailer();
		$templates 	= array();

		foreach( $recipients as $recipient )
		{
			// Get boilerplate
			$template 	= $mailer->getTemplate();

			// Set recipient
			$template->setRecipient( '' , $recipient );

			// Set title of email
			$template->setTitle('COM_EASYSOCIAL_EMAILS_NEW_REPORT_SUBJECT');

			// Set template file.
			$template->setTemplate('site/reports/moderator', $params );

			$mailer->create( $template );
		}


	}
}
