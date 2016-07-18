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

// This is required if anyone needs access to the engine.
require_once(JPATH_ADMINISTRATOR . '/components/com_easysocial/constants.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_easysocial/tables/table.php');
require_once(SOCIAL_LIB . '/exception/exception.php');
require_once(SOCIAL_LIB . '/router.php');

jimport('joomla.filesystem.file');

if (!function_exists('dump')) {
	function dump()
	{
		$args = func_get_args();

		echo '<pre>';

		foreach ($args as $arg) {
			var_dump($arg);
		}
		echo '</pre>';
		exit;
	}
}

// @Copyright message
define('SOCIAL_SCRIPT_CODE', '<div class="center mt-20"><a href="http://stackideas.com/easysocial">Joomla Social Network</a> powered by EasySocial</div>');

/**
 * Base helper class to provide additional helpers to subclasses.
 *
 */
class EasySocial
{
	public $config = null;
	public $jConfig = null;
	public $app = null;
	public $input = null;
	public $my = null;
	public $doc = null;
	public $access = null;

	protected $error = null;

	public function __construct()
	{
		$this->doc = JFactory::getDocument();
		$this->config = ES::config();
		$this->jConfig = ES::jConfig();
		$this->app = JFactory::getApplication();
		$this->input = ES::request();
		$this->my = ES::user();
		$this->access = ES::access();
	}

	public function setError($message)
	{
		$this->error = $message;
	}

	public function getError()
	{
		if (!$this->error) {
			return false;
		}

		return JText::_($this->error);
	}
}

/**
 * Reusable classes
 */
class SocialObject
{
	/**
	 * Given an array of items, map it against the object properties.
	 *
	 * @access	public
	 * @param 	Array	A list of items in an associative array.
	 * @return 	null
	 */
	public function map( $items )
	{
		// @task: Process arrays
		if( is_array( $items ) )
		{
			foreach( $items as $itemKey => $itemValue )
			{
				if( isset( $this->$itemKey ) )
				{
					$this->$itemKey	= $itemValue;
				}
			}
		}

		// @task: If this is a stdclass object.
		if( is_object( $items ) )
		{
			$properties 	= get_object_vars( $items );

			foreach( $properties as $property )
			{
				if( isset( $this->$property ) )
				{
					$this->$property 	= $items->$property;
				}
			}
		}
	}

	/**
	 * Returns a property value from the object.
	 *
	 * @access	public
	 * @param	string 	$key		The key property.
	 * @param	string 	$default 	The default value if the property is empty.
	 */
	public function get( $key , $default = '' )
	{
		if( !isset( $this->$key ) || empty( $this->$key ) || is_null( $this->$key ) )
		{
			return $default;
		}

		return $this->$key;
	}
}
