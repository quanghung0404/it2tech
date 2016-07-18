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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class SocialConversation extends EasySocial
{
	public $conversation = null;

	public $recipients = array();
	public $sender = null;

	public $message = null;
	public $tags = array();
	public $attachments = array();
	public $location = null;

	public function __construct($id = null, $options = array())
	{
		parent::__construct();

		// By default the sender would always be the current logged in user.
		$this->sender = $this->my;

		$this->conversation = ES::table('Conversation');

		if ($id) {
			$this->conversation->load($id);
		}
	}

	/**
	 * Sets the sender of this conversation
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setSender(SocialUser $user)
	{
		$this->sender = $user;
	}

	/**
	 * Defines a list of recipients for this conversation
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setRecipients($recipients = array())
	{
		$this->addRecipient($recipients);
	}

	/**
	 * Defines a list of friend lists for this conversations
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setFriendList($lists = array())
	{
		$model = FD::model('Lists');
		$ids = array();

		foreach ($lists as $id) {

			// Get a list of users from the friend list
			$users = $model->getMembers($id, true);

			// Merge the result set
			$ids = array_merge($ids, $users);
		}

		$this->addRecipient($ids);
	}

	/**
	 * Adds a recipient to the recipient list
	 *
	 * @since	1.3
	 * @access	private
	 * @param	string
	 * @return	null
	 */
	private function addRecipient($id)
	{
		if (is_array($id)) {
			$this->recipients = array_merge($id, $this->recipients);
		} else {
			$this->recipients[] = $id;	
		}
		
		$this->recipients = array_unique($recipients);
	}

	/**
	 * Sets the message
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setMessage($message)
	{
		// Normalize CRLF (\r\n) to just LF (\n)
		$msg = str_ireplace("\r\n", "\n", $msg);
	}

	/**
	 * Sets any tags associated with this conversation
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setTags($tags = array())
	{
	}

	/**
	 * Sets location for this conversation
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setLocation($address, $latitude, $longitude)
	{
	}

	/**
	 * Sets any attachments
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setAttachments($attachments = array())

	/**
	 * Sends a new message
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function send()
	{
		if (!$this->recipients) {
			$this->setError('Please enter a list of recipients for this message.');
			return false;
		}

		// Check if user is allowed to create new conversations
		$access = FD::access();

		if (!$access->allowed('conversations.create')) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_CONVERSATIONS_ERROR_NOT_ALLOWED'), SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__);
		}

		// If recipients is not provided, we need to throw an error.
		if (empty($recipients)) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_ERROR_EMPTY_RECIPIENTS' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}
	}

	/**
	 * Retrieves a list of messages in this conversation
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getMessages()
	{
	}

	/**
	 * Creates the message
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	private function createMessage()
	{
	}


	/**
	 * Checks for the daily sending limit
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	private function checkDailySendLimit()
	{
	}

	/**
	 * Retrieves a list of participants
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getParticipants()
	{
	}
}

$conversation = ES::conversation();

$conversation->setRecipients($arr);
$conversation->setMessage('hello world');
$conversation->setAttachments($arr);
$conversation->setLocation($address, $lat, $lng);
$conversation->send();

$conversation->getMessages();
