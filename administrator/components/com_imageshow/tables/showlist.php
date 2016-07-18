<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: showlist.php 10238 2011-12-14 08:09:07Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');

class TableShowList extends JTable
{
	var $showlist_id 			= null;
	var $showlist_title			= null;
	var $published 				= null;
	var $ordering 				= null;
	var $access					= null;
	var $hits 					= null;
	var $description 			= null;
	var $showlist_link 			= null;
	var $alter_autid 			= null;
	var $date_create 			= null;
	var $image_source_type 		= null;
	var $image_source_profile_id= null;
	var $image_source_name 		= null;
	var $authorization_status 	= null;
	var $date_modified 			= null;
	var $override_title 		= null;
	var $override_description 	= null;
	var $override_link 			= null;
	var $image_loading_order 	= null;
	var $show_exif_data		 	= null;
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableShowList(& $db) {
		parent::__construct('#__imageshow_showlist', 'showlist_id', $db);
	}
}
?>