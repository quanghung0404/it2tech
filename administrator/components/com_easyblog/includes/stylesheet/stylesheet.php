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

require_once(FD50_FOUNDRY_CLASSES . '/stylesheet.php');

class EasyBlogStylesheet extends FD50_Stylesheet
{
	public $config = null;
	public $doc = null;
	public $app = null;
	public $isModule = false;

	public function __construct($location, $name = null, $useOverride=false)
	{
		static $defaultWorkspace;

		$this->app = JFactory::getApplication();
		$this->input = EB::request();
		$this->config = EB::config();
		$this->doc = JFactory::getDocument();

		// Determines if this is a module section
		if ($location == 'module') {
			$this->isModule = true;
			$location = 'site';
		}

		if (!isset($defaultWorkspace)) {

			$override = $this->app->getTemplate();

			$defaultWorkspace = array(
				'site' => strtolower($this->config->get('theme_site')),
				'site_base' => strtolower($this->config->get('theme_site_base')),
				'admin' => strtolower($this->config->get('theme_admin')),
				'admin_base' => strtolower($this->config->get('theme_admin_base')),
				'module' => null,
				'override' => $override
			);
		}

		// Explicitly override
		if ($location == 'site' && !is_null($name)) {
			$defaultWorkspace['site'] = $name;
		}

		$this->workspace = $defaultWorkspace;

		$workspace = array();

		// Internally, override is a location.
		if ($useOverride) {
			$location = 'override';
		}

		// For specific template, else default template will be used.
		if (!empty($name)) {
			$workspace[$location] = $name;
		}

		// Because we can't do late static binding on PHP < 5.3.
		// Used by $this->override() method.
		$this->class = __CLASS__;

		parent::__construct('EASYBLOG', $workspace, $location);
	}

