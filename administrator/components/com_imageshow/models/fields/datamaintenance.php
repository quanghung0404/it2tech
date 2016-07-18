<?php
/**
 * @version     $Id: datamaintenance.php 16077 2012-09-17 02:30:25Z giangnd $
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
class JFormFieldDataMaintenance extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	protected $type = 'DataMaintenance';

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
		$token 	= JFactory::getSession()->getFormToken();

		// Preset output
		$html = array();

		// Generate data backup form
		$html[] = '<form action="index.php" name="adminFormDatamaintenance" id="frm_datamaintenance">';
		$html[] = '<fieldset>';
		$html[] = '<legend>';
		$html[] = JText::_("MAINTENANCE_RECREATE_THUMBNAILS") . '&nbsp;';
		$html[] = '<span class="jsn-icon16 jsn-icon-loading" id="jsn-creating-thumbnail"></span>';
		$html[] = '<span class="jsn-icon16 jsn-icon-ok" id="jsn-creat-thumbnail-successful"></span>';
		$html[] = '<span class="jsn-icon16 jsn-icon-warning-sign" id="jsn-creat-thumbnail-unsuccessful"></span>';
		$html[] = '</legend>';
		$html[] = '<div class="control-group">';
		$html[] = '<p>' . JText::_('MAINTENANCE_THIS_PROCESS_WILL_RECREATE_ALL_THUMBNAILS') . '</p>';
		$html[] = '</div>';
		$html[] = '<div class="form-actions">';
		$html[] = '<a class="btn btn-primary" id="jsn-button-delete-obsolete-thumnail" href="javascript: void(0);" value="' . JText::_('MAINTENANCE_START') . '" onclick="JSNISImageShow.deleteObsoleteThumbnails(\'' . $token . '\')">' . JText::_('MAINTENANCE_START') . '</a>';
		$html[] = '</div>';
		$html[] = '</fieldset>';
		$html[] = '</form>';
		return implode($html);
	}
}
