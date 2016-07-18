<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: log.php 6505 2011-05-31 11:01:31Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');

class TableLog extends JTable
{

	var $log_id 		= null;
	var $user_id 		= null;
	var $url 			= null;
	var $result 		= null;
	var $screen 		= null;
	var $action 		= null;
	var $time_created 	= null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db) {
		parent::__construct('#__imageshow_log', 'log_id', $db);
	}

}

?>