<?php
/**
 * @package         Modals
 * @version         6.2.9PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class PlgSystemModalsHelperScripts
{
	var $helpers = array();
	var $params = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemModalsHelpers::getInstance();
		$this->params  = $this->helpers->getParams();
	}

	public function loadScriptsStyles(&$buffer)
	{
		if (JFactory::getApplication()->input->getInt('ml', 0))
		{
			$this->loadRedirectScript($buffer);

			return;
		}

		// Add scripts and styles
		$this->loadJQuery();

		JHtml::script('modals/jquery.touchSwipe.min.js', false, true);
		JHtml::script('modals/jquery.colorbox-min.js', false, true);
		JHtml::script('modals/script.min.js', false, true);

		$defaults   = $this->setDefaults();
		$defaults[] = "current: '" . JText::sprintf('MDL_MODALTXT_CURRENT', '{current}', '{total}') . "'";
		$defaults[] = "previous: '" . JText::_('MDL_MODALTXT_PREVIOUS') . "'";
		$defaults[] = "next: '" . JText::_('MDL_MODALTXT_NEXT') . "'";
		$defaults[] = "close: '" . JText::_('MDL_MODALTXT_CLOSE') . "'";
		$defaults[] = "xhrError: '" . JText::_('MDL_MODALTXT_XHRERROR') . "'";
		$defaults[] = "imgError: '" . JText::_('MDL_MODALTXT_IMGERROR') . "'";
		$script     = "
			var modals_class = '" . $this->params->class . "';
			var modals_disable_on_mobile = " . (int) $this->params->disable_on_mobile . ";
			var modals_mobile_max_width = " . (int) $this->params->mobile_max_width . ";
			var modals_open_by_url = '" . JFactory::getApplication()->input->get('modal') . "';
			var modals_defaults = { " . implode(',', $defaults) . " };
		";
		JFactory::getDocument()->addScriptDeclaration(';/* START: Modals scripts */ ' . preg_replace('#\n\s*#s', ' ', trim($script)) . ' /* END: Modals scripts */');

		if ($this->params->load_stylesheet)
		{
			JHtml::stylesheet('modals/' . $this->params->style . '.min.css', false, true);
		}
	}

	private function loadJQuery()
	{
		if (!$this->params->load_jquery)
		{
			return;
		}

		JHtml::_('jquery.framework');
	}

	private function loadRedirectScript(&$buffer)
	{
		if (!$this->params->add_redirect)
		{
			return;
		}

		// Add redirect script
		$script =
			";if( parent.location.href === window.location.href ) {
				loc = window.location.href.replace( /(\?|&)ml=1(&|$)/, '$1' );
				if(parent.location.href !== loc) {
					parent.location.href = loc;
				}
			}";

		if (JFactory::getApplication()->input->get('iframe'))
		{
			JFactory::getDocument()->addScriptDeclaration($script);

			return;
		}

		$buffer =
			'<script type="text/javascript">' . $script . '</script>'
			. $buffer;
	}

	private function setDefaults()
	{
		$keyvals = array(
			'transition'     => 'elastic',
			'speed'          => 300,
			'scalePhotos'    => true,
			'returnFocus'    => true,
			'fastIframe'     => true,
			'opacity'        => 0.9,
			'closeButton'    => true,
			'overlayClose'   => true,
			'escKey'         => true,
			'arrowKey'       => true,
			'width'          => '',
			'height'         => '',
			'initialWidth'   => 600,
			'initialHeight'  => 450,
			'maxWidth'       => false,
			'maxHeight'      => false,
			'fixed'          => false,
			'reposition'     => true,
			'top'            => false,
			'bottom'         => false,
			'left'           => false,
			'right'          => false,
			'preloading'     => true,
			'loop'           => true,
			'slideshow'      => false,
			'slideshowSpeed' => 2500,
			'slideshowAuto'  => true,
			'retinaImage'    => false,
			'retinaUrl'      => false,
			'retinaSuffix'   => '@2x.$1',
		);

		$defaults = array();
		foreach ($keyvals as $key => $default)
		{
			$param_key = strtolower($key);
			if (isset($this->params->{$param_key}) && $this->params->{$param_key} != $default)
			{
				$val = $this->params->{$param_key};
				if (in_array($param_key, $this->params->paramNamesBooleans))
				{
					$val = (!$val || $val == 'false') ? 'false' : 'true';
				}
				$defaults[] = $key . ": '" . $val . "'";
			}
		}

		return $defaults;
	}

	public function addTmpl(&$url, $iframe = 0)
	{
		$url = explode('#', $url, 2);

		if (strpos($url['0'], 'ml=1') === false)
		{
			$url['0'] .= (strpos($url['0'], '?') === false) ? '?ml=1' : '&amp;ml=1';
		}

		if ($iframe && strpos($url['0'], 'iframe=1') === false)
		{
			$url['0'] .= (strpos($url['0'], '?') === false) ? '?iframe=1' : '&amp;iframe=1';
		}

		$url = implode('#', $url);

		if (substr($url, 0, 4) != 'http' && strpos($url, 'index.php') === 0 && strpos($url, '/') === false)
		{
			$url = JRoute::_($url);
		}
	}
}
