<?php
/**
 * @version    $Id: thememanager.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JFormFieldThemeManager Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class JFormFieldThemeManager extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'ThemeManager';

	/**
	 * Always return null to disable label markup generation.
	 *
	 * @return  string
	 */
	protected function getLabel()
	{
		return '';
	}

	/**
	 * Get the messages list markup.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		$token 	= JFactory::getSession()->getFormToken();
		// Preset output
		$model 	= JModelLegacy::getInstance('plugins', 'imageshowmodel');
		$items	= $model->getData();

		$html[] = '<table class="table table-bordered">';
		$html[] = '<thead>
				<tr>
					<th width="20" nowrap="nowrap" class="center">#</th>
					<th class="title" nowrap="nowrap">' . JText::_('MAINTENANCE_THEME_THEME_NAME') . '</th>
					<th width="80" nowrap="nowrap" class="center">' . JText::_('MAINTENANCE_THEME_THEME_VERSION') . '</th>
					<th width="80" nowrap="nowrap" class="center">' . JText::_('MAINTENANCE_THEME_ACTIONS') . '</th>
				</tr>
			</thead><tbody>';

		if (count($items))
		{
			for ($i = 0, $count = count($items); $i < $count; $i++)
			{
				$row = $items[$i];
				$html[] = '<tr>';
				$html[] = '<td class="center">' . ($i + 1) . '</td>';
				$html[] = '<td>' . $row->name. '</td>';
				$html[] = '<td class="center">' . $row->version . '</td>';
				$html[] = '<td class="actionprofile center">';
				if (JFile::exists(JPATH_PLUGINS . DS . 'jsnimageshow' . DS . $row->element . DS . 'views' . DS . 'maintenance' . DS . 'tmpl' . DS . 'default_theme_parameters.php'))
				{
					$html[] = '<a rel=\'{"size": {"x": 400, "y": 500}}\' href="index.php?option=com_imageshow&controller=maintenance&type=themeparameters&theme_name=' . $row->element . '&tmpl=component" class="jsn-icon16 jsn-icon-pencil jsn-is-form-modal" name="' . JText::_('MAINTENANCE_THEME_PARAMETER_SETTINGS') . '" title="' . JText::_('MAINTENANCE_THEME_EDIT_SETTINGS') . '"></a>&nbsp;';
				}

				if ($count)
				{
					$html[] = '<a href="javascript: void(0);" rel=\'' . json_encode(array('theme_id' => $row->extension_id, 'theme_name' => $row->element, 'token' => $token)) . '\' class="jsn-icon16 jsn-icon-trash jsn-is-delete-theme"> </a>';
				}
				else
				{
					$html[] = '<a class="jsn-icon16 jsn-icon-trash disabled" title="' . JText::_('MAINTENANCE_THEME_YOU_CAN_NOT_DELETE_THE_ONLY_THEME_IN_THE_LIST') . '"> </a>';
				}
				$html[] = '</td>';
				$html[] = '</tr>';
			}

		}

		$html[] = '</tbody></table>';
		$html[] = JSNHtmlAsset::loadScript('imageshow/joomlashine/maintenance', array(
				'pathRoot' => JURI::root(),
				'language' => JSNUtilsLanguage::getTranslated(array(
						'JSN_IMAGESHOW_SAVE',
						'JSN_IMAGESHOW_CLOSE',
						'JSN_IMAGESHOW_CONFIRM'
						))
						), true);
						return implode($html);
	}
}
