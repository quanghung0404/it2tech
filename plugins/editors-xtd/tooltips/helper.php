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

/**
 ** Plugin that places the button
 */
class PlgButtonTooltipsHelper
{
	public function __construct(&$params)
	{
		$this->params = $params;
	}

	/**
	 * Display the button
	 *
	 * @return array A two element array of ( imageName, textToInsert )
	 */
	function render($name)
	{
		$button = new JObject;

		if (JFactory::getApplication()->isSite() && !$this->params->enable_frontend)
		{
			return $button;
		}

		$user = JFactory::getUser();
		if ($user->get('guest')
			|| (
				!$user->authorise('core.edit', 'com_content')
				&& !$user->authorise('core.create', 'com_content')
			)
		)
		{
			return $button;
		}

		if ($this->params->button_use_simple_button)
		{
			return $this->renderSimpleButton($name);
		}

		return $this->renderButton($name);
	}

	private function renderButton($name)
	{
		JHtml::stylesheet('nnframework/style.min.css', false, true);

		$link = 'index.php?nn_qp=1'
			. '&folder=plugins.editors-xtd.tooltips'
			. '&file=popup.php'
			. '&name=' . $name;

		$button = new JObject;

		$button->modal   = true;
		$button->class   = 'btn';
		$button->link    = $link;
		$button->text    = $this->getButtonText();
		$button->name    = 'nonumber icon-tooltips';
		$button->options = "{handler: 'iframe', size: {x:Math.min(window.getSize().x-100, 620), y:Math.min(window.getSize().y-100, 790)}}";

		return $button;
	}

	private function renderSimpleButton($name)
	{
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('plg_editors-xtd_tooltips');

		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');
		JHtml::stylesheet('nnframework/style.min.css', false, true);

		$this->params->tag = preg_replace('#[^a-z0-9-_]#s', '', $this->params->tag);

		$text = $this->getExampleText();
		$text = str_replace('\\\\n', '\\n', addslashes($text));
		$text = str_replace('{', '{\'+\'', $text);

		$js = "
			function insertTooltips(editor) {
				selection = nnScripts.getEditorSelection(editor);
				selection = selection ? selection : '" . JText::_('TT_LINK', true) . "';

				text = '" . $text . "';
				text = text.replace('[:SELECTION:]', selection);

				jInsertEditorText(text, editor);
			}
		";
		JFactory::getDocument()->addScriptDeclaration($js);

		$button = new JObject;

		$button->modal   = false;
		$button->class   = 'btn';
		$button->link    = '#';
		$button->onclick = 'insertTooltips(\'' . $name . '\');return false;';
		$button->text    = $this->getButtonText();
		$button->name    = 'nonumber icon-tooltips';

		return $button;
	}

	private function getButtonText()
	{
		$text_ini = strtoupper(str_replace(' ', '_', $this->params->button_text));
		$text     = JText::_($text_ini);
		if ($text == $text_ini)
		{
			$text = JText::_($this->params->button_text);
		}

		return trim($text);
	}

	private function getExampleText()
	{
		switch (true)
		{
			case ($this->params->button_use_custom_code && $this->params->button_custom_code):
				return $this->getCustomText();
			default:
				return $this->getDefaultText();
		}
	}

	private function getDefaultText()
	{
		return '{' . $this->params->tag . ' ' . JText::_('TT_TITLE') . '::' . JText::_('TT_TEXT') . '}[:SELECTION:]{/' . $this->params->tag . '}';
	}

	private function getCustomText()
	{
		$text = trim($this->params->button_custom_code);
		$text = str_replace(array("\r", "\n"), array('', '</p>\n<p>'), trim($text)) . '</p>';
		$text = preg_replace('#^(.*?)</p>#', '\1', $text);
		$text = str_replace(array('{tip', '{/tip}'), array('{' . $this->params->tag, '{/' . $this->params->tag . '}'), trim($text));

		return $text;
	}
}
