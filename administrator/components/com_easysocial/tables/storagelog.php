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
 * Object mapping for Storage Log table.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.0
 */
class SocialTableStorageLog extends SocialTable
{
	/**
	 * The unique id of the field item.
	 * @var	int
	 */
	public $id			= null;

	/**
	 * The name of the file that is throwing errors.
	 * @var	string
	 */
	public $object_id		= null;

	/**
	 * The errors at line number.
	 * @var	string
	 */
	public $object_type 	= null;

	/**
	 * The target storage. E.g: amazon, local
	 * @var	string
	 */
	public $target 		= null;

	/**
	 * The error message.
	 * @var	string
	 */
	public $state 		= null;

	/**
	 * The date time of this error message.
	 * @var	int
	 */
	public $created 	= null;


	/**
	 * Class Constructor
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__social_storage_log' , 'id' , $db );
	}

}
