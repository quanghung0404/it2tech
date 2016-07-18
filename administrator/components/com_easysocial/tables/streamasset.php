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
 * Object relation mapping for location.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialTableStreamAsset extends SocialTable
{
	/**
	 * The unique location id.
	 * @var	int
	 */
	public $id				= null;

	/**
	 * The unique type id.
	 * @var	int
	 */
	public $stream_id 		= null;

	/**
	 * The asset type
	 * @var	string
	 */
	public $type 			= null;

	/**
	 * The assets data
	 * @var	int
	 */
	public $data	 		= null;

	/**
	 * Class Constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct( $db )
	{
		parent::__construct( '#__social_stream_assets' , 'id' , $db);
	}

	public function getParams()
	{
		$registry 	= FD::registry();
		$registry->load( $this->data );

		return $registry;
	}
}
