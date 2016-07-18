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
ES::import('admin:/includes/fields/value');

// We forcefully load the site language here because in backend edit user/group/event page, the field is making an ajax call to the respective field, and there will be success/error message accordingly
ES::language()->loadSite();

abstract class SocialFieldItem
{
	/**
	 * States if this field is an extended field or not
	 * @var boolean/string (false by default)
	 */
	public $extends		= false;

	/**
	 * Holds the object relation mapping for the field item.
	 * @var	SocialTableField
	 */
	public $field 	= null;

	/**
	 * Holds the field group name.
	 * @var	string
	 */
	public $group	= null;

	/**
	 * Holds the field element name.
	 * @var	string
	 */
	public $element	= null;

	/**
	 * Holds the field parameter object.
	 * @var	JParameter
	 */
	public $params		= null;

	/**
	 * Holds the field configuration object.
	 * @var	JParameter
	 */
	public $config		= null;

	/**
	 * Holds the SocialThemes object.
	 * @var	SocialThemes
	 */
	public $theme		= null;

	/**
	 * Determines if this field has an error.
	 * @var	bool
	 */
	public $hasErrors	= false;

	/**
	 * Stores any error messages.
	 * @var	string
	 */
	public $error 		= null;

	/**
	 * Stores a list of already initiated models.
	 * @var	Array
	 */
	protected $models 	= array();

	/**
	 * Stores a list of already initiated views.
	 * @var	Array
	 */
	protected $views 	= array();

	/**
	 * Stores the state of tables being loaded
	 * @var	bool
	 */
	protected $tables = false;

	/**
	 * Stores the current triggered event
	 * @var string
	 */
	public $event = null;

	public function __construct($config = array())
	{
		$app = JFactory::getApplication();

		$this->input = ES::request();
		$this->config = ES::config();
		$this->my = ES::user();
		$this->doc = JFactory::getDocument();

		if ($this->doc->getType() == 'ajax') {
			$this->ajax = ES::ajax();
		}
		
		$this->init($config);
	}

	/**
	 * Normalizes a given set of data
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function normalize($data, $key, $default = null)
	{
		if (isset($data[$key])) {
			return $data[$key];
		}

		return $default;
	}

	/**
	 * Generic method to format the export data
	 *
	 * @since	1.3
	 * @access	public
	 * @return	string
	 */
	public function onExport($data, $userid)
	{
		$field = $this->field;

		$formatted = array();

		if (isset($data[$field->id])) {
			$formatted = $data[$field->id];
		} else {
			$formatted['default'] = '';
		}

		return $formatted;
	}

	/**
	 * Generic method to retrieve data of a field
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string
	 */
	public function getFieldData()
	{
		return $this->field->data;
	}

	/**
	 * Get the params from the field.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getParams()
	{
		// Default params
		$params = FD::registry();

		if (isset($this->field)) {
			$params = $this->field->getParams();
		}

		return $params;
	}

	/**
	 * This is to return a key value paired array to indicate what choices are available for callee to populate the choices.
	 * This options is not field specific, the options should be generic choices available in the field
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.1
	 * @access public
	 * @return Array    The available options
	 */
	public function getOptions()
	{
		return $this->field->getOptions();
	}

	/**
	 * Returns a formatted value from the field
	 * Fallback is to return raw data
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.1
	 * @access public
	 * @return Mixed    The formatted field values
	 */
	public function getValue()
	{
		return $this->getValueContainer();
	}

	/**
	 * Returns a formatted data from the field
	 * Fallback is to return raw data
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.1
	 * @access public
	 * @return Mixed    The formatted field datas
	 */
	public function getData()
	{
		return $this->getFieldData();
	}

	/**
	 * Returns a formatted data for display from the field
	 * Fallback is to return the data of getValue()
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @return Mixed    The formatted field datas
	 */
	public function getDisplayValue()
	{
		return $this->getValue();
	}

