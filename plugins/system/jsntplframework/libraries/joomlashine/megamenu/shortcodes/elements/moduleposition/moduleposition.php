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

class JSNTplMMShortcodeModuleposition extends JSNTplMMShortcodeElement
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
		JSNTplMMHelperFunctions::printAssetTag($this->element_url .'/moduleposition/assets/js/moduleposition-settings.js', 'js');
		JSNTplMMHelperFunctions::printAssetTag($this->element_url .'/moduleposition/assets/css/moduleposition-settings.css', 'css');
	}	
	/**
	 * DEFINE configuration information of shortcode
	 */
	public function elementConfig() 
	{
		$this->config['shortcode'] 			= 'jsn_tpl_mm_moduleposition';
		$this->config['name']				= JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_POSITION_TITLE', true);
		$this->config['icon']				= 'icon-grid-view';
		$this->config['description']		= JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_POSITION_ELEMENT_DESC', true);		
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
		                    'name'    => JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_POSITION_TITLE', true),
		                    'id'      => 'position_id',
		                    'type'    => 'select_module_position_field',
		                    'std'     => '',
		                    'class'   => 'hidden',
		                    'tooltip' => JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_POSITION_DESC', true)
		                ),
				),
				'appearance' => array(
				
						array(
								'name'	=> JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_POSITION_SHOW_MODULE_TITLE_TITLE', true),
								'id'	  => 'show_module_title',
								'type'	=> 'radio',
								'std'	 => 'yes',
								'options' => array('yes' => JText::_('JYES', true), 'no' => JText::_('JNO', true)),
								'tooltip' => JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_POSITION_SHOW_MODULE_TITLE_DESC', true),
						),		
						
				)				
		);
	}
		
	public function elementShortcode($atts = null, $content = null)
	{
		$arrParams = JSNTplMMHelperShortcode::shortcodeAtts($this->config['params'], $atts);
		extract($arrParams);
		$html = '';
		$html .= '<div class="jsn-tpl-mm-module-position-element jsn-tpl-mm-module-position-container '. $css_suffix .'" id="' . $id_wrapper . '">';
		
		if (trim((string) $position_id) == '')
		{
			$html .= '<div class="alert alert-block">' . JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_POSITION_NO_POSITION_SELECTED', true) . '</div>';
		}
		else
		{
			$position = trim((string) $position_id);
			
			$modules = $this->getModules($position);
			
			if (!count($modules))
			{
				$html .= '<div class="alert alert-block">' . JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_POSITION_NO_POSITION_SELECTED', true) . '</div>';
			}	

			$html .= $this->renderModule($modules, $show_module_title);
		}
		
		$html .= '</div>';
		return $html;	
	}

	public static function getModuleList()
	{
		$app = JFactory::getApplication();
		
		$groups = implode(',', JFactory::getUser()->getAuthorisedViewLevels());
		$lang = JFactory::getLanguage()->getTag();
		$clientId = (int) $app->getClientId();
	
		$db = JFactory::getDbo();
	
		$query = $db->getQuery(true)
		->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params')
		->from('#__modules AS m')

		->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id')
		->where('e.enabled = 1');
	
		$date = JFactory::getDate();
		$now = $date->toSql();
		$nullDate = $db->getNullDate();
		$query->where('(m.publish_up = ' . $db->quote($nullDate) . ' OR m.publish_up <= ' . $db->quote($now) . ')')
		->where('(m.publish_down = ' . $db->quote($nullDate) . ' OR m.publish_down >= ' . $db->quote($now) . ')')
		->where('m.access IN (' . $groups . ')')
		->where('m.published = 1')
		->where('m.client_id = ' . $clientId);
	
		// Filter by language
		if ($app->isSite() && $app->getLanguageFilter())
		{
			$query->where('m.language IN (' . $db->quote($lang) . ',' . $db->quote('*') . ')');
		}
	
		$query->order('m.position, m.ordering');
		
		// Set the query
		$db->setQuery($query);
	
		try
		{
			$modules = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			return array();
		}
	
		return $modules;
	}
	
	public function getModules($position)
	{
		$modules 	= self::getModuleList();
		$total 		= count($modules);	
		$result		= array();
		
		//JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_POSITION_NO_MODULES_WITH_THIS_POSTIONS	
		if (!count($total))
		{
			return '<div class="alert alert-block">' . JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_POSITION_NO_MODULES', true) . '</div>';
		}
			
		for ($i = 0; $i < $total; $i++)
		{
			if ($modules[$i]->position == $position)
			{
				$result[] = $modules[$i];
			}
		}
		
		return $result;
	}
	
	public function renderModule($modules, $showModuleTitle)
	{
		$document 	= JFactory::getDocument();
		$renderer 	= $document->loadRenderer('module');
		$html = '';
		$html .= '<div class="jsn-tpl-mm-module-position-element-items">';
		foreach ($modules as $module)
		{
			$module->user 	= '';
			$title			= $module->title;
			$id 			= $module->id;
			$content 		= $module->content;
			$params 		= new JRegistry;
			$params->loadString($module->params);
			$module->params = $params;
			$html .= '<div class="jsn-tpl-mm-module-position-element-item">';
			$moduleHTML = $renderer->render($module, $params, $content);
			if (trim($moduleHTML) == "")
			{
				$html .= '<div class="alert alert-block">' . JText::sprintf('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_POSITION_HAS_NO_CONTENT', $module->title) . '</div>';
			}
			else
			{
				
				$html .= '<div class="jsn-tpl-mm-module-position-element-item-content">';
				if ((string) $showModuleTitle == 'yes')
				{
					$html .= '<h3 class="module-title">' . $title . '</h3>';
				}
				$html .= '<div class="module-content">';
				$html .= $moduleHTML;
				$html .= '</div>';
				$html .= '</div>';
			}	
			$html .= '</div>';
			$html .= '<div class="clearfix"></div>';
		}
		$html .= '</div>';
		
		return $html;
	}
}