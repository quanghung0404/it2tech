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

// Load common functions
require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/tags.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

NNFrameworkFunctions::loadLanguage('plg_system_tabs');

/**
 * Plugin that replaces stuff
 */
class PlgSystemTabsHelper
{
	var $params = null;
	var $helpers = array();

	public function __construct(&$params)
	{
		$this->params = $params;

		$this->params->comment_start = '<!-- START: Tabs -->';
		$this->params->comment_end   = '<!-- END: Tabs -->';

		$this->params->tag_open  = preg_replace('#[^a-z0-9-_]#si', '', $this->params->tag_open);
		$this->params->tag_close = preg_replace('#[^a-z0-9-_]#si', '', $this->params->tag_close);
		$this->params->tag_link  = preg_replace('#[^a-z0-9-_]#si', '', $this->params->tag_link);

		$disabled_components = is_array($this->params->disabled_components) ? $this->params->disabled_components : explode('|', $this->params->disabled_components);
		$this->params->disabled_components = array('com_acymailing');
		$this->params->disabled_components = array_merge($disabled_components, $this->params->disabled_components);

		
		$url                       = NNText::getURI();
		$this->params->cookie_name = 'nn_tabs_' . md5($url);

		require_once __DIR__ . '/helpers/helpers.php';
		$this->helpers = PlgSystemTabsHelpers::getInstance($this->params);
	}

	public function onContentPrepare(&$article, &$context, &$params)
	{
		$area    = isset($article->created_by) ? 'articles' : 'other';
		$context = (($params instanceof JRegistry) && $params->get('nn_search')) ? 'com_search.' . $params->get('readmore_limit') : $context;

		NNFrameworkHelper::processArticle($article, $context, $this, 'replaceTags', array($area, $context));
	}

	public function onAfterDispatch()
	{
		// only in html
		if (JFactory::getDocument()->getType() !== 'html' && !NNFrameworkFunctions::isFeed())
		{
			return;
		}

		$this->helpers->get('head')->addHeadStuff();

		$buffer = JFactory::getDocument()->getBuffer('component');

		if (empty($buffer) || is_array($buffer))
		{
			return;
		}

		$this->replaceTags($buffer, 'component');

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

		if (
			strpos($html, '{' . $this->params->tag_open) === false
			&& strpos($html, 'nn_tabs-scrollto') === false
		)
		{
			$this->helpers->get('head')->removeHeadStuff($html);

			$this->helpers->get('clean')->cleanLeftoverJunk($html);

			JResponse::setBody($html);

			return;
		}

		// only do stuff in body
		list($pre, $body, $post) = NNText::getBody($html);
		$this->replaceTags($body, 'body');
		$html = $pre . $body . $post;

		$this->helpers->get('clean')->cleanLeftoverJunk($html);

		JResponse::setBody($html);
	}

	public function replaceTags(&$string, $area = 'article', $context = '')
	{
		$this->helpers->get('replace')->replaceTags($string, $area, $context);
	}
}
