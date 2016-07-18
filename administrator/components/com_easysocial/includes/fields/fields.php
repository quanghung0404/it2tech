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
ES::import('admin:/includes/fields/triggers');
ES::import('admin:/includes/fields/handlers');
ES::import('admin:/includes/apps/apps');

class SocialFields
{
	/**
	 * The triggerer object for fields.
	 * @var	SocialFieldTriggers
	 */
	private $triggerer	= null;

	/**
	 * General handler class for fields
	 * @var SocialFieldHandlers
	 */
	private $handler = null;

	static 	$_apps 		= array();

	private $params = array();

	private $user = null;

	public function __construct($params = array())
	{
		$this->init($params);
	}

	/**
	 * Object initialisation for the class to fetch the appropriate user
	 * object.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function getInstance($params = array())
	{
		$args = func_get_args();

		static $obj = null;

		if (is_null($obj)) {
			$obj = new self($params);
		}

		return $obj;
	}

	/**
	 * Inits some override params to pass to the triggerer/field
	 * This is to have a master switch for certain parameter
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  array     $params The params to override
	 */
	public function init($params = array())
	{
		if (!empty($params)) {
			$this->params = array_merge($this->params, $params);
		}
	}

	/**
	 * This is to set the target user that the fields is acting on
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  SocialUser    $user The target user
	 */
	public function setUser($user)
	{
		$this->user = $user;
	}

	/**
	 * Manually get the field class file
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.1
	 * @access public
	 *
	 * @param  string	$element	The element name
	 * @param  string	$group		The group name
	 * @param  string	$options	The options to pass into the class
	 *
	 * @param  string	$element	The element name
	 * @param  string	$options	The options to pass into the class
	 *
	 * @param  SocialTableField	$field	The field table
	 * @param  string	$options	The options to pass into the class
	 *
	 * @param  SocialTableApp	$app	The app table
	 * @param  string	$options	The options to pass into the class
	 *
	 * @return SocialFieldItem		The field item class
	 */
	public function getClass()
	{
		$args = func_get_args();
		$count = func_num_args();

		if( $count === 0 )
		{
			return false;
		}

		$element = '';
		$group = SOCIAL_FIELDS_GROUP_USER;
		$field = null;
		$options = array();

		if( $args[0] instanceof SocialTableField )
		{
			$field = array_shift( $args );

			$app = $field->getApp();

			$element = $app->element;
			$group = $app->group;
		}
		elseif( $args[0] instanceof SocialTableApp )
		{
			if( $args[0]->type !== SOCIAL_APPS_TYPE_FIELDS )
			{
				return false;
			}

			$app = array_shift($args);

			$element = $app->element;
			$group = $app->group;
		}
		elseif( is_string( $args[0] ) )
		{
			$element = array_shift($args);

			if( !empty( $args[0] ) && is_string( $args[0] ) )
			{
				$group = array_shift($args);
			}
		}

		if( !empty( $args[0] ) && is_array( $args[0] ) )
		{
			$options = array_shift($args);
		}

		$file = SOCIAL_FIELDS . '/' . $group . '/' . $element . '/' . $element . '.php';

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		require_once( $file );

		$classname 	= 'SocialFields' . ucfirst( $group ) . ucfirst( $element );

		if( !class_exists( $classname ) )
		{
			return false;
		}

		$options = array_merge($options, array(
				'element' => $element,
				'group' => $group
			)
		);

		if( !empty( $field ) )
		{
			$params = $this->getFieldConfigValues( $field );
			$options['field'] = $field;
			$options['params'] = $params;
		}

		$class = new $classname( $options );

		return $class;
	}

