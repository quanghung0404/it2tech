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
<div data-field-joomla_fullname class="form-inline">

	<?php if ($params->get('format') != 5) { ?>
		<input type="text"
			class="form-control input-sm"
			id="first_name"
			name="first_name"
			placeholder="<?php echo JText::_( $params->get( 'format' ) == 3 ? 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_YOUR_NAME' : 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_FIRST_NAME' , true );?>"
			<?php if( $params->get( 'format' ) != 3 ) { ?>
			style="width: 49%;"
			<?php } ?>
			data-field-jname-first
			/>

		<?php if( $params->get( 'format' ) != 3 ) { ?>
		<input type="text"
			class="form-control input-sm"
			id="last_name"
			name="last_name"
			placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_LAST_NAME' , true );?>"
			style="width: 49%;"
			/>
		<?php } ?>
	<?php } ?>

	<?php if ($params->get('format') == 5) { ?>

		<input type="text"
			class="form-control input-sm"
			id="last_name"
			name="last_name"
			placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_LAST_NAME' , true );?>"
			style="width: 49%;"
			/>

		<input type="text"
			class="form-control input-sm"
			id="first_name"
			name="first_name"
			placeholder="<?php echo JText::_( $params->get( 'format' ) == 3 ? 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_YOUR_NAME' : 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_FIRST_NAME' , true );?>"
			<?php if( $params->get( 'format' ) != 3 ) { ?>
			style="width: 49%;"
			<?php } ?>
			data-field-jname-first
			/>

	<?php } ?>
</div>
