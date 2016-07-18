<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/table.php');

class EasyBlogTableCategoryAcl extends EasyBlogTable
{
	/*
	 * The id of the category acl
	 * @var int
	 */
	var $id 			= null;

	/*
	 * The category id
	 * @var int
	 */
	var $category_id	= null;

	/*
	 * Category acl content id (joomla group id)
	 * @var int
	 */
	var $content_id     = null;


	/*
	 * Category acl type (group)
	 * @var string
	 */
	var $type			= null;

	/*
	 * Category status
	 * @var int
	 */
	var $status			= null;



	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_category_acl' , 'id' , $db );
	}

}