	/**
	 * Attaches stylesheet on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function attach($minified = null, $allowOverride = true, $customCategoryTemplate = null)
	{
		$environment = $this->config->get('easyblog_css_environment');
		$mode = $this->config->get('easyblog_css_mode');

		// If caller did not specify whether or not
		// to attach compressed stylesheets.
		if (is_null($minified)) {
			// Then decide from configuration mode
			$minified = $mode == 'compressed';
		}

		// Default settings
		$build = false;

		// If we're in a development environment,
		// always cache compile stylesheet and
		// attached uncompressed stylesheets.
		if ($environment == 'development') {

			$build = true;

			// Never attach minified stylesheet while in development mode.
			$minified = false;

			// Only super developers can build admin stylesheets.
			if ($this->location == 'admin') {
				$build = false;
			}

			// Do not build if stylesheet has not been compiled before.
			$cacheFolder = $this->folder('cache');
			$exists = JFolder::exists($cacheFolder);

			if (!$exists) {
				$build = false;
			}

			// Always build for superdevs
			$super = $this->config->get('system_superdev');

			if ($super) {
				$build = true;
			}
		}

		// Rebuild stylesheet on page load if necessary
		if ($build) {
			$task = $this->build($environment);
			$this->generateClientSideLog($task);
		}

		// Determines if the viewer is viewing the admin section.
		$app = JFactory::getApplication();
		$isAdmin = $app->isAdmin();

		if ($isAdmin) {
			$allowOverride = false;
			parent::attach($minified, $allowOverride);
			return;
		}

		// If there's a custom category theme, we need to check for it here
		if ($customCategoryTemplate) {

			// Check if there's a css file in /templates/JOOMLA_TEMPLATE/html/com_easyblog/themes/THEME_NAME/styles/style.min.css
			$path = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easyblog/themes/' . $customCategoryTemplate . '/styles/custom.css';

			if (JFile::exists($path)) {
				$customURI = JURI::root() . 'templates/' . $this->app->getTemplate() . '/html/com_easyblog/themes/' . $customCategoryTemplate . '/styles/custom.css';
				$this->doc->addStyleSheet($customURI);
				return;
			} else {

				// If there is no css file, we should revert to the currently configured theme
				$this->workspace['site'] = $this->config->get('theme_site');
			}

		}

		// NOTE: The following code is copied and pasted from parent::stylesheet()
		// and modified to load specifically style.css, composer.css or dashboard.css
		// depending on the view that you are on.

		// If this stylesheet has overrides
		if (!$this->isOverride && $allowOverride && $this->hasOverride()) {

			// get override stylesheet instance,
			$override = $this->override();

			// and let override stylesheet attach itself.
			return $override->attach();
		}

		// Load manifest file.
		$manifest = $this->manifest();

		$uris = array();

		// Determine the type of stylesheet to attach
		$type = $minified ? 'minified' : 'css';

		// Determines which file to attach
		$filename = 'style';
		$input = $app->input;
		$view = $input->get('view', '', 'cmd');

		if ($view == 'composer') {
			$filename = 'style-composer';
		}

		if ($view == 'dashboard') {
			$filename = 'style-dashboard';
		}

		// Build path options
		$target = array(
			'location' => $this->isOverride ? 'override' : $this->location,
			'filename' => $filename,
			'type' => $type
		);

		// For modules, the path would be on the theme
		if ($this->isModule) {
			$target['filename'] = 'style-modules';
		}

		// Fallback to css if minified not exists,
		// only for template overrides because
		// we don't want too much disk i/o.
		if ($this->isOverride && $minified) {

			$minifiedFile = $this->file($target);
			$exists = JFile::exists($minifiedFile);

			if (!$exists) {
				$target['type'] = 'css';
			}
		}

		// If there's a cdn url, use it instead
		$uri = $this->cdn($target);

		$uris[] = $uri;

		// Stop because this stylesheet
		// has been attached.
		if (isset(parent::$attached[$uri])) {
			return;
		}

		// Attach to document head.
		$this->doc->addStyleSheet($uri);

        $lang = JFactory::getLanguage();
        $isRtl = $lang->isRTL();


		// Check if custom.css exists on the site as template overrides
		$file = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easyblog/styles/custom.css';

		if (JFile::exists($file)) {
			$customCssFile = rtrim(JURI::root(), '/') . '/templates/' . $this->app->getTemplate() . '/html/com_easyblog/styles/custom.css';
			$this->doc->addStylesheet($customCssFile);
		}

		if ($view == 'composer' && $isRtl) {
			$this->doc->addStyleSheet(JURI::root() . 'components/com_easyblog/themes/wireframe/styles/style-composer-rtl.css');
		}

		if ($view == 'dashboard' && $isRtl) {
			$this->doc->addStyleSheet(JURI::root() . 'components/com_easyblog/themes/wireframe/styles/style-dashboard-rtl.css');
		}

		// Remember this stylesheet so
		// we won't reattach it again.
		parent::$attached[$uri] = true;
	}

	/**
	 * Generates logs on the client site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function generateClientSideLog($task)
	{
		// Generate log
		ob_start();
		?>
		<script>
		try {

			var task = <?php echo $task->toJSON(); ?>,
				method = {
					"success": "info",
					"info": "info",
					"warning": "warn",
					"error": "warn"
				};

			console.groupCollapsed(task.failed ? "Build stylesheet FAILED!" : "Build stylesheet OK!");

			jQuery.each(task.details, function(i, detail){
				console[method[detail.type]](detail.message);
			});

			console.log("Total time: " + (Math.round(task.time_total * 1000) / 1000) + "s");
			console.log("Peak memory usage: " + (task.mem_peak/1048576).toFixed(2) + "mb");
			console.log("View complete log: ", task);

			console.groupEnd();

		} catch(e) {};
		</script>
		<?php
		$log = ob_get_contents();
		ob_end_clean();

		// Attach log
		$document = JFactory::getDocument();

		if ($document->getType() == 'html') {
			$document->addCustomTag($log);
		}
	}
}
