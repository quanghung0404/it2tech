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
<div class="form-group <?php echo !empty( $error ) ? 'has-error' : '';?>"
	data-field
	data-field-<?php echo $field->id; ?>
	data-registration-field
	data-registration-field-<?php echo $field->id; ?>
	<?php if( !isset( $options['check'] ) || $options['check'] !== false ) { ?>data-check<?php } ?>
>
	<?php if( $params->get( 'privacy' ) ) {
		// Temporarily removing privacy in registration form
		// 1. Does not make sense to set the privacy during registration
		// 2. Due to unavailablity of user id during registration, privacy will have to be in HTML mode
		// 3. Due to limitation of privacy API HTML mode, it does not support multiple privacy element per page
		// echo $this->includeTemplate( 'site/fields/privacy', array( 'html' => false ) );
	} ?>

	<?php if( $params->get( 'display_title' ) ) {
		echo $this->includeTemplate( 'site/fields/title' );
	} ?>

	<div class="col-xs-12 col-sm-8 data" data-content>
		<?php echo $this->includeTemplate( $subNamespace ); ?>
	</div>

	<?php if( !isset( $options['error'] ) || $options['error'] !== false ) {
		echo $this->includeTemplate( 'site/fields/error' );
	} ?>

	<?php if( $params->get( 'display_description' ) ) {
		echo $this->includeTemplate( 'site/fields/description' );
	} ?>
</div>
