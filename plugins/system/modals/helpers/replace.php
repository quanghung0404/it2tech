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

class PlgSystemModalsHelperReplace
{
	var $helpers = array();

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemModalsHelpers::getInstance();
		$this->params  = $this->helpers->getParams();

		$this->params->tag = trim($this->params->tag);
		$this->params->tag_content = trim($this->params->tag_content);

		// Tag character start and end
		list($tag_start, $tag_end) = $this->getTagCharacters(true);

		// Break/paragraph start and end tags
		$this->params->breaks_start = NNTags::getRegexSurroundingTagPre();
		$this->params->breaks_end   = NNTags::getRegexSurroundingTagPost();
		$breaks_start               = $this->params->breaks_start;
		$breaks_end                 = $this->params->breaks_end;
		$spaces                     = NNTags::getRegexSpaces();
		$spaces_none                = NNTags::getRegexSpaces('*');
		$inside_tag                 = NNTags::getRegexInsideTag();

		$a_tag        = NNTags::getRegexTags('a', false, false);
		$spans_images = NNTags::getRegexTags(array('span', 'i', 'img'));
		$spans = NNTags::getRegexTags(array('span', 'i'));
		$image = NNTags::getRegexTags('img', false, false, 'class');
		$any_text = '[^<>]*';

		$this->params->regex = '#'
			. '(?P<start_pre>' . $breaks_start . ')'
			. $tag_start . $this->params->tag . $spaces . '(?P<data>' . $inside_tag . ')' . $tag_end
			. '(?P<start_post>' . $breaks_end . ')'

			. '(?P<pre>' . $breaks_start . ')'
			. '(?P<text>.*?)'
			. '(?P<post>' . $breaks_end . ')'

			. '(?P<end_pre>' . $breaks_start . ')'
			. $tag_start . '\/' . $this->params->tag . $tag_end
			. '(?P<end_post>' . $breaks_end . ')'
			. '#s';

		$this->params->regex_inlink = '#'
			. '(?P<link_start>' . $a_tag . ')'
			. '(?P<pre>' . $any_text . ')'

			. '(?P<image_pre>(?:' . $spans_images . $any_text . '){0,6})'

			. $tag_start . $this->params->tag . $spaces_none . '(?P<data>' . $inside_tag . ')' . $tag_end

			. '(?P<text>.*?)'

			. $tag_start . '\/' . $this->params->tag . $tag_end

			. '(?P<image_post>(?:' . $any_text . $spans_images . '){0,6})'

			. '(?P<post>' . $any_text . ')'
			. '(?P<link_end></a>)'
			. '#s';

		$this->params->regex_link = '#'
			. $a_tag
			. '#s';

		$this->params->regex_image = '#'
			. '(?P<link_start>(?:' . $a_tag . $any_text . '(?:' . $spans . $any_text . '){0,6})?)'
			. '(?P<image>' . $image . ')'
			. '(?P<link_end>(?:' . $any_text . '(?:' . $spans . $any_text . '){0,6}<\/a>)?)'
			. '#s';

		$this->params->regex_content = '#'
			. $breaks_start
			. $tag_start . $this->params->tag_content . '(?:\=|' . $spaces . ')+' . '(?P<id>' . $inside_tag . ')' . $tag_end
			. $breaks_end

			. '(?P<content>.*?)'

