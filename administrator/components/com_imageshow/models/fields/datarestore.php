<?php
/**
 * @version     $Id: datarestore.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package     JSN_Framework
 * @subpackage  Config
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Create data restore form.
 *
 * Below is a sample field declaration for generating data restore form:
 *
 * <code>&lt;field name="datarestore" type="datarestore" label="JSN_SAMPLE_DATA_RESTORE" task="data.restore" /&gt;</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldDataRestore extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	protected $type = 'DataRestore';

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
	 * Get the markup for data restore form.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Get session
		$session 		= JFactory::getSession();
		$restoreResult 	= $session->get('JSNISRestore');

		// Preset output
		$html = array();

		// Generate data backup form
		$html[] = '<form name="JSNDataRestoreForm" action="' . JRoute::_('index.php') . '" method="POST" enctype="multipart/form-data" onsubmit="return false;">';
		$html[] = '<fieldset>';
		$html[] = '<legend>' . JText::_('JSN_EXTFW_DATA_RESTORE') . '</legend>';
		if (!is_array($restoreResult))
		{
			$html[] = '<div class="control-group">
								<label class="control-label">' . JText::_('JSN_EXTFW_DATA_RESTORE_FILE') . ':</label>
								<div class="controls">
									<input name="datarestore" type="file" size="70" class="input-file" />
								</div>
							</div>
						<div class="form-actions">
							<div class="jsn-bootstrap"></div>
							<button class="btn btn-primary" value="' . ($this->element['task'] ? (string) $this->element['task'] : 'data.restore') . '" disabled="disabled" track-change="yes" ajax-request="disabled">' . JText::_('JSN_EXTFW_DATA_RESTORE_BUTTON') . '</button>
						</div>';
		}
		else
		{
			$html[] = '<div>';
			$html[] = '<p id="jsn-restore-result">' . JText::_('MAINTENANCE_RESTORE_RESULT_HEADER'). '</p><ul>';

			if (is_array($restoreResult) && !$restoreResult['extractFile'])
			{
				$html[] = '<li>';
				$html[] = JText::_('MAINTENANCE_RESTORE_EXTRACT_BACKUP_FILE'). '<span class="jsn-icon16 jsn-icon-remove">&nbsp;</span><p id="jsn-restore-extract-failure">' . $restoreResult['message'] . '</p>';
				$html[] = '</li>';
			}
			else
			{
				$html[] = '<li>';
				$html[] =  JText::_('MAINTENANCE_RESTORE_EXTRACT_BACKUP_FILE') . '<span class="jsn-icon16 jsn-icon-ok">&nbsp;</span>';
				$html[] = '</li>';
				$html[] = '<li id="jsn-restore-data-wrap">';
				$html[] = JText::_('MAINTENANCE_RESTORE_RESTORE_DATA');

				if ($restoreResult['requiredSourcesNeedInstall'] || $restoreResult['requiredThemesNeedInstall'])
				{
					$html[] = '<span class="jsn-restore-icon-failure jsn-icon16 jsn-icon-remove">&nbsp;</span>';
					$html[] = '<div id="jsn-restore-data" class="jsn-restore-icon-process">';
					$html[] = '<img src="components/com_imageshow/assets/images/icons-16/icon-16-loading-circle.gif"/>';
					$html[] = '</div>';
					$html[] = '<div class="jsn-restore-warning">';
					$html[] = '<div id="jsn-restore-install-required-warning">';
					$html[] = '<p>' . JText::_('MAINTENANCE_RESTORE_IMAGESOURCES_AND_THEMES_REQUIRED') . '</p>';
					$html[] = '<ul id="jsn-restore-list-required-install">';
					foreach ($restoreResult['requiredSourcesNeedInstall'] as $source)
					{
						$html[] = '<li>' . $source->name .' </li>';
					}

					foreach ($restoreResult['requiredThemesNeedInstall'] as $theme)
					{
						$html[] = '<li>' . $theme->name .' </li>';
					}
					$html[] = '</ul>';

					if (isset($restoreResult['requiredInstallData']['commercial']) && $restoreResult['requiredInstallData']['commercial'])
					{

					}
					else
					{
						$html[] = '<p>';
						$html[] = '<a class="jsn-link-action" href="javascript: void(0);"
									onclick="JSNISInstallDefault.restoreInstall(' . htmlspecialchars(json_encode($restoreResult['requiredInstallData']), ENT_COMPAT, 'UTF-8') . ');">' .
						JText::_('MAINTENANCE_RESTORE_INSTALL_ALL_REQUIRED_PLUGINS') . '</a>';
						$html[] = '</p>';
					}
					$html[] = '</div>';
					$html[] = '<ul class="jsn-restore-install-required">';
					if (count($restoreResult['requiredSourcesNeedInstall']))
					{
						$html[] = '<li id="jsn-restore-required-sources">';
						$html[] = JText::_('MAINTENANCE_RESTORE_INSTALL_REQUIRED_IMAGE_SOURCES') . '.';
						$html[] = '<div class="jsn-restore-icon-process">';
						$html[] = '<img src="components/com_imageshow/assets/images/icons-16/icon-16-loading-circle.gif"/>';
						$html[] = '</div>';
						$html[] = '<span class="jsn-restore-change-text" id="jsn-restore-source-change-text"></span>';
						$html[] = '<span class="jsn-icon16 jsn-icon-ok">&nbsp;</span>';
						$html[] = '</li>';
					}

					if (count($restoreResult['requiredThemesNeedInstall']))
					{
						$html[] = '<li id="jsn-restore-required-themes">';
						$html[] = JText::_('MAINTENANCE_RESTORE_INSTALL_REQUIRED_IMAGE_THEMES') . '.';
						$html[] = '<div class="jsn-restore-icon-process">';
						$html[] = '<img src="components/com_imageshow/assets/images/icons-16/icon-16-loading-circle.gif"/>';
						$html[] = '</div>';
						$html[] = '<span class="jsn-restore-change-text" id="jsn-restore-theme-change-text"></span>';
						$html[] = '<span class="jsn-icon16 jsn-icon-ok">&nbsp;</span>';
						$html[] = '</li>';
					}
					$html[] = '</div>';
					$html[] = '</ul>';
				}
				else
				{
					$html[] = '<span class="jsn-icon16 jsn-icon-ok">&nbsp;</span>';
				}
				$html[] = '</li>';
				$html[] = '</ul>';
				$html[] = '<div id="jsn-restore-database-success"' . (($restoreResult['error'] == false) ? ' style="display:block;"' : '') . '>';
				$html[] = '<hr />';
				$html[] = '<h3 class="jsn-text-success">' . JText::_('MAINTENANCE_RESTORE_RESTORE_DATABASE_SUCCESS') . '</h3>';
				$html[] = '<p>' . JText::_('MAINTENANCE_RESTORE_OPERATE_ON_DATA') . '</p>';
				$html[] = '</div>';
				$html[] = '<div id="jsn-restore-buttons" class="form-actions' . ((!$restoreResult['error']) ? ' jsn-restore-installing-success' : '') . '">';
				$html[] = '<a href="javascript: void(0);" class="btn btn-primary" id="jsn-restore-finish-button" type="button" value="' . JText::_('MAINTENANCE_RESTORE_FINISH') . '" name="button_backup_restore">' . JText::_('MAINTENANCE_RESTORE_FINISH') . '</a>';
				$html[] = '<a href="javascript: void(0);" class="btn btn-primary" id="jsn-restore-button-cancel" type="button" value="' . JText::_('MAINTENANCE_RESTORE_CANCEL') . '" name="button_backup_restore">' . JText::_('MAINTENANCE_RESTORE_CANCEL') . '</a>';
				$html[] = '</div>';
				$html[] = '</div>';
			}
		}
		$html[] = '<input type="hidden" name="' . $this->name . '" value="JSN_CONFIG_SKIP_SAVING" />';
		$html[] = '</fieldset>';
		$html[] = '</form>';
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
