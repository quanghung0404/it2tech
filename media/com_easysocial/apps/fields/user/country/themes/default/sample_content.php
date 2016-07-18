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
<div data-field-country>
	<div data-country-select data-country-select-textboxlist <?php if( $params->get( 'select_type' ) !== 'textboxlist' ) { ?>style="display: none;"<?php } ?>>
		<input type="text" class="form-control input-sm" />
	</div>

	<div data-country-select data-country-select-multilist <?php if( $params->get( 'select_type' ) !== 'multilist' ) { ?>style="display: none;"<?php } ?>>
		<select multiple="multiple" size="<?php echo $params->get( 'multilist_size' ); ?>">
			<?php foreach( $countries as $country ) { ?>
			<option value="<?php echo $country->id; ?>"><?php echo $country->title; ?></option>
			<?php } ?>
		</select>
	</div>

	<div data-country-select data-country-select-checkbox style="height: 200px; overflow-y: scroll;<?php if( $params->get( 'select_type' ) !== 'checkbox' ) { ?>display: none;<?php } ?>">
		<?php foreach( $countries as $country ) { ?>
		<label><input type="checkbox" /> <?php echo $country->title; ?></label>
		<?php } ?>
	</div>

	<div data-country-select data-country-select-dropdown <?php if( $params->get( 'select_type' ) !== 'dropdown' ) { ?>style="display: none;"<?php } ?>>
		<select>
			<option><?php echo JText::_( 'PLG_FIELDS_COUNTRY_SELECT_A_COUNTRY' ); ?></option>
		</select>
	</div>

	<div data-country-max-message <?php if( !$params->get( 'show_max_message' ) ) { ?>style="display: none;"<?php } ?>>
		<?php echo JText::_( 'PLG_FIELDS_COUNTRY_LIMIT_MAX_SELECTION' ); ?>: <span data-country-max-count><?php echo $params->get( 'max' ); ?></span>
	</div>
</div>
