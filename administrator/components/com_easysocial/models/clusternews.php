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

jimport('joomla.application.component.model');

FD::import( 'admin:/includes/model' );

class EasySocialModelClusterNews extends EasySocialModel
{
	public function __construct( $config = array() )
	{
		parent::__construct( 'clusternews' , $config );
	}

	/**
	 * Deletes all news from a specific cluster
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The cluster id
	 * @return	bool	True on success false otherwise.
	 */
	public function delete( $id )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->delete( '#__social_clusters_news' );
		$sql->where( 'cluster_id' , $id );

		$db->setQuery( $sql );

		return $db->Query();
	}
}
