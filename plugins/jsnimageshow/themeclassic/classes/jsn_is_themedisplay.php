<?php
/**
 * @version    $Id: jsn_is_themedisplay.php 16090 2012-09-17 04:57:35Z haonv $
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
jimport('joomla.application.component.model');

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

class JSNISThemeDisplay extends JObject
{
	var $_themename 	= 'themeclassic';
	var $_themetype 	= 'jsnimageshow';
	var $_assetsPath 	= 'plugins/jsnimageshow/themeclassic/assets/';
	function __construct() {}

	public function flashLayout($args, $skin)
	{
		$filterLangSys	= $this->getFilterLangSystem();
		JHTML::script($this->_assetsPath.'js/' . 'swfobject.js');
		$path = JPATH_PLUGINS.DS.$this->_themetype.DS.$this->_themename.DS.'models';
		JModelLegacy::addIncludePath($path, $skin);
		$model 				= JModelLegacy::getInstance($this->_themename);
		$skin				= $model->getSkin($args->theme_id, $args->showcase_id);
		$themeData  		= $model->getData($args->theme_id, $skin);
		$backgroundColor	= ($themeData->general_background_color != '')?$themeData->general_background_color:'#ffffff';
		$html  = '<div class="jsn-'.$this->_themename.'-gallery">'."\n";
		// fix error: click back browser, no event onclick of flash
		$html  .= '<script type="text/javascript"> window.onbeforeunload = function() {}; </script>'."\n";
		if ($args->swf)
		{
			$showcaseURL = '';
			$showlistURL = '';
			if(!$args->showlist_id && !$args->showcase_id)
			{
				$showcaseURL = $args->uri . $filterLangSys . 'option=com_imageshow%26view=show%26Itemid='.$args->item_id.'%26format=showcase';
				$showlistURL = $args->uri . $filterLangSys . 'option=com_imageshow%26view=show%26Itemid='.$args->item_id.'%26format=showlist';
			}
			else
			{
				$showcaseURL = $args->uri . $filterLangSys . 'option=com_imageshow%26view=show%26showcase_id='.$args->showcase_id.'%26format=showcase';
				$showlistURL = $args->uri . $filterLangSys . 'option=com_imageshow%26view=show%26showlist_id='.$args->showlist_id.'%26format=showlist';
			}
			$html .= '<script type="text/javascript">'."\n";
			$html .= 'swfobject.embedSWF('."\n";
			$html .= '"'.$args->url.'Gallery.swf'.'",'."\n";
			$html .= '"jsn-imageshow-'.$args->random_number.'",'."\n";
			$html .= '"'.$args->width.'",'."\n";
			$html .= '"'.$args->height.'",'."\n";
			$html .= '"9.0.45",'."\n";
			$html .= '"'.$args->url.'assets/js/expressInstall.swf",'."\n";
			$html .= '{'."\n";
			$html .= 'baseurl:"'.$args->url.'",'."\n";
			$html .= 'showcase:"'.$showcaseURL.'",'."\n";
			$html .= 'showlist:"'.$showlistURL.'",'."\n";
			$html .= 'language:"'.$args->language.'",'."\n";
			$html .= 'edition:"'.$args->edition.'"'."\n";
			$html .= '},'."\n";
			$html .= '{'."\n";
			$html .= 'wmode:"opaque",'."\n";
			$html .= 'bgcolor:"'.$backgroundColor.'",'."\n";
			$html .= 'menu:"false",'."\n";
			$html .= 'allowFullScreen:"true"'."\n";
			$html .= '});'."\n";
			$html .= '</script>'."\n";
			$html .= '<div id="jsn-imageshow-'.$args->random_number.'">'.$this->displayAlternativeContent().'</div>'."\n";
		}
		else
		{
			$strParameter 	= '';
			$strEmbed 		= '<embed src="'.$args->url.'Gallery.swf" menu="false" bgcolor="'.$backgroundColor.'" width="'.$args->width.'" height="'.$args->height.'" name="jsn-imageshow-'.$args->random_number.'" align="middle" allowScriptAccess="sameDomain" allowFullScreen="true" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" wmode="opaque" ';
			if(!$args->showlist_id && !$args->showcase_id)
			{
				$strParameter  = '<param name="flashvars" value="baseurl='.$args->url.'&amp;showcase='.$args->uri . $filterLangSys . 'option=com_imageshow%26view=show%26Itemid='.$args->item_id.'%26format=showcase&amp;showlist='.$args->uri . $filterLangSys . 'option=com_imageshow%26view=show%26Itemid='.$args->item_id.'%26format=showlist&amp;language='.$args->language.'&amp;edition='.$args->edition.'"/>'."\n";
				$strEmbed	  .= 'flashvars="baseurl='.$args->url.'&amp;showcase='.$args->uri . $filterLangSys . 'option=com_imageshow%26view=show%26Itemid='.$args->item_id.'%26format=showcase&amp;showlist='.$args->uri . $filterLangSys . 'option=com_imageshow%26view=show%26Itemid='.$args->item_id.'%26format=showlist&amp;language='.$args->language.'&amp;edition='.$args->edition.'"/>'."\n";
			}
			else
			{
				$strParameter  = '<param name="flashvars" value="baseurl='.$args->url.'&amp;showcase='.$args->uri . $filterLangSys . 'option=com_imageshow%26view=show%26showcase_id='.$args->showcase_id.'%26format=showcase&amp;showlist='.$args->uri . $filterLangSys . 'option=com_imageshow%26view=show%26showlist_id='.$args->showlist_id.'%26format=showlist&amp;language='.$args->language.'&amp;edition='.$args->edition.'"/>'."\n";
				$strEmbed	  .= 'flashvars="baseurl='.$args->url.'&amp;showcase='.$args->uri . $filterLangSys . 'option=com_imageshow%26view=show%26showcase_id='.$args->showcase_id.'%26format=showcase&amp;showlist='.$args->uri . $filterLangSys . 'option=com_imageshow%26view=show%26showlist_id='.$args->showlist_id.'%26format=showlist&amp;language='.$args->language.'&amp;edition='.$args->edition.'"/>'."\n";
			}
			$html  .= '<object height="'.$args->height.'" class="jsn-flash-object" width="'.$args->width.'" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" id="jsn-imageshow-'.$args->random_number.'" align="middle">'."\n";
			$html  .= '<param name="bgcolor" value="'.$backgroundColor.'"/>'."\n";
			$html  .= '<param name="wmode" value="opaque"/>'."\n";
			$html  .= '<param name="menu" value="false"/>'."\n";
			$html  .= '<param name="allowFullScreen" value="true"/>'."\n";
			$html  .= '<param name="allowScriptAccess" value="sameDomain" />'."\n";
			$html  .= '<param name="movie" value="'.$args->url.'Gallery.swf"/>'."\n";
			$html  .= $strParameter;
			$html  .= $strEmbed;
			$html  .= '</object>'."\n";
		}
		$html .= '</div>'."\n";
		return $html;
	}

	public function displayAlternativeContent()
	{
		$html    = '<div class="jsn-'.$this->_themename.'-msgnonflash">'."\n";
		$html   .= '<p>'.JText::_('SITE_SHOW_YOU_NEED_FLASH_PLAYER').'</p>'."\n";
		$html   .= '<p>'."\n";
		$html   .= '<a href="http://www.adobe.com/go/getflashplayer">'."\n";
		$html   .= JText::_('SITE_SHOW_GET_FLASH_PLAYER')."\n";
		$html   .='</a>'."\n";
		$html   .='</p>'."\n";
		$html   .='</div>'."\n";
		return $html;
	}

	public function displaySEOContent($args)
	{
		$html    = '<div class="jsn-'.$this->_themename.'-seocontent">'."\n";

		if (count($args->images))
		{
			$html .= '<div>';
			$html .= '<p>'.@$args->showlist['showlist_title'].'</p>';
			$html .= '<p>'.@$args->showlist['description'].'</p>';
			$html .= '<ul>';

			for ($i = 0, $n = count($args->images); $i < $n; $i++)
			{
				$row 	=& $args->images[$i];
				$html  .= '<li>';
				if ($row->image_title != '')
				{
					$html .= '<p>'.$row->image_title.'</p>';
				}
				if ($row->image_description != '')
				{
					$html .= '<p>'.$row->image_description.'</p>';
				}
				if ($row->image_link != '')
				{
					$html .= '<p><a href="'.htmlspecialchars($row->image_link).'">'.htmlspecialchars($row->image_link).'</a></p>';
				}
				$html .= '</li>';
			}
			$html .= '</ul></div>';
		}
		$html   .='</div>'."\n";
		return $html;
	}

	public function display($args)
	{
		$objUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
		$device     = $objUtils->checkSupportedFlashPlayer();
		$string		= '';
		$path 		= JPATH_PLUGINS.DS.$this->_themetype.DS.$this->_themename.DS.'models';
		JModelLegacy::addIncludePath($path);
		$model 		= JModelLegacy::getInstance($this->_themename);
		$skin		= $model->getSkin($args->theme_id, $args->showcase_id);

		$parameters	= $model->getParameters();
		$args->swf	= $parameters->general_swf_library;
		$args->uri	= $this->_overwriteURL($parameters);
		$args->url	= $args->uri.'plugins/'.$this->_themetype.'/'.$this->_themename.'/assets/swf/';

		$functionName = $skin . 'Layout';

		if ($skin == 'javascript')
		{
			$string .= $this->{$functionName}($args, $skin);
		}
		else
		{
			if ($device == 'iphone' || $device == 'ipad' || $device == 'ipod' || $device == 'android' || $device == 'windows')
			{
				$string .= $this->javascriptLayout($args, 'flash');
			}
			else
			{
				$string .= $this->{$functionName}($args, $skin);
			}
		}
		$string .= $this->displaySEOContent($args);
		return $string;
	}

	public function _convertToBool($str)
	{
		$str = (string) $str;

		if ($str != '')
		{
			return ((strcasecmp($str, 'yes') == 0) || (strcasecmp($str, 'on') == 0) || ($str == '1') || (strcasecmp($str, 'auto') == 0));
		}
		return false;
	}

	public function _convertFromBoolToString($bool)
	{
		$bool = (boolean) $bool;
		if ($bool)
		{
			return 'true';
		}
		return 'false';
	}

	public function _hex2rgb($hexVal = "")
	{
		if (0 === strpos($hexVal, '#'))
		{
			$hexVal = substr($hexVal, 1);
		}
		$hexVal = preg_replace("[^a-fA-F0-9]", "", $hexVal);
		if (strlen($hexVal) != 6) {return array();}
		$arrTmp = explode(" ", chunk_split($hexVal, 2, " "));
		$arrTmp = array_map("hexdec", $arrTmp);
		return array("red" => $arrTmp[0], "green" => $arrTmp[1], "blue" => $arrTmp[2]);
	}

	public function _wordLimiter($str, $limit = 100, $endChar = '&#8230;')
	{
		if (trim($str) == '')
		{
		    return $str;
		}
		$append = '';
		$str 	= strip_tags(trim($str), '<b><i><s><strong><em><strike><u><br>');
		$words 	= explode(" ", $str);
		if(count($words) > $limit)
		{
			$append = $endChar;
		}

		return implode(" ", array_splice($words, 0, $limit)) . $append;
	}

	public function _overwriteURL($parameters)
	{
		if (!is_null($parameters) && $parameters->root_url == 2)
		{
			return JURI::base();
		}
		else
		{
			$pathURL 			= array();
			$uri				= JURI::getInstance();
			$pathURL['prefix'] 	= $uri->toString( array('scheme', 'host', 'port'));

			if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
			{
				$pathURL['path'] =  rtrim(dirname(str_replace(array('"', '<', '>', "'"), '', $_SERVER["PHP_SELF"])), '/\\');
			}
			else
			{
				$pathURL['path'] =  rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
			}
			return $pathURL['prefix'].$pathURL['path'].'/';
		}
	}

	public function javascriptLayout($args, $skin)
	{
		$userAgent			= $_SERVER['HTTP_USER_AGENT'];
		$objJSNShowlist		= JSNISFactory::getObj('classes.jsn_is_showlist');
		$showlistInfo 		= $objJSNShowlist->getShowListByID($args->showlist['showlist_id'], true);
		$dataObj 			= $objJSNShowlist->getShowlist2JSON($args->uri, $args->showlist['showlist_id']);
		$images				= $dataObj->showlist->images->image;
		$plugin				= false;
		if (!count($images)) return '';

		if (isset($args->plugin) && $args->plugin == true)
		{
			$plugin = true;
		}

		switch ($showlistInfo['image_loading_order'])
		{
			case 'backward':
				krsort($images);
				$tmpImageArray = $images;
				$images = array_values($images);
				break;
			case 'random':
				shuffle($images);
				break;
			case 'forward':
			default:
				ksort($images);
				break;
		}
		$this->loadjQuery();
		JHTML::script($this->_assetsPath.'js/' . 'jsn_is_conflict.js');
		JHTML::script($this->_assetsPath.'js/galleria/' . 'galleria-1.2.8.js');
		JHTML::script($this->_assetsPath.'js/galleria/themes/classic/' . 'galleria.classic.js');
		JHTML::stylesheet($this->_assetsPath.'js/galleria/themes/classic/' . 'galleria.classic.css');

		$path = JPATH_PLUGINS.DS.$this->_themetype.DS.$this->_themename.DS.$this->_themename.DS.'models';
		JModelLegacy::addIncludePath($path);
		$model 		= JModelLegacy::getInstance($this->_themename);
		$themeData  = $model->getData($args->theme_id, $skin);

		$jsImagePanelDefaultPresentationMode 	= '';

		$pluginOpenTagDiv 						= '';
		$pluginCloseTagDiv 						= '';

		$jsWidth 						 		= '';
		$jsAutoPlay 					 		= '';
		$jsShowThumbnail 						= '';
		$jsToolBarpanelPresentation		 		= '';
		$jsToolBarpanelPresentationValue        = '';
		$jsToolBarpanelShowCounter              = '';
		$jsPauseOnInteraction			 		= '';
		$jsSlideshowLooping				 		= '';

		$jsInformationPanelPresentation  		= '';
		$jsInformationPanelShowTitle			= '';
		$jsInformationPanelShowDescription		= '';
		$jsInformationPanelImageShowLink 		= '';
		$jsInformationpanelPopupLinks			= '';
		$jsInformationPanelClickAction			= '';

		$jsThumbnailHeight 						= '';
		$jsThumbnailPosition					= '';

		$jsPopupLinks					 		= '';
		$jsImagePanelImageClickAction	 		= '';
		$css							 		= '';
		$cssInformationPanelPosition			= '';
		$cssThumbnailPanelPosition				= '';

		$cssGeneralRoundCornerRadius			= ($themeData->general_round_corner_radius != '')?$themeData->general_round_corner_radius:'0';
		$cssGeneralBorderStroke					= ($themeData->general_border_stroke != '')?$themeData->general_border_stroke:'2';
		$cssGeneralBorderColor					= ($themeData->general_border_color != '')?$themeData->general_border_color:'#000000';
		$cssGeneralBackgroundColor				= ($themeData->general_background_color != '')?$themeData->general_background_color:'#ffffff';

		$cssToolBarpanelPresentation			= '';

		$imagePanelBackground					= '';
		$doc									= JFactory::getDocument();

		// Parameters of Theme
		$percent  						= strpos($args->width, '%');
		$autoPlay		 				= $this->_convertToBool($themeData->slideshow_auto_play);
		$slideshowLooping		 		= $this->_convertToBool($themeData->slideshow_looping);

		$showThumbnail 							= $this->_convertToBool($themeData->thumbpanel_show_panel);
		$toolBarpanelPresentation 				= $this->_convertToBool($themeData->toolbarpanel_presentation);
		$normalStateColor		 				= $this->_hex2rgb($themeData->thumbpanel_thumnail_normal_state);
		$activeStateColor		 				= $themeData->thumbpanel_active_state_color;
		$panelThumbnailPanelBackgroundColor		= $themeData->thumbpanel_thumnail_panel_color;
		$panelInfoPanelBackgroundColor			= $themeData->infopanel_bg_color_fill;
		$thumbnailWidth		 		    		= (int) $themeData->thumbpanel_thumb_width;
		$thumbnailHeight	 		   	 		= (int) $themeData->thumbpanel_thumb_height;
		$thumbnailBorder	 		    		= (int) $themeData->thumbpanel_border;
		$thumbnailPanelPosition					= trim($themeData->thumbpanel_panel_position);

		$panelInfoPanelBackgroundColor			= $this->_hex2rgb($panelInfoPanelBackgroundColor);
		$informationPanelPresentation			= $this->_convertToBool($themeData->infopanel_presentation);
		$informationPanelShowTitle				= $this->_convertToBool($themeData->infopanel_show_title);
		$informationPanelShowDescription		= $this->_convertToBool($themeData->infopanel_show_des);
		$informationPanelTitleCSS				= trim($themeData->infopanel_title_css);
		$informationPanelDescriptionCSS			= trim($themeData->infopanel_des_css);
		$informationPanelDescriptionLenghtLimit	= (int) trim($themeData->infopanel_des_lenght_limitation);
		$informationPanelImageShowLink			= $this->_convertToBool($themeData->infopanel_show_link);
		$informationPanelLinkCSS				= trim($themeData->infopanel_link_css);
		$informationPanelPosition				= trim($themeData->infopanel_panel_position);
		$informationPanelClickAction			= trim($themeData->infopanel_panel_click_action);
		$informationPanelOpenLink				= trim($themeData->infopanel_open_link_in);

		$imagePanelBackgroundType				= trim($themeData->imgpanel_bg_type);
		$imagePanelBackgroundValue				= trim($themeData->imgpanel_bg_value);
		$imagePanelDefaultPresentationMode		= trim($themeData->imgpanel_presentation_mode);
		if ($thumbnailPanelPosition == 'bottom')
		{
			if($informationPanelPosition == 'top')
			{
				if($showThumbnail)
				{
					$cssInformationPanelPosition = 'top: 0;';
				}
				else
				{
					$cssInformationPanelPosition = 'top: 0;';
				}

			}
			elseif ($informationPanelPosition == 'bottom')
			{
				if($showThumbnail)
				{
					$cssInformationPanelPosition = 'bottom:'. ($thumbnailHeight + 15).'px;';
				}
				else
				{
					$cssInformationPanelPosition = 'bottom:0;';
				}
			}
			$cssThumbnailPanelPosition = 'bottom: 0;';
		}
		elseif ($thumbnailPanelPosition == 'top')
		{
			$cssThumbnailPanelPosition 	= 'top: 0;';
			if($informationPanelPosition == 'top')
			{
				if($showThumbnail)
				{
					$cssInformationPanelPosition = 'top: '. ($thumbnailHeight + 15).'px;';
				}
				else
				{
					$cssInformationPanelPosition = 'top: 0;';
				}

			}
			elseif ($informationPanelPosition == 'bottom')
			{
				if($showThumbnail)
				{
					$cssInformationPanelPosition = 'bottom: 0;';
				}
				else
				{
					$cssInformationPanelPosition = 'bottom:0;';
				}
			}
		}

		switch ($imagePanelDefaultPresentationMode)
		{
			case 'fit-in':
				$imagePanelImageClickAction	= trim($themeData->imgpanel_img_click_action_fit);
				$imagePanelOpenLinkIn		= trim($themeData->imgpanel_img_open_link_in_fit);
				$jsImagePanelDefaultPresentationMode = 'imageCrop: false,';
				break;
			case 'expand-out':
				$imagePanelImageClickAction	= trim($themeData->imgpanel_img_click_action_expand);
				$imagePanelOpenLinkIn		= trim($themeData->imgpanel_img_open_link_in_expand);
				$jsImagePanelDefaultPresentationMode = 'imageCrop: true,';
				break;
		}

		if ($imagePanelImageClickAction == 'open-image-link')
		{
			if ($imagePanelOpenLinkIn == 'current-browser')
			{
				$jsPopupLinks = 'popupLinks:false,';
			}
			elseif ($imagePanelOpenLinkIn == 'new-browser')
			{
				$jsPopupLinks = 'popupLinks:true,';
			}
			$jsImagePanelImageClickAction = 'imageClickAction:true,';
		}
		else
		{
			$jsImagePanelImageClickAction = 'imageClickAction:false,';
		}

		if ($informationPanelClickAction == 'open-image-link')
		{
			if ($informationPanelOpenLink == 'current-browser')
			{
				$jsInformationpanelPopupLinks = 'infoPanelPopupLinks:false,';
			}
			elseif ($informationPanelOpenLink == 'new-browser')
			{
				$jsInformationpanelPopupLinks = 'infoPanelPopupLinks:true,';
			}
			$jsInformationPanelClickAction = 'informationPanelClickAction:true,';
		}
		else
		{
			$jsInformationPanelClickAction = 'informationPanelClickAction:false,';
		}
		switch ($imagePanelBackgroundType)
		{
			case 'solid-color':
				$imagePanelBackground = 'background: '.$imagePanelBackgroundValue.';';
				break;
			case 'linear-gradient':
			case 'radial-gradient':
				$tmpImagePanelBackgroundValue 	= @explode(',', $imagePanelBackgroundValue);
				$imagePanelBackground 			= 'background: '.@$tmpImagePanelBackgroundValue[0].';';
				break;
			case 'pattern':
				$tmpImagePanelBackgroundValue 	= $args->uri.$imagePanelBackgroundValue;
				$imagePanelBackground 			= 'background: url("'.preg_replace('/\s/', '%20', $tmpImagePanelBackgroundValue).'") repeat left center;';
				break;
			case 'image':
				$tmpImagePanelBackgroundValue 	= $args->uri.$imagePanelBackgroundValue;
				$imagePanelBackground 			= 'background: url("'.preg_replace('/\s/', '%20', $tmpImagePanelBackgroundValue).'");';
				$imagePanelBackground 			.= 'background-position: center center;'."\n";
				$imagePanelBackground 			.= 'background-repeat: no-repeat;'."\n";
				$imagePanelBackground 			.= 'background-size: cover;'."\n";
				break;
		}
		// Parameters of JS Gallery
		$jsToolBarpanelPresentationValue    = $themeData->toolbarpanel_presentation == 'on' ? 'true' : 'false';
		$jsToolBarpanelShowCounter          = $themeData->toolbarpanel_show_counter == 'yes' ? 'true' : 'false';
		$jsShowThumbnail 					= 'thumbnails: '.$this->_convertFromBoolToString($showThumbnail).',';
		$jsToolBarpanelPresentation 		= 'showImagenav: '.$this->_convertFromBoolToString($toolBarpanelPresentation).',';
		$jsToolBarpanelPresentationValue    = 'showImagenavValue: '.$jsToolBarpanelPresentationValue.',';
		$jsToolBarpanelShowCounter          = 'showCounter: '. $jsToolBarpanelShowCounter. ',';
		$jsPauseOnInteraction			    = 'pauseOnInteraction: false,';
		$jsInformationPanelPresentation		= 'showInfo: '.$this->_convertFromBoolToString($informationPanelPresentation).',';
		$jsInformationPanelShowTitle		= 'infoPanelShowTitle: '.$this->_convertFromBoolToString($informationPanelShowTitle).',';
		$jsInformationPanelShowDescription	= 'infoPanelShowDescription: '.$this->_convertFromBoolToString($informationPanelShowDescription).',';
		$jsInformationPanelImageShowLink	= 'showImageLink:'.$this->_convertFromBoolToString($informationPanelImageShowLink).',';
		$jsSlideshowLooping					= 'loop:'.$this->_convertFromBoolToString($slideshowLooping).',';
		$jsThumbnailHeight					= 'thumbHeight:'.$thumbnailHeight.',';
		$jsThumbnailPosition				= 'thumbPosition:"'.$thumbnailPanelPosition.'",';


		$pluginOpenTagDiv = '<div style="max-width:'.$args->width.((!$percent)?'px':'').'; margin: 0 auto;">';
		$pluginCloseTagDiv = '</div>';
		$percent = true;
		$args->width = '100%';

		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.'{
	    			width: '.$args->width.((!$percent)?'px':'').';
	    			background-color: '.$cssGeneralBackgroundColor.';
	    			display:inline-table;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-container {
	    			margin: 0 auto;
	    			padding: 0;
	    			'.$imagePanelBackground.'
	    			border: '.$cssGeneralBorderStroke.'px solid '.$cssGeneralBorderColor.';
					-webkit-border-radius: '.$cssGeneralRoundCornerRadius.'px;
					-moz-border-radius: '.$cssGeneralRoundCornerRadius.'px;
					border-radius: '.$cssGeneralRoundCornerRadius.'px;
					height: '.$args->height.'px;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-container .galleria-stage{
	    			position: absolute;
				    top:5%;
				    bottom: 5%;
				    left: 5%;
				    right: 5%;
				    overflow:hidden;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-container .galleria-image-nav{
				    position: absolute;
				    top: 50%;
				    margin-top: -62px;
				    width: 100%;
				    height: 62px;
				    left: 0;
				}'."\n";
		if (preg_match('/MSIE/i',$userAgent))
		{
			$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails .galleria-image {
	    			border: '.$thumbnailBorder.'px solid ' . $themeData->thumbpanel_thumnail_normal_state .';
				}'."\n";
		}
		else
		{
			$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails .galleria-image {
						border: '.$thumbnailBorder.'px solid rgba('.$normalStateColor['red'].', '.$normalStateColor['green'].', '.$normalStateColor['blue'].', 0.3);
					}'."\n";
		}
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails .galleria-image:hover {
	    			border: '.$thumbnailBorder.'px solid '.$activeStateColor.';
	    			filter: alpha(opacity=100);
					-moz-opacity: 1;
					-khtml-opacity: 1;
					opacity: 1;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails .active {
	    			border: '.$thumbnailBorder.'px solid '.$activeStateColor.';
	    			filter: alpha(opacity=100);
					-moz-opacity: 1;
					-khtml-opacity: 1;
					opacity: 1;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails  {
					height: '.($thumbnailHeight +4).'px;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails-container{
	    			background-color: '.$panelThumbnailPanelBackgroundColor.';
	    			left: 0;
				    right: 0;
				    width: 100%;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails-list {
    				margin-top: 5px;
    				margin-left: 10px;
    				margin-bottom: 5px;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-carousel .galleria-thumbnails-list {
   	 				margin-left: 30px;
   					margin-right: 30px;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails .galleria-image {
    				width: '.$thumbnailWidth.'px;
    				height: '.$thumbnailHeight.'px;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails-container {
					height: '.($thumbnailHeight + 15).'px;
					'.$cssThumbnailPanelPosition.'
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-info {
					color: #FFFFFF;
				    display: none;
				    position: absolute;
				    text-align: left;
				    '.$cssInformationPanelPosition.'
				    width: 100%;
				    z-index: 4;
				    left:0;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-info .galleria-info-text {
				    background: none repeat scroll 0 0 rgba('.$panelInfoPanelBackgroundColor['red'].', '.$panelInfoPanelBackgroundColor['green'].', '.$panelInfoPanelBackgroundColor['blue'].', 0.7);
				    padding: 12px;
				    height: auto;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-info .galleria-info-text .galleria-info-title{
					'.$informationPanelTitleCSS.'
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-info .galleria-info-text .galleria-info-description{
					'.$informationPanelDescriptionCSS.'
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-info .galleria-info-text .galleria-info-image-link{
					'.$informationPanelLinkCSS.'
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails-container .galleria-thumb-nav-right{
					  background-position: -578px '.(($thumbnailHeight - 20)/2).'px;
					  height: '.($thumbnailHeight + 15).'px;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails-container .galleria-thumb-nav-left{
					  background-position: -495px '.(($thumbnailHeight - 20)/2).'px;
					  height: '.($thumbnailHeight + 15).'px;
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails-container .galleria-thumb-nav-left:hover{
					   background-color: rgba(255, 255, 255, 0.3);
				}'."\n";
		$css .= '#jsn-themeclassic-jsgallery-'.$args->random_number.' .galleria-thumbnails-container .galleria-thumb-nav-right:hover{
					   background-color: rgba(255, 255, 255, 0.3);
				}'."\n";
		if ($autoPlay)
		{
			$jsAutoPlay = 'autoplay:'.(int) $themeData->slideshow_slide_timing.'000,';
		}
		else
		{
			$jsAutoPlay = 'autoplay:false,';
		}

		if (!$percent)
		{
			$jsWidth = 'width:'.$args->width.',';
		}

		$doc->addStyleDeclaration($css);
		$html  = $pluginOpenTagDiv.'<div id="jsn-themeclassic-jsgallery-'.$args->random_number.'"><div id="jsn-themeclassic-galleria-'.$args->random_number.'">'."\n";
		for($i = 0, $counti = count($images); $i < $counti; $i++)
		{
			$image = $images[$i];
			$alt	= htmlentities($image->title, ENT_QUOTES, 'UTF-8', false);
			if (isset($image->alt_text))
			{
				if ($image->alt_text != '')
				{
					$alt	= htmlentities($image->alt_text, ENT_QUOTES, 'UTF-8', false);
				}
			}			
			$desc  = $this->_wordLimiter(htmlspecialchars($image->description), $informationPanelDescriptionLenghtLimit);
			$html .= '<a href="'.$image->image.'"><img title="'.htmlspecialchars($image->title).'" alt="'.$alt.'" shortdesc="'.$desc.'" src="'.$image->thumbnail.'" longdesc="'.$image->link.'" /></a>'."\n";
		}
		$html .= '</div></div>'.$pluginCloseTagDiv."\n";
		$html .= '<script type="text/javascript">jsnThemeClassicjQuery(function() {jsnThemeClassicjQuery("#jsn-themeclassic-galleria-'.$args->random_number.'").galleria({'.$jsWidth.$jsAutoPlay.$jsShowThumbnail.$jsToolBarpanelPresentation.$jsToolBarpanelPresentationValue.$jsPauseOnInteraction.$jsInformationPanelPresentation.$jsInformationPanelShowTitle.$jsInformationPanelShowDescription.$jsPopupLinks.$jsImagePanelImageClickAction.$jsInformationPanelImageShowLink.$jsSlideshowLooping.$jsThumbnailHeight.$jsThumbnailPosition.$jsImagePanelDefaultPresentationMode.$jsInformationPanelClickAction.$jsInformationpanelPopupLinks.$jsToolBarpanelShowCounter.'height:'.$args->height.', initialTransition: "fade", transition: "slide", thumbCrop: false, thumbFit: false, thumbQuality: false, imageTimeout: 300000});});</script>';

		return $html;
	}

	/**
	 * Check whether the SEO of website is enable or not
	 *
	 * @return string
	 */

	public function getFilterLangSystem()
	{
		$app 			= JFactory::getApplication();
		$router 		= $app->getRouter();
		$modeSef 		= ($router->getMode() == JROUTER_MODE_SEF) ? true : false;
		$languageFilter = $app->getLanguageFilter();
		$uri 			= JFactory::getURI();
		$langCode		= JLanguageHelper::getLanguages('lang_code');
		$langDefault	= JComponentHelper::getParams('com_languages')->get('site', 'en-GB');

		$realPath = 'index.php?';

		if ($languageFilter)
		{
			if (isset($langCode[$langDefault]))
			{
				if ($modeSef)
				{
					$realPath = '';
					$realPath .= JFactory::getConfig()->get('sef_rewrite') ? '' : 'index.php/';
					$realPath .= $langCode[$langDefault]->sef . '/?';
				}
				else
				{
					$realPath = 'index.php?lang=' . $uri->getVar('lang') . '%26';
				}
			}
		}

		return $realPath;
	}

	function loadjQuery()
	{
		$loadJoomlaDefaultJQuery = true;
		if (class_exists('JSNConfigHelper')) {
			$objConfig = JSNConfigHelper::get('com_imageshow');
			if ($objConfig->get('jquery_using') != 'joomla_default') {
				$objUtils = JSNISFactory::getObj('classes.jsn_is_utils');

				if (method_exists($objUtils, 'loadJquery')) {
					$objUtils->loadJquery();
				}
				else {
					JHTML::script($this->_assetsPath . 'js/jsn_is_jquery_safe.js');
					JHTML::script('https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
				}
				$loadJoomlaDefaultJQuery = false;
			}
		}
		if ($loadJoomlaDefaultJQuery) {
			JHTML::script($this->_assetsPath . 'js/jsn_is_jquery_safe.js');
			JHtml::_('jquery.framework');
		}
	}
}