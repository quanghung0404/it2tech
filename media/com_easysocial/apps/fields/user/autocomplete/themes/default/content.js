
EasySocial
.require()
.script('apps/fields/user/autocomplete/content')
.done(function($) {

    $('[data-autocomplete-wrapper-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Autocomplete', {
        required: <?php echo $field->required ? 1 : 0; ?>,
        id: <?php echo $field->id; ?>,
        fieldname: '<?php echo $inputName; ?>',
        exclusion: <?php echo $exclusion ? $exclusion : '[]';?>
    });
});