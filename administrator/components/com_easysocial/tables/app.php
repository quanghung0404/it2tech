<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Dependencies
ES::import('admin:/tables/table');

class SocialTableApp extends SocialTable
{
	/**
	 * The unique id of the application
	 * @var int
	 */
	public $id = null;

	/**
	 * The type of the application. E.g: fields, applications
	 * @var string
	 */
	public $type = null;

	/**
	 * Determines if the application is a core application.
	 * @var int
	 */
	public $core = null;

	/**
	 * Determines if the application is only used for processing only.
	 * @var int
	 */
	public $system		= null;

	/**
	 * Determines if the application is a unique application.
	 * @var int
	 */
	public $unique		= null;

	/**
	 * The unique element of the application.
	 * @var string
	 */
	public $element		= null;

	/**
	 * The group type of the application. E.g: people, groups , events etc.
	 * @var string
	 */
	public $group 		= null;

	/**
	 * The title of the application
	 * @var string
	 */
	public $title		= null;

	/**
	 * The permalink of the application
	 * @var string
	 */
	public $alias		= null;

	/**
	 * The state of the application
	 * @var int
	 */
	public $state		= null;

	/**
	 * The user visibility of the application
	 * @var int
	 */
	public $visible		= null;

	/**
	 * The creation date time.
	 * @var datetime
	 */
	public $created		= null;

	/**
	 * The ordering of the application
	 * @var int
	 */
	public $ordering	= null;

	/**
	 * Custom parameters for the application
	 * @var string
	 */
	public $params		= null;

	/**
	 * The version number of the application.
	 * @var string
	 */
	public $version			= null;

	/**
	 * The author of the application
	 * @var string
	 */
	public $author			= null;

	/**
	 * Determines if this app plans to load as widgets
	 * @var string
	 */
	public $widget			= null;

	/**
	 * Determines if this app would be installable
	 * @var string
	 */
	public $installable		= null;

	/**
	 * Determines if this app would be installable
	 * @var string
	 */
	public $default		= null;

	/**
	 * Used for caching internally.
	 * @var Array
	 */
	public $layouts 		= null;

	public function __construct(& $db )
	{
		parent::__construct( '#__social_apps' , 'id' , $db );
	}

	public function load($keys = null, $reset = true)
	{
		// Liase to Dbcache
		$cache = FD::dbcache('app');
		$data = $cache->get($keys);

		if (!$data) {
			return false;
		}

		$state = $this->bind($data);

		// Load the app's language
		$this->loadLanguage();

		return $state;
	}

	/**
	 * Deprecated. Use native load function with array keys instead.
	 * Loads the application given the `element`, `type` and `group`.
	 *
	 * @since	1.0
	 * @deprecated	1.2
	 * @access	public
	 * @param	string	The unique element name. (E.g: notes )
	 * @param	string	The group of the application. (E.g: people or group)
	 * @param	string	The unique type of the app. (E.g: apps or fields )
	 *
	 * @return	bool	True on success false otherwise
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function loadByElement( $element , $group , $type )
	{
		return $this->load(array('element' => $element, 'group' => $group, 'type' => $type));

		// $db 	= FD::db();

		// $query		= array();
		// $query[]	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl );
		// $query[]	= 'WHERE ' . $db->nameQuote( 'element' ) . '=' . $db->Quote( $element );
		// $query[]	= 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );
		// $query[]	= 'AND ' . $db->nameQuote( 'group' ) . '=' . $db->Quote( $group );

		// $query 		= implode( ' ' , $query );
		// $db->setQuery( $query );

		// $result	= $db->loadObject();

		// if( !$result )
		// {
		// 	return false;
		// }

		// return parent::bind( $result );
	}

	/**
	 * Deprecated. Use native load function with array keys instead.
	 * Loads an application by group
	 *
	 * @deprecated	1.2
	 * @param	string	$element	The element to look for.
	 * @return	boolean	True on success false otherwise
	 */
	public function loadByGroup( $group , $element )
	{
		return $this->load(array('group' => $group, 'element' => $element));

		// $db 	= FD::db();

		// $query	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl )
		// 		. ' WHERE ' . $db->nameQuote( 'element' ) . '=' . $db->Quote( $element )
		// 		. ' AND ' . $db->nameQuote( 'group' ) . '=' . $db->Quote( $group );
		// $db->setQuery( $query );

		// $result	= $db->loadObject();

		// if (!$result)
		// {
		// 	return false;
		// }

		// return parent::bind( $result );
	}

