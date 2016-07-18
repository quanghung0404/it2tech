<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class ModRSFormListHelper
{
	protected $formId = 1;
	protected $params;
	protected $replacements;
	protected $textareaFields = array();
	
	protected $_form;
	protected $_data = array();
	protected $_total = 0;
	protected $_query = '';
	protected $_pagination;
	protected $_db;
	protected $_state;
	
	public function __construct($params)
	{
		$this->params = $params;
		$this->formId = (int) $this->params->def('formId', 1);
		$this->_state = new JObject();
		
		$this->_db 		= JFactory::getDBO();
		$this->_query 	= $this->_buildQuery();
		
		// Get pagination request variables
		$limit 		= $this->params->def('limit', 30);
		$limitstart	= JFactory::getApplication()->input->getInt('limitstart', 0);
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('mod_rsform_list.submissions.'.$this->formId.'.limit', $limit);
		$this->setState('mod_rsform_list.submissions.'.$this->formId.'.limitstart', $limitstart);
	}
	
	public function getUrl($submissionId) {
		static $type = array(); 
		static $itemId;
		
		if (!isset($type[$this->formId])) {
			$type[$this->formId]	= 'module';
			$itemId					= (int) $this->params->get('menu_type_itemid');
			
			// Do we have menu item ID set?
			if ($itemId) {
				// Let's check the menu item type
				if (($item = JFactory::getApplication()->getMenu()->getItem($itemId)) // Menu item exists
					&& (isset($item->query) && is_array($item->query)) // Has query element and it's an array
					&& (isset($item->query['option']) && ($item->query['option'] == 'com_rsform')) // Is an RSForm! Pro menu item
					&& (isset($item->query['view']) && ($item->query['view'] == 'submissions' || $item->query['view'] == 'directory')) // Points to Submissions or Directory.
					) {
						// Everything looks good here, grab the menu type
						$type[$this->formId] = $item->query['view'];
				}
			}
		}
		
		switch ($type[$this->formId])
		{
			case 'submissions':
				return JRoute::_("index.php?option=com_rsform&view=submissions&layout=view&cid=$submissionId&Itemid=$itemId");
			break;
			
			case 'directory':
				return JRoute::_("index.php?option=com_rsform&view=directory&layout=view&id=$submissionId&Itemid=$itemId");
			break;
			
			case 'module':
			default:
				// Build base URL.
				static $baseUrl;
				if (!$baseUrl) {
					$uri = JFactory::getUri();
					$uri->delVar('detail'.$this->formId);
					$baseUrl  = (string) $uri;
					$baseUrl .= strpos($baseUrl, '?') !== false ? '&' : '?';
				}
				
				return JRoute::_("{$baseUrl}detail{$this->formId}=$submissionId");
			break;
		}
	}
	
	public function getDate($date) {
		if (method_exists('RSFormProHelper', 'getDate')) {
			return RSFormProHelper::getDate($date);
		} else {
			return JHtml::_('date', $date, 'Y-m-d H:i:s');
		}
	}
	
	public function getForm()
	{
		if (empty($this->_form))
		{
			$this->_db->setQuery("SELECT * FROM #__rsform_forms WHERE FormId='".$this->formId."'");
			$this->_form = $this->_db->loadObject();
			
			$this->_form->MultipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $this->_form->MultipleSeparator);
		}
		
		return $this->_form;
	}
	
	protected function _buildQuery()
	{
		$query  = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(sv.SubmissionId), s.* FROM #__rsform_submissions s";
		$query .= " LEFT JOIN #__rsform_submission_values sv ON (s.SubmissionId=sv.SubmissionId)";
		$query .= " WHERE s.FormId='".$this->formId."'";
		
		$confirmed = $this->params->get('show_confirmed', 0);
		if ($confirmed)
			$query .= " AND s.confirmed='1'";
		
		$lang = $this->params->get('lang', '');
		if ($lang)
			$query .= " AND s.Lang='".$this->_db->escape($lang)."'";
		
		$userId = $this->params->def('userId', 0);
		if ($userId == 'login')
		{
			$user = JFactory::getUser();
			if ($user->get('guest'))
				$query .= " AND 1>2";
			
			$query .= " AND s.UserId='".(int) $user->get('id')."'";
		}
		elseif ($userId == 0)
		{
			// Show all submissions
		}
		else
		{
			$userId = explode(',', $userId);
			JArrayHelper::toInteger($userId);
			
			$query .= " AND s.UserId IN (".implode(',', $userId).")";
		}
		
		$dir = $this->params->get('sort_submissions') ? 'ASC' : 'DESC';
		
		$query .= " ORDER BY s.DateSubmitted $dir";
		
		return $query;
	}
	
	public function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('mod_rsform_list.submissions.'.$this->formId.'.limitstart'), $this->getState('mod_rsform_list.submissions.'.$this->formId.'.limit'));
		}
		
		return $this->_pagination;
	}
	
	public function getTotal()
	{
		return $this->_total;
	}
	
	public function getSubmissions()
	{
		if (empty($this->_data))
		{
			$this->getComponents();

			$this->_db->setQuery("SET SQL_BIG_SELECTS=1");
			$this->_db->execute();
			
			$submissionIds = array();
			
			$this->_db->setQuery($this->_query, $this->getState('mod_rsform_list.submissions.'.$this->formId.'.limitstart'), $this->getState('mod_rsform_list.submissions.'.$this->formId.'.limit'));
			$results = $this->_db->loadObjectList();
			$this->_db->setQuery("SELECT FOUND_ROWS()");
			$this->_total = $this->_db->loadResult();
			foreach ($results as $result)
			{
				$submissionIds[] = $result->SubmissionId;
				
				$this->_data[$result->SubmissionId]['FormId'] = $result->FormId;
				$this->_data[$result->SubmissionId]['DateSubmitted'] = $this->getDate($result->DateSubmitted);
				
				$this->_data[$result->SubmissionId]['UserIp'] = $result->UserIp;
				$this->_data[$result->SubmissionId]['Username'] = $result->Username;
				$this->_data[$result->SubmissionId]['UserId'] = $result->UserId;
				$this->_data[$result->SubmissionId]['confirmed'] = $result->confirmed ? JText::_('RSFP_YES') : JText::_('RSFP_NO');
				$this->_data[$result->SubmissionId]['SubmissionValues'] = array();
			}
			
			$form = $this->getForm();
			
			if (!empty($submissionIds))
			{
				$this->_db->setQuery("SELECT * FROM `#__rsform_submission_values` WHERE `SubmissionId` IN (".implode(',',$submissionIds).")");
				$results = $this->_db->loadObjectList();
				
				$config = JFactory::getConfig();
				$secret = $config->get('secret');
				foreach ($results as $result)
				{
					// Check if this is an upload field
					if (in_array($result->FieldName, $this->uploadFields) && !empty($result->FieldValue))
					{
						$result->FilePath = $result->FieldValue;
						$result->FieldValue = '<a href="'.JURI::root().'index.php?option=com_rsform&amp;task=submissions.view.file&amp;hash='.md5($result->SubmissionId.$secret.$result->FieldName).'">'.basename($result->FieldValue).'</a>';
					}
					// Check if this is a multiple field
					elseif (in_array($result->FieldName, $this->multipleFields))
						$result->FieldValue = str_replace("\n", $form->MultipleSeparator, $result->FieldValue);
					elseif ($form->TextareaNewLines && in_array($result->FieldName, $this->textareaFields))
						$result->FieldValue = nl2br($result->FieldValue);
						
					$this->_data[$result->SubmissionId]['SubmissionValues'][$result->FieldName] = array('Value' => $result->FieldValue, 'Id' => $result->SubmissionValueId);
					if (in_array($result->FieldName, $this->uploadFields) && !empty($result->FieldValue))
					{
						$filepath = $result->FilePath;
						$filepath = str_replace(JPATH_SITE.DIRECTORY_SEPARATOR, JURI::root(), $filepath);
						$filepath = str_replace(array('\\', '\\/', '//\\'), '/', $filepath);
						
						$this->_data[$result->SubmissionId]['SubmissionValues'][$result->FieldName]['Path'] = $filepath;
					}
				}
			}
			unset($results);
		}
		
		return $this->_data;
	}
	
	public function getReplacements($user_id=0)
	{
		static $sitename, $siteurl, $mailfrom, $fromname;
		
		if (is_null($siteurl)) {
			$config 	= JFactory::getConfig();
			$sitename 	= $config->get('sitename');
			$siteurl	= JURI::root();
			$mailfrom	= $config->get('mailfrom');
			$fromname	= $config->get('fromname');
		}
		
		$user    = JFactory::getUser((int) $user_id);
		$replace = array('{global:sitename}', '{global:siteurl}', '{global:userid}', '{global:username}', '{global:email}', '{global:mailfrom}', '{global:fromname}', '{/details}', '{/detailspdf}');
		$with 	 = array($sitename, JURI::root(), $user->get('id'), $user->get('username'), $user->get('email'), $mailfrom, $fromname, '</a>', '</a>');
			
		$this->replacements = array($replace, $with);
		
		return $this->replacements;
	}
	
	protected function getComponents()
	{
		$this->_db->setQuery("SELECT c.ComponentTypeId, p.ComponentId, p.PropertyName, p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".$this->formId."' AND c.Published='1' AND p.PropertyName IN ('NAME', 'WYSIWYG')");
		$components = $this->_db->loadObjectList();
		$this->uploadFields   = array();
		$this->multipleFields = array();
		$this->textareaFields = array();
		
		foreach ($components as $component)
		{
			// Upload fields
			if ($component->ComponentTypeId == 9)
			{
				$this->uploadFields[] = $component->PropertyValue;
			}
			// Multiple fields
			elseif (in_array($component->ComponentTypeId, array(3, 4)))
			{
				$this->multipleFields[] = $component->PropertyValue;
			}
			// Textarea fields
			elseif ($component->ComponentTypeId == 2)
			{
				if ($component->PropertyName == 'WYSIWYG' && $component->PropertyValue == 'NO')
					$this->textareaFields[] = $component->ComponentId;
			}
		}
		
		if (!empty($this->textareaFields))
		{
			$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.ComponentId IN (".implode(',', $this->textareaFields).")");
			$this->textareaFields = $this->_db->loadColumn();
		}
	}
	
	public function getHeaders()
	{
		$query  = "SELECT p.PropertyValue FROM #__rsform_components c";
		$query .= " LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId AND p.PropertyName='NAME')";
		$query .= " LEFT JOIN #__rsform_component_types ct ON (c.ComponentTypeId=ct.ComponentTypeId)";
		$query .= " WHERE c.FormId='".$this->formId."' AND c.Published='1'";
		
		$this->_db->setQuery($query);
		$headers = $this->_db->loadColumn();
		
		return $headers;
	}
	
	protected function getState($property) {
		return $this->_state->get($property);
	}
	
	protected function setState($property, $value) {
		return $this->_state->set($property, $value);
	}
}