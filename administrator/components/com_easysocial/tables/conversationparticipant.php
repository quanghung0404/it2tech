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
defined( 'JPATH_BASE' ) or die( 'Unauthorized Access' );

FD::import( 'admin:/tables/table' );

class SocialTableConversationParticipant extends SocialTable
{
	/**
	 * The unique id of the participant record.
	 * @var	int
	 */
	public $id				= null;

	/**
	 * The unique conversation id.
	 * @var	int
	 */
	public $conversation_id	= null;

	/**
	 * The unique user id that is participating in this conversation.
	 * @var	int
	 */
	public $user_id			= null;

	/**
	 * The state of the user's participant
	 * @var	int
	 */
	public $state 			= null;

	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct( $db )
	{
		parent::__construct('#__social_conversations_participants', 'id' , $db);
	}
}
