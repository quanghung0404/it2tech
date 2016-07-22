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
class JFormFieldJSNMobileMenuEffect extends JSNTPLFormField
{
	public $type = 'JSNMobileMenuEffect';

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
		
		$mobileMenuEffect = (string) $templateXml->mobileMenuEffect;

		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();
		
		//if (strtolower($mobileMenuEffect) != 'yes')
		//{	
			//Only get default if the template does not support Mobile Menu Effect
			//$options = array(@$options[0]);
		//}
		
		if (strtolower($mobileMenuEffect) != 'yes')
		{
			$html[] = '<script type="text/javascript">		
				(function($) {
					$(document).ready(function() {
						$("select[name=\'jsn[mobileMenuEffect]\']").parent().parent().hide();
					});
				})(jQuery);
			
			</script>';
		}
		// Create a read-only list (no name) with hidden input(s) to store the value(s).
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);

			// E.g. form field type tag sends $this->value as array
			if ($this->multiple && is_array($this->value))
			{
				if (!count($this->value))
				{
					$this->value[] = '';
				}

				foreach ($this->value as $value)
				{
					$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"/>';
				}
			}
			else
			{
				$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
			}
		}
		else
		// Create a regular list.
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
	}
	
	
	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();
	
		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}
	
			// Filter requirements
			if ($requires = explode(',', (string) $option['requires']))
			{
				// Requires multilanguage
				if (in_array('multilanguage', $requires) && !JLanguageMultilang::isEnabled())
				{
					continue;
				}
	
				// Requires associations
				if (in_array('associations', $requires) && !JLanguageAssociations::isEnabled())
				{
					continue;
				}
			}
	
			$value = (string) $option['value'];
	
			$disabled = (string) $option['disabled'];
			$disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');
	
			$disabled = $disabled || ($this->readonly && $value != $this->value);
	
			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
					'select.option', $value,
					JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text',
					$disabled
			);
	
			// Set some option attributes.
			$tmp->class = (string) $option['class'];
	
			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];
	
			// Add the option object to the result set.
			$options[] = $tmp;
		}
	
		reset($options);
	
		return $options;
	}	
}
