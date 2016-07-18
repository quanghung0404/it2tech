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

jimport('joomla.application.component.model');

FD::import('admin:/includes/model');

class EasySocialModelApps extends EasySocialModel
{
	private $data = null;
	protected $pagination	= null;

	protected $limitstart 	= null;
	protected $limit 		= null;

	public function __construct( $config = array() )
	{
		parent::__construct( 'apps' , $config );
	}

	/**
	 * Loads the css for apps on the site
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadAppCss($options = array())
	{
		static $loaded = false;

		if (!$loaded) {
			$apps = $this->getApps($options);

			// We need to load the app's own css file.
			if ($apps) {
				foreach ($apps as $app) {
					$app->loadCss();
				}
			}

			$loaded = true;
		}
	}

	/**
	 * Removes app from the `#__social_apps_map` table
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The application id
	 * @return
	 */
	public function removeUserApp( $id )
	{

		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->delete( '#__social_apps_map' );
		$sql->where( 'app_id' , $id );

		$db->setQuery( $sql );
		$state	= $db->Query();

		return $state;
	}

	/**
	 * Initializes all the generic states from the form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initStates()
	{
		$state 	= $this->getUserStateFromRequest( 'state' , 'all' );
		$filter	= $this->getUserStateFromRequest( 'filter' , 'all' );
		$group	= $this->getUserStateFromRequest( 'group' , 'all' );

		$this->setState( 'group'	, $group );
		$this->setState( 'filter'	, $filter );
		$this->setState( 'state' 	, $state );

		parent::initStates();
	}

	/**
	 * Deletes existing views for specific app id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 		The application id.
	 * @return	boolean		True if success false otherwise.
	 */
	public function deleteExistingViews( $appId )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->delete( '#__social_apps_views' );
		$sql->where( 'app_id', $appId );

		$db->setQuery( $sql );

		$state 		= $db->Query();

