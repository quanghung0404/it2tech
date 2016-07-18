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

// Usage Syntax:
//
// $explorer 	= FD::explorer( $group->id , SOCIAL_TYPE_GROUP );
// $result 		= $explorer->execute();
//
class SocialExplorer
{
	/**
	 * Stores the uid of the owner
	 * @var int
	 */
	protected $uid 		= null;

	/**
	 * Stores the type of the owner
	 * @var string
	 */
	protected $type 	= null;

	/**
	 * Stores the storage for the current explorer.
	 * @var string
	 */
	protected $storage 	= null;

	protected $adapter 	= null;


	public function __construct($uid, $type)
	{
		$config = FD::config();

		$this->uid = $uid;
		$this->type = $type;

		require_once(dirname(__FILE__ ) . '/hooks/' . $this->type . '.php');

		$class 			= 'SocialExplorerHook' . ucfirst( $this->type );
		$this->adapter 	= new $class( $this->uid , $this->type );
	}

	public static function getInstance($uid, $type)
	{
		static $instance = null;

		if (!$instance) {
			$instance = new self( $uid , $type );
		}

		return $instance;
	}

	/**
	 * Processes the hook
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hook($hook)
	{
		$result = $this->adapter->$hook();

		return $result;
	}

	/**
	 * Renders the html output for explorer
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function render($url, $options=array())
	{
		$uuid  = uniqid();
		$theme = FD::themes();

		$theme->set('options', $options);
		$theme->set('uuid', $uuid);
		$theme->set('uid', $this->uid);
		$theme->set('type', $this->type);
		$theme->set('url', $url);
		$theme->set('showUse', true);

		$html = $theme->output('site/explorer/default');

		return $html;
	}
}
