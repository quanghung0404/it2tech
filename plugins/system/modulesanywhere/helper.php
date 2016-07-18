<?php
/**
 * Plugin Helper File
 *
 * @package         Modules Anywhere
 * @version         4.1.3PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/tags.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

NNFrameworkFunctions::loadLanguage('plg_system_modulesanywhere');

/**
 * Plugin that places modules
 */
class PlgSystemModulesAnywhereHelper
{
	var $option = '';
	var $params = null;
	var $aid = array();

	public function __construct(&$params)
	{
		$this->option = JFactory::getApplication()->input->get('option');

		$this->params                = $params;
		$this->params->comment_start = '<!-- START: Modules Anywhere -->';
		$this->params->comment_end   = '<!-- END: Modules Anywhere -->';
		$this->params->message_start = '<!--  Modules Anywhere Message: ';
		$this->params->message_end   = ' -->';
		$this->params->protect_start = '<!-- START: MA_PROTECT -->';
		$this->params->protect_end   = '<!-- END: MA_PROTECT -->';

		$this->params->module_tag    = trim($this->params->module_tag);
		$this->params->modulepos_tag = trim($this->params->modulepos_tag);

		$tags   = array();
		$tags[] = preg_quote($this->params->module_tag, '#');
		$tags[] = preg_quote($this->params->modulepos_tag, '#');
		if ($this->params->handle_loadposition)
		{
			$tags[] = 'loadposition';
		}

		$this->params->tags = '(' . implode('|', $tags) . ')';

		// Tag character start and end
		list($tag_start, $tag_end) = $this->getTagCharacters(true);

		$this->params->start_tags = $tags;
		array_walk(
			$this->params->start_tags,
			function (&$tag, $key, $tag_start)
			{
				$tag = $tag_start . $tag;
			},
			$this->params->tag_character_start
		);

		// Break/paragraph start and end tags
		$this->params->breaks_start = NNTags::getRegexSurroundingTagPre();
		$this->params->breaks_end   = NNTags::getRegexSurroundingTagPost();
		$breaks_start               = $this->params->breaks_start;
		$breaks_end                 = $this->params->breaks_end;
		$spaces                     = NNTags::getRegexSpaces();
		$inside_tag                 = NNTags::getRegexInsideTag();

		$this->params->regex = '#'
			. '(?P<start_div>(?:'
			. $breaks_start
			. $tag_start . 'div(?: ' . $inside_tag . ')?' . $tag_end
			. $breaks_end
			. '\s*)?)'

			. '(?P<pre>' . $breaks_start . ')'
			. $tag_start . '(?P<type>' . implode('|', $tags) . ')' . $spaces . '(?P<id>' . $inside_tag . ')' . $tag_end
			. '(?P<post>' . $breaks_end . ')'

			. '(?P<end_div>(?:\s*'
			. $breaks_start
			. $tag_start . '/div' . $tag_end
			. $breaks_end
			. ')?)'
			. '#s';

		$this->params->protected_tags = array();
		foreach ($tags as $tag)
		{
			$this->params->protected_tags[] = $this->params->tag_character_start . $tag;
		}

		$this->params->message = '';

		$this->aid = JFactory::getUser()->getAuthorisedViewLevels();

		$disabled_components = is_array($this->params->components) ? $this->params->components : explode('|', $this->params->components);
		$this->params->disabled_components = array('com_acymailing');
		$this->params->disabled_components = array_merge($disabled_components, $this->params->disabled_components);
	}

	public function onContentPrepare(&$article, &$context, &$params)
	{
		$this->params->message = '';

		if (!NNProtect::articlePassesSecurity($article, $this->params->articles_security_level))
		{
			$this->params->message = JText::_('MA_OUTPUT_REMOVED_SECURITY');
		}

		$area    = isset($article->created_by) ? 'articles' : 'other';
		$context = (($params instanceof JRegistry) && $params->get('nn_search')) ? 'com_search.' . $params->get('readmore_limit') : $context;

		NNFrameworkHelper::processArticle($article, $context, $this, 'processModules', array($area, $context));
	}

	public function onAfterDispatch()
	{
		// only in html
		if (JFactory::getDocument()->getType() !== 'html' && !NNFrameworkFunctions::isFeed())
		{
			return;
		}

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

		if (NNFrameworkFunctions::isFeed())
		{
			$this->replaceTags($html);
		}
		else
		{
			// only do stuff in body
			list($pre, $body, $post) = NNText::getBody($html);
			$this->replaceTags($body);
			$html = $pre . $body . $post;
		}

		$this->cleanLeftoverJunk($html);

		JResponse::setBody($html);
	}

