<?php
/**
 * @version		$Id: category.php 2065 2012-10-25 11:47:56Z lefteris.kavadas $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

require_once (JPATH_ADMINISTRATOR.'/components/com_fpss/helpers/legacy.php');
FPSSHelperLegacy::setup();

if (version_compare(JVERSION, '1.6', '<'))
{
	jimport('joomla.html.parameter.element');
	class FPSSElement extends JElement
	{
	}

}
else
{
	jimport('joomla.form.formfield');
	class FPSSElement extends JFormField
	{

		function getInput()
		{
			return $this->fetchElement($this->name, $this->value, $this->element, $this->options['control']);
		}

		function getLabel()
		{
			if (method_exists($this, 'fetchTooltip'))
			{
				return $this->fetchTooltip($this->element['label'], $this->description, $this->element, $this->options['control'], $this->element['name'] = '');
			}
			else
			{
				return parent::getLabel();
			}

		}

		function render()
		{
			return $this->getInput();
		}

	}

}