	/**
	 * Loads the app's css file.
	 *
	 * @param	string	$element	The element to look for.
	 * @return	boolean	True on success false otherwise
	 */
	public function loadCss()
	{
		$doc 	= JFactory::getDocument();

		$file 	= SOCIAL_APPS . '/' . $this->group . '/' . $this->element . '/assets/styles/style.css';

		jimport( 'joomla.filessytem.file' );

		if( JFile::exists( $file ) )
		{
			$doc->addStyleSheet( rtrim( JURI::root() , '/' ) . '/media/com_easysocial/apps/' . $this->group . '/' . $this->element . '/assets/styles/style.css' );
		}
	}

	/**
	 * Determines if this app has user settings.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	boolean	True if app has user settings.
	 */
	public function hasUserSettings()
	{
		$file 	= SOCIAL_APPS . '/' . $this->group . '/' . $this->element . '/config/user.json';

		jimport( 'joomla.filesystem.file' );
		$exists	= JFile::exists( $file );

		return $exists;
	}

	/**
	 * Determines if the app should appear in the app listing
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function appListing( $view , $uid = '' , $type = '' )
	{
		$apps = ES::apps();
		$hasListing = $apps->hasAppListing($this, $view, $uid, $type);

		return $hasListing;
	}

	/**
	 * Determines if the current application has a bookmark view.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 *
	 * @return	boolean		True if app contains a bookmark, false otherwise.
	 */
	public function hasDashboard()
	{
		// Ensure that they are all in lowercase
		$group 		= strtolower( $this->group );
		$element	= strtolower( $this->element );

		// Build the path
		$path 		= SOCIAL_APPS . '/' . $group . '/' . $element . '/views/dashboard/view.html.php';

		jimport( 'joomla.filesystem.file' );

		$state 		= JFile::exists( $path );

		return $state;
	}

	/**
	 * Retrieve the local version of the application
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	string	The application version.
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Retrieves description of the app for the user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	string
	 */
	public function getUserDesc()
	{
		$text 	= 'APP_' . strtoupper( $this->element ) . '_' . strtoupper( $this->group ) . '_DESC_USER';

		return JText::_( $text );
	}

	/**
	 * Gets the application meta data from the manifest file.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialAppMeta
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getMeta()
	{
		if ($this->type == 'fields') {
			$manifestFile 	= SOCIAL_APPS . '/' . $this->type . '/' . $this->group . '/' . $this->element . '/' . $this->element . '.xml';
		}

		if ($this->type == 'apps') {
			$manifestFile 	= SOCIAL_APPS . '/' . $this->group . '/' . $this->element . '/' . $this->element . '.xml';
		}

		$meta 	= new SocialAppMeta( $manifestFile );

		return $meta;
	}

	/**
	 * Deletes any views that are related to the current view.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	boolean		True if success.
	 */
	public function deleteExistingViews()
	{
		$model 	= FD::model( 'Apps' );
		$state 	= $model->deleteExistingViews( $this->id );

		return $state;
	}

	/**
	 * Get's the application type in text.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	string	The string value for the application type.
	 */
	public function getTypeString()
	{
		$languageString 	= 'COM_EASYSOCIAL_APPS_TYPE_' . JString::strtoupper( $this->type );

		return JText::_( $languageString );
	}

