<?php
/**
* @package RSform!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * RSForm! Pro system plugin
 */
class plgSystemRSFPDotMailer extends JPlugin
{	
	public function rsfp_onFormSave($form) {
		$post = JFactory::getApplication()->input->get('dotmailer', array(), 'array');
		$post['form_id'] = JFactory::getApplication()->input->getInt('formId',0);
		$row = JTable::getInstance('RSForm_DotMailer', 'Table');
		
		if (!$row) {
			return;
		}
		
		if (!$row->bind($post)) {
			JError::raiseWarning(500, $row->getError());
			return false;
		}
		
		$row->dm_merge_vars = isset($post['dm_merge_vars']) ? serialize($post['dm_merge_vars']) : '';
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT form_id FROM #__rsform_dotmailer WHERE form_id='".(int) $post['form_id']."'");
		if (!$db->loadResult()) {
			$db->setQuery("INSERT INTO #__rsform_dotmailer SET form_id='".(int) $post['form_id']."'");
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
		JFactory::getLanguage()->load('plg_system_rsfpdotmailer');
		
		$formId = JFactory::getApplication()->input->getInt('formId',0);
		$row	= JTable::getInstance('RSForm_DotMailer', 'Table');
		
		if (!$row) {
			return;
		}
		
		$row->load($formId);
		$row->dm_merge_vars = @unserialize($row->dm_merge_vars);
		if ($row->dm_merge_vars === false) {
			$row->dm_merge_vars = array();
		}
		
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/dotmailerapi.php';
		$dotmailer = new RSFPDotmailer(RSFormProHelper::getConfig('dotmailer.username'), RSFormProHelper::getConfig('dotmailer.password'));
		
		// Fields
		$fields_array = $this->_getFields($formId);
		$fields = array();
		foreach ($fields_array as $field)
			$fields[] = JHTML::_('select.option', $field, $field);
		
		// Action
		$dotmailer_action = array(
			JHTML::_('select.option', 1, JText::_('RSFP_DOTMAILER_ACTION_SUBSCRIBE')),
			JHTML::_('select.option', 0, JText::_('RSFP_DOTMAILER_ACTION_UNSUBSCRIBE')),
			JHTML::_('select.option', 3, JText::_('RSFP_DOTMAILER_ACTION_REMOVE')),
			JHTML::_('select.option', 2, JText::_('RSFP_DOTMAILER_LET_USER_DECIDE'))
		);
		$lists['dm_action'] = JHTML::_('select.genericlist', $dotmailer_action, 'dotmailer[dm_action]', 'onchange="rsfp_changeDmAction(this);"', 'value', 'text', $row->dm_action);
		
		// Action Field
		$lists['dm_action_field'] = JHTML::_('select.genericlist', $fields, 'dotmailer[dm_action_field]', $row->dm_action != 2 ? 'disabled="disabled"' : '', 'value', 'text', $row->dm_action_field);
		
		// Audience Type
		$dotmailer_audience_type = array(
			JHTML::_('select.option', 'Unknown', JText::_('RSFP_DOTMAILER_UNKNOWN')),
			JHTML::_('select.option', 'B2C', JText::_('RSFP_DOTMAILER_B2C')),
			JHTML::_('select.option', 'B2B', JText::_('RSFP_DOTMAILER_B2B')),
			JHTML::_('select.option', 'B2M', JText::_('RSFP_DOTMAILER_B2M')),
			JHTML::_('select.option', 'user', JText::_('RSFP_DOTMAILER_LET_USER_DECIDE'))
		);
		$lists['dm_audience_type'] = JHTML::_('select.genericlist', $dotmailer_audience_type, 'dotmailer[dm_audience_type]', 'onchange="rsfp_changeDmAudienceType(this);"', 'value', 'text', $row->dm_audience_type);
		
		// Audience Type Field
		$lists['dm_audience_type_field'] = JHTML::_('select.genericlist', $fields, 'dotmailer[dm_audience_type_field]', $row->dm_audience_type != 'user' ? 'disabled="disabled"' : '', 'value', 'text', $row->dm_audience_type_field);
		
		// OptIn Type
		$dotmailer_optin_type = array(
			JHTML::_('select.option', 'Unknown', JText::_('RSFP_DOTMAILER_UNKNOWN')),
			JHTML::_('select.option', 'Single', JText::_('RSFP_DOTMAILER_SINGLE')),
			JHTML::_('select.option', 'VerifiedDouble', JText::_('RSFP_DOTMAILER_VERIFIEDDOUBLE')),
			JHTML::_('select.option', 'user', JText::_('RSFP_DOTMAILER_LET_USER_DECIDE'))
		);
		$lists['dm_optin_type'] = JHTML::_('select.genericlist', $dotmailer_optin_type, 'dotmailer[dm_optin_type]', 'onchange="rsfp_changeDmOptinType(this);"', 'value', 'text', $row->dm_optin_type);
		
		// OptIn Type Field
		$lists['dm_optin_type_field'] = JHTML::_('select.genericlist', $fields, 'dotmailer[dm_optin_type_field]', $row->dm_optin_type != 'user' ? 'disabled="disabled"' : '', 'value', 'text', $row->dm_optin_type_field);
		
		// Email Type
		$dotmailer_email_type = array(
			JHTML::_('select.option', 'Html', JText::_('RSFP_DOTMAILER_HTML')),
			JHTML::_('select.option', 'PlainText', JText::_('RSFP_DOTMAILER_TEXT')),
			JHTML::_('select.option', 'user', JText::_('RSFP_DOTMAILER_LET_USER_DECIDE'))
		);
		$lists['dm_email_type'] = JHTML::_('select.genericlist', $dotmailer_email_type, 'dotmailer[dm_email_type]', 'onchange="rsfp_changeDmEmailType(this);"', 'value', 'text', $row->dm_email_type);
		
		// Email Type Field
		$lists['dm_email_type_field'] = JHTML::_('select.genericlist', $fields, 'dotmailer[dm_email_type_field]', $row->dm_email_type != 'user' ? 'disabled="disabled"' : '', 'value', 'text', $row->dm_email_type_field);
		
		// DotMailer Lists
		try {
			$results = $dotmailer->getLists();
		} catch (Exception $e) {
			JError::raiseWarning(500, 'DotMailer: '.$e->getMessage());
		}
		$dotmailer_lists = array(
			JHTML::_('select.option', '', JText::_('RSFP_PLEASE_SELECT_LIST'))
		);
		
		if (!empty($results) && is_array($results)) {
			foreach ($results as $result) {
				$dotmailer_lists[] = JHTML::_('select.option', $result->id, $result->name);
			}
		}
		
		$lists['dm_list_id'] = JHTML::_('select.genericlist', $dotmailer_lists, 'dotmailer[dm_list_id]', 'onchange="rsfp_changeDmList(this);"', 'value', 'text', $row->dm_list_id);
		
		// Merge Vars
		$merge_vars = JText::_('RSFP_PLEASE_SELECT_LIST');
		if ($row->dm_list_id) {
			try {
				$merge_vars = $dotmailer->getFields();
			} catch (Exception $e) {
				JError::raiseWarning(500, 'DotMailer: '.$e->getMessage());
			}
		}
		
		$lists['fields'] = array();
		if (!empty($merge_vars) && is_array($merge_vars))
			foreach ($merge_vars as $i => $merge_var)
				$lists['dm_fields'][$merge_var->name] = JHTML::_('select.genericlist', $fields, 'dotmailer[dm_merge_vars]['.$merge_var->name.']', null, 'value', 'text', isset($row->dm_merge_vars[$merge_var->name]) ? $row->dm_merge_vars[$merge_var->name] : null);
		
		$lists['dm_email'] = JHTML::_('select.genericlist', $fields, 'dotmailer[dm_email]', '', 'value', 'text', $row->dm_email);
		$lists['dm_published'] = RSFormProHelper::renderHTML('select.booleanlist','dotmailer[dm_published]','class="inputbox"',$row->dm_published);
		
		echo '<div id="dotmailerdiv">';
			include JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/dotmailer.php';
		echo '</div>';
	}
	
	public function rsfp_bk_onAfterShowFormEditTabsTab() {
		JFactory::getLanguage()->load('plg_system_rsfpdotmailer');
		
		echo '<li><a href="javascript: void(0);" id="dotmailer"><span class="rsficon rsficon-dot-circle-o"></span><span class="inner-text">'.JText::_('RSFP_DOTMAILER_INTEGRATION').'</span></a></li>';
	}
	
	public function rsfp_f_onBeforeStoreSubmissions($args) {
		$db				= JFactory::getDBO();		
		$formId			= (int) $args['formId'];
		$SubmissionId	= (int) $args['SubmissionId'];
		
		$db->setQuery("SELECT * FROM #__rsform_dotmailer WHERE `form_id`='".$formId."' AND `dm_published`='1'");
		if ($row = $db->loadObject()) {
			if (!$row->dm_list_id)	return;
			if (!$row->dm_email)	return;
			
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/dotmailerapi.php';
			$dotmailer = new RSFPDotmailer(RSFormProHelper::getConfig('dotmailer.username'), RSFormProHelper::getConfig('dotmailer.password'));
			
			list($replace, $with) = RSFormProHelper::getReplacements($SubmissionId);
			
			$row->dm_merge_vars = @unserialize($row->dm_merge_vars);
			if ($row->dm_merge_vars === false)
				$row->dm_merge_vars = array();
			
			$form =& $args['post'];
			
			$email_address = @$form[$row->dm_email];
			$email_address = trim($email_address);
			
			$merge_vars = array();		
			if (!empty($row->dm_merge_vars)) {
				foreach ($row->dm_merge_vars as $tag => $field) {
					if ($field == '- IGNORE -') continue;
					
					if (!isset($form[$field]))
						$form[$field] = '';
					
					if (is_array($form[$field]))
					{
						array_walk($form[$field], array('plgSystemRSFPDotMailer', '_escapeCommas'));
						$form[$field] = implode(',', $form[$field]);
					}
					
					$merge_vars[] = array('Key' => $tag, 'Value' => $form[$field]);
				}
			}
			
			// Audience Type
			$audience_type = $row->dm_audience_type;
			$valid_audience = array('Unknown', 'B2C', 'B2B', 'B2M');
			if ($row->dm_audience_type == 'user')
				$audience_type = isset($form[$row->dm_audience_type_field]) && in_array(trim($form[$row->dm_audience_type_field]), $valid_audience) ? $form[$row->dm_audience_type_field] : 'Unknown';
			
			// OptIn Type
			$optin_type = $row->dm_optin_type;
			$valid_optin = array('Unknown', 'Single', 'VerifiedDouble');
			if ($row->dm_optin_type == 'user')
				$optin_type = isset($form[$row->dm_optin_type_field]) && in_array(trim($form[$row->dm_optin_type_field]), $valid_optin) ? $form[$row->dm_optin_type_field] : 'Single';
			
			// Email Type
			$email_type = $row->dm_email_type;
			$valid_email = array('Html', 'PlainText');
			if ($row->dm_email_type == 'user')
				$email_type = isset($form[$row->dm_email_type_field]) && in_array(trim($form[$row->dm_email_type_field]), $valid_email) ? $form[$row->dm_email_type_field] : 'Html';

			$list_id = $row->dm_list_id;
			
			// Subscribe action - Subscribe, Unsubscribe, Remove or Let the user choose
			$action = 'ignore';
			if ($row->dm_action == 0) {
				$action = 'unsubscribe';
			} elseif ($row->dm_action == 1) {
				$action = 'subscribe';
			} elseif ($row->dm_action == 3) {
				$action = 'remove';
			} elseif ($row->dm_action == 2 && isset($form[$row->dm_action_field])) {
				if (is_array($form[$row->dm_action_field])) {
					foreach ($form[$row->dm_action_field] as $i => $value) {
						$value = strtolower(trim($value));
						if ($value == 'subscribe') {
							$action = 'subscribe';
							break;
						} elseif ($value == 'unsubscribe') {
							$action = 'unsubscribe';
							break;
						} elseif ($value == 'remove') {
							$action = 'remove';
							break;
						}
					}
				} else {
					$form[$row->dm_action_field] = strtolower(trim($form[$row->dm_action_field]));
					if ($form[$row->dm_action_field] == 'subscribe') {
						$action = 'subscribe';
					} elseif ($form[$row->dm_action_field] == 'unsubscribe') {
						$action = 'unsubscribe';
					} elseif ($form[$row->dm_action_field] == 'remove') {
						$action = 'remove';
					}
				}
			}
			
			
			try {
				if ($action == 'subscribe') {
					// Add new contact to list
					$contact = array(
						'id' => 'generic',
						'email' => $email_address,
						'optInType' => $optin_type,
						'emailType' => $email_type,
						'dataFields' => $merge_vars,
						'status' => 'Subscribed'
					);
					$dotmailer->addContact($list_id,$contact);
				} elseif ($action == 'unsubscribe') {
					// Unsubscribe the user
					$contact = array(
						'email' => $email_address,
						'status' => 'Unsubscribed'
					);
					$dotmailer->unsubscribeContact($list_id,$contact);
				} elseif ($action == 'remove') {
					$dotmailer->removeContact($list_id,$email_address);
				}
			} catch (Exception $e) {
				JError::raiseWarning(500, 'DotMailer: '.$e->getMessage());
				return false;
			}
		}
	}
	
	public function rsfp_bk_onAfterShowConfigurationTabs($tabs) {		
		JFactory::getLanguage()->load('plg_system_rsfpdotmailer');
		
		$tabs->addTitle(JText::_('DotMailer'), 'form-dotmailer');
		$tabs->addContent($this->dotMailerConfigurationScreen());
	}
	
	public function dotMailerConfigurationScreen() {
		ob_start();
		?>
		<div id="page-dotmailer" class="com-rsform-css-fix">
			<table class="admintable">
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="dotmailerusername"><span class="hasTip" title="<?php echo JText::_('RSFP_DOTMAILER_USERNAME_DESC'); ?>"><?php echo JText::_( 'RSFP_DOTMAILER_USERNAME' ); ?></span></label></td>
					<td><input autocomplete="off" type="text" name="rsformConfig[dotmailer.username]" id="dotmailerusername" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('dotmailer.username')); ?>" size="100"></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="dotmailerpassword"><span class="hasTip" title="<?php echo JText::_('RSFP_DOTMAILER_PASSWORD_DESC'); ?>"><?php echo JText::_( 'RSFP_DOTMAILER_PASSWORD' ); ?></span></label></td>
					<td><input autocomplete="off" type="password" name="rsformConfig[dotmailer.password]" id="dotmailerpassword" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('dotmailer.password')); ?>" size="100"></td>
				</tr>
			</table>
		</div>
		<?php
		
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	public function _getFields($formId) {
		$db = JFactory::getDBO();
		
		$db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".(int) $formId."' AND p.PropertyName='NAME' ORDER BY c.Order");
		$fields = $db->loadColumn();
		
		array_unshift($fields, '- IGNORE -');
		return $fields;
	}
	