	/**
	 * Triggers specific for custom fields. This needs to be triggered differently.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The event trigger name.
	 * @param	string	The group of the trigger. (E.g: user, groups)
	 * @param	Array	An array of SocialField objects.
	 * @param	Array	An array of data for the fields.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function trigger($event, $group, &$fields, &$data = array(), $callback = null)
	{
		// If there's no fields to load, we shouldn't be doing anything at all.
		if (empty($fields)) {
			return false;
		}
		// Initialize adapter if necessary.
		if (is_null($this->triggerer)) {
			// Create the triggers
			$this->triggerer = new SocialFieldTriggers($this->params);

			if (empty($this->user)) {
				$this->user = FD::user();
			}

			$this->triggerer->setUser($this->user);
		}

		// Change to is_callable because we've implemented magic method __call on SocialFieldTriggers
		$exists = is_callable(array($this->triggerer, $event));

		if (!$exists) {
			return false;
		}

		// Set the event name for element references
		$this->triggerer->setEvent($event);

		$arguments = array($group, &$fields, &$data, $callback);

		return call_user_func_array(array($this->triggerer, $event), $arguments);
	}

	public function getHandler()
	{
		if (is_null($this->handler)) {
			$this->handler = new SocialFieldHandlers();
		}

		return $this->handler;
	}

	/**
	 * Renders field widgets
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderWidgets($group, $view, $position, $args)
	{
		// Get the app that uses the unique key.
		$model = FD::model('Fields');

		// Get the unique key from the arguments
		$key = $args[0];

		// Get the user from the arguments
		$user = $args[1];

		$options = array('key' => $key , 'profile_id' => $user->profile_id , 'data' => true , 'dataId' => $user->id ,'dataType' => SOCIAL_TYPE_USER );

		// There should only be 1 field that is tied to a single unique key at all point of time.
		$fields 	= $model->getCustomFields( $options );

		if( !isset( $fields[ 0 ] ) )
		{
			return false;
		}

		$field 		= $fields[ 0 ];

		// Initialize default contents
		$contents 	= '';

		// Build the path to the field.
		$file = SOCIAL_FIELDS . '/' . $group . '/' . $field->element . '/widgets/' . $view . '/view.html.php';
		$exists = JFile::exists($file);

		if (!$exists) {
			return;
		}

		require_once($file);

		// Construct the class name for the widget
		$className = ucfirst($field->element) . 'FieldWidgets' . ucfirst($view);

		// Check if the class exists in this context.
		if (!class_exists($className)) {
			return;
		}

		$widgetObj = new $className();

		// Check if the position exists as a method.
		$exists = method_exists( $widgetObj , $position );

		if (!$exists) {
			return;
		}

		// Send the field as argument
		$args[] = $field;

		ob_start();
		call_user_func_array( array( $widgetObj , $position ) , $args );
		$output 	= ob_get_contents();
		ob_end_clean();

		$contents .= $output;

		// If nothing to display, just return false.
		if( empty( $contents ) )
		{
			return false;
		}

		// We need to wrap the app contents with our own wrapper.
		$theme 		= FD::themes();
		$theme->set( 'contents' , $contents );
		$contents	= $theme->output( 'site/apps/default.field.widget.' . strtolower( $view ) );

		return $contents;
	}

	/**
	 * Retrieves a formatted value from a particular custom field given the unique key
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableField	$field	The field table
	 * @param   string display type. E.g. listing, item, header and etc
	 * @param   boolean determine if the value should link to advanced search or not.
	 * @return	Mixed	The field value
	 */
	public function getValue( SocialTableField $field )
	{
		$class = $this->getClass( $field );

		if( $class === false )
		{
			return;
		}

		return $class->getValue();
	}

	/**
	 * Retrieves raw data from a particular custom field given the unique key
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableField	$field	The field table
	 * @return	Mixed	The field data
	 */
	public function getData( SocialTableField $field )
	{
		$class = $this->getClass( $field );

		if( $class === false )
		{
			return;
		}

		return $class->getData();
	}

	/**
	 * Retrieves raw data from a particular custom field given the unique key
	 *
	 * @since	1.0
	 * @access	public
	 *
	 * @param	string	$element	The element name
	 * @param	string	$group		The group name
	 *
	 * @param	SocialTableField	$field	The field table
	 *
	 * @param	SocialTableApp	$app	The app table
	 *
	 * @return	Mixed	The field data
	 */
	public function getOptions()
	{
		$class = call_user_func_array(array($this, 'getClass'), func_get_args());

		if( $class === false )
		{
			return;
		}

		return $class->getOptions();
	}

	/**
	 * Get the default manifest from defaults/fields.json
	 *
	 * @since	1.0
	 * @access	public
	 * @return	Object	The default manifest object
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com
	 */
	public function getDefaultManifest($type = SOCIAL_FIELDS_GROUP_USER)
	{
		static $manifest = array();

		if (empty($manifest[$type])) {
			$path		= SOCIAL_CONFIG_DEFAULTS . '/fields/config/' . $type . '.json';

			if (JFile::exists($path)) {
				$raw		= JFile::read( $path );
				$manifest[$type]	= FD::json()->decode( $raw );
			} else {
				$manifest[$type] = new stdClass();
			}
		}

		return $manifest[$type];
	}