	/**
	 * Gets a list of views that are assigned to this app.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function getViews( $viewName = '' )
	{

		$model 		= FD::model('Apps');
		$appViews 	= $model->getViews($this->id);

		foreach( $appViews as $view )
		{
			if ($view->view == $viewName) {
				$view->title 		= trim( $view->title );
				$view->description	= trim( $view->description );

				return $view;
			}

		}

		return false;

		// No cache is required as we are testing caching on dbcache layer in getViews function

		// static $views 	= array();

		// if( !isset( $views[ $this->id ] ) )
		// {
		// 	$model 		= FD::model( 'Apps' );
		// 	$appViews 	= $model->getViews( $this->id );

		// 	$views[ $this->id ]	= array();

		// 	if( $appViews )
		// 	{
		// 		foreach( $appViews as &$view )
		// 		{
		// 			$view->title 		= trim( $view->title );
		// 			$view->description	= trim( $view->description );

		// 			$views[ $this->id ][ $view->view ]	= $view;
		// 		}
		// 	}
		// }

		// return $views[ $this->id ][ $viewName ];
	}

	/**
	 * Gets a list of available layouts for this app.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function getLayout( $layout = '' )
	{
		static $layouts 	= array();

		if( $this->type !== SOCIAL_APPS_TYPE_APPS )
		{
			return false;
		}

		if( !$layouts[ $this->id ] )
		{
			// Build the path to the layouts file.
			$path 	= SOCIAL_APPS . '/' . $this->group . '/' . $this->element . '/config/layouts.json';

			jimport( 'joomla.filesystem.file' );

			if( !JFile::exists( $path ) )
			{
				return false;
			}

			$contents 	= JFile::read( $path );
			$result		= FD::json()->decode( $contents );

			// Let's re-organize these layouts.
			$layouts 	= array();

			foreach( $result as $item )
			{
				$layouts[ $item->view ]	= $item;
			}

			$this->layouts 	= $layouts;
		}

		return $this->layouts;
	}

	/**
	 * Determines if the app has activit log
	 *
	 * @since	1.1
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasActivityLog()
	{
		return FD::apps()->hasActivityLog( $this );
	}

	/**
	 * Determines if the current app has already been installed by the user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		User id
	 * @return	bool	Result
	 */
	public function isInstalled( $userId = null )
	{
		if( empty( $userId ) )
		{
			$userId = FD::user()->id;
		}

		$model 		= FD::model( 'Apps' );
		$installed	= $model->isInstalled( $this->id, $userId );

		return $installed;
	}

	/**
	 * Installs the app
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		User id
	 * @return	bool	Result
	 */
	public function install( $userId = null )
	{
		$user 	= FD::user( $userId );
		$userId = $user->id;

		$config = FD::config();
		$map	= FD::table( 'appsmap' );

		$map->app_id = $this->id;
		$map->uid = $userId;
		$map->type = SOCIAL_APPS_GROUP_USER;
		$map->created = FD::date()->toSql();

		$state = $map->store();

		if( !$state )
		{
			return false;
		}

		// @badge: apps.install
		// Assign a badge to the user when they install apps.
		$badge 	= FD::badges();
		$badge->log( 'com_easysocial' , 'apps.install' , $userId , JText::_( 'COM_EASYSOCIAL_APPS_BADGE_INSTALLED' ) );

		// Give points to the author when installing apps
		$points 	= FD::points();
		$points->assign( 'apps.install' , 'com_easysocial' , $userId );

		// Get the application settings
		$app 		= FD::table( 'App' );
		$app->load( array( 'type' => SOCIAL_TYPE_APPS , 'group' => SOCIAL_TYPE_USER , 'element' => SOCIAL_TYPE_APPS ) );
		$params 	= $app->getParams();

		// If configured to publish on the stream, share this to the world.
		if( $app->id && $params->get( 'stream_install' , true ) )
		{
			// lets add a stream item here.
			$stream 	= FD::stream();
			$template 	= $stream->getTemplate();

			$template->setActor( $user->id, SOCIAL_TYPE_USER );
			$template->setContext( $this->id, SOCIAL_TYPE_APPS );
			$template->setVerb( 'install' );
			$template->setType( SOCIAL_STREAM_DISPLAY_MINI );
			$template->setAggregate( false );
			$template->setParams( $this );

			$template->setAccess( 'core.view' );


			$stream->add( $template );
		}

		return true;
	}

