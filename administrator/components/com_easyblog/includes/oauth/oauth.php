<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogOauth extends EasyBlog
{
	/**
	 * Determines if a respective oauth client has been setup in the system
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string		The client type
	 * @return	boolean
	 */
	public function associated($client)
	{
		$model = EB::model('OAuth');

		$state = $model->isAssociated($client);

		return $state;
	}

	/**
	 * Determines if the provided user is associated with the respective oauth client
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function isUserAssociated($client, $userId = null)
	{
		$config = EB::config();
		$allowed = $config->get('integrations_' . strtolower($client) . '_centralized_and_own');

		if (!$allowed) {
			return false;
		}

		$allowed = $config->get('integrations_' . strtolower($client));

		if (!$allowed) {
			return false;
		}

		$oauth = EB::table('OAuth');
		$exists = $oauth->loadByUser($userId, constant('EBLOG_OAUTH_' . strtoupper($client)));

		return $exists;
	}


	/**
	 * Retrieve the Consumer API
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getClient($type)
	{
		static $adapters = array();

		if (!isset($loaded[$type])) {

			$type = strtolower($type);
			$file = dirname(__FILE__) . '/adapters/' . $type . '/client.php';

			require_once($file);

			$class = 'EasyBlogClient' . ucfirst($type);
			$obj = new $class();

			$adapters[$type] = $obj;
		}

		return $adapters[$type];
	}

	/**
	 * Try to get the consumer type based on the given type.
	 *
	 * @param	string	$type	The client app type.
	 * @param	string	$api	The API key required for most oauth clients
	 * @param	string	$secret	The API secret required for oauth to work
	 * @param	string	$callback	The callback URL.
	 *
	 * @return	oauth objects.
	 **/
	public function getConsumer($type , $api , $secret , $callback)
	{
		static $loaded	= array();

		if( !isset( $loaded[ $type ] ) )
		{
			$file	= EBLOG_CLASSES . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . 'helper.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );
				$class	= 'EasyBlog' . ucfirst( $type );

				if( class_exists( ucfirst( $class ) ) )
				{
					$loaded[ $type ]	= new $class( $api , $secret , $callback );
				}
				else
				{
					$loaded[ $type ]	= false;
				}
			}
			else
			{
				$loaded[ $type ]	= false;
			}

		}
		return $loaded[ $type ];
	}
}


class EasyBlogOAuthConsumer
{
	public function __construct()
	{
		$this->config 	= EB::config();
		$this->app 		= JFactory::getApplication();
		$this->input 	= $this->app->input;
	}
}