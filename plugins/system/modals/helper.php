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

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/tags.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

NNFrameworkFunctions::loadLanguage('plg_system_modals');

/**
 * Plugin that replaces stuff
 */
class PlgSystemModalsHelper
{
	var $params = null;
	var $helpers = array();

	public function __construct(&$params)
	{
		$this->params = $params;

		$this->params->class = 'modal_link';
		// array_filter will remove any empty values
		$this->params->classnames = $this->params->autoconvert_classnames ? NNText::createArray(str_replace(' ', ',', trim($this->params->classnames))) : array();
		$this->params->classnames_images = $this->params->autoconvert_classnames_images ? NNText::createArray(str_replace(' ', ',', trim($this->params->classnames_images))) : array();
		$this->params->filetypes         = $this->params->autoconvert_filetypes ? NNText::createArray(str_replace(array(' ', '.'), '', $this->params->filetypes)) : array();
		$this->params->urls              = $this->params->autoconvert_urls ? NNText::createArray(str_replace("\r", '', $this->params->urls), "\n") : array();
		$this->params->auto_group_id     = uniqid('gallery_');

		$this->params->tag = preg_replace('#[^a-z0-9-_]#si', '', $this->params->tag);
		$this->params->tag_content = preg_replace('#[^a-z0-9-_]#si', '', $this->params->tag_content);

		$this->params->paramNamesCamelcase = array(
			'innerWidth', 'innerHeight', 'initialWidth', 'initialHeight', 'maxWidth', 'maxHeight', 'className',
			'scalePhotos', 'returnFocus', 'fastIframe',
			'closeButton', 'overlayClose', 'escKey', 'arrowKey', 'xhrError', 'imgError',
			'slideshowSpeed', 'slideshowAuto', 'slideshowStart', 'slideshowStop',
			'retinaImage', 'retinaUrl', 'retinaSuffix',
			'onOpen', 'onLoad', 'onComplete', 'onCleanup', 'onClosed',
		);
		$this->params->paramNamesLowercase = array_map('strtolower', $this->params->paramNamesCamelcase);
		$this->params->paramNamesBooleans  = array(
			'scalephotos', 'scrolling', 'inline', 'iframe', 'fastiframe',
			'photo', 'preloading', 'retinaimage', 'open', 'returnfocus', 'trapfocus', 'reposition',
			'loop', 'slideshow', 'slideshowauto', 'overlayclose', 'closebutton', 'esckey', 'arrowkey', 'fixed',
		);

		if (JFactory::getApplication()->input->getInt('ml', 0))
		{
			JFactory::getApplication()->input->set('tmpl', JFactory::getApplication()->input->getWord('tmpl', $this->params->tmpl));
		}

		$disabled_components = is_array($this->params->disabled_components) ? $this->params->disabled_components : explode('|', $this->params->disabled_components);
		$this->params->disabled_components = array('com_acymailing');
		$this->params->disabled_components = array_merge($disabled_components, $this->params->disabled_components);

		require_once __DIR__ . '/helpers/helpers.php';
		$this->helpers = PlgSystemModalsHelpers::getInstance($params);
	}

	public function onContentPrepare(&$article, &$context, &$params)
	{
		$area    = isset($article->created_by) ? 'articles' : 'other';
		$context = (($params instanceof JRegistry) && $params->get('nn_search')) ? 'com_search.' . $params->get('readmore_limit') : $context;

		NNFrameworkHelper::processArticle($article, $context, $this, 'replace', array($area, $context));
	}

	public function onAfterDispatch()
	{
		// only in html
		if (JFactory::getDocument()->getType() !== 'html'
			&& !NNFrameworkFunctions::isFeed()
		)
		{
			return;
		}

		$buffer = JFactory::getDocument()->getBuffer('component');

		if (empty($buffer) || is_array($buffer))
		{
			return;
		}

		// do not load scripts/styles on feed or print page
		if (!NNFrameworkFunctions::isFeed()
			&& !JFactory::getApplication()->input->getInt('print', 0)
		)
		{
			$this->helpers->get('scripts')->loadScriptsStyles($buffer);
		}

		$this->replace($buffer, 'component');

		JFactory::getDocument()->setBuffer($buffer, 'component');
	}

	public function onAfterRender()
	{
		// only in html and feeds
		if (JFactory::getDocument()->getType() !== 'html' && !NNFrameworkFunctions::isFeed())
		{
			return;
		}

		$html = JResponse::getBody();
		if ($html == '')
		{
			return;
		}

		// only do stuff in body
		list($pre, $body, $post) = NNText::getBody($html);
		$this->replace($body, 'body');

		if (strpos($body, $this->params->class) === false)
		{
			// remove style and script if no items are found
			$pre = preg_replace('#\s*<' . 'link [^>]*href="[^"]*/(modals/css|css/modals)/[^"]*\.css[^"]*"[^>]* />#s', '', $pre);
			$pre = preg_replace('#\s*<' . 'script [^>]*src="[^"]*/(modals/js|js/modals)/[^"]*\.js[^"]*"[^>]*></script>#s', '', $pre);
			$pre = preg_replace('#((?:;\s*)?)(;?)/\* START: Modals .*?/\* END: Modals [a-z]* \*/\s*#s', '\1', $pre);
		}

		$html = $pre . $body . $post;

		$this->cleanLeftoverJunk($html);

		JResponse::setBody($html);
	}

	public function replace(&$string, $area = 'article', $context = '')
	{
		$this->helpers->get('replace')->replace($string, $area, $context);
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	private function cleanLeftoverJunk(&$string)
	{
		$this->helpers->get('protect')->unprotectTags($string);

		NNProtect::removeFromHtmlTagContent($string, $this->params->protected_tags);
		NNProtect::removeInlineComments($string, 'Modals');
	}
}
