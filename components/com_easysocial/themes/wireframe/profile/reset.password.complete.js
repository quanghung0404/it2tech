<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<?php if (isset($enableValidation) && $enableValidation) { ?>
EasySocial
	.require()
	.script('apps/fields/user/joomla_password/content')
	.done(function($) {
		$('[data-reset-password]').addController('EasySocial.Controller.Field.Joomla_password', {
			triggerError: false,
			required: 1,
			passwordStrength: <?php echo $params->get( 'password_strength' ) ? 'true' : 'false'; ?>,
			reconfirmPassword: true,
			min: <?php echo $params->get( 'min', 4 ); ?>,
			max: <?php echo $params->get( 'max', 0 ); ?>,

			minInteger: <?php echo $params->get( 'min_integer', 0 ); ?>,
			minSymbol: <?php echo $params->get( 'min_symbols', 0 ); ?>,
			minUpperCase: <?php echo $params->get( 'min_uppercase', 0 ); ?>,

			requireOriginal: false,
			event: 'onRegister'
		});
	});
<?php } ?>
