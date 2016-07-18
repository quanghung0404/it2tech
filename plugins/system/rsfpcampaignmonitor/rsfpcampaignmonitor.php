<?php
/**
 * @version       1.0
 * @package       RSform!Pro 1.51.0
 * @copyright (C) 2007-2012 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

/**
 * Class plgSystemRSFPCampaignMonitor
 */
class plgSystemRSFPCampaignMonitor extends JPlugin
{

	/**
	 * @var bool
	 */
	protected $autoloadLanguage = true;

	/**
	 * plgSystemRSFPCampaignMonitor constructor.
	 *
	 * @param object $subject
	 * @param array  $config
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		$jversion = new JVersion();
		if ($jversion->isCompatible('2.5') && !$jversion->isCompatible('3.0'))
		{
			$this->loadLanguage();
		}
	}

	/**
	 * @param $form
	 *
	 * @return bool|void
	 */
	public function rsfp_onFormSave($form)
	{
		$post            = JRequest::get('post', JREQUEST_ALLOWRAW);
		$post['form_id'] = $post['formId'];

		$row = JTable::getInstance('RSForm_CampaignMonitor', 'Table');
		if (!$row)
		{
			return;
		}
		if (!$row->bind($post))
		{
			JError::raiseWarning(500, $row->getError());

			return false;
		}

		$db = JFactory::getDBO();
		$db->setQuery("SELECT form_id FROM #__rsform_campaignmonitor WHERE form_id='" . (int) $post['form_id'] . "'");
		if (!$db->loadResult())
		{
			$db->setQuery("INSERT INTO #__rsform_campaignmonitor SET form_id='" . (int) $post['form_id'] . "'");
			$db->execute();
		}

		if (!empty($post['campaignmonitor_vars']))
		{
			$row->vars = serialize($post['campaignmonitor_vars']);

		}
		else
		{
			$row->vars = null;
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

	/**
	 * @param $tabs
	 */
	public function rsfp_bk_onAfterShowConfigurationTabs($tabs)
	{
		$tabs->addTitle(JText::_('RSFP_CAMPAIGNMONITOR_LABEL'), 'rsfp-campaignmonitor');
		$tabs->addContent($this->showConfigurationScreen());
	}

	/**
	 * @return string
	 */
	protected function showConfigurationScreen()
	{
		ob_start();

		$jversion = new JVersion();
		if ($jversion->isCompatible('3.0'))
		{
			JHtml::_('jquery.framework');
		}
		else
		{
			RSFormProAssets::addScript(JURI::root(true) . '/administrator/components/com_rsform/assets/js/jquery.js');
		}

		?>
		<div id="page-rsfpcampaignmonitor">
			<table class="admintable">
				<tr>
					<td width="200" class="key">
						<label for="campaignmonitor.api"><?php echo JText::_('RSFP_CAMPAIGNMONITOR_API'); ?></label>
					</td>
					<td>
						<input id="campaignmonitorapi" type="text" name="rsformConfig[campaignmonitor.api]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('campaignmonitor.api')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr>
					<td width="200" class="key">
						<label for="campaignmonitorclient"><?php echo JText::_('RSFP_CAMPAIGNMONITOR_CLIENT'); ?></label>
					</td>
					<td>
						<input id="campaignmonitorclient" type="text" name="rsformConfig[campaignmonitor.client]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('campaignmonitor.client')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" class="key">
						<label for="campaignmonitorssl"><?php echo JText::_('RSFP_CAMPAIGNMONITOR_USESSL'); ?></label>
					</td>
					<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'rsformConfig[campaignmonitor.usessl]', '', RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('campaignmonitor.usessl')), JText::_('JYES'), JText::_('JNO')); ?>
					</td>
				</tr>
				<tr>
					<td width="200" colspan="2" class="key">
						<a href="http://help.campaignmonitor.com/topic.aspx?t=206" class="btn btn-primary" target="_blank"><?php echo JText::_('RSFP_CAMPAIGNMONITOR_GETKEY'); ?></a>
					</td>
				</tr>
			</table>
		</div>

		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * @throws Exception
	 */
	public function rsfp_bk_onSwitchTasks()
	{
		$app = JFactory::getApplication();

		if ($app->input->getCmd('plugin_task') == 'defaultListCM')
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsfpcampaignmonitor/csrest_lists.php';
			$campaignmonitor_api    = RSFormProHelper::getConfig('campaignmonitor.api');
			$campaignmonitor_client = RSFormProHelper::getConfig('campaignmonitor.client');

			$auth = array(
				'api_key' => $campaignmonitor_api,
			);

			$rsfp_list = array(
				'Title'              => 'RSForm!Pro Default List',
				'UnsubscribeSetting' => 'OnlyThisList',
			);

			$lists  = new CS_REST_Lists('', $auth);
			$create = $lists->create($campaignmonitor_client, $rsfp_list);

			if ($create->was_successful())
			{
				$url = JURI::root() . '/administrator/index.php?option=com_rsform&task=forms.edit&formId=' . $app->input->get('formId', '') . '&tabposition='. $app->input->get('tabposition', '') .'&tab='.  $app->input->get('tab', '');

				$app->redirect($url, JText::_('RSFP_DEFAULTLISTCREATED'), 'Notice');

			} else {
				throw new Exception(JText::sprintf('RSFP_CAMPAIGNMONITOR_ERROR', $create->response->Code, $create->response->Message));
			}
		}

	}

