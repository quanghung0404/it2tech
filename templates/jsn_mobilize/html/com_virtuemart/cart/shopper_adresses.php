<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<table width="100%">
  <tr>
    <td width="50%" bgcolor="#ccc">
		<?php echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?>
	</td>
	<td width="50%" bgcolor="#ccc">
		<?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?>
	</td>
  </tr>
  <tr>
    <td width="50%">

		<?php 	foreach($this->BTaddress['fields'] as $item){
					if(!empty($item['value'])){
						echo $item['title'].': '.$this->escape($item['value']).'<br/>';
					}
				} ?>

	</td>
    <td width="50%">
			<?php
			if(!empty($this->STaddress['fields'])){
				foreach($this->STaddress['fields'] as $item){
					if(!empty($item['value'])){
						echo $item['title'].': '.$this->escape($item['value']).'<br/>';
					}
				}
			} else {
				foreach($this->BTaddress['fields'] as $item){
					if(!empty($item['value'])){
						echo $item['title'].': '.$this->escape($item['value']).'<br/>';
					}
				}
			} ?>
	</td>
  </tr>
</table>