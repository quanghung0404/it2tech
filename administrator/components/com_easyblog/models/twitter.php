<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelTwitter extends EasyBlogAdminModel
{
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	function markAsSent($blogId)
	{
		$posts = EB::table('TwitterPosts');
    	$posts->load($blogId);
    	
    	$date 	= new JDate();
    	$now	= $date->toMySQL();
    	
    	if(empty($posts->id))
    	{
    		$posts->created = $now;
    	}
    	
    	$posts->modified = $now;
    	
    	if($posts->store())
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
	}
	
	function checkIfSent($blogId)
	{
		$posts = EB::table('TwitterPosts');
    	$posts->load($blogId);
    	
    	if(empty($posts->id))
    	{
    		return false;
    	}
    	
    	return true;
	}
}
