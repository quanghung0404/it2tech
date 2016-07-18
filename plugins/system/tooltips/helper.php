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

// Load common functions
require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/tags.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

NNFrameworkFunctions::loadLanguage('plg_system_tooltips');

/**
 * Plugin that replaces stuff
 */
class PlgSystemTooltipsHelper
{
	var $params = null;

	public function __construct(&$params)
	{
		$this->params = $params;

		$this->params->comment_start = '<!-- START: Tooltips -->';
		$this->params->comment_end   = '<!-- END: Tooltips -->';

		$this->params->tag = trim($this->params->tag);
		$this->params->tag = preg_replace('#[^a-z0-9-_]#s', '', $this->params->tag);

		// Tag character start and end
		list($tag_start, $tag_end) = $this->getTagCharacters(true);

		$inside_tag = NNTags::getRegexInsideTag();
		$spaces     = NNTags::getRegexSpaces();

		$this->params->regex = '#'
			. $tag_start . preg_quote($this->params->tag, '#') . '(?P<tip>(?:' . $spaces . '|<)' . $inside_tag . ')' . $tag_end
			. '(?P<text>.*?)'
			. $tag_start . '/' . preg_quote($this->params->tag, '#') . $tag_end
			. '#s';

		$this->params->protected_tags = array(
			$this->params->tag_character_start . $this->params->tag,
		);

		$disabled_components = is_array($this->params->disabled_components) ? $this->params->disabled_components : explode('|', $this->params->disabled_components);
		$this->params->disabled_components = array('com_acymailing');
		$this->params->disabled_components = array_merge($disabled_components, $this->params->disabled_components);
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

		// do not load scripts/styles on print page
		if (!NNFrameworkFunctions::isFeed() && !JFactory::getApplication()->input->getInt('print', 0) && !JFactory::getApplication()->input->getInt('noscript', 0))
		{
			if ($this->params->load_bootstrap_framework)
			{
				JHtml::_('bootstrap.framework');
			}

			$script = '
				var tooltips_timeout = ' . ($this->params->use_timeout ? (int) $this->params->timeout : 0) . ';
				var tooltips_delay_hide = ' . ($this->params->delay_hide ? (int) $this->params->delay_hide : 0) . ';
				var delay_hide_touchscreen = ' . ($this->params->delay_hide_touchscreen ? (int) $this->params->delay_hide_touchscreen : 0) . ';
				var tooltips_use_auto_positioning = ' . ($this->params->use_auto_positioning ? (int) $this->params->use_auto_positioning : 0) . ';
				var tooltips_fallback_position = \'' . ($this->params->fallback_position ? $this->params->fallback_position : 'bottom') . '\';
			';
			JFactory::getDocument()->addScriptDeclaration('/* START: Tooltips scripts */ ' . preg_replace('#\n\s*#s', ' ', trim($script)) . ' /* END: Tooltips scripts */');
			JHtml::script('tooltips/script.min.js', false, true);
			if ($this->params->load_stylesheet)
			{
				JHtml::stylesheet('tooltips/style.min.css', false, true);
			}

			$styles = array();
			if ($this->params->color_link)
			{
				$styles['.nn_tooltips-link'][] = 'color: ' . $this->params->color_link;
			}
			if ($this->params->underline && $this->params->underline_color)
			{
				$styles['.nn_tooltips-link'][] = 'border-bottom: 1px ' . $this->params->underline . ' ' . $this->params->underline_color;
			}
			if ($this->params->max_width)
			{
				$styles['.nn_tooltips.popover'][] = 'max-width: ' . (int) $this->params->max_width . 'px';
			}
			if ($this->params->zindex)
			{
				$styles['.nn_tooltips.popover'][] = 'z-index: ' . (int) $this->params->zindex;
			}
			if ($this->params->border_color)
			{
				$styles['.nn_tooltips.popover'][]            = 'border-color: ' . $this->params->border_color;
				$styles['.nn_tooltips.popover.top .arrow'][] = 'border-top-color: ' . $this->params->border_color;
				$styles['.nn_tooltips.popover.left .arrow'][]   = 'border-left-color: ' . $this->params->border_color;
				$styles['.nn_tooltips.popover.right .arrow'][]  = 'border-right-color: ' . $this->params->border_color;
				$styles['.nn_tooltips.popover.bottom .arrow'][] = 'border-bottom-color: ' . $this->params->border_color;
			}
			if ($this->params->bg_color_text)
			{
				$styles['.nn_tooltips.popover'][]                  = 'background-color: ' . $this->params->bg_color_text;
				$styles['.nn_tooltips.popover.top .arrow:after'][] = 'border-top-color: ' . $this->params->bg_color_text;
				$styles['.nn_tooltips.popover.left .arrow:after'][]   = 'border-left-color: ' . $this->params->bg_color_text;
				$styles['.nn_tooltips.popover.right .arrow:after'][]  = 'border-right-color: ' . $this->params->bg_color_text;
				$styles['.nn_tooltips.popover.bottom .arrow:after'][] = 'border-bottom-color: ' . $this->params->bg_color_text;
			}
			if ($this->params->text_color)
			{
				$styles['.nn_tooltips.popover'][] = 'color: ' . $this->params->text_color;
			}
			if ($this->params->link_color)
			{
				$styles['.nn_tooltips.popover a'][] = 'color: ' . $this->params->link_color;
			}
			if ($this->params->bg_color_title)
			{
				$styles['.nn_tooltips.popover .popover-title'][] = 'background-color: ' . $this->params->bg_color_title;
			}
			if ($this->params->title_color)
			{
				$styles['.nn_tooltips.popover .popover-title'][] = 'color: ' . $this->params->title_color;
			}
			if (!empty($styles))
			{
				$style = array();
				foreach ($styles as $key => $vals)
				{
					$style[] = $key . ' {' . implode(';', $vals) . ';}';
				}
				JFactory::getDocument()->addStyleDeclaration('/* START: Tooltips styles */ ' . implode(' ', $style) . ' /* END: Tooltips styles */');
			}
		}

		$buffer = JFactory::getDocument()->getBuffer('component');

		if (empty($buffer) || is_array($buffer))
		{
			return;
		}

		if (strpos($buffer, $this->params->tag_character_start . $this->params->tag) === false)
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

		if (strpos($html, $this->params->tag_character_start . $this->params->tag) === false)
		{
			if (strpos($html, 'class="nn_tooltips-link') === false)
			{
				// remove style and script if no items are found
				$html = preg_replace('#\s*<' . 'link [^>]*href="[^"]*/(tooltips/css|css/tooltips)/[^"]*\.css[^"]*"[^>]* />#s', '', $html);
				$html = preg_replace('#\s*<' . 'script [^>]*src="[^"]*/(tooltips/js|js/tooltips)/[^"]*\.js[^"]*"[^>]*></script>#s', '', $html);
				$html = preg_replace('#((?:;\s*)?)(;?)/\* START: Tooltips .*?/\* END: Tooltips [a-z]* \*/\s*#s', '\1', $html);
			}

			$this->cleanLeftoverJunk($html);

			JResponse::setBody($html);

			return;
		}

		// only do stuff in body
		list($pre, $body, $post) = NNText::getBody($html);
		$this->replaceTags($body, 'body');
		$html = $pre . $body . $post;

		$this->cleanLeftoverJunk($html);

		JResponse::setBody($html);
	}