	function replaceTags(&$string, $area = 'article')
	{
		if (!is_string($string) || $string == '')
		{
			return;
		}

		if (!NNText::stringContains($string, $this->params->start_tags))
		{
			return;
		}

		// allow in component?
		if (
			$area == 'component'
			&& in_array(JFactory::getApplication()->input->get('option'), $this->params->disabled_components)
		)
		{
			if (!$this->params->disable_components_remove)
			{

				$this->protectTags($string);

				return;
			}

			$this->protect($string);

			$this->removeAll($string, $area);

			NNProtect::unprotect($string);

			return;
		}

		$this->protect($string);

		// COMPONENT
		if (NNFrameworkFunctions::isFeed())
		{
			$s      = '#(<item[^>]*>)#s';
			$string = preg_replace($s, '\1<!-- START: MODA_COMPONENT -->', $string);
			$string = str_replace('</item>', '<!-- END: MODA_COMPONENT --></item>', $string);
		}

		if (strpos($string, '<!-- START: MODA_COMPONENT -->') === false)
		{
			$this->tagArea($string, 'MODA', 'component');
		}

		$this->params->message = '';
		$components = $this->params->components;
		if (!is_array($components))
		{
			$components = explode('|', $components);
		}

		if (in_array($this->option, $components))
		{
			// For all components that are selected, set the message
			$this->params->message = JText::_('MA_OUTPUT_REMOVED_NOT_ENABLED');
		}

		$components = $this->getTagArea($string, 'MODA', 'component');

		foreach ($components as $component)
		{
			if (strpos($string, $component['0']) === false)
			{
				continue;
			}

			$this->processModules($component['1'], 'components');
			$string = str_replace($component['0'], $component['1'], $string);
		}

		// EVERYWHERE
		$this->processModules($string, 'other');

		NNProtect::unprotect($string);
	}

	function tagArea(&$string, $ext = 'EXT', $area = '')
	{
		if (!$string || !$area)
		{
			return;
		}

		$string = '<!-- START: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->' . $string . '<!-- END: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->';

		if ($area == 'article_text')
		{
			$string = preg_replace('#(<hr class="system-pagebreak".*?/>)#si', '<!-- END: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->\1<!-- START: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->', $string);
		}
	}

	function getTagArea(&$string, $ext = 'EXT', $area = '')
	{
		if (!$string || !$area)
		{
			return array();
		}

		$start   = '<!-- START: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->';
		$end     = '<!-- END: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->';
		$matches = explode($start, $string);
		array_shift($matches);
		foreach ($matches as $i => $match)
		{
			list($text) = explode($end, $match, 2);
			$matches[$i] = array(
				$start . $text . $end,
				$text,
			);
		}

		return $matches;
	}

	public function removeAll(&$string, $area = 'articles')
	{
		$this->params->message = JText::_('MA_OUTPUT_REMOVED_NOT_ENABLED');
		$this->processModules($string, $area);
	}

	function processModules(&$string, $area = 'articles', $context = '')
	{
		// Check if tags are in the text snippet used for the search component
		if (strpos($context, 'com_search.') === 0)
		{
			$limit = explode('.', $context, 2);
			$limit = (int) array_pop($limit);

			$string_check = substr($string, 0, $limit);

			if (!NNText::stringContains($string_check, $this->params->start_tags))
			{
				return;
			}
		}

		if (!NNText::stringContains($string, $this->params->start_tags))
		{
			return;
		}

		if (
			$area == 'articles' && !$this->params->articles_enable
			|| $area == 'components' && !$this->params->components_enable
			|| $area == 'other' && !$this->params->other_enable
		)
		{
			$this->params->message = JText::_('MA_OUTPUT_REMOVED_NOT_ENABLED');
		}

		jimport('joomla.application.module.helper');

		if (!NNFrameworkFunctions::isFeed())
		{
			JPluginHelper::importPlugin('content');
		}

		$this->replace($string, $area);
	}

