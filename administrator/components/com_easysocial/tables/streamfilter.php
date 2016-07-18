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
 * @since	1.1
 * @author	Sam Teh <sam@stackideas.com>
 */
class SocialTableStreamFilter extends SocialTable
{
	/**
	 * The unique id.
	 * @var	int
	 */
	public $id				= null;

	/**
	 * Uid - user id
	 * @var	int
	 */
	public $uid 			= null;

	/**
	 * Type - user
	 * @var	string
	 */
	public $utype 			= null;

	/**
	 * Title
	 * @var	int
	 */
	public $title	 		= null;

	/**
	 * The alias of the stream filter
	 * @var	string
	 */
	public $alias	 		= null;

	/**
	 * Class Constructor.
	 *
	 * @since	1.1
	 * @access	public
	 */
	public function __construct( $db )
	{
		parent::__construct( '#__social_stream_filter' , 'id' , $db);
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

		$sql->select( '#__social_stream_filter' );
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

	public function getHashTag( $display = false )
	{
		if(! $this->id )
		{
			return '';
		}

		$filterItem = FD::table( 'StreamFilterItem' );
		$filterItem->load( array( 'filter_id' => $this->id, 'type' => 'hashtag' ) );

		if( $display )
		{
			//for display
			$filterItem->content = str_replace( ',', ', #', $filterItem->content);
			$filterItem->content = '#' . $filterItem->content;

			return $filterItem->content;
		}
		else
		{
			return $filterItem->content;
		}
	}

	public function getMention()
	{
		if(! $this->id )
		{
			return '';
		}

		$filterItem = FD::table( 'StreamFilterItem' );
		$filterItem->load( array( 'filter_id' => $this->id, 'type' => 'mention' ) );

		return $filterItem->content;
	}

	public function deleteItem( $type = '' )
	{
		if(! $this->id )
			return;

		$db = FD::db();
		$sql = $db->sql();

		$query = 'delete from `#__social_stream_filter_item` where `filter_id` = ' . $db->Quote( $this->id );
		if( $type )
		{
			$query .= ' and `type` = ' . $db->Quote( $type );
		}

		$sql->raw( $query );
		$db->setQuery( $sql );

		$db->query();

		return true;
	}

}
