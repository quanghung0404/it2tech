<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

class SigProViewSettings extends SigProView
{
	public function display($tpl = null)
	{
		if (version_compare(JVERSION, '2.5.0', 'ge'))
		{
			if (!JFactory::getUser()->authorise('core.admin', 'com_sigpro'))
			{
				JFactory::getApplication()->redirect('index.php?option=com_sigpro', JText::_('JERROR_ALERTNOAUTHOR'));
				return;
			}
		}
		JHTML::_('behavior.tooltip');
		$model = $this->getModel();
		$model->setState('option', 'com_sigpro');
		$form = $model->getForm();
		$this->assignRef('form', $form);
		$id = $model->getExtensionID();
		$this->assignRef('id', $id);
		parent::display($tpl);
	}

}
