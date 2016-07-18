<?php
/**
 * @version    $Id: themeclassic.php 16409 2012-09-25 11:29:55Z giangnd $
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
JTable::addIncludePath(JPATH_ROOT.DS.'plugins'.DS.'jsnimageshow'.DS.'themeclassic'.DS.'tables');
class ThemeClassic
{
	var $_pluginName 	= 'themeclassic';
	var $_pluginType 	= 'jsnimageshow';

	function &getInstance()
	{
		static $themeClassic;
		if ($themeClassic == null){
			$themeClassic = new ThemeClassic();
		}
		return $themeClassic;
	}

	function __construct()
	{
		$pathModelShowcaseTheme = JPATH_PLUGINS.DS.$this->_pluginType.DS.$this->_pluginName.DS.'models';
		$pathTableShowcaseTheme = JPATH_PLUGINS.DS.$this->_pluginType.DS.$this->_pluginName.DS.'tables';
		JModelLegacy::addIncludePath($pathModelShowcaseTheme);
		JTable::addIncludePath($pathTableShowcaseTheme);
	}

	function _prepareSaveData($data)
	{
		if(!empty($data))
		{
			$imgPanelBackgroundValue = $data['imgpanel_bg_value'];
			if(count($imgPanelBackgroundValue) == 2 && $imgPanelBackgroundValue[1] != ''){
				$data['imgpanel_bg_value'] = implode(',', $imgPanelBackgroundValue);
			}else{
				$data['imgpanel_bg_value'] = $imgPanelBackgroundValue[0];
			}

			return $data;
		}
		return false;
	}

	function getTable($themeID = 0, $skin = '')
	{
		$cid				= JRequest::getVar('cid', array(0), '', 'array');
		$showcaseID			= (int) $cid[0];
		// backward compatibility with core 3.1.3
		if ($themeID == 0)
		{
			$showcaseTable 		= JTable::getInstance('showcase', 'Table');

			$showcaseTable->load($showcaseID);

			if (isset($showcaseTable->theme_id)) {
				$themeID = $showcaseTable->theme_id;
			}
		}
		if ($skin  == '')
		{
			$skin = $this->getSkin($themeID, $showcaseID);
		}

		//end backward compatibility with core 3.1.3

		$showcaseThemeTable = JTable::getInstance($this->_pluginName . $skin, 'Table');

		if(!$showcaseThemeTable->load((int) $themeID))
		{
			$showcaseThemeTable = JTable::getInstance($this->_pluginName . $skin, 'Table');// need to load default value when theme record has been deleted
			$showcaseThemeTable->load(0);
		}

		return $showcaseThemeTable;
	}

	function _prepareDataJSON($themeID, $URL, $skin = 'flash')
	{
		$showcaseThemeTable = $this->getTable($themeID, $skin);

		$row =& $showcaseThemeTable;

		$showcaseObject = new stdClass();

		//Container

		$generalObj 							= new stdClass();
		$generalObj->{'round-corner'} 			= ($row->general_round_corner_radius != '')?$row->general_round_corner_radius:'0';
		$generalObj->{'border-stroke'} 			= ($row->general_border_stroke != '')?$row->general_border_stroke:'0';
		$generalObj->{'background-color'} 		= ($row->general_background_color != '')?$row->general_background_color:'#ffffff';
		$generalObj->{'border-color'} 			= ($row->general_border_color != '')?$row->general_border_color:'#000000';
		$generalObj->{'number-images-preload'} 	= 3;
		$generalObj->{'images-order'}			= 'forward';
		$showcaseObject->{'general'}	 		= $generalObj;

		//image-panel
		$imagePanelObj 								= new stdClass();
		$imagePanelObj->{'default-presentation'}	= $row->imgpanel_presentation_mode;
		$imagePanelObj->{'background-type'} 		= $row->imgpanel_bg_type;
		$imagePanelObj->{'background-value'} 		= (strstr($row->imgpanel_bg_value, '#')== false and $row->imgpanel_bg_value!='') ? $URL.$row->imgpanel_bg_value : $row->imgpanel_bg_value;
		$imagePanelObj->{'show-watermark'} 			= $row->imgpanel_show_watermark;
		$imagePanelObj->{'watermark-path'} 			= ($row->imgpanel_watermark_path != null && $row->imgpanel_watermark_path != '') ? $URL.$row->imgpanel_watermark_path : '';
		$imagePanelObj->{'watermark-opacity'} 		= $row->imgpanel_watermark_opacity;
		$imagePanelObj->{'watermark-position'} 		= $row->imgpanel_watermark_position;
		$imagePanelObj->{'watermark-offset'} 		= $row->imgpanel_watermark_offset;
		$imagePanelObj->{'show-inner-shadow'} 		= $row->imgpanel_show_inner_shawdow;
		$imagePanelObj->{'inner-shadow-color'} 		= ($row->imgpanel_inner_shawdow_color != '') ? $row->imgpanel_inner_shawdow_color : '' ;
		$imagePanelObj->{'show-overlay'} 			= ($row->imgpanel_show_overlay_effect == 2) ? 'no' : $row->imgpanel_show_overlay_effect;
		$imagePanelObj->{'overlay-type'} 			= $row->imgpanel_overlay_effect_type;

		//fitin-settings object
		$fitinSettingObj = new stdClass();
		$fitinSettingObj->{'transition-type'} 	= $row->imgpanel_img_transition_type_fit;
		$fitinSettingObj->{'transition-timing'} = 2;
		$fitinSettingObj->{'click-action'} 		= $row->imgpanel_img_click_action_fit;
		$fitinSettingObj->{'open-link-in'} 		= $row->imgpanel_img_open_link_in_fit;
		$fitinSettingObj->{'show-image-shadow'}	= $row->imgpanel_img_show_image_shadow_fit;

		$imagePanelObj->{'fitin-settings'} 		= $fitinSettingObj;
		//end fittin-settings object

		//expandout-settings object
		$expandOutSettingObj 						= new stdClass();
		$expandOutSettingObj->{'transition-type'} 	= $row->imgpanel_img_transition_type_expand;
		$expandOutSettingObj->{'transition-timing'} = 2;
		$expandOutSettingObj->{'motion-type'} 		= ($row->imgpanel_img_motion_type_expand == 'no-motion') ? $row->imgpanel_img_motion_type_expand: $row->imgpanel_img_zooming_type_expand.'-'.$row->imgpanel_img_motion_type_expand;
		$expandOutSettingObj->{'motion-timing'} 	= 3;
		$expandOutSettingObj->{'click-action'} 		= $row->imgpanel_img_click_action_expand;
		$expandOutSettingObj->{'open-link-in'} 		= $row->imgpanel_img_open_link_in_expand;

		$imagePanelObj->{'expandout-settings'} = $expandOutSettingObj;
		//end expandout-settings object

		$showcaseObject->{'image-panel'} = $imagePanelObj;
		//end image-panel

		//thumbnail panel
		$thumbnailPanelObj 									= new stdClass();
		$thumbnailPanelObj->{'show-panel'} 					= $row->thumbpanel_show_panel;
		$thumbnailPanelObj->{'panel-position'} 				= $row->thumbpanel_panel_position;
		$thumbnailPanelObj->{'collapsible-panel'} 			= $row->thumbpanel_collapsible_position;
		$thumbnailPanelObj->{'background-color'} 			= $row->thumbpanel_thumnail_panel_color;
		$thumbnailPanelObj->{'thumbnail-row'} 				= $row->thumbpanel_thumb_row;
		$thumbnailPanelObj->{'thumbnail-width'} 			= $row->thumbpanel_thumb_width;
		$thumbnailPanelObj->{'thumbnail-height'} 			= $row->thumbpanel_thumb_height;
		$thumbnailPanelObj->{'thumbnail-opacity'} 			= $row->thumbpanel_thumb_opacity;
		$thumbnailPanelObj->{'active-state-color'} 			= $row->thumbpanel_active_state_color;
		$thumbnailPanelObj->{'normal-state-color'} 			= $row->thumbpanel_thumnail_normal_state;
		$thumbnailPanelObj->{'thumbnails-browsing-mode'} 	= $row->thumbpanel_thumb_browsing_mode;
		$thumbnailPanelObj->{'thumbnails-presentation-mode'} = $row->thumbpanel_presentation_mode;
		$thumbnailPanelObj->{'thumbnail-border'} 			= $row->thumbpanel_border;
		$thumbnailPanelObj->{'show-thumbnails-status'} 		= $row->thumbpanel_show_thumb_status;
		$thumbnailPanelObj->{'enable-big-thumbnail'} 		= $row->thumbpanel_enable_big_thumb;
		$thumbnailPanelObj->{'big-thumbnail-size'} 			= $row->thumbpanel_big_thumb_size;
		$thumbnailPanelObj->{'big-thumbnail-color'} 		= $row->thumbpanel_big_thumb_color;
		$thumbnailPanelObj->{'big-thumbnail-border'} 		= $row->thumbpanel_thumb_border;

		$showcaseObject->{'thumbnail-panel'} 				= $thumbnailPanelObj;
		//end thumbnail panel

		//information-panel
		$informationPanelObj 							= new stdClass();
		$informationPanelObj->{'panel-presentation'} 	= $row->infopanel_presentation;
		$informationPanelObj->{'panel-position'} 		= $row->infopanel_panel_position;
		$informationPanelObj->{'background-color-fill'} = $row->infopanel_bg_color_fill;
		$informationPanelObj->{'show-title'} 			= $row->infopanel_show_title;
		$informationPanelObj->{'click-action'} 			= $row->infopanel_panel_click_action;
		$informationPanelObj->{'open-link-in'} 			= $row->infopanel_open_link_in;
		$informationPanelObj->{'title-css'} 			= ($row->infopanel_title_css!='')?$row->infopanel_title_css:'';
		$informationPanelObj->{'show-description'} 		= $row->infopanel_show_des;
		$informationPanelObj->{'description-length-limitation'} = $row->infopanel_des_lenght_limitation;
		$informationPanelObj->{'description-css'} 				= ($row->infopanel_des_css!='')?$row->infopanel_des_css:'';
		$informationPanelObj->{'show-link'}						= $row->infopanel_show_link;
		$informationPanelObj->{'link-css'} 						= ($row->infopanel_link_css!='')?$row->infopanel_link_css:'';

		$showcaseObject->{'information-panel'} = $informationPanelObj;
		//end information-panel

		//toobar-panel
		$toolbarPanelObj = new stdClass();
		$toolbarPanelObj->{'panel-position'} 		= $row->toolbarpanel_panel_position;
		$toolbarPanelObj->{'panel-presentation'} 	= $row->toolbarpanel_presentation;
		$toolbarPanelObj->{'show-image-navigation'} 	= $row->toolbarpanel_show_image_navigation;
		$toolbarPanelObj->{'show-slideshow-player'} 	= $row->toolbarpanel_slideshow_player;
		$toolbarPanelObj->{'show-fullscreen-switcher'} 	= $row->toolbarpanel_show_fullscreen_switcher;
		$toolbarPanelObj->{'show-tooltip'} 				= $row->toolbarpanel_show_tooltip;

		$showcaseObject->{'toolbar-panel'} 				= $toolbarPanelObj;
		// end toobar-panel

		//slideshow panel
		$slidePanelObj = new stdClass();
		$slidePanelObj->{'image-presentation'} 		= ($row->slideshow_enable_ken_burn_effect == 'yes') ? 'expand-out' : $row->imgpanel_presentation_mode;
		$slidePanelObj->{'show-thumbnail-panel'} 	= ($row->slideshow_hide_thumb_panel == 'yes') ? 'off' : $row->thumbpanel_show_panel;
		$slidePanelObj->{'show-image-navigation'} 	= ($row->slideshow_hide_image_navigation == 'yes') ? 'no' : $row->toolbarpanel_show_image_navigation;
		$slidePanelObj->{'show-watermark'} 			= $row->imgpanel_show_watermark;
		$slidePanelObj->{'show-status'} 			= $row->slideshow_show_status;
		$slidePanelObj->{'show-overlay'} 			= ($row->imgpanel_show_overlay_effect == 'during') ? 'yes' : $row->imgpanel_show_overlay_effect;
		$slidePanelObj->{'slide-timing'} 		= $row->slideshow_slide_timing;
		$slidePanelObj->{'auto-play'} 			= $row->slideshow_auto_play;
		$slidePanelObj->{'slideshow-looping'} 	= $row->slideshow_looping;
		$slidePanelObj->{'enable-kenburn'} 		= $row->slideshow_enable_ken_burn_effect;

		$showcaseObject->{'slideshow'} = $slidePanelObj;
		//end slideshow panel

		return $showcaseObject;
	}

	function getData($id, $tableSuffix)
	{
		$db    = JFactory::getDbo();
		$query = 'SELECT * FROM #__imageshow_theme_classic_'. $tableSuffix .' WHERE theme_id = '.(int) $id;
		$db->setQuery($query);
		$data = $db->loadObject();
		return $data;
	}

	function getParameters()
	{
		$db    	= JFactory::getDbo();
		$query 	= 'SELECT * FROM #__imageshow_theme_classic_parameters';
		$db->setQuery($query);
		$data 	= $db->loadObject();
		if (!count($data))
		{
			$param	 					= new stdClass();
			$param->id 					= 0;
			$param->general_swf_library	= 0;
			$param->root_url			= 1;
			return $param;
		}
		return $data;
	}

	function getSkin($themeID, $showcaseID)
	{
		$db    	= JFactory::getDbo();
		$query 	= 'SELECT * FROM #__imageshow_theme_profile WHERE showcase_id = ' . (int) $showcaseID . ' AND theme_id = ' . (int) $themeID;
		$db->setQuery($query);
		$data 	= $db->loadObject();

		if (!count($data))
		{
			return 'javascript';
		}
		else
		{
			if ($data->theme_style_name == '')
			{
				return 'flash';
			}
			else
			{
				return $data->theme_style_name;
			}
		}
	}
}