<?php
/**
 * Main Component File
 * Used for the editor button (template xml)
 *
 * @package         Content Templater
 * @version         5.1.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

// Load common functions
require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';;
$user = JFactory::getUser();
if (
	$user->get('guest')
	|| (
		!$user->authorise('core.create', 'com_content')
		&& !$user->authorise('core.edit', 'com_content')
		&& !count($user->getAuthorisedCategories('com_content', 'core.create'))
		&& !count($user->getAuthorisedCategories('com_content', 'core.edit'))
	)
)
{
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

if (JFactory::getApplication()->isSite())
{
	$params = JComponentHelper::getParams('com_contenttemplater');
	if (!$params->get('enable_frontend', 1))
	{
		JError::raiseError(403, JText::_("ALERTNOTAUTH"));
	}
}

$class = new PlgButtonContentTemplaterData;
$class->render();
die;

class PlgButtonContentTemplaterData
{
	function render()
	{
		header('Content-Type: text/html; charset=utf-8');

		$id = JFactory::getApplication()->input->getInt('id');

		if (!$id)
		{
			return;
		}

		JHtml::stylesheet('nnframework/style.min.css', false, true);

		$nocontent   = JFactory::getApplication()->input->getInt('nocontent', 0);
		$unprotected = (JFactory::getUser()->authorise('core.manage', 'com_contenttemplater')) ? JFactory::getApplication()->input->getInt('unprotect') : 0;

		require_once JPATH_ADMINISTRATOR . '/components/com_contenttemplater/models/item.php';

		// Create a new class of classname and set the default task: display
		$model = new ContentTemplaterModelItem;
		$item  = $model->getItem($id, 0, 1);

		$output = array();

		$ignore = array(
			'view_state',
			'id',
			'ordering',
			'name',
			'description',
			'ordering',
			'published',
			'checked_out',
			'checked_out_time',
			'show_url_field_sef',
			'show_url_field',
			'match_method',
			'show_assignments',
			'defaults',
			'form_defaults',
		);

		$customfields = array();
		foreach ($item as $key => $val)
		{
			if ($val != ''
				&& !isset($output[$key])
				&& !in_array($key, $ignore)
				&& strpos($key, '@') !== 0
				&& strpos($key, 'button_') !== 0
				&& strpos($key, 'load_') !== 0
				&& strpos($key, 'url_') !== 0
				&& strpos($key, 'assignto_') !== 0
			)
			{
				if ($key == 'content' && $nocontent)
				{
					continue;
				}
				if (strpos($key, 'customfield') === 0)
				{
					$field = explode('_', $key);
					//$name = str_replace('_key', '', str_replace('_value', '', $key));
					if (!isset($customfields[$field['0']]))
					{
						$customfields[$field['0']] = '';
					}
					$customfields[$field['0']]->$field['1'] = $val;
				}
				else
				{
					$default      = isset($item->defaults->$key) ? $item->defaults->$key : '';
					$form_default = isset($item->form_defaults->$key) ? $item->form_defaults->$key : $default;
					if ($val != $default && $val != $form_default)
					{
						if (strpos($key, 'jform_') === 0 && $val == -2)
						{
							$val = '';
						}
						list($key, $val) = $this->getStr($model, $key, $val, $form_default);
						$output[$key] = $val;
					}
				}
			}
		}

		foreach ($customfields as $customfield)
		{
			if (isset($customfield->key) && $customfield->key != '' && isset($customfield->value))
			{
				list($key, $val) = $this->getStr($model, $customfield->key, $customfield->value, 'customfield');
				$output[$key] = $val;
			}
		}

		$str = implode("\n", $output);
		if (!$unprotected)
		{
			$str = base64_encode($str);
			$str = wordwrap($str, 80, "\n", 1);
		}
		echo $str;
	}

	function getStr(&$item, $key, $val, $default = '')
	{
		switch ($key)
		{
			case 'jform_access':
				$default = 1;
				break;
			case 'jform_categories_k2':
				$key     = 'catid';
				$default = 0;
				break;
			case 'jform_categories_zoo':
				$key     = 'categories';
				$default = '';
				break;
		}
		if (is_array($val))
		{
			$val = implode(',', $val);
		}
		if ($key != 'content')
		{
			$val = html_entity_decode($val, ENT_QUOTES, 'UTF-8');
			if (strpos($key, 'jform_') !== false)
			{

				$key = preg_replace('#jform_(params|attribs|images|urls|metadata)_#', 'jform[\1][', $key);
				$key = str_replace('jform_', 'jform[', $key) . ']';
			}
		}
		$item->replaceVars($val);

		return array($key, '[CT]' . $key . '[CT]' . $default . '[CT]' . $val . '[/CT]');
	}
}
