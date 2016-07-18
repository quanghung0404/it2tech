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

class PlgSystemEasySocial extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		$return = $this->exclude();

		if ($return) {
			return;
		}

		parent::__construct($subject, $config);

		$this->app = JFactory::getApplication();
		$this->doc = JFactory::getDocument();
	}

	/**
	 * Determines if EasySocial exists on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';

			jimport('joomla.filesystem.file');

			$exists = JFile::exists($file);

			if ($exists) {
				include_once($file);
			}
		}

		return $exists;
	}

	/**
	 * Determines if we should exclude certain item from triggering this plugin.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function exclude()
	{
		$option = JRequest::getVar('option');

		// To fix drag and drop image issue in tinymce in joomla 3.5.
		if ($option == 'com_media') {
			return true;
		}

		return false;		
	}

	/**
	 * Triggered upon Joomla application initialization
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function onAfterInitialise()
	{
		// We only process on the front end.
		if ($this->app->isAdmin()) {
			return;
		}

		if (!$this->exists()) {
			return;
		}

		// Optimize responsive layouts
		if ($this->doc->getType() == 'html') {
			$scripts = $this->getResponsiveScripts();
			$this->doc->addCustomTag($scripts);
		}

		$esConfig = ES::config();

		if ($esConfig->get('users.simpleUrl')) {
			$jRouter = JFactory::getApplication()->getRouter();
			$jRouter->attachParseRule(array($this, 'processSimpleUrlsParse'));
		}

		return true;
	}

	/**
	 * Executes before the router
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterRoute()
	{
		// We only process on the front end.
		if ($this->app->isAdmin()) {
			return;
		}

		if (!$this->exists()) {
			return;
		}

		// Process redirection
		$this->processUsersRedirection();
	}


	/**
	 * Processes simple urls
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function processSimpleUrlsParse(&$jRouter, &$uri)
	{
		$id = $this->getSimpleUrlId();

		if ($id === false || !$id) {
			return array();
		}

		// Get the item id for the user
		$itemId = $this->getItemId();

		// Set the current view to profile
		// JRequest::setVar('option', 'com_easysocial');
		// JRequest::setVar('view', 'profile');
		// JRequest::setVar('id', $id);
		// JRequest::setVar('Itemid', $itemId);

		JFactory::getApplication()->input->set('option', 'com_easysocial');
		JFactory::getApplication()->input->set('view', 'profile');
		JFactory::getApplication()->input->set('id', $id);
		JFactory::getApplication()->input->set('Itemid', $itemId);

		$menu = JFactory::getApplication()->getMenu();
		$menu->setActive($itemId);

		$uri->setPath('');
		$uri->setQuery('option=com_easysocial&view=profile&id=' . $id . '&Itemid=' . $itemId);

		return array();
	}


	/**
	 * Processes simple urls
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSimpleUrlId()
	{
		// Clean the url
		$url = $this->getCurrentUrl();

		// var_dump($uri);exit;

		$doc = JFactory::getDocument();

		// If SH404 is installed skip this
		if (ES::isSh404Installed()) {
			return false;
		}

		// If this is tmpl=component, skip this. Some ajax calls might be made from 3rd party extensions.
		if (JRequest::getVar('tmpl') == 'component') {
			return false;
		}

		// We only perform redirections when it is currently on html mode
		if ($doc->getType() != 'html') {
			return false;
		}

		// if url is empty, we do not process further as this might be the home url.
		if (! $url) {
			return false;
		}

		$jConfig = ES::jConfig();

		if (!$jConfig->getValue('sef') || !$jConfig->getValue('sef_rewrite')) {
			return false;
		}

		// Let's try to search to see if there are any permalinks on the site
		$model = ES::model('Users');
		$id = $model->getUserIdFromAlias($url);

		// To ensure that the id is an integer instead of string to avoid possible 404 issue
		$id = (int) $id;

		// If we can't detect any user's skip this
		if (!$id) {
			return false;
		}

		return $id;
	}

	/**
	 * Get an item id for the profile
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getItemId()
	{
		$itemId = ESR::getItemId('profile');

		return $itemId;
	}

	/**
	 * Retrieves the current url
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getCurrentUrl()
	{
		// Try to get the current url
		$uri = JUri::getInstance();

		$url = $uri->toString();
		$url = str_ireplace(JURI::root(), '', $url);

		// Make sure that the url is clean
		$url = ltrim($url, '/');

		// If language is enabled, we need to clean it up
		$languageEnabled = JPluginHelper::isEnabled('system', 'languagefilter');

		if ($languageEnabled) {

			$url = explode('/', $url);
			if (count($url) > 1) {
				// Remove the language portion
				array_shift($url);
			}

			$url = $url[0];
		}

		return $url;
	}

	/**
	 * Redirects users view to easysocial
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function processUsersRedirection()
	{
		if ($this->doc->getType() != 'html') {
			return;
		}

		// Check if the admin wants to enable this
		if (!$this->params->get('redirection', true)) {
			return;
		}

		// If this is registration from com_users, redirect to the appropriate page.
		if ($this->isUserRegistration()) {
			$url = FRoute::registration(array(), false);

			return $this->app->redirect($url);
		}

		// If this is username reminder, redirect to the appropriate page.
		if ($this->isUserRemind()) {
			$url = FRoute::account(array('layout' => 'forgetUsername'), false);

			return $this->app->redirect($url);
		}

		// If this is password reset, redirect to the appropriate page.
		if ($this->isUserReset()) {
			$url = FRoute::account(array('layout' => 'forgetPassword'), false);

			return $this->app->redirect($url);
		}

		// If this is logout, we should respect this.
		if ($this->isUserLogout()) {
			return;
		}

		// If this is password reset, redirect to the appropriate page.
		if ($this->isUserLogin()) {

			$return = $this->app->input->get('return', '', 'default');

			if ($return) {
				ES::setCallback(base64_decode($return));
			}

			// Redirect to EasySocial's registration
			$url = FRoute::login(array(), false);

			return $this->app->redirect($url);
		}

		// If this is password reset, redirect to the appropriate page.
		if ($this->isUserProfile()) {
			$url = FRoute::profile(array(), false);

			return $this->app->redirect($url);
		}
	}

	/**
	 * Determines if the current access is for profile
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isUserProfile()
	{
		$option 	= JRequest::getVar( 'option' );
		$view 		= JRequest::getVar( 'view' );

		if ($option == 'com_users' && $view == 'profile') {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for logout
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isUserLogout()
	{
		$option = JRequest::getVar('option');
		$view = JRequest::getVar('view');
		$layout = JRequest::getVar('layout');

		if ($option == 'com_users' && $view == 'login' && $layout == 'logout') {
			return true;
		}

		return false;
	}	

	/**
	 * Determines if the current access is for login
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isUserLogin()
	{
		$option 	= JRequest::getVar( 'option' );
		$view 		= JRequest::getVar( 'view' );

		if( $option == 'com_users' && $view == 'login' )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for reset password
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isUserReset()
	{
		$option 	= JRequest::getVar( 'option' );
		$view 		= JRequest::getVar( 'view' );

		if( $option == 'com_users' && $view == 'reset' )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for remind username
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isUserRemind()
	{
		$option 	= JRequest::getVar( 'option' );
		$view 		= JRequest::getVar( 'view' );

		if( $option == 'com_users' && $view == 'remind' )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for registration
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isUserRegistration()
	{
		$option 	= JRequest::getVar( 'option' );
		$view 		= JRequest::getVar( 'view' );

		if ($option == 'com_users' && $view == 'registration') {
			return true;
		}

		return false;
	}

	/**
	 * Generates the responsive script codes
	 *
	 * @since	1.4.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getResponsiveScripts()
	{
		ob_start();
?>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function(){

    if (window.innerWidth < 600) {
        var wrapper = document.querySelectorAll(".es-responsive");

        if (wrapper.length < 1) {
        	return;
        }

        for(var i = 0; i < wrapper.length; i++) {
            wrapper[i].classList.add("w320" ,"w480" ,"w600");
        }
    }
});
</script>
<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}
}
