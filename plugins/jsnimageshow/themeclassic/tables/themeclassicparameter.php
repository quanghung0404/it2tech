<?php
/**
 * @version    $Id: themeclassicparameter.php 16090 2012-09-17 04:57:35Z haonv $
 * @package    JSN.ImageShow
 * @subpackage JSN.ThemeClassic
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

class TableThemeClassicParameter extends JTable
{
	var $id 								= null;
	var $general_swf_library				= 0;
	var $root_url							= 1;

	function __construct(&$db)
	{
		parent::__construct('#__imageshow_theme_classic_parameters', 'id', $db);
	}
}
?>