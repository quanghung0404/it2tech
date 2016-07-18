<?php
/**
 * @version       1.0
 * @package       RSform!Pro 1.51.0
 * @copyright (C) 2007-2012 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

/**
 * Class plgSystemRSFPGetResponse
 */
class plgSystemRSFPGetResponse extends JPlugin
{

	/**
	 * @var bool
	 */
	protected $autoloadLanguage = true;

	/**
	 * plgSystemRSFPGetResponse constructor.
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

		$row = JTable::getInstance('RSForm_GetResponse', 'Table');
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
		$db->setQuery("SELECT form_id FROM #__rsform_getresponse WHERE form_id='" . (int) $post['form_id'] . "'");
		if (!$db->loadResult())
		{
			$db->setQuery("INSERT INTO #__rsform_getresponse SET form_id='" . (int) $post['form_id'] . "'");
			$db->execute();
		}

		if (!empty($post['getresponse_vars']))
		{
			$row->vars = serialize($post['getresponse_vars']);
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
		$tabs->addTitle(JText::_('RSFP_GETRESPONSE'), 'rsfp-getresponse');
		$tabs->addContent($this->showConfigurationScreen());
	}

	/**
	 * @return string
	 */
	protected function showConfigurationScreen()
	{
		ob_start();

		?>
		<div id="page-rsfpgetresponse">
			<table class="admintable">
				<tr>
					<td width="200" style="width: 200px;" class="key">
						<label for="getresponsekey"><?php echo JText::_('RSFP_GETRESPONSE_KEY'); ?></label></td>
					<td>
						<input id="getresponsekey" type="text" name="rsformConfig[getresponse.key]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('getresponse.key')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" class="key">
						<label for="getresponsessl"><?php echo JText::_('RSFP_GETRESPONSE_USESSL'); ?></label>
					</td>
					<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'rsformConfig[getresponse.usessl]', '', RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('getresponse.usessl')), JText::_('JYES'), JText::_('JNO')); ?>
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
	 *
	 */
	public function rsfp_bk_onAfterShowFormEditTabsTab()
	{
		echo '<li><a href="javascript: void(0);" id="rsfpgetresponse"><span class="rsficon rsficon-envelope-square"></span><span class="inner-text">' . JText::_('RSFP_GETRESPONSE') . '</span></a></li>';
	}

	/**
	 * @throws Exception
	 */
	public function rsfp_bk_onAfterShowFormEditTabs()
	{
		$formId = JFactory::getApplication()->input->getInt('formId', 0);
		$row    = JTable::getInstance('RSForm_GetResponse', 'Table');
		$app    = JFactory::getApplication();

		if (!$row)
		{
			return;
		}

		$row->load($formId);

		if (RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('getresponse.key')) == '')
		{
			?>
			<div id="rsfpgetresponsediv">
				<table class="admintable">
					<tr>
						<td valign="top" align="left" width="30%">
							<table class="table table-bordered">
								<div class="alert alert-warning"><?php echo JText::_('RSFP_GETRESPONSE_NOTOKEN') ?></div>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<?php
			return;
		}

		if ($row->vars)
		{
			$row->vars = unserialize($row->vars);
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/getresponse/GetResponseAPI3.class.php';

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
			$response = new GetResponse(RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('getresponse.key')));

			$ping = $response->ping();

			if (isset($ping->code))
			{
				?>

				<div id="rsfpgetresponsediv">
					<legend><?php echo JText::_('RSFP_GETRESPONSE') ?></legend>
					<table class="admintable">
						<tr>
							<td colspan="2"><div class="alert alert-error" style="width: 620px;"><?php echo JText::sprintf('RSFP_GETRESPONSE_ERROR', $ping->code, $ping->message); ?></div></td>
						</tr>
					</table>
				</div>
				<?php

				return false;
			}

			$custom_fields = $response->getCustomFields();
			$custom_values = array();

			if (!empty($custom_fields))
			{
				foreach ($custom_fields as $custom_field)
				{
					$merge_vars[$custom_field->name] = $custom_field->name;
				}

				foreach ($custom_fields as $custom_field)
				{
					$custom_values[$custom_field->name] = $custom_field->values;
				}
			}

			$custom_values = array_filter($custom_values);

			// Load the view
			include_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/getresponse.php';

		} catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

	}

	/**
	 * @param $args
	 *
	 * @throws Exception
	 */
	public function rsfp_f_onAfterFormProcess($args)
	{
		$db           = JFactory::getDBO();
		$formId       = (int) $args['formId'];
		$SubmissionId = (int) $args['SubmissionId'];
		$app          = JFactory::getApplication();

		$db->setQuery("SELECT * FROM #__rsform_getresponse WHERE `form_id`='" . $formId . "' AND `enable_getresponse`='1'");
		if ($row = $db->loadObject())
		{
			try
			{
				require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/getresponse/GetResponseAPI3.class.php';
				$response = new GetResponse(RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('getresponse.key')));

				$custom_fields = array();

				foreach ($response->getCustomFields() as $field)
				{
					$custom_fields[$field->name] = $field->customFieldId;
				}

				$ping = $response->ping();

				if (isset($ping->code))
				{
					throw new Exception(JText::sprintf('RSFP_GETRESPONSE_ERROR', $ping->code, $ping->message));
				}

				list($args['placeholders'], $args['$values']) = RSFormProHelper::getReplacements($SubmissionId);

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

				$element = array(
					'campaign'          => array(
						'campaignId' => $row->getresponse_list
					),
					'dayOfCycle'        => 0,
					'customFieldValues' => array(),
				);

				foreach ($fields as $field => $value)
				{
					// Custom fields should be in a separate array
					if ($field == 'name' || $field == 'email')
					{
						$element[$field] = str_replace($args['placeholders'], $args['values'], $value);
					}
					else
					{
						if ($value !== '')
						{
							$element['customFieldValues'][] = array(
								'customFieldId' => $custom_fields[$field],
								'value'         => array(
									str_replace($args['placeholders'], $args['values'], $value)
								),
							);
						}
					}
				}

				if ((int) $row->getresponse_update)
				{

					$result = $response->getContacts(array(
						'query' => array(
							'email' => $element['email'],
						),
					));

					$result = (array) $result;

					if (!empty($result))
					{
						$contact = $response->updateContact($result[0]->contactId, $element);
					}
					else
					{
						$contact = $response->addContact($element);
					}
				}
				else
				{
					$contact = $response->addContact($element);
				}

				if (property_exists($contact, 'code'))
				{
					throw new Exception(JText::sprintf('RSFP_GETRESPONSE_ERROR', $contact->code, $contact->message));
				}

			} catch (Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}
	}

	/**
	 * @return array
	 */
	public function standardFields()
	{
		return array(
			'name'  => JText::_('RSFP_GETRESPONSE_NAME'),
			'email' => JText::_('RSFP_GETRESPONSE_EMAIL'),
		);
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
	 * @param $formId
	 */
	public function rsfp_onFormDelete($formId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__rsform_getresponse')
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
			->from($db->qn('#__rsform_getresponse'))
			->where($db->qn('form_id') . '=' . $db->q($form->FormId));
		$db->setQuery($query);
		if ($result = $db->loadObject())
		{
			// No need for a form_id
			unset($result->form_id);

			$xml->add('getresponse');
			foreach ($result as $property => $value)
			{
				$xml->add($property, $value);
			}
			$xml->add('/getresponse');
		}
	}

	/**
	 * @param $form
	 * @param $xml
	 * @param $fields
	 */
	public function rsfp_onFormRestore($form, $xml, $fields)
	{
		if (isset($xml->getresponse))
		{
			$data = array(
				'form_id' => $form->FormId
			);
			foreach ($xml->getresponse->children() as $property => $value)
			{
				$data[$property] = (string) $value;
			}
			$row = JTable::getInstance('RSForm_GetResponse', 'Table');

			if (!$row->load($form->FormId))
			{
				$db    = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->insert('#__rsform_getresponse')
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
		JFactory::getDbo()->truncateTable('#__rsform_getresponse');
	}
}