<?php
/**
 * @package        RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link           https://www.rsjoomla.com
 * @license        GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die;

/**
 * Class plgSystemRSFPVerticalResponse
 */
class plgSystemRSFPVerticalResponse extends JPlugin
{

	/**
	 * @var bool
	 */
	protected $autoloadLanguage = true;

	/**
	 * plgSystemRSFPVerticalResponse constructor.
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
	 * @return bool
	 */
	protected function canRun()
	{
		if (class_exists('RSFormProHelper'))
		{
			return true;
		}

		return false;
	}

	/**
	 * @param $form
	 *
	 * @return bool
	 */
	public function rsfp_onFormSave($form)
	{
		$post            = JRequest::get('post', JREQUEST_ALLOWRAW);
		$post['form_id'] = $post['formId'];

		$row = JTable::getInstance('RSForm_VerticalResponse', 'Table');
		if (!$row)
		{
			return false;
		}
		if (!$row->bind($post))
		{
			JError::raiseWarning(500, $row->getError());

			return false;
		}

		$db = JFactory::getDBO();
		$db->setQuery("SELECT form_id FROM #__rsform_verticalresponse WHERE form_id='" . (int) $post['form_id'] . "'");
		if (!$db->loadResult())
		{
			$db->setQuery("INSERT INTO #__rsform_verticalresponse SET form_id='" . (int) $post['form_id'] . "'");
			$db->execute();
		}

		$row->vars = null;
		if (!empty($post['verticalresponse_vars']))
		{
			$row->vars = serialize($post['verticalresponse_vars']);

		}

		if ($row->store())
		{
			return true;
		}

		JError::raiseWarning(500, $row->getError());

		return false;
	}

