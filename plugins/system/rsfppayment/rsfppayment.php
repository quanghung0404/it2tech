<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemRSFPPayment extends JPlugin
{
	protected $_products = array();
	
	public function __construct( &$subject, $config ) {
		parent::__construct( $subject, $config );
		$this->newComponents = array(21, 22, 23, 27, 28);
		
		$this->loadLanguage();
	}
	
	public function rsfp_bk_onAfterShowComponents() {
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfppayment');
		
		$mainframe 	= JFactory::getApplication();
		$db 		= JFactory::getDBO();
		$formId 	=  JFactory::getApplication()->input->getInt('formId');
		
		$link1 = "displayTemplate('21')";
		$link2 = "displayTemplate('23')";
		$link3 = "displayTemplate('27')";
		$link4 = "displayTemplate('28')";
		if ($components = RSFormProHelper::componentExists($formId, 21))
			$link1 = "displayTemplate('21', '".$components[0]."')";
		if ($components = RSFormProHelper::componentExists($formId, 23))
			$link2 = "displayTemplate('23', '".$components[0]."')";
		if ($components = RSFormProHelper::componentExists($formId, 27))
			$link3 = "displayTemplate('27', '".$components[0]."')";
		if ($components = RSFormProHelper::componentExists($formId, 28))
			$link4 = "displayTemplate('28', '".$components[0]."')";
		?>
		<li class="rsform_navtitle"><?php echo JText::_('RSFP_PAYMENT'); ?></li>
		<li><a href="javascript: void(0);" onclick="<?php echo $link1;?>;return false;" id="rsfpc21"><span class="rsficon rsficon-dollar2"></span><span class="inner-text"><?php echo JText::_('RSFP_SPRODUCT'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="displayTemplate('22');return false;" id="rsfpc22"><span class="rsficon rsficon-dollar2"></span><span class="inner-text"><?php echo JText::_('RSFP_MPRODUCT'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="<?php echo $link4;?>;return false;" id="rsfpc28"><span class="rsficon rsficon-moneybag"></span><span class="inner-text"><?php echo JText::_('RSFP_DONATION'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="<?php echo $link2;?>;return false;" id="rsfpc23"><span class="rsficon rsficon-dollar"></span><span class="inner-text"><?php echo JText::_('RSFP_TOTAL'); ?></span></a></li>
		<li><a href="javascript: void(0);" onclick="<?php echo $link3;?>;return false;" id="rsfpc27"><span class="rsficon rsficon-list-alt"></span><span class="inner-text"><?php echo JText::_('RSFP_CHOOSE_PAYMENT'); ?></span></a></li>
		<?php
	}
	
	public function rsfp_bk_onAfterShowFormEditTabs() {
		$formId = JFactory::getApplication()->input->getInt('formId');
		
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfppayment');
		
		$row = JTable::getInstance('RSForm_Payment', 'Table');
		if (!$row)
			return;
		$row->load($formId);
		$row->params = !empty($row->params) ? unserialize($row->params) : new stdClass();
		
		$def_params = array('UserEmail', 'AdminEmail', 'AdditionalEmails');
		foreach ($def_params as $def_param)
			if (!isset($row->params->{$def_param}))
				$row->params->{$def_param} = 0;
		
		$lists['UserEmail'] 		= RSFormProHelper::renderHTML('select.booleanlist','payment[UserEmail]','class="inputbox"',$row->params->UserEmail);
		$lists['AdminEmail'] 		= RSFormProHelper::renderHTML('select.booleanlist','payment[AdminEmail]','class="inputbox"',$row->params->AdminEmail);
		$lists['AdditionalEmails'] 	= RSFormProHelper::renderHTML('select.booleanlist','payment[AdditionalEmails]','class="inputbox"',$row->params->AdditionalEmails);
		
		echo '<div id="paymentdiv">';
			include JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/payment.php';
		echo '</div>';
	}
	
	public function rsfp_bk_onAfterShowFormEditTabsTab() {
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfppayment');
		
		echo '<li><a href="javascript: void(0);"><span class="rsficon rsficon-dollar2"></span><span class="inner-text">'.JText::_('RSFP_PAYMENT_EMAIL_SETTINGS').'</span></a></li>';
	}
	
	public function rsfp_onFormSave($form) {
		$post = JRequest::get('post', JREQUEST_ALLOWRAW);
		
		$row = JTable::getInstance('RSForm_Payment', 'Table');
		if (!$row)
			return;
		$row->form_id = $post['formId'];
		$params = new stdClass();
		if (isset($post['payment']) && is_array($post['payment']))
			foreach ($post['payment'] as $key => $value)
				$params->{$key} = $value;
		$row->params = serialize($params);
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT form_id FROM #__rsform_payment WHERE form_id='".(int) $post['formId']."'");
		if (!$db->loadResult())
		{
			$db->setQuery("INSERT INTO #__rsform_payment SET form_id='".(int) $post['formId']."'");
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
	
	public function rsfp_bk_onAfterCreateComponentPreview($args = array()) {
		$nodecimals = RSFormProHelper::getConfig('payment.nodecimals');
		$decimal    = RSFormProHelper::getConfig('payment.decimal');
		$thousands  = RSFormProHelper::getConfig('payment.thousands');
		$currency   = RSFormProHelper::getConfig('payment.currency');
		
		switch ($args['ComponentTypeName'])
		{
			case 'singleProduct':
				if (!$args['data']['PRICE']) {
					$args['data']['PRICE'] = 0;
				}
				$args['out'] = '<td>'.$args['data']['CAPTION'].'</td>';
				$args['out'].= '<td><span class="rsficon rsficon-dollar2" style="font-size:24px;margin-right:5px"></span> '.$args['data']['CAPTION'].' - '.number_format($args['data']['PRICE'], $nodecimals, $decimal, $thousands).' '.$currency.'</td>';	
			break;
			
			case 'multipleProducts':
				$out 	=& $args['out'];
				$data 	=& $args['data'];
				
				$out  = '<td>'.$data['CAPTION'].'</td>';
				$out .= '<td><span class="rsficon rsficon-dollar2" style="font-size:24px;margin-right:5px"></span>';
				
				$items = RSFormProHelper::explode(RSFormProHelper::isCode($data['ITEMS']));
				
				if ($data['VIEW_TYPE'] == 'DROPDOWN') {
					$out .= '<select '.($data['MULTIPLE']=='YES' ? 'multiple="multiple"' : '').'>';

					$special = array('[c]', '[g]', '[d]');
					
					foreach ($items as $item) {
						@list($val, $txt) = @explode('|', str_replace($special, '', $item), 2);
						if (is_null($txt))
							$txt = $val;
						if (!$val) {
							$val = 0;
						}
						if ($val) {
							$txt_price = $this->_getPriceMask($txt, $val);
						} else {
							$txt_price = $txt;
						}
						
						// <optgroup>
						if (strpos($item, '[g]') !== false) {
							$out .= '<optgroup label="'.$this->_escape($val).'">';
							continue;
						}
						// </optgroup>
						if(strpos($item, '[/g]') !== false) {
							$out .= '</optgroup>';
							continue;
						}
						
						$additional = '';
						// selected
						if (strpos($item, '[c]') !== false)
							$additional .= 'selected="selected"';
						// disabled
						if (strpos($item, '[d]') !== false)
							$additional .= 'disabled="disabled"';
						
						$out .= '<option '.$additional.' value="'.($val ? $this->_escape($txt) : '').'">'.$this->_escape($txt_price).'</option>';
					}
					$out .= '</select>';
				} elseif ($data['VIEW_TYPE'] == 'CHECKBOX' || $data['VIEW_TYPE'] == 'RADIOGROUP') {
					$i = 0;
					
					$special = array('[c]', '[d]');
					
					$type = $data['VIEW_TYPE'] == 'CHECKBOX' ? 'checkbox' : 'radio';
					foreach ($items as $item) {
						@list($val, $txt) = @explode('|', str_replace($special, '', $item), 2);
						if (is_null($txt))
							$txt = $val;
						if (!$val) {
							$val = 0;
						}
						$txt_price = $this->_getPriceMask($txt, $val);
						
						$additional = '';
						// checked
						if (strpos($item, '[c]') !== false)
							$additional .= 'checked="checked"';
						// disabled
						if (strpos($item, '[d]') !== false)
							$additional .= 'disabled="disabled"';
						
						

						$out .= '<label class="'.$type.($data['FLOW'] != 'VERTICAL' ? ' inline':'').'"><input '.$additional.' type="'.$type.'" value="'.$this->_escape($txt).'" />'.$txt_price.'</label>';
						$i++;
					}
				}
				
				$out .= '</td>';
			break;
			
			case 'total':
				$args['out'] = '<td>'.$args['data']['CAPTION'].'</td>';
				$args['out'].= '<td><span class="rsficon rsficon-dollar" style="font-size:24px;margin-right:5px"></span> '.number_format(0, $nodecimals, $decimal, $thousands).' '.$currency.'</td>';	
			break;
			
			case 'choosePayment':
				$out 	=& $args['out'];
				$data 	=& $args['data'];
				$out =  '<td>'.$data['CAPTION'].'</td>';
				$out .= '<td><span class="rsficon rsficon-list-alt" style="font-size:24px;margin-right:5px"></span> ';	
				
				$items = $this->_getPayments($args['formId']);
				if ($data['VIEW_TYPE'] == 'DROPDOWN') {
					$out .= '<select>';
					foreach ($items as $item) {
						$out .= '<option>'.$this->_escape($item->text).'</option>';
					}
					$out .= '</select>';
				} elseif ($data['VIEW_TYPE'] == 'RADIOGROUP') {
					$i = 0;
					foreach ($items as $item) {
						$checked = $i == 0 ? 'checked="checked"' : '';
						$out .= '<label for="'.$data['NAME'].$i.'" class="radio'.($data['FLOW'] != 'VERTICAL' ? ' inline': '').'"><input '.$checked.' type="radio" value="'.$this->_escape($item->value).'" id="'.$data['NAME'].$i.'" />'.$this->_escape($item->text).'</label>';
						$i++;
					}
				}
				$out .= '</td>';
			break;
			
			case 'donationProduct':
				$defaultValue = RSFormProHelper::isCode($args['data']['DEFAULTVALUE']);
				
				$args['out']  = '<td>'.$args['data']['CAPTION'].'</td>';
				$args['out'] .= '<td><span class="rsficon rsficon-moneybag" style="font-size:24px;margin-right:5px"></span> <input type="text" value="'.$this->_escape($defaultValue).'" size="'.$args['data']['SIZE'].'" /></td>';
			break;
		}
	}
	
	public function rsfp_bk_onAfterShowConfigurationTabs($tabs) {
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfppayment');
		
		$tabs->addTitle(JText::_('RSFP_PAYMENT'), 'form-payment');
		$tabs->addContent($this->paymentConfigurationScreen());
	}
	
	public function rsfp_bk_onAfterCreateFrontComponentBody($args) {
		$db = JFactory::getDBO();
		
		$nodecimals = RSFormProHelper::getConfig('payment.nodecimals');
		$decimal    = RSFormProHelper::getConfig('payment.decimal');
		$thousands  = RSFormProHelper::getConfig('payment.thousands');
		$currency   = RSFormProHelper::getConfig('payment.currency');
		$totalMask 	= RSFormProHelper::getConfig('payment.totalmask');
		
		$value  = $args['value'];
		$formId = (int) $args['formId'];
		
		$db->setQuery("SELECT FormLayoutName FROM #__rsform_forms WHERE FormId='".$formId."'");
		$layoutName = $db->loadResult();
		
		$out 	 =& $args['out'];
		$data 	 =& $args['data'];
		$invalid =& $args['invalid'];
		
		switch($args['r']['ComponentTypeId'])
		{
			case 21:
			{
				$out  = '<input type="hidden" value="'.$this->_escape($data['PRICE']).'" />';
				$out .= '<input type="hidden" name="form['.$data['NAME'].']" id="'.$data['NAME'].'" value="'.$this->_escape($data['CAPTION']).'" />';
			}
			break;
			
			case 22:
			{
				if (!isset($this->_products[$args['componentId']])) {
					$this->_products[$args['componentId']] = array();
				}
				
				switch($args['data']['VIEW_TYPE'])
				{
					case 'DROPDOWN':
					{
						$className = 'rsform-select-box';
						if ($layoutName == 'bootstrap3') {
							$className .= ' form-control';
						}
						if ($invalid)
							$className .= ' rsform-error';
						RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
						
						$out .= '<select '.($data['MULTIPLE']=='YES' ? 'multiple="multiple"' : '').' name="form['.$data['NAME'].'][]" '.((int) $data['SIZE'] > 0 ? 'size="'.(int) $data['SIZE'].'"' : '').' id="payment-'.$args['componentId'].'" '.$data['ADDITIONALATTRIBUTES'].' onchange="getPrice_'.$args['formId'].'();">';
						
						$items = RSFormProHelper::explode(RSFormProHelper::isCode($data['ITEMS']));
						
						$special = array('[c]', '[g]', '[d]');
						
						foreach ($items as $item)
						{
							@list($val, $txt) = @explode('|', str_replace($special, '', $item), 2);
							if (is_null($txt))
								$txt = $val;

							if ($val) {
								$txt_price = $this->_getPriceMask($txt, $val);
							} else { // no point showing - 0.00
								$txt_price = $txt;
								if ($val === '0') {
									$val = $txt;
								}
							}
							
							// <optgroup>
							if (strpos($item, '[g]') !== false) {
								$out .= '<optgroup label="'.$this->_escape($val).'">';
								continue;
							}
							// </optgroup>
							if(strpos($item, '[/g]') !== false) {
								$out .= '</optgroup>';
								continue;
							}
							
							$additional = '';
							// selected
							if ((strpos($item, '[c]') !== false && empty($value)) || (isset($value[$data['NAME']]) && in_array($txt, (array) $value[$data['NAME']])))
								$additional .= 'selected="selected"';
							// disabled
							if (strpos($item, '[d]') !== false)
								$additional .= 'disabled="disabled"';
							
							$out .= '<option '.$additional.' value="'.($val ? $this->_escape($txt) : '').'">'.$this->_escape($txt_price).'</option>';
							
							$this->_products[$args['componentId']][] = array(
								'val' => $val,
								'txt' => $txt
							);
						}
						$out .= '</select>';
					}
					break;
					
					case 'CHECKBOX':
					{
						$i = 0;
				
						$items = RSFormProHelper::explode(RSFormProHelper::isCode($data['ITEMS']));
						
						$special = array('[c]', '[d]');
						
						foreach ($items as $item)
						{
							@list($val, $txt) = @explode('|', str_replace($special, '', $item), 2);
							if (is_null($txt))
								$txt = $val;
							if ($val) {
								$txt_price = $this->_getPriceMask($txt, $val);
							} else { // no point showing - 0.00
								$txt_price = $txt;
								if ($val === '0') {
									$val = $txt;
								}

							}

							
							$additional = '';
							// checked
							if ((strpos($item, '[c]') !== false && empty($value)) || (isset($value[$data['NAME']]) && in_array($txt, (array) $value[$data['NAME']])))
								$additional .= 'checked="checked"';
							// disabled
							if (strpos($item, '[d]') !== false)
								$additional .= 'disabled="disabled"';

							switch($layoutName) {
								case 'bootstrap2':
									$out .= '<label for="payment-'.$args['componentId'].'-'.$i.'" class="checkbox'.($data['FLOW'] != 'VERTICAL' ? ' inline' : '').'"><input '.$additional.' name="form['.$data['NAME'].'][]" type="checkbox" value="'.$this->_escape($txt).'" id="payment-'.$args['componentId'].'-'.$i.'" '.$data['ADDITIONALATTRIBUTES'].' onclick="getPrice_'.$args['formId'].'();" />'.$txt_price.'</label>';
								break;
								case 'bootstrap3':
									$out .= '<label for="payment-'.$args['componentId'].'-'.$i.'" class="checkbox'.($data['FLOW'] != 'VERTICAL' ? '-inline' : '').'"><input '.$additional.' name="form['.$data['NAME'].'][]" type="checkbox" value="'.$this->_escape($txt).'" id="payment-'.$args['componentId'].'-'.$i.'" '.$data['ADDITIONALATTRIBUTES'].' onclick="getPrice_'.$args['formId'].'();" />'.$txt_price.'</label>';
								break;
								case 'uikit':
									$out .= '<label for="payment-'.$args['componentId'].'-'.$i.'"><input '.$additional.' name="form['.$data['NAME'].'][]" type="checkbox" value="'.$this->_escape($txt).'" id="payment-'.$args['componentId'].'-'.$i.'" '.$data['ADDITIONALATTRIBUTES'].' onclick="getPrice_'.$args['formId'].'();" />'.$txt_price.'</label>';
									if ($data['FLOW'] == 'VERTICAL')
									{
										$out .= '<br />';
									}
								break;
								
								default:
									if ($data['FLOW']=='VERTICAL' && $layoutName == 'responsive') {
										$out .= '<p class="rsformVerticalClear">';
									}
									$out .= '<input '.$additional.' name="form['.$data['NAME'].'][]" type="checkbox" value="'.$this->_escape($txt).'" id="payment-'.$args['componentId'].'-'.$i.'" '.$data['ADDITIONALATTRIBUTES'].' onclick="getPrice_'.$args['formId'].'();" /><label for="payment-'.$args['componentId'].'-'.$i.'">'.$txt_price.'</label>';
									if ($data['FLOW'] == 'VERTICAL')
									{
										if ($layoutName == 'responsive')
											$out .= '</p>';
										else
											$out .= '<br />';
									}
								break;
							}
							
							$this->_products[$args['componentId']][] = array(
								'val' => $val,
								'txt' => $txt
							);
							
							$i++;
						}
					}
					break;
					
					case 'RADIOGROUP':
					{
						$i = 0;
				
						$items = RSFormProHelper::explode(RSFormProHelper::isCode($data['ITEMS']));
						
						$special = array('[c]', '[d]');
						
						foreach ($items as $item)
						{
							@list($val, $txt) = @explode('|', str_replace($special, '', $item), 2);
							if (is_null($txt))
								$txt = $val;
							if ($val) {
								$txt_price = $this->_getPriceMask($txt, $val);
							} else { // no point showing - 0.00
								$txt_price = $txt;
								if ($val === '0') {
									$val = $txt;
								}
							}
							
							$additional = '';
							// checked
							if ((strpos($item, '[c]') !== false && empty($value)) || (isset($value[$data['NAME']]) && in_array($txt, (array) $value[$data['NAME']])))
								$additional .= 'checked="checked"';
							// disabled
							if (strpos($item, '[d]') !== false)
								$additional .= 'disabled="disabled"';
								
							
							switch($layoutName) {
								case 'bootstrap2':
									$out .= '<label for="payment-'.$args['componentId'].'-'.$i.'" class="radio'.($data['FLOW'] != 'VERTICAL' ? ' inline' : '').'"><input '.$additional.' name="form['.$data['NAME'].']" type="radio" value="'.$this->_escape($txt).'" id="payment-'.$args['componentId'].'-'.$i.'" '.$data['ADDITIONALATTRIBUTES'].' onclick="getPrice_'.$args['formId'].'();" />'.$txt_price.'</label>';
								break;
								case 'bootstrap3':
									$out .= '<label for="payment-'.$args['componentId'].'-'.$i.'" class="radio'.($data['FLOW'] != 'VERTICAL' ? '-inline' : '').'"><input '.$additional.' name="form['.$data['NAME'].']" type="radio" value="'.$this->_escape($txt).'" id="payment-'.$args['componentId'].'-'.$i.'" '.$data['ADDITIONALATTRIBUTES'].' onclick="getPrice_'.$args['formId'].'();" />'.$txt_price.'</label>';
								break;
								case 'uikit':
									$out .= '<label for="payment-'.$args['componentId'].'-'.$i.'"><input '.$additional.' name="form['.$data['NAME'].']" type="radio" value="'.$this->_escape($txt).'" id="payment-'.$args['componentId'].'-'.$i.'" '.$data['ADDITIONALATTRIBUTES'].' onclick="getPrice_'.$args['formId'].'();" />'.$txt_price.'</label>';
									if ($data['FLOW'] == 'VERTICAL')
									{
										$out .= '<br />';
									}
								break;
								
								default:
									if ($data['FLOW']=='VERTICAL' && $layoutName == 'responsive') {
										$out .= '<p class="rsformVerticalClear">';
									}
									$out .= '<input '.$additional.' name="form['.$data['NAME'].']" type="radio" value="'.$this->_escape($txt).'" id="payment-'.$args['componentId'].'-'.$i.'" '.$data['ADDITIONALATTRIBUTES'].' onclick="getPrice_'.$args['formId'].'();" /><label for="payment-'.$args['componentId'].'-'.$i.'">'.$txt_price.'</label>';
									if ($data['FLOW'] == 'VERTICAL')
									{
										if ($layoutName == 'responsive')
											$out .= '</p>';
										else
											$out .= '<br />';
									}
								break;
							}	
							
							$this->_products[$args['componentId']][] = array(
								'val' => $val,
								'txt' => $txt
							);
							
							$i++;
						}
					}
					break;
				}
			}
			break;
		
			case 23:
			{
				$price = number_format(0, $nodecimals, $decimal, $thousands);
				$total = str_replace(array('{price}', '{currency}'), array($price, $currency), $totalMask);
				$args['out'] = '<span id="payment_total_'.$args['formId'].'" class="rsform_payment_total">'.$total.'</span> <input type="hidden" id="'.$args['data']['NAME'].'" value="0" name="form['.$args['data']['NAME'].']" />';
			}
			break;
			
			case 28:
			{
				$defaultValue = RSFormProHelper::isCode($data['DEFAULTVALUE']);
				
				$className = 'rsform-input-box';
				if ($layoutName == 'bootstrap3') {
					$className .= ' form-control';
				}
				if ($invalid)
					$className .= ' rsform-error';
				RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
				
				$out .= '<input type="text" value="'.(isset($value[$data['NAME']]) ? $this->_escape($value[$data['NAME']]) : $this->_escape($defaultValue)).'" size="'.$data['SIZE'].'" '.((int) $data['MAXSIZE'] > 0 ? 'maxlength="'.(int) $data['MAXSIZE'].'"' : '').' onkeyup="getPrice_'.$formId.'()" name="form['.$data['NAME'].']" id="'.$data['NAME'].'" '.$data['ADDITIONALATTRIBUTES'].'/>';
			}
			break;
			
			case 27:
			{
				$data 	= $args['data'];
				$out  	=& $args['out'];
				$value 	= $args['value'];
				
				if (isset($data['SHOW']) && $data['SHOW'] == 'NO') {
					RSFormProAssets::addStyleDeclaration('.rsform-block-'.JFilterOutput::stringURLSafe($data['NAME']).' { display: none !important; }');
				}
				
				$items = $this->_getPayments($args['formId']);
				if ($data['VIEW_TYPE'] == 'DROPDOWN') {
					$className = 'rsform-select-box';
					if ($layoutName == 'bootstrap3') {
						$className .= ' form-control';
					}
					RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
					
					$out .= '<select name="form['.$data['NAME'].'][]" id="'.$data['NAME'].'" '.$data['ADDITIONALATTRIBUTES'].' >';
					foreach ($items as $item)
					{
						$selected = '';
						if (isset($value[$data['NAME']]) && in_array($item->value, (array) $value[$data['NAME']]))
							$selected = 'selected="selected"';
						
						$out .= '<option '.$selected.' value="'.$this->_escape($item->value).'">'.$this->_escape($item->text).'</option>';
					}
					
					$out .= '</select>';
				} elseif ($data['VIEW_TYPE'] == 'RADIOGROUP') {
					$i = 0;
					foreach ($items as $item)
					{
						$checked = '';
						if (isset($value[$data['NAME']]) && $item->value == $value[$data['NAME']])
							$checked = 'checked="checked"';
						elseif (!isset($value[$data['NAME']]) && $i == 0)
							$checked = 'checked="checked"';
											
						switch($layoutName) {
							case 'bootstrap2':
								$out .= '<label for="'.$data['NAME'].$i.'" class="radio'.($data['FLOW'] != 'VERTICAL' ? ' inline' : '').'"><input name="form['.$data['NAME'].']" type="radio" '.$checked.' value="'.$this->_escape($item->value).'" id="'.$data['NAME'].$i.'" '.$data['ADDITIONALATTRIBUTES'].' />'.$this->_escape($item->text).'</label>';
							break;
							case 'bootstrap3':
								$out .= '<label for="'.$data['NAME'].$i.'" class="radio'.($data['FLOW'] != 'VERTICAL' ? '-inline' : '').'"><input name="form['.$data['NAME'].']" type="radio" '.$checked.' value="'.$this->_escape($item->value).'" id="'.$data['NAME'].$i.'" '.$data['ADDITIONALATTRIBUTES'].' />'.$this->_escape($item->text).'</label>';
							break;
							case 'uikit':
								$out .= '<label for="'.$data['NAME'].$i.'"><input name="form['.$data['NAME'].']" type="radio" '.$checked.' value="'.$this->_escape($item->value).'" id="'.$data['NAME'].$i.'" '.$data['ADDITIONALATTRIBUTES'].' />'.$this->_escape($item->text).'</label>';
								if ($data['FLOW'] == 'VERTICAL')
								{
									$out .= '<br />';
								}
							break;
							
							default:
								if ($data['FLOW']=='VERTICAL' && $layoutName == 'responsive') {
									$out .= '<p class="rsformVerticalClear">';
								}
								$out .= '<input name="form['.$data['NAME'].']" type="radio" '.$checked.' value="'.$this->_escape($item->value).'" id="'.$data['NAME'].$i.'" '.$data['ADDITIONALATTRIBUTES'].' /><label for="'.$data['NAME'].$i.'">'.$this->_escape($item->text).'</label>';
								if ($data['FLOW']=='VERTICAL')
								{
									if ($layoutName == 'responsive')
										$out .= '</p>';
									else
										$out .= '<br />';
								}
							break;
						}
						
						$i++;
					}
				}
			}
			break;
		}
	}
	
	protected function _escape($string) {
		return RSFormProHelper::htmlEscape($string);
	}
	
	protected function _getPayments($formId) {
		$items 		= array();
		$mainframe 	= JFactory::getApplication();
		$mainframe->triggerEvent('rsfp_getPayment', array(&$items, $formId));
		
		return $items;
	}
	
	public function rsfp_afterConfirmPayment($SubmissionId) {
		RSFormProHelper::sendSubmissionEmails($SubmissionId);
	}
	
	public function rsfp_f_onBeforeFormDisplay($args) {
		$formId		= $args['formId'];
		$formLayout = &$args['formLayout'];
		$nodecimals = RSFormProHelper::getConfig('payment.nodecimals');
		$decimal    = RSFormProHelper::getConfig('payment.decimal');
		$thousands  = RSFormProHelper::getConfig('payment.thousands');
		$currency   = RSFormProHelper::getConfig('payment.currency');
		$totalMask  = RSFormProHelper::getConfig('payment.totalmask');
		
		$donation = RSFormProHelper::componentExists($formId, 28);
		$single   = RSFormProHelper::componentExists($formId, 21);
		$multiple = RSFormProHelper::componentExists($formId, 22);
		$total 	  = RSFormProHelper::componentExists($formId, 23);
		
		$allComponents = array();
		if ($donation) {
			$allComponents = array_merge($allComponents, $donation);
		}
		if ($single) {
			$allComponents = array_merge($allComponents, $single);
		}
		if ($multiple) {
			$allComponents = array_merge($allComponents, $multiple);
		}
		if ($total) {
			$allComponents = array_merge($allComponents, $total);
		}
		
		// no point going ahead if we have no fields added
		if (!$allComponents) {
			return;
		}
		$properties = RSFormProHelper::getComponentProperties($allComponents);
		
		$formLayout .= '<script type="text/javascript">'."\n";
		$formLayout .= 'function getPrice_'.$formId.'() {'."\n";
		$formLayout .= 'var total = 0;'."\n";
		
		// Single product
		if ($single) {
			$data = $properties[$single[0]];
			
			$formLayout .= "var singlePrice = parseFloat('".addslashes($data['PRICE'])."');\n";
			$formLayout .= "if (!isNaN(singlePrice)) {\n";
			$formLayout .= "total += singlePrice;\n";
			$formLayout .= "}\n";
		}
		
		// Donation field
		if ($donation) {
			$data = $properties[$donation[0]];
			
			$formLayout .= "var donationPrice = parseFloat(document.getElementById('".addslashes($data['NAME'])."').value);\n";
			$formLayout .= "if (!isNaN(donationPrice)) {\n";
			$formLayout .= "total += donationPrice;\n";
			$formLayout .= "}\n";
		}
		
		// Multiple products
		if ($multiple) {
			$formLayout .= 'var products = {};'."\n";
			foreach ($multiple as $componentId) {
				$data = $properties[$componentId];
				
				$formLayout .= 'products['.$componentId.'] = [];'."\n";
				if (isset($this->_products[$componentId])) {
					foreach ($this->_products[$componentId] as $item) {
						$txt = $item['txt'];
						$val = $item['val'];
						
						$formLayout .= "products[".$componentId."].push(parseFloat('".addslashes($val)."'));\n";
					}
				
					$formLayout .= "var fields = rsfp_getFieldsByName($formId, '".addslashes($data['NAME'])."');\n";
					if ($data['VIEW_TYPE'] == 'DROPDOWN') {
						$formLayout .= "for (var i=0; i<fields[0].options.length; i++) {\n";
						$formLayout .= "if (fields[0].options[i].selected && typeof products[".$componentId."][i] != 'undefined') {\n";
						$formLayout .= "var price = products[".$componentId."][i];\n";
						$formLayout .= "if (!isNaN(price)) {\n";
						$formLayout .= "total += price;\n";
						$formLayout .= "}\n";
						$formLayout .= "}\n";
						$formLayout .= "}\n";
					} elseif ($data['VIEW_TYPE'] == 'CHECKBOX' || $data['VIEW_TYPE'] == 'RADIOGROUP') {
						$formLayout .= "for (var i=0; i<fields.length; i=i+2) {\n";
						$formLayout .= "if (fields[i].checked && typeof products[".$componentId."][i/2] != 'undefined') {\n";
						$formLayout .= "var price = products[".$componentId."][i/2];\n";
						$formLayout .= "if (!isNaN(price)) {\n";
						$formLayout .= "total += price;\n";
						$formLayout .= "}\n";
						$formLayout .= "}\n";
						$formLayout .= "}\n";
					}
				}
			}
		}
		
		// Format the price
		$formLayout .= "var formattedTotal = number_format(total, '".addslashes($nodecimals)."', '".addslashes($decimal)."', '".addslashes($thousands)."');\n";
		$formLayout .= "var hiddenFormattedTotal = number_format(total, 2, '.', '');\n";
		
		// Total field - populate it
		if ($total) {
			$data = $properties[$total[0]];
			
			$formLayout .= "var totalMask = '".addslashes($totalMask)."';\n";
			$formLayout .= "totalMask = totalMask.replace('{price}', formattedTotal);\n";
			$formLayout .= "totalMask = totalMask.replace('{currency}', '".addslashes($currency)."');\n";
			$formLayout .= "document.getElementById('payment_total_".$formId."').innerHTML = totalMask;\n";
			$formLayout .= "document.getElementById('".addslashes($data['NAME'])."').value = hiddenFormattedTotal;\n";
		}
		
		$formLayout = str_replace('</form>', '<input type="hidden" name="form[rsfp_Total]" value="0" />'."\n".'</form>', $formLayout);
		
		$formLayout .= "var field = rsfp_getFieldsByName($formId, 'rsfp_Total');\n";
		$formLayout .= "field[0].value = hiddenFormattedTotal;\n";
		
		$formLayout .= '}'."\n";
		$formLayout .= 'getPrice_'.$formId.'();'."\n";
		$formLayout .= '</script>';
	}
	
	public function rsfp_f_onBeforeStoreSubmissions($args) {
		if (RSFormProHelper::componentExists($args['formId'], $this->newComponents)) {
			$args['post']['_STATUS'] = '0';
		}
	}
	
	public function rsfp_f_onAfterFormProcess($args) {
		if (RSFormProHelper::componentExists($args['formId'], $this->newComponents))
		{
			$db = JFactory::getDBO();
			
			$products   = array();
			$donation   = RSFormProHelper::componentExists($args['formId'], 28);
			$single 	= RSFormProHelper::componentExists($args['formId'], 21);			
			$multiple 	= RSFormProHelper::componentExists($args['formId'], 22);
			$total 	  	= RSFormProHelper::componentExists($args['formId'], 23);
			
			$choosePayment = RSFormProHelper::componentExists($args['formId'], 27);
			
			$allComponents = array();
			if ($donation) {
				$allComponents = array_merge($allComponents, $donation);
			}
			if ($single) {
				$allComponents = array_merge($allComponents, $single);
			}
			if ($multiple) {
				$allComponents = array_merge($allComponents, $multiple);
			}
			if ($total) {
				$allComponents = array_merge($allComponents, $total);
			}
			$properties = RSFormProHelper::getComponentProperties($allComponents);
			
			// PayPal, for legacy reasons
			$hasPayPal = RSFormProHelper::componentExists($args['formId'], 500);
			// Total price
			$price = $this->_getSubmissionValue($args['SubmissionId'], 'rsfp_Total');
			// Build products information
			// Single product
			if ($single) {
				$data = $properties[$single[0]];
				$products[] = strip_tags($data['CAPTION']);
			}
			// Multiple product
			if ($multiple) {
				foreach ($multiple as $componentId) {
					$data = $properties[$componentId];
					$bought = $this->_getSubmissionValue($args['SubmissionId'], $data['NAME']);
					if ($bought) {
						$products[] = strip_tags($data['CAPTION'].' - '.$bought);
					}
				}
			}
			// Donation product
			if ($donation) {
				$data = $properties[$donation[0]];
				$donated = $this->_getSubmissionValue($args['SubmissionId'], $data['NAME']);
				if ($donated) {
					$products[] = strip_tags($data['CAPTION']);
				}
			}
			
			if (($choosePayment && ($payValue = $this->_getSubmissionValue($args['SubmissionId'], $choosePayment[0]))) || ($hasPayPal && !$choosePayment && $payValue = 'paypal'))
			{
				//build verification code
				$db->setQuery("SELECT DateSubmitted FROM #__rsform_submissions WHERE SubmissionId = '".$args['SubmissionId']."'");
				$code = md5($args['SubmissionId'].$db->loadResult());
				
				$mainframe = JFactory::getApplication();
				$mainframe->triggerEvent('rsfp_doPayment', array($payValue, $args['formId'], $args['SubmissionId'], $price, $products, $code));
			}
		}
	}
	
	protected function _getComponentName($componentId) {
		$componentId = (int) $componentId;
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT PropertyValue FROM #__rsform_properties WHERE ComponentId='".$componentId."' AND PropertyName='NAME'");
		return $db->loadResult();
	}
	
	protected function _getComponentId($name, $formId) {
		$formId = (int) $formId;
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT p.ComponentId FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId=c.ComponentId) WHERE p.PropertyValue='".$db->escape($name)."' AND p.PropertyName='NAME' AND c.FormId='".$formId."'");
		
		return $db->loadResult();
	}
	
	protected function _getSubmissionValue($submissionId, $componentId) {
		if (is_numeric($componentId)) {
			$name = $this->_getComponentName($componentId);
		} else {
			$name = $componentId;
		}
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT FieldValue FROM #__rsform_submission_values WHERE SubmissionId='".(int) $submissionId."' AND FieldName='".$db->escape($name)."'");
		return $db->loadResult();
	}
	
	protected function paymentConfigurationScreen() {
		ob_start();
		
		?>
		<div id="page-payments">
			<table class="admintable">
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="currency"><?php echo JText::_( 'RSFP_PAYMENT_CURRENCY' ); ?></label></td>
					<td><input type="text" name="rsformConfig[payment.currency]" value="<?php echo $this->_escape(RSFormProHelper::getConfig('payment.currency'));  ?>" size="4" maxlength="50"></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="thousands"><?php echo JText::_( 'RSFP_PAYMENT_THOUSANDS' ); ?></label></td>
					<td><input type="text" name="rsformConfig[payment.thousands]" value="<?php echo $this->_escape(RSFormProHelper::getConfig('payment.thousands'));  ?>" size="4" maxlength="50"></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="decimal"><?php echo JText::_( 'RSFP_PAYMENT_DECIMAL_SEPARATOR' ); ?></label></td>
					<td><input type="text" name="rsformConfig[payment.decimal]" value="<?php echo $this->_escape(RSFormProHelper::getConfig('payment.decimal'));  ?>" size="4" maxlength="50"></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="nr.decimal"><?php echo JText::_( 'RSFP_PAYMENT_NR_DECIMALS' ); ?></label></td>
					<td><input type="text" name="rsformConfig[payment.nodecimals]" value="<?php echo $this->_escape(RSFormProHelper::getConfig('payment.nodecimals'));  ?>" size="4" maxlength="50"></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key hasTip" title="<?php echo JText::_( 'RSFP_PAYMENT_MASK_DESC' ); ?>"><label for="nr.decimal"><?php echo JText::_( 'RSFP_PAYMENT_MASK' ); ?></label></td>
					<td><input type="text" name="rsformConfig[payment.mask]" value="<?php echo $this->_escape(RSFormProHelper::getConfig('payment.mask'));  ?>" size="100"></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key hasTip" title="<?php echo JText::_( 'RSFP_PAYMENT_TOTAL_MASK_DESC' ); ?>"><label for="nr.decimal"><?php echo JText::_( 'RSFP_PAYMENT_TOTAL_MASK' ); ?></label></td>
					<td><input type="text" name="rsformConfig[payment.totalmask]" value="<?php echo $this->_escape(RSFormProHelper::getConfig('payment.totalmask'));  ?>" size="100"></td>
				</tr>
			</table>
		</div>
		<?php
		
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	public function rsfp_beforeUserEmail($args) {
		$form =& $args['form'];
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `params` FROM #__rsform_payment WHERE form_id='".(int) $form->FormId."'");
		if ($params = $db->loadResult())
		{
			$params = @unserialize($params);
			if (is_object($params) && isset($params->UserEmail))
			{
				$db->setQuery("SELECT FieldValue,FieldName FROM #__rsform_submission_values WHERE FieldName='_STATUS' AND SubmissionId='".(int) $args['submissionId']."'");
				if ($status = $db->loadObject())
				{
					// defer sending if
					// - user email is defered && the payment is not confirmed (send email only when payment is confirmed)
					if ($params->UserEmail == 1 && $status->FieldValue == 0) {
						$args['userEmail']['to'] = '';
					}
					
					// - user email is not defered && the payment is confirmed (don't send the email once again, it has already been sent)
					if ($params->UserEmail == 0 && $status->FieldValue == 1) {
						$args['userEmail']['to'] = '';
					}
				}
			}
		}
	}
	
	public function rsfp_beforeAdminEmail($args) {
		$form =& $args['form'];
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `params` FROM #__rsform_payment WHERE form_id='".(int) $form->FormId."'");
		if ($params = $db->loadResult())
		{
			$params = @unserialize($params);
			if (is_object($params) && isset($params->AdminEmail))
			{
				$db->setQuery("SELECT FieldValue,FieldName FROM #__rsform_submission_values WHERE FieldName='_STATUS' AND SubmissionId='".(int) $args['submissionId']."'");
				if ($status = $db->loadObject())
				{
					// defer sending if
					// - admin email is defered && the payment is not confirmed (send email only when payment is confirmed)
					if ($params->AdminEmail == 1 && $status->FieldValue == 0) {
						$args['adminEmail']['to'] = '';
					}
					
					// - admin email is not defered && the payment is confirmed (don't send the email once again, it has already been sent)
					if ($params->AdminEmail == 0 && $status->FieldValue == 1) {
						$args['adminEmail']['to'] = '';
					}
				}
			}
		}
	}
	
	public function rsfp_beforeAdditionalEmail($args) {
		$form =& $args['form'];
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `params` FROM #__rsform_payment WHERE form_id='".(int) $form->FormId."'");
		if ($params = $db->loadResult())
		{
			$params = @unserialize($params);
			if (is_object($params) && isset($params->AdditionalEmails))
			{
				$db->setQuery("SELECT FieldValue,FieldName FROM #__rsform_submission_values WHERE FieldName='_STATUS' AND SubmissionId='".(int) $args['submissionId']."'");
				if ($status = $db->loadObject())
				{
					// defer sending if
					// - admin email is defered && the payment is not confirmed (send email only when payment is confirmed)
					if ($params->AdditionalEmails == 1 && $status->FieldValue == 0) {
						$args['additionalEmail']['to'] = '';
					}
					
					// - admin email is not defered && the payment is confirmed (don't send the email once again, it has already been sent)
					if ($params->AdditionalEmails == 0 && $status->FieldValue == 1) {
						$args['additionalEmail']['to'] = '';
					}
				}
			}
		}
	}
	
	public function rsfp_onAfterCreatePlaceholders($args) {
		$formId 			= $args['form']->FormId;
		$submissionId 		= $args['submission']->SubmissionId;
		$multipleSeparator 	= $args['form']->MultipleSeparator;
		
		if (RSFormProHelper::componentExists($formId, $this->newComponents)) {
			$singleProduct 		= RSFormProHelper::componentExists($formId, 21);
			$multipleProducts 	= RSFormProHelper::componentExists($formId, 22);
			$total				= RSFormProHelper::componentExists($formId, 23);
			$donationProduct 	= RSFormProHelper::componentExists($formId, 28);
			$choosePayment		= RSFormProHelper::componentExists($formId, 27);
			
			// choose payment
			if (!empty($choosePayment)) {
				$details 	= RSFormProHelper::getComponentProperties($choosePayment[0]);
				$items 		= $this->_getPayments($formId);
				$value 		= $this->_getSubmissionValue($submissionId, $choosePayment[0]);
				$text		= '';
				
				if ($items) {
					foreach ($items as $item) {
						if ($item->value == $value) {
							$text = $item->text;
							break;
						}
					}
				}
				
				$args['placeholders'][] = '{'.$details['NAME'].':text}';
				$args['values'][] 		= $text;
			}
			
			// multiple products
			if (!empty($multipleProducts)) {
				$special = array('[c]', '[g]', '[d]');
				foreach ($multipleProducts as $product)
				{
					$pdetail 	= RSFormProHelper::getComponentProperties($product);
					$detail 	= $this->_getSubmissionValue($submissionId, $product);
					if ($detail == '') continue;
					
					$detail = explode("\n", $detail);
					
					$items 		= RSFormProHelper::explode(RSFormProHelper::isCode($pdetail['ITEMS']));
					$replace 	= '{'.$pdetail['NAME'].':price}';
					$with 		= array();
					foreach ($items as $item)
					{
						@list($val, $txt) = @explode('|', str_replace($special, '', $item), 2);
						if (is_null($txt))
							$txt = $val;
						if (!$val) {
							$val = 0;
						}
						
						if (in_array($txt, $detail)) {
							$txt_price = $this->_getPriceMask($txt, $val);
							$with[] = $txt_price;
						}
					}
					
					$args['placeholders'][] = $replace;
					$args['values'][] 		= implode($multipleSeparator, $with);
				}
			}
			
			// donation
			if (!empty($donationProduct)) {
				$price = $this->_getSubmissionValue($submissionId, $donationProduct[0]);
				$details = RSFormProHelper::getComponentProperties($donationProduct[0]);
				
				$args['placeholders'][] = '{'.$details['NAME'].':price}';
				$args['values'][] 		= $this->_getPriceMask($details['CAPTION'], $price);
			}
			
			// single product
			if (!empty($singleProduct)) {
				//Get Component properties
				$data 	= RSFormProHelper::getComponentProperties($this->_getComponentId('rsfp_Product', $formId));
				$price 	= $data['PRICE'];
				
				$args['placeholders'][] = '{rsfp_Product:price}';
				$args['values'][] 		= $this->_getPriceMask($data['CAPTION'], $price);
			}
			
			if (!empty($total)) {
				$price 		= $this->_getSubmissionValue($submissionId, $total[0]);
				$details 	= RSFormProHelper::getComponentProperties($total[0]);
				
				$args['placeholders'][] = '{'.$details['NAME'].':price}';
				$args['values'][] 		= $this->_getPriceMask($details['CAPTION'], $price);
			}
		}
	}
	
	private function _getPriceMask($txt, $val) {
		static $init, $nodecimals, $decimal, $thousands, $currency, $mask;
		if (!$init) {
			$init = true;
			
			$nodecimals = RSFormProHelper::getConfig('payment.nodecimals');
			$decimal    = RSFormProHelper::getConfig('payment.decimal');
			$thousands  = RSFormProHelper::getConfig('payment.thousands');
			$currency   = RSFormProHelper::getConfig('payment.currency');
			
			$mask = RSFormProHelper::getConfig('payment.mask');
		}
		
		$formattedPrice = number_format((float) $val, $nodecimals, $decimal, $thousands);
		$replacements   = array(
			'{product}' 	=> $txt,
			'{price}' 		=> $formattedPrice,
			'{currency}' 	=> $currency,
		);
		
		return str_replace(array_keys($replacements), array_values($replacements), $mask);
	}
	
	public function rsfp_onFormDelete($formId) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->delete('#__rsform_payment')
			  ->where($db->qn('form_id').'='.$db->q($formId));
		$db->setQuery($query)->execute();
	}
	
	public function rsfp_onFormBackup($form, $xml, $fields) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('*')
			  ->from($db->qn('#__rsform_payment'))
			  ->where($db->qn('form_id').'='.$db->q($form->FormId));
		$db->setQuery($query);
		if ($payment = $db->loadObject()) {
			// No need for a form_id
			unset($payment->form_id);
			
			$xml->add('payment');
			foreach ($payment as $property => $value) {
				$xml->add($property, $value);
			}
			$xml->add('/payment');
		}
	}
	
	public function rsfp_onFormRestore($form, $xml, $fields) {
		if (isset($xml->payment)) {
			$data = array(
				'form_id' => $form->FormId
			);
			foreach ($xml->payment->children() as $property => $value) {
				$data[$property] = (string) $value;
			}
			
			$row = JTable::getInstance('RSForm_Payment', 'Table');
			
			if (!$row->load($form->FormId)) {
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query	->insert('#__rsform_payment')
						->set(array(
								$db->qn('form_id') .'='. $db->q($form->FormId),
						));
				$db->setQuery($query)->execute();
			}
			$row->save($data);
		}
	}
	
	public function rsfp_bk_onFormRestoreTruncate() {
		JFactory::getDbo()->truncateTable('#__rsform_payment');
	}
}