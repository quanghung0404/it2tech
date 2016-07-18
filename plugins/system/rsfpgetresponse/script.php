<?php
/**
* @version 1.51.0
* @package RSform!Pro 1.51.0
* @copyright (C) 2007-2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemRSFPGetResponseInstallerScript
{

	/**
	 * @param $type
	 * @param $parent
	 *
	 * @return bool
	 */
	public function preflight($type, $parent) {
		if ($type == 'uninstall') {
			return true;
		}
		try {
			$source = $parent->getParent()->getPath('source');
		
			$jversion = new JVersion();
			if (!$jversion->isCompatible('2.5.28')) {
				throw new Exception('Please upgrade to at least Joomla! 2.5.28 before continuing!');
			}
		
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php')) {
				throw new Exception('Please install the RSForm! Pro component before continuing.');
			}
			
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/assets.php')) {
				throw new Exception('Please upgrade RSForm! Pro to at least version 1.51.0 before continuing!');
			}

			$this->copyFiles($parent);

			// Update? Run our SQL file
			if ($type == 'update') {
				$this->copyFiles($parent);
				$this->runSQL($source, 'install');
			}
		} catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
		
		return true;
	}

	/**
	 * @param $parent
	 */
	protected function copyFiles($parent) {
		$app = JFactory::getApplication();
		$installer = $parent->getParent();
		$src = $installer->getPath('source').'/admin';
		$dest = JPATH_ADMINISTRATOR.'/components/com_rsform';

		if (!JFolder::copy($src, $dest, '', true)) {
			$app->enqueueMessage('Could not copy to '.str_replace(JPATH_SITE, '', $dest).', please make sure destination is writable!', 'error');
		}
	}

	/**
	 * @param $source
	 * @param $file
	 *
	 * @throws Exception
	 */
	protected function runSQL($source, $file) {
		$db 	= JFactory::getDbo();
		$driver = strtolower($db->name);
		if (strpos($driver, 'mysql') !== false) {
			$driver = 'mysql';
		}
		
		$sqlfile = $source.'/sql/'.$driver.'/'.$file.'.sql';
		
		if (file_exists($sqlfile)) {
			$buffer = file_get_contents($sqlfile);
			if ($buffer !== false) {
				$queries = JInstallerHelper::splitSql($buffer);
				foreach ($queries as $query) {
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->execute()) {
							throw new Exception(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
						}
					}
				}
			}
		}
	}

	/**
	 * @param $type
	 * @param $parent
	 */
	function postflight($type, $parent)
	{
		if ($type == 'install')
		{
			$db    = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->update($db->qn('#__extensions'))
					->set($db->qn('enabled') . ' = ' . $db->q(1))
					->where($db->qn('element') . ' = ' . $db->q('rsfpgetresponse'))
					->where($db->qn('type') . '=' . $db->q('plugin'))
					->where($db->qn('folder') . '=' . $db->q('system'));
			$db->setQuery($query);
			$db->execute();
		}

	}
}