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
jimport('joomla.application.component.model');
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
class JSNISPileDisplay extends JObject
{
	var $_themename 	= 'themepile';
	var $_themetype 	= 'jsnimageshow';
	var $_assetsPath 	= 'plugins/jsnimageshow/themepile/assets/';
    var $_document      = null;
	function __construct() {
        $this->_document = JFactory::getDocument();
    }

	function standardLayout($args)
	{
		$objJSNShowList	= JSNISFactory::getObj('classes.jsn_is_showlist');
		$showListInfo 	= $objJSNShowList->getShowListByID($args->showlist['showlist_id'], true);
		$dataObj 		= $objJSNShowList->getShowlist2JSON($args->uri, $args->showlist['showlist_id']);
		$images			= $dataObj->showlist->images->image;

		if (!count($images)) return '';

		switch ($showListInfo['image_loading_order'])
		{
			case 'backward':
				krsort($images);
				$images = array_values($images);
				break;
			case 'random':
				shuffle($images);
				break;
			case 'forward':
				ksort($images);
		}

        $this->_document->addStyleSheet(JUri::root() . $this->_assetsPath . 'css/photopile/photopile.css');
        $this->_document->addStyleSheet(JUri::root() . $this->_assetsPath . 'css/style.css');
        $this->loadjQuery();
		$this->_document->addScript(JUri::root() . $this->_assetsPath . 'js/jsn_is_conflict.js');
		$this->_document->addScript(JUri::root() . $this->_assetsPath . 'js/jquery-ui-1.8.9.custom.js');
		$this->_document->addScript(JUri::root() . $this->_assetsPath . 'js/photopile/photopile.js');

		$percent  			= strpos($args->width, '%');

        $pluginOpenTagDiv = '<div style="max-width:' . $args->width . ((!$percent) ? 'px' : '') . '; margin: 0 auto;">';
		$pluginCloseTagDiv = '</div>';
		$percent = true;
		$args->width = '100%';

		$themeData 		   	= $this->getThemeDataStandard($args);
        $imageSource        = ($themeData->image_source == 'thumbnail') ? 'thumbnail' : 'image';
		$objAllows			= new stdClass;
		$objAllows->show_caption 		= @$themeData->show_title;
		$objAllows->show_description	= $themeData->show_description;
		//$objAllows->show_close			= $themeData->show_close;
		//$objAllows->show_thumbs			= $themeData->show_thumbs;
        $imageLink          = ($themeData->image_click_action == 'show_original_image') ? 'image' : 'link';
        $openLinkIn         = ($themeData->open_link_in == 'current_browser') ? '' : 'target="_blank"';
        $themeDataJson      = json_encode($themeData);
        $width              = ($percent === FALSE) ? $args->width . 'px' : $args->width;
        $wrapClass          = 'jsn-' . $this->_themename . '-container-' . $args->random_number;


		$html  = $pluginOpenTagDiv.'<div style="width: '.$width.'; height:auto;border:none;margin:30px 0;" class="jsn-themepile-container '.$wrapClass.'">';

        $html .= "<div id='jsn-themepile-wrapper-{$args->random_number}' class='jsn-themepile-wrapper'><ul class='photopile'>";
		$showTitle = $themeData->show_title == '0' ? 'false' : 'true';
		$showDescription = $themeData->show_description == '0' ? 'false' : 'true';
		
		foreach ($images as $image) {
			$imageTitle = htmlentities($image->title, ENT_QUOTES, 'UTF-8', false);
			$imageDescription = htmlentities($image->description, ENT_QUOTES, 'UTF-8', false);
            $html .= "<li data-random-number='".$args->random_number."' data-show-title='".$showTitle."' data-show-description='".$showDescription."'>";
            $html .= "<a href='{$image->image}'>";
            if ($imageSource == 'thumbnail') {
                $html .= "<img src='{$image->thumbnail}' data-title='{$imageTitle}' data-desc='{$imageDescription}' alt='{$image->alt_text}'/>";
            } else {
                $html .= "<img src='{$image->image}' data-title='{$imageTitle}' data-desc='{$imageDescription}' alt='{$image->alt_text}'/>";
            }
            $html .= "</a>";
            $html .= "</li>";
		}
        $html .= "</ul></div>";
        $html .= '</div>' . $pluginCloseTagDiv;
        $html .= '<input type="hidden" id="data_allow_pile_' . $args->random_number . '" value="' . htmlspecialchars(json_encode($objAllows)) . '"/>';

        
        $titleCss = strip_tags(str_replace("\n", "", $themeData->title_css));
       
        $descCss = strip_tags(str_replace("\n", "", $themeData->description_css));
        $fadeDuration = ((int) $themeData->fade_duration);
        $pickupDuration = ((int) $themeData->pickup_duration);
        $showShadow = $themeData->show_shadow == '0' ? 'false' : 'true';      
        $rootURL = JURI::root(true);

        $script = "
        <script type='text/javascript'>
            (function($) {
                $(document).ready(function () {
                    photopile.scatter($('#jsn-themepile-wrapper-' + '{$args->random_number}'), {
                        thumbOverlap: {$themeData->thumbnail_overlap},
                        thumbRotation: {$themeData->thumbnail_rotation},
                        thumbBorderWidth: {$themeData->thumbnail_border_width},
                        thumbBorderColor: '{$themeData->thumbnail_border_color}',
                        thumbBorderHover: '{$themeData->thumbnail_border_hover}',
                        thumbShadow: {$showShadow},
                        thumbShadowColor: '{$themeData->thumbnail_shadow_color}',
                        thumbWidth: {$themeData->image_width},
                        thumbHeight: {$themeData->image_height},
                        fadeDuration: {$fadeDuration},
                        pickupDuration: {$pickupDuration},
                        clickAction: '{$themeData->image_click_action}',
                        openLinkIn: '{$themeData->open_link_in}',
                        classContainer: '{$args->random_number}',
						rootURL: '{$rootURL}',
                    });  // ### initialize the photopile ###
                });
            })(jsnThemePilejQuery);
        </script>";
        $html .= $script;
        $style = "
        <style>
        #photopile-active-image-info h3.jsn-pile-active-title-{$args->random_number} {
           {$titleCss}
        }
        #photopile-active-image-info p.jsn-pile-active-desc-{$args->random_number} {
           {$descCss}
        }
        </style>
        ";
        $html .= $style;
		return $html;
	}

	function displayAlternativeContent()
	{
		return '';
	}

	function displaySEOContent($args)
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
					$html .= '<p><a href="'.$row->image_link.'">'.$row->image_link.'</a></p>';
				}
				$html .= '</li>';
			}
			$html .= '</ul></div>';
		}
		$html   .='</div>'."\n";
		return $html;
	}
	function mobileLayout($args){
		return '';
	}
	function display($args)
	{
		$string		= '';
		$args->uri	= JURI::base();
		$string .= $this->standardLayout($args);
		$string .= $this->displaySEOContent($args);
		return $string;
	}

	function getThemeDataStandard($args)
	{
		if (is_object($args))
		{
			$path = JPath::clean(JPATH_PLUGINS.DS.$this->_themetype.DS.$this->_themename.DS.'models');
			JModelLegacy::addIncludePath($path);

			$model 		= JModelLegacy::getInstance($this->_themename);
			$themeData  = $model->getTable($args->theme_id);

			return $themeData;
		}
		return false;
	}

	function getThemeDataMobile($args)
	{
		return false;
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