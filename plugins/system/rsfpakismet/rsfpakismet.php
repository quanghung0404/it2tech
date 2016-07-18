<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemRSFPAkismet extends JPlugin
{
	public function rsfp_onFormSave($form)
	{
		$post = JRequest::get('post', JREQUEST_ALLOWRAW);
		$post['form_id'] = $post['formId'];
		
		$row = JTable::getInstance('RSForm_Akismet', 'Table');
		if (!$row)
			return;
		if (!$row->bind($post))
		{
			JError::raiseWarning(500, $row->getError());
			return false;
		}
		
		$row->aki_merge_vars = serialize($post['aki_merge_vars']);
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT form_id FROM #__rsform_akismet WHERE form_id='".(int) $post['form_id']."'");
		if (!$db->loadResult())
		{
			$db->setQuery("INSERT INTO #__rsform_akismet SET form_id='".(int) $post['form_id']."'");
			$db->execute();
		}
		
		if ($row->store())
		{
			return true;
		}
		else
		{
			JError::raiseWarning(500, $row->getError());
			return false;
		}
	}
	
	public function rsfp_bk_onAfterShowFormEditTabs()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfpakismet');
		
		$row = JTable::getInstance('RSForm_Akismet', 'Table');
		if (!$row) return;
		$row->load($formId);
		$row->aki_merge_vars = @unserialize($row->aki_merge_vars);
		if ($row->aki_merge_vars === false)
			$row->aki_merge_vars = array();
		
		// Fields
		$fields_array = $this->_getFields($formId);
		$fields = array();
		foreach ($fields_array as $field)
			$fields[] = JHTML::_('select.option', $field, $field);
		
		// Merge Vars
		$merge_vars = array("author" => JText::_('RSFP_AKI_AUTHOR'),"email" => JText::_('RSFP_AKI_EMAIL'),"body" => JText::_('RSFP_AKI_BODY'));
		
		$lists['fields'] = array();
		if (is_array($merge_vars))
			foreach ($merge_vars as $merge_var => $title)
			{
				$lists['fields'][$merge_var] = JHTML::_('select.genericlist', $fields, 'aki_merge_vars['.$merge_var.']', null, 'value', 'text', isset($row->aki_merge_vars[$merge_var]) ? $row->aki_merge_vars[$merge_var] : null);
			}
		
		$lists['published'] = RSFormProHelper::renderHTML('select.booleanlist','aki_published','class="inputbox"',$row->aki_published);
		
		echo '<div id="akismetdiv">';
		include JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/akismet.php';
		echo '</div>';
	}
	
	public function rsfp_bk_onAfterShowFormEditTabsTab()
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfpakismet');
		
		echo '<li><a href="javascript: void(0);" id="akismet"><span class="rsficon rsficon-text-color"></span><span class="inner-text">'.JText::_('RSFP_AKI_INTEGRATION').'</span></a></li>';
	}
	
	public function rsfp_f_onBeforeFormValidation($args)
	{
		$db = JFactory::getDBO();
		$post = JRequest::getVar('form', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		$formId = (int) $post['formId'];		
		
		$db->setQuery("SELECT * FROM #__rsform_akismet WHERE `form_id`='".$formId."' AND `aki_published`='1'");
		if ($row = $db->loadObject())
		{
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/akismet.class.php';
			
			$row->aki_merge_vars = @unserialize($row->aki_merge_vars);
			if ($row->aki_merge_vars === false)
				$row->aki_merge_vars = array();
			
			$apikey = RSFormProHelper::getConfig('aki.key');
			$vars = array();
			$vars['website'] = JURI::root();
			$vars['permalink'] = JURI::root();
			foreach ($row->aki_merge_vars as $tag => $field)
			{
				if (empty($tag)) continue;
				
				if (!isset($post[$field]))
					$post[$field] = '';
				
				if (is_array($post[$field]))
				{
					array_walk($post[$field], array($this, '_escapeCommas'));
					$post[$field] = implode(',', $post[$field]);
				}
				$vars[$tag] = $post[$field];
			}
			
			$app = JFactory::getApplication();
			$akismet = new RSFPAkismet(JURI::root(), $apikey, $vars);
			if ($msg = $akismet->getError(AKISMET_SERVER_NOT_FOUND)) {
				$app->enqueueMessage($msg, 'error');
			} elseif ($msg = $akismet->getError(AKISMET_RESPONSE_FAILED)) {
				$app->enqueueMessage($msg, 'error');
			} elseif ($msg = $akismet->getError(AKISMET_INVALID_KEY)) {
				$app->enqueueMessage($msg, 'error');
			} elseif($akismet->isSpam()) {
				$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE FormId = ".$formId." AND Published = 1");
				$components = $db->loadColumn();
				
				if (!empty($components))
				{
					$args['invalid'] = array_merge($args['invalid'],$components);
					$args['invalid'] = array_unique($args['invalid']);
				}
			}
		}
	}
	
	public function rsfp_bk_onAfterShowConfigurationTabs($tabs)
	{		
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfpakismet');
		
		$tabs->addTitle(JText::_('RSFP_AKI_NAME'), 'form-akismet');
		$tabs->addContent($this->akismetConfigurationScreen());
	}
	
	public function rsfp_onFormDelete($formId) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->delete('#__rsform_akismet')
			  ->where($db->qn('form_id').'='.$db->q($formId));
		$db->setQuery($query)->execute();
	}
	
	public function rsfp_onFormBackup($form, $xml, $fields) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('*')
			  ->from($db->qn('#__rsform_akismet'))
			  ->where($db->qn('form_id').'='.$db->q($form->FormId));
		$db->setQuery($query);
		if ($akismet = $db->loadObject()) {
			// No need for a form_id
			unset($akismet->form_id);
			
			$xml->add('akismet');
			foreach ($akismet as $property => $value) {
				$xml->add($property, $value);
			}
			$xml->add('/akismet');
		}
	}
	
	public function rsfp_onFormRestore($form, $xml, $fields) {
		if (isset($xml->akismet)) {
			$data = array(
				'form_id' => $form->FormId
			);
			foreach ($xml->akismet->children() as $property => $value) {
				$data[$property] = (string) $value;
			}
			$row = JTable::getInstance('RSForm_Akismet', 'Table');
			
			if (!$row->load($form->FormId)) {
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query	->insert('#__rsform_akismet')
						->set(array(
								$db->qn('form_id') .'='. $db->q($form->FormId),
						));
				$db->setQuery($query)->execute();
			}
			
			$row->save($data);
		}
	}
	
	public function rsfp_bk_onFormRestoreTruncate() {
		JFactory::getDbo()->truncateTable('#__rsform_akismet');
	}
	
	protected function akismetConfigurationScreen()
	{
		ob_start();
		?>
		<div id="page-recaptcha">
			<table class="admintable">
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="akikey"><span class="hasTip" title="<?php echo JText::_('RSFP_AKI_API_KEY_DESC'); ?>"><?php echo JText::_( 'RSFP_AKI_API_KEY' ); ?></span></label></td>
					<td><input type="text" name="rsformConfig[aki.key]" id="akikey" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('aki.key')); ?>" size="100" maxlength="100"></td>
				</tr>
			</table>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	protected function _getFields($formId)
	{
		$db = JFactory::getDBO();
		
		$db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".(int) $formId."' AND p.PropertyName='NAME' ORDER BY c.Order");
		return $db->loadColumn();
	}
	
	protected function _escapeCommas(&$item) {
		$item = str_replace(',', '\,', $item);
	}
}