	/**
	 * Get the default data from the manifest file of the core apps.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of applications
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getCoreManifest( $fieldGroup = SOCIAL_FIELDS_GROUP_USER , $apps )
	{
		// Ensure that it's an array.
		$apps 	= FD::makeArray( $apps );

		// If apps is empty, ignore.
		if( !$apps )
		{
			return false;
		}

		// Default value
		$fields 	= array();

		// Lets go through the list of apps that are core.
		foreach( $apps as $app )
		{
			// Get the full default configuration
			$config = $this->getFieldConfigParameters( $app->id );

			// Initialise an object that should stores only the default value
			$obj = new stdClass();

			// Manually extract the default values
			foreach( $config as $name => $fields )
			{
				if( property_exists( $fields, 'default' ) )
				{
					$obj->$name = $fields->default;
				}
			}

			// We need to set the application id here.
			$obj->app_id	= $app->id;

			// Add them to the fields list.
			$fields[]		= $obj;
		}

		return $fields;
	}

	private function loadAppData( $appId )
	{
		if( count( self::$_apps ) == 0 )
		{
			// lets load all apps.
			// $model		= FD::model( 'Apps' );
			// $options	= array( 'type' => SOCIAL_APPS_TYPE_FIELDS );

			// $apps		= $model->setLimit(0)->getApps( $options );

			// if( $apps )
			// {
			// 	foreach( $apps as $app )
			// 	{
			// 		self::$_apps[ $app->id ] = $app;
			// 	}
			// }

			$dbcache = FD::dbcache('app');
			// TODO: Change this to where case.
			$result = $dbcache->loadObjectList(array('type' => SOCIAL_APPS_TYPE_FIELDS));

			self::$_apps = $dbcache->bindTable($result);
		}

		return self::$_apps[ $appId ];
	}

	/**
	 * Retrieves a field configuration.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The key to #__social_apps
	 * @param	int		Optional field id. If there's no field id, we assume that it's a new field being added to the form.
	 * @param	bool	If true, method will return a json string instead of an object.
	 *
	 * @return	object	The field configuration
	 */
	public function getFieldConfigParameters( $appId, $groupTabs = false, $jsonString = false )
	{
		// Note: We put this function here instead of field table because sometimes a field might not have a field id to load the parameters

		static $configParams = array();

		if (empty($configParams[$appId])) {

			// Load json library
			$json = FD::json();

			$app = $this->loadAppData($appId);
			$config = $app->getManifest();

			if (empty($config)) {
				$config = new stdClass();
			}

			// Get the default core parameters
			$defaults = $this->getDefaultManifest($app->group);

			// Manually perform a deep array merge to carry the defaults over to the config object
			foreach ($defaults as $name => $params) {
				
				if (property_exists($config, $name)) {

					if (is_bool($config->$name)) {
						$params = $config->$name;
					} else {
						$params = (object) array_merge( (array) $params, (array) $config->$name );
					}
				}

				$config->$name = $params;
			}

			foreach( $config as $name => &$field )
			{
				$this->translateConfigParams( $field );

				if( isset( $field->subfield ) )
				{
					foreach( $field->subfields as $subname => &$subfield )
					{
						$this->translateConfigParams( $subfield );
					}
				}
			}

			$configParams[$appId] = $config;
		}

		// Make a clone to prevent pass by reference
		$data = clone $configParams[$appId];

		if( $groupTabs )
		{
			$groupedConfig = new stdClass();

			foreach( $data as $key => $value )
			{
				if( !is_bool( $value ) )
				{
					// This will enforce group to be either basic or advance
					// $type = property_exists( $value, 'group' ) && $value->group == 'advance' ? 'advance' : 'basic';

					// This will allow any group
					$type = property_exists( $value, 'group' ) ? $value->group : 'basic';

					if( !property_exists( $groupedConfig, $type ) )
					{
						$groupedConfig->$type = new stdClass();
					}

					$groupedConfig->$type->title = JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_TAB_' . strtoupper( $type ) );

					if( !property_exists( $groupedConfig->$type, 'fields' ) )
					{
						$groupedConfig->$type->fields = new stdClass();
					}

					$groupedConfig->$type->fields->$key = $value;
				}
			}

			$data = $groupedConfig;
		}

		if( $jsonString )
		{
			return $json->encode( $data );
		}

		return $data;
	}