	/**
	 * Allows caller to uninstall this app from the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function uninstall()
	{
		$path = SOCIAL_APPS . '/' . $this->group . '/' . $this->element;
		$exists = JFolder::exists($path);
		// Check if the folder exists.
		if ($exists) {

			// Try to delete the folder
			$state 	= JFolder::delete( $path );
		}

		// Delete app views
		$model	= FD::model( 'Apps' );
		$model->deleteExistingViews( $this->id );

		// Just delete this record from the database.
		$state 	= $this->delete();

		// Remove the stream item as well.
		FD::stream()->delete( $this->id , SOCIAL_TYPE_APPS );

		return $state;
	}

	/**
	 * Allows the caller to uninstall the app from the user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function uninstallUserApp( $userId = null )
	{
		$config 	= FD::config();
		$user 		= FD::user( $userId );
		$userId 	= $user->id;

		// Delete user mapping
		$map		= FD::table( 'appsmap' );
		$map->load( array( 'app_id' => $this->id , 'uid' => $userId ) );

		$state 		= $map->delete();

		// Give points to the author when uninstalling apps
		if( $state )
		{
			$points 	= FD::points();
			$points->assign( 'apps.uninstall' , 'com_easysocial' , $userId );
		}

		// Delete any stream that's related to the user installing this app
		$stream	= FD::stream();
		$stream->delete( $this->id , SOCIAL_TYPE_APPS , $userId );

		return $state;
	}

	/**
	 * Retrieves the new favicon style
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFavIcon()
	{
		$app 	= FD::apps()->getApp( $this );

		if( !$app )
		{
			return false;
		}

		if( !method_exists( $app , __FUNCTION__ ) )
		{
			return false;
		}

		return $app->getFavIcon();
	}

	/**
	 * Get's the icon's absolute path.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The size to load. (E.g: favicon, small , medium , cover )
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getIcon( $size = 'small' )
	{
		$path 	= SOCIAL_APPS;

		if( $this->type == 'fields' )
		{
			$path 	= $path . '/fields';
		}

		// If there's no icon provided for the app, we load our own default icons.
		$default 	= SOCIAL_DEFAULTS_URI . '/apps/' . $size . '.png';

		// Test for template overrides
		$app 		= JFactory::getApplication();
		$override 	= JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easysocial/apps/' . $this->group . '/' . $this->element . '/' . $size . '.png';

		if( JFile::exists( $override ) )
		{

			$url 	= JURI::root() . '/templates/' . $app->getTemplate() . '/html/com_easysocial/apps/' . $this->group . '/' . $this->element . '/' . $size . '.png';
			return $url;
		}


		$path 	= $path . '/' . $this->group . '/' . $this->element . '/assets/icons/' . $size . '.png';

		if( JFile::exists( $path ) )
		{
			$url	= SOCIAL_APPS_URI;

			if( $this->type == 'fields' )
			{
				$url = $url . '/fields';
			}

			$url = $url . '/' . $this->group . '/' . $this->element . '/assets/icons/' . $size . '.png';

			return $url;
		}

		return $default;
	}

	/**
	 * Retrieve user params
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserParams( $id = null )
	{
		$user 	= FD::user( $id );

		$map 	= FD::table( 'AppsMap' );
		$map->load( array( 'uid' => $user->id , 'app_id' => $this->id ) );

		$params 	= FD::registry( $map->params );

		return $params;
	}

	/**
	 * Retrieve user params
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getParams()
	{
		$params 	= FD::registry( $this->params );

		return $params;
	}

	/**
	 * Render's application parameters form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	string	HTML codes.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function renderForm( $type = 'form' , $params = null , $prefix = '' , $tabs = false )
	{
		// Get the manifest path.
		$file		= $this->getManifestPath( $type );

		if( $file === false )
		{
			return false;
		}

		$registry = FD::makeObject( $file );

		// Check for custom callbacks
		foreach( $registry as &$section )
		{
			foreach( $section->fields as &$field )
			{
				if( isset( $field->callback ) )
				{
					$callable = FD::apps()->getCallable( $field->callback );

					if( !$callable )
					{
						continue;
					}

					$field->options = call_user_func_array( $callable, array( $this ) );
				}
			}
		}

		// Get the parameter object.
		$form 		= FD::get( 'Form' );
		$form->load( $registry );

		if( $params )
		{
			$form->bind( $params );
		}
		else
		{
			// Bind the stored data with the params.
			$form->bind( $this->params );
		}

		// Get the HTML output.
		return $form->render( $tabs , false , '' , $prefix );
	}

	/**
	 * Returns the path of the manifest file for this application.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The path to the manifest file.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getManifestPath( $type = 'config', $extension = 'json' )
	{
		$name	= strtolower( $type === '' ? $this->element : $type );

		$path 	= SOCIAL_APPS;

		if( $this->type == 'fields' )
		{
			$path 	= SOCIAL_FIELDS;
		}

		$path 	= $path . '/' . $this->group . '/' . $this->element . '/config/' . $type . '.' . $extension;

		jimport( 'joomla.filesystem.file' );

		if( JFile::exists( $path ) )
		{
			return $path;
		}

		return false;
	}

	/**
	 * Returns the manifest of the application
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The type of the manifest.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getManifest( $manifestType = 'config' )
	{
		// Get the path to the manifest file.
		$path = $this->getManifestPath( $manifestType );

		if( $path === false )
		{
			return false;
		}

		// Loads the language of this app
		// $this->loadLanguage();

		// Load json library
		$json 	= FD::json();

		// Get the json contents from the path.
		$raw 	= JFile::read( $path );

		// Let's decode the object.
		$obj 	= $json->decode( $raw );

		$this->getExtendedManifest($obj, $manifestType);

		return $obj;
	}

	private function getExtendedManifest(&$obj, $manifestType)
	{
		// It is possible that the manifest extends from another app/field
		if( !empty( $obj->extends ) )
		{
			// Extends should be in the format of:
			// "extends": "type/group/element"

			list( $type, $group, $element ) = explode( '/', $obj->extends );
			$extendApp = FD::table( 'app' );
			$state = $extendApp->load( array( 'type' => $type, 'group' => $group, 'element' => $element ) );

			if( $state )
			{
				$manifest = $extendApp->getManifest( $manifestType );

				if( $manifest !== false )
				{
					// Manually perform a deep array merge
					foreach( $manifest as $name => $params )
					{
						// If this key does not exist in the parent object, then carry it over
						// Else, the child one should merge into parent
						if( !isset( $obj->$name ) )
						{
							$obj->$name = $params;
						}
						else
						{
							// Only do a merge if both parent and child is not a boolean
							// If either one is a boolean, then we just use the parent's data as is
							if( !is_bool( $params ) && !is_bool( $obj->$name ) )
							{
								$obj->$name = (object) array_merge( (array) $params, (array) $obj->$name );
							}
						}
					}
				}
			}

			unset( $obj->extends );
		}
	}

	public function getElement()
	{
		return $this->element;
	}

	public function installAlerts()
	{
		$rules = $this->getManifest( $this->element, 'alert' );

		if( !$rules )
		{
			return false;
		}

		$alert = FD::alert( $this->element );

		$options = array( 'core' => $this->core, 'app' => 1 );

		foreach( $rules as $rulename => $values )
		{
			$alert->register( $rulename, $values->email, $values->system, $options );
		}

		return true;
	}

	public function isCore()
	{
		return (bool) $this->core;
	}

	/**
	 * Returns the alias of the app
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlias()
	{
		$alias 	= $this->id . ':' . $this->alias;

		return $alias;
	}

	/**
	 * Retrieves the permalink of an app
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink($layout, $segments = array(), $xhtml = false)
	{
		$options 	= array('id' => $this->getAlias());

		$options['layout']	= $layout;

		// Merge the segments with the options
		$options 	= array_merge($options, $segments);

		if (isset($options['userId'])) {

			$user 	= FD::user($options['userId']);
			$options['uid']		= $user->getAlias();
			$options['type']	= SOCIAL_TYPE_USER;

			unset($options['userId']);
		}

		if (isset($options['groupId'])) {

			$group 	= FD::group($options['groupId']);
			$options['uid']		= $group->getAlias();
			$options['type']	= SOCIAL_TYPE_GROUP;

			unset($options['groupId']);
		}

		if (isset($options['eventId'])) {

			$event = FD::event($options['eventId']);
			$options['uid'] = $event->getAlias();
			$options['type'] = SOCIAL_TYPE_EVENT;

			unset($options['eventId']);
		}

		$url 		= FRoute::apps($options);

		return $url;
	}

	/**
	 * Gets the user permalink for the app
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserPermalink( $userAlias )
	{
		// Get the profile view
		$view	= $this->getViews( 'profile' );
		$type 	= $view->type;

		// The app is embedded on the page
		if ($type == 'embed') {
			$url 	= FRoute::profile( array( 'id' => $userAlias , 'appId' => $this->getAlias() ) );

			return $url;
		}

		// If it's a canvas view
		$url 	= FRoute::apps( array( 'id' => $this->getAlias() , 'layout' => 'canvas' , 'type' => 'user', 'uid' => $userAlias ) );


		return $url;
	}

	/**
	 * Retrieves the canvas url
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCanvasUrl( $options = array() , $xhtml = true )
	{
		$default	= array( 'layout' => 'canvas' , 'id' => $this->getAlias() );
		$options	= array_merge( $default , $options );

		$url 		= FRoute::apps( $options , $xhtml );

		return $url;
	}

	/**
	 * Determines if the app is accessible by the provided user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id
	 * @return
	 */
	public function accessible( $id )
	{
		if( !$this->isInstalled( $id ) && !$this->default )
		{
			return false;
		}

		return true;
	}

