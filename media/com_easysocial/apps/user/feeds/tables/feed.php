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
FD::import( 'admin:/tables/table' );

/**
 * Tasks object relation mapper.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class FeedsTableFeed extends SocialTable
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
		parent::__construct( '#__social_feeds' , 'id' , $db );
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
	public function createStream( $verb )
	{
		// Add activity logging when a friend connection has been made.
		// Activity logging.
		$stream				= FD::stream();
		$streamTemplate		= $stream->getTemplate();

		// Set the actor.
		$streamTemplate->setActor( $this->user_id , SOCIAL_TYPE_USER );

		// Set the context.
		$streamTemplate->setContext( $this->id , 'feeds' );

		// Set the verb.
		$streamTemplate->setVerb( $verb );

		// Set the public stream
		$streamTemplate->setAccess( 'core.view' );

		// Set the params to offload the loading
		$streamTemplate->setParams( $this );

		// Create the stream data.
		$stream->add( $streamTemplate );
	}

	/**
	 * Initializes the parser
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getParser()
	{
		static $parsers	= array();

		if( !isset( $parsers[ $this->id ] ) )
		{
			$connector 	= FD::get( 'Connector' );
			$connector->addUrl( $this->url );

			$connector->connect();

			$contents 	= $connector->getResult( $this->url );

			// Ensure that there are no leading text before the <?xml> tag.
			$pattern    	= '/(.*?)<\?xml version/is';
			$replacement    = '<?xml version';
			$contents		= preg_replace( $pattern , $replacement , $contents , 1 );

			// If there's no xml text in the contents, we need to add them
			if( strpos( $contents, '<?xml version' ) === false )
			{
				$contents 	= '<?xml version="1.0" encoding="utf-8"?>' . $contents;
			}

			jimport( 'simplepie.simplepie' );

			$parser 	= new SimplePie();
			$parser->strip_htmltags( false );
			$parser->set_raw_data( $contents );
			$parser->init();

			$parsers[ $this->id ]	= $parser;
		}

		return $parsers[ $this->id ];
	}
}


