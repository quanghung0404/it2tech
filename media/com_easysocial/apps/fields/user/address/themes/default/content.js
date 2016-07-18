<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>

<?php if ($params->get('use_maps')) { ?>
EasySocial.require().script('apps/fields/user/address/maps').done(function($) {
	$('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Address.Maps', {
        id: <?php echo $field->id; ?>,
        latitude: <?php echo !empty($value->latitude) ? $value->latitude : '""'; ?>,
        longitude: <?php echo !empty($value->longitude) ? $value->longitude : '""'; ?>,
        address: '<?php echo addslashes($value->toString()); ?>',
        zoom: <?php echo !empty($value->zoom) ? $value->zoom : 2; ?>,
        required: <?php echo $required; ?>
    });
});
<?php } else { ?>
EasySocial.require().script('apps/fields/user/address/content').done(function($) {
	$('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Address', {
        id: <?php echo $field->id; ?>,
		required: <?php echo $required; ?>,
		show: <?php echo $show; ?>
	});
});
<?php }
