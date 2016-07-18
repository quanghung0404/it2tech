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
FD::import('admin:/tables/table');

/**
 * Object relation mapping for conversation message.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialTableConversationMessage extends SocialTable
{
	public $id = null;
	public $conversation_id = null;
	public $type = null;
	public $message = null;
	public $created = null;
	public $created_by = null;

	// These columns are not real columns in the database table.
	protected $target		= null;

	public function __construct(&$db)
	{
		parent::__construct('#__social_conversations_message', 'id' , $db);
	}

	/**
	 * Retrieves the author of the message.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return 	SocialUser		The user object.
	 */
	public function getCreator()
	{
		static $nodes	= array();

		if( !isset( $nodes[ $this->created_by ] ) )
		{
			$nodes[ $this->created_by ]	= FD::user( $this->created_by );
		}
		return $nodes[ $this->created_by ];
	}

	/**
	 * Retrieves the content of the message.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return 	SocialUser		The user object.
	 */
	public function getContents()
	{
		if ($this->type == 'join') {
			return JText::sprintf('COM_EASYSOCIAL_CONVERSATIONS_INVITED_INTO_CONVERSATION_MESSAGE', $this->getCreator()->getName(), $this->getTarget()->getName());
		}

		if ($this->type == 'leave') {
			return JText::sprintf('COM_EASYSOCIAL_CONVERSATIONS_LEFT_CONVERSATION_MESSAGE', $this->getCreator()->getName());
		}

		if ($this->type == 'message') {
			$message = $this->message;
			$tags = $this->getTags();

			// Apply mentions and hashtags
			if ($tags) {
				$message = FD::string()->processTags($tags, $message);
			}

			// Apply e-mail replacements
			$message = FD::string()->replaceEmails($message);

			// Apply hyperlinks
			$message = FD::string()->replaceHyperlinks($message);

			// Apply bbcode
			$message = FD::string()->parseBBCode($message, array('escape' => false));
		}


		return $message;
	}

	/**
	 * Retrieves a list of tags for this conversation
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTags()
	{
		$model 	= FD::model( 'Tags' );

		$tags 	= $model->getTags( $this->id , 'conversations' );

		return $tags;
	}

	/**
	 * Retrieves the intro text portion of a message.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return 	string		The intro section of the message.
	 */
	public function getIntro( $overrideLength = null )
	{
		$config 	= FD::config();

		// Get the maximum length.
		$maxLength	= is_null( $overrideLength ) ? $config->get( 'conversations.layout.intro' ) : $overrideLength;

		$message 	= strip_tags( $this->message );
		$message	= JString::substr( $message , 0 , $maxLength ) . ' ' . JText::_( 'COM_EASYSOCIAL_ELLIPSIS' );

		return $message;
	}

	/**
	 * Retrieves a list of attachment for this conversation message.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return 	Array	An array of SocialUploads ORM.
	 */
	public function getAttachments()
	{
		$model 	= FD::model( 'Files' );

		$files 	= $model->getFiles( $this->id , SOCIAL_TYPE_CONVERSATIONS );

		return $files;
	}


	/**
	 * Binds any temporary files to the message.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bindTemporaryFiles( $ids )
	{
		// This should only be executed with a valid conversation.
		if( !$this->id )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_ERROR_STORE_CONVERSATION_FIRST' ) );
			return false;
		}

		// Ensure that they are in an array form.
		$ids 	= FD::makeArray( $ids );

		foreach( $ids as $id )
		{
			$file 			= FD::table( 'File' );

			$file->uid		= $this->id;
			$file->type 	= SOCIAL_TYPE_CONVERSATIONS;

			// Copy some of the data from the temporary table.
			$file->copyFromTemporary( $id );

			$file->store();
		}

		return true;
	}

	public function getType()
	{
		return strtolower( $this->type );
	}

	/**
	 * This is only used when the conversation type is a "join" or "leave" type.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getTarget()
	{
		$target 	= $this->message;

		$user 		= FD::user( $target );

		return $user;
	}

	/**
	 * Override parent's store method so that we can
	 * run our own maintenance here.
	 */
	public function store( $updateNulls = false )
	{
		$state	=  parent::store( $updateNulls );

		if( $state )
		{
			// Add a new location item if address, latitude , longitude is provided.
			$latitude		= JRequest::getVar( 'latitude' );
			$longitude		= JRequest::getVar( 'longitude' );
			$address 		= JRequest::getVar( 'address' );

			// Let's add the location now.
			if( !empty( $latitude ) && !empty( $longitude ) && !empty( $address ) )
			{
				$location 				= FD::table( 'Location' );
				$location->latitude		= $latitude;
				$location->longitude	= $longitude;
				$location->address		= $address;
				$location->uid 			= $this->id;
				$location->type 		= SOCIAL_TYPE_CONVERSATIONS;
				$location->user_id		= $this->created_by;

				$location->store();
			}
		}
		return $state;
	}

	/**
	 * Returns a standard location object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	stdClass
	 */
	public function getLocation()
	{
		$location 	= FD::table( 'Location' );
		$state 		= $location->loadByType( $this->id , SOCIAL_TYPE_CONVERSATIONS );

		if( !$state )
		{
			return false;
		}

		return $location;
	}
}