	/**
	 * @param $tabs
	 */
	public function rsfp_bk_onAfterShowConfigurationTabs($tabs)
	{
		$tabs->addTitle(JText::_('RSFP_VERTICALRESPONSE_LABEL'), 'rsfp-verticalresponse');
		$tabs->addContent($this->showConfigurationScreen());
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	public function onAfterRoute()
	{

		$app = JFactory::getApplication();

		if (!$app->isAdmin())
		{
			return false;
		}

		$code = $app->input->get('code', '');
		if ($code)
		{
			if (!class_exists('RSFormProHelper'))
			{
				$helper = JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsform.php';
				if (file_exists($helper))
				{
					require_once($helper);
					RSFormProHelper::readConfig();
				}
				else
				{
					return false;
				}
			}

			$http          = JHttpFactory::getHttp();
			$client_id     = RSFormProHelper::getConfig('verticalresponse.key');
			$client_secret = RSFormProHelper::getConfig('verticalresponse.secret');

			$returnUrl = JURI::root() . 'administrator/index.php';

			$url = 'https://vrapi.verticalresponse.com/api/v1/oauth/access_token?client_id=' . $client_id . '&client_secret=' . $client_secret . '&redirect_uri=' . $returnUrl . '&code=' . $code;

			try
			{
				$response = $http->get($url, array(), 3);

				if ($response->code != 200)
				{
					throw new Exception(JText::sprintf('RSFP_VERTICALRESPONSE_CONNECTION_ERROR', $response->code));
				}

				$token = $response->body;

				RSFormProConfig::getInstance()->set('verticalresponse.token', $token);

				JFactory::getDocument()->addScriptDeclaration("
					window.opener.jQuery('#verticalresponsetoken').val('" . $token . "');
					window.opener.jQuery('#verticalresponsemessage').show();
					window.close();
				");
			} catch (Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}

		}
	}

	/**
	 * @return string
	 */
	protected function showConfigurationScreen()
	{
		ob_start();
		$key    = RSFormProHelper::getConfig('verticalresponse.key');
		$secret = RSFormProHelper::getConfig('verticalresponse.secret');
		$token  = RSFormProHelper::getConfig('verticalresponse.token');

		$url = '';
		if (isset($key))
		{
			$returnUrl = JURI::root() . 'administrator/index.php';
			$url       = 'https://vrapi.verticalresponse.com/api/v1/oauth/authorize?client_id=' . $key . '&redirect_uri=' . urlencode($returnUrl);
		}

		?>
		<div id="page-rsfpverticalresponse">
			<table class="admintable">
				<tr>
					<td width="200" class="key">
						<label for="verticalresponsekey"><?php echo JText::_('RSFP_VERTICALRESPONSE_KEY'); ?></label>
					</td>
					<td>
						<input id="verticalresponsekey" type="text" name="rsformConfig[verticalresponse.key]" value="<?php echo RSFormProHelper::htmlEscape($key); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr>
					<td width="200" class="key">
						<label for="verticalresponsesecret"><?php echo JText::_('RSFP_VERTICALRESPONSE_SECRET'); ?></label>
					</td>
					<td>
						<input id="verticalresponsesecret" type="text" name="rsformConfig[verticalresponse.secret]" value="<?php echo RSFormProHelper::htmlEscape($secret); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" class="key">
						<label for="verticalresponsessl"><?php echo JText::_('RSFP_VERTICALRESPONSE_USESSL'); ?></label>
					</td>
					<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'rsformConfig[verticalresponse.usessl]', '', RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('verticalresponse.usessl')), JText::_('JYES'), JText::_('JNO')); ?>
					</td>
				</tr>
				<tr>
					<td width="200" class="key" colspan="2">
						<button type="button" id="verticalresponseauthorize" class="btn<?php if ($key)
						{ ?> btn-success<?php } ?>" <?php if (!$key)
						{ ?>disabled="disabled"<?php } ?> onclick="window.open(jQuery(this).data('url'), 'vrAuth', 'width=600,height=700,menubar=no');" data-url="<?php echo RSFormProHelper::htmlEscape($url); ?>"><?php echo JText::_('RSFP_VERTICALRESPONSE_AUTHORIZE'); ?></button>
					</td>
				</tr>
			</table>
			<?php if (!$key)
			{ ?>
				<p>
					<a href="http://developers.verticalresponse.com/" class="btn btn-primary" target="_blank"><?php echo JText::_('RSFP_VERTICALRESPONSE_GETKEY'); ?></a>
				</p>
			<?php } ?>
			<input id="verticalresponsetoken" type="hidden" name="rsformConfig[verticalresponse.token]" value="<?php echo RSFormProHelper::htmlEscape($token); ?>" size="100" maxlength="64">

			<div id="verticalresponsemessage" <?php if (!$token)
			{ ?>style="display: none;"<?php } ?> class="alert alert-success"><?php echo JText::_('RSFP_VERTICALRESPONSE_TOKEN_SAVED'); ?></div>
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
		echo '<li><a href="javascript: void(0);" id="rsfpverticalresponse"><span class="rsficon rsficon-envelope-square"></span><span class="inner-text">' . JText::_('RSFP_VERTICALRESPONSE_LABEL') . '</span></a></li>';
	}

