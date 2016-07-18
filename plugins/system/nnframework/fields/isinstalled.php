<?php
/**
 * @package         NoNumber Framework
 * @version         16.1.9037
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.form.formfield');

require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/field.php';

class JFormFieldNN_IsInstalled extends NNFormField
{
	public $type = 'IsInstalled';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$is_installed = NNFrameworkFunctions::extensionInstalled($this->get('extension'), $this->get('extension_type'), $this->get('folder'));

		return '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . (int) $is_installed . '" />';
	}
}
