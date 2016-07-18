<?php
/**
 * @package         Sliders
 * @version         5.1.11PRO
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
$params     = $parameters->getPluginParams('sliders');

if (JFactory::getApplication()->isSite() && !$params->enable_frontend)
{
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

$class = new PlgButtonSlidersPopup($params);
$class->render();

class PlgButtonSlidersPopup
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
		NNFrameworkFunctions::loadLanguage('plg_editors-xtd_sliders');
		NNFrameworkFunctions::loadLanguage('plg_system_sliders');

		JHtml::stylesheet('nnframework/style.min.css', false, true);
		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');

		// Tag character start and end
		list($tag_start, $tag_end) = explode('.', $this->params->tag_characters);

		$script = "
			var sliders_tag_open = '" . preg_replace('#[^a-z0-9-_]#s', '', $this->params->tag_open) . "';
			var sliders_tag_close = '" . preg_replace('#[^a-z0-9-_]#s', '', $this->params->tag_close) . "';
			var sliders_tag_delimiter = '" . (($this->params->tag_delimiter == '=') ? '=' : ' ') . "';
			var sliders_tag_characters = ['" . $tag_start . "', '" . $tag_end . "'];
			var sliders_editorname = '" . JFactory::getApplication()->input->getString('name', 'text') . "';
			var sliders_content_placeholder = '" . JText::_('SLD_TEXT', true) . "';
			var sliders_error_empty_title = '" . JText::_('SLD_ERROR_EMPTY_TITLE', true) . "';
			var sliders_max_count = " . (int) $this->params->button_max_count . ";
			var sliders_root = '" . JUri::root(true) . "';
		";
		JFactory::getDocument()->addScriptDeclaration($script);
		JHtml::stylesheet('sliders/popup.min.css', false, true);

		JHtml::script('sliders/popup.min.js', false, true);

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