	public function getAppTitle()
	{
		$element 	= strtoupper($this->element);
		$group 		= strtoupper($this->group);

		return JText::_('APP_' . $element . '_' . $group . '_TITLE');
	}

	public function getAppClass()
	{

		static $classes = array();

		$className = '';

		if($this->type == 'apps') {
			$className = 'Social' . ucfirst($this->group) . 'App' . ucfirst($this->element);
		}

		if($this->type == 'fields') {
			$className = 'SocialFields' . ucfirst($this->group) . ucfirst($this->element);
		}

		if( empty( $classes[$className] ) )
		{
			$root = $this->type == 'apps' ? SOCIAL_APPS : SOCIAL_FIELDS;

			$path = $root . '/' . $this->group . '/' . $this->element . '/' . $this->element . '.php';

			if( !JFile::exists( $path ) )
			{
				return false;
			}

			include_once( $path );

			$args = array( 'group' => $this->group, 'element' => $this->element );

			$class = new $className( $args );

			$classes[$className] = $class;
		}

		return $classes[$className];
	}


	/**
	 * Returns the page title
	 *
	 * @since	1.2.8
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPageTitle()
	{
		$page 	= FD::page();

		$title	= $page->getSiteTitle($this->get('title'));

		return $title;
	}

	/**
	 * Shorthand to load this app's language
	 *
	 * @since	1.1
	 * @access	public
	 *
	 */
	public function loadLanguage($reload = false, $default = true)
	{
		if (empty($this->type) || empty($this->group) || empty($this->element)) {
			return false;
		}

		$method = '';

		if ($this->type == SOCIAL_APPS_TYPE_APPS) {
			$method = 'app';
		}

		if ($this->type == SOCIAL_APPS_TYPE_FIELDS) {
			$method = 'field';
		}

		if (!$method) {
			return false;
		}

		$method = 'load' . strtoupper($method);

		$lang = FD::language();

		$lang->$method($this->group, $this->element, $reload, $default);

		return true;
	}
}

