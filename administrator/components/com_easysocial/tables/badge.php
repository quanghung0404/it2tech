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
 * Object mapping for `#__social_badges`.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.0
 */
class SocialTableBadge extends SocialTable
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
	 * Explains steps needed to unlock the badge.
	 * @var string
	 */
	public $howto			= null;

	/**
	 * The permalink for this point
	 * @var datetime
	 */
	public $alias				= null;

	/**
	 * The avatar file for this badge
	 * @var int
	 */
	public $avatar 			= null;

	/**
	 * Creation date of the list.
	 * @var datetime
	 */
	public $created				= null;

	/**
	 * The state of this point. 0 - unpublished , 1 - published.
	 * @var int
	 */
	public $state 				= null;

	/**
	 * The frequency for this badge before user achieves this.
	 * @var int
	 */
	public $frequency			= null;


	/**
	 * Stored internally
	 */
	public $achieved_date 	= null;

	/**
	 * Class construct
	 *
	 * @since	1.0
	 * @param	JDatabase
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__social_badges' , 'id' , $db );
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

		if( $this->extension != SOCIAL_COMPONENT_NAME )
		{
			$extension 	= strtoupper( $this->extension );

			// Load custom language
			FD::language()->load( $this->extension , JPATH_ROOT );
			FD::language()->load( $this->extension , JPATH_ADMINISTRATOR );
		}

		$text 	= $extension . '_BADGES_EXTENSION_' . strtoupper( $this->extension );

		return JText::_( $text );
	}

	/**
	 * Retrieve a number of users who achieved this badge.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	int		The total number of users who achieved this badge.
	 */
	public function getTotalAchievers()
	{
		$model 	= FD::model( 'Badges' );
		$total 	= $model->getTotalAchievers( $this->id );

		return $total;
	}

	/**
	 * Override parent's get behavior so that we can load admin's language file.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function get($key, $default = '')
	{
		FD::language()->loadAdmin();

		return parent::get($key, $default);
	}

	/**
	 * Retrieve a users who has unlocked this badge.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	Array		An array of SocialUser objects.
	 */
	public function getAchievers( $options = array() )
	{
		$model 	= FD::model( 'Badges' );

		$users 	= $model->getAchievers( $this->id, $options );

		return $users;
	}

	/**
	 * Retrieve the badge permalink
	 *
	 * @since	1.0
	 * @access	public
	 * @return	int		The total number of users who achieved this badge.
	 */
	public function getPermalink( $xhtml = false , $external = false )
	{
		$url 	= FRoute::badges( array( 'id' => $this->getAlias() , 'external' => $external , 'layout' => 'item' ) , $xhtml );

		return $url;
	}

	/**
	 * Retrieves the alias for this badge
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
	 * Override parent's delete implementation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	boolean		True on success, false otherwise.
	 */
	public function delete( $pk = null )
	{
		$state 	= parent::delete();

		// Get the model
		$model 	= FD::model( 'Badges' );

		// Delete the user's badge associations
		$model->deleteAssociations( $this->id );

		// Delete the user's badge history
		$model->deleteHistory( $this->id );

		// Delete any stream related items for this badge
		$stream 	= FD::stream();
		$stream->delete( $this->id , SOCIAL_TYPE_BADGES );

		return $state;
	}

	/**
	 * Retrieves the avatar of the bage
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAvatar()
	{
		jimport( 'joomla.filesystem.file' );

		// Allow template overrides for badges
		$app 		= JFactory::getApplication();
		$avatar 	= basename($this->avatar);
		$override 	= JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easysocial/badges/' . $avatar;

		if (JFile::exists($override)) {

			$url	= rtrim(JURI::root()) . '/templates/' . $app->getTemplate() . '/html/com_easysocial/badges/' . $avatar;

			return $url;
		}

		// Construct the avatar file.
		$file 	= JPATH_ROOT . '/' . $this->avatar;

		// Test if the file exists.
		if (!JFile::exists($file)) {

			// @TODO: Configurable default badge location
			$default 	= rtrim( JURI::root() , '/' ) . '/media/com_easysocial/avatars/defaults/badges/default.png';

			return $default;
		}

		$url	 = rtrim( JURI::root() , '/' ) . '/' . $this->avatar;

		return $url;
	}

	/**
	 * Loads the point record given the composite indices.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The command to lookup for.
	 * @param	string		The extension to lookup for.
	 * @return	bool		True if exists, false otherwise.
	 */
	public function loadByCommand( $extension , $command )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( $this->_tbl );

		$sql->where( 'command', $command );
		$sql->where( 'extension', $extension );

		$db->setQuery( $query );

		$row 		= $db->loadObject();

		if( !$row )
		{
			return false;
		}

		return parent::bind( $row );
	}

	/**
	 * Retrieves the achievement date
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAchievedDate()
	{
		$date 	= FD::date( $this->achieved_date );

		return $date;
	}

	/**
	 * Loads the badge language based on the extension
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
