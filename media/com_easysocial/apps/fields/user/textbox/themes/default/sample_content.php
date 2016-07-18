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
<div data-field-textbox>
	<input type="text" id="<?php echo $inputName;?>"
		name="<?php echo $inputName;?>"
		class="form-control input-sm"
		value="<?php echo JText::_( $params->get( 'default' ), true ); ?>"
		placeholder="<?php echo JText::_( $params->get( 'placeholder' ), true ); ?>"
		data-input
		<?php if( $params->get( 'readonly' ) ) { ?>disabled="disabled"<?php } ?>
	/>
</div>