	/**
	 * Initialises the field properties from trigger functions
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	final public function init( $properties = array() )
	{
		if (empty($properties)) {
			return;
		}

		foreach ($properties as $key => $val) {
			// Make this variable available in property scope.
			$this->$key 	= $val;

			// Make this variable available in theme scope.
			$this->set( $key , $val );
		}

		// Manually set the self class into the themes
		$this->set( 'class', $this );
	}

	/**
	 * Responsible to help fields to output theme files.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	final public function display($templateFile = null)
	{
		// Initialize the theme object for the current app.
		if (!$this->theme) {
			$this->theme = FD::themes();
		}

		/* Template hierarchy
		1. Check if field is extended
			a. Check if child field event.php exists
			b. Check if child field event_content.php exists
			c. Check if child field content.php exists
		2. If field is not extended or no child field template exists
			a. Check if event.php exists
			b. Check if event_content.php exists
			c. Check if content.php exists
		*/

		$namespace = $this->resolve($templateFile);

		if (empty($namespace)) {
			echo JText::_('COM_EASYSOCIAL_FIELDS_NO_THEME_FILE_FOUND') . ': ' . $this->group . '/' . $this->element . '/' .$templateFile;
			return;
		}

		echo $this->theme->output($namespace);
	}

	/**
	 * Shorthand function to load field template on a new theme class.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.1
	 * @access public
	 * @param  string    $file   The theme file
	 * @param  array     $params Parameters to pass in to the theme class
	 * @return string            The HTML content
	 */
	final public function loadTemplate( $file, $params = array() )
	{
		$namespace = $this->resolve( $file );

		if( empty( $namespace ) )
		{
			echo JText::_( 'COM_EASYSOCIAL_FIELDS_NO_THEME_FILE_FOUND' ) . ': ' . $file;
			return;
		}

		$theme = FD::get( 'Themes' );

		foreach( $params as $k => $v )
		{
			$theme->set( $k, $v );
		}

		return $theme->output( $namespace );
	}

	/**
	 * Shorthand function to include field template on the parent theme class.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.1
	 * @access public
	 * @param  string    $file   The theme file
	 * @param  array     $params Parameters to pass in to the theme class
	 * @return string            The HTML content
	 */
	final public function includeTemplate( $file, $params = array() )
	{
		$namespace = $this->resolve( $file );

		if( empty( $namespace ) )
		{
			echo JText::_( 'COM_EASYSOCIAL_FIELDS_NO_THEME_FILE_FOUND' ) . ': ' . $file;
			return;
		}

		foreach( $params as $k => $v )
		{
			$this->theme->set( $k, $v );
		}

		return $this->theme->output( $namespace );
	}

	/**
	 * Returns the full namespace of the template file
	 *
	 * @access public
	 * @param  string $file
	 * @return string
	 */
	public function resolve($file)
	{
		// Default namespace
		$namespace = '';
		$class = get_class($this);

		do {
			// Get the group and the element from the class name
			$segments = preg_split( '/(?=[A-Z])/', str_ireplace( 'SocialFields', '', $class ), '-1', PREG_SPLIT_NO_EMPTY );

			// Get the group
			$group = strtolower(array_shift($segments));

			// Instead of strtolower, we do lcfirst because some field's element is using camelcasing. strtolower will cause the theme file to not resolve properly especially on servers that has strict case file system.
			$element = lcfirst(implode('', $segments));

			if (!$group || !$element) {
				break;
			}

			// Get the namespace now given the file, group and element
			$namespace = $this->checkTemplateFile($file, $group, $element);


			if (empty($namespace)) {
				$class = get_parent_class( $class );
			}

			// Stop the loop when namespace is not empty or there is no more parent class or class reaches parent class SocialFieldItem
		} while(empty($namespace) || $class === false || $class === 'SocialFieldItem');

		return $namespace;
	}

	/**
	 * Checks if the template file exists and returns the template namespace
	 *
	 * @access private
	 * @param  string $file
	 * @param  string $group
	 * @param  string $element
	 * @return string
	 */
	private function checkTemplateFile( $file, $group, $element )
	{
		$checkSubFiles = false;

		// If file is null, then we generate it based on the event that is triggered
		if( is_null( $file ) && !empty( $this->event ) )
		{
			$file = strtolower( substr( $this->event, 2 ) );

			// We only check sub files if we are trying to display an event based theme file
			$checkSubFiles = true;
		}

		do
		{
			$prefix = 'fields/' . $group . '/' . $element;

			$fieldFile = $prefix . '/' . $file;

			if( $this->theme->exists( $fieldFile ) )
			{
				return $fieldFile;
			}

			$generalFieldFile = $prefix . '/event';

			if( $this->theme->exists( $generalFieldFile ) )
			{
				return $generalFieldFile;
			}

			if( $checkSubFiles )
			{
				$filecontent = $file;

				$subFile = 'site/fields/' . $filecontent;

				if( $this->theme->exists( $subFile ) )
				{
					do
					{
						$subFieldFile = $prefix . '/' . $filecontent . '_content';

						if( $this->theme->exists( $subFieldFile ) )
						{
							$this->theme->set( 'subNamespace', $subFieldFile );
							return $subFile;
						}

						// If reach here, means no subFieldFile is found, then we fallback
						$subFallback = $this->getFallbackTemplate( $filecontent );

						if( $subFallback !== false )
						{
							$filecontent = $subFallback;
						}
					} while( $subFallback !== false );

					$subFieldContent = $prefix . '/content';

					if( $this->theme->exists( $subFieldContent ) )
					{
						$this->theme->set( 'subNamespace', $subFieldContent );
						return $subFile;
					}
				}
			}

			// If we reach here, means no return is executed, then we try to fallback
			$fallback = $this->getFallbackTemplate( $file );

			if( $fallback !== false )
			{
				$file = $fallback;
			}

		} while( $fallback !== false );

		return false;
	}

	private function getFallbackTemplate($file)
	{
		// Define all possible fallback events here
		// TODO: Better OOP approach to this in the future
		switch ($file)
		{
			case 'registermini':
				return 'register';
				break;
			case 'adminedit':
				return 'edit';
				break;
		}

		return false;
	}

	/**
	 * Set the field with some errors
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The error message
	 * @return
	 */
	public function setError($message = null)
	{
		// Set the field to report an error.
		$this->hasErrors = true;

		// Set the error message.
		$this->error = JText::_($message);

		return false;
	}

	/**
	 * Helper method to determine if this field is required.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isRequired()
	{
		return $this->field->isRequired();
	}

	/**
	 * Determines if there's an error in this field.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function hasError()
	{
		return (bool) $this->hasErrors;
	}

	/**
	 * Get error message
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string
	 */
	public function getError( $errors = null )
	{
		// If array of errors is passed in, we search the error from this array instead

		if( !is_null( $errors ) )
		{
			return !empty( $errors[$this->inputName] ) ? $errors[$this->inputName] : false;
		}

		return $this->error;
	}

	/**
	 * Sets a variable to the theme object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function set()
	{
		if (!$this->theme)
		{
			$this->theme = FD::get('Themes');
		}

		$args = func_get_args();

		if (count($args) === 1 && is_array($args[0]))
		{
			$vars = $args[0];

			foreach ($vars as $key => $var)
			{
				$this->theme->set($key, $var);
			}
		}

		if (count($args) === 2)
		{
			$this->theme->set($args[0], $args[1]);
		}
	}

	/**
	 * Gets the field model
	 *
	 * @access public
	 * @param  string $name
	 * @return SocialFieldModel
	 */
	public function model( $name = null )
	{
		if( empty( $name ) )
		{
			$name = $this->element;
		}

		if( !isset( $this->models[$name] ) )
		{
			$base = SOCIAL_FIELDS . '/' . $this->group . '/' . $this->element . '/models';

			$classname = 'SocialFieldModel' . ucfirst( $this->group ) . ucfirst( $name );

			if( !class_exists( $classname ) )
			{
				if( !JFile::exists( $base . '/' . $name . '.php' ) )
				{
					return JError::raiseError( 500, JText::sprintf( 'COM_EASYSOCIAL_FIELDS_MODEL_DOES_NOT_EXIST', $name ) );
				}

				require_once( $base . '/' . $name . '.php' );
			}

			if( !class_exists( $classname ) )
			{
				return JError::raiseError( 500, JText::sprintf( 'COM_EASYSOCIAL_FIELDS_CLASS_DOES_NOT_EXIST', $classname ) );
			}

			$model = new $classname( $this->group, $this->element );

			$this->models[$name] = $model;
		}

		return $this->models[$name];
	}

	/**
	 * Gets the field table
	 *
	 * @access public
	 * @param  string $name
	 * @param  string $prefix
	 * @return SocialFieldTable
	 */
	public function table( $name = null, $prefix = '' )
	{
		if( !$this->tables )
		{
			$base = SOCIAL_FIELDS . '/' . $this->group . '/' . $this->element . '/tables';

			JTable::addIncludePath( $base );

			$this->tables = true;
		}

		if( empty( $name ) )
		{
			$name = $this->element;
		}

		$prefix	= empty( $prefix ) ? 'SocialFieldTable' . ucfirst( $this->group ) : $prefix;

		$table	= JTable::getInstance( $name , $prefix );

		return $table;
	}

	/**
	 * Gets the field view
	 *
	 * @access public
	 * @param  string $name
	 * @return SocialFieldView
	 */
	public function view( $name = null )
	{
		if( empty( $name ) )
		{
			$name = $this->element;
		}

		if( !isset( $this->views[$name] ) )
		{
			$base = SOCIAL_FIELDS . '/' . $this->group . '/' . $this->element . '/views';

			$classname = 'SocialFieldView' . ucfirst( $this->group ) . ucfirst( $name );

			if( !class_exists( $classname ) )
			{
				if( !JFile::exists( $base . '/' . $name . '.php' ) )
				{
					return JError::raiseError( 500, JText::sprintf( 'COM_EASYSOCIAL_FIELDS_VIEW_DOES_NOT_EXIST', $name ) );
				}

				require_once( $base . '/' . $name . '.php' );
			}

			if( !class_exists( $classname ) )
			{
				return JError::raiseError( 500, JText::sprintf( 'COM_EASYSOCIAL_FIELDS_CLASS_DOES_NOT_EXIST', $classname ) );
			}

			$view = new $classname( $this->group, $this->element );

			$this->views[$name] = $view;
		}

		return $this->views[$name];
	}

	/**
	 * Shorthand function to check privacy of the viewing user against the privacy set in the field
	 *
	 * @access public
	 * @param  SocialUser $user
	 * @return boolean
	 */
	public function allowedPrivacy( $user, $type = SOCIAL_TYPE_FIELD )
	{
		$result = true;

		// For now we only validate privacy if the object is a SocialUser object
		// This is because group fields sometimes rides on user field, then when it comes to this part, it fails because we don't have privacy for group fields for now
		if ($user instanceof SocialUser) {
			$my = FD::user();
			$lib = FD::privacy( $my->id );

			$result = $lib->validate( 'field.' . $this->element, $this->field->id, $type, $user->id );
		}

		return $result;
	}

	/**
	 * Shorthand function to escape string
	 *
	 * @access public
	 * @param  string $text
	 * @return string
	 */
	public function escape($text)
	{
		return ES::string()->escape( $text );
	}

	/**
	 * Returns a standard value container for all fields.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @return SocialFieldValue    Field value standard object.
	 */
	final public function getValueContainer()
	{
		$class = 'SocialFields' . ucfirst($this->group) . ucfirst($this->element) . 'Value';

		if (class_exists($class)) {
			$container = new $class($this->field);
		} else {
			$container = new SocialFieldValue($this->field);
		}

		return $container;
	}

	/**
	 * Retrieves the field value from post data
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	final public function getValueFromPost($post)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $value;
	}
}

FD::import( 'admin:/includes/model' );

class SocialFieldModel extends EasySocialModel
{
	protected $group = null;
	protected $element = null;

	private $models = array();

	private $views = array();

	private $tables = null;

	public function __construct( $group, $element )
	{
		$this->group = $group;
		$this->element = $element;
	}

	/**
	 * Gets the field model
	 *
	 * @access public
	 * @param  string $name
	 * @return SocialFieldModel
	 */
	public function model( $name = null )
	{
		if( empty( $name ) )
		{
			$name = $this->element;
		}

		if( !isset( $this->models[$name] ) )
		{
			$base = SOCIAL_FIELDS . '/' . $this->group . '/' . $this->element . '/models';

			$classname = 'SocialFieldModel' . ucfirst( $this->group ) . ucfirst( $name );

			if( !class_exists( $classname ) )
			{
				if( !JFile::exists( $base . '/' . $name . '.php' ) )
				{
					return JError::raiseError( 500, JText::sprintf( 'COM_EASYSOCIAL_FIELDS_MODEL_DOES_NOT_EXIST', $name ) );
				}

				require_once( $base . '/' . $name . '.php' );
			}

			if( !class_exists( $classname ) )
			{
				return JError::raiseError( 500, JText::sprintf( 'COM_EASYSOCIAL_FIELDS_CLASS_DOES_NOT_EXIST', $classname ) );
			}

			$model = new $classname( $this->group, $this->element );

			$this->models[$name] = $model;
		}

		return $this->models[$name];
	}

	/**
	 * Gets the field table
	 *
	 * @access public
	 * @param  string $name
	 * @param  string $prefix
	 * @return SocialFieldTable
	 */
	public function table( $name = null, $prefix = '' )
	{
		if( !$this->tables )
		{
			$base = SOCIAL_FIELDS . '/' . $this->group . '/' . $this->element . '/tables';

			JTable::addIncludePath( $base );

			$this->tables = true;
		}

		if( empty( $name ) )
		{
			$name = $this->element;
		}

		$prefix	= empty( $prefix ) ? 'SocialFieldTable' . ucfirst( $this->group ) : $prefix;

		$table	= JTable::getInstance( $name , $prefix );

		return $table;
	}

	/**
	 * Gets the field view
	 *
	 * @access public
	 * @param  string $name
	 * @return SocialFieldView
	 */
	public function view( $name = null )
	{
		if( empty( $name ) )
		{
			$name = $this->element;
		}

		if( !isset( $this->views[$name] ) )
		{
			$base = SOCIAL_FIELDS . '/' . $this->group . '/' . $this->element . '/views';

			$classname = 'SocialFieldView' . ucfirst( $this->group ) . ucfirst( $name );

			if( !class_exists( $classname ) )
			{
				if( !JFile::exists( $base . '/' . $name . '.php' ) )
				{
					return JError::raiseError( 500, JText::sprintf( 'COM_EASYSOCIAL_FIELDS_VIEW_DOES_NOT_EXIST', $name ) );
				}

				require_once( $base . '/' . $name . '.php' );
			}

			if( !class_exists( $classname ) )
			{
				return JError::raiseError( 500, JText::sprintf( 'COM_EASYSOCIAL_FIELDS_CLASS_DOES_NOT_EXIST', $classname ) );
			}

			$view = new $classname( $this->group, $this->element );

			$this->views[$name] = $view;
		}

		return $this->views[$name];
	}
}

