<?php
/**
 * @version    $Id: themeclassicjavascript.php 16394 2012-09-25 08:31:07Z giangnd $
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

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

include_once JPATH_PLUGINS . DS. 'jsnimageshow'. DS . 'themeclassic' . DS . 'classes'.DS.'jsn_is_themeclassic.php';

class TableThemeClassicJavascript extends JTable
{
	var $theme_id 								= null;
	var $imgpanel_presentation_mode 			= 'fit-in';
	var $imgpanel_img_click_action_fit 			= 'image-zooming';
	var $imgpanel_img_open_link_in_fit	 		= 'new-browser';
	var $imgpanel_img_click_action_expand 		= 'open-image-link';
	var $imgpanel_img_open_link_in_expand 		= 'new-browser';
	var $imgpanel_bg_type 						= 'solid-color';
	var $imgpanel_bg_value	 					= '#595959';
	var $thumbpanel_show_panel 					= 'on';
	var $thumbpanel_panel_position 				= 'bottom';
	var $thumbpanel_active_state_color 			= '#ff6200';
	var $thumbpanel_border						= 2;
	var $thumbpanel_thumb_width					= 60;
	var $thumbpanel_thumb_height				= 50;
	var $thumbpanel_thumnail_panel_color		= '#000000';
	var $infopanel_panel_position 				= 'top';
	var $infopanel_presentation      			= 'auto';
	var $infopanel_bg_color_fill 				= '#000000';
	var $infopanel_panel_click_action			= 'no-action';
	var $infopanel_open_link_in					= 'new-browser';
	var $infopanel_show_title 					= 'yes';
	var $infopanel_title_css 					= "font-family: Verdana;\nfont-size: 12px;\nfont-weight: bold;\ntext-align: left;\ncolor: #E9E9E9;";
	var $infopanel_show_des 					= 'yes';
	var $infopanel_des_lenght_limitation 		= 50;
	var $infopanel_des_css 						= "font-family: Arial;\nfont-size: 11px;\nfont-weight: normal;\ntext-align: left;\ncolor: #AFAFAF;";
	var $infopanel_show_link 					= 'no';
	var $infopanel_link_css 					= "font-family: Verdana;\nfont-size: 11px;\nfont-weight: bold;\ntext-align: right;\ncolor: #E06614;";
	var $toolbarpanel_presentation 				= 'auto';
	var $toolbarpanel_show_counter                   = 'no';
	var $slideshow_slide_timing					= 6;
	var $slideshow_auto_play					= 'no';
	var $slideshow_looping						= 'yes';
	var $general_round_corner_radius			= 0;
	var $general_border_color					= '#cccccc';
	var $general_background_color				= '#efefef';
	var $general_border_stroke					= 1;
	function __construct(& $db) {
		parent::__construct('#__imageshow_theme_classic_javascript', 'theme_id', $db);
	}

	public function bind($src, $ignore = array())
	{
		$task = JRequest::getVar('task');
		if ($task == 'apply' || $task == 'save')
		{
			if ((int) $src['theme_id'])
			{
				$objThemeClassic = new JSNISThemeClassic;
				$recordIsExisted = $objThemeClassic->recordIsExistedInSpecifiedTabled($src['theme_id'], $src['showcase_id'], $src['theme_style_name'], 'javascript');
				if (!$recordIsExisted)
				{
					$tmpThemeID = $src['theme_id'];
					$src['theme_id'] = null;
					$currentTheme = $objThemeClassic->getSkin($tmpThemeID, $src['showcase_id']);
					$objThemeClassic->deleteThemeProfile($tmpThemeID, $src['showcase_id']);
					$objThemeClassic->deleteRecordOfSpecifiedTable($tmpThemeID, $currentTheme);
				}
			}
		}
		if (isset($src['js_infopanel_show_title']))
		{
			$src['infopanel_show_title'] = $src['js_infopanel_show_title'];
			unset($src['js_infopanel_show_title']);
		}
		if (isset($src['js_infopanel_show_des']))
		{
			$src['infopanel_show_des'] = $src['js_infopanel_show_des'];
			unset($src['js_infopanel_show_des']);
		}
		if (isset($src['js_infopanel_show_link']))
		{
			$src['infopanel_show_link'] = $src['js_infopanel_show_link'];
			unset($src['js_infopanel_show_link']);
		}
		if (isset($src['js_slideshow_auto_play']))
		{
			$src['slideshow_auto_play'] = $src['js_slideshow_auto_play'];
			unset($src['js_slideshow_auto_play']);
		}
		if (isset($src['js_slideshow_looping']))
		{
			$src['slideshow_looping'] = $src['js_slideshow_looping'];
			unset($src['js_slideshow_looping']);
		}

		// If the source value is not an array or object return false.
		if (!is_object($src) && !is_array($src))
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
			$this->setError($e);
			return false;
		}

		// If the source value is an object, get its accessible properties.
		if (is_object($src))
		{
			$src = get_object_vars($src);
		}

		// If the ignore value is a string, explode it over spaces.
		if (!is_array($ignore))
		{
			$ignore = explode(' ', $ignore);
		}

		// Bind the source value, excluding the ignored fields.
		foreach ($this->getProperties() as $k => $v)
		{
			// Only process fields not in the ignore array.
			if (!in_array($k, $ignore))
			{
				if (isset($src[$k]))
				{
					$this->$k = $src[$k];
				}
			}
		}

		return true;
	}
}