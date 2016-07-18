<?php
/**
 * @package         Tooltips
 * @version         4.1.5PRO
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
$params     = $parameters->getPluginParams('tooltips');

if (JFactory::getApplication()->isSite() && !$params->enable_frontend)
{
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

$class = new PlgButtonTooltipsPopup($params);
$class->render();

class PlgButtonTooltipsPopup
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
		NNFrameworkFunctions::loadLanguage('plg_editors-xtd_tooltips');
		NNFrameworkFunctions::loadLanguage('plg_system_tooltips');

		JHtml::stylesheet('nnframework/style.min.css', false, true);
		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');

		// Tag character start and end
		list($tag_start, $tag_end) = explode('.', $this->params->tag_characters);

		$script = "
			var tooltips_tag = '" . preg_replace('#[^a-z0-9-_]#s', '', $this->params->tag) . "';
			var tooltips_tag_characters = ['" . $tag_start . "', '" . $tag_end . "'];
			var tooltips_editorname = '" . JFactory::getApplication()->input->getString('name', 'text') . "';
			var tooltips_text_placeholder = '" . JText::_('TT_TEXT', true) . "';
			var tooltips_error_empty_image = '" . JText::_('TT_ERROR_EMPTY_IMAGE', true) . "';
			var tooltips_error_empty_text = '" . JText::_('TT_ERROR_EMPTY_TEXT', true) . "';
			var tooltips_root = '" . JUri::root(true) . "';
		";
		JFactory::getDocument()->addScriptDeclaration($script);
		JHtml::stylesheet('tooltips/popup.min.css', false, true);

		JHtml::script('tooltips/popup.min.js', false, true);

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