class SocialFieldView
{
	protected $field = null;

	protected $group = null;
	protected $element = null;

	private $theme = null;

	private $models = array();

	private $views = array();

	private $tables = null;

	public $params = null;
	public $inputName = null;

	public function __construct( $group, $element )
	{
		$this->group = $group;
		$this->element = $element;

		$this->theme = FD::themes();
	}

	/**
	 * Initialises the field view
	 *
	 * @access public
	 * @param  SocialFieldItem $field
	 */
	public function init( $field )
	{
		$this->field = $field;

		$this->params = FD::fields()->getFieldConfigValues( $field );

		$this->inputName = SOCIAL_FIELDS_PREFIX . $field->id;
	}

	/**
	 * Gets the field model
	 *
	 * @access public
	 * @param  string $name
	 * @return SocialFieldModel
	 */
	public function model( $name = null )
	{
		if( empty( $name ) )
		{
			$name = $this->element;
		}

		if( !isset( $this->models[$name] ) )
		{
			$base = SOCIAL_FIELDS . '/' . $this->group . '/' . $this->element . '/models';

			$classname = 'SocialFieldModel' . ucfirst( $this->group ) . ucfirst( $name );

			if( !class_exists( $classname ) )
			{
				if( !JFile::exists( $base . '/' . $name . '.php' ) )
				{
					return JError::raiseError( 500, JText::sprintf( 'COM_EASYSOCIAL_FIELDS_MODEL_DOES_NOT_EXIST', $name ) );
				}

				require_once( $base . '/' . $name . '.php' );
			}

			if( !class_exists( $classname ) )
			{
				return JError::raiseError( 500, JText::sprintf( 'COM_EASYSOCIAL_FIELDS_CLASS_DOES_NOT_EXIST', $classname ) );
			}

			$model = new $classname( $this->group, $this->element );

			$this->models[$name] = $model;
		}

		return $this->models[$name];
	}

