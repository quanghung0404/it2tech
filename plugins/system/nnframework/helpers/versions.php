<?php
/**
 * @package         NoNumber Framework
 * @version         16.1.9037
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/functions.php';

class NNVersions
{
	public static $instance = null;

	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new NoNumberVersions;
		}

		return self::$instance;
	}
}

class NoNumberVersions
{
	/*
	 *  Deprecated. Use render()
	*/
	public function getMessage($alias = '', $xml = '', $version = '')
	{
		self::render($alias);
	}

	public static function render($alias)
	{
		if (!$alias)
		{
			return '';
		}

		require_once __DIR__ . '/functions.php';

		$name  = NNFrameworkFunctions::getNameByAlias($alias);
		$alias = NNFrameworkFunctions::getAliasByName($alias);

		if (!$version = self::getXMLVersion($alias))
		{
			return '';
		}

		JHtml::_('jquery.framework');

		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');
		$url    = 'download.nonumber.nl/extensions.xml?j=3&e=' . $alias;
		$script = "
			jQuery(document).ready(function() {
				nnScripts.loadajax(
					'" . $url . "',
					'nnScripts.displayVersion( data, \"" . $alias . "\", \"" . str_replace(array('FREE', 'PRO'), '', $version) . "\" )',
					'nnScripts.displayVersion( \"\" )',
					null, null, null, (60 * 60)
				);
			});
		";
		JFactory::getDocument()->addScriptDeclaration($script);

		return '<div class="alert alert-success" style="display:none;" id="nonumber_version_' . $alias . '">' . self::getMessageText($alias, $name, $version) . '</div>';
	}

	public static function getMessageText($alias, $name, $version)
	{
		list($url, $onclick) = self::getUpdateLink($alias, $version);

		$href    = $onclick ? '' : 'href="' . $url . '" target="_blank" ';
		$onclick = $onclick ? 'onclick="' . $onclick . '" ' : '';

		$is_pro  = strpos($version, 'PRO') !== false;
		$version = str_replace(array('FREE', 'PRO'), array('', ' <small>[PRO]</small>'), $version);

		$msg = '<div class="text-center">'
			. '<span class="ghosted">'
			. JText::sprintf('NN_NEW_VERSION_OF_AVAILABLE', JText::_($name))
			. '</span>'
			. '<br />'
			. '<a ' . $href . $onclick . ' class="btn btn-large btn-success">'
			. '<span class="icon-upload"></span> '
			. html_entity_decode(JText::sprintf('NN_UPDATE_TO', '<span id="nonumber_newversionnumber_' . $alias . '"></span>'), ENT_COMPAT, 'UTF-8')
			. '</a>';

		if (!$is_pro)
		{
			$msg .= ' <a href="https://www.nonumber.nl/purchase?ext=' . $alias . '" target="_blank" class="btn btn-large btn-primary">'
				. '<span class="icon-basket"></span> '
				. JText::_('NN_GO_PRO')
				. '</a>';
		}

		$msg .= '<br />'
			. '<span class="ghosted">'
			. '[ <a href="https://www.nonumber.nl/' . $alias . '#changelog" target="_blank">'
			. JText::_('NN_CHANGELOG')
			. '</a> ]'
			. '<br />'
			. JText::sprintf('NN_CURRENT_VERSION', $version)
			. '</span>'
			. '</div>';

		return html_entity_decode($msg, ENT_COMPAT, 'UTF-8');
	}

	public static function getUpdateLink($alias, $version)
	{
		$is_pro = strpos($version, 'PRO') !== false;

		if (!JFile::exists(JPATH_ADMINISTRATOR . '/components/com_nonumbermanager/nonumbermanager.xml'))
		{
			$url = $is_pro
				? 'https://www.nonumber.nl/' . $alias . '#download'
				: JRoute::_('index.php?option=com_installer&view=update');

			return array($url, '');
		}

		$config = JComponentHelper::getParams('com_nonumbermanager');

		$key = trim($config->get('key'));

		if ($is_pro && !$key)
		{
			return array('index.php?option=com_nonumbermanager', '');
		}

		JHtml::_('bootstrap.framework');
		JHtml::_('behavior.modal');
		jimport('joomla.filesystem.file');

		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');
		JFactory::getDocument()->addScriptDeclaration(
			"
			var NNEM_TIMEOUT = " . (int) $config->get('timeout', 5) . ";
			var NNEM_TOKEN = '" . JSession::getFormToken() . "';
		"
		);
		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nonumbermanager/js/script.min.js');

		$url = 'http://download.nonumber.nl?ext=' . $alias . '&j=3';

		if ($is_pro)
		{
			$url .= '&k=' . strtolower(substr($key, 0, 8) . md5(substr($key, 8)));
		}

		return array('', 'nnManager.openModal(\'update\', [\'' . $alias . '\'], [\'' . $url . '\'], true);');
	}

	/*
	 *  Deprecated. Use getFooter()
	*/
	public function getCopyright($name, $version, $jedid = 0, $element = 'nnframework', $type = 'system', $copyright = 1, $admin = 1)
	{
		return self::getFooter($name, $copyright);
	}

	public static function getFooter($name, $copyright = 1)
	{
		$html = array();

		$html[] = '<div class="nn_footer_extension">' . self::getFooterName($name) . '</div>';

		if ($copyright)
		{
			$html[] = '<div class="nn_footer_review">' . self::getFooterReview($name) . '</div>';
			$html[] = '<div class="nn_footer_logo">' . self::getFooterLogo() . '</div>';
			$html[] = '<div class="nn_footer_copyright">' . self::getFooterCopyright() . '</div>';
		}

		return '<div class="nn_footer">' . implode('', $html) . '</div>';
	}

	private static function getFooterName($name)
	{
		$name = JText::_($name);

		if (!$version = self::getXMLVersion($name))
		{
			return $name;
		}

		if (strpos($version, 'PRO') !== false)
		{
			return $name . ' v' . str_replace('PRO', '', $version) . ' <small>[PRO]</small>';
		}

		if (strpos($version, 'FREE') !== false)
		{
			return $name . ' v' . str_replace('FREE', '', $version) . ' <small>[FREE]</small>';
		}

		return $name . ' v' . $version;
	}

	private static function getFooterReview($name)
	{
		require_once __DIR__ . '/functions.php';

		$alias = NNFrameworkFunctions::getAliasByName($name);

		$jed_url = 'http://nonr.nl/jed-' . $alias . '#reviews';

		return
			html_entity_decode(
				JText::sprintf(
					'NN_JED_REVIEW',
					'<a href="' . $jed_url . '" target="_blank">',
					'</a>'
					. ' <a href="' . $jed_url . '" target="_blank" class="stars">'
					. '<span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span>'
					. '</a>'
				)
			);
	}

	private static function getFooterLogo()
	{
		return
			JText::sprintf(
				'NN_POWERED_BY',
				'<a href="https://www.nonumber.nl" target="_blank"><img src="' . JUri::root() . 'media/nnframework/images/logo.png" /></a>'
			);
	}

	private static function getFooterCopyright()
	{
		return JText::_('NN_COPYRIGHT') . ' &copy; ' . date('Y') . ' NoNumber ' . JText::_('NN_ALL_RIGHTS_RESERVED');
	}

	public static function getXMLVersion($alias, $urlformat = false, $type = 'component', $folder = 'system')
	{
		require_once __DIR__ . '/functions.php';

		if (!$version = NNFrameworkFunctions::getXMLValue('version', $alias, $type, $folder))
		{
			return '';
		}

		$version = trim($version);

		if (!$urlformat)
		{
			return $version;
		}

		return $version . '?v=' . strtolower(str_replace(array('FREE', 'PRO'), array('f', 'p'), $version));
	}

	public static function getPluginXMLVersion($alias, $folder = 'system')
	{
		return NoNumberVersions::getXMLVersion($alias, false, 'plugin', $folder);
	}
}
