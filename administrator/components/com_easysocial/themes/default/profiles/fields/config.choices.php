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
<ul class="list-unstyled fields-config-param-choices" data-fields-config-param data-fields-config-param-choices data-fields-config-param-field-<?php echo $name; ?> data-name="<?php echo $name; ?>" data-unique="<?php echo isset( $field->unique ) ? $field->unique : 1; ?>">
<?php if( !empty( $value ) ) {
	foreach( $value as $k => $v ) {
		echo $this->loadTemplate( 'admin/profiles/fields/config.choices.item', array( 'id' => $k, 'title' => $v->label, 'value' => $v->value, 'default' => isset( $v->default ) ? $v->default : 0 ) );
	}
} else {
	echo $this->loadTemplate( 'admin/profiles/fields/config.choices.item', array( 'id' => 0, 'title' => '', 'value' => '', 'default' => 0 ) );
} ?>
</ul>
