<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemRSFPvTiger extends JPlugin
{
	public function rsfp_onFormSave($form) {
		$post = JRequest::get('post', JREQUEST_ALLOWRAW);
		$post['form_id'] = $post['formId'];
		
		$row = JTable::getInstance('RSForm_vTiger', 'Table');
		if (!$row)
			return;
		if (!$row->bind($post))
		{
			JError::raiseWarning(500, $row->getError());
			return false;
		}
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT form_id FROM #__rsform_vtiger WHERE form_id='".(int) $post['form_id']."'");
		if (!$db->loadResult())
		{
			$db->setQuery("INSERT INTO #__rsform_vtiger SET form_id='".(int) $post['form_id']."'");
			$db->execute();
		}
		
		if (!empty($post['vt_fields'])) {
			$row->vt_fields = serialize($post['vt_fields']);
		} else {
			$row->vt_fields = null;
		}
		
		if (!$row->store()) {
			JError::raiseWarning(500, $row->getError());
			return false;
		}
		
		return true;
	}
	
	public function rsfp_bk_onAfterShowFormEditTabs()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfpvtiger');
		
		$row = JTable::getInstance('RSForm_vTiger', 'Table');
		if (!$row) {
			return false;
		}
		$row->load($formId);
		
		// Unserialize fields if this value is populated.
		if ($row->vt_fields) {
			$row->vt_fields = unserialize($row->vt_fields);
		}
		
		// Make sure it's an array.
		if (!is_array($row->vt_fields)) {
			$row->vt_fields = array();
		}
		
		$lists['published'] = RSFormProHelper::renderHTML('select.booleanlist', 'vt_published', 'class="inputbox" onclick="enablevTiger(this.value)"', $row->vt_published);
		$fields				= false;
		
		if ($row->vt_published) {
			// Show errors if plugin is not configured correctly.
			if (!$row->vt_accesskey || !$row->vt_username || !$row->vt_hostname) {
				// No access key
				if (!$row->vt_accesskey) {
					JError::raiseWarning(500, JText::_('RSFP_VT_PLEASE_SUPPLY_YOUR_ACCESS_KEY'));
				}
				// No username
				if (!$row->vt_username) {
					JError::raiseWarning(500, JText::_('RSFP_VT_PLEASE_SUPPLY_YOUR_USERNAME'));
				}
				// No host setup
				if (!$row->vt_hostname) {
					JError::raiseWarning(500, JText::_('RSFP_VT_PLEASE_SUPPLY_YOUR_HOSTNAME'));
				}
				
				// Info
				JError::raiseNotice(500, JText::_('RSFP_VT_SURPRESS_ERRORS_DISABLE_INTEGRATION'));
			} else {
				// Connect to Vtiger and grab fields.
				try {
					list($sessionName, $userId) = $this->login($row->vt_username, $row->vt_accesskey, $row->vt_hostname);
					
					$response = $this->webservice($row->vt_hostname, $row->vt_username, array(
						'operation' 	=> 'describe',
						'sessionName' 	=> $sessionName,
						'elementType' 	=> 'Leads'
					));
					
					$fields = $response['result']['fields'];
				} catch (Exception $e) {
					JError::raiseWarning(500, '[Vtiger CRM Integration] '.$e->getMessage());
				}
			}
		}
		
		echo '<div id="vtigerdiv">';
			include JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/vtiger.php';
		echo '</div>';
	}
	
	protected function login($username, $accessKey, $hostname) {
		$response = $this->webservice($hostname, $username, array(
			'operation' => 'getchallenge',
			'username'  => $username
		));
		
		$token = $response['result']['token'];
		
		$response = $this->webservice($hostname, $username, array(
			'operation' => 'login',
			'username'  => $username,
			'accessKey' => md5($token . $accessKey),
		), true);
		
		return array($response['result']['sessionName'], $response['result']['userId']);
	}
	
	public function rsfp_bk_onAfterShowFormEditTabsTab()
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfpvtiger');
		
		echo '<li><a href="javascript: void(0);" id="vtiger"><span class="rsficon rsficon-paw"></span><span class="inner-text">'.JText::_('RSFP_VTIGER_INTEGRATION').'</span></a></li>';
	}
	
	public function rsfp_f_onAfterFormProcess($args)
	{
		$db				= JFactory::getDBO();
		$formId 		= (int) $args['formId'];
		$SubmissionId 	= (int) $args['SubmissionId'];
		
		$db->setQuery("SELECT * FROM #__rsform_vtiger WHERE `form_id`='".$formId."' AND `vt_published`='1'");
		if ($row = $db->loadObject()) {
			// Get replacements
			list($replace, $with) = RSFormProHelper::getReplacements($SubmissionId);
			
			// Add some of our own
			$replace[] = '\n';
			$with[]	   = "\n";

			// Grab configured fields
			$fields = array();
			if ($row->vt_fields) {
				$fields = unserialize($row->vt_fields);
			}
			
			if (!is_array($fields)) {
				$fields = array();
			}
			
			// Prepare the element ("Leads" in our case) properties.
			$element = array();
            foreach ($fields as $field => $value) {
				$element[$field] = str_replace($replace, $with, $value);
            }
			
			try {
				// Connect to Vtiger and login.
				list($sessionName, $userId) = $this->login($row->vt_username, $row->vt_accesskey, $row->vt_hostname);
				
				// Assign the Lead's user ID.
				$element['assigned_user_id'] =  $userId;
				
				// Create the Lead.
				$response = $this->webservice($row->vt_hostname, $row->vt_username, array(
					'operation' 	=> 'create',
					'sessionName' 	=> $sessionName,
					'elementType' 	=> 'Leads',
					'element' 		=> json_encode($element)
				), true);
				
				// Logout
				$this->webservice($row->vt_hostname, $row->vt_username, array(
					'operation' => 'logout',
					'sessionName' => $sessionName,
				), true);
			} catch (Exception $e) {
				JError::raiseWarning(500, '[Vtiger CRM Integration] '.$e->getMessage());
			}
		}
	}
    
    protected function webservice($hostname, $username, $data = null, $post = false) {
		// Create URL
		$url = rtrim($hostname, ' /').'/webservice.php';
		
		if (!function_exists('curl_init') || !function_exists('curl_exec')) {
			throw new Exception(JText::_('RSFP_VT_CURL_IS_NOT_INSTALLED_OR_ENABLED'));
		}
		
		if ($post) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		} else {
			$ch = curl_init($url.'?'.http_build_query($data));
		}
	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
		
        $json = curl_exec($ch);
		// Attempt to clean JSON response of extra characters.
		if (preg_match('/{.*}/', $json, $match)) {
			$json = $match[0];
		}
		
		$code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);
		
		if ($error) {
			throw new Exception($error);
		}
		
		if ($code != 200) {
			throw new Exception(JText::sprintf('RSFP_VT_HTTP_CODE_RECEIVED', $code));
		}
		
		$data = json_decode($json, true);
		
		if (!$data) {
			throw new Exception(JText::_('RSFP_VT_COULD_NOT_PARSE_JSON_DATA'));
		}
		
		if (!$data['success']) {
			throw new Exception($data['error']['message']);
		}
		
		return $data;
    }
	
	public function rsfp_onFormDelete($formId) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->delete('#__rsform_vtiger')
			  ->where($db->qn('form_id').'='.$db->q($formId));
		$db->setQuery($query)->execute();
	}
	
	public function rsfp_onFormBackup($form, $xml, $fields) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('*')
			  ->from($db->qn('#__rsform_vtiger'))
			  ->where($db->qn('form_id').'='.$db->q($form->FormId));
		$db->setQuery($query);
		if ($vtiger = $db->loadObject()) {
			// No need for a form_id
			unset($vtiger->form_id);
			
			$xml->add('vtiger');
			foreach ($vtiger as $property => $value) {
				$xml->add($property, $value);
			}
			$xml->add('/vtiger');
		}
	}
	
	public function rsfp_onFormRestore($form, $xml, $fields) {
		if (isset($xml->vtiger)) {
			$data = array(
				'form_id' => $form->FormId
			);
			
			foreach ($xml->vtiger->children() as $property => $value) {
				$data[$property] = (string) $value;
			}
			
			$row = JTable::getInstance('RSForm_vTiger', 'Table');
			
			if (!$row->load($form->FormId)) {
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query	->insert('#__rsform_vtiger')
						->set(array(
								$db->qn('form_id') .'='. $db->q($form->FormId),
						));
				$db->setQuery($query)->execute();
			}
			
			$row->save($data);
		}
	}
	
	public function rsfp_bk_onFormRestoreTruncate() {
		JFactory::getDbo()->truncateTable('#__rsform_vtiger');
	}
}