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

/**
 * Object mapping for `#__social_points_history`.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.0
 */
class SocialTablePointsHistory extends SocialTable
{
	/**
	 * The unique id which is auto incremented.
	 * @var int
	 */
	public $id					= null;

	/**
	 * Foreign key to `#__social_points`
	 * @var int
	 */
	public $points_id 			= null;

	/**
	 * The user that earned this point.
	 * @var int
	 */
	public $user_id				= null;

	/**
	 * The number of points earned.
	 * @var int
	 */
	public $points 				= null;

	/**
	 * The datetime string when the user earned this point.
	 * @var datetime
	 */
	public $created 			= null;

	/**
	 * The state of this point. 0 - unpublished , 1 - published.
	 * @var int
	 */
	public $state 				= null;

	/**
	 * Custom message for the points
	 * @var int
	 */
	public $message				= null;

	/**
	 * Class construct
	 *
	 * @since	1.0
	 * @param	JDatabase
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__social_points_history' , 'id' , $db );
	}

	/**
	 * Get's the point table
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	SocialTablePoint
	 */
	public function getPoint()
	{
		$table 	= FD::table( 'Points' );
		$table->load( $this->points_id );

		return $table;
	}
}
