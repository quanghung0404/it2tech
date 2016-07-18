<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: imageshow.php 17428 2012-10-25 04:29:38Z dinhln $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
class plgContentImageShow extends JPlugin
{
	var $_application 	= null;
	var $_user  		= null;

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->_application = JFactory::getApplication();
		$this->_user = JFactory::getUser();
	}

	public function onContentPrepare($context, &$row, &$params, $page=0)
	{
		if (is_file(JPATH_ADMINISTRATOR . '/components/com_imageshow/classes/jsn_is_factory.php'))
		{
			include_once(JPATH_ADMINISTRATOR . '/components/com_imageshow/classes/jsn_is_factory.php');
		}
		else
		{
			return;
		}		
		
		if ($this->_application->isAdmin()) return;

		JHtmlBehavior::framework();	
		JHTML::_('behavior.modal', 'a.modal');
		JPlugin::loadLanguage('plg_content_imageshow', JPATH_BASE);
		$dispatcher				= JDispatcher::getInstance();
		$objUtils				= JSNISFactory::getObj('classes.jsn_is_utils');
		$objJSNShow 			= JSNISFactory::getObj('classes.jsn_is_show');
		$objJSNShowcase			= JSNISFactory::getObj('classes.jsn_is_showcase');
		$objJSNShowlist			= JSNISFactory::getObj('classes.jsn_is_showlist');
		$objJSNImages			= JSNISFactory::getObj('classes.jsn_is_images');
		$objJSNLang				= JSNISFactory::getObj('classes.jsn_is_language');
		$coreData 	  			= $objUtils->getComponentInfo();
		$coreInfo				= json_decode($coreData->manifest_cache);
		preg_match_all('/\{imageshow (.*)\/\}/U', $row->text, $matches, PREG_SET_ORDER);

		$paramsCom			= $this->_application->getParams('com_imageshow');

		$language			= '';
		$shortEdition  		= $objUtils->getShortEdition();
		$filterLangSys		= $objJSNLang->getFilterLangSystem();

		if ($objUtils->checkSupportLang())
		{
			$objLanguage = JFactory::getLanguage();
			$language    = $objLanguage->getTag();
		}

		$display			= false;
		$authAvailable 		= $this->_user->getAuthorisedViewLevels();

		if (count($matches))
		{
			for ($i = 0, $counti = count($matches); $i <$counti; $i++)
			{
				$data 		= explode(' ', $matches[$i][1]);
				$width 		= '';
				$height 	= '';
				$html 		= '';
				$showListID = 0;
				$showCaseID = 0;
				foreach ($data as $values)
				{
					$value = $values;
					if (stristr($values, 'sl'))
					{
						$showListValue 	= explode('=', $values);
						$showListID 	= $showListValue[1];
					}
					elseif (stristr($values, 'sc'))
					{
						$showCaseValue 	= explode('=', $values);
						$showCaseID 	= $showCaseValue[1];
					}
					elseif (stristr($values, 'w'))
					{
						$widthValue 	= explode('=', $values);
						$width 			= str_replace($values, $widthValue[1], $values);
					}
					elseif (stristr($values, 'h'))
					{
						$heightValue 	= explode('=', $values);
						$height 		= str_replace($values, $heightValue[1], $values);
					}
				}

				$showlistInfo 	= $objJSNShowlist->getShowListByID($showListID);

				if (is_null($showlistInfo))
				{
					$missingDataBox = $objUtils->displayShowlistMissingMessage();
					$row->text = str_replace("{imageshow ".$matches[$i][1]."/}", $missingDataBox, $row->text);
				}

				$showcaseInfo 	= $objJSNShowcase->getShowCaseByID($showCaseID);

				if (is_null($showcaseInfo))
				{
					$missingDataBox = $objUtils->displayShowcaseMissingMessage();
					$row->text = str_replace("{imageshow ".$matches[$i][1]."/}", $missingDataBox, $row->text);
				}

				$themeProfile = false;

				if ($showcaseInfo) {
					$objJSNTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');
					$themeProfile 	= $objJSNTheme->getThemeProfile($showcaseInfo->showcase_id);
				}

				if (!$themeProfile)
				{
					$missingDataBox = $objUtils->displayThemeMissingMessage();
					$row->text = str_replace("{imageshow ".$matches[$i][1]."/}", $missingDataBox, $row->text);
				}

				if (!is_null($showcaseInfo) && !is_null($showlistInfo) && $themeProfile)
				{
					$themeInfo 		= $objJSNTheme->getThemeInfo($themeProfile->theme_name);
					$editionVersion = '<!-- '.@$coreInfo->description.' '.@$coreInfo->version.' - '.@$themeInfo->name.' '.@$themeInfo->version.' -->';

					if ($width != '')
					{
						$width  = $width;
					}
					else
					{
						$width 	= @$showcaseInfo->general_overall_width;
					}
					if ($height != '')
					{
						$height = $height;
					}
					else
					{
						$height = @$showcaseInfo->general_overall_height;
					}
					$articleAuth 		= $objJSNShow->getArticleAuth($showListID);

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
					$object->showlist_id 	= $showListID;
					$object->showcase_id 	= $showCaseID;
					$object->item_id	 	= 0;
					$object->random_number	= $objUtils->randSTR(8);
					$object->language		= $language;
					$object->edition		= $shortEdition;
					$object->images			= $imagesData;
					$object->showlist		= $showlistInfo;
					$object->showcase		= $showcaseInfo;
					$object->theme_id		= $themeProfile->theme_id;
					$object->theme_name		= $themeProfile->theme_name;
					$object->plugin			= true;

					$html 	.= $editionVersion;
					$html	.='<div class="jsn-container">';
					$html 	.='<div class="jsn-gallery">';

					$result = $objJSNTheme->displayTheme($object);

					if ($result !== false) {
						$html .= $result;
					}

					$html 	.='</div>';
					$html 	.='</div>';
					if ($display)
					{
						$row->text = str_replace("{imageshow ".$matches[$i][1]."/}", $html, $row->text);
					}
					else
					{
						if ($showlistInfo['authorization_status'])
						{
							$row->text = str_replace("{imageshow ".$matches[$i][1]."/}", '<div>'.$articleAuth['introtext'].$articleAuth['fulltext'].'</div>', $row->text);
						}
						else
						{
							$row->text = str_replace("{imageshow ".$matches[$i][1]."/}", '&nbsp;', $row->text);
						}
					}
				}
			}
		}

		preg_match_all('/\{imageshow (.*)\}(.*)\{\/imageshow\}/U', $row->text, $matchesLink, PREG_SET_ORDER);

		if (count($matchesLink))
		{
			for ($z=0; $z < count($matchesLink); $z++)
			{
				$dataLink 	= explode(' ', $matchesLink[$z][1]);
				$width 		='';
				$height 	='';
				$showCaseID	= 0;
				$showListID = 0;
				foreach ($dataLink as $values)
				{
					$value = $values;
					if (stristr($values, 'sl'))
					{
						$showListValue 	= explode('=', $values);
						$showList 		= str_replace($values, 'showlist_id='.$showListValue[1], $values);
						$showListID 	= $showListValue[1];
					}
					elseif (stristr($values, 'sc'))
					{
						$showCaseValue 	= explode('=', $values);
						$showCase 		= str_replace($values, 'showcase_id='.$showCaseValue[1], $values);
						$showCaseID 	= $showCaseValue[1];
					}
					elseif (stristr($values, 'w'))
					{
						$widthValue 	= explode('=', $values);
						$width 			= str_replace($values, $widthValue[1], $values);
					}
					elseif (stristr($values, 'h'))
					{
						$heightValue 	= explode('=', $values);
						$height 		= str_replace($values, $heightValue[1], $values);
					}
				}

				$showlistInfo 	= $objJSNShowlist->getShowListByID($showListID);
				$showcaseInfo 	= $objJSNShowcase->getShowCaseByID($showCaseID, true,  'loadAssoc');

				if ($width != '')
				{
					$width  = $width;
				}
				else
				{
					$width = $showcaseInfo['general_overall_width'];
				}

				if ($height != '')
				{
					$height = $height;
				}
				else
				{
					$height = $showcaseInfo['general_overall_height'];
				}

				if (strpos($width, '%'))
				{
					$width = '650';
				}

				$width 	= (int) $width;
				$height = (int) $height;
				$sefRewrite = JFactory::getConfig()->get('sef_rewrite');
				$link = ($sefRewrite) ? '' : 'index.php';
				$link .='?option=com_imageshow&amp;tmpl=component&amp;view=show&amp;'.$showList.'&amp;'.$showCase.'&amp;w='.$width.'&amp;h='.$height;
				$html = '<a rel="{handler: \'iframe\', size: {x: '.($width).', y: '.($height).'}}" href="'.$link.'" class="modal">'.$matchesLink[$z][2].'</a>';
				$row->text = str_replace("{imageshow ".$matchesLink[$z][1]."}".$matchesLink[$z][2]."{/imageshow}", $html, $row->text);
			}
		}
		return true;
	}
}
?>