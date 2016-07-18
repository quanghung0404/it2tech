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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-field-terms>
	<div class="col-xs-12 col-sm-8">
		<textarea class="form-control input-sm es-terms-field" readonly="readonly" data-field-terms-textbox><?php echo JText::_($params->get( 'message' , JText::_('PLG_FIELDS_TERMS_CONDITION_MESSAGE_TERMS')));?></textarea>
	</div>
	<div class="mt-5 terms-checkbox">
		<div class="checkbox">
			<label for="terms-<?php echo $inputName;?>">
				<input type="checkbox" id="terms-<?php echo $inputName;?>" name="<?php echo $inputName;?>" data-field-terms-checkbox <?php if ($value) { ?>checked="checked"<?php } ?> /> <?php echo JText::_('PLG_FIELDS_TERMS_ACCEPT_TERMS');?>
			</label>
		</div>
	</div>
</div>
