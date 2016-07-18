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
 * Object mapping for `#__social_clusters` table.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.2
 */
class SocialTableCluster extends SocialTable
{
	/**
	 * The unique id of the cluster
	 * @var int
	 */
	public $id			= null;

	/**
	 * The category id of the cluster.
	 * @var string
	 */
	public $category_id	= null;

	/**
	 * Determines the cluster type
	 * @var string
	 */
	public $cluster_type = null;

	/**
	 * The owner type of this cluster
	 * @var string
	 */
	public $creator_type 		= null;

	/**
	 * The owner unique id for this cluster
	 * @var int
	 */
	public $creator_uid		= null;

	/**
	 * The title of this cluster
	 * @var string
	 */
	public $title		= null;

	/**
	 * The description of this cluster
	 * @var string
	 */
	public $description	= null;

	/**
	 * The alias for this cluster. Used for SEF
	 * @var string
	 */
	public $alias 		= null;

	/**
	 * The state of the cluster
	 * @var int
	 */
	public $state		= null;

	/**
	 * The creation date of this cluster
	 * @var datetime
	 */
	public $created		= null;

	/**
	 * JSON string that is used as params
	 * @var string
	 */
	public $params		= null;

	/**
	 * Total number of hits this cluster obtained
	 * @var int
	 */
	public $hits		= null;

	/**
	 * The type of this cluster. Whether it is a private / public / invite only
	 * @var string
	 */
	public $type 		= null;

	/**
	 * The secret key for this group for admin actions.
	 * @var string
	 */
	public $key 		= null;

	/**
	 * Parent id of this cluster.
	 * @var integer
	 */
	public $parent_id = null;

	/**
	 * Parent type of this cluster.
	 * @var string
	 */
	public $parent_type = null;

	/**
	 * Longitude value of this cluster.
	 * @var float
	 */
	public $longitude = null;

	/**
	 * Latitude value of this cluster.
	 * @var float
	 */
	public $latitude = null;

	/**
	 * Address of this cluster.
	 * @var string
	 */
	public $address = null;

	public function __construct(& $db )
	{
		parent::__construct( '#__social_clusters' , 'id' , $db );
	}

    public function load( $keys = null, $reset = true )
    {
        if (! is_array($keys)) {

            // attempt to get from cache
            $catKey = 'cluster.'. $keys;

            if (FD::cache()->exists($catKey)) {
                $state = parent::bind(FD::cache()->get($catKey));
                return $state;
            }
        }

        $state = parent::load( $keys, $reset );
        return $state;
    }

	/**
	 * Override parent's hit behavior
	 *
	 * @since	1.0
	 * @access	public
	 * @return	boolean
	 */
	public function hit( $pk = null )
	{
		$ip			= JRequest::getVar( 'REMOTE_ADDR' , '' , 'SERVER' );

		if( !empty( $ip ) && !empty($this->id) )
		{
			$token		= md5( $ip . $this->id );

			$session	= JFactory::getSession();
			$exists		= $session->get( $token , false );

			if( $exists )
			{
				return true;
			}

			$session->set( $token , 1 );
		}

		return parent::hit( $pk );
	}

}
