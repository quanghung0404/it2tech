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

jimport('joomla.filesystem.file');

class com_EasyBlogInstallerScript
{
	/**
	 * Triggered after the installation is completed
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function postflight()
	{
		ob_start();
		?>
<style type="text/css">
#j-main-container > .adminform > tbody > tr:first-child {
	display: none !important;
}
</style>

<table border="0" cellpadding="0" cellspacing="0" style="
	background: #fff;
	background: #25384b;
	font: 12px/1.5 Arial, sans-serif;
	color: rgba(255,255,255,.5);
	width: 100%;
	max-width: 100%;
	border-radius: 4px;
	overflow: hidden;
	box-shadow: 0 1px 1px rgba(0,0,0,.08);
	text-align: left;
	margin: 0 auto 20px;
	">
	<tbody>
		<tr>
			<td style="padding: 40px; font-size: 12px;">
				<div style="margin-bottom: 20px;">
					<div style="display: table-cell; vertical-align: middle; padding-right: 15px">
						<img src="<?php echo JURI::root();?>/administrator/components/com_easyblog/setup/assets/images/logo.png" height="48" style="height:48px !important;">
					</div>
					<div style="display: table-cell; vertical-align: middle;">
						<b style="font-size: 26px; color: #fff; font-weight: normal; line-height: 1; margin: 5px 0;">EasyBlog</b>
					</div>
				</div>

				<p style="font-size: 14px; color: rgba(255,255,255,.8);">
					Thank you for your recent purchase of EasyBlog, the best blogging component for Joomla! This is a confirmation message that the necessary setup files are already loaded on the site.</p>
				<p style="font-size: 14px; color: rgba(255,255,255,.8);">You will need to proceed with the installation process by clicking on the button below.</p>

				<br />

				<a href="<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&amp;install=true" style="
						background-color: #6c5;
						border-radius: 4px;
						color: #fff;
						display: inline-block;
						font-weight: bold;
						font-size: 16px;
						padding: 10px 15px;
						text-decoration: none !important;
				">
					Proceed With Installation &rarr;
				</a>
			</td>
		</tr>
	</tbody>
</table>
		<?php
		$contents 	= ob_get_contents();
		ob_end_clean();

		echo $contents;
	}

	/**
	 * Triggered before the installation is complete
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preflight()
	{
		// During the preflight, we need to create a new installer file in the temporary folder
		$file = JPATH_ROOT . '/tmp/easyblog.installation';

		// Determines if the installation is a new installation or old installation.
		$obj = new stdClass();
		$obj->new = false;
		$obj->step = 1;
		$obj->status = 'installing';

		$contents = json_encode($obj);

		if (!JFile::exists($file)) {
			JFile::write($file, $contents);
		}

		// remove old constant.php if exits.
		$this->removeConstantFile();

		if ($this->isUpgradeFrom3x()) {

			// @TODO: Disable modules
			//$this->unPublishdModules(true);

			// @TODO: Disable plugins
			$this->unPublishPlugins();

			// remove older helper files
			$this->removeOldHelpers();
		}

		// now let check the eb config
		$this->checkEBVersionConfig();
	}

	/**
	 * Responsible to remove old constant.php file to avoid redefine of same constant error
	 *
	 * @since	5.0
	 * @access	public
	 * @param
	 * @return
	 */
	public function removeConstantFile()
	{
		$file = JPATH_ROOT. '/components/com_easyblog/constants.php';
		if (JFile::exists($file)) {
			JFile::delete($file);
		}
	}

