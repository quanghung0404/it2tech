<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Check if the helper exists
$helper = JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php';
if (!file_exists($helper)) {
	return;
}

// Load Helper functions
require_once $helper;
require_once dirname(__FILE__).'/helper.php';

// Objects
$user = JFactory::getUser();
$db	  = JFactory::getDbo();

// Params
$formId			 = (int) $params->def('formId', 1);
$moduleclass_sfx = $params->def('moduleclass_sfx', '');
$userId 		 = $params->def('userId', 0);

// Template params
$template_module      = $params->def('template_module', '');
$template_formdatarow = $params->def('template_formdatarow', '');
$template_formdetail  = $params->def('template_formdetail', '');

$app 				= JFactory::getApplication();
$detail 			= $app->input->getInt('detail'.$formId);
$helper 			= new ModRSFormListHelper($params);

if (!$detail)
{
	$submissions = $helper->getSubmissions();
	$pagination  = $helper->getPagination();
	$headers	 = $helper->getHeaders();
	$form		 = $helper->getForm();
	
	$formdata = '';
	$i  	  = 0;
	
	foreach ($submissions as $SubmissionId => $submission)
	{
		$url = $helper->getUrl($SubmissionId);
		list($replace, $with) = $helper->getReplacements($submission['UserId']);
		$replace = array_merge($replace, array('{global:userip}', '{global:date_added}', '{global:submissionid}', '{global:submission_id}', '{global:counter}', '{global:naturalcounter}', '{details}', '{details_link}', '{global:confirmed}'));
		$with 	 = array_merge($with, array($submission['UserIp'], $submission['DateSubmitted'], $SubmissionId, $SubmissionId, $pagination->getRowOffset($i), $params->get('sort_submissions') ? $pagination->getRowOffset($i) : ($pagination->total + 1 - $pagination->getRowOffset($i)), '<a href="'.$url.'">', $url, $submission['confirmed']));
		
		foreach ($headers as $header)
		{
			if (!isset($submission['SubmissionValues'][$header]['Value']))
				$submission['SubmissionValues'][$header]['Value'] = '';
				
			$replace[] = '{'.$header.':value}';
			$with[] = $submission['SubmissionValues'][$header]['Value'];
			
			if (!empty($submission['SubmissionValues'][$header]['Path']))
			{
				$replace[] = '{'.$header.':path}';
				$with[] = $submission['SubmissionValues'][$header]['Path'];
			}
		}
		
		$replace[] 	= '{_STATUS:value}';
		$with[] 	= isset($submission['SubmissionValues']['_STATUS']) ? JText::_('RSFP_PAYPAL_STATUS_'.$submission['SubmissionValues']['_STATUS']['Value']) : '';
		
		$row = $template_formdatarow;
		
		// RSForm! Pro Scripting - Form Data Row
		// performance check
		if (strpos($row, '{/if}') !== false) {
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
			RSFormProScripting::compile($row, $replace, $with);
		}
		
		$formdata .= str_replace($replace, $with, $row);
		
		$i++;
	}

	$html = str_replace('{formdata}', $formdata, $template_module);
	if ($params->get('show_pagination', 1)) {
		if ($params->get('show_pagination_counter', 1)) {
			$html .= '<div>'.$pagination->getResultsCounter().'</div>';
		}
		$html .= '<div class="pagination">'.$pagination->getPagesLinks().'</div>';
	}
} else {
	if ($userId != 'login' && $userId != 0)
	{
		$userId = explode(',', $userId);
		JArrayHelper::toInteger($userId);
	}
	
	$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__rsform_submissions'))
				->where($db->qn('SubmissionId').' = '.$db->q($detail));
	if ($submission = $db->setQuery($query)->loadObject()) {
		if ($submission->FormId != $formId) {
			$app->enqueueMessage(JText::sprintf('MOD_RSFORMLIST_SUBMISSION_DOES_NOT_BELONG_TO_FORM', $detail, $formId), 'warning');
			return;
		}
		
		if ($userId == 'login' && $submission->UserId != $user->get('id')) {
			$app->enqueueMessage(JText::sprintf('MOD_RSFORMLIST_SUBMISSION_DOES_NOT_BELONG_TO_LOGGED_IN_USER', $detail), 'warning');
			return;
		}
		
		if ($params->get('show_confirmed', 0) && !$submission->confirmed)
		{
			$app->enqueueMessage(JText::sprintf('MOD_RSFORMLIST_SUBMISSION_IS_NOT_CONFIRMED', $detail), 'warning');
			return;
		}
	} else {
		$app->enqueueMessage(JText::sprintf('MOD_RSFORMLIST_SUBMISSION_DOESNT_EXIST', $detail), 'warning');
		return;
	}
	
	$confirmed 				= $submission->confirmed ? JText::_('JYES') : JText::_('JNO');
	list($replace, $with) 	= RSFormProHelper::getReplacements($detail, true);
	list($replace2, $with2) = $helper->getReplacements($submission->UserId);
	
	$replace = array_merge($replace, $replace2, array('{global:submissionid}', '{global:submission_id}', '{global:date_added}', '{global:confirmed}'));
	$with 	 = array_merge($with, $with2, array($detail, $detail, $helper->getDate($submission->DateSubmitted), $confirmed));
	
	// RSForm! Pro Scripting - Form Detail
	// performance check
	if (strpos($template_formdetail, '{/if}') !== false) {
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
		RSFormProScripting::compile($template_formdetail, $replace, $with);
	}
	
	$html = str_replace($replace, $with, $template_formdetail);
}

// Display template
require JModuleHelper::getLayoutPath('mod_rsform_list');