	public function _escapeCommas(&$item) {
		$item = str_replace(',', '\,', $item);
	}
	
	public function rsfp_bk_onSwitchTasks() {
		$input		 = JFactory::getApplication()->input;
		$plugin_task = $input->getCmd('plugin_task','');
		$list_id	 = $input->getInt('list_id',0);
		
		if (!empty($list_id)) {
			switch ($plugin_task) {
				case 'get_merge_vars_dm':
					require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/dotmailerapi.php';
					$dotmailer  = new RSFPDotmailer(RSFormProHelper::getConfig('dotmailer.username'), RSFormProHelper::getConfig('dotmailer.password'));
					$results 	= $dotmailer->getFields();
					$array 		= array();
					
					if (is_array($results)) {
						foreach ($results as $i => $result) {
							$array[] = $result->name;
						}
						echo implode("\n",$array);
					}
				break;
			}
		}
		die();
	}
	
	public function rsfp_onFormDelete($formId) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->delete('#__rsform_dotmailer')
			  ->where($db->qn('form_id').'='.$db->q($formId));
		$db->setQuery($query)->execute();
	}
	
	public function rsfp_onFormBackup($form, $xml, $fields) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('*')
			  ->from($db->qn('#__rsform_dotmailer'))
			  ->where($db->qn('form_id').'='.$db->q($form->FormId));
		$db->setQuery($query);
		if ($dotmailer = $db->loadObject()) {
			// No need for a form_id
			unset($dotmailer->form_id);
			
			$xml->add('dotmailer');
			foreach ($dotmailer as $property => $value) {
				$xml->add($property, $value);
			}
			$xml->add('/dotmailer');
		}
	}
	
	public function rsfp_onFormRestore($form, $xml, $fields) {
		if (isset($xml->dotmailer)) {
			$data = array(
				'form_id' => $form->FormId
			);
			
			foreach ($xml->dotmailer->children() as $property => $value) {
				$data[$property] = (string) $value;
			}
			
			$row = JTable::getInstance('RSForm_DotMailer', 'Table');
			
			if (!$row->load($form->FormId)) {
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query	->insert('#__rsform_dotmailer')
						->set(array($db->qn('form_id') .'='. $db->q($form->FormId)));
				$db->setQuery($query)->execute();
			}
			
			$row->save($data);
		}
	}
	
	public function rsfp_bk_onFormRestoreTruncate() {
		JFactory::getDbo()->truncateTable('#__rsform_dotmailer');
	}
}