	function replace(&$string, $area = 'articles')
	{
		list($pre_string, $string, $post_string) = NNText::getContentContainingSearches(
			$string,
			$this->params->start_tags,
			null, 200, 500
		);

		if ($string == '' || !NNText::stringContains($string, $this->params->start_tags))
		{
			$string = $pre_string . $string . $post_string;

			return;
		}

		$regex = $this->params->regex;

		if (@preg_match($regex . 'u', $string))
		{
			$regex .= 'u';
		}

		$matches  = array();
		$protects = array();

		if (!preg_match_all($regex, $string, $matches, PREG_SET_ORDER))
		{
			$string = $pre_string . $string . $post_string;

			return;
		}

		foreach ($matches as $match)
		{
			if (strpos($string, $match['0']) === false)
			{
				continue;
			}

			if ($this->processMatch($string, $match, $area))
			{
				continue;
			}

			$protected  = $this->params->protect_start . base64_encode($match['0']) . $this->params->protect_end;
			$string     = str_replace($match['0'], $protected, $string);
			$protects[] = array($match['0'], $protected);
		}

		unset($matches);

		foreach ($protects as $protect)
		{
			if (strpos($string, $protect['1']) === false)
			{
				continue;
			}

			$string = str_replace($protect['1'], $protect['0'], $string);
		}

		$string = $pre_string . $string . $post_string;
	}

	function processMatch(&$string, &$data, $area = 'articles')
	{
		if (!empty($this->params->message))
		{
			$html = '';

			if ($this->params->place_comments)
			{
				$html = $this->params->message_start . $this->params->message . $this->params->message_end;
			}

			$string = str_replace($data['0'], $html, $string);

			return true;
		}

		$type = trim($data['type']);
		$id   = trim($data['id']);

		// The core loadposition tag supports chrome after a comma. Modules Anywhere uses a bar.
		if ($type == 'loadposition')
		{
			$id = str_replace(',', '|', $id);
		}

		$chrome     = '';
		$forcetitle = 0;

		$ignores   = array();
		$overrides = array();

		$vars = str_replace('\|', '[:MA_BAR:]', $id);
		$vars = explode('|', $vars);
		$id   = array_shift($vars);

		foreach ($vars as $var)
		{
			$var = trim(str_replace('[:MA_BAR:]', '|', $var));

			if (!$var)
			{
				continue;
			}

			if (strpos($var, '=') === false)
			{
				if ($this->params->override_style)
				{
					$chrome = $var;
				}

				continue;
			}

			if ($type != $this->params->module_tag)
			{
				continue;
			}

			list($key, $val) = explode('=', $var, 2);
			$val = str_replace(array('\{', '\}'), array('{', '}'), $val);

			switch ($key)
			{
				case 'style':
					$chrome = $val;
					break;

				case 'ignore_access':
				case 'ignore_state':
				case 'ignore_assignments':
				case 'ignore_caching':
					$ignores[$key] = $val;
					break;

				case 'showtitle':
					$overrides['showtitle'] = $val;
					$forcetitle             = $val;
					break;

				default:
					if ($this->params->override_settings)
					{
						$overrides[$key] = html_entity_decode($val);
					}
					break;
			}
		}

		if ($type == $this->params->module_tag)
		{
			if (!$chrome)
			{
				$chrome = ($forcetitle && $this->params->style == 'none') ? 'xhtml' : $this->params->style;
			}

			// module
			$html = $this->processModule($id, $chrome, $ignores, $overrides, $area);

			if ($html == 'MA_IGNORE')
			{
				return false;
			}
		}
		else
		{
			if (!$chrome)
			{
				$chrome = ($forcetitle) ? 'xhtml' : '';
			}

			// module position
			$html = $this->processPosition($id, $chrome);
		}

		list($start_div, $end_div) = $this->getDivTags($data);

		$tags = NNTags::cleanSurroundingTags(array(
			'start_div_pre'  => $start_div['pre'],
			'start_div_post' => $start_div['post'],
			'pre'            => $data['pre'],
			'post'           => $data['post'],
			'end_div_pre'    => $end_div['pre'],
			'end_div_post'   => $end_div['post'],
		));

		$html = $tags['start_div_pre'] . $start_div['tag'] . $tags['start_div_post']
			. $tags['pre'] . $html . $tags['post']
			. $tags['end_div_pre'] . $end_div['tag'] . $tags['end_div_post'];

		if ($this->params->place_comments)
		{
			$html = $this->params->comment_start . $html . $this->params->comment_end;
		}

		$string = str_replace($data['0'], $html, $string);
		unset($data);

		return $id;
	}

