<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: showcase.php 8463 2011-09-23 08:50:20Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
class TableShowcase extends JTable
{
	var $showcase_id = null;
	var $showcase_title = null;
	var $published = null;
	var $ordering = null;
	var $general_overall_width = null;
	var $general_overall_height = null;
	var $date_created = null;
	var $date_modified = null;

	function __construct(& $db) {
		parent::__construct('#__imageshow_showcase', 'showcase_id', $db);
	}
}
?>