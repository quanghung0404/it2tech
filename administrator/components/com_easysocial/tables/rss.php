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

// Import main table to assis this table.
FD::import('admin:/tables/table');

class SocialTableRss extends SocialTable
{
	/**
	 * The unique task id.
	 * @var	int
	 */
	public $id 		= null;

	/**
	 * The owner of the task.
	 * @var	int
	 */
	public $user_id	= null;

	/**
	 * The owner of the task.
	 * @var	int
	 */
	public $uid	= null;

	/**
	 * The owner of the task.
	 * @var	int
	 */
	public $type	= null;

	/**
	 * The feed title.
	 * @var	string
	 */
	public $title 	= null;

	/**
	 * The feed description.
	 * @var	string
	 */
	public $description 	= null;

	/**
	 * The feed url.
	 * @var	string
	 */
	public $url 	= null;

	/**
	 * The state of the task
	 * @var	string
	 */
	public $state 	= null;

	/**
	 * The date time this task has been created.
	 * @var	datetime
	 */
	public $created 	= null;

	/**
	 * Class Constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__social_rss' , 'id' , $db );
	}

	/**
	 * Overrides the delete function
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function delete( $pk = null )
	{
		$state 		= parent::delete();

		// Delete any items that are related to this stream
		$stream 	= FD::stream();
		$stream->delete( $this->id , 'feeds' );

		return $state;
	}

	/**
	 * Creates a new stream for the feed
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createStream($verb)
	{
		// Add activity logging when a friend connection has been made.
		// Activity logging.
		$stream				= FD::stream();
		$streamTemplate		= $stream->getTemplate();

		// Set the cluster
		if ($this->type == SOCIAL_TYPE_GROUP) {
			$streamTemplate->setCluster($this->uid, SOCIAL_TYPE_GROUP, 1);
		}

		// Set the actor.
		$streamTemplate->setActor($this->user_id, SOCIAL_TYPE_USER);

		// Set the context.
		$streamTemplate->setContext($this->id, 'feeds');

		// Set the verb.
		$streamTemplate->setVerb($verb);

		// Set the public stream
		$streamTemplate->setPublicStream('core.view');

		// Set the params to offload the loading
		$streamTemplate->setParams($this);

		// Create the stream data.
		$stream->add($streamTemplate);
	}
}


