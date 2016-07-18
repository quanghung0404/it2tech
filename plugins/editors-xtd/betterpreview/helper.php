<?php
/**
 * Plugin Helper File
 *
 * @package         Better Preview
 * @version         4.1.2PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 ** Plugin that places the button
 */
class PlgButtonBetterPreviewHelper
{
	public function __construct(&$params)
	{
		$this->params = $params;
		$this->helper = new $params->class($params);
	}

	/**
	 * Display the button
	 *
	 * @return array A two element array of ( imageName, textToInsert )
	 */
	function render($name, $content)
	{
		$button = new JObject;

		if (JFactory::getApplication()->isSite())
		{
			return $button;
		}

		$url = $this->helper->getURL($name);
		if (!$url)
		{
			return $button;
		}

		$user    = JFactory::getUser();
		$session = JFactory::getSession();

		JHtml::stylesheet('nnframework/style.min.css', false, true);
		if ($itemId = $this->helper->getItemId($url))
		{
			$url .= '&Itemid=' . $itemId;
		}

		$fid    = uniqid('betterPreviewData_');
		$script = '
			function ' . $fid . '()
			{
				form = document.adminForm;
				text = ' . $content . '
				isjform = 1;
				overrides = { text: text };
				' . $this->helper->getExtraJavaScript($content) . '
				return {
					url: "' . JRoute::_(JUri::root() . $url) . '",
					user: ' . (int) $user->get('id', 0) . ',
					session_id: "' . $session->getId() . '",
					form: form,
					isjform: isjform,
					overrides: overrides
				};
			}
			';

		if ($this->params->button_primary)
		{
			$script .= '
				(function($){
					$(document).ready(function()
					{
						$(".icon-betterpreview").each(function(){
							if($(this).parent().hasClass("modal-button")) {
								$(this).parent().addClass("btn-primary");
							}
						});
					});
				})(jQuery);
				';
		}
		JFactory::getDocument()->addScriptDeclaration($script);

		$link = 'index.php?bp_preloader=1&tmpl=component&fid=' . $fid;

		$text = $this->getButtonText();

		$icon = $this->params->button_icon;
		if ($icon == 'betterpreview')
		{
			$icon = 'nonumber icon-' . $icon;
		}

		if (!defined('BETTERPREVIEW_INIT') && $this->params->display_toolbar_button)
		{
			JHTML::_('behavior.modal');

			define('BETTERPREVIEW_INIT', 1);
			// Generate html for toolbar button
			$html    = array();
			$html[]  = '<a href="' . $link . '" class="btn btn-small betterpreview_link modal' . ($this->params->button_primary ? ' btn-primary' : '') . '"'
				. ' rel="{handler: \'iframe\', size: {x:window.getSize().x-100, y: window.getSize().y-100}}">';
			$html[]  = '<span class="icon-' . $icon . '"></span> ';
			$html[]  = $text;
			$html[]  = '</a>';
			$toolbar = JToolBar::getInstance('toolbar');
			$toolbar->appendButton('Custom', implode('', $html));
		}

		if ($this->params->display_editor_button)
		{
			$button->modal   = true;
			$button->class   = 'btn';
			$button->link    = $link;
			$button->text    = $text;
			$button->name    = $icon;
			$button->options = "{handler: 'iframe', size: {x:window.getSize().x-100, y: window.getSize().y-100}}";
		}

		return $button;
	}

	function getButtonText()
	{
		$text = $this->params->button_text;

		if ($text == 'Preview')
		{
			return JText::_('BP_PREVIEW');
		}

		$text_ini = strtoupper(str_replace(' ', '_', $text));
		$text     = JText::_($text_ini);

		if ($text == $text_ini)
		{
			return trim(JText::_($this->params->button_text));
		}

		return trim($text);
	}
}
