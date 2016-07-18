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
<div data-field-joomla_username>
	<div class="input-group input-group-sm">
		<input type="text" class="form-control validation keyup length-4 required username"
			name="username" value=""
			autocomplete="off"
			placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_USERNAME_USERNAME' ); ?>" />
		<span class="input-group-btn">
			<button type="button" class="btn btn-es" id="checkUsername" <?php if( !$params->get( 'check_username' ) ) { ?>style="display: none;"<?php } ?> data-check-username>Check</button>
		</span>
	</div>
</div>
