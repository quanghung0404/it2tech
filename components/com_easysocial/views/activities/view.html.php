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

// Necessary to import the custom view.
FD::import( 'site:/views/views' );

class EasySocialViewActivities extends EasySocialSiteView
{
	/**
	 * Responsible to output the activity log for a user
	 *
	 * @access	public
	 * @return	null
	 */
	public function display($tpl = null)
	{
		// Unauthorized users should not be allowed to access this page.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get the necessary attributes from the request
		$filterType = $this->input->get('type', 'all', 'default');
		$active = $filterType;
		$context = SOCIAL_STREAM_CONTEXT_TYPE_ALL;
		
		// Default title 
		$title = JText::sprintf('COM_EASYSOCIAL_ACTIVITY_ITEM_TITLE', ucfirst($filterType));

		if ($filterType == 'all') {
			$title = JText::_('COM_EASYSOCIAL_ACTIVITY_YOUR_LOGS');
		}

		if ($filterType == 'hidden') {
			$title = JText::_('COM_EASYSOCIAL_ACTIVITY_YOUR_HIDDEN_ACTIVITIES');
		}

		if ($filterType == 'hiddenapp') {
			$title = JText::_('COM_EASYSOCIAL_ACTIVITY_YOUR_HIDDEN_APPS');
		}

		if ($filterType == 'hiddenactor') {
			$title = JText::_('COM_EASYSOCIAL_ACTIVITY_YOUR_HIDDEN_ACTORS');
		}

		// Set the page title
		$this->page->title($title);

		// Set the page breadcrumb
		$this->page->breadcrumb($title);

		if ($filterType != 'all' && $filterType != 'hidden' && $filterType != 'hiddenapp' && $filterType != 'hiddenactor') {
			$context = $filterType;
			$filterType = 'all';
		}

		// Load up activities model
		$model = FD::model('Activities');

		if ($filterType == 'hiddenapp') {
			$activities = $model->getHiddenApps($this->my->id);
			$nextLimit = $model->getNextLimit( '0' );
		} else if($filterType == 'hiddenactor') {
			$activities = $model->getHiddenActors($this->my->id);
			$nextLimit = $model->getNextLimit( '0' );
		} else {
			// Retrieve user activities.
			$stream = FD::stream();
			$options = array('uId' => $this->my->id, 'context' => $context, 'filter' => $filterType);

			$activities = $stream->getActivityLogs($options);
			$nextLimit = $stream->getActivityNextLimit();
		}

		// Get a list of apps
		$result = $model->getApps();
		$apps = array();

		foreach ($result as $app) {
			if (!$app->hasActivityLog()) {
				continue;
			}

			$app->favicon = '';
			$app->image = $app->getIcon();
			$favicon = $app->getFavIcon();

			if ($favicon) {
				$app->favicon = $favicon;
			}

			// Load the app's css
			$app->loadCss();

			$apps[] = $app;
		}

		$this->set('active', $active);
		$this->set('title', $title);
		$this->set('apps', $apps);
		$this->set('activities', $activities);
		$this->set('nextlimit', $nextLimit);
		$this->set('filtertype', $filterType);

		echo parent::display('site/activities/default');


	}

}
