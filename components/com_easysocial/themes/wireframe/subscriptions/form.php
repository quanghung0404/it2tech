<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

$my	= FD::get( 'User' );

?>
<form name="frmSubscribe" id="frmSubscribe">
<table width="100%" cellpadding="0" cellspacing="5" border="0" class="form-layout" style="margin-top: 10px;">
	<?php if ( !empty( $my->email ) ) { ?>
	<tr>
		<td class="key" width="30%"><?php echo JText::_('COM_EASYSOCIAL_SUBSCRIPTION_EMAIL'); ?></td>
		<td>
			<?php echo $my->email; ?>
			<input type="text" style="display: none;" id="email" name="email" size="45" value="<?php echo $my->email; ?>" />
			<input type="hidden" id="esfullname" name="esfullname" size="45" value="<?php echo $this->html( 'string.escape' ,  $my->name ); ?>" />
		</td>
	</tr>
	<?php } else { ?>
	<tr>
		<td class="key" width="30%">
			<label class="key" for="esfullname"><?php echo JText::_('COM_EASYSOCIAL_SUBSCRIPTION_FULLNAME'); ?> <small>(<?php echo JText::_('COM_EASYSOCIAL_SUBSCRIPTION_REQUIRED'); ?>)</small></label>
		</td>
		<td>
			<input class="inputbox" type="text" id="esfullname" name="esfullname" size="45" />
		</td>
	</tr>
	<tr>
		<td class="key" width="30%">
			<label class="key" for="email"><?php echo JText::_('COM_EASYSOCIAL_SUBSCRIPTION_EMAIL'); ?> <small>(<?php echo JText::_('COM_EASYSOCIAL_SUBSCRIPTION_REQUIRED'); ?>)</small></label>
		</td>
		<td>
			<input class="inputbox" type="text" id="email" name="email" size="45" />
		</td>
	</tr>
	<?php } ?>
</table>

<input class="inputbox" type="hidden" name="contentId" id="contentId" value="<?php echo $this->contentId; ?>" />
<input class="inputbox" type="hidden" name="contentType" id="contentType" value="<?php echo $this->contentType; ?>" />
<input class="inputbox" type="hidden" name="userId" id="userId" value="<?php echo $my->id; ?>" />

</form>
