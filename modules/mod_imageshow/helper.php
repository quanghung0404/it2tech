<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: helper.php 16594 2012-10-02 04:28:45Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );

class modImageShowHelper
{
	public static function render(&$params)
	{
		global $mainframe;
		$mainframe  = JFactory::getApplication();
		$dispatcher				= JDispatcher::getInstance();
		$objUtils				= JSNISFactory::getObj('classes.jsn_is_utils');
		$paramsCom				= $mainframe->getParams('com_imageshow');
		$language				= '';
		$shortEdition			= $objUtils->getShortEdition();
		$objJSNLang				= JSNISFactory::getObj('classes.jsn_is_language');
		$filterLangSys			= $objJSNLang->getFilterLangSystem();
		$coreData 	  			= $objUtils->getComponentInfo();
		$coreInfo				= json_decode($coreData->manifest_cache);
		if ($objUtils->checkSupportLang())
		{
			$objLanguage = JFactory::getLanguage();
			$language    = $objLanguage->getTag();
		}

		$display			= false;
		$user 				= JFactory::getUser();
		$authAvailable 		= $user->getAuthorisedViewLevels();
		$showcaseID 		= $params->get('showcase_id');
		$showlistID 		= $params->get('showlist_id');
		$objJSNShow			= JSNISFactory::getObj('classes.jsn_is_show');
		$objJSNShowcase		= JSNISFactory::getObj('classes.jsn_is_showcase');
		$objJSNShowlist		= JSNISFactory::getObj('classes.jsn_is_showlist');
		$objJSNImages		= JSNISFactory::getObj('classes.jsn_is_images');
		$randomString 		= $objUtils->randSTR(5);
		$articleAuth 		= $objJSNShow->getArticleAuth($showlistID);
		$showlistInfo 		= $objJSNShowlist->getShowListByID($showlistID);
		$showcaseInfo 		= $objJSNShowcase->getShowCaseByID($showcaseID);
		$html 				= '';

		if (is_null($showlistInfo))
		{
			$html .= $objUtils->displayShowlistMissingMessage();
			echo $html;
			return;
		}

		if (is_null($showcaseInfo))
		{
			$html .= $objUtils->displayShowcaseMissingMessage();
			echo $html;
			return;
		}

		$objJSNTheme  = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
		$themeProfile = $objJSNTheme->getThemeProfile($showcaseInfo->showcase_id);

		if (!$themeProfile)
		{
			$html .= $objUtils->displayThemeMissingMessage();
			echo $html;
			return;
		}

		if ($params->get('width') !='')
		{
			$width  = $params->get('width');
		}
		else
		{
			$width = @$showcaseInfo->general_overall_width;
		}

		if ($params->get('height') !='')
		{
			$height = $params->get('height');
		}
		else
		{
			$height = @$showcaseInfo->general_overall_height;
		}

		$imageSource = JSNISFactory::getSource($showlistInfo['image_source_name'], $showlistInfo['image_source_type'], $showlistInfo['showlist_id']);
		$imagesData  = $objJSNImages->getImagesByShowlistID($showlistInfo['showlist_id']);

		if (!in_array($showlistInfo['access'],  $authAvailable))
		{
			$display = false;
		}
		else
		{
			$display = true;
		}

		if ($width == '')
		{
			$width = '100%';
		}

		if ($height == '')
		{
			$height = '100';
		}

		$posPercentageWidth = strpos($width, '%');

		if ($posPercentageWidth)
		{
			$width = substr($width, 0, $posPercentageWidth + 1);
		}
		else
		{
			$width = (int) $width;
		}

		$height = (int) $height;
		$object					= new stdClass();
		$object->width			= $width;
		$object->height			= $height;
		$object->showlist_id 	= $showlistID;
		$object->showcase_id 	= $showcaseID;
		$object->item_id	 	= 0;
		$object->random_number	= $randomString;
		$object->language		= $language;
		$object->edition		= $shortEdition;
		$object->images			= $imagesData;
		$object->showlist		= $showlistInfo;
		$object->showcase		= $showcaseInfo;
		$object->theme_id		= $themeProfile->theme_id;
		$object->theme_name		= $themeProfile->theme_name;

		$themeInfo 	 = $objJSNTheme->getThemeInfo($themeProfile->theme_name);

		$html .='<!-- '.@$coreInfo->description.' '.@$coreInfo->version.' - ' .@$themeInfo->name. ' '.@$themeInfo->version .' -->';
		if ($params->get('pretext'))
		{
			$html .= '<div class="pretext">';
			$html .= $params->get('pretext');
			$html .= '</div>';
		}
		$html.='<div class="jsn-container">';
		$html.='<div class="jsn-gallery">';

		$result = $objJSNTheme->displayTheme($object);

		if ($result !== false) {
			$html .= $result;
		}



		$html 	.= '</div>';
		$html 	.= '</div>';
		if ($params->get('posttext'))
		{
			$html .= '<div class="posttext">';
			$html .= $params->get('posttext');
			$html .= '</div>';
		}
		if ($display)
		{
			echo $html;
		}
		else
		{
			if ($showlistInfo['authorization_status'] == 1)
			{
				echo '<div>'.$articleAuth['introtext'].$articleAuth['fulltext'].'</div>';
			}
			else
			{
				echo '&nbsp;';
			}
		}
	}

	function approveModule($moduleName, $publish = 1)
	{
		$db 	= JFactory::getDBO();
		$query 	= 'UPDATE #__modules SET published ='.$publish.' WHERE module = '.$db->Quote($moduleName, false);
		$db->setQuery($query);
		if (!$db->query())
		{
			return false;
		}
		return true;
	}
}