<?php
/**
* @package RSForm!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/Ctct/autoload.php')) {
	return;
}

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/Ctct/autoload.php';
use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;

class plgSystemRSFPConstantContact extends JPlugin
{
	public function rsfp_onFormSave($form) {
		$post = JFactory::getApplication()->input->get('ccontact', array(), 'array');
		$post['form_id'] = JFactory::getApplication()->input->getInt('formId',0);
		$row = JTable::getInstance('RSForm_ConstantContact', 'Table');
		
		if (!$row) {
			return;
		}
		
		if (!$row->bind($post)) {
			JError::raiseWarning(500, $row->getError());
			return false;
		}
		
		$row->cc_merge_vars = isset($post['cc_merge_vars']) ? serialize($post['cc_merge_vars']) : '';
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT form_id FROM #__rsform_constantcontact WHERE form_id='".(int) $post['form_id']."'");
		if (!$db->loadResult()) {
			$db->setQuery("INSERT INTO #__rsform_constantcontact SET form_id='".(int) $post['form_id']."'");
			$db->execute();
		}
		
		if ($row->store()) {
			return true;
		} else {
			JError::raiseWarning(500, $row->getError());
			return false;
		}
	}
	
	public function rsfp_bk_onAfterShowFormEditTabs() {
		JFactory::getLanguage()->load('plg_system_rsfpconstantcontact');
		
		$formId = JFactory::getApplication()->input->getInt('formId',0);
		$row	= JTable::getInstance('RSForm_ConstantContact', 'Table');
		$fields = array();
		
		if (!$row) {
			return;
		}
		
		$row->load($formId);
		$row->cc_merge_vars = @unserialize($row->cc_merge_vars);
		if ($row->cc_merge_vars === false) {
			$row->cc_merge_vars = array();
		}
		
		$cc_action = array(
			JHTML::_('select.option', 1, JText::_('RSFP_CC_ACTION_SUBSCRIBE')),
			JHTML::_('select.option', 0, JText::_('RSFP_CC_ACTION_UNSUBSCRIBE')),
			JHTML::_('select.option', 2, JText::_('RSFP_CC_LET_USER_DECIDE'))
		);
		
		$fields_array = $this->_getFields($formId);
		$fields[] = JHTML::_('select.option', '', JText::_('RSFP_CC_IGNORE'));
		foreach ($fields_array as $field) {
			$fields[] = JHTML::_('select.option', $field, $field);
		}
		
		$cc_email_type = array(
			JHTML::_('select.option', 'HTML', JText::_('RSFP_CC_HTML')),
			JHTML::_('select.option', 'Text', JText::_('RSFP_CC_TEXT')),
			JHTML::_('select.option', 'user', JText::_('RSFP_CC_LET_USER_DECIDE'))
		);
		
		$merge_vars = array(
			"email_address" 			=> JText::_('RSFP_CC_EMAIL_ADDRESS'),
			"first_name" 				=> JText::_('RSFP_CC_FIRSTNAME'),
			"last_name" 				=> JText::_('RSFP_CC_LASTNAME'),
			"middle_name" 				=> JText::_('RSFP_CC_MIDDLENAME'),
			"home_phone" 				=> JText::_('RSFP_CC_HOMEPHONE'),
			"company_name" 				=> JText::_('RSFP_CC_COMPANYNAME'),
			"job_title" 				=> JText::_('RSFP_CC_JOBTITLE'),
			"work_phone" 				=> JText::_('RSFP_CC_WORKPHONE'),
			"note_note" 				=> JText::_('RSFP_CC_NOTES'),
			"address_address_type"		=> JText::_('RSFP_CC_ADDR_TYPE'),
			"address_line1" 			=> JText::_('RSFP_CC_ADDR1'),
			"address_line2" 			=> JText::_('RSFP_CC_ADDR2'),
			"address_line3" 			=> JText::_('RSFP_CC_ADDR3'),
			"address_city" 				=> JText::_('RSFP_CC_CITY'), 
			"address_state_code" 		=> JText::_('RSFP_CC_STATECODE'),
			"address_state" 			=> JText::_('RSFP_CC_STATENAME'),
			"address_country_code" 		=> JText::_('RSFP_CC_COUNTRYCODE'), 
			"address_postal_code" 		=> JText::_('RSFP_CC_POSTALCODE'),
			"address_sub_postal_code" 	=> JText::_('RSFP_CC_SUBPOSTALCODE'),
			"CustomField1" 				=> JText::_('RSFP_CC_CF1'),
			"CustomField2" 				=> JText::_('RSFP_CC_CF2'),
			"CustomField3" 				=> JText::_('RSFP_CC_CF3'),
			"CustomField4" 				=> JText::_('RSFP_CC_CF4'),
			"CustomField5" 				=> JText::_('RSFP_CC_CF5'),
			"CustomField6" 				=> JText::_('RSFP_CC_CF6'),
			"CustomField7" 				=> JText::_('RSFP_CC_CF7'),
			"CustomField8" 				=> JText::_('RSFP_CC_CF8'),
			"CustomField9" 				=> JText::_('RSFP_CC_CF9'),
			"CustomField10" 			=> JText::_('RSFP_CC_CF10'),
			"CustomField11" 			=> JText::_('RSFP_CC_CF11'),
			"CustomField12" 			=> JText::_('RSFP_CC_CF12'),
			"CustomField13" 			=> JText::_('RSFP_CC_CF13'),
			"CustomField14" 			=> JText::_('RSFP_CC_CF14'),
			"CustomField15" 			=> JText::_('RSFP_CC_CF15')
		);
		
		// Enable integration
		$lists['cc_published'] = RSFormProHelper::renderHTML('select.booleanlist','ccontact[cc_published]','class="inputbox"',$row->cc_published);
		
		// Action
		$lists['cc_action'] = JHTML::_('select.genericlist', $cc_action, 'ccontact[cc_action]', 'onchange="rsfp_changeCcAction(this);"', 'value', 'text', $row->cc_action);
		
		// Action field
		$lists['cc_action_field'] = JHTML::_('select.genericlist', $fields, 'ccontact[cc_action_field]', $row->cc_action != 2 ? 'disabled="disabled"' : '', 'value', 'text', $row->cc_action_field);
		
		// Delete
		$lists['cc_delete_member'] = RSFormProHelper::renderHTML('select.booleanlist','ccontact[cc_delete_member]','class="inputbox"',$row->cc_delete_member);
		
		// Lists
		$lists['cc_list_id'] = JHTML::_('select.genericlist', $this->getLists(), 'ccontact[cc_list_id]', 'onchange="rsfp_showCcVars(this.value)"', 'value', 'text', $row->cc_list_id);
		
		// Update existing contact
		$lists['cc_update'] = RSFormProHelper::renderHTML('select.booleanlist','ccontact[cc_update]','class="inputbox"',$row->cc_update);
		
		// Fields
		$lists['cc_fields'] = array();
		if (is_array($merge_vars)) {
			foreach ($merge_vars as $merge_var => $title) {
				$lists['cc_fields'][$merge_var] = JHTML::_('select.genericlist', $fields, 'ccontact[cc_merge_vars]['.$merge_var.']', null, 'value', 'text', isset($row->cc_merge_vars[$merge_var]) ? $row->cc_merge_vars[$merge_var] : null);
			}
		}
		
		echo '<div id="constantcontactdiv">';
			include JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/constantcontact.php';
		echo '</div>';
	}
	
	protected function getLists() {
		$api 	= RSFormProHelper::getConfig('cc.api');
		$token 	= RSFormProHelper::getConfig('cc.token');
		$lists	= array(JHTML::_('select.option', '0', JText::_('RSFP_PLEASE_SELECT_LIST')));
		$cc		= new ConstantContact($api);
		
		try {
			if ($cclists = $cc->getLists($token)) {
				foreach ($cclists as $cclist) {
					$lists[] = JHTML::_('select.option', $cclist->id, $cclist->name);
				}
			}
		} catch (Exception $e) {
			foreach ($e->getErrors() as $error) {
				JFactory::getApplication()->enqueueMessage('ConstantContact: '.$error['error_message'], 'warning');
			}
		}
		
		return $lists;
	}
	
	public function rsfp_bk_onAfterShowFormEditTabsTab() {
		JFactory::getLanguage()->load('plg_system_rsfpconstantcontact');
		echo '<li><a href="javascript: void(0);" id="constantcontact"><span class="rsficon rsficon-envelope"></span><span class="inner-text">'.JText::_('RSFP_CONSTANTCONTACT_INTEGRATION').'</span></a></li>';
	}
	
	public function rsfp_f_onAfterFormProcess($args) {
		$db				= JFactory::getDBO();
		$formId			= (int) $args['formId'];
		$SubmissionId	= (int) $args['SubmissionId'];
		
		$db->setQuery("SELECT * FROM #__rsform_constantcontact WHERE `form_id`='".$formId."' AND `cc_published`='1'");
		if ($row = $db->loadObject()) {
			if (!$row->cc_list_id) return;

			JFactory::getLanguage()->load('plg_system_rsfpconstantcontact', JPATH_ADMINISTRATOR);

			list($replace, $with) = RSFormProHelper::getReplacements($SubmissionId);
			
			$row->cc_merge_vars = @unserialize($row->cc_merge_vars);
			if ($row->cc_merge_vars === false) {
				$row->cc_merge_vars = array();
			}
			
			if (!isset($row->cc_merge_vars['email_address'])) {
				return;
			}
			
			$form	= JFactory::getApplication()->input->get('form', array(), 'array');
			$email	= isset($form[$row->cc_merge_vars['email_address']]) ? $form[$row->cc_merge_vars['email_address']] : null;
			
			if (!$email) {
				return;
			}
			
			$subscribe = true;
			if ($row->cc_action == 1) {
				$subscribe = true;
			} elseif ($row->cc_action == 0) {
				$subscribe = false;
			} elseif ($row->cc_action == 2 && isset($form[$row->cc_action_field])) {
				$subscribe = null;
				if (is_array($form[$row->cc_action_field])) {
					foreach ($form[$row->cc_action_field] as $i => $value) {
						$value = strtolower(trim($value));
						if ($value == 'subscribe') {
							$subscribe = true;
							break;
						} elseif ($value == 'unsubscribe') {
							$subscribe = false;
							break;
						}
					}
				} else {
					$form[$row->cc_action_field] = strtolower(trim($form[$row->cc_action_field]));
					if ($form[$row->cc_action_field] == 'subscribe') {
						$subscribe = true;
					} elseif ($form[$row->cc_action_field] == 'unsubscribe') {
						$subscribe = false;
					} else {
						$subscribe = null;
					}
				}
			}
			
			$merge_vars = array();
			$merge_vars['addresses'][] = array();
			$merge_vars['notes'][] = array();
			
			foreach ($row->cc_merge_vars as $tag => $field) {
				if (empty($tag)) continue;
				if ($tag == 'email_address') continue;
				
				if (!isset($form[$field]))
					$form[$field] = '';
				
				if (is_array($form[$field]))
				{
					array_walk($form[$field], array($this, '_escapeCommas'));
					$form[$field] = implode(',', $form[$field]);
				}
				
				if (strncmp($tag, 'CustomField', strlen('CustomField')) === 0) {
					$merge_vars['custom_fields'][] = array('name' => $tag, 'value' => $form[$field]);
				} elseif (strncmp($tag, 'note', strlen('note')) === 0) {
					if (!empty($form[$field])) {
						$merge_vars['notes'][0][str_replace('note_','',$tag)] = $form[$field];
					}
				} elseif (strncmp($tag, 'address_', strlen('address_')) === 0) {
					$merge_vars['addresses'][0][substr($tag, strlen('address_'))] = $form[$field];
				} else {
					$merge_vars[$tag] = $form[$field];
				}
			}
			
			if (empty($merge_vars['notes'][0])) {
				unset($merge_vars['notes']);
			}
			
			$address_type = &$merge_vars['addresses'][0]['address_type'];
			
			if (empty($address_type) || !in_array($address_type, array('PERSONAL', 'BUSINESS'))) {
				$address_type = 'PERSONAL';
			}
			
			$api 	= RSFormProHelper::getConfig('cc.api');
			$token 	= RSFormProHelper::getConfig('cc.token');
			$cc		= new ConstantContact($api);
			
			if ($subscribe) {
				try {
					$response = $cc->getContactByEmail($token, $email);
					
					// Add contact
					if (empty($response->results)) {
						$contact = Contact::create($merge_vars);
						$contact->addEmail($email);
						$contact->addList($row->cc_list_id);
						
						$cc->addContact($token, $contact, true);
					} else {
						// Update contact
						if ($row->cc_update) {
							$contact = $response->results[0];
							$contact->addList($row->cc_list_id);
							
							if (!empty($merge_vars)) {
								foreach ($merge_vars as $var => $value) {
									$contact->$var = $value;
								}
							}
							
							$cc->updateContact($token, $contact, true);
						} else {
							JFactory::getApplication()->enqueueMessage(JText::sprintf('RSFP_CC_EMAIL_COULD_NOT_UPDATE', $email), 'warning');
						}
					}
				} catch (Exception $e) {
					foreach ($e->getErrors() as $error) {
						JFactory::getApplication()->enqueueMessage('ConstantContact: '.$error['error_message'], 'warning');
					}
				}
			} elseif ($subscribe === false) {
				try {
					$response	= $cc->getContactByEmail($token, $email);
					$contactID	= !empty($response->results) && isset($response->results[0]) ? $response->results[0]->id : null;

					if ($contactID) {
						if ($row->cc_delete_member) {
							$cc->deleteContactFromList($token, $contactID, $row->cc_list_id);
						} else {
							$cc->deleteContact($token, $contactID);
						}
					} else {
						JFactory::getApplication()->enqueueMessage(JText::sprintf('RSFP_CC_EMAIL_DOES_NOT_EXIST', $email), 'warning');
					}
				} catch (Exception $e) {
					foreach ($e->getErrors() as $error) {
						JFactory::getApplication()->enqueueMessage('ConstantContact: '.$error['error_message'], 'warning');
					}
				}
			}
		}
	}
	
	public function rsfp_bk_onAfterShowConfigurationTabs($tabs) {
		JFactory::getLanguage()->load('plg_system_rsfpconstantcontact');
		
		$tabs->addTitle(JText::_('Constant Contact'), 'form-constantcontact');
		$tabs->addContent($this->constantContactConfigurationScreen());
	}
	
	protected function constantContactConfigurationScreen() {
		ob_start();
		?>
		<div id="page-constantcontact">
			<p><?php echo JText::_('RSFP_CC_API_KEY_REQUEST'); ?></p>
			<table class="admintable">
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="ccapi"><span class="hasTip" title="<?php echo JText::_('RSFP_CC_API_KEY_DESC'); ?>"><?php echo JText::_('RSFP_CC_API_KEY'); ?></span></label></td>
					<td><input type="text" name="rsformConfig[cc.api]" id="ccapi" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('cc.api')); ?>" size="100" maxlength="100"></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="cctoken"><span class="hasTip" title="<?php echo JText::_('RSFP_CC_TOKEN_DESC'); ?>"><?php echo JText::_('RSFP_CC_TOKEN'); ?></span></label></td>
					<td><input type="text" name="rsformConfig[cc.token]" id="cctoken" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('cc.token')); ?>" size="50" maxlength="100"></td>
				</tr>
			</table>
		</div>
		<?php
		
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	public function rsfp_onFormDelete($formId) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->delete('#__rsform_constantcontact')
			  ->where($db->qn('form_id').'='.$db->q($formId));
		$db->setQuery($query)->execute();
	}
	
	public function rsfp_onFormBackup($form, $xml, $fields) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('*')
			  ->from($db->qn('#__rsform_constantcontact'))
			  ->where($db->qn('form_id').'='.$db->q($form->FormId));
		$db->setQuery($query);
		if ($cc = $db->loadObject()) {
			// No need for a form_id
			unset($cc->form_id);
			
			$xml->add('constantcontact');
			foreach ($cc as $property => $value) {
				$xml->add($property, $value);
			}
			$xml->add('/constantcontact');
		}
	}
	
	public function rsfp_onFormRestore($form, $xml, $fields) {
		if (isset($xml->constantcontact)) {
			$data = array(
				'form_id' => $form->FormId
			);
			
			foreach ($xml->constantcontact->children() as $property => $value) {
				$data[$property] = (string) $value;
			}
			
			$row = JTable::getInstance('RSForm_ConstantContact', 'Table');
			
			if (!$row->load($form->FormId)) {
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query	->insert('#__rsform_constantcontact')
						->set(array(
								$db->qn('form_id') .'='. $db->q($form->FormId),
						));
				$db->setQuery($query)->execute();
			}
			
			$row->save($data);
		}
	}
	
	public function rsfp_bk_onFormRestoreTruncate() {
		JFactory::getDbo()->truncateTable('#__rsform_constantcontact');
	}
	
	protected function _getFields($formId) {
		$db = JFactory::getDBO();
		
		$db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".(int) $formId."' AND p.PropertyName='NAME' ORDER BY c.Order");
		return $db->loadColumn();
	}
	
	protected function _escapeCommas(&$item) {
		$item = str_replace(',', '\,', $item);
	}
}