<?php
/**
 * @version     $Id: installsample.php 16204 2012-09-20 04:31:14Z giangnd $
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
 * Create sample data installation form.
 *
 * Below is a sample field declaration for generating sample data installation
 * form:
 *
 * <code>&lt;field
 *     name="installsample" type="installsample" task="data.installSample"
 *     download-url="http://localhost/jsn/framework/jsn-sample-data-j25.zip"
 *     warning-message="JSN_SAMPLE_SAMPLE_DATA_INSTALLATION_WARN"
 *     confirm-message="JSN_SAMPLE_SAMPLE_DATA_INSTALLATION_CONFIRM"
 * /&gt;</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldInstallSample extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	protected $type = 'InstallSample';

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
	 * Get the markup for sample data installation form.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Preset output
		$html = array();
		$objJSNUtil = JSNISFactory::getObj('classes.jsn_is_utils');
		$auto 	=  $objJSNUtil->checkEnvironmentDownload();
		// Get input object
		$input = JFactory::getApplication()->input;

		// Generate data backup form
		$html[] = '<script type="text/javascript">';
		$html[] = 'function setButtonState(form)
		{
			if(form.agree_install_sample.checked)
			{
				form.button_installation_data.disabled = false;
				$(form.button_installation_data).removeClass(\'disabled\');
			}
			else
			{
				form.button_installation_data.disabled = true;
				$(form.button_installation_data).addClass(\'disabled\');
			}
		}';

		$html[] = '</script>';
		if ($auto)
		{
			$html[] = '<div id="jsn-sample-data">';
			$html[] = '<div class="alert alert-warning" id="jsn-sample-data-text-alert">';
			$html[] = '<p><span class="label label-important">' . JText::_('MAINTENANCE_SAMPLE_DATA_WARNING') . '</span></p>';
			$html[] = JText::_('MAINTENANCE_SAMPLE_DATA_INSTALL_SUGGESTION');
			$html[] = '</div>';
			$html[] = '<div id="jsn-sample-data-install">';
			$html[] = '<form action="index.php" method="post" enctype="multipart/form-data">';
			$html[] = '<div id="jsn-start-installing-sampledata">';
			$html[] = '<div class="control-group">';
			$html[] = '<label for="agree_install_sample_local" class="checkbox">';
			$html[] = '<input onclick="return setButtonState(this.form);" type="checkbox" name="agree_install_sample" id="agree_install_sample_local" value="1" />';
			$html[] = '<strong>' . JText::_('MAINTENANCE_SAMPLE_DATA_AGREE_INSTALL_SAMPLE_DATA') . '</strong>';
			$html[] = '</label>';
			$html[] = '</div>';
			$html[] = '<div class="form-actions">';
			$html[] = '<button class="btn btn-primary disabled agree_install_sample_local" type="button" ajax-request="yes" name="button_installation_data" onclick="JSNISSampleData.installSampleData(); return false;" disabled="disabled">' . JText::_('MAINTENANCE_SAMPLE_DATA_INSTALL_SAMPLE_DATA') . '</button>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '<div id="jsn-installing-sampledata">';
			$html[] = '<p>' . JText::_('MAINTENANCE_SAMPLE_DATA_AFTER_DOWNLOAD_SUGGESTION') . '</p>';
			$html[] = '<ul>';
			$html[] = '<li id="jsn-download-sample-data-package-title">' . JText::_('MAINTENANCE_SAMPLE_DATA_DOWNLOAD_SAMPLE_DATA_PACKAGE'). '.';
			$html[] = '<span class="jsn-icon16 jsn-icon-loading" id="jsn-downloading-sampledata"></span>';
			$html[] = '<span class="jsn-icon16 jsn-icon-ok" id="jsn-download-sampledata-success"></span>';
			$html[] = '<span class="jsn-icon16 jsn-icon-remove" id="jsn-span-unsuccessful-downloading-sampledata"></span>';
			$html[] = '<br />';
			$html[] = '<p id="jsn-span-unsuccessful-downloading-sampledata-message" class="jsn-text-important"></p>';
			$html[] = '</li>';
			$html[] = '<li id="jsn-install-sample-data-package-title">' . JText::_('MAINTENANCE_SAMPLE_DATA_INSTALL_SAMPLE_DATA'). '.';
			$html[] = '<span class="jsn-icon16 jsn-icon-loading" id="jsn-span-installing-sampledata-state"></span>';
			$html[] = '<span class="jsn-icon16 jsn-icon-ok" id="jsn-span-successful-installing-sampledata"></span>';
			$html[] = '<span class="jsn-icon16 jsn-icon-remove" id="jsn-install-sampledata-unsuccessful"></span>';
			$html[] = '<br />';
			$html[] = '<p id="jsn-span-unsuccessful-installing-sampledata-message" class="jsn-text-important"></p>';
			$html[] = '<div class="jsn-sampledata-warnings-text" id="jsn-sampledata-warnings">';
			$html[] = '<ul id="jsn-sampledata-ul-warnings">';
			$html[] = '</ul>';
			$html[] = '<p id="jsn-sampledata-link-install-all-requried-plugins"><a id="jsn-sampledata-a-link-install-all-requried-plugins" rel="{handler: \'iframe\', size: {x: 450, y: 250}}" onclick="JSNISSampleData.installAllRequiredPlugins(false);" class="jsn-link-action">' . JText::_('MAINTENANCE_SAMPLE_DATA_INSTALL_ALL_REQUIRED_PLUGINS') . '</a></p>';
			$html[] = '</div>';
			$html[] = '</li>';
			$html[] = '</ul>';
			$html[] = '</div>';
			$html[] = '<div id="jsn-installing-sampledata-unsuccessfully">';
			$html[] = '<div class="form-actions">';
			$html[] = '<a class="btn btn-primary" href="javascript: void(0);" onclick="window.top.location=\'index.php?option=com_imageshow&view=maintenance&s=maintenance&g=data\';">' . JText::_('MAINTENANCE_SAMPLE_DATA_CANCEL') . '</a>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '<div id="jsn-installing-sampledata-successfully">';
			$html[] = '<hr />';
			$html[] = '<h3 class="jsn-text-success">' . JText::_('MAINTENANCE_SAMPLE_DATA_SAMPLE_DATA_IS_SUCCESSFULLY_INSTALLED') . '</h3>';
			$html[] = '<p>' . JText::_('MAINTENANCE_SAMPLE_DATA_CONGRATULATIONS_NOW_YOU_CAN_OPERATE_ON_SAMPLE_SHOWLISTS_AND_SHOWCASES'). '</p>';
			$html[] = '<div class="form-actions">';
			$html[] = '<a class="btn btn-primary agree_install_sample_local" href="javascript: void(0);" onclick="window.top.location=\'index.php?option=com_imageshow\';">' . JText::_('MAINTENANCE_SAMPLE_DATA_FINISH') . '</a>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '<input type="hidden" name="sample_download_url" id="sample_download_url" value="' . (string) $this->element['download-url'] . '" />';
			$html[] = '</form>';
			$html[] = '</div>';
			$html[] = '</div>';
		}
		else
		{

			/// manuall sample data
			$subAction					= JRequest::getVar('sub_action');
			$session 					= JFactory::getSession();
			$uploadIdentifier 			= md5('upload_sampledata_package');
			$packagenameIdentifier 		= md5('sampledata_package_name');

			if ($subAction == 'cancelintallation')
			{
				$session->set($uploadIdentifier, false, 'jsnimageshow');
				$session->set($packagenameIdentifier, '', 'jsnimageshow');
			}

			$sessionValue				= $session->get($uploadIdentifier, false, 'jsnimageshow');
			$packageName				= $session->get($packagenameIdentifier, '', 'jsnimageshow');

			$html[] = '<div id="jsn-manuall-sample-data">';

			if (!$sessionValue)
			{
				$html[] = '<div class="alert alert-warning">';
				$html[] = '<p><span class="label label-important">' . JText::_('MAINTENANCE_SAMPLE_DATA_WARNING') . '</span></p>';
				$html[] = JText::_('MAINTENANCE_SAMPLE_DATA_INSTALL_SUGGESTION');
				$html[] = '</div>';
			}

			$html[] = '<div id="jsn-sample-data-install">';
			$html[] = '<form action="index.php?option=com_imageshow&controller=maintenance" method="post" enctype="multipart/form-data">';

			if (!$sessionValue)
			{
				$html[] = '<div class="jsn-manual-installation">';
				$html[] = '<ul>';
				$html[] = '<li>1. ' . JText::_('MANUAL_DOWNLOAD_INSTALLATION_PACKAGE') . ': <a class="btn" href="' . (string) $this->element['download-url'] .' "><span>' . JText::_('MANUAL_DOWNLOAD') . '</span></a></li>';
				$html[] = '<li>2. ' . JText::_('MANUAL_SELECT_DOWNLOADED_PACKAGE') . ': <input id="sample_data_input_file" size="40" type="file" name="install_package" /></li>';
				$html[] = '</ul>';
				$html[] = '</div>';
				$html[] = '<div id="jsn-start-installing-sampledata">';
				$html[] = '<p>';
				$html[] = '<label for="agree_install_sample_local" class="checkbox">';
				$html[] = '<input onclick="return setButtonState(this.form);" type="checkbox" name="agree_install_sample" id="agree_install_sample_local" value="1" />';
				$html[] = '<strong>' . JText::_('MAINTENANCE_SAMPLE_DATA_AGREE_INSTALL_SAMPLE_DATA') . '</strong>';
				$html[] = '</label>';
				$html[] = '</p>';
				$html[] = '<div class="form-actions">';
				$html[] = '<button class="btn btn-primary agree_install_sample_local disabled" type="submit" name="button_installation_data" value="maintenance.installsampledatamanually" disabled="disabled">' . JText::_('MAINTENANCE_SAMPLE_DATA_INSTALL_SAMPLE_DATA') . '</button>';
				$html[] = '</div>';
				$html[] = '</div>';
				$html[] = '<input type="hidden" name="method_install_sample_data" value="manually" />';
				$html[] = '</form>';
			}
			else
			{
				$html[] = '<script type="text/javascript">
				window.addEvent(\'domready\', function() {
					$(\'jsn-installing-sampledata\').setStyle(\'display\', \'block\');
					$(\'jsn-downloading-sampledata\').setStyle(\'display\', \'none\');
					$(\'jsn-download-sampledata-success\').setStyle(\'display\', \'inline-block\');
					$(\'jsn-install-sample-data-package-title\').setStyle(\'display\', \'list-item\');
					JSNISSampleDataInstallSampleDataName = \''. trim($packageName). '\';
					JSNISSampleDataManual.installPackage(\''. trim($packageName). '\');
				});
				</script>';
				$html[] = '<div id="jsn-installing-sampledata">';
				$html[] = '<p>' . JText::_('MAINTENANCE_SAMPLE_DATA_AFTER_DOWNLOAD_SUGGESTION') . '</p>';
				$html[] = '<ul>';
				$html[] = '<li id="jsn-download-sample-data-package-title">' . JText::_('MAINTENANCE_SAMPLE_DATA_UPLOAD_SAMPLE_DATA_PACKAGE'). ' .<span class="jsn-icon jsn-icon-loading" id="jsn-downloading-sampledata">&nbsp;</span><span class="jsn-icon16 jsn-icon-ok" id="jsn-download-sampledata-success">&nbsp;</span><span class="jsn-icon jsn-icon-failed" id="jsn-span-unsuccessful-downloading-sampledata" style="display:none;">&nbsp;</span><br />
							<p id="jsn-span-unsuccessful-downloading-sampledata-message"></p></li>';
				$html[] = '<li id="jsn-install-sample-data-package-title">' . JText::_('MAINTENANCE_SAMPLE_DATA_INSTALL_SAMPLE_DATA') . '.<span class="jsn-icon16 jsn-icon-loading" id="jsn-span-installing-sampledata-state">&nbsp;</span><span class="jsn-icon16 jsn-icon-ok" id="jsn-span-successful-installing-sampledata">&nbsp;</span><span class="jsn-icon16 jsn-icon-remove" id="jsn-install-sampledata-unsuccessful">&nbsp;</span><br />
							<p id="jsn-span-unsuccessful-installing-sampledata-message" class="jsn-text-important"></p>
							<div id="jsn-installing-sampledata_install_requried_plugin">
								<form action="index.php?option=com_imageshow&controller=maintenance&type=data" method="post" name="installPluignForm" enctype="multipart/form-data">
									<input id="pluign_file" size="75%" type="file" name="pluign_file" />
									<div class="form-actions">
										<button class="btn btn-primary agree_install_sample_local" type="submit" name="button_installation_data" value="maintenance.installRequiredPlugin">' . JText::_('MAINTENANCE_SAMPLE_DATA_INSTALL_REQUIRED_PLUGIN') . '</button>
										<button class="btn" type="button" name="button_installation_sampledata_unsuccessfully" onclick="window.top.location=\'index.php?option=com_imageshow&view=maintenance&s=maintenance&g=data&sub_action=cancelintallation#data-sample-installation\';">' . JText::_('MAINTENANCE_SAMPLE_DATA_CANCEL') . '</button>
									</div>
									<input type="hidden" name="method_install_sample_data" value="manually" />
									<input type="hidden" name="element_type" id="element_type" value="" />
								</form>
							</div>
						</li>';
				$html[] = '</ul>';
				$html[] = '</div>';
				$html[] = '<div id="jsn-installing-sampledata-unsuccessfully">
								<div class="form-actions">
									<button class="btn btn-primary" type="button" name="button_installation_sampledata_unsuccessfully" onclick="window.top.location=\'index.php?option=com_imageshow&view=maintenance&s=maintenance&g=data&sub_action=cancelintallation#data-sample-installation\';">' . JText::_('MAINTENANCE_SAMPLE_DATA_CANCEL') . '</button>
								</div>
							</div>';
				$html[] = '<div id="jsn-installing-sampledata-successfully">
								<hr>
								<h3 class="jsn-text-success">' . JText::_('MAINTENANCE_SAMPLE_DATA_SAMPLE_DATA_IS_SUCCESSFULLY_INSTALLED') .' </h3>
								<p>' . JText::_('MAINTENANCE_SAMPLE_DATA_CONGRATULATIONS_NOW_YOU_CAN_OPERATE_ON_SAMPLE_SHOWLISTS_AND_SHOWCASES') . '</p>
								<div class="form-actions">
									<button class="btn btn-primary agree_install_sample_local" type="button" name="button_installation_sampledata_finish" onclick="window.top.location=\'index.php?option=com_imageshow\';">' . JText::_('MAINTENANCE_SAMPLE_DATA_FINISH') . '</button>
								</div>
							</div>';
			}
			$html[] = '</div></div>';
		}

		//////////////////

		$html[] = JSNHtmlAsset::loadScript('jsn/data',
		array(
						'language' => JSNUtilsLanguage::getTranslated(array('JSN_EXTFW_GENERAL_STILL_WORKING', 'JSN_EXTFW_GENERAL_PLEASE_WAIT'))
		),
		true
		);
		return implode($html);
	}
}