	/**
	 * Responsible to remove old helper files
	 *
	 * @since	5.0
	 * @access	public
	 * @param
	 * @return
	 */
	public function removeOldHelpers()
	{
		// helpers
		$path = JPATH_ROOT . '/components/com_easyblog/helpers';
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}
	}


	/**
	 * Responsible to check eb configs db version
	 *
	 * @since	5.0
	 * @access	public
	 * @param
	 * @return
	 */
	public function checkEBVersionConfig()
	{
		// if there is the config table but no dbversion, we know this upgrade is coming from pior 5.0. lets add on dbversion into config table.
		if ($this->isUpgradeFrom3x()) {

			// get current installed eb version.
			$xmlfile = JPATH_ROOT. '/administrator/components/com_easyblog/easyblog.xml';

			// set this to version prior 3.8.0 so that it will execute the db script from 3.9.0 as well incase
			// this upgrade is from very old version.
			$version = '3.8.0';

			if (JFile::exists($xmlfile)) {
				$contents = JFile::read($xmlfile);
				$parser = simplexml_load_string($contents);
				$version = $parser->xpath('version');
				$version = (string) $version[0];
			}

			$db = JFactory::getDBO();

			// ok, now we got the version. lets add this version into dbversion.
			$query = 'INSERT INTO ' . $db->quoteName('#__easyblog_configs') . ' (`name`, `params`) VALUES';
			$query .= ' (' . $db->Quote('dbversion') . ',' . $db->Quote($version) . '),';
			$query .= ' (' . $db->Quote('scriptversion') . ',' . $db->Quote($version) . ')';

			$db->setQuery($query);
			$db->query();
		}
	}

	private function isUpgradeFrom3x()
	{
		static $isUpgrade = null;

		if (is_null($isUpgrade)) {

			$isUpgrade = false;

			$db = JFactory::getDBO();

			$jConfig = JFactory::getConfig();
			$prefix = $jConfig->get('dbprefix');

			$query = "SHOW TABLES LIKE '%" . $prefix . "easyblog_configs%'";
			$db->setQuery($query);

			$result = $db->loadResult();

			if ($result) {
				// this is an upgrade. lets check if the upgrade from 3.x or not.
				$query = 'SELECT ' . $db->quoteName('params') . ' FROM ' . $db->quoteName('#__easyblog_configs') . ' WHERE ' . $db->quoteName('name') . '=' . $db->Quote('dbversion');
				$db->setQuery($query);

				$exists = $db->loadResult();
				if (!$exists) {
					$isUpgrade = true;
				}
			}
		}

		return $isUpgrade;
	}

	/**
	 * Responsible to perform the uninstallation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function uninstall()
	{
		// @TODO: Disable modules
		$this->unPublishdModules();

		// @TODO: Disable plugins
		$this->unPublishPlugins();
	}

	/**
	 * Responsible to perform component updates
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function update()
	{

	}



	public function unPublishPlugins()
	{
		$db = JFactory::getDBO();

		// $pluginNames = array(
		// 				'pagebreak' => 'easyblog',
		// 				'autoarticle' => 'easyblog',
		// 				'easyblog' => 'community',
		// 				'easyblogtoolbar' => 'community',
		// 				'easyblog' => 'finder',
		// 				'easyblog' => 'phocapdf',
		// 				'easyblogcomment' => 'search',
		// 				'easyblog' => 'search',
		// 				'easyblogredirect' => 'system',
		// 				'blogurl' => 'system',
		// 				'eventeasyblog' => 'system',
		// 				'groupeasyblog' => 'system',
		// 				'easyblogusers' => 'user'
		// 			  );

		$pluginNames = array(
				'easyblog' => array('pagebreak', 'autoarticle'),
				'community' => array('easyblog', 'easyblogtoolbar'),
				'finder' => 'easyblog',
				'phocapdf' => 'easyblog',
				'search' => array('easyblogcomment', 'easyblog'),
				'system' => array('easyblogredirect', 'blogurl', 'eventeasyblog', 'groupeasyblog'),
				'user' => 'easyblogusers'
			);

		foreach($pluginNames as $folder => $elements) {


			$tempElements = '';

			if (is_array($elements)) {
				foreach($elements as $element){
					$tempElements .= ($tempElements) ? ',' . $db->Quote($element) : $db->Quote($element);
				}
			} else {
				$tempElements = $db->Quote($elements);
			}

		    // jos_modules
		    $query = array();
		    $query[] = 'UPDATE ' . $db->quoteName('#__extensions') . ' SET ' . $db->quoteName('enabled') . '=' . $db->Quote('0');
		    $query[] = 'WHERE ' . $db->quoteName('folder') . '=' . $db->Quote($folder);
		    $query[] = 'AND ' . $db->quoteName('element') . ' IN (' . $tempElements . ')';

		    $query = implode(' ', $query);

		    $db->setQuery($query);
		    $state = $db->query();
		}

		return true;

	}

	public function unPublishdModules($upgrade = false)
	{
		$db = JFactory::getDBO();

		$moduleNames = array('mod_easyblogimagewall', 'mod_easybloglatestblogs', 'mod_easyblogticker', 'mod_easyblogtopblogs', 'mod_easyblogsearch',
							'mod_easyblogteamblogs', 'mod_easyblogshowcase', 'mod_easyblogsubscribers', 'mod_easyblogpostmeta', 'mod_easyblogquickpost', 'mod_easyblogarchive',
							'mod_easyblogbio', 'mod_easyblogcalendar', 'mod_easyblogcategories', 'mod_easybloglatestblogger', 'mod_easybloglatestcomment', 'mod_easybloglist',
							'mod_easyblogmostcommentedpost', 'mod_easyblogmostpopularpost', 'mod_easyblogpostmap', 'mod_easyblograndompost', 'mod_easyblogrelatedpost',
							'mod_easyblogsubscribe', 'mod_easyblogtagcloud', 'mod_easyblogwelcome', 'mod_easyblognewpost', 'mod_easyblogmostactiveblogger');

		if ($upgrade) {
			// unpublish olds modules
			$moduleNames = array('mod_easyblogarchive','mod_easyblogbio','mod_easyblogcalendar','mod_easyblogcategories','mod_easybloglatestblogger','mod_easybloglatestcomment','mod_easybloglist',
								'mod_easyblogmostactiveblogger','mod_easyblogmostcommentedpost','mod_easyblogmostpopularpost','mod_easyblognewpost','mod_easyblogpostmap','mod_easyblograndompost',
								'mod_easyblogrelatedpost','mod_easyblogsubscribe','mod_easyblogtagcloud','mod_easyblogwelcome','mod_imagewall','mod_latestblogs','mod_searchblogs','mod_showcase',
								'mod_subscribers','mod_teamblogs','mod_topblogs');
		}

		$modules = '';
		foreach($moduleNames as $module){
			$modules .= ($modules) ? ',' . $db->Quote($module) : $db->Quote($module);
		}

	    // jos_modules
	    $query = array();
	    $query[] = 'UPDATE ' . $db->quoteName('#__modules') . ' SET ' . $db->quoteName('published') . '=' . $db->Quote('0');
	    $query[] = 'WHERE ' . $db->quoteName('module') . ' IN (' . $modules . ')';
	    $query[] = 'AND ' . $db->quoteName('published') . '=' . $db->Quote('1');

	    $query = implode(' ', $query);

	    $db->setQuery($query);
	    $state = $db->query();

		return true;
	}



}
