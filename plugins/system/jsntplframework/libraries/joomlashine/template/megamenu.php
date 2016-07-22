<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Helper class to generate Cookie Law for template
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       1.0.0
 */
include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES .'/loader.php';
include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES .'/libraries/element.php';

class JSNTplTemplateMegamenu
{
	/**
	 * Instance of template administrator object
	 *
	 * @var  JSNTplTemplateCookielaw
	 */

	private static $_instance;
	private static $_document;
	private static $_templateParameters = array();

	public function __construct ()
	{
		JSNTplMMLoader::register(JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/helpers', 'JSNTplMMHelper');
		self::$_document = JFactory::getDocument();
		if (self::$_document->getType() !== 'html')
		{
			// do nothing
		}
		else
		{
			self::$_templateParameters = isset(self::$_document->params) ? self::$_document->params : null;
		}

		if (empty(self::$_templateParameters))
		{
			$getTemplate = JFactory::getApplication()->getTemplate(true);
			self::$_templateParameters = $getTemplate->params;
		}

	}

	/**
	 * Return an instance of JSNTplTemplateMegamenu class.
	 *
	 * @return  JSNTplTemplateCookielaw
	 */
	public static function getInstance()
	{
		if ( ! isset(self::$_instance))
		{
			self::$_instance = new JSNTplTemplateMegamenu;
		}

		return self::$_instance;
	}

	/**
	 * render MegaMene
	 *
	 * @return string
	 */
	public static function render($inherit = false)
	{
		$tempParams = self::$_templateParameters;
		$html 		= '';
		$uri		= JUri::root(true);

		if ((string) $tempParams->get('enableMegamenu') == '1')
		{
			$doc = JFactory::getDocument();

			$doc->addStyleSheet($uri . '/plugins/system/jsntplframework/assets/3rd-party/bootstrap3/css/bootstrap.css');
			if ($inherit)
			{
				$doc->addStyleSheet($uri . '/plugins/system/jsntplframework/assets/joomlashine/css/jsn-megamenu-frontend.css');
			}
			else
			{	
				$doc->addStyleSheet($uri . '/plugins/system/jsntplframework/assets/joomlashine/css/jsn-megamenu-frontend-old.css');
			}
			$doc->addScript($uri . '/plugins/system/jsntplframework/assets/joomlashine/js/megamenu/megamenu-frontend.js');

			$templateName	= isset($doc->template)	? $doc->template : null;

			if (empty($templateName) OR $templateName == 'system')
			{
				$templateDetails	= JFactory::getApplication()->getTemplate(true);
				$templateName		= $templateDetails->template;
			}


			$templateUrl = $uri . '/templates/' . $templateName;

			// Load megamenu Template CSS
			if (is_readable(JPATH_ROOT . '/templates/' . $templateName . '/css/megamenu/jsn_megamenu.css'))
			{
				$doc->addStylesheet($templateUrl . '/css/megamenu/jsn_megamenu.css');
			}

			$mmItems = self::megamenuItems();
			$renderShortcodeItems = self::renderShortcode($mmItems);
			$items = self::getMenuList();
			$html .= self::beginMegamenu($inherit);
			if (count($items))
			{
				$html .= self::middleMegamenu($items, $mmItems, $renderShortcodeItems);
			}
			$html .= self::endMegamenu($inherit);
		}
		return $html;

	}

	/**
	 * Get a list of the menu items.
	 *
	 * @param   \Joomla\Registry\Registry  &$params  The module options.
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public static function getMenuList()
	{
		$app 		= JFactory::getApplication();
		$tempParams = self::$_templateParameters;
		$megamenu	= $tempParams->get('megamenu');
		$menyType 	= self::getMenuType();
		$mmItems 	= self::megamenuItems();

		$megamenuID = array();
		foreach ($mmItems as $key => $value)
		{
			if ($value['isMegamenu'] == 'true')
			{
				$megamenuID [] = $key;
			}
		}


		$menu 	= $app->getMenu();
		$base 	= self::getActive();
		$user 	= JFactory::getUser();
		$levels = $user->getAuthorisedViewLevels();
		asort($levels);

		$path    	= $base->tree;
 		$start   	= (int) 1;
 		$end     	= (int) 0;
		$showAll	= '1';
		$lastitem 	= 0;

		$attributes[]	= 'menutype';
		$values[]		= $menyType;

		$items   		= $menu->getItems($attributes, $values);

		if ($items)
		{
			foreach ($items as $i => $item)
			{
				if (($start && $start > $item->level)
						|| ($end && $item->level > $end)
						|| (!$showAll && $item->level > 1 && !in_array($item->parent_id, $path))
						|| ($start > 1 && !in_array($item->tree[$start - 2], $path)))
				{
					unset($items[$i]);
					continue;
				}

				if (in_array($item->parent_id, $megamenuID))
				{
					$megamenuID = array_merge(array((int) $item->id), $megamenuID);

					unset($items[$i]);
					continue;
				}

				$item->deeper     = false;
				$item->shallower  = false;
				$item->level_diff = 0;

				if (isset($items[$lastitem]))
				{
					$items[$lastitem]->deeper     = ($item->level > $items[$lastitem]->level);
					$items[$lastitem]->shallower  = ($item->level < $items[$lastitem]->level);
					$items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
				}

				$item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);

				$lastitem     = $i;
				$item->active = false;
				$item->flink  = $item->link;

				// Reverted back for CMS version 2.5.6
				switch ($item->type)
				{
					case 'separator':
					case 'heading':
						// No further action needed.
						continue;

					case 'url':
						if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false))
						{
							// If this is an internal Joomla link, ensure the Itemid is set.
							$item->flink = $item->link . '&Itemid=' . $item->id;
						}
						break;

					case 'alias':
						$item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
						break;

					default:
						$item->flink = 'index.php?Itemid=' . $item->id;
						break;
				}

				if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false))
				{
					$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
				}
				else
				{
					$item->flink = JRoute::_($item->flink);
				}

				// We prevent the double encoding because for some reason the $item is shared for menu modules and we get double encoding
				// when the cause of that is found the argument should be removed
				$item->title        = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
				$item->anchor_css   = htmlspecialchars($item->params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
				$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
				$item->menu_image   = $item->params->get('menu_image', '') ?
				htmlspecialchars($item->params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';
			}

			if (isset($items[$lastitem]))
			{
				$items[$lastitem]->deeper     = (($start?$start:1) > $items[$lastitem]->level);
				$items[$lastitem]->shallower  = (($start?$start:1) < $items[$lastitem]->level);
				$items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start?$start:1));
			}
		}

		return $items;
	}

	/**
	 * Get Active menu
	 *
	 * @return object
	 */
	public static function getActive()
	{
		$menu = JFactory::getApplication()->getMenu('site');
		$lang = JFactory::getLanguage();

		// Look for the home menu
		if (JLanguageMultilang::isEnabled())
		{
			$home = $menu->getDefault($lang->getTag());
		}
		else
		{
			$home  = $menu->getDefault();
		}

		return $menu->getActive() ? $menu->getActive() : $home;
	}

	/**
	 * Render begin of megamenu HTML
	 *
	 * @return string
	 */
	public static function beginMegamenu($inherit = false)
	{
		$tempParams = self::$_templateParameters;
		$showMegamenuItemDescription 	= $tempParams->get('showMegamenuItemDescription');
		$showMegamenuItemIcon 			= $tempParams->get('showMegamenuItemIcon');
		$class = '';

		if ($showMegamenuItemDescription)
		{
			$class .= ' jsn-hasDescription';
		}

		if ($showMegamenuItemIcon)
		{
			$class .= ' jsn-hasIcon';
		}
		
		if ($inherit)
		{
			return '<div class="jsn-modulecontainer jsn-megamenu jsn-tpl-bootstrap3"><div class="jsn-modulecontainer_inner"><div class="jsn-modulecontent"><span class="jsn-menu-toggle">Menu</span><ul class="jsn-tpl-megamenu menu-mainmenu' . $class . '" id="jsn-tpl-megamenu">';
		}
		
		return '<div class="jsn-tpl-megamenu-container jsn-tpl-bootstrap3"><ul class="jsn-tpl-megamenu menu-mainmenu' . $class . '" id="jsn-tpl-megamenu">';
	}

	/**
	 * Render end of megamenu HTML
	 *
	 * @return string
	 */
	public static function endMegamenu($inherit = false)
	{
		if ($inherit)
		{
			return '</ul><div class="clearbreak"></div></div></div></div>';
		}
		return '</ul></div>';
	}

	/**
	 * Render middle of megamenu HTML
	 *
	 * @return string
	 */
	public static function middleMegamenu($items, $mmItems, $renderShortcodeItems)
	{
		$html 		= '';
		$active    	= self::getActive();
		$path     	= $active->tree;
		$active_id 	= $active->id;
		$tempParams = self::$_templateParameters;
		$megamenu	= $tempParams->get('megamenu');
		$menuCount 	= count($items);
		$count 		= 1;
		$flag 		= false;

		foreach ($items as $i => &$item)
		{
			$class = 'item-' . $item->id;

			if (($item->id == $active_id) OR ($item->type == 'alias' AND $item->params->get('aliasoptions') == $active_id))
			{
				$class .= ' current';
			}

			if (in_array($item->id, $path))
			{
				$class .= ' active';
			}
			elseif ($item->type == 'alias')
			{
				$aliasToId = $item->params->get('aliasoptions');

				if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
				{
					$class .= ' active';
				}
				elseif (in_array($aliasToId, $path))
				{
					$class .= ' alias-parent-active';
				}
			}

			if ($item->type == 'separator')
			{
				$class .= ' divider';
			}

			if ($item->deeper)
			{
				$class .= ' parent';
			}

// 			if ($item->parent && !isset($mmItems[$item->id]) && $mmItems[$item->id]['isMegamenu'] != 'true')
// 			{
// 				$class .= ' parent';
// 			}

			if ($item->shallower || $count == $menuCount)
			{
				$class .= ' last';
			}

			if (($count == 1) || ($flag == true))
			{
				$class .= ' first';
			}

			if (isset($mmItems[$item->id]) && $mmItems[$item->id]['isMegamenu'] == 'true')
			{
				$subPClass = '';
				$cssClassSuffix = '';

				$mmItemSettings = $mmItems[$item->id]['menuSetting'];
				if ($mmItemSettings['full_width_value'] == '1')
				{
					$subPClass = ' megamenu-full-width';
				}
				else
				{
					$subPClass = ' megamenu-fixed-width';
				}

				if (isset($mmItemSettings['css_class_suffix_value']))
				{
					if ($mmItemSettings['css_class_suffix_value'] != '')
					{
						$cssClassSuffix = ' ' . $mmItemSettings['css_class_suffix_value'];
					}
				}

				$class .= ' megamenu' . $subPClass . $cssClassSuffix;
			}

			// Icon menu
			if ($item->anchor_css)
			{
				//$class .= ' ' . $item->anchor_css;
			}

			if (!empty($class))
			{
				$class = ' class="' . trim($class) . '"';
			}


			$html .= '<li' . $class . '>';
			$flag = false;
			$item->title = html_entity_decode($item->title);
			// Render the menu item.
			switch ($item->type)
			{
				case 'separator':
					$html .= self::renderSeparatorItemLayout($item, $active_id);
					break;
				case 'url':
					$html .= self::renderUrlItemLayout($item, $active_id);
					break;
				case 'component':
					$html .= self::renderComponentItemLayout($item, $active_id);
					break;
				case 'heading':
					$html .= self::renderHeadingItemLayout($item, $active_id);
					break;
				break;

				default:
					$html .= self::renderUrlItemLayout($item, $active_id);
					break;
			}

			if (isset($mmItems[$item->id]) && $mmItems[$item->id]['isMegamenu'] == 'true')
			{
				$style = '';
				$subClass = '';
				$mmItemSettings = $mmItems[$item->id]['menuSetting'];
				if ($mmItemSettings['full_width_value'] == '1')
				{
					$style = ' style="width:100%; left:0;"';
					$subClass = ' full-width';
				}
				else
				{
					$style = ' style="width:' . $mmItemSettings['container_width'] . 'px;"';
					$subClass = ' fixed-width';
				}


				$html .= '<ul class="jsn-tpl-mm-megamenu-inner jsn-tpl-mm-megamenu-sub-menu sub-menu' . $subClass . '"'. $style . '><li class="grid">' . @$renderShortcodeItems[$item->id] . '</li></ul>';
				$html .= '</li>';
			}
			else
			{
				// The next item is deeper.
				if ($item->deeper)
				{
					$html .= '<ul class="nav-child unstyled small sub-menu">';
					$flag = true;
				}
				elseif ($item->shallower)
				{
					// The next item is shallower.
					$html .= '</li>';
					$html .= str_repeat('</ul></li>', $item->level_diff);
				}
				else
				{
					// The next item is on the same level.
					$html .= '</li>';
				}
			}

			$count ++;
		}

		return $html;
	}

	/**
	 * render component item layout
	 * @param object $item
	 *
	 * @return string
	 */
	public static function renderComponentItemLayout($item, $active_id)
	{
		$tempParams = self::$_templateParameters;
		$showMegamenuItemDescription 	= $tempParams->get('showMegamenuItemDescription');
		$showMegamenuItemIcon 			= $tempParams->get('showMegamenuItemIcon');

		$html = '';
		//$class = $item->anchor_css ? 'class="' . $item->anchor_css . '" ' : '';
		$class = '';
		$title = $item->anchor_title ? 'title="' . $item->anchor_title . '" ' : '';

		if ($item->id == $active_id)
		{
			$class .= ' current';
		}

		$class .= ' clearfix';

		if (!empty($class))
		{
			$class = 'class="' . trim($class) . '" ';
		}

		if ($item->menu_image)
		{
			$item->params->get('menu_text', 1) ?
			$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' :
			$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
		}
		else
		{
			$linktype = $item->title;
		}

		if ($item->anchor_title)
		{
			$linktype = '<span data-title="'. $linktype .'"><span class="jsn-menutitle">' . $linktype . '</span>';
			if ($showMegamenuItemDescription)
			{

				$linktype .= '<span class="jsn-menudescription">' . $item->anchor_title . '</span>';
			}

			$linktype .= '</span>';
		}
		else
		{
			$linktype = '<span data-title="'. $linktype .'"><span class="jsn-menutitle">' . $linktype . '</span></span>';
		}

		$icon = '';
		if ($item->anchor_css && $showMegamenuItemIcon)
		{
			$icon = '<i class="' . $item->anchor_css . '"></i>';
		}
		switch ($item->browserNav)
		{
			default:
			case 0:
				$html = '<a ' . $class . 'href="' . $item->flink . '" ' . $title . '>' . $icon . $linktype .'</a>';
				break;
			case 1:
				$html = '<a ' . $class . 'href="' . $item->flink . '" target="_blank" ' . $title . '>' . $icon . $linktype .'</a>';
				break;
			case 2:
				$html = '<a ' . $class . 'href="' . $item->flink . '" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes\');return false;" ' . $title . '>' . $icon . $linktype . '</a>';
				break;
		}

		return $html;
	}

	/**
	 * render separator item layout
	 * @param object $item
	 *
	 * @return string
	 */
	public static function renderSeparatorItemLayout($item, $active_id)
	{
		$tempParams = self::$_templateParameters;
		$showMegamenuItemDescription 	= $tempParams->get('showMegamenuItemDescription');
		$showMegamenuItemIcon 			= $tempParams->get('showMegamenuItemIcon');

		$html = '';
		$title = $item->anchor_title ? ' title="' . $item->anchor_title . '" ' : '';
		if ($item->menu_image)
		{
			$item->params->get('menu_text', 1) ?
			$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' :
			$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
		}
		else
		{
			$linktype = $item->title;
		}

		if ($item->anchor_title)
		{
			$linktype = '<span data-title="'. $linktype .'"><span class="jsn-menutitle">' . $linktype . '</span>';
			if ($showMegamenuItemDescription)
			{
				$linktype .= '<span class="jsn-menudescription">' . $item->anchor_title . '</span>';
			}
			$linktype .= '</span>';
		}
		else
		{
			$linktype = '<span data-title="'. $linktype .'"><span class="jsn-menutitle">' . $linktype . '</span></span>';
		}

		$icon = '';
		if ($item->anchor_css && $showMegamenuItemIcon)
		{
			$icon = '<i class="' . $item->anchor_css . '"></i>';
		}

		$html .= '<a class="clearfix" href="javascript: void(0)">' . $icon . $linktype . '</a>';

		return $html;
	}

	/**
	 * render URL item layout
	 * @param object $item
	 *
	 * @return string
	 */

	public static function renderUrlItemLayout($item, $active_id)
	{
		$tempParams = self::$_templateParameters;
		$showMegamenuItemDescription 	= $tempParams->get('showMegamenuItemDescription');
		$showMegamenuItemIcon 			= $tempParams->get('showMegamenuItemIcon');

		$html  = '';
		$class = '';
		//$class = $item->anchor_css ? 'class="' . $item->anchor_css . '" ' : '';
		$title = $item->anchor_title ? 'title="' . $item->anchor_title . '" ' : '';

		if ($item->id == $active_id)
		{
			$class .= ' current';
		}

		$class .= ' clearfix';

		if (!empty($class))
		{
			$class = 'class="' . trim($class) . '" ';
		}

		if ($item->menu_image)
		{
			$item->params->get('menu_text', 1) ?
			$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' :
			$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
		}
		else
		{
			$linktype = $item->title;
		}

		$flink = $item->flink;
		$flink = JFilterOutput::ampReplace(htmlspecialchars($flink));

		if ($item->anchor_title)
		{
			$linktype = '<span data-title="'. $linktype .'"><span class="jsn-menutitle">' . $linktype . '</span>';
			if ($showMegamenuItemDescription)
			{
				$linktype .= '<span class="jsn-menudescription">' . $item->anchor_title . '</span>';
			}
			$linktype .= '</span>';
		}
		else
		{
			$linktype = '<span data-title="'. $linktype .'"><span class="jsn-menutitle">' . $linktype . '</span></span>';
		}

		$icon = '';
		if ($item->anchor_css && $showMegamenuItemIcon)
		{
			$icon = '<i class="' . $item->anchor_css . '"></i>';
		}

		switch ($item->browserNav)
		{
			default:
			case 0:
				$html .= '<a ' . $class . 'href="' . $flink . '" ' . $title . '>' . $icon . $linktype .'</a>';
				break;
			case 1:
				$html .= '<a ' . $class . 'href="' . $flink . '" target="_blank" ' . $title . '>' . $icon . $linktype .'</a>';
				break;
			case 2:
				$options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes';
				$html .= '<a ' . $class . 'href="' . $flink . '" onclick="window.open(this.href,\'targetWindow\',\'' . $options . '\');return false;" ' . $title . '>' . $icon . $linktype . '</a>';
			break;
		}

		return $html;
	}

	/**
	 * render Heading item layout
	 * @param object $item
	 *
	 * @return string
	 */
	public static function renderHeadingItemLayout($item, $active_id)
	{
		$html = '';

		$title = $item->anchor_title ? 'title="' . $item->anchor_title . '" ' : '';

		if ($item->menu_image)
		{
			$item->params->get('menu_text', 1) ?
			$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' :
			$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
		}
		else
		{
			$linktype = $item->title;
		}

		$html .= '<span class="nav-header ' . $item->anchor_css . '" ' . $title . '>' . $linktype . '</span>';

		return $html;
	}

	/**
	 * Get megamenu template parameters
	 * @param object $item
	 *
	 * @return object
	 */
	public static function megamenuItems()
	{
		$app = JFactory::getApplication();	
		// Get the template
		$template = $app->getTemplate(true);
		$language = JFactory::getLanguage()->getTag();		
		
		if ($template->id == null)
		{
			$tmpTemplate = JSNTplMMHelperMegamenu::getTemplateHomeStyle($template->template);
			
			$megamenu    = JSNTplMMHelperMegamenu::getMegamenuItemsByStyleId($tmpTemplate->id, $language);
			
		}
		else
		{	
			$megamenu = JSNTplMMHelperMegamenu::getMegamenuItemsByStyleId($template->id, $language);
		}
		
		if (!count($megamenu))
		{
			//backwards compatible
			$tempParams = self::$_templateParameters;
			$item = $tempParams->get('megamenu');
		}
		else
		{
			$item = json_decode($megamenu->params, true);
		}
		
		
		if (isset($item['items']) && count($item['items']))
		{
			$item = $item['items'];
			return $item;
		}

		return array();
	}

	/**
	 * render Shortcode
	 * @param object $mmItems
	 *
	 * @return array
	 */
	public static function renderShortcode($mmItems)
	{
		$items = array();
		foreach ($mmItems as $key => $mmItem)
		{
			if ($mmItem['isMegamenu'] == 'true')
			{
				$shortcodeContent = $mmItem['shortcodeContent'];
				$shortcodeContent = urldecode($shortcodeContent);
				$items[$key] = JSNTplMMHelperShortcode::doShortcodeFrontend($shortcodeContent);
			}
		}

		return $items;

	}

	/**
	 * Check wherther megamenu is enabled or not
	 *
	 * @return bool
	 */
	public static function isEnabledMegamenu()
	{
		$jversion = new JVersion();

		if (version_compare($jversion->getShortVersion(), "3.0", "<"))
		{
			return false;
		}
		else
		{
			$tempParams = self::$_templateParameters;
			if ((string) $tempParams->get('enableMegamenu') == '1')
			{
				return true;
			}
		}

		return false;
	}
	
	public static function getMenuType()
	{
		$app = JFactory::getApplication();
		// Get the template
		$template = $app->getTemplate(true);
		$language = JFactory::getLanguage()->getTag();
		

		if ($template->id == null)
		{
			$tmpTemplate = JSNTplMMHelperMegamenu::getTemplateHomeStyle($template->template);
			$megamenu    = JSNTplMMHelperMegamenu::getMegamenuItemsByStyleId($tmpTemplate->id, $language);
		}
		else
		{	
			$megamenu = JSNTplMMHelperMegamenu::getMegamenuItemsByStyleId($template->id, $language);
		}

		if (count($megamenu))
		{
			return (string) $megamenu->menu_type;
		}
		else
		{
			//backwards compatible
			$tempParams = self::$_templateParameters;
			$megamenu	= $tempParams->get('megamenu');
			$menyType 	= $megamenu['menuType'];
			return $menyType;
		}
	}
}