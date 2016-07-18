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
 * Object mapping for `#__social_apps_map` table.
 *
 * @author	Jason Rey <jasonrey@stackideas.com>
 * @since	1.0
 */
class SocialTableAppsmap extends SocialTable
{
	/**
	 * The unique id of the mapping
	 * @var int
	 */
	public $id			= null;

	/**
	 * The userid
	 * @var int
	 */
	public $uid			= null;

	/**
	 * The type of the app group
	 * @var string
	 */
	public $type		= null;

	/**
	 * The app_id
	 * @var int
	 */
	public $app_id		= null;

	/**
	 * The position of the app
	 * @var string
	 */
	public $position	= null;

	/**
	 * The creation date time.
	 * @var datetime
	 */
	public $created		= null;

	/**
	 * The json params
	 * @var datetime
	 */
	public $params		= null;

	public function __construct(& $db )
	{
		parent::__construct( '#__social_apps_map' , 'id' , $db );
	}
}