			. $breaks_start
			. $tag_start . '\/' . $this->params->tag_content . $tag_end
			. $breaks_end
			. '#s';
	}

	public function replace(&$string, $area = 'article', $context = '')
	{
		if ($area == 'article')
		{
			return;
		}

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

			if (
				strpos($string_check, $this->params->tag_character_start . $this->params->tag) === false
				&& strpos($string_check, $this->params->tag_character_start . $this->params->tag_content) === false
			)
			{
				return;
			}
		}

		NNProtect::removeFromHtmlTagAttributes(
			$string, array(
				$this->params->tag,
				$this->params->tag_content
			)
		);

		// allow in component?
		if (
			$area == 'component'
			&& in_array(JFactory::getApplication()->input->get('option'), $this->params->disabled_components)
		)
		{
			if (!$this->params->disable_components_remove)
			{
				$this->helpers->get('protect')->protectTags($string);

				return;
			}

			$this->helpers->get('protect')->protect($string);

			$string = preg_replace($this->params->regex, '\4', $string);

			NNProtect::unprotect($string);

			return;
		}

		$this->helpers->get('protect')->protect($string);

		// Handle content inside the iframed modal
		if (JFactory::getApplication()->input->getInt('ml', 0) && JFactory::getApplication()->input->getInt('iframe', 0))
		{
			$this->replaceInsideModal($string);

			NNProtect::unprotect($string);

			return;
		}

		$this->replaceLinks($string);

		// tag syntax inside links
		$this->replaceTagSyntaxInsideLinks($string);

		list($pre_string, $string, $post_string) = NNText::getContentContainingSearches(
			$string,
			array(
				$this->params->tag_character_start . $this->params->tag,
			),
			array(
				$this->params->tag_character_start . '/' . $this->params->tag . '}',
			)
		);

		// tag syntax
		$this->replaceTagSyntax($string);

		$string = $pre_string . $string . $post_string;

		// content tag
		$this->replaceContentTags($string);

		$this->replaceImages($string);

		NNProtect::unprotect($string);
	}

	// add ml to internal links
	private function replaceInsideModal(&$string)
	{
		$this->replaceTagSyntax($string);

		preg_match_all($this->params->regex_link, $string, $matches, PREG_SET_ORDER);

		if (empty($matches))
		{
			return;
		}

		foreach ($matches as $match)
		{
			// get the link attributes
			$attributes = $this->helpers->get('link')->getLinkAttributeList($match['0']);

			// ignore if the link has no href or is an anchor or has a target
			if (empty($attributes->href) || $attributes->href['0'] != '#' || isset($attributes->target))
			{
				continue;
			}

			// ignore if link is external or an image
			if ($this->helpers->get('file')->isExternal($attributes->href) || $this->helpers->get('file')->isMedia($attributes->href))
			{
				continue;
			}

			$href = $attributes->href;
			$this->helpers->get('scripts')->addTmpl($attributes->href, 1);
			$this->replaceOnce('href="' . $href . '"', 'href="' . $attributes->href . '"', $string);
		}
	}

	private function replaceTagSyntaxInsideLinks(&$string)
	{
		preg_match_all($this->params->regex_inlink, $string, $matches, PREG_SET_ORDER);

		if (empty($matches))
		{
			return;
		}

		foreach ($matches as $match)
		{
			$content = trim($match['image_pre'] . $match['text'] . $match['image_post']);

			list($link, $extra) = $this->helpers->get('link')->getLink($match['data'], $match['link_start'], $content);
			$link .= '</a>';

			$this->replaceOnce($match['0'], $link, $string, $extra);
		}
	}

	private function replaceTagSyntax(&$string)
	{
		preg_match_all($this->params->regex, $string, $matches, PREG_SET_ORDER);

		if (empty($matches))
		{
			return;
		}

		foreach ($matches as $match)
		{
			$tags = NNTags::cleanSurroundingTags(
				array(
					'end_pre'    => $match['end_pre'],
					'start_post' => $match['start_post'],
				)
			);
			$tags = NNTags::cleanSurroundingTags(
				array(
					'end_pre'    => $tags['end_pre'],
					'pre'        => $match['pre'],
					'post'       => $match['post'],
					'start_post' => $tags['start_post'],
				),
				array('p')
			);

			list($link, $extra) = $this->helpers->get('link')->getLink($match['data'], '', trim($tags['pre'] . $match['text'] . $tags['post']));

			$html = $match['start_pre'] . $tags['start_post']
				. $link . '</a>'
				. $tags['end_pre'] . $match['end_post'];

			$this->replaceOnce($match['0'], $html, $string, $extra);
		}
	}

	private function replaceLinks(&$string)
	{
		if (
			(
				empty($this->params->classnames)
				&& !preg_match('#class="[^"]*(' . implode('|', $this->params->classnames) . ')#s', $string)
			)
			&& !$this->params->external
			&& !$this->params->target
			&& empty($this->params->filetypes)
			&& empty($this->params->urls)
		)
		{
			return;
		}

		preg_match_all($this->params->regex_link, $string, $matches, PREG_SET_ORDER);

		if (empty($matches))
		{
			return;
		}

		foreach ($matches as $match)
		{
			$this->replaceLink($string, $match);
		}
	}

	private function replaceLink(&$string, $match)
	{
		// get the link attributes
		$attributes = $this->helpers->get('link')->getLinkAttributeList($match['0']);

		if (!$this->helpers->get('pass')->passLinkChecks($attributes))
		{
			return;
		}

		$data       = array();
		$isexternal = $this->helpers->get('file')->isExternal($attributes->href);
		$ismedia    = $this->helpers->get('file')->isMedia($attributes->href);
		$iframe     = $this->helpers->get('file')->isIframe($attributes->href, $data);

		// Force/overrule certain data values
		if ($iframe || ($isexternal && !$ismedia))
		{
			// use iframe mode for external urls
			$data['iframe'] = 'true';
			$this->helpers->get('data')->setDataWidthHeight($data, $isexternal);
		}

		$attributes->class = !empty($attributes->class) ? $attributes->class . ' ' . $this->params->class : $this->params->class;
		$link              = $this->helpers->get('link')->buildLink($attributes, $data);

		$this->replaceOnce($match['0'], $link, $string);
	}

	private function replaceContentTags(&$string)
	{
		preg_match_all($this->params->regex_content, $string, $matches, PREG_SET_ORDER);

		if (empty($matches))
		{
			return;
		}

		foreach ($matches as $match)
		{
			$this->replaceContentTag($string, $match);
		}
	}

	private function replaceContentTag(&$string, $match)
	{
		$id   = str_replace('#', '', $match['id']);
		$html = '<div style="display:none;"><div id="' . $id . '">' . $match['content'] . '</div></div>';

		$this->replaceOnce($match['0'], $html, $string);
	}

	private function replaceImages(&$string)
	{
		if (
			empty($this->params->classnames_images)
			|| !preg_match('#class="[^"]*(' . implode('|', $this->params->classnames_images) . ')#s', $string)
		)
		{
			return;
		}

		preg_match_all($this->params->regex_image, $string, $matches, PREG_SET_ORDER);

		if (empty($matches))
		{
			return;
		}

		jimport('joomla.filesystem.file');
		foreach ($matches as $match)
		{
			$this->replaceImage($string, $match);
		}
	}

	private function replaceImage(&$string, $match)
	{
		// Do nothing if the image is already surrounded by a link
		if (!empty($match['link_start']) || !empty($match['link_end']))
		{
			return;
		}

		// get the image attributes
		$image_attributes = $this->helpers->get('link')->getLinkAttributeList($match['image']);

		if (!isset($image_attributes->class) || !isset($image_attributes->src))
		{
			return;
		}

		$image_attributes->class = explode(' ', $image_attributes->class);

		if (!array_intersect($image_attributes->class, $this->params->classnames_images))
		{
			return;
		}

		$image_attributes->class = implode(' ', array_diff($image_attributes->class, $this->params->classnames_images));

		$image = (object) array(
			'path'      => $this->helpers->get('file')->getFilePath($image_attributes->src),
			'folder'    => $this->helpers->get('file')->getFilePath($image_attributes->src),
			'image'     => $this->helpers->get('file')->getFileName($image_attributes->src),
			'thumbnail' => $this->helpers->get('file')->getFileName($image_attributes->src),
		);
		unset($image_attributes->src);

		$params = (object) array(
			'thumbsuffix' => $this->params->gallery_thumb_suffix,
		);

		if (
			!$this->helpers->get('file')->isExternal($image->folder . '/' . $image->image)
			&& $check = $this->helpers->get('image')->getImageObject($image->folder, $image->image, $params)
		)
		{
			$image = $check;
		}

		$attributes = new stdClass;
		$data       = array();

		$attributes->href  = $image->folder . '/' . $image->image;
		$attributes->class = $this->params->class;

		$this->helpers->get('image')->setImageDataFromDataFile($image->folder, $data);
		$this->helpers->get('image')->setImageDataAtribute('title', $data, $image->image);
		$this->helpers->get('image')->setImageDataAtribute('description', $data, $image->image);

		$data['title'] = isset($image_attributes->title) ? $image_attributes->title : (isset($image_attributes->alt) ? $image_attributes->alt : '');
		if (!$data['title'] && $this->params->auto_titles)
		{
			// set the auto title
			$data['title'] = $this->helpers->get('file')->getTitle($image->image, $this->params->title_case);
		}
		$data['group'] = $this->params->auto_group_id;

		$link = array();

		$link[] = $this->helpers->get('link')->buildLink($attributes, $data);
		$link[] = '<img src="' . $image->folder . '/' . $image->thumbnail . '"' . $this->helpers->get('data')->flattenAttributeList($image_attributes) . ' />';
		$link[] = '</a>';

		$link = implode('', $link);

		$this->replaceOnce($match['image'], $link, $string);
	}

	private function replaceOnce($search, $replace, &$string, $extra = '')
	{
		if (!$extra
			|| !preg_match('#' . preg_quote($search, '#') . '(?P<post>.*?</(?:div|p)>)#', $string, $match)
		)
		{
			$string = NNText::strReplaceOnce($search, $replace . $extra, $string);

			return;
		}

		// Place the extra div stuff behind the first ending div/p tag
		$string = NNText::strReplaceOnce(
			$match['0'],
			$replace . $match['post'] . $extra,
			$string
		);
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
