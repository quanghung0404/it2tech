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

class SocialBBCode
{
	private $adapter    = null;

	public function __construct()
	{
		// For now, we'll hardcode it to use decoda.

		require_once(__DIR__ . '/adapters/decoda/decoda.php');

		$this->adapter = new BBCodeDecodaAdapter();
	}

	/**
	 * This class uses the factory pattern.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string			The image driver to use.
	 * @return	SocialImage		Returns itself for chaining.
	 */
	public static function factory()
	{
		$decoda = new self();

		return $decoda;
	}

	public static function getSmileys()
	{
			
	}

	/**
	 * Processes a string with decoda library.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function parse( $string , $options = array() )
	{
		if( !isset( $options[ 'escape' ] ) )
		{
			$options[ 'escape' ]	= false;
		}

		return $this->adapter->parse( $string , $options );
	}

	public function parseRaw( $string , $filters = array() )
	{
		return $this->adapter->parseRaw( $string , $filters );
	}

	/**
	 * Displays the markitup html
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function editor( $nameAttribute , $value = '' , $config = array() , $attributes = array() )
	{
		$theme = ES::themes();
		$uniqueId = uniqid(rand());
		$attr = '';

		if (!empty($attributes)) {

			foreach ($attributes as $key => $val) {
				$attr .= ' ' . $key . '="' . $val . '"';
			}
		}

		// Determines if we should display the file browser
		$files = isset($config['files']) && $config['files'] ? true : false;

		// Determine the correct uid and type
		$uid  = isset($config['uid']) ? $config['uid'] : FD::user()->id;
		$type = isset($config['type']) ? $config['type'] : SOCIAL_TYPE_USER;

		if (isset($config['controllerName'])) {
			$theme->set('controllerName', $config['controllerName']);
		}

		$theme->set('uid', $uid);
		$theme->set('type', $type);
		$theme->set('files', $files);
		$theme->set('value', $value);
		$theme->set('attr', $attr);
		$theme->set('nameAttribute', $nameAttribute);
		$theme->set('uniqueId', $uniqueId);

		$output 	= $theme->output('site/bbcode/editor');

		return $output;
	}
}