	/**
	 *
	 */
	public function rsfp_bk_onAfterShowFormEditTabsTab()
	{
		echo '<li><a href="javascript: void(0);" id="rsfpcampaignmonitor"><span class="rsficon rsficon-envelope-square"></span><span class="inner-text">' . JText::_('RSFP_CAMPAIGNMONITOR_LABEL') . '</span></a></li>';
	}

	/**
	 * @throws Exception
	 */
	public function rsfp_bk_onAfterShowFormEditTabs()
	{
		$formId = JFactory::getApplication()->input->getInt('formId', 0);
		$row    = JTable::getInstance('RSForm_CampaignMonitor', 'Table');

		$campaignmonitor_api    = RSFormProHelper::getConfig('campaignmonitor.api');
		$campaignmonitor_client = RSFormProHelper::getConfig('campaignmonitor.client');

		$app = JFactory::getApplication();

		if ($campaignmonitor_api == '' || $campaignmonitor_client == '')
		{
			?>
			<div id="rsfpverticalresponsediv">
				<table class="admintable">
					<tr>
						<td valign="top" align="left" width="30%">
							<table class="table table-bordered">
								<div class="alert alert-warning"><?php echo JText::_('RSFP_CAMPAIGNMONITOR_NOTOKEN') ?></div>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<?php
			return;
		}

		if (!$row)
		{
			return;
		}

		$row->load($formId);

		if ($row->vars)
		{
			$row->vars = unserialize($row->vars);
		}

		$fields_array = $this->_getFields($formId);
		$fields       = array(
			JHtml::_('select.option', '', JText::_('JSELECT')),
		);
		foreach ($fields_array as $field)
		{
			$fields[] = JHtml::_('select.option', '{' . $field . ':value}', $field);
		}

		// Merge Vars
		$merge_vars = $this->standardFields();

		try
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsfpcampaignmonitor/csrest_clients.php';
			require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsfpcampaignmonitor/csrest_lists.php';

			$auth = array(
				'api_key' => $campaignmonitor_api,
			);

			if (!empty($row->campaignmonitor_list))
			{
				$cfields = new CS_REST_Lists($row->campaignmonitor_list, $auth);
				$cfields = $cfields->get_custom_fields();

				if (!$cfields->was_successful())
				{
					throw new Exception(JText::sprintf('RSFP_CAMPAIGNMONITOR_ERROR', $cfields->response->Code, $cfields->response->Message));
				}

				$cfields = $cfields->response;

				if (!empty($cfields))
				{
					foreach ($cfields as $custom_field)
					{
						$merge_vars[$custom_field->FieldName] = $custom_field->FieldName;
					}
				}

			}

			//Load the view
			include_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/campaignmonitor.php';

		} catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'warning');
		}

	}

	/**
	 * @param $args
	 *
	 * @throws Exception
	 */
	public function rsfp_f_onAfterFormProcess($args)
	{

		$db                     = JFactory::getDBO();
		$formId                 = (int) $args['formId'];
		$SubmissionId           = (int) $args['SubmissionId'];
		$campaignmonitor_api    = RSFormProHelper::getConfig('campaignmonitor.api');
		$campaignmonitor_client = RSFormProHelper::getConfig('campaignmonitor.client');
		$app                    = JFactory::getApplication();

		$db->setQuery("SELECT * FROM #__rsform_campaignmonitor WHERE `form_id`='" . $formId . "' AND `enable_campaignmonitor`='1'");
		if ($row = $db->loadObject())
		{
			try
			{
				require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsfpcampaignmonitor/csrest_subscribers.php';
				require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsfpcampaignmonitor/csrest_lists.php';

				$auth = array(
					'api_key' => $campaignmonitor_api,
				);

				$response = new CS_REST_Subscribers($row->campaignmonitor_list, $auth);

				// Get replacements
				list($args['placeholders'], $args['values']) = RSFormProHelper::getReplacements($SubmissionId);

				// Grab configured fields
				$fields = array();
				if ($row->vars)
				{
					$fields = unserialize($row->vars);
				}

				if (!is_array($fields))
				{
					$fields = array();
				}

				$element = array();
				foreach ($fields as $field => $value)
				{
					// Custom fields should be in a separate array
					if ($field == 'Name' || $field == 'EmailAddress')
					{
						$element[$field] = str_replace($args['placeholders'], $args['values'], $value);
					}
					else
					{
						if ($value !== '')
						{
							$element['CustomFields'][] = array(
								'Key'   => $field,
								'Value' => str_replace($args['placeholders'], $args['values'], $value)
							);
						}
					}
				}

				$search = $response->get($element['EmailAddress']);

				if ($search->was_successful() && $row->campaignmonitor_update)
				{
					$contact = $response->update($element['EmailAddress'], $element);
				}
				elseif ($search->was_successful() && !$row->campaignmonitor_update)
				{
					$getListName = new CS_REST_Lists($row->campaignmonitor_list, $auth);
					$getListName = $getListName->get();

					throw new Exception(JText::sprintf('RSFP_CAMPAIGNMONITOR_ALREADY_SUBSCRIBED', $element['EmailAddress'], $getListName->response->Title));
				}
				else
				{
					$contact = $response->add($element);
				}

				if (!$contact->was_successful())
				{
					throw new Exception(JText::sprintf('RSFP_CAMPAIGNMONITOR_ERROR', $contact->response->Code, $contact->response->Message));
				}
			} catch (Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}

		}

	}

	/**
	 * @param $formId
	 *
	 * @return mixed
	 */
	protected function _getFields($formId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('p.PropertyValue'))
			->from($db->qn('#__rsform_components', 'c'))
			->join('LEFT', $db->qn('#__rsform_properties', 'p') . ' ON (' . $db->qn('c.ComponentId') . '=' . $db->qn('p.ComponentId') . ')')
			->where($db->qn('c.FormId') . '=' . $db->q($formId))
			->where($db->qn('p.PropertyName') . '=' . $db->q('NAME'))
			->order($db->qn('c.Order') . ' ' . $db->escape('ASC'));

		return $db->setQuery($query)->loadColumn();
	}

	/**
	 * @return array
	 */
	public function standardFields()
	{
		return array(
			'Name'         => JText::_('RSFP_CAMPAIGNMONITOR_NAME'),
			'EmailAddress' => JText::_('RSFP_CAMPAIGNMONITOR_EMAIL'),
		);
	}

	/**
	 * @param $formId
	 */
	public function rsfp_onFormDelete($formId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__rsform_campaignmonitor')
			->where($db->qn('form_id') . '=' . $db->q($formId));
		$db->setQuery($query)->execute();
	}

	/**
	 * @param $form
	 * @param $xml
	 * @param $fields
	 */
	public function rsfp_onFormBackup($form, $xml, $fields)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->qn('#__rsform_campaignmonitor'))
			->where($db->qn('form_id') . '=' . $db->q($form->FormId));
		$db->setQuery($query);
		if ($result = $db->loadObject())
		{
			// No need for a form_id
			unset($result->form_id);

			$xml->add('campaignmonitor');
			foreach ($result as $property => $value)
			{
				$xml->add($property, $value);
			}
			$xml->add('/campaignmonitor');
		}
	}

	/**
	 * @param $form
	 * @param $xml
	 * @param $fields
	 */
	public function rsfp_onFormRestore($form, $xml, $fields)
	{
		if (isset($xml->campaignmonitor))
		{
			$data = array(
				'form_id' => $form->FormId
			);
			foreach ($xml->campaignmonitor->children() as $property => $value)
			{
				$data[$property] = (string) $value;
			}
			$row = JTable::getInstance('RSForm_CampaignMonitor', 'Table');

			if (!$row->load($form->FormId))
			{
				$db    = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->insert('#__rsform_campaignmonitor')
					->set(array(
						$db->qn('form_id') . '=' . $db->q($form->FormId),
					));
				$db->setQuery($query)->execute();
			}

			$row->save($data);
		}
	}

	/**
	 *
	 */
	public function rsfp_bk_onFormRestoreTruncate()
	{
		JFactory::getDbo()->truncateTable('#__rsform_campaignmonitor');
	}
}