EasySocial.require().script('apps/fields/user/datetime/display_content').done(function($) {

    $('[data-display-field="<?php echo $field->id; ?>"]').addController('EasySocial.Controller.Field.Datetime.Display', {
        id: <?php echo $field->id; ?>,
        userid: <?php echo $user->id; ?>
    });
});
