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

class FPSSViewSlide extends FPSSView
{

	function display($tpl = null)
	{

		$mainframe = JFactory::getApplication();
		JHTML::_('behavior.keepalive');
		$params = JComponentHelper::getParams('com_fpss');
		$db = JFactory::getDBO();
		$document = JFactory::getDocument();
		$config = JFactory::getConfig();
		JRequest::setVar('hidemainmenu', 1);
		$model = FPSSModel::getInstance('slide', 'FPSSModel');
		$model->setState('id', JRequest::getInt('id'));
		$slide = $model->getData();
		$model->getSlideImages($slide);
		$slide->reference = '';
		if (!$slide->id)
		{
			$slide->title = JText::_('FPSS_TITLE');
			$slide->tagline = JText::_('FPSS_TAGLINE');
			$slide->published = 1;
			$date = JFactory::getDate();
			$slide->publish_up = version_compare(JVERSION, '1.6.0', '<') ? $date->toMySQL() : $date->toSql();
			$slide->publish_down = $db->getNullDate();
			$slide->referenceType = 'custom';
			$slide->reference = JText::_('FPSS_URL');
		}
		if ($slide->referenceType == 'custom' && !empty($slide->custom))
		{
			$slide->reference = $slide->custom;
		}

		$lists = array();

		// Convert dates to local offset
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$dateFormat = JText::_('FPSS_J16_CALENDAR_DATE_FORMAT');
		}
		else
		{
			$dateFormat = JText::_('FPSS_CALENDAR_DATE_FORMAT');
		}
		$slide->publish_up = JHTML::_('date', $slide->publish_up, $dateFormat);
		if ($slide->publish_down == $db->getNullDate())
		{
			$slide->publish_down = '';
		}
		else
		{
			$slide->publish_down = JHTML::_('date', $slide->publish_down, $dateFormat);
		}

		// Set up calendar regional settings
		$document->addScriptDeclaration("
			\$FPSS.datepicker.setDefaults( {
				closeText: 'Done',
				prevText: 'Prev',
				nextText: 'Next',
				currentText: 'Today',
				monthNames: ['January','February','March','April','May','June',
				'July','August','September','October','November','December'],
				monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
				'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
				dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
				dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],
				weekHeader: 'Wk',
				firstDay: 1,
				isRTL: false,
				showMonthAfterYear: false,
				yearSuffix: ''});
		");

