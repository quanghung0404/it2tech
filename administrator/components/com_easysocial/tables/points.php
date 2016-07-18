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

// Load parent table.
FD::import( 'admin:/tables/table' );

/**
 * Object mapping for `#__social_points`.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.0
 */
class SocialTablePoints extends SocialTable
{
	/**
	 * The unique id which is auto incremented.
	 * @var int
	 */
	public $id					= null;

	/**
	 * The command string.
	 * @var string
	 */
	public $command 			= null;

	/**
	 * The extension / app name.
	 * @var string
	 */
	public $extension 			= null;

	/**
	 * The title of the points
	 * @var string
	 */
	public $title				= null;

	/**
	 * Description of the list (Optional)
	 * @var string
	 */
	public $description			= null;

	/**
	 * The permalink for this point
	 * @var datetime
	 */
	public $alias				= null;

	/**
	 * Creation date of the list.
	 * @var datetime
	 */
	public $created				= null;

	/**
	 * The threshold for this point (Optional)
	 * @var int
	 */
	public $threshold 			= null;

	/**
	 * The interval for this points. 0 - every time , 1 - once , 2 - twice , n - (n)th times
	 * @var int
	 */
	public $interval 			= null;

	/**
	 * The number of points.
	 * @var int
	 */
	public $points 				= null;

	/**
	 * The state of this point. 0 - unpublished , 1 - published.
	 * @var int
	 */
	public $state 				= null;

	/**
	 * Custom params data for the points
	 * @var int
	 */
	public $params 				= null;

	/**
	 * Class construct
	 *
	 * @since	1.0
	 * @param	JDatabase
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__social_points' , 'id' , $db );
	}

	/**
	 * Retrieves the list of achievers for this point.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	int		The number of achievers.
	 */
	public function getAchievers()
	{
		$model 	= FD::model( 'Points' );
		return $model->getAchievers( $this->id );
	}

	/**
	 * Retrieves the extension translation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getExtensionTitle()
	{
		$extension	= 'COM_EASYSOCIAL';

		if( $this->extension != 'com_easysocial' )
		{
			$extension 	= strtoupper( $this->extension );

			// Load custom language
			FD::language()->load( $this->extension , JPATH_ROOT );
			FD::language()->load( $this->extension , JPATH_ADMINISTRATOR );
		}

		$text 	= $extension . '_POINTS_EXTENSION_' . strtoupper( $this->extension );

		return JText::_( $text );
	}

	/**
	 * Retrieves the list of achievers for this point.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	int		The number of achievers.
	 */
	public function getTotalAchievers()
	{
		$model 	= FD::model( 'Points' );
		return $model->getTotalAchievers( $this->id );
	}

	/**
	 * Checks if the target user belongs to a group that has access to this point.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The user's id.
	 * @return
	 */
	public function isAllowed( $userId )
	{
		$model 	= FD::model( 'Points' );
		return $model->isAllowed( $this->id , $userId );
	}


	/**
	 * Retrieve the points permalink
	 *
	 * @since	1.0
	 * @access	public
	 * @return	int		The total number of users who achieved this badge.
	 */
	public function getPermalink( $xhtml = false )
	{
		$url 	= FRoute::points( array( 'id' => $this->getAlias() , 'layout' => 'item' ) , $xhtml );

		return $url;
	}

	/**
	 * Retrieves the alias for this point
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The alias for this badge
	 */
	public function getAlias()
	{
		$alias 	= $this->id . ':' . $this->alias;

		return $alias;
	}

	/**
	 * Loads the points language based on the extension
	 * @since	1.0
	 * @access	public
	 *
	 */
	public function loadLanguage()
	{
		if( empty( $this->extension ) )
		{
			return;
		}

		$lang = FD::language();

		$lang->load( $this->extension, JPATH_ROOT );
		$lang->load( $this->extension, JPATH_ADMINISTRATOR );
	}
}
