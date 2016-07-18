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

class PlgSystemSlidersHelperHead
{
	var $helpers = array();
	var $params = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemSlidersHelpers::getInstance();
		$this->params  = $this->helpers->getParams();
	}

	public function addHeadStuff()
	{
		// do not load scripts/styles on feeds or print pages
		if (NNFrameworkFunctions::isFeed() || JFactory::getApplication()->input->getInt('print', 0))
		{
			return;
		}

		if ($this->params->load_bootstrap_framework)
		{
			JHtml::_('bootstrap.framework');
		}

		if ($this->params->use_cookies || $this->params->set_cookies)
		{
			JHtml::script('nnframework/jquery.cookie.min.js', false, true);
		}

		$script = '
			var nn_sliders_mode = \'' . $this->params->mode . '\';
			var nn_sliders_use_cookies = ' . (int) $this->params->use_cookies . ';
			var nn_sliders_set_cookies = ' . (int) $this->params->set_cookies . ';
			var nn_sliders_cookie_name = \'' . $this->params->cookie_name . '\';
			var nn_sliders_scroll = ' . (int) $this->params->scroll . ';
			var nn_sliders_linkscroll = ' . (int) $this->params->linkscroll . ';
			var nn_sliders_urlscroll = ' . (int) $this->params->urlscroll . ';
			var nn_sliders_scrolloffset = ' . (int) $this->params->scrolloffset . ';
			var nn_sliders_use_hash = ' . (int) $this->params->use_hash . ';
			var nn_sliders_reload_iframes = ' . (int) $this->params->reload_iframes . ';
			var nn_sliders_init_timeout = ' . (int) $this->params->init_timeout . ';
			';
		JFactory::getDocument()->addScriptDeclaration('/* START: Sliders scripts */ ' . preg_replace('#\n\s*#s', ' ', trim($script)) . ' /* END: Sliders scripts */');

		JHtml::script('sliders/script.min.js', false, true);

		switch ($this->params->load_stylesheet)
		{
			case 2:
				JHtml::stylesheet('sliders/old.min.css', false, true);
				break;

			case 1:
				JHtml::stylesheet('sliders/style.min.css', false, true);
				break;

			case 0:
			default:
				// Do not load styles
				break;
		}

		$style = '';
		if ($this->params->load_stylesheet != 2 && $this->params->slide_speed != 350)
		{
			$style .= '
				.nn_sliders.has_effects .collapse {
				  -webkit-transition-duration: ' . $this->params->slide_speed . 'ms;
				  -moz-transition-duration: ' . $this->params->slide_speed . 'ms;
				  -o-transition-duration: ' . $this->params->slide_speed . 'ms;
				  transition-duration: ' . $this->params->slide_speed . 'ms;
				}
			';
		}

		if ($this->params->scrolloffset)
		{
			$style .= '
				.nn_sliders-scroll {
					top: ' . $this->params->scrolloffset . 'px;
				}
			';
		}

		if (!$style)
		{
			return;
		}

		JFactory::getDocument()->addStyleDeclaration('/* START: Sliders styles */ ' . preg_replace('#\n\s*#s', ' ', trim($style)) . ' /* END: Sliders styles */');
	}

	public function removeHeadStuff(&$html)
	{
		// Don't remove if sliders id is found
		if (strpos($html, 'id="set-nn_sliders') !== false)
		{
			return;
		}

		// remove style and script if no items are found
		$html = preg_replace('#\s*<' . 'link [^>]*href="[^"]*/(sliders/css|css/sliders)/[^"]*\.css[^"]*"[^>]* />#s', '', $html);
		$html = preg_replace('#\s*<' . 'script [^>]*src="[^"]*/(sliders/js|js/sliders)/[^"]*\.js[^"]*"[^>]*></script>#s', '', $html);
		$html = preg_replace('#((?:;\s*)?)(;?)/\* START: Sliders .*?/\* END: Sliders [a-z]* \*/\s*#s', '\1', $html);
	}
}
