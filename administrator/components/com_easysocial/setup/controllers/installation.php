<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/controller.php');

class EasySocialControllerInstallation extends EasySocialSetupController
{
	/**
	 * Retrieves the main menu item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMainMenuType()
	{
		require_once( JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php' );

		$db 		= FD::db();
		$sql 		= $db->sql();

		$sql->select( '#__menu' );
		$sql->column( 'menutype' );
		$sql->where( 'home' , '1' );

		$db->setQuery( $sql );
		$menuType	= $db->loadResult();

		return $menuType;
	}

	/**
	 * Install default custom profiles and fields
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installProfiles()
	{
		// Include foundry framework
		$this->foundry();

		$results = array();

		// Create the default custom profile first.
		$results[] = $this->createCustomProfile();

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class 	= $obj->state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}


		return $this->output( $result );
	}


	/**
	 * Creates default group categories
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installDefaultGroupCategories()
	{
		$this->foundry();

		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_clusters_categories' );
		$sql->column( 'COUNT(1)' );
		$sql->where('type', SOCIAL_TYPE_GROUP);

		$db->setQuery( $sql );
		$total 	= $db->loadResult();

		// There are categories already, we shouldn't be doing anything here.
		if( $total )
		{
			$result 	= $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_CREATE_DEFAULT_GROUP_CATEGORIES_EXISTS' ) , true );

			return $this->output( $result );
		}

		$categories 	= array( 'general','automobile','technology','business','music' );

		foreach( $categories as $categoryKey )
		{
			$results[]	= $this->createGroupCategory( $categoryKey );
		}

		$result 			= new stdClass();
		$result->state		= true;
		$result->message	= '';

		foreach( $results as $obj )
		{
			$class 	= $obj->state ? 'success' : 'error';

			$result->message 	.= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output( $result );
	}

	/**
	 * Creates default group categories
	 *
	 * @since	1.3
	 * @access	public
	 * @return
	 */
	public function installDefaultEventCategories()
	{
		$this->foundry();

		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_clusters_categories');
		$sql->column('COUNT(1)');
		$sql->where('type', SOCIAL_TYPE_EVENT);

		$db->setQuery( $sql );
		$total = $db->loadResult();

		// There are categories already, we shouldn't be doing anything here.
		if ($total) {
			$result = $this->getResultObj(JText::_('COM_EASYSOCIAL_INSTALLATION_ERROR_CREATE_DEFAULT_EVENT_CATEGORIES_EXISTS'), true);

			return $this->output($result);
		}

		$categories = array('general', 'meeting');

		foreach ($categories as $categoryKey) {
			$results[] = $this->createEventCategory($categoryKey);
		}

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class = $obj->state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output($result);
	}

	/**
	 * Creates default video categories
	 *
	 * @since	1.4
	 * @access	public
	 * @return
	 */
	public function installDefaultVideoCategories()
	{
		$this->foundry();

		$db = FD::db();
		$sql = $db->sql();

        // Check if there are any video categories already exists on the site
        $sql->select('#__social_videos_categories');
        $sql->column('COUNT(1)');

        $db->setQuery($sql);
        $total = $db->loadResult();

		// There are categories already, we shouldn't be doing anything here.
		if ($total) {
			$result = $this->getResultObj(JText::_('COM_EASYSOCIAL_INSTALLATION_ERROR_CREATE_DEFAULT_VIDEO_CATEGORIES_EXISTS'), true);

			return $this->output($result);
		}

        $categories = array('General', 'Music', 'Sports', 'News', 'Gaming', 'Movies', 'Documentary', 'Fashion', 'Travel', 'Technology');
        $i = 0;

		foreach ($categories as $categoryKey) {
			$results[] = $this->createVideoCategory($categoryKey, $i);
            $i++;

		}

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class = $obj->state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output($result);
	}