		// Joomla! 1.6 language
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$languages = JHTML::_('contentlanguage.existing', true, true);
			$lists['language'] = JHTML::_('select.genericlist', $languages, 'language', '', 'value', 'text', $slide->language);
		}

		JFilterOutput::objectHTMLSafe($slide, ENT_QUOTES, array(
			'text',
			'params'
		));
		$this->assignRef('row', $slide);

		$lists['published'] = JHTML::_('select.booleanlist', 'published', '', $slide->published);
		$lists['featured'] = JHTML::_('select.booleanlist', 'featured', '', $slide->featured);

		$model = FPSSModel::getInstance('categories', 'FPSSModel');
		$model->setState('published', -1);
		$model->setState('ordering', 'category.name');
		$model->setState('orderingDir', 'ASC');
		$categories = $model->getData();
		if (empty($categories))
		{
			$mainframe->redirect('index.php?option=com_fpss&view=category', JText::_('FPSS_YOU_HAVE_TO_CREATE_A_CATEGORY_FIRST'), 'notice');
		}

		// Check the categories for user permissions
		if (version_compare(JVERSION, '1.6.0', 'ge') && !$slide->id)
		{
			$user = JFactory::getUser();
			foreach ($categories as $key => $category)
			{
				if (!$user->authorise('core.create', 'com_fpss.category.'.$category->id))
				{
					unset($categories[$key]);
				}
			}
		}

		$lists['category'] = JHTML::_('select.genericlist', $categories, 'catid', '', 'id', 'name', $slide->catid);
		jimport('joomla.html.parameter');
		if ($slide->catid)
		{
			foreach ($categories as $category)
			{
				if ($category->id == $slide->catid)
				{
					$activeCategory = $category;
				}
			}
		}
		else
		{
			$activeCategory = reset($categories);
		}
		$activeCategoryParameters = version_compare(JVERSION, '1.6.0', 'ge') ? new JRegistry($activeCategory->params) : new JParameter($activeCategory->params);
		$js = 'var categoriesDimensions = new Array();';
		foreach ($categories as $key => $category)
		{
			$categoryParameters = version_compare(JVERSION, '1.6.0', 'ge') ? new JRegistry($category->params) : new JParameter($category->params);
			$js .= 'categoriesDimensions['.$key.'] = new Array('.$categoryParameters->get('imageWidth', 400).', '.$categoryParameters->get('thumbWidth', 100).', '.$categoryParameters->get('previewWidth', 600).');';
			if ($category->id == $slide->catid)
			{
				$activeCategoryParameters = $categoryParameters;
			}
		}
		
		$document->addScriptDeclaration($js);
		$lists['mainImageWidth'] = $activeCategoryParameters->get('imageWidth', 400);
		$lists['thumbImageWidth'] = $activeCategoryParameters->get('thumbWidth', 100);
		$lists['previewImageWidth'] = $activeCategoryParameters->get('previewWidth', 600);

		$lists['access'] = version_compare(JVERSION, '3.0', 'ge') ? JHTML::_('access.level', 'access', $slide->access) : JHTML::_('list.accesslevel', $slide);
		$lists['access'] = JString::str_ireplace('size="3"', '', $lists['access']);
		
		if ($params->get('wysiwyg'))
		{
			// Determine the editor that will be used. Only JCE and tinyMCE are supported.
			$editor = version_compare(JVERSION, '1.6.0', 'ge') ? $config->get('editor') : $config->getValue('config.editor');
			if ($editor != 'jce' && $editor != 'tinymce')
			{
				if (JPluginHelper::isEnabled('editors', 'tinymce'))
				{
					$editor = 'tinymce';
				}
			}
			// Get the editor
			if (JPluginHelper::isEnabled('editors', $editor))
			{
				$wysiwyg = JFactory::getEditor($editor);
				$lists['wysiwyg'] = $wysiwyg->display('text', $slide->text, '100%', '300', '40', '5', array(
					'pagebreak',
					'readmore',
					'image',
					'rokcandy_button',
					'rokcandy'
				));
				$js = 'var wysiwyg = true;';
			}
			else
			{
				$mainframe->enqueueMessage(JText::_('FPSS_WYSIWYG_HAS_BEEN_DISABLED_BECAUSE_THE_TINYMCE_EDITOR_PLUGIN_IS_DISABLED_PLEASE_ENABLE_THE_TINYMCE_EDITOR_PLUGIN_TO_USE_THIS_FEATURE'), 'notice');
				$params->set('wysiwyg', 0);
				$js = 'var wysiwyg = false;';
			}
		}
		else
		{
			$js = 'var wysiwyg = false;';
		}
		$js .= ' var sizeNote = "'.JText::_('FPSS_NOTE').': '.JText::_('FPSS_THE_IMAGE_HAS_BEEN_SCALED_DOWN_TO_FIT_YOUR_BROWSER_SCREEN_ACTUAL_IMAGE_SIZE').' '.'";';
		$js .= ' var linkNote = "'.JText::_('FPSS_NOTE').': '.JText::_('FPSS_THIS_SLIDE_LINKS_TO_A_THIRD_PARTY_EXTENSION').':'.'";';
		$document->addScriptDeclaration($js);

		if ($slide->id)
		{
			$lists['created'] = JHTML::_('date', $slide->created, JText::_('DATE_FORMAT_LC2'));
		}
		else
		{
			$lists['created'] = JText::_('FPSS_NEW_SLIDE');
		}

		if ($slide->modified == $db->getNullDate() || !$slide->id)
		{
			$lists['modified'] = JText::_('FPSS_NEVER');
		}
		else
		{
			$lists['modified'] = JHTML::_('date', $slide->modified, JText::_('DATE_FORMAT_LC2'));
		}

		$author = JFactory::getUser($slide->created_by);
		$lists['created_by'] = $author->name;
		if ($slide->modified_by)
		{
			$moderator = JFactory::getUser($slide->modified_by);
			$lists['modified_by'] = $moderator->name;
		}
		else
		{
			$lists['modified_by'] = JText::_('FPSS_NONE');
		}
		$this->assignRef('lists', $lists);

		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			jimport('joomla.form.form');
			$form = JForm::getInstance('fpssSlideForm', JPATH_COMPONENT.DS.'models'.DS.'slide.xml');
			$values = array('params' => json_decode($slide->params));
			$form->bind($values);
			$slide->useOriginal = isset($values['params']->useOriginal) ? $values['params']->useOriginal : '0';
			$slide->authorAlias = isset($values['params']->authorAlias) ? $values['params']->authorAlias : '';
			$model = version_compare(JVERSION, '3.0', 'ge') ? JModelLegacy::getInstance('Helper', 'FPSSModel') : JModel::getInstance('Helper', 'FPSSModel');
			$model->set('assetType', 'slide');
			$permissions = $model->getForm();
			$rules = $permissions->getInput('rules').$permissions->getInput('asset_id');
			$this->assignRef('rules', $rules);
		}
		else
		{
			$form = new JParameter('', JPATH_COMPONENT.DS.'models'.DS.'slide.xml');
			$form->loadINI($slide->params);
			$slide->useOriginal = $form->get('useOriginal');
			$slide->authorAlias = $form->get('authorAlias');
		}
		$this->assignRef('form', $form);

		$this->loadHelper('extension');

		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$articlesModalLink = "index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=j16SelectArticle";
		}
		else
		{
			$articlesModalLink = "index.php?option=com_content&amp;task=element&amp;tmpl=component";
		}
		$this->assignRef('articlesModalLink', $articlesModalLink);

		JHTML::_('behavior.modal');
		$this->assignRef('params', $params);
		$title = ($slide->id) ? JText::_('FPSS_EDIT_SLIDE') : JText::_('FPSS_ADD_SLIDE');
		$this->assignRef('title', $title);
		$this->loadHelper('html');
		FPSSHelperHTML::title($title);
		FPSSHelperHTML::toolbar();
		parent::display($tpl);

	}

}
