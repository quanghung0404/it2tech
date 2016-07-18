<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Classic
 * @version $Id: themegrid.php 14559 2012-07-28 11:50:34Z haonv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
class TableThemeGrid extends JTable
{
	var $theme_id 					= null;
	var $image_source				= 'thumbnail';
	var $show_caption				= 'yes';
	var $caption_show_description	= 'yes';
	var $show_close					= 'yes';
	var $show_thumbs				= 'yes';
	var $click_action				= 'show_original_image';
	var $open_link_in				= 'current_browser';
	var $img_layout					= 'fixed';
	var $background_color			= '#ffffff';
	var $thumbnail_width			= '50';
	var $thumbnail_height			= '50';
	var $thumbnail_space			= '10';
	var $thumbnail_border			= '3';
	var $thumbnail_rounded_corner	= '3';
	var $thumbnail_border_color		= '#ffffff';
	var $thumbnail_shadow			= '1';//0:noshadow,1:lightshadow,2:boldshadow
	var $container_transparent_background = 'no';
	var $container_height_type		= 'inherited';
	var $auto_play					= 'no';
	var $slide_timing				= '3';
	var $item_per_page				= '5';
	var $navigation_type			= 'show_all';

	function __construct(& $db) {
		parent::__construct('#__imageshow_theme_grid', 'theme_id', $db);
	}
}
?>