	public function replaceTags(&$string, $area = 'article', $context = '')
	{
		if (!is_string($string) || $string == '')
		{
			return;
		}

		// Check if tags are in the text snippet used for the search component
		if (strpos($context, 'com_search.') === 0)
		{
			$limit = explode('.', $context, 2);
			$limit = (int) array_pop($limit);

			$string_check = substr($string, 0, $limit);

			if (strpos($string_check, $this->params->tag_character_start . $this->params->tag) === false)
			{
				return;
			}
		}

		if (strpos($string, $this->params->tag_character_start . $this->params->tag) === false)
		{
			return;
		}

		// allow in component?
		if (
			($area == 'component' || ($area == 'article' && JFactory::getApplication()->input->get('option') == 'com_content'))
			&& in_array(JFactory::getApplication()->input->get('option'), $this->params->disabled_components)
		)
		{
			if (!$this->params->disable_components_remove)
			{
				$this->protectTags($string);

				return;
			}

			$this->protect($string);

			if (substr($this->params->regex, -1) != 'u' && @preg_match($this->params->regex . 'u', $string))
			{
				$this->params->regex .= 'u';
			}

			$string = preg_replace($this->params->regex, '\2', $string);

			NNProtect::unprotect($string);

			return;
		}

		$this->protect($string);

		list($pre_string, $string, $post_string) = NNText::getContentContainingSearches(
			$string,
			array(
				$this->params->tag_character_start . $this->params->tag,
			),
			array(
				$this->params->tag_character_start . '/' . $this->params->tag . $this->params->tag_character_end,
			)
		);

		if (substr($this->params->regex, -1) != 'u' && @preg_match($this->params->regex . 'u', $string))
		{
			$this->params->regex .= 'u';
		}

		preg_match_all($this->params->regex, $string, $matches, PREG_SET_ORDER);

		if (!empty($matches))
		{
			foreach ($matches as $match)
			{
				$tip  = $match['tip'];
				$text = $match['text'];

				$classes = str_replace('\|', '[:TT_BAR:]', $tip);
				$classes = explode('|', $classes);
				foreach ($classes as $i => $class)
				{
					$classes[$i] = trim(str_replace('[:TT_BAR:]', '|', $class));
				}
				$tip = array_shift($classes);

				$classes_popover = $classes;

				if (!in_array('click', $classes) && !in_array('sticky', $classes) && !in_array('hover', $classes))
				{
					$classes   = array_diff($classes, array('hover', 'sticky', 'click'));
					$classes[] = $this->params->mode;
				}

				$position = 'top';
				$position = $this->params->position;
				$position = in_array('left', $classes) ? 'left' : $position;
				$position = in_array('right', $classes) ? 'right' : $position;
				$position = in_array('top', $classes) ? 'top' : $position;
				$position = in_array('bottom', $classes) ? 'bottom' : $position;

				preg_match_all('#href="([^"]*)"#si', $tip, $url_matches, PREG_SET_ORDER);

				if (!empty($url_matches))
				{
					foreach ($url_matches as $url_match)
					{
						$url = 'href="' . JRoute::_($url_match['1']) . '"';
						$tip = str_replace($url_match['0'], $url, $tip);
					}
				}

				preg_match_all('#src="([^"]*)"#si', $tip, $url_matches, PREG_SET_ORDER);

				if (!empty($url_matches))
				{
					foreach ($url_matches as $url_match)
					{
						$url = $url_match['1'];
						if (strpos($url, 'http') !== 0)
						{
							$url = JUri::root() . $url;
						}
						$url = 'src="' . $url . '"';
						$tip = str_replace($url_match['0'], $url, $tip);
					}
				}

				$tip = explode('::', $this->makeSave($tip), 2);
				if (!isset($tip['1']))
				{
					$classes_popover[] = 'notitle';
					$title             = '';
					$content           = $tip['0'];
					if (preg_match('#^\s*(&lt;|<)img [^>]*(&gt;|>)\s*$#', $content))
					{
						$classes_popover[] = 'isimg';
					}
				}
				else
				{
					if (!$tip['1'])
					{
						$classes_popover[] = 'nocontent';
					}
					$title   = $tip['0'];
					$content = $tip['1'];
				}

				if (preg_match('#^\s*<img [^>]*>\s*$#', $text))
				{
					$classes[] = 'isimg';
				}

				$template = '<div class="popover nn_tooltips ' . implode(' ', $classes_popover) . '"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>';

				$r = '<span'
					. ' class="nn_tooltips-link ' . implode(' ', $classes) . '"'
					. ' data-toggle="popover"'
					. ' data-html="true"'
					. ' data-template="' . $this->makeSave($template) . '"'
					. ' data-placement="' . $position . '"'
					. ' data-content="' . $content . '"'
					. ' title="' . $title . '">' . $text . '</span>';

				if (in_array('isimg', $classes_popover))
				{
					// place the full image in a hidden span to make it pre-load it
					$r .= '<span style="display:none;">' . html_entity_decode($content) . '</span>';
				}
				$string = str_replace($match['0'], $r, $string);
			}
		}

		$string = $pre_string . $string . $post_string;

		NNProtect::unprotect($string);
	}

	private function makeSave($string)
	{
		if (strpos($string, '&lt;img') === false)
		{
			// convert & to html entities
			// If string contains an <img> tag, interpret as html
			$string = str_replace('&', '&amp;', $string);
		}

		return str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $string);
	}

	private function protect(&$string)
	{
		NNProtect::protectFields($string);
		NNProtect::protectSourcerer($string);
	}

	private function protectTags(&$string)
	{
		NNProtect::protectTags($string, $this->params->protected_tags);
	}

	private function unprotectTags(&$string)
	{
		NNProtect::unprotectTags($string, $this->params->protected_tags);
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	private function cleanLeftoverJunk(&$string)
	{
		$this->unprotectTags($string);

		NNProtect::removeInlineComments($string, 'Tooltips');
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
}
