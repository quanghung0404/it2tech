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
class plgSystemRSFPZohoCrm extends JPlugin
{	
	public function rsfp_onFormSave($form) {
		$post = JFactory::getApplication()->input->get('zohocrm', array(), 'array');
		$post['form_id'] = JFactory::getApplication()->input->getInt('formId',0);
		$row = JTable::getInstance('RSForm_ZohoCrm', 'Table');
		
		if (!$row) {
			return;
		}
		
		if (!$row->bind($post)) {
			JError::raiseWarning(500, $row->getError());
			return false;
		}
		
		$row->zh_merge_vars = isset($post['zh_merge_vars']) ? serialize($post['zh_merge_vars']) : '';
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT form_id FROM #__rsform_zohocrm WHERE form_id='".(int) $post['form_id']."'");
		if (!$db->loadResult()) {
			$db->setQuery("INSERT INTO #__rsform_zohocrm SET form_id='".(int) $post['form_id']."'");
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
		JFactory::getLanguage()->load('plg_system_rsfpzohocrm');
		
		$formId = JFactory::getApplication()->input->getInt('formId',0);
		$row	= JTable::getInstance('RSForm_ZohoCrm', 'Table');
		$lists	= array();
		
		if (!$row) {
			return;
		}
		
		$row->load($formId);
		$row->zh_merge_vars = @unserialize($row->zh_merge_vars);
		if ($row->zh_merge_vars === false) {
			$row->zh_merge_vars = array();
		}
		
		$zohoToken = RSFormProHelper::getConfig('zohocrm.token');
		
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/zohocrmapi.php';
		$zohocrm = new RSFPZohoCrm($zohoToken);
		
		// Fields
		$fields_array = $this->_getFields($formId);
		$fields = array();
		foreach ($fields_array as $field)
			$fields[] = JHTML::_('select.option', $field, $field);
		
		// Trigger workflow
		$lists['zh_trigger'] = RSFormProHelper::renderHTML('select.booleanlist','zohocrm[zh_wf_trigger]','class="inputbox"',$row->zh_wf_trigger);
		
		$lists['zh_duplicates'] = RSFormProHelper::renderHTML('select.booleanlist','zohocrm[zh_duplicate_check]','class="inputbox"',$row->zh_duplicate_check);
		
		// Approval
		$lists['zh_approval'] = RSFormProHelper::renderHTML('select.booleanlist','zohocrm[zh_is_approval]','class="inputbox"',$row->zh_is_approval );
		
		// Fromat type
		$format = array(
			JHTML::_('select.option', 1, JText::_('RSFP_ZOHOCRM_FORMAT_1')),
			JHTML::_('select.option', 2, JText::_('RSFP_ZOHOCRM_FORMAT_2'))
		);
		
		$lists['zh_format'] = JHTML::_('select.genericlist', $format, 'zohocrm[zh_format]', '', 'value', 'text', $row->zh_format);
		
		// Published
		$lists['zh_published'] = RSFormProHelper::renderHTML('select.booleanlist','zohocrm[zh_published]','class="inputbox"',$row->zh_published);
		
		// Debugging
		$lists['zh_debug'] = RSFormProHelper::renderHTML('select.booleanlist','zohocrm[zh_debug]','class="inputbox"',$row->zh_debug);

		// Fields
		$sections = null;
		$lists['zh_fields'] = array();
		try {
			$sections = $zohocrm->getFields();
		} catch (Exception $e) {
			JError::raiseWarning(500, 'Zoho CRM: '.$e->getMessage());
		}
		
		if (is_array($sections)) {
			foreach ($sections as $section) {
				if (is_array($section)) {
					foreach ($section as $field) {
						$lists['zh_fields'][$field->label] = JHTML::_('select.genericlist', $fields, 'zohocrm[zh_merge_vars]['.$field->label.']', null, 'value', 'text', isset($row->zh_merge_vars[$field->label]) ? $row->zh_merge_vars[$field->label] : null);
					}
				}
			}
		}
		
		echo '<div id="zohocrmdiv">';
			include JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/zohocrm.php';
		echo '</div>';
	}
	
	public function rsfp_bk_onAfterShowFormEditTabsTab() {
		JFactory::getLanguage()->load('plg_system_rsfpzohocrm');
		
		echo '<li><a href="javascript: void(0);" id="zohocrm"><span class="rsficon rsficon-stack"></span><span class="inner-text">'.JText::_('RSFP_ZOHOCRM_INTEGRATION').'</span></a></li>';
	}
	
	public function rsfp_f_onBeforeStoreSubmissions($args) {
		$db				= JFactory::getDBO();		
		$formId			= (int) $args['formId'];
		$SubmissionId	= (int) $args['SubmissionId'];
		
		$db->setQuery("SELECT * FROM #__rsform_zohocrm WHERE `form_id`='".$formId."' AND `zh_published`='1'");
		if ($row = $db->loadObject()) {
			if (!$row->zh_merge_vars) return;
			
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/zohocrmapi.php';
			$zohocrm = new RSFPZohoCrm(RSFormProHelper::getConfig('zohocrm.token'));
			$zohocrm->debug = $row->zh_debug;
			
			list($replace, $with) = RSFormProHelper::getReplacements($SubmissionId);
			
			$row->zh_merge_vars = @unserialize($row->zh_merge_vars);
			if ($row->zh_merge_vars === false)
				$row->zh_merge_vars = array();
			
			$form =& $args['post'];
			
			try {
				if (!class_exists('SimpleXMLElement')) {
					throw new Exception('SimpleXMLElement class is missing. Please contact your hosting provider and have them enable SimpleXML functions.');
				}
				
				$data = array(
					'wfTrigger' 		=> $row->zh_wf_trigger ? 'true' : 'false',
					'isApproval'		=> $row->zh_is_approval ? 'true' : 'false',
					'newFormat'			=> $row->zh_format
				);
				
				if ($row->zh_duplicate_check) {
					$data['duplicateCheck'] = 2;
				}
				
				$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Leads><row no="1"></row></Leads>');
				
				if (!empty($row->zh_merge_vars)) {
					foreach ($row->zh_merge_vars as $tag => $field) {
						if ($field == '- IGNORE -') continue;
						
						if (!isset($form[$field]))
							$form[$field] = '';
						
						if (is_array($form[$field])) {
							$form[$field] = implode(',', $form[$field]);
						}
						
						$field = $xml->row->addChild('FL', $form[$field]);
						$field->addAttribute('val', $tag);
					}
				}
				
				$data['xmlData'] = $xml->asXML();
				
				$zohocrm->addLead($data);
			} catch (Exception $e) {
				JError::raiseWarning(500, 'Zoho CRM: '.$e->getMessage());
				return false;
			}
		}
	}
	
	public function rsfp_bk_onAfterShowConfigurationTabs($tabs) {		
		JFactory::getLanguage()->load('plg_system_rsfpzohocrm');
		
		$tabs->addTitle(JText::_('Zoho CRM'), 'form-zohocrm');
		$tabs->addContent($this->zohoCrmConfigurationScreen());
	}
	
	public function zohoCrmConfigurationScreen() {
		ob_start();
		?>
		<div id="page-zohocrm" class="com-rsform-css-fix">
			<table class="admintable">
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="zohocrmtoken"><span class="hasTip" title="<?php echo JText::_('RSFP_ZOHOCRM_TOKEN_DESC'); ?>"><?php echo JText::_( 'RSFP_ZOHOCRM_TOKEN' ); ?></span></label></td>
					<td><input autocomplete="off" type="text" name="rsformConfig[zohocrm.token]" id="zohocrmtoken" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('zohocrm.token')); ?>" size="100"></td>
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
	
	public function rsfp_onFormDelete($formId) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->delete('#__rsform_zohocrm')
			  ->where($db->qn('form_id').'='.$db->q($formId));
		$db->setQuery($query)->execute();
	}
	