	/**
	 * Retrieves a field configuration.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The key to #__social_apps
	 * @param	int			Optional field id. If there's no field id, we assume that it's a new field being added to the form.
	 * @return	JRegistry	Registry of the field configuration
	 */
	public function getFieldConfigValues( $appId, $fieldId = 0 )
	{
		$field = null;

		// If first parameter is object, we assume caller pass in field table
		if( is_object( $appId ) )
		{
			// Reassign appid accordingly
			$field = $appId;
			$appId = $field->app_id;
		}

		$defaults	= $this->getFieldConfigParameters( $appId );

		// If the first parameter is appId, then $field should be null by now, and if second parameter is valid, then we load the table
		if( empty( $field ) && !empty( $fieldId ) )
		{
			$field = FD::table( 'field' );
			$field->load( $fieldId );
		}

		// Initialise a registry first
		$params		= FD::registry();

		// Initialise a string library
		$string = FD::string();

		// If $field is still empty then we shouldn't get the field parameters
		if( !empty( $field ) )
		{
			// Get the params from the table
			$params		= $field->getParams();

			// Get the choices
			$choices 	= $field->getOptions();

			// Manually merge in the choices into the parameter object
			foreach( $choices as $choice => $values )
			{
				foreach( $values as $id => &$value )
				{
					// $value->label = JText::_( $value->label );
					// $value->title = JText::_( $value->title );
					$value->label = $string->escape($value->label);
				}

				$params->set( $choice, $values );
			}
		}

		// This is to get the default values of the params and
		// merge it in as the value if the value does not exist yet
		foreach( $defaults as $name => $obj )
		{
			// Check if this name exists in the params
			if( !$params->exists( $name ) )
			{
				$default = '';

				if( is_bool( $obj ) )
				{
					$default = $obj;
				}

				if( isset( $obj->default ) )
				{
					$default = $obj->default;
				}

				$params->set( $name, $default );
			}

			if( isset( $obj->subfields ) )
			{
				foreach( $obj->subfields as $subname => $subfield )
				{
					if( !$params->exists( $name . '_' . $subname ) )
					{
						$default = isset( $subfield->default ) ? $subfield->default : '';

						$params->set( $name . '_' . $subname, $default );
					}
				}
			}
		}

		return $params;
	}

	/**
	 * Retrieves the custom fields configuration html output
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getConfigHtml($id, $fieldId = 0)
	{
		// Load the app
		$app = FD::table('App');
		$app->load($id);

		// App title
		$title = $app->title;

		// Get config parameters
		$params = $this->getFieldConfigParameters($id, true);

		// Get config values
		$values = $this->getFieldConfigValues($id, $fieldId);

		foreach ($params as $tab => &$data) {

			foreach ($data->fields as $name => &$field) {

				// Normalize the types here
				$this->normalizeConfigType($field);

				// Normalize the subfield types too
				if (isset($field->subfields)) {

					foreach ($field->subfields as $subname => $subfield) {
						$this->normalizeConfigType($subfield);
					}
				}

				// Check the values for this name
				if (!$values->exists($name)) {
					$values->set($name, '');
				}
			}
		}

		$tabs = array('basic', 'core', 'view', 'advance');

		$tabs = array_merge($tabs, array_diff(array_keys((array) $params), $tabs));

		$theme = FD::themes();

		$theme->set('title', $title);
		$theme->set('params', $params);
		$theme->set('values', $values);
		$theme->set('tabs', $tabs);

		return $theme->output('admin/profiles/fields/config');
	}

	/**
	 * Normalizes the field types that is retrieved from the config file
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	private function normalizeConfigType(&$field)
	{
		$type = 'boolean';

		if (isset($field->type)) {

			if ($field->type == 'input' || $field->type == 'text') {
				$type = 'input';
			}

			if ($field->type == 'dropdown' || $field->type == 'list' || $field->type == 'select') {
				$type = 'dropdown';
			}

			if ($field->type == 'editors' || $field->type == 'checkbox' || $field->type == 'radio' || $field->type == 'textarea' || $field->type == 'choices' || $field->type == 'article') {
				$type = $field->type;
			}
		}

		$field->type = $type;
	}

	/*
	 * Returns html formatted data for validations
	 *
	 * @param 	string 	$element 	The field element.
	 * @return 	string 	HTML formatted values.
	 */
	public function renderValidations( $element )
	{
		$path	= SOCIAL_MEDIA . DS . SOCIAL_APPS_TYPE_FIELDS . DS . strtolower( $element ) . DS . 'tmpl' . DS . 'params.xml';

		if( !FD::get( 'Files' )->exists( $path ) )
		{
			return false;
		}

		$parser	= JFactory::getXMLParser( 'Simple' );
		$parser->loadFile( $path );

		if( !$parser->document->getElementByPath( 'validations' ) )
		{
			return false;
		}

		$validations	= $parser->document->getElementByPath( 'validations' )->children();

		return FD::get( 'Themes' )->set( 'validations' , $validations )->output( 'admin.profiles.fields_validation' );
	}

