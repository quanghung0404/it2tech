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
 * Object mapping for `#__social_clusters_news` table.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.2
 */
class SocialTableClusterNews extends SocialTable
{
	/**
	 * The unique id for this cluster mapping.
	 * @var int
	 */
	public $id			= null;

	/**
	 * The id of the cluster
	 * @var int
	 */
	public $cluster_id	= null;

	/**
	 * The title for the news
	 * @var string
	 */
	public $title = null;

	/**
	 * The content for the news
	 * @var string
	 */
	public $content 	= null;

	/**
	 * The creation date of the news
	 * @var datetime
	 */
	public $created		= null;

	/**
	 * Determines the owner of the news item
	 * @var int
	 */
	public $created_by		= null;

	/**
	 * The state of the mapping
	 * @var int
	 */
	public $state		= null;

	/**
	 * Determines if the comments should be rendered
	 * @var int
	 */
	public $comments	= null;

	/**
	 * The total number of hits for this news article
	 * @var int
	 */
	public $hits		= null;

	public function __construct(& $db )
	{
		parent::__construct( '#__social_clusters_news' , 'id' , $db );
	}

	/**
	 * Allows the caller to check on some of the required items
	 *
	 * @since	1.2
	 * @access	public
	 * @param	Array
	 * @return	boolean		True if success, false otherwise.
	 */
	public function check()
	{
		if( empty( $this->title ) )
		{
			$this->setError( JText::_( 'Please enter a title for your article.' ) );
			return false;
		}

		if( empty( $this->content ) )
		{
			$this->setError( JText::_( 'Please enter some contents for your article.' ) );
			return false;
		}

		if( empty( $this->cluster_id ) )
		{
			$this->setError( JText::_( 'Please specify the owner of this item.') );
			return false;
		}

		if( empty( $this->created_by ) )
		{
			$this->setError( JText::_( 'Please specify an author for this article.' ) );
			return false;
		}

		return true;
	}

	/**
	 * Returns the permalink to the announcement
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink($xhtml = true, $external = false, $sef = true)
	{
		static $app = null;

		if (!$app) {
			$app = FD::table('App');
			$app->load(array('element' => 'news', 'group' => SOCIAL_TYPE_GROUP));
		}

		$url 	= $app->getPermalink('canvas', array('customView' => 'item', 'newsId' => $this->id, 'groupId' => $this->cluster_id, 'external' => $external, 'sef' => $sef), $xhtml);

		return $url;
	}

	/**
	 * Override parent's behavior to delete the item.
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function delete( $pk = null )
	{
		$state 	= parent::delete( $pk );

		if( $state )
		{
			// Delete stream items
			FD::stream()->delete( $this->id , 'news' );

			// Delete comments
			FD::comments( $this->id , 'news', 'create' )->delete();

			// Delete likes
			FD::likes()->delete( $this->id , 'news', 'create' );
		}

		return $state;
	}
}
