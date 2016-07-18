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

class EasySocialModelLanguages extends EasySocialModel
{
	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	function __construct( $config = array() )
	{
		parent::__construct( 'languages' , $config );
	}

	/**
	 * Populates the state
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initStates()
	{
		$profile 	= $this->getUserStateFromRequest( 'profile' );
		$group 		= $this->getUserStateFromRequest( 'group' );
		$published	= $this->getUserStateFromRequest( 'published' , 'all' );

		$this->setState( 'published' , $published );
		$this->setState( 'group'	, $group );
		$this->setState( 'profile'	, $profile );

		parent::initStates();
	}

	/**
	 * Determines if the language rows has been populated
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initialized()
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_languages' );
		$sql->column( 'COUNT(1)' );

		$db->setQuery( $sql );

		$initialized	= $db->loadResult() > 0;

		return $initialized;
	}

	/**
	 * Retrieves languages
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLanguages()
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_languages' );

		$order	= $this->getState( 'ordering' );

		if( $order )
		{
			$direction	= $this->getState( 'direction' );

			$sql->order( $order , $direction );
		}

		$db->setQuery( $sql );

		$result	= $db->loadObjectList();

		return $result;
	}

	/**
	 * Purges non installed languages
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function purge()
	{
		$db 	= FD::db();

		$sql	= $db->sql();

		$sql->delete( '#__social_languages' );
		$sql->where( 'state' , SOCIAL_LANGUAGES_NOT_INSTALLED );

		$db->setQuery( $sql );

		return $db->Query();
	}
}
