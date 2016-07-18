<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
// To pass field independent data in through php
EasySocial.module('field.relationship/<?php echo $field->id; ?>', function($) {
	this.resolve(<?php echo FD::json()->encode( $types ); ?>);
});

EasySocial
	.require()
	.script('apps/fields/user/relationship/content')
	.done(function($) {
		$('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Relationship', {
			required: <?php echo $field->required ? 1 : 0; ?>,
			id: <?php echo $field->id; ?>,
			fieldname: '<?php echo $inputName; ?>'
		});
	});
