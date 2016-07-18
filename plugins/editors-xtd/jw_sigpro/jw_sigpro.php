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

jimport('joomla.plugin.plugin');

class plgButtonJw_SigPro extends JPlugin
{

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage('', JPATH_ADMINISTRATOR);
	}

	function onDisplay($name)
	{
		$document = JFactory::getDocument();
		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js');
		}
		else
		{
			JHtml::_('jquery.framework');
		}
		$document->addScript(JURI::root(true).'/administrator/components/com_sigpro/js/fancybox/jquery.fancybox.pack.js?v=3.1.0');
		$document->addStyleSheet(JURI::root(true).'/administrator/components/com_sigpro/js/fancybox/jquery.fancybox.css?v=3.1.0');
		$document->addStyleSheet(JURI::root(true).'/administrator/components/com_sigpro/css/editor.css?v=3.1.0');
		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			$option = JRequest::getCmd('option');
			if ($option == 'com_virtuemart' || $option == 'com_tienda' || $option == 'com_k2')
			{
				$document->addScript(JURI::root(true).'/administrator/components/com_sigpro/js/jquery.noconflict.restore.js?v=3.1.0');
			}
			else
			{
				$document->addScript(JURI::root(true).'/administrator/components/com_sigpro/js/jquery.noconflict.js?v=3.1.0');
			}

		}
		$document->addScript(JURI::root(true).'/administrator/components/com_sigpro/js/editor.js?v=3.1.0');
		$button = new JObject();
		$link = 'index.php?option=com_sigpro&amp;tmpl=component&amp;type=site&amp;editorName='.$name;
		$j35Link = 'index.php?option=com_sigpro&tmpl=component&type=site&editorName='.$name;
		$application = JFactory::getApplication();
		if ($application->isSite())
		{
			$link .= '&amp;template=system';
			$j35Link .= '&template=system';
		}
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_EDITORS-XTD_JW_SIGPRO_IMAGE_GALLERIES'));
		$button->set('name', 'sigProEditorButton');
		$button->set('onclick', 'SigProModal(this); return false;');
		if(version_compare(JVERSION, '3.0', 'ge'))
		{
			$button->class = 'btn';
		}
		if(version_compare(JVERSION, '3.5', 'ge'))
		{
			$config = JFactory::getConfig();
			$user = JFactory::getUser();
			$editor = $user->getParam('editor', $config->get('editor'));
			if($editor == 'tinymce') {
				$button->modal = false;
				$button->link = '#';
			}
			$button->set('onclick', 'SigProModal(this, \''.$j35Link.'\'); return false;');
		}
		return $button;
	}

}
