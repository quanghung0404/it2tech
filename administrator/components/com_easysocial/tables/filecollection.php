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

class SocialTableFileCollection extends SocialTable
{
	/**
	 * The unique id of the file.
	 * @var int
	 */
	public $id 		= null;

	/**
	 * The owner's unique id this collection belongs to.
	 * @var int
	 */
	public $owner_id = null;

	/**
	 * The owner's unique type this collection belongs to.
	 * @var string
	 */
	public $owner_type 	= null;

	/**
	 * The user's id which created this collection
	 * @var int
	 */
	public $user_id = null;

	/**
	 * The title for the collection.
	 * @var string
	 */
	public $title 	= null;

	/**
	 * The description for the collection.
	 * @var string
	 */
	public $desc 	= null;

	/**
	 * The date time the collection has been created.
	 * @var datetime
	 */
	public $created	= null;

	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 */
	public function __construct( $db )
	{
		parent::__construct( '#__social_files_collections' , 'id', $db);
	}

	/**
	 * Retrieves the total number of files in this collection.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalFiles()
	{
		static $stats 	= array();

		if( !isset( $stats[ $this->id ] ) )
		{
			$model	= FD::model( 'FileCollections' );
			$stats[ $this->id ] 	= $model->getTotalFiles( $this->id );
		}

		return $stats[ $this->id ];
	}
}
