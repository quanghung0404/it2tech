<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewThemes extends EasyBlogAdminView
{
	/**
	 * Displays the theme listings
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.theme');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Set heading text
		$this->setHeading('COM_EASYBLOG_TITLE_THEMES', '', 'fa-flask');

		// Get themes
		$model = EB::model('Themes');
		$themes = $model->getThemes();

		$this->set('default', $this->config->get('theme_site'));
		$this->set('themes', $themes);
		$this->set('search', '');

		parent::display('themes/default');
	}

	/**
	 * Displays the theme installer form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function install($tpl = null)
	{
		// Set heading text
		$this->setHeading('COM_EASYBLOG_THEMES_INSTALL', '', 'fa-flask');

		parent::display('themes/install');
	}

	public function registerToolbar()
	{
		if ($this->getLayout() == 'install') {
			JToolBarHelper::title(JText::_('COM_EASYBLOG_THEMES_INSTALL'), 'themes');
			JToolbarHelper::divider();
			JToolBarHelper::custom('themes.upload', 'save', '', JText::_('COM_EASYBLOG_UPLOAD_AND_INSTALL_BUTTON'), false);
			return;
		}

		JToolBarHelper::title(JText::_('COM_EASYBLOG_THEMES_TITLE'), 'themes');


		JToolBarHelper::custom('themes.setDefault', 'star', '', JText::_('COM_EASYBLOG_SET_DEFAULT'), false);
		JToolbarHelper::divider();
		JToolBarHelper::custom('themes.recompile', 'wand', '', JText::_('COM_EASYBLOG_COMPILE_LESS_FILES'), false);
	}
}