	/**
	 * Synchronizes database tables
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function syncDB()
	{
		// Load foundry
		$this->foundry();

		// Get this installations version
		$version	= $this->getInstalledVersion();

		// Get previous version installed
		$previous	= $this->getPreviousVersion( 'dbversion' );

		// Get total tables affected
		$affected	= FD::syncDB( $previous );

		// If the previous version is empty, we can skip this altogether as we know this is a fresh installation
		if( !empty( $affected ) ) {
			// Get list of folders from previous version installed to this version.
			$result 	= $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_DB_SYNCED' , $version ) , 1 , JText::_( 'COM_EASYSOCIAL_INSTALLATION_STEP_SUCCESS' ) );
		}
		else
		{
			$result 	= $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_DB_NOTHING_TO_SYNC' , $version ) , 1 , JText::_( 'COM_EASYSOCIAL_INSTALLATION_STEP_SUCCESS' ) );
		}

		// @TODO: In the future synchronize database table indexes here.

		// Update the version in the database to the latest now
		$config 	= FD::table( 'Config' );
		$exists		= $config->load( array( 'type' => 'dbversion' ) );
		$config->type	= 'dbversion';
		$config->value	= $version;

		$config->store();

		return $this->output( $result );
	}

	public function createGroupCategory( $categoryTitle )
	{
		$key 		= strtoupper( $categoryTitle );
		$title 		= JText::_( 'COM_EASYSOCIAL_INSTALLATION_DEFAULT_GROUP_CATEGORY_' . $key );
		$desc 		= JText::_( 'COM_EASYSOCIAL_INSTALLATION_DEFAULT_GROUP_CATEGORY_' . $key . '_DESC' );

		$category 				= FD::table( 'GroupCategory' );
		$category->alias 		= strtolower( $categoryTitle );
		$category->title 		= $title;
		$category->description 	= $desc;
		$category->type 		= SOCIAL_TYPE_GROUP;
		$category->created 		= FD::date()->toSql();
		$category->uid 			= FD::user()->id;
		$category->state 		= SOCIAL_STATE_PUBLISHED;

		$category->store();

		$result 			= new stdClass();
		$result->state		= true;
		$result->message	= JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_CREATE_GROUP_CATEGORY_SUCCESS' , $title );

		return $result;
	}

	public function createEventCategory($categoryTitle)
	{
		$key = strtoupper($categoryTitle);
		$title = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_EVENT_CATEGORY_' . $key);
		$desc = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_EVENT_CATEGORY_' . $key . '_DESC');

		$category = FD::table('EventCategory');
		$category->alias = strtolower($categoryTitle);
		$category->title = $title;
		$category->description = $desc;
		$category->type = SOCIAL_TYPE_EVENT;
		$category->created = FD::date()->toSql();
		$category->uid = FD::user()->id;
		$category->state = SOCIAL_STATE_PUBLISHED;

		$category->store();

		$result = new stdClass();
		$result->state = true;
		$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_CREATE_EVENT_CATEGORY_SUCCESS', $title);

		return $result;
	}


	public function createVideoCategory($categoryTitle, $i = 0)
	{
		$key = strtoupper($categoryTitle);
		$title = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_VIDEO_CATEGORY_' . $key);
		$desc = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_VIDEO_CATEGORY_' . $key . '_DESC');

        $category = ES::table('VideoCategory');
        $category->title = ucfirst($title);
        $category->alias = strtolower($title);
        $category->description = $desc;

        if ($i == 0) {
            $category->default = true;
        }

        // Get the current user's id
        $category->user_id = ES::user()->id;

        $category->state = true;
        $category->store();


		$result = new stdClass();
		$result->state = true;
		$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_CREATE_VIDEO_CATEGORY_SUCCESS', $title);

		return $result;
	}


	/**
	 * Creates the default custom profile
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createCustomProfile()
	{
		$this->foundry();

		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_profiles' );
		$sql->column( 'id' );
		$sql->limit( 0 , 1 );

		$db->setQuery( $sql );
		$id 	= $db->loadResult();

		// We don't have to do anything since there's already a default profile
		if( $id )
		{
			// Store the default profile for Facebook
			$this->updateConfig( 'oauth.facebook.registration.profile' , $id );

			$result 	= $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_CREATE_DEFAULT_PROFILE_EXISTS' ) , true );

			return $result;
		}

		// If it doesn't exist, we'll have to create it.
		$profile 				= FD::table( 'Profile' );
		$profile->title 		= JText::_( 'COM_EASYSOCIAL_INSTALLATION_DEFAULT_PROFILE_TITLE' );
		$profile->description	= JText::_( 'COM_EASYSOCIAL_INSTALLATION_DEFAULT_PROFILE_DESC' );

		// Get the default user group that the site is configured and select this group as the default for this profile.
		$usersConfig 			= JComponentHelper::getParams( 'com_users' );
		$group 					= array( $usersConfig->get( 'new_usertype' ) );

		// Set the group for this default profile
		$profile->gid 			= FD::json()->encode( $group );

		$profile->default 		= 1;
		$profile->state 		= SOCIAL_STATE_PUBLISHED;

		// Set the default params for profile
		$params 	= FD::registry();
		$params->set( 'delete_account' , 0 );
		$params->set( 'theme' , '' );
		$params->set( 'registration' , 'approvals' );
		$profile->params 		= $params->toString();

		// Try to save the profile.
		$state 	= $profile->store();

		if( !$state )
		{
			$result 	= $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_CREATE_DEFAULT_PROFILE' ) , false );

			return $result;
		}

		$this->updateConfig( 'oauth.facebook.registration.profile' , $profile->id );

		$result 	= $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_SUCCESS_CREATE_DEFAULT_PROFILE' ) , true );

		return $result;
	}

	/**
	 * Saves a configuration item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The key to save
	 * @param	mixed	The data to save
	 * @return
	 */
	public function updateConfig( $key , $value )
	{
		$this->foundry();

		$config 	= FD::config();
		$config->set( $key , $value );

		$jsonString 	= $config->toString();

		$configTable 	= FD::table( 'Config' );

		if( !$configTable->load( 'site' ) )
		{
			$configTable->type 	= 'site';
		}

		$configTable->set( 'value' , $jsonString );
		$configTable->store();
	}

