<?php
/**
 * @package         Tabs
 * @version         5.1.10PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
if ($user->get('guest')
	|| (
		!$user->authorise('core.edit', 'com_content')
		&& !$user->authorise('core.create', 'com_content')
	)
)
{
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
$parameters = NNParameters::getInstance();
$params     = $parameters->getPluginParams('tabs');

if (JFactory::getApplication()->isSite() && !$params->enable_frontend)
{
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

$class = new PlgButtonTabsPopup($params);
$class->render();

class PlgButtonTabsPopup
{
	var $params = null;

	function __construct(&$params)
	{
		$this->params = $params;
	}

	function render()
	{
		jimport('joomla.filesystem.file');

		// Load plugin language
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('plg_system_nnframework');
		NNFrameworkFunctions::loadLanguage('plg_editors-xtd_tabs');
		NNFrameworkFunctions::loadLanguage('plg_system_tabs');

		JHtml::stylesheet('nnframework/style.min.css', false, true);
		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');

		// Tag character start and end
		list($tag_start, $tag_end) = explode('.', $this->params->tag_characters);

		$script = "
			var tabs_tag_open = '" . preg_replace('#[^a-z0-9-_]#s', '', $this->params->tag_open) . "';
			var tabs_tag_close = '" . preg_replace('#[^a-z0-9-_]#s', '', $this->params->tag_close) . "';
			var tabs_tag_delimiter = '" . (($this->params->tag_delimiter == '=') ? '=' : ' ') . "';
			var tabs_tag_characters = ['" . $tag_start . "', '" . $tag_end . "'];
			var tabs_editorname = '" . JFactory::getApplication()->input->getString('name', 'text') . "';
			var tabs_content_placeholder = '" . JText::_('TAB_TEXT', true) . "';
			var tabs_error_empty_title = '" . JText::_('TAB_ERROR_EMPTY_TITLE', true) . "';
			var tabs_max_count = " . (int) $this->params->button_max_count . ";
			var tabs_root = '" . JUri::root(true) . "';
		";
		JFactory::getDocument()->addScriptDeclaration($script);
		JHtml::stylesheet('tabs/popup.min.css', false, true);

		JHtml::script('tabs/popup.min.js', false, true);

		echo $this->getHTML();
	}

	function getHTML()
	{
		ob_start();
		include __DIR__ . '/popup.tmpl.php';
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
