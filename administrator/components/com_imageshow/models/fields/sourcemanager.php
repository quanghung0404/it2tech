<?php
/**
 * @version    $Id: sourcemanager.php 16077 2012-09-17 02:30:25Z giangnd $
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
 * JFormFieldSourceManager Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class JFormFieldSourceManager extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	protected $type = 'SourceManager';

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
		// Preset output
		$token 			= JFactory::getSession()->getFormToken();
		$objJSNSource 	= JSNISFactory::getObj('classes.jsn_is_source');
		$objJSNProfile 	= JSNISFactory::getObj('classes.jsn_is_profile');
		$sources  		= $objJSNSource->getListSources();
		$items			= array();
		if (count($sources))
		{
			for ($i = 0, $count = count($sources); $i < $count; $i++)
			{
				$source = $sources[$i];
				if ($source->type == ('external' || 'internal'))
				{
					$source->profiles = $objJSNProfile->getProfiles('', $source->identified_name);
				}
				$items[] = $source;
			}
		}

		$html[] = '<table class="table table-bordered">';
		$html[] = '<thead>
				<tr>
					<th width="20" nowrap="nowrap" class="center">#</th>
					<th class="title" nowrap="nowrap">' . JText::_("TITLE") . '</th>
					<th width="80" nowrap="nowrap" class="center">' . JText::_('MAINTENANCE_SOURCE_VERSION') . '</th>
					<th width="80" nowrap="nowrap" class="center">' . JText::_('ACTION') . '</th>
				</tr>
			</thead><tbody>';

		if (count($items))
		{
			for ($i = 0, $count = count($items); $i < $count; $i++)
			{
				$row = $items[$i];
				if ($row->type !='folder')
				{
					$manifest = json_decode($row->pluginInfo->manifest_cache);

					$html[] = '<tr>';
					$html[] = '<td class="center">' . ($i + 1) . '</td>';
					$html[] = '<td>';
					$html[] = $row->title;
					if ($row->type == ('external') && count($row->profiles))
					{
						$html[] = '<span class="jsn-image-source-seperator">|</span>';
						$html[] = '<a href="javascript: void(0);" class="jsn-link-action jsn-is-view-profile" rel=\'' .json_encode(array('container_id' => 'jsn-image-source-profile-item-' . $row->identified_name)) . '\'>';
						$html[] = '<span class="jsn-image-source-open-profile">' . JText::_('MAINTENANCE_SOURCE_SEE_PROFILES') . '</span>';
						$html[] = '<span class="jsn-image-source-close-profile">' . JText::_('MAINTENANCE_SOURCE_CLOSE') . '</span>';
					}

					$html[] ='</td>';
					$html[] = '<td class="center">' . $manifest->version . '</td>';
					$html[] = '<td class="actionprofile center">';
					if (JFile::exists(JPATH_PLUGINS . DS . 'jsnimageshow' . DS . $row->pluginInfo->element . DS . 'views' . DS . 'maintenance' . DS . 'tmpl' . DS . 'default_profile_parameters.php'))
					{
						$html[] = '<a rel=\'{"size": {"x": 400, "y": 500}}\' href="index.php?option=com_imageshow&controller=maintenance&type=profileparameters&source_type=' . $row->pluginInfo->element . '&tmpl=component" class="jsn-icon16 jsn-icon-pencil jsn-is-form-modal" name="' . JText::_('MAINTENANCE_SOURCE_PARAMETER_SETTINGS') . '" title="' . JText::_('MAINTENANCE_SOURCE_EDIT_SETTINGS') . '"></a>&nbsp;';
					}

					if ($count)
					{
						$html[] = '<a href="javascript: void(0);" rel=\'' . json_encode(array('source_id' => $row->pluginInfo->extension_id, 'token' => $token)) . '\' class="jsn-icon16 jsn-icon-trash jsn-is-delete-source"> </a>';
					}
					else
					{
						$html[] = '<a class="jsn-icon16 jsn-icon-trash disabled" title="' . JText::_('MAINTENANCE_SOURCE_YOU_CAN_NOT_DELETE_THE_ONLY_SOURCE_IN_THE_LIST') . '"> </a>';
					}

					if (count($row->profiles))
					{
						for ($z = 0, $countz = count($row->profiles); $z < $countz; $z++)
						{
							$profile = $row->profiles[$z];
							$profile->token = $token;
							$html[]	 = '<tr class="jsn-image-source-profile-item-' . $row->identified_name . ' jsn-image-source-profile-close">';
							$html[]	 = '<td></td>';
							$html[]	 = '<td class="jsn-image-source-profile-title">';
							$html[]	 = $profile->external_source_profile_title;
							$html[]	 = '<span class="jsn-image-source-seperator">|</span>';
							$html[]	 = '<a class="jsn-is-view-modal jsn-link-action" rel=\'{"size": {"x": 500, "y": 300}}\' href="index.php?option=com_imageshow&controller=showlist&task=elements&tmpl=component&limit=0&external_source_id=' . $profile->external_source_id . '&image_source_name=' . $profile->image_source_name . '" name="' . JText::_('SHOWLIST_IMAGE_SOURCE_PROFILE_SHOWLISTS') . '">';
							$html[]	 = JText::_('MAINTENANCE_SOURCE_SEE_SHOWLISTS');
							$html[]	 = '</a></td>';
							$html[]	 = '<td></td>';
							$html[]	 = '<td align="center" class="center actionprofile" nowrap="nowrap">';
							$html[]	 = '<a name="' . JText::_('MAINTENANCE_SOURCE_PROFILE_SETTINGS') . '" rel=\'{"size": {"x": 400, "y": 500}}\' href="index.php?option=com_imageshow&controller=maintenance&type=editprofile&source_type=' . $profile->image_source_name . '&tmpl=component&external_source_id=' . $profile->external_source_id . '&count_showlist=' . $profile->totalshowlist . '" class="jsn-icon16 jsn-icon-pencil jsn-is-profile-form-modal" title="' . JText::_('EDIT') . '"></a>&nbsp';
							$html[]	 = '<a rel=\'' . json_encode($profile) . '\' href="javascript: void(0);" class="jsn-icon16 jsn-icon-trash jsn-is-delete-profile" title="' . JText::_('DELETE') . '"></a>';
							$html[]	 = '</td></tr>';
						}
					}
					$html[] = '</td>';
					$html[] = '</tr>';
				}
			}

		}

		$html[] = '</tbody></table>';
		$html[] = JSNHtmlAsset::loadScript('imageshow/joomlashine/maintenance', array(
				'pathRoot' => JURI::root(),
				'language' => JSNUtilsLanguage::getTranslated(array(
						'JSN_IMAGESHOW_SAVE',
						'JSN_IMAGESHOW_CLOSE',
						'JSN_IMAGESHOW_CONFIRM',
						'MAINTENANCE_SOURCE_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_IMAGE_SOURCE_PROFILE'
						))
						), true);
						return implode($html);
	}
}