	/**
	 * Installs a single custom field
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installField($path, $group = 'user')
	{
		// Include core library
		require_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');

		// Retrieve the installer library.
		$installer = FD::get('Installer');

		// Get the element
		$element = basename($path);

		// Try to load the installation from path.
		$state = $installer->load($path);

		// Try to load and see if the previous field apps already has a record
		$oldField = FD::table('App');
		$fieldExists = $oldField->load(array('type' => SOCIAL_APPS_TYPE_FIELDS , 'element' => $element, 'group' => $group));

		// If there's an error, we need to log it down.
		if (!$state) {

			$result = $this->getResultObj(JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_FIELD_ERROR_LOADING_FIELD', ucfirst($element)), false);

			return $result;
		}

		// Let's try to install it now.
		$app = $installer->install();

		// If there's an error installing, log this down.
		if ($app === false) {

			$result = $this->getResultObj(JText::sprintf('COM_EASYSOCIAL_INSTALLATION_FIELD_ERROR_INSTALLING_FIELD', ucfirst($element)), false);

			return $result;
		}

		// If the field apps already exist, use the previous title.
		if ($fieldExists) {
			$app->title = $oldField->title;
			$app->alias = $oldField->alias;
		}

		// Ensure that the field apps is published
		$app->state	= $fieldExists ? $oldField->state : SOCIAL_STATE_PUBLISHED;
		$app->store();

		$result = $this->getResultObj(JText::sprintf('COM_EASYSOCIAL_INSTALLATION_FIELD_SUCCESS_INSTALLING_FIELD', ucfirst($element)), true);

		return $result;
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createMenu()
	{
		// Include foundry framework
		$this->foundry();

		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__extensions' , 'id' );
		$sql->where( 'element' , 'com_easysocial' );

		$db->setQuery( $sql );

		// Get the extension id
		$extensionId 	= $db->loadResult();

		// Get the main menu that is used on the site.
		$menuType			= $this->getMainMenuType();

		if( !$menuType )
		{
			return false;
		}

		$sql 	= $db->sql();

		$sql->select( '#__menu' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'link' , '%index.php?option=com_easysocial%' , 'LIKE' );
		$sql->where( 'type'	, 'component' );
		$sql->where( 'client_id'	, 0 );

		$db->setQuery( $sql );

		$exists	= $db->loadResult();

		if( $exists )
		{
			// we need to update all easysocial menu item with this new component id.
			$query = 'update `#__menu` set component_id = ' . $db->Quote( $extensionId );
			$query .= ' where `link` like ' . $db->Quote( '%index.php?option=com_easysocial%' );
			$query .= ' and `type` = ' . $db->Quote( 'component' );
			$query .= ' and `client_id` = ' . $db->Quote( '0' );

			$sql->clear();
			$sql->raw( $query );
			$db->setQuery( $sql );
			$db->query();

			return $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_SITE_MENU_UPDATED' ) , true );
		}

		$menu 					= JTable::getInstance( 'Menu' );
		$menu->menuType 		= $menuType;
		$menu->title 			= JText::_( 'COM_EASYSOCIAL_INSTALLATION_DEFAULT_MENU_COMMUNITY' );
		$menu->alias 			= 'community';
		$menu->path 			= 'easysocial';
		$menu->link 			= 'index.php?option=com_easysocial&view=dashboard';
		$menu->type 			= 'component';
		$menu->published 		= 1;
		$menu->parent_id 		= 1;
		$menu->component_id 	= $extensionId;
		$menu->client_id 		= 0;
		$menu->language 		= '*';

		$menu->setLocation( '1' , 'last-child' );

		$state 	= $menu->store();

		// @TODO: Assign modules to dashboard menu
		$this->installModulesMenu( $menu->id );

		return $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_SITE_MENU_CREATED' ) , true );
	}


	/**
	 * install module and assign to unity view
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installModulesMenu( $dashboardMenuId = null )
	{
		// Include foundry framework
		$this->foundry();

		$db 	= FD::db();
		$sql 	= $db->sql();

		$modulesToInstall = array();

		// register modules here.

		// online user
		$modSetting = new stdClass();
		$modSetting->title 		= 'Online Users';
		$modSetting->name 		= 'mod_easysocial_users';
		$modSetting->position 	= 'es-dashboard-sidebar-bottom';
		$modSetting->config 	= array('filter' 	=> 'online',
										'total' 	=> '5',
										'ordering' 	=> 'name',
										'direction' => 'asc' );
		$modulesToInstall[] 	= $modSetting;

		// Recent user
		$modSetting = new stdClass();
		$modSetting->title 		= 'Recent Users';
		$modSetting->name 		= 'mod_easysocial_users';
		$modSetting->position 	= 'es-dashboard-sidebar-bottom';
		$modSetting->config 	= array('filter' 	=> 'recent',
										'total' 	=> '5',
										'ordering' 	=> 'registerDate',
										'direction' => 'desc' );
		$modulesToInstall[] 	= $modSetting;

		// Recent albums
		$modSetting = new stdClass();
		$modSetting->title 		= 'Recent Albums';
		$modSetting->name 		= 'mod_easysocial_albums';
		$modSetting->position 	= 'es-dashboard-sidebar-bottom';
		$modSetting->config 	= array();
		$modulesToInstall[] 	= $modSetting;

		// leaderboard
		$modSetting = new stdClass();
		$modSetting->title 		= 'Leaderboard';
		$modSetting->name 		= 'mod_easysocial_leaderboard';
		$modSetting->position 	= 'es-dashboard-sidebar-bottom';
		$modSetting->config 	= array('total' => '5');
		$modulesToInstall[] 	= $modSetting;

		// Dating Search
		$modSetting = new stdClass();
		$modSetting->title 		= 'Dating Search';
		$modSetting->name 		= 'mod_easysocial_dating_search';
		$modSetting->position 	= 'es-users-sidebar-bottom';
		$modSetting->config 	= array('searchname' 	=> '1',
										'searchgender' 	=> '1',
										'searchage' 	=> '1',
										'searchdistance' => '1' );
		$modulesToInstall[] 	= $modSetting;


		// real work here.
		foreach( $modulesToInstall as $module )
		{
			$jMod	= JTable::getInstance( 'Module' );

			$jMod->title 		= $module->title;
			$jMod->ordering 	= $this->getModuleOrdering( $module->position );
			$jMod->position 	= $module->position;
			$jMod->published 	= 1;
			$jMod->module 		= $module->name;
			$jMod->access 		= 1;

			if( $module->config )
			{
				$jMod->params 		= FD::json()->encode( $module->config );
			}
			else
			{
				$jMod->params 		= '';
			}

			$jMod->client_id 	= 0;
			$jMod->language 	= '*';

			$state = $jMod->store();

			if( $state && $dashboardMenuId )
			{
				// lets add into module menu.
				$modMenu = new stdClass();
				$modMenu->moduleid 	= $jMod->id;
				$modMenu->menuid 	= $dashboardMenuId;

				$state	= $db->insertObject( '#__modules_menu' , $modMenu );
			}

		}

	}


	/**
	 * get ordering based on the module position.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getModuleOrdering( $position )
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = 'select `ordering` from `#__modules` where `position` = ' . $db->Quote( $position );
		$query .= ' order by `ordering` desc limit 1';
		$sql->raw( $query );

		$db->setQuery( $sql );

		$result = $db->loadResult();

		return ( $result ) ? $result + 1 : 1;

	}



	/**
	 * Post installation process
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installPost()
	{
		$results = array();

		// Get the user's current api key
		$apiKey = JRequest::getVar('apikey', '');

		// Only update the api key when it is not empty.
		if ($apiKey) {
			$this->updateConfig('general.key', $apiKey);
		}

		// Setup site menu.
		$results[] = $this->createMenu('site');

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class = $obj->state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		// Cleanup temporary files from the tmp folder
		$tmp = dirname(dirname(__FILE__)) . '/tmp';
		$folders = JFolder::folders($tmp, '.', false, true);

		if ($folders) {
			foreach ($folders as $folder) {
				@JFolder::delete($folder);
			}
		}

		return $this->output($result);
	}

	/**
	 * Install alert rules
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installAlerts()
	{
		// Get the path to the defaults folder
		$path 			= JPATH_ADMINISTRATOR . '/components/com_easysocial/defaults/alerts';

		// Include foundry framework
		$this->foundry();

		// Retrieve the privacy model to scan for the path
		$model 	= FD::model( 'Alert' );

		// Scan and install privacy
		$total 	= 0;
		$files 	= JFolder::files( $path , '.alert' , false , true );

		if( $files )
		{
			foreach( $files as $file )
			{
				$model->install( $file );
				$total 	+= 1;
			}
		}

		return $this->output( $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_ALERT_SUCCESS' , $total ) , true ) );
	}

	/**
	 * Install privacy items.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installPrivacy()
	{
		if( $this->isDevelopment() )
		{
			return $this->output( $this->getResultObj( 'ok' , true )  );
		}

		// Get the temporary path from the server.
		$tmpPath 		= JRequest::getVar( 'path' );

		// There should be a queries.zip archive in the archive.
		$archivePath 	= $tmpPath . '/privacy.zip';

		// Where the badges should reside after extraction
		$path 			= $tmpPath . '/privacy';

		// Extract badges
		$state 	= JArchive::extract( $archivePath , $path );

		if( !$state )
		{
			return $this->output( $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_PRIVACY' ) , false ) );
		}

		// Include foundry framework
		$this->foundry();

		// Retrieve the privacy model to scan for the path
		$model 	= FD::model( 'Privacy' );

		// Scan and install privacy
		$totalPrivacy 	= 0;
		$files 			= JFolder::files( $path , '.privacy' , false , true );

		if( $files )
		{
			foreach( $files as $file )
			{
				$model->install( $file );
				$totalPrivacy 	+= 1;
			}
		}

		return $this->output( $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_PRIVACY_SUCCESS' , $totalPrivacy ) , true ) );
	}

	/**
	 * Install access rules on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installAccess()
	{
		if( $this->isDevelopment() )
		{
			return $this->output( $this->getResultObj( 'ok' , true )  );
		}

		// Include foundry framework
		$this->foundry();

		// Scan and install alert files
		$model 	= FD::model('AccessRules');
		$path 	= JPATH_ADMINISTRATOR . '/components/com_easysocial/defaults/access';
		$files	= JFolder::files($path, '.access$', true, true);

		$totalRules	= 0;

		if ($files) {

			foreach ($files as $file) {

				$model->install($file);

				$totalRules += 1;
			}
		}

		return $this->output( $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_RULES_SUCCESS' , $totalRules ) , true ) );
	}

	/**
	 * Install points on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installPoints()
	{
		if( $this->isDevelopment() )
		{
			return $this->output( $this->getResultObj( 'ok' , true )  );
		}

		// Get the temporary path from the server.
		$tmpPath 		= JRequest::getVar( 'path' );

		// There should be a queries.zip archive in the archive.
		$archivePath 	= $tmpPath . '/points.zip';

		// Where the badges should reside after extraction
		$path 			= $tmpPath . '/points';

		// Extract badges
		$state 	= JArchive::extract( $archivePath , $path );

		if( !$state )
		{
			return $this->output( $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_POINTS' ) , false ) );
		}

		// Include foundry framework
		$this->foundry();

		// Retrieve the points model to scan for the path
		$model 	= FD::model( 'Points' );

		// Scan and install badges
		$points = JFolder::files( $path , '.points' , true , true );

		$totalPoints 	= 0;

		if( $points )
		{
			foreach( $points as $point )
			{
				$model->install( $point );

				$totalPoints 	+= 1;
			}
		}

		return $this->output( $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_POINTS_SUCCESS' , $totalPoints ) , true ) );
	}

	/**
	 * Installation of plugins on the site
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installPlugins()
	{
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('ok', true) );
		}

		// We need the foundry library here
		$this->foundry();

		// Get the path to the current installer archive
		$tmpPath = JRequest::getVar('path');

		// Path to the archive
		$archivePath = $tmpPath . '/plugins.zip';

		// Where should the archive be extrated to
		$path = $tmpPath . '/plugins';

		$state = JArchive::extract($archivePath, $path);

		if (!$state) {
			return $this->output($this->getResultObj(JText::_('COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_PLUGINS'), false));
		}

		// Get a list of apps we should install.
		$groups = JFolder::folders($path, '.', false, true);

		// Get Joomla's installer instance
		$installer = JInstaller::getInstance();

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($groups as $group) {

			// Now we find the plugin info
			$plugins = JFolder::folders( $group , '.' , false , true );
			$groupName = basename($group);
			$groupName = ucfirst($groupName);

			foreach ($plugins as $pluginPath) {

				$pluginName = basename($pluginPath);
				$pluginName = ucfirst($pluginName);

				// We need to try to load the plugin first to determine if it really exists
				$plugin = JTable::getInstance('extension');
				$options = array('folder' => strtolower($groupName), 'element' => strtolower($pluginName));
				$exists = $plugin->load($options);

				// Allow overwriting existing plugins
				$installer->setOverwrite(true);
				$state = $installer->install($pluginPath);

				if (!$exists) {
					$plugin->load($options);
				}


				// Load the plugin and ensure that it's published
				if ($state) {

					// If the plugin was previously disabled, do not turn this on.
					if (($exists && $plugin->enabled) || !$exists) {
						$plugin->state = true;
						$plugin->enabled = true;
					}

					$plugin->store();
				}

				$message = $state ? JText::sprintf('COM_EASYSOCIAL_INSTALLATION_SUCCESS_PLUGIN', $groupName, $pluginName) : JText::sprintf('COM_EASYSOCIAL_INSTALLATION_ERROR_PLUGIN', $groupName, $pluginName);
				$class = $state ? 'success' : 'error';

				$result->message .= '<div class="text-' . $class . '">' . $message . '</div>';
			}
		}

		return $this->output($result);
	}

	/**
	 * Installation of modules on the site
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installModules()
	{
		if( $this->isDevelopment() )
		{
			return $this->output( $this->getResultObj( 'ok' , true )  );
		}

		// We need the foundry library here
		$this->foundry();

		// Get the path to the current installer archive
		$tmpPath 		= JRequest::getVar( 'path' );

		// Path to the archive
		$archivePath 	= $tmpPath . '/modules.zip';

		if( !JFile::exists( $archivePath ) )
		{
			return $this->output( $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_NO_MODULES_AVAILABLE' ) , true ) );
		}
		// Where should the archive be extrated to
		$path 			= $tmpPath . '/modules';

		$state 			= JArchive::extract( $archivePath , $path );

		if( !$state )
		{
			return $this->output( $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_MODULES' ) , false ) );
		}

		// Get a list of apps we should install.
		$modules 	= JFolder::folders( $path , '.' , false , true );


		$result 			= new stdClass();
		$result->state		= true;
		$result->message	= '';

		foreach( $modules as $module )
		{
			$moduleName 	= basename( $module );

			// Get Joomla's installer instance
			$installer 	= new JInstaller();

			// Allow overwriting existing plugins
			$installer->setOverwrite( true );
			$state 		= $installer->install( $module );

			if( $state )
			{
				$db = FD::db();
				$sql = $db->sql();

				$query = 'update `#__extensions` set `access` = 1';
				$query .= ' where `type` = ' . $db->Quote( 'module' );
				$query .= ' and `element` = ' . $db->Quote( $moduleName );
				$query .= ' and `access` = ' . $db->Quote( '0' );

				$sql->clear();
				$sql->raw( $query );
				$db->setQuery( $sql );
				$db->query();

				// we need to check if this module record already exists in module_menu or not. if not, lets create one for this module.
				$query = 'select a.`id`, b.`moduleid` from #__modules as a';
				$query .= ' left join `#__modules_menu` as b on a.`id` = b.`moduleid`';
				$query .= ' where a.`module` = ' . $db->Quote( $moduleName );
				$query .= ' and b.`moduleid` is null';

				$sql->clear();
				$sql->raw( $query );
				$db->setQuery( $sql );

				$results = $db->loadObjectList();

				if( $results )
				{
					foreach( $results as $item )
					{
						// lets add into module menu.
						$modMenu = new stdClass();
						$modMenu->moduleid 	= $item->id;
						$modMenu->menuid 	= 0;

						$db->insertObject( '#__modules_menu' , $modMenu );
					}
				}

			}

			$message 	= $state ? JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_SUCCESS_MODULE' , $moduleName ) : JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_ERROR_MODULE' , $moduleName );

			$class 		= $state ? 'success' : 'error';

			$result->message 	.= '<div class="text-' . $class . '">' . $message . '</div>';
		}

		return $this->output( $result );
	}

	/**
	 * Install badges on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installBadges()
	{
		if( $this->isDevelopment() )
		{
			return $this->output( $this->getResultObj( 'ok' , true )  );
		}

		// Get the temporary path from the server.
		$tmpPath 		= JRequest::getVar( 'path' );

		// There should be a queries.zip archive in the archive.
		$archivePath 	= $tmpPath . '/badges.zip';

		// Where the badges should reside after extraction
		$path 			= $tmpPath . '/badges';

		// Extract badges
		$state 	= JArchive::extract( $archivePath , $path );

		if( !$state )
		{
			return $this->output( $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_BADGES' ) , false ) );
		}

		// Include foundry framework
		$this->foundry();

		// Retrieve the points model to scan for the path
		$model 	= FD::model( 'Badges' );

		// Scan and install badges
		$badges = JFolder::files( $path , '.badge$' , true , true );

		$totalBadges 	= 0;

		if( $badges )
		{
			foreach( $badges as $badge )
			{
				$model->install( $badge );

				$totalBadges 	+= 1;
			}
		}

		// After installing the badge, copy the badges folder over to ADMIN/com_easysocial/defaults/
		JFolder::copy($path, JPATH_ADMINISTRATOR . '/components/com_easysocial/defaults/badges', '', true);

		return $this->output( $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_BADGES_SUCCESS' , $totalBadges ) , true ) );
	}

	/**
	 * Performs the installation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function install()
	{
		$item 	= JRequest::getWord( 'item' , '' );

		$method	= 'install' . ucfirst( $item );

		$this->$method();
	}

	/**
	 * Responsible to install apps
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installApps()
	{
		// For development mode, we want to skip all this
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('Skipping this step because we are in development mode.', true));
		}

		// Get the group of apps to install.
		$group = JRequest::getVar('group');

		// Get the temporary path to the archive
		$tmpPath = JRequest::getVar('path');

		// Get the archive path
		$archivePath = $tmpPath . '/' . $group . 'apps.zip';

		// Where the extracted items should reside.
		$path = $tmpPath . '/' . $group . 'apps';

		// Detect if the target folder exists
		$target = JPATH_ROOT . '/media/com_easysocial/apps/' . $group;

		// Try to extract the archive first
		$state = JArchive::extract($archivePath, $path);

		if (!$state) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_APPS', $group);

			return $this->output($result);
		}

		// If the apps folder does not exist, create it first.
		$exists = JFolder::exists($target);

		if (!$exists) {
			$state = JFolder::create($target);

			if (!$state) {
				$result = new stdClass();
				$result->state = false;
				$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_ERROR_CREATE_APPS_FOLDER', $target);

				return $this->output($result);
			}
		}

		// Get a list of apps within this folder.
		$apps = JFolder::folders($path, '.', false, true);
		$totalApps 	= 0;

		// If there are no apps to install, just silently continue
		if (!$apps) {
			$result = new stdClass();
			$result->state = true;
			$result->message = JText::_('COM_EASYSOCIAL_INSTALLATION_APPS_NO_APPS');

			return $this->output($result);
		}

		$results = array();

		// Go through the list of apps on the site and try to install them.
		foreach ($apps as $app) {
			$results[] = $this->installApp($app, $target, $group);
			$totalApps += 1;
		}

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class = $obj->state ? 'success' : 'error';
			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output($result);
	}

	/**
	 * Installs Single Application
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installApp($appArchivePath, $target, $group = 'user')
	{
		// Get the element of the app
		$element = basename($appArchivePath);
		$element = str_ireplace('.zip', '' , $element);

		// Get the installation source folder.
		$path = dirname($appArchivePath) . '/' . $element;

		// Include core library
		require_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');

		// Get installer library
		$installer = ES::get('Installer');

		// Try to load the installation from path.
		$state = $installer->load($path);

		// Try to load and see if the previous app already has a record
		$oldApp = ES::table('App');
		$appExists = $oldApp->load(array('type' => SOCIAL_TYPE_APPS, 'element' => $element, 'group' => $group));

		// If there's an error with this app, we should silently continue
		if (!$state) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_ERROR_LOADING_APP', $element);

			return $result;
		}

		// Let's try to install the app.
		$app = $installer->install();

		// If there's an error with this app, we should silently continue
		if ($app === false) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_ERROR_INSTALLING_APP', $element);

			return $result;
		}

		// If application already exist, use the previous title.
		if ($appExists) {
			$app->title = $oldApp->title;
			$app->alias = $oldApp->alias;
		}

		$app->state = $appExists ? $oldApp->state : SOCIAL_STATE_PUBLISHED;
		$app->store();

		$result = new stdClass();
		$result->state = true;
		$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_APPS_INSTALLED_APP_SUCCESS', $element);

		return $result;
	}

	/**
	 * Responsible to copy the necessary files over.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installCopy()
	{
		$type = JRequest::getVar('type', '');

		// Get the temporary path from the server.
		$tmpPath = JRequest::getVar('path');

		// Get the path to the zip file
		$archivePath = $tmpPath . '/' . $type . '.zip';

		// Where the extracted items should reside
		$path = $tmpPath . '/' . $type;

		// For development mode, we want to skip all this
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('Skipping this step because we are in development mode.', true));
		}

		// Extract the admin folder
		$state = JArchive::extract($archivePath, $path);

		if (!$state) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_COPY_ERROR_UNABLE_EXTRACT', $type);

			return $this->output($result);
		}

		// Look for files in this path
		$files = JFolder::files($path, '.', false, true);

		// Look for folders in this path
		$folders = JFolder::folders($path, '.', false, true);

		// Construct the target path first.
		if ($type == 'admin') {
			$target = JPATH_ADMINISTRATOR . '/components/com_easysocial';
		}

		if ($type == 'site') {
			$target = JPATH_ROOT . '/components/com_easysocial';
		}

		// Languages
		if ($type == 'languages') {

			// Admin language files
			$adminPath = JPATH_ADMINISTRATOR . '/language/en-GB';
			$adminSource = $path . '/admin/en-GB.com_easysocial.ini';
			$adminSysSource	= $path . '/admin/en-GB.com_easysocial.sys.ini';
			
			JFile::copy($adminSource, $adminPath . '/en-GB.com_easysocial.ini');
			JFile::copy($adminSysSource, $adminPath . '/en-GB.com_easysocial.sys.ini');

			// Site language files
			$sitePath = JPATH_ROOT . '/language/en-GB';
			$siteSource = $path . '/site/en-GB.com_easysocial.ini';

			JFile::copy($siteSource, $sitePath . '/en-GB.com_easysocial.ini');

			$result = new stdClass();
			$result->state = true;
			$result->message = JText::_('COM_EASYSOCIAL_INSTALLATION_LANGUAGES_UPDATED');
			
			return $this->output($result);
		}

		if ($type == 'media') {
			$target = JPATH_ROOT . '/media/com_easysocial';
		}

		if ($type == 'foundry') {
			// Should we be overwriting the foundry folder.
			$overwrite 	= false;

			// Check the current version of Foundry installed and determine if we should overwrite foundry.
			$foundryVersion = '4.0';
			$currentFoundryVersion = JPATH_ROOT . '/media/foundry/' . $foundryVersion . '/version';
			$exists = JFile::exists($currentFoundryVersion);

			// If foundry folder already exists exists, we need to perform the upgrades ourselves.
			if ($exists) {

				// If foundry exists, do a version compare and see if we should overwrite.
				$target = JPATH_ROOT . '/media/foundry/' . $foundryVersion;

				// Get the current foundry version
				$currentFoundryVersion = JFile::read($currentFoundryVersion);

				// Get the incoming version
				$incomingFoundryVersion = JFile::read($path . '/version');

				// Determines if an upgrade is necessary
				$requiresUpdating = version_compare($currentFoundryVersion, $incomingFoundryVersion);

				// We need to upgrade Foundry
				if ($requiresUpdating <= 0) {
					JFolder::copy($path, $target, '', true);

					$result = new stdClass();
					$result->state = true;

					$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_COPY_OVERWRITE_FOUNDRY_FILES_SUCCESS', $incomingFoundryVersion);
					return $this->output($result);
				}

				// Otherwise, there's nothing to do here.
				$result = new stdClass();
				$result->state = true;

				$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_FOUNDRY_NO_CHANGES', $incomingFoundryVersion);
				return $this->output($result);
			}

			// Overwrite the foundry folder
			$target = $this->makeFoundryFolders($foundryVersion);
		}

		// Ensure that the target folder exists
		if (!JFolder::exists($target)) {
			JFolder::create($target);
		}

		// Scan for files in the folder
		$totalFiles = 0;

		foreach ($files as $file) {

			$name = basename($file);
			$targetFile	= $target . '/' . $name;

			// For site's cron.php, we need to ensure that we do not replace it.
			if ($type == 'site' && $name == 'cron.php') {

				// Check if the targets exists
				if (JFile::exists($targetFile)) {
					continue;
				}

			}

			JFile::copy($file, $targetFile);

			$totalFiles += 1;
		}

		// Scan for folders in this folder
		$totalFolders = 0;

		foreach ($folders as $folder) {

			$name = basename($folder);
			$targetFolder = $target . '/' . $name;

			// Try to copy the folder over
			JFolder::copy($folder, $targetFolder, '', true);

			$totalFolders += 1;
		}

		$result = new stdClass();
		$result->state = true;
		$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_COPY_FILES_SUCCESS', $totalFiles, $totalFolders);

		return $this->output($result);
	}

	/**
	 * Create foundry folders given the current version
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function makeFoundryFolders( $version )
	{
		$version 		= explode( '.' , $version );
		$majorVersion	= $version[ 0 ] . '.' . $version[ 1 ];
		$path 			= JPATH_ROOT . '/media/foundry/' . $majorVersion;
		$state 			= true;

		if( !JFolder::exists( $path ) )
		{
			$state = JFolder::create( $path );

			if( !$state )
			{
				$result 			= new stdClass();
				$result->state		= false;
				$result->message	= JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_UNABLE_TO_CREATE_FOUNDRY_FOLDER' , $path );

				return $this->output( $result );
			}
		}

		return $path;
	}

	/**
	 * Perform installation of SQL queries
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installSQL()
	{
		// Get the temporary path from the server.
		$tmpPath 	= JRequest::getVar( 'path' );

		// There should be a queries.zip archive in the archive.
		$tmpQueriesPath 	= $tmpPath . '/queries.zip';

		// Extract the queries
		$path 				= $tmpPath . '/queries';

		// Check if this folder exists.
		if( JFolder::exists( $path ) )
		{
			JFolder::delete( $path );
		}

		$state 	= JArchive::extract( $tmpQueriesPath , $path );

		if( !$state )
		{
			$result 			= new stdClass();
			$result->state 		= false;
			$result->message	= JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_UNABLE_EXTRACT_QUERIES' );

			return $this->output( $result );
		}

		// Get the list of files in the folder.
		$queryFiles 	= JFolder::files($path , '.' , false, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', '.php'));

		// When there are no queries file, we should just display a proper warning instead of exit
		if( !$queryFiles )
		{
			$result 			= new stdClass();
			$result->state 		= true;
			$result->message	= JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_EMPTY_QUERIES_FOLDER' );

			return $this->output( $result );
		}

		$db 		= JFactory::getDBO();
		$total 		= 0;

		foreach( $queryFiles as $file )
		{
			$contents 	= JFile::read( $file );

			$queries	= JInstallerHelper::splitSql( $contents );


			foreach( $queries as $query )
			{
				$query 	= trim( $query );

				if( !empty( $query ) )
				{
					$db->setQuery( $query );

					$db->execute();
				}

			}

			$total 	+= 1;
		}

		$result 			= new stdClass();
		$result->state		= true;
		$result->message	= JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_SQL_EXECUTED_SUCCESS' , $total );

		return $this->output( $result );
	}

	/**
	 * Downloads the file from the server
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function download()
	{
		// Check the api key from the request
		$apiKey = JRequest::getVar( 'apikey' , '' );
		$license = JRequest::getVar( 'license' , '' );

		// If the user is updating, we always need to get the latest version.
		$update = JRequest::getBool('update', false);

		// Get information about the current release.
		$info = $this->getInfo($update);

		if (!$info) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::_('COM_EASYSOCIAL_INSTALLATION_ERROR_REQUEST_INFO');

			$this->output($result);
			exit;
		}

		if( isset( $info->error ) && $info->error == 408 )
		{
			$result 			= new stdClass();
			$result->state 		= false;
			$result->message	= $info->message;

			$this->output( $result );
			exit;
		}

		// Download the component installer.
		$storage 	= $this->getDownloadFile( $info , $apiKey , $license );

		if( $storage === false )
		{
			$result 			= new stdClass();
			$result->state 		= false;
			$result->message	= JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_DOWNLOADING_INSTALLER' );

			$this->output( $result );
			exit;
		}

		// Get the md5 hash of the stored file
		$hash 		= md5_file( $storage );

		// Check if the md5 check sum matches the one provided from the server.
		if( !in_array( $hash , $info->md5 ) )
		{
			$result 	= new stdClass();
			$result->state 		= false;
			$result->message	= JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_MD5_CHECKSUM' );

			$this->output( $result );
			exit;
		}

		// Extract files here.
		$tmp 		= ES_TMP . '/com_easysocial_v' . $info->version;

		if( JFolder::exists( $tmp ) )
		{
			JFolder::delete( $tmp );
		}

		// Try to extract the files
		$state = JArchive::extract( $storage , $tmp );

		if (!$state) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_ERRORS' );

			$this->output($result);
			exit;
		}

		// After installation is completed, cleanup all zip files from the site
		$this->cleanupZipFiles(dirname($storage));

		$result = new stdClass();

		$result->message	= JText::_( 'COM_EASYSOCIAL_INSTALLATION_ARCHIVE_DOWNLOADED_SUCCESS' );
		$result->state 		= $state;
		$result->path 		= $tmp;

		header('Content-type: text/x-json; UTF-8');
		echo json_encode( $result );
		exit;
	}

	/**
	 * Allows cleanup of installation files
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function cleanupZipFiles($path)
	{
		$zipFiles = JFolder::files($path, '.zip', false, true);

		if ($zipFiles) {
			foreach ($zipFiles as $file) {
				@JFile::delete($file);
			}
		}

		return true;
	}

	/**
	 * For users who uploaded the installer and needs a manual extraction
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function extract()
	{
		// Check the api key from the request
		$apiKey = JRequest::getVar('apikey', '');

		// Get the package
		$package = JRequest::getVar('package', '');

		// Construct the storage path
		$storage = ES_PACKAGES . '/' . $package;
		$exists = JFile::exists($storage);

		// Test if package really exists
		if (!$exists) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::_('COM_EASYSOCIAL_INSTALLATION_ERROR_PACKAGE_DOESNT_EXIST');

			$this->output($result);
			exit;
		}

		// Get the folder name
		$folderName = basename($storage);
		$folderName = str_ireplace('.zip', '', $folderName);

		// Extract files here.
		$tmp = ES_TMP . '/' . $folderName;

		// Ensure that there is no such folders exists on the site
		if (JFolder::exists($tmp)) {
			JFolder::delete($tmp);
		}

		// Try to extract the files
		$state = JArchive::extract($storage, $tmp);

		// Regardless of the extraction state, delete the zip file otherwise anyone can download the zip file.
		@JFile::delete($storage);

		if (!$state) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::_('COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_ERRORS');

			$this->output($result);
			exit;
		}

		$result = new stdClass();

		$result->message = JText::_( 'COM_EASYSOCIAL_INSTALLATION_EXTRACT_SUCCESS' );
		$result->state = $state;
		$result->path = $tmp;

		header('Content-type: text/x-json; UTF-8');
		echo json_encode($result);
		exit;
	}

	/**
	 * Executes the file download from the server.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object 	The manifest data from server.
	 * @param	string	The user's api key.
	 * @param	string	The license key to use for this installation
	 * @return	mixed	false if download failed or path to the file if success.
	 */
	public function getDownloadFile( $info , $apikey , $license )
	{
		// Request the server to download the file.
		$url 	= $info->install;

		// Get the latest version
		$ch 	= curl_init( $info->install );

		// We need to pass the api keys to the server
		curl_setopt( $ch , CURLOPT_POST , true );
		curl_setopt( $ch , CURLOPT_POSTFIELDS , 'extension=easysocial&apikey=' . $apikey . '&license=' . $license . '&version=' . $info->version );

		// We don't want the output immediately.
		curl_setopt( $ch , CURLOPT_RETURNTRANSFER , true );

		// Set a large timeout incase the server fails to download in time.
		curl_setopt( $ch , CURLOPT_TIMEOUT , 30000 );

		// Get the response of the server
		$result 	= curl_exec( $ch );

		// Close the connection
		curl_close( $ch );

		// Set the storage page
		$storage	= ES_PACKAGES . '/easysocial_v' . $info->version . '_component.zip';

		// Delete zip archive if it already exists.
		if( JFile::exists( $storage ) )
		{
			JFile::delete( $storage );
		}

		// Debug md5
		// $result 	= $result . 'somedebugcontents';

		$state		= JFile::write( $storage , $result );

		if( !$state )
		{
			return false;
		}

		return $storage;
	}

