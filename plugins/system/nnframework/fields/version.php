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

require_once dirname(__DIR__) . '/helpers/field.php';

class JFormFieldNN_Version extends NNFormField
{
	public $type = 'Version';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$extension = $this->get('extension');
		$xml       = $this->get('xml');

		if (!$xml && $this->form->getValue('element'))
		{
			if ($this->form->getValue('folder'))
			{
				$xml = 'plugins/' . $this->form->getValue('folder') . '/' . $this->form->getValue('element') . '/' . $this->form->getValue('element') . '.xml';
			}
			else
			{
				$xml = 'administrator/modules/' . $this->form->getValue('element') . '/' . $this->form->getValue('element') . '.xml';
			}
			if (!JFile::exists(JPATH_SITE . '/' . $xml))
			{
				return '';
			}
		}

		if (!strlen($extension) || !strlen($xml))
		{
			return '';
		}

		$authorise = JFactory::getUser()->authorise('core.manage', 'com_installer');
		if (!$authorise)
		{
			return '';
		}

		require_once dirname(__DIR__) . '/helpers/versions.php';

		return '</div><div class="hide">' . NoNumberVersions::render($extension);
	}
}