	/**
	 * Gets the field table
	 *
	 * @access public
	 * @param  string $name
	 * @param  string $prefix
	 * @return SocialFieldTable
	 */
	public function table( $name = null, $prefix = '' )
	{
		if( !$this->tables )
		{
			$base = SOCIAL_FIELDS . '/' . $this->group . '/' . $this->element . '/tables';

			JTable::addIncludePath( $base );

			$this->tables = true;
		}

		if( empty( $name ) )
		{
			$name = $this->element;
		}

		$prefix	= empty( $prefix ) ? 'SocialFieldTable' . ucfirst( $this->group ) : $prefix;

		$table	= JTable::getInstance( $name , $prefix );

		return $table;
	}

	/**
	 * Gets the field view
	 *
	 * @access public
	 * @param  string $name
	 * @return SocialFieldView
	 */
	public function view( $name = null )
	{
		if( empty( $name ) )
		{
			$name = $this->element;
		}

		if( !isset( $this->views[$name] ) )
		{
			$base = SOCIAL_FIELDS . '/' . $this->group . '/' . $this->element . '/views';

			$classname = 'SocialFieldView' . ucfirst( $this->group ) . ucfirst( $name );

			if( !class_exists( $classname ) )
			{
				if( !JFile::exists( $base . '/' . $name . '.php' ) )
				{
					return JError::raiseError( 500, JText::sprintf( 'COM_EASYSOCIAL_FIELDS_VIEW_DOES_NOT_EXIST', $name ) );
				}

				require_once( $base . '/' . $name . '.php' );
			}

			if( !class_exists( $classname ) )
			{
				return JError::raiseError( 500, JText::sprintf( 'COM_EASYSOCIAL_FIELDS_CLASS_DOES_NOT_EXIST', $classname ) );
			}

			$view = new $classname( $this->group, $this->element );

			$this->views[$name] = $view;
		}

		return $this->views[$name];
	}

	/**
	 * Sets a variable into the theme
	 *
	 * @access public
	 * @param  string $name
	 * @param  mixed $value
	 */
	public function set( $name, $value )
	{
		return $this->theme->set( $name, $value );
	}

	/**
	 * Displays the template
	 *
	 * @access public
	 * @param  string $name
	 * @return string
	 */
	public function display( $name = 'default' )
	{
		$path 	= 'fields/' . $this->group . '/' . $this->element . '/' . $name;

		return $this->theme->output( $path );
	}

	/**
	 * Shorthand function to redirect browser
	 *
	 * @access public
	 * @param  string $uri
	 */
	public function redirect( $uri )
	{
		static $app = null;

		if( empty( $app ) )
		{
			$app = JFactory::getApplication();
		}

		$app->redirect( $uri );
		$app->close();
	}
}


// Backwards compatible fixes for PHP 5.2.x
if(function_exists('lcfirst') === false) {
    function lcfirst($str) {
        $str[0] = strtolower($str[0]);
        return $str;
    }
}
