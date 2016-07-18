<?php
/**
 * @version     $Id$
 * @package     JSN ImageShow
 * @subpackage  ThemePile
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

class TableThemePile extends JTable
{
	var $theme_id 			    = null;
    var $image_source			= 'thumbnail';
    var $image_width			= '130';
    var $image_height			= '130';
    var $thumbnail_overlap      = 50;
    var $thumbnail_rotation     = 45;
    var $thumbnail_border_width = 2;
    var $thumbnail_border_color = '#ffffff';
    var $thumbnail_border_hover = '#ffffff';
    var $show_shadow            = 1;
    var $thumbnail_shadow_color = "#ffffff";
    var $image_click_action		= 'show_original_image';
    var $open_link_in			= 'current_browser';
    var $fade_duration          = 200;
    var $pickup_duration        = 500;
    var $show_title             = 1;
    var $title_css 	            = "font-family: Verdana;\nfont-size: 12px;\nfont-weight: bold;\ntext-align: left;\ncolor: #E9E9E9;";
    var $show_description       = 1;
    var $description_css  	    = "font-family: Arial;\nfont-size: 11px;\nfont-weight: normal;\ntext-align: left;\ncolor: #AFAFAF;";

	function __construct(& $db) {
		parent::__construct('#__imageshow_theme_pile', 'theme_id', $db);
	}
}
?>