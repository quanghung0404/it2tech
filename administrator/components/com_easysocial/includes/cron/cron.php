<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialCron
{
	private $status = null;
	private $output = array();

	public function __construct()
	{
		// constructor
	}

	public static function factory()
	{
		$obj = new self();

		return $obj;
	}

	/**
	 * Allows caller to invoke sending of emails internally
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function dispatchEmails()
	{
		$path = dirname(__FILE__) . '/hooks/email.php';

		require_once($path);

		$states = array();

		$hook = new SocialCronHooksEmail();
		$hook->execute($states);
	}

	/**
	 * Triggers the cron service
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function execute()
	{
		$config 		= FD::config();

		// Check if we need a secure phrase.
		$requirePhrase 	= $config->get( 'general.cron.secure' );
		$storedPhrase 	= $config->get( 'general.cron.key' );
		$phrase 		= JRequest::getVar( 'phrase' , '' );

		if( $requirePhrase && empty( $phrase ) || ( $requirePhrase && $storedPhrase != $phrase ) )
		{
			$this->setStatus( 'failed' );
			$this->output(JText::_('COM_EASYSOCIAL_CRONJOB_PASSPHRASE_INVALID'));
			return $this->render();
		}

		// Data to be passed to the triggers.
		$data 		= array();

		// Array of states
		$states		= array();

		// @trigger: fields.onBeforeCronExecute
		// Retrieve custom fields for the current step
		$fieldsModel 	= FD::model( 'Fields' );
		$customFields	= $fieldsModel->getCustomFields(array('appgroup' => SOCIAL_TYPE_USER));

		$fields 	= FD::fields();
		$fields->trigger( 'onCronExecute' , SOCIAL_TYPE_USER , $customFields , $data );

		// @trigger: apps.onBeforeCronExecute
		$apps 		= FD::apps();
		$dispatcher	= FD::dispatcher();
		$dispatcher->trigger( SOCIAL_TYPE_USER , 'onCronExecute' , $data );

		// Load up files in hooks
		$this->hook($states);

		if (!empty($states)) {
			foreach($states as $state) {
				$this->output($state);
			}
		}

		// Perform maintenance
		$maintenance	= FD::get( 'Maintenance' );
		$maintenance->cleanup();

		$this->render();
	}

	/**
	 * Retrieves a list of hooks for cron services
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hook(&$state)
	{
		$path = __DIR__ . '/hooks';
		$files = JFolder::files($path, '.', false, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));

		if (!$files) {
			return;
		}

		foreach ($files as $file) {
			require_once($path . '/' . $file);

			$className 	= 'SocialCronHooks' . str_ireplace('.php', '', $file);

			$hook 		= new $className();
			$hook->execute($state);
		}
	}

	/**
	 * Sets the status
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setStatus( $status )
	{
		$this->status = $status;
	}

	/**
	 * Displays the json codes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The content of the item
	 * @param	string	The state of the item
	 * @return
	 */
	public function output( $contents , $status = '200' )
	{
		$output 			= new stdClass();

		$output->status 	= $status;
		$output->contents 	= $contents;
		$output->time 		= FD::date()->toMySQL();

		$this->output[]		= $output;
	}

	/**
	 * Renders the cronjob output
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function render()
	{
		header('Content-type: text/x-json; UTF-8');

		echo FD::json()->encode( $this->output );
		exit;
	}
}
