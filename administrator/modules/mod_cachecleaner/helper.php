<?php
/**
 * Module Helper File
 *
 * @package         Cache Cleaner
 * @version         4.2.3PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class ModCacheCleaner
{
	function modCacheCleaner()
	{
		// Load plugin parameters
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters   = NNParameters::getInstance();
		$this->params = $parameters->getPluginParams('cachecleaner');
	}

	function render()
	{
		if (!isset($this->params->display_link))
		{
			return;
		}

		// load the admin language file
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('mod_cachecleaner');

		JHtml::stylesheet('nnframework/style.min.css', false, true);
		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');

		$script = "
			var cachecleaner_base = '" . JUri::base(true) . "';
			var cachecleaner_root = '" . JUri::root() . "';
			var cachecleaner_msg_clean = '" . addslashes(html_entity_decode(JText::_('CC_CLEANING_CACHE'))) . "';
			var cachecleaner_msg_inactive = '" . addslashes(html_entity_decode(JText::sprintf('CC_SYSTEM_PLUGIN_NOT_ENABLED', '<a href=&quot;index.php?option=com_plugins&filter_type=system&filter_folder=system&search=cache cleaner&filter_search=cache cleaner&quot;>', '</a>'))) . "';
			var cachecleaner_msg_failure = '" . addslashes(html_entity_decode(JText::_('CC_CACHE_COULD_NOT_BE_CLEANED'))) . "';";
		JFactory::getDocument()->addScriptDeclaration($script);
		JHtml::stylesheet('cachecleaner/style.min.css', false, true);
		JHtml::script('cachecleaner/script.min.js', false, true);

		$text_ini = strtoupper(str_replace(' ', '_', $this->params->icon_text));
		$text     = JText::_($text_ini);
		if ($text == $text_ini)
		{
			$text = JText::_($this->params->icon_text);
		}

		if ($this->params->display_toolbar_button)
		{
			// Generate html for toolbar button
			$html    = array();
			$html[]  = '<a href="javascript:;" onclick="return false;"  class="btn btn-small cachecleaner_link">';
			$html[]  = '<span class="icon-nonumber icon-cachecleaner"></span> ';
			$html[]  = $text;
			$html[]  = '</a>';
			$toolbar = JToolBar::getInstance('toolbar');
			$toolbar->appendButton('Custom', implode('', $html));
		}

		// Generate html for status link
		$html   = array();
		$html[] = '<div class="btn-group cachecleaner">';
		$html[] = '<a href="javascript:;" onclick="return false;" class="cachecleaner_link">';

		if ($this->params->display_link != 'text')
		{
			$html[] = '<span class="icon-nonumber icon-cachecleaner"></span> ';
		}

		if ($this->params->display_link != 'icon')
		{
			$html[] = $text;
		}

		$html[] = '</a>';
		$html[] = '</div>';

		echo implode('', $html);
	}
}
