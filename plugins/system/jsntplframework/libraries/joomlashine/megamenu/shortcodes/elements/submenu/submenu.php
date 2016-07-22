<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2015 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file.
defined('_JEXEC') || die('Restricted access');

include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/element.php';

class JSNTplMMShortcodeSubmenu extends JSNTplMMShortcodeElement
{
	/**
	 * Constructor
	 *
	 * @return type
	 */
	public function __construct()
	{
		$this->type = 'element';
		parent::__construct();
	}

	public function backendElementAssets()
	{
		JSNTplMMHelperFunctions::printAssetTag($this->element_url .'/submenu/assets/js/submenu-settings.js', 'js');
		JSNTplMMHelperFunctions::printAssetTag($this->element_url .'/submenu/assets/css/submenu-settings.css', 'css');
	}
	/**
	 * DEFINE configuration information of shortcode
	 */
	public function elementConfig()
	{
		$this->config['shortcode'] 			= 'jsn_tpl_mm_submenu';
		$this->config['name']				= JText::_('JSN_TPLFW_MEGAMENU_JOOMLA_SUBMENU_ELEMENT_TITLE', true);
		$this->config['icon']				= 'icon-list-view';
		$this->config['description']		= JText::_('JSN_TPLFW_MEGAMENU_JOOMLA_SUBMENU_ELEMENT_DESC', true);
		$this->config['edit_using_ajax'] = true;
	}

	/**
	 * contain setting items of this element ( use for modal box )
	 *
	 */
	public function elementItems() {
		$this->items = array(
				'content' => array(

						array(
								'name'       => JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_TITLE_TITLE', true),
								'id'         => 'el_title',
								'type'       => 'text_field',
								'std'        => '',
								'class'		 => 'input-sm',
								'role'  	 => 'title',
								'tooltip'=> JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_TITLE_DESC', true),
						),
		                array(
		                    'name'    => JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_SUBMENU_PARENT_ID_TITLE', true),
		                    'id'      => 'parent_id',
		                    'type'    => 'text_field',
		                    'std'     => '',
		                    'class'   => 'hidden',
		                    'tooltip' => JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_SUBMENU_PARENT_ID_DESC', true)
		                ),
				),
				'appearance' => array(



				)
		);
	}

	public function elementShortcode($atts = null, $content = null)
	{
		$templateParameters = $this->getTemplateParams();

		$arrParams = JSNTplMMHelperShortcode::shortcodeAtts($this->config['params'], $atts);
		extract($arrParams);

		$html = '<div class="jsn-tpl-megamenu-submenu-container jsn_tpl_mm_menu_element">';
		$items = self::getList($templateParameters, $parent_id);
		$html .= self::beginMenu();

		if (count($items))
		{
			$html .= self::middleMenu($items, $parent_id);
		}
		else
		{
			$html .= '<li><div class="alert alert-block">' . JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_SUBMENU_NO_SUBMENU', true) . '</div></li>';
		}

		$html .= self::endMenu();
		$html .= '</div>';

		return $html;
	}

	public function getTemplateParams()
	{
		$document = JFactory::getDocument();
		if ($document->getType() !== 'html')
		{
			return false;
		}
		else
		{
			$templateParameters = isset($document->params) ? $document->params : null;
		}

		if (empty($templateParameters))
		{
			$getTemplate = JFactory::getApplication()->getTemplate(true);
			$templateParameters = $getTemplate->params;
		}
		
		$app = JFactory::getApplication();
		// Get the template
		$template = $app->getTemplate(true);
		$language = JFactory::getLanguage()->getTag();
			
		$megamenu = JSNTplMMHelperMegamenu::getMegamenuItemsByStyleId($template->id, $language);
		if (count($megamenu))
		{
			$templateParameters->set('megamenu', json_decode($megamenu->params, true));
		}
		
		return $templateParameters;
	}

	public static function getList($tempParams, $parentID)
	{

		$parentIDs 		= array();
		$parentIDs[] 	= $parentID;

		$megamenu	= $tempParams->get('megamenu');

		$menyType = $megamenu['menuType'];
		//
		$app 	= JFactory::getApplication();
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
		$attributes		= array();
		$values 		= array();

		$attributes[]	= 'menutype';
		$values[]		= $menyType;

// 		$attributes[]	= 'parent_id';
// 		$values[]		= $parentID;

		$items   	= $menu->getItems($attributes, $values);

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

				if (!in_array($item->parent_id, $parentIDs))
				{
					//$megamenuID = array_merge(array((int) $item->id), $megamenuID);

					unset($items[$i]);
					continue;
				}
				$parentIDs 			= array_merge(array((int) $item->id), $parentIDs);
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

	public static function beginMenu()
	{
		return '<ul class="jsn-tpl-megamenu-submenu-wrapper">';
	}

	public static function endMenu()
	{
		return '</ul>';
	}

	public static function middleMenu($items, $parentID)
	{
		$html 			= '';
		$active    		= self::getActive();
		$path     		= $active->tree;
		$active_id 		= $active->id;

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
				$class .= ' deeper';
			}

			if ($item->parent)
			{
				$class .= ' parent';
			}

			if (!empty($class))
			{
				$class = ' class="' . trim($class) . '"';
			}

			$html .= '<li' . $class . '>';

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
			// The next item is deeper.

			if ($item->deeper)
			{
				$html .= '<ul class="nav-child unstyled small sub-menu">';
			}
			elseif ($item->shallower && $item->parent_id != $parentID)
			{
				// The next item is shallower.

				$html .= '</li>';
				$html .= str_repeat('</ul></li>', 1);

			}
			else
			{
				// The next item is on the same level.
				$html .= '</li>';
			}
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
			$linktype = '<span><span class="jsn-menutitle">' . $linktype . '</span>';
			$linktype .= '<span class="jsn-menudescription">' . $item->anchor_title . '</span></span>';
		}
		else
		{
			$linktype = '<span><span class="jsn-menutitle">' . $linktype . '</span></span>';
		}

		$icon = '';
		if ($item->anchor_css)
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
			$linktype = '<span><span class="jsn-menutitle">' . $linktype . '</span>';
			$linktype .= '<span class="jsn-menudescription">' . $item->anchor_title . '</span></span>';
		}
		else
		{
			$linktype = '<span><span class="jsn-menutitle">' . $linktype . '</span></span>';
		}

		$icon = '';
		if ($item->anchor_css)
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
		$html = '';
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
			$linktype = '<span><span class="jsn-menutitle">' . $linktype . '</span>';
			$linktype .= '<span class="jsn-menudescription">' . $item->anchor_title . '</span></span>';
		}
		else
		{
			$linktype = '<span><span class="jsn-menutitle">' . $linktype . '</span></span>';
		}

		$icon = '';
		if ($item->anchor_css)
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
}