<?php

/**
 * @version     $Id: view.html.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Forms
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * View class for a list of Forms.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNMobilizeViewProfiles extends JSNBaseView
{

	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @see     fetch()
	 * @since   11.1
	 */
	function display($tpl = null)
	{
		// Get config
		$config = JSNConfigHelper::get();

		// Get messages
		$msgs = '';

		if (!$config->get('disable_all_messages'))
		{
			$msgs = JSNUtilsMessage::getList('PROFIELS');
			$msgs = count($msgs)?JSNUtilsMessage::showMessages($msgs):'';
		}

		// Initialize toolbar
		$this->initToolbar();

		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);

		// Set sub-menu
		JSNMobilizeHelper::addSubmenu('profiles');
		// Display the view
		parent::display($tpl);

		// Load assets
		JSNMobilizeHelper::loadAssets();
		$this->addAssets();

	}

	/**
	 * Load assets.
	 *
	 * @return void
	 */
	protected function addAssets()
	{
		$arrayTranslated = array('JSN_MOBILIZE_UPGRADE_EDITION_TITLE', 'JSN_MOBILIZE_UPGRADE_EDITION', 'JSN_MOBILIZE_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_FORM_IN_FREE_EDITION_0');
		JSNHtmlAsset::loadScript('mobilize/profiles', array('language' => JSNUtilsLanguage::getTranslated($arrayTranslated)));
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since        1.6
	 */
	protected function initToolbar()
	{
		$bar = JToolBar::getInstance('toolbar');
		$edition = defined('JSN_MOBILIZE_EDITION') ? JSN_MOBILIZE_EDITION : "free";
		if (strtolower($edition) == "free")
		{
			$dataListForm = JSNMobilizeHelper::getProfiles();
			$countForm = 1 - count($dataListForm) > 0 ? 1 - count($dataListForm) : 0;
			$msg = JText::sprintf('JSN_MOBILIZE_YOU_HAVE_REACHED_THE_LIMITATION_OF_1_PROFILE_IN_FREE_EDITION', (int) $countForm) . ' <a class="jsn-link-action" href="index.php?option=com_mobilize&view=upgrade">' . JText::_("JSN_MOBILIZE_UPGRADE_EDITION") . '</a>';

			if (count($dataListForm) < 1)
			{
				JToolBarHelper::addNew('profile.add', 'JTOOLBAR_NEW');
				JFactory::getApplication()->enqueueMessage($msg);
			}
			else
			{

				$bar->appendButton('Custom', '<button class="btn btn-small btn-success disabled jsn-popup-upgrade" onclick="return false;"><i class="icon-new icon-white"></i>' . JText::_('JTOOLBAR_NEW') . '</button>');

				$session = JFactory::getSession();
				$seesionQueue = $session->get('application.queue');

				if ($seesionQueue[0]['type'] != "error")
				{
					JError::raiseNotice(100, $msg);
				}
			}
		}else{
			JToolBarHelper::addNew('profile.add', 'JTOOLBAR_NEW');
		}

		JToolBarHelper::editList('profile.edit', 'JTOOLBAR_EDIT');
		!JSNVersion::isJoomlaCompatible('2.5') OR JToolBarHelper::divider();

		JToolBarHelper::publish('profiles.publish', 'JSN_MOBILIZE_PUBLISH', true);
		JToolBarHelper::unpublish('profiles.unpublish', 'JSN_MOBILIZE_UNPUBLISH', true);

		!JSNVersion::isJoomlaCompatible('2.5') OR JToolBarHelper::divider();

		JToolBarHelper::deleteList('JSN_MOBILIZE_CONFIRM_DELETE', 'profiles.delete', 'JTOOLBAR_DELETE');

		!JSNVersion::isJoomlaCompatible('2.5') OR JToolBarHelper::divider();

		JSNMobilizeHelper::initToolbar('JSN_MOBILIZE_PROFILES_MANAGER', 'mobilize-profiles');
	}

	/**
	 * Reder OS Support
	 *
	 * @param   type  $item  itemlist
	 *
	 * @return html code
	 */
	public function renderOSSupport($item)
	{
		$getOsSupport = JSNMobilizeHelper::getOsSupportByProfileId($item->profile_id);
		$opSupport = "";
		$opSupports = array();
		foreach ($getOsSupport as $os)
		{
			$opSupports[$os->os_type][] = $os->os_title;
		}
		foreach ($opSupports as $item)
		{
			if ($item)
			{
				$opSupport[] = "<li>" . implode(", ", $item) . "</li>";
			}
		}
		if ($opSupport)
		{
			$opSupport = "<ul>" . implode("\n", $opSupport) . "</ul>";
			$html = "<a original-title=\"" . $opSupport . "\" class=\"control-label jsn-tipsy\" href='javascript:void(0);'><i class=\"icon-question-sign\"></i></a>";
		}
		else
		{
			$html = "<a original-title=\"" . JText::_("JSN_MOBILIZE_NO_SUPPORT_OS") . "\" class=\"control-label jsn-tipsy\" href='javascript:void(0);'><i class=\"icon-warning\"></i></a>";
		}
		return $html;
	}

	/**
	 * Reder Theme
	 *
	 * @param   type  $item  itemlist
	 *
	 * @return html code
	 */
	public function renderTheme($item)
	{
		$getProfile = JSNMobilizeHelper::getDesignByProfileId($item->profile_id);

		foreach ($getProfile as $profile)
		{
			if ($profile->name == "style")
			{
				if (strpos($profile->value, "jsn_") === false)
				{
					if ($profile->value == "ios")
					{
						$value = "iOS";
					}
					else
					{
						$value = ucfirst($profile->value);
					}
				}
				else
				{
					$value = str_replace("jsn_", "", $profile->value);
					$value = "JSN " . ucfirst($value);
				}
				return $value;
			}
		}

	}
}
