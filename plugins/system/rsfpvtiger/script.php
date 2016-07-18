<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemRSFPVtigerInstallerScript
{
	public function preflight($type, $parent) {
		if ($type == 'uninstall') {
			return true;
		}
		
		$app = JFactory::getApplication();
		
		if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php')) {
			$app->enqueueMessage('Please install the RSForm! Pro component before continuing.', 'error');
			return false;
		}
		
		if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/assets.php')) {
			$app->enqueueMessage('Please upgrade RSForm! Pro to at least version 1.51.0 before continuing!', 'error');
			return false;
		}
		
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php';
		$version = new RSFormProVersion;
		
		if (version_compare((string) $version, '1.51.12', '<')) {
			throw new Exception('Please upgrade RSForm! Pro to at least version 1.51.12 before continuing!');
		}
		
		$jversion = new JVersion();
		if (!$jversion->isCompatible('2.5.28')) {
			$app->enqueueMessage('Please upgrade to at least Joomla! 2.5.28 before continuing!', 'error');
			return false;
		}
		
		return true;
	}
	
	public function update($parent) {
		$this->copyFiles($parent);
		
		$db 		= JFactory::getDbo();
		$columns 	= $db->getTableColumns('#__rsform_vtiger', false);		
		
		if ($columns['vt_accesskey']->Type == 'varchar(50)') {
			$db->setQuery("ALTER TABLE `#__rsform_vtiger` CHANGE `vt_accesskey` `vt_accesskey` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
			$db->execute();
			$db->setQuery("ALTER TABLE `#__rsform_vtiger` CHANGE `vt_username` `vt_username` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
			$db->execute();
			$db->setQuery("ALTER TABLE `#__rsform_vtiger` CHANGE `vt_hostname` `vt_hostname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
			$db->execute();
		}
		
		/* No longer need this
		$db->setQuery("SHOW COLUMNS FROM `#__rsform_vtiger` WHERE `Field`='vt_salutationtype'");
		if (!$db->loadObject()) {
			$db->setQuery("ALTER TABLE `#__rsform_vtiger` ADD `vt_salutationtype` VARCHAR( 255 ) NOT NULL AFTER `vt_leadstatus`");
			$db->execute();
		}
		*/
		
		if (!isset($columns['vt_fields'])) {			
			$query = $db->getQuery(true);
			
			// Grab all Vtiger integrations from db.
			$query->select('*')
				  ->from($db->qn('#__rsform_vtiger'));
			$db->setQuery($query);
			$results = $db->loadObjectList();
			
			// Add missing table.
			$db->setQuery("ALTER TABLE `#__rsform_vtiger` ADD `vt_fields` MEDIUMTEXT NOT NULL AFTER `form_id`");
			$db->execute();
			
			foreach ($results as $result) {				
				$newFields = array();
				foreach ($result as $oldField => $oldValue) {
					// Skip these fields
					if (!in_array($oldField, array('form_id', 'vt_debug', 'vt_accesskey', 'vt_username', 'vt_hostname', 'vt_debugEmail', 'vt_published'))) {
						// Special handling
						if ($oldField == 'vt_custom_fields') {
							if ($oldValue && ($customFields = unserialize($oldValue))) {
								foreach ($customFields as $customField) {
									$newFields[$customField->api_name] = $customField->value;
								}
							}
						} else {
							$newFields[substr($oldField, 3)] = $oldValue;
						}
					}
				}
				
				$query = $db->getQuery(true);
				if ($newFields) {
					$query->update($db->qn('#__rsform_vtiger'))
						  ->set($db->qn('vt_fields').'='.$db->q(serialize($newFields)))
						  ->where($db->qn('form_id').'='.$db->q($result->form_id));
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}
	
	public function install($parent) {
		$this->copyFiles($parent);
	}
	
	protected function copyFiles($parent) {
		$app = JFactory::getApplication();
		$installer = $parent->getParent();
		$src = $installer->getPath('source').'/admin';
		$dest = JPATH_ADMINISTRATOR.'/components/com_rsform';
		
		if (!JFolder::copy($src, $dest, '', true)) {
			$app->enqueueMessage('Could not copy to '.str_replace(JPATH_SITE, '', $dest).', please make sure destination is writable!', 'error');
		}
	}
}