	/**
	 * @throws Exception
	 */
	public function rsfp_bk_onAfterShowFormEditTabs()
	{
		$formId = JFactory::getApplication()->input->getInt('formId', 0);
		$row    = JTable::getInstance('RSForm_VerticalResponse', 'Table');
		$token  = RSFormProHelper::getConfig('verticalresponse.token');
		$app    = JFactory::getApplication();

		if ($token == '')
		{
			?>
			<div id="rsfpverticalresponsediv">
				<table class="admintable">
					<tr>
						<td valign="top" align="left" width="30%">
							<table class="table table-bordered">
								<div class="alert alert-warning"><?php echo JText::_('RSFP_VERTICALRESPONSE_NOTOKEN') ?></div>
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
			require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/verticalresponse/vr_api_client.php';
			require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/verticalresponse/vr_api_contact_list.php';

			$token = json_decode($token);

			if ($token === null)
			{
				throw new Exception(JText::_('RSFP_VERTICALRESPONSE_COULD_NOT_DECODE_JSON_DATA'));
			}

			$protocol = '';
			if (RSFormProHelper::getConfig('verticalresponse.usessl'))
			{
				$protocol = 's';
			}

			if (!defined('ROOT_URL'))
			{
				define('ROOT_URL', 'http' . $protocol . '://vrapi.verticalresponse.com/api/v1/');
			}
			if (!defined('VR_API_ACCESS_TOKEN'))
			{
				define('VR_API_ACCESS_TOKEN', $token->access_token);
			}

			$response = new VR_APIClient(null);

			$response = $response->get(
				ROOT_URL . 'custom_fields'
			);

			if ($response == 'Invalid or expired token')
			{
				throw new Exception(JText::_('RSFP_VERTICALRESPONSE_INVALIDTOKEN'));
			}

			if (!empty($response['items']))
			{
				foreach ($response['items'] as $custom_field)
				{
					$merge_vars[$custom_field['attributes']['name']] = $custom_field['attributes']['name'];
				}
			}

			JFactory::getDocument()->addScriptDeclaration('
		jQuery(document).ready(function ($) {
			var $enable_vr = $(\'.enable_vr\');
			var $vr_default = $(\'.enable_vr input[type="radio"]:checked\').val();

			if ($vr_default == \'1\') {
				$(\'.vertical_response_fields\').show();
			} else {
				$(\'.vertical_response_fields\').hide();
			}

			$enable_vr.change(function () {
				var $val = $(\'.enable_vr input[type="radio"]:checked\').val();
				if ($val == \'1\') {
					$(\'.vertical_response_fields\').show();
				} else {
					$(\'.vertical_response_fields\').hide();
				}
			});
		});
	');

			//Load the view
			include_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/verticalresponse.php';

		} catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'warning');
		}

	}

	/**
	 * @param $args
	 */
	public function rsfp_f_onAfterFormProcess($args)
	{

		$db           = JFactory::getDBO();
		$formId       = (int) $args['formId'];
		$SubmissionId = (int) $args['SubmissionId'];

		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__rsform_verticalresponse'))
			->where($db->qn('form_id') . '=' . $db->q($formId))
			->where($db->qn('enable_verticalresponse') . '=' . $db->q(1));
		$db->setQuery($query);

		if ($row = $db->loadObject())
		{
			try
			{
				require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/verticalresponse/vr_api_contact.php';
				require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/verticalresponse/vr_api_contact_list.php';

				$token = RSFormProHelper::getConfig('verticalresponse.token');

				$token = json_decode($token);

				if ($token === null)
				{
					throw new Exception(JText::_('RSFP_VERTICALRESPONSE_COULD_NOT_DECODE_JSON_DATA'));
				}

				$protocol = '';
				if (RSFormProHelper::getConfig('verticalresponse.usessl'))
				{
					$protocol = 's';
				}

				if (!defined('ROOT_URL'))
				{
					define('ROOT_URL', 'http' . $protocol . '://vrapi.verticalresponse.com/api/v1/');
				}
				if (!defined('VR_API_ACCESS_TOKEN'))
				{
					define('VR_API_ACCESS_TOKEN', $token->access_token);
				}

				// Get replacements
				list($args['$placeholders'], $args['values']) = RSFormProHelper::getReplacements($SubmissionId);

				$standard_fields = $this->standardFields();

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
					if (array_key_exists($field, $standard_fields))
					{
						$element[$field] = str_replace($args['placeholders'], $args['values'], $value);
					}
					else
					{
						$element['custom'][$field] = str_replace($args['placeholders'], $args['values'], $value);
					}
				}

				$element = array_filter($element);

				if ($row->verticalresponse_list !== '')
				{
					$url = ROOT_URL . 'lists/' . $row->verticalresponse_list . '/contacts';

				}

				else
				{
					$url = ROOT_URL . 'contacts';
				}

				if ($row->verticalresponse_update)
				{

					$search = $this->userExists($element);

					if ($search)
					{
						$url = ROOT_URL . 'contacts/' . $search;

						unset($element['email']);

						$result = VR_APIClient::put(
							$url,
							$element
						);

						if ($result == 'Invalid or expired token')
						{
							throw new Exception(JText::_('RSFP_VERTICALRESPONSE_INVALIDTOKEN'));
						}
					}
					else
					{
						$result = VR_APIClient::post(
							$url,
							$element
						);

						if ($result == 'Invalid or expired token')
						{
							throw new Exception(JText::_('RSFP_VERTICALRESPONSE_INVALIDTOKEN'));
						}
					}

				}
				else
				{
					$result = VR_APIClient::post(
						$url,
						$element
					);

					if ($result == 'Invalid or expired token')
					{
						throw new Exception(JText::_('RSFP_VERTICALRESPONSE_INVALIDTOKEN'));
					}
				}

			} catch (Exception $e)
			{
				JError::raiseWarning(500, $e->getMessage());
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
			'first_name'          => JText::_('RSFP_VERTICALRESPONSE_FIRSTNAME'),
			'last_name'           => JText::_('RSFP_VERTICALRESPONSE_LASTNAME'),
			'marital_status'      => JText::_('RSFP_VERTICALRESPONSE_MARITAL_STATUS'),
			'birthdate_month'     => JText::_('RSFP_VERTICALRESPONSE_BIRTHMONTH'),
			'birthdate_day'       => JText::_('RSFP_VERTICALRESPONSE_BIRTHDAY'),
			'birthdate_year'      => JText::_('RSFP_VERTICALRESPONSE_BIRTHYEAR'),
			'gender'              => JText::_('RSFP_VERTICALRESPONSE_GENDER'),
			'email'               => JText::_('RSFP_VERTICALRESPONSE_EMAIL'),
			'work_phone'          => JText::_('RSFP_VERTICALRESPONSE_WORK_PHONE'),
			'home_phone'          => JText::_('RSFP_VERTICALRESPONSE_HOME_PHONE'),
			'mobile_phone'        => JText::_('RSFP_VERTICALRESPONSE_MOBILE_PHONE'),
			'fax'                 => JText::_('RSFP_VERTICALRESPONSE_FAX'),
			'website'             => JText::_('RSFP_VERTICALRESPONSE_WEBSITE'),
			'title'               => JText::_('RSFP_VERTICALRESPONSE_TITLE'),
			'company_name'        => JText::_('RSFP_VERTICALRESPONSE_COMPANY_NAME'),
			'street_address'      => JText::_('RSFP_VERTICALRESPONSE_ADDRESS1'),
			'extended_address'    => JText::_('RSFP_VERTICALRESPONSE_ADDRESS2'),
			'locality'            => JText::_('RSFP_VERTICALRESPONSE_LOCALITY'),
			'postal_code'         => JText::_('RSFP_VERTICALRESPONSE_POSTAL_CODE'),
			'region'              => JText::_('RSFP_VERTICALRESPONSE_REGION'),
			'country_code_alpha2' => JText::_('RSFP_VERTICALRESPONSE_COUNTRYCODE')
		);
	}

	/**
	 * Function returns the USER ID if it's present in the contact list,
	 * else - returns false
	 *
	 * @param $args
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function userExists($args)
	{

		$response = new VR_APIClient(null);
		$search   = array(
			'type'  => 'all',
			'email' => $args['email']
		);

		$response = $response->get(
			ROOT_URL . 'contacts',
			$search
		);

		if ($response == 'Invalid or expired token')
		{
			throw new Exception(JText::_('RSFP_VERTICALRESPONSE_INVALIDTOKEN'));
		}

		if (empty($response['items']))
		{
			return false;
		}

		return $response['items'][0]['attributes']['id'];

	}

	/**
	 * @param $formId
	 */
	public function rsfp_onFormDelete($formId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__rsform_verticalresponse')
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
			->from($db->qn('#__rsform_verticalresponse'))
			->where($db->qn('form_id') . '=' . $db->q($form->FormId));
		$db->setQuery($query);
		if ($result = $db->loadObject())
		{
			// No need for a form_id
			unset($result->form_id);

			$xml->add('verticalresponse');
			foreach ($result as $property => $value)
			{
				$xml->add($property, $value);
			}
			$xml->add('/verticalresponse');
		}
	}

	/**
	 * @param $form
	 * @param $xml
	 * @param $fields
	 */
	public function rsfp_onFormRestore($form, $xml, $fields)
	{
		if (isset($xml->verticalresponse))
		{
			$data = array(
				'form_id' => $form->FormId
			);
			foreach ($xml->verticalresponse->children() as $property => $value)
			{
				$data[$property] = (string) $value;
			}
			$row = JTable::getInstance('RSForm_VerticalResponse', 'Table');

			if (!$row->load($form->FormId))
			{
				$db    = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->insert('#__rsform_verticalresponse')
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
		JFactory::getDbo()->truncateTable('#__rsform_verticalresponse');
	}
}