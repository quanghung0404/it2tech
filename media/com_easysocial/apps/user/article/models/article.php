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

FD::import( 'admin:/includes/model' );

class ArticleModel extends EasySocialModel
{
	/**
	 * Retrieves a list of tasks created by a particular user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		$userId		The user's / creator's id.
	 *
	 * @return	Array				A list of notes item.
	 */
	public function getItems( $userId , $limit = 0 )
	{
		$db 		= FD::db();
		$sql 		= $db->sql();

		$sql->select( '#__content' );
		$sql->where( 'created_by' , $userId );
		$sql->where( 'state' , 1 );

		if( $limit )
		{
			$sql->limit( $limit );
		}

		// Always order by creation date
		$sql->order( 'created' , 'DESC' );

		$db->setQuery( $sql );

		$result	= $db->loadObjectList();

		return $result;
	}

}