	/**
	 * Loads a specific language file given the field's element and group.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The group's name.
	 * @param	string		The element's name.
	 * @return
	 */
	public function loadLanguage( $group , $element )
	{
		$lang 		= JFactory::getLanguage();
		$file 		= 'plg_fields_' . $group . '_' . $element;

		// Load the language file.
		$lang->load( $file , JPATH_ROOT . '/administrator' );
	}

	/**
	 * Helper function to translate any config parameters text
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.1
	 * @access private
	 * @param  SocialTableField    $field The field table item
	 */
	private function translateConfigParams( &$field )
	{
		// Only try to JText the label field if it exists.
		if( isset( $field->label ) )
		{
			$field->label	= JText::_( $field->label );
		}

		// Only try to JText the tooltip field if it exists.
		if( isset( $field->tooltip ) )
		{
			$field->tooltip	= JText::_( $field->tooltip );
		}

		// Do not translate the default value of the field because the default might be a language key
		// if( isset( $field->default ) && is_string( $field->default ) )
		// {
		// 	$field->default = JText::_( $field->default );
		// }

		// If there are options set, we need to jtext them as well.
		if( isset( $field->option ) )
		{
			$field->option 	= FD::makeArray( $field->option );

			foreach( $field->option as &$option )
			{
				$option->label 	= JText::_( $option->label );
			}
		}

		// Only try to JText the info value if it exist
		if( isset( $field->info ) )
		{
			$field->info = JText::_( $field->info );
		}
	}

