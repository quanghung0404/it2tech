<?php
/**
 * Plugin Helper File: Image
 *
 * @package         Dummy Content
 * @version         2.1.2PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;



class PlgSystemDummyContentHelperImage
{
	var $helpers = array();
	var $params = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemDummyContentHelpers::getInstance();
		$this->params  = $this->helpers->getParams();
	}

	public function render(&$options)
	{
		$options->width  = isset($options->width) ? (int) $options->width : (int) $this->params->image_width;
		$options->height = isset($options->height) ? (int) $options->height : (int) $this->params->image_height;

		$title = isset($options->title) ? ' title="' . $options->title . '"' : '';
		$alt   = ' alt="' . (isset($options->alt) ? $options->alt : (isset($options->title) ? $options->title : '')) . '"';
		$class = 'dummycontent_image ' . (isset($options->class) ? $options->class : '');
		$float = isset($options->float) ? ' style="float:' . $options->float . ';"' : '';

		$image_service = $this->getService($options);
		$url           = $this->$image_service($options);

		// make the url unique
		$this->addToUrl($url, uniqid());

		return '<img src="' . $url . '" width="' . $options->width . '" height="' . $options->height . '"' . $alt . $title . 'class="' . trim($class) . '"' . $float . ' />';
	}

	private function addToUrl(&$url, $attribute)
	{
		if (empty($attribute))
		{
			return;
		}

		$url .= strpos($url, '?') === false ? '?' : '&';
		$url .= $attribute;
	}

	private function getText(&$options)
	{
		if (isset($options->text))
		{
			$options->text = trim($options->text);
			switch ($options->text)
			{
				case '':
				case 'none':
					return '+';

				case 'dimensions':
				case 'dimentions':
					return '';

				default:
					return $options->text;
			}
		}

		switch ($this->params->image_show_text)
		{
			case 'none':
				return '+';

			case 'dimensions':
				return '';

			default:
				return $this->params->image_text ?: '+';
		}
	}

	private function getService(&$options)
	{
		$image_service = isset($options->service) ? $options->service : $this->params->image_service;

		$image_service = strtolower($image_service);

		if (strpos($image_service, '.') !== false)
		{
			$image_service = substr($image_service, 0, strpos($image_service, '.'));
		}

		$image_service = method_exists($this, $image_service) ? $image_service : 'lorempixel';

		return $image_service;
	}

	private function lorempixel(&$options)
	{
		$options->category = isset($options->category) ? $options->category : $this->params->image_category_lorempixel;
		$options->color    = isset($options->color) ? $options->color : $this->params->image_color_scheme;

		$text = str_replace(array('.', '/', '\\', ';', '|'), '', $this->getText($options));

		if ($text == '')
		{
			$text = $options->width . ' x ' . $options->height;
		}
		if ($text == '+')
		{
			$text = '';
		}

		$url = 'http://lorempixel.com'
			. ($options->color ? '' : '/g')
			. '/' . $options->width . '/' . $options->height
			. (($options->category == 'none') ? '' : '/' . $options->category)
			. ($text ? '/' . $text : '');

		return $url;
	}

	private function placeimg(&$options)
	{
		$options->category = isset($options->category) ? $options->category : $this->params->image_category_placeimg;
		$options->color    = isset($options->color) ? $options->color : $this->params->image_color_scheme2;

		$url = 'https://placeimg.com'
			. '/' . $options->width . '/' . $options->height
			. '/' . $options->category
			. ($options->color == 'color' ? '' : '/' . $options->color);

		return $url;
	}

	private function placebeard(&$options)
	{
		$options->color           = isset($options->color) ? $options->color : $this->params->image_color_scheme;
		$options->show_dimensions = isset($options->show_dimensions) ? $options->show_dimensions : $this->params->image_show_dimensions;

		$url = 'http://placebeard.it'
			. ($options->color ? '' : '/g')
			. '/' . $options->width . '/' . $options->height
			. ($options->show_dimensions ? '' : '/notag');

		return $url;
	}

	/*private function placebear(&$options)
	{
		$url = 'http://placebear.com'
			. ($options->color ? '' : '/g')
			. '/' . $options->width . '/' . $options->height;

		return $url;
	}*/

	private function placeholder(&$options, $url = 'http://dummyimage.com')
	{
		$color      = $this->getColor($options);
		$text_color = $this->getForgroundColor($options);

		$text = $this->getText($options);

		$url .= '/' . $options->width . 'x' . $options->height
			. '/' . $color
			. '/' . $text_color
			. ($text ? '&text=' . $text : '');

		return $url;
	}

	private function dummyimage(&$options)
	{
		return $this->placeholder($options, 'http://dummyimage.com');
	}

	private function placehold(&$options)
	{
		return $this->placeholder($options, 'http://placehold.it');
	}

	private function placeholdus(&$options)
	{
		$url = 'http://placehold.us'
			. '/' . $options->width . '/' . $options->height;

		return $url;
	}

	private function fakeimg(&$options)
	{
		$color      = $this->getColor($options);
		$text_color = $this->getForgroundColor($options);

		$opacity = (isset($options->opacity) ? (int) $options->opacity : (int) $this->params->image_background_opacity);
		$opacity = $opacity == 100 ? '' : ',' . round($opacity * 2.55);

		$text_opacity = (isset($options->text_opacity) ? (int) $options->text_opacity : (int) $this->params->image_foreground_opacity);
		$text_opacity = $text_opacity == 100 ? '' : ',' . round($text_opacity * 2.55);

		$text = $this->getText($options);

		$font = isset($options->font) ? $options->font : $this->params->image_font;

		$url = 'http://fakeimg.pl'
			. '/' . $options->width . 'x' . $options->height
			. '/' . $color . $opacity
			. '/' . $text_color . $text_opacity;

		if ($text != '')
		{
			$this->addToUrl($url, 'text=' . $text);
		}

		$this->addToUrl($url, 'font=' . $font);

		return $url;
	}

	private function placeskull(&$options)
	{
		$color                    = $this->getColor($options);
		$options->show_dimensions = isset($options->show_dimensions) ? $options->show_dimensions : $this->params->image_show_dimensions;

		$url = 'http://placeskull.com'
			. '/' . $options->width . '/' . $options->height
			. '/' . $color
			. ($options->show_dimensions ? '/' . mt_rand(1, 45) . '/1' : '');

		return $url;
	}

	private function getColor(&$options)
	{
		if (isset($options->color) && $options->color == 'random')
		{
			return $this->getRandomColor();
		}

		if (isset($options->color) && $options->color != '0')
		{
			return $this->removeLeadingHash($options->color);
		}

		if ($this->params->image_background_color_random)
		{
			return $this->getRandomColor();
		}

		return $this->removeLeadingHash($this->params->image_background_color);
	}

	private function getForgroundColor(&$options)
	{
		if (isset($options->text_color))
		{
			return $this->removeLeadingHash($options->text_color);
		}

		return $this->removeLeadingHash($this->params->image_foreground_color);
	}

	private function getRandomColor()
	{
		$r = rand($this->params->image_background_color_random_start, $this->params->image_background_color_random_end);
		$g = rand($this->params->image_background_color_random_start, $this->params->image_background_color_random_end);
		$b = rand($this->params->image_background_color_random_start, $this->params->image_background_color_random_end);

		return dechex($r) . dechex($g) . dechex($b);
	}

	private function removeLeadingHash($string)
	{
		if (substr($string, 0, 1) != '#')
		{
			return $string;
		}

		return substr($string, 1);
	}
}