	/**
	 * Installs fields based on group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function installFields()
	{
		// For development mode, we want to skip all this
		if( $this->isDevelopment() )
		{
			return $this->output( $this->getResultObj( 'ok' , true )  );
		}

		// Get the group of apps to install.
		$group	 = JRequest::getVar( 'group' );

		// Get the temporary path to the archive
		$tmpPath 		= JRequest::getVar( 'path' );

		// Get the archive path
		$archivePath 	= $tmpPath . '/' . $group . 'fields.zip';

		// Where the extracted items should reside.
		$path 			= $tmpPath . '/' . $group . 'fields';

		// Detect if the target folder exists
		$target		= JPATH_ROOT . '/media/com_easysocial/apps/fields/' . $group;

		// Try to extract the archive first
		$state 		= JArchive::extract( $archivePath , $path );

		if( !$state )
		{
			$result 			= new stdClass();
			$result->state 		= false;
			$result->message	= JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_FIELDS' , $group );

			return $this->output( $result );
		}

		// If the apps folder does not exist, create it first.
		if( !JFolder::exists( $target ) )
		{
			$state 	= JFolder::create( $target );

			if( !$state )
			{
				$result 			= new stdClass();
				$result->state 		= false;
				$result->message	= JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_ERROR_CREATE_FIELDS_FOLDER' , $target );

				return $this->output( $result );
			}
		}

		// Get a list of apps within this folder.
		$fields 		= JFolder::folders( $path , '.' , false , true );

		$totalFields 	= 0;

		// If there are no apps to install, just silently continue
		if( !$fields )
		{
			$result 			= new stdClass();
			$result->state 		= true;
			$result->message	= JText::_( 'COM_EASYSOCIAL_INSTALLATION_FIELDS_NO_FIELDS' );

			return $this->output( $result );
		}

		$results	= array();

		// Go through the list of apps on the site and try to install them.
		foreach( $fields as $field )
		{
			$results[]	= $this->installField($field, $group);

			$totalFields 	+= 1;
		}

		$result 			= new stdClass();
		$result->state		= true;
		$result->message	= '';

		foreach( $results as $obj )
		{
			$class 	= $obj->state ? 'success' : 'error';

			$result->message 	.= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output( $result );
	}
}
