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

/**
 * Document layout for EasySocial
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialDocument
{
	/**
	 * A copy of itself for caching purposes.
	 * @var SocialDocument
	 */
	static $instance	= null;

	/**
	 * The adapter
	 * @var Object
	 */
	private $helper 	= null;

	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		$doc = JFactory::getDocument();

		// Determine the current document type.
		$type = $doc->getType();

		// Let's find for any helpers for this type.
		$file = __DIR__ . '/helpers/' . strtolower($type) . '.php';

		require_once($file);

		$docClass = 'SocialDocument' . strtoupper($type);

		$this->helper = new $docClass();
	}

	/**
	 * There should only be one copy of document running at page load.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function getInstance()
	{
		if (self::$instance === null) {
			self::$instance	= new self();
		}

		return self::$instance;
	}

	public function __call($method, $args)
	{
		return call_user_func_array(array($this->helper, $method), $args);
	}
}
