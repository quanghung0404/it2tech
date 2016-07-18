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
 * Advanced search filter
 * @since	1.2
 * @author	Sam Teh <sam@stackideas.com>
 */
class SocialTableSearchFilter extends SocialTable
{
	/**
	 * The unique id.
	 * @var	int
	 */
	public $id				= null;

	/**
	 * Element
	 * @var	string - user / group
	 */
	public $element 			= null;

	/**
	 * Uid - user id / group id
	 * @var	int
	 */
	public $uid 			= null;

	/**
	 * Title
	 * @var	int
	 */
	public $title	 		= null;

	/**
	 * The alias of the search filter
	 * @var	string
	 */
	public $alias	 		= null;

	/**
	 * The filter data
	 * @var	json string
	 */
	public $filter	 		= null;

	/**
	 * user id who created the filter
	 * @var	int
	 */
	public $created_by	 		= null;

	/**
	 * creation date
	 * @var	datetime
	 */
	public $created	 		= null;

	/**
	 * indicate if this is a sitewide filter
	 * @var	int
	 */
	public $sitewide	 		= null;


	/**
	 * Class Constructor.
	 *
	 * @since	1.1
	 * @access	public
	 */
	public function __construct( $db )
	{
		parent::__construct( '#__social_search_filter' , 'id' , $db);
	}

	/**
	 * Override parent's store function
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store( $updateNulls = false )
	{
		// Generate an alias for this filter if it is empty.
		if( empty( $this->alias ) )
		{
			$alias 	= $this->title;
			$alias 	= JFilterOutput::stringURLSafe( $alias );
			$tmp	= $alias;

			$i 		= 1;

			while( $this->aliasExists( $alias ) )
			{
				$alias 	= $tmp . '-' . $i;
				$i++;
			}

			$this->alias 	= $alias;
		}

		$state 	= parent::store( $updateNulls );
	}

	/**
	 * Checks the database to see if there are any same alias
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function aliasExists( $alias )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_search_filter' );
		$sql->column( 'COUNT(1)' , 'total' );
		$sql->where( 'alias' , $alias );

		$db->setQuery( $sql );

		$exists 	= $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	/**
	 * Retrieves the alias of this filter
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlias()
	{
		$alias 	= $this->id . '-' . $this->alias;

		return $alias;
	}

}
