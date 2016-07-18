<?php
/**
 * Plugin Helper File: Replace
 *
 * @package         Articles Anywhere
 * @version         4.1.5PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class PlgSystemArticlesAnywhereHelperReplace
{
	var $helpers = array();
	var $params = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemArticlesAnywhereHelpers::getInstance();
		$this->params  = $this->helpers->getParams();
		list($this->params->tag_character_start, $this->params->tag_character_end) = explode('.', $this->params->tag_characters);
	}

	public function replaceTags(&$string, $area = 'article')
	{
		if (!is_string($string) || $string == '')
		{
			return;
		}

		if (
			strpos($string, $this->params->tag_character_start . $this->params->article_tag) === false
			&& strpos($string, $this->params->tag_character_start . $this->params->articles_tag) === false
		)
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

				$this->helpers->get('protect')->protectTags($string);

				return;
			}

			$this->helpers->get('protect')->protect($string);

			$this->helpers->get('process')->removeAll($string, $area);

			NNProtect::unprotect($string);

			return;
		}

		$this->helpers->get('protect')->protect($string);

		$this->params->message = '';

		// COMPONENT
		if (NNFrameworkFunctions::isFeed())
		{
			$s      = '#(<item[^>]*>)#s';
			$string = preg_replace($s, '\1<!-- START: AA_COMPONENT -->', $string);
			$string = str_replace('</item>', '<!-- END: AA_COMPONENT --></item>', $string);
		}

		if (strpos($string, '<!-- START: AA_COMPONENT -->') === false)
		{
			$this->helpers->get('tag')->tagArea($string, 'component');
		}

		$components = $this->helpers->get('tag')->getAreaByType($string, 'component');

		foreach ($components as $component)
		{
			if (strpos($string, $component['0']) === false)
			{
				continue;
			}

			$this->helpers->get('process')->processArticles($component['1'], 'components');
			$string = str_replace($component['0'], $component['1'], $string);
		}

		// EVERYWHERE
		$this->helpers->get('process')->processArticles($string, 'other');

		NNProtect::unprotect($string);
	}
}