	public function rsfp_onFormBackup($form, $xml, $fields) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('*')
			  ->from($db->qn('#__rsform_zohocrm'))
			  ->where($db->qn('form_id').'='.$db->q($form->FormId));
		$db->setQuery($query);
		if ($zohocrm = $db->loadObject()) {
			// No need for a form_id
			unset($zohocrm->form_id);
			
			$xml->add('zohocrm');
			foreach ($zohocrm as $property => $value) {
				$xml->add($property, $value);
			}
			$xml->add('/zohocrm');
		}
	}
	
	public function rsfp_onFormRestore($form, $xml, $fields) {
		if (isset($xml->zohocrm)) {
			$data = array(
				'form_id' => $form->FormId
			);
			
			foreach ($xml->zohocrm->children() as $property => $value) {
				$data[$property] = (string) $value;
			}
			
			$row = JTable::getInstance('RSForm_ZohoCrm', 'Table');
			
			if (!$row->load($form->FormId)) {
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query	->insert('#__rsform_zohocrm')
						->set(array($db->qn('form_id') .'='. $db->q($form->FormId)));
				$db->setQuery($query)->execute();
			}
			
			$row->save($data);
		}
	}
	
	public function rsfp_bk_onFormRestoreTruncate() {
		JFactory::getDbo()->truncateTable('#__rsform_zohocrm');
	}
}