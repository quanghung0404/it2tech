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

jimport('joomla.application.component.model');

FD::import( 'admin:/includes/model' );

class EasySocialModelNews extends EasySocialModel
{
	private $data			= null;

	function __construct()
	{
		parent::__construct( 'news' );
	}

	/**
	 * Retrieve updates from news update servers.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The name of the app.
	 * @return	Array	An array of news objects.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getNews( $app = 'easysocial' )
	{
		switch( $app )
		{
			// This is the core news item.
			case 'easysocial':
				$url 	= SOCIAL_SERVICE_NEWS;
			break;
		}

		$connector		= FD::get( 'Connector' );
		$connector->addUrl( $url );
		$connector->connect();

		// Get the json contents
		$contents		= $connector->getResult( $url );

		// Convert the json string to an object.
		$obj 			= FD::makeObject( $contents );

		return $obj;
	}
}
