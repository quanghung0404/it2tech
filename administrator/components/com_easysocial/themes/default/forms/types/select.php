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
<div class="row">
	<div class="col-sm-6">
		<select name="<?php echo $field->inputName;?>" class="form-control input-sm<?php echo isset( $field->class ) ? $field->class : '';?>">
			<?php if( isset( $field->options ) ){ ?>
				<?php foreach( $field->options as $option ){ ?>
				<option value="<?php echo $option->value;?>"<?php echo $params->get( $field->name , $field->default ) == $option->value ? ' selected="selected"' : '';?>><?php echo JText::_( $option->title );?></option>
				<?php } ?>
			<?php } ?>
		</select>
	</div>
</div>
