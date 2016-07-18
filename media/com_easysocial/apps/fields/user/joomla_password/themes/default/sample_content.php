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
<div data-field-joomla_password>
	<ul class="input-vertical list-unstyled">
		<li>
			<input type="password" size="30" class="form-control input-sm text validation required match-password2" autocomplete="off" value="" id="password" name="password"
					data-errors-invalid="<?php echo JText::_( 'Please enter valid password' );?>"
					data-errors-match="<?php echo JText::_( 'Passwords do not match' );?>"
					data-errors-required="<?php echo JText::_( 'Password is required' );?>"
					placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_PASSWORD_ENTER_PASSWORD' );?>"
					data-password />
		</li>
		<li <?php if( !$params->get( 'reconfirm_password' ) ) { ?>style="display: none;"<?php } ?> data-password-confirm>
			<input type="password" size="30" class="form-control input-sm text validation required match-password" autocomplete="off" value="" id="password2" name="password2"
					data-errors-invalid="<?php echo JText::_( 'Please enter valid password' );?>"
					data-errors-match="<?php echo JText::_( 'Passwords do not match' );?>"
					data-errors-required="<?php echo JText::_( 'Password is required' );?>"
					placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_PASSWORD_RECONFIRM_PASSWORD' );?>"
					data-password />
		</li>
	</ul>
</div>
