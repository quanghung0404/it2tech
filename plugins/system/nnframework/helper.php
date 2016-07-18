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

require_once __DIR__ . '/helpers/functions.php';

/**
 * Helper NoNumber Quick Page stuff (nn_qp=1 in url)
 */
class PlgSystemNNFrameworkHelper
{
	function render()
	{
		$url = JFactory::getApplication()->input->getString('url', '');

		$func = new NNFrameworkFunctions;

		if ($url)
		{
			echo $func->getByUrl($url);

			die;
		}

		$allowed = array(
			'administrator/components/com_dbreplacer/ajax.php',
			'administrator/modules/mod_addtomenu/popup.php',
			'media/rereplacer/images/popup.php',
			'plugins/editors-xtd/articlesanywhere/popup.php',
			'plugins/editors-xtd/contenttemplater/popup.php',
			'plugins/editors-xtd/dummycontent/popup.php',
			'plugins/editors-xtd/modals/popup.php',
			'plugins/editors-xtd/modulesanywhere/popup.php',
			'plugins/editors-xtd/sliders/popup.php',
			'plugins/editors-xtd/snippets/popup.php',
			'plugins/editors-xtd/sourcerer/popup.php',
			'plugins/editors-xtd/tabs/popup.php',
			'plugins/editors-xtd/tooltips/popup.php',
			// old filenames
			'administrator/components/com_dbreplacer/dbreplacer.inc.php',
			'administrator/components/com_nonumbermanager/details.inc.php',
			'administrator/modules/mod_addtomenu/addtomenu.inc.php',
			'media/rereplacer/images/image.inc.php',
			'plugins/editors-xtd/articlesanywhere/articlesanywhere.inc.php',
			'plugins/editors-xtd/contenttemplater/contenttemplater.inc.php',
			'plugins/editors-xtd/dummycontent/dummycontent.inc.php',
			'plugins/editors-xtd/modulesanywhere/modulesanywhere.inc.php',
			'plugins/editors-xtd/snippets/snippets.inc.php',
			'plugins/editors-xtd/sourcerer/sourcerer.inc.php',
		);

		$file   = JFactory::getApplication()->input->getString('file', '');
		$folder = JFactory::getApplication()->input->getString('folder', '');

		if ($folder)
		{
			$file = implode('/', explode('.', $folder)) . '/' . $file;
		}

		if (!$file || in_array($file, $allowed) === false)
		{
			die;
		}

		jimport('joomla.filesystem.file');

		if (JFactory::getApplication()->isSite())
		{
			JFactory::getApplication()->setTemplate('../administrator/templates/isis');
		}

		$_REQUEST['tmpl'] = 'component';
		JFactory::getApplication()->input->set('option', 'com_content');

		header('Content-Type: text/html; charset=utf-8');
		JHtml::_('bootstrap.framework');
		JFactory::getDocument()->addScript(JUri::root(true) . '/administrator/templates/isis/js/template.js');
		JFactory::getDocument()->addStyleSheet(JUri::root(true) . '/administrator/templates/isis/css/template.css');

		JHtml::stylesheet('nnframework/popup.min.css', false, true);

		$file = JPATH_SITE . '/' . $file;

		$html = '';
		if (JFile::exists($file))
		{
			ob_start();
			include $file;
			$html = ob_get_contents();
			ob_end_clean();
		}

		JFactory::getDocument()->setBuffer($html, 'component');

		NNApplication::render();

		$html = JResponse::toString(JFactory::getApplication()->getCfg('gzip'));
		$html = preg_replace('#\s*<' . 'link [^>]*href="[^"]*templates/system/[^"]*\.css[^"]*"[^>]* />#s', '', $html);
		$html = preg_replace('#(<' . 'body [^>]*class=")#s', '\1nnpopup ', $html);
		$html = str_replace('<' . 'body>', '<' . 'body class="nnpopup"', $html);

		echo $html;

		die;
	}
}

class NNApplication
{
	static function render()
	{
		$app = JFactory::getApplication();

		$options = array();
		// Setup the document options.
		$options['template']  = $app->get('theme');
		$options['file']      = $app->get('themeFile', 'index.php');
		$options['params']    = $app->get('themeParams');
		$options['directory'] = self::getThemesDirectory();

		// Parse the document.
		JFactory::getDocument()->parse($options);

		// Trigger the onBeforeRender event.
		JPluginHelper::importPlugin('system');
		$app->triggerEvent('onBeforeRender');

		$caching = false;

		if ($app->isSite() && $app->get('caching') && $app->get('caching', 2) == 2 && !JFactory::getUser()->get('id'))
		{
			$caching = true;
		}

		// Render the document.
		$data = JFactory::getDocument()->render($caching, $options);

		// Set the application output data.
		$app->setBody($data);

		// Trigger the onAfterRender event.
		$app->triggerEvent('onAfterRender');

		// Mark afterRender in the profiler.
		// Causes issues, so commented out.
		// JDEBUG ? $app->profiler->mark('afterRender') : null;
	}

	static function getThemesDirectory()
	{
		if (JFactory::getApplication()->get('themes.base'))
		{
			return JFactory::getApplication()->get('themes.base');
		}

		if (defined('JPATH_THEMES'))
		{
			return JPATH_THEMES;
		}

		if (defined('JPATH_BASE'))
		{
			return JPATH_BASE . '/themes';
		}

		return __DIR__ . '/themes';
	}
}
