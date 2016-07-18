<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      Proprietary Use License http://stackideas.com/licensing.html
* @author       Stack Ideas Sdn Bhd
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($params->get('calendar')) { ?>
EasySocial
    .require()
    .script('apps/fields/user/datetime/content')
    .done(function($) {
        $('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Datetime', {
            required: <?php echo $field->required ? 1 : 0; ?>,
            calendarDateFormat: '<?php echo $calendarDateFormat; ?>',
            yearfrom: <?php echo $yearRange ? $yearRange->min : 'null'; ?>,
            yearto: <?php echo $yearRange ? $yearRange->max : 'null'; ?>,
            lang: '<?php echo JFactory::getDocument()->getLanguage();?>',
            allowTime: <?php echo (int) $params->get('allow_time', 0); ?>,
            calendarLanguage: '<?php echo $params->get('calendar_language', 'english'); ?>'
        });
});
<?php } else { ?>
EasySocial.require().script('apps/fields/user/datetime/dropdown').done(function($) {
    $('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Datetime.Dropdown', {
        required: <?php echo $field->required ? 1 : 0; ?>,
        yearfrom: <?php echo $yearRange ? $yearRange->min : 'null'; ?>,
        yearto: <?php echo $yearRange ? $yearRange->max : 'null'; ?>,
        allowTime: <?php echo (int) $params->get('allow_time', 0); ?>
    });
});
<?php }
