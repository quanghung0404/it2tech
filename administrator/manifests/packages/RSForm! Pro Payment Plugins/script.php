<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class pkg_RSFormProPaymentPluginsInstallerScript
{
	protected $migrate = false;
	
	public function preflight($type, $parent) {
		if ($type != 'uninstall') {
			$app = JFactory::getApplication();
			
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php')) {
				$app->enqueueMessage('Please install the RSForm! Pro component before continuing.', 'error');
				return false;
			}
			
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/assets.php')) {
				$app->enqueueMessage('Please upgrade RSForm! Pro to at least version 1.51.0 before continuing!', 'error');
				return false;
			}
			
			$jversion = new JVersion();
			if (!$jversion->isCompatible('2.5.28')) {
				$app->enqueueMessage('Please upgrade to at least Joomla! 2.5.28 before continuing!', 'error');
				return false;
			}
			
			if (file_exists(JPATH_SITE.'/plugins/system/rsfppaypal/rsfppaypal.xml')) {
				$xml = file_get_contents(JPATH_SITE.'/plugins/system/rsfppaypal/rsfppaypal.xml');
				if (strpos($xml, '<extension') === false) {
					$this->migrate = true;
				}
			}
		}
		
		return true;
	}
	
	public function postflight($type, $parent) {
		if ($type == 'uninstall') {
			return true;
		}
		
		$source = $parent->getParent()->getPath('source');
		
		// need to update the old plugin?
		if ($this->migrate) {
			// it's a migration, we need to add the data to the PayPal Plugin
			$this->runSQL($source, 'install.sql', 'plg_paypal');
			
			$db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true);
			
			// find old forms
			$query->select($db->quoteName('FormId'))
				  ->from('#__rsform_components')
				  ->where($db->quoteName('ComponentTypeId').' IN (21, 22, 23)');
			$db->setQuery($query);
			$forms = $db->loadColumn();
			
			// add the PayPal component to the forms
			if ($forms = array_unique($forms)) {
				foreach ($forms as $formId) {
					$this->addComponent($formId, 500);
				}
			}
			
			// migrate old configuration parameters
			$query 	= $db->getQuery(true);
			$query->select('*')
				  ->from('#__rsform_config')
				  ->where($db->quoteName('SettingName').' LIKE '.$db->quote('paypal.%'));
			$db->setQuery($query);
			if ($results = $db->loadObjectList('SettingName')) {
				require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/config.php';
				
				$config = RSFormProConfig::getInstance();
				$config->set('payment.currency', $results['paypal.currency']->SettingValue);
				$config->set('payment.thousands', $results['paypal.thousands']->SettingValue);
				$config->set('payment.decimal', $results['paypal.decimal']->SettingValue);
				$config->set('payment.nodecimals', $results['paypal.nodecimals']->SettingValue);
			}
		}
		
		$this->enablePlugin('rsfppayment');
		$this->enablePlugin('rsfppaypal');
		$this->enablePlugin('rsfpofflinepayment');
	}
	
	protected function runSQL($source, $file, $package='') {
		$db = JFactory::getDbo();
		$driver = strtolower($db->name);
		if (strpos($driver, 'mysql') !== false) {
			$driver = 'mysql';
		} elseif ($driver == 'sqlsrv') {
			$driver = 'sqlazure';
		}
		
		if ($package) {
			$source .= '/packages/'.$package;
		}
		
		$sqlfile = $source.'/sql/'.$driver.'/'.$file;
		
		if (file_exists($sqlfile)) {
			$buffer = file_get_contents($sqlfile);
			if ($buffer !== false) {
				$queries = JInstallerHelper::splitSql($buffer);
				foreach ($queries as $query) {
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->execute()) {
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
						}
					}
				}
			}
		}
	}
	
	protected function addComponent($formId, $type) {
		$db = JFactory::getDbo();
		static $properties = array();
		if (!isset($properties[$type])) {
			$query = $db->getQuery(true);
			$query->select($db->quoteName('FieldName'))
				  ->select($db->quoteName('FieldValues'))
				  ->from('#__rsform_component_type_fields')
				  ->where($db->quoteName('ComponentTypeId').'='.$db->quote($type));
			$db->setQuery($query);
			$results = $db->loadObjectList();
			$properties[$type] = array();
			foreach ($results as $result) {
				$properties[$type][$result->FieldName] = $result->FieldValues;
			}
			
			$properties[$type]['NAME'] = 'PayPal';
			$properties[$type]['LABEL'] = 'PayPal';
		}
		
		$query = $db->getQuery(true);
		$query->select('MAX('.$db->quoteName('Order').')')
			  ->from('#__rsform_components')
			  ->where($db->quoteName('FormId').'='.$db->quote($formId));
		$db->setQuery($query);
		$max = (int) $db->loadResult() + 1;
		
		$query = $db->getQuery(true);
		$query->insert('#__rsform_components')
			  ->set($db->quoteName('FormId').'='.$db->quote($formId))
			  ->set($db->quoteName('ComponentTypeId').'='.$db->quote($type))
			  ->set($db->quoteName('Order').'='.$db->quote($max))
			  ->set($db->quoteName('Published').'='.$db->quote(1));
		$db->setQuery($query);
		$db->execute();
		
		$componentId = $db->insertid();
		
		foreach ($properties[$type] as $k => $v) {
			if ($k == 'NAME') {
				$v .= $componentId;
			}
			
			$query->clear();
			$query->insert('#__rsform_properties')
				  ->set($db->quoteName('ComponentId').'='.$db->quote($componentId))
				  ->set($db->quoteName('PropertyName').'='.$db->quote($k))
				  ->set($db->quoteName('PropertyValue').'='.$db->quote($v));
			$db->setQuery($query);
			$db->execute();
		}
	}
	
	protected function enablePlugin($element) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		
		$query->update('#__extensions')
			  ->set($db->quoteName('enabled').'='.$db->quote(1))
			  ->where($db->quoteName('type').'='.$db->quote('plugin'))
			  ->where($db->quoteName('folder').'='.$db->quote('system'))
			  ->where($db->quoteName('element').'='.$db->quote($element));
		$db->setQuery($query);
		return $db->execute();
	}
}