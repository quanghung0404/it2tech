<?php
/**
 * @version		$Id: view.html.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSViewCategory extends FPSSView
{

	function display($tpl = null)
	{

		JRequest::setVar('hidemainmenu', 1);
		JHTML::_('behavior.keepalive');
		$model = $this->getModel();
		$model->setState('id', JRequest::getInt('id'));
		$category = $model->getData();
		if (!$category->id)
		{
			$category->published = 1;
			$category->name = JText::_('FPSS_NAME');
		}
		JFilterOutput::objectHTMLSafe($category, ENT_QUOTES, 'params');
		$this->assignRef('row', $category);

		$lists = array();
		$lists['published'] = JHTML::_('select.booleanlist', 'published', '', $category->published);
		// Joomla! 1.6 language
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$languages = JHTML::_('contentlanguage.existing', true, true);
			$lists['language'] = JHTML::_('select.genericlist', $languages, 'language', '', 'value', 'text', $category->language);
		}
		$this->assignRef('lists', $lists);
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			jimport('joomla.form.form');
			$form = JForm::getInstance('fpssCategoryForm', JPATH_COMPONENT.DS.'models'.DS.'category.xml');
			$values = array('params' => json_decode($category->params));
			$form->bind($values);
			$model = version_compare(JVERSION, '3.0', 'ge') ? JModelLegacy::getInstance('Helper', 'FPSSModel') : JModel::getInstance('Helper', 'FPSSModel');
			$model->set('assetType', 'category');
			$permissions = $model->getForm();
			$rules = $permissions->getInput('rules').$permissions->getInput('asset_id');
			$this->assignRef('rules', $rules);
		}
		else
		{
			$form = new JParameter('', JPATH_COMPONENT.DS.'models'.DS.'category.xml');
			$form->loadINI($category->params);
		}
		$this->assignRef('form', $form);

		$title = ($category->id) ? JText::_('FPSS_EDIT_CATEGORY') : JText::_('FPSS_ADD_CATEGORY');
		$this->assignRef('title', $title);

		$this->loadHelper('html');
		FPSSHelperHTML::title($title);
		FPSSHelperHTML::toolbar();

		parent::display($tpl);

	}

}
