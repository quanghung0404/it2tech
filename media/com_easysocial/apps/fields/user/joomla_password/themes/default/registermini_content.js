<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
EasySocial
	.require()
	.script('apps/fields/user/joomla_password/registermini_content')
	.done(function($) {
		$('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Joomla_password.Mini', {
			required: <?php echo $field->required ? 1 : 0; ?>,
            reconfirmPassword: <?php echo $params->get( 'mini_reconfirm_password' ) ? 'true' : 'false'; ?>,
			min: <?php echo $params->get( 'min', 4 ); ?>,
            minInteger: <?php echo $params->get( 'min_integer', 0 ); ?>,
            minSymbol: <?php echo $params->get( 'min_symbols', 0 ); ?>,
            minUpperCase: <?php echo $params->get( 'min_uppercase', 0 ); ?>
		});
	});
