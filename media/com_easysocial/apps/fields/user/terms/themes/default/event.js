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
	.script('apps/fields/user/terms/content')
	.done(function($) {
		$('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Terms', {
			required: <?php echo $field->required ? 1 : 0; ?>,
			event: '<?php echo $event; ?>'
	    });

        $('[data-field-terms-dialog]').on('click', function(){
            EasySocial.dialog({
                content: EasySocial.ajax('fields/user/terms/getTerms', {"id": "<?php echo $field->id;?>"})
            });
        });
    });
