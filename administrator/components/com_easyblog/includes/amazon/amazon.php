<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/libraries/s3.php');


class EasyBlogAmazon
{
	public function __construct()
	{
		// Get the config object
		$this->config 	= EB::config();

		// Get app settings
		$this->app 	 = JFactory::getApplication();
		$this->input = EB::request();

		// Assign the key and secret
		$this->key    = $this->config->get('amazon_key');
		$this->secret = $this->config->get('amazon_secret');
	}

	/**
	 * Retrieves the proper location name
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getLocationTitle($location)
	{
		$locations 	= array('us' => 'US Standard',
							'us-west-2' => 'US West Oregon',
							'us-west-1' => 'US West Northern California',
							'eu-west-1' => 'EU Ireland',
							'ap-southeast-1' => 'Asia Pacific Singapore',
							'ap-southeast-2' => 'Asia Pacific Sydney',
							'ap-northeast-1' => 'Asia Pacific Tokyo',
							'sa-east-1' => 'South America Sau Paulo'
					  );

		return $locations[$location];
	}

	/**
	 * Retrieves a list of buckets
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBuckets()
	{
		S3::setAuth($this->key, $this->secret);

		$data = S3::listBuckets();

		if (!$data) {
			return false;
		}

		$buckets = array();

		foreach ($data as $item) {
			$bucket 		= new stdClass();
			$bucket->title	= $item;

			// Get bucket location
			$location = S3::getBucketLocation($item);

			$bucket->locationTitle = $this->getLocationTitle($location);
			$bucket->location  = $location;

			$buckets[]	= $bucket;
		}

		return $buckets;
	}
}
