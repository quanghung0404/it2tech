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

class JSNTplMMShortcodeModule extends JSNTplMMShortcodeElement
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
		JSNTplMMHelperFunctions::printAssetTag($this->element_url .'/module/assets/js/module-settings.js', 'js');
		$token = JSession::getFormToken();
		JSNTplMMHelperFunctions::printAssetTag('
			(function($) {
			    $(document).ready(function() {
					new $.JSNTplMMShortcodeModuleSettings({
						token: "'. $token .'"
					});
				});
			})(jQuery);
		', 'js', true, true);
		JSNTplMMHelperFunctions::printAssetTag($this->element_url .'/module/assets/css/module-settings.css', 'css');
	}
	/**
	 * DEFINE configuration information of shortcode
	 */
	public function elementConfig()
	{
		$this->config['shortcode'] 			= 'jsn_tpl_mm_module';
		$this->config['name']				= JText::_('JSN_TPLFW_MEGAMENU_JOOMLA_MODULE_ELEMENT_TITLE', true);
		$this->config['icon']				= 'icon-joomla';
		$this->config['description']		= JText::_('JSN_TPLFW_MEGAMENU_JOOMLA_MODULE_ELEMENT_DESC', true);
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
								'class'		 => 'input-sm jsn-tpl-mm-element-module-title',
								'role'  	 => 'title',
								'tooltip'=> JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_TITLE_DESC', true),
						),

		                array(
		                    'name'    => JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_TITLE', true),
		                    'id'      => 'module_id',
		                    'type'    => 'select_module_field',
		                    'std'     => '',
		                    'class'   => 'hidden',
		                    'tooltip' => JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_DESC', true)
		                ),

				),
				'appearance' => array(

						array(
								'name'	=> JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_SHOW_MODULE_TITLE_TITLE', true),
								'id'	  => 'show_module_title',
								'type'	=> 'radio',
								'std'	 => 'yes',
								'options' => array('yes' => JText::_('JYES', true), 'no' => JText::_('JNO', true)),
								'tooltip' => JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_SHOW_MODULE_TITLE_DESC', true),
						),

				)
		);
	}

	public function elementShortcode($atts = null, $content = null)
	{
		$html = '';
		$id = '';
		$class = '';
		$arrParams = JSNTplMMHelperShortcode::shortcodeAtts($this->config['params'], $atts);
		extract($arrParams);
		if ($id_wrapper != '')
		{
			$id = ' id="' . $id_wrapper . '"';
		}

		$class = "jsn-tpl-mm-module-element jsn-tpl-mm-module-element-container ". $css_suffix;

		$html .= '<div class="'. trim($class) .'"' . $id . '>';
		if ((string) $module_id == '' || (int) $module_id <= 0)
		{
			$html .= '<div class="alert alert-block">' . JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_NO_MODULE_SELECTED', true) . '</div>';
		}
		else
		{
			$moduleID = (int) $module_id;
			$html .= $this->loadModule($moduleID, $show_module_title);
		}

		$html .= '</div>';

		return $html;
	}

	public function loadModule($mID, $showModuleTitle)
	{
		$app 		= JFactory::getApplication();
		$lang 		= JFactory::getLanguage()->getTag();
		$user 		= JFactory::getUser();
		$groups 	= implode(',', $user->getAuthorisedViewLevels());
		$clientId 	= (int) $app->getClientId();
		$date 		= JFactory::getDate();
		$now 		= $date->toSql();

		$document = JFactory::getDocument();
		$renderer = $document->loadRenderer('module');

		$db			= JFactory::getDbo();
		$nullDate 	= $db->getNullDate();

		$query = $db->getQuery(true);
		$query->clear();
		$query->select('m.id, m.title, m.module, m.position, m.ordering, m.content, m.showtitle, m.params, m.access');
		$query->from('#__modules AS m');
		$query->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id');
		$query->where('e.enabled = 1');
		$query->where('(m.publish_up = ' . $db->quote($nullDate) . ' OR m.publish_up <= ' . $db->quote($now) . ')');
		$query->where('(m.publish_down = ' . $db->quote($nullDate) . ' OR m.publish_down >= ' . $db->quote($now) . ')');
		$query->where('m.id=' . (int) $mID . ' AND m.client_id= ' . $clientId);
		$query->where('m.published = 1');
		$query->where('m.access IN (' . $groups . ')');
		// Filter by language
		if ($app->isSite() && $app->getLanguageFilter())
		{
			$query->where('m.language IN (' . $db->quote($lang) . ',' . $db->quote('*') . ')');
		}

		$db->setQuery($query);

		try
		{
			$module = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			return '';
		}


		if (!count($module))
		{
			return '';
		}

		$module->user 	= '';
		$title			= $module->title;
		$content 		= $module->content;
		$id 			= $module->id;

		if (!is_object($module))
		{
			if (is_null($content))
			{
				return '';
			}
			else
			{
				$tmp = $module;
				$module = new stdClass;
				$module->params = null;
				$module->module = $tmp;
				$module->id = 0;
				$module->user = 0;
			}
		}

		if (!is_null($content))
		{
			$module->content = $content;
		}

		$params = new JRegistry;
		$params->loadString($module->params);
		$module->params = $params;
		$html = '<div class="jsn-tpl-mm-module-element-item module-'. $id . '">';
		$moduleHTML = $renderer->render($module, $params, $content);

		if (trim($moduleHTML) == "")
		{
			$html .= '<div class="alert alert-block">' . JText::sprintf('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_HAS_NO_CONTENT', $module->title) . '</div>';
		}
		else
		{
			$class = '';

			if (strtolower(@$module->module) == 'mod_menu')
			{
				$class = 'menu_element';
			}
			else
			{
				$class = strtolower(@$module->module);
			}

			$html .= '<div class="jsn-tpl-mm-module-element-item-content">';
			if ((string) $showModuleTitle == 'yes')
			{
				$html .= '<h3 class="module-title">' . $title . '</h3>';
			}
			$html .= '<div class="module-content jsn_tpl_mm_' . $class . '">';
			$html .= $moduleHTML;
			$html .= '</div>';
			$html .= '</div>';
		}

		$html .= '</div>';

		return $html;
	}
}