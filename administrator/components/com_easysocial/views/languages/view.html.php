<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include main views file.
FD::import( 'admin:/views/views' );

class EasySocialViewLanguages extends EasySocialAdminView
{
	/**
	 * Default user listings page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display( $tpl = null )
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_LANGUAGES');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_LANGUAGES');

		// Get the server keys
		$key = $this->config->get('general.key');

		if (!$key) {

			$return = base64_encode('index.php?option=com_easysocial&view=languages');

			$this->set('return', $return);

			return parent::display('admin/settings/key');
		}

		// Check if there's any data on the server
		$model = FD::model('Languages', array('initState' => true));
		$initialized = $model->initialized();

		if (!$initialized) {

			$this->set('key', $key);

			return parent::display('admin/languages/initialize');
		}

		// Add Joomla buttons
		JToolbarHelper::custom( 'discover' , 'refresh' , '' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_BUTTON_FIND_UPDATES' ) , false );
		JToolbarHelper::custom( 'purge' , 'purge' , '' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_BUTTON_PURGE_CACHE') , false );
		JToolbarHelper::divider();
		JToolbarHelper::custom('install', 'upload' , '' , JText::_('COM_EASYSOCIAL_TOOLBAR_BUTTON_INSTALL_OR_UPDATE'));
		JToolbarHelper::custom('uninstall', 'remove', '', JText::_('COM_EASYBLOG_TOOLBAR_BUTTON_UNINSTALL'));
		

		// Get filter states.
		$ordering  = $this->input->get('ordering', $model->getState('ordering'), 'cmd');
		$direction = $this->input->get('direction', $model->getState('direction'), 'cmd');
		$limit = $model->getState('limit');
		$published = $model->getState('published');

		// Get the list of languages now
		$languages = $model->getLanguages();

		foreach ($languages as &$language) {

			$translators = json_decode($language->translator);
			$language->translator 	= $translators;
		}

		$pagination	= $model->getPagination();

		$this->set('ordering', $ordering);
		$this->set('direction', $direction);
		$this->set('languages', $languages);
		$this->set('pagination', $pagination);

		return parent::display('admin/languages/default');
	}

	/**
	 * Discover languages from our server
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function discover()
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_LANGUAGES');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_LANGUAGES');

		// Get the stored settings
		$key = $this->config->get('general.key');

		$this->set('key', $key);

		return parent::display('admin/languages/initialize');
	}

	/**
	 * Post processing after uninstall happens
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function uninstall()
	{
		$this->info->set($this->getMessage());
		$this->redirect('index.php?option=com_easysocial&view=languages');
	}

	/**
	 * Post processing after purge happens
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function purge()
	{
		$this->info->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=languages');
	}

	/**
	 * Post processing after language has been installed
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function install()
	{
		$this->info->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=languages');
	}
}
