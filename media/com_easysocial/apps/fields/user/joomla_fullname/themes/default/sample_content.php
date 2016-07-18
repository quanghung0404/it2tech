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
<div data-field-joomla_fullname>
	<ul class="input-vertical list-unstyled" <?php if( $params->get( 'format' , 1 ) != 1 ) { ?>style="display: none;"<?php } ?> data-fullname-format>
		<li>
			<input type="text"
				size="30"
				class="form-control input-sm"
				id="first_name"
				name="first_name"
				placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_FIRST_NAME' , true );?>" />
		</li>
		<li>
			<input type="text"
				size="30"
				class="form-control input-sm"
				id="middle_name"
				name="middle_name"
				placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_MIDDLE_NAME' , true );?>" />
		</li>
		<li>
			<input type="text"
				size="30"
				class="form-control input-sm"
				id="last_name"
				name="last_name"
				placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_LAST_NAME' , true );?>" />
		</li>
	</ul>

	<ul class="input-vertical list-unstyled" <?php if( $params->get( 'format' , 1 ) != 2 ) { ?>style="display: none;"<?php } ?> data-fullname-format>
		<li>
			<input type="text"
				size="30"
				class="form-control input-sm"
				id="last_name"
				name="last_name"
				placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_LAST_NAME' , true );?>" />
		</li>
		<li>
			<input type="text"
				size="30"
				class="form-control input-sm"
				id="middle_name"
				name="middle_name"
				placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_MIDDLE_NAME' , true );?>" />
		</li>
		<li>
			<input type="text"
				size="30"
				class="form-control input-sm"
				id="first_name"
				name="first_name"
				placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_FIRST_NAME' , true );?>" />
		</li>
	</ul>


	<input type="text"
		size="30"
		class="form-control input-sm"
		<?php if( $params->get( 'format' , 1 ) != 3 ) { ?>style="display: none;"<?php } ?>
		id="first_name"
		name="first_name"
		placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_YOUR_NAME' , true );?>"
		data-fullname-format
		/>

	<ul class="input-vertical list-unstyled" <?php if( $params->get( 'format' , 1 ) != 4 ) { ?>style="display: none;"<?php } ?> data-fullname-format>
		<li>
			<input type="text"
				size="30"
				class="form-control input-sm"
				id="first_name"
				name="first_name"
				placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_FIRST_NAME' , true );?>" />
		</li>
		<li>
			<input type="text"
				size="30"
				class="form-control input-sm"
				id="last_name"
				name="last_name"
				placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_LAST_NAME' , true );?>" />
		</li>
	</ul>

	<ul class="input-vertical list-unstyled" <?php if( $params->get( 'format' , 1 ) != 5 ) { ?>style="display: none;"<?php } ?> data-fullname-format>
		<li>
			<input type="text"
				size="30"
				class="form-control input-sm"
				id="last_name"
				name="last_name"
				placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_LAST_NAME' , true );?>" />
		</li>
		<li>
			<input type="text"
				size="30"
				class="form-control input-sm"
				id="first_name"
				name="first_name"
				placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_FIRST_NAME' , true );?>" />
		</li>
	</ul>
</div>
