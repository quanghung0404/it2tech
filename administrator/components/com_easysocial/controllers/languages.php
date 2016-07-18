<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/controllers/controller');

class EasySocialControllerLanguages extends EasySocialController
{
	/**
	 * Purges the cache of language items
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function purge()
	{
		// Check for request forgeries here
		ES::checkToken();

		// Get the current view
		$view = $this->getCurrentView();

		$model	 = ES::model( 'Languages' );
		$model->purge();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_LANGUAGES_PURGED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Allows caller to remove languages
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function uninstall()
	{
		// Check for request forgeries here
		ES::checkToken();

		// Get the list of items to be deleted
		$ids = $this->input->get('cid', array(), 'array');

		foreach ($ids as $id) {
			$id = (int) $id;

			$table = ES::table('Language');
			$table->load($id);

			if (!$table->isInstalled()) {
				continue;
			}

			$table->uninstall();
		}

		$this->view->setMessage('COM_EASYSOCIAL_LANGUAGES_UNINSTALLED_SUCCESS', SOCIAL_MSG_SUCCESS);
		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Installs a language file
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function install()
	{
		// Check for request forgeries here
		ES::checkToken();

		// Get the language id's to install
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_LANGUAGES_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		foreach ($ids as $id) {
			$table = ES::table('Language');
			$table->load($id);

			$table->install();
		}

		$this->view->setMessage( JText::_( 'COM_EASYSOCIAL_LANGUAGES_INSTALLED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $this->view->call( __FUNCTION__ );
	}


	/**
	 * Retrieves a list of languages from API server
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getLanguages()
	{
		// Check for request forgeries here
		ES::checkToken();

		// Get the stored key
		$key = $this->config->get('general.key');

		// Start connecting
		$connector = ES::connector();
		$connector->addUrl(SOCIAL_UPDATER_LANGUAGE);
		$connector->setMethod('POST');
		$connector->addQuery('key', $key);
		$connector->connect();

		$result = $connector->getResult(SOCIAL_UPDATER_LANGUAGE);

		$obj = json_decode($result);

		if (!$obj || !isset($obj->code) || $obj->code != 200) {
			return $this->view->call(__FUNCTION__, $obj);
		}

		// Go through each of the languages now
		foreach ($obj->languages as $language) {

			// Check if the language was previously installed thorugh our system.
			// If it does, load it instead of overwriting it.
			$table = ES::table('Language');
			$exists = $table->load(array('locale' => $language->locale));

			// We do not want to bind the id
			unset($language->id);


			// Since this is the retrieval, the state should always be disabled
			if (!$exists) {
				$table->state	= SOCIAL_STATE_UNPUBLISHED;
			}

			// If the language file has been installed, we want to check the last updated time
			if ($exists && $table->state == SOCIAL_LANGUAGES_INSTALLED) {

				// Then check if the language needs to be updated. If it does, update the ->state to SOCIAL_LANGUAGES_NEEDS_UPDATING
				// We need to check if the language updated time is greater than the local updated time
				$languageTime 		= strtotime($language->updated);
				$localLanguageTime	= strtotime($table->updated);

				if ($languageTime > $localLanguageTime && $table->state == SOCIAL_LANGUAGES_INSTALLED) {
					$table->state	= SOCIAL_LANGUAGES_NEEDS_UPDATING;
				}
			}

			// Set the title
			$table->title 		= $language->title;

			// Set the locale
			$table->locale		= $language->locale;

			// Set the translator
			$table->translator	= $language->translator;

			// Set the updated time
			$table->updated 	= $language->updated;

			// Update the progress
			$table->progress 	= $language->progress;

			// Update the table with the appropriate params
			$params = ES::registry();

			$params->set('download', $language->download);
			$params->set('md5', $language->md5);
			$table->params 	= $params->toString();

			$table->store();
		}

		return $this->view->call(__FUNCTION__, $obj);
	}
}
