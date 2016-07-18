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

FD::import( 'admin:/tables/clusternews' );

/**
 * Object mapping for `#__social_clusters_news` table.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.2
 */
class SocialTableGroupNews extends SocialTableClusterNews
{

	/**
	 * used to override the stream creation date.
	 * @var date string
	 */
	public $_stream_date			= null;

	/**
	 * Override parent's implementation
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store( $pk = null )
	{
		$isNew	= $this->id ? false : true;
		$state 	= parent::store( $pk );

		// Only store "create" in stream
		if ($state && $isNew) {
			$this->createStream('create');
		}

		return $state;
	}

	/**
	 * to override stream creation date.
	 * this function need to be called before calling the store function.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setStreamDate( $datestring )
	{
		$this->_stream_date = $datestring;
	}

	public function createStream($verb)
	{
		// Create a new stream item for this discussion
		$stream		= FD::stream();

		// Get the stream template
		$tpl		= $stream->getTemplate();

		// Someone just joined the group
		$tpl->setActor( $this->created_by , SOCIAL_TYPE_USER );

		// Set the params to cache the group data
		$registry 	= FD::registry();
		$registry->set( 'news' 	, $this );

		// Set the context
		$tpl->setContext( $this->id , 'news' );

		$group = FD::group($this->cluster_id);

		// Set the cluster
		$tpl->setCluster( $this->cluster_id , SOCIAL_TYPE_GROUP, $group->type );

		// Set the verb
		$tpl->setVerb( $verb );

		// Set the params
		$tpl->setParams( $registry );

		if( $this->_stream_date )
		{
			$tpl->setDate( $this->_stream_date );
		}

		$tpl->setAccess('core.view');

		// Add the stream
		$stream->add( $tpl );

	}
}