		return $state;
	}

	/**
	 * Deletes discovered items
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteDiscovered()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->delete( '#__social_apps' );
		$sql->where( 'state', SOCIAL_APP_STATE_DISCOVERED );

		$db->setQuery( $sql );

		$state 		= $db->Query();

		return $state;
	}

	/**
	 * Discover new applications on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function discover()
	{
		// Default paths
		$folders = array(SOCIAL_APPS . '/user', SOCIAL_APPS . '/group', SOCIAL_APPS . '/event', SOCIAL_FIELDS . '/user', SOCIAL_FIELDS . '/group', SOCIAL_FIELDS . '/event');
		$total = 0;

		// Go through each of the folders and look for any app folders.
		foreach ($folders as $folder) {

			if (!JFolder::exists($folder)) {
				continue;
			}

			$items = JFolder::folders($folder, '.', false, true);

			foreach ($items as $item) {

				// Load the installer and pass in the folder
				$installer = ES::installer();
				$installer->load($item);

				$state = $installer->discover();

				if ($state) {
					$total += 1;
				}
			}
		}

		return $total;
	}


	/**
	 * Determines if the app has been installed in the system
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The app's id.
	 * @param	int		The user's id.
	 * @return	bool	Result
	 */
	public function isAppInstalled( $element , $group , $type )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_apps' );
		$sql->column( 'COUNT(1)' , 'count' );
		$sql->where( 'element', $element );
		$sql->where( 'group', $group );
		$sql->where( 'type', $type );

		$db->setQuery( $sql );

		$installed 	= (bool) $db->loadResult();

		return $installed;
	}

	/**
	 * Determines if the app has been installed by the provided user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The app's id.
	 * @param	int		The user's id.
	 * @return	bool	Result
	 */
	public function isInstalled( $appId, $userId = null )
	{
		if( empty( $userId ) )
		{
			$userId = FD::user()->id;
		}

		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_apps_map' );
		$sql->where( 'app_id', $appId );
		$sql->where( 'uid', $userId );

		$db->setQuery( $sql->getTotalSql() );
		$installed 	= (bool) $db->loadResult();

		return $installed;
	}

	/**
	 * Retrieve a list of applications that is installed on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of configuration.
	 * @return	Array	An array of application object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getItemsWithState($options = array())
	{
		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_apps');

		// Determine if we should only fetch apps that are widgets
		$widget = isset($options['widget']) ? $options['widget'] : false;

		if ($widget) {
			$sql->where('widget', SOCIAL_STATE_PUBLISHED);
		}

		// Depending on type of apps.
		$filter = $this->normalize($options, 'filter', 'all');

		if ($filter && $filter != 'all') {
			$sql->where('type', $filter);
		}

		// Filter by group
		$group 		= $this->getState( 'group' );

		if( $group && $group != 'all' )
		{
			$sql->where( 'group' , $group );
		}

		// Search filter
		$search 	= $this->getState( 'search' );

		if( $search )
		{
			$sql->where( 'title' , '%' . $search . '%' , 'LIKE' );
		}

		// Depending on group of apps.
		$group 		= isset( $options[ 'group' ] ) ? $options[ 'group' ] : '';

		if( $group )
		{
			$sql->where( 'group', $group );
		}

		// Discover apps
		$discover 	= isset( $options[ 'discover' ] ) ? $options[ 'discover' ] : '';

		if( $discover )
		{
			$sql->where( 'state', SOCIAL_APP_STATE_DISCOVERED );
		}
		else
		{
			// State filters
			$state 		= $this->getState( 'state' );

			if( $state !== '' && $state != 'all' )
			{
				$sql->where( 'state', $state );
			}

			$sql->where( '(' );
			$sql->where( 'state' , SOCIAL_STATE_PUBLISHED , '=' , 'OR' );
			$sql->where( 'state' , SOCIAL_STATE_UNPUBLISHED  , '=' , 'OR' );
			$sql->where( ')' );

			$sql->where( 'state', SOCIAL_APP_STATE_DISCOVERED , '!=' );
		}

		// Check for ordering
		$ordering 	= $this->getState( 'ordering' );

		if( $ordering )
		{
			$direction	 = $this->getState( 'direction' ) ? $this->getState( 'direction' ) : 'DESC';

			$sql->order( $ordering , $direction );
		}

		$limit 	= $this->getState( 'limit' , 0 );

		if( $limit )
		{
			$this->setState( 'limit' , $limit );

			// Get the limitstart.
			$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
			$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState( 'limitstart' , $limitstart );

			// Set the total number of items.
			$this->setTotal( $sql->getSql() , true );

			// Get the list of users
			$result			= parent::getData( $sql->getSql() );
		}
		else
		{
			// Set the total
			$this->setTotal( $sql->getTotalSql() );

			// Get the result using parent's helper
			$result		= $this->getData( $sql->getSql() );
		}


		if( !$result )
		{
			return $result;
		}

		$apps 	= array();

		foreach( $result as $row )
		{
			$appTable 	= FD::table( 'App' );
			$appTable->bind( $row );

			$apps[]		= $appTable;
		}

		return $apps;
	}


	/**
	 * Retrieve a list of applications that is installed on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of configuration.
	 * @return	Array	An array of application object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getItems( $options = array() )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_apps' );

		// Determine if we should only fetch apps that are widgets
		$widget 	= isset( $options[ 'widget' ] ) ? $options[ 'widget' ] : false;

		if( $widget )
		{
			$sql->where( 'widget' , SOCIAL_STATE_PUBLISHED );
		}

		// Depending on group of apps.
		$group 		= isset( $options[ 'group' ] ) ? $options[ 'group' ] : '';

		if( $group )
		{
			$sql->where( 'group', $group );
		}

		$db->setQuery( $sql );
		$result 	= $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		$apps 	= array();

		foreach( $result as $row )
		{
			$appTable 	= FD::table( 'App' );
			$appTable->bind( $row );

			$apps[]		= $appTable;
		}

		return $apps;
	}

	/**
	 * Retrieve a list of SocialTableAppViews for an app.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The app's id.
	 * @return
	 */
	public function getViews( $appId )
	{
		// TODO: Change this to where case.
		$cache = FD::dbcache('appview');
		$result = $cache->loadObjectList(array('app_id' => $appId));
		$views = $cache->bindTable($result);

		return $views;
	}

	public function getElement( $type , $element , $lookup )
	{
		$path		= SOCIAL_MEDIA . DS . constant( 'SOCIAL_APPS_' . strtoupper( $type ) ) . DS . $element . DS . $element . '.xml';
		$data		= JText::_( 'Unknown' );
		$xml        = FD::get( 'Parser' )->read( $path );

		if( isset( $xml->{$lookup} ) )
		{
			$data   = $xml->{$lookup};
		}
		return $data;
	}

	/**
	 * Get's a list of folder and determines if the folder is writable.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	Array	An array of stdClass objects.
	 *
	 */
	public function getDirectoryPermissions()
	{
		$jConfig = ES::jconfig();

		// Get a list of folders.
		$folders = array(
						$jConfig->getValue( 'tmp_path' ),
						SOCIAL_MEDIA,
						SOCIAL_APPS . '/fields',
						SOCIAL_APPS . '/user'
					);

		$directories	= array();

		foreach ($folders as $folder) {
			$obj = new stdClass();
			$obj->path = $folder;
			$obj->writable = is_writable($folder);

			$directories[] = $obj;
		}

		return $directories;
	}

	/**
	 * This is a temporary method until @1.3 allows the group the ability to add new apps
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getGroupApps( $groupId )
	{
		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_apps', 'a');
		$sql->column('a.*');

		$sql->where('a.group', SOCIAL_TYPE_GROUP);
		$sql->where('a.state', SOCIAL_STATE_PUBLISHED);
		$sql->where('a.type', SOCIAL_APPS_TYPE_APPS);
		$sql->where('a.system', SOCIAL_STATE_PUBLISHED, '!=');

		$db->setQuery($sql);
		$result = $db->loadObjectList();

		$apps = array();

		foreach ($result as $row) {
			$app = ES::table('App');
			$app->bind($row);

			$hasListing = $app->appListing('groups', $groupId, SOCIAL_TYPE_GROUP);

			if ($hasListing) {
				$apps[] = $app;
			}
		}

		return $apps;
	}

	/**
	 * This is a temporary method until @future allows the event the ability to add new apps
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getEventApps($eventId)
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_apps');

		$sql->where('group', SOCIAL_TYPE_EVENT);
		$sql->where('state', SOCIAL_STATE_PUBLISHED);
		$sql->where('type', SOCIAL_APPS_TYPE_APPS);
		$sql->where('system', SOCIAL_STATE_PUBLISHED, '!=');

		$db->setQuery($sql);
		$result = $db->loadObjectList();

		$apps = array();

		foreach ($result as $row) {
			$app = FD::table('App');
			$app->bind($row);

			// Check if the apps should really have such view
			if ($app->appListing('events', $eventId, SOCIAL_TYPE_EVENT)) {
				$apps[] = $app;
			}
		}

		return $apps;
	}

	/**
	 * Returns a list of field type applications that are installed and published.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of options.
	 * @return	Array	An array of SocialTableField item.
	 */
	public function getApps($options = array(), $debug = false)
	{
		static $cache = array();

		$db = FD::db();
		$sql = $db->sql();

		// Serialize the key so that we can cache them
		ksort($options);
		$idx = serialize($options);

		if (!isset($cache[$idx])) {

			$sql->select('#__social_apps', 'a');
			$sql->column('a.*');

			// If uid / key is passed in, we need to only fetch apps that are related to the uid / key.
			$uid = $this->normalize($options, 'uid');
			$key = $this->normalize($options, 'key');

			if (!is_null($uid) && !is_null($key)) {
				$sql->join('#__social_apps_map', 'b');
				$sql->on('b.app_id', 'a.id');
				$sql->on('b.uid', $uid);
				$sql->on('b.type', $key);

				$sql->where('a.state', SOCIAL_STATE_PUBLISHED);
			}

			// Test if 'view' is provided. If view is provided, we only want to fetch apps for these views.
			$view = $this->normalize($options, 'view');

			if (!is_null($view)) {
				$sql->innerjoin('#__social_apps_views', 'c' );
				$sql->on( 'c.app_id', 'a.id' );
				$sql->on( 'c.view', $view );
			}

			// If state filter is provided, we need to filter the state.
			$state = $this->normalize($options, 'state');

			if (!is_null($state)) {
				$sql->where('a.state', $state);
			}

			// If type filter is provided, we need to filter the type.
			$type = $this->normalize($options, 'type');

			if (!is_null($type)) {
				$sql->where('a.type', $type);
			}

			// If group filter is provided, we need to filter apps by group.
			$group = $this->normalize($options, 'group');

			if (!is_null($group)) {
				$sql->where('a.group', $group);
			}

			// Detect if we should only pull apps that are installable
			$installable = $this->normalize($options, 'installable');

			if (!is_null($installable)) {
				$sql->where('(', '', '', 'AND');
				$sql->where('a.installable', $installable , '=' , 'AND');
				$sql->where('a.default', SOCIAL_STATE_PUBLISHED, '!=', 'AND');
				$sql->where(')');
				$sql->where('a.state', SOCIAL_STATE_PUBLISHED);
			}

			// Check for widgets
			$widgets = $this->normalize($options, 'widget');

			if ($widgets) {
				$sql->where('a.widget', $widgets);
			}

			// Check for core app
			$core = $this->normalize($options, 'core');

			// If core is provided, we want to load core apps
			if (!is_null($core)) {
				$sql->where('a.core', $core);
			}

			// What is this?
			if (!is_null($uid) && !is_null($key) && $group != 'group') {
				$sql->where( '(' , '' , '' , 'AND' );
				$sql->where( 'a.default' , SOCIAL_STATE_PUBLISHED , '=' , 'OR' );
				$sql->where( 'b.id' , null , 'IS NOT' , 'OR' );

				if ($widgets) {
					$sql->where('a.system', true , '=' , 'OR');
				}

				// If there is a list of inclusion given, we need to include these apps as well
				$inclusion = $this->normalize($options, 'inclusion', null);

				if (!is_null($inclusion) && $inclusion) {
					$sql->where('a.id', $inclusion, 'IN', 'OR');
				}

				$sql->where( ')' );
			}

			// What is this?
			if (!$uid && !$key && is_null($installable) && (is_null($type) || $type == SOCIAL_APPS_TYPE_APPS)) {
				$sql->where('a.default', SOCIAL_STATE_PUBLISHED, '=', 'OR');
			}

			// Sorting and ordering options
			$sort = $this->normalize($options, 'sort');

			if (!is_null($sort)) {
				$order = $this->normalize($options, 'order', 'asc');
				$sql->order($sort, $order);
			}

			// Set the total query.
			$this->setTotal($sql->getTotalSql());

			// Get data
			$result = $this->getData($sql->getSql(), false);

			if (!$result) {
				$cache[$idx] = false;
				return $cache[$idx];
			}

			$apps = array();

			foreach ($result as $row) {
				$app = ES::table('App');
				$app->bind($row);

				// 3rd party apps might have their language strings
				$app->loadLanguage();

				// Check if the apps should really have such view
				if ($view && $app->appListing($view)) {
					$apps[]	= $app;
				} else {
					$apps[] = $app;
				}
			}

			$cache[$idx] = $apps;
		}

		return $cache[$idx];
	}


	/**
	 * Returns a list of user type applications that are installed and published.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	Array	An array of SocialTableApps item.
	 */
	public function getUserApps( $userId, $view = '' )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_apps', 'a' );
		$sql->column( 'a.*' );
		$sql->innerjoin( '#__social_apps_map', 'b' );
		$sql->on( 'b.app_id', 'a.id' );
		$sql->on( 'b.uid', $userId );
		$sql->on( 'b.type', SOCIAL_APPS_GROUP_USER );

		// Test if 'view' is provided. If view is provided, we only want to fetch apps for these views.
		if( $view )
		{
			$sql->innerjoin( '#__social_apps_views', 'c' );
			$sql->on( 'c.app_id', 'a.id' );
			$sql->on( 'c.view', $view );
		}

		$sql->where( 'a.state', SOCIAL_STATE_PUBLISHED );
		$sql->where( 'a.type', SOCIAL_APPS_TYPE_APPS );
		$sql->where( 'a.group', SOCIAL_APPS_GROUP_USER );

		// Set the total query.
		$this->setTotal( $sql->getTotalSql() );

		// Get data
		$result 	= $this->getData( $sql->getSql(), false );

		if( !$result )
		{
			return false;
		}

		$apps 	= array();

		foreach( $result as $row )
		{
			$app 		= FD::table( 'App' );
			$app->bind( $row );

			$apps[]		= $app;
		}

		return $apps;
	}



	/**
	 * Retrieve a list of core apps from the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultApps( $config = array() )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_apps', 'a' );
		$sql->column( 'a.*' );

		$sql->where( '(' );
		$sql->where( 'a.core', '1' );
		$sql->where( 'a.default', '1', '=', 'or' );
		$sql->where( ')' );
		$sql->where( 'a.state', SOCIAL_STATE_PUBLISHED );

		// If caller wants only specific type of apps.
		if( isset( $config[ 'type' ] ) )
		{
			$sql->where( 'a.type', $config[ 'type' ] );
		}

		$db->setQuery( $sql );

		$fields	= $db->loadObjectList();

		return $fields;
	}

	/**
	 * Returns a list of tending apps from the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getTrendingApps( $options = array() )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_apps', 'a' );
		$sql->column( 'a.*' );

		$sql->leftjoin( '#__social_apps_map', 'b' );
		$sql->on( 'a.id', 'b.app_id' );

		$sql->where( 'a.state', SOCIAL_STATE_PUBLISHED );

		if (isset($options['type'])) {
			$sql->where( 'a.type', $options['type'] );
		}

		if (isset($options['timefrom'])) {
			$sql->where( 'b.created', FD::date( $options['timefrom'] )->toSql(), '>=' );
		}

		if (isset($options['timeto'])) {
			$sql->where( 'b.created', FD::date( $options['timeto'] )->toSql(), '<=' );
		}


		// If group filter is provided, we need to filter apps by group.
		$group 		= isset( $options[ 'group' ] ) ? $options[ 'group' ] : null;

		if (!is_null($group)) {
			$sql->where( 'a.group', $group );
		}

		// Determines if caller wants to only display the installable apps
		$installable 	= isset( $options[ 'installable' ] ) ? $options[ 'installable' ] : '';


		if( $installable )
		{
			$sql->where( '(' , '' , '' , 'AND' );
			$sql->where( 'a.installable' , $installable , '=' , 'AND' );
			$sql->where( 'a.default' , SOCIAL_STATE_PUBLISHED , '!=' , 'AND' );
			$sql->where( ')' );

			$sql->where( 'a.state' , SOCIAL_STATE_PUBLISHED );
		}

		$sql->group( 'a.id' );
		$sql->order( 'b.app_id', 'desc', 'count' );

		$db->setQuery( $sql );

		$result = $db->loadObjectList();

		$apps = array();

		foreach ($result as $row) {
			$app 		= FD::table( 'App' );
			$app->bind( $row );

			$apps[]		= $app;
		}

		return $apps;
	}

	public function assignProfileUsersApps($profileId, $appId)
	{
		$db = ES::db();
		$sql = $db->sql();

		$now = ES::date()->toSql();

		$query = "insert into `#__social_apps_map` (`uid`, `type`, `app_id`, `created`) select a.user_id, 'user', " . $db->Quote($appId) . ", " . $db->Quote($now);
		$query .= " from `#__social_profiles_maps` as a";
		$query .= " where not exists (select b.`uid` from `#__social_apps_map` as b where b.`uid` = a.`user_id` and b.`type` = " . $db->Quote(SOCIAL_TYPE_USER) . " and b.`app_id` = " . $db->Quote($appId) . ")";
		$query .= " and a.`profile_id` = " . $db->Quote($profileId);

		$sql->raw($query);
		$db->setQuery($sql);

		$state = $db->query();
		return $state;
	}
}
