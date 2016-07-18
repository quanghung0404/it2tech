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
<div data-field-dropdown>
	<select class="select"
		name="<?php echo $inputName; ?>"
		id="<?php echo $inputName; ?>"
		data-field-dropdown-item
		data-id="<?php echo $field->id;?>"
	>
		<option value=""><?php echo JText::_($params->get('placeholder')); ?></option>
		<?php foreach( $options as $id => $option ){ ?>
			<option value="<?php echo $option->value;?>"<?php echo $option->value == $selected ? ' selected="selected"' : '';?>><?php echo JText::_($option->title); ?></option>
		<?php } ?>
	</select>
</div>
