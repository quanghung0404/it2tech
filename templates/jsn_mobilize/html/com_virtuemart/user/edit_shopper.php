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
<?php if(!$this->userDetails->user_is_vendor){ ?>
<div class="buttonBar-right">
	<button class="button" type="submit" onclick="javascript:return myValidator(userForm, 'saveUser');" ><?php echo $this->button_lbl ?></button>
	&nbsp;
	<button class="button" type="reset" onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_virtuemart&view=user'); ?>'" ><?php echo JText::_('COM_VIRTUEMART_CANCEL'); ?></button>

</div>
<?php } ?>
<?php if( $this->userDetails->virtuemart_user_id!=0)  {
    echo $this->loadTemplate('vmshopper');
    } ?>
<?php echo $this->loadTemplate('address_userfields'); ?>



<?php if ($this->userDetails->JUser->get('id') ) {
  echo $this->loadTemplate('address_addshipto');
  }
  ?>
<?php if(!empty($this->virtuemart_userinfo_id)){
	echo '<input type="hidden" name="virtuemart_userinfo_id" value="'.(int)$this->virtuemart_userinfo_id.'" />';
}
?>
<input type="hidden" name="task" value="<?php echo $this->fTask; // I remember, we removed that, but why?   ?>" />
<input type="hidden" name="address_type" value="BT" />