/**
 * Meta for apps
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialAppMeta
{
	public $author		= null;
	public $url 		= null;
	public $created		= null;
	public $version 	= null;
	public $desc 		= null;

	/**
	 * This is the parser.
	 * @var SocialParser
	 */
	private $parser		= null;

	public function __construct( $path )
	{
		if( JFile::exists($path) )
		{
			$parser 	= FD::get( 'Parser' );
			$parser->load( $path );

			$this->parser 	= $parser;

			// Initialize variables.
			$this->init();
		}
	}

	/**
	 * Initializes all the properties.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function init()
	{
		// @TODO: Dynamically load all the methods that begins with "set"
		$this->setAuthor();
		$this->setURL();
		$this->setCreated();
		$this->setVersion();
		$this->setDescription();
	}

	/**
	 * Sets the app description
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function setDescription()
	{
		$desc 	= $this->parser->xpath( 'description' );

		if( !empty( $desc ) )
		{
			$this->desc 	= (string) $desc[0];
			$this->desc 	= trim( $this->desc );

			// Remove trailing whitespaces.
			$this->desc 	= JText::_( $this->desc );
		}
	}

	/**
	 * Sets the author name.
	 *
	 * @since	1.0
	 * @access	public
	 * @author 	Mark Lee <mark@stackideas.com>
	 */
	private function setAuthor()
	{
		$author		= $this->parser->xpath( 'author' );

		if( !empty( $author ) )
		{
			$this->author 	= (string) $author[ 0 ];
		}
	}

	/**
	 * Sets the author's url.
	 *
	 * @since	1.0
	 * @access	public
	 * @author 	Mark Lee <mark@stackideas.com>
	 */
	private function setURL()
	{
		$url		= $this->parser->xpath( 'url' );

		if( !empty( $url ) )
		{
			$this->url 	= (string) $url[ 0 ];
		}
	}

	/**
	 * Sets the creation date
	 *
	 * @since	1.0
	 * @access	public
	 * @author 	Mark Lee <mark@stackideas.com>
	 */
	private function setCreated()
	{
		$created		= $this->parser->xpath( 'created' );

		if( !empty( $created ) )
		{
			$this->created 	= (string) $created[ 0 ];
		}
	}


	/**
	 * Sets the version
	 *
	 * @since	1.0
	 * @access	public
	 * @author 	Mark Lee <mark@stackideas.com>
	 */
	private function setVersion()
	{
		$version		= $this->parser->xpath( 'version' );

		if( !empty( $version ) )
		{
			$this->version 	= (string) $version[ 0 ];
		}
	}
}
