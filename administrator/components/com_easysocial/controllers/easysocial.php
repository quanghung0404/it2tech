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

FD::import( 'admin:/controllers/controller' );

class EasySocialControllerEasySocial extends EasySocialController
{
	/**
	 * Checks to see if there are any new columns that are added to the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sync()
	{
		// FD::checkToken();
		$affected = FD::syncDB();

		if (!$affected) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_NO_COLUMNS_TO_UPDATE'));
		} else {
			$this->view->setMessage(JText::sprintf('COM_EASYSOCIAL_UPDATED_COLUMNS', $affected));
		}

		return $this->view->call( __FUNCTION__ );
	}

	/**
	 * Retrieves a list of unique countries
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCountries()
	{
		// Check for request forgeries
		FD::checkToken();

		$model = FD::model('Users');

		// Get a list of countries
		$countries = $model->getUniqueCountries();

		return $this->view->call(__FUNCTION__, $countries);
	}

	/**
	 * Purges the less cache files on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function clearCache()
	{
		// Check for request forgeries
		FD::checkToken();

		// Determines if we should purge all javascripts.
		$purgeScripts = $this->input->get('script-cache', false, 'bool');

		// Clear javascript files
		if ($purgeScripts) {
			FD::purgeJavascriptResources();
		}

		// Determines if we should purge the cached less stylesheets
		$purgeCssCache = $this->input->get('stylesheet-cache', false, 'bool');

		if ($purgeCssCache) {

			$templates = JFolder::folders(EASYSOCIAL_SITE_THEMES);

			foreach ($templates as $template) {
				$task = FD::stylesheet('site', $template)->purge();
			}

			// Compile admin themes
			$templates = JFolder::folders(EASYSOCIAL_ADMIN_THEMES);
			foreach ($templates as $template) {
				$task = FD::stylesheet('admin', $template)->purge();
			}

			// Compile modules
			$modules = FD::stylesheet('module')->modules();
			foreach ($modules as $module) {
				$task = FD::stylesheet('module', $module)->purge();
			}
		}

		$message = JText::sprintf('COM_EASYSOCIAL_CACHE_PURGED_FROM_SITE');

		$this->view->setMessage($message, SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__);
	}
}
