<?php
/**
 * Module Helper File
 *
 * @package         Add to Menu
 * @version         4.0.4PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class ModAddToMenu
{
	function modAddToMenu(&$params)
	{
		// Load plugin parameters
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();
		$this->params = $parameters->getModuleParams('addtomenu', 1, $params);
	}

	function render()
	{
		if (!isset($this->params->display_link))
		{
			return;
		}

		$option = JFactory::getApplication()->input->get('option');

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		$func = new NNFrameworkFunctions;

		$folder = JPATH_ADMINISTRATOR . '/components/' . $option . '/addtomenu';
		if (!JFolder::exists($folder))
		{
			$folder = JPATH_ADMINISTRATOR . '/modules/mod_addtomenu/components/' . $option;
		}

		$comp_file = '';
		$template = '';
		$vars = array();

		foreach (JFolder::files($folder, '.xml') as $filename)
		{
			$file = $folder . '/' . $filename;

			$xml = $func->xmlToObject($file, 'params');
			if (isset($xml->required) && (empty($xml->required) || self::checkRequiredFields($xml->required, $vars)))
			{
				$comp_file = JFile::stripExt($filename);
				$template = $xml;
				break;
			}
		}

		if (!$comp_file)
		{
			return;
		}

		$opt = $option;
		// load the admin language file
		if ($opt == 'com_categories')
		{
			$opt = JFactory::getApplication()->input->get('extension', 'com_content');
		}
		$lang = JFactory::getLanguage();
		$lang->load('mod_addtomenu', JPATH_ADMINISTRATOR);
		$lang->load($opt, JPATH_ADMINISTRATOR);
		$lang->load($opt . '.sys', JPATH_ADMINISTRATOR);

		JHtml::_('behavior.modal');

		JHtml::stylesheet('nnframework/style.min.css', false, true);
		JHtml::stylesheet('addtomenu/style.min.css', false, true);

		$script = "var addtomenu_root = '" . JUri::root() . "';";
		JFactory::getDocument()->addScriptDeclaration($script);
		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');
		JHtml::script('addtomenu/script.min.js', false, true);

		// set height for popup
		$popup_width = 600 + (int) $this->params->adjust_modal_w;
		$popup_height = 444 + (int) $this->params->adjust_modal_h;
		if ($this->params->display_field_access)
		{
			$popup_height += 46;
		}
		if ($this->params->display_field_language)
		{
			$popup_height += 46;
		}
		if ($this->params->display_field_template_style)
		{
			$popup_height += 46;
		}
		if (isset($template->adjust_height))
		{
			$popup_height += (int) $template->adjust_height;
		}
		if (isset($template->extras) && is_object($template->extras) && isset($template->extras->extra))
		{
			if (!is_array($template->extras->extra))
			{
				$template->extras->extra = array($template->extras->extra);
			}
			foreach ($template->extras->extra as $element)
			{
				if (isset($element->type))
				{
					switch ($element->type)
					{
						case 'radio':
							// add height for every line
							$popup_height += 46 + (23 * (count($element->values) - 1));
							break;
						case 'textarea':
							$popup_height += 140;
							break;
						case 'hidden':
						case 'toggler':
							// no height
							break;
						default:
							$popup_height += 46;
							break;
					}
				}
			}
		}

		$link = 'index.php?nn_qp=1';
		$link .= '&folder=administrator.modules.mod_addtomenu';
		$link .= '&file=popup.php';
		$link .= '&comp=' . $comp_file;

		$uri = JUri::getInstance();
		$url_query = $uri->getQuery(1);
		foreach ($url_query as $key => $val)
		{
			$vars[$key] = $val;
		}
		if (!isset($vars['option']))
		{
			$vars['option'] = $option;
		}
		foreach ($vars as $key => $val)
		{
			if (is_array($val))
			{
				$val = $val['0'];
			}
			$link .= '&vars[' . $key . ']=' . $val;
		}

		$text_ini = strtoupper(str_replace(' ', '_', $this->params->button_text));
		$text = JText::_($text_ini);
		if ($text == $text_ini)
		{
			$text = JText::_($this->params->button_text);
		}

		$tip = '';
		if ($this->params->display_tooltip)
		{
			JHtml::_('bootstrap.tooltip');
			$tip = '<strong>' . JText::_('ADD_TO_MENU') . '</strong><br />' . JText::_($template->name);
		}

		if ($this->params->display_toolbar_button)
		{
			// Generate html for toolbar button
			$html = array();
			$html[] = '<a href="' . $link . '" class="btn btn-small addtomenu_link modal' . ($tip ? ' hasTooltip" title="' . $tip : '') . '"'
				. ' rel="{handler: \'iframe\', size: {x: ' . $popup_width . ', y: ' . $popup_height . '}}">';
			$html[] = '<span class="icon-nonumber icon-addtomenu"></span> ';
			$html[] = $text;
			$html[] = '</a>';
			$toolbar = JToolBar::getInstance('toolbar');
			$toolbar->appendButton('Custom', implode('', $html));
		}

		if ($this->params->display_link)
		{
			// Generate html for status link
			$html = array();
			$html[] = '<div class="btn-group addtomenu">';
			$html[] = '<a href="' . $link . '" class="addtomenu_link modal' . ($tip ? ' hasTooltip" title="' . $tip : '') . '"'
				. ' rel="{handler: \'iframe\', size: {x: ' . $popup_width . ', y: ' . $popup_height . '}}">';
			if ($this->params->display_link != 'text')
			{
				$html[] = '<span class="icon-nonumber icon-addtomenu"></span> ';
			}
			if ($this->params->display_link != 'icon')
			{
				$html[] = $text;
			}
			$html[] = '</a>';
			$html[] = '</div>';
			echo implode('', $html);
		}
	}

	public static function getVar($var)
	{
		if ($var['0'] == '$')
		{
			$var = substr($var, 1);
			$var = self::getVal($var);
		}

		return $var;
	}

	public static function getVal($value, $vars = '')
	{
		$url = JFactory::getApplication()->input->getVar('url');
		$extra = JFactory::getApplication()->input->getVar('extra');

		if (isset($vars[$value]))
		{
			$val = $vars[$value];
		}
		else if (isset($url[$value]))
		{
			$val = $url[$value];
		}
		else if (isset($extra[$value]))
		{
			$val = $extra[$value];
		}
		else
		{
			$val = JFactory::getApplication()->input->getVar($value);
			if ($val == '')
			{
				$val = self::getUserStateFromRequest($value);
			}
		}

		if (is_array($val))
		{
			$val = $val['0'];
		}

		return $val;
	}

	public static function getUserStateFromRequest($value)
	{
		$context = array();
		if (JFactory::getApplication()->input->get('option'))
		{
			$context[] = JFactory::getApplication()->input->get('option');
		}
		if (JFactory::getApplication()->input->get('layout'))
		{
			$context[] = JFactory::getApplication()->input->get('layout');
		}
		else if (JFactory::getApplication()->input->get('view'))
		{
			$context[] = JFactory::getApplication()->input->get('view');
		}
		else
		{
			switch (JFactory::getApplication()->input->get('option'))
			{
				case 'com_content':
					$context[] = 'articles';
					break;
			}
		}
		$context[] = 'filter';
		$val = self::getUSFR($value, $context, '.', '.');
		if ($val != '')
		{
			return $val;
		}
		$val = self::getUSFR($value, $context, '', '.');
		if ($val != '')
		{
			return $val;
		}
		$val = self::getUSFR($value, $context, '.', '_');
		if ($val != '')
		{
			return $val;
		}
		$val = self::getUSFR($value, $context, '', '_');
		if ($val != '')
		{
			return $val;
		}
		$val = self::getUSFR($value, $context, '.', '');
		if ($val != '')
		{
			return $val;
		}
		$val = self::getUSFR($value, $context, '', '');
		if ($val != '')
		{
			return $val;
		}
		$context['0'] = 'global';
		$val = self::getUSFR($value, $context, '.', '.');
		if ($val != '')
		{
			return $val;
		}
		$val = self::getUSFR($value, $context, '', '.');
		if ($val != '')
		{
			return $val;
		}
		$val = self::getUSFR($value, $context, '.', '_');
		if ($val != '')
		{
			return $val;
		}
		$val = self::getUSFR($value, $context, '', '_');
		if ($val != '')
		{
			return $val;
		}
		$val = self::getUSFR($value, $context, '.', '');
		if ($val != '')
		{
			return $val;
		}
		$val = self::getUSFR($value, $context, '', '');

		return $val;
	}

	public static function getUSFR($value, $context = array('filter'), $glue = '', $glue2 = '')
	{
		return JFactory::getApplication()->getUserStateFromRequest(implode($glue, $context) . $glue2 . $value, 'filter_' . $value);
	}

	public static function checkRequiredFields(&$required, &$vars)
	{
		$pass = 1;
		foreach ($required as $key => $values)
		{
			$keyval = self::getVal($key, $vars);

			if (is_string($values))
			{
				$values = explode(',', $values);
			}

			foreach ($values as $val)
			{
				$pass = 0;
				switch ($val)
				{
					case '*':
						if (strlen($keyval))
						{
							$pass = 1;
						}
						break;
					case '+':
						if ($keyval)
						{
							$pass = 1;
						}
						break;
					default:
						if ($keyval == $val)
						{
							$pass = 1;
						}
						break;
				}
				if ($pass)
				{
					break;
				}
			}
			if (!$pass)
			{
				break;
			}
			$vars[$key] = $keyval;
		}

		return $pass;
	}
}