	function processPosition($position, $chrome = 'none')
	{
		$renderer = JFactory::getDocument()->loadRenderer('module');

		$html = array();
		foreach (JModuleHelper::getModules($position) as $module)
		{
			$module_html = $renderer->render($module, array('style' => $chrome));

			if ($this->params->show_edit)
			{
				$this->addFrontendEditing($module, $module_html);
			}

			$html[] = $module_html;
		}

		return implode('', $html);
	}

	function processModule($id, $chrome = '', $ignores = array(), $overrides = array(), $area = 'articles')
	{
		$ignore_access      = isset($ignores['ignore_access']) ? $ignores['ignore_access'] : $this->params->ignore_access;
		$ignore_state       = isset($ignores['ignore_state']) ? $ignores['ignore_state'] : $this->params->ignore_state;
		$ignore_assignments = isset($ignores['ignore_assignments']) ? $ignores['ignore_assignments'] : $this->params->ignore_assignments;
		$ignore_caching     = isset($ignores['ignore_caching']) ? $ignores['ignore_caching'] : $this->params->ignore_caching;

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('m.*')
			->from('#__modules AS m')
			->where('m.client_id = 0');
		if (is_numeric($id))
		{
			$query->where('m.id = ' . (int) $id);
		}
		else
		{
			$query->where('m.title = ' . $db->quote(NNText::html_entity_decoder($id)));
		}
		if (!$ignore_access)
		{
			$query->where('m.access IN (' . implode(',', $this->aid) . ')');
		}
		if (!$ignore_state)
		{
			$query->where('m.published = 1')
				->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id')
				->where('e.enabled = 1');
		}
		if (!$ignore_assignments)
		{
			$date     = JFactory::getDate();
			$now      = $date->toSql();
			$nullDate = $db->getNullDate();
			$query->where('(m.publish_up = ' . $db->quote($nullDate) . ' OR m.publish_up <= ' . $db->quote($now) . ')')
				->where('(m.publish_down = ' . $db->quote($nullDate) . ' OR m.publish_down >= ' . $db->quote($now) . ')');
		}
		$query->order('m.ordering');
		$db->setQuery($query);
		$module = $db->loadObject();

		if ($module && !$ignore_assignments)
		{
			$this->applyAssignments($module);
		}

		if (empty($module))
		{
			if ($this->params->place_comments)
			{
				return $this->params->message_start . JText::_('MA_OUTPUT_REMOVED_NOT_PUBLISHED') . $this->params->message_end;
			}

			return '';
		}

		//determine if this is a custom module
		$module->user = (substr($module->module, 0, 4) == 'mod_') ? 0 : 1;

		// set style
		$module->style = $chrome ?: 'none';

		$params = new stdClass;
		if (!empty($module->params))
		{
			$params = substr(trim($module->params), 0, 1) == '{'
				? json_decode($module->params)
				// Old ini style. Needed for crappy old style modules like swMenuPro
				: JRegistryFormat::getInstance('INI')->stringToObject($module->params);
		}

		if (!empty($overrides))
		{
			// override module parameters
			foreach ($overrides as $key => $value)
			{
				if (isset($module->{$key}))
				{
					$module->{$key} = $value;
					continue;
				}

				if ($value && $value['0'] == '[' && $value[strlen($value) - 1] == ']')
				{
					$value          = json_decode('{"val":' . $value . '}');
					$params->{$key} = $value->val;
					continue;
				}

				if (isset($params->{$key}) && is_array($params->{$key}))
				{
					$params->{$key} = explode(',', $value);
					continue;
				}

				$params->{$key} = $value;
			}
		}

		if (isset($module->access) && !in_array($module->access, $this->aid))
		{
			if ($this->params->place_comments)
			{
				return $this->params->message_start . JText::_('MA_OUTPUT_REMOVED_ACCESS') . $this->params->message_end;
			}

			return '';
		}

		// Set style in params to override the chrome override in module settings
		if ($chrome)
		{
			if (is_null($params))
			{
				$params = new stdClass;
			}

			if (isset($params->style) && $pos = strrpos($params->style, '-'))
			{
				// Get part before the last '-'
				$params->style = substr($params->style, 0, $pos);
			}

			$params->style = isset($params->style) && $params->style ? $params->style . '-' . $chrome : $chrome;
		}

		$module->params  = json_encode($params);
		$document        = clone(JFactory::getDocument());
		$document->_type = 'html';
		$renderer        = $document->loadRenderer('module');
		$html            = $renderer->render($module, array('style' => $module->style, 'name' => ''));

		$show_edit = isset($overrides['show_edit']) ? $overrides['show_edit'] : $this->params->show_edit;
		if ($show_edit)
		{
			$this->addFrontendEditing($module, $html);
		}

		// don't return html on article level when caching is set
		if (
			$area == 'articles'
			&& !$ignore_caching
			&& (
				(isset($params->cache) && !$params->cache)
				|| (isset($params->owncache) && !$params->owncache) // for stupid modules like RAXO that mess about with default params
			)
		)
		{
			return 'MA_IGNORE';
		}

		return $html;
	}

