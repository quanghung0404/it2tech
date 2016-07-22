<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPL
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
 * JSNCheckbox field
 *
 * @package     JSNTPL
 * @subpackage  Form
 * @since       2.0.0
 */
class JFormFieldJSNMobileMenuIconType extends JSNTPLFormField
{
	public $type = 'JSNMobileMenuIconType';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$request = JFactory::getApplication()->input;

		if (class_exists('JModelLegacy'))
		{
			$templateModel = JModelLegacy::getInstance('Style', 'TemplatesModel');
		}
		else
		{
			$templateModel = JModel::getInstance('Style', 'TemplatesModel');
		}

		$templateData 	= $templateModel->getItem($request->getInt('id'));
		$templateXml 	= JSNTplHelper::getManifest($templateData->template);

		$mobileMenuIconType = (string) $templateXml->mobileMenuIconType;

		if (strtolower($mobileMenuIconType) != 'yes')
		{
			$html[] = '<script type="text/javascript">
				(function($) {
					$(document).ready(function() {
						$("#jsn_mobileMenuIconTypeText").parent().parent().prev().hide();
						$("#jsn_mobileMenuIconTypeText").parent().parent().hide();
					});
				})(jQuery);
			</script>';
		}
		else
		{
			$html[] = '<script type="text/javascript">
				(function($) {
					$(document).ready(function() {

						if ($("input[name=\'jsn[mobileMenuIconType]\']:checked").val() == "text")
						{
							$("#jsn_mobileMenuIconTypeText").parent().parent().show();
						} else {
							$("#jsn_mobileMenuIconTypeText").parent().parent().hide();
						}

						$(".radio input[name=\'jsn[mobileMenuIconType]\']").click(function(){
						    if ($(this).val() == "text") {
						    	$("#jsn_mobileMenuIconTypeText").parent().parent().show();
						    } else {
						    	$("#jsn_mobileMenuIconTypeText").parent().parent().hide();
						    }
						});
					});
				})(jQuery);
			</script>';
		}
		$data = array();
		$options = array('default' => ($this->value == '' ? (int) $this->element['default'] : $this->value));

		if (isset($this->element['disabled']) && $this->element['disabled'] == 'true') {
			$options['class'] = 'disabled';
			$options['disabled'] = 'disabled';
		}

		// Get all radio options from xml
		foreach ($this->element->children() as $option) {
			$data[] = array(
					'value' => $option['value'],
					'text'  => (string) $option
			);
		}

		$html[] = JSNTplFormHelper::radio($this->name, $data, $options);

		return implode($html);
	}
}