	public function saveFields($uid, $type, &$data, $options = array())
	{
		// The fact that we come in here means there are some thing changed

		$isCopy = isset( $options['copy'] ) && $options['copy'];

		if( !$isCopy && isset( $data->deleted ) )
		{
			$deleted = $data->deleted;

			foreach( $deleted as $deletedtype => $deletedids )
			{
				if( !empty( $deletedids ) )
				{
					$name = $deletedtype == 'pages' ? 'fieldstep' : 'field';

					foreach( $deletedids as $deletedid )
					{
						$table = FD::table( $name );
						$state = $table->load( $deletedid );

						if( $state )
						{
							$table->delete();
						}
					}
				}
			}
		}

		$sequence = 1;

		foreach( $data->data as $step )
		{
			$stepTable = FD::table( 'fieldstep' );

			$state = false;

			if( !$step->newpage )
			{
				$state = $stepTable->load( $step->id );
			}

			if( $isCopy )
			{
				$stepTable->_isCopy = true;
				$stepTable->id = null;
				$state = false;
			}

			// If there's a problem retrieving this step, this is probably a new step.
			if( !$state )
			{
				$stepTable->uid			= $uid;
				$stepTable->type		= $type;
				$stepTable->state		= SOCIAL_STATE_PUBLISHED;
				$stepTable->created		= FD::date()->toMySQL();
			}

			$stepTable->sequence	= $sequence;

			// Set the step values
			$stepTable->processParams( $step );

			// Try to store the step.
			$state 	= $stepTable->store();

			// If there's a problem storing the state, we should log errors here.
			if (!$state) {
				return false;
			}

			// Assign back the id to pass back to client
			$step->id = $stepTable->id;

			$sequence++;

			// When there's no fields for this step, just skip the rest of processing.
			if( !isset( $step->fields ) )
			{
				continue;
			}

			// Reset the ordering for the fields.
			$ordering	= 0;

			// Now let's go through the list of fields for this step.
			foreach( $step->fields as $field )
			{
				$appTable 	= FD::table( 'App' );
				$fieldTable	= FD::table( 'Field' );

				$appTable->load( $field->appid );

				if( !empty( $field->fieldid ) )
				{
					$fieldTable->load( $field->fieldid );
				}

				if( $isCopy )
				{
					// Before we set the id to null, get the choices first
					$checkChoices = (array) $field->choices;
					if (empty($checkChoices)) {
						$field->choices = $fieldTable->getOptions();
					}

					$fieldTable->_isCopy = true;
					$fieldTable->id = null;
				}

				// Set the application id.
				$fieldTable->app_id				= $field->appid;

				// Set the step id since we now know the step id.
				$fieldTable->step_id			= $stepTable->id;

				// Let's process the params
				if( isset( $field->params ) )
				{
					$fieldTable->processParams( $field->params );
				}

				// Set the ordering now.
				$fieldTable->ordering			= $ordering;

				// The core state would be dependent on the app's settings.
				$fieldTable->core				= $appTable->core;

				// Let's try to store the field now.
				$state 	= $fieldTable->store();

				// If there's any problems storing the state, we should log errors and not proceed.
				if (!$state) {
					return false;
				}

				// Carry out actions that can only be done after the initial store to generate field id here

				// Assign back the field id to pass back to client
				$field->fieldid = $fieldTable->id;

				// Check if unique key for this field is valid and assign it back for the client
				$field->unique_key = $fieldTable->checkUniqueKey();

				// Save any post actions
				$fieldTable->store();

				// Check for choices here
				if( isset( $field->choices ) )
				{
					foreach( $field->choices as $name => $choices )
					{
						$origChoices = $fieldTable->getOptions( $name );

						$currentChoices = array();

						$choiceOrdering = 1;

						foreach( $choices as $choice )
						{
							$fieldoptionsTable = FD::table( 'FieldOptions' );

							if( !empty( $choice->id ) )
							{
								$fieldoptionsTable->load( $choice->id );
							}

							if( $isCopy )
							{
								$fieldoptionsTable->id = null;
							}

							$fieldoptionsTable->parent_id	= $fieldTable->id;
							$fieldoptionsTable->key			= $name;
							$fieldoptionsTable->title		= $choice->title;
							$fieldoptionsTable->value		= $choice->value;
							$fieldoptionsTable->ordering	= $choiceOrdering;

							if( isset( $choice->default ) ) {
								$fieldoptionsTable->default = $choice->default;
							}

							if (!$fieldoptionsTable->store()) {
								return false;
							}

							// Assign back the options id to pass back to client
							$choice->id = $fieldoptionsTable->id;

							$currentChoices[] = $choice->id;

							$choiceOrdering++;
						}

						if( !$isCopy )
						{
							foreach( $origChoices as $origId => $origChoices )
							{
								if( !in_array( $origId, $currentChoices ) )
								{
									$origChoices->delete();
								}
							}
						}
					}
				}

				$ordering++;
			}
		}

		return true;
	}

	/**
	 * Deprecated from 1.3.
	 * Checks if the user's profile is complete by triggering onFieldCheck.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @deprecated Deprecated from 1.3. Filled fields are now denormalized.
	 * @access public
	 */
	public function checkCompleteProfile()
	{
		$me = FD::user();

		// If current user is a guest or no profile is assigned, we cannot check against anything
		if (!$me->isRegistered() || empty($me->profile_id)) {
			return true;
		}

		static $result = null;

		if (!isset($result)) {
			$model = FD::model('fields');

			$fields = $model->getCustomFields(array('profile_id' => $me->profile_id, 'data' => true, 'dataId' => $me->id, 'dataType' => SOCIAL_TYPE_USER, 'visible' => SOCIAL_PROFILES_VIEW_EDIT, 'group' => SOCIAL_FIELDS_GROUP_USER));

			if (empty($fields)) {
				$result = array();
				return $result;
			}

			$args 	= array(&$me);

			$result = $this->trigger('onFieldCheck', SOCIAL_FIELDS_GROUP_USER, $fields, $args);
		}

		return $result;
	}
}
