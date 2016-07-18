<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewAutoposting extends EasyBlogAdminView
{
	/**
	 * Default autoposting display
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function display($tpl = null)
	{
		// Check for user access
		$this->checkAccess('easyblog.manage.autoposting');

		$layout		= $this->getLayout();
		$step		= JRequest::getVar('step', 1);

		$this->set('step', $step);

		if (method_exists($this, $layout)) {
			return $this->$layout($tpl);
		}

		JToolBarHelper::title(JText::_('COM_EASYBLOG_AUTOPOSTING'), 'autoposting');

		// Set page details
		$this->setHeading(JText::_('COM_EASYBLOG_AUTOPOSTING_TITLE'));

		$facebook 	= EB::oauth()->associated('facebook');
		$twitter 	= EB::oauth()->associated('twitter');
		$linkedin 	= EB::oauth()->associated('linkedin');

		$this->set('facebook', $facebook);
		$this->set('twitter', $twitter);
		$this->set('linkedin', $linkedin);

		parent::display('autoposting/default');
	}

	/**
	 * Displays the facebook process to setup auto posting for Facebook
	 *
	 * @since	4.0
	 * @access	public
	 * @return	
	 */
	public function facebook()
	{
		// Add the button
		JToolbarHelper::apply('facebook.save');

		// Determines if facebook has already been associated
		$associated = EB::oauth()->associated('facebook');

		// Load the oauth table
		$oauth = EB::table('Oauth');
		$oauth->load(array('type' => 'facebook', 'system' => true));

		// Set page details
		JToolbarHelper::title(JText::_('COM_EASYBLOG_AUTOPOSTING_FB_TITLE'));
		$this->setHeading('COM_EASYBLOG_AUTOPOSTING_FB_TITLE');

		// Default expire values
		$expire = '';

		if (isset($oauth->expires)) {
			$expire = $oauth->expires;
		}

		// Legacy codes will contain an "expires" property in the json object
		if ($oauth->id && isset($oauth->expires) && !$oauth->expires) {

			$legacyExpires	= $oauth->getAccessTokenValue('expires');

			if ($legacyExpires) {
				$created = strtotime($oauth->created);
				$expire = $legacyExpires + $created;
			}
		}

		// Format the expiry date
		if ($expire) {
			$expire = EB::date($expire)->format(JText::_('DATE_FORMAT_LC1'));
		}


		// Get the facebook client
		$client = EB::oauth()->getClient('Facebook');

		// Get a list of pages		
		$pages = array();

		if ($associated && $oauth->access_token) {
			$client->setAccess($oauth->access_token);

			// Get pages that are available
			try {
				$pages = $client->getPages();
			} catch(Exception $e) {
				$pages = array();
			}
		}

		// Get a list of stored pages
		$storedPages = $this->config->get('integrations_facebook_page_id');

		if ($storedPages) {
			$storedPages = explode(',', $storedPages);
		}

		// Get a list of groups
		$groups = array();

		if ($associated && $oauth->access_token) {
			$client = EB::oauth()->getClient('Facebook');
			$client->setAccess($oauth->access_token);

			// Get groups that are available
			$groups = array();

			try {
				$groups = $client->getGroups();
			} catch(Exception $e) {
				
			}
		}

		// Get a list of stored groups
		$storedGroups = $this->config->get('integrations_facebook_group_id', array());

		if ($storedGroups) {
			$storedGroups = explode(',', $storedGroups);
		}

		$this->set('client', $client);
		$this->set('storedGroups', $storedGroups);
		$this->set('groups', $groups);
		$this->set('storedPages', $storedPages);
		$this->set('pages', $pages);
		$this->set('expire', $expire);
		$this->set('associated', $associated);

		parent::display('autoposting/facebook/default');
	}

	/**
	 * Displays the twitter process to setup auto posting for Facebook
	 *
	 * @since	4.0
	 * @access	public
	 * @return	
	 */
	public function twitter()
	{
		// Add the button
		JToolbarHelper::title(JText::_('COM_EASYBLOG_AUTOPOSTING_TWITTER_TITLE'));
		JToolbarHelper::apply('twitter.save');

		// Set page details
		$this->setHeading('COM_EASYBLOG_AUTOPOSTING_TWITTER_TITLE');
			
		$client = EB::oauth()->getClient('twitter');
		$associated = EB::oauth()->associated('twitter');

		$this->set('client', $client);
		$this->set('associated', $associated);

		parent::display('autoposting/twitter/default');
	}

	/**
	 * Displays the linkedin process to setup auto posting
	 *
	 * @since	4.0
	 * @access	public
	 * @return	
	 */
	public function linkedin()
	{
		// Add the button
		JToolbarHelper::apply('linkedin.save');

		// Set page details
		JToolbarHelper::title(JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_TITLE'));
		$this->setHeading('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_TITLE');

		$associated = EB::oauth()->associated('linkedin');

		// Initialize the default value
		$companies = array();
		
		$client = EB::oauth()->getClient('linkedin');		

		if ($associated) {

			$oauth = EB::table('oauth');
			$oauth->load(array('type' => 'linkedin', 'system' => true));

			$client->setAccess($oauth->access_token);

			// Get the company data
			$data = $client->company('?is-company-admin=true');
			$result = $data['linkedin'];

			$parser = JFactory::getXML($result, false);
			$result = $parser->children();

			$companies = array();

			if ($result) {

				foreach ($result as $item) {
					$company = new stdClass();

					$company->id    = (int) $item->id;
					$company->title = (string) $item->name;

					$companies[] = $company;
				}
			}
		}

		$storedCompanies = explode(',', $this->config->get('integrations_linkedin_company'));

		$this->set('client', $client);
		$this->set('storedCompanies', $storedCompanies);
		$this->set('companies', $companies);
		$this->set('associated', $associated);

		parent::display('autoposting/linkedin/default');
	}

	public function registerToolbar()
	{
		
		
		if( $this->getLayout() == 'form' )
		{
			JToolbarHelper::divider();
			JToolBarHelper::apply( 'applyForm' );
			JToolBarHelper::save( 'saveForm' );
			JToolBarHelper::cancel();
		}
	}
}