	function addFrontendEditing(&$module, &$html)
	{
		if (
			trim($html) == ''
			|| !JFactory::getApplication()->isSite()
			|| !JFactory::getUser()->id
			|| !JFactory::getUser()->authorise('core.edit', 'com_modules')
			|| !JFactory::getUser()->authorise('core.edit', 'com_modules.module.' . $module->id)
		)
		{
			return;
		}

		if (!$frontediting = JFactory::getApplication()->get('frontediting', 1))
		{
			return;
		}

		$displayData = array('moduleHtml' => &$html, 'module' => $module, 'position' => '[:REMOVE:]', 'menusediting' => ($frontediting == 2));
		JLayoutHelper::render('joomla.edit.frontediting_modules', $displayData);

		$position_tip = htmlspecialchars('<br />' . sprintf(JText::_('JLIB_HTML_EDIT_MODULE_IN_POSITION'), '[:REMOVE:]'));
		$html         = str_replace($position_tip, '', $html);
	}

	function applyAssignments(&$module)
	{
		$this->setModulePublishState($module);

		if (empty($module->published))
		{
			$module = null;
		}
	}

	function setModulePublishState(&$module)
	{
		$module->published = true;

		// for old Advanced Module Manager versions
		if (function_exists('PlgSystemAdvancedModulesPrepareModuleList'))
		{
			$modules = array($module->id => $module);
			PlgSystemAdvancedModulesPrepareModuleList($modules);
			$module = array_shift($modules);

			return;
		}

		// for new Advanced Module Manager versions
		if (class_exists('PlgSystemAdvancedModuleHelper'))
		{
			$modules = array($module->id => $module);
			$helper  = new PlgSystemAdvancedModuleHelper;
			$helper->onPrepareModuleList($modules);
			$module = array_shift($modules);

			return;
		}

		// for core Joomla
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('mm.moduleid')
			->from('#__modules_menu AS mm')
			->where('mm.moduleid = ' . (int) $module->id)
			->where('(mm.menuid = ' . ((int) JFactory::getApplication()->input->getInt('Itemid')) . ' OR mm.menuid <= 0)');
		$db->setQuery($query);
		$result            = $db->loadResult();
		$module->published = !empty($result);
	}

	function protect(&$string)
	{
		NNProtect::protectFields($string);
		NNProtect::protectSourcerer($string);
	}

	function protectTags(&$string)
	{
		NNProtect::protectTags($string, $this->params->protected_tags);
	}

	function unprotectTags(&$string)
	{
		NNProtect::unprotectTags($string, $this->params->protected_tags);
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	function cleanLeftoverJunk(&$string)
	{
		$this->unprotectTags($string);

		$string = preg_replace('#<\!-- (START|END): MODA_[^>]* -->#', '', $string);

		if ($this->params->place_comments)
		{
			return;
		}

		$string = str_replace(
			array(
				$this->params->comment_start, $this->params->comment_end,
				htmlentities($this->params->comment_start), htmlentities($this->params->comment_end),
				urlencode($this->params->comment_start), urlencode($this->params->comment_end),
			), '', $string
		);
		$string = preg_replace('#' . preg_quote($this->params->message_start, '#') . '.*?' . preg_quote($this->params->message_end, '#') . '#', '', $string);
	}

	public function getTagCharacters($quote = false)
	{
		if (!isset($this->params->tag_character_start))
		{
			list($this->params->tag_character_start, $this->params->tag_character_end) = explode('.', $this->params->tag_characters);
		}

		$start = $this->params->tag_character_start;
		$end   = $this->params->tag_character_end;

		if ($quote)
		{
			$start = preg_quote($start, '#');
			$end   = preg_quote($end, '#');
		}

		return array($start, $end);
	}

	private function getDivTags($data)
	{
		list($tag_start, $tag_end) = $this->getTagCharacters(true);

		return NNTags::getDivTags($data['start_div'], $data['end_div'], $tag_start, $tag_end);
	}
}
