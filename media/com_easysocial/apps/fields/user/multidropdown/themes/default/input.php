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
?>
<li data-field-multidropdown-item class="data-field-multidropdown-item">
	<div class="media">
		<div class="media-object">
			<span class="item-move" data-field-multidropdown-move><i class="icon-es-drag"></i></span>
		</div>

		<div class="media-body">
			<select name="<?php echo $inputName; ?>[]" data-field-multidropdown-input class="form-control input-sm">
			<?php foreach ($choices as $id => $choice) { ?>
				<option value="<?php echo $choice->value; ?>" <?php if ((!empty($value) && $value === $choice->value) || (empty($value) && isset($choice->default) && $choice->default)) { ?>selected="selected"<?php } ?>><?php echo JText::_($choice->title); ?></option>
			<?php } ?>
			</select>
			<button class="btn btn-del btn-es btn-sm" type="button" data-field-multidropdown-delete>×</button>
		</div>
	</div>